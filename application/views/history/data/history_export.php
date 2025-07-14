<?php
// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="history_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers
$headers = array(
    'No',
    'Date & Time',
    'Activity',
    'Device SN',
    'Device Name',
    'Size',
    'Admin',
    'Location'
);
fputcsv($output, $headers);

// Data rows
if (isset($data['data']) && !empty($data['data'])) {
    $no = 1;
    foreach ($data['data'] as $row) {
        $csv_row = array(
            $no,
            date('d/m/Y H:i', strtotime($row['tanggal'])),
            $row['activity_type'],
            $row['dvc_sn'],
            isset($row['dvc_name']) ? $row['dvc_name'] : 'N/A',
            $row['dvc_size'],
            $row['admin'] ? $row['admin'] : '-',
            isset($row['location']) && $row['location'] ? $row['location'] : '-'
        );
        fputcsv($output, $csv_row);
        $no++;
    }
}

fclose($output);
?> 