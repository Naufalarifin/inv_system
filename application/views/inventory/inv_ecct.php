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

<!-- Modal Overlay for Filter -->
<div id="modal_filter_overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:9998;"></div>

<!-- Modal Filter ECCT -->
<div class="modal" data-modal="true" id="modal_filter_ecct" style="display:none; position:fixed; z-index:9999; left:50%; top:50%; transform:translate(-50%,-50%); min-width:350px;">
  <div class="modal-content max-w-[600px] top-[10%]">
    <div class="modal-header">
      <h3 class="modal-title">ECCT Data Filter</h3>
      <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" onclick="closeModal('modal_filter_ecct')">
        <i class="ki-outline ki-cross"></i>
      </button>
    </div>
    <div class="modal-body">
      <div class="grid lg:grid-cols-3 gap-2.5 lg:gap-2.5">
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">Device Name</span>
          <input class="input" type="text" value="" id="dvc_name_ecct" placeholder="Device name..." />
        </div>
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">Device Code</span>
          <input class="input" type="text" value="" id="dvc_code_ecct" placeholder="Device code..." />
        </div>
        <div class="col-span-1 lg:col-span-1">
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
    <div class="modal-body m_foot">
      <div class="flex gap-4" style="float: right;">
        <button class="btn btn-light" data-modal-dismiss="true" onclick="closeModal('modal_filter_ecct')">Cancel</button>
        <button class="btn btn-primary" data-modal-dismiss="true" onclick="showDataEcct(); closeModal('modal_filter_ecct');" style="float:right;">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Filter All Item -->
<div class="modal" data-modal="true" id="modal_filter_item" style="display:none; position:fixed; z-index:9999; left:50%; top:50%; transform:translate(-50%,-50%); min-width:350px;">
  <div class="modal-content max-w-[600px] top-[10%]">
    <div class="modal-header">
      <h3 class="modal-title">Data Filter</h3>
      <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true" onclick="closeModal('modal_filter_item')">
        <i class="ki-outline ki-cross"></i>
      </button>
    </div>
    <div class="modal-body">
      <div class="grid lg:grid-cols-3 gap-2.5 lg:gap-2.5">
        <div class="col-span-1 lg:col-span-1">
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
        <div class="col-span-1 lg:col-span-1">
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
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">QC Status</span>
          <select class="select" id="dvc_qc">
            <option value="">All</option>
            <option value="0">Pending</option>
            <option value="1">Passed</option>
            <option value="2">Failed</option>
          </select>
        </div>
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">Date From</span>
          <input class="input calendar" type="text" value="" id="date_from" />
        </div>
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">Date To</span>
          <input class="input calendar" type="text" value="" id="date_to" />
        </div>
        <div class="col-span-1 lg:col-span-1">
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
        <div class="col-span-1 lg:col-span-1">
          <span class="form-hint">Sort By</span>
          <select class="select" id="sort_by">
            <option value="">None</option>
            <option value="id_act_asc">ID ASC</option>
            <option value="id_act_desc">ID DESC</option>
            <option value="dvc_sn_asc">Serial Number ASC</option>
            <option value="dvc_sn_desc">Serial Number DESC</option>
          </select>
        </div>
        <div class="col-span-1 lg:col-span-1">
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
    <div class="modal-body m_foot">
      <div class="flex gap-4" style="float: right;">
        <button class="btn btn-light" data-modal-dismiss onclick="closeModal('modal_filter_item')">Cancel</button>
        <button class="btn btn-primary" data-modal-dismiss onclick="showDataAllItem(); closeModal('modal_filter_item');" style="float:right;">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Input All Item -->
