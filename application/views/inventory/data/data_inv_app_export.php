<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="app_export_' . date('Y-m-d_H-i-s') . '.xls"');

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
$sizes = array('size_xs','size_s','size_m','size_l','size_xl','size_xxl','size_3xl','size_all','size_cus');
$model_data = isset($data['data']) && is_array($data['data']) ? $data['data'] : array();
foreach ($model_data as $item) {
    $grand_total += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
    foreach ($sizes as $size_key) {
        $column_totals[$size_key] += isset($item[$size_key]) ? (int)$item[$size_key] : 0;
    }
}
$is_ecbs = isset($model_data[0]['warna']);
$voh_colors = array(
    array('name' => 'Navy'),
    array('name' => 'Maroon'),
    array('name' => 'Army'),
    array('name' => 'Black'),
    array('name' => 'Grey'),
    array('name' => 'Blue Navy'),
    array('name' => 'Custom'),
);
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kode</th>
            <?php if($is_ecbs): ?><th>Warna</th><?php endif; ?>
            <th>XS</th>
            <th>S</th>
            <th>M</th>
            <th>L</th>
            <th>XL</th>
            <th>XXL</th>
            <th>3XL</th>
            <th>ALL</th>
            <th>CUS</th>
            <th>Subtotal</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $row_display_no = 1;
        if($is_ecbs) {
            // --- ECBS MODE: Tampilkan 7 baris VOH sesuai $voh_colors ---
            $voh_rows_to_display = array();
            foreach ($voh_colors as $color_info) {
                $found_voh_item = null;
                foreach ($model_data as $item) {
                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                        $found_voh_item = $item;
                        break;
                    }
                }
                if (!$found_voh_item) {
                    $found_voh_item = array(
                        'dvc_name' => 'Vest Outer Hoodie Element',
                        'dvc_code' => 'VOH',
                        'warna' => $color_info['name'],
                        'subtotal' => 0,
                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                    );
                }
                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
            }
            $voh_count = count($voh_rows_to_display);
            if ($voh_count > 0) {
                foreach ($voh_rows_to_display as $idx => $item) {
                    $row_data = $item['data'];
                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                    $percentage = $grand_total > 0 ? round(($subtotal / $grand_total) * 100, 1) : 0;
        ?>
        <tr>
            <?php if ($idx == 0) { ?>
                <td rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                <td rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
            <?php } ?>
            <td><?php echo htmlspecialchars($row_data['dvc_code']); ?></td>
            <td><?php echo htmlspecialchars($row_data['warna']); ?></td>
            <?php foreach ($sizes as $sz) { ?>
                <td><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
            <?php } ?>
            <td><strong><?php echo $subtotal; ?></strong></td>
            <td><?php echo $percentage; ?>%</td>
        </tr>
        <?php
                }
                $row_display_no++;
            }
            // --- Barang lain (non-VOH), agregasi per nama/kode ---
            if (!empty($model_data)) {
                $other_items_agg = array();
                foreach ($model_data as $item) {
                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                    if (!isset($other_items_agg[$key])) {
                        $other_items_agg[$key] = $item;
                    } else {
                        foreach ($sizes as $sz) {
                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                        }
                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                    }
                }
                foreach ($other_items_agg as $item) {
                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                    $percentage = $grand_total > 0 ? round(($subtotal / $grand_total) * 100, 1) : 0;
        ?>
        <tr>
            <td><?php echo $row_display_no++; ?></td>
            <td><?php echo htmlspecialchars($item['dvc_name']); ?></td>
            <td><?php echo htmlspecialchars($item['dvc_code']); ?></td>
            <td><?php echo htmlspecialchars($item['warna']); ?></td>
            <?php foreach ($sizes as $sz) { ?>
                <td><?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?></td>
            <?php } ?>
            <td><strong><?php echo $subtotal; ?></strong></td>
            <td><?php echo $percentage; ?>%</td>
        </tr>
        <?php
                }
            }
            if ($row_display_no == 1) {
        ?>
        <tr>
            <td colspan="15"><i>No ECBS APP Data Found</i></td>
        </tr>
        <?php } ?>
        <?php
        } else {
            // --- ECCT MODE ---
            $no = 0;
            if(isset($model_data) && !empty($model_data)) {
                foreach ($model_data as $row) {
                    $no++;
                    $percentage = $grand_total > 0 ? round(($row['subtotal'] / $grand_total) * 100, 1) : 0;
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row['dvc_name']; ?></td>
            <td><?php echo $row['dvc_code']; ?></td>
            <?php foreach ($sizes as $sz) { ?>
                <td><?php echo $row[$sz]; ?></td>
            <?php } ?>
            <td><strong><?php echo $row['subtotal']; ?></strong></td>
            <td><?php echo $percentage; ?>%</td>
        </tr>
        <?php
                }
            } else {
        ?>
        <tr>
            <td colspan="14"><i>No ECCT APP Data Found</i></td>
        </tr>
        <?php } ?>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
            <td colspan="<?php echo $is_ecbs ? 4 : 3; ?>">TOTAL</td>
            <td><?php echo $column_totals['size_xs']; ?></td>
            <td><?php echo $column_totals['size_s']; ?></td>
            <td><?php echo $column_totals['size_m']; ?></td>
            <td><?php echo $column_totals['size_l']; ?></td>
            <td><?php echo $column_totals['size_xl']; ?></td>
            <td><?php echo $column_totals['size_xxl']; ?></td>
            <td><?php echo $column_totals['size_3xl']; ?></td>
            <td><?php echo $column_totals['size_all']; ?></td>
            <td><?php echo $column_totals['size_cus']; ?></td>
            <td rowspan="2"><strong><?php echo $grand_total; ?></strong></td>
            <td rowspan="2"><strong>100%</strong></td>
        </tr>
        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
            <td colspan="<?php echo $is_ecbs ? 4 : 3; ?>">PERSENTASE</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_xs'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_s'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_m'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_l'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_xxl'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_3xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_all'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td><?php echo $grand_total > 0 ? round(($column_totals['size_cus'] / $grand_total) * 100, 1) : 0; ?>%</td>
            <td colspan="2">-</td>
        </tr>
    </tfoot>
</table> 