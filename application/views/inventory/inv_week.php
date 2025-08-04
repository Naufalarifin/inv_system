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

.input-form-label { font: bold 16px/1 sans-serif; margin-bottom: 4px; display: block; }
.input-result-message { margin-top: 10px; padding: 8px; border-radius: 4px; font-size: 13px; display: none; white-space: pre-wrap; }
.input-result-message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.input-result-message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.loading-spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite; vertical-align: middle; margin-left: 8px; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

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
        <!-- Input Button - Matching ECCT style -->
        <button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal('modal_input_period')" id="input_btn" type="button">Input</button>
      </div>
      <div id="toolbar_right" class="flex items-center gap-2">
        <button class="btn btn-sm btn-icon-lg btn-light" onclick="exportData();" style="margin-left:4px;">
          <i class="ki-filled ki-exit-down !text-base"></i>Export
        </button>
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

<!-- Modal Input Period - Matching ECCT modal style -->
<div id="modal_input_period" class="modal-container">
  <div class="modal-header">
    <h3 class="modal-title">Generate Periode Mingguan</h3>
    <button class="btn-close" onclick="closeModal('modal_input_period')">&times;</button>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <span class="form-hint">Tahun</span>
      <select class="select" id="year">
        <option value="">Pilih Tahun</option>
        <?php for($i = date('Y')-2; $i <= date('Y')+2; $i++): ?>
          <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="form-group">
      <span class="form-hint">Bulan</span>
      <select class="select" id="month">
        <option value="">Pilih Bulan</option>
        <option value="1" <?= date('n') == 1 ? 'selected' : '' ?>>Januari</option>
        <option value="2" <?= date('n') == 2 ? 'selected' : '' ?>>Februari</option>
        <option value="3" <?= date('n') == 3 ? 'selected' : '' ?>>Maret</option>
        <option value="4" <?= date('n') == 4 ? 'selected' : '' ?>>April</option>
        <option value="5" <?= date('n') == 5 ? 'selected' : '' ?>>Mei</option>
        <option value="6" <?= date('n') == 6 ? 'selected' : '' ?>>Juni</option>
        <option value="7" <?= date('n') == 7 ? 'selected' : '' ?>>Juli</option>
        <option value="8" <?= date('n') == 8 ? 'selected' : '' ?>>Agustus</option>
        <option value="9" <?= date('n') == 9 ? 'selected' : '' ?>>September</option>
        <option value="10" <?= date('n') == 10 ? 'selected' : '' ?>>Oktober</option>
        <option value="11" <?= date('n') == 11 ? 'selected' : '' ?>>November</option>
        <option value="12" <?= date('n') == 12 ? 'selected' : '' ?>>Desember</option>
      </select>
    </div>
    <div id="modal_result_message" class="input-result-message"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('modal_input_period')">Batal</button>
    <button class="btn btn-primary" onclick="generatePeriods()">
      Generate Periode <span id="generate_loading_spinner" class="loading-spinner" style="display:none;"></span>
    </button>
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

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<script>
let currentYear = '';
let currentMonth = '';

