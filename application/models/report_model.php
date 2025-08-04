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
        $this->db->order_by('dvc_name', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function saveNeedsData($data) {
        return $this->db->insert('inv_needs', $data);
    }
    
    public function updateNeedsData($id, $data) {
        $this->db->where('id_needs', $id);
        return $this->db->update('inv_needs', $data);
    }
    
    public function getNeedsData($id_dvc, $dvc_size, $dvc_col, $dvc_qc) {
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $dvc_size);
        $this->db->where('dvc_col', $dvc_col);
        $this->db->where('dvc_qc', $dvc_qc);
        
        $query = $this->db->get('inv_needs');
        return $query->row_array();
    }
}
?>
