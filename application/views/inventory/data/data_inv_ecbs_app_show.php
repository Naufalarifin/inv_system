<?php
$no = 0;
$grand_total = 0;
$column_totals = [
    'size_xs' => 0,
    'size_s' => 0,
    'size_m' => 0,
    'size_l' => 0,
    'size_xl' => 0,
    'size_xxl' => 0,
    'size_3xl' => 0,
    'size_all' => 0,
    'size_cus' => 0,
];

// Tambahkan: urutkan agar dvc_code 'VOH' di atas
if(isset($data['data']) && !empty($data['data'])) {
    usort($data['data'], function($a, $b) {
        $a_is_voh = (strpos($a['dvc_code'], 'VOH') === 0) ? 1 : 0;
        $b_is_voh = (strpos($b['dvc_code'], 'VOH') === 0) ? 1 : 0;
        if ($a_is_voh && !$b_is_voh) return -1;
        if (!$a_is_voh && $b_is_voh) return 1;
        return 0;
    });
    foreach ($data['data'] as $row) {
        $grand_total += $row['subtotal'];
        foreach ($column_totals as $key => $value) {
            if (isset($row[$key])) {
                $column_totals[$key] += $row[$key];
            }
        }
    }
}
?>

<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="60">Nama Barang</th>
                    <th align="center" width="100">Kode</th>
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
                if(isset($data['data']) && !empty($data['data'])) {
                    foreach ($data['data'] as $row) {
                        $no++;
                        $percentage = $grand_total > 0 ? round(($row['subtotal'] / $grand_total) * 100, 1) : 0;
                ?>
                <tr>
                    <td align="center"><?php echo $no; ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="left">
                        <?php echo $row['dvc_code']; ?>
                        <?php if(isset($row['warna']) && $row['warna'] && $row['warna'] != '-') { ?>
                            <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($row['warna']); ?>;border-radius:3px;margin-left:6px;vertical-align:middle;border:1px solid #ccc;"></span>
                        <?php } ?>
                    </td>
                    <td align="center"><?php echo $row['size_xs']; ?></td>
                    <td align="center"><?php echo $row['size_s']; ?></td>
                    <td align="center"><?php echo $row['size_m']; ?></td>
                    <td align="center"><?php echo $row['size_l']; ?></td>
                    <td align="center"><?php echo $row['size_xl']; ?></td>
                    <td align="center"><?php echo $row['size_xxl']; ?></td>
                    <td align="center"><?php echo $row['size_3xl']; ?></td>
                    <td align="center"><?php echo $row['size_all']; ?></td>
                    <td align="center"><?php echo $row['size_cus']; ?></td>
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
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="3">TOTAL</td>
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
                    <td align="center" colspan="3">PERSENTASE</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xs'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_s'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_m'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_l'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_xxl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_3xl'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_all'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <td align="center"><?php echo $grand_total > 0 ? round(($column_totals['size_cus'] / $grand_total) * 100, 1) : 0; ?>%</td>
                    <!-- Sel Subtotal dan Persentase dihapus dari baris ini karena sudah di-rowspan dari baris atas -->
                </tr>
            </tfoot>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
    </div>
</div>
