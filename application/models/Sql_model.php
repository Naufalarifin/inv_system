<?php
class Sql_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // Existing function for device calibration
    public function getFilterDeviceCalibrationList($show) {
        $filter = array();
        $filter['first'] = 0;
        $filter['all'] = "";
        $filter['sort'] = "";
        
        // Handle pagination
        if (isset($_GET['p']) && $_GET['p'] > 1) {
            $filter['first'] = ($_GET['p'] - 1) * $show;
        }
        
        // Handle search and filters
        if (isset($_GET['key']) && $_GET['key'] != "") {
            $filter['all'] .= " AND (kai.dvc_code LIKE '%" . $_GET['key'] . "%' OR kai.dvc_sn LIKE '%" . $_GET['key'] . "%')";
        }
        if (isset($_GET['var1_status']) && $_GET['var1_status'] != "") {
            $filter['all'] .= " AND kai.var1_status = '" . $_GET['var1_status'] . "'";
        }
        if (isset($_GET['var2_status']) && $_GET['var2_status'] != "") {
            $filter['all'] .= " AND kai.var2_status = '" . $_GET['var2_status'] . "'";
        }
        if (isset($_GET['var3_status']) && $_GET['var3_status'] != "") {
            $filter['all'] .= " AND kai.var3_status = '" . $_GET['var3_status'] . "'";
        }
        if (isset($_GET['date_first']) && $_GET['date_first'] != "") {
            $filter['all'] .= " AND ka.added >= '" . $_GET['date_first'] . "'";
        }
        if (isset($_GET['date_last']) && $_GET['date_last'] != "") {
            $filter['all'] .= " AND ka.added <= '" . $_GET['date_last'] . "'";
        }
        
        // Handle sorting
        if (isset($_GET['sort_lt']) && $_GET['sort_lt'] != "") {
            if ($_GET['sort_lt'] == 'asc') {
                $filter['sort'] = "ORDER BY kai.dvc_rls ASC";
            } elseif ($_GET['sort_lt'] == 'desc') {
                $filter['sort'] = "ORDER BY kai.dvc_rls DESC";
            }
        }
        
        return $filter;
    }

    // NEW: Function for item list filtering
    // PERBAIKAN: Function for item list filtering
    public function getFilterItemList($show) {
        $filter = array();
        $filter['first'] = 0;
        $filter['all'] = "";
        $filter['sort'] = "";
        
        // PERBAIKAN: Handle pagination dengan perhitungan yang benar
        if (isset($_GET['p']) && $_GET['p'] != "" && $_GET['p'] > 0) {
            $page = intval($_GET['p']);
            $filter['first'] = ($page - 1) * $show;  // PERBAIKAN: Formula yang benar
        }
        
        // Handle search and filters dengan validasi yang lebih baik
        if (isset($_GET['key_item']) && $_GET['key_item'] != "") {
            $key = $this->db->escape_str($_GET['key_item']);
            $filter['all'] .= " AND (inv_act.dvc_sn LIKE '%" . $key . "%' OR inv_act.id_act LIKE '%" . $key . "%')";
        }
        if (isset($_GET['dvc_size']) && $_GET['dvc_size'] != "") {
            $filter['all'] .= " AND inv_act.dvc_size = '" . $this->db->escape_str($_GET['dvc_size']) . "'";
        }
        if (isset($_GET['dvc_col']) && $_GET['dvc_col'] != "") {
            $filter['all'] .= " AND inv_act.dvc_col = '" . $this->db->escape_str($_GET['dvc_col']) . "'";
        }
        if (isset($_GET['dvc_qc']) && $_GET['dvc_qc'] != "") {
            $filter['all'] .= " AND inv_act.dvc_qc = '" . $this->db->escape_str($_GET['dvc_qc']) . "'";
        }
        if (isset($_GET['date_from']) && $_GET['date_from'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_in) >= '" . $this->db->escape_str($_GET['date_from']) . "'";
        }
        if (isset($_GET['date_to']) && $_GET['date_to'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_in) <= '" . $this->db->escape_str($_GET['date_to']) . "'";
        }
        if (isset($_GET['loc_move']) && $_GET['loc_move'] != "") {
            $filter['all'] .= " AND inv_act.loc_move = '" . $this->db->escape_str($_GET['loc_move']) . "'";
        }
        
        // Handle sorting dengan validasi
        if (isset($_GET['sort_by']) && $_GET['sort_by'] != "") {
            switch ($_GET['sort_by']) {
                case 'id_act_asc':
                    $filter['sort'] = "ORDER BY inv_act.id_act ASC";
                    break;
                case 'id_act_desc':
                    $filter['sort'] = "ORDER BY inv_act.id_act DESC";
                    break;
                case 'dvc_sn_asc':
                    $filter['sort'] = "ORDER BY inv_act.dvc_sn ASC";
                    break;
                case 'dvc_sn_desc':
                    $filter['sort'] = "ORDER BY inv_act.dvc_sn DESC";
                    break;
                default:
                    $filter['sort'] = "ORDER BY inv_act.id_act DESC";
                    break;
            }
        }
        
        return $filter;
    }
}
?>
