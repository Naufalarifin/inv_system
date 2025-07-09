<script>

<?php for($i=0;$i<(sizeof($chart));$i++){ ?>	

	<?php if($chart[$i]['type']=="Bar"){ ?>

		var <?php echo $chart[$i]['name']; ?> = {
			labels : [
				<?php
					for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
						if($j>0){ echo ",";}
						echo "'".$chart[$i]['label'][$j]."'";
					}
				?>	
			],
			datasets : [
				{
					fillColor : "#<?php echo $chart[$i]['color']; ?>",
					highlightFill: "#<?php echo $chart[$i]['hover']; ?>",
					data : [
					<?php
						for($j=0;$j<(sizeof($chart[$i]['value']));$j++){
							if($j>0){ echo ",";}
							echo "'".$chart[$i]['value'][$j]."'";
						}
					?>
					]
				}
			]
			
		};
		new Chart(document.getElementById("<?php echo $chart[$i]['name']; ?>").getContext("2d")).Bar(<?php echo $chart[$i]['name']; ?>);

	<?php }elseif($chart[$i]['type']=="Pie"){ ?>
		
		var <?php echo $chart[$i]['name']; ?> = [
			<?php for($j=0;$j<(sizeof($chart[$i]['label']));$j++){ ?>
			<?php if($j>0){ echo ",";} ?>
			{
				value: <?php echo $chart[$i]['value'][$j]; ?>,
				color:"#<?php echo $chart[$i]['color'][$j]; ?>",
				label: "<?php echo $chart[$i]['label'][$j]; ?>"
			}
			<?php } ?>
			
		];
		new Chart(document.getElementById("<?php echo $chart[$i]['name']; ?>").getContext("2d")).Pie(<?php echo $chart[$i]['name']; ?>);
	
	<?php }elseif($chart[$i]['type']=="Doughnut"){ ?>
		
		var <?php echo $chart[$i]['name']; ?> = [
			<?php for($j=0;$j<(sizeof($chart[$i]['label']));$j++){ ?>
			<?php if($j>0){ echo ",";} ?>
			{
				value: <?php echo $chart[$i]['value'][$j]; ?>,
				color:"#<?php echo $chart[$i]['color'][$j]; ?>",
				label: "<?php echo $chart[$i]['label'][$j]; ?>"
			}
			<?php } ?>
			
		];
		new Chart(document.getElementById("<?php echo $chart[$i]['name']; ?>").getContext("2d")).Doughnut(<?php echo $chart[$i]['name']; ?>);

	<?php }elseif($chart[$i]['type']=="Line"){ ?>

		var <?php echo $chart[$i]['name']; ?> = {
			labels : [
				<?php
					for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
						if($j>0){ echo ",";}
						echo "'".$chart[$i]['label'][$j]."'";
					}
				?>	
			],
			datasets : [
				{
					fillColor : "#<?php echo $chart[$i]['color']; ?>",
					highlightFill: "#<?php echo $chart[$i]['hover']; ?>",
					data : [
					<?php
						for($j=0;$j<(sizeof($chart[$i]['value']));$j++){
							if($j>0){ echo ",";}
							echo "'".$chart[$i]['value'][$j]."'";
						}
					?>
					]
				}
			],
			yaxis: { min: 0, max: 150000000 } 
			
		};
		new Chart(document.getElementById("<?php echo $chart[$i]['name']; ?>").getContext("2d")).Line(<?php echo $chart[$i]['name']; ?>);

	<?php } ?>

<?php } ?>

</script>