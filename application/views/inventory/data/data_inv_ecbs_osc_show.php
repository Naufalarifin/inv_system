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
                    <td align="left"><?php echo $no; ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="left"><?php echo $row['dvc_code']; ?></td>
                    <td align="left"><strong><?php echo $row['total_count']; ?></strong></td>
                    <td align="left"><?php echo $row['dvc_tech']; ?></td>
                    <td align="left"><?php echo $row['dvc_type']; ?></td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="6"><i>No ECBS OSC Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
    </div>
</div>
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