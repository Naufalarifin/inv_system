<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="osc_sync_differences_' . date('Y-m-d_H-i-s') . '.xls"');
?>

<h2>Data OSC yang Tidak Sinkron</h2>
<p>Tanggal: <?php echo date('Y-m-d H:i:s'); ?></p>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr style="background-color: #FFB6C1;">
            <th>NO</th>
            <th>KODE OSC</th>
            <th>NAMA OSC</th>
            <th>TECH</th>
            <th>INVENTORY TOTAL</th>
            <th>KALIBRASI COUNT</th>
            <th>PERBEDAAN</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($differences)): ?>
            <?php $no = 1; ?>
            <?php foreach ($differences as $item): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $item['dvc_code']; ?></td>
                    <td><?php echo $item['dvc_name']; ?></td>
                    <td><?php echo strtoupper($item['dvc_tech']); ?></td>
                    <td><?php echo $item['inventory_total']; ?></td>
                    <td><?php echo $item['kalibrasi_count']; ?></td>
                    <td><?php echo $item['inventory_total'] - $item['kalibrasi_count']; ?></td>
                    <td>
                        <?php 
                        $diff = $item['inventory_total'] - $item['kalibrasi_count'];
                        if ($diff > 0) {
                            echo "Inventory lebih banyak (" . $diff . " item)";
                        } else {
                            echo "Kalibrasi lebih banyak (" . abs($diff) . " item)";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8"><i>Semua data OSC sudah sinkron!</i></td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #e0e0e0;">
            <td colspan="4"><strong>TOTAL PERBEDAAN</strong></td>
            <td><strong><?php echo array_sum(array_column($differences, 'inventory_total')); ?></strong></td>
            <td><strong><?php echo array_sum(array_column($differences, 'kalibrasi_count')); ?></strong></td>
            <td><strong><?php echo array_sum(array_column($differences, 'inventory_total')) - array_sum(array_column($differences, 'kalibrasi_count')); ?></strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<br/>

<h3>Rekomendasi Perbaikan:</h3>
<ol>
    <li><strong>Periksa Data Inventory:</strong> Pastikan semua item OSC sudah tercatat dengan benar di sistem inventory</li>
    <li><strong>Periksa Data Kalibrasi:</strong> Pastikan semua item OSC sudah tercatat dengan benar di sistem kalibrasi</li>
    <li><strong>Periksa Status Item:</strong> Pastikan status item (LN/DN) sudah sesuai dengan kondisi fisik</li>
    <li><strong>Periksa Proses Input:</strong> Pastikan tidak ada duplikasi atau kesalahan input data</li>
    <li><strong>Sinkronisasi Manual:</strong> Jika diperlukan, lakukan sinkronisasi manual untuk item yang tidak sinkron</li>
</ol> 