<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}
.modal-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    min-width: 350px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}
.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}
.modal-title {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #333;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e5e5;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8f9fa;
    border-radius: 0 0 8px 8px;
}
.btn-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}
.btn-close:hover {
    color: #000;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.input-tab-btn {
  background: #fff;
  color: #0074d9;
  border: 1px solid #0074d9;
  border-radius: 4px 4px 0 0;
  padding: 6px 24px;
  margin-right: 4px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  outline: none;
  transition: background 0.2s, color 0.2s;
}
.input-tab-btn.active {
  background: #0074d9;
  color: #fff;
}
.input-mode-btn {
  background: #fff;
  color: #0074d9;
  border: 1px solid #0074d9;
  border-radius: 4px;
  padding: 4px 12px;
  margin-right: 4px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  outline: none;
  transition: background 0.2s, color 0.2s;
}
.input-mode-btn.active {
  background: #0074d9;
  color: #fff;
}
.input-form-label {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 4px;
  display: block;
}
.input-result-message {
  margin-top: 10px;
  padding: 8px;
  border-radius: 4px;
  font-size: 13px;
  display: none;
  white-space: pre-wrap;
}
.input-result-message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.input-result-message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
.activity-date-section {
  padding-top: 20px;
}
.activity-date-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 5px;
}
.activity-date-column {
  text-align: center;
}
.activity-date-column {
  font-size: 12px;
  text-align: center;
}
.activity-date-label {
  font-weight: bold;
  margin-bottom: 2px;
  background:rgb(247, 245, 245) ;
  font-size: 12px;
}
.auto-submit-info {
  font-size: 12px;
  color: #666;
  font-style: italic;
  margin-top: 5px;
}
.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #1677ff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  vertical-align: middle;
  margin-left: 8px;
}
.massive-textarea {
  min-height: 120px;
  resize: vertical;
  font-family: monospace;
  font-size: 14px;
}

