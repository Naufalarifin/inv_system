<?php
class Sql_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getFilterDeviceCalibrationList($show) {
        $filter = array();
        $filter['first'] = 0;
        $filter['all'] = "";
        $filter['sort'] = "";

        if (isset($_GET['p']) && $_GET['p'] > 1) {
            $filter['first'] = ($_GET['p'] - 1) * $show;
        }

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

        if (isset($_GET['sort_lt']) && $_GET['sort_lt'] != "") {
            if ($_GET['sort_lt'] == 'asc') {
                $filter['sort'] = "ORDER BY kai.dvc_rls ASC";
            } elseif ($_GET['sort_lt'] == 'desc') {
                $filter['sort'] = "ORDER BY kai.dvc_rls DESC";
            }
        }

        return $filter;
    }

    public function getFilterItemList($show) {
        $filter = array();
        $filter['first'] = 0;
        $filter['all'] = "";
        $filter['sort'] = "";

        if (isset($_GET['p']) && $_GET['p'] != "" && $_GET['p'] > 0) {
            $page = intval($_GET['p']);
            $filter['first'] = ($page - 1) * $show;
        }

        if (isset($_GET['key_activity']) && $_GET['key_activity'] != "") {
            $key = $this->db->escape_str($_GET['key_activity']);
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

        // Filter Device Type - Join dengan inv_dvc untuk mendapatkan dvc_type
        if (isset($_GET['dvc_type']) && $_GET['dvc_type'] != "") {
            $filter['all'] .= " AND inv_dvc.dvc_type = '" . $this->db->escape_str($_GET['dvc_type']) . "'";
        }

        if (isset($_GET['loc_move']) && $_GET['loc_move'] != "") {
            $filter['all'] .= " AND inv_act.loc_move = '" . $this->db->escape_str($_GET['loc_move']) . "'";
        }

        // Activity Date Filters
        // IN Date Filters
        if (isset($_GET['in_date_from']) && $_GET['in_date_from'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_in) >= '" . $this->db->escape_str($_GET['in_date_from']) . "'";
        }
        if (isset($_GET['in_date_to']) && $_GET['in_date_to'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_in) <= '" . $this->db->escape_str($_GET['in_date_to']) . "'";
        }

        // MOVE Date Filters
        if (isset($_GET['move_date_from']) && $_GET['move_date_from'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_move) >= '" . $this->db->escape_str($_GET['move_date_from']) . "'";
        }
        if (isset($_GET['move_date_to']) && $_GET['move_date_to'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_move) <= '" . $this->db->escape_str($_GET['move_date_to']) . "'";
        }

        // OUT Date Filters
        if (isset($_GET['out_date_from']) && $_GET['out_date_from'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_out) >= '" . $this->db->escape_str($_GET['out_date_from']) . "'";
        }
        if (isset($_GET['out_date_to']) && $_GET['out_date_to'] != "") {
            $filter['all'] .= " AND DATE(inv_act.inv_out) <= '" . $this->db->escape_str($_GET['out_date_to']) . "'";
        }

        if (isset($_GET['act_date_from']) && $_GET['act_date_from'] != "") {
            $filter['all'] .= " AND DATE(inv_act.act_date) >= '" . $this->db->escape_str($_GET['act_date_from']) . "'";
        }
        if (isset($_GET['act_date_to']) && $_GET['act_date_to'] != "") {
            $filter['all'] .= " AND DATE(inv_act.act_date) <= '" . $this->db->escape_str($_GET['act_date_to']) . "'";
        }
        $activity_filter = isset($_GET['activity']) ? $_GET['activity'] : '';

        switch($activity_filter) {
            case 'in-only':
                $filter['all'] .= " AND (inv_move IS NULL OR inv_move = '') AND (inv_out IS NULL OR inv_out = '') ";
                break;
            case 'in-stock':
                $filter['all'] .= " AND (inv_out IS NULL OR inv_out = '') ";
                break;
            case 'move':
                $filter['all'] .= " AND inv_in IS NOT NULL AND inv_in != '' AND inv_move IS NOT NULL AND inv_move != '' AND (inv_out IS NULL OR inv_out = '') ";
                break;   
            case 'out':
                $filter['all'] .= " AND inv_out IS NOT NULL AND inv_out != '' ";
                break;
            default:
                $filter['all'] .= '';
                break;
        }

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

    // METHOD BARU untuk filter ECCT
    public function getFilterEcctList($show) {
        $filter = array();
        $filter['first'] = 0;
        $filter['all'] = "";
        $filter['sort'] = "";

        if (isset($_GET['p']) && $_GET['p'] != "" && $_GET['p'] > 0) {
            $page = intval($_GET['p']);
            $filter['first'] = ($page - 1) * $show;
        }

        // Filter untuk pencarian umum (key_ecct) pada dvc_name atau dvc_code
        if (isset($_GET['key_ecct']) && $_GET['key_ecct'] != "") {
            $key = $this->db->escape_str($_GET['key_ecct']);
            $filter['all'] .= " AND (dvc.dvc_name LIKE '%" . $key . "%' OR dvc.dvc_code LIKE '%" . $key . "%')";
        }

        // Filter spesifik untuk Device Name (dvc_name_ecct)
        if (isset($_GET['dvc_name_ecct']) && $_GET['dvc_name_ecct'] != "") {
            $filter['all'] .= " AND dvc.dvc_name LIKE '%" . $this->db->escape_str($_GET['dvc_name_ecct']) . "%'";
        }

        // Filter spesifik untuk Device Code (dvc_code_ecct)
        if (isset($_GET['dvc_code_ecct']) && $_GET['dvc_code_ecct'] != "") {
            $filter['all'] .= " AND dvc.dvc_code LIKE '%" . $this->db->escape_str($_GET['dvc_code_ecct']) . "%'";
        }

        // Filter-filter berikut tidak relevan untuk query utama inv_dvc di getEcctAppData/getEcctOscData
        // dan telah dihapus dari sini untuk menghindari error SQL.
        // if (isset($_GET['dvc_size']) && $_GET['dvc_size'] != "") { ... }
        // if (isset($_GET['dvc_col']) && $_GET['dvc_col'] != "") { ... }
        // if (isset($_GET['dvc_qc']) && $_GET['dvc_qc'] != "") { ... }
        // if (isset($_GET['date_from']) && $_GET['date_from'] != "") { ... }
        // if (isset($_GET['date_to']) && $_GET['date_to'] != "") { ... }

        // Sortir juga tidak diperlukan di sini karena sudah diatur di Data_model.php
        // if (isset($_GET['sort_by']) && $_GET['sort_by'] != "") { ... }

        return $filter;
    }
}
