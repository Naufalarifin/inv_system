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
    
    $('.needs-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });
});
</script>