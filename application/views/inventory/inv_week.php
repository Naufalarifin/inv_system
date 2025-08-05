<style>
/* Ultra Simplified CSS - Matching inv_ecct.php exactly */
:root { --primary: #0074d9; --border: #e5e5e5; --bg: #f8f9fa; --radius: 8px; }

.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
.modal-container { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%); background: white; border-radius: var(--radius); box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; min-width: 350px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header, .modal-footer { padding: 20px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; }
.modal-header { justify-content: space-between; border-bottom: 1px solid var(--border); border-radius: var(--radius) var(--radius) 0 0; }
.modal-footer { justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius); }
.modal-title { margin: 0; font: 600 24px/1 sans-serif; color: #333; }
.modal-body { padding: 20px; }
.btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; transition: color 0.2s; }
.btn-close:hover { color: #000; }

.input-tab-btn, .input-mode-btn { background: white; color: var(--primary); border: 1px solid var(--primary); font: 600 15px/1 sans-serif; cursor: pointer; transition: all 0.2s; }
.input-tab-btn { border-radius: 4px 4px 0 0; padding: 6px 24px; margin-right: 4px; }
.input-mode-btn { border-radius: 4px; padding: 4px 12px; margin-right: 4px; font-size: 13px; }
.input-tab-btn.active, .input-mode-btn.active { background: var(--primary); color: white; }

.input-form-label { font: bold 16px/1 sans-serif; margin-bottom: 4px; display: block; }
.input-result-message { margin-top: 10px; padding: 8px; border-radius: 4px; font-size: 13px; display: none; white-space: pre-wrap; }
.input-result-message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.input-result-message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.activity-date-section { padding-top: 20px; }
.activity-date-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; }
.activity-date-column { text-align: center; font-size: 12px; }
.activity-date-label { font-weight: bold; margin-bottom: 2px; background: rgb(247,245,245); font-size: 12px; }

