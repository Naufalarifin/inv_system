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
                        <button class="btn btn-sm btn-primary" id="btn_ecbs" onclick="selectTech('ecbs')">ECBS</button>
                        <button class="btn btn-sm btn-light" id="btn_ecct" onclick="selectTech('ecct')">ECCT</button>
                    </div>
                    
                    <!-- APP/OSC Group -->
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" id="btn_app" onclick="selectType('app')">APP</button>
                        <button class="btn btn-sm btn-light" id="btn_osc" onclick="selectType('osc')">OSC</button>
                    </div>
                    
                    <!-- Export Button -->
                    <a class="btn btn-sm btn-icon-lg btn-light" onclick="exportData();">
                        <i class="ki-filled ki-exit-down !text-base"></i>Export
                    </a>
                </div>
            </h3>
        </div>
        <div id="show_data">Loading...</div>
    </div>
</div>

<script type="text/javascript">
    var selectedTech = 'ecbs';
    var selectedType = 'app';
    
    function selectTech(tech) {
        selectedTech = tech;
        
        // Update button states
        document.getElementById('btn_ecbs').className = tech === 'ecbs' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        document.getElementById('btn_ecct').className = tech === 'ecct' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        
        showData();
    }
    
    function selectType(type) {
        selectedType = type;
        
        // Update button states
        document.getElementById('btn_app').className = type === 'app' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        document.getElementById('btn_osc').className = type === 'osc' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-light';
        
        showData();
    }
    
    function showData() {
        var link = "<?php echo base_url(); ?>inventory/report/report_" + selectedTech + "_" + selectedType + "_show";
        
        document.getElementById('show_data').innerHTML = '<div style="text-align: center; padding: 20px;">Loading data...</div>';
        $("#show_data").load(link);
    }
    
    function exportData() {
        var link = "<?php echo base_url(); ?>inventory/report/report_" + selectedTech + "_" + selectedType + "_export";
        window.open(link, '_blank').focus();
    }
    
    // Load default data on page load
    $(document).ready(function() {
        showData();
    });
</script>
