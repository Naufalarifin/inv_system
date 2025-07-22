<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="osc_export_' . date('Y-m-d_H-i-s') . '.xls"');

$oscillators = [];
$accessories = [];
$accessories_codes = ['EFD', 'SC2', 'SC3'];

if($data['data'] && is_array($data['data'])) {
    foreach ($data['data'] as $row) {
        // Jika ECBS (hanya ada total_count), mapping ke struktur ECCT agar bisa diproses
        if (isset($row['total_count'])) {
            $row['ln_count'] = $row['total_count'];
            $row['dn_count'] = 0;
        }
        if (in_array($row['dvc_code'], $accessories_codes)) {
            $accessories[] = $row;
        }
        else if (isset($row['dvc_type']) && strtolower($row['dvc_type']) === 'osc') {
            $oscillators[] = $row;
        }
    }
}

function renderOscTableSectionExport($title, $items) {
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
        $grouped_items[$code]['ln_count'] += isset($item['ln_count']) ? $item['ln_count'] : 0;
        $grouped_items[$code]['dn_count'] += isset($item['dn_count']) ? $item['dn_count'] : 0;
    }

    $section_ln_total = 0;
    $section_dn_total = 0;
    $section_subtotal_total = 0;

    foreach ($grouped_items as $item) {
        $section_ln_total += $item['ln_count'];
        $section_dn_total += $item['dn_count'];
        $section_subtotal_total += ($item['ln_count'] + $item['dn_count']);
    }

    echo '<table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">';
    echo '    <thead>';
    echo '        <tr style="background-color: #f0f0f0;">';
    echo '            <th>NO</th>';
    echo '            <th>' . $title . '</th>';
    echo '            <th>KODE</th>';
    echo '            <th>LN</th>';
    echo '            <th>DN</th>';
    echo '            <th>Subtotal</th>';
    echo '            <th>%</th>';
    echo '            <th>INV</th>';
    echo '        </tr>';
    echo '    </thead>';
    echo '    <tbody>';
    if (!empty($grouped_items)) {
        $no = 0;
        foreach ($grouped_items as $row) {
            $no++;
            $row_subtotal = $row['ln_count'] + $row['dn_count'];
            $row_percentage = $section_subtotal_total > 0 ? round(($row_subtotal / $section_subtotal_total) * 100, 1) : 0;
            echo '        <tr>';
            echo '            <td>' . $no . '</td>';
            echo '            <td>' . $row['dvc_name'] . '</td>';
            echo '            <td>' . $row['dvc_code'] . '</td>';
            echo '            <td>' . $row['ln_count'] . '</td>';
            echo '            <td>' . $row['dn_count'] . '</td>';
            echo '            <td><strong>' . $row_subtotal . '</strong></td>';
            echo '            <td>' . $row_percentage . '%</td>';
            echo '            <td>' . $row_subtotal . '</td>';
            echo '        </tr>';
        }
    } else {
        echo '        <tr>';
        echo '            <td colspan="8"><i>No Data Found</i></td>';
        echo '        </tr>';
    }
    echo '    </tbody>';
    echo '    <tfoot>';
    echo '        <tr>'; 
    echo '            <td colspan="3">TOTAL</td>';
    echo '            <td>' . $section_ln_total . '</td>';
    echo '            <td>' . $section_dn_total . '</td>';
    echo '            <td rowspan="2"><strong>' . $section_subtotal_total . '</strong></td>'; 
    echo '            <td rowspan="2"><strong>100%</strong></td>';
    echo '            <td rowspan="2"><strong>' . $section_subtotal_total . '</strong></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td colspan="3">PERSENTASE</td>';
    echo '            <td>' . ($section_subtotal_total > 0 ? round(($section_ln_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '            <td>' . ($section_subtotal_total > 0 ? round(($section_dn_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '        </tr>';
    echo '    </tfoot>';
    echo '</table>';
}

renderOscTableSectionExport('JENIS OSCILLATOR', $oscillators);
echo '<br/>';
renderOscTableSectionExport('JENIS ACCESORIES', $accessories); 