/* Searchable dropdown styles */
.searchable-dropdown {
    position: relative;
}
.searchable-dropdown .select-display {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.searchable-dropdown .dropdown-arrow {
    font-size: 12px;
    color: #666;
}
.searchable-dropdown .dropdown-content {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}
.searchable-dropdown .search-input {
    width: 100%;
    padding: 8px 12px;
    border: none;
    border-bottom: 1px solid #eee;
    outline: none;
    font-size: 14px;
}
.searchable-dropdown .dropdown-option {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
}
.searchable-dropdown .dropdown-option:hover {
    background: #f5f5f5;
}
.searchable-dropdown .dropdown-option.selected {
    background: #007bff;
    color: white;
}
.searchable-dropdown .no-results {
    padding: 8px 12px;
    color: #999;
    font-style: italic;
}
.searchable-dropdown.active .dropdown-content {
    display: block;
}
.searchable-dropdown.active .select-display {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
</style>

<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div id="toolbar_left" class="flex items-center gap-2">

      </div>
      <div id="toolbar_right" class="flex items-center gap-2"></div>
    </div>
    <div id="show_data"></div>
  </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<!-- Modal Filter All Item -->
<div id="modal_filter_item" class="modal-container">
  <div class="modal-header">
    <h3 class="modal-title">Data Filter</h3>
    <button class="btn-close" onclick="closeModal('modal_filter_item')">&times;</button>
  </div>
  <div class="modal-body">
  <div class="grid lg:grid-cols-3 gap-2">
    <div class="form-group">
        <span class="form-hint">Activity</span>
        <select class="select" id="activity">
            <option value="">All</option>
            <option value="in-only">In-Only</option>
            <option value="in-stock">In-Stock</option>
            <option value="move">Move</option>
            <option value="out">Out</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Device Size</span>
        <select class="select" id="dvc_size">
            <option value="">All</option>
            <option value="XS">XS</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
            <option value="3XL">3XL</option>
            <option value="ALL">ALL SIZE</option>
            <option value="Cus">Cus</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Device Color</span>
        <select class="select" id="dvc_col">
            <option value="">All</option>
            <option value="Dark Gray">Dark Gray</option>
            <option value="Black">Black</option>
            <option value="Grey">Grey</option>
            <option value="Blue Navy">Blue Navy</option>
            <option value="Green Army">Green Army</option>
            <option value="Red Maroon">Red Maroon</option>
            <option value="Custom">Custom</option>
            <option value="-">-</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">QC Status</span>
        <select class="select" id="dvc_qc">
            <option value="">All</option>
            <option value="LN">LN</option>
            <option value="DN">DN</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Device Type</span>
        <select class="select" id="dvc_type">
            <option value="">All</option>
            <option value="osc">OSC</option>
            <option value="APP">APP</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Location</span>
        <select class="select" id="loc_move">
            <option value="">All</option>
            <option value="Lantai 2">Lantai 2</option>
            <option value="Bang Toni">Bang Toni</option>
            <option value="Om Bob">Om Bob</option>
            <option value="Rekanan">Rekanan</option>
            <option value="LN">LN</option>
            <option value="ECCT">ECCT</option>
            <option value="LN Office">LN Office</option>
            <option value="Lantai 1">Lantai 1</option>
            <option value="Unknow">Unknow</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Sort By</span>
        <select class="select" id="sort_by">
            <option value="">None</option>
            <option value="id_act_asc">ID ASC</option>
            <option value="id_act_desc">ID DESC</option>
            <option value="dvc_sn_asc">Serial Number ASC</option>
            <option value="dvc_sn_desc">Serial Number DESC</option>
        </select>
    </div>
    <div class="form-group">
        <span class="form-hint">Data View</span>
        <select class="select" id="data_view_item">
            <option value="5">5</option>
            <option value="10" selected="selected">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>
    <div class="form-group">
                  <span class="form-hint">Device Code</span>
                  <div class="searchable-dropdown" id="dvc_code_dropdown">
                    <div class="select select-display" id="dvc_code_display">
                        <span id="dvc_code_selected">All</span>
                    </div>
                    <div class="dropdown-content">
                        <input type="text" class="search-input" placeholder="Type to search..." id="dvc_code_search">
                        <div class="dropdown-options" id="dvc_code_options">
                            <!-- Options akan di-populate oleh JavaScript -->
                        </div>
                    </div>
                  </div>
                  <!-- Hidden input untuk menyimpan nilai yang dipilih -->
                  <input type="hidden" id="dvc_code" name="dvc_code" value="">
              </div>
</div>

<div class="activity-date-section">
    <h4 style="margin-bottom: 5px; color: #333;">Activity Date Filters</h4>
    <div class="activity-date-grid">
        <!-- In Activity Dates -->
        <div class="activity-date-column">
            <div class="activity-date-label">IN</div>
            <div class="form-group">
                <span class="form-hint">Date From</span>
                <input class="input" type="date" value="" id="in_date_from" />
            </div>
            <div class="form-group">
                <span class="form-hint">Date To</span>
                <input class="input" type="date" value="" id="in_date_to" />
            </div>
        </div>
        <!-- Move Activity Dates -->
        <div class="activity-date-column">
            <div class="activity-date-label">MOVE</div>
            <div class="form-group">
                <span class="form-hint">Date From</span>
                <input class="input" type="date" value="" id="move_date_from" />
            </div>
            <div class="form-group">
                <span class="form-hint">Date To</span>
                <input class="input" type="date" value="" id="move_date_to" />
            </div>
        </div>
        <!-- Out Activity Dates -->
        <div class="activity-date-column">
            <div class="activity-date-label">OUT</div>
            <div class="form-group">
                <span class="form-hint">Date From</span>
                <input class="input" type="date" value="" id="out_date_from" />
            </div>
            <div class="form-group">
                <span class="form-hint">Date To</span>
                <input class="input" type="date" value="" id="out_date_to" />
            </div>
        </div>
    </div>
</div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeModal('modal_filter_item')">Cancel</button>
    <button class="btn btn-primary" onclick="showDataActivity(); closeModal('modal_filter_item');">Submit</button>
  </div>
</div>

<!-- Modal Input Gabungan In/Move/Out dengan Tab -->
<div id="modal_input_all" class="modal-container" style="min-width:500px; max-width: 650px;">
  <div class="modal-header">
    <h3 class="modal-title" style="font-size: 24px;">Input Inventory</h3>
    <button class="btn-close" onclick="closeModal('modal_input_all')" style="font-size: 24px;">&times;</button>
  </div>

  <div class="modal-body" style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Tab Navigation - Now aligned horizontally -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <!-- Tab Buttons -->
      <div id="inputTabButtons">
        <button type="button" class="input-tab-btn active" onclick="showInputTab('in')" id="tabBtn_in" style="font-size: 24px; padding: 12px 24px; margin-right: 0; border-top-right-radius: 0; border-bottom-right-radius: 0;">in</button>
        <button type="button" class="input-tab-btn" onclick="showInputTab('move')" id="tabBtn_move" style="font-size: 24px; padding: 12px 24px; margin-left: -1px; margin-right: 0; border-radius: 0;">move</button>
        <button type="button" class="input-tab-btn" onclick="showInputTab('out')" id="tabBtn_out" style="font-size: 24px; padding: 12px 24px; margin-left: -1px; border-top-left-radius: 0; border-bottom-left-radius: 0;">out</button>
      </div>

      <!-- Mode Toggle Buttons -->
      <div>
        <button type="button" class="input-mode-btn active" onclick="switchInput('singular')" id="btn_singular" style="padding: 12px 24px; font-size: 24px; margin-right: 0; border-top-right-radius: 0; border-bottom-right-radius: 0;">Singular</button>
        <button type="button" class="input-mode-btn" onclick="switchInput('massive')" id="btn_massive" style="padding: 12px 24px; font-size: 24px; margin-left: -1px; border-top-left-radius: 0; border-bottom-left-radius: 0;">Massive</button>
      </div>
    </div>

    <div style="display: flex; gap: 24px; justify-content: space-between; align-items: flex-start;">
      <!-- Input In -->
      <div id="inputTab_in" class="input-tab-content" style="flex:1; min-width: 220px; border-right:1px solid #eee; padding:0 16px; display: flex; flex-direction: column; justify-content: flex-start;">
        <div class="form-group" style="margin-bottom: 24px;">
          <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">QC Status</label>
          <select id="in_qc_status" class="select" style="font-size: 24px;">
            <option value="DN">DN</option>
            <option value="LN">LN</option>
          </select>
        </div>

        <!-- Singular Input -->
        <div id="in_singular_container">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Number</label>
            <input type="text" id="in_serial_number" class="input" placeholder="Masukan nomor seri di sini" style="font-size: 24px;" maxlength="15" />
          </div>
        </div>

        <!-- Massive Input -->
        <div id="in_massive_container" style="display: none;">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Numbers</label>
            <textarea id="in_serial_numbers_massive" class="input massive-textarea" placeholder="Enter serial numbers, one per line or separated by tabs" style="font-size: 20px;"></textarea>
            <!-- START MODIFIKASI: Petunjuk untuk input massal -->
            <small style="color: #666; font-size: 12px;">
              Format: `SN` atau `SN yyyy-mm-dd`.
            </small>
            <!-- END MODIFIKASI -->
          </div>
          <button class="btn btn-primary" onclick="submitInput('in')" style="font-size: 24px;">
            Submit <span id="in_loading_spinner" class="loading-spinner" style="display:none;"></span>
          </button>
        </div>
        <div id="in_result_message" class="input-result-message" style="font-size: 24px;"></div>
      </div>

      <!-- Input Move -->
      <div id="inputTab_move" class="input-tab-content" style="flex:1; min-width: 220px; border-right:1px solid #eee; padding:0 16px; display: none; flex-direction: column; justify-content: flex-start;">
        <div class="form-group" style="margin-bottom: 24px;">
          <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Lokasi Tujuan</label>
          <select id="move_location" class="select" style="font-size: 24px;">
            <option value="">-- Pilih Lokasi Tujuan --</option>
            <option value="Lantai 2">🏢 Lantai 2</option>
            <option value="Bang Toni">👨‍💼 Bang Toni</option>
            <option value="Om Bob">👨‍💼 Om Bob</option>
            <option value="Rekanan">🤝 Rekanan</option>
            <option value="LN">🏭 LN</option>
            <option value="ECCT">🏭 ECCT</option>
            <option value="LN Office">🏢 LN Office</option>
            <option value="Lantai 1">🏢 Lantai 1</option>
            <option value="Unknow">❓ Unknown</option>
          </select>
        </div>

        <!-- Singular Input -->
        <div id="move_singular_container">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Number</label>
            <input type="text" id="move_serial_number" class="input" placeholder="Masukan nomor seri di sini" style="font-size: 24px;" maxlength="15" />
          </div>
        </div>

        <!-- Massive Input -->
        <div id="move_massive_container" style="display: none;">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Numbers</label>
            <textarea id="move_serial_numbers_massive" class="input massive-textarea" placeholder="Enter serial numbers, one per line or separated by tabs" style="font-size: 24px;"></textarea>
            <small style="color: #666; font-size: 12px;">
              Format: `SN` atau `SN yyyy-mm-dd`.
            </small>
          </div>
          <button class="btn btn-primary" onclick="submitInput('move')" style="font-size: 24px;">
            Submit <span id="move_loading_spinner" class="loading-spinner" style="display:none;"></span>
          </button>
        </div>

        <div id="move_result_message" class="input-result-message" style="font-size: 24px;"></div>
      </div>

      <!-- Input Out -->
      <div id="inputTab_out" class="input-tab-content" style="flex:1; min-width: 220px; padding-left:16px; display: none; flex-direction: column; justify-content: flex-start;">
        <!-- Singular Input -->
        <div id="out_singular_container">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Number</label>
            <input type="text" id="out_serial_number" class="input" placeholder="Masukan nomor seri di sini" style="font-size: 24px;" maxlength="15" />
          </div>
        </div>

        <!-- Massive Input -->
        <div id="out_massive_container" style="display: none;">
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="input-form-label" style="font-size: 24px; display: block; margin-bottom: 12px; font-weight: 600;">Serial Numbers</label>
            <textarea id="out_serial_numbers_massive" class="input massive-textarea" placeholder="Enter serial numbers, one per line or separated by tabs" style="font-size: 24px;"></textarea>
            <small style="color: #666; font-size: 12px;">
              Format: `SN` atau `SN yyyy-mm-dd`.
            </small>
          </div>
          <button class="btn btn-primary" onclick="submitInput('out')" style="font-size: 24px;">
            Submit <span id="out_loading_spinner" class="loading-spinner" style="display:none;"></span>
          </button>
        </div>

        <div id="out_result_message" class="input-result-message" style="font-size: 24px;"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
// Set inventory type untuk universal script
window.INVENTORY_TYPE = 'ECBS';

// CONFIG object untuk universal script
window.CONFIG = {
  urlMenu: '<?php echo $config['url_menu']; ?>'
};

// Backward compatibility variables
var currentEcbsType = 'app';

window.deviceCodes = [
    <?php 
    if(!empty($dvc_code)) {
        $codes = [];
        foreach($dvc_code as $code) {
            $codes[] = "{ dvc_code: '" . addslashes($code->dvc_code) . "' }";
        }
        echo implode(",\n    ", $codes);
    }
    ?>
];
</script>


<!-- Load Universal Inventory Script -->
<script src="<?php echo base_url('js/inventory.js'); ?> ? v = 1.3"></script>
