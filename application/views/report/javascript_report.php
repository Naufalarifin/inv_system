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
        $('.needs-input').each(function() { 
            $(this).data('orig', $(this).val()); 
        });
    } else {
        $btn.text('Edit').removeClass('btn-secondary').addClass('btn-primary');
        $save.hide();
        if (hasChanges()) {
            $('.needs-input').each(function() { 
                $(this).val($(this).data('orig') || 0); 
            });
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
    
    // Calculate totals for each size and QC combination
    sizes.forEach(function(size) {
        var total_ln = 0;
        var total_dn = 0;
        
        // Calculate LN totals
        $('.needs-input[data-size="' + size + '"][data-qc="LN"]').each(function() {
            total_ln += parseInt($(this).val()) || 0;
        });
        
        // Calculate DN totals
        $('.needs-input[data-size="' + size + '"][data-qc="DN"]').each(function() {
            total_dn += parseInt($(this).val()) || 0;
        });
        
        $('#total_' + size + '_ln').text(total_ln);
        $('#total_' + size + '_dn').text(total_dn);
        
        // Calculate percentages
        $('#percent_' + size + '_ln').text(grand > 0 ? Math.round(total_ln / grand * 1000) / 10 : 0);
        $('#percent_' + size + '_dn').text(grand > 0 ? Math.round(total_dn / grand * 1000) / 10 : 0);
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
    
    var $saveBtn = $('#saveAllDataBtn');
    var originalText = $saveBtn.text();
    $saveBtn.text('Saving...').prop('disabled', true);
    
    $.ajax({
        url: '<?= base_url('inventory/save_all_needs_data') ?>',
        type: 'POST',
        data: { data: data },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Data saved successfully!', 'success');
                $('.needs-input').each(function() {
                    $(this).data('orig', $(this).val());
                });
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
    const selectedYear = document.getElementById('year_filter').value;
    
    if (!selectedMonth) {
        showMessage('Silakan pilih bulan terlebih dahulu', 'error');
        return;
    }
    
    if (!selectedYear) {
        showMessage('Silakan pilih tahun terlebih dahulu', 'error');
        return;
    }
    
    // Set current month and year
    currentMonth = parseInt(selectedMonth);
    currentYear = parseInt(selectedYear);
    
    // Load data for selected month and year
    loadInvWeekData(currentYear, currentMonth);
    
    // Show success message
    showMessage(`Menampilkan data untuk bulan ${getMonthName(currentMonth)} ${currentYear}`, 'success');
}

// =================== YEAR DROPDOWN POPULATION FUNCTION ===================
function populateYearDropdown() {
    const yearFilter = document.getElementById('year_filter');
    const currentYearValue = new Date().getFullYear();
    const startYear = 2020; // Mulai dari tahun 2020
    
    // Clear existing options except the first one
    yearFilter.innerHTML = '<option value="">Pilih Tahun</option>';
    
    // Add year options from startYear to currentYear + 1
    for (let year = startYear; year <= currentYearValue + 1; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        
        // Set current year as selected by default
        if (year === currentYearValue) {
            option.selected = true;
        }
        
        yearFilter.appendChild(option);
    }
    
    // Set global variables
    currentYear = currentYearValue;
    currentMonth = new Date().getMonth() + 1; // Current month (1-12)
}

// =================== INITIALIZATION FUNCTION ===================
function initializeFilters() {
    populateYearDropdown();
    
    // Set current month as selected by default
    const monthFilter = document.getElementById('month_filter');
    if (monthFilter) {
        monthFilter.value = currentMonth;
    }
    
    // Load data for current month and year
    if (currentYear && currentMonth) {
        loadInvWeekData(currentYear, currentMonth);
    }
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
        document.getElementById("show_data").innerHTML = '<div class="no-data" style="text-align: center; padding: 40px; font-style: italic; color: #666; font-size: 18px;">No Data, Generate Please</div>';
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
                        '<div class="no-data" style="text-align: center; padding: 40px; font-style: italic; color: #666; font-size: 18px;">No Data, Generate Please</div>';
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
    
    showModalMessage('Generating periods...', 'success');
    
    const requestData = {
        year: parseInt(year),
        month: parseInt(month)
    };
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inv_week_periods', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        error: function(xhr, status, error) {
            showToast('Error saving data: ' + error, 'error');
        },
        complete: function() {
            $saveBtn.text(originalText).prop('disabled', false);
        }
    });
}

$(document).ready(function() {
    $('.needs-input').prop('disabled', true);
    setTimeout(calculateTotals, 100);
    
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
                    Yakin ingin regenerate? Data lama akan dihapus.
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
    
    showModalMessage('Regenerating periods...', 'success');
    
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
=======
    $('.needs-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
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
    
    // Check if message contains "Generating" or "Regenerating" to show loading spinner
    if (message.includes('Generating') || message.includes('Regenerating')) {
        const isRegenerating = message.includes('Regenerating');
        const actionText = isRegenerating ? 'Regenerating' : 'Generating';
        
        element.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="loading-spinner" style="width: 20px; height: 20px; border: 2px solid #e9ecef; border-top: 2px solid #28a745; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <span style="color: #28a745; font-weight: 500;">${actionText} periods...</span>
            </div>
        `;
    } else {
        element.textContent = message;
    }
    
    element.className = 'input-result-message ' + type;
    element.style.display = 'block';
}

// Event listeners for inv_week
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters and populate year dropdown
    initializeFilters();
    
    console.log('DOMContentLoaded - Initialized year:', currentYear, 'month:', currentMonth);
    
    // Add event listener for month dropdown Enter key
    const monthFilter = document.getElementById('month_filter');
    if (monthFilter) {
        monthFilter.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchByMonth();
            }
        });
    }
    
    // Add event listener for year dropdown Enter key
    const yearFilter = document.getElementById('year_filter');
    if (yearFilter) {
        yearFilter.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchByMonth();
            }
        });
        
        // Add change event listener for year filter
        yearFilter.addEventListener('change', function() {
            if (monthFilter && monthFilter.value) {
                searchByMonth();
            }
        });
    }
    
    // Add change event listener for month filter
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            if (yearFilter && yearFilter.value) {
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
=======
>>>>>>> 83781fc6d6228129d717108e9ca0f92a156ec604
});
</script>