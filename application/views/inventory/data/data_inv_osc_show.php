<?php
$oscillators = array();
$accessories = array();
$accessories_codes = array('EFD', 'SC2', 'SC3');

if($data['query'] && $data['query']->num_rows() > 0) {
    foreach ($data['query']->result_array() as $row) {
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

function renderOscTableSection($title, $items) {
    $grouped_items =  array();
    foreach ($items as $item) {
        $code = $item['dvc_code'];
        if (!isset($grouped_items[$code])) {
            $grouped_items[$code] =  array(
                'dvc_code' => $code,
                'dvc_name' => $item['dvc_name'],
                'ln_count' => 0,
                'dn_count' => 0
            );
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

    echo '<div class="card-table mb-4">';
    echo '    <div class="table-responsive">';
    echo '        <table class="table table-border align-middle text-gray-700 font-medium text-sm compact-table">';
    echo '            <thead>';
    echo '                <tr>';
    echo '                    <th align="center" width="40">NO</th>';
    echo '                    <th align="center" width="200">' . $title . '</th>';
    echo '                    <th align="center" width="100">KODE</th>';
    echo '                    <th align="center" width="60">LN</th>';
    echo '                    <th align="center" width="60">DN</th>';
    echo '                    <th align="center" width="80">Subtotal</th>';
    echo '                    <th align="center" width="60">%';
    echo '                    </th>';
    echo '                    <th align="center" width="60">INV</th>';
    echo '                </tr>';
    echo '            </thead>';
    echo '            <tbody>';
    if (!empty($grouped_items)) {
        $no = 0;
        foreach ($grouped_items as $row) {
            $no++;
            $row_subtotal = $row['ln_count'] + $row['dn_count'];
            $row_percentage = $section_subtotal_total > 0 ? round(($row_subtotal / $section_subtotal_total) * 100, 1) : 0;
            echo '                <tr>';
            echo '                    <td align="center">' . $no . '</td>';
            echo '                    <td align="left">' . $row['dvc_name'] . '</td>';
            echo '                    <td align="center">' . $row['dvc_code'] . '</td>';
            echo '                    <td align="center">' . $row['ln_count'] . '</td>';
            echo '                    <td align="center">' . $row['dn_count'] . '</td>';
            echo '                    <td align="center"><strong>' . $row_subtotal . '</strong></td>';
            echo '                    <td align="center">' . $row_percentage . '%</td>';
            echo '                    <td align="center">' . $row_subtotal . '</td>';
            echo '                </tr>';
        }
    } else {
        echo '                <tr>';
        echo '                    <td align="center" colspan="8"><i>No Data Found</i></td>';
        echo '                </tr>';
    }
    echo '            </tbody>';
    echo '            <tfoot>';
    echo '                <tr>'; 
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;" colspan="3">TOTAL</td>';
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;">' . $section_ln_total . '</td>';
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;">' . $section_dn_total . '</td>';
    echo '                    <td style="text-align: center; vertical-align: middle; padding-left: 8px;" rowspan="2"><strong>' . $section_subtotal_total . '</strong></td>'; 
    echo '                    <td style="text-align: center; vertical-align: middle; padding-left: 8px;" rowspan="2"><strong>100%</strong></td>';
    echo '                    <td style="text-align: center; vertical-align: middle; padding-left: 8px;" rowspan="2"><strong>' . $section_subtotal_total . '</strong></td>';
    echo '                </tr>';
    echo '                <tr>';
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;" colspan="3">PERSENTASE</td>';
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;">' . ($section_subtotal_total > 0 ? round(($section_ln_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '                    <td style="text-align: center; vertical-align: top; padding-left: 8px;">' . ($section_subtotal_total > 0 ? round(($section_dn_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '                </tr>';
    echo '            </tfoot>';
    echo '        </table>';
    echo '    </div>';
    echo '</div>';
}

renderOscTableSection('JENIS OSCILLATOR', $oscillators);
echo '<div style="margin-top: 20px;"></div>';
renderOscTableSection('JENIS ACCESORIES', $accessories);
?>

<style>
.compact-table {
    font-size: 14px !important;
}
.compact-table th,
.compact-table td {
    padding: 0px 4px !important;
    line-height: 1.8 !important;
}
.compact-table th {
    font-size: 14px !important;
}

</style> 