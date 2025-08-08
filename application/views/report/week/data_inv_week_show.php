<?php if (!empty($data)): ?>
<div class="card-table" style="margin-top: 0;">
    <div class="table-responsive">
        <table class="table table-border align-middle text-gray-700 text-s compact-table">
            <thead>
                <tr>
                    <th align="center" style="width: 80px;">Week</th>
                    <th align="center">Periode</th>
                    <th align="center">Tanggal Mulai</th>
                    <th align="center">Tanggal Selesai</th>
                    <th align="center">Durasi (Hari)</th>
                    <th align="center" style="width: 100px;">Action</th>
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
                    <td align="center" style="font-weight: bold;"><?= $row['period_w'] ?></td>
                    <td align="left">
                        <strong><?= getMonthName($row['period_m']) ?> <?= $row['period_y'] ?></strong><br>
                        <small style="color: #666;">Minggu ke-<?= $row['period_w'] ?></small>
                    </td>
                    <td align="center">
                        <?= $start_date->format('d/m/Y H:i') ?><br>
                        <small style="color: #666;"><?= getDayName($start_date->format('N')) ?></small>
                    </td>
                    <td align="center">
                        <?= $finish_date->format('d/m/Y H:i') ?><br>
                        <small style="color: #666;"><?= getDayName($finish_date->format('N')) ?></small>
                    </td>
                    <td align="center">
                        <span style="background: #e3f2fd; padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                            <?= $duration ?> hari
                        </span>
                    </td>
                    <td align="center">
                        <i class="edit-icon <?= $can_edit ? '' : 'disabled' ?>" 
                           onclick="<?= $can_edit ? "editPeriod('{$row['id_week']}', '{$row['date_start']}', '{$row['date_finish']}')" : '' ?>"
                           title="<?= $can_edit ? 'Edit periode' : 'Tidak dapat edit di luar periode aktif' ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </i>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 10px; padding: 10px; background:rgb(255, 255, 255); border-radius: 6px;">
            <h5 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">Informasi Periode</h5>
            <p style="margin: 2px 0; font-size: 12px; color: #666;">
                <strong>Total Minggu:</strong> <?= count($data) ?> minggu
            </p>
            <p style="margin: 2px 0; font-size: 12px; color: #666;">
                <strong>Periode Penuh:</strong> 
                <?= $month_start->format('d/m/Y H:i') ?> - 
                <?= $month_end->format('d/m/Y H:i') ?>
            </p>
            <p style="margin: 2px 0; font-size: 12px; color: #666;">
                <strong>Catatan:</strong> Periode dimulai dari tanggal 27 bulan sebelumnya (08:00) hingga tanggal 26 bulan ini (17:00)
            </p>
        </div>
    </div>
</div>

<style>
.compact-table { font-size: 13px !important; }
.compact-table th, .compact-table td { padding: 6px 8px !important; line-height: 1.4 !important; vertical-align: middle !important; }
.compact-table th { font-size: 14px !important; font-weight: 600 !important; background-color: #f8f9fa !important; }
.edit-icon { cursor: pointer; color: #0074d9; display: inline-flex; align-items: center; justify-content: center; padding: 4px; border-radius: 4px; transition: all 0.2s ease; }
.edit-icon:hover { color: #0056b3; background-color: rgba(0, 116, 217, 0.1); transform: scale(1.1); }
.edit-icon.disabled { color: #ccc; cursor: not-allowed; opacity: 0.5; }
.edit-icon.disabled:hover { background-color: transparent; transform: none; }
</style>

<?php else: ?>
<div class="no-data">
    <p>Tidak ada data periode untuk bulan dan tahun yang dipilih.</p>
    <p>Silakan generate periode terlebih dahulu.</p>
</div>
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