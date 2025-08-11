<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div id="toolbar_left" class="flex items-center gap-2">
        <button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal('modal_input_all')" id="input_btn" type="button">Input</button>
        <span class="ki-duotone ki-information-2" onclick="openModal('modal_period_info')" title="Klik untuk melihat informasi periode kantor" style="cursor: pointer; font-size: 20px; color: #0074d9;"></span>
      </div>
      <div id="toolbar_right" class="flex items-center gap-2">
        <div class="input-group input-sm">
          <select class="select" id="year_filter">
            <option value="">Pilih Tahun</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
          </select>
          <select class="select select-bulan" id="month_filter">
            <option value="">Pilih Bulan</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>
          <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportInvWeekData();">
            <i class="ki-filled ki-exit-down !text-base"></i>Export
          </a>
        </div>
      </div>
    </div>
    <div id="result_message" class="input-result-message" style="display:none;"></div>
    <div id="show_data"></div>
  </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<!-- Modal Input All -->
<div id="modal_input_all" class="modal-container" style="min-width:500px; max-width: 650px;">
  <div class="modal-header">
    <h3 class="modal-title">Generate Periode Mingguan</h3>
    <button class="btn-close" onclick="closeModal('modal_input_all')" style="font-size: 24px;">&times;</button>
  </div>
  <div class="modal-body"></div>
  <div class="modal-footer"></div>
</div>

<!-- Modal Edit -->
<div id="modal_edit" class="modal-container">
  <div class="modal-header">
    <h3 class="modal-title">Edit Periode</h3>
    <button class="btn-close" onclick="closeModal('modal_edit')">&times;</button>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <span class="form-hint">Tanggal Mulai</span>
      <input type="datetime-local" class="input" id="edit_date_start">
      <small style="color: #666; font-size: 12px;">Waktu akan otomatis diset ke 08:00</small>
    </div>
    <div class="form-group">
      <span class="form-hint">Tanggal Selesai</span>
      <input type="datetime-local" class="input" id="edit_date_finish">
      <small style="color: #666; font-size: 12px;">Waktu akan otomatis diset ke 17:00</small>
    </div>
    <input type="hidden" id="edit_id_week">
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modal_edit')">Batal</button>
    <button class="btn btn-primary" onclick="updatePeriod()">Update</button>
  </div>
</div>

<!-- Modal Period Info -->
<div id="modal_period_info" class="modal-container" style="max-width: 600px;">
  <div class="modal-header">
    <h3 class="modal-title">Informasi Periode Kantor</h3>
    <button class="btn-close" onclick="closeModal('modal_period_info')">&times;</button>
  </div>
  <div class="modal-body">
    <p><strong>Logika Periode:</strong> 1 bulan di kantor dimulai dari tanggal 27 bulan sebelumnya sampai tanggal 26 bulan ini</p>
    <p><strong>Waktu Kerja:</strong> Mulai jam 08:00 pagi, selesai jam 17:00 sore</p>
    <p><strong>Minggu Kerja:</strong> Senin-Jumat (5 hari kerja per minggu)</p>
    <p><strong>Contoh:</strong> Periode Januari 2024 = 27 Desember 2023 (08:00) s/d 26 Januari 2024 (17:00)</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" onclick="closeModal('modal_period_info')">Tutup</button>
  </div>
</div>

