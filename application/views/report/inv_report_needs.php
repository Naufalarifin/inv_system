<!-- Container -->
<div class="container-fixed">
    <div class="card min-w-full">
        <div class="card-header">
            <h3 class="card-title" style="width: 100%;">
                Inventory Report Needs
                
                <!-- Toggle Buttons - Horizontal Layout -->
                <div style="float: right; display: flex; align-items: center; gap: 15px;">
                    <!-- ECBS/ECCT Group -->
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" id="btn_ecbs" onclick="selectTech_needs('ecbs')">ECBS</button>
                        <button class="btn btn-sm btn-light" id="btn_ecct" onclick="selectTech_needs('ecct')">ECCT</button>
                    </div>
                    
                    <!-- APP/OSC Group -->
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" id="btn_app" onclick="selectType_needs('app')">APP</button>
                        <button class="btn btn-sm btn-light" id="btn_osc" onclick="selectType_needs('osc')">OSC</button>
                    </div>
                    
                    <!-- Export Button -->
                    <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportData();">
                        <i class="ki-filled ki-exit-down !text-base"></i>Export
                    </a>
                </div>
            </h3>
        </div>
        <div id="show_data_needs"></div>
    </div>
</div>

<script type="text/javascript">

    // Load default data on page load
    $(document).ready(function() {
        showData_needs();
    });
</script>
