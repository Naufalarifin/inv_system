<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 compact-table">
            <thead>
                <tr>
                    <th align="center" >No</th>
                    <th align="center" >Serial Number</th>
                    <th align="center" >Device Info</th>
                    <th align="center" >Size</th>
                    <?php 
                    // Tampilkan kolom warna hanya untuk ECBS
                    $showColorColumn = (isset($data['tech']) && $data['tech'] === 'ecbs');
                    
                    if ($showColorColumn) { ?>
                        <th align="center">Warna</th>
                    <?php } ?>
                    <th align="center" >QC Status</th>
                    <th align="center" >Act Date</th>
                    <th align="center" >In</th>
                    <th align="center" >Move</th>
                    <th align="center" >Out</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = isset($data['page']['first']) ? $data['page']['first'] : 0;
                if($data['query'] && $data['query']->num_rows() > 0) {
                    foreach ($data['query']->result_array() as $key => $row) {
                        $no++;
                        // Check if this device has color info (6th character of serial number is 'S')
                        $hasColor = (strlen($row['dvc_sn']) >= 6 && strtoupper($row['dvc_sn'][5]) === 'S');
                        // Hanya tampilkan warna jika jenis ECBS dan device memiliki info warna
                        $displayColor = $showColorColumn && $hasColor;
                ?>
                <tr>
                    <td align="center"><?php echo $no; ?></td>
                    <td align="center"><?php echo $row['dvc_sn']; ?></td>
                    <td align="center">
                        <span class="hover-info" data-info="<?php echo htmlspecialchars($row['dvc_name']); ?>">
                            <?php echo $row['dvc_code']; ?>
                        </span>
                    </td>
                    <td align="center">
                        <?php if (!empty($row['dvc_size'])) { ?>
                            <?php if ($row['dvc_size'] == '-') { ?>
                                <span> </span>
                            <?php } else { ?>
                                <span class="badge badge-xs badge-light badge-outline"><?php echo $row['dvc_size']; ?></span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <?php if ($showColorColumn) { ?>
                    <td align="left" style="padding: 2px 4px;">
                        <?php if ($displayColor && isset($row['warna'])) { ?>
                            <?php
                            $color_map = array(
                                'Black' => '#000000',
                                'Navy' => '#001f5b',
                                'Army' => '#4b5320',
                                'Maroon' => '#800000',
                                'Dark Gray' => '#A9A9A9',
                                'Grey' => '#808080',
                                'Custom' => '#ffffff',
                                'none' => '#fff',
                                '-' => '#fff'
                            );
                            $warna = trim($row['warna']);
                            if (strtolower($warna) === 'custom') {
                                echo '<span style="font-size:10px;">CUSTOM</span>';
                            } elseif (!empty($warna) && $warna != '-') {
                                $warna_css = isset($color_map[$warna]) ? $color_map[$warna] : '#fff';
                                echo '<div style="display:flex;align-items:center;gap:4px;">';
                                echo '<span style="display:inline-block;width:14px;height:14px;background:'.$warna_css.';border-radius:2px;border:1px solid #ccc;flex-shrink:0;"></span>';
                                echo '<span style="font-size:10px;white-space:nowrap;">'.htmlspecialchars($row['warna']).'</span>';
                                echo '</div>';
                            }
                            ?>
                        <?php } ?>
                    </td>
                    <?php } ?>
                    <td align="center">
                        <?php if ($row['dvc_qc'] == 'LN') { ?>
                            <span class="badge badge-xs badge-success badge-outline">LN</span>
                        <?php } ?>
                    </td>
                    <td align="center"> 
                        <?php if ($row['act_date']) { ?>
                        <span class="hover-info" data-info="Admin: <?php echo htmlspecialchars($row['adm_act']); ?>">
                            <?php echo date("d/m/y H:i", strtotime($row['act_date'])); ?>
                        </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_in']) { ?>
                            <span class="hover-info" data-info="Admin: <?php echo htmlspecialchars($row['adm_in']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_in'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_move']) { ?>
                            <span class="hover-info" data-info="Admin: <?php echo htmlspecialchars($row['adm_move']); ?>, Lokasi: <?php echo htmlspecialchars($row['loc_move']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_move'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                    <td align="center">
                        <?php if ($row['inv_out']) { ?>
                            <span class="hover-info" data-info="Admin: <?php echo htmlspecialchars($row['adm_out']); ?>">
                                <?php echo date("d/m/y H:i", strtotime($row['inv_out'])); ?>
                            </span>
                        <?php } else { echo '-'; } ?>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="<?php echo $showColorColumn ? '9' : '8'; ?>"><i>No Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.compact-table {
    font-size: 13px !important;
}
.compact-table th,
.compact-table td {
    padding: 0px 4px !important;
    line-height: 1.8 !important;
}
.compact-table th {
    font-size: 14px !important;
}

/* Simple Hover Info */
.hover-info {
    position: relative;
    cursor: pointer;
}

.hover-info::after {
    content: attr(data-info);
    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    z-index: 999;
}

.hover-info:hover::after {
    opacity: 1;
}
</style>