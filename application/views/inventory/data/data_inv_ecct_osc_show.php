<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="250">Nama Barang</th>
                    <th align="center" width="120">Kode</th>
                    <th align="center" width="80">Jumlah*</th>
                    <th align="center" width="80">Tech</th>
                    <th align="center" width="80">Type</th>
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
                    <td align="center"><?php echo $no + ($data['page']['show'] * (isset($_GET['p']) ? (int)$_GET['p'] : 0)); ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="center"><?php echo $row['dvc_code']; ?></td>
                    <td align="center"><strong><?php echo $row['total_count']; ?></strong></td>
                    <td align="center"><?php echo $row['dvc_tech']; ?></td>
                    <td align="center"><?php echo $row['dvc_type']; ?></td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="6"><i>No ECCT OSC Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($data['page']['sum'] > $data['page']['show']) { ?>
<div class="card-footer justify-center">
    <div class="pagination">
        <?php
        $total_pages = ceil($data['page']['sum'] / $data['page']['show']);
        $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
        
        // Previous button
        if ($current_page > 0) {
            echo '<a href="javascript:showDataEcct(' . ($current_page - 1) . ')" class="btn btn-sm btn-light">Previous</a>';
        }
        
        // Page numbers
        $start_page = max(0, $current_page - 2);
        $end_page = min($total_pages - 1, $current_page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="btn btn-sm btn-primary">' . ($i + 1) . '</span>';
            } else {
                echo '<a href="javascript:showDataEcct(' . $i . ')" class="btn btn-sm btn-light">' . ($i + 1) . '</a>';
            }
        }
        
        // Next button
        if ($current_page < $total_pages - 1) {
            echo '<a href="javascript:showDataEcct(' . ($current_page + 1) . ')" class="btn btn-sm btn-light">Next</a>';
        }
        ?>
    </div>
</div>
<?php } ?>

<style>
.card-table {
    max-width: 900px;
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
    min-width: 700px;
    width: 100%;
    white-space: nowrap;
}

.table-responsive td, .table-responsive th {
    padding: 8px 6px;
    font-size: 12px;
    text-align: center;
}
</style>
