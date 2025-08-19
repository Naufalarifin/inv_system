<!-- Container -->
<div class="container-fixed">
    <div class="card min-w-full">
        <div class="card-header flex items-center justify-between">
            <div id="toolbar_left" class="flex items-center gap-2">
                <!-- Summary / Detail Toggle Button -->
                <button class="btn btn-sm btn-outline-primary" id="btn_mode_toggle" onclick="toggleViewMode()" title="Toggle Summary / Detail">
                    <span class="two-arrows-icon" aria-hidden="true" style="display:inline-flex;align-items:center;margin-right:6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 8H16" stroke="#007bff" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14 6L16 8L14 10" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 16H8" stroke="#007bff" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 14L8 16L10 18" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span id="mode_toggle_label">Summary</span>
                </button>
                
                <!-- Input On PMS Button -->
                <button class="btn btn-sm btn-primary" id="btn_input_pms" onclick="showInputPmsModal()">
                    <i class="ki-filled ki-plus !text-base"></i>Input On PMS
                </button>
                
                <!-- ECBS/ECCT Group -->
                <div class="btn-group" id="group_tech">
                    <button class="btn btn-sm btn-primary" id="btn_ecbs" onclick="selectTech_report('ecbs')">ECBS</button>
                    <button class="btn btn-sm btn-light" id="btn_ecct" onclick="selectTech_report('ecct')">ECCT</button>
                </div>
                
                <!-- APP/OSC Group -->
                <div class="btn-group" id="group_type" style="display:none;">
                    <button class="btn btn-sm btn-primary" id="btn_app" onclick="selectType_report('app')">APP</button>
                    <button class="btn btn-sm btn-light" id="btn_osc" onclick="selectType_report('osc')">OSC</button>
                </div>
            </div>
            <div id="toolbar_right" class="flex items-center gap-2">
                <!-- Controls (summary vs detail) -->
                <div id="summary_controls" class="input-group input-sm" style="display:inline-flex;">
                    <span class="btn btn-light btn-sm" onclick="openModal_report('modal_filter_report')">Filter</span>
                </div>
                <div id="detail_controls" class="input-group input-sm" style="display:none;">
                    <input class="input input-sm" placeholder="Search Device Name..." type="text" id="device_search" value="<?php echo isset($current_filters['device_search']) ? htmlspecialchars($current_filters['device_search']) : ''; ?>" />
                    <span class="btn btn-light btn-sm" onclick="openModal_report('modal_filter_report')">Filter</span>
                    <span class="btn btn-primary btn-sm" onclick="applyFilters()">Search</span>
                </div>
                <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportData_report();">
                    <i class="ki-filled ki-exit-down !text-base"></i>Export
                </a>
            </div>
        </div>
        <div id="show_summary_report" style="display:block;">
            <div id="summary_ecbs_wrapper" style="display:block;">
                <?php $this->load->view('report/report/summary_ecbs', array('current_week' => isset($current_week)?$current_week:null, 'current_filters' => isset($current_filters)?$current_filters:array())); ?>
            </div>
            <div id="summary_ecct_wrapper" style="display:none;">
                <?php $this->load->view('report/report/summary_ecct', array('current_week' => isset($current_week)?$current_week:null, 'current_filters' => isset($current_filters)?$current_filters:array())); ?>
            </div>
        </div>
        <div id="show_data_report" style="display:none;"></div>
    </div>
</div>

