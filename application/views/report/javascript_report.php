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
    
    var $btn = $('#saveAllDataBtn');
    $btn.prop('disabled', true).text('Saving...');
    
    // Split batches if needed
    var batches = [];
    for (var i = 0; i < data.length; i += 100) {
        batches.push(data.slice(i, i + 100));
    }
    
    var actions = {inserted: 0, updated: 0, deleted: 0};
    
    function processBatch(idx) {
        if (idx >= batches.length) {
            $btn.prop('disabled', false).text('Save All Data');
            var msgs = [];
            if (actions.inserted) msgs.push(actions.inserted + ' added');
            if (actions.updated) msgs.push(actions.updated + ' updated');
            if (actions.deleted) msgs.push(actions.deleted + ' removed');
            
            showToast('Saved' + (msgs.length ? ' (' + msgs.join(', ') + ')' : ''));
            $('.needs-input').each(function() { $(this).data('orig', $(this).val()); });
            setTimeout(toggleEditMode, 500);
            return;
        }
        
        $.post('<?php echo base_url(); ?>inventory/save_all_needs_data', {data: batches[idx]}, function(res) {
            if (res.success !== false && res.actions) {
                Object.keys(actions).forEach(k => actions[k] += res.actions[k] || 0);
                processBatch(idx + 1);
            } else {
                $btn.prop('disabled', false).text('Save All Data');
                showToast('Save failed', 'error');
            }
        }, 'json').fail(function() {
            $btn.prop('disabled', false).text('Save All Data');
            showToast('Save failed', 'error');
        });
    }
    
    processBatch(0);
}

$(function() {
    calculateTotals();
    
    // Set initial state: view-only mode (disabled inputs)
    editMode = false;
    $('.needs-input').prop('disabled', true);
    $('#editModeBtn').text('Edit').removeClass('btn-secondary').addClass('btn-primary');
    $('#saveAllDataBtn').hide();
    
    $(document).on('input', '.needs-input', calculateTotals);
    $(document).keydown(function(e) {
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
            e.preventDefault();
            if (editMode) saveAllData();
        }
        if (e.keyCode === 27 && editMode) toggleEditMode();
    });
});
</script>