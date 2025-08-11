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
    <?php if (!empty($current_filters['device_search'])): ?>
        <span class="badge badge-info" style="background: #17a2b8; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            Device: <?php echo htmlspecialchars($current_filters['device_search']); ?>
        </span>
    <?php endif; ?>
    <?php if (!empty($current_filters['year'])): ?>
        <span class="badge badge-secondary" style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            Year: <?php echo $current_filters['year']; ?>
        </span>
    <?php endif; ?>
    <?php if (!empty($current_filters['month'])): ?>
        <span class="badge badge-secondary" style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            Month: <?php echo $current_filters['month']; ?>
        </span>
    <?php endif; ?>
    <?php if (!empty($current_filters['week'])): ?>
        <span class="badge badge-secondary" style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 3px; margin-left: 5px;">
            Week: <?php echo $current_filters['week']; ?>
        </span>
    <?php endif; ?>
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
                    <th align="center">Size</th>
                    <th align="center">Color</th>
                    <th align="center">QC</th>
                    <th align="center">Stock</th>
                    <th align="center">On PMS</th>
                    <th align="center">Needs</th>
                    <th align="center">Order</th>
                    <th align="center">Over</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($report_data)) {
                    $no = 1;
                    foreach ($report_data as $row) {
                        // Format week display
                        $week_display = 'W' . $row['period_w'] . '/' . $row['period_m'] . '/' . $row['period_y'];
                        $week_detail = 'Period: ' . $row['date_start'] . ' to ' . $row['date_finish'];
                        
                        // Format device display
                        $device_display = htmlspecialchars($row['dvc_code']);
                        $device_detail = htmlspecialchars($row['dvc_name']);
                        
                        // Format color display
                        $color_display = $row['dvc_col'] === '' ? '-' : htmlspecialchars($row['dvc_col']);
                ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center" title="<?php echo $week_detail; ?>" style="cursor: help;">
                        <?php echo $week_display; ?>
                    </td>
                    <td align="center" title="<?php echo $device_detail; ?>" style="cursor: help;">
                        <?php echo $device_display; ?>
                    </td>
                    <td align="center"><?php echo strtoupper($row['dvc_size']); ?></td>
                    <td align="center"><?php echo $color_display; ?></td>
                    <td align="center"><?php echo htmlspecialchars($row['dvc_qc']); ?></td>
                    <td align="center"><?php echo intval($row['stock']); ?></td>
                    <td align="center"><?php echo intval($row['on_pms']); ?></td>
                    <td align="center"><?php echo intval($row['needs']); ?></td>
                    <td align="center"><?php echo intval($row['order']); ?></td>
                    <td align="center"><?php echo intval($row['over']); ?></td>
                </tr>
                <?php 
                    }
                } else { 
                ?>
                <tr>
                    <td align="center" colspan="11"><i>No Report Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #00bfff; color: white; font-weight: bold;">
                    <td align="center" colspan="6">TOTAL</td>
                    <td align="center">
                        <?php 
                        $total_stock = 0;
                        foreach ($report_data as $row) {
                            $total_stock += intval($row['stock']);
                        }
                        echo $total_stock;
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                        $total_on_pms = 0;
                        foreach ($report_data as $row) {
                            $total_on_pms += intval($row['on_pms']);
                        }
                        echo $total_on_pms;
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                        $total_needs = 0;
                        foreach ($report_data as $row) {
                            $total_needs += intval($row['needs']);
                        }
                        echo $total_needs;
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                        $total_order = 0;
                        foreach ($report_data as $row) {
                            $total_order += intval($row['order']);
                        }
                        echo $total_order;
                        ?>
                    </td>
                    <td align="center">
                        <?php 
                        $total_over = 0;
                        foreach ($report_data as $row) {
                            $total_over += intval($row['over']);
                        }
                        echo $total_over;
                        ?>
                    </td>
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

/* Tooltip styling */
td[title]:hover {
    background-color: #e3f2fd !important;
}
</style>