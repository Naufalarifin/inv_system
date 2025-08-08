<script type="text/javascript">
var editMode = false;

function showToast(msg, type = 'success') {
    var t = document.getElementById('toast') || document.createElement('div');
    t.id = 'toast';
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 20px;border-radius:6px;color:white;z-index:9999;transition:all 0.3s;';
    t.style.backgroundColor = type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#10b981';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

function toggleEditMode() {
    if (editMode && hasChanges() && !confirm('Discard changes?')) return;
    
    editMode = !editMode;
    $('.needs-input').prop('disabled', !editMode);
    
    var $btn = $('#editModeBtn');
    var $save = $('#saveAllDataBtn');
    
    if (editMode) {
        $btn.text('Cancel').removeClass('btn-primary').addClass('btn-secondary');
        $save.show();
        $('.needs-input').each(function() { $(this).data('orig', $(this).val()); });
    } else {
        $btn.text('Edit').removeClass('btn-secondary').addClass('btn-primary');
        $save.hide();
        if (hasChanges()) {
            $('.needs-input').each(function() { $(this).val($(this).data('orig') || 0); });
            calculateTotals();
        }
    }
}

function hasChanges() {
    var changed = false;
    $('.needs-input').each(function() {
        if (($(this).data('orig') || 0) != ($(this).val() || 0)) {
            changed = true;
            return false;
        }
    });
    return changed;
}

function calculateTotals() {
    var sizes = ['xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus'];
    var grand = 0, rowTotals = {};
    
    $('.needs-input').each(function() {
        var key = $(this).data('id-dvc') + '_' + $(this).data('color');
        rowTotals[key] = (rowTotals[key] || 0) + (parseInt($(this).val()) || 0);
    });
    
    for (var key in rowTotals) {
        $('#subtotal_' + key).text(rowTotals[key]);
        grand += rowTotals[key];
    }
    
    $('#grand_total').text(grand);
    
    sizes.forEach(function(size) {
        var total = 0;
        $('.needs-input[data-size="' + size + '"]').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#total_' + size).text(total);
        $('#percent_' + size).text(grand > 0 ? Math.round(total / grand * 1000) / 10 : 0);
    });
    
    for (var key in rowTotals) {
        $('#percentage_' + key).text(grand > 0 ? Math.round(rowTotals[key] / grand * 1000) / 10 : 0);
    }
}

function saveAllData() {
    var data = [], changes = 0;
    
    $('.needs-input').each(function() {
        var qty = parseInt($(this).val()) || 0;
        var orig = parseInt($(this).data('orig')) || 0;
        
        if (qty > 0 || orig > 0) {
            if (qty !== orig) changes++;
            data.push({
                id_dvc: $(this).data('id-dvc'),
                dvc_size: $(this).data('size'),
                dvc_col: $(this).data('color'),
                dvc_qc: $(this).data('qc'),
                needs_qty: qty,
                original_qty: orig
            });
        }
    });
    
    if (!changes) {
        showToast(data.length ? 'No changes detected' : 'No data to save', 'warning');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('inventory/save_needs_data') ?>',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                showToast('Data saved successfully!', 'success');
                toggleEditMode();
            } else {
                showToast(response.message || 'Failed to save data', 'error');
            }
        },
        error: function() {
            showToast('Error saving data', 'error');
        }
    });
}

// =================== MONTH SEARCH FUNCTION ===================
function searchByMonth() {
    const selectedMonth = document.getElementById('month_filter').value;
    const currentYear = new Date().getFullYear();
    
    if (!selectedMonth) {
        showMessage('Silakan pilih bulan terlebih dahulu', 'error');
        return;
    }
    
    // Set current month and year
    currentMonth = parseInt(selectedMonth);
    
    // Load data for selected month
    loadInvWeekData(currentYear, currentMonth);
    
    // Show success message
    showMessage(`Menampilkan data untuk bulan ${getMonthName(currentMonth)} ${currentYear}`, 'success');
}

// =================== EXISTING FUNCTIONS ===================

// Global variables for inv_week
let currentYear = '';
let currentMonth = '';