function openModal(modalId) {
  const overlay = document.getElementById("modal_overlay")
  const modal = document.getElementById(modalId)

  if (overlay && modal) {
    overlay.style.display = "block"
    modal.style.display = "block"
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
  if (modalId === 'modal_input_period') {
    const modalResultDiv = document.getElementById('modal_result_message');
    if (modalResultDiv) {
      modalResultDiv.style.display = 'none';
      modalResultDiv.textContent = '';
    }
  }
}

function showInvWeekData() {
    // Try to load data for current year/month by default
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    
    // Load data if available
    loadInvWeekData(currentYear, currentMonth);
}

// Tambahkan fungsi untuk debugging
function debugLoadData() {
    console.log('Current year:', currentYear);
    console.log('Current month:', currentMonth);
    
    if (currentYear && currentMonth) {
        const link = '<?= base_url('inventory/data/data_inv_week_show') ?>/' + currentYear + '?month=' + currentMonth;
        console.log('Debug: Loading from link:', link);
        loadData(link);
    } else {
        console.log('Debug: No year/month set, showing empty state');
        document.getElementById("show_data").innerHTML = '<div class="no-data"><p>Silakan klik tombol Input untuk generate periode mingguan.</p></div>';
    }
}

function generatePeriods() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const loadingSpinner = document.getElementById('generate_loading_spinner');
    const modalResultDiv = document.getElementById('modal_result_message');
    
    console.log('generatePeriods called with year:', year, 'month:', month);
    
    if (!year || !month) {
        showModalMessage('Pilih tahun dan bulan terlebih dahulu', 'error');
        return;
    }
    
    // Show loading
    loadingSpinner.style.display = 'inline-block';
    showModalMessage('Generating periods dengan logika 27-26, waktu 08:00-17:00, dan minggu kerja Senin-Jumat...', 'success');
    
    const requestData = {
        year: year,
        month: month
    };
    
    console.log('Sending request data:', requestData);
    
    fetch('<?= base_url('inventory/generate_inv_week_periods') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Generate response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Generate response data:', data);
        loadingSpinner.style.display = 'none';
        
        if (data.success) {
            showModalMessage('Periode berhasil di-generate dengan waktu 08:00-17:00 dan minggu kerja Senin-Jumat', 'success');
            currentYear = year;
            currentMonth = month;
            loadInvWeekData(year, month);
            
            // Auto close modal after 2 seconds
            setTimeout(() => {
                closeModal('modal_input_period');
            }, 2000);
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

function loadInvWeekData(year, month) {
    const link = '<?= base_url('inventory/data/data_inv_week_show') ?>/' + year + '?month=' + month;
    console.log('Loading data from:', link);
    loadData(link);
}

// Function to load data (matching inv_ecct style)
function loadData(link) {
    console.log('loadData called with link:', link);
    if (typeof window.$ !== "undefined") {
        console.log('Using jQuery load');
        window.$("#show_data").load(link);
    } else {
        console.log('Using fetch');
        fetch(link)
            .then((response) => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then((data) => {
                console.log('Data loaded successfully, length:', data.length);
                document.getElementById("show_data").innerHTML = data;
            })
            .catch((error) => {
                console.error('Error loading data:', error);
                document.getElementById("show_data").innerHTML =
                    '<div style="padding: 20px; text-align: center; color: red;">Error loading data: ' + error.message + '</div>';
            });
    }
}

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
    
    if (!date_start || !date_finish) {
        showMessage('Tanggal mulai dan selesai harus diisi', 'error');
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
            id_week: id_week,
            date_start: date_start,
            date_finish: date_finish
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Periode berhasil diupdate dengan waktu 08:00-17:00', 'success');
            closeModal('modal_edit');
            loadInvWeekData(currentYear, currentMonth);
        } else {
            showMessage(data.message || 'Gagal update periode', 'error');
        }
    })
    .catch(error => {
        showMessage('Error: ' + error.message, 'error');
    });
}

function exportData() {
    const year = currentYear || new Date().getFullYear();
    const month = currentMonth || (new Date().getMonth() + 1);
    
    if (!currentYear || !currentMonth) {
        showMessage('Generate periode terlebih dahulu sebelum export', 'error');
        return;
    }
    
    window.open('<?= base_url('inventory/export_inv_week') ?>?year=' + year + '&month=' + month, '_blank');
}

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

// Close modal when clicking overlay (matching inv_ecct behavior)
document.getElementById('modal_overlay').addEventListener('click', function(event) {
    if (event.target === document.getElementById('modal_overlay')) {
        // Close any open modal
        const modals = ['modal_input_period', 'modal_edit'];
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
        const modals = ['modal_input_period', 'modal_edit'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'block') {
                closeModal(modalId);
            }
        });
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show empty state initially
    document.getElementById("show_data").innerHTML = '<div class="no-data"><p>Silakan klik tombol <strong>Input</strong> untuk generate periode mingguan.</p><p>Pilih tahun dan bulan, lalu klik Generate Periode.</p></div>';
});
</script>
