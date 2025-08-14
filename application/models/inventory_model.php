<?php

class inventory_model extends CI_Model {

	public function getAllItem($show = 20, $adddata = "", $sort = "") {
        if ($show < 100 && isset($_GET['data_view_item']) && $_GET['data_view_item'] != "") {
            $show = $_GET['data_view_item'];
        }
        $sort = "ORDER BY id_act DESC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterItemList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        if (isset($filter['sort']) && $filter['sort'] != "") {
            $sort = $filter['sort'];
        }
        $sql = "SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code
                FROM inv_act
                LEFT JOIN inv_dvc ON inv_act.id_dvc = inv_dvc.id_dvc
                WHERE 1=1 ";
                if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
                $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code", "SELECT COUNT(*) as total", $sql);
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $main_query = $this->db->query($sql . $limit);

        $data = array();
        $data['query'] = $main_query;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        return $data;
    }

    public function getAllItemByTech($tech_type, $show = 20, $adddata = "", $sort = "") {
        if (!in_array($tech_type, array('ecct', 'ecbs'))) {
            throw new InvalidArgumentException("Parameter tech_type harus 'ecct' atau 'ecbs'");
        }
        if ($show < 100 && isset($_GET['data_view_item']) && $_GET['data_view_item'] != "") {
            $show = $_GET['data_view_item'];
        }
        $sort = "ORDER BY id_act DESC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterItemList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        if (isset($filter['sort']) && $filter['sort'] != "") {
            $sort = $filter['sort'];
        }        
        $select_fields = "inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code, inv_dvc.dvc_tech, inv_act.dvc_col as warna";
        
        $sql = "SELECT " . $select_fields . " " .
               "FROM inv_act " .
               "INNER JOIN inv_dvc ON inv_act.id_dvc = inv_dvc.id_dvc AND inv_dvc.dvc_tech = '" . $tech_type . "' ";
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code, inv_dvc.dvc_tech, inv_act.dvc_col as warna", "SELECT COUNT(*) as total", $sql);
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $main_query = $this->db->query($sql . $limit);

        $data = array();
        $data['query'] = $main_query;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        if ($tech_type === 'ecbs') {
            $data['tech'] = 'ecbs';
        } else {
            $data['tech'] = 'ecct';
        }
        return $data;
    }

    public function getDeviceStockApp($tech, $show = 20, $adddata = '', $sort = '') {
        $tech = strtolower($tech);
        $show_key = $tech === 'ecct' ? 'data_view_ecct' : 'data_view_ecbs';
        if ($show < 100 && isset($_GET[$show_key]) && $_GET[$show_key] != "") {
            $show = $_GET[$show_key];
        }
        $sort = "ORDER BY dvc.dvc_priority ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        $result_data = array();
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code, dvc.dvc_priority FROM inv_dvc dvc WHERE LOWER(dvc.dvc_tech) = '".$tech."' AND UPPER(dvc.dvc_type) = 'APP' ";
        $sql .= "AND dvc.status = 0 ";
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code, dvc.dvc_priority", "SELECT COUNT(*) as total", $sql);
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $devices_query = $this->db->query($sql . $limit);
        $warna_map = array(
            'Blue Navy' => 'Navy',
            'Navy' => 'Navy',
            'Maroon' => 'Maroon',
            'Army' => 'Army',
            'Green Army' => 'Army',
            'Black' => 'Black',
            'Grey' => 'Grey',
            'Gray' => 'Grey',
            'Dark Gray' => 'Dark Gray',
            'Dark Grey' => 'Dark Gray',
            'Custom' => 'Custom',
            '-' => 'Custom',
            '' => 'Custom',
        );
        if ($devices_query && $devices_query->num_rows() > 0) {
            foreach ($devices_query->result_array() as $device) {
                if ($tech === 'ecbs') {
                    // Ambil semua warna unik
                    $warna_sql = "SELECT DISTINCT dvc_col FROM inv_act WHERE id_dvc = ? AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                    $warna_query = $this->db->query($warna_sql, array($device['id_dvc']));
                    $warna_list = $warna_query && $warna_query->num_rows() > 0 ? $warna_query->result_array() : array(array('dvc_col' => '-'));
                    foreach ($warna_list as $warna_row) {
                        $warna_raw = $warna_row['dvc_col'] ? $warna_row['dvc_col'] : '-';
                        $warna = isset($warna_map[$warna_raw]) ? $warna_map[$warna_raw] : $warna_raw;
                        $device_data = $device;
                        $device_data['warna'] = $warna;
                        $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'Cus');
                        $total = 0;
                        foreach ($sizes as $size) {
                            $count_sql = "SELECT COUNT(*) as count FROM inv_act WHERE id_dvc = ? AND dvc_size = ? AND dvc_col = ? AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                            $count_query = $this->db->query($count_sql, array($device['id_dvc'], $size, $warna_raw));
                            $count = $count_query ? $count_query->row()->count : 0;
                            $device_data['size_' . strtolower($size)] = $count;
                            $total += $count;
                        }
                        $device_data['subtotal'] = $total;
                        $result_data[] = $device_data;
                    }
                } else {
                    $device_data = $device;
                    $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'Cus');
                    $total = 0;
                    foreach ($sizes as $size) {
                        $count_sql = "SELECT COUNT(*) as count FROM inv_act WHERE id_dvc = ? AND dvc_size = ? AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                        $count_query = $this->db->query($count_sql, array($device['id_dvc'], $size));
                        $count = $count_query ? $count_query->row()->count : 0;
                        $device_data['size_' . strtolower($size)] = $count;
                        $total += $count;
                    }
                    $device_data['subtotal'] = $total;
                    $result_data[] = $device_data;
                } 
            }
        }
        $data['data'] = $result_data;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        return $data;
    }

    public function getDeviceStockOsc($tech, $show = 20, $adddata = '', $sort = '') {
        $tech = strtolower($tech);
        $show_key = $tech === 'ecct' ? 'data_view_ecct' : 'data_view_ecbs';
        if ($show < 100 && isset($_GET[$show_key]) && $_GET[$show_key] != "") {
            $show = $_GET[$show_key];
        }
        $sort = "ORDER BY dvc.id_dvc ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code, dvc.dvc_tech, dvc.dvc_type, " .
            "(SELECT COUNT(*) FROM inv_act WHERE id_dvc = dvc.id_dvc AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00') AND dvc_qc = 'LN') as ln_count, " .
            "(SELECT COUNT(*) FROM inv_act WHERE id_dvc = dvc.id_dvc AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00') AND dvc_qc = 'DN') as dn_count " .
            "FROM inv_dvc dvc WHERE LOWER(dvc.dvc_tech) = '".$tech."' ".
            "AND UPPER(dvc.dvc_type) = 'OSC' AND dvc.status = 0";
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        $sql .= " " . $sort . " ";
        $count_sql = "SELECT COUNT(*) as total FROM inv_dvc dvc WHERE LOWER(dvc.dvc_tech) = '".$tech."' ".
                    "AND dvc.status = 0 AND UPPER(dvc.dvc_type) = 'OSC'";
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $main_query = $this->db->query($sql . $limit);
        $data['query'] = $main_query;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        return $data;
    }

    public function getDeviceTypes($dvc_tech = '') {
        $this->db->select('dvc_code');
        $this->db->distinct();
        $this->db->where('status', 0);
        
        if (!empty($dvc_tech)) {
            $this->db->where('dvc_tech', $dvc_tech);
        }
        
        $this->db->where('dvc_code IS NOT NULL');
        $this->db->where('dvc_code !=', '');
        $this->db->order_by('dvc_code', 'ASC');
        
        $query = $this->db->get('inv_dvc');
        return $query->result();
    }

    public function getCustom($col, $tab, $key, $val) {
        $sql = "SELECT " . $col . " ";
        $sql .= "FROM " . $tab . " ";
        $sql .= "WHERE " . $key . "='" . $val . "' ";
        $query = $this->db->query($sql);
        $data = $query->row_array();
        if ($col == "*") {
            return $data;
        } else {
            return $data[$col];
        }
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
            $inv_in_value = $user_provided_date_string . ' 00:00:00';
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
            $out_timestamp = $user_provided_date_string . ' 00:00:00';
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
            $move_timestamp = $user_provided_date_string . ' 00:00:00';
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

        // Validasi format yyyy-mm-dd
        if (!$this->_isValidYmdFormat($date_string)) {
            return $this->_response(false, 'Format tanggal tidak valid. Gunakan format yyyy-mm-dd.');
        }

        // Cek apakah tanggal tidak melebihi hari ini
        $today = date('Y-m-d');
        if ($date_string > $today) {
            return $this->_response(false, 'Tanggal tidak boleh melebihi hari ini (' . date('Y-m-d') . ').');
        }

        // Validasi tambahan berdasarkan type
        if ($type === 'move' && $inventory_item !== null) {
            // MOVE: Tanggal tidak boleh sebelum tanggal IN
            if (!empty($inventory_item->inv_in) && $inventory_item->inv_in !== '0000-00-00 00:00:00') {
                $inv_in_date = date('Y-m-d', strtotime($inventory_item->inv_in));
                if ($date_string < $inv_in_date) {
                    return $this->_response(false, 'Tanggal MOVE tidak boleh sebelum tanggal IN (' . date('Y-m-d', strtotime($inventory_item->inv_in)) . ').');
                }
            }
        } elseif ($type === 'out' && $inventory_item !== null) {
            // OUT: Tanggal tidak boleh sebelum tanggal IN dan MOVE
            if (!empty($inventory_item->inv_in) && $inventory_item->inv_in !== '0000-00-00 00:00:00') {
                $inv_in_date = date('Y-m-d', strtotime($inventory_item->inv_in));
                if ($date_string < $inv_in_date) {
                    return $this->_response(false, 'Tanggal OUT tidak boleh sebelum tanggal IN (' . date('Y-m-d', strtotime($inventory_item->inv_in)) . ').');
                }
            }
            
            // Cek tanggal MOVE jika ada
            if (!empty($inventory_item->inv_move) && $inventory_item->inv_move !== '0000-00-00 00:00:00') {
                $inv_move_date = date('Y-m-d', strtotime($inventory_item->inv_move));
                if ($date_string < $inv_move_date) {
                    return $this->_response(false, 'Tanggal OUT tidak boleh sebelum tanggal MOVE (' . date('Y-m-d', strtotime($inventory_item->inv_move)) . ').');
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
            return array('valid' => false, 'message' => 'Serial number harus 15 karakter');
        }

        $yy = substr($serial_number, 0, 2);
        $mm = strtoupper(substr($serial_number, 2, 1));
        $dd = substr($serial_number, 3, 2);

        $flag = strtoupper(substr($serial_number, 6, 1)); // 1 digit flag N/R

        $bulan_map = array(
            '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6,
            '7' => 7, '8' => 8, '9' => 9, '0' => 10, 'A' => 11, 'B' => 12
        );
        $tahun = 2000 + intval($yy);
        $bulan = isset($bulan_map[$mm]) ? $bulan_map[$mm] : 0;
        $tanggal = intval($dd);

        $now = getdate();
        $tahun_now = $now['year'];
        $bulan_now = $now['mon'];
        $tanggal_now = $now['mday'];

        if ($tahun > $tahun_now) {
            return array('valid' => false, 'message' => 'Tahun produksi serial number tidak boleh melebihi tahun sekarang');
        }

        if ($bulan < 1 || $bulan > 12) {
            return array('valid' => false, 'message' => 'Bulan produksi serial number tidak valid!');
        }
        if ($tahun == $tahun_now && $bulan > $bulan_now) {
            return array('valid' => false, 'message' => 'Bulan produksi serial number tidak boleh melebihi bulan sekarang!');
        }

        $max_tanggal = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        if ($tanggal < 1 || $tanggal > $max_tanggal) {
            return array('valid' => false, 'message' => 'Tanggal produksi serial number tidak valid!');
        }
        if ($tahun == $tahun_now && $bulan == $bulan_now && $tanggal > $tanggal_now) {
            return array('valid' => false, 'message' => 'Tanggal produksi serial number tidak boleh melebihi tanggal hari ini!');
        }

        if ($flag !== 'N' && $flag !== 'R') {
            return array('valid' => false, 'message' => 'Karakter ke-7 serial number harus N (New) atau R (Refurbish)!');
        }

        $char_5 = substr($serial_number, 5, 1);
        $char_7 = substr($serial_number, 7, 1);
        $char_8 = substr($serial_number, 8, 1);
        $device_code = $char_5 . $char_7 . $char_8;

        $size = null;
        $color = null;

        if ($char_5 === 'T') {
            $combined_78 = intval($char_7 . $char_8);
            if ($combined_78 <= 50 && strlen($serial_number) == 15) {
                $color = 'Dark Grey';
            }
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

    /**
     * Validasi format yyyy-mm-dd
     */
    private function _isValidYmdFormat($date_string) {
        $d = DateTime::createFromFormat('Y-m-d', $date_string);
        return $d && $d->format('Y-m-d') === $date_string;
    }

}