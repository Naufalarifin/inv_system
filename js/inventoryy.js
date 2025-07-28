/**
 * Universal Inventory Management System
 * File JavaScript universal untuk inv_ecbs.php dan inv_ecct.php
 * 
 * Deteksi otomatis berdasarkan INVENTORY_TYPE yang di-set di PHP
 */

// =================== GLOBAL VARIABLES ===================
var currentTable = '';
var currentType = 'app'; // app atau osc
var inventoryType = ''; // 'ECBS' atau 'ECCT'

// Debug: log CONFIG object
console.log('CONFIG:', CONFIG);

// =================== INITIALIZATION ===================
document.addEventListener('DOMContentLoaded', function() {
  // Deteksi inventory type dari global variable yang di-set di PHP
  inventoryType = window.INVENTORY_TYPE || 'ECBS';
  
  // Set current table berdasarkan inventory type
  if (inventoryType === 'ECBS') {
    currentTable = 'ecbs';
  } else if (inventoryType === 'ECCT') {
    currentTable = 'ecct';
  }
  
  // Initialize
  initializeEventListeners();
  renderToolbar();
  showMainData();
  showInputTab('in');
});

// =================== MODAL UTILITIES ===================
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

// =================== EVENT LISTENERS ===================
function initializeEventListeners() {
  const overlay = document.getElementById('modal_overlay');

  // Klik overlay untuk menutup modal
  overlay.addEventListener('click', function(event) {
    if (event.target === overlay) {
      const modals = ['modal_filter_ecbs', 'modal_filter_ecct', 'modal_filter_item', 'modal_input_all'];
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
      const modals = ['modal_filter_ecbs', 'modal_filter_ecct', 'modal_filter_item', 'modal_input_all'];
      modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && modal.style.display === 'block') {
          closeModal(modalId);
        }
      });
    }
  });
}

// =================== COMMON UTILITIES ===================
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

function showLoading() {
  return '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
}

// =================== SERIAL NUMBER VALIDATION ===================
function validateSerialNumber(serialNumber, type, resultDiv) {
  let expectedChar = inventoryType === 'ECBS' ? 'S' : 'T';
  let deviceName = inventoryType;
  
  if (serialNumber.length < 6 || serialNumber.charAt(5).toUpperCase() !== expectedChar) {
    if (resultDiv) {
      resultDiv.innerText = `Masukan ${deviceName}!`;
      resultDiv.className = 'input-result-message error';
      resultDiv.style.display = 'block';
    }
    return false;
  }
  return true;
}

// =================== TABLE SWITCHING ===================
function switchTable(type) {
  currentTable = type;
  renderToolbar();
  
  if (inventoryType === 'ECBS') {
    if (type === 'ecbs') {
      showMainData();
    } else {
      showDataActivity();
    }
    document.getElementById('btn_ecbs').className = 'btn btn-sm ' + (currentTable === 'ecbs' ? 'btn-primary' : 'btn-light');
    document.getElementById('btn_activity').className = 'btn btn-sm ' + (currentTable === 'activity' ? 'btn-primary' : 'btn-light');
  } else if (inventoryType === 'ECCT') {
    if (type === 'ecct') {
      showMainData();
    } else {
      showDataAllItem();
    }
    document.getElementById('btn_ecct').className = 'btn btn-sm ' + (currentTable === 'ecct' ? 'btn-primary' : 'btn-light');
    document.getElementById('btn_allitem').className = 'btn btn-sm ' + (currentTable === 'allitem' ? 'btn-primary' : 'btn-light');
  }
}

