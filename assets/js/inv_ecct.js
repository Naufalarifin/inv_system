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
  var toolbarLeft = '';
  var toolbarRight = '';
  
  if (currentTable === 'ecct') {
    // Untuk ECCT, semua tetap di kanan
    toolbarRight += '<div class="btn-group mr-2">';
    toolbarRight += '<button id="btn_app" class="btn btn-sm ' + (currentEcctType === 'app' ? 'btn-primary' : 'btn-light') + '" onclick="switchEcctType(\'app\')">APP</button>';
    toolbarRight += '<button id="btn_osc" class="btn btn-sm ' + (currentEcctType === 'osc' ? 'btn-primary' : 'btn-light') + '" onclick="switchEcctType(\'osc\')">OSC</button>';
    toolbarRight += '</div>';
    toolbarRight += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataEcct(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  } else {
    // Untuk allitem, tombol Input di kiri
    toolbarLeft += '<button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal(\'modal_input_all\')">Input</button>';
    
    // Search, Filter, dan Export di kanan
    toolbarRight += '<div class="input-group input-sm">';
    toolbarRight += '<input class="input input-sm" placeholder="Search" type="text" id="key_item" onkeyup="if(event.key === \'Enter\'){showDataAllItem();}" />';
    toolbarRight += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_item\')">Filter</span>';
    toolbarRight += '<span class="btn btn-primary btn-sm" onclick="showDataAllItem();">Search</span>';
    toolbarRight += '</div>';
    toolbarRight += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataAllItem(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  }
  
  // Update kedua div
  document.getElementById('toolbar_left').innerHTML = toolbarLeft;
  document.getElementById('toolbar_right').innerHTML = toolbarRight;
}

function switchEcctType(type) {
  currentEcctType = type;
  renderToolbar();
  showDataEcct();
}

function openModal(modalId) {
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);

  if (overlay && modal) {
    overlay.style.display = 'block';
    modal.style.display = 'block';
  }
}

function closeModal(modalId) {
  const overlay = document.getElementById('modal_overlay');
  const modal = document.getElementById(modalId);

  if (overlay && modal) {
    overlay.style.display = 'none';
    modal.style.display = 'none';
  }

  if (modalId === 'modal_input_all') {
    resetInputForm();
  }
}

function resetInputForm() {
  document.getElementById('in_serial_number').value = '';
  document.getElementById('out_serial_number').value = '';
  document.getElementById('move_serial_number').value = '';
  document.getElementById('move_location').value = '';
}

