<script type="text/javascript">
// Add CSS for massive textarea
const style = document.createElement('style');
style.textContent = `
    .massive-textarea { 
        min-height: 120px; 
        resize: vertical; 
        font: 14px/1 monospace; 
        font-family: 'Courier New', monospace;
        line-height: 1.4;
    }
`;
document.head.appendChild(style);

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

function showConfirmDialog(message, onConfirm) {
    // Remove existing dialog if any
    var existingDialog = document.getElementById('confirmDialog');
    if (existingDialog) {
        existingDialog.remove();
    }
    
    // Create dialog container
    var dialog = document.createElement('div');
    dialog.id = 'confirmDialog';
    dialog.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:white;border:1px solid #ddd;border-radius:8px;padding:20px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:10000;min-width:300px;max-width:400px;';
    
    // Create message
    var msgDiv = document.createElement('div');
    msgDiv.style.cssText = 'margin-bottom:15px;color:#333;font-size:14px;line-height:1.4;';
    msgDiv.textContent = message;
    
    // Create buttons container
    var btnContainer = document.createElement('div');
    btnContainer.style.cssText = 'display:flex;gap:10px;justify-content:flex-end;';
    
    // Create Cancel button
    var cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'padding:8px 16px;border:1px solid #ddd;background:white;border-radius:4px;cursor:pointer;font-size:14px;';
    cancelBtn.onmouseover = function() { this.style.backgroundColor = '#f5f5f5'; };
    cancelBtn.onmouseout = function() { this.style.backgroundColor = 'white'; };
    cancelBtn.onclick = function() { dialog.remove(); };
    
    // Create OK button
    var okBtn = document.createElement('button');
    okBtn.textContent = 'OK';
    okBtn.style.cssText = 'padding:8px 16px;border:none;background:#007bff;color:white;border-radius:4px;cursor:pointer;font-size:14px;';
    okBtn.onmouseover = function() { this.style.backgroundColor = '#0056b3'; };
    okBtn.onmouseout = function() { this.style.backgroundColor = '#007bff'; };
    okBtn.onclick = function() { 
        dialog.remove(); 
        if (onConfirm) onConfirm();
    };
    
    // Assemble dialog
    btnContainer.appendChild(cancelBtn);
    btnContainer.appendChild(okBtn);
    dialog.appendChild(msgDiv);
    dialog.appendChild(btnContainer);
    
    // Add to page
    document.body.appendChild(dialog);
    
    // Auto-remove after 10 seconds
    setTimeout(function() {
        if (dialog.parentNode) {
            dialog.remove();
        }
    }, 10000);
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
        var colorKey = String($(this).data('color')).replace(/\s+/g,'-');
        var key = $(this).data('id-dvc') + '_' + colorKey;
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

// =================== YEAR/MONTH FILTER TRIGGER (NO UI POPULATION) ===================
function initializeFilters() {
    // Set default dropdown ke tahun & bulan saat ini jika opsi tersedia, lalu trigger filter sekali
    const yearEl = document.getElementById('year_filter');
    const monthEl = document.getElementById('month_filter');
    if (!yearEl || !monthEl) return; // aman untuk halaman tanpa filter

    const now = new Date();
    const nowYear = String(now.getFullYear());
    const nowMonth = String(now.getMonth() + 1);

    const optionExists = (select, value) => !!(select && select.querySelector(`option[value="${value}"]`));

    // Paksa default ke current year/month bila tersedia di opsi
    if (optionExists(yearEl, nowYear)) yearEl.value = nowYear;
    if (optionExists(monthEl, nowMonth)) monthEl.value = nowMonth;

    // Gunakan fungsi pusat filter jika ada untuk hindari duplikasi logic
    if (typeof searchByMonth === 'function') {
        searchByMonth();
        return;
    }

    // Fallback jika searchByMonth tidak tersedia
    const selectedYear = yearEl.value;
    const selectedMonth = monthEl.value;
    if (!selectedYear || !selectedMonth) return;

    currentYear = parseInt(selectedYear, 10);
    currentMonth = parseInt(selectedMonth, 10);
    loadInvWeekData(currentYear, currentMonth);
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
        showNoDataMessage();
    }
}

function showNoDataMessage() {
    document.getElementById("show_data").innerHTML = '<div class="no-data" style="text-align:center;font-style:italic;">No Data, Generate Please</div>';
}

// Treat HTML as empty if it only contains whitespace, comments, or empty tags
function isEmptyHtmlContent(html) {
    if (html == null) return true;
    const cleaned = String(html)
        .replace(/<!--([\s\S]*?)-->/g, '')
        .replace(/<script[\s\S]*?<\/script>/gi, '')
        .replace(/<style[\s\S]*?<\/style>/gi, '')
        .replace(/<[^>]*>/g, '')
        .trim();
    return cleaned.length === 0;
}

function loadInvWeekData(year, month) {
    console.log('loadInvWeekData called with year:', year, 'month:', month);
    const link = window.location.origin + '/cdummy/inventory/data/data_inv_week_show/' + year + '?month=' + month;
    console.log('Loading from URL:', link);
    loadInvWeekDataFromServer(link);
}

