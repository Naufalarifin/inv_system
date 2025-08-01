<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="osc_sync_report_' . date('Y-m-d_H-i-s') . '.xls"');
?>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr style="background-color: #f0f0f0;">
            <th>NO</th>
            <th>KODE OSC</th>
            <th>NAMA OSC</th>
            <th>TECH</th>
            <th>INVENTORY LN</th>
            <th>INVENTORY DN</th>
            <th>TOTAL INVENTORY</th>
            <th>KALIBRASI COUNT</th>
            <th>STATUS SYNC</th>
            <th>PERBEDAAN</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data)): ?>
            <?php $no = 1; ?>
            <?php foreach ($data as $item): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $item['dvc_code']; ?></td>
                    <td><?php echo $item['dvc_name']; ?></td>
                    <td><?php echo strtoupper($item['dvc_tech']); ?></td>
                    <td><?php echo $item['ln_count']; ?></td>
                    <td><?php echo $item['dn_count']; ?></td>
                    <td><strong><?php echo $item['inventory_total']; ?></strong></td>
                    <td><?php echo $item['kalibrasi_count']; ?></td>
                    <td style="background-color: <?php echo $item['sync_status'] === 'SYNC' ? '#90EE90' : '#FFB6C1'; ?>">
                        <?php echo $item['sync_status']; ?>
                    </td>
                    <td><?php echo $item['inventory_total'] - $item['kalibrasi_count']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10"><i>No Data Found</i></td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #e0e0e0;">
            <td colspan="6"><strong>TOTAL</strong></td>
            <td><strong><?php echo array_sum(array_column($data, 'inventory_total')); ?></strong></td>
            <td><strong><?php echo array_sum(array_column($data, 'kalibrasi_count')); ?></strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<br/>

<h3>Ringkasan Sinkronisasi:</h3>
<table border="1" style="border-collapse: collapse; width: 50%;">
    <tr>
        <td><strong>Total Item OSC:</strong></td>
        <td><?php echo count($data); ?></td>
    </tr>
    <tr>
        <td><strong>Item Sinkron:</strong></td>
        <td><?php echo count(array_filter($data, function($item) { return $item['sync_status'] === 'SYNC'; })); ?></td>
    </tr>
    <tr>
        <td><strong>Item Tidak Sinkron:</strong></td>
        <td><?php echo count(array_filter($data, function($item) { return $item['sync_status'] === 'NOT_SYNC'; })); ?></td>
    </tr>
    <tr>
        <td><strong>Persentase Sinkronisasi:</strong></td>
        <td><?php echo count($data) > 0 ? round((count(array_filter($data, function($item) { return $item['sync_status'] === 'SYNC'; })) / count($data)) * 100, 2) : 0; ?>%</td>
    </tr>
</table> 