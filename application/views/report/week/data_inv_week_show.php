<?php if (!empty($data)): ?>
<div class="card-table">
    <div class="table-responsive">
        <table class="table compact-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Week</th>
                    <th>Periode</th>
                    <th style="text-align: center;">Tanggal Mulai</th>
                    <th style="text-align: center;">Tanggal Selesai</th>
                    <th style="text-align: center;">Durasi (Hari)</th>
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
                    <td style="text-align: center; font-weight: bold;"><?= $row['period_w'] ?></td>
                    <td>
                        <strong><?= getMonthName($row['period_m']) ?> <?= $row['period_y'] ?></strong><br>
                        <small class="text-muted">Minggu ke-<?= $row['period_w'] ?></small>
                    </td>
                    <td style="text-align: center;">
                        <?= $start_date->format('d/m/Y H:i') ?><br>
                        <small class="text-muted"><?= getDayName($start_date->format('N')) ?></small>
                    </td>
                    <td style="text-align: center;">
                        <?= $finish_date->format('d/m/Y H:i') ?><br>
                        <small class="text-muted"><?= getDayName($finish_date->format('N')) ?></small>
                    </td>
                    <td style="text-align: center;">
                        <span class="duration-badge"><?= $duration ?> hari</span>
                    </td>
                    <td style="text-align: center;">
                        <button class="edit-btn <?= $can_edit ? '' : 'disabled' ?>" 
                                onclick="<?= $can_edit ? "editPeriod('{$row['id_week']}', '{$row['date_start']}', '{$row['date_finish']}')" : '' ?>"
                                title="<?= $can_edit ? 'Edit periode' : 'Tidak dapat edit di luar periode aktif' ?>">
                            ✏️
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="info-panel">
            <h5 class="info-title">Informasi Periode</h5>
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

<style>
.compact-table { font-size: 13px; }
.compact-table th, .compact-table td { padding: 8px 10px; line-height: 1.4; vertical-align: middle; }
.compact-table th { font-size: 14px; font-weight: 600; background-color: #f8f9fa; }
.text-muted { color: #6c757d; font-size: 12px; }
.duration-badge { background: #e3f2fd; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
.edit-btn { background: none; border: none; cursor: pointer; font-size: 16px; padding: 4px 8px; border-radius: 4px; transition: all 0.2s ease; }
.edit-btn:hover { background-color: rgba(0, 116, 217, 0.1); transform: scale(1.1); }
.edit-btn.disabled { cursor: not-allowed; opacity: 0.5; }
.edit-btn.disabled:hover { background-color: transparent; transform: none; }
.info-panel { margin-top: 15px; padding: 15px; background: #ffffff; border-radius: 6px; border: 1px solid #e9ecef; }
.info-title { margin: 0 0 10px 0; color: #333; font-size: 16px; font-weight: 600; }
.info-item { margin: 5px 0; font-size: 13px; color: #666; }
.no-data { text-align: center; padding: 40px; font-style: italic; color: #666; font-size: 18px; }
</style>
<?php endif; ?>

<?php
function getMonthName($month) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return isset($months[$month]) ? $months[$month] : $month;
}

function getDayName($dayNumber) {
    $days = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
    ];
    return isset($days[$dayNumber]) ? $days[$dayNumber] : '';
}
?>