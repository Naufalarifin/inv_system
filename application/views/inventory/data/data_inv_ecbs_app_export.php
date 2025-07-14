<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ecbs_app_export_' . date('Y-m-d_H-i-s') . '.xls"');
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Tipe</th>
            <th>Kode</th>
            <th>Warna</th>
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
        $no = 0;
        $grand_total = 0;
        if(isset($data['data']) && !empty($data['data'])) {
            foreach ($data['data'] as $row) {
                $grand_total += $row['subtotal'];
            }
        }
        if(isset($data['data']) && !empty($data['data'])) {
            foreach ($data['data'] as $row) {
                $no++;
                $percentage = $grand_total > 0 ? round(($row['subtotal'] / $grand_total) * 100, 1) : 0;
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row['dvc_name']; ?></td>
            <td>-</td>
            <td><?php echo $row['dvc_code']; ?></td>
            <td><?php echo isset($row['warna']) ? $row['warna'] : '-'; ?></td>
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
        <?php 
            }
        } else {
        ?>
        <tr>
            <td colspan="16">No ECBS APP Data Found</td>
        </tr>
        <?php } ?>
    </tbody>
</table> 