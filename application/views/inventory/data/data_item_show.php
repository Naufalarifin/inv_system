<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="70">Action ID</th>
                    <th align="center" width="120">Device Info</th>
                    <th align="center" width="90">Size/Color</th>
                    <th align="center" width="100">Serial Number</th>
                    <th align="center" width="80">QC Status</th>
                    <th align="center" width="150">Inventory Timeline</th>
                    <th align="center" width="120">Admin Timeline</th>
                    <th align="center" width="80">Location Move</th>
                    <th align="center" width="80">Aksi</th>
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
                    <td align="center"><?php echo $row['id_act']; ?></td>
                    <td class="device-info">
                        <div class="device-name">Device <?php echo $row['id_dvc']; ?></div>
                        <div class="device-id">DVC<?php echo str_pad($row['id_dvc'], 3, '0', STR_PAD_LEFT); ?></div>
                    </td>
                    <td align="center">
                        <div class="size-color-container">
                            <div>
                                <?php if ($row['dvc_size'] == 'small') { ?>
                                    <span class="badge badge-sm badge-success badge-outline">S</span>
                                <?php } elseif ($row['dvc_size'] == 'medium') { ?>
                                    <span class="badge badge-sm badge-warning badge-outline">M</span>
                                <?php } elseif ($row['dvc_size'] == 'large') { ?>
                                    <span class="badge badge-sm badge-danger badge-outline">L</span>
                                <?php } else { ?>
                                    <span class="badge badge-sm badge-light badge-outline"><?php echo $row['dvc_size']; ?></span>
                                <?php } ?>
                            </div>
                            <div>
                                <span class="badge badge-sm badge-light badge-outline" style="background-color: <?php echo $row['dvc_col']; ?>; color: <?php echo ($row['dvc_col'] == 'white' || $row['dvc_col'] == 'yellow') ? 'black' : 'white'; ?>;">
                                    <?php echo substr(ucfirst($row['dvc_col']), 0, 3); ?>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td align="center"><?php echo $row['dvc_sn']; ?></td>
                    <td align="center">
                        <?php if ($row['dvc_qc'] == '1') { ?>
                            <span class="badge badge-sm badge-success badge-outline">Pass</span>
                        <?php } elseif ($row['dvc_qc'] == '2') { ?>
                            <span class="badge badge-sm badge-danger badge-outline">Fail</span>
                        <?php } else { ?>
                            <span class="badge badge-sm badge-warning badge-outline">Pend</span>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="timeline-container">
                            <?php if ($row['inv_in']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">In:</span> <?php echo date("d/m/y H:i", strtotime($row['inv_in'])); ?>
                                </div>
                            <?php } ?>
                            <?php if ($row['inv_move']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Move:</span> <?php echo date("d/m/y H:i", strtotime($row['inv_move'])); ?>
                                </div>
                            <?php } ?>
                            <?php if ($row['inv_out']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Out:</span> <?php echo date("d/m/y H:i", strtotime($row['inv_out'])); ?>
                                </div>
                            <?php } ?>
                            <?php if ($row['inv_rls']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Release:</span> <?php echo date("d/m/y H:i", strtotime($row['inv_rls'])); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </td>
                    <td>
                        <div class="timeline-container">
                            <?php if (isset($row['adm_in']) && $row['adm_in']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">In:</span> <?php echo (int) $row['adm_in']; ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($row['adm_move']) && $row['adm_move']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Move:</span> <?php echo (int) $row['adm_move']; ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($row['adm_out']) && $row['adm_out']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Out:</span> <?php echo (int) $row['adm_out']; ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($row['adm_rls']) && $row['adm_rls']) { ?>
                                <div class="timeline-item">
                                    <span class="timeline-label">Release:</span> <?php echo (int) $row['adm_rls']; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </td>
                    <td align="center">
                        <?php 
                        if ($row['loc_move'] == 1) {
                            echo '<span class="badge badge-sm badge-primary badge-outline">WH A</span>';
                        } elseif ($row['loc_move'] == 2) {
                            echo '<span class="badge badge-sm badge-info badge-outline">WH B</span>';
                        } elseif ($row['loc_move'] == 3) {
                            echo '<span class="badge badge-sm badge-success badge-outline">Lab</span>';
                        } else {
                            echo '<span class="badge badge-sm badge-light badge-outline">' . $row['loc_move'] . '</span>';
                        }
                        ?>
                    </td>
                    <td align="center">
                        <button class="btn btn-xs btn-warning" title="Edit" onclick="openEditModal('<?php echo $row['id_act']; ?>', '<?php echo htmlspecialchars($row['dvc_sn'], ENT_QUOTES); ?>', '<?php echo $row['dvc_qc']; ?>', '<?php echo htmlspecialchars($row['loc_move'], ENT_QUOTES); ?>')">
                            ✏️
                        </button>
                        <button class="btn btn-xs btn-danger" title="Hapus" onclick="confirmDelete('<?php echo $row['id_act']; ?>')">
                            🗑️
                        </button>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="10"><i>No Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
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
            echo '<a href="javascript:showDataItem(' . ($current_page - 1) . ')" class="btn btn-sm btn-light">Previous</a>';
        }
        
        // Page numbers
        $start_page = max(0, $current_page - 2);
        $end_page = min($total_pages - 1, $current_page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="btn btn-sm btn-primary">' . ($i + 1) . '</span>';
            } else {
                echo '<a href="javascript:showDataItem(' . $i . ')" class="btn btn-sm btn-light">' . ($i + 1) . '</a>';
            }
        }
        
        // Next button
        if ($current_page < $total_pages - 1) {
            echo '<a href="javascript:showDataItem(' . ($current_page + 1) . ')" class="btn btn-sm btn-light">Next</a>';
        }
        ?>
    </div>
</div>
<?php } ?>

