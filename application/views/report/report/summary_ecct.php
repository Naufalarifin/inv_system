<?php
// Load model
$this->load->model('report_model');

// Build filters from current_filters passed by controller (year/month/week or id_week)
$filters = array();
if (isset($current_filters['id_week']) && $current_filters['id_week']) {
    $filters['id_week'] = $current_filters['id_week'];
}
if (isset($current_filters['year']) && $current_filters['year'] !== '') {
    $filters['year'] = $current_filters['year'];
}
if (isset($current_filters['month']) && $current_filters['month'] !== '') {
    $filters['month'] = $current_filters['month'];
}
if (isset($current_filters['week']) && $current_filters['week'] !== '') {
    $filters['week'] = $current_filters['week'];
}
// Fallback to current week when no period filters supplied
if (empty($filters) && isset($current_week['id_week']) && $current_week['id_week']) {
    $filters['id_week'] = $current_week['id_week'];
}

// Get aggregated inv_report for ECCT (already grouped by week+code+size+color+qc)
$rows = $this->report_model->getInventoryReportData('ecct', null, $filters);

// Helper functions
// Helper: use simple functions (compatible with PHP 5)
function ecct_calc_order($needs, $on_pms, $stock) { $d = (int)$needs - (int)$on_pms - (int)$stock; return ($d >= 0) ? $d : 0; }
function ecct_calc_over($needs, $on_pms, $stock)  { $d = (int)$needs - (int)$on_pms - (int)$stock; return ($d < 0) ? abs($d) : 0; }
function ecct_calc_pct($needs, $stock)            { return ((int)$needs > 0) ? round(((int)$stock / (int)$needs) * 100) : 100; }

// ===================== APP (merge LN+DN and colors) =====================
$appIndex = array();
foreach ($rows as $r) {
    if (strtoupper($r['dvc_type']) !== 'APP') continue;
    $code = $r['dvc_code'];
    $name = $r['dvc_name'];
    $priority = isset($r['dvc_priority']) ? (int)$r['dvc_priority'] : 999;
    $size = strtoupper(trim($r['dvc_size'])) ?: '-';
    if (!isset($appIndex[$code])) {
        $appIndex[$code] = array('dvc_code' => $code, 'dvc_name' => $name, 'dvc_priority' => $priority, 'sizes' => array());
    }
    if (!isset($appIndex[$code]['sizes'][$size])) {
        $appIndex[$code]['sizes'][$size] = array('stock' => 0, 'on_pms' => 0, 'needs' => 0);
    }
    $appIndex[$code]['sizes'][$size]['stock'] += (int)$r['stock'];
    $appIndex[$code]['sizes'][$size]['on_pms'] += (int)$r['on_pms'];
    $appIndex[$code]['sizes'][$size]['needs'] += (int)$r['needs'];
}
$appItems = array_values($appIndex);
// Sort by dvc_priority ASC first, then by name
usort($appItems, function($a, $b) { 
    if ($a['dvc_priority'] != $b['dvc_priority']) {
        return $a['dvc_priority'] - $b['dvc_priority'];
    }
    return strcasecmp($a['dvc_name'].'|'.$a['dvc_code'], $b['dvc_name'].'|'.$b['dvc_code']); 
});
$splitIndex = (count($appItems) > 0) ? (int)ceil(count($appItems) / 2) : 0;
$appLeft  = array_slice($appItems, 0, $splitIndex);
$appRight = array_slice($appItems, $splitIndex);

// ===================== OSC (split LN / DN, merge sizes/colors) =====================
$oscLN = array();
$oscDN = array();
foreach ($rows as $r) {
    if (strtoupper($r['dvc_type']) !== 'OSC') continue;
    // Choose destination array by QC without using reference on ternary (PHP 5 compatibility)
    if (strtoupper(trim($r['dvc_qc'])) === 'DN') {
        $dest =& $oscDN;
    } else {
        $dest =& $oscLN; // default LN when not DN
    }
    $code = $r['dvc_code'];
    $priority = isset($r['dvc_priority']) ? (int)$r['dvc_priority'] : 999;
    if (!isset($dest[$code])) {
        $dest[$code] = array('dvc_code' => $code, 'dvc_name' => $r['dvc_name'], 'dvc_priority' => $priority, 'stock' => 0, 'on_pms' => 0, 'needs' => 0);
    }
    $dest[$code]['stock'] += (int)$r['stock'];
    $dest[$code]['on_pms'] += (int)$r['on_pms'];
    $dest[$code]['needs'] += (int)$r['needs'];
}
// PHP 5 compatible comparator (no closures with external scope required)
function ecct_osc_sort_cmp($a, $b) {
    if ($a['dvc_priority'] != $b['dvc_priority']) {
        return $a['dvc_priority'] - $b['dvc_priority'];
    }
    return strcasecmp($a['dvc_name'].'|'.$a['dvc_code'], $b['dvc_name'].'|'.$b['dvc_code']);
}
$oscLNItems = array_values($oscLN); usort($oscLNItems, 'ecct_osc_sort_cmp');
$oscDNItems = array_values($oscDN); usort($oscDNItems, 'ecct_osc_sort_cmp');
?>

