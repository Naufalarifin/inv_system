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
    
    public function get_inv_week_data($year = null, $month = null) {
        $this->db->select('*');
        $this->db->from('inv_week');
        
        if ($year) {
            $this->db->where('period_y', $year);
        }
        if ($month) {
            $this->db->where('period_m', $month);
        }
        
        $this->db->order_by('period_y DESC, period_m DESC, period_w ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Generate weekly periods with proper 5-day work week logic (Monday-Friday)
     * Period runs from 27th of previous month (08:00) to 26th of current month (17:00)
     */
    public function generate_weekly_periods($year, $month) {
        // Validate input
        if (!$year || !$month || $year < 2020 || $year > 2030 || $month < 1 || $month > 12) {
            throw new Exception('Invalid year or month provided');
        }
        
        // Clear existing data for this year/month
        $this->db->where('period_y', $year);
        $this->db->where('period_m', $month);
        $this->db->delete('inv_week');
        
        $periods = array();
        $week_number = 1;
        
        // Calculate the period start (27th of previous month at 08:00)
        if ($month == 1) {
            $prev_month = 12;
            $prev_year = $year - 1;
        } else {
            $prev_month = $month - 1;
            $prev_year = $year;
        }
        
        // Period boundaries
        $period_start = new DateTime("$prev_year-$prev_month-27 08:00:00");
        $period_end = new DateTime("$year-$month-26 17:00:00");
        
        // Find first Monday on or after period start
        $current_monday = clone $period_start;
        while ($current_monday->format('N') != 1) { // 1 = Monday
            $current_monday->add(new DateInterval('P1D'));
        }
        $current_monday->setTime(8, 0, 0);
        
        // Generate weekly periods (Monday to Friday only)
        while ($current_monday <= $period_end) {
            $week_start = clone $current_monday;
            $week_start->setTime(8, 0, 0);
            
            // Calculate Friday of the same week (Monday + 4 days = Friday)
            $week_end = clone $current_monday;
            $week_end->add(new DateInterval('P4D'));
            $week_end->setTime(17, 0, 0);
            
            // If week end goes beyond period end, adjust it but keep Friday logic
            if ($week_end > $period_end) {
                $week_end = clone $period_end;
                
                // If adjusted end is not a weekday, find last weekday before period end
                while ($week_end->format('N') > 5) { // 6=Saturday, 7=Sunday
                    $week_end->sub(new DateInterval('P1D'));
                }
                $week_end->setTime(17, 0, 0);
            }
            
            // Only create period if week_start is within the period bounds
            if ($week_start <= $period_end) {
                $data = array(
                    'date_start' => $week_start->format('Y-m-d H:i:s'),
                    'date_finish' => $week_end->format('Y-m-d H:i:s'),
                    'period_y' => $year,
                    'period_m' => $month,
                    'period_w' => $week_number
                );
                
                $this->db->insert('inv_week', $data);
                $data['id_week'] = $this->db->insert_id();
                $periods[] = $data;
                
                $week_number++;
            }
            
            // Move to next Monday (7 days later)
            $current_monday->add(new DateInterval('P7D'));
            
            // Safety break to prevent infinite loop
            if ($week_number > 10) break; // Max 10 weeks per month should be enough
        }
        
        return $periods;
    }
    
    /**
     * Update weekly period with proper time enforcement
     */
    public function update_inv_week($id_week, $date_start, $date_finish) {
        // Validate input
        if (!$id_week || !$date_start || !$date_finish) {
            return false;
        }
        
        try {
            // Parse the datetime and ensure proper time format
            $start_dt = new DateTime($date_start);
            $finish_dt = new DateTime($date_finish);
            
            // Validation: start must be before finish
            if ($start_dt >= $finish_dt) {
                throw new Exception('Start date must be before finish date');
            }
            
            // Validation: both dates should be weekdays (Monday-Friday)
            if ($start_dt->format('N') > 5 || $finish_dt->format('N') > 5) {
                throw new Exception('Dates must be weekdays (Monday-Friday)');
            }
            
            // Ensure start time is 08:00 and finish time is 17:00
            $start_dt->setTime(8, 0, 0);
            $finish_dt->setTime(17, 0, 0);
            
            $data = array(
                'date_start' => $start_dt->format('Y-m-d H:i:s'),
                'date_finish' => $finish_dt->format('Y-m-d H:i:s')
            );
            
            $this->db->where('id_week', intval($id_week));
            return $this->db->update('inv_week', $data);
            
        } catch (Exception $e) {
            // Log error for debugging
            log_message('error', 'Update inv_week failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if current date allows editing of a weekly period
     * Based on 27th-26th period logic
     */
    public function check_edit_permission($date_start, $date_finish) {
        try {
            $current_date = new DateTime();
            $start_date = new DateTime($date_start);
            $finish_date = new DateTime($date_finish);
            
            // Extract the period month and year from the finish date
            $period_month = intval($finish_date->format('m'));
            $period_year = intval($finish_date->format('Y'));
            
            // Calculate the actual period boundaries (27th to 26th)
            if ($period_month == 1) {
                $prev_month = 12;
                $prev_year = $period_year - 1;
            } else {
                $prev_month = $period_month - 1;
                $prev_year = $period_year;
            }
            
            $actual_start = new DateTime("$prev_year-$prev_month-27 08:00:00");
            $actual_end = new DateTime("$period_year-$period_month-26 17:00:00");
            
            return ($current_date >= $actual_start && $current_date <= $actual_end);
            
        } catch (Exception $e) {
            log_message('error', 'Check edit permission failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get working days count between two dates (excluding weekends)
     */
    private function getWorkingDays($start_date, $end_date) {
        $working_days = 0;
        $current = clone $start_date;
        
        while ($current <= $end_date) {
            // Check if current day is weekday (Monday=1 to Friday=5)
            if ($current->format('N') <= 5) {
                $working_days++;
            }
            $current->add(new DateInterval('P1D'));
        }
        
        return $working_days;
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