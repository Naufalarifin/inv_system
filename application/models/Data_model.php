<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get device calibration list
     * @param int $limit - limit results
     * @return array - with query and page data
     */
    public function getDeviceCalibrationList($limit = null) {
        $this->load->model("sql_model");
        
        $show = $limit ?: 20;
        $filter = $this->sql_model->getFilterDeviceCalibrationList($show);

        if ($show >= 999999) {
            $filter['first'] = 0;
        }

        $sort = "ORDER BY added DESC ";
        if (isset($filter['sort']) && $filter['sort'] != "") {
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

        $limit_sql = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $data['query'] = $this->db->query($sql . $limit_sql);
        $data['page']['sum'] = $this->db->query($sql)->num_rows();
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        
        return $data;
    }

    /**
     * Get all items for inventory
     * @param int $limit - limit results
     * @return array - with query and page data
     */
    public function getAllItem($limit = null) {
        $this->load->model("sql_model");
        
        $show = $limit ?: 20;
        $filter = $this->sql_model->getFilterItemList($show);

        if ($show >= 999999) {
            $filter['first'] = 0;
        }

        $sort = "ORDER BY id_act DESC ";
        if (isset($filter['sort']) && $filter['sort'] != "") {
            $sort = $filter['sort'];
        }
        
        // Query dengan JOIN untuk mendapatkan device name dan code
        $sql = "SELECT inv_act.*, inv_dvc.dvc_name, inv_dvc.dvc_code 
                FROM inv_act 
                LEFT JOIN inv_dvc ON inv_act.id_dvc = inv_dvc.id_dvc 
                WHERE 1=1 ";
        
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        
        $sql .= " " . $sort . " ";

        $limit_sql = "LIMIT " . $filter['first'] . ", " . $show . " ";
        $data['query'] = $this->db->query($sql . $limit_sql);
        $data['page']['sum'] = $this->db->query($sql)->num_rows();
        $data['page']['show'] = $show;
        $data['page']['first'] = $filter['first'];
        
        return $data;
    }

    /**
     * Get custom data
     * @param string $col - column to select
     * @param string $tab - table name
     * @param string $key - key field
     * @param string $val - key value
     * @return mixed
     */
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

    /**
     * Get location data
     * @return array
     */
    public function getLocations() {
        $this->db->order_by('location_name', 'ASC');
        $query = $this->db->get('location');
        return $query->result_array();
    }

    /**
     * Get device types
     * @return array
     */
    public function getDeviceTypes() {
        $this->db->distinct();
        $this->db->select('dvc_type');
        $this->db->where('dvc_type IS NOT NULL');
        $this->db->where('dvc_type !=', '');
        $this->db->order_by('dvc_type', 'ASC');
        $query = $this->db->get('inv_dvc');
        return $query->result_array();
    }

    /**
     * Get devices by type
     * @param string $type - device type
     * @return array
     */
    public function getDevicesByType($type) {
        $this->db->where('dvc_type', $type);
        $this->db->order_by('dvc_name', 'ASC');
        $query = $this->db->get('inv_dvc');
        return $query->result_array();
    }

    /**
     * Get device by ID
     * @param int $id - device ID
     * @return array
     */
    public function getDeviceById($id) {
        $this->db->where('id_dvc', $id);
        $query = $this->db->get('inv_dvc');
        return $query->row_array();
    }

    /**
     * Get device by serial number
     * @param string $sn - serial number
     * @return array
     */
    public function getDeviceBySN($sn) {
        $this->db->where('dvc_sn', $sn);
        $query = $this->db->get('inv_dvc');
        return $query->row_array();
    }

    /**
     * Get inventory activity by ID
     * @param int $id - activity ID
     * @return array
     */
    public function getInventoryActivityById($id) {
        $this->db->select('ia.*, id.dvc_name, id.dvc_type, id.dvc_code, l.location_name');
        $this->db->from('inv_act ia');
        $this->db->join('inv_dvc id', 'ia.id_dvc = id.id_dvc', 'left');
        $this->db->join('location l', 'ia.loc_move = l.id_location', 'left');
        $this->db->where('ia.id_act', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get admin users
     * @return array
     */
    public function getAdmins() {
        $this->db->distinct();
        $this->db->select('adm_in as admin');
        $this->db->from('inv_act');
        $this->db->where('adm_in IS NOT NULL');
        $this->db->where('adm_in !=', '');
        $this->db->union('SELECT adm_out as admin FROM inv_act WHERE adm_out IS NOT NULL AND adm_out != ""');
        $this->db->union('SELECT adm_move as admin FROM inv_act WHERE adm_move IS NOT NULL AND adm_move != ""');
        $this->db->union('SELECT adm_rls as admin FROM inv_act WHERE adm_rls IS NOT NULL AND adm_rls != ""');
        $this->db->order_by('admin', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // METHOD untuk ECCT APP - DENGAN ATURAN inv_out KOSONG
    public function getEcctAppData($show = 20, $adddata = "", $sort = "") {
        if ($show < 100 && isset($_GET['data_view_ecct']) && $_GET['data_view_ecct'] != "") {
            $show = $_GET['data_view_ecct'];
        }

        $sort = "ORDER BY dvc.dvc_name ASC ";
        $this->load->model("sql_model");
        $filter = $this->sql_model->getFilterEcctList($show);

        if ($show >= 999999) {
            $filter['first'] = 0;
        }

        // Get devices dengan dvc_tech = 'ecct' dan dvc_type = 'APP' SAJA
        $sql = "SELECT dvc.id_dvc, dvc.dvc_name, dvc.dvc_code ";
        $sql .= "FROM inv_dvc dvc ";
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecct' AND UPPER(dvc.dvc_type) = 'APP' ";
        
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

        $sort = "ORDER BY dvc.dvc_name ASC ";
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
        $sql .= "WHERE LOWER(dvc.dvc_tech) = 'ecct' AND UPPER(dvc.dvc_type) = 'OSC' ";
        
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
}
?>