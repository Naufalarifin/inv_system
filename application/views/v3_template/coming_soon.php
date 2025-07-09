<div class="container-fixed">
          <div class="grid justify-center py-5" style="padding-top:100px;">
        <?php for($i=0;$i<=1;$i++){ ?>
           <img alt="" class="dark:hidden max-h-[170px]" src="<?php echo $config['base_url']; ?>assets/media/illustrations/<?php echo $i; ?>.svg">
           <img alt="" class="light:hidden max-h-[170px]" src="<?php echo $config['base_url']; ?>assets/media/illustrations/<?php echo $i; ?>-dark.svg">
        <?php } ?>
          </div>
          <div class="text-lg font-medium text-gray-900 text-center">
           Coming Soon..
          </div>
          <div class="text-sm text-gray-700 text-center gap-1">
           Stay Tuned for Next Amazing Features
          </div>
</div>