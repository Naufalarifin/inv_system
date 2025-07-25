<!-- ========================================== -->
<!-- UNTUK FILE inv_ecbs.php -->
<!-- ========================================== -->

<!-- HTML structure tetap sama, hanya hapus semua <script> JavaScript -->
<!-- CSS tetap di tempatnya -->

<!-- Di bagian sebelum closing </body> atau di head -->
<script>
// Config untuk JavaScript
const CONFIG = {
    baseUrl: '<?php echo $config["base_url"]; ?>',
    urlMenu: '<?php echo $config["url_menu"]; ?>'
};

// Set inventory type untuk deteksi otomatis
window.INVENTORY_TYPE = 'ECBS';
</script>

<!-- Load file JavaScript universal -->
<script src="js/inventory-universal.js"></script>

<!-- ========================================== -->
<!-- UNTUK FILE inv_ecct.php -->
<!-- ========================================== -->

<!-- HTML structure tetap sama, hanya hapus semua <script> JavaScript -->
<!-- CSS tetap di tempatnya -->

<!-- Di bagian sebelum closing </body> atau di head -->
<script>
// Config untuk JavaScript
const CONFIG = {
    baseUrl: '<?php echo $config["base_url"]; ?>',
    urlMenu: '<?php echo $config["url_menu"]; ?>'
};

// Set inventory type untuk deteksi otomatis
window.INVENTORY_TYPE = 'ECCT';
</script>

<!-- Load file JavaScript universal -->
<script src="js/inventory-universal.js"></script>

<!-- ========================================== -->
<!-- CONTOH FILE PHP YANG SUDAH DIBERSIHKAN -->
<!-- ========================================== -->

<?php
// inv_ecbs.php (contoh yang sudah bersih)
?>
<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}
/* ... semua CSS lainnya tetap sama ... */
</style>

<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div id="toolbar_left" class="flex items-center gap-2"></div>
      <div id="toolbar_right" class="flex items-center gap-2"></div>
    </div>
    <div id="show_data"></div>
  </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<!-- Modal Filter All Item -->
<div id="modal_filter_item" class="modal-container">
  <!-- ... HTML modal tetap sama ... -->
</div>

<!-- Modal Input Gabungan In/Move/Out dengan Tab -->
<div id="modal_input_all" class="modal-container" style="min-width:500px;">
  <!-- ... HTML modal tetap sama ... -->
</div>

<!-- JavaScript Config dan Load -->
<script>
const CONFIG = {
    baseUrl: '<?php echo $config["base_url"]; ?>',
    urlMenu: '<?php echo $config["url_menu"]; ?>'
};
window.INVENTORY_TYPE = 'ECBS'; // atau 'ECCT' untuk inv_ecct.php
</script>
<script src="js/inventory-universal.js"></script>

<!-- ========================================== -->
<!-- KEUNTUNGAN PENDEKATAN INI -->
<!-- ========================================== -->

/**
 * 1. SATU FILE JS - Lebih mudah maintain
 * 2. AUTO DETECTION - Otomatis detect ECBS/ECCT berdasarkan INVENTORY_TYPE
 * 3. BACKWARD COMPATIBILITY - Semua function lama masih bisa dipanggil
 * 4. CLEAN PHP - File PHP jadi sangat bersih
 * 5. REUSABLE - Bisa dipakai untuk inventory type lain di masa depan
 * 6. SMART VALIDATION - Validasi serial number otomatis menyesuaikan (S untuk ECBS, T untuk ECCT)
 * 7. DYNAMIC LINKS - URL endpoint otomatis menyesuaikan inventory type
 */

<!-- ========================================== -->
<!-- CARA PENGGUNAAN -->
<!-- ========================================== -->

/**
 * 1. Buat folder js/ di project root
 * 2. Simpan inventory-universal.js di folder js/
 * 3. Di inv_ecbs.php set: window.INVENTORY_TYPE = 'ECBS';
 * 4. Di inv_ecct.php set: window.INVENTORY_TYPE = 'ECCT';
 * 5. Hapus semua JavaScript code dari kedua file PHP
 * 6. Load inventory-universal.js di kedua file
 * 7. Done! Kedua halaman akan bekerja dengan 1 file JS yang sama
 */