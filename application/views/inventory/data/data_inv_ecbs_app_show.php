<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="200">Nama Barang</th>
                    <th align="center" width="100">Kode</th>
                    <th align="center" width="80">Warna</th>
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
                $no = 0;
                $grand_total = 0;
                // Calculate grand total first
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
                    <td align="center"><?php echo $no + ($data['page']['show'] * (isset($_GET['p']) ? (int)$_GET['p'] : 0)); ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="center"><?php echo $row['dvc_code']; ?></td>
                    <td align="center"><?php echo isset($row['warna']) ? $row['warna'] : '-'; ?></td>
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
                    <td align="center" colspan="16"><i>No ECBS APP Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
        <?php if(isset(
            $data['data']) && !empty($data['data'])): ?>
            <div style="margin:10px 0;">
                <?php foreach($data['data'] as $row): ?>
                    <?php if(isset($row['warna']) && $row['warna'] != '-' && $row['warna'] != ''): ?>
                        <span class="badge badge-sm" style="background:<?php echo $row['warna']; ?>;color:#fff;margin-right:5px;">
                            <?php echo $row['dvc_name']; ?>: <?php echo $row['warna']; ?>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Pagination -->
<?php if (isset($data['page']) && $data['page']['sum'] > $data['page']['show']) { ?>
<div class="card-footer justify-center">
    <div class="pagination">
        <?php
        $total_pages = ceil($data['page']['sum'] / $data['page']['show']);
        $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
        // Previous button
        if ($current_page > 0) {
            echo '<a href="javascript:showDataEcbs(' . ($current_page - 1) . ')" class="btn btn-sm btn-light">Previous</a>';
        }
        // Page numbers
        $start_page = max(0, $current_page - 2);
        $end_page = min($total_pages - 1, $current_page + 2);
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="btn btn-sm btn-primary">' . ($i + 1) . '</span>';
            } else {
                echo '<a href="javascript:showDataEcbs(' . $i . ')" class="btn btn-sm btn-light">' . ($i + 1) . '</a>';
            }
        }
        // Next button
        if ($current_page < $total_pages - 1) {
            echo '<a href="javascript:showDataEcbs(' . ($current_page + 1) . ')" class="btn btn-sm btn-light">Next</a>';
        }
        ?>
    </div>
</div>
<?php } ?>
<style>
.card-table {
    max-width: 1200px;
    margin: 0 auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 16px 8px;
}
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}
.table-responsive table {
    min-width: 1100px;
    width: 100%;
    white-space: nowrap;
}
.table-responsive td, .table-responsive th {
    padding: 8px 6px;
    font-size: 12px;
    text-align: center;
}
</style> 