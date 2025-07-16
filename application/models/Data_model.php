<?php
class Data_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ... existing methods remain the same ...

    public function getDeviceCalibrationList($show = 20, $adddata = "", $sort = "") {
        if ($show < 100 && isset($_GET['data_view']) && $_GET['data_view'] != "") {
            $show = $_GET['data_view'];
        }
        $sort = "ORDER BY added DESC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterDeviceCalibrationList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        if ($filter['sort'] != "") {
            $sort = $filter['sort'];
        }
        $sql = "SELECT * ";
        $sql .= ", (SELECT kodeAlat FROM data_alat WHERE idAlat=kai.dvc_id_alat) AS dvc_code ";
        $sql .= ", (SELECT namaAlat FROM data_alat WHERE idAlat=kai.dvc_id_alat) AS dvc_name ";
        $sql .= ", 'Dummy Dummy' AS client_name ";
        $sql .= ", '0101/0101' AS client_nip ";
        $sql .= "FROM kal_alat ka, kal_alat_item kai ";
        $sql .= "WHERE ka.id=kai.id_kal ";
        $sql .= " " . $filter['all'] . " ";
        $sql .= " " . $sort . " ";
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $data['query'] = $this->db->query($sql . $limit);
        $data['page']['sum'] = $this->db->query($sql)->num_rows();
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        return $data;
    }

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

        // Kirim hasil query ke view sebagai $query
        $data = array();
        $data['query'] = $main_query;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        return $data;
    }

    // Ambil semua item, hanya yang dvc_tech = 'ecct'
    public function getAllItemEcctOnly($show = 20, $adddata = "", $sort = "") {
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
        // Pastikan hanya data dengan dvc_tech = 'ecct' yang diambil
        $sql = "SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code, inv_dvc.dvc_tech ".
                "FROM inv_act ".
                "INNER JOIN inv_dvc ON inv_act.id_dvc = inv_dvc.id_dvc AND inv_dvc.dvc_tech = 'ecct' ";
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code, inv_dvc.dvc_tech", "SELECT COUNT(*) as total", $sql);
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

    // METHOD untuk ECCT APP - DENGAN ATURAN inv_out KOSONG
    public function getEcctAppData($show = 20, $adddata = "", $sort = "") {
        // Tampilkan semua data dalam satu halaman
        // $show = 1000; // Ini dikomentari agar bisa diatur dari parameter atau GET
        if ($show < 100 && isset($_GET['data_view_ecct']) && $_GET['data_view_ecct'] != "") {
            $show = $_GET['data_view_ecct'];
        }
        $sort = "ORDER BY dvc.id_dvc ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        // Get devices dengan dvc_tech = 'ecct' dan dvc_type = 'APP' SAJA
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code ";
        $sql .= "FROM inv_dvc dvc ";
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecct' AND UPPER(dvc.dvc_type) = 'APP' AND dvc.status = 0 ";
                if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
                $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code", "SELECT COUNT(*) as total", $sql);
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $devices_query = $this->db->query($sql . $limit);

        // Untuk setiap device, hitung jumlah per ukuran DENGAN KONDISI inv_out KOSONG
        $result_data = array();
        if ($devices_query && $devices_query->num_rows() > 0) {
            foreach ($devices_query->result_array() as $device) {
                $device_data = $device;

                // Hitung jumlah per ukuran dengan kondisi inv_out kosong
                $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'Cus');
                $total = 0;

                foreach ($sizes as $size) {
                    // Query dengan kondisi inv_out kosong/null
                    $count_sql = "SELECT COUNT(*) as count FROM inv_act
                                     WHERE id_dvc = ?
                                     AND dvc_size = ?
                                     AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                    $count_query = $this->db->query($count_sql, array($device['id_dvc'], $size));
                    $count = $count_query ? $count_query->row()->count : 0;

                    $device_data['size_' . strtolower($size)] = $count;
                    $total += $count;
                }

                $device_data['subtotal'] = $total;
                $result_data[] = $device_data;
            }
        }

        $data['data'] = $result_data;
        $data['page']['sum'] = $total_records;
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];

        return $data;
    }

    // METHOD untuk ECCT OSC - DENGAN ATURAN inv_out KOSONG
    public function getEcctOscData($show = 20, $adddata = "", $sort = "") {
        if ($show < 100 && isset($_GET['data_view_ecct']) && $_GET['data_view_ecct'] != "") {
            $show = $_GET['data_view_ecct'];
        }
        $sort = "ORDER BY dvc.id_dvc ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show);
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        // Get devices dengan dvc_tech = 'ecct' dan dvc_type = 'OSC' SAJA
        // DENGAN KONDISI inv_out KOSONG
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code, dvc.dvc_tech, dvc.dvc_type, ";
        $sql .= "(SELECT COUNT(*) FROM inv_act
                    WHERE id_dvc = dvc.id_dvc
                    AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')
                ) as total_count ";
        $sql .= "FROM inv_dvc dvc ";
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecct' AND UPPER(dvc.dvc_type) = 'OSC' AND dvc.status = 0 ";
                if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
                $sql .= " " . $sort . " ";
        $count_sql = "SELECT COUNT(*) as total FROM inv_dvc dvc WHERE LOWER(dvc.dvc_tech) = 'ecct' AND UPPER(dvc.dvc_type) = 'OSC'";
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

    // METHOD untuk ECBS APP - DENGAN ATURAN inv_out KOSONG
    public function getEcbsAppData($show = 20, $adddata = "", $sort = "") {
        // Tampilkan semua data dalam satu halaman
            if ($show < 100 && isset($_GET['data_view_ecbs']) && $_GET['data_view_ecbs'] != "") {
            $show = $_GET['data_view_ecbs'];
        }
        $sort = "ORDER BY dvc.dvc_name ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show); // gunakan filter yang sama
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        // Get devices dengan dvc_tech = 'ecbs' dan dvc_type = 'APP' SAJA
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code ";
        $sql .= "FROM inv_dvc dvc ";
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecbs' AND UPPER(dvc.dvc_type) = 'APP' ";
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        $sql .= " " . $sort . " ";
        $count_sql = str_replace("SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code", "SELECT COUNT(*) as total", $sql);
        $count_query = $this->db->query($count_sql);
        $total_records = $count_query ? $count_query->row()->total : 0;
        $limit = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $devices_query = $this->db->query($sql . $limit);

        // Untuk setiap device, ambil semua warna unik dari inv_act yang belum keluar
        $result_data = array();
        if ($devices_query && $devices_query->num_rows() > 0) {
            foreach ($devices_query->result_array() as $device) {
                // Ambil semua warna unik
                $warna_sql = "SELECT DISTINCT dvc_col FROM inv_act
                              WHERE id_dvc = ?
                              AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                $warna_query = $this->db->query($warna_sql, array($device['id_dvc']));
                $warna_list = $warna_query && $warna_query->num_rows() > 0 ? $warna_query->result_array() : [['dvc_col' => '-']];

                foreach ($warna_list as $warna_row) {
                    $warna = $warna_row['dvc_col'] ? $warna_row['dvc_col'] : '-';
                    // Mapping warna ke format CSS-friendly dan konsisten
                    $warna_map = [
                        'Dark Gray' => 'Dark Grey',
                        'Black' => 'Black',
                        'Grey' => 'Grey',
                        'Blue Navy' => 'Navy',
                        'Army' => 'Army',
                        'Maroon' => 'Maroon',
                        'Custom' => 'Custom',
                        '-' => 'none'
                    ];
                    // Pastikan semua abu-abu jadi "Grey" (bukan "Gray")
                    if ($warna === 'Dark Gray') $warna = 'Dark Grey';
                    if ($warna === 'Gray') $warna = 'Grey';
                    $warna_css = isset($warna_map[$warna]) ? $warna_map[$warna] : strtolower(str_replace(' ', '_', $warna));

                    $device_data = $device;
                    $device_data['warna'] = $warna;
                    $device_data['warna_css'] = $warna_css;

                    // Hitung jumlah per ukuran untuk warna ini
                    $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'Cus');
                    $total = 0;
                    foreach ($sizes as $size) {
                        $count_sql = "SELECT COUNT(*) as count FROM inv_act
                                     WHERE id_dvc = ?
                                     AND dvc_size = ?
                                     AND dvc_col = ?
                                     AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                        $count_query = $this->db->query($count_sql, array($device['id_dvc'], $size, $warna));
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

    // METHOD untuk ECBS OSC - DENGAN ATURAN inv_out KOSONG
    public function getEcbsOscData($show = 20, $adddata = "", $sort = "") {
        if ($show < 100 && isset($_GET['data_view_ecbs']) && $_GET['data_view_ecbs'] != "") {
            $show = $_GET['data_view_ecbs'];
        }
        $sort = "ORDER BY dvc.dvc_name ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show); // gunakan filter yang sama
        if ($show >= 999999) {
            $filter['first'] = 0;
        }
        // Get devices dengan dvc_tech = 'ecbs' dan dvc_type = 'OSC' SAJA
        // DENGAN KONDISI inv_out KOSONG
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code, dvc.dvc_tech, dvc.dvc_type, ";
        $sql .= "(SELECT COUNT(*) FROM inv_act
                    WHERE id_dvc = dvc.id_dvc
                    AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')
                ) as total_count ";
        $sql .= "FROM inv_dvc dvc ";
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecbs' AND UPPER(dvc.dvc_type) = 'OSC' ";
                if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
                $sql .= " " . $sort . " ";
        $count_sql = "SELECT COUNT(*) as total FROM inv_dvc dvc WHERE LOWER(dvc.dvc_tech) = 'ecbs' AND UPPER(dvc.dvc_type) = 'OSC'";
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
}
