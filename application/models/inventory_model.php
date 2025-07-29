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
        $user_provided_date_string = isset($data['user_date']) ? trim($data['user_date']) : '';

        // Validasi user_date jika ada
        if (!empty($user_provided_date_string)) {
            $date_validation = $this->_validateUserDate($user_provided_date_string, 'in');
            if (!$date_validation['success']) {
                return $this->_response(false, $date_validation['message']);
            }
        }

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

        // Tentukan nilai untuk inv_in dan act_date
        $inv_in_value = null;
        if (!empty($user_provided_date_string)) {
            $converted_date_ymd = $this->_convertDdMmYyyyToYmd($user_provided_date_string);
            $inv_in_value = $converted_date_ymd . '00:00:00';
        } else {
            $inv_in_value = date('Y-m-d H:i:s');
        }

        $act_date_value = date('Y-m-d H:i:s');

        $insert_data = array(
            'id_act' => $this->_generateNewActId(),
            'id_dvc' => $device->id_dvc,
            'dvc_size' => $parsed_data['size'],
            'dvc_col' => $parsed_data['color'],
            'dvc_sn' => $serial_number,
            'dvc_qc' => $qc_status,
            'inv_in' => $inv_in_value,
            'adm_in' => $this->_getAdminId(),
            'inv_move' => null,
            'inv_out' => null,
            'inv_rls' => null,
            'adm_move' => null,
            'adm_out' => null,
            'adm_rls' => null,
            'loc_move' => null,
            'act_date' => $act_date_value,
            'adm_act' => $this->_getAdminId(),
        );

        if ($this->db->insert('inv_act', $insert_data)) {
            return $this->_response(true, 'Data berhasil diinput dengan ID: ' . $insert_data['id_act']);
        } else {
            return $this->_response(false, 'Gagal menyimpan data ke database');
        }
    }

    public function processInventoryOut($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $user_provided_date_string = isset($data['user_date']) ? trim($data['user_date']) : '';

        $inventory_item = $this->_getInventoryBySerial($serial_number);
        if (!$inventory_item) {
            return $this->_response(false, 'Serial number tidak ditemukan: ' . $serial_number);
        }

        if ($inventory_item->inv_out !== null && $inventory_item->inv_out !== '0000-00-00 00:00:00') {
            return $this->_response(false, 'Item sudah dalam status OUT');
        }

        // Validasi user_date jika ada - dengan validasi tambahan untuk OUT
        if (!empty($user_provided_date_string)) {
            $date_validation = $this->_validateUserDate($user_provided_date_string, 'out', $inventory_item);
            if (!$date_validation['success']) {
                return $this->_response(false, $date_validation['message']);
            }
        }

        // Tentukan timestamp untuk inv_out
        $out_timestamp = null;
        if (!empty($user_provided_date_string)) {
            $converted_date_ymd = $this->_convertDdMmYyyyToYmd($user_provided_date_string);
            $out_timestamp = $converted_date_ymd . ' 00:00:00';
        } else {
            $out_timestamp = date('Y-m-d H:i:s');
        }

        $update_data = array(
            'adm_act' => $this->_getAdminId(),
            'inv_out' => $out_timestamp,
            'adm_out' => $this->_getAdminId()
        );

        if (empty($inventory_item->inv_move) || $inventory_item->inv_move == '0000-00-00 00:00:00') {
            $update_data['inv_move'] = $out_timestamp;
            $update_data['adm_move'] = $this->_getAdminId();
            $update_data['loc_move'] = 'Lantai 1';
        }

        if ($this->db->where('dvc_sn', $serial_number)->update('inv_act', $update_data)) {
            return $this->_response(true, 'Data berhasil di-update untuk OUT: ' . $serial_number);
        } else {
            return $this->_response(false, 'Gagal mengupdate data');
        }
    }

    public function processInventoryMove($data) {
        $serial_number = isset($data['serial_number']) ? trim($data['serial_number']) : '';
        $location = isset($data['location']) ? trim($data['location']) : '';
        $user_provided_date_string = isset($data['user_date']) ? trim($data['user_date']) : '';

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

        // Validasi user_date jika ada - dengan validasi tambahan untuk MOVE
        if (!empty($user_provided_date_string)) {
            $date_validation = $this->_validateUserDate($user_provided_date_string, 'move', $inventory_item);
            if (!$date_validation['success']) {
                return $this->_response(false, $date_validation['message']);
            }
        }

        // Tentukan timestamp untuk inv_move
        $move_timestamp = null;
        if (!empty($user_provided_date_string)) {
            $converted_date_ymd = $this->_convertDdMmYyyyToYmd($user_provided_date_string);
            $move_timestamp = $converted_date_ymd . ' 00:00:00';
        } else {
            $move_timestamp = date('Y-m-d H:i:s');
        }

        $update_data = array(
            'adm_act' => $this->_getAdminId(),
            'inv_move' => $move_timestamp,
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
            return $this->_response(false, 'Serial number tidak boleh kosong');
        }
        return $this->_response(true, 'Valid');
    }

    /**
     * Validasi user_date dengan aturan berbeda berdasarkan type
     */
    private function _validateUserDate($date_string, $type = 'in', $inventory_item = null) {
        if (empty($date_string)) {
            return $this->_response(true, 'Valid'); // Empty date is allowed
        }

        // Cek format tanggal dd-mm-yyyy
        $converted_date = $this->_convertDdMmYyyyToYmd($date_string);
        if ($converted_date === null) {
            return $this->_response(false, 'Format tanggal tidak valid. Gunakan format dd-mm-yyyy.');
        }

        // Cek apakah tanggal tidak melebihi hari ini
        $today = date('Y-m-d');
        if ($converted_date > $today) {
            return $this->_response(false, 'Tanggal tidak boleh melebihi hari ini (' . date('d-m-Y') . ').');
        }

        // Validasi tambahan berdasarkan type
        if ($type === 'move' && $inventory_item !== null) {
            // MOVE: Tanggal tidak boleh sebelum tanggal IN
            if (!empty($inventory_item->inv_in) && $inventory_item->inv_in !== '0000-00-00 00:00:00') {
                $inv_in_date = date('Y-m-d', strtotime($inventory_item->inv_in));
                if ($converted_date < $inv_in_date) {
                    return $this->_response(false, 'Tanggal MOVE tidak boleh sebelum tanggal IN (' . date('d-m-Y', strtotime($inventory_item->inv_in)) . ').');
                }
            }
        } elseif ($type === 'out' && $inventory_item !== null) {
            // OUT: Tanggal tidak boleh sebelum tanggal IN dan MOVE
            if (!empty($inventory_item->inv_in) && $inventory_item->inv_in !== '0000-00-00 00:00:00') {
                $inv_in_date = date('Y-m-d', strtotime($inventory_item->inv_in));
                if ($converted_date < $inv_in_date) {
                    return $this->_response(false, 'Tanggal OUT tidak boleh sebelum tanggal IN (' . date('d-m-Y', strtotime($inventory_item->inv_in)) . ').');
                }
            }
            
            // Cek tanggal MOVE jika ada
            if (!empty($inventory_item->inv_move) && $inventory_item->inv_move !== '0000-00-00 00:00:00') {
                $inv_move_date = date('Y-m-d', strtotime($inventory_item->inv_move));
                if ($converted_date < $inv_move_date) {
                    return $this->_response(false, 'Tanggal OUT tidak boleh sebelum tanggal MOVE (' . date('d-m-Y', strtotime($inventory_item->inv_move)) . ').');
                }
            }
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
            if ($combined_78 <= 50 && strlen($serial_number) == 15) {
                if ($combined_78 == 27){
                    $color_chars = substr($serial_number, 10, 2);
                    $color = $this->_getColorFromChars($color_chars);
                }else{
                    $color = 'Black'; 
                }
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

    private function _convertDdMmYyyyToYmd($date_string) {
        $d = DateTime::createFromFormat('d-m-Y', $date_string);
        if ($d && $d->format('d-m-Y') === $date_string) {
            return $d->format('Y-m-d');
        }
        return null;
    }
}