function loadInvWeekDataFromServer(link) {
    console.log('loadInvWeekDataFromServer called with link:', link);
    
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
                if (isEmptyHtmlContent(response)) {
                    showNoDataMessage();
                } else {
                    // Initialize info panel after data is loaded
                    setTimeout(initializeInfoPanel, 100);
                }
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
                if (isEmptyHtmlContent(data)) {
                    console.log('Empty data received');
                    showNoDataMessage();
                } else {
                    console.log('Data loaded successfully');
                    document.getElementById("show_data").innerHTML = data;
                    // Initialize info panel after data is loaded
                    setTimeout(initializeInfoPanel, 100);
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
    
    showModalMessage('Generating periods...', 'success', true);
    
    const requestData = {
        year: parseInt(year),
        month: parseInt(month)
    };
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inv_week_periods', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (loadingSpinner) loadingSpinner.style.display = 'none';
        if (data.success) {
            currentYear = parseInt(year);
            currentMonth = parseInt(month);
            // Sinkronkan dropdown filter utama agar menampilkan periode yang baru dibuat
            if (typeof syncFilterDropdowns === 'function') {
                syncFilterDropdowns(currentYear, currentMonth);
            }
            showModalMessage(data.message || 'Periode berhasil di-generate', 'success', false, 2000);
            loadInvWeekData(currentYear, currentMonth);
            setTimeout(() => closeModal('modal_input_all'), 2000);
        } else {
            showModalMessage(data.message || 'Gagal generate periode', 'error');
            // Jika gagal (kemungkinan periode sudah ada), otomatis alihkan ke mode Regenerate
            checkPeriodsExist();
        }
    })
    .catch(error => {
        if (loadingSpinner) loadingSpinner.style.display = 'none';
        // Fallback: verify if periods actually exist despite error (e.g., server returned 500 after processing)
        verifyPeriodsCreated(year, month)
            .then(exist => {
                if (exist) {
                    currentYear = parseInt(year);
                    currentMonth = parseInt(month);
                    if (typeof syncFilterDropdowns === 'function') {
                        syncFilterDropdowns(currentYear, currentMonth);
                    }
                    showModalMessage('Periode berhasil di-generate', 'success', false, 2000);
                    loadInvWeekData(currentYear, currentMonth);
                    setTimeout(() => closeModal('modal_input_all'), 2000);
                } else {
                    showModalMessage('Error: ' + error.message, 'error');
                }
            })
            .catch(() => {
                showModalMessage('Error: ' + error.message, 'error');
            });
    });
}

// Removed invalid jQuery ready block that caused parse errors

// (regenerate functionality removed)

// Helper to verify if periods exist for given year/month
function verifyPeriodsCreated(year, month) {
    const url = window.location.origin + '/cdummy/inventory/check_inv_week_periods?year=' + year + '&month=' + month;
    return fetch(url)
        .then(res => res.ok ? res.json() : Promise.reject(new Error('HTTP ' + res.status)))
        .then(json => !!(json && json.exists))
        .catch(() => false);
}

// Export function
function exportInvWeekData() {
    // Ambil nilai langsung dari dropdown jika tersedia, agar UI mengikuti export
    const yearEl = document.getElementById('year_filter');
    const monthEl = document.getElementById('month_filter');

    let usedYear = (yearEl && yearEl.value) ? parseInt(yearEl.value, 10) : (currentYear || new Date().getFullYear());
    let usedMonth = (monthEl && monthEl.value) ? parseInt(monthEl.value, 10) : (currentMonth || (new Date().getMonth() + 1));

    // Sinkronkan ke variabel global agar konsisten di seluruh halaman
    currentYear = usedYear;
    currentMonth = usedMonth;

    // Pastikan dropdown menampilkan nilai yang akan diexport
    if (yearEl) yearEl.value = String(usedYear);
    if (monthEl) monthEl.value = String(usedMonth);

    if (!currentYear || !currentMonth) {
        showMessage('Pilih tahun dan bulan terlebih dahulu sebelum export', 'error');
        return;
    }

    try {
        showMessage('Exporting data...', 'info');
        const exportUrl = window.location.origin + '/cdummy/inventory/export_inv_week?year=' + usedYear + '&month=' + usedMonth;
        window.open(exportUrl, '_blank');
        setTimeout(() => {
            showMessage('Export berhasil dibuka di tab baru', 'success');
        }, 1000);
    } catch (error) {
        console.error('Export error:', error);
        showMessage('Error saat export: ' + error.message, 'error');
    }
}

// View existing data from modal action
function viewExistingData(year, month) {
    try {
        currentYear = parseInt(year);
        currentMonth = parseInt(month);
        // Pastikan dropdown filter ikut menampilkan periode yang dilihat
        if (typeof syncFilterDropdowns === 'function') {
            syncFilterDropdowns(currentYear, currentMonth);
        }
        showModalMessage(`Menampilkan data untuk bulan ${getMonthName(currentMonth)} ${currentYear}`, 'success');
        loadInvWeekData(currentYear, currentMonth);
        setTimeout(() => closeModal('modal_input_all'), 300);
    } catch (e) {
        showModalMessage('Gagal menampilkan data: ' + e.message, 'error');
    }
}