<style>
:root { --primary: #0074d9; --border: #e5e5e5; --bg: #f8f9fa; --radius: 8px; }
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
.modal-container { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%); background: white; border-radius: var(--radius); box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; min-width: 350px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header, .modal-footer { padding: 20px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; }
.modal-header { justify-content: space-between; border-bottom: 1px solid var(--border); border-radius: var(--radius) var(--radius) 0 0; }
.modal-footer { justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius); }
.modal-title { margin: 0; font: 600 20px/1 sans-serif; color: #333; }
.modal-body { padding: 20px; }
.btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; transition: color 0.2s; }
.btn-close:hover { color: #000; }
.form-group { margin-bottom: 15px; }
.form-hint { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #333; }
.select, .input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; transition: border-color 0.2s; min-height: 40px; line-height: 1.4; }
.select:focus, .input:focus { outline: none; border-color: var(--primary); }
.select option { 
  padding: 8px 12px !important; 
  font-size: 14px !important; 
  font-weight: 500 !important; 
  background-color: #ffffff !important;
  color: #333 !important;
  border: none !important;
  outline: none !important;
}
#year, #month { width: 100% !important; max-width: none !important; }

select.select { 
  text-overflow: unset !important; 
  overflow: visible !important; 
  border: 1px solid #ddd !important;
  border-radius: 4px !important;
  outline: none !important;
}
.input-group { 
  display: flex; 
  align-items: center; 
  gap: 5px; 
  border: none !important;
  outline: none !important;
}
.input-group .select { 
  flex: 1; 
  min-width: 150px; 
  border: 1px solid #ddd !important;
  border-radius: 4px !important;
}
.input-group .btn { 
  white-space: nowrap; 
  border: none !important;
}
#month_filter { 
  min-height: 35px !important; 
  height: 35px !important; 
  line-height: 1.2 !important; 
  padding: 8px 12px !important; 
  font-size: 14px !important; 
  font-weight: 500 !important; 
  background-color: #ffffff !important; 
  border-radius: 4px !important; 
  border: 1px solid #ddd !important; 
  border-right: 1px solid #ddd !important;
  color: #333 !important; 
  width: 100% !important;
  transition: border-color 0.2s !important;
  outline: none !important;
}
#year_filter { 
  min-height: 35px !important; 
  height: 35px !important; 
  line-height: 1.2 !important; 
  padding: 8px 12px !important; 
  font-size: 14px !important; 
  font-weight: 500 !important; 
  background-color: #ffffff !important; 
  border-radius: 4px !important; 
  border: 1px solid #ddd !important; 
  color: #333 !important; 
  width: 100% !important;
  transition: border-color 0.2s !important;
  outline: none !important;
}
#year_filter:hover { border-color: #0074d9 !important; }
#year_filter:focus { border-color: #0074d9 !important; outline: none !important; box-shadow: 0 0 0 2px rgba(0,116,217,0.2) !important; }
#month_filter:hover { 
  border-color: #0074d9 !important; 
}
#month_filter:focus { 
  border-color: #0074d9 !important; 
  outline: none !important; 
  box-shadow: 0 0 0 2px rgba(0, 116, 217, 0.2) !important; 
}
#month_filter option { 
  font-size: 14px !important; 
  font-weight: 500 !important; 
  padding: 8px 12px !important; 
  background-color: #ffffff !important;
  color: #333 !important;
  border: none !important;
  outline: none !important;
}
#year_filter option { 
  font-size: 14px !important; 
  font-weight: 500 !important; 
  padding: 8px 12px !important; 
  background-color: #ffffff !important;
  color: #333 !important;
  border: none !important;
  outline: none !important;
}
.btn { display: inline-flex !important; align-items: center !important; cursor: pointer !important; line-height: 1 !important; border-radius: 0.375rem !important; height: 2.5rem !important; padding-inline-start: 1rem !important; padding-inline-end: 1rem !important; gap: 0.375rem !important; border: 1px solid transparent !important; font-weight: 500 !important; font-size: 0.8125rem !important; outline: none !important; }
.btn-sm { height: 2rem !important; padding-inline-start: 0.75rem !important; padding-inline-end: 0.75rem !important; font-weight: 500 !important; font-size: 0.75rem !important; gap: 0.275rem !important; }
.btn i { font-size: 1.125rem !important; line-height: 0 !important; }
.btn-sm i { font-size: 0.875rem !important; }
.input { display: block !important; width: 100% !important; -webkit-appearance: none !important; -moz-appearance: none !important; appearance: none !important; box-shadow: none !important; outline: none !important; font-weight: 500 !important; font-size: 0.8125rem !important; line-height: 1 !important; background-color: var(--tw-light-active) !important; border-radius: 0.375rem !important; height: 2.5rem !important; padding-inline-start: 0.75rem !important; padding-inline-end: 0.75rem !important; border: 1px solid var(--tw-gray-300) !important; color: var(--tw-gray-700) !important; }
.input:hover { border-color: var(--tw-gray-400) !important; }
.input:focus { border-color: var(--tw-primary) !important; box-shadow: var(--tw-input-focus-box-shadow) !important; color: var(--tw-gray-700) !important; }
.select { 
  font-weight: 500 !important; 
  font-size: 14px !important; 
  line-height: 1.4 !important; 
  background-color: #ffffff !important; 
  border-radius: 4px !important; 
  height: 40px !important; 
  padding: 10px 12px !important; 
  border: 1px solid #ddd !important; 
  border-right: 1px solid #ddd !important;
  color: #333 !important; 
  width: 100% !important;
  transition: border-color 0.2s !important;
  outline: none !important;
}
.select:hover { 
  border-color: #0074d9 !important; 
}
.select:focus { 
  border-color: #0074d9 !important; 
  outline: none !important; 
  box-shadow: 0 0 0 2px rgba(0, 116, 217, 0.2) !important; 
}
.select-sm { font-weight: 500 !important; font-size: 0.75rem !important; height: 2rem !important; padding-inline-start: 0.625rem !important; padding-inline-end: 0.625rem !important; background-size: 14px 10px !important; background-position: inset-inline-end 0.55rem center !important; }
.card-header { padding: 1rem 1.5rem !important; border-bottom: 1px solid var(--tw-gray-200) !important; background-color: var(--tw-white) !important; }
.card-header .btn { margin: 0 !important; }
.card-header .btn-success { background-color: #28a745 !important; border-color: #28a745 !important; color: white !important; }
.card-header .btn-success:hover { background-color: #218838 !important; border-color: #1e7e34 !important; }
.card-header .btn-light { background-color: var(--tw-gray-100) !important; border-color: var(--tw-gray-300) !important; color: var(--tw-gray-700) !important; }
.card-header .btn-light:hover { background-color: var(--tw-gray-200) !important; border-color: var(--tw-gray-400) !important; }

/* Custom styling for Export button in inv_week */
.input-group .btn-light {
  background-color: #ffffff !important;
  border: 1px solid #ddd !important;
  color: #333 !important;
  box-shadow: none !important;
  border-radius: 4px !important;
  transition: border-color 0.2s !important;
}

.input-group .btn-light:hover {
  background-color: #f8f9fa !important;
  border-color: #0074d9 !important;
  box-shadow: none !important;
}

.input-group .btn-light:focus {
  outline: none !important;
  box-shadow: 0 0 0 2px rgba(0,116,217,0.2) !important;
}

/* Styling for modal buttons */
.btn-secondary {
  background-color: #6c757d !important;
  border: 1px solid #6c757d !important;
  color: white !important;
  box-shadow: none !important;
}

.btn-secondary:hover {
  background-color: #5a6268 !important;
  border-color: #5a6268 !important;
  color: white !important;
}

.btn-lihat-data {
  background-color: #ffffff !important;
  border: 1px solid #0074d9 !important;
  color: #0074d9 !important;
  box-shadow: none !important;
}

.btn-lihat-data:hover {
  background-color: #f8f9fa !important;
  border-color: #0056b3 !important;
  color: #0056b3 !important;
}

.btn-regenerate {
  background-color: #0074d9 !important;
  border: 1px solid #0074d9 !important;
  color: white !important;
  box-shadow: none !important;
}

.btn-regenerate:hover {
  background-color: #0056b3 !important;
  border-color: #0056b3 !important;
  color: white !important;
}

/* Styling for confirmation buttons */
.btn-confirm {
  background-color: #0074d9 !important;
  border: 1px solid #0074d9 !important;
  color: white !important;
  box-shadow: none !important;
  padding: 6px 12px !important;
  border-radius: 4px !important;
  font-weight: 500 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  min-width: 40px !important;
}

.btn-confirm:hover {
  background-color: #0056b3 !important;
  border-color: #0056b3 !important;
  color: white !important;
}

.btn-cancel {
  background-color: #ffffff !important;
  border: 1px solid #0074d9 !important;
  color: #0074d9 !important;
  box-shadow: none !important;
  padding: 6px 12px !important;
  border-radius: 4px !important;
  font-weight: 500 !important;
  font-size: 12px !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  min-width: 40px !important;
}

.btn-cancel:hover {
  background-color: #f8f9fa !important;
  border-color: #0056b3 !important;
  color: #0056b3 !important;
}

/* Loading spinner animation */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-spinner {
  animation: spin 1s linear infinite;
}

/* Styling for confirmation section layout */
.confirmation-section {
  margin-top: 15px !important;
  padding: 15px !important;
  background-color: #f8f9fa !important;
  border-radius: 6px !important;
  border: 1px solid #e9ecef !important;
}

.confirmation-content {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 15px !important;
}

.confirmation-text {
  color: #495057 !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  flex: 1 !important;
}

.confirmation-buttons {
  display: flex !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
}
.card-body { padding: 1.5rem !important; }
.table { font-size: 0.875rem !important; }
.table th { font-weight: 600 !important; font-size: 0.875rem !important; padding: 0.75rem !important; }
.table td { font-size: 0.875rem !important; padding: 0.75rem !important; }
.form-label { font-weight: 500 !important; font-size: 0.875rem !important; color: var(--tw-gray-700) !important; margin-bottom: 0.5rem !important; }
.form-hint { font-size: 0.75rem !important; color: var(--tw-gray-500) !important; margin-top: 0.25rem !important; }
.modal-header { padding: 1rem 1.5rem !important; border-bottom: 1px solid var(--tw-gray-200) !important; }
.modal-title { font-weight: 600 !important; font-size: 1.125rem !important; color: var(--tw-gray-900) !important; }
.modal-body { padding: 1.5rem !important; }
.modal-footer { padding: 1rem 1.5rem !important; border-top: 1px solid var(--tw-gray-200) !important; }
.text-muted { color: var(--tw-gray-500) !important; }
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #17a2b8 !important; }
.badge { font-size: 0.75rem !important; font-weight: 500 !important; padding: 0.25rem 0.5rem !important; border-radius: 0.25rem !important; }
.badge-success { background-color: #28a745 !important; color: white !important; }
.badge-danger { background-color: #dc3545 !important; color: white !important; }
.badge-warning { background-color: #ffc107 !important; color: #212529 !important; }
.badge-info { background-color: #17a2b8 !important; color: white !important; }
.alert { padding: 0.75rem 1rem !important; margin-bottom: 1rem !important; border: 1px solid transparent !important; border-radius: 0.375rem !important; }
.alert-success { background-color: #d4edda !important; border-color: #c3e6cb !important; color: #155724 !important; }
.alert-danger { background-color: #f8d7da !important; border-color: #f5c6cb !important; color: #721c24 !important; }
.alert-warning { background-color: #fff3cd !important; border-color: #ffeaa7 !important; color: #856404 !important; }
.alert-info { background-color: #d1ecf1 !important; border-color: #bee5eb !important; color: #0c5460 !important; }
.input-result-message { display:none; padding: 10px 12px; border-radius: 6px; margin: 12px 16px; font-weight: 500; }
.input-result-message.success { display:block; background: #d4edda; color:#155724; border:1px solid #c3e6cb; }
.input-result-message.error { display:block; background: #f8d7da; color:#721c24; border:1px solid #f5c6cb; }
.input-result-message.warning { display:block; background: #fff3cd; color:#856404; border:1px solid #ffeaa7; }
.input-result-message.info { display:block; background: #d1ecf1; color:#0c5460; border:1px solid #bee5eb; }
.btn-group { display: inline-flex !important; }
.btn-group .btn { border-radius: 0 !important; }
.btn-group .btn:first-child { border-top-left-radius: 0.375rem !important; border-bottom-left-radius: 0.375rem !important; }
.btn-group .btn:last-child { border-top-right-radius: 0.375rem !important; border-bottom-right-radius: 0.375rem !important; }
.dropdown-menu { font-size: 0.875rem !important; }
.dropdown-item { font-size: 0.875rem !important; padding: 0.5rem 1rem !important; }
.pagination { font-size: 0.875rem !important; }
.page-link { font-size: 0.875rem !important; padding: 0.5rem 0.75rem !important; }
.progress { height: 0.5rem !important; font-size: 0.75rem !important; }
.list-group-item { font-size: 0.875rem !important; padding: 0.75rem 1rem !important; }
.nav-tabs .nav-link { font-size: 0.875rem !important; padding: 0.75rem 1rem !important; }
.nav-pills .nav-link { font-size: 0.875rem !important; padding: 0.75rem 1rem !important; }
.tooltip { font-size: 0.75rem !important; }
.popover { font-size: 0.875rem !important; }
.modal-dialog { font-size: 0.875rem !important; }
.form-control { font-size: 0.875rem !important; }
.form-select { font-size: 0.875rem !important; }
.form-check-label { font-size: 0.875rem !important; }
.form-check-input { font-size: 0.875rem !important; }
.input-group-text { font-size: 0.875rem !important; }
/* Removed duplicate btn-close style */
.accordion-button { font-size: 0.875rem !important; }
.accordion-body { font-size: 0.875rem !important; }
.carousel-caption { font-size: 0.875rem !important; }
.toast { font-size: 0.875rem !important; }
.toast-header { font-size: 0.875rem !important; }
.toast-body { font-size: 0.875rem !important; }
.offcanvas-title { font-size: 1.125rem !important; }
.offcanvas-body { font-size: 0.875rem !important; }
/* Additional styles for complete consistency */
.container-fixed { max-width: 1280px !important; margin: 0 auto !important; padding: 0 1rem !important; }
@media (min-width: 1280px) { .container-fixed { padding: 0 2rem !important; } }
.card { background-color: var(--tw-white) !important; border: 1px solid var(--tw-gray-200) !important; border-radius: 0.5rem !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important; }
.card-header { background-color: var(--tw-white) !important; border-bottom: 1px solid var(--tw-gray-200) !important; border-top-left-radius: 0.5rem !important; border-top-right-radius: 0.5rem !important; }
.card-body { background-color: var(--tw-white) !important; }
.card-footer { background-color: var(--tw-gray-50) !important; border-top: 1px solid var(--tw-gray-200) !important; border-bottom-left-radius: 0.5rem !important; border-bottom-right-radius: 0.5rem !important; }
.table { width: 100% !important; border-collapse: collapse !important; }
.table th { background-color: var(--tw-gray-50) !important; border-bottom: 2px solid var(--tw-gray-200) !important; text-align: left !important; font-weight: 600 !important; color: var(--tw-gray-900) !important; }
.table td { border-bottom: 1px solid var(--tw-gray-200) !important; color: var(--tw-gray-700) !important; }
.table tbody tr:hover { background-color: var(--tw-gray-50) !important; }
.table-striped tbody tr:nth-child(odd) { background-color: var(--tw-gray-50) !important; }
.table-striped tbody tr:nth-child(odd):hover { background-color: var(--tw-gray-100) !important; }
.table-bordered { border: 1px solid var(--tw-gray-200) !important; }
.table-bordered th, .table-bordered td { border: 1px solid var(--tw-gray-200) !important; }
.table-sm th, .table-sm td { padding: 0.5rem !important; }
.table-responsive { overflow-x: auto !important; }
.modal { background-color: rgba(0, 0, 0, 0.5) !important; }
.modal-content { background-color: var(--tw-white) !important; border: 1px solid var(--tw-gray-200) !important; border-radius: 0.5rem !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
.modal-header { border-bottom: 1px solid var(--tw-gray-200) !important; }
.modal-footer { border-top: 1px solid var(--tw-gray-200) !important; }
/* Removed conflicting Bootstrap btn-close styles to prevent double X */
.dropdown-menu { position: absolute !important; top: 100% !important; left: 0 !important; z-index: 1000 !important; display: none !important; min-width: 10rem !important; padding: 0.5rem 0 !important; margin: 0.125rem 0 0 !important; font-size: 0.875rem !important; color: var(--tw-gray-700) !important; text-align: left !important; list-style: none !important; background-color: var(--tw-white) !important; background-clip: padding-box !important; border: 1px solid var(--tw-gray-200) !important; border-radius: 0.375rem !important; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
.dropdown-menu.show { display: block !important; }
.dropdown-item { display: block !important; width: 100% !important; padding: 0.5rem 1rem !important; clear: both !important; font-weight: 400 !important; color: var(--tw-gray-700) !important; text-align: inherit !important; text-decoration: none !important; white-space: nowrap !important; background-color: transparent !important; border: 0 !important; }
.dropdown-item:hover { color: var(--tw-gray-900) !important; background-color: var(--tw-gray-100) !important; }
.dropdown-item:focus { color: var(--tw-gray-900) !important; background-color: var(--tw-gray-100) !important; }
.dropdown-item:active { color: var(--tw-white) !important; text-decoration: none !important; background-color: var(--tw-primary) !important; }
.dropdown-item.disabled { color: var(--tw-gray-500) !important; pointer-events: none !important; background-color: transparent !important; }
.dropdown-divider { height: 0 !important; margin: 0.5rem 0 !important; overflow: hidden !important; border-top: 1px solid var(--tw-gray-200) !important; }
.dropdown-header { display: block !important; padding: 0.5rem 1rem !important; margin-bottom: 0 !important; font-size: 0.875rem !important; color: var(--tw-gray-500) !important; white-space: nowrap !important; }
.dropdown-text { display: block !important; padding: 0.5rem 1rem !important; color: var(--tw-gray-700) !important; }
.dropdown-menu-end { right: 0 !important; left: auto !important; }
.dropdown-menu-start { right: auto !important; left: 0 !important; }
.dropdown-menu-sm-start { right: auto !important; left: 0 !important; }
.dropdown-menu-sm-end { right: 0 !important; left: auto !important; }
.dropdown-menu-md-start { right: auto !important; left: 0 !important; }
.dropdown-menu-md-end { right: 0 !important; left: auto !important; }
.dropdown-menu-lg-start { right: auto !important; left: 0 !important; }
.dropdown-menu-lg-end { right: 0 !important; left: auto !important; }
.dropdown-menu-xl-start { right: auto !important; left: 0 !important; }
.dropdown-menu-xl-end { right: 0 !important; left: auto !important; }
.dropdown-menu-xxl-start { right: auto !important; left: 0 !important; }
.dropdown-menu-xxl-end { right: 0 !important; left: auto !important; }
.dropdown-menu-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-sm-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-md-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-lg-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-xl-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-xxl-center { left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-toggle::after { display: inline-block !important; margin-left: 0.255em !important; vertical-align: 0.255em !important; content: "" !important; border-top: 0.3em solid !important; border-right: 0.3em solid transparent !important; border-bottom: 0 !important; border-left: 0.3em solid transparent !important; }
.dropdown-toggle:empty::after { margin-left: 0 !important; }
.dropdown-menu { margin-top: 0.125rem !important; }
.dropdown-menu[data-bs-popper] { top: 100% !important; left: 0 !important; margin-top: 0.125rem !important; }
.dropdown-menu-start { --bs-position: start !important; }
.dropdown-menu-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-end { --bs-position: end !important; }
.dropdown-menu-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-center { --bs-position: center !important; }
.dropdown-menu-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-sm-start { --bs-position: start !important; }
.dropdown-menu-sm-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-sm-end { --bs-position: end !important; }
.dropdown-menu-sm-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-sm-center { --bs-position: center !important; }
.dropdown-menu-sm-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-md-start { --bs-position: start !important; }
.dropdown-menu-md-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-md-end { --bs-position: end !important; }
.dropdown-menu-md-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-md-center { --bs-position: center !important; }
.dropdown-menu-md-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-lg-start { --bs-position: start !important; }
.dropdown-menu-lg-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-lg-end { --bs-position: end !important; }
.dropdown-menu-lg-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-lg-center { --bs-position: center !important; }
.dropdown-menu-lg-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-xl-start { --bs-position: start !important; }
.dropdown-menu-xl-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-xl-end { --bs-position: end !important; }
.dropdown-menu-xl-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-xl-center { --bs-position: center !important; }
.dropdown-menu-xl-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
.dropdown-menu-xxl-start { --bs-position: start !important; }
.dropdown-menu-xxl-start[data-bs-popper] { right: auto !important; left: 0 !important; }
.dropdown-menu-xxl-end { --bs-position: end !important; }
.dropdown-menu-xxl-end[data-bs-popper] { right: 0 !important; left: auto !important; }
.dropdown-menu-xxl-center { --bs-position: center !important; }
.dropdown-menu-xxl-center[data-bs-popper] { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; }
@media (min-width: 576px) { .dropdown-menu-sm-start { right: auto !important; left: 0 !important; }
  .dropdown-menu-sm-end { right: 0 !important; left: auto !important; }
  .dropdown-menu-sm-center { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; } }
@media (min-width: 768px) { .dropdown-menu-md-start { right: auto !important; left: 0 !important; }
  .dropdown-menu-md-end { right: 0 !important; left: auto !important; }
  .dropdown-menu-md-center { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; } }
@media (min-width: 992px) { .dropdown-menu-lg-start { right: auto !important; left: 0 !important; }
  .dropdown-menu-lg-end { right: 0 !important; left: auto !important; }
  .dropdown-menu-lg-center { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; } }
@media (min-width: 1200px) { .dropdown-menu-xl-start { right: auto !important; left: 0 !important; }
  .dropdown-menu-xl-end { right: 0 !important; left: auto !important; }
  .dropdown-menu-xl-center { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; } }
@media (min-width: 1400px) { .dropdown-menu-xxl-start { right: auto !important; left: 0 !important; }
  .dropdown-menu-xxl-end { right: 0 !important; left: auto !important; }
  .dropdown-menu-xxl-center { right: auto !important; left: 50% !important; transform: translateX(-50%) !important; } }
.dropup .dropdown-menu { top: auto !important; bottom: 100% !important; margin-top: 0 !important; margin-bottom: 0.125rem !important; }
.dropup .dropdown-toggle::after { display: inline-block !important; margin-left: 0.255em !important; vertical-align: 0.255em !important; content: "" !important; border-top: 0 !important; border-right: 0.3em solid transparent !important; border-bottom: 0.3em solid !important; border-left: 0.3em solid transparent !important; }
.dropup .dropdown-toggle:empty::after { margin-left: 0 !important; }
.dropend .dropdown-menu { top: 0 !important; right: auto !important; left: 100% !important; margin-top: 0 !important; margin-left: 0.125rem !important; }
.dropend .dropdown-toggle::after { display: inline-block !important; margin-left: 0.255em !important; vertical-align: 0.255em !important; content: "" !important; border-top: 0.3em solid transparent !important; border-right: 0 !important; border-bottom: 0.3em solid transparent !important; border-left: 0.3em solid !important; }
.dropend .dropdown-toggle:empty::after { margin-left: 0 !important; }
.dropend .dropdown-toggle::after { vertical-align: 0 !important; }
.dropstart .dropdown-menu { top: 0 !important; right: 100% !important; left: auto !important; margin-top: 0 !important; margin-right: 0.125rem !important; }
.dropstart .dropdown-toggle::after { display: inline-block !important; margin-left: 0.255em !important; vertical-align: 0.255em !important; content: "" !important; border-top: 0.3em solid transparent !important; border-right: 0.3em solid !important; border-bottom: 0.3em solid transparent !important; }
.dropstart .dropdown-toggle::before { display: inline-block !important; margin-right: 0.255em !important; vertical-align: 0.255em !important; content: "" !important; border-top: 0.3em solid transparent !important; border-right: 0.3em solid !important; border-bottom: 0.3em solid transparent !important; }
.dropstart .dropdown-toggle:empty::after { margin-left: 0 !important; }
.dropstart .dropdown-toggle::before { vertical-align: 0 !important; }
.dropdown-divider { height: 0 !important; margin: 0.5rem 0 !important; overflow: hidden !important; border-top: 1px solid var(--tw-gray-200) !important; }
.dropdown-item-text { display: block !important; width: 100% !important; padding: 0.5rem 1rem !important; clear: both !important; font-weight: 400 !important; color: var(--tw-gray-700) !important; text-align: inherit !important; text-decoration: none !important; white-space: nowrap !important; background-color: transparent !important; border: 0 !important; }
.dropdown-item-text:hover { color: var(--tw-gray-900) !important; background-color: var(--tw-gray-100) !important; }
.dropdown-item-text:focus { color: var(--tw-gray-900) !important; background-color: var(--tw-gray-100) !important; }
.dropdown-item-text:active { color: var(--tw-white) !important; text-decoration: none !important; background-color: var(--tw-primary) !important; }
.dropdown-item-text.disabled { color: var(--tw-gray-500) !important; pointer-events: none !important; background-color: transparent !important; }
</style>
