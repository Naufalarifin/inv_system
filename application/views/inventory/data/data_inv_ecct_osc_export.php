<?php
// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="ECCT_OSC_Export_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Group data into categories
$oscillators = [];
$accessories = [];

// Define codes for each category
$oscillator_codes = ['STD', 'MVHF C1', 'MVSF'];
$accessories_codes = ['EFD', 'SC2', 'SC3'];

if($data['query'] && $data['query']->num_rows() > 0) {
    foreach ($data['query']->result_array() as $row) {
        if (in_array($row['dvc_code'], $oscillator_codes)) {
            $oscillators[] = $row;
        } elseif (in_array($row['dvc_code'], $accessories_codes)) {
            $accessories[] = $row;
        }
    }
}

// Function to group and merge items by code
function groupItemsByCode($items) {
    $grouped_items = [];
    foreach ($items as $item) {
        $code = $item['dvc_code'];
        if (!isset($grouped_items[$code])) {
            $grouped_items[$code] = [
                'dvc_code' => $code,
                'dvc_name' => $item['dvc_name'],
                'ln_count' => 0,
                'dn_count' => 0
            ];
        }
        $grouped_items[$code]['ln_count'] += $item['ln_count'];
        $grouped_items[$code]['dn_count'] += $item['dn_count'];
    }
    return $grouped_items;
}

// Function to render table section for export
function renderExportTableSection($title, $items) {
    $grouped_items = groupItemsByCode($items);
    
    // Calculate totals
    $section_ln_total = 0;
    $section_dn_total = 0;
    $section_subtotal_total = 0;

    foreach ($grouped_items as $item) {
        $section_ln_total += $item['ln_count'];
        $section_dn_total += $item['dn_count'];
        $section_subtotal_total += ($item['ln_count'] + $item['dn_count']);
    }

    echo '<table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">';
    echo '<thead>';
    echo '<tr style="background-color: #f0f0f0;">';
    echo '<th>NO</th>';
    echo '<th>' . $title . '</th>';
    echo '<th>KODE</th>';
    echo '<th>LN</th>';
    echo '<th>DN</th>';
    echo '<th>Subtotal</th>';
    echo '<th>%</th>';
    echo '<th>INV</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    if (!empty($grouped_items)) {
        $no = 0;
        foreach ($grouped_items as $row) {
            $no++;
            $row_subtotal = $row['ln_count'] + $row['dn_count'];
            $row_percentage = $section_subtotal_total > 0 ? round(($row_subtotal / $section_subtotal_total) * 100, 1) : 0;
            echo '<tr>';
            echo '<td>' . $no . '</td>';
            echo '<td>' . $row['dvc_name'] . '</td>';
            echo '<td>' . $row['dvc_code'] . '</td>';
            echo '<td>' . $row['ln_count'] . '</td>';
            echo '<td>' . $row['dn_count'] . '</td>';
            echo '<td>' . $row_subtotal . '</td>';
            echo '<td>' . $row_percentage . '%</td>';
            echo '<td>' . $row_subtotal . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
        echo '<td colspan="8">No Data Found</td>';
        echo '</tr>';
    }
    
    // Total row
    echo '<tr style="background-color: #e6f3ff; font-weight: bold;">';
    echo '<td colspan="3">TOTAL</td>';
    echo '<td>' . $section_ln_total . '</td>';
    echo '<td>' . $section_dn_total . '</td>';
    echo '<td>' . $section_subtotal_total . '</td>';
    echo '<td>100%</td>';
    echo '<td>' . $section_subtotal_total . '</td>';
    echo '</tr>';
    
    // Percentage row
    echo '<tr style="background-color: #fff2cc; font-weight: bold;">';
    echo '<td colspan="3">PERSENTASE</td>';
    echo '<td>' . ($section_subtotal_total > 0 ? round(($section_ln_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '<td>' . ($section_subtotal_total > 0 ? round(($section_dn_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '<td colspan="3">-</td>';
    echo '</tr>';
    
    echo '</tbody>';
    echo '</table>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ECCT OSC Export</title>
</head>
<body>
    <?php
    renderExportTableSection('JENIS OSCILLATOR', $oscillators);
    renderExportTableSection('JENIS ACCESORIES', $accessories);
    ?>
</body>
</html>
