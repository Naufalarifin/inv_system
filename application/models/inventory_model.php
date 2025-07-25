<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('config_model');
    }

    public function processInventoryIn($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $qc_status = isset($data['qc_status']) ? trim($data['qc_status']) : '';

        $validation_result = $this->_validateSerialNumber($serial_number);
        if (!$validation_result['success']) { 
            return $this->_response(false, $validation_result['message']);
        }

        if ($this->_isSerialNumberExists($serial_number)) {
            return $this->_response(false, 'Serial number sudah ada dalam database');
        }

        $parsed_data = $this->_parseSerialNumber($serial_number);
        if (!$parsed_data['valid']) { 
            return $this->_response(false, $parsed_data['message']);
        }

        $device = $this->_getDeviceByCode($parsed_data['device_code']);
        if (!$device) {
            return $this->_response(false, 'Device tidak ditemukan dengan kode: ' . $parsed_data['device_code']);
        }

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
            'loc_move' => null,
        );

        if ($this->db->insert('inv_act', $insert_data)) {
            return $this->_response(true, 'Data berhasil diinput dengan ID: ' . $insert_data['id_act']);
        } else {
            return $this->_response(false, 'Gagal menyimpan data ke database');
        }
    }

    public function processInventoryOut($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $inventory_item = $this->_getInventoryBySerial($serial_number);
        if (!$inventory_item) {
            return $this->_response(false, 'Serial number tidak ditemukan: ' . $serial_number);
        }

        if ($inventory_item->inv_out !== null && $inventory_item->inv_out !== '0000-00-00 00:00:00') {
            return $this->_response(false, 'Item sudah dalam status OUT');
        }

        $now = date('Y-m-d H:i:s');
        $update_data = array(
            'inv_out' => $now,
            'adm_out' => $this->_getAdminId()
        );

        if (empty($inventory_item->inv_move) || $inventory_item->inv_move == '0000-00-00 00:00:00') {
            $update_data['inv_move'] = $now;
            $update_data['adm_move'] = $this->_getAdminId();
            $update_data['loc_move'] = 'Lantai 1';
        }

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

        if (empty($location)) {
            return $this->_response(false, 'Lokasi tujuan tidak boleh kosong');
        }

        $inventory_item = $this->_getInventoryBySerial($serial_number);
        if (!$inventory_item) {
            return $this->_response(false, 'Serial number tidak ditemukan: ' . $serial_number);
        }

        if ($inventory_item->inv_out !== null && $inventory_item->inv_out !== '0000-00-00 00:00:00') {
            return $this->_response(false, 'Item sudah dalam status OUT, tidak bisa dipindah');
        }

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
     * Process inventory EDIT - METHOD BARU
     */
    // public function processInventoryEdit($data) {
    //     $id_act = isset($data['id_act']) ? trim($data['id_act']) : '';
    //     $serial_number = isset($data['dvc_sn']) ? trim($data['dvc_sn']) : '';
    //     $qc_status = isset($data['dvc_qc']) ? trim($data['dvc_qc']) : '';
    //     $loc_move = isset($data['loc_move']) ? trim($data['loc_move']) : '';

    //     // Validate ID
    //     if (empty($id_act)) {
    //         return $this->_response(false, 'ID tidak ditemukan');
    //     }

    //     // Get existing data
    //     $existing_item = $this->_getInventoryById($id_act);
    //     if (!$existing_item) {
    //         return $this->_response(false, 'Data tidak ditemukan');
    //     }

    //     $update_data = array();

    //     // Handle serial number update
    //     if (!empty($serial_number) && $serial_number !== $existing_item->dvc_sn) {
    //         // Validate serial number
    //         $validation_result = $this->_validateSerialNumber($serial_number);
    //         if (!$validation_result['valid']) {
    //             return $this->_response(false, $validation_result['message']);
    //         }

    //         // Check if new serial number already exists (exclude current record)
    //         if ($this->_isSerialNumberExistsForEdit($serial_number, $id_act)) {
    //             return $this->_response(false, 'Serial number sudah ada dalam database');
    //         }

    //         // Parse serial number
    //         $parsed_data = $this->_parseSerialNumber($serial_number);

    //         // Get device info
    //         $device = $this->_getDeviceByCode($parsed_data['device_code']);
    //         if (!$device) {
    //             return $this->_response(false, 'Device tidak ditemukan dengan kode: ' . $parsed_data['device_code']);
    //         }

    //         // Update serial number, device ID, size, dan color
    //         $update_data['dvc_sn'] = $serial_number;
    //         $update_data['id_dvc'] = $device->id_dvc;

    //         if ($parsed_data['size'] !== null) {
    //             $update_data['dvc_size'] = $parsed_data['size'];
    //         }
    //         if (isset($update_data['dvc_col'])) {
    //             $update_data['dvc_col'] = $parsed_data['color'];
    //         }
    //     }

    //     // Handle QC status update
    //     if ($qc_status !== '') {
    //         $update_data['dvc_qc'] = $qc_status;
    //     }

    //     // Handle location move update
    //     if ($loc_move !== '0' && $loc_move !== '') {
    //         if (!$existing_item->inv_out) {
    //             $update_data['loc_move'] = $loc_move;
    //         } else {
    //             if ($loc_move != $existing_item->loc_move) {
    //                 return $this->_response(false, 'Location Move hanya bisa diubah jika belum keluar!');
    //             }
    //         }
    //     }

    //     // Check if there are changes
    //     if (empty($update_data)) {
    //         return $this->_response(false, 'Tidak ada data yang diubah');
    //     }

    //     if ($this->db->where('id_act', $id_act)->update('inv_act', $update_data)) {
    //         // Build success message with updated info
    //         $updated_info = array();
    //         if (isset($update_data['id_dvc'])) {
    //             $updated_info[] = 'Device ID: ' . $update_data['id_dvc'];
    //         }
    //         if (isset($update_data['dvc_size'])) {
    //             $updated_info[] = 'Size: ' . $update_data['dvc_size'];
    //         }
    //         if (isset($update_data['dvc_col'])) {
    //             $updated_info[] = 'Color: ' . $update_data['dvc_col'];
    //         }

    //         $message = 'Data berhasil diupdate';
    //         if (!empty($updated_info)) {
    //             $message .= ' (' . implode(', ', $updated_info) . ')';
    //         }

    //         return $this->_response(true, $message);
    //     } else {
    //         return $this->_response(false, 'Gagal update data');
    //     }
    // }

    /**
     * Private helper methods
     */
    private function _validateSerialNumber($serial_number) {
        if (empty($serial_number)) {
            return $this->_response(false, 'Serial number tidak boleh kosong');
        }
        return $this->_response(true, 'Valid');
    }

    private function _isSerialNumberExists($serial_number) {
        $query = $this->db->where('dvc_sn', $serial_number)->get('inv_act');
        return $query->num_rows() > 0;
    }

    private function _parseSerialNumber($serial_number) {

        if (strlen($serial_number) !== 15) {
            return ['valid' => false, 'message' => 'Serial number harus 15 karakter'];
        }

        $yy = substr($serial_number, 0, 2); 
        $mm = strtoupper(substr($serial_number, 2, 1)); 
        $dd = substr($serial_number, 3, 2); 

        $flag = strtoupper(substr($serial_number, 6, 1)); // 1 digit flag N/R

        $bulan_map = [
            '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6,
            '7' => 7, '8' => 8, '9' => 9, '0' => 10, 'A' => 11, 'B' => 12
        ];
        $tahun = 2000 + intval($yy);
        $bulan = isset($bulan_map[$mm]) ? $bulan_map[$mm] : 0;
        $tanggal = intval($dd);

        $now = getdate();
        $tahun_now = $now['year'];
        $bulan_now = $now['mon'];
        $tanggal_now = $now['mday'];

        if ($tahun > $tahun_now) {
            return ['valid' => false, 'message' => 'Tahun produksi serial number tidak boleh melebihi tahun sekarang'];
        }

        if ($bulan < 1 || $bulan > 12) {
            return ['valid' => false, 'message' => 'Bulan produksi serial number tidak valid!'];
        }
        if ($tahun == $tahun_now && $bulan > $bulan_now) {
            return ['valid' => false, 'message' => 'Bulan produksi serial number tidak boleh melebihi bulan sekarang!'];
        }

        $max_tanggal = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        if ($tanggal < 1 || $tanggal > $max_tanggal) {
            return ['valid' => false, 'message' => 'Tanggal produksi serial number tidak valid!'];
        }
        if ($tahun == $tahun_now && $bulan == $bulan_now && $tanggal > $tanggal_now) {
            return ['valid' => false, 'message' => 'Tanggal produksi serial number tidak boleh melebihi tanggal hari ini!'];
        }

        if ($flag !== 'N' && $flag !== 'R') {
            return ['valid' => false, 'message' => 'Karakter ke-7 serial number harus N (New) atau R (Refurbish)!'];
        }

        $char_5 = substr($serial_number, 5, 1);
        $char_7 = substr($serial_number, 7, 1);
        $char_8 = substr($serial_number, 8, 1);
        $device_code = $char_5 . $char_7 . $char_8;

        $size = null;
        $color = null;

        if ($char_5 === 'T') {
            $color = 'Dark Grey';
            if (strlen($serial_number) > 9) {
                $size_char = substr($serial_number, 9, 1);
                $size = $this->_getSizeFromChar($size_char);
            }
        } elseif ($char_5 === 'S') {
            if (strlen($serial_number) > 9) {
                $size_char = substr($serial_number, 9, 1);
                $size = $this->_getSizeFromChar($size_char);
            }
            $combined_78 = intval($char_7 . $char_8);
            if ($combined_78 <= 50 && strlen($serial_number) > 11) {
                $color_chars = substr($serial_number, 10, 2);
                $color = $this->_getColorFromChars($color_chars);
            }
        }

        return array(
            'valid' => true,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'tanggal' => $tanggal,
            'flag' => $flag,
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
            '01' => 'Dark Gray', '02' => 'Black', '03' => 'Grey', '04' => 'Navy',
            '05' => 'Army', '06' => 'Maroon', '99' => 'Custom', '00' => '-'
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
