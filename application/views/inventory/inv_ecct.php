<?php
// Tambahkan CSS untuk modal di bagian head atau sebelum closing body
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

.modal-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    min-width: 350px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e5e5;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8f9fa;
    border-radius: 0 0 8px 8px;
}

.btn[onclick*="openModal"] {
    position: relative;
    z-index: 10;
    pointer-events: auto;
}
</style>

<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="btn-group ml-2">
          <button id="btn_ecct" class="btn btn-sm btn-primary" onclick="switchTable('ecct')">ECCT</button>
          <button id="btn_allitem" class="btn btn-sm btn-light" onclick="switchTable('allitem')">All Item</button>
        </div>
      </div>
      <div id="toolbar_right" class="flex items-center gap-2"></div>
    </div>
    <div id="show_data"></div>
  </div>
</div>

<!-- Modal Overlay -->
<div id="modal_overlay" class="modal-overlay"></div>

<!-- Modal Filter ECCT -->
<div id="modal_filter_ecct" class="modal-container">
  <div class="modal-header">
    <h3 class="modal-title">ECCT Data Filter</h3>
    <button class="btn-close" onclick="closeModal('modal_filter_ecct')">&times;</button>
  </div>
  <div class="modal-body">
    <div class="grid lg:grid-cols-3">
      <div class="form-group">
        <span class="form-hint">Device Name</span>
        <input class="input" type="text" value="" id="dvc_name_ecct" placeholder="Device name..." />
      </div>
      <div class="form-group">
        <span class="form-hint">Device Code</span>
        <input class="input" type="text" value="" id="dvc_code_ecct" placeholder="Device code..." />
      </div>
      <div class="form-group">
        <span class="form-hint">Data View</span>
        <select class="select" id="data_view_ecct">
          <option value="5">5</option>
          <option value="10" selected="selected">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
          <option value="50">50</option>
        </select>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeModal('modal_filter_ecct')">Cancel</button>
    <button class="btn btn-primary" onclick="showDataEcct(); closeModal('modal_filter_ecct');">Submit</button>
  </div>
</div>

<!-- Modal Filter All Item -->
<div id="modal_filter_item" class="modal-container">
  <div class="modal-header">
    <h3 class="modal-title">Data Filter</h3>
    <button class="btn-close" onclick="closeModal('modal_filter_item')">&times;</button>
  </div>
  <div class="modal-body">
    <div class="grid lg:grid-cols-3">
      <div class="form-group">
        <span class="form-hint">Device Size</span>
        <select class="select" id="dvc_size">
          <option value="">All</option>
          <option value="XS">XS</option>
          <option value="S">S</option>
          <option value="M">M</option>
          <option value="L">L</option>
          <option value="XL">XL</option>
          <option value="XXL">XXL</option>
          <option value="3XL">3XL</option>
          <option value="ALL">ALL SIZE</option>
          <option value="Cus">Cus</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">Device Color</span>
        <select class="select" id="dvc_col">
          <option value="">All</option>
          <option value="Dark Gray">Dark Gray</option>
          <option value="Black">Black</option>
          <option value="Grey">Grey</option>
          <option value="Blue Navy">Blue Navy</option>
          <option value="Green Army">Green Army</option>
          <option value="Red Maroon">Red Maroon</option>
          <option value="Custom">Custom</option>
          <option value="-">-</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">QC Status</span>
        <select class="select" id="dvc_qc">
          <option value="">All</option>
          <option value="0">LN</option>
          <option value="1">DN</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">Date From</span>
        <input class="input calendar" type="text" value="" id="date_from" />
      </div>
      <div class="form-group">
        <span class="form-hint">Date To</span>
        <input class="input calendar" type="text" value="" id="date_to" />
      </div>
      <div class="form-group">
        <span class="form-hint">Location</span>
        <select class="select" id="loc_move">
          <option value="">All</option>
          <option value="Lantai 2">Lantai 2</option>
          <option value="Bang Toni">Bang Toni</option>
          <option value="Om Bob">Om Bob</option>
          <option value="Rekanan">Rekanan</option>
          <option value="LN">LN</option>
          <option value="ECBS">ECBS</option>
          <option value="LN Office">LN Office</option>
          <option value="Lantai 1">Lantai 1</option>
          <option value="Unknow">Unknow</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">Sort By</span>
        <select class="select" id="sort_by">
          <option value="">None</option>
          <option value="id_act_asc">ID ASC</option>
          <option value="id_act_desc">ID DESC</option>
          <option value="dvc_sn_asc">Serial Number ASC</option>
          <option value="dvc_sn_desc">Serial Number DESC</option>
        </select>
      </div>
      <div class="form-group">
        <span class="form-hint">Data View</span>
        <select class="select" id="data_view_item">
          <option value="5">5</option>
          <option value="10" selected="selected">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
          <option value="50">50</option>
        </select>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeModal('modal_filter_item')">Cancel</button>
    <button class="btn btn-primary" onclick="showDataAllItem(); closeModal('modal_filter_item');">Submit</button>
  </div>