document.addEventListener('DOMContentLoaded', function() {
  const overlay = document.getElementById('modal_overlay');

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

function showDataEcct(page = 1) {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  // Hapus fields filter dan search ECCT yang tidak digunakan
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

function showDataAllItem(page = 1) {
  var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  const fields = ["key_activity", "dvc_size", "dvc_col", "dvc_qc", "date_from", "date_to", "loc_move", "sort_by", "data_view_item"];

  fields.forEach(field => {
    var element = document.getElementById(field);
    if (element) {
      val += "&" + field + "=" + encodeURIComponent(element.value);
    }
  });

  val += "&context=inv_ecct"; // Pertahankan konteks inv_ecct

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

// Fungsi untuk menangani pagination berdasarkan table yang aktif
function handlePagination(page) {
  if (currentTable === 'allitem') {
    showDataAllItem(page);
  } else if (currentTable === 'ecct') {
    showDataEcct(page);
  }
}

// Fungsi untuk mendapatkan fungsi table yang aktif
function getCurrentTableFunction() {
  if (currentTable === 'allitem') {
    return 'showDataAllItem';
  } else if (currentTable === 'ecct') {
    return 'showDataEcct';
  }
  return 'showDataAllItem'; // Default
}

function showInputTab(tab) {
  // Hide all tab contents
  document.querySelectorAll('.input-tab-content').forEach(function(el) {
    el.style.display = 'none';
  });
  // Remove active from all tab buttons
  document.querySelectorAll('.input-tab-btn').forEach(function(btn) {
    btn.classList.remove('active');
  });
  // Show selected tab content
  document.getElementById('inputTab_' + tab).style.display = 'flex';
  // Set active tab button
  document.getElementById('tabBtn_' + tab).classList.add('active');
}

// Set default tab to 'in' on modal open
if (typeof window.inputTabDefaultSet === 'undefined') {
  window.inputTabDefaultSet = true;
  document.addEventListener('DOMContentLoaded', function() {
    showInputTab('in');
  });
}

function submitInput(type) {
  let data = {};
  let url = "<?php echo $config['url_menu']; ?>input_process";
  let resultDiv = null;
  let serialNumber = '';

  if (type === 'in') {
    serialNumber = document.getElementById('in_serial_number').value.trim();
    data = {
      type: 'in',
      serial_number: serialNumber,
      qc_status: document.getElementById('in_qc_status').value
    };
    resultDiv = document.getElementById('in_result_message');
  } else if (type === 'out') {
    serialNumber = document.getElementById('out_serial_number').value.trim();
    data = {
      type: 'out',
      serial_number: serialNumber
    };
    resultDiv = document.getElementById('out_result_message');
  } else if (type === 'move') {
    serialNumber = document.getElementById('move_serial_number').value.trim();
    data = {
      type: 'move',
      serial_number: serialNumber,
      location: document.getElementById('move_location').value
    };
    resultDiv = document.getElementById('move_result_message');
  }

  if (resultDiv) {
    resultDiv.style.display = 'none';
    resultDiv.innerText = '';
  }

  if (!serialNumber) {
    if (resultDiv) {
      resultDiv.innerText = 'Serial number harus diisi!';
      resultDiv.className = 'input-result-message error';
      resultDiv.style.display = 'block';
    }
    return;
  }

  // Validasi khusus ECCT: Serial number harus mengandung 'T' di posisi ke-6
  if (serialNumber.length < 6 || serialNumber.charAt(5).toUpperCase() !== 'T') {
    if (resultDiv) {
      resultDiv.innerText = 'Masukan ECCT!';
      resultDiv.className = 'input-result-message error';
      resultDiv.style.display = 'block';
    }
    return;
  }

  if (type === 'move' && !data.location) {
    if (resultDiv) {
      resultDiv.innerText = 'Lokasi harus dipilih!';
      resultDiv.className = 'input-result-message error';
      resultDiv.style.display = 'block';
    }
    return;
  }

  if (resultDiv) {
    resultDiv.innerText = 'Memproses...';
    resultDiv.className = 'input-result-message';
    resultDiv.style.display = 'block';
  }

  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if (resultDiv) {
      resultDiv.innerText = result.message;
      if (result.success) {
        resultDiv.className = 'input-result-message success';
        if (type === 'in') document.getElementById('in_serial_number').value = '';
        if (type === 'out') document.getElementById('out_serial_number').value = '';
        if (type === 'move') document.getElementById('move_serial_number').value = '';
      } else {
        resultDiv.className = 'input-result-message error';
      }
      resultDiv.style.display = 'block';
    } else {
      if (result.success) {
        closeModal('modal_input_all');
        if (currentTable === 'allitem') {
          showDataAllItem();
        } else if (currentTable === 'ecct') {
          showDataEcct();
        }
      } else {
        alert(result.message);
      }
    }
  })
  .catch(error => {
    if (resultDiv) {
      resultDiv.innerText = '❌ Error: ' + error.message;
      resultDiv.className = 'input-result-message error';
      resultDiv.style.display = 'block';
    } else {
      alert('❌ Error: ' + error.message);
    }
  });
}

window.onload = function() {
  renderToolbar();
  showDataEcct(); // Default ke tabel ECCT saat halaman dimuat
}