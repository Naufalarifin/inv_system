<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ecct_osc_export_' . date('Y-m-d_H-i-s') . '.xls"');
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kode</th>
            <th>Jumlah</th>
            <th>Tech</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 0;
        if($data['query'] && $data['query']->num_rows() > 0) {
            foreach ($data['query']->result_array() as $row) {
                $no++;
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $row['dvc_name']; ?></td>
            <td><?php echo $row['dvc_code']; ?></td>
            <td><?php echo $row['total_count']; ?></td>
            <td><?php echo $row['dvc_tech']; ?></td>
            <td><?php echo $row['dvc_type']; ?></td>
        </tr>
        <?php 
            }
        } else {
        ?>
        <tr>
            <td colspan="6">No ECCT OSC Data Found</td>
        </tr>
        <?php } ?>
    </tbody>
</table> 