<?php
$report_data = isset($data) && is_array($data) ? $data : array();
$current_week = isset($current_week) ? $current_week : null;
$current_filters = isset($current_filters) ? $current_filters : array();
?>

<!-- Filter Info Display -->
<?php if ($current_week || !empty(array_filter($current_filters))): ?>
<div class="filter-info" style="background: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #e9ecef;">
    <strong>Current Filters:</strong>
    <?php if ($current_week): ?>
        <span class="badge badge-primary" style="background: #007bff; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            Current Week: W<?php echo $current_week['period_w']; ?>/<?php echo $current_week['period_m']; ?>/<?php echo $current_week['period_y']; ?>
        </span>
    <?php endif; ?>
    <?php 
    $filter_labels = [
        'device_search' => 'Device',
        'year' => 'Year', 
        'month' => 'Month',
        'week' => 'Week'
    ];
    
    foreach ($filter_labels as $key => $label):
        if (!empty($current_filters[$key])):
    ?>
        <span class="badge badge-secondary" style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            <?php echo $label; ?>: <?php echo htmlspecialchars($current_filters[$key]); ?>
        </span>
    <?php 
        endif;
    endforeach; 
    ?>
</div>
<?php endif; ?>

<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 text-s compact-table">
            <thead>
                <tr>
                    <th align="center">No</th>
                    <th align="center">Week</th>
                    <th align="center">Device</th>
                    <th align="center">QC</th>
                    <th align="center">Stock</th>
                    <th align="center">On PMS</th>
                    <th align="center">Needs</th>
                    <th align="center">Order</th>
                    <th align="center">Over</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Initialize totals array first
                $totals = ['stock' => 0, 'on_pms' => 0, 'needs' => 0, 'order' => 0, 'over' => 0];
                
                if (!empty($report_data)): 
                    $no = 1;
                    
                    foreach ($report_data as $row): 
                        // Format week display
                        $week_display = 'W' . $row['period_w'] . '/' . $row['period_m'] . '/' . $row['period_y'];
                        $week_detail = 'Period: ' . $row['date_start'] . ' to ' . $row['date_finish'];
                        
                        // Format device display
                        $device_display = htmlspecialchars($row['dvc_code']);
                        $device_detail = htmlspecialchars($row['dvc_name']);
                        
                        // Calculate totals
                        $totals['stock'] += intval($row['stock']);
                        $totals['on_pms'] += intval($row['on_pms']);
                        $totals['needs'] += intval($row['needs']);
                        $totals['order'] += intval($row['order']);
                        $totals['over'] += intval($row['over']);
                ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center" title="<?php echo $week_detail; ?>" style="cursor: help;">
                        <?php echo $week_display; ?>
                    </td>
                    <td align="center" title="<?php echo $device_detail; ?>" style="cursor: help;">
                        <?php echo $device_display; ?>
                    </td>
                    <td align="center"><?php echo htmlspecialchars($row['dvc_qc']); ?></td>
                    <td align="center"><?php echo intval($row['stock']); ?></td>
                    <td align="center"><?php echo intval($row['on_pms']); ?></td>
                    <td align="center"><?php echo intval($row['needs']); ?></td>
                    <td align="center"><?php echo intval($row['order']); ?></td>
                    <td align="center"><?php echo intval($row['over']); ?></td>
                </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                <tr>
                    <td align="center" colspan="9">
                        <div style="text-align: center; padding: 40px; color: #666; font-style: italic;">
                            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                            No OSC Report Data Found
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="4">TOTAL</td>
                    <td align="center"><?php echo $totals['stock']; ?></td>
                    <td align="center"><?php echo $totals['on_pms']; ?></td>
                    <td align="center"><?php echo $totals['needs']; ?></td>
                    <td align="center"><?php echo $totals['order']; ?></td>
                    <td align="center"><?php echo $totals['over']; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
.compact-table {
    font-size: 13px !important;
}

.compact-table th,
.compact-table td {
    padding: 8px 4px !important;
    line-height: 1.4 !important;
}

.compact-table th {
    font-size: 14px !important;
    background-color: #f8f9fa;
}

.compact-table tbody tr:hover {
    background-color: #f5f5f5;
}

td[title]:hover {
    background-color: #e3f2fd !important;
}
</style>