</div>

<div id="modal_input_in" class="modal-container" style="display:none;">
  <div class="modal-header">
    <h3 class="modal-title">Input In - Barang Masuk</h3>
    <button class="btn-close" onclick="closeInputModal()">&times;</button>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <label>Serial Number *</label>
      <input type="text" id="in_serial_number" class="input" placeholder="Contoh: ABC12D34E567890" />
      <small style="color: #666; font-size: 12px;">Minimal 11 karakter untuk parsing otomatis</small>
    </div>
    <div class="form-group">
      <label>QC Status *</label>
      <select id="in_qc_status" class="select">
        <option value="0">LN</option>
        <option value="1">DN</option>
      </select>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeInputModal()">Cancel</button>
    <button class="btn btn-primary" onclick="submitInput('in')">💾 Submit</button>
  </div>
</div>

<!-- Modal Input Out -->
<div id="modal_input_out" class="modal-container" style="display:none;">
  <div class="modal-header">
    <h3 class="modal-title">Input Out - Barang Keluar</h3>
    <button class="btn-close" onclick="closeInputModal()">&times;</button>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <label>Serial Number *</label>
      <input type="text" id="out_serial_number" class="input" placeholder="Masukkan nomor seri yang akan keluar" />
      <small style="color: #666; font-size: 12px;">Serial number harus sudah ada di database</small>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeInputModal()">Cancel</button>
    <button class="btn btn-danger" onclick="submitInput('out')">📤 Submit</button>
  </div>
</div>

<!-- Modal Input Move -->
<div id="modal_input_move" class="modal-container" style="display:none;">
  <div class="modal-header">
    <h3 class="modal-title">Input Move - Pindah Lokasi</h3>
    <button class="btn-close" onclick="closeInputModal()">&times;</button>
  </div>
  <div class="modal-body">
    <div class="form-group">
      <label>Serial Number *</label>
      <input type="text" id="move_serial_number" class="input" placeholder="Masukkan nomor seri yang akan dipindah" />
      <small style="color: #666; font-size: 12px;">Serial number harus sudah ada di database</small>
    </div>
    <div class="form-group">
      <label>Lokasi Tujuan *</label>
      <select id="move_location" class="select">
        <option value="">-- Pilih Lokasi Tujuan --</option>
        <option value="Lantai 2">🏢 Lantai 2</option>
        <option value="Bang Toni">👨‍💼 Bang Toni</option>
        <option value="Om Bob">👨‍💼 Om Bob</option>
        <option value="Rekanan">🤝 Rekanan</option>
        <option value="LN">🏭 LN</option>
        <option value="ECBS">🏭 ECBS</option>
        <option value="LN Office">🏢 LN Office</option>
        <option value="Lantai 1">🏢 Lantai 1</option>
        <option value="Unknow">❓ Unknown</option>
      </select>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-light" onclick="closeInputModal()">Cancel</button>
    <button class="btn btn-info" onclick="submitInput('move')">🚚 Submit</button>
  </div>
</div>

<script type="text/javascript">
var currentTable = 'ecct';
var currentEcctType = 'app';

function switchTable(type) {
  currentTable = type;
  renderToolbar();
  if (type === 'ecct') {
    showDataEcct();
  } else {
    showDataAllItem();
  }
  document.getElementById('btn_ecct').className = 'btn btn-sm ' + (currentTable === 'ecct' ? 'btn-primary' : 'btn-light');
  document.getElementById('btn_allitem').className = 'btn btn-sm ' + (currentTable === 'allitem' ? 'btn-primary' : 'btn-light');
}

