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

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

<div id="modal_overlay" class="modal-overlay"></div>

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

<!-- Modal Input Gabungan In/Move/Out -->
<div id="modal_input_all" class="modal-container" style="min-width:900px;">
  <div class="modal-header">
    <h3 class="modal-title">Input Inventory</h3>
    <button class="btn-close" onclick="closeModal('modal_input_all')">&times;</button>
  </div>
  <div class="modal-body" style="display: flex; gap: 24px; justify-content: space-between; align-items: flex-start;">
    <!-- Input In -->
    <div style="flex:1; min-width: 220px; border-right:1px solid #eee; padding-right:16px; display: flex; flex-direction: column; justify-content: flex-start;">
      <h4 style="font-size:15px; font-weight:600; margin-bottom:10px;">In</h4>
      <div class="form-group">
        <label>Serial Number *</label>
        <input type="text" id="in_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
        <small style="color: #666; font-size: 12px;">Masukan serial number sesuai ketentuan</small>
      </div>
      <div class="form-group">
        <label>QC Status *</label>
        <select id="in_qc_status" class="select">
          <option value="0">LN</option>
          <option value="1">DN</option>
        </select>
      </div>
      <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('in')">Submit In</button>
    </div>
    <!-- Input Move -->
    <div style="flex:1; min-width: 220px; border-right:1px solid #eee; padding:0 16px; display: flex; flex-direction: column; justify-content: flex-start;">
      <h4 style="font-size:15px; font-weight:600; margin-bottom:10px;">Move</h4>
      <div class="form-group">
        <label>Serial Number *</label>
        <input type="text" id="move_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
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
      <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('move')">Submit Move</button>
    </div>
    <!-- Input Out -->
    <div style="flex:1; min-width: 220px; padding-left:16px; display: flex; flex-direction: column; justify-content: flex-start;">
      <h4 style="font-size:15px; font-weight:600; margin-bottom:10px;">Out</h4>
      <div class="form-group">
        <label>Serial Number *</label>
        <input type="text" id="out_serial_number" class="input" placeholder="Masukan nomor seri di sini" />
        <small style="color: #666; font-size: 12px;">Serial number harus sudah ada di database</small>
      </div>
      <button class="btn btn-primary" style="padding: 0 14px; min-width: 90px; font-size: 13px; margin-top: 18px; align-self: flex-start;" onclick="submitInput('out')">Submit Out</button>
    </div>
  </div>
</div>

<script type="text/javascript">
// Perbaikan untuk JavaScript di inv_ecct.php
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
    // Hapus input search, tombol filter, dan tombol search pada ECCT
    toolbar += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataEcct(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  } else {
    toolbar += '<input class="input input-sm" placeholder="Search" type="text" id="key_item" style="margin-right:4px;" onkeyup="if(event.key === \'Enter\'){showDataAllItem();}" />';
    toolbar += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_item\')">Filter</span>';
    toolbar += '<span class="btn btn-primary btn-sm" onclick="showDataAllItem();">Search</span>';
    toolbar += '<button class="btn btn-sm" style="background: #28a745; color: white; margin-left:5px;" onclick="openModal(\'modal_input_all\')">Input</button>';
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
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);

  if (overlay && modal) {
    overlay.style.display = 'block';
    modal.style.display = 'block';
  }
}

// Fungsi untuk menutup modal
function closeModal(modalId) {
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);

  if (overlay && modal) {
    overlay.style.display = 'none';
    modal.style.display = 'none';
  }

  // Reset form jika modal input
  if (modalId === 'modal_input_all') {
    resetInputForm();
  }
}

// Reset form input
function resetInputForm() {
  document.getElementById('in_serial_number').value = '';
  document.getElementById('out_serial_number').value = '';
  document.getElementById('move_serial_number').value = '';
  document.getElementById('move_location').value = '';
}

// Event listener untuk menutup modal dengan klik overlay atau ESC
document.addEventListener('DOMContentLoaded', function() {
  const overlay = document.getElementById('modal_overlay');

  // Klik overlay untuk menutup modal
  overlay.addEventListener('click', function(event) {
    if (event.target === overlay) {
      const modals = ['modal_filter_ecct', 'modal_filter_item', 'modal_input_all'];
      modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.style.display === 'block') {
          closeModal(modalId);
        }
      });
    }
  });

  // ESC key untuk menutup modal
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      const modals = ['modal_filter_ecct', 'modal_filter_item', 'modal_input_all'];
      modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.style.display === 'block') {
          closeModal(modalId);
        }
      });
    }
  });
});

