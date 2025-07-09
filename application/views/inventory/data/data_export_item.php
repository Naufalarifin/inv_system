<?php
// Set headers untuk CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="inventory_data_' . date('Y-m-d_H-i-s') . '.csv"');

// Output CSV
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers
fputcsv($output, array(
    'No',
    'Action ID', 
    'Device ID',
    'Device Code',
    'Size',
    'Color', 
    'Serial Number',
    'QC Status',
    'Inv In',
    'Inv Move', 
    'Inv Out',
    'Inv Release',
    'Location',
    'Admin In',
    'Admin Move',
    'Admin Out', 
    'Admin Release'
));

// CSV Data
if($data['query'] && $data['query']->num_rows() > 0) {
    $no = 1;
    foreach($data['query']->result_array() as $row) {
        fputcsv($output, array(
            $no++,
            $row['id_act'],
            $row['id_dvc'], 
            isset($row['dvc_code']) ? $row['dvc_code'] : 'N/A',
            $row['dvc_size'],
            $row['dvc_col'],
            $row['dvc_sn'],
            $row['dvc_qc'] == '1' ? 'Passed' : ($row['dvc_qc'] == '2' ? 'Failed' : 'Pending'),
            $row['inv_in'],
            $row['inv_move'],
            $row['inv_out'], 
            $row['inv_rls'],
            isset($row['location_name']) ? $row['location_name'] : 'Unknown',
            $row['adm_in'],
            $row['adm_move'],
            $row['adm_out'],
            $row['adm_rls']
        ));
    }
}

fclose($output);
exit;
?>
