<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="200">Nama Barang</th>
                    <th align="center" width="100">Kode</th>
                    <th align="center" width="100">Jumlah*</th>
                    <th align="center" width="100">Tech</th>
                    <th align="center" width="100">Type</th>
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
                    <td align="center"><?php echo $no + $data['page']['first']; ?></td>
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

