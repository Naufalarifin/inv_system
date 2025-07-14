<div class="card-table">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 font-medium text-sm">
            <thead>
                <tr>
                    <th align="center" width="40">No</th>
                    <th align="center" width="120">Date & Time</th>
                    <th align="center" width="100">Activity</th>
                    <th align="center" width="90">Device SN</th>
                    <th align="center" width="150">Device Name</th>
                    <th align="center" width="60">Size</th>
                    <th align="center" width="80">Admin</th>
                    <th align="center" width="100">Location</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 0;
                if(isset($data['data']) && !empty($data['data'])) {
                    foreach ($data['data'] as $row) {
                        $no++;
                ?>
                <tr>
                    <td align="center"><?php echo $no; ?></td>
                    <td align="center"><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                    <td align="center">
                        <?php 
                        $badge_class = '';
                        switch($row['activity_type']) {
                            case 'IN': 
                                $badge_class = 'badge-success';
                                break;
                            case 'OUT': 
                                $badge_class = 'badge-danger';
                                break;
                            case 'MOVE': 
                                $badge_class = 'badge-warning';
                                break;
                            case 'RELEASE': 
                                $badge_class = 'badge-info';
                                break;
                            default: 
                                $badge_class = 'badge-secondary';
                        }
                        ?>
                        <span class="badge badge-sm <?php echo $badge_class; ?> badge-outline">
                            <?php echo $row['activity_type']; ?>
                        </span>
                    </td>
                    <td align="center"><strong><?php echo $row['dvc_sn']; ?></strong></td>
                    <td align="center"><?php echo isset($row['dvc_name']) ? $row['dvc_name'] : 'N/A'; ?></td>
                    <td align="center">
                        <?php if ($row['dvc_size'] == 'S') { ?>
                            <span class="badge badge-sm badge-success badge-outline">S</span>
                        <?php } elseif ($row['dvc_size'] == 'M') { ?>
                            <span class="badge badge-sm badge-warning badge-outline">M</span>
                        <?php } elseif ($row['dvc_size'] == 'L') { ?>
                            <span class="badge badge-sm badge-danger badge-outline">L</span>
                        <?php } else { ?>
                            <span class="badge badge-sm badge-light badge-outline"><?php echo $row['dvc_size']; ?></span>
                        <?php } ?>
                    </td>
                    <td align="center"><?php echo $row['admin'] ? $row['admin'] : '-'; ?></td>
                    <td align="center">
                        <?php 
                        if (isset($row['location']) && $row['location']) {
                            if ($row['location'] == '1') {
                                echo '<span class="badge badge-sm badge-primary badge-outline">WH A</span>';
                            } elseif ($row['location'] == '2') {
                                echo '<span class="badge badge-sm badge-info badge-outline">WH B</span>';
                            } elseif ($row['location'] == '3') {
                                echo '<span class="badge badge-sm badge-success badge-outline">Lab</span>';
                            } else {
                                echo '<span class="badge badge-sm badge-light badge-outline">' . $row['location'] . '</span>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td align="center" colspan="8"><i>No History Data Found</i></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if (isset($data['page']['sum']) && $data['page']['sum'] > $data['page']['show']) { ?>
<div class="card-footer justify-center">
    <div class="pagination">
        <?php
        $total_pages = ceil($data['page']['sum'] / $data['page']['show']);
        $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
        
        // Determine which function to call based on activity type
        $js_function = 'showAllHistory';
        if (isset($_GET['activity_type'])) {
            switch($_GET['activity_type']) {
                case 'IN':
                    $js_function = 'showInHistory';
                    break;
                case 'OUT':
                    $js_function = 'showOutHistory';
                    break;
                case 'MOVE':
                    $js_function = 'showMoveHistory';
                    break;
                case 'RELEASE':
                    $js_function = 'showReleaseHistory';
                    break;
                default:
                    $js_function = 'showAllHistory';
            }
        }
        
        // Previous button
        if ($current_page > 0) {
            echo '<a href="javascript:' . $js_function . '(' . ($current_page - 1) . ')" class="btn btn-sm btn-light">Previous</a>';
        }
        
        // Page numbers
        $start_page = max(0, $current_page - 2);
        $end_page = min($total_pages - 1, $current_page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="btn btn-sm btn-primary">' . ($i + 1) . '</span>';
            } else {
                echo '<a href="javascript:' . $js_function . '(' . $i . ')" class="btn btn-sm btn-light">' . ($i + 1) . '</a>';
            }
        }
        
        // Next button
        if ($current_page < $total_pages - 1) {
            echo '<a href="javascript:' . $js_function . '(' . ($current_page + 1) . ')" class="btn btn-sm btn-light">Next</a>';
        }
        ?>
    </div>
</div>
<?php } ?>

<style>
.card-table {
    max-width: 1100px;
    margin: 0 auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 16px 8px;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.table-responsive table {
    min-width: 900px;
    width: 100%;
    white-space: nowrap;
}

.table-responsive td, .table-responsive th {
    padding: 8px 6px;
    font-size: 12px;
    text-align: center;
}

.badge-sm {
    font-size: 10px;
    padding: 2px 6px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 15px;
}

.pagination .btn {
    margin: 0 2px;
}
</style>
