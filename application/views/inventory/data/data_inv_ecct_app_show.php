<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="200">Nama Barang</th>
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
                    <td align="center"><?php echo $no; ?></td>
                    <td align="left"><?php echo $row['dvc_name']; ?></td>
                    <td align="center"><?php echo $row['dvc_code']; ?></td>
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
                    <td align="center" colspan="15"><i>No ECCT APP Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div style="padding: 10px; font-size: 11px; color: #666;">
            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)
        </div>
    </div>
</div>
