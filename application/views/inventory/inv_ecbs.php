<!-- Container -->
<div class="container-fixed">
<div class="card min-w-full">
    <div class="card-header">
        <h3 class="card-title" style="width: 100%;">
            Data ECBS Inventory
            <div class="input-group input-sm" style="float: right;">
                <!-- Toggle Buttons APP/OSC - BERSEBELAHAN -->
                <div style="margin-right: 10px; display: inline-flex; gap: 0;">
                    <button id="btn_app" class="btn btn-sm btn-primary" onclick="switchEcbsType('app')" style="border-radius: 4px 0 0 4px;">APP</button>
                    <button id="btn_osc" class="btn btn-sm btn-light" onclick="switchEcbsType('osc')" style="border-radius: 0 4px 4px 0; margin-left: -1px;">OSC</button>
                </div>
                
                <input class="input input-sm" placeholder="Search" type="text" value="" id="key_ecbs" />
                <span class="btn btn-light btn-sm" data-modal-toggle="#modal_filter_ecbs">Filter</span>
                <span class="btn btn-primary btn-sm" onclick="showDataEcbs();" id="btn_search_ecbs">Search</span>
            </div>
            <a class="btn btn-sm btn-icon-lg btn-light" onclick="showDataEcbs('export');" style="float: right;">
                <i class="ki-filled ki-exit-down !text-base"></i>Export
            </a>
        </h3>
    </div>
    <div id="show_data_ecbs"></div>
</div>
</div>

<!-- Modal Filter ECBS -->
<div class="modal" data-modal="true" id="modal_filter_ecbs">
<div class="modal-content max-w-[600px] top-[10%]">
    <div class="modal-header">
        <h3 class="modal-title">ECBS Data Filter</h3>
        <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true">
            <i class="ki-outline ki-cross"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="grid lg:grid-cols-3 gap-2.5 lg:gap-2.5">
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Device Name</span>
                <input class="input" type="text" value="" id="dvc_name_ecbs" placeholder="Device name..." />
            </div>
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Device Code</span>
                <input class="input" type="text" value="" id="dvc_code_ecbs" placeholder="Device code..." />
            </div>
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Data View</span>
                <select class="select" id="data_view_ecbs">
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
            <button class="btn btn-light" data-modal-dismiss="true">Cancel</button>
            <button class="btn btn-primary" data-modal-dismiss="true" onclick="showDataEcbs();" style="float:right;">Submit</button>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
// Global variable untuk track current type
var currentEcbsType = 'app';

// Function untuk encode URL
function encodeUrl(str) {
    return encodeURIComponent(str || '');
}

// Function untuk switch antara APP dan OSC
function switchEcbsType(type) {
    currentEcbsType = type;
    
    // Update button styles
    if (type === 'app') {
        document.getElementById('btn_app').className = 'btn btn-sm btn-primary';
        document.getElementById('btn_osc').className = 'btn btn-sm btn-light';
    } else {
        document.getElementById('btn_app').className = 'btn btn-sm btn-light';
        document.getElementById('btn_osc').className = 'btn btn-sm btn-primary';
    }
    
    // Reload data
    showDataEcbs();
}

// MAIN FUNCTION untuk show data ECBS
function showDataEcbs(page = '', in_sort = '') {
    var loading = '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>';
    var val = "?";
    const arr = ["key_ecbs", "dvc_name_ecbs", "dvc_code_ecbs", "data_view_ecbs"];
    
    for (let i = 0; i < arr.length; i++) {
        var element = document.getElementById(arr[i]);
        if (element) {
            // Map ECBS field names to original field names for backend
            var fieldName = arr[i].replace('_ecbs', '');
            if (fieldName === 'key') fieldName = 'key_ecbs';
            val = val + "&" + fieldName + "=" + encodeUrl(element.value);
        }
    }
    
    // Determine which endpoint to call based on current type
    var endpoint = currentEcbsType === 'app' ? 'data_inv_ecbs_app' : 'data_inv_ecbs_osc';
    
    if (page == 'export') {
        var link = "<?php echo $config['url_menu']; ?>data/" + endpoint + "_export" + val;
        window.open(link, '_blank').focus();
    } else {
        val = val + "&p=" + page;
        var link = "<?php echo $config['url_menu']; ?>data/" + endpoint + "_show" + val;
        document.getElementById('show_data_ecbs').innerHTML = loading;
        
        if (typeof $ !== 'undefined') {
            $("#show_data_ecbs").load(link);
        } else {
            fetch(link)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('show_data_ecbs').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('show_data_ecbs').innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Error loading data</div>';
                });
        }
    }
}

// Auto load data saat halaman dimuat
window.onload = function() {
    showDataEcbs();
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