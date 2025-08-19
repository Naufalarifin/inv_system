<?php
// Load model
$this->load->model('report_model');

// Build filters from current_filters to allow week/year/month selection
$filters = array();
if (isset($current_filters['id_week']) && $current_filters['id_week']) { $filters['id_week'] = $current_filters['id_week']; }
if (isset($current_filters['year']) && $current_filters['year'] !== '') { $filters['year'] = $current_filters['year']; }
if (isset($current_filters['month']) && $current_filters['month'] !== '') { $filters['month'] = $current_filters['month']; }
if (isset($current_filters['week']) && $current_filters['week'] !== '') { $filters['week'] = $current_filters['week']; }
if (isset($current_filters['device_search']) && $current_filters['device_search'] !== '') { $filters['device_search'] = $current_filters['device_search']; }

// Daftar warna untuk 7 baris VOH (tetap hardcoded untuk tampilan)
$voh_colors = array(
    array('name' => 'Black', 'hex' => '#000000'),
    array('name' => 'Navy', 'hex' => '#001f5b'),
    array('name' => 'Maroon', 'hex' => '#800000'),
    array('name' => 'Army', 'hex' => '#4b5320'),
    array('name' => 'Dark Gray', 'hex' => '#A9A9A9'),
    array('name' => 'Grey', 'hex' => '#808080'),
    array('name' => 'Custom', 'hex' => '#ffffff'),
);
$sizes = array('size_xs','size_s','size_m','size_l','size_xl','size_xxl','size_3xl','size_all','size_cus');
$voh_count = count($voh_colors);

// Load data for each section with filters applied
$model_data_needs_app = $this->report_model->getSummaryEcbsReportData('needs', $filters);
$model_data_needs_osc = $this->report_model->getSummaryEcbsOscReportData('needs', $filters);

$model_data_onpms_app = $this->report_model->getSummaryEcbsReportData('on_pms', $filters);
$model_data_onpms_osc = $this->report_model->getSummaryEcbsOscReportData('on_pms', $filters);

$model_data_order_app = $this->report_model->getSummaryEcbsReportData('order', $filters);
$model_data_order_osc = $this->report_model->getSummaryEcbsOscReportData('order', $filters);

$model_data_over_app = $this->report_model->getSummaryEcbsReportData('over', $filters);
$model_data_over_osc = $this->report_model->getSummaryEcbsOscReportData('over', $filters);

// STOCK section
$model_data_stock_app = $this->report_model->getSummaryEcbsReportData('stock', $filters);
$model_data_stock_osc = $this->report_model->getSummaryEcbsOscReportData('stock', $filters);

