<?php
class Data_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
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
        
        $sql = "SELECT * FROM inv_act WHERE 1=1 ";
        
        if (isset($filter['all'])) {
            $sql .= " " . $filter['all'] . " ";
        }
        
        $sql .= " " . $sort . " ";

        $count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
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
?>
