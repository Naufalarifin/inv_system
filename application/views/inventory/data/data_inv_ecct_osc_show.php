<?php
// Group data into categories
$oscillators = [];
$accessories = [];

// Define codes for each category based on the image
// Pastikan kode ini sesuai dengan dvc_code yang ada di database Anda
$oscillator_codes = ['STD', 'MVHF C1', 'MVSF'];
$accessories_codes = ['EFD', 'SC2', 'SC3']; // Menambahkan SC2 dan SC3

if($data['query'] && $data['query']->num_rows() > 0) {
    foreach ($data['query']->result_array() as $row) {
        if (in_array($row['dvc_code'], $oscillator_codes)) {
            $oscillators[] = $row;
        } elseif (in_array($row['dvc_code'], $accessories_codes)) {
            $accessories[] = $row;
        }
        // Anda bisa menambahkan kategori default atau penanganan untuk item yang tidak terklasifikasi
    }
}

// Helper function to render a table section
function renderOscTableSection($title, $items) {
    // Group items by dvc_code and merge their counts
    $grouped_items = [];
    foreach ($items as $item) {
        $code = $item['dvc_code'];
        if (!isset($grouped_items[$code])) {
            $grouped_items[$code] = [
                'dvc_code' => $code,
                'dvc_name' => $item['dvc_name'], // Use the first name found
                'ln_count' => 0,
                'dn_count' => 0
            ];
        }
        $grouped_items[$code]['ln_count'] += $item['ln_count'];
        $grouped_items[$code]['dn_count'] += $item['dn_count'];
    }

    // Calculate totals for this section
    $section_ln_total = 0;
    $section_dn_total = 0;
    $section_subtotal_total = 0;

    foreach ($grouped_items as $item) {
        $section_ln_total += $item['ln_count'];
        $section_dn_total += $item['dn_count'];
        $section_subtotal_total += ($item['ln_count'] + $item['dn_count']);
    }

    echo '<div class="card-table mb-4">'; // Added mb-4 for spacing between tables
    echo '    <div class="table-responsive">';
    echo '        <table class="table table-border align-middle text-gray-700 font-medium text-sm">';
    echo '            <thead>';
    echo '                <tr>';
    echo '                    <th align="center" width="40">NO</th>';
    echo '                    <th align="center" width="200">' . $title . '</th>'; // Dynamic title
    echo '                    <th align="center" width="100">KODE</th>';
    echo '                    <th align="center" width="60">LN</th>';
    echo '                    <th align="center" width="60">DN</th>';
    echo '                    <th align="center" width="80">Subtotal</th>';
    echo '                    <th align="center" width="60">%</th>';
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
            echo '                    <td align="center">' . $row_subtotal . '</td>'; // INV is same as Subtotal for row
            echo '                </tr>';
        }
    } else {
        echo '                <tr>';
        echo '                    <td align="center" colspan="8"><i>No Data Found</i></td>';
        echo '                </tr>';
    }
    echo '            </tbody>';
    echo '            <tfoot>';
    echo '                <tr>'; // Removed background-color and color
    echo '                    <td align="center" colspan="3">TOTAL</td>';
    echo '                    <td align="center">' . $section_ln_total . '</td>';
    echo '                    <td align="center">' . $section_dn_total . '</td>';
    echo '                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>' . $section_subtotal_total . '</strong></td>'; // Removed background-color
    echo '                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>'; // Removed background-color
    echo '                    <td align="center" rowspan="2" style="vertical-align: middle;"><strong>' . $section_subtotal_total . '</strong></td>'; // INV total is same as Subtotal total
    echo '                </tr>';
    echo '                <tr>'; // Removed background-color and color
    echo '                    <td align="center" colspan="3">PERSENTASE</td>';
    echo '                    <td align="center">' . ($section_subtotal_total > 0 ? round(($section_ln_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '                    <td align="center">' . ($section_subtotal_total > 0 ? round(($section_dn_total / $section_subtotal_total) * 100, 1) : 0) . '%</td>';
    echo '                </tr>';
    echo '            </tfoot>';
    echo '        </table>';
    echo '        <div style="padding: 10px; font-size: 11px; color: #666;">';
    echo '            <strong>*Keterangan:</strong> Jumlah hanya menghitung item yang belum keluar (inv_out masih kosong)';
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
}

// Render the tables
renderOscTableSection('JENIS OSCILLATOR', $oscillators);
echo '<div style="margin-top: 20px;"></div>'; // Spacer between tables
renderOscTableSection('JENIS ACCESORIES', $accessories);
?>

<style>
/* Mengembalikan gaya tabel ke default Metronic/shadcn */
.table.table-border.text-xs td, .table.table-border.text-xs th {
  font-size: 10px !important;
  padding: 4px 6px !important;
}
.table.table-border tfoot tr td {
    font-size: 12px !important;
    padding: 6px 8px !important;
}
/* Menghapus styling warna yang sebelumnya ditambahkan */
/* Tidak ada lagi blok style khusus untuk warna di sini */
</style>
