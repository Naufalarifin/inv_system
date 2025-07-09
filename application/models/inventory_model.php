<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('config_model');
    }

    /**
     * Process inventory IN
     */
    public function processInventoryIn($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $qc_status = isset($data['qc_status']) ? trim($data['qc_status']) : '';
        
        // Validate input
        $validation_result = $this->_validateSerialNumber($serial_number);
        if (!$validation_result['valid']) {
            return $this->_response(false, $validation_result['message']);
        }
        
        // Check if serial number already exists
        if ($this->_isSerialNumberExists($serial_number)) {
            return $this->_response(false, 'Serial number sudah ada dalam database');
        }
        
        // Parse serial number
        $parsed_data = $this->_parseSerialNumber($serial_number);
        
        // Get device info
        $device = $this->_getDeviceByCode($parsed_data['device_code']);
        if (!$device) {
            return $this->_response(false, 'Device tidak ditemukan dengan kode: ' . $parsed_data['device_code']);
        }
        
        // Insert new inventory record
        $insert_data = array(
            'id_act' => $this->_generateNewActId(),
            'id_dvc' => $device->id_dvc,
            'dvc_size' => $parsed_data['size'],
            'dvc_col' => $parsed_data['color'],
            'dvc_sn' => $serial_number,
            'dvc_qc' => $qc_status,
            'inv_in' => date('Y-m-d H:i:s'),
            'adm_in' => $this->_getAdminId(),
            'inv_move' => null,
            'inv_out' => null,
            'inv_rls' => null,
            'adm_move' => null,
            'adm_out' => null,
            'adm_rls' => null,
            'loc_move' => null
        );
        
        if ($this->db->insert('inv_act', $insert_data)) {
            return $this->_response(true, 'Data berhasil diinput dengan ID: ' . $insert_data['id_act']);
        } else {
            return $this->_response(false, 'Gagal menyimpan data ke database');
        }
    }

    /**
     * Process inventory OUT
     */
    public function processInventoryOut($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        
        // Validate serial number exists
        $inventory_item = $this->_getInventoryBySerial($serial_number);
        if (!$inventory_item) {
            return $this->_response(false, 'Serial number tidak ditemukan: ' . $serial_number);
        }
        
        // Check if already out
        if ($inventory_item->inv_out !== null) {
            return $this->_response(false, 'Item sudah dalam status OUT');
        }
        
        // Update inventory record
        $update_data = array(
            'inv_out' => date('Y-m-d H:i:s'),
            'adm_out' => $this->_getAdminId()
        );
        
        if ($this->db->where('dvc_sn', $serial_number)->update('inv_act', $update_data)) {
            return $this->_response(true, 'Data berhasil di-update untuk OUT: ' . $serial_number);
        } else {
            return $this->_response(false, 'Gagal mengupdate data');
        }
    }

    /**
     * Process inventory MOVE
     */
    public function processInventoryMove($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $location = isset($data['location']) ? trim($data['location']) : '';
        
        // Validate inputs
        if (empty($location)) {
            return $this->_response(false, 'Lokasi tujuan tidak boleh kosong');
        }
        
        // Validate serial number exists
        $inventory_item = $this->_getInventoryBySerial($serial_number);
        if (!$inventory_item) {
            return $this->_response(false, 'Serial number tidak ditemukan: ' . $serial_number);
        }
        
        // Check if already out
        if ($inventory_item->inv_out !== null) {
            return $this->_response(false, 'Item sudah dalam status OUT, tidak bisa dipindah');
        }
        
        // Update inventory record
        $update_data = array(
            'inv_move' => date('Y-m-d H:i:s'),
            'adm_move' => $this->_getAdminId(),
            'loc_move' => $location
        );
        
        if ($this->db->where('dvc_sn', $serial_number)->update('inv_act', $update_data)) {
            return $this->_response(true, 'Data berhasil dipindah ke: ' . $location);
        } else {
            return $this->_response(false, 'Gagal mengupdate data');
        }
    }

    /**
     * Private helper methods
     */
    private function _validateSerialNumber($serial_number) {
        if (empty($serial_number)) {
            return array('valid' => false, 'message' => 'Serial number tidak boleh kosong');
        }
        
        if (strlen($serial_number) < 11) {
            return array('valid' => false, 'message' => 'Serial number tidak valid (minimal 11 karakter)');
        }
        
        return array('valid' => true, 'message' => 'Valid');
    }

    private function _isSerialNumberExists($serial_number) {
        $query = $this->db->where('dvc_sn', $serial_number)->get('inv_act');
        return $query->num_rows() > 0;
    }

    private function _parseSerialNumber($serial_number) {
        // Parse serial number components
        $char_5 = substr($serial_number, 4, 1); // index 4 = karakter ke-5
        $char_7 = substr($serial_number, 6, 1); // index 6 = karakter ke-7
        $char_8 = substr($serial_number, 7, 1); // index 7 = karakter ke-8
        
        // Get device code from characters 5,7,8
        $device_code = $char_5 . $char_7 . $char_8;
        
        // Determine size and color
        $size = null;
        $color = null;
        
        if ($char_5 === 'T') {
            $color = null;
        } elseif ($char_5 === 'S') {
            // Get size from character 10 (index 9)
            if (strlen($serial_number) > 9) {
                $size_char = substr($serial_number, 9, 1);
                $size = $this->_getSizeFromChar($size_char);
            }
            
            // Get color from characters 7,8 combined
            $combined_78 = intval($char_7 . $char_8);
            if ($combined_78 <= 50 && strlen($serial_number) > 11) {
                $color_chars = substr($serial_number, 10, 2); // characters 11,12 (index 10,11)
                $color = $this->_getColorFromChars($color_chars);
            }
        }
        
        return array(
            'device_code' => $device_code,
            'size' => $size,
            'color' => $color
        );
    }

    private function _getSizeFromChar($size_char) {
        $size_map = array(
            '1' => 'XS', '2' => 'S', '3' => 'M', '4' => 'L', '5' => 'XL',
            '6' => 'XXL', '7' => '3XL', '8' => 'ALL', '9' => 'Cus', '0' => '-'
        );
        
        return isset($size_map[$size_char]) ? $size_map[$size_char] : null;
    }

    private function _getColorFromChars($color_chars) {
        $color_map = array(
            '01' => 'Dark Gray', '02' => 'Black', '03' => 'Grey', '04' => 'Blue Navy',
            '05' => 'Green Army', '06' => 'Red Maroon', '99' => 'Custom', '00' => '-'
        );
        
        return isset($color_map[$color_chars]) ? $color_map[$color_chars] : null;
    }

    private function _getDeviceByCode($device_code) {
        $query = $this->db->where('dvc_code_sn', $device_code)->get('inv_dvc');
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    private function _getInventoryBySerial($serial_number) {
        $query = $this->db->where('dvc_sn', $serial_number)->get('inv_act');
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    private function _generateNewActId() {
        $query = $this->db->select_max('id_act')->get('inv_act');
        $last_id = $query->row()->id_act;
        return $last_id + 1;
    }

    private function _getAdminId() {
        $config = $this->config_model->getConfig();
        return $config['adm_id'];
    }

    private function _response($success, $message, $data = null) {
        $response = array(
            'success' => $success,
            'message' => $message
        );
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $response;
    }
}
?>