<div id="summary_ecct_table" class="ecct-summary-wrapper">

<!-- ================= TOP: APP tables (right continues left) ================= -->
<div class="tables-container">
    <?php $sumLN = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0); $sumDN = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0); ?>
    <div class="table-left">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Apparel</th>
                            <th align="center">Size</th>
                            <th align="center">Stock</th>
                            <th align="center">OnPMS</th>
                            <th align="center">Needs</th>
                            <th align="center">Order</th>
                            <th align="center">Over</th>
                            <th align="center">%Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $totL = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0);
                        $sizesOrder = array('XS','S','M','L','XL','XXL','3XL','ALL','CUS','-');
                        foreach ($appLeft as $g) {
                            // Build size rows and skip sizes where all metrics are zero
                            $filtered = array();
                            foreach ($g['sizes'] as $sz => $v) {
                                $stock=(int)$v['stock']; $onp=(int)$v['on_pms']; $need=(int)$v['needs'];
                                $order=ecct_calc_order($need,$onp,$stock); $over=ecct_calc_over($need,$onp,$stock); $pct=ecct_calc_pct($need,$stock);
                                if ($stock==0 && $onp==0 && $need==0 && $order==0 && $over==0) { continue; }
                                $filtered[$sz] = array('stock'=>$stock,'on_pms'=>$onp,'needs'=>$need,'order'=>$order,'over'=>$over,'pct'=>$pct);
                            }
                            if (empty($filtered)) { continue; }
                            uksort($filtered, function($a,$b) use($sizesOrder){ $ia=array_search(strtoupper($a),$sizesOrder); $ia=($ia===false?999:$ia); $ib=array_search(strtoupper($b),$sizesOrder); $ib=($ib===false?999:$ib); return $ia-$ib; });
                            $rowspan = count($filtered);
                            $first = true;
                            foreach ($filtered as $sz=>$vals) {
                                $totL['stock'] += $vals['stock'];
                                $totL['on_pms'] += $vals['on_pms'];
                                $totL['needs']  += $vals['needs'];
                                $totL['order']  += $vals['order'];
                                $totL['over']   += $vals['over'];
                                ?>
                                <tr>
                                    <?php if ($first) { $first=false; ?>
                                        <td align="center" rowspan="<?php echo $rowspan; ?>"><?php echo $no++; ?></td>
                                        <td align="left" rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($g['dvc_code']); ?></td>
                                    <?php } ?>
                                    <td align="center"><?php echo htmlspecialchars($sz); ?></td>
                                    <?php $isAllZero = ($vals['stock']==0 && $vals['on_pms']==0 && $vals['needs']==0 && $vals['order']==0 && $vals['over']==0); ?>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['stock']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['on_pms']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['needs']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['order']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['over']; ?></td>
                                    <?php $pct_style = ($vals['pct'] < 50) ? 'background-color:#d32f2f;color:#fff;font-weight:bold;' : ''; ?>
                                    <td align="center" style="<?php echo $pct_style; ?>"><?php echo $vals['pct']; ?>%</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-separator"></div>

    <div class="table-right">
        <div class="table-title">APPAREL</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Apparel</th>
                            <th align="center">Size</th>
                            <th align="center">Stock</th>
                            <th align="center">OnPMS</th>
                            <th align="center">Needs</th>
                            <th align="center">Order</th>
                            <th align="center">Over</th>
                            <th align="center">%Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totR = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0);
                        $sizesOrder = array('XS','S','M','L','XL','XXL','3XL','ALL','CUS','-');
                        foreach ($appRight as $g) {
                            // Build size rows and skip sizes where all metrics are zero
                            $filtered = array();
                            foreach ($g['sizes'] as $sz => $v) {
                                $stock=(int)$v['stock']; $onp=(int)$v['on_pms']; $need=(int)$v['needs'];
                                $order=ecct_calc_order($need,$onp,$stock); $over=ecct_calc_over($need,$onp,$stock); $pct=ecct_calc_pct($need,$stock);
                                if ($stock==0 && $onp==0 && $need==0 && $order==0 && $over==0) { continue; }
                                $filtered[$sz] = array('stock'=>$stock,'on_pms'=>$onp,'needs'=>$need,'order'=>$order,'over'=>$over,'pct'=>$pct);
                            }
                            if (empty($filtered)) { continue; }
                            uksort($filtered, function($a,$b) use($sizesOrder){ $ia=array_search(strtoupper($a),$sizesOrder); $ia=($ia===false?999:$ia); $ib=array_search(strtoupper($b),$sizesOrder); $ib=($ib===false?999:$ib); return $ia-$ib; });
                            $rowspan = count($filtered);
                            $first = true;
                            foreach ($filtered as $sz=>$vals) {
                                $totR['stock'] += $vals['stock'];
                                $totR['on_pms'] += $vals['on_pms'];
                                $totR['needs']  += $vals['needs'];
                                $totR['order']  += $vals['order'];
                                $totR['over']   += $vals['over'];
                                ?>
                                <tr>
                                    <?php if ($first) { $first=false; ?>
                                        <td align="center" rowspan="<?php echo $rowspan; ?>"><?php echo $no++; ?></td>
                                        <td align="left" rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($g['dvc_code']); ?></td>
                                    <?php } ?>
                                    <td align="center"><?php echo htmlspecialchars($sz); ?></td>
                                    <?php $isAllZero = ($vals['stock']==0 && $vals['on_pms']==0 && $vals['needs']==0 && $vals['order']==0 && $vals['over']==0); ?>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['stock']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['on_pms']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['needs']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['order']; ?></td>
                                    <td align="center"><?php echo $isAllZero ? '' : $vals['over']; ?></td>
                                    <?php $pct_style = ($vals['pct'] < 50) ? 'background-color:#d32f2f;color:#fff;font-weight:bold;' : ''; ?>
                                    <td align="center" style="<?php echo $pct_style; ?>"><?php echo $vals['pct']; ?>%</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#00bfff;color:#fff;font-weight:bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $totL['stock'] + $totR['stock']; ?></td>
                            <td align="center"><?php echo $totL['on_pms'] + $totR['on_pms']; ?></td>
                            <td align="center"><?php echo $totL['needs'] + $totR['needs']; ?></td>
                            <td align="center"><?php echo $totL['order'] + $totR['order']; ?></td>
                            <td align="center"><?php echo $totL['over'] + $totR['over']; ?></td>
                            <td align="center">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= BOTTOM: OSC LN (left) and DN (right) ================= -->
<div class="filter-info" style="margin:20px 0 10px 0;"><strong>Oscillator</strong></div>

<div class="tables-container">
    <div class="table-left">
        <div class="table-title">Oscillator LN</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Stock</th>
                            <th align="center">OnPMS</th>
                            <th align="center">Needs</th>
                            <th align="center">Order</th>
                            <th align="center">Over</th>
                            <th align="center">%Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $noLN = 1; $sumLN = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0);
                        foreach ($oscLNItems as $g) { $stock=(int)$g['stock']; $onp=(int)$g['on_pms']; $need=(int)$g['needs']; $order=ecct_calc_order($need,$onp,$stock); $over=ecct_calc_over($need,$onp,$stock); $pct=ecct_calc_pct($need,$stock); if ($stock==0 && $onp==0 && $need==0 && $order==0 && $over==0) { continue; } $sumLN['stock']+=$stock; $sumLN['on_pms']+=$onp; $sumLN['needs']+=$need; $sumLN['order']+=$order; $sumLN['over']+=$over; ?>
                        <tr>
                            <td align="center"><?php echo $noLN++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($g['dvc_code']); ?></td>
                            <td align="center"><?php echo $stock; ?></td>
                            <td align="center"><?php echo $onp; ?></td>
                            <td align="center"><?php echo $need; ?></td>
                            <td align="center"><?php echo $order; ?></td>
                            <td align="center"><?php echo $over; ?></td>
                            <?php $pct_style = ($pct < 50) ? 'background-color:#d32f2f;color:#fff;font-weight:bold;' : ''; ?>
                            <td align="center" style="<?php echo $pct_style; ?>"><?php echo $pct; ?>%</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <!-- No TOTAL on the left (LN) as requested -->
                </table>
            </div>
        </div>
    </div>

    <div class="table-separator"></div>

    <div class="table-right">
        <div class="table-title">Oscillator DN</div>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-border align-middle text-gray-700 text-s compact-table">
                    <thead>
                        <tr>
                            <th align="center">No</th>
                            <th align="center">Oscillator</th>
                            <th align="center">Stock</th>
                            <th align="center">OnPMS</th>
                            <th align="center">Needs</th>
                            <th align="center">Order</th>
                            <th align="center">Over</th>
                            <th align="center">%Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $noDN = 1; $sumDN = array('stock'=>0,'on_pms'=>0,'needs'=>0,'order'=>0,'over'=>0);
                        foreach ($oscDNItems as $g) { $stock=(int)$g['stock']; $onp=(int)$g['on_pms']; $need=(int)$g['needs']; $order=ecct_calc_order($need,$onp,$stock); $over=ecct_calc_over($need,$onp,$stock); $pct=ecct_calc_pct($need,$stock); if ($stock==0 && $onp==0 && $need==0 && $order==0 && $over==0) { continue; } $sumDN['stock']+=$stock; $sumDN['on_pms']+=$onp; $sumDN['needs']+=$need; $sumDN['order']+=$order; $sumDN['over']+=$over; ?>
                        <tr>
                            <td align="center"><?php echo $noDN++; ?></td>
                            <td align="left"><?php echo htmlspecialchars($g['dvc_code']); ?></td>
                            <td align="center"><?php echo $stock; ?></td>
                            <td align="center"><?php echo $onp; ?></td>
                            <td align="center"><?php echo $need; ?></td>
                            <td align="center"><?php echo $order; ?></td>
                            <td align="center"><?php echo $over; ?></td>
                            <?php $pct_style = ($pct < 50) ? 'background-color:#d32f2f;color:#fff;font-weight:bold;' : ''; ?>
                            <td align="center" style="<?php echo $pct_style; ?>"><?php echo $pct; ?>%</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <?php $totalOSC = array(
                            'stock' => $sumLN['stock'] + $sumDN['stock'],
                            'on_pms' => $sumLN['on_pms'] + $sumDN['on_pms'],
                            'needs' => $sumLN['needs'] + $sumDN['needs'],
                            'order' => $sumLN['order'] + $sumDN['order'],
                            'over'  => $sumLN['over']  + $sumDN['over']
                        ); ?>
                        <tr style="background-color:#00bfff;color:#fff;font-weight:bold;">
                            <td align="center" colspan="3">TOTAL</td>
                            <td align="center"><?php echo $totalOSC['stock']; ?></td>
                            <td align="center"><?php echo $totalOSC['on_pms']; ?></td>
                            <td align="center"><?php echo $totalOSC['needs']; ?></td>
                            <td align="center"><?php echo $totalOSC['order']; ?></td>
                            <td align="center"><?php echo $totalOSC['over']; ?></td>
                            <td align="center">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- end summary wrapper -->
</div>

<style>
/* Scope all styles under the ECCT wrapper to avoid affecting other views */
#summary_ecct_table.ecct-summary-wrapper { padding: 8px; border: 1px dashed #dee2e6; border-radius: 6px; background: #ffffff; max-height: none; overflow: visible; }
#summary_ecct_table .compact-table {
    font-size: 12px !important;
    table-layout: fixed;
}
#summary_ecct_table .compact-table th,
#summary_ecct_table .compact-table td {
    padding: 2px 2px !important;
    line-height: 1.1 !important;
    text-align: center;
    white-space: nowrap;
}
#summary_ecct_table .compact-table td:nth-child(2),
#summary_ecct_table .compact-table th:nth-child(2) {
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
}
#summary_ecct_table .compact-table th {
    font-size: 15px !important;
    background-color: #f8f9fa;
    text-align: center;
}
#summary_ecct_table .compact-table tfoot td {
    font-size: 10px !important;
    text-transform: none;
}
#summary_ecct_table .tables-container { display: flex; gap: 0; align-items: flex-start; width: 100%; }
#summary_ecct_table .table-left { flex: 1; min-width: 0; }
#summary_ecct_table .table-right { flex: 1; min-width: 0; }
#summary_ecct_table .table-separator { width: 3px; background: linear-gradient(to bottom, #3498db, #2980b9, #3498db); margin: 0 12px; border-radius: 2px; box-shadow: 0 0 8px rgba(52,152,219,.3); }
#summary_ecct_table .table-title { font-size: 14px; font-weight: 700; color: #2c3e50; margin-bottom: 8px; padding: 6px 10px; background: linear-gradient(135deg, #3498db, #2980b9); color: #fff; border-radius: 8px 8px 0 0; text-align: center; }
#summary_ecct_table .card-table { border-right: 2px solid #e0e0e0; border-left: 2px solid #e0e0e0; border-bottom: 2px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px; background: #fff; }
@media (max-width: 1200px) { 
  #summary_ecct_table .tables-container { flex-direction: column; gap: 16px; }
  #summary_ecct_table .table-separator { width: 100%; height: 3px; margin: 8px 0; background: linear-gradient(to right, #3498db, #2980b9, #3498db); }
}
</style>