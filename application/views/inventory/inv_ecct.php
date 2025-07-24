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
    font-size: 18px;
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
  padding: 6px 18px;
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
    <div class="grid lg:grid-cols-3">
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
          <option value="0">LN</option>
          <option value="1">DN</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">Date From</span>
        <input class="input calendar" type="text" value="" id="date_from" />
      </div>
      <div class="form-group">
        <span class="form-hint">Date To</span>
        <input class="input calendar" type="text" value="" id="date_to" />
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
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeModal('modal_filter_item')">Cancel</button>
    <button class="btn btn-primary" onclick="showDataAllItem(); closeModal('modal_filter_item');">Submit</button>
  </div>
</div>

<!-- Modal Input Gabungan In/Move/Out dengan Tab -->
<div id="modal_input_all" class="modal-container" style="min-width:500px;">
  <div class="modal-header">
    <h3 class="modal-title">Input Inventory</h3>
    <button class="btn-close" onclick="closeModal('modal_input_all')">&times;</button>
  </div>

  <div class="modal-body" style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Tab Buttons -->
    <div id="inputTabButtons" style="margin-bottom: 18px;">
      <button type="button" class="input-tab-btn active" onclick="showInputTab('in')" id="tabBtn_in">in</button>
      <button type="button" class="input-tab-btn" onclick="showInputTab('move')" id="tabBtn_move">move</button>
      <button type="button" class="input-tab-btn" onclick="showInputTab('out')" id="tabBtn_out">out</button>

    </div>
    <div style="display: flex; gap: 24px; justify-content: space-between; align-items: flex-start;">
      <!-- Input In -->
      <div id="inputTab_in" class="input-tab-content" style="flex:1; min-width: 220px; border-right:1px solid #eee; padding:0 16px; display: flex; flex-direction: column; justify-content: flex-start;">
        <div class="form-group">
          <label class="input-form-label">QC Status</label>
          <select id="in_qc_status" class="select">
            <option value="0">LN</option>
            <option value="1">DN</option>
          </select>
        </div>
        <div class="form-group">
          <label class="input-form-label">Serial Number</label>
          <input type="text" id="in_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
        </div>
        <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('in')">Submit</button>
        <div id="in_result_message" class="input-result-message"></div>
      </div>
      <!-- Input Move -->
      <div id="inputTab_move" class="input-tab-content" style="flex:1; min-width: 220px; border-right:1px solid #eee; padding:0 16px; display: none; flex-direction: column; justify-content: flex-start;">
        <div class="form-group">
        <div class="form-group">
          <label class="input-form-label">Lokasi Tujuan</label>
          <select id="move_location" class="select">
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
          <label class="input-form-label">Serial Number</label>
          <input type="text" id="move_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
        </div>
        <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('move')">Submit</button>
        <div id="move_result_message" class="input-result-message"></div>
      </div>
      <!-- Input Out -->
      <div id="inputTab_out" class="input-tab-content" style="flex:1; min-width: 220px; padding-left:16px; display: none; flex-direction: column; justify-content: flex-start;">
        <div class="form-group">
          <label class="input-form-label">Serial Number</label>
          <input type="text" id="out_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
        </div>
        <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('out')">Submit</button>
        <div id="out_result_message" class="input-result-message"></div>
      </div>
    </div>
  </div>
</div>

<!-- Pindahkan script ke file eksternal -->
<script src="/assets/js/inv_ecct.js"></script>