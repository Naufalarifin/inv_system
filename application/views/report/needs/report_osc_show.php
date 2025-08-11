<?php
$sizes = array('xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus');
$model_data = isset($data) && is_array($data) ? $data : array();
$existing_needs = isset($existing_needs) && is_array($existing_needs) ? $existing_needs : array();

// Function to get existing value
function getExistingValue($existing_needs, $id_dvc, $size, $color, $qc) {
    $key = $id_dvc . '_' . $size . '_' . $color . '_' . $qc;
    return isset($existing_needs[$key]) ? $existing_needs[$key] : 0;
}
?>

<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 text-s compact-table">
            <thead>
                <tr>
                    <th align="center">No</th>
                    <th align="center">Nama Barang</th>
                    <th align="center">Kode</th>
                    <?php foreach ($sizes as $sz) { ?>
                        <th align="center" colspan="2">
                            <?php echo strtoupper($sz); ?>
                        </th>
                    <?php } ?>
                    <th align="center">Subtotal</th>
                    <th align="center">%</th>
                </tr>
                <tr>
                    <th align="center"></th>
                    <th align="center"></th>
                    <th align="center"></th>
                    <?php foreach ($sizes as $sz) { ?>
                        <th align="center" style="font-size: 11px;">LN</th>
                        <th align="center" style="font-size: 11px;">DN</th>
                    <?php } ?>
                    <th align="center"></th>
                    <th align="center"></th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($model_data) && !empty($model_data)) {
                    $no = 0;
                    $grouped_data = array();
                    $current_group = null;
                    
                    foreach ($model_data as $row) {
                        if ($current_group === null || $current_group['dvc_name'] !== $row['dvc_name']) {
                            if ($current_group !== null) {
                                $grouped_data[] = $current_group;
                            }
                            $current_group = array(
                                'dvc_name' => $row['dvc_name'],
                                'rows' => array($row),
                                'rowspan' => 1
                            );
                        } else {
                            $current_group['rows'][] = $row;
                            $current_group['rowspan']++;
                        }
                    }
                    
                    if ($current_group !== null) {
                        $grouped_data[] = $current_group;
                    }
                    
                    foreach ($grouped_data as $group) {
                        $first_row = true;
                        
                        foreach ($group['rows'] as $row) {
                            $no++;
                ?>
                <tr>
                    <td align="center"><?php echo $no; ?></td>
                    <?php if ($first_row) { ?>
                        <td align="left" rowspan="<?php echo $group['rowspan']; ?>"><?php echo htmlspecialchars($group['dvc_name']); ?></td>
                    <?php } ?>
                    <td align="center"><?php echo htmlspecialchars($row['dvc_code']); ?></td>
                    <?php foreach ($sizes as $sz) { 
                        // OSC items use single space for color (exactly as stored in database)
                        $item_color = ' '; // Single space for OSC
                        
                        // LN column for OSC items
                        $input_id_ln = $row['dvc_code'] . '_' . $sz . '_' . $item_color . '_LN';
                        $existing_value_ln = getExistingValue($existing_needs, $row['id_dvc'], strtoupper($sz), $item_color, 'LN');
                        
                        // DN column for OSC items
                        $input_id_dn = $row['dvc_code'] . '_' . $sz . '_' . $item_color . '_DN';
                        $existing_value_dn = getExistingValue($existing_needs, $row['id_dvc'], strtoupper($sz), $item_color, 'DN');
                    ?>
                        <td align="center">
                            <input type="number" 
                                   class="form-control form-control-sm needs-input" 
                                   id="<?php echo $input_id_ln; ?>" 
                                   value="<?php echo $existing_value_ln; ?>" 
                                   min="0" 
                                   disabled
                                   style="width: 50px; text-align: center;"
                                   data-id-dvc="<?php echo $row['id_dvc']; ?>"
                                   data-size="<?php echo $sz; ?>"
                                   data-color="<?php echo $item_color; ?>"
                                   data-qc="LN"
                                   onchange="calculateTotals()">
                        </td>
                        <td align="center">
                            <input type="number" 
                                   class="form-control form-control-sm needs-input" 
                                   id="<?php echo $input_id_dn; ?>" 
                                   value="<?php echo $existing_value_dn; ?>" 
                                   min="0" 
                                   disabled
                                   style="width: 50px; text-align: center;"
                                   data-id-dvc="<?php echo $row['id_dvc']; ?>"
                                   data-size="<?php echo $sz; ?>"
                                   data-color="<?php echo $item_color; ?>"
                                   data-qc="DN"
                                   onchange="calculateTotals()">
                        </td>
                    <?php } ?>
                    <td align="center"><strong><span id="subtotal_<?php echo $row['id_dvc']; ?>_<?php echo $item_color; ?>">0</span></strong></td>
                    <td align="center"><span id="percentage_<?php echo $row['id_dvc']; ?>_<?php echo $item_color; ?>">0</span>%</td>
                </tr>
                <?php
                            $first_row = false;
                        }
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="<?php echo 3 + (count($sizes) * 2) + 2; ?>"><i>No OSC Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="3">TOTAL</td>
                    <?php foreach ($sizes as $sz) { ?>
                        <td align="center"><span id="total_<?php echo $sz; ?>_ln">0</span></td>
                        <td align="center"><span id="total_<?php echo $sz; ?>_dn">0</span></td>
                    <?php } ?>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong><span id="grand_total">0</span></strong></td>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                </tr>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="3">PERSENTASE</td>
                    <?php foreach ($sizes as $sz) { ?>
                        <td align="center"><span id="percent_<?php echo $sz; ?>_ln">0</span>%</td>
                        <td align="center"><span id="percent_<?php echo $sz; ?>_dn">0</span>%</td>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card-footer">
    <button class="btn btn-primary" id="editModeBtn" onclick="toggleEditMode()">Edit</button>
    <button class="btn btn-success" id="saveAllDataBtn" onclick="saveAllData()" style="display: none;">Save All Data</button>
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

<?php $this->load->view('report/javascript_report'); ?>
