<?php
$voh_colors = array(
    array('name' => 'Black', 'hex' => '#000000'),
    array('name' => 'Navy', 'hex' => '#001f5b'),
    array('name' => 'Maroon', 'hex' => '#800000'),
    array('name' => 'Army', 'hex' => '#4b5320'),
    array('name' => 'Dark Grey', 'hex' => '#A9A9A9'),
    array('name' => 'Grey', 'hex' => '#808080'),
    array('name' => 'Custom', 'hex' => '#ffffff'),
);

$sizes = array('xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', 'all', 'cus');
$model_data = isset($data) && is_array($data) ? $data : array();
$existing_needs = isset($existing_needs) && is_array($existing_needs) ? $existing_needs : array();
$is_ecbs = isset($tech) && $tech == 'ecbs';

// Function to get existing value - simplified
function getExistingValue($existing_needs, $id_dvc, $size, $color, $qc) {
    $key = $id_dvc . '_' . $size . '_' . $color . '_' . $qc;
    return isset($existing_needs[$key]) ? $existing_needs[$key] : 0;
}

// Group VOH items together
$grouped_data = array();
$voh_items = array();
$regular_items = array();

foreach ($model_data as $item) {
    if ($is_ecbs && (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)) {
        $voh_items[] = $item;
    } else {
        $regular_items[] = $item;
    }
}

// Merge VOH items if they exist
if (!empty($voh_items)) {
    $merged_voh = $voh_items[0];
    $merged_voh['dvc_name'] = 'Vest Outer Hoodie Element';
    $grouped_data[] = $merged_voh;
}

// Add regular items
foreach ($regular_items as $item) {
    $grouped_data[] = $item;
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
                <?php if (!empty($grouped_data)) {
                    $row_display_no = 1;
                    foreach ($grouped_data as $item) {
                        $is_voh = ($is_ecbs && (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0));
                        
                        if ($is_voh) {
                            foreach ($voh_colors as $color_idx => $color_info) {
                                // Use exact color for DB, and a hyphenated key for DOM ids
                                $color_name = $color_info['name'];
                                $color_key = str_replace(' ', '-', $color_name);
                            ?>
                                <tr>
                                    <?php if ($color_idx == 0) { ?>
                                        <td align="center" rowspan="<?php echo count($voh_colors); ?>"><?php echo $row_display_no; ?></td>
                                        <td align="left" rowspan="<?php echo count($voh_colors); ?>"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                                    <?php } ?>
                                    <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                        <?php echo htmlspecialchars($item['dvc_code']); ?>
                                        <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                            <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                        <?php } else { ?>
                                            <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                        <?php } ?>
                                    </td>
                                    <?php foreach ($sizes as $sz) {
                                         // LN column for VOH items
                                         $input_id_ln = $item['dvc_code'] . '_' . $sz . '_' . $color_name . '_LN';
                                         $existing_value_ln = getExistingValue($existing_needs, $item['id_dvc'], strtoupper($sz), $color_name, 'LN');
                                         
                                         // DN column for VOH items
                                         $input_id_dn = $item['dvc_code'] . '_' . $sz . '_' . $color_name . '_DN';
                                         $existing_value_dn = getExistingValue($existing_needs, $item['id_dvc'], strtoupper($sz), $color_name, 'DN');
                                    ?>
                                        <td align="center">
                                            <input type="number"
                                                    class="form-control form-control-sm needs-input"
                                                    id="<?php echo $input_id_ln; ?>"
                                                    value="<?php echo $existing_value_ln; ?>"
                                                    min="0"
                                                    disabled
                                                    style="width: 40px; text-align: right;"
                                                   data-id-dvc="<?php echo $item['id_dvc']; ?>"
                                                   data-size="<?php echo $sz; ?>"
                                                   data-color="<?php echo $color_name; ?>"
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
                                                    style="width: 40px; text-align: right;"
                                                   data-id-dvc="<?php echo $item['id_dvc']; ?>"
                                                   data-size="<?php echo $sz; ?>"
                                                   data-color="<?php echo $color_name; ?>"
                                                   data-qc="DN"
                                                   onchange="calculateTotals()">
                                        </td>
                                    <?php } ?>
                                    <td align="center"><strong><span id="subtotal_<?php echo $item['id_dvc']; ?>_<?php echo $color_key; ?>">0</span></strong></td>
                                    <td align="center"><span id="percentage_<?php echo $item['id_dvc']; ?>_<?php echo $color_key; ?>">0</span>%</td>
                                </tr>
                            <?php }
                            $row_display_no++;
                        } else { ?>
                            <tr>
                                <td align="center"><?php echo $row_display_no++; ?></td>
                                <td align="left"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                                <td align="center"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                                <?php foreach ($sizes as $sz) {
                                     // Determine color based on tech and type
                                     $item_color = 'default';
                                     if (!$is_voh) {
                                         if ($is_ecbs) {
                                             $item_color = 'Black'; // ECBS + APP = Black
                                         } else {
                                             $item_color = 'Dark Grey'; // ECCT + APP = Dark Grey
                                         }
                                     }
                                     
                                     // LN column for regular items
                                     $input_id_ln = $item['dvc_code'] . '_' . $sz . '_' . $item_color . '_LN';
                                     $existing_value_ln = getExistingValue($existing_needs, $item['id_dvc'], strtoupper($sz), $item_color, 'LN');
                                     
                                     // DN column for regular items
                                     $input_id_dn = $item['dvc_code'] . '_' . $sz . '_' . $item_color . '_DN';
                                     $existing_value_dn = getExistingValue($existing_needs, $item['id_dvc'], strtoupper($sz), $item_color, 'DN');
                                ?>
                                    <td align="center">
                                        <input type="number"
                                                class="form-control form-control-sm needs-input"
                                                id="<?php echo $input_id_ln; ?>"
                                                value="<?php echo $existing_value_ln; ?>"
                                                min="0"
                                                disabled
                                                style="width: 40px; text-align: right;"
                                               data-id-dvc="<?php echo $item['id_dvc']; ?>"
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
                                                style="width: 40px; text-align: right;"
                                               data-id-dvc="<?php echo $item['id_dvc']; ?>"
                                               data-size="<?php echo $sz; ?>"
                                               data-color="<?php echo $item_color; ?>"
                                               data-qc="DN"
                                               onchange="calculateTotals()">
                                    </td>
                                <?php } ?>
                                <td align="center"><strong><span id="subtotal_<?php echo $item['id_dvc']; ?>_<?php echo str_replace(' ', '-', $item_color); ?>">0</span></strong></td>
                                <td align="center"><span id="percentage_<?php echo $item['id_dvc']; ?>_<?php echo str_replace(' ', '-', $item_color); ?>">0</span>%</td>
                            </tr>
                        <?php }
                    }
                } else { ?>
                    <tr>
                        <td align="center" colspan="<?php echo 3 + (count($sizes) * 2) + 2; ?>"><i>No Data Found</i></td>
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
    font-size: 14px !important;
}
.compact-table th,
.compact-table td {
    padding: 2px 0px !important;
    line-height: 1.2 !important;
}
.compact-table th {
    font-size: 14px !important;
}
</style>

<?php $this->load->view('report/javascript_report'); ?>