.auto-submit-info { font: italic 12px/1 sans-serif; color: #666; margin-top: 5px; }
.massive-textarea { min-height: 120px; resize: vertical; font: 14px/1 monospace; }
.loading-spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite; vertical-align: middle; margin-left: 8px; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

.searchable-dropdown { position: relative; }
.searchable-dropdown .select-display { cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
.searchable-dropdown .dropdown-arrow { font-size: 12px; color: #666; }
.searchable-dropdown .dropdown-content { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px; max-height: 200px; overflow-y: auto; z-index: 1000; display: none; }
.searchable-dropdown .search-input { width: 100%; padding: 8px 12px; border: none; border-bottom: 1px solid #eee; outline: none; font-size: 14px; }
.searchable-dropdown .dropdown-option { padding: 8px 12px; cursor: pointer; font-size: 14px; transition: background 0.2s; }
.searchable-dropdown .dropdown-option:hover { background: #f5f5f5; }
.searchable-dropdown .dropdown-option.selected { background: #007bff; color: white; }
.searchable-dropdown .no-results { padding: 8px 12px; color: #999; font-style: italic; }
.searchable-dropdown.active .dropdown-content { display: block; }
.searchable-dropdown.active .select-display { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }

/* Additional styles for inv_week specific elements */
.edit-icon { cursor: pointer; color: #0074d9; font-size: 18px; }
.edit-icon:hover { color: #0056b3; }
.edit-icon.disabled { color: #ccc; cursor: not-allowed; }

.form-group { margin-bottom: 15px; }
.form-hint { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
.select, .input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
.btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; transition: all 0.2s; }
.btn-primary { background: var(--primary); color: white; }
.btn-primary:hover { background: #0056b3; }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #545b62; }

.table-container { margin-top: 20px; }
.table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
.table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
.table th { background: #f8f9fa; font-weight: bold; }
.table tr:hover { background: #f5f5f5; }

.no-data { text-align: center; padding: 40px; color: #666; font-style: italic; }

.period-info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
.period-info h4 { margin: 0 0 10px 0; color: #1976d2; }
.period-info p { margin: 5px 0; color: #424242; }
</style>

<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div id="toolbar_left" class="flex items-center gap-2">
        <!-- Input Button -->
        <button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal('modal_input_all')" id="input_btn" type="button">Input</button>
      </div>
      <div id="toolbar_right" class="flex items-center gap-2">
        <!-- Export button -->
        <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportInvWeekData();" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="card-body" style="padding: 20px;">
      <!-- Period Information -->
      <div class="period-info">
        <h4>Informasi Periode Kantor</h4>
        <p><strong>Logika Periode:</strong> 1 bulan di kantor dimulai dari tanggal 27 bulan sebelumnya sampai tanggal 26 bulan ini</p>
        <p><strong>Waktu Kerja:</strong> Mulai jam 08:00 pagi, selesai jam 17:00 sore</p>
        <p><strong>Minggu Kerja:</strong> Senin-Jumat (5 hari kerja per minggu)</p>
        <p><strong>Contoh:</strong> Periode Januari 2024 = 27 Desember 2023 (08:00) s/d 26 Januari 2024 (17:00)</p>
      </div>
      
      <div id="result_message" class="input-result-message"></div>
    </div>
    
    <!-- Table -->
    <div id="show_data"></div>
  </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<!-- Modal Input All - Unified modal system -->
<div id="modal_input_all" class="modal-container" style="min-width:500px; max-width: 650px;">
  <div class="modal-header">
    <h3 class="modal-title">Generate Periode Mingguan</h3>
    <button class="btn-close" onclick="closeModal('modal_input_all')" style="font-size: 24px;">&times;</button>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
  </div>
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

<script type="text/javascript">
// Set inventory type untuk universal script
window.INVENTORY_TYPE = 'INV_WEEK';

// CONFIG object untuk universal script
window.CONFIG = {
  urlMenu: '<?php echo $config['url_menu']; ?>'
};

// Global variables for inv_week
let currentYear = '';
let currentMonth = '';

// Modal functions
function openModal(modalId) {
  const overlay = document.getElementById("modal_overlay")
  const modal = document.getElementById(modalId)

  if (overlay && modal) {
    overlay.style.display = "block"
    modal.style.display = "block"
    
    // If opening modal_input_all, render the inv_week input mode
    if (modalId === 'modal_input_all') {
      renderInvWeekInputMode();
    }
  }
}

function closeModal(modalId) {
  const overlay = document.getElementById("modal_overlay")
  const modal = document.getElementById(modalId)

  if (overlay && modal) {
    overlay.style.display = "none"
    modal.style.display = "none"
  }

  // Clear modal result message when closing
  if (modalId === 'modal_input_all') {
    const modalResultDiv = document.getElementById('modal_result_message');
    if (modalResultDiv) {
      modalResultDiv.style.display = 'none';
      modalResultDiv.textContent = '';
    }
  }
}

// Data loading functions
function showInvWeekData() {
    if (currentYear && currentMonth) {
        console.log('Loading data for year:', currentYear, 'month:', currentMonth);
        loadInvWeekData(currentYear, currentMonth);
    } else {
        console.log('No year/month set, showing empty state');
        document.getElementById("show_data").innerHTML = '<div class="no-data"><p>Silakan klik tombol <strong>Input</strong> untuk generate periode mingguan.</p><p>Pilih tahun dan bulan, lalu klik Generate Periode.</p></div>';
    }
}

function loadInvWeekData(year, month) {
    const link = window.location.origin + '/cdummy/inventory/data/data_inv_week_show/' + year + '?month=' + month;
    console.log('Loading data from:', link);
    loadData(link);
}

function loadData(link) {
    console.log('loadData called with link:', link);
    
    // Show loading indicator
    document.getElementById("show_data").innerHTML = '<div style="padding: 20px; text-align: center;"><div class="loading-spinner"></div> Loading data...</div>';
    
    if (typeof window.$ !== "undefined") {
        console.log('Using jQuery load');
        window.$("#show_data").load(link, function(response, status, xhr) {
            if (status === "error") {
                console.error('jQuery load error:', xhr.status, xhr.statusText);
                document.getElementById("show_data").innerHTML = 
                    '<div style="padding: 20px; text-align: center; color: red;">Error loading data: ' + xhr.statusText + '</div>';
            }
        });
    } else {
        console.log('Using fetch');
        fetch(link)
            .then((response) => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.text();
            })
            .then((data) => {
                console.log('Data loaded successfully, length:', data.length);
                if (data.trim() === '') {
                    document.getElementById("show_data").innerHTML = 
                        '<div class="no-data"><p>Tidak ada data periode untuk bulan dan tahun yang dipilih.</p><p>Silakan generate periode terlebih dahulu.</p></div>';
                } else {
                    document.getElementById("show_data").innerHTML = data;
                }
            })
            .catch((error) => {
                console.error('Error loading data:', error);
                document.getElementById("show_data").innerHTML =
                    '<div style="padding: 20px; text-align: center; color: red;">Error loading data: ' + error.message + '</div>';
            });
    }
}

// Period generation functions
function generateInvWeekPeriods() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const loadingSpinner = document.getElementById('generate_loading_spinner');
    const modalResultDiv = document.getElementById('modal_result_message');
    
    console.log('generateInvWeekPeriods called with year:', year, 'month:', month);
    
    if (!year || !month) {
        showModalMessage('Pilih tahun dan bulan terlebih dahulu', 'error');
        return;
    }
    
    // Show loading
    loadingSpinner.style.display = 'inline-block';
    showModalMessage('Generating periods dengan logika 27-26, waktu 08:00-17:00, dan minggu kerja Senin-Jumat...', 'success');
    
    const requestData = {
        year: parseInt(year),
        month: parseInt(month)
    };
    
    console.log('Sending request data:', requestData);
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inv_week_periods', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Generate response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Generate response data:', data);
        loadingSpinner.style.display = 'none';
        
        if (data.success) {
            showModalMessage('Periode berhasil di-generate dengan waktu 08:00-17:00 dan minggu kerja Senin-Jumat', 'success');
            currentYear = year;
            currentMonth = month;
            
            // Reload data after successful generation
            setTimeout(() => {
                loadInvWeekData(year, month);
            }, 1000);
            
            // Auto close modal after 3 seconds
            setTimeout(() => {
                closeModal('modal_input_all');
            }, 3000);
        } else {
            showModalMessage(data.message || 'Gagal generate periode', 'error');
        }
    })
    .catch(error => {
        console.error('Generate error:', error);
        loadingSpinner.style.display = 'none';
        showModalMessage('Error: ' + error.message, 'error');
    });
}

// Export function
function exportInvWeekData() {
    const year = currentYear || new Date().getFullYear();
    const month = currentMonth || (new Date().getMonth() + 1);
    
    if (!currentYear || !currentMonth) {
        showMessage('Generate periode terlebih dahulu sebelum export', 'error');
        return;
    }
    
    window.open(window.location.origin + '/cdummy/inventory/export_inv_week?year=' + year + '&month=' + month, '_blank');
}

// Edit period functions
function editPeriod(id_week, date_start, date_finish) {
    document.getElementById('edit_id_week').value = id_week;
    document.getElementById('edit_date_start').value = formatDateTimeForInput(date_start);
    document.getElementById('edit_date_finish').value = formatDateTimeForInput(date_finish);
    
    openModal('modal_edit');
}

function updatePeriod() {
    const id_week = document.getElementById('edit_id_week').value;
    const date_start = document.getElementById('edit_date_start').value;
    const date_finish = document.getElementById('edit_date_finish').value;
    
    if (!id_week || !date_start || !date_finish) {
        showMessage('Semua field harus diisi', 'error');
        return;
    }
    
    if (!confirm('Apakah Anda yakin ingin mengupdate periode ini? Waktu akan otomatis diset ke 08:00-17:00')) {
        return;
    }
    
    fetch('<?= base_url('inventory/update_inv_week_period') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_week: parseInt(id_week),
            date_start: date_start,
            date_finish: date_finish
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage('Periode berhasil diupdate dengan waktu 08:00-17:00', 'success');
            closeModal('modal_edit');
            
            // Reload data after successful update
            setTimeout(() => {
                loadInvWeekData(currentYear, currentMonth);
            }, 1000);
        } else {
            showMessage(data.message || 'Gagal update periode', 'error');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        showMessage('Error: ' + error.message, 'error');
    });
}

// UI rendering functions
function renderInvWeekInputMode() {
    // Get the modal body
    const modalBody = document.querySelector('#modal_input_all .modal-body');
    if (!modalBody) return;
    
    // Render the inv_week period generator interface
    modalBody.innerHTML = `
        <div class="form-group">
            <span class="form-hint">Tahun</span>
            <select class="select" id="year">
                <option value="">Pilih Tahun</option>
                ${generateYearOptions()}
            </select>
        </div>
        <div class="form-group">
            <span class="form-hint">Bulan</span>
            <select class="select" id="month">
                <option value="">Pilih Bulan</option>
                ${generateMonthOptions()}
            </select>
        </div>
        <div id="modal_result_message" class="input-result-message"></div>
    `;
    
    // Update modal title and footer
    const modalTitle = document.querySelector('#modal_input_all .modal-title');
    if (modalTitle) {
        modalTitle.textContent = 'Generate Periode Mingguan';
    }
    
    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
    if (modalFooter) {
        modalFooter.innerHTML = `
            <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
            <button class="btn btn-primary" onclick="generateInvWeekPeriods()">
                Generate Periode <span id="generate_loading_spinner" class="loading-spinner" style="display:none;"></span>
            </button>
        `;
    }
}

// Helper functions
function generateYearOptions() {
    const currentYear = new Date().getFullYear();
    let options = '';
    for (let i = currentYear - 2; i <= currentYear + 2; i++) {
        const selected = i === currentYear ? 'selected' : '';
        options += `<option value="${i}" ${selected}>${i}</option>`;
    }
    return options;
}

function generateMonthOptions() {
    const currentMonth = new Date().getMonth() + 1;
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    let options = '';
    months.forEach((month, index) => {
        const monthNumber = index + 1;
        const selected = monthNumber === currentMonth ? 'selected' : '';
        options += `<option value="${monthNumber}" ${selected}>${month}</option>`;
    });
    return options;
}

function getMonthName(month) {
    const months = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month] || month;
}

function formatDateTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
}

function formatDateTimeForInput(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toISOString().slice(0, 16);
}

// Message functions
function showMessage(message, type) {
    const element = document.getElementById('result_message');
    element.textContent = message;
    element.className = 'input-result-message ' + type;
    element.style.display = 'block';
    
    setTimeout(() => {
        element.style.display = 'none';
    }, 5000);
}

function showModalMessage(message, type) {
    const element = document.getElementById('modal_result_message');
    element.textContent = message;
    element.className = 'input-result-message ' + type;
    element.style.display = 'block';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initialize current year and month
    currentYear = new Date().getFullYear();
    currentMonth = new Date().getMonth() + 1;
    
    // Try to load existing data for current year/month
    showInvWeekData();
});

// Close modal when clicking overlay
document.getElementById('modal_overlay').addEventListener('click', function(event) {
    if (event.target === document.getElementById('modal_overlay')) {
        // Close any open modal
        const modals = ['modal_input_all', 'modal_edit'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'block') {
                closeModal(modalId);
            }
        });
    }
});

// ESC key untuk menutup modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['modal_input_all', 'modal_edit'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'block') {
                closeModal(modalId);
            }
        });
    }
});
</script>
