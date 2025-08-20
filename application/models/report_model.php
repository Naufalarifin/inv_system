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
            MIN(ir.id_pms) AS id_pms,
            ir.id_week,
            id.dvc_code,
            MIN(id.dvc_name) AS dvc_name,
            MIN(id.dvc_tech) AS dvc_tech,
            MIN(id.dvc_type) AS dvc_type,
            MIN(id.dvc_priority) AS dvc_priority,
            ir.dvc_size,
            ir.dvc_col,
            ir.dvc_qc,
            SUM(ir.stock) AS stock,
            SUM(ir.on_pms) AS on_pms,
            SUM(ir.needs) AS needs,
            SUM(ir.`order`) AS `order`,
            SUM(ir.`over`) AS `over`,
            iw.date_start, iw.date_finish, iw.period_y, iw.period_m, iw.period_w', false);

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

        // Group by week + device code + size + color + qc and week period columns
        $this->db->group_by('ir.id_week');
        $this->db->group_by('id.dvc_code');
        $this->db->group_by('id.dvc_priority');
        $this->db->group_by('ir.dvc_size');
        $this->db->group_by('ir.dvc_col');
        $this->db->group_by('ir.dvc_qc');
        $this->db->group_by('iw.date_start');
        $this->db->group_by('iw.date_finish');
        $this->db->group_by('iw.period_y');
        $this->db->group_by('iw.period_m');
        $this->db->group_by('iw.period_w');

        // Order - prioritize by dvc_priority ASC, then by other criteria
        $this->db->order_by('id.dvc_priority ASC, iw.period_y DESC, iw.period_m DESC, iw.period_w ASC, id.dvc_code ASC, ir.dvc_size ASC, ir.dvc_col ASC, ir.dvc_qc ASC');
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
            $colors = array('Dark Gray', 'Black', 'Grey', 'Navy', 'Army', 'Maroon', 'Custom');
        } elseif ($device['dvc_tech'] == 'ecct' && $device['dvc_type'] == 'APP') {
            // ECCT APP devices
            $colors = array('Dark Gray');
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
            $row = $existing->row_array();
            $stock = (int)$row['stock'];
            $needs = (int)$row['needs'];
            $on_pms = (int)$data['on_pms'];
            
            // Calculate order and over
            $order = max(0, $needs - $on_pms - $stock);
            $over = max(0, $on_pms + $stock - $needs);
            
            // Update existing record with new on_pms and calculated order/over
            $this->db->where('id_week', $data['id_week']);
            $this->db->where('id_dvc', $data['id_dvc']);
            $this->db->where('dvc_size', $data['dvc_size']);
            $this->db->where('dvc_col', $data['dvc_col']);
            $this->db->where('dvc_qc', $data['dvc_qc']);
            
            return $this->db->update('inv_report', array(
                'on_pms' => $data['on_pms'],
                'order' => $order,
                'over' => $over
            ));
        } else {
            // This should not happen if inv_report is properly generated
            // But we can handle it by creating the record
            $on_pms = (int)$data['on_pms'];
            $order = max(0, 0 - $on_pms - 0); // needs=0, stock=0
            $over = max(0, $on_pms + 0 - 0);  // needs=0, stock=0
            
            return $this->db->insert('inv_report', array(
                'id_week' => $data['id_week'],
                'id_dvc' => $data['id_dvc'],
                'dvc_size' => $data['dvc_size'],
                'dvc_col' => $data['dvc_col'],
                'dvc_qc' => $data['dvc_qc'],
                'stock' => 0,
                'on_pms' => $data['on_pms'],
                'needs' => 0,
                'order' => $order,
                'over' => $over
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
            // For weekly generation, we still skip truly all-zero rows.
            // However, stock calculation should already reflect items present by end-of-week.
            if (intval($stock) === 0 && intval($needs) === 0) {
                return false;
            }
            
            // Calculate order and over for new record
            $order = max(0, $needs - 0 - $stock); // on_pms = 0 for new records
            $over = max(0, 0 + $stock - $needs);  // on_pms = 0 for new records
            
            return $this->db->insert('inv_report', array(
                'id_week' => $id_week,
                'id_dvc' => $id_dvc,
                'dvc_size' => $size,
                'dvc_col' => $color,
                'dvc_qc' => $qc,
                'stock' => $stock,
                'on_pms' => 0,
                'needs' => $needs,
                'order' => $order,
                'over' => $over
            ));
        }

        $this->db->where('id_week', $id_week);
        $this->db->where('id_dvc', $id_dvc);
        $this->db->where('dvc_size', $size);
        $this->db->where('dvc_col', $color);
        $this->db->where('dvc_qc', $qc);
        
        // Get current on_pms to calculate order/over
        $current = $existing->row_array();
        $current_on_pms = (int)$current['on_pms'];
        
        // Calculate order and over
        $order = max(0, $needs - $current_on_pms - $stock);
        $over = max(0, $current_on_pms + $stock - $needs);
        
        // Update existing with stock, needs, and calculated order/over
        return $this->db->update('inv_report', array(
            'stock' => $stock, 
            'needs' => $needs,
            'order' => $order,
            'over' => $over
        ));
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

    public function calculateStock($week, $id_dvc, $size, $color, $qc) {
        // Count inventory IN as of the end of the current week, minus those that have OUT before or on the week finish day.
        // Rules:
        // - inv_in DATE <= date_finish (end of day)
        // - inv_out is NULL (still in) OR inv_out DATE > date_finish (after the end day)
        $this->db->select('COUNT(*) as stock_count');
        $this->db->from('inv_act');
        $this->db->where('id_dvc', $id_dvc);
        // Apply uniform size/color filters
        $this->applySizeColorFilters($size, $color);
        $this->db->where('dvc_qc', $qc);

        $finish_day = date('Y-m-d', strtotime($week['date_finish']));
        $finish_plus_one = date('Y-m-d', strtotime($finish_day . ' +1 day'));

        $this->db->where('inv_in <', $finish_plus_one);
        // inv_out not yet happened by end-of-week: NULL or strictly after finish day (>= finish+1 day)
        $this->db->where('(inv_out IS NULL OR inv_out >= "' . $finish_plus_one . '")');

        $query = $this->db->get();
        $result = $query->row_array();
        return intval($result['stock_count']);
    }

    public function calculateNeeds($week, $id_dvc, $size, $color, $qc) {
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
            // Apply tolerant color matching for Gray/Grey synonyms
            $synonyms = $this->getColorSynonyms($color);
            if (count($synonyms) > 1) {
                $this->db->group_start();
                foreach ($synonyms as $idx => $col) {
                    if ($idx === 0) { $this->db->where('dvc_col', $col); }
                    else { $this->db->or_where('dvc_col', $col); }
                }
                $this->db->group_end();
            } else {
                $this->db->where('dvc_col', $color);
            }
        }
    }

    // Return tolerated synonyms for common Gray/Grey variants (case-insensitive)
    public function getColorSynonyms($color) {
        $c = trim(strtolower($color));
        $c = preg_replace('/\s+/', ' ', $c);
        if ($c === 'gray' || $c === 'grey') { return array('Grey', 'Gray'); }
        if ($c === 'dark gray' || $c === 'dark grey' || $c === 'darkgray' || $c === 'darkgrey') {
            return array('Dark Grey', 'Dark Gray');
        }
        return array($color);
    }

    // Normalize provided color to canonical form for the device (handles ECCT vs VOH differences)
    public function normalizeColorForDevice($id_dvc, $color) {
        $normalized = trim($color);
        $this->db->select('dvc_code, dvc_tech, dvc_type');
        $this->db->from('inv_dvc');
        $this->db->where('id_dvc', $id_dvc);
        $this->db->limit(1);
        $row = $this->db->get()->row_array();
        $lc = strtolower(preg_replace('/\s+/', ' ', $normalized));
        if (!$row) { return $normalized; }
        $isEcctApp = (strtolower($row['dvc_tech']) === 'ecct' && strtoupper($row['dvc_type']) === 'APP');
        $isVoh = (stripos($row['dvc_code'], 'VOH') === 0);
        if ($isEcctApp) {
            if ($lc === 'dark grey' || $lc === 'darkgray' || $lc === 'dark grey ') { return 'Dark Gray'; }
            if ($lc === 'grey' || $lc === 'gray') { return 'Dark Gray'; }
        }
        if ($isVoh) {
            if ($lc === 'gray') { return 'Grey'; }
            if ($lc === 'dark gray') { return 'Dark Grey'; }
        }
        // Default: title-case
        return trim(ucwords($lc));
    }

    /**
     * Calculate order value: needs - on_pms - stock (minimum 0)
     */
    public function calculateOrder($needs, $on_pms, $stock) {
        return max(0, $needs - $on_pms - $stock);
    }

    /**
     * Calculate over value: if (needs - on_pms - stock) < 0, take positive value, else 0
     */
    public function calculateOver($needs, $on_pms, $stock) {
        $calculation = $needs - $on_pms - $stock;
        return ($calculation < 0) ? abs($calculation) : 0;
    }
    


    public function getCurrentWeekPeriod() {
        // Compare by DATE so DATETIME values with time still match the calendar day
        $today = date('Y-m-d');
        
        $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
        $this->db->from('inv_week');
        // DATE() ensures comparison ignores time components
        $this->db->where('DATE(date_start) <=', $today);
        $this->db->where('DATE(date_finish) >=', $today);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        // Fallback: last finished week before today
        $this->db->select('id_week, date_start, date_finish, period_y, period_m, period_w');
        $this->db->from('inv_week');
        $this->db->where('DATE(date_finish) <=', $today);
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
            // Apply device search filter
            if (isset($filters['device_search']) && $filters['device_search']) {
                $search_term = $this->db->escape_like_str($filters['device_search']);
                $this->db->group_start()
                         ->like('dvc_name', $search_term)
                         ->or_like('dvc_code', $search_term)
                         ->group_end();
            }
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    $device_data = $device;
                    
                    // Get report data for this device
                    $this->db->select('ir.dvc_size, ir.dvc_col, ir.`' . $report_type . '` as qty');
                    $this->db->from('inv_report ir');
                    $this->db->join('inv_week iw', 'ir.id_week = iw.id_week', 'left');
                    // Apply filters on period (consistent with detail)
                    $this->applyOptionalFilter($filters, 'id_week', 'ir.id_week');
                    $this->applyOptionalFilter($filters, 'year', 'iw.period_y');
                    $this->applyOptionalFilter($filters, 'month', 'iw.period_m');
                    $this->applyOptionalFilter($filters, 'week', 'iw.period_w');
                    $this->db->where('ir.id_dvc', $device['id_dvc']);
                    $this->db->where('ir.`' . $report_type . '` >', 0); // Only get items with quantity > 0
                    
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
                    } // When no report rows, skip adding this device to match ECCT empty-state behavior
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
            // Apply device search filter
            if (isset($filters['device_search']) && $filters['device_search']) {
                $search_term = $this->db->escape_like_str($filters['device_search']);
                $this->db->group_start()
                         ->like('dvc_name', $search_term)
                         ->or_like('dvc_code', $search_term)
                         ->group_end();
            }
            $this->db->order_by('dvc_priority', 'ASC');
            
            $devices_query = $this->db->get();
            $result_data = array();
            
            if ($devices_query && $devices_query->num_rows() > 0) {
                foreach ($devices_query->result_array() as $device) {
                    $device_data = $device;
                    
                    // Sum report metric for this device (no sizes/colors for OSC/ACC summary)
                    $this->db->select('SUM(ir.`' . $report_type . '`) as total_qty');
                    $this->db->from('inv_report ir');
                    $this->db->join('inv_week iw', 'ir.id_week = iw.id_week', 'left');
                    // Apply filters on period (consistent with detail)
                    $this->applyOptionalFilter($filters, 'id_week', 'ir.id_week');
                    $this->applyOptionalFilter($filters, 'year', 'iw.period_y');
                    $this->applyOptionalFilter($filters, 'month', 'iw.period_m');
                    $this->applyOptionalFilter($filters, 'week', 'iw.period_w');
                    $this->db->where('ir.id_dvc', $device['id_dvc']);
                    $this->db->where('ir.`' . $report_type . '` >', 0);
                    
                    $report_query = $this->db->get();
                    $total_qty = 0;
                    if ($report_query && $report_query->num_rows() > 0) {
                        $total_qty = (int)$report_query->row()->total_qty;
                    }
                    // Only include devices that have data (>0) to mirror ECCT's empty-state behavior
                    if ($total_qty > 0) {
                        $device_data['subtotal'] = $total_qty;
                        $result_data[] = $device_data;
                    }
                }
            }
            
            return $result_data;
            
        } catch (Exception $e) {
            log_message('error', 'Error in getSummaryEcbsOscReportData: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get ECCT summary data for summary_ecct view
     * Returns processed data for APP and OSC devices with proper grouping and filtering
     */
    public function getECCTSummaryData($filters = array()) {
        try {
            $rows = $this->getInventoryReportData('ecct', null, $filters);
            
            // Helper function for percentage calculation only
            $calc_pct = function($n,$s) { return ((int)$n > 0) ? round(((int)$s / (int)$n) * 100) : 100; };
            
            // Process APP devices (group by code+size, merge LN+DN)
            $appIndex = array();
            foreach ($rows as $r) {
                if (strtoupper($r['dvc_type']) !== 'APP') continue;
                $code = $r['dvc_code'];
                $size = strtoupper(trim($r['dvc_size']));
                if (empty($size)) $size = '-';
                
                if (!isset($appIndex[$code])) {
                    $appIndex[$code] = array(
                        'dvc_code' => $code,
                        'dvc_name' => $r['dvc_name'],
                        'dvc_priority' => isset($r['dvc_priority']) ? (int)$r['dvc_priority'] : 999,
                        'sizes' => array()
                    );
                }
                
                if (!isset($appIndex[$code]['sizes'][$size])) {
                    $appIndex[$code]['sizes'][$size] = array('stock' => 0, 'on_pms' => 0, 'needs' => 0, 'order' => 0, 'over' => 0);
                }
                
                $appIndex[$code]['sizes'][$size]['stock'] += (int)$r['stock'];
                $appIndex[$code]['sizes'][$size]['on_pms'] += (int)$r['on_pms'];
                $appIndex[$code]['sizes'][$size]['needs'] += (int)$r['needs'];
                $appIndex[$code]['sizes'][$size]['order'] += (int)$r['order'];
                $appIndex[$code]['sizes'][$size]['over'] += (int)$r['over'];
            }
            
            // Sort APP items and split into left/right tables
            $appItems = array_values($appIndex);
            usort($appItems, function($a,$b) { 
                if ($a['dvc_priority'] != $b['dvc_priority']) {
                    return $a['dvc_priority'] - $b['dvc_priority'];
                }
                return strcasecmp($a['dvc_name'].'|'.$a['dvc_code'], $b['dvc_name'].'|'.$b['dvc_code']); 
            });
            
            $splitIndex = (count($appItems) > 0) ? (int)ceil(count($appItems) / 2) : 0;
            $appLeft = array_slice($appItems, 0, $splitIndex);
            $appRight = array_slice($appItems, $splitIndex);
            
            // Process OSC devices (split LN/DN by QC)
            $oscLN = array();
            $oscDN = array();
            foreach ($rows as $r) {
                if (strtoupper($r['dvc_type']) !== 'OSC') continue;
                
                if (strtoupper(trim($r['dvc_qc'])) === 'DN') {
                    $dest =& $oscDN;
                } else {
                    $dest =& $oscLN;
                }
                $code = $r['dvc_code'];
                
                if (!isset($dest[$code])) {
                    $dest[$code] = array(
                        'dvc_code' => $code,
                        'dvc_name' => $r['dvc_name'],
                        'dvc_priority' => isset($r['dvc_priority']) ? (int)$r['dvc_priority'] : 999,
                        'stock' => 0, 'on_pms' => 0, 'needs' => 0, 'order' => 0, 'over' => 0
                    );
                }
                
                $dest[$code]['stock'] += (int)$r['stock'];
                $dest[$code]['on_pms'] += (int)$r['on_pms'];
                $dest[$code]['needs'] += (int)$r['needs'];
                $dest[$code]['order'] += (int)$r['order'];
                $dest[$code]['over'] += (int)$r['over'];
            }
            
            // Sort OSC items
            $osc_sort = function($a,$b) { 
                if ($a['dvc_priority'] != $b['dvc_priority']) {
                    return $a['dvc_priority'] - $b['dvc_priority'];
                }
                return strcasecmp($a['dvc_name'].'|'.$a['dvc_code'], $b['dvc_name'].'|'.$b['dvc_code']);
            };
            
            $oscLNItems = array_values($oscLN);
            $oscDNItems = array_values($oscDN);
            usort($oscLNItems, $osc_sort);
            usort($oscDNItems, $osc_sort);
            
            return array(
                'app_left' => $appLeft,
                'app_right' => $appRight,
                'osc_ln' => $oscLNItems,
                'osc_dn' => $oscDNItems,
                'calc_pct' => $calc_pct
            );
            
        } catch (Exception $e) {
            log_message('error', 'Error in getECCTSummaryData: ' . $e->getMessage());
            return array(
                'app_left' => array(), 'app_right' => array(), 'osc_ln' => array(), 'osc_dn' => array(),
                'calc_pct' => function($n,$s) { return 100; }
            );
        }
    }
}
?>