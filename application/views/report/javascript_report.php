<script type="text/javascript">
var currentEditMode = false; // true for edit mode, false for view-only mode

// Function to set the edit mode explicitly
function setEditMode(mode) {
    currentEditMode = mode; // Update the global mode variable

    // Enable/disable inputs based on the mode
    $('.needs-input').prop('disabled', !currentEditMode);

    // Always show the Edit button
    $('#editModeBtn').show();

    // Show/hide Save All Data button based on the mode
    if (currentEditMode) {
        $('#saveAllDataBtn').show();
    } else {
        $('#saveAllDataBtn').hide();
    }
    calculateTotals(); // Recalculate totals to ensure display is correct
}

// Function to toggle the edit mode
function toggleEditMode() {
    setEditMode(!currentEditMode); // Simply toggle the current mode
}

function calculateTotals() {
    var sizes = ['xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus'];
    var grandTotal = 0;
    var rowSubtotals = {};

    $('.needs-input').each(function() {
        var idDvc = $(this).data('id-dvc');
        var color = $(this).data('color');
        var rowKey = idDvc + '_' + color;
        rowSubtotals[rowKey] = (rowSubtotals[rowKey] || 0) + (parseInt($(this).val()) || 0);
    });

    for (var rowKey in rowSubtotals) {
        if (rowSubtotals.hasOwnProperty(rowKey)) {
            $('#subtotal_' + rowKey).text(rowSubtotals[rowKey]);
            grandTotal += rowSubtotals[rowKey];
        }
    }

    $('#grand_total').text(grandTotal);

    sizes.forEach(function(size) {
        var sizeTotal = 0;
        $('.needs-input[data-size="' + size + '"]').each(function() {
            sizeTotal += parseInt($(this).val()) || 0;
        });
        $('#total_' + size).text(sizeTotal);
        $('#percent_' + size).text(grandTotal > 0 ? Math.round((sizeTotal / grandTotal) * 100 * 10) / 10 : 0);
    });

    for (var rowKey in rowSubtotals) {
        if (rowSubtotals.hasOwnProperty(rowKey)) {
            $('#percentage_' + rowKey).text(grandTotal > 0 ? Math.round((rowSubtotals[rowKey] / grandTotal) * 100 * 10) / 10 : 0);
        }
    }
}

function saveAllData() {
    var allData = [];
    $('.needs-input').each(function() {
        allData.push({
            id_dvc: $(this).data('id-dvc'),
            dvc_size: $(this).data('size'),
            dvc_col: $(this).data('color'),
            dvc_qc: $(this).data('qc'),
            needs_qty: parseInt($(this).val()) || 0
        });
    });
    
    $.ajax({
        url: '<?php echo base_url(); ?>inventory/save_all_needs_data',
        type: 'POST',
        data: {data: allData},
        success: function(response) {
            alert('All data saved successfully!');
            calculateTotals(); 
            setEditMode(false); // Revert to view-only mode after saving
        },
        error: function() {
            alert('Failed to save all data.');
        }
    });
}

$(document).ready(function() {
    calculateTotals();
    // Set mode awal ke "view-only" saat halaman dimuat
    setEditMode(false); 
});
</script>
