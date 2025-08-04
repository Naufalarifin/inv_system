<?php
$grand_total = 0;
$column_totals = array(
    'size_xs' => 0,
    'size_s' => 0,
    'size_m' => 0,
    'size_l' => 0,
    'size_xl' => 0,
    'size_xxl' => 0,
    'size_3xl' => 0,
    'size_all' => 0,
    'size_cus' => 0,
);

$sizes = array('xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus');
$model_data = isset($data['data']) && is_array($data['data']) ? $data['data'] : array();
?>

<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 text-s compact-table">
            <thead>
                <tr>
                    <th align="center">No</th>
                    <th align="center">Nama Barang</th>
                    <th align="center">Kode</th>
                    <th align="center">XS</th>
                    <th align="center">S</th>
                    <th align="center">M</th>
                    <th align="center">L</th>
                    <th align="center">XL</th>
                    <th align="center">XXL</th>
                    <th align="center">3XL</th>
                    <th align="center">ALL</th>
                    <th align="center">CUS</th>
                    <th align="center">Subtotal</th>
                    <th align="center">%</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                if(isset($model_data) && !empty($model_data)) {
                    // Group consecutive items with same device name
                    $grouped_data = array();
                    $current_group = null;
                    
                    foreach ($model_data as $row) {
                        if ($current_group === null || $current_group['dvc_name'] !== $row['dvc_name']) {
                            // Start new group
                            if ($current_group !== null) {
                                $grouped_data[] = $current_group;
                            }
                            $current_group = array(
                                'dvc_name' => $row['dvc_name'],
                                'rows' => array($row),
                                'rowspan' => 1
                            );
                        } else {
                            // Add to current group
                            $current_group['rows'][] = $row;
                            $current_group['rowspan']++;
                        }
                    }
                    
                    // Don't forget the last group
                    if ($current_group !== null) {
                        $grouped_data[] = $current_group;
                    }
                    
                    // Render the grouped data
                    foreach ($grouped_data as $group) {
                        $first_row = true;
                        
                        foreach ($group['rows'] as $row) {
                            $no++;
                ?>
                <tr>
                    <td align="center"><?php echo $no; ?></td>
                    <?php if ($first_row) { ?>
                        <td align="left" rowspan="<?php echo $group['rowspan']; ?>"><?php echo $group['dvc_name']; ?></td>
                    <?php } ?>
                    <td align="center"><?php echo $row['dvc_code']; ?></td>
                    <?php foreach ($sizes as $sz) { 
                        $input_id = $row['dvc_code'] . '_' . $sz . '_default_' . $row['id_dvc'];
                    ?>
                        <td align="center">
                            <input type="number" 
                                   class="form-control form-control-sm needs-input" 
                                   id="<?php echo $input_id; ?>" 
                                   value="0" 
                                   min="0" 
                                   style="width: 60px; text-align: center;"
                                   data-id-dvc="<?php echo $row['id_dvc']; ?>"
                                   data-size="<?php echo $sz; ?>"
                                   data-color="default"
                                   data-qc="<?php echo $row['id_dvc']; ?>"
                                   onchange="updateSubtotal(this)">
                        </td>
                    <?php } ?>
                    <td align="center"><strong><span id="subtotal_<?php echo $row['id_dvc']; ?>_default">0</span></strong></td>
                    <td align="center"><span id="percentage_<?php echo $row['id_dvc']; ?>_default">0</span>%</td>
                </tr>
                <?php
                            $first_row = false;
                        }
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="13"><i>No OSC Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="3">TOTAL</td>
                    <td align="center"><span id="total_xs">0</span></td>
                    <td align="center"><span id="total_s">0</span></td>
                    <td align="center"><span id="total_m">0</span></td>
                    <td align="center"><span id="total_l">0</span></td>
                    <td align="center"><span id="total_xl">0</span></td>
                    <td align="center"><span id="total_xxl">0</span></td>
                    <td align="center"><span id="total_3xl">0</span></td>
                    <td align="center"><span id="total_all">0</span></td>
                    <td align="center"><span id="total_cus">0</span></td>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong><span id="grand_total">0</span></strong></td>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                </tr>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="3">PERSENTASE</td>
                    <td align="center"><span id="percent_xs">0</span>%</td>
                    <td align="center"><span id="percent_s">0</span>%</td>
                    <td align="center"><span id="percent_m">0</span>%</td>
                    <td align="center"><span id="percent_l">0</span>%</td>
                    <td align="center"><span id="percent_xl">0</span>%</td>
                    <td align="center"><span id="percent_xxl">0</span>%</td>
                    <td align="center"><span id="percent_3xl">0</span>%</td>
                    <td align="center"><span id="percent_all">0</span>%</td>
                    <td align="center"><span id="percent_cus">0</span>%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Save Button -->
<div class="card-footer">
    <button class="btn btn-primary" onclick="saveAllData()">Save All Data</button>
    <button class="btn btn-success" onclick="calculateTotals()">Calculate Totals</button>
</div>

<style>
.compact-table {
    font-size: 13px !important;
}
.compact-table th,
.compact-table td {
    padding: 0px 4px !important;
    line-height: 1.8 !important;
}
.compact-table th {
    font-size: 14px !important;
}
</style>

<script type="text/javascript">
function updateSubtotal(input) {
    var idDvc = $(input).data('id-dvc');
    var color = $(input).data('color');
    var subtotalId = 'subtotal_' + idDvc + '_' + color;
    var percentageId = 'percentage_' + idDvc + '_' + color;
    
    // Calculate subtotal for this row
    var subtotal = 0;
    $('input[data-id-dvc="' + idDvc + '"][data-color="' + color + '"]').each(function() {
        subtotal += parseInt($(this).val()) || 0;
    });
    
    $('#' + subtotalId).text(subtotal);
    
    // Save to database
    saveInputData(input);
    
    // Recalculate totals
    calculateTotals();
}

function calculateTotals() {
    var sizes = ['xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus'];
    var grandTotal = 0;
    
    sizes.forEach(function(size) {
        var sizeTotal = 0;
        $('.needs-input[data-size="' + size + '"]').each(function() {
            sizeTotal += parseInt($(this).val()) || 0;
        });
        $('#total_' + size).text(sizeTotal);
        grandTotal += sizeTotal;
    });
    
    $('#grand_total').text(grandTotal);
    
    // Calculate percentages
    sizes.forEach(function(size) {
        var sizeTotal = parseInt($('#total_' + size).text()) || 0;
        var percentage = grandTotal > 0 ? Math.round((sizeTotal / grandTotal) * 100 * 10) / 10 : 0;
        $('#percent_' + size).text(percentage);
    });
    
    // Update row percentages
    $('[id^="subtotal_"]').each(function() {
        var subtotal = parseInt($(this).text()) || 0;
        var percentage = grandTotal > 0 ? Math.round((subtotal / grandTotal) * 100 * 10) / 10 : 0;
        var percentageId = $(this).attr('id').replace('subtotal_', 'percentage_');
        $('#' + percentageId).text(percentage);
    });
}

function saveInputData(input) {
    var data = {
        id_dvc: $(input).data('id-dvc'),
        dvc_size: $(input).data('size'),
        dvc_col: $(input).data('color'),
        dvc_qc: $(input).data('qc'),
        needs_qty: $(input).val()
    };
    
    $.ajax({
        url: '<?php echo base_url(); ?>inventory/save_needs_data',
        type: 'POST',
        data: data,
        success: function(response) {
            console.log('Data saved successfully');
        },
        error: function() {
            console.log('Error saving data');
        }
    });
}

function saveAllData() {
    var allData = [];
    
    $('.needs-input').each(function() {
        if ($(this).val() > 0) {
            allData.push({
                id_dvc: $(this).data('id-dvc'),
                dvc_size: $(this).data('size'),
                dvc_col: $(this).data('color'),
                dvc_qc: $(this).data('qc'),
                needs_qty: $(this).val()
            });
        }
    });
    
    $.ajax({
        url: '<?php echo base_url(); ?>inventory/save_all_needs_data',
        type: 'POST',
        data: {data: allData},
        success: function(response) {
            alert('All data saved successfully!');
        },
        error: function() {
            alert('Error saving data!');
        }
    });
}

// Calculate totals on page load
$(document).ready(function() {
    calculateTotals();
});
</script>