function renderToolbar() {
  var toolbar = '';
  if (currentTable === 'ecct') {
    toolbar += '<div class="btn-group mr-2">';
    toolbar += '<button id="btn_app" class="btn btn-sm ' + (currentEcctType === 'app' ? 'btn-primary' : 'btn-light') + '" onclick="switchEcctType(\'app\')">APP</button>';
    toolbar += '<button id="btn_osc" class="btn btn-sm ' + (currentEcctType === 'osc' ? 'btn-primary' : 'btn-light') + '" onclick="switchEcctType(\'osc\')">OSC</button>';
    toolbar += '</div>';
    toolbar += '<input class="input input-sm" placeholder="Search" type="text" id="key_ecct" style="margin-right:4px;" onkeyup="if(event.key === \'Enter\'){showDataEcct();}" />';
    toolbar += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_ecct\')">Filter</span>';
    toolbar += '<span class="btn btn-primary btn-sm" onclick="showDataEcct();" id="btn_search_ecct">Search</span>';
    toolbar += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataEcct(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  } else {
    toolbar += '<input class="input input-sm" placeholder="Search" type="text" id="key_item" style="margin-right:4px;" onkeyup="if(event.key === \'Enter\'){showDataAllItem();}" />';
    toolbar += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_item\')">Filter</span>';
    toolbar += '<span class="btn btn-primary btn-sm" onclick="showDataAllItem();" id="btn_search_item">Search</span>';
    toolbar += '<div style="position: relative; display: inline-block; margin-left: 5px;">';
    toolbar += '<button class="btn btn-sm" style="background: #28a745; color: white;" onclick="toggleInputDropdown()" id="input_btn" type="button">Input ▼</button>';
    toolbar += '<div id="input_dropdown" style="display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); min-width: 120px; z-index: 1000; margin-top: 2px;">';
    toolbar += '<button type="button" onclick="openInputModal(\'in\')" style="display: block; width: 100%; padding: 8px 15px; color: #333; text-decoration: none; font-size: 14px; border: none; background: none; text-align: left; border-bottom: 1px solid #f0f0f0; cursor: pointer;">In</button>';
    toolbar += '<button type="button" onclick="openInputModal(\'out\')" style="display: block; width: 100%; padding: 8px 15px; color: #333; text-decoration: none; font-size: 14px; border: none; background: none; text-align: left; border-bottom: 1px solid #f0f0f0; cursor: pointer;">Out</button>';
    toolbar += '<button type="button" onclick="openInputModal(\'move\')" style="display: block; width: 100%; padding: 8px 15px; color: #333; text-decoration: none; font-size: 14px; border: none; background: none; text-align: left; cursor: pointer;">Move</button>';
    toolbar += '</div></div>';
    toolbar += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataAllItem(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  }
  document.getElementById('toolbar_right').innerHTML = toolbar;
}

function switchEcctType(type) {
  currentEcctType = type;
  renderToolbar();
  showDataEcct();
}

// Fungsi untuk membuka modal
function openModal(modalId) {
  console.log('Opening modal:', modalId); // Debug
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);
  
  if (overlay && modal) {
    overlay.style.display = 'block';
    modal.style.display = 'block';
    
    // Tambahkan event listener untuk menutup modal ketika overlay diklik
    overlay.onclick = function(event) {
      if (event.target === overlay) {
        closeModal(modalId);
      }
    };
  } else {
    console.error('Modal or overlay not found:', modalId);
  }
}

// Fungsi untuk menutup modal
function closeModal(modalId) {
  console.log('Closing modal:', modalId); // Debug
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);
  
  if (overlay && modal) {
    overlay.style.display = 'none';
    modal.style.display = 'none';
  }
}

// Event listener untuk tombol ESC
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeModal('modal_filter_ecct');
    closeModal('modal_filter_item');
  }
});

function showDataEcct(page = 1, in_sort = '') {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;
  
  var val = "?";
  const arr = ["key_ecct", "dvc_name_ecct", "dvc_code_ecct", "data_view_ecct"];
  for (let i = 0; i < arr.length; i++) {
    var element = document.getElementById(arr[i]);
    if (element) {
      var fieldName = arr[i].replace('_ecct', '');
      if (fieldName === 'key') fieldName = 'key_ecct';
      val = val + "&" + fieldName + "=" + encodeURIComponent(element.value);
    }
  }
  val = val + "&type=" + currentEcctType;
  
  if (page == 'export') {
    var link = "<?php echo $config['url_menu']; ?>data/data_inv_ecct_" + currentEcctType + "_export" + val;
    window.open(link, '_blank').focus();
    return;
  } else {
    val = val + "&p=" + page;
    var link = "<?php echo $config['url_menu']; ?>data/data_inv_ecct_" + currentEcctType + "_show" + val;
    if (typeof $ !== 'undefined') {
      $("#show_data").load(link);
    } else {
      fetch(link)
        .then(response => response.text())
        .then(data => {
          document.getElementById('show_data').innerHTML = data;
        })
        .catch(error => {
          document.getElementById('show_data').innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Error loading data</div>';
        });
    }
  }
}

