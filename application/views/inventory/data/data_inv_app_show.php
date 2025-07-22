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


// Daftar warna untuk 7 baris VOH (tetap hardcoded untuk tampilan)
$voh_colors = array(
    array('name' => 'Black', 'hex' => '#000000'),
    array('name' => 'Navy', 'hex' => '#001f5b'),
    array('name' => 'Maroon', 'hex' => '#800000'),
    array('name' => 'Army', 'hex' => '#4b5320'),
    array('name' => 'Dark Gray', 'hex' => '#A9A9A9'), // hex #A9A9A9 untuk dark gray
    array('name' => 'Gray', 'hex' => '#808080'),
    array('name' => 'Custom', 'hex' => '#ffffff'),
);

$sizes = array('size_xs','size_s','size_m','size_l','size_xl','size_xxl','size_3xl','size_all','size_cus');
$model_data = isset($data['data']) && is_array($data['data']) ? $data['data'] : array();

// Hitung grand_total dan column_totals
foreach ($model_data as $item) {
    $grand_total += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
    foreach ($sizes as $size_key) {
        $column_totals[$size_key] += isset($item[$size_key]) ? (int)$item[$size_key] : 0;
    }
}

// Cek apakah data ECBS (ada field warna)
$is_ecbs = isset($model_data[0]['warna']);

// Daftar warna untuk 7 baris VOH (khusus ECBS)
$voh_colors = array(
    array('name' => 'Navy', 'hex' => '#001f5b'),
    array('name' => 'Maroon', 'hex' => '#800000'),
    array('name' => 'Army', 'hex' => '#4b5320'),
    array('name' => 'Black', 'hex' => '#000000'),
    array('name' => 'Grey', 'hex' => '#808080'),
    array('name' => 'Blue Navy', 'hex' => '#000080'),
    array('name' => 'Custom', 'hex' => '#ffffff'),
);
?>
<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="60">Nama Barang</th>
                    <th align="center" width="100">Kode</th>
                    <?php if($is_ecbs): ?><th align="center" width="60">Warna</th><?php endif; ?>
                    <th align="center" width="60">XS</th>
                    <th align="center" width="60">S</th>
                    <th align="center" width="60">M</th>
                    <th align="center" width="60">L</th>
                    <th align="center" width="60">XL</th>
                    <th align="center" width="60">XXL</th>
                    <th align="center" width="60">3XL</th>
                    <th align="center" width="60">ALL</th>
                    <th align="center" width="60">CUS</th>
                    <th align="center" width="80">Subtotal*</th>
                    <th align="center" width="60">%</th>
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
                            $color_info = $item['color_info'];
                            $row_data = $item['data'];
                            $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                ?>
                <tr>
                    <?php if ($idx == 0) { ?>
                        <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                        <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                    <?php } ?>
                    <td align="left"><?php echo htmlspecialchars($row_data['dvc_code']); ?></td>
                    <td align="left">
                        <?php echo htmlspecialchars($row_data['warna']); ?><br>
                        <?php if (strtolower($color_info['name']) === 'custom') { ?>
                            <span style="font-size:12px;font-weight:bold;">CUSTOM</span>
                        <?php } else { ?>
                            <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;margin-top:4px;vertical-align:middle;border:1px solid #ccc;"></span>
                        <?php } ?>
                    </td>
                    <?php foreach ($sizes as $sz) { ?>
                        <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                    <?php } ?>
                    <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($subtotal / $grand_total) * 100, 1) : 0; ?>%</td>
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
                    <td align="center"><?php echo $row_display_no++; ?></td>
                    <td align="left"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                    <td align="left"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                    <td align="left"><?php echo htmlspecialchars($item['warna']); ?></td>
                    <?php foreach ($sizes as $sz) { ?>
                        <td align="center">
                            <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; 
                            ?>
                            <br>
                        </td>
                    <?php } ?>
                    <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                    <td align="center"><?php echo $percentage; ?>%</td>
                </tr>
                <?php
                        }
                    }
                    if ($row_display_no == 1) {
                ?>
                <tr>
                    <td align="center" colspan="15"><i>No ECBS APP Data Found</i></td>
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
                    <td align="center"><?php echo $no; ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="center"><?php echo $row['dvc_code']; ?></td>
                    <?php foreach ($sizes as $sz) { ?>
                        <td align="center"><?php echo $row[$sz]; ?></td>
                    <?php } ?>
                    <td align="center"><strong><?php echo $row['subtotal']; ?></strong></td>
                    <td align="center"><?php echo $percentage; ?>%</td>
                </tr>
                <?php
                        }
                    } else {
                ?>
                <tr>
                    <td align="center" colspan="14"><i>No ECCT APP Data Found</i></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="<?php echo $is_ecbs ? 4 : 3; ?>">TOTAL</td>
                    <td align="center"><?php echo $column_totals['size_xs']; ?></td>
                    <td align="center"><?php echo $column_totals['size_s']; ?></td>
                    <td align="center"><?php echo $column_totals['size_m']; ?></td>
                    <td align="center"><?php echo $column_totals['size_l']; ?></td>
                    <td align="center"><?php echo $column_totals['size_xl']; ?></td>
                    <td align="center"><?php echo $column_totals['size_xxl']; ?></td>
                    <td align="center"><?php echo $column_totals['size_3xl']; ?></td>
                    <td align="center"><?php echo $column_totals['size_all']; ?></td>
                    <td align="center"><?php echo $column_totals['size_cus']; ?></td>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total; ?></strong></td>
                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                </tr>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="<?php echo $is_ecbs ? 4 : 3; ?>">PERSENTASE</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xs'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_s'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_m'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_l'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xxl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_3xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_all'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_cus'] / $grand_total) * 100, 1) : 0; ?>%</td>
                </tr>
            </tfoot>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
    </div>
</div>
<style>
.table.table-border.text-xs td, .table.table-border.text-xs th {
    font-size: 10px !important;
    padding: 4px 6px !important;
}
.table.table-border tfoot tr td {
    font-size: 12px !important; 
    padding: 6px 8px !important;
}
</style> 