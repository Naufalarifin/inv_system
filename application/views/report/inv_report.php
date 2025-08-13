<!-- Container -->
<div class="container-fixed">
    <div class="card min-w-full">
        <div class="card-header flex items-center justify-between">
            <div id="toolbar_left" class="flex items-center gap-2">
                <!-- Generate Data Button -->
                <button class="btn btn-sm btn-success" id="btn_generate" onclick="generateInventoryReport()" title="Generate inventory report data from database">
                    <i class="ki-filled ki-setting !text-base"></i>Generate Data
                </button>
                
                <!-- Input On PMS Button -->
                <button class="btn btn-sm btn-primary" id="btn_input_pms" onclick="showInputPmsModal()">
                    <i class="ki-filled ki-plus !text-base"></i>Input On PMS
                </button>
                
                <!-- ECBS/ECCT Group -->
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" id="btn_ecbs" onclick="selectTech_report('ecbs')">ECBS</button>
                    <button class="btn btn-sm btn-light" id="btn_ecct" onclick="selectTech_report('ecct')">ECCT</button>
                </div>
                
                <!-- APP/OSC Group -->
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" id="btn_app" onclick="selectType_report('app')">APP</button>
                    <button class="btn btn-sm btn-light" id="btn_osc" onclick="selectType_report('osc')">OSC</button>
                </div>
            </div>
            <div id="toolbar_right" class="flex items-center gap-2">
                <!-- Search, Filter, dan Export di kanan -->
                <div class="input-group input-sm">
                    <input class="input input-sm" placeholder="Search Device Name..." type="text" id="device_search" />
                    <span class="btn btn-light btn-sm" onclick="openModal_report('modal_filter_report')">Filter</span>
                    <span class="btn btn-primary btn-sm" onclick="applyFilters()">Search</span>
                </div>
                <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportData_report();">
                    <i class="ki-filled ki-exit-down !text-base"></i>Export
                </a>
            </div>
        </div>
        <div id="show_data_report">Loading...</div>
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
                            <option value="<?php echo $year_data['year']; ?>" <?php echo (isset($current_filters['year']) && $current_filters['year'] == $year_data['year']) ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $month_data['month']; ?>" <?php echo (isset($current_filters['month']) && $current_filters['month'] == $month_data['month']) ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $week_data['week']; ?>" <?php echo (isset($current_filters['week']) && $current_filters['week'] == $week_data['week']) ? 'selected' : ''; ?>>
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
                    (<?php echo $current_week['date_start']; ?> - <?php echo $current_week['date_finish']; ?>)
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



<!-- Input On PMS Modal -->
<div class="modal fade" id="inputPmsModal" tabindex="-1" role="dialog" aria-labelledby="inputPmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputPmsModalLabel">Input On PMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="pmsInputForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="select_week">Select Week Period:</label>
                                <select class="form-control" id="select_week" name="select_week" required>
                                    <option value="">-- Select Week --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="select_device">Select Device:</label>
                                <select class="form-control" id="select_device" name="select_device" required>
                                    <option value="">-- Select Device --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="select_size">Size:</label>
                                <select class="form-control" id="select_size" name="select_size" required>
                                    <option value="">-- Select Size --</option>
                                    <option value="xs">XS</option>
                                    <option value="s">S</option>
                                    <option value="m">M</option>
                                    <option value="l">L</option>
                                    <option value="xl">XL</option>
                                    <option value="xxl">XXL</option>
                                    <option value="3xl">3XL</option>
                                    <option value="all">ALL</option>
                                    <option value="cus">CUS</option>
                                    <option value="-">-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="select_color">Color:</label>
                                <select class="form-control" id="select_color" name="select_color" required>
                                    <option value="">-- Select Color --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="select_qc">Quality Control:</label>
                                <select class="form-control" id="select_qc" name="select_qc" required>
                                    <option value="">-- Select QC --</option>
                                    <option value="LN">LN</option>
                                    <option value="DN">DN</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_on_pms">On PMS Quantity:</label>
                        <input type="number" class="form-control" id="input_on_pms" name="input_on_pms" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveOnPms()">Save</button>
            </div>
        </div>
    </div>
</div>