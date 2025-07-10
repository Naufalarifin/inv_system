<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="inventory_items_' . date('Y-m-d') . '.xls"');
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Action ID</th>
            <th>Device Name</th>
            <th>Device Code</th>
            <th>Size</th>
            <th>Color</th>
            <th>Serial Number</th>
            <th>QC Status</th>
            <th>Inv In</th>
            <th>Inv Move</th>
            <th>Inv Out</th>
            <th>Inv Release</th>
            <th>Admin In</th>
            <th>Admin Move</th>
            <th>Admin Out</th>
            <th>Admin Release</th>
            <th>Location Move</th>
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
            <td><?php echo $no; ?></td>
            <td><?php echo $row['id_act']; ?></td>
            <td><?php echo !empty($row['dvc_name']) ? $row['dvc_name'] : 'Unknown Device'; ?></td>
            <td><?php echo !empty($row['dvc_code']) ? $row['dvc_code'] : '-'; ?></td>
            <td><?php echo $row['dvc_size']; ?></td>
            <td><?php echo $row['dvc_col']; ?></td>
            <td><?php echo $row['dvc_sn']; ?></td>
            <td><?php 
                if ($row['dvc_qc'] == '1') echo 'Pass';
                elseif ($row['dvc_qc'] == '2') echo 'Fail';
                else echo 'Pending';
            ?></td>
            <td><?php echo $row['inv_in'] ? date("d/m/Y H:i", strtotime($row['inv_in'])) : '-'; ?></td>
            <td><?php echo $row['inv_move'] ? date("d/m/Y H:i", strtotime($row['inv_move'])) : '-'; ?></td>
            <td><?php echo $row['inv_out'] ? date("d/m/Y H:i", strtotime($row['inv_out'])) : '-'; ?></td>
            <td><?php echo $row['inv_rls'] ? date("d/m/Y H:i", strtotime($row['inv_rls'])) : '-'; ?></td>
            <td><?php echo isset($row['adm_in']) ? $row['adm_in'] : '-'; ?></td>
            <td><?php echo isset($row['adm_move']) ? $row['adm_move'] : '-'; ?></td>
            <td><?php echo isset($row['adm_out']) ? $row['adm_out'] : '-'; ?></td>
            <td><?php echo isset($row['adm_rls']) ? $row['adm_rls'] : '-'; ?></td>
            <td><?php echo $row['loc_move']; ?></td>
        </tr>
        <?php 
            }
        }
        ?>
    </tbody>
</table>