// Modal functions for inv_week
function openModal(modalId) {
    const overlay = document.getElementById("modal_overlay");
    const modal = document.getElementById(modalId);

    if (overlay && modal) {
        overlay.style.display = "block";
        modal.style.display = "block";
        
        // If opening modal_input_all, render the inv_week input mode
        if (modalId === 'modal_input_all') {
            renderInvWeekInputMode();
        }
    }
}

function closeModal(modalId) {
    const overlay = document.getElementById("modal_overlay");
    const modal = document.getElementById(modalId);

    if (overlay && modal) {
        overlay.style.display = "none";
        modal.style.display = "none";
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
    // Use global currentYear and currentMonth variables
    if (currentYear && currentMonth) {
        console.log('Loading data for year:', currentYear, 'month:', currentMonth);
        loadInvWeekData(currentYear, currentMonth);
    } else {
        console.log('No year/month set, showing empty state');
        document.getElementById("show_data").innerHTML = '<div class="no-data"><p>Silakan klik tombol <strong>Input</strong> untuk generate periode mingguan.</p><p>Pilih tahun dan bulan, lalu klik Generate Periode.</p></div>';
    }
}

function loadInvWeekData(year, month) {
    console.log('loadInvWeekData called with year:', year, 'month:', month);
    const link = window.location.origin + '/cdummy/inventory/data/data_inv_week_show/' + year + '?month=' + month;
    console.log('Loading from URL:', link);
    loadInvWeekDataFromServer(link);
}

function loadInvWeekDataFromServer(link) {
    console.log('loadInvWeekDataFromServer called with link:', link);
    
    // Show loading indicator
    document.getElementById("show_data").innerHTML = '<div style="padding: 20px; text-align: center;"><div class="loading-spinner"></div> Loading data...</div>';
    
    if (typeof window.$ !== "undefined") {
        console.log('Using jQuery load method');
        window.$("#show_data").load(link, function(response, status, xhr) {
            console.log('jQuery load response status:', status);
            if (status === "error") {
                console.error('jQuery load error:', xhr.statusText);
                document.getElementById("show_data").innerHTML = 
                    '<div style="padding: 20px; text-align: center; color: red;">Error loading data: ' + xhr.statusText + '</div>';
            } else {
                console.log('jQuery load successful, response length:', response.length);
            }
        });
    } else {
        console.log('Using fetch method');
        fetch(link)
            .then((response) => {
                console.log('Fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.text();
            })
            .then((data) => {
                console.log('Fetch data received, length:', data.length);
                if (data.trim() === '') {
                    console.log('Empty data received');
                    document.getElementById("show_data").innerHTML = 
                        '<div class="no-data"><p>Tidak ada data periode untuk bulan dan tahun yang dipilih.</p><p>Silakan generate periode terlebih dahulu.</p></div>';
                } else {
                    console.log('Data loaded successfully');
                    document.getElementById("show_data").innerHTML = data;
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
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
    
    if (!year || !month) {
        showModalMessage('Pilih tahun dan bulan terlebih dahulu', 'error');
        return;
    }
    
    if (loadingSpinner) {
        loadingSpinner.style.display = 'inline-block';
    }
    
    showModalMessage('Generating periods dengan logika 27-26, waktu 08:00-17:00, dan minggu kerja Senin-Jumat...', 'success');
    
    const requestData = {
        year: parseInt(year),
        month: parseInt(month)
    };
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inv_week_periods', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
        
        if (data.success) {
            showModalMessage('Periode berhasil di-generate dengan waktu 08:00-17:00 dan minggu kerja Senin-Jumat', 'success');
            currentYear = year;
            currentMonth = month;
            
            // Close modal immediately after successful generation
            closeModal('modal_input_all');
            
            // Reload data after modal is closed
            setTimeout(() => {
                loadInvWeekData(year, month);
            }, 500);
        } else {
            // Check if the error is about existing periods
            if (data.message && data.message.includes('sudah ada')) {
                showModalMessage(data.message + '\n\nKlik "Lihat Data" untuk menampilkan periode yang sudah ada.', 'error');
                
                // Update modal footer to show "Lihat Data" button
                const modalFooter = document.querySelector('#modal_input_all .modal-footer');
                if (modalFooter) {
                    modalFooter.innerHTML = `
                        <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
                        <button class="btn btn-lihat-data" onclick="viewExistingData(${year}, ${month})">Lihat Data</button>
                        <button class="btn btn-regenerate" onclick="regeneratePeriods(${year}, ${month})">
                            Regenerate
                        </button>
                    `;
                }
            } else {
                showModalMessage(data.message || 'Gagal generate periode', 'error');
            }
        }
    })
    .catch(error => {
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
        showModalMessage('Error: ' + error.message, 'error');
    });
}

// View existing data function
function viewExistingData(year, month) {
    console.log('viewExistingData called with:', year, month);
    
    // Ensure year and month are numbers
    year = parseInt(year);
    month = parseInt(month);
    
    currentYear = year;
    currentMonth = month;
    
    // Close modal
    closeModal('modal_input_all');
    
    // Show message
    showMessage(`Menampilkan data periode untuk tahun ${year} bulan ${getMonthName(month)}`, 'success');
    
    // Load existing data immediately
    loadInvWeekData(year, month);
}

// Regenerate periods function
function regeneratePeriods(year, month) {
    showRegenerateConfirmation(year, month);
}

function showRegenerateConfirmation(year, month) {
    const existingConfirmation = document.getElementById('regenerate_confirmation');
    if (existingConfirmation) {
        return;
    }
    
    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
    if (!modalFooter) return;
    
    // Add confirmation section after modal footer
    const confirmationHtml = `
        <div id="regenerate_confirmation" class="confirmation-section" style="display: block;">
            <div class="confirmation-content">
                <div class="confirmation-text">
                    Apakah Anda yakin untuk generate ulang? Tindakan ini akan menghapus data yang sudah ada dan membuat ulang.
                </div>
                <div class="confirmation-buttons">
                    <button class="btn-confirm" onclick="confirmRegenerate(${year}, ${month})">YA</button>
                    <button class="btn-cancel" onclick="cancelRegenerate()">TIDAK</button>
                </div>
            </div>
        </div>
    `;
    
    modalFooter.insertAdjacentHTML('afterend', confirmationHtml);
}

function confirmRegenerate(year, month) {
    const confirmationSection = document.getElementById('regenerate_confirmation');
    if (confirmationSection) {
        confirmationSection.remove();
    }
    executeRegenerate(year, month);
}

function cancelRegenerate() {
    const confirmationSection = document.getElementById('regenerate_confirmation');
    if (confirmationSection) {
        confirmationSection.remove();
    }
}

function executeRegenerate(year, month) {
    const loadingSpinner = document.getElementById('generate_loading_spinner');
    
    if (loadingSpinner) {
        loadingSpinner.style.display = 'inline-block';
    }
    
    showModalMessage('Regenerating periods dengan logika 27-26, waktu 08:00-17:00, dan minggu kerja Senin-Jumat...', 'success');
    
    const requestData = {
        year: parseInt(year),
        month: parseInt(month),
        regenerate: true
    };
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inv_week_periods', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
        
        if (data.success) {
            showModalMessage('Periode berhasil di-regenerate dengan waktu 08:00-17:00 dan minggu kerja Senin-Jumat', 'success');
            currentYear = year;
            currentMonth = month;
            
            // Close modal immediately after successful regeneration
            closeModal('modal_input_all');
            
            // Reload data after modal is closed
            setTimeout(() => {
                loadInvWeekData(year, month);
            }, 500);
        } else {
            showModalMessage(data.message || 'Gagal regenerate periode', 'error');
        }
    })
    .catch(error => {
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
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
        showMessage('Error: ' + error.message, 'error');
    });
}

// UI rendering functions
function renderInvWeekInputMode() {
    const modalBody = document.querySelector('#modal_input_all .modal-body');
    if (!modalBody) return;
    
    // Render the inv_week period generator interface
    modalBody.innerHTML = `
        <div class="form-group">
            <span class="form-hint">Tahun</span>
            <select class="select" id="year" onchange="checkPeriodsExist()" style="min-height: 45px; height: 45px; line-height: 1.2; padding: 12px 12px; font-size: 14px;">
                <option value="">Pilih Tahun</option>
                ${generateYearOptions()}
            </select>
        </div>
        <div class="form-group">
            <span class="form-hint">Bulan</span>
            <select class="select" id="month" onchange="checkPeriodsExist()" style="min-height: 45px; height: 45px; line-height: 1.2; padding: 12px 12px; font-size: 14px;">
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
            <button class="btn btn-primary" onclick="generateInvWeekPeriods()" id="generate_btn">
                Generate Periode <span id="generate_loading_spinner" class="loading-spinner" style="display:none;"></span>
            </button>
        `;
    }
    
    // Reset modal state when opening
    resetModalState();
}

// Debounce function to prevent too many API calls
let checkPeriodsTimeout = null;

// Reset modal state function
function resetModalState() {
    const modalResultDiv = document.getElementById('modal_result_message');
    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
    
    // Remove any existing confirmation dialog
    const confirmationSection = document.getElementById('regenerate_confirmation');
    if (confirmationSection) {
        confirmationSection.remove();
    }
    
    // Reset modal footer to default state
    if (modalFooter) {
        modalFooter.innerHTML = `
            <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
            <button class="btn btn-primary" onclick="generateInvWeekPeriods()" id="generate_btn">
                Generate Periode <span id="generate_loading_spinner" class="loading-spinner" style="display:none;"></span>
            </button>
        `;
    }
    
    // Reset result message
    if (modalResultDiv) {
        modalResultDiv.style.display = 'none';
        modalResultDiv.innerHTML = '';
        modalResultDiv.className = 'input-result-message';
    }
}

// Check if periods exist for selected year/month
function checkPeriodsExist() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const modalResultDiv = document.getElementById('modal_result_message');
    
    // Clear any existing timeout
    if (checkPeriodsTimeout) {
        clearTimeout(checkPeriodsTimeout);
    }
    
    // Always reset to default state first
    resetModalState();
    
    // If either year or month is not selected, stay in default state
    if (!year || !month) {
        return;
    }
    
    // Debounce the API call to prevent too many requests
    checkPeriodsTimeout = setTimeout(() => {
        // Check if periods exist
        fetch(window.location.origin + '/cdummy/inventory/check_inv_week_periods?year=' + year + '&month=' + month)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    // Update modal footer for existing periods
                    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
                    if (modalFooter) {
                        modalFooter.innerHTML = `
                            <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
                            <button class="btn btn-lihat-data" onclick="console.log('Lihat Data clicked'); viewExistingData(${year}, ${month})">Lihat Data</button>
                            <button class="btn btn-regenerate" onclick="console.log('Regenerate clicked'); regeneratePeriods(${year}, ${month})">
                                Regenerate
                            </button>
                        `;
                    }
                    
                    if (modalResultDiv) {
                        modalResultDiv.innerHTML = `
                            <div style="margin-bottom: 10px;">
                                <strong>Periode untuk tahun ${year} bulan ${getMonthName(month)} sudah ada.</strong><br>
                                • Klik "Lihat Data" untuk menampilkan data yang sudah ada<br>
                                • Atau klik "Regenerate" untuk membuat ulang periode (akan menghapus data lama)
                            </div>
                        `;
                        modalResultDiv.className = 'input-result-message error';
                        modalResultDiv.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                // On error, ensure we're in default state
                resetModalState();
            });
    }, 300); // 300ms debounce delay
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

// Event listeners for inv_week
document.addEventListener('DOMContentLoaded', function() {
    // Initialize current year and month
    currentYear = new Date().getFullYear();
    currentMonth = new Date().getMonth() + 1;
    
    console.log('DOMContentLoaded - Initialized year:', currentYear, 'month:', currentMonth);
    
    // Try to load existing data for current year/month
    showInvWeekData();
    
    // Add event listener for month dropdown Enter key
    const monthFilter = document.getElementById('month_filter');
    if (monthFilter) {
        monthFilter.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchByMonth();
            }
        });
    }
});

// Close modal when clicking overlay
document.addEventListener('click', function(event) {
    if (event.target === document.getElementById('modal_overlay')) {
        // Close any open modal
        const modals = ['modal_input_all', 'modal_edit', 'modal_period_info'];
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
        const modals = ['modal_input_all', 'modal_edit', 'modal_period_info'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'block') {
                closeModal(modalId);
            }
        });
    }
});
</script>