<!-- Toolbar -->
<div class="pb-5">
    <!-- Container -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex flex-col gap-2">
            <h1 class="font-medium text-base text-gray-900" style="font-size:26px;">
                <?php echo ucwords($title_page); ?>
            </h1>
            <div class="flex items-center flex-wrap gap-1 text-sm">
                <a class="text-gray-700 hover:text-primary" href="<?php echo $config['base_url']; ?>">Home</a>
                <span class="text-gray-400 text-sm">/</span>
                <a class="text-gray-700 hover:text-primary" href="<?php echo $config['base_url']; ?>inventory">Inventory</a>
                <span class="text-gray-400 text-sm">/</span>
                <?php
                // Tentukan label dan link breadcrumb terakhir
                $breadcrumb_last = 'All Item';
                $breadcrumb_link = $config['base_url'] . 'inventory/all_item';
                if (isset($config['hal_sub'])) {
                    if ($config['hal_sub'] == 'inv_ecct') {
                        $breadcrumb_last = 'ECCT';
                        $breadcrumb_link = $config['base_url'] . 'inventory/inv_ecct';
                    } elseif ($config['hal_sub'] == 'inv_ecbs') {
                        $breadcrumb_last = 'ECBS';
                        $breadcrumb_link = $config['base_url'] . 'inventory/inv_ecbs';
                    } elseif ($config['hal_sub'] == 'inv_week') {
                        $breadcrumb_last = 'Weekly Period';
                        $breadcrumb_link = $config['base_url'] . 'inventory/inv_week';
                    } elseif ($config['hal_sub'] == 'massive_input') {
                        $breadcrumb_last = 'Massive Input';
                        $breadcrumb_link = $config['base_url'] . 'inventory/massive_input';
                    }
                }
                ?>
                <a class="text-gray-900 hover:text-primary" href="<?php echo $breadcrumb_link; ?>"><?php echo $breadcrumb_last; ?></a>
            </div>
        </div>
        
        <!-- Dynamic Menu Based on Current Page -->
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <?php
            // Determine current page context
            $current_page = '';
            if (isset($config['hal_sub'])) {
                $current_page = $config['hal_sub'];
            }

            // Display menu based on current page
            if ($current_page == 'inv_ecct'): ?>
                <!-- ECCT Menu - Controls JavaScript switchTable() -->
                <div class="menu menu-default flex flex-wrap justify-center gap-1.5 rounded-lg py-2">
                    <div class="menu-item">
                        <button id="banner_btn_stock" class="menu-link" onclick="switchTable('ecct');">
                            <span class="menu-icon">
                                <i class="ki-filled ki-chart-simple"></i>
                            </span>
                            <span class="menu-title">Stock</span>
                        </button>
                    </div>
                    <div class="menu-item">
                        <button id="banner_btn_activity" class="menu-link" onclick="switchTable('allitem');">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-search"></i>
                            </span>
                            <span class="menu-title">Activity</span>
                        </button>
                    </div>
                </div>
                
            <?php elseif ($current_page == 'inv_ecbs'): ?>
                <!-- ECBS Menu - Controls JavaScript switchTable() -->
                <div class="menu menu-default flex flex-wrap justify-center gap-1.5 rounded-lg py-2">
                    <div class="menu-item">
                        <button id="banner_btn_stock" class="menu-link" onclick="switchTable('ecbs');">
                            <span class="menu-icon">
                                <i class="ki-filled ki-chart-simple"></i>
                            </span>
                            <span class="menu-title">Stock</span>
                        </button>
                    </div>
                    <div class="menu-item">
                        <button id="banner_btn_activity" class="menu-link" onclick="switchTable('activity');">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-search"></i>
                            </span>
                            <span class="menu-title">Activity</span>
                        </button>
                    </div>
                </div>
                
            <?php elseif ($current_page == 'inv_week'): ?>
                <!-- Weekly Period Menu -->
                <div class="menu menu-default flex flex-wrap justify-center gap-1.5 rounded-lg py-2">
                    <div class="menu-item">
                        <a class="menu-link" href="<?php echo $config['base_url']; ?>inventory/inv_week">
                            <span class="menu-icon">
                                <i class="ki-filled ki-calendar"></i>
                            </span>
                            <span class="menu-title">Weekly Period</span>
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Default Menu (Other pages) -->
                <div class="menu menu-default flex flex-wrap justify-center gap-1.5 rounded-lg py-2">
                    <div class="menu-item">
                        <a class="menu-link" href="<?php echo $config['base_url']; ?>inventory/inv_ecct">
                            <span class="menu-icon">
                                <i class="ki-filled ki-chart-simple"></i>
                            </span>
                            <span class="menu-title">ECCT</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="<?php echo $config['base_url']; ?>inventory/inv_ecbs">
                            <span class="menu-icon">
                                <i class="ki-filled ki-chart-simple"></i>
                            </span>
                            <span class="menu-title">ECBS</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="<?php echo $config['base_url']; ?>inventory/inv_week">
                            <span class="menu-icon">
                                <i class="ki-filled ki-calendar"></i>
                            </span>
                            <span class="menu-title">Weekly Period</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- End of Container -->
</div>
<!-- End of Toolbar -->

<style>
.hero-bg {
    background-image: url('<?php echo $config['base_url']; ?>assets/media/images/2600x1200/bg-1.png');
}
.dark .hero-bg {
    background-image: url('<?php echo $config['base_url']; ?>assets/media/images/2600x1200/bg-1-dark.png');
}

/* Style untuk button menu agar terlihat seperti menu-link */

</style>

<div class="bg-center bg-cover bg-no-repeat hero-bg" style="margin-top:-80px;">
    <div class="container-fixed">
        <div class="flex flex-col items-center gap-2 lg:gap-3.5 py-4 lg:pt-5 lg:pb-10" style="padding:0px 10px 10px 10px;">
            <img src="<?php echo $config['base_url']; ?>images/banner/device-2.png" width="300px" />
        </div>
    </div>
</div>