// Edit period functions
function editPeriod(id_week, date_start, date_finish) {
    document.getElementById('edit_id_week').value = id_week;
    document.getElementById('edit_date_start').value = (String(date_start).slice(0,10));
    document.getElementById('edit_date_finish').value = (String(date_finish).slice(0,10));
    
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
    
    // tampilkan konfirmasi ala regenerate
    showUpdateConfirmation();
}

// Konfirmasi update periode (mirip dengan konfirmasi regenerate)
function showUpdateConfirmation() {
    renderConfirmation('#modal_edit .modal-footer', 'edit_confirmation',
        'Apakah Anda yakin ingin mengupdate periode ini?',
        () => confirmUpdatePeriod(),
        () => cancelUpdatePeriod()
    );
}

function confirmUpdatePeriod() {
    const section = document.getElementById('edit_confirmation');
    if (section) section.remove();
    executeUpdatePeriod();
}

function cancelUpdatePeriod() {
    const section = document.getElementById('edit_confirmation');
    if (section) section.remove();
}

// Generic confirmation renderer
function renderConfirmation(footerSelector, elementId, text, onConfirm, onCancel) {
    const existing = document.getElementById(elementId);
    if (existing) return;
    const modalFooter = document.querySelector(footerSelector);
    if (!modalFooter) return;
    const confirmationHtml = `
        <div id="${elementId}" class="confirmation-section" style="display: block;">
            <div class="confirmation-content">
                <div class="confirmation-text">${text}</div>
                <div class="confirmation-buttons">
                    <button class="btn-confirm" data-role="confirm">Ya</button>
                    <button class="btn-cancel" data-role="cancel">Tidak</button>
                </div>
            </div>
        </div>
    `;
    modalFooter.insertAdjacentHTML('afterend', confirmationHtml);
    const container = document.getElementById(elementId);
    if (!container) return;
    const btnConfirm = container.querySelector('button[data-role="confirm"]');
    const btnCancel = container.querySelector('button[data-role="cancel"]');
    if (btnConfirm) btnConfirm.addEventListener('click', onConfirm);
    if (btnCancel) btnCancel.addEventListener('click', onCancel);
}

function executeUpdatePeriod() {
    const id_week = document.getElementById('edit_id_week').value;
    const date_start = document.getElementById('edit_date_start').value;
    const date_finish = document.getElementById('edit_date_finish').value;

    // Show loading state
    const updateBtn = document.querySelector('#modal_edit .btn-primary');
    const originalText = updateBtn.textContent;
    updateBtn.textContent = 'Updating...';
    updateBtn.disabled = true;

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
            showMessage('Periode berhasil diupdate', 'success');
            closeModal('modal_edit');
            setTimeout(() => { loadInvWeekData(currentYear, currentMonth); }, 800);
        } else {
            showMessage(data.message || 'Gagal update periode', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating period:', error);
        showMessage('Error: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        updateBtn.textContent = originalText;
        updateBtn.disabled = false;
    });
}

// Delete week functions
function deleteWeek(id_week) {
    if (!id_week) return;
    showConfirmDialog('Yakin ingin menghapus periode minggu ini? Tindakan ini tidak dapat dibatalkan.', function(){
        executeDeleteWeek(id_week);
    });
}

