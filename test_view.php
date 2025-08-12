<?php
// Test file untuk memverifikasi view data_inv_week_show.php
echo "<h1>Test View data_inv_week_show.php</h1>";

// Simulasi data yang seharusnya dikirim dari controller
$data = [
    [
        'id_week' => 1,
        'period_w' => 1,
        'period_m' => 12,
        'period_y' => 2024,
        'date_start' => '2024-11-27 08:00:00',
        'date_finish' => '2024-12-01 17:00:00'
    ],
    [
        'id_week' => 2,
        'period_w' => 2,
        'period_m' => 12,
        'period_y' => 2024,
        'date_start' => '2024-12-02 08:00:00',
        'date_finish' => '2024-12-06 17:00:00'
    ]
];

echo "<h2>Test Data:</h2>";
echo "<pre>" . print_r($data, true) . "</pre>";

echo "<h2>Rendering View:</h2>";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px;'>";

// Include helper functions yang dibutuhkan
function getMonthName($month) {
    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 
               7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    return isset($months[$month]) ? $months[$month] : $month;
}

function getDayName($dayNumber) {
    $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    return isset($days[$dayNumber]) ? $days[$dayNumber] : '';
}

// Render view dengan data test
if (!empty($data)): ?>
<div class="card-table">
    <div class="table-responsive">
        <table class="table compact-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Week</th>
                    <th>Periode</th>
                    <th style="text-align: center;">Tanggal Mulai</th>
                    <th style="text-align: center;">Tanggal Selesai</th>
                    <th style="text-align: center;">Durasi</th>
                    <th style="width: 100px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $month_start = new DateTime($data[0]['date_start']);
                $month_end = new DateTime(end($data)['date_finish']);
                $current_date = new DateTime();
                $can_edit = ($current_date >= $month_start && $current_date <= $month_end);
                
                foreach ($data as $row): 
                    $start_date = new DateTime($row['date_start']);
                    $finish_date = new DateTime($row['date_finish']);
                    $duration = $start_date->diff($finish_date)->days + 1;
                ?>
                <tr>
                    <td class="text-center fw-bold"><?= $row['period_w'] ?></td>
                    <td>
                        <strong><?= getMonthName($row['period_m']) ?> <?= $row['period_y'] ?></strong><br>
                        <small class="text-muted">Minggu ke-<?= $row['period_w'] ?></small>
                    </td>
                    <td class="text-center">
                        <?= $start_date->format('d/m/Y H:i') ?><br>
                        <small class="text-muted"><?= getDayName($start_date->format('N')) ?></small>
                    </td>
                    <td class="text-center">
                        <?= $finish_date->format('d/m/Y H:i') ?><br>
                        <small class="text-muted"><?= getDayName($finish_date->format('N')) ?></small>
                    </td>
                    <td class="text-center">
                        <span class="duration-badge"><?= $duration ?> hari</span>
                    </td>
                    <td class="text-center">
                        <button class="edit-btn <?= $can_edit ? '' : 'disabled' ?>" 
                                onclick="<?= $can_edit ? "editPeriod('{$row['id_week']}', '{$row['date_start']}', '{$row['date_finish']}')" : '' ?>"
                                title="<?= $can_edit ? 'Edit periode' : 'Tidak dapat edit di luar periode aktif' ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.50023C18.8978 2.10244 19.4374 1.87891 20 1.87891C20.5626 1.87891 21.1022 2.10244 21.5 2.50023C21.8978 2.89801 22.1213 3.43762 22.1213 4.00023C22.1213 4.56284 21.8978 5.10244 21.5 5.50023L12 15.0002L8 16.0002L9 12.0002L18.5 2.50023Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="info-toggle-container">
            <button class="info-toggle-btn" onclick="toggleInfoPanel()">
                <span>Informasi Periode</span>
                <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="info-panel" id="infoPanel">
                <h5>Informasi Periode</h5>
                <div class="info-item">
                    <strong>Total Minggu:</strong> <?= count($data) ?> minggu
                </div>
                <div class="info-item">
                    <strong>Periode Penuh:</strong> 
                    <?= $month_start->format('d/m/Y H:i') ?> - 
                    <?= $month_end->format('d/m/Y H:i') ?>
                </div>
                <div class="info-item">
                    <strong>Catatan:</strong> Periode dimulai dari tanggal 27 bulan sebelumnya (08:00) hingga tanggal 26 bulan ini (17:00)
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.compact-table { font-size: 13px; }
.compact-table th, .compact-table td { padding: 8px 10px; line-height: 1.4; vertical-align: middle; }
.compact-table th { font-size: 14px; font-weight: 600; background-color: #f8f9fa; }
.text-center { text-align: center; }
.fw-bold { font-weight: bold; }
.text-muted { color: #6c757d; font-size: 12px; }
.duration-badge { background: #e3f2fd; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
.edit-btn { background: none; border: none; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: all 0.2s ease; }
.edit-btn:hover { background-color: rgba(0, 116, 217, 0.1); transform: scale(1.1); }
.edit-btn.disabled { cursor: not-allowed; opacity: 0.5; }
.edit-btn.disabled:hover { background-color: transparent; transform: none; }
.info-toggle-container { margin-top: 15px; }
.info-toggle-btn { width: 100%; padding: 5px 15px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease; font-size: 14px; font-weight: 500; color: #333;}
.info-toggle-btn:hover { background: #f8f9fa; border-color: #0074d9; }
.info-toggle-btn:focus { outline: none; box-shadow: 0 0 0 2px rgba(0,116,217,0.2); }
.toggle-icon { transition: transform 0.3s ease; color: #666; }
.toggle-icon.active { transform: rotate(180deg); color: #0074d9; }
.info-panel { margin-top: 0; padding: 0; background: #fff; border: 1px solid #e9ecef; border-top: none; border-radius: 0 0 6px 6px; max-height: 0; overflow: hidden; opacity: 0; transition: all 0.3s ease; }
.info-panel.active { max-height: 200px; padding: 15px; opacity: 1; }
.info-panel h5 { margin: 0 0 10px 0; color: #333; font-size: 16px; font-weight: 600; }
.info-item { margin: 5px 0; font-size: 13px; color: #666; }
</style>
<?php else: ?>
<div class="no-data" style="text-align: center; padding: 40px; font-style: italic; color: #666; font-size: 18px;">
    No Data Available
</div>
<?php endif; ?>

</div>

<script>
function toggleInfoPanel() {
    const infoPanel = document.getElementById('infoPanel');
    const toggleIcon = document.querySelector('.toggle-icon');
    
    if (infoPanel && toggleIcon) {
        infoPanel.classList.toggle('active');
        toggleIcon.classList.toggle('active');
    }
}

function editPeriod(id, start, finish) {
    alert('Edit Period: ' + id + '\nStart: ' + start + '\nFinish: ' + finish);
}
</script>
