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
          <!-- Hidden native selects to preserve existing JS logic -->
          <select class="select select-bulan" id="year_filter" style="display:none;">
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
          </select>
          <select class="select select-bulan" id="month_filter" style="display:none;">
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

          <div class="cs-select" id="cs_year">
            <button type="button" class="cs-button" id="cs_year_btn">2025
              <svg class="cs-caret" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="cs-menu" id="cs_year_menu">
              <div class="cs-option" data-value="2020">2020</div>
              <div class="cs-option" data-value="2021">2021</div>
              <div class="cs-option" data-value="2022">2022</div>
              <div class="cs-option" data-value="2023">2023</div>
              <div class="cs-option" data-value="2024">2024</div>
              <div class="cs-option active" data-value="2025">2025</div>
            </div>
          </div>
          <div class="cs-select" id="cs_month">
             <button type="button" class="cs-button" id="cs_month_btn">Agustus
              <svg class="cs-caret" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="cs-menu" id="cs_month_menu">
              <div class="cs-option" data-value="1">Januari</div>
              <div class="cs-option" data-value="2">Februari</div>
              <div class="cs-option" data-value="3">Maret</div>
              <div class="cs-option" data-value="4">April</div>
              <div class="cs-option" data-value="5">Mei</div>
              <div class="cs-option" data-value="6">Juni</div>
              <div class="cs-option" data-value="7">Juli</div>
              <div class="cs-option active" data-value="8">Agustus</div>
              <div class="cs-option" data-value="9">September</div>
              <div class="cs-option" data-value="10">Oktober</div>
              <div class="cs-option" data-value="11">November</div>
              <div class="cs-option" data-value="12">Desember</div>
            </div>
          </div>
           <a class="btn btn-sm btn-light export-btn" onclick="exportInvWeekData();" style="border: 1px solid #ddd !important; background-color: #ffffff !important; color: #333 !important;">
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
      <input type="date" class="input" id="edit_date_start">
    </div>
    <div class="form-group">
      <span class="form-hint">Tanggal Selesai</span>
      <input type="date" class="input" id="edit_date_finish">
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
      <p><strong>Logika Periode:</strong> 1 bulan dimulai dari tanggal 26 bulan sebelumnya sampai tanggal 25 bulan ini</p>
      <p><strong>Minggu Kerja:</strong> Sabtu–Jumat (6 hari kerja per minggu)</p>
      <p><strong>Contoh:</strong> Periode Januari 2024 = 26 Desember 2023 s/d 25 Januari 2024</p>
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
.select, .input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; transition: all 0.3s ease; min-height: 40px; line-height: 1.4; }
.select:focus, .input:focus { outline: none; border-color: var(--primary); }
.select option { padding: 8px 12px !important; font-size: 14px !important; font-weight: 500 !important; background-color: #ffffff !important; color: #333 !important; border: none !important; outline: none !important; }
.input-group { display: flex; align-items: center; gap: 5px; }
.input-group .select { min-width: 150px; border: 1px solid #ddd !important; border-radius: 4px !important; }
.input-group .select, .select-bulan { border: 1px solid #ddd !important; border-radius: 4px !important; box-sizing: border-box !important; }
#year_filter, #month_filter { border: 1px solid #ddd !important; border-radius: 4px !important; box-sizing: border-box !important; }
.input-group #year_filter, .input-group #month_filter { border: 1px solid #ddd !important; border-radius: 4px !important; box-sizing: border-box !important; border-right: 1px solid #ddd !important; }
.input-group .btn { white-space: nowrap; border: none !important; }
.btn-sm { height: 2rem !important; padding-inline-start: 0.75rem !important; padding-inline-end: 0.75rem !important; font-weight: 500 !important; font-size: 0.75rem !important; gap: 0.275rem !important; }
.btn-secondary { background-color: #6c757d !important; border: 1px solid #6c757d !important; color: white !important; box-shadow: none !important; }
.btn-secondary:hover { background-color: #5a6268 !important; border-color: #5a6268 !important; color: white !important; }
.btn-primary { background-color: #0074d9 !important; border: 1px solid #0074d9 !important; color: white !important; box-shadow: none !important; }
.btn-primary:hover { background-color: #0056b3 !important; border-color: #0056b3 !important; color: white !important; }
.btn-light { background-color: #ffffff !important; border: 1px solid #ddd !important; color: #333 !important; box-shadow: none !important; border-radius: 4px !important; transition: all 0.2s ease !important; }
.btn-light:hover { background-color: #f8f9fa !important; border-color: #0074d9 !important; box-shadow: none !important; }
.btn-light:focus { outline: none !important; box-shadow: 0 0 0 2px rgba(0,116,217,0.2) !important; }
.btn-light:active { background-color: #e9ecef !important; border-color: #0074d9 !important; }
/* Custom buttons for modal actions */
.btn-lihat-data { background-color: #ffffff !important; color: var(--primary) !important; border: 1px solid var(--primary) !important; box-shadow: none !important; border-radius: 4px !important; }
.btn-lihat-data:hover { background-color: #f8f9fa !important; }
.btn-regenerate { background-color: var(--primary) !important; color: #ffffff !important; border: 1px solid var(--primary) !important; box-shadow: none !important; border-radius: 4px !important; }
.btn-regenerate:hover { background-color: #0056b3 !important; border-color: #0056b3 !important; }
/* Confirmation area styles */
.confirmation-section { background: #fff !important; border-top: 1px solid var(--border) !important; padding: 12px 20px !important; }
.confirmation-content { display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 12px !important; }
.confirmation-text { font-size: 16px !important; color: #333 !important; font-weight:  !important; }
.confirmation-buttons { margin-left: auto !important; display: flex !important; align-items: center !important; gap: 8px !important; }
.confirmation-buttons .btn-confirm { background-color: var(--primary) !important; color: #ffffff !important; border: 1px solid var(--primary) !important; padding: 0 12px !important; height: 2rem !important; min-width: 100px !important; border-radius: 4px !important; text-align: center !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; }
.confirmation-buttons .btn-confirm:hover { background-color: #0056b3 !important; border-color: #0056b3 !important; }
.confirmation-buttons .btn-cancel { background-color: #ffffff !important; color: var(--primary) !important; border: 1px solid var(--primary) !important; padding: 0 12px !important; height: 2rem !important; min-width: 100px !important; border-radius: 4px !important; text-align: center !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; }
.confirmation-buttons .btn-cancel:hover { background-color: #f8f9fa !important; }
.export-btn { border: 1px solid #ddd !important; background-color: #ffffff !important; color: #333 !important; }
.export-btn:hover { border-color: #0074d9 !important; background-color: #f8f9fa !important; }
.export-btn:focus { border-color: #0074d9 !important; box-shadow: 0 0 0 2px rgba(0,116,217,0.2) !important; }
.input-result-message { display:none; padding: 10px 12px; border-radius: 6px; margin: 12px 16px; font-weight: 500; }
.input-result-message.success { display:block; background: #d4edda; color:#155724; border:1px solid #c3e6cb; }
.input-result-message.error { display:block; background: #f8d7da; color:#721c24; border:1px solid #f5c6cb; }
.input-result-message.warning { display:block; background: #fff3cd; color:#856404; border:1px solid #ffeaa7; }
.input-result-message.info { display:block; background: #d1ecf1; color:#0c5460; border:1px solid #bee5eb; }
.container-fixed { max-width: 1280px !important; margin: 0 auto !important; padding: 0 1rem !important; }
@media (min-width: 1280px) { .container-fixed { padding: 0 2rem !important; } }
.card { background-color: #fff !important; border: 1px solid #e5e5e5 !important; border-radius: 0.5rem !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important; }
.card-header { background-color: #fff !important; border-bottom: 1px solid #e5e5e5 !important; border-top-left-radius: 0.5rem !important; border-top-right-radius: 0.5rem !important; }
.select-bulan { cursor: pointer !important; }
.select-bulan:hover, #year_filter:hover, #month_filter:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.12) !important; }

/* Custom dropdown menu (options list) */
.cs-select { position: relative; display: inline-block; min-width: 150px; }
.cs-button { width: 100%; min-height: 32px; padding: 6px 32px 6px 10px; border: 1px solid #ddd; background: #fff; border-radius: 4px; text-align: left; font-size: 14px; color: #333; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: box-shadow .2s ease, border-color .2s ease; }
.cs-button:hover { border-color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.cs-button:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px rgba(0,116,217,0.2); }
.cs-caret { margin-left: auto; width: 16px; height: 16px; color: #666; }
.cs-menu { position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: #fff; border: 1px solid #e5e5e5; border-radius: 6px; box-shadow: 0 12px 28px rgba(0,0,0,0.15); z-index: 10000; padding: 6px 0; max-height: 260px; overflow: auto; display: none; }
.cs-select.open .cs-menu { display: block; }
.cs-option { padding: 8px 12px; font-size: 14px; color: #333; cursor: pointer; display: flex; align-items: center; }
.cs-option:hover { background: rgba(0,116,217,0.06); }
.cs-option.active { background: #e6f2ff; color: #0056b3; font-weight: 600; }

/* Keep spacing in toolbar */
.input-group .cs-select { min-width: 150px; }
</style>