function showDataAllItem(page = 1, in_sort = '') {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;
  
  var val = "?";
  const arr = ["key_item", "dvc_size", "dvc_col", "dvc_qc", "date_from", "date_to", "loc_move", "sort_by", "data_view_item"];
  for (let i = 0; i < arr.length; i++) {
    var element = document.getElementById(arr[i]);
    if (element) {
      val = val + "&" + arr[i] + "=" + encodeURIComponent(element.value);
    }
  }
  
  if (page == 'export') {
    var link = "<?php echo $config['url_menu']; ?>data/data_item_export" + val;
    window.open(link, '_blank').focus();
    return;
  } else {
    val = val + "&p=" + page;
    var link = "<?php echo $config['url_menu']; ?>data/data_item_show" + val;
    if (typeof $ !== 'undefined') {
      $("#show_data").load(link);
    } else {
      fetch(link)
        .then(response => response.text())
        .then(data => {
          document.getElementById('show_data').innerHTML = data;
        })
        .catch(error => {
          document.getElementById('show_data').innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Error loading data</div>';
        });
    }
  }
}

function toggleInputDropdown() {
  const dropdown = document.getElementById('input_dropdown');
  dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function openInputModal(type) {
  document.getElementById('modal_overlay').style.display = 'block';
  document.getElementById('modal_input_in').style.display = 'none';
  document.getElementById('modal_input_out').style.display = 'none';
  document.getElementById('modal_input_move').style.display = 'none';
  if(type === 'in') document.getElementById('modal_input_in').style.display = 'block';
  if(type === 'out') document.getElementById('modal_input_out').style.display = 'block';
  if(type === 'move') document.getElementById('modal_input_move').style.display = 'block';
}

// --- Tambahan untuk close dropdown input jika klik di luar ---
document.addEventListener('mousedown', function(event) {
  const dropdown = document.getElementById('input_dropdown');
  const inputBtn = document.getElementById('input_btn');
  if (dropdown && inputBtn && dropdown.style.display === 'block') {
    if (!dropdown.contains(event.target) && !inputBtn.contains(event.target)) {
      dropdown.style.display = 'none';
    }
  }
});

// --- Tambahan untuk close modal input jika klik di luar modal (overlay) ---
document.getElementById('modal_overlay').addEventListener('mousedown', function(event) {
  // Cek jika salah satu modal input sedang terbuka
  const modals = ['modal_input_in', 'modal_input_out', 'modal_input_move'];
  for (let i = 0; i < modals.length; i++) {
    const modal = document.getElementById(modals[i]);
    if (modal && modal.style.display === 'block') {
      // Pastikan klik di overlay, bukan di modal
      if (event.target === this) {
        closeInputModal();
      }
    }
  }
});

// --- Reset input serial number setiap modal input tertutup ---
function closeInputModal() {
  document.getElementById('modal_overlay').style.display = 'none';
  document.getElementById('modal_input_in').style.display = 'none';
  document.getElementById('modal_input_out').style.display = 'none';
  document.getElementById('modal_input_move').style.display = 'none';
  // Reset input serial number
  var inSerial = document.getElementById('in_serial_number');
  if (inSerial) inSerial.value = '';
  var outSerial = document.getElementById('out_serial_number');
  if (outSerial) outSerial.value = '';
  var moveSerial = document.getElementById('move_serial_number');
  if (moveSerial) moveSerial.value = '';
}

// --- Reset input serial number juga setelah submitInput berhasil ---
function submitInput(type) {
  let data = {};
  let url = "<?php echo $config['url_menu']; ?>input_process";
  if (type === 'in') {
    data = {
      type: 'in',
      serial_number: document.getElementById('in_serial_number').value.trim(),
      qc_status: document.getElementById('in_qc_status').value
    };
  } else if (type === 'out') {
    data = {
      type: 'out',
      serial_number: document.getElementById('out_serial_number').value.trim()
    };
  } else if (type === 'move') {
    data = {
      type: 'move',
      serial_number: document.getElementById('move_serial_number').value.trim(),
      location: document.getElementById('move_location').value
    };
  }
  if (!data.serial_number) {
    alert('⚠️ Serial number harus diisi!');
    return;
  }
  if (type === 'move' && !data.location) {
    alert('⚠️ Lokasi harus dipilih!');
    return;
  }
  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    alert(result.message);
    if(result.success) {
      closeInputModal();
      showDataAllItem();
    }
    // Reset input serial number meskipun gagal
    var inSerial = document.getElementById('in_serial_number');
    if (inSerial) inSerial.value = '';
    var outSerial = document.getElementById('out_serial_number');
    if (outSerial) outSerial.value = '';
    var moveSerial = document.getElementById('move_serial_number');
    if (moveSerial) moveSerial.value = '';
  })
  .catch(err => alert('Gagal memproses data!'));
}

// Inisialisasi saat halaman dimuat
window.onload = function() {
  renderToolbar();
  showDataEcct();
}

// CSS untuk animasi loading
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>