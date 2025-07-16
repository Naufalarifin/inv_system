<?php
// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ECCT_APP_Export_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Calculate totals
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

if(isset($data['data']) && !empty($data['data'])) {
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

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ECCT APP Export</title>
</head>
<body>
    <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kode</th>
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
            <?php if(isset($data['data']) && !empty($data['data'])): ?>
                <?php foreach ($data['data'] as $row): ?>
                    <?php 
                    $no++;
                    $percentage = $grand_total > 0 ? round(($row['subtotal'] / $grand_total) * 100, 1) : 0;
                    ?>
                    <tr>
                        <td><?php echo $no; ?></td>
                        <td><?php echo $row['dvc_name']; ?></td>
                        <td><?php echo $row['dvc_code']; ?></td>
                        <td><?php echo $row['size_xs']; ?></td>
                        <td><?php echo $row['size_s']; ?></td>
                        <td><?php echo $row['size_m']; ?></td>
                        <td><?php echo $row['size_l']; ?></td>
                        <td><?php echo $row['size_xl']; ?></td>
                        <td><?php echo $row['size_xxl']; ?></td>
                        <td><?php echo $row['size_3xl']; ?></td>
                        <td><?php echo $row['size_all']; ?></td>
                        <td><?php echo $row['size_cus']; ?></td>
                        <td><?php echo $row['subtotal']; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14">No ECCT APP Data Found</td>
                </tr>
            <?php endif; ?>
            
            <!-- Total Row -->
            <tr style="background-color: #e6f3ff; font-weight: bold;">
                <td colspan="3">TOTAL</td>
                <td><?php echo $column_totals['size_xs']; ?></td>
                <td><?php echo $column_totals['size_s']; ?></td>
                <td><?php echo $column_totals['size_m']; ?></td>
                <td><?php echo $column_totals['size_l']; ?></td>
                <td><?php echo $column_totals['size_xl']; ?></td>
                <td><?php echo $column_totals['size_xxl']; ?></td>
                <td><?php echo $column_totals['size_3xl']; ?></td>
                <td><?php echo $column_totals['size_all']; ?></td>
                <td><?php echo $column_totals['size_cus']; ?></td>
                <td><?php echo $grand_total; ?></td>
                <td>100%</td>
            </tr>
            
            <!-- Percentage Row -->
            <tr style="background-color: #fff2cc; font-weight: bold;">
                <td colspan="3">PERSENTASE</td>
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
        </tbody>
    </table>
</body>
</html>