// =================== TOOLBAR RENDERING ===================
function renderToolbar() {
  var toolbarLeft = '';
  var toolbarRight = '';
  
  const isMainTable = (inventoryType === 'ECBS' && currentTable === 'ecbs') || 
                     (inventoryType === 'ECCT' && currentTable === 'ecct');
  
  if (isMainTable) {
    // Untuk main table (ECBS/ECCT), semua di kanan
    toolbarRight += '<div class="btn-group mr-2">';
    toolbarRight += '<button id="btn_app" class="btn btn-sm ' + (currentType === 'app' ? 'btn-primary' : 'btn-light') + '" onclick="switchMainType(\'app\')">APP</button>';
    toolbarRight += '<button id="btn_osc" class="btn btn-sm ' + (currentType === 'osc' ? 'btn-primary' : 'btn-light') + '" onclick="switchMainType(\'osc\')">OSC</button>';
    toolbarRight += '</div>';
    toolbarRight += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showMainData(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  } else {
    // Untuk activity/allitem, tombol Input di kiri
    toolbarLeft += '<button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal(\'modal_input_all\')" id="input_btn" type="button">Input</button>';
    
    // Search, Filter, dan Export di kanan
    toolbarRight += '<div class="input-group input-sm">';
    toolbarRight += '<input class="input input-sm" placeholder="Search" type="text" id="key_activity" onkeyup="if(event.key === \'Enter\'){showSecondaryData();}" />';
    toolbarRight += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_item\')">Filter</span>';
    toolbarRight += '<span class="btn btn-primary btn-sm" onclick="showSecondaryData();">Search</span>';
    toolbarRight += '</div>';
    toolbarRight += '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showSecondaryData(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>';
  }
  
  // Update kedua div
  document.getElementById('toolbar_left').innerHTML = toolbarLeft;
  document.getElementById('toolbar_right').innerHTML = toolbarRight;
}

// =================== TYPE SWITCHING ===================
function switchMainType(type) {
  currentType = type;
  renderToolbar();
  showMainData();
}

// Alias functions untuk backward compatibility
function switchEcbsType(type) { switchMainType(type); }
function switchEcctType(type) { switchMainType(type); }

// =================== DATA LOADING FUNCTIONS ===================
function showMainData(page = 1) {
  const loading = showLoading();
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  val += "&type=" + currentType;

  const deviceType = inventoryType.toLowerCase();
  
  if (page === 'export') {
    var link = CONFIG.urlMenu + `data/data_inv_${deviceType}_` + currentType + "_export" + val;
    window.open(link, '_blank').focus();
    return;
  }

  val += "&p=" + page;
  var link = CONFIG.urlMenu + `data/data_inv_${deviceType}_` + currentType + "_show" + val;

  loadData(link);
}

function showSecondaryData(page = 1) {
  const loading = showLoading();
  if (page !== 'export') document.getElementById('show_data').innerHTML = loading;

  var val = "?";
  const fields = ["key_activity", "dvc_size", "dvc_col", "dvc_qc", "dvc_type","in_date_from", "in_date_to", "move_date_from", "move_date_to","out_date_from", "out_date_to","loc_move", "sort_by", "data_view_item", "activity"];

  fields.forEach(field => {
    var element = document.getElementById(field);
    if (element) {
      val += "&" + field + "=" + encodeURIComponent(element.value);
    }
  });

  // Tambahkan context berdasarkan inventory type
  val += "&context=inv_" + inventoryType.toLowerCase();

  if (page === 'export') {
    var link = CONFIG.urlMenu + "data/data_item_export" + val;
    window.open(link, '_blank').focus();
    return;
  }

  val += "&p=" + page;
  
  // Tentukan link berdasarkan inventory type
  var linkPath = inventoryType === 'ECBS' ? "data_item_show_ecbs" : "data_item_show";
  var link = CONFIG.urlMenu + "data/" + linkPath + val;

  loadData(link);
}

// Alias functions untuk backward compatibility
function showDataEcbs(page) { showMainData(page); }
function showDataEcct(page) { showMainData(page); }
function showDataActivity(page) { showSecondaryData(page); }
function showDataAllItem(page) { showSecondaryData(page); }

// =================== PAGINATION HANDLING ===================
function handlePagination(page) {
  const isMainTable = (inventoryType === 'ECBS' && currentTable === 'ecbs') || 
                     (inventoryType === 'ECCT' && currentTable === 'ecct');
  
  if (isMainTable) {
    showMainData(page);
  } else {
    showSecondaryData(page);
  }
}

function getCurrentTableFunction() {
  const isMainTable = (inventoryType === 'ECBS' && currentTable === 'ecbs') || 
                     (inventoryType === 'ECCT' && currentTable === 'ecct');
  
  if (isMainTable) {
    return inventoryType === 'ECBS' ? 'showDataEcbs' : 'showDataEcct';
  } else {
    return inventoryType === 'ECBS' ? 'showDataActivity' : 'showDataAllItem';
  }
}

// =================== INPUT TAB FUNCTIONS ===================
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

// =================== INPUT SUBMISSION ===================
function submitInput(type) {
  let data = {};
  let url = "http://localhost/cdummy/inventory/input_process";
  let resultDiv = " ";
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
        refreshCurrentData();
      } else {
        resultDiv.className = 'input-result-message error';
      }
      resultDiv.style.display = 'block';
    }
  })
  .catch(error => {
      resultDiv.innerText = '❌ Error: ' + error.message;
  });
}

// =================== REFRESH FUNCTIONS ===================
function refreshCurrentData() {
  const isMainTable = (inventoryType === 'ECBS' && currentTable === 'ecbs') || 
                     (inventoryType === 'ECCT' && currentTable === 'ecct');
  
  if (isMainTable) {
    showMainData();
  } else {
    showSecondaryData();
  }
}

// Backward compatibility aliases
function refreshCurrentTable() { refreshCurrentData(); }

// =================== WINDOW ONLOAD ===================
window.onload = function() {
  if (document.readyState !== 'loading') {
    renderToolbar();
    showMainData();
  }
}