<!-- Modal Filter Report -->
<div id="modal_filter_report" class="modal-container">
    <div class="modal-header">
        <h3 class="modal-title">Filter Report</h3>
        <button class="btn-close" onclick="closeModal_report('modal_filter_report')">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="input-form-label">Year</label>
            <select class="input" id="filter_year">
                <?php if (isset($current_week['period_y'])): ?>
                    <option value="<?php echo $current_week['period_y']; ?>">
                        Current Year: <?php echo $current_week['period_y']; ?>
                    </option>
                <?php endif; ?>
                <?php if (isset($available_years) && is_array($available_years)): ?>
                    <?php foreach ($available_years as $year_data): ?>
                        <?php if (!isset($current_week['period_y']) || $year_data['year'] != $current_week['period_y']): ?>
                            <option value="<?php echo $year_data['year']; ?>" <?php echo (!empty($current_filters['year']) && $current_filters['year'] == $year_data['year']) ? 'selected' : ''; ?>>
                                <?php echo $year_data['year']; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <option value="">All Years</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="input-form-label">Month</label>
            <select class="input" id="filter_month">
                <?php if (isset($current_week['period_m'])): ?>
                    <option value="<?php echo $current_week['period_m']; ?>">
                        Current Month: <?php echo $current_week['period_m']; ?>
                    </option>
                <?php endif; ?>
                <?php if (isset($available_months) && is_array($available_months)): ?>
                    <?php foreach ($available_months as $month_data): ?>
                        <?php if (!isset($current_week['period_m']) || $month_data['month'] != $current_week['period_m']): ?>
                            <option value="<?php echo $month_data['month']; ?>" <?php echo (!empty($current_filters['month']) && $current_filters['month'] == $month_data['month']) ? 'selected' : ''; ?>>
                                <?php echo $month_data['month']; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <option value="">All Months</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="input-form-label">Week</label>
            <select class="input" id="filter_week">
                <?php if (isset($current_week['period_w'])): ?>
                    <option value="<?php echo $current_week['period_w']; ?>">
                        Current Week: <?php echo $current_week['period_w']; ?>
                    </option>
                <?php endif; ?>
                <?php if (isset($available_weeks) && is_array($available_weeks)): ?>
                    <?php foreach ($available_weeks as $week_data): ?>
                        <?php if (!isset($current_week['period_w']) || $week_data['week'] != $current_week['period_w']): ?>
                            <option value="<?php echo $week_data['week']; ?>" <?php echo (!empty($current_filters['week']) && $current_filters['week'] == $week_data['week']) ? 'selected' : ''; ?>>
                                Week <?php echo $week_data['week']; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <option value="">All Weeks</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="input-form-label">Current Week</label>
            <div class="input-info">
                <?php if (isset($current_week) && $current_week): ?>
                    Week <?php echo $current_week['period_w']; ?>/<?php echo $current_week['period_m']; ?>/<?php echo $current_week['period_y']; ?>
                    (<?php echo date('d/m/Y', strtotime($current_week['date_start'])); ?> - <?php echo date('d/m/Y', strtotime($current_week['date_finish'])); ?>)
                <?php else: ?>
                    No current week found
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal_report('modal_filter_report')">Cancel</button>
        <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
    </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>



<!-- Input On PMS Modal (Custom like Filter) -->
<div id="modal_input_pms" class="modal-container">
    <div class="modal-header">
        <h3 class="modal-title">Input On PMS - Massive Input</h3>
        <button class="btn-close" onclick="closeInputPmsModal()">&times;</button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="input-form-label">Massive Input Data</label>
            <textarea id="massive_pms_input" class="input massive-textarea" 
                placeholder="Format: KODE_ALAT\tUKURAN\tWARNA\tSTATUS\tSTOCK (optional)&#10;Contoh:&#10;ABC123\tM\tMerah\tDN\t2&#10;XYZ789\tL\tBiru\tLN" 
                style="min-height: 160px; font-family: monospace; font-size: 14px;"></textarea>
        </div>

        <div class="form-group">
            <div id="preview_pms_data" style="display:none; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px; border-radius: 4px;"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeInputPmsModal()">Cancel</button>
        <button class="btn btn-primary" onclick="saveMassiveOnPms()"><i class="ki-filled ki-check !text-base"></i>Save On PMS</button>
    </div>
</div>

<style>
/* =================== REPORT STYLES =================== */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: none; }
.modal-container { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 1001; width: 400px; display: none; }
.modal-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.modal-title { margin: 0; font-size: 18px; font-weight: 600; }
.btn-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #666; }
.modal-body { padding: 20px; }
.modal-footer { padding: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: flex-end; }
.form-group { margin-bottom: 15px; }
.input-form-label { display: block; margin-bottom: 5px; font-weight: 500; }
.input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
.input-info { padding: 10px; background: #f5f5f5; border-radius: 4px; color: #666; font-size: 14px; }
.btn  border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: all 0.2s ease; }
.btn:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-secondary { background: #6c757d; color: white; }
.btn-primary { background: #007bff; color: white; }
.btn-success { background: #28a745; color: white; }
.btn-light { background: #f8f9fa; color: #495057; border: 1px solid #dee2e6; }
.btn-outline-primary { background: #ffffff; color: #007bff; border: 1px solid #007bff; }
.compact-table { font-size: 13px !important; }
.compact-table th, .compact-table td { padding: 8px 4px !important; line-height: 1.4 !important; }
.compact-table th { font-size: 14px !important; background-color: #f8f9fa; }
.compact-table tbody tr:hover { background-color: #f5f5f5; }
td[title]:hover { background-color: #e3f2fd !important; }
.filter-info { background: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #e9ecef; }
.badge { padding: 2px 8px; border-radius: 3px; margin-left: 5px; font-size: 12px; font-weight: 500; }
.badge-primary { background: #007bff; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.badge-info { background: #17a2b8; color: white; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@media (max-width: 768px) { .modal-container { width: 90%; max-width: 400px; } .card-header { flex-direction: column; gap: 10px; align-items: stretch; } #toolbar_left, #toolbar_right { justify-content: center; } .btn-group { flex-wrap: wrap; justify-content: center; } }
</style>