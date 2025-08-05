<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getReportData($tech, $type) {
        $this->db->select('id_dvc, dvc_code, dvc_name, status');
        $this->db->from('inv_dvc');
        $this->db->where('status', '0');
        $this->db->where('dvc_tech', $tech);
        $this->db->where('dvc_type', $type);
        $this->db->order_by('dvc_priority', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function saveNeedsData($data) {
        // Validasi id_dvc
        if (empty($data['id_dvc']) || $data['id_dvc'] == '0') {
            return false;
        }
        
        // Cek keberadaan id_dvc di inv_dvc
        $this->db->where('id_dvc', $data['id_dvc']);
        $exists = $this->db->get('inv_dvc')->num_rows();
        
        if ($exists == 0) {
            return false;
        }
        
        // id_needs akan di-generate otomatis oleh database (AUTO_INCREMENT)
        // dvc_col seharusnya sudah disanitasi dari frontend
        return $this->db->insert('inv_needs', $data);
    }
    
    public function updateNeedsData($id, $data) {
        $this->db->where('id_needs', $id);
        if (isset($data['id_needs'])) {
            unset($data['id_needs']);
        }
        // dvc_col seharusnya sudah disanitasi dari frontend
        return $this->db->update('inv_needs', $data);
    }
    
    public function getNeedsData($id_dvc, $dvc_size, $dvc_col, $dvc_qc) {
        // dvc_col yang diterima di sini seharusnya sudah disanitasi dari frontend
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $dvc_size);
        $this->db->where('dvc_col', $dvc_col);
        $this->db->where('dvc_qc', $dvc_qc);
        
        $query = $this->db->get('inv_needs');
        return $query->row_array();
    }
    
    public function deleteNeedsData($id_dvc, $dvc_size, $dvc_col, $dvc_qc) {
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $dvc_size);
        $this->db->where('dvc_col', $dvc_col);
        $this->db->where('dvc_qc', $dvc_qc);
        
        return $this->db->delete('inv_needs');
    }
    
    public function getExistingNeedsData($tech, $type) {
        $this->db->select('n.id_dvc, n.dvc_size, n.dvc_col, n.dvc_qc, n.needs_qty');
        $this->db->from('inv_needs n');
        $this->db->join('inv_dvc d', 'n.id_dvc = d.id_dvc');
        $this->db->where('d.dvc_tech', $tech);
        $this->db->where('d.dvc_type', $type);
        $this->db->where('d.status', '0');
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        // Convert to associative array for easy lookup
        $needs_data = array();
        foreach ($result as $row) {
            // Sanitize dvc_col from database before creating the key for lookup
            // This handles cases where dvc_col might be stored with spaces (e.g., "Dark Gray")
            $sanitized_db_color = str_replace(' ', '-', strtolower($row['dvc_col']));
            $key = $row['id_dvc'] . '_' . $row['dvc_size'] . '_' . $sanitized_db_color . '_' . $row['dvc_qc'];
            $needs_data[$key] = $row['needs_qty'];
        }
        
        return $needs_data;
    }
}
?>