// Calculate totals for each section
function calculateTotals($data) {
    $totals = array(
        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
        'grand_total' => 0
    );
    
    foreach ($data as $item) {
        foreach ($totals as $key => $value) {
            if ($key !== 'grand_total' && isset($item[$key])) {
                $totals[$key] += (int)$item[$key];
            }
        }
        $totals['grand_total'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
    }
    
    return $totals;
}

function calculateOscTotal($data) {
    $total = 0;
    foreach ($data as $item) {
        $total += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
    }
    return $total;
}

// Calculate totals for each section
$column_totals_needs_app = calculateTotals($model_data_needs_app);
$grand_total_needs_app = $column_totals_needs_app['grand_total'];
$grand_total_needs_osc = calculateOscTotal($model_data_needs_osc);

$column_totals_onpms_app = calculateTotals($model_data_onpms_app);
$grand_total_onpms_app = $column_totals_onpms_app['grand_total'];
$grand_total_onpms_osc = calculateOscTotal($model_data_onpms_osc);

$column_totals_order_app = calculateTotals($model_data_order_app);
$grand_total_order_app = $column_totals_order_app['grand_total'];
$grand_total_order_osc = calculateOscTotal($model_data_order_osc);

$column_totals_over_app = calculateTotals($model_data_over_app);
$grand_total_over_app = $column_totals_over_app['grand_total'];
$grand_total_over_osc = calculateOscTotal($model_data_over_osc);

$column_totals_stock_app = calculateTotals($model_data_stock_app);
$grand_total_stock_app = $column_totals_stock_app['grand_total'];
$grand_total_stock_osc = calculateOscTotal($model_data_stock_osc);

// Check if we're in ECBS mode
$is_ecbs = true; // Since this is summary_ecbs.php
?>

<div class="filter-info" style="margin:10px 0;">
    <strong>NEEDS</strong>
</div>

<!-- STOCK Section -->
<div class="filter-info" style="margin:30px 0 10px 0;">
    <strong>STOCK</strong>
</div>

<div class="tables-container">
    <!-- Left Table - APP Devices -->
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Jenis Apparel</th>
                            <th align="center">Kode</th>
                            <th align="center">XS</th>
                            <th align="center">S</th>
                            <th align="center">M</th>
                            <th align="center">L</th>
                            <th align="center">XL</th>
                            <th align="center">XXL</th>
                            <th align="center">3XL</th>
                            <th align="center">ALL</th>
                            <th align="center">CUS</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_display_no = 1;
                        if($is_ecbs && !empty($model_data_stock_app)) {
                            // VOH fixed 7 colors
                            $voh_rows_to_display = array();
                            foreach ($voh_colors as $color_info) {
                                $found_voh_item = null;
                                foreach ($model_data_stock_app as $item) {
                                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                                        $found_voh_item = $item;
                                        break;
                                    }
                                }
                                if (!$found_voh_item) {
                                    $found_voh_item = array(
                                        'dvc_name' => 'Vest Outer Hoodie Element',
                                        'dvc_code' => 'VOH',
                                        'warna' => $color_info['name'],
                                        'subtotal' => 0,
                                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                                    );
                                }
                                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
                            }
                            $voh_count = count($voh_rows_to_display);
                            if ($voh_count > 0) {
                                foreach ($voh_rows_to_display as $idx => $item) {
                                    $color_info = $item['color_info'];
                                    $row_data = $item['data'];
                                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                        ?>
                        <tr>
                            <?php if ($idx == 0) { ?>
                                <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                                <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                            <?php } ?>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                <?php echo htmlspecialchars($row_data['dvc_code']); ?>
                                <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                    <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                <?php } else { ?>
                                    <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                <?php } ?>
                            </td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                            <?php } ?>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($subtotal / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                        <?php
                                }
                                $row_display_no++;
                            }
                            // Non-VOH items aggregation
                            if (!empty($model_data_stock_app)) {
                                $other_items_agg = array();
                                foreach ($model_data_stock_app as $item) {
                                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                                    if (!isset($other_items_agg[$key])) {
                                        $other_items_agg[$key] = $item;
                                    } else {
                                        foreach ($sizes as $sz) {
                                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                                        }
                                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    }
                                }
                                foreach ($other_items_agg as $item) {
                                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    $percentage = $grand_total_stock_app > 0 ? round(($subtotal / $grand_total_stock_app) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;">&nbsp;<?php echo $row_display_no++; ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td style="text-align: center; vertical-align: top; padding-left: 8px;">
                                    <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?>
                                </td>
                            <?php } ?>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><strong><?php echo $subtotal; ?></strong></td>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $column_totals_stock_app['size_xs']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_s']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_m']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_l']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_xl']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_xxl']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_3xl']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_all']; ?></td>
                            <td align="center"><?php echo $column_totals_stock_app['size_cus']; ?></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total_stock_app; ?></strong></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                        </tr>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">PERSENTASE</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_xs'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_s'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_m'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_l'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_xl'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_xxl'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_3xl'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_all'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_stock_app > 0 ? round(($column_totals_stock_app['size_cus'] / $grand_total_stock_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Vertical Separator -->
    <div class="table-separator"></div>

    <!-- Right Table - OSC Devices -->
    <div class="table-right">
        <div class="table-title">OSCILATOR</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Kode</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($model_data_stock_osc)) {
                            $osc_row_no = 1;
                            foreach ($model_data_stock_osc as $osc_item) {
                                $subtotal = isset($osc_item['subtotal']) ? (int)$osc_item['subtotal'] : 0;
                                $percentage = $grand_total_stock_osc > 0 ? round(($subtotal / $grand_total_stock_osc) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td align="center"><?php echo $osc_row_no++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($osc_item['dvc_name']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($osc_item['dvc_code']); ?></td>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><strong><?php echo $grand_total_stock_osc; ?></strong></td>
                            <td align="center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- STOCK Section -->
<div class="filter-info" style="margin:30px 0 10px 0;">
    <strong>NEEDS</strong>
</div>

<div class="tables-container">
    <!-- Left Table - APP Devices -->
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Jenis Apparel</th>
                            <th align="center">Kode</th>
                            <th align="center">XS</th>
                            <th align="center">S</th>
                            <th align="center">M</th>
                            <th align="center">L</th>
                            <th align="center">XL</th>
                            <th align="center">XXL</th>
                            <th align="center">3XL</th>
                            <th align="center">ALL</th>
                            <th align="center">CUS</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_display_no = 1;
                        if($is_ecbs && !empty($model_data_needs_app)) {
                            // --- ECBS MODE: Tampilkan 7 baris VOH sesuai $voh_colors ---
                            $voh_rows_to_display = array();
                            foreach ($voh_colors as $color_info) {
                                $found_voh_item = null;
                                foreach ($model_data_needs_app as $item) {
                                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                                        $found_voh_item = $item;
                                        break;
                                    }
                                }
                                if (!$found_voh_item) {
                                    $found_voh_item = array(
                                        'dvc_name' => 'Vest Outer Hoodie Element',
                                        'dvc_code' => 'VOH',
                                        'warna' => $color_info['name'],
                                        'subtotal' => 0,
                                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                                    );
                                }
                                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
                            }
                            $voh_count = count($voh_rows_to_display);
                            if ($voh_count > 0) {
                                foreach ($voh_rows_to_display as $idx => $item) {
                                    $color_info = $item['color_info'];
                                    $row_data = $item['data'];
                                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                        ?>
                        <tr>
                            <?php if ($idx == 0) { ?>
                                <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                                <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                            <?php } ?>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                <?php echo htmlspecialchars($row_data['dvc_code']); ?>
                                <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                    <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                <?php } else { ?>
                                    <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                <?php } ?>
                            </td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                            <?php } ?>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($subtotal / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                        <?php
                                }
                                $row_display_no++;
                            }
                            // --- Barang lain (non-VOH), agregasi per nama/kode ---
                            if (!empty($model_data_needs_app)) {
                                $other_items_agg = array();
                                foreach ($model_data_needs_app as $item) {
                                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                                    if (!isset($other_items_agg[$key])) {
                                        $other_items_agg[$key] = $item;
                                    } else {
                                        foreach ($sizes as $sz) {
                                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                                        }
                                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    }
                                }
                                foreach ($other_items_agg as $item) {
                                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    $percentage = $grand_total_needs_app > 0 ? round(($subtotal / $grand_total_needs_app) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $row_display_no++; ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td style="text-align: center; vertical-align: top; padding-left: 8px;">
                                    <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?>
                                </td>
                            <?php } ?>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><strong><?php echo $subtotal; ?></strong></td>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                                }
                            }
                            if ($row_display_no == 1) {
                        ?>
                        <tr>
                            
                        </tr>
                        <?php } ?>
                        <?php
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $column_totals_needs_app['size_xs']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_s']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_m']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_l']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_xl']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_xxl']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_3xl']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_all']; ?></td>
                            <td align="center"><?php echo $column_totals_needs_app['size_cus']; ?></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total_needs_app; ?></strong></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                        </tr>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">PERSENTASE</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_xs'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_s'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_m'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_l'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_xl'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_xxl'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_3xl'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_all'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_needs_app > 0 ? round(($column_totals_needs_app['size_cus'] / $grand_total_needs_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Vertical Separator -->
    <div class="table-separator"></div>

    <!-- Right Table - OSC Devices -->
    <div class="table-right">
        <div class="table-title">OSCILATOR</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Kode</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($model_data_needs_osc)) {
                            $osc_row_no = 1;
                            foreach ($model_data_needs_osc as $osc_item) {
                                $subtotal = isset($osc_item['subtotal']) ? (int)$osc_item['subtotal'] : 0;
                                $percentage = $grand_total_needs_osc > 0 ? round(($subtotal / $grand_total_needs_osc) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td align="center"><?php echo $osc_row_no++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($osc_item['dvc_name']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($osc_item['dvc_code']); ?></td>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><strong><?php echo $grand_total_needs_osc; ?></strong></td>
                            <td align="center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ON PMS Section -->
<div class="filter-info" style="margin:30px 0 10px 0;">
    <strong>ON PMS</strong>
</div>

<div class="tables-container">
    <!-- Left Table - APP Devices -->
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Jenis Apparel</th>
                            <th align="center">Kode</th>
                            <th align="center">XS</th>
                            <th align="center">S</th>
                            <th align="center">M</th>
                            <th align="center">L</th>
                            <th align="center">XL</th>
                            <th align="center">XXL</th>
                            <th align="center">3XL</th>
                            <th align="center">ALL</th>
                            <th align="center">CUS</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_display_no = 1;
                        if($is_ecbs && !empty($model_data_onpms_app)) {
                            // --- ECBS MODE: Tampilkan 7 baris VOH sesuai $voh_colors ---
                            $voh_rows_to_display = array();
                            foreach ($voh_colors as $color_info) {
                                $found_voh_item = null;
                                foreach ($model_data_onpms_app as $item) {
                                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                                        $found_voh_item = $item;
                                        break;
                                    }
                                }
                                if (!$found_voh_item) {
                                    $found_voh_item = array(
                                        'dvc_name' => 'Vest Outer Hoodie Element',
                                        'dvc_code' => 'VOH',
                                        'warna' => $color_info['name'],
                                        'subtotal' => 0,
                                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                                    );
                                }
                                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
                            }
                            $voh_count = count($voh_rows_to_display);
                            if ($voh_count > 0) {
                                foreach ($voh_rows_to_display as $idx => $item) {
                                    $color_info = $item['color_info'];
                                    $row_data = $item['data'];
                                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                        ?>
                        <tr>
                            <?php if ($idx == 0) { ?>
                                <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                                <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                            <?php } ?>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                <?php echo htmlspecialchars($row_data['dvc_code']); ?>
                                <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                    <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                <?php } else { ?>
                                    <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                <?php } ?>
                            </td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                            <?php } ?>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($subtotal / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                        <?php
                                }
                                $row_display_no++;
                            }
                            // --- Barang lain (non-VOH), agregasi per nama/kode ---
                            if (!empty($model_data_onpms_app)) {
                                $other_items_agg = array();
                                foreach ($model_data_onpms_app as $item) {
                                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                                    if (!isset($other_items_agg[$key])) {
                                        $other_items_agg[$key] = $item;
                                    } else {
                                        foreach ($sizes as $sz) {
                                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                                        }
                                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    }
                                }
                                foreach ($other_items_agg as $item) {
                                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    $percentage = $grand_total_onpms_app > 0 ? round(($subtotal / $grand_total_onpms_app) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $row_display_no++; ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td style="text-align: center; vertical-align: top; padding-left: 8px;">
                                    <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?>
                                </td>
                            <?php } ?>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><strong><?php echo $subtotal; ?></strong></td>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                                }
                            }
                            if ($row_display_no == 1) {
                        ?>
                        <tr>
                            
                        </tr>
                        <?php } ?>
                        <?php
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_xs']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_s']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_m']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_l']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_xl']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_xxl']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_3xl']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_all']; ?></td>
                            <td align="center"><?php echo $column_totals_onpms_app['size_cus']; ?></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total_onpms_app; ?></strong></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                        </tr>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">PERSENTASE</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_xs'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_s'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_m'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_l'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_xl'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_xxl'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_3xl'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_all'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_onpms_app > 0 ? round(($column_totals_onpms_app['size_cus'] / $grand_total_onpms_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Vertical Separator -->
    <div class="table-separator"></div>

    <!-- Right Table - OSC Devices -->
    <div class="table-right">
        <div class="table-title">OSCILATOR</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Kode</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($model_data_onpms_osc)) {
                            $osc_row_no = 1;
                            foreach ($model_data_onpms_osc as $osc_item) {
                                $subtotal = isset($osc_item['subtotal']) ? (int)$osc_item['subtotal'] : 0;
                                $percentage = $grand_total_onpms_osc > 0 ? round(($subtotal / $grand_total_onpms_osc) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td align="center"><?php echo $osc_row_no++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($osc_item['dvc_name']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($osc_item['dvc_code']); ?></td>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><strong><?php echo $grand_total_onpms_osc; ?></strong></td>
                            <td align="center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ORDER Section -->
<div class="filter-info" style="margin:30px 0 10px 0;">
    <strong>ORDER</strong>
</div>

<div class="tables-container">
    <!-- Left Table - APP Devices -->
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Jenis Apparel</th>
                            <th align="center">Kode</th>
                            <th align="center">XS</th>
                            <th align="center">S</th>
                            <th align="center">M</th>
                            <th align="center">L</th>
                            <th align="center">XL</th>
                            <th align="center">XXL</th>
                            <th align="center">3XL</th>
                            <th align="center">ALL</th>
                            <th align="center">CUS</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_display_no = 1;
                        if($is_ecbs && !empty($model_data_order_app)) {
                            // --- ECBS MODE: Tampilkan 7 baris VOH sesuai $voh_colors ---
                            $voh_rows_to_display = array();
                            foreach ($voh_colors as $color_info) {
                                $found_voh_item = null;
                                foreach ($model_data_order_app as $item) {
                                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                                        $found_voh_item = $item;
                                        break;
                                    }
                                }
                                if (!$found_voh_item) {
                                    $found_voh_item = array(
                                        'dvc_name' => 'Vest Outer Hoodie Element',
                                        'dvc_code' => 'VOH',
                                        'warna' => $color_info['name'],
                                        'subtotal' => 0,
                                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                                    );
                                }
                                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
                            }
                            $voh_count = count($voh_rows_to_display);
                            if ($voh_count > 0) {
                                foreach ($voh_rows_to_display as $idx => $item) {
                                    $color_info = $item['color_info'];
                                    $row_data = $item['data'];
                                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                        ?>
                        <tr>
                            <?php if ($idx == 0) { ?>
                                <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                                <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                            <?php } ?>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                <?php echo htmlspecialchars($row_data['dvc_code']); ?>
                                <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                    <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                <?php } else { ?>
                                    <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                <?php } ?>
                            </td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                            <?php } ?>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($subtotal / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                        <?php
                                }
                                $row_display_no++;
                            }
                            // --- Barang lain (non-VOH), agregasi per nama/kode ---
                            if (!empty($model_data_order_app)) {
                                $other_items_agg = array();
                                foreach ($model_data_order_app as $item) {
                                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                                    if (!isset($other_items_agg[$key])) {
                                        $other_items_agg[$key] = $item;
                                    } else {
                                        foreach ($sizes as $sz) {
                                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                                        }
                                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    }
                                }
                                foreach ($other_items_agg as $item) {
                                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    $percentage = $grand_total_order_app > 0 ? round(($subtotal / $grand_total_order_app) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $row_display_no++; ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td style="text-align: center; vertical-align: top; padding-left: 8px;">
                                    <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?>
                                </td>
                            <?php } ?>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><strong><?php echo $subtotal; ?></strong></td>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                                }
                            }
                            if ($row_display_no == 1) {
                        ?>
                        <tr>
                            
                        </tr>
                        <?php } ?>
                        <?php
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $column_totals_order_app['size_xs']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_s']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_m']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_l']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_xl']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_xxl']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_3xl']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_all']; ?></td>
                            <td align="center"><?php echo $column_totals_order_app['size_cus']; ?></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total_order_app; ?></strong></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                        </tr>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">PERSENTASE</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_xs'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_s'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_m'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_l'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_xl'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_xxl'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_3xl'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_all'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_order_app > 0 ? round(($column_totals_order_app['size_cus'] / $grand_total_order_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Vertical Separator -->
    <div class="table-separator"></div>

    <!-- Right Table - OSC Devices -->
    <div class="table-right">
        <div class="table-title">OSCILATOR</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Kode</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($model_data_order_osc)) {
                            $osc_row_no = 1;
                            foreach ($model_data_order_osc as $osc_item) {
                                $subtotal = isset($osc_item['subtotal']) ? (int)$osc_item['subtotal'] : 0;
                                $percentage = $grand_total_order_osc > 0 ? round(($subtotal / $grand_total_order_osc) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td align="center"><?php echo $osc_row_no++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($osc_item['dvc_name']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($osc_item['dvc_code']); ?></td>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><strong><?php echo $grand_total_order_osc; ?></strong></td>
                            <td align="center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- OVER Section -->
<div class="filter-info" style="margin:30px 0 10px 0;">
    <strong>OVER</strong>
</div>

<div class="tables-container">
    <!-- Left Table - APP Devices -->
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Jenis Apparel</th>
                            <th align="center">Kode</th>
                            <th align="center">XS</th>
                            <th align="center">S</th>
                            <th align="center">M</th>
                            <th align="center">L</th>
                            <th align="center">XL</th>
                            <th align="center">XXL</th>
                            <th align="center">3XL</th>
                            <th align="center">ALL</th>
                            <th align="center">CUS</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_display_no = 1;
                        if($is_ecbs && !empty($model_data_over_app)) {
                            // --- ECBS MODE: Tampilkan 7 baris VOH sesuai $voh_colors ---
                            $voh_rows_to_display = array();
                            foreach ($voh_colors as $color_info) {
                                $found_voh_item = null;
                                foreach ($model_data_over_app as $item) {
                                    if ((stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0)
                                        && strtolower(trim($item['warna'])) === strtolower(trim($color_info['name']))) {
                                        $found_voh_item = $item;
                                        break;
                                    }
                                }
                                if (!$found_voh_item) {
                                    $found_voh_item = array(
                                        'dvc_name' => 'Vest Outer Hoodie Element',
                                        'dvc_code' => 'VOH',
                                        'warna' => $color_info['name'],
                                        'subtotal' => 0,
                                        'size_xs' => 0, 'size_s' => 0, 'size_m' => 0, 'size_l' => 0,
                                        'size_xl' => 0, 'size_xxl' => 0, 'size_3xl' => 0, 'size_all' => 0, 'size_cus' => 0,
                                    );
                                }
                                $voh_rows_to_display[] = array('color_info' => $color_info, 'data' => $found_voh_item);
                            }
                            $voh_count = count($voh_rows_to_display);
                            if ($voh_count > 0) {
                                foreach ($voh_rows_to_display as $idx => $item) {
                                    $color_info = $item['color_info'];
                                    $row_data = $item['data'];
                                    $subtotal = isset($row_data['subtotal']) ? (int)$row_data['subtotal'] : 0;
                        ?>
                        <tr>
                            <?php if ($idx == 0) { ?>
                                <td align="center" rowspan="<?php echo $voh_count; ?>"><?php echo $row_display_no; ?></td>
                                <td align="left" rowspan="<?php echo $voh_count; ?>">Vest Outer Hoodie Element</td>
                            <?php } ?>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;">
                                <?php echo htmlspecialchars($row_data['dvc_code']); ?>
                                <?php if (strtolower($color_info['name']) === 'custom') { ?>
                                    <span style="font-size:12px;font-weight:bold;margin-left:4px;">CUSTOM</span>
                                <?php } else { ?>
                                    <span style="display:inline-block;width:16px;height:16px;background:<?php echo htmlspecialchars($color_info['hex']); ?>;border-radius:3px;vertical-align:baseline;border:1px solid #ccc;margin-left:4px;margin-right:8px;"></span><span style="vertical-align:baseline;"><?php echo htmlspecialchars($color_info['name']) ?></span>
                                <?php } ?>
                            </td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td align="center"><?php echo isset($row_data[$sz]) ? (int)$row_data[$sz] : 0; ?></td>
                            <?php } ?>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($subtotal / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                        <?php
                                }
                                $row_display_no++;
                            }
                            // --- Barang lain (non-VOH), agregasi per nama/kode ---
                            if (!empty($model_data_over_app)) {
                                $other_items_agg = array();
                                foreach ($model_data_over_app as $item) {
                                    if (stripos($item['dvc_name'], 'Vest Outer Hoodie') !== false || stripos($item['dvc_code'], 'VOH') === 0) continue;
                                    $key = strtolower(trim($item['dvc_name'])) . '|' . strtolower(trim($item['dvc_code'])) . '|' . strtolower(trim($item['warna']));
                                    if (!isset($other_items_agg[$key])) {
                                        $other_items_agg[$key] = $item;
                                    } else {
                                        foreach ($sizes as $sz) {
                                            $other_items_agg[$key][$sz] += isset($item[$sz]) ? (int)$item[$sz] : 0;
                                        }
                                        $other_items_agg[$key]['subtotal'] += isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    }
                                }
                                foreach ($other_items_agg as $item) {
                                    $subtotal = isset($item['subtotal']) ? (int)$item['subtotal'] : 0;
                                    $percentage = $grand_total_over_app > 0 ? round(($subtotal / $grand_total_over_app) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $row_display_no++; ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_name']); ?></td>
                            <td style="text-align: left; vertical-align: top; padding-left: 8px;"><?php echo htmlspecialchars($item['dvc_code']); ?></td>
                            <?php foreach ($sizes as $sz) { ?>
                                <td style="text-align: center; vertical-align: top; padding-left: 8px;">
                                    <?php echo isset($item[$sz]) ? (int)$item[$sz] : 0; ?>
                                </td>
                            <?php } ?>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><strong><?php echo $subtotal; ?></strong></td>
                            <td style="text-align: center; vertical-align: top; padding-left: 8px;"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                                }
                            }
                            if ($row_display_no == 1) {
                        ?>
                        <tr>
                            
                        </tr>
                        <?php } ?>
                        <?php
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $column_totals_over_app['size_xs']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_s']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_m']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_l']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_xl']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_xxl']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_3xl']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_all']; ?></td>
                            <td align="center"><?php echo $column_totals_over_app['size_cus']; ?></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong><?php echo $grand_total_over_app; ?></strong></td>
                            <td align="center" rowspan="2" style="vertical-align: middle;"><strong>100%</strong></td>
                        </tr>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">PERSENTASE</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_xs'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_s'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_m'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_l'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_xl'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_xxl'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_3xl'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_all'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                            <td align="center"><?php echo $grand_total_over_app > 0 ? round(($column_totals_over_app['size_cus'] / $grand_total_over_app) * 100, 1) : 0; ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
</div>

    <!-- Vertical Separator -->
    <div class="table-separator"></div>

    <!-- Right Table - OSC Devices -->
    <div class="table-right">
        <div class="table-title">OSCILATOR</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Kode</th>
                            <th align="center">Subtotal</th>
                            <th align="center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($model_data_over_osc)) {
                            $osc_row_no = 1;
                            foreach ($model_data_over_osc as $osc_item) {
                                $subtotal = isset($osc_item['subtotal']) ? (int)$osc_item['subtotal'] : 0;
                                $percentage = $grand_total_over_osc > 0 ? round(($subtotal / $grand_total_over_osc) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td align="center"><?php echo $osc_row_no++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($osc_item['dvc_name']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($osc_item['dvc_code']); ?></td>
                            <td align="center"><strong><?php echo $subtotal; ?></strong></td>
                            <td align="center"><?php echo $percentage; ?>%</td>
                        </tr>
                        <?php
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><strong><?php echo $grand_total_over_osc; ?></strong></td>
                            <td align="center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
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

/* Side-by-side layout using flexbox */
.tables-container {
    display: flex;
    gap: 0; /* Remove gap, we'll use separator instead */
    align-items: flex-start;
    width: 100%;
}

.table-left {
    flex: 2;  /* Takes 2/3 of the space */
    min-width: 0; /* Allows table to shrink */
}

.table-right {
    flex: 1;  /* Takes 1/3 of the space */
    min-width: 0; /* Allows table to shrink */
}

/* Table titles */
.table-title {
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 15px;
    padding: 10px 15px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 8px 8px 0 0;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Table borders - right, left, bottom (no top) */
.card-table {
    border-right: 2px solid #e0e0e0;
    border-left: 2px solid #e0e0e0;
    border-bottom: 2px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background: white;
}

/* Vertical separator between tables */
.table-separator {
    width: 3px;
    background: linear-gradient(to bottom, #3498db, #2980b9, #3498db);
    margin: 0 15px;
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(52, 152, 219, 0.3);
}

/* Responsive design */
@media (max-width: 1200px) {
    .tables-container {
        flex-direction: column;
        gap: 20px;
    }
    
    .table-left, .table-right {
        flex: none;
        width: 100%;
    }
    
    .table-separator {
        width: 100%;
        height: 3px;
        margin: 10px 0;
        background: linear-gradient(to right, #3498db, #2980b9, #3498db);
    }
}
</style>