<!-- Modal Edit Data Item - DIPERBAIKI -->
<div id="modal_edit_item" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:10px; max-width:400px; width:90%; margin:auto; padding:24px; position:relative; top:10vh;">
        <h3 style="margin-bottom:16px; font-size:18px;">Edit Data Item</h3>
        <input type="hidden" id="edit_id_act">
        
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:bold;">Serial Number</label>
            <input type="text" id="edit_dvc_sn" class="input" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
        </div>
        
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:bold;">QC Status</label>
            <select id="edit_dvc_qc" class="select" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                <option value="0">Pending</option>
                <option value="1">Passed</option>
                <option value="2">Failed</option>
            </select>
        </div>
        
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:bold;">Location Move</label>
            <select id="edit_loc_move" class="select" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                <option value="0">-</option>
                <option value="Lantai 2">🏢 Lantai 2</option>
                <option value="Bang Toni">👨‍💼 Bang Toni</option>
                <option value="Om Bob">👨‍💼 Om Bob</option>
                <option value="Rekanan">🤝 Rekanan</option>
                <option value="LN">🏭 LN</option>
                <option value="ECBS">🏭 ECBS</option>
                <option value="LN Office">🏢 LN Office</option>
                <option value="Lantai 1">🏢 Lantai 1</option>
                <option value="Unknow">❓ Unknown</option>
            </select>
        </div>
        
        <div style="text-align:right; margin-top:20px;">
            <button class="btn btn-light" onclick="closeEditModal()" style="margin-right:8px; padding:8px 16px;">Batal</button>
            <button class="btn btn-primary" onclick="submitEditItem()" style="padding:8px 16px;">Simpan</button>
        </div>
    </div>
</div>

<!-- JavaScript DIPERBAIKI - Langsung di dalam view -->
<script>
function openEditModal(id_act, dvc_sn, dvc_qc, loc_move) {
    console.log("Opening edit modal with:", { id_act, dvc_sn, dvc_qc, loc_move });
    
    document.getElementById('edit_id_act').value = id_act;
    document.getElementById('edit_dvc_sn').value = dvc_sn || '';
    document.getElementById('edit_dvc_qc').value = dvc_qc || '0';
    document.getElementById('edit_loc_move').value = loc_move || '0';
    document.getElementById('modal_edit_item').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('modal_edit_item').style.display = 'none';
}

function confirmDelete(id_act) {
    if(confirm('Yakin ingin menghapus data ini?')) {
        fetch('<?php echo $config['base_url']; ?>inventory/input_delete_process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_act: id_act })
        })
        .then(res => res.json())
        .then(result => {
            alert(result.message);
            if(result.success) window.location.reload();
        })
        .catch(err => alert('Gagal menghapus data!'));
    }
}

function submitEditItem() {
    const id_act = document.getElementById('edit_id_act').value;
    const dvc_sn = document.getElementById('edit_dvc_sn').value.trim();
    const dvc_qc = document.getElementById('edit_dvc_qc').value;
    const loc_move = document.getElementById('edit_loc_move').value.trim();
    fetch('<?php echo $config['base_url']; ?>inventory/input_edit_process', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_act, dvc_sn, dvc_qc, loc_move })
    })
    .then(res => res.json())
    .then(result => {
        alert(result.message);
        if(result.success) window.location.reload();
    })
    .catch(err => alert('Gagal update data!'));
    closeEditModal();
}
</script>