// PERBAIKAN UTAMA: Fungsi showDataEcct dengan parameter page yang benar
function showDataEcct(page = 1) {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  // Hapus fields filter dan search ECCT
  // const fields = ["key_ecct", "dvc_name_ecct", "dvc_code_ecct", "data_view_ecct"];
  // fields.forEach(field => {
  //   var element = document.getElementById(field);
  //   if (element) {
  //     var fieldName = field.replace('_ecct', '');
  //     if (fieldName === 'key') fieldName = 'key_ecct';
  //     val += "&" + fieldName + "=" + encodeURIComponent(element.value);
  //   }
  // });

  val += "&type=" + currentEcctType;

  if (page === 'export') {
    var link = "<?php echo $config['url_menu']; ?>data/data_inv_ecct_" + currentEcctType + "_export" + val;
    window.open(link, '_blank').focus();
    return;
  }

  val += "&p=" + page;
  var link = "<?php echo $config['url_menu']; ?>data/data_inv_ecct_" + currentEcctType + "_show" + val;

  loadData(link);
}

// PERBAIKAN UTAMA: Fungsi showDataAllItem dengan parameter page yang benar
function showDataAllItem(page = 1) {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  const fields = ["key_item", "dvc_size", "dvc_col", "dvc_qc", "date_from", "date_to", "loc_move", "sort_by", "data_view_item"];

  fields.forEach(field => {
    var element = document.getElementById(field);
    if (element) {
      val += "&" + field + "=" + encodeURIComponent(element.value);
    }
  });

  val += "&context=inv_ecct";

  if (page === 'export') {
    var link = "<?php echo $config['url_menu']; ?>data/data_item_export" + val;
    window.open(link, '_blank').focus();
    return;
  }

  val += "&p=" + page;
  var link = "<?php echo $config['url_menu']; ?>data/data_item_show" + val;

  loadData(link);
}

function loadData(link) {
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

function handlePagination(page) {
  if (currentTable === 'allitem') {
    showDataAllItem(page);
  } else if (currentTable === 'ecct') {
    showDataEcct(page);
  }
}

function getCurrentTableFunction() {
  if (currentTable === 'allitem') {
    return 'showDataAllItem';
  } else if (currentTable === 'ecct') {
    return 'showDataEcct';
  }
  return 'showDataItem';
}

function submitInput(type) {
  let data = {};
  let url = "<?php echo $config['url_menu']; ?>input_process";

  let serialNumber = '';
  if (type === 'in') {
    serialNumber = document.getElementById('in_serial_number').value.trim();
    data = {
      type: 'in',
      serial_number: serialNumber,
      qc_status: document.getElementById('in_qc_status').value
    };
  } else if (type === 'out') {
    serialNumber = document.getElementById('out_serial_number').value.trim();
    data = {
      type: 'out',
      serial_number: serialNumber
    };
  } else if (type === 'move') {
    serialNumber = document.getElementById('move_serial_number').value.trim();
    data = {
      type: 'move',
      serial_number: serialNumber,
      location: document.getElementById('move_location').value
    };
  }

  if (!serialNumber) {
    alert('⚠️ Serial number harus diisi!');
    return;
  }

  // Validasi karakter ke-6 harus 'T'
  if (serialNumber.length < 6 || serialNumber.charAt(5).toUpperCase() !== 'T') {
    alert('masukan ecct!');
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
    if (result.success) {
      closeModal('modal_input_all');
      // Refresh tabel yang aktif setelah input berhasil
      if (currentTable === 'allitem') {
        showDataAllItem();
      } else if (currentTable === 'ecct') {
        showDataEcct();
      }
    }
  })
  .catch(error => {
        alert('❌ Error: ' + error.message);
  });
}

// Inisialisasi saat halaman dimuat
window.onload = function() {
  renderToolbar();
  showDataEcct(); // Default ke tabel ECCT saat halaman dimuat
}
</script>