function executeDeleteWeek(id_week) {
    fetch('<?= base_url('inventory/delete_inv_week_period') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_week: parseInt(id_week) })
    })
    .then(function(response){
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(function(data){
        if (data.success) {
            showMessage('Periode berhasil dihapus', 'success');
            setTimeout(function(){ loadInvWeekData(currentYear, currentMonth); }, 500);
        } else {
            showMessage(data.message || 'Gagal menghapus periode', 'error');
        }
    })
    .catch(function(err){
        showMessage('Error: ' + err.message, 'error');
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
        setDefaultGenerateFooter();
    }
    
    // Reset modal state when opening
    resetModalState();
    // Cek status periode untuk tahun/bulan default saat modal pertama dibuka
    checkPeriodsExist();
}

// Debounce function to prevent too many API calls
let checkPeriodsTimeout = null;

// Reset modal state function
function resetModalState() {
    const modalResultDiv = document.getElementById('modal_result_message');
    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
    
    // Remove any existing confirmation dialog (legacy id removed)
    
    // Reset modal footer to default state
    if (modalFooter) {
        setDefaultGenerateFooter();
    }
    
    // Reset result message
    if (modalResultDiv) {
        modalResultDiv.style.display = 'none';
        modalResultDiv.innerHTML = '';
        modalResultDiv.className = 'input-result-message';
    }
}

// Set default modal footer for Generate action
function setDefaultGenerateFooter() {
    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
    if (!modalFooter) return;
    modalFooter.innerHTML = `
        <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
        <button class="btn btn-primary" onclick="generateInvWeekPeriods()" id="generate_btn">
            Generate Periode <span id="generate_loading_spinner" class="loading-spinner" style="display:none;"></span>
        </button>
    `;
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
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.exists) {
                    // Update modal footer for existing periods (no regenerate)
                    const modalFooter = document.querySelector('#modal_input_all .modal-footer');
                    if (modalFooter) {
                        modalFooter.innerHTML = `
                            <button class="btn btn-secondary" onclick="closeModal('modal_input_all')">Batal</button>
                            <button class="btn btn-lihat-data" onclick="viewExistingData(${year}, ${month})">Lihat Data</button>
                        `;
                    }
                    
                    if (modalResultDiv) {
                        modalResultDiv.innerHTML = `
                            <div style="margin-bottom: 10px;">
                                <strong>Periode untuk tahun ${year} bulan ${getMonthName(month)} sudah ada.</strong><br>
                                • Klik "Lihat Data" untuk menampilkan data yang sudah ada
                            </div>
                        `;
                        modalResultDiv.className = 'input-result-message error';
                        modalResultDiv.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error checking periods:', error);
                // On error, ensure we're in default state and show error message
                resetModalState();
                if (modalResultDiv) {
                    modalResultDiv.innerHTML = `
                        <div style="margin-bottom: 10px; color: #721c24;">
                            <strong>Error:</strong> Gagal memeriksa status periode. Silakan coba lagi.
                        </div>
                    `;
                    modalResultDiv.className = 'input-result-message error';
                    modalResultDiv.style.display = 'block';
                }
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
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    let options = '';
    months.forEach((month, index) => {
        const monthNumber = index + 1;
        // Do not preselect any month for the modal input; keep placeholder "Pilih Bulan"
        options += `<option value="${monthNumber}">${month}</option>`;
    });
    return options;
}

// Toggle info panel function
function toggleInfoPanel() {
    const infoPanel = document.getElementById('infoPanel');
    const toggleIcon = document.querySelector('.toggle-icon');
    const toggleBtn = document.querySelector('.info-toggle-btn');
    
    if (infoPanel && toggleIcon && toggleBtn) {
        infoPanel.classList.toggle('active');
        toggleIcon.classList.toggle('active');
        
        // Keep button text constant
        const toggleText = toggleBtn.querySelector('span');
        if (toggleText) {
            toggleText.textContent = 'Informasi Periode';
        }
    }
}

// Initialize info panel when data is loaded
function initializeInfoPanel() {
    const infoPanel = document.getElementById('infoPanel');
    const toggleBtn = document.querySelector('.info-toggle-btn');
    
    if (infoPanel && toggleBtn) {
        // Ensure info panel starts in closed state
        infoPanel.classList.remove('active');
        
        // Reset toggle icon and text state
        const toggleIcon = document.querySelector('.toggle-icon');
        if (toggleIcon) {
            toggleIcon.classList.remove('active');
        }
        const toggleText = toggleBtn.querySelector('span');
        if (toggleText) {
            toggleText.textContent = 'Informasi Periode'; // keep constant
        }

        // Remove inline onclick to prevent double trigger
        if (toggleBtn.hasAttribute('onclick')) {
            toggleBtn.removeAttribute('onclick');
        }
        
        // Add click event listener if not already added
        if (!toggleBtn.hasAttribute('data-initialized')) {
            toggleBtn.setAttribute('data-initialized', 'true');
            toggleBtn.addEventListener('click', toggleInfoPanel);
        }
    }
}

function getMonthName(month) {
    const months = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month] || month;
}

// Sinkronkan nilai dropdown filter tahun/bulan pada toolbar dengan periode aktif
function syncFilterDropdowns(year, month) {
    const yearEl = document.getElementById('year_filter');
    const monthEl = document.getElementById('month_filter');

    if (yearEl) {
        // Tambahkan opsi tahun jika belum ada
        if (!yearEl.querySelector(`option[value="${year}"]`)) {
            const opt = document.createElement('option');
            opt.value = String(year);
            opt.textContent = String(year);
            yearEl.appendChild(opt);
        }
        yearEl.value = String(year);
    }

    if (monthEl) {
        // Tambahkan opsi bulan jika belum ada (harusnya sudah ada dari 1-12)
        if (!monthEl.querySelector(`option[value="${month}"]`)) {
            const opt = document.createElement('option');
            opt.value = String(month);
            opt.textContent = getMonthName(parseInt(month, 10));
            monthEl.appendChild(opt);
        }
        monthEl.value = String(month);
    }
}

// Removed unused formatDateTime (kept formatDateTimeForInput which is used)

function formatDateTimeForInput(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toISOString().slice(0, 10);
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

function showModalMessage(message, type, withSpinner = false, durationMs = null) {
    const element = document.getElementById('modal_result_message');
    if (!element) return;

    element.className = 'input-result-message ' + type;
    element.style.display = 'block';

    if (withSpinner) {
        element.innerHTML = `
            <span style="display: inline-flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="#0074d9" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 188.4">
                        <animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite" />
                    </circle>
                </svg>
                <span>${message}</span>
            </span>
        `;
    } else {
        element.textContent = message;
    }

    if (durationMs && Number.isFinite(durationMs)) {
        setTimeout(() => {
            element.style.display = 'none';
        }, durationMs);
    }
}

// Event listeners for inv_week
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters and populate year dropdown
    initializeFilters();

    // Custom select for year/month (inv_week toolbar)
    const csYear = document.getElementById('cs_year');
    const csMonth = document.getElementById('cs_month');

    function bindCustomSelect(csRoot, onChange) {
        if (!csRoot) return;
        const btn = csRoot.querySelector('.cs-button');
        const menu = csRoot.querySelector('.cs-menu');
        csRoot.addEventListener('click', function(e){
            if (e.target.classList.contains('cs-option')) {
                const value = e.target.getAttribute('data-value');
                const label = e.target.textContent;
                csRoot.querySelectorAll('.cs-option').forEach(o => o.classList.remove('active'));
                e.target.classList.add('active');
                if (btn) btn.firstChild.nodeValue = label + ' ';
                if (typeof onChange === 'function') onChange(value, label);
                csRoot.classList.remove('open');
            } else if (e.target === btn || btn.contains(e.target)) {
                csRoot.classList.toggle('open');
            }
        });
        document.addEventListener('click', function(e){
            if (!csRoot.contains(e.target)) csRoot.classList.remove('open');
        });
    }

    bindCustomSelect(csYear, function(value){
        const yf = document.getElementById('year_filter');
        if (yf) { yf.value = String(value); }
        if (document.getElementById('month_filter')?.value) {
            searchByMonth();
        }
    });

    bindCustomSelect(csMonth, function(value){
        const mf = document.getElementById('month_filter');
        if (mf) { mf.value = String(value); }
        if (document.getElementById('year_filter')?.value) {
            searchByMonth();
        }
    });
    
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
// Unified overlay and ESC handlers
document.addEventListener('click', function(event) {
    const overlay = document.getElementById('modal_overlay');
    if (event.target === overlay) {
        const modals = document.querySelectorAll('.modal-container');
        modals.forEach(modal => {
            if (modal && modal.style.display === 'block') {
                if (typeof closeModal === 'function') closeModal(modal.id); else modal.style.display = 'none';
            }
        });
        overlay.style.display = 'none';
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const overlay = document.getElementById('modal_overlay');
        const modals = document.querySelectorAll('.modal-container');
        let hasOpen = false;
        modals.forEach(modal => {
            if (modal && modal.style.display === 'block') {
                hasOpen = true;
                if (typeof closeModal === 'function') closeModal(modal.id); else modal.style.display = 'none';
            }
        });
        if (hasOpen && overlay) overlay.style.display = 'none';
    }
});

$(document).ready(function() {
    $('.needs-input').prop('disabled', true);
    setTimeout(calculateTotals, 100);
    
    $('.needs-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });
});

// =================== INVENTORY REPORT FUNCTIONS ===================

// Global variables for inv_report
var selectedTech = 'ecbs';
var selectedType = 'app';
var viewMode = 'summary'; // 'summary' | 'detail'

function setVisibilityForMode() {
    var isSummary = viewMode === 'summary';
    var lbl = document.getElementById('mode_toggle_label');
    if (lbl) lbl.textContent = isSummary ? 'Summary' : 'Detail';
    // Tech group always visible; type group only in detail
    var typeGroup = document.getElementById('group_type');
    if (typeGroup) typeGroup.style.display = isSummary ? 'none' : 'inline-flex';
    // Containers
    var sum = document.getElementById('show_summary_report');
    var det = document.getElementById('show_data_report');
    if (sum) sum.style.display = isSummary ? 'block' : 'none';
    if (det) det.style.display = isSummary ? 'none' : 'block';
}

function toggleViewMode() {
    viewMode = (viewMode === 'summary') ? 'detail' : 'summary';
    setVisibilityForMode();
    if (viewMode === 'detail') {
        // Keep existing behavior
        showData();
    } else {
        // In summary, show wrapper according to selected tech
        updateSummaryTech();
    }
}

function updateSummaryTech() {
    var showEcbs = selectedTech === 'ecbs';
    var ecbsWrap = document.getElementById('summary_ecbs_wrapper');
    var ecctWrap = document.getElementById('summary_ecct_wrapper');
    if (ecbsWrap) ecbsWrap.style.display = showEcbs ? 'block' : 'none';
    if (ecctWrap) ecctWrap.style.display = showEcbs ? 'none' : 'block';
}

function selectTech_report(tech) {
    selectedTech = tech;
    
    // Update button states
    document.getElementById('btn_ecbs').className = tech === 'ecbs' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
    document.getElementById('btn_ecct').className = tech === 'ecct' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
    
    if (viewMode === 'detail') {
        showData();
    } else {
        updateSummaryTech();
    }
}

function selectType_report(type) {
    selectedType = type;
    
    // Update button states
    document.getElementById('btn_app').className = type === 'app' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
    document.getElementById('btn_osc').className = type === 'osc' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
    
    if (viewMode === 'detail') {
        showData();
    }
}

function showData() {
    var link = window.location.origin + '/cdummy/inventory/inv_report_data/report_' + selectedTech + '_' + selectedType + '_show';
    
    // Add current search parameters
    var deviceSearch = document.getElementById('device_search') ? document.getElementById('device_search').value : '';
    var year = document.getElementById('filter_year') ? document.getElementById('filter_year').value : '';
    var month = document.getElementById('filter_month') ? document.getElementById('filter_month').value : '';
    var week = document.getElementById('filter_week') ? document.getElementById('filter_week').value : '';
    
    var params = [];
    if (deviceSearch) params.push('device_search=' + encodeURIComponent(deviceSearch));
    if (year) params.push('year=' + encodeURIComponent(year));
    if (month) params.push('month=' + encodeURIComponent(month));
    if (week) params.push('week=' + encodeURIComponent(week));
    
    if (params.length > 0) {
        link += '?' + params.join('&');
    }
    
    document.getElementById('show_data_report').innerHTML = '<div style="text-align: center; padding: 20px;">Loading data...</div>';
    
    if (typeof window.$ !== "undefined") {
        window.$("#show_data_report").load(link);
    } else {
        fetch(link)
            .then(response => response.text())
            .then(data => {
                document.getElementById("show_data_report").innerHTML = data;
            })
            .catch(error => {
                document.getElementById("show_data_report").innerHTML = 
                    '<div style="padding: 20px; text-align: center; color: red;">Error loading data: ' + error.message + '</div>';
            });
    }
}

function exportData_report() {
    var link = window.location.origin + '/cdummy/inventory/inv_report_data/report_' + selectedTech + '_' + selectedType + '_export';
    window.open(link, '_blank').focus();
}

// Generate inventory report data
function generateInventoryReport() {
    showConfirmDialog('This will generate inventory report data from existing tables. This may take some time. Continue?', function() {
        proceedWithGeneration();
    });
}

function proceedWithGeneration() {
    
    var btn = document.getElementById('btn_generate');
    btn.disabled = true;
    btn.innerHTML = '<i class="ki-filled ki-loading !text-base"></i>Generating...';
    
    fetch(window.location.origin + '/cdummy/inventory/generate_inventory_report', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({}) // Send empty JSON body to ensure proper request
    })
    .then(response => {
        
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        
        return response.text().then(text => {
            
            if (!text || text.trim() === '') {
                throw new Error('Empty response received');
            }
            
            try {
                const parsed = JSON.parse(text);
                console.log('Successfully parsed JSON:', parsed);
                return parsed;
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Raw text that failed to parse:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        console.log('Parsed response data:', data);
        if (data.success) {
            showToast('Inventory report data generated successfully!', 'success');
            // Auto refresh after successful generation
            setTimeout(() => {
                showData(); // Refresh the table
            }, 1000);
        } else {
            showToast('Failed to generate: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Generate error:', error);
        showToast('Failed to generate inventory report data: ' + error.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ki-filled ki-setting !text-base"></i>Generate Data';
    });
}

function showInputPmsModal() {
    // Use the same custom modal system as Filter
    openModal_report('modal_input_pms');
    
    // Add event listener for preview
    const textarea = document.getElementById('massive_pms_input');
    if (textarea) {
        textarea.focus();
        textarea.addEventListener('input', updatePmsPreview);
    }
}

function closeInputPmsModal() {
    // Close modal via the shared handler
    closeModal_report('modal_input_pms');
    // Clear textarea and preview after closing
    const textarea = document.getElementById('massive_pms_input');
    if (textarea) textarea.value = '';
    const preview = document.getElementById('preview_pms_data');
    if (preview) {
        preview.innerHTML = '';
        preview.style.display = 'none';
    }
}

// Removed week/device loaders per request

function updateDeviceColorsPms() {
    const deviceId = document.getElementById('select_device_pms').value;
    if (deviceId) {
        const params = new URLSearchParams({ id_dvc: deviceId });
        fetch(window.location.origin + '/cdummy/inventory/get_device_colors?' + params)
            .then(response => response.json())
            .then(data => {
                // Update preview with available colors
                updatePmsPreview();
            })
            .catch(error => {
                console.error('Error loading colors:', error);
            });
    }
}

function updateDeviceColors() {
    var deviceId = document.getElementById('select_device').value;
    if (deviceId) {
        var params = new URLSearchParams({ id_dvc: deviceId });
        fetch(window.location.origin + '/cdummy/inventory/get_device_colors?' + params)
            .then(response => response.json())
            .then(data => {
                var options = '<option value="">-- Select Color --</option>';
                if (data.success && data.colors) {
                    data.colors.forEach(function(color) {
                        var displayColor = color === '' ? '(Empty)' : color;
                        options += '<option value="' + color + '">' + displayColor + '</option>';
                    });
                }
                document.getElementById('select_color').innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading colors:', error);
            });
    } else {
        document.getElementById('select_color').innerHTML = '<option value="">-- Select Color --</option>';
    }
}

// Removed device color updater per request

function updatePmsPreview() {
    const input = document.getElementById('massive_pms_input').value;
    const preview = document.getElementById('preview_pms_data');
    
    if (!input.trim()) {
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }
    
    const lines = input.split('\n').filter(line => line.trim());
    let previewHTML = '<div style="margin-bottom: 10px;"><strong>Preview Data:</strong></div>';
    
    const processedData = [];
    const dataCount = {};
    
    lines.forEach((line, index) => {
        const parts = line.split('\t').map(part => part.trim());
        
        if (parts.length >= 4) {
            const kodeAlat = parts[0] || '';
            const ukuran = parts[1] || '';
            const warna = parts[2] || '';
            const status = parts[3] || '';
            const stock = parts.length >= 5 ? parts[4] : '1';
            
            // Create unique key for counting duplicates
            const key = `${kodeAlat}|${ukuran}|${warna}|${status}`;
            dataCount[key] = (dataCount[key] || 0) + 1;
            
            processedData.push({
                kodeAlat,
                ukuran,
                warna,
                status,
                stock: stock === '' ? '1' : stock,
                originalStock: stock
            });
        }
    });
    
    // Calculate final stock for duplicates
    const finalData = [];
    processedData.forEach(item => {
        const key = `${item.kodeAlat}|${item.ukuran}|${item.warna}|${item.status}`;
        const count = dataCount[key];
        
        if (count > 1) {
            // If there are duplicates, use the count as stock
            item.finalStock = count;
        } else {
            // If no duplicates, use original stock or 1
            item.finalStock = item.originalStock === '' ? 1 : parseInt(item.originalStock) || 1;
        }
        
        finalData.push(item);
    });
    
    // Remove duplicates for display
    const uniqueData = [];
    const seen = new Set();
    
    finalData.forEach(item => {
        const key = `${item.kodeAlat}|${item.ukuran}|${item.warna}|${item.status}`;
        if (!seen.has(key)) {
            seen.add(key);
            uniqueData.push(item);
        }
    });
    
    if (uniqueData.length === 0) {
        previewHTML += '<div style="color: red;">Format data tidak valid. Pastikan minimal 4 kolom dipisahkan dengan Tab.</div>';
    } else {
        previewHTML += '<table style="width: 100%; border-collapse: collapse; font-size: 11px;">';
        previewHTML += '<tr style="background: #e9ecef;"><th style="border: 1px solid #ddd; padding: 4px;">Kode</th><th style="border: 1px solid #ddd; padding: 4px;">Ukuran</th><th style="border: 1px solid #ddd; padding: 4px;">Warna</th><th style="border: 1px solid #ddd; padding: 4px;">Status</th><th style="border: 1px solid #ddd; padding: 4px;">Stock</th></tr>';
        
        uniqueData.forEach(item => {
            previewHTML += `<tr>
                <td style="border: 1px solid #ddd; padding: 4px;">${item.kodeAlat}</td>
                <td style="border: 1px solid #ddd; padding: 4px;">${item.ukuran}</td>
                <td style="border: 1px solid #ddd; padding: 4px;">${item.warna}</td>
                <td style="border: 1px solid #ddd; padding: 4px;">${item.status}</td>
                <td style="border: 1px solid #ddd; padding: 4px;">${item.finalStock}</td>
            </tr>`;
        });
        
        previewHTML += '</table>';
        previewHTML += `<div style="margin-top: 10px; color: #666;">Total data: ${uniqueData.length}</div>`;
    }
    
    preview.innerHTML = previewHTML;
    preview.style.display = 'block';
}

function saveMassiveOnPms() {
    const input = document.getElementById('massive_pms_input').value;
    if (!input.trim()) {
        showToast('Input masih kosong', 'error');
        return;
    }
    
    // Process input data
    const lines = input.split('\n').filter(line => line.trim());
    const processedData = [];
    const dataCount = {};
    
    lines.forEach(line => {
        const parts = line.split('\t').map(part => part.trim());
        
        if (parts.length >= 4) {
            const kodeAlat = parts[0] || '';
            const ukuran = parts[1] || '';
            const warna = parts[2] || '';
            const status = parts[3] || '';
            const stock = parts.length >= 5 ? parts[4] : '';
            
            // Create unique key for counting duplicates
            const key = `${kodeAlat}|${ukuran}|${warna}|${status}`;
            dataCount[key] = (dataCount[key] || 0) + 1;
            
            processedData.push({
                kodeAlat,
                ukuran,
                warna,
                status,
                originalStock: stock
            });
        }
    });
    
    // Calculate final stock for duplicates and create unique data
    const uniqueData = [];
    const seen = new Set();
    
    processedData.forEach(item => {
        const key = `${item.kodeAlat}|${item.ukuran}|${item.warna}|${item.status}`;
        if (!seen.has(key)) {
            seen.add(key);
            
            const count = dataCount[key];
            let finalStock;
            
            if (count > 1) {
                // If there are duplicates, use the count as stock
                finalStock = count;
            } else {
                // If no duplicates, use original stock or 1
                finalStock = item.originalStock === '' ? 1 : parseInt(item.originalStock) || 1;
            }
            
            uniqueData.push({
                kodeAlat: item.kodeAlat,
                ukuran: item.ukuran,
                warna: item.warna,
                status: item.status,
                stock: finalStock
            });
        }
    });
    
    if (uniqueData.length === 0) {
        showToast('No valid data found. Please check the format.', 'error');
        return;
    }
    
    // Show confirmation
    const confirmMessage = `Yakin simpan ${uniqueData.length} data ke On PMS?`;
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Send data to server
    const formData = { data: uniqueData };
    
    fetch(window.location.origin + '/cdummy/inventory/save_massive_on_pms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('On PMS data saved successfully!', 'success');
            closeInputPmsModal();
            showData(); // Refresh the table
        } else {
            showToast('Failed to save: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error saving data:', error);
        showToast('Failed to save data', 'error');
    });
}

// Apply filters function
function applyFilters() {
    closeModal('modal_filter_report');
    if (viewMode === 'detail') {
        showData();
        return;
    }
    // Summary mode: load only the active summary wrapper without changing URL
    var params = [];
    var deviceSearch = document.getElementById('device_search') ? document.getElementById('device_search').value : '';
    var year = document.getElementById('filter_year') ? document.getElementById('filter_year').value : '';
    var month = document.getElementById('filter_month') ? document.getElementById('filter_month').value : '';
    var week = document.getElementById('filter_week') ? document.getElementById('filter_week').value : '';
    params.push('tech=' + encodeURIComponent(selectedTech));
    if (deviceSearch) params.push('device_search=' + encodeURIComponent(deviceSearch));
    if (year) params.push('year=' + encodeURIComponent(year));
    if (month) params.push('month=' + encodeURIComponent(month));
    if (week) params.push('week=' + encodeURIComponent(week));
    var query = params.length ? ('?' + params.join('&')) : '';
    var link = window.location.origin + '/cdummy/inventory/inv_report_summary' + query;
    var targetId = selectedTech === 'ecbs' ? 'summary_ecbs_wrapper' : 'summary_ecct_wrapper';
    var container = document.getElementById(targetId);
    if (container) {
        container.innerHTML = '<div style="text-align:center;padding:16px;">Loading...</div>';
        if (typeof window.$ !== 'undefined') {
            window.$('#' + targetId).load(link);
        } else {
            fetch(link).then(function(r){return r.text()}).then(function(html){ container.innerHTML = html; });
        }
    }
}

// Helper to read URL query params
function getQueryParam(name) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Auto search functionality
let searchTimeout = null;

function setupAutoSearch() {
    const searchInput = document.getElementById("device_search");
    
    if (searchInput) {
        searchInput.removeEventListener("input", handleAutoSearch);
        
        searchInput.addEventListener("input", handleAutoSearch);
        
        // Keep entr key functionality
        searchInput.addEventListener("keyup", function(event) {
            if (event.key === 'Enter') {
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                showData();
            }
        });
    }
}

// Handle auto search with debounce
function handleAutoSearch(event) {
    const searchTerm = event.target.value.trim();
    
    // Clear existing timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Set timeout for debounce (500ms delay)
    searchTimeout = setTimeout(() => {
        showData();
    }, 500);
}

// Modal functions for inv_report
function openModal_report(modalId) {
    // Delegate to unified modal opener
    if (typeof openModal === 'function') {
        openModal(modalId);
    } else {
        document.getElementById(modalId).style.display = 'block';
        document.getElementById('modal_overlay').style.display = 'block';
    }
}

function closeModal_report(modalId) {
    // Delegate to unified modal closer
    if (typeof closeModal === 'function') {
        closeModal(modalId);
    } else {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById('modal_overlay').style.display = 'none';
    }
}

// Event listeners for inv_report
document.addEventListener('DOMContentLoaded', function() {
    // Setup auto search for inv_report
    setupAutoSearch();
    
    // Setup device color change listener
    const deviceSelect = document.getElementById('select_device');
    if (deviceSelect) {
        deviceSelect.addEventListener('change', updateDeviceColors);
    }
    
    // Initialize view mode visibility (default Summary)
    setVisibilityForMode();
    updateSummaryTech();
});

// (handlers consolidated above)
    
    function selectTech_needs(tech) {
        selectedTech = tech;
        
        // Update button states
        document.getElementById('btn_ecbs').className = tech === 'ecbs' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        document.getElementById('btn_ecct').className = tech === 'ecct' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        
        showData_needs();
    }
    
    function selectType_needs(type) {
        selectedType = type;
        
        // Update button states
        document.getElementById('btn_app').className = type === 'app' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        document.getElementById('btn_osc').className = type === 'osc' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        
        showData_needs();
    }
    
    function showData_needs() {
        var link = "<?php echo base_url(); ?>inventory/report/report_" + selectedTech + "_" + selectedType + "_show";
        
        document.getElementById('show_data_needs').innerHTML = '<div style="text-align: center; padding: 20px;">Loading data...</div>';
        $("#show_data_needs").load(link);
    }
    
    function exportData_needs() {
        var link = "<?php echo base_url(); ?>inventory/report/report_" + selectedTech + "_" + selectedType + "_export";
        window.open(link, '_blank').focus();
    }
</script>