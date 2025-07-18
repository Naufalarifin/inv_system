<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 text-xs">
            <thead>
                <tr class="text-xs">
                    <th align="center" width="30">No</th>
                    <th align="center" width="100">Device Info</th>
                    <th align="center" width="70">Size</th>
                    <th align="center" width="70">Warna</th>
                    <th align="center" width="80">Serial Number</th>
                    <th align="center" width="60">QC Status</th>
                    <th align="center" width="80">In</th>
                    <th align="center" width="80">Out</th>
                    <th align="center" width="80">Move</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = isset($data['page']['first']) ? $data['page']['first'] : 0;
                if($data['query'] && $data['query']->num_rows() > 0) {
                    foreach ($data['query']->result_array() as $key => $row) {
                        // Tampilkan hanya jika karakter ke-6 dvc_sn adalah 'S'
                        if (strlen($row['dvc_sn']) < 6 || strtoupper($row['dvc_sn'][5]) !== 'S') continue;
                        $no++;
                ?>
                <tr>
                    <td align="center" class="text-xs"><?php echo $no; ?></td>
                    <td align="center" class="text-xs">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo htmlspecialchars($row['dvc_name']); ?>">
                            <?php echo $row['dvc_code']; ?>
                        </span>
                    </td>
                    <td align="center">
                        <div class="text-xs">
                            <?php if (!empty($row['dvc_size'])) { ?>
                                <?php if ($row['dvc_size'] == 'small') { ?>
                                    <span class="badge badge-xs badge-success badge-outline">S</span>
                                <?php } elseif ($row['dvc_size'] == 'medium') { ?>
                                    <span class="badge badge-xs badge-warning badge-outline">M</span>
                                <?php } elseif ($row['dvc_size'] == 'large') { ?>
                                    <span class="badge badge-xs badge-danger badge-outline">L</span>
                                <?php } else { ?>
                                    <span class="badge badge-xs badge-light badge-outline"><?php echo $row['dvc_size']; ?></span>
                                <?php } ?>
                            <?php } ?>

                        </div>
                    </td>
                    <td align="left">
                            <?php
                        $color_map = [
                            'Dark Gray' => '#555555',
                            'dark grey' => '#555555',
                            'Black' => '#000000',
                            'Grey' => '#888888',
                            'Blue Navy' => '#001f5b',
                            'Navy' => '#001f5b',
                            'Army' => '#4b5320',
                            'Maroon' => '#800000',
                            'Custom' => '#ffffff',
                            'none' => '#fff',
                            '-' => '#fff'
                        ];
                        ?>
                        <?php
                        $warna = trim($row['warna']);
                        if (strtolower($warna) === 'custom') {
                            echo '<span style="font-size:12px;">CUSTOM</span>';
                        } elseif (!empty($warna) && $warna != '-') {
                            $warna_css = isset($color_map[$warna]) ? $color_map[$warna] : '#fff';
                            echo '<span style="display:inline-block;width:16px;height:16px;background:'.$warna_css.';border-radius:3px;vertical-align:middle;border:1px solid #ccc;"></span>';
                            echo '<span class="text-xs" style="margin-left:4px;display:inline-block;text-align:left;min-width:50px;vertical-align:middle;">'.htmlspecialchars($row['warna']).'</span>';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td align="center" class="text-xs"><?php echo $row['dvc_sn']; ?></td>
                    <td align="center">
                        <?php if ($row['dvc_qc'] == '0') { ?>
                            <span class="badge badge-xs badge-success badge-outline">LN</span>
                        <?php } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_in']) { ?>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Admin: <?php echo htmlspecialchars($row['adm_in']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_in'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_out']) { ?>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Admin: <?php echo htmlspecialchars($row['adm_out']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_out'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_move']) { ?>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Admin: <?php echo htmlspecialchars($row['adm_move']); ?>, Lokasi: <?php echo htmlspecialchars($row['loc_move']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_move'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="8" class="text-xs"><i>No Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
