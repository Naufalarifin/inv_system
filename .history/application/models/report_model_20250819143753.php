<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getReportData($tech, $type, $filters = array()) {
        $qb = $this->buildDevicesBaseQuery($tech, $type);
        $qb->select('id_dvc, dvc_code, dvc_name, dvc_tech, dvc_type, status');

        // Apply filters
        if (isset($filters['device_search']) && $filters['device_search']) {
            $search_term = $this->db->escape_like_str($filters['device_search']);
            $qb->group_start()
               ->like('dvc_name', $search_term)
               ->or_like('dvc_code', $search_term)
               ->group_end();
        }

        $qb->order_by('dvc_priority', 'ASC');
        $query = $qb->get();
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

        $result = $this->db->insert('inv_needs', $data);
        return $result;
    }
    
    public function updateNeedsData($id, $data) {
        $this->db->where('id_needs', $id);
        if (isset($data['id_needs'])) {
            unset($data['id_needs']);
        }
        
        // Store color exactly as provided (VOH uses Display Names, OSC may be ' ')
        
        $result = $this->db->update('inv_needs', $data);
        return $result;
    }
    
    public function getNeedsData($id_dvc, $dvc_size, $dvc_col, $dvc_qc) {
        // Match color exactly as stored (VOH uses Display Names, OSC may be ' ')
        $sanitized_dvc_col = $dvc_col;
        
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $dvc_size);
        $this->db->where('dvc_col', $sanitized_dvc_col);
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
        $weeks = $query->result_array();

        // Augment with has_report flag per id_week to drive UI edit/delete availability
        if (!empty($weeks)) {
            $weekIds = array();
            foreach ($weeks as $w) {
                if (isset($w['id_week'])) {
                    $weekIds[] = intval($w['id_week']);
                }
            }

            if (!empty($weekIds)) {
                $this->db->select('id_week, COUNT(*) as cnt');
                $this->db->from('inv_report');
                $this->db->where_in('id_week', $weekIds);
                $this->db->group_by('id_week');
                $rep = $this->db->get()->result_array();
                $hasMap = array();
                foreach ($rep as $row) {
                    $hasMap[intval($row['id_week'])] = intval($row['cnt']) > 0 ? 1 : 0;
                }
                foreach ($weeks as &$w) {
                    $id = intval($w['id_week']);
                    $w['has_report'] = isset($hasMap[$id]) ? $hasMap[$id] : 0;
                }
                unset($w);
            }
        }

        return $weeks;
    }
    

    public function periods_exist($year, $month) {
        $this->db->where('period_y', $year);
        $this->db->where('period_m', $month);
        $this->db->from('inv_week');
        return $this->db->count_all_results() > 0;
    }
    

    public function generate_weekly_periods($year, $month, $regenerate = false) {
        try {
            // Validate input
            if (!$year || !$month || $year < 2020 || $year > 2030 || $month < 1 || $month > 12) {
                throw new Exception('Invalid year or month provided');
            }
            
            log_message('info', "Generating weekly periods for year: $year, month: $month");
            
            // Check if periods already exist for this year/month
            if ($this->periods_exist($year, $month)) {
                throw new Exception('Periode untuk tahun ' . $year . ' bulan ' . $month . ' sudah ada. Silakan pilih tahun/bulan lain atau gunakan data yang sudah ada.');
            }

            $week=$this->report_model->get_week_periods($year, $month);

            foreach($week as $data){
                $insert_result = $this->db->insert('inv_week', $data);
                if ($insert_result) {
                    $data['id_week'] = $this->db->insert_id();
                    log_message('info', "Created week $week_number: " . $data['date_start'] . " to " . $data['date_finish']);
                } else {
                    log_message('error', "Failed to insert week $week_number");
                }
            }
            
            
            log_message('info', "Generated x periods for year: $year, month: $month");
            return $week;
            
        } catch (Exception $e) {
            log_message('error', 'Error in generate_weekly_periods: ' . $e->getMessage());
            throw $e;
        }
    }

    // Build week periods for given year/month using simple +1 day loop (PHP 5 compatible)
    public function get_week_periods($year, $month) {
        $year = intval($year);
        $month = intval($month);

        // Previous month/year
        if ($month == 1) {
            $prev_month = 12;
            $prev_year = $year - 1;
        } else {
            $prev_month = $month - 1;
            $prev_year = $year;
        }

        // Period range: 26 prev month to 25 current month (Y-m-d strings)
        $period_start_dt = new DateTime($prev_year . '-' . $prev_month . '-26');
        $period_end_dt = new DateTime($year . '-' . $month . '-25');
        $period_start = $period_start_dt->format('Y-m-d');
        $period_end = $period_end_dt->format('Y-m-d');

        $today = $period_start;
        $limit = 50; // safety
        $i = 0;
        $week = array();
        $arr = 0;

        while ($i < $limit && $today <= $period_end) {
            if (date('D', strtotime($today)) == 'Fri' || $today == $period_end) {
                $week[] = array(
                    'date_start' => $period_start,
                    'date_finish' => $today,
                    'period_y' => $year,
                    'period_m' => $month,
                    'period_w' => $arr + 1
                );

                // next segment starts the day after today
                $period_start = date('Y-m-d', strtotime($today . ' +1 day'));
                $arr++;
            }

            $today = date('Y-m-d', strtotime($today . ' +1 day'));
            $i++;
        }

        return $week;
    }

    /**
     * Generate inv_report data for a specific week period
     */
    public function generateInventoryReportForWeek($id_week, $week_data = null) {
        try {
            log_message('info', "Generating inv_report data for week ID: $id_week");
            
            // If week_data is not provided, get it from database
            if (!$week_data) {
                $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
                $this->db->from('inv_week');
                $this->db->where('id_week', $id_week);
                $week_query = $this->db->get();
                
                if ($week_query->num_rows() == 0) {
                    log_message('error', "Week ID $id_week not found in database");
                    return false;
                }
                
                $week_data = $week_query->row_array();
            }
            
            // Get all devices
            $devices = $this->buildDevicesBaseQuery(null, null, true)
                ->select('id_dvc, dvc_code, dvc_tech, dvc_type')
                ->get()
                ->result_array();
            
            // QC array
            $qc_types = $this->getQcTypes();
            
            $generated_count = 0;
            
            foreach ($devices as $device) {
                // Get colors and sizes for this device
                $colors = $this->getDeviceColors($device['id_dvc']);
                $sizes = $this->getSizesForDeviceRecord($device);
                
                foreach ($sizes as $size) {
                    foreach ($colors as $color) {
                        foreach ($qc_types as $qc) {
                            if ($this->upsertInvReport($id_week, $device['id_dvc'], $size, $color, $qc, $week_data)) {
                                $generated_count++;
                            }
                        }
                    }
                }
            }
            
            log_message('info', "Generated $generated_count inv_report records for week ID: $id_week");
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'Error generating inv_report for week ID ' . $id_week . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update weekly period using DATE-only fields
     */
    public function update_inv_week($id_week, $date_start, $date_finish) {
        try {
            log_message('info', "Updating inv_week ID: $id_week with dates: $date_start to $date_finish");
            
            // Validate parameters
            if (!$id_week || !$date_start || !$date_finish) {
                log_message('error', 'Missing required parameters for update_inv_week');
                return false;
            }
            
            // Parse dates (DATE only)
            $start_dt = DateTime::createFromFormat('Y-m-d', $date_start);
            $finish_dt = DateTime::createFromFormat('Y-m-d', $date_finish);
            
            if (!$start_dt || !$finish_dt) {
                log_message('error', 'Invalid date format provided');
                return false;
            }
            
            // Ensure start is before finish
            if ($start_dt >= $finish_dt) {
                log_message('error', 'Start date must be before finish date');
                return false;
            }
            
            // Check if the week exists
            $this->db->where('id_week', $id_week);
            $existing = $this->db->get('inv_week');
            
            if ($existing->num_rows() == 0) {
                log_message('error', "Week ID $id_week not found");
                return false;
            }
            
            $old_data = $existing->row_array();
            
            // Update the week (DATE only)
            $update_data = array(
                'date_start' => $start_dt->format('Y-m-d'),
                'date_finish' => $finish_dt->format('Y-m-d')
            );
            
            $this->db->where('id_week', $id_week);
            $update_result = $this->db->update('inv_week', $update_data);
            
            if ($update_result) {
                log_message('info', "Successfully updated inv_week ID: $id_week");
                return true;
            } else {
                log_message('error', "Failed to update inv_week ID: $id_week");
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error in update_inv_week: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Regenerate inv_report data for a specific week (used when week period is updated)
     */
    public function regenerateInventoryReportForWeek($id_week) {
        try {
            log_message('info', "Regenerating inv_report data for week ID: $id_week");
            
            // Get week data
            $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
            $this->db->from('inv_week');
            $this->db->where('id_week', $id_week);
            $week_query = $this->db->get();
            
            if ($week_query->num_rows() == 0) {
                log_message('error', "Week ID $id_week not found in database");
                return false;
            }
            
            $week_data = $week_query->row_array();
            
            // Delete existing inv_report records for this week
            $this->db->where('id_week', $id_week);
            $this->db->delete('inv_report');
            
            log_message('info', "Deleted existing inv_report records for week ID: $id_week");
            
            // Generate new inv_report data
            return $this->generateInventoryReportForWeek($id_week, $week_data);
            
        } catch (Exception $e) {
            log_message('error', 'Error regenerating inv_report for week ID ' . $id_week . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a week period by ID (cascades to inv_report via FK)
     */
    public function delete_inv_week($id_week) {
        try {
            if (!$id_week) {
                return false;
            }

            // Get the week info before delete
            $this->db->select('period_y, period_m, period_w');
            $this->db->from('inv_week');
            $this->db->where('id_week', $id_week);
            $row = $this->db->get()->row_array();
            if (!$row) {
                return false;
            }

            $year = intval($row['period_y']);
            $month = intval($row['period_m']);
            $deleted_week_no = intval($row['period_w']);

            // Transaction: delete then compact week numbers (shift down subsequent weeks)
            $this->db->trans_start();

            // Delete the target week (inv_report will cascade delete via FK if configured)
            $this->db->where('id_week', $id_week);
            $this->db->delete('inv_week');

            // Shift subsequent weeks within the same period
            $this->db->set('period_w', 'period_w - 1', false);
            $this->db->where('period_y', $year);
            $this->db->where('period_m', $month);
            $this->db->where('period_w >', $deleted_week_no);
            $this->db->update('inv_week');

            $this->db->trans_complete();

            return $this->db->trans_status();
        } catch (Exception $e) {
            log_message('error', 'Error deleting week ID ' . $id_week . ': ' . $e->getMessage());
            return false;
        }
    }

    
    
    public function deleteNeedsData($id_dvc, $dvc_size, $dvc_col, $dvc_qc) {
        // Match color exactly as stored
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $dvc_size);
        $this->db->where('dvc_col', $dvc_col);
        $this->db->where('dvc_qc', $dvc_qc);
        $result = $this->db->delete('inv_needs');
        return $result;
    }


    
    public function getExistingNeedsData($tech, $type, $filters = array()) {
        $this->db->select('n.id_dvc, n.dvc_size, n.dvc_col, n.dvc_qc, n.needs_qty');
        $this->db->from('inv_needs n');
        $this->db->join('inv_dvc d', 'n.id_dvc = d.id_dvc');
        $this->db->where('d.dvc_tech', $tech);
        $this->db->where('d.dvc_type', $type);
        $this->db->where('d.status', '0');
        
        // Apply filters
        if (isset($filters['device_search']) && $filters['device_search']) {
            $search_term = $this->db->escape_like_str($filters['device_search']);
            $this->db->group_start()
                     ->like('d.dvc_name', $search_term)
                     ->or_like('d.dvc_code', $search_term)
                     ->group_end();
        }
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        // Convert to associative array for easy lookup
        $needs_data = array();
        foreach ($result as $row) {
            // Generate key that matches the view's getExistingValue function
            // The view expects: $id_dvc . '_' . $size . '_' . $color . '_' . $qc
            $key = $row['id_dvc'] . '_' . $row['dvc_size'] . '_' . $row['dvc_col'] . '_' . $row['dvc_qc'];
            $needs_data[$key] = $row['needs_qty'];
        }
        
        return $needs_data;
    }


    public function getInventoryReportData($tech, $type, $filters = array()) {
        $this->db->select('
            ir.id_pms, ir.id_week, ir.id_dvc, ir.dvc_size, ir.dvc_col, ir.dvc_qc,
            ir.stock, ir.on_pms, ir.needs, ir.order, ir.over,
            iw.date_start, iw.date_finish, iw.period_y, iw.period_m, iw.period_w,
            id.dvc_code, id.dvc_name, id.dvc_tech, id.dvc_type
        ');
        $this->db->from('inv_report ir');
        $this->db->join('inv_week iw', 'ir.id_week = iw.id_week', 'left');
        $this->db->join('inv_dvc id', 'ir.id_dvc = id.id_dvc', 'left');
        $this->db->group_start();
        if ($tech !== null) $this->db->where('id.dvc_tech', $tech);
        if ($type !== null) $this->db->where('id.dvc_type', $type);
        $this->db->group_end();
        $this->db->where('id.status', '0');
        
        // Apply filters
        $this->applyOptionalFilter($filters, 'id_week', 'ir.id_week');
        
        if (isset($filters['device_search']) && $filters['device_search']) {
            $search_term = $this->db->escape_like_str($filters['device_search']);
            $this->db->group_start()
                     ->like('id.dvc_name', $search_term)
                     ->or_like('id.dvc_code', $search_term)
                     ->group_end();
        }
        
        $this->applyOptionalFilter($filters, 'year', 'iw.period_y');
        $this->applyOptionalFilter($filters, 'month', 'iw.period_m');
        $this->applyOptionalFilter($filters, 'week', 'iw.period_w');
        
        $this->db->order_by('iw.period_y DESC, iw.period_m DESC, iw.period_w ASC, id.dvc_priority ASC, ir.dvc_size ASC, ir.dvc_col ASC, ir.dvc_qc ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    

    
    public function getWeekPeriods() { return $this->get_inv_week_data(); }
    
    public function getDevicesForReport($tech, $type) {
        $qb = $this->buildDevicesBaseQuery($tech, $type);
        $qb->select('id_dvc, dvc_code, dvc_name, dvc_tech, dvc_type');
        $qb->order_by('dvc_priority', 'ASC');
        return $qb->get()->result_array();
    }
    
    public function getDeviceColors($id_dvc) {
        // Get device info first
        $this->db->select('dvc_code, dvc_tech, dvc_type');
        $this->db->from('inv_dvc');
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('status', '0');
        $device_query = $this->db->get();
        
        if ($device_query->num_rows() == 0) {
            return array();
        }
        
        $device = $device_query->row_array();
        $colors = array();
        
        // Determine colors based on device code and type
        if (stripos($device['dvc_code'], 'VOH') === 0) {
            // VOH devices have multiple colors
            $colors = array('Dark Grey', 'Black', 'Grey', 'Navy', 'Army', 'Maroon', 'Custom');
        } elseif ($device['dvc_tech'] == 'ecct' && $device['dvc_type'] == 'APP') {
            // ECCT   devices
            $colors = array('Dark Grey');
        } elseif ($device['dvc_tech'] == 'ecbs' && $device['dvc_type'] == 'APP') {
            // ECBS APP devices
            $colors = array('Black');
        } elseif ($device['dvc_type'] == 'osc') {
            $colors = array(' '); // Empty string for OSC
        }
        
        return $colors;
    }
    
    public function saveOnPmsData($data) {
        // First, check if record exists in inv_report
        $this->db->where('id_week', $data['id_week']);
        $this->db->where('id_dvc', $data['id_dvc']);
        $this->db->where('dvc_size', $data['dvc_size']);
        $this->db->where('dvc_col', $data['dvc_col']);
        $this->db->where('dvc_qc', $data['dvc_qc']);
        $existing = $this->db->get('inv_report');
        
        if ($existing->num_rows() > 0) {
            // Update existing record
            $this->db->where('id_week', $data['id_week']);
            $this->db->where('id_dvc', $data['id_dvc']);
            $this->db->where('dvc_size', $data['dvc_size']);
            $this->db->where('dvc_col', $data['dvc_col']);
            $this->db->where('dvc_qc', $data['dvc_qc']);
            
            return $this->db->update('inv_report', array('on_pms' => $data['on_pms']));
        } else {
            // This should not happen if inv_report is properly generated
            // But we can handle it by creating the record
            return $this->db->insert('inv_report', array(
                'id_week' => $data['id_week'],
                'id_dvc' => $data['id_dvc'],
                'dvc_size' => $data['dvc_size'],
                'dvc_col' => $data['dvc_col'],
                'dvc_qc' => $data['dvc_qc'],
                'stock' => 0,
                'on_pms' => $data['on_pms'],
                'needs' => 0,
                'order' => 0,
                'over' => 0
            ));
        }
    }

    /**
     * Upsert helper for inv_report: insert if not exists else update stock/needs
     */
    public function upsertInvReport($id_week, $id_dvc, $size, $color, $qc, $week_data) {
        $this->db->where('id_week', $id_week);
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $size);
        $this->db->where('dvc_col', $color);
        $this->db->where('dvc_qc', $qc);
        $existing = $this->db->get('inv_report');

        $stock = $this->calculateStock($week_data, $id_dvc, $size, $color, $qc);
        $needs = $this->calculateNeeds($week_data, $id_dvc, $size, $color, $qc);

        if ($existing->num_rows() == 0) {
            return $this->db->insert('inv_report', array(
                'id_week' => $id_week,
                'id_dvc' => $id_dvc,
                'dvc_size' => $size,
                'dvc_col' => $color,
                'dvc_qc' => $qc,
                'stock' => $stock,
                'on_pms' => 0,
                'needs' => $needs,
                'order' => 0,
                'over' => 0
            ));
        }

        $this->db->where('id_week', $id_week);
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $size);
        $this->db->where('dvc_col', $color);
        $this->db->where('dvc_qc', $qc);
        return $this->db->update('inv_report', array('stock' => $stock, 'needs' => $needs));
    }

    /**
     * Base query builder for devices to reduce duplication
     * - If $tech or $type are null, they are not filtered
     * - If $onlyActive is true, filter status='0'
     */
    private function buildDevicesBaseQuery($tech = null, $type = null, $onlyActive = true) {
        $qb = $this->db->from('inv_dvc');
        if ($onlyActive) {
            $qb->where('status', '0');
        }
        if ($tech !== null) {
            $qb->where('dvc_tech', $tech);
        }
        if ($type !== null) {
            $qb->where('dvc_type', $type);
        }
        return $qb;
    }

    /**
     * Return QC types list (centralized)
     */
    private function getQcTypes() { return array('LN', 'DN'); }

    /**
     * Return sizes for a device record
     */
    private function getSizesForDeviceRecord($device) {
        return (strtoupper($device['dvc_type']) === 'OSC')
            ? array('-')
            : array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'CUS');
    }

    /**
     * Apply optional filter helper
     */
    private function applyOptionalFilter($filters, $key, $column) {
        if (isset($filters[$key]) && $filters[$key]) {
            $this->db->where($column, $filters[$key]);
        }
    }

    /**
     * Generate inventory report data
     */
    public function generateInventoryReportData() {
        try {
            // Generate per-week using the single week generator to avoid duplicated logic
            $weeks = $this->getWeekPeriods();
            $all_ok = true;
            foreach ($weeks as $week) {
                $ok = $this->generateInventoryReportForWeek($week['id_week'], $week);
                if (!$ok) {
                    $all_ok = false;
                }
            }
            return $all_ok;
        } catch (Exception $e) {
            log_message('error', 'Generate inventory report data error: ' . $e->getMessage());
            return false;
        }
    }

    public function generateInventoryReportForPeriod($year, $month) {
        try {
            log_message('info', "Generating inv_report data for period: year $year, month $month");
            
            // Get weeks for this period
            $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
            $this->db->from('inv_week');
            $this->db->where('period_y', $year);
            $this->db->where('period_m', $month);
            $this->db->order_by('period_w', 'ASC');
            $weeks_query = $this->db->get();
            $weeks = $weeks_query->result_array();
            
            if (empty($weeks)) {
                log_message('warning', "No weeks found for period: year $year, month $month");
                return array('success' => false, 'message' => 'No weeks found for this period');
            }
            
            $total_generated = 0;
            $week_results = array();
            
            foreach ($weeks as $week) {
                $result = $this->generateInventoryReportForWeek($week['id_week'], $week);
                $week_results[] = array(
                    'week_id' => $week['id_week'],
                    'period_w' => $week['period_w'],
                    'success' => $result
                );
                
                if ($result) {
                    $total_generated++;
                }
            }
            
            $success = ($total_generated == count($weeks));
            $message = "Generated inv_report for $total_generated out of " . count($weeks) . " weeks in period $month/$year";
            
            log_message('info', $message);
            
            return array(
                'success' => $success,
                'message' => $message,
                'total_weeks' => count($weeks),
                'generated_weeks' => $total_generated,
                'week_results' => $week_results
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error generating inv_report for period year ' . $year . ', month ' . $month . ': ' . $e->getMessage());
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    private function calculateStock($week, $id_dvc, $size, $color, $qc) {
        $this->db->select('COUNT(*) as stock_count');
        $this->db->from('inv_act');
        $this->db->where('id_dvc', $id_dvc);
        // Apply uniform size/color filters
        $this->applySizeColorFilters($size, $color);
        
        $this->db->where('dvc_qc', $qc);
        
        // inv_in must be within the week period [date_start, date_finish] (use < finish+1day to include full day)
        $finish_plus_one = date('Y-m-d', strtotime($week['date_finish'] . ' +1 day'));
        $this->db->where('inv_in >=', $week['date_start']);
        $this->db->where('inv_in <', $finish_plus_one);
        
        // inv_out must NOT be within the week period (or be null)
        $this->db->where('(inv_out IS NULL OR inv_out < "' . $week['date_start'] . '" OR inv_out >= "' . $finish_plus_one . '")');
        
        $query = $this->db->get();
        $result = $query->row_array();
        
        return intval($result['stock_count']);
    }

    private function calculateNeeds($week, $id_dvc, $size, $color, $qc) {
        // Only apply needs to current and future weeks; past weeks get 0
        if (!$this->isWeekActiveOrFuture($week)) {
            return 0;
        }

        // Get needs data
        $this->db->select('needs_qty');
        $this->db->from('inv_needs');
        $this->db->where('id_dvc', $id_dvc);
        // Apply uniform size/color filters
        $this->applySizeColorFilters($size, $color);
        
        $this->db->where('dvc_qc', $qc);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return intval($result['needs_qty']);
        }
        
        return 0;
    }

    private function isWeekActiveOrFuture($week) { return isset($week['date_finish']) && $week['date_finish'] >= date('Y-m-d'); }

    private function applySizeColorFilters($size, $color) {
        if ($size === '-') {
            $this->db->group_start();
            $this->db->where('dvc_size IS NULL');
            $this->db->or_where('dvc_size', '');
            $this->db->or_where('dvc_size', '-');
            $this->db->group_end();
        } else {
            $this->db->where('dvc_size', $size);
        }
        if ($color === '') {
            $this->db->group_start();
            $this->db->where('dvc_col IS NULL');
            $this->db->or_where('dvc_col', '');
            $this->db->group_end();
        } else {
            $this->db->where('dvc_col', $color);
        }
    }
    


    public function getCurrentWeekPeriod() {
        $today = date('Y-m-d');
        
        $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
        $this->db->from('inv_week');
        $this->db->where('date_start <=', $today);
        $this->db->where('date_finish >=', $today);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
        $this->db->from('inv_week');
        $this->db->where('date_finish <=', $today);
        $this->db->order_by('date_finish DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }

    public function getAvailableYears() { return $this->getDistinctFromInvWeek('period_y', 'DESC', 'year'); }
    public function getAvailableMonths() { return $this->getDistinctFromInvWeek('period_m', 'ASC', 'month'); }
    public function getAvailableWeeks() { return $this->getDistinctFromInvWeek('period_w', 'ASC', 'week'); }

    /**
     * Get ECBS summary data for summary_ecbs view
     * Returns data similar to inventory_model but for summary display
     */
    public function getSummaryEcbsData() {
        try {
            // Get all ECBS devices
            $this->db->select('id_dvc, dvc_name, dvc_code, dvc_priority');
            $this->db->from('inv_dvc');
            $this->db->where('dvc_tech', 'ecbs');
            $this->db->where('dvc_type', 'APP');  // Only APP devices
            $this->db->where('status', '0');
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    // Check if this is VOH device
                    $is_voh = (stripos($device['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($device['dvc_code'], 'VOH') === 0);
                    
                    if ($is_voh) {
                        // VOH devices - create 7 color variants
                        $voh_colors = array('Black', 'Navy', 'Maroon', 'Army', 'Dark Gray', 'Grey', 'Custom');
                        
                        foreach ($voh_colors as $color) {
                            $device_data = $device;
                            $device_data['warna'] = $color;
                            $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'CUS');
                            $total = 0;
                            
                            foreach ($sizes as $size) {
                                $count_sql = "SELECT COUNT(*) as count FROM inv_act WHERE id_dvc = ? AND dvc_size = ? AND dvc_col = ? AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                                $count_query = $this->db->query($count_sql, array($device['id_dvc'], $size, $color));
                                $count = $count_query ? $count_query->row()->count : 0;
                                $device_data['size_' . strtolower($size)] = $count;
                                $total += $count;
                            }
                            
                            $device_data['subtotal'] = $total;
                            $result_data[] = $device_data;
                        }
                    } else {
                        // Non-VOH devices - single row per device
                        $device_data = $device;
                        $device_data['warna'] = 'Black'; // Default color for non-VOH ECBS
                        $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'ALL', 'CUS');
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
            
            return $result_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error in getSummaryEcbsData: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get ECBS OSC summary data for summary_ecbs view (right table)
     * Returns OSC data for the right side table
     */
    public function getSummaryEcbsOscData() {
        try {
            // Get all ECBS OSC devices
            $this->db->select('id_dvc, dvc_name, dvc_code, dvc_priority');
            $this->db->from('inv_dvc');
            $this->db->where('dvc_tech', 'ecbs');
            $this->db->where('dvc_type', 'OSC');  // Only OSC devices
            $this->db->where('status', '0');
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    $device_data = $device;
                    
                    // OSC devices don't have sizes, just count total stock
                    $count_sql = "SELECT COUNT(*) as count FROM inv_act WHERE id_dvc = ? AND (inv_out IS NULL OR inv_out = '' OR inv_out = '0000-00-00 00:00:00')";
                    $count_query = $this->db->query($count_sql, array($device['id_dvc']));
                    $count = $count_query ? $count_query->row()->count : 0;
                    
                    $device_data['subtotal'] = $count;
                    $result_data[] = $device_data;
                }
            }
            
            return $result_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error in getSummaryEcbsOscData: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Generic distinct fetcher for inv_week
     */
    private function getDistinctFromInvWeek($column, $orderDirection = 'ASC', $alias = null) {
        $alias = $alias ?: $column;
        return $this->db
            ->select('DISTINCT(' . $column . ') as ' . $alias, false)
            ->from('inv_week')
            ->order_by($column . ' ' . $orderDirection)
            ->get()
            ->result_array();
    }

    /**
     * Get ECBS Report Data for different report types (needs, order, on_pms, over)
     * @param string $report_type - 'needs', 'order', 'on_pms', 'over'
     * @return array
     */
    public function getSummaryEcbsReportData($report_type = 'needs', $filters = array()) {
        try {
            // Validate report type
            $valid_types = array('stock', 'needs', 'order', 'on_pms', 'over');
            if (!in_array($report_type, $valid_types)) {
                log_message('error', 'Invalid report type: ' . $report_type);
                return array();
            }

            // Get ECBS APP devices only
            $this->db->select('id_dvc, dvc_name, dvc_code, dvc_priority');
            $this->db->from('inv_dvc');
            $this->db->where('dvc_tech', 'ecbs');
            $this->db->where('dvc_type', 'APP');
            $this->db->where('status', '0');
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    $device_data = $device;
                    
                    // Get report data for this device
                    $this->db->select('dvc_size, dvc_col, `' . $report_type . '` as qty');
                    $this->db->from('inv_report');
                    // Apply filters on period
                    if (isset($filters['id_week']) && $filters['id_week']) {
                        $this->db->where('id_week', $filters['id_week']);
                    }
                    $this->db->where('id_dvc', $device['id_dvc']);
                    $this->db->where('`' . $report_type . '` >', 0); // Only get items with quantity > 0
                    
                    $report_query = $this->db->get();
                    
                    if ($report_query && $report_query->num_rows() > 0) {
                        // Initialize size counters
                        $size_xs = 0; $size_s = 0; $size_m = 0; $size_l = 0;
                        $size_xl = 0; $size_xxl = 0; $size_3xl = 0; $size_all = 0; $size_cus = 0;
                        $subtotal = 0;
                        
                        foreach ($report_query->result_array() as $report_item) {
                            $qty = (int)$report_item['qty'];
                            $size = strtolower(trim($report_item['dvc_size']));
                            $color = trim($report_item['dvc_col']);
                            
                            // Count by size
                            switch ($size) {
                                case 'xs': $size_xs += $qty; break;
                                case 's': $size_s += $qty; break;
                                case 'm': $size_m += $qty; break;
                                case 'l': $size_l += $qty; break;
                                case 'xl': $size_xl += $qty; break;
                                case 'xxl': $size_xxl += $qty; break;
                                case '3xl': $size_3xl += $qty; break;
                                case 'all': $size_all += $qty; break;
                                case 'cus': $size_cus += $qty; break;
                                default: $size_cus += $qty; break; // Default to custom
                            }
                            
                            $subtotal += $qty;
                        }
                        
                        // Add size data to device
                        $device_data['size_xs'] = $size_xs;
                        $device_data['size_s'] = $size_s;
                        $device_data['size_m'] = $size_m;
                        $device_data['size_l'] = $size_l;
                        $device_data['size_xl'] = $size_xl;
                        $device_data['size_xxl'] = $size_xxl;
                        $device_data['size_3xl'] = $size_3xl;
                        $device_data['size_all'] = $size_all;
                        $device_data['size_cus'] = $size_cus;
                        $device_data['subtotal'] = $subtotal;
                        $device_data['warna'] = isset($color) ? $color : 'Default';
                        
                        $result_data[] = $device_data;
                    } else {
                        // Device exists but no report data, add with zeros
                        $device_data['size_xs'] = 0; $device_data['size_s'] = 0; $device_data['size_m'] = 0;
                        $device_data['size_l'] = 0; $device_data['size_xl'] = 0; $device_data['size_xxl'] = 0;
                        $device_data['size_3xl'] = 0; $device_data['size_all'] = 0; $device_data['size_cus'] = 0;
                        $device_data['subtotal'] = 0;
                        $device_data['warna'] = 'Default';
                        
                        $result_data[] = $device_data;
                    }
                }
            }
            
            return $result_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error in getSummaryEcbsReportData: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get ECBS OSC Report Data for different report types
     * @param string $report_type - 'needs', 'order', 'on_pms', 'over'
     * @return array
     */
    public function getSummaryEcbsOscReportData($report_type = 'needs', $filters = array()) {
        try {
            // Validate report type
            $valid_types = array('stock', 'needs', 'order', 'on_pms', 'over');
            if (!in_array($report_type, $valid_types)) {
                log_message('error', 'Invalid report type: ' . $report_type);
                return array();
            }

            // Get ECBS OSC + ACC devices (merged to Oscillator table)
            $this->db->select('id_dvc, dvc_name, dvc_code, dvc_priority');
            $this->db->from('inv_dvc');
            $this->db->where('dvc_tech', 'ecbs');
            $this->db->where_in('dvc_type', array('OSC', 'ACC'));
            $this->db->where('status', '0');
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    $device_data = $device;
                    
                    // Sum report metric for this device (no sizes/colors for OSC/ACC summary)
                    $this->db->select('SUM(`' . $report_type . '`) as total_qty');
                    $this->db->from('inv_report');
                    if (isset($filters['id_week']) && $filters['id_week']) {
                        $this->db->where('id_week', $filters['id_week']);
                    }
                    $this->db->where('id_dvc', $device['id_dvc']);
                    $this->db->where('`' . $report_type . '` >', 0);
                    
                    $report_query = $this->db->get();
                    $total_qty = 0;
                    
                    if ($report_query && $report_query->num_rows() > 0) {
                        $total_qty = (int)$report_query->row()->total_qty;
                    }
                    
                    $device_data['subtotal'] = $total_qty;
                    $result_data[] = $device_data;
                }
            }
            
            return $result_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error in getSummaryEcbsOscReportData: ' . $e->getMessage());
            return array();
        }
    }
}
?>