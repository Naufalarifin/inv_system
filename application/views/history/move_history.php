<!-- Container -->
<div class="container-fixed">
<div class="card min-w-full">
    <div class="card-header">
        <h3 class="card-title" style="width: 100%;">
            Data History - MOVE Activities
            <div class="input-group input-sm" style="float: right;">
                <input class="input input-sm" placeholder="Search" type="text" value="" id="key_history" />
                <span class="btn btn-light btn-sm" data-modal-toggle="#modal_filter_history">Filter</span>
                <span class="btn btn-primary btn-sm" onclick="showMoveHistory();" id="btn_search_history">Search</span>
            </div>
            <a class="btn btn-sm btn-icon-lg btn-light" onclick="showMoveHistory('export');" style="float: right;">
                <i class="ki-filled ki-exit-down !text-base"></i>Export
            </a>
        </h3>
    </div>
    <div id="show_data_history"></div>
</div>
</div>

<!-- Modal Filter -->
<div class="modal" data-modal="true" id="modal_filter_history">
<div class="modal-content max-w-[600px] top-[10%]">
    <div class="modal-header">
        <h3 class="modal-title">MOVE History Filter</h3>
        <button class="btn btn-xs btn-icon btn-light" data-modal-dismiss="true">
            <i class="ki-outline ki-cross"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="grid lg:grid-cols-3 gap-2.5 lg:gap-2.5">
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Device SN</span>
                <input class="input" type="text" value="" id="dvc_sn" placeholder="Search by Device SN" />
            </div>
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Device Name</span>
                <input class="input" type="text" value="" id="dvc_name" placeholder="Search by Device Name" />
            </div>
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Admin</span>
                <input class="input" type="text" value="" id="admin" placeholder="Search by Admin" />
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
                <span class="form-hint">Sort By</span>
                <select class="select" id="sort_by">
                    <option value="">None</option>
                    <option value="tanggal_asc">Date ASC</option>
                    <option value="tanggal_desc">Date DESC</option>
                    <option value="dvc_sn_asc">Serial Number ASC</option>
                    <option value="dvc_sn_desc">Serial Number DESC</option>
                </select>
            </div>
            <div class="col-span-1 lg:col-span-1">
                <span class="form-hint">Data View</span>
                <select class="select" id="data_view_history">
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
            <button class="btn btn-primary" data-modal-dismiss="true" onclick="showMoveHistory();" style="float:right;">Submit</button>
        </div>
    </div>
</div>
</div>

<div id="loading" style="display: none;">
<div style="text-align: center; padding: 40px;">
    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="margin-top: 10px;">Loading data...</p>
</div>
</div>

<script type="text/javascript">
// Function untuk encode URL yang hilang
function encodeUrl(str) {
    return encodeURIComponent(str || '');
}

// ORIGINAL FUNCTION - JANGAN DIUBAH
function showMoveHistory(page = '', in_sort = '') {
    var loading = document.getElementById('loading').innerHTML;
    var val = "?";
    const arr = ["key_history", "dvc_sn", "dvc_name", "admin", "date_from", "date_to", "sort_by", "data_view_history"];
    
    for (let i = 0; i < arr.length; i++) {
        var element = document.getElementById(arr[i]);
        if (element) {
            val = val + "&" + arr[i] + "=" + encodeUrl(element.value);
        }
    }
    
    if (page == 'export') {
        var link = "<?php echo $config['url_menu']; ?>data/history_export" + val + "&activity_type=MOVE";
        window.open(link, '_blank').focus();
    } else {
        val = val + "&p=" + page + "&activity_type=MOVE";
        var link = "<?php echo $config['url_menu']; ?>data/history_show" + val;
        document.getElementById('show_data_history').innerHTML = loading;
        
        // Perbaiki AJAX call - tambahkan fallback jika jQuery tidak ada
        if (typeof $ !== 'undefined') {
            $("#show_data_history").load(link);
        } else {
            // Fallback menggunakan fetch
            fetch(link)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('show_data_history').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('show_data_history').innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Error loading data</div>';
                });
        }
    }
}

// Auto load data saat halaman dimuat - ORIGINAL
window.onload = function() {
    showMoveHistory();
}

// CSS untuk animasi loading - ORIGINAL
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script> 