<div id="modal_overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
  <div id="modal_input_in" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
    <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 12px 12px 0 0;">
      <h3 style="font-size: 18px; font-weight: 600; color: #333; margin: 0;">📥 Input In - Barang Masuk</h3>
      <button onclick="closeInputModal()" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
    <div style="padding: 30px;">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Serial Number *</label>
        <input type="text" id="in_serial_number" placeholder="Contoh: ABC12D34E567890" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;" onfocus="this.style.borderColor='#1677ff'" onblur="this.style.borderColor='#ddd'" />
        <small style="color: #666; font-size: 12px;">Minimal 11 karakter untuk parsing otomatis</small>
      </div>
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">QC Status *</label>
        <select id="in_qc_status" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: white;">
          <option value="0">🟡 Pending</option>
          <option value="1">✅ Passed</option>
          <option value="2">❌ Failed</option>
        </select>
      </div>
    </div>
    <div style="padding: 20px 30px; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
      <button onclick="closeInputModal()" style="background: #6c757d; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">Cancel</button>
      <button onclick="submitInput('in')" style="background: #28a745; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">💾 Submit</button>
    </div>
  </div>
  <div id="modal_input_out" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
    <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 12px 12px 0 0;">
      <h3 style="font-size: 18px; font-weight: 600; color: #333; margin: 0;">📤 Input Out - Barang Keluar</h3>
      <button onclick="closeInputModal()" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
    <div style="padding: 30px;">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Serial Number *</label>
        <input type="text" id="out_serial_number" placeholder="Masukkan nomor seri yang akan keluar" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;" onfocus="this.style.borderColor='#1677ff'" onblur="this.style.borderColor='#ddd'" />
        <small style="color: #666; font-size: 12px;">Serial number harus sudah ada di database</small>
      </div>
    </div>
    <div style="padding: 20px 30px; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
      <button onclick="closeInputModal()" style="background: #6c757d; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">Cancel</button>
      <button onclick="submitInput('out')" style="background: #dc3545; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">📤 Submit</button>
    </div>
  </div>
  <div id="modal_input_move" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
    <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 12px 12px 0 0;">
      <h3 style="font-size: 18px; font-weight: 600; color: #333; margin: 0;">🚚 Input Move - Pindah Lokasi</h3>
      <button onclick="closeInputModal()" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">×</button>
    </div>
    <div style="padding: 30px;">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Serial Number *</label>
        <input type="text" id="move_serial_number" placeholder="Masukkan nomor seri yang akan dipindah" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;" onfocus="this.style.borderColor='#1677ff'" onblur="this.style.borderColor='#ddd'" />
        <small style="color: #666; font-size: 12px;">Serial number harus sudah ada di database</small>
      </div>
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">Lokasi Tujuan *</label>
        <select id="move_location" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: white;">
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
    <div style="padding: 20px 30px; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
      <button onclick="closeInputModal()" style="background: #6c757d; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">Cancel</button>
      <button onclick="submitInput('move')" style="background: #17a2b8; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-weight: 500;">🚚 Submit</button>
    </div>
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
  document.getElementById('btn_ecct').className = 'btn btn-sm ' + (currentTable === 'ecct' ? 'btn-primary' : 'btn-light');
  document.getElementById('btn_allitem').className = 'btn btn-sm ' + (currentTable === 'allitem' ? 'btn-primary' : 'btn-light');
}

function switchEcctType(type) {
  currentEcctType = type;
  renderToolbar();
  showDataEcct();
}

function openModal(id) {
  document.getElementById('modal_filter_overlay').style.display = '';
  document.getElementById(id).style.display = '';
}
function closeModal(id) {
  document.getElementById('modal_filter_overlay').style.display = 'none';
  document.getElementById(id).style.display = 'none';
}
window.onclick = function(event) {
  var overlay = document.getElementById('modal_filter_overlay');
  var modal1 = document.getElementById('modal_filter_ecct');
  var modal2 = document.getElementById('modal_filter_item');
  if (event.target === overlay) {
    overlay.style.display = 'none';
    modal1.style.display = 'none';
    modal2.style.display = 'none';
  }
}

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
  document.getElementById('modal_overlay').style.display = '';
  document.getElementById('modal_input_in').style.display = 'none';
  document.getElementById('modal_input_out').style.display = 'none';
  document.getElementById('modal_input_move').style.display = 'none';
  if(type === 'in') document.getElementById('modal_input_in').style.display = '';
  if(type === 'out') document.getElementById('modal_input_out').style.display = '';
  if(type === 'move') document.getElementById('modal_input_move').style.display = '';
}
function closeInputModal() {
  document.getElementById('modal_overlay').style.display = 'none';
  document.getElementById('modal_input_in').style.display = 'none';
  document.getElementById('modal_input_out').style.display = 'none';
  document.getElementById('modal_input_move').style.display = 'none';
}

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
  })
  .catch(err => alert('Gagal memproses data!'));
}

window.onload = function() {
  renderToolbar();
  showDataEcct();
}

const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
