<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get history by activity type
     * @param string $activity_type - 'ALL', 'IN', 'OUT', 'MOVE', 'RELEASE'
     * @param array $filters - additional filters
     * @param int $limit - limit results
     * @param int $offset - offset for pagination
     * @return array
     */
    public function get_history_by_type($activity_type = 'ALL', $filters = array(), $limit = null, $offset = 0) {
        $sql_parts = array();
        
        // Build query parts based on activity type
        if ($activity_type == 'ALL' || $activity_type == 'IN') {
            $sql_parts[] = "
            (SELECT 
                ia.id_act,
                ia.dvc_sn,
                ia.dvc_size,
                ia.inv_in as tanggal,
                ia.adm_in as admin,
                'IN' as activity_type,
                ia.loc_move as location,
                id.dvc_name,
                id.dvc_type,
                id.dvc_code
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc
            )";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'OUT') {
            $sql_parts[] = "
            (SELECT 
                ia.id_act,
                ia.dvc_sn,
                ia.dvc_size,
                ia.inv_out as tanggal,
                ia.adm_out as admin,
                'OUT' as activity_type,
                ia.loc_move as location,
                id.dvc_name,
                id.dvc_type,
                id.dvc_code
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc
            )";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'MOVE') {
            $sql_parts[] = "
            (SELECT 
                ia.id_act,
                ia.dvc_sn,
                ia.dvc_size,
                ia.inv_move as tanggal,
                ia.adm_move as admin,
                'MOVE' as activity_type,
                ia.loc_move as location,
                id.dvc_name,
                id.dvc_type,
                id.dvc_code
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc
            )";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'RELEASE') {
            $sql_parts[] = "
            (SELECT 
                ia.id_act,
                ia.dvc_sn,
                ia.dvc_size,
                ia.inv_rls as tanggal,
                ia.adm_rls as admin,
                'RELEASE' as activity_type,
                ia.loc_move as location,
                id.dvc_name,
                id.dvc_type,
                id.dvc_code
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc
            )";
        }
        
        // Combine all parts with UNION ALL
        $sql = "SELECT * FROM (" . implode(' UNION ALL ', $sql_parts) . ") as history_data ORDER BY tanggal DESC";
        error_log('SQL: ' . $sql);

        // Apply additional filters
        if (!empty($filters)) {
            // Search across multiple fields
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search_term = $this->db->escape_like_str($filters['search']);
                $sql .= " AND (dvc_sn LIKE '%" . $search_term . "%' 
                           OR dvc_name LIKE '%" . $search_term . "%' 
                           OR admin LIKE '%" . $search_term . "%')";
            }
            
            if (isset($filters['dvc_sn']) && !empty($filters['dvc_sn'])) {
                $sql .= " AND dvc_sn LIKE '%" . $this->db->escape_like_str($filters['dvc_sn']) . "%'";
            }
            
            if (isset($filters['dvc_name']) && !empty($filters['dvc_name'])) {
                $sql .= " AND dvc_name LIKE '%" . $this->db->escape_like_str($filters['dvc_name']) . "%'";
            }
            
            if (isset($filters['admin']) && !empty($filters['admin'])) {
                $sql .= " AND admin LIKE '%" . $this->db->escape_like_str($filters['admin']) . "%'";
            }
            
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $sql .= " AND DATE(tanggal) >= '" . $this->db->escape_str($filters['date_from']) . "'";
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $sql .= " AND DATE(tanggal) <= '" . $this->db->escape_str($filters['date_to']) . "'";
            }
        }

        // Add limit if specified
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);
        }

        $query = $this->db->query($sql);
        error_log('RESULT: ' . print_r($query->result_array(), true));
        return $query->result_array();
    }

    /**
     * Get formatted history for view display
     * @param string $activity_type
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_formatted_history($activity_type = 'ALL', $filters = array(), $limit = 20, $offset = 0) {
        $raw_data = $this->get_history_by_type($activity_type, $filters, $limit, $offset);
        $formatted_data = array();
        $counter = $offset + 1;

        foreach ($raw_data as $row) {
            $formatted_data[] = array(
                'nomor' => $counter++,
                'tanggal' => $row['tanggal'], // Keep original date for view formatting
                'admin' => $row['admin'] ? $row['admin'] : '-',
                'dvc_sn' => $row['dvc_sn'],
                'dvc_name' => $row['dvc_name'],
                'dvc_size' => $row['dvc_size'],
                'activity_type' => $row['activity_type'],
                'location' => $row['location'] ? $row['location'] : '-'
            );
        }

        return $formatted_data;
    }

    /**
     * Count history records by type for pagination
     * @param string $activity_type
     * @param array $filters
     * @return int
     */
    public function count_history_by_type($activity_type = 'ALL', $filters = array()) {
        $sql_parts = array();
        
        if ($activity_type == 'ALL' || $activity_type == 'IN') {
            $sql_parts[] = "
            (SELECT ia.id_act, ia.dvc_sn, id.dvc_name, ia.adm_in as admin, ia.inv_in as tanggal
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc 
            WHERE ia.inv_in IS NOT NULL 
            AND ia.inv_in != '0000-00-00 00:00:00' 
            AND ia.inv_in != '')";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'OUT') {
            $sql_parts[] = "
            (SELECT ia.id_act, ia.dvc_sn, id.dvc_name, ia.adm_out as admin, ia.inv_out as tanggal
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc 
            WHERE ia.inv_out IS NOT NULL 
            AND ia.inv_out != '0000-00-00 00:00:00' 
            AND ia.inv_out != '')";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'MOVE') {
            $sql_parts[] = "
            (SELECT ia.id_act, ia.dvc_sn, id.dvc_name, ia.adm_move as admin, ia.inv_move as tanggal
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc 
            WHERE ia.inv_move IS NOT NULL 
            AND ia.inv_move != '0000-00-00 00:00:00' 
            AND ia.inv_move != '')";
        }
        
        if ($activity_type == 'ALL' || $activity_type == 'RELEASE') {
            $sql_parts[] = "
            (SELECT ia.id_act, ia.dvc_sn, id.dvc_name, ia.adm_rls as admin, ia.inv_rls as tanggal
            FROM inv_act ia 
            LEFT JOIN inv_dvc id ON ia.id_dvc = id.id_dvc 
            WHERE ia.inv_rls IS NOT NULL 
            AND ia.inv_rls != '0000-00-00 00:00:00' 
            AND ia.inv_rls != '')";
        }
        
        $sql = "SELECT COUNT(*) as total FROM (" . implode(' UNION ALL ', $sql_parts) . ") as count_data WHERE 1=1";
        
        // Apply same filters as main query
        if (!empty($filters)) {
            // Search across multiple fields
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search_term = $this->db->escape_like_str($filters['search']);
                $sql .= " AND (dvc_sn LIKE '%" . $search_term . "%' 
                           OR dvc_name LIKE '%" . $search_term . "%' 
                           OR admin LIKE '%" . $search_term . "%')";
            }
            
            if (isset($filters['dvc_sn']) && !empty($filters['dvc_sn'])) {
                $sql .= " AND dvc_sn LIKE '%" . $this->db->escape_like_str($filters['dvc_sn']) . "%'";
            }
            
            if (isset($filters['dvc_name']) && !empty($filters['dvc_name'])) {
                $sql .= " AND dvc_name LIKE '%" . $this->db->escape_like_str($filters['dvc_name']) . "%'";
            }
            
            if (isset($filters['admin']) && !empty($filters['admin'])) {
                $sql .= " AND admin LIKE '%" . $this->db->escape_like_str($filters['admin']) . "%'";
            }
            
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $sql .= " AND DATE(tanggal) >= '" . $this->db->escape_str($filters['date_from']) . "'";
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $sql .= " AND DATE(tanggal) <= '" . $this->db->escape_str($filters['date_to']) . "'";
            }
        }

        $query = $this->db->query($sql);
        $result = $query->row();
        return $result ? $result->total : 0;
    }

    // ========== SUBFITUR METHODS ==========

    /**
     * SUBFITUR 1: All History
     */
    public function get_all_history($filters = array(), $limit = 20, $offset = 0) {
        $activity_type = 'ALL';
        if (isset($filters['activity_type']) && !empty($filters['activity_type'])) {
            $activity_type = $filters['activity_type'];
        }
        return $this->get_formatted_history($activity_type, $filters, $limit, $offset);
    }

    /**
     * SUBFITUR 2: IN History only
     */
    public function get_in_history($filters = array(), $limit = 20, $offset = 0) {
        return $this->get_formatted_history('IN', $filters, $limit, $offset);
    }

    /**
     * SUBFITUR 3: MOVE History only
     */
    public function get_move_history($filters = array(), $limit = 20, $offset = 0) {
        return $this->get_formatted_history('MOVE', $filters, $limit, $offset);
    }

    /**
     * SUBFITUR 4: OUT History only
     */
    public function get_out_history($filters = array(), $limit = 20, $offset = 0) {
        return $this->get_formatted_history('OUT', $filters, $limit, $offset);
    }

    /**
     * SUBFITUR 5: RELEASE History only
     */
    public function get_release_history($filters = array(), $limit = 20, $offset = 0) {
        return $this->get_formatted_history('RELEASE', $filters, $limit, $offset);
    }

    // ========== COUNT METHODS FOR PAGINATION ==========

    public function count_all_history($filters = array()) {
        $activity_type = 'ALL';
        if (isset($filters['activity_type']) && !empty($filters['activity_type'])) {
            $activity_type = $filters['activity_type'];
        }
        return $this->count_history_by_type($activity_type, $filters);
    }

    public function count_in_history($filters = array()) {
        return $this->count_history_by_type('IN', $filters);
    }

    public function count_move_history($filters = array()) {
        return $this->count_history_by_type('MOVE', $filters);
    }

    public function count_out_history($filters = array()) {
        return $this->count_history_by_type('OUT', $filters);
    }

    public function count_release_history($filters = array()) {
        return $this->count_history_by_type('RELEASE', $filters);
    }

    /**
     * Get statistics for all activity types
     * @return array
     */
    public function get_activity_stats() {
        return array(
            'total_all' => $this->count_all_history(),
            'total_in' => $this->count_in_history(),
            'total_out' => $this->count_out_history(),
            'total_move' => $this->count_move_history(),
            'total_release' => $this->count_release_history()
        );
    }
}
?>