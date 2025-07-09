<script>

<?php for($i=0;$i<(sizeof($chart));$i++){ ?>	
	
	<?php if($chart[$i]['type']=="line"){ ?>
		class chart_<?php echo $chart[$i]['name']; ?> {
	      static init() {
	        const data = [
				<?php
					$max_value=0;
					for($j=0;$j<(sizeof($chart[$i]['value']));$j++){
						if($j>0){ echo ",";}
						$value=$chart[$i]['value'][$j];
						if($value>$max_value){ $max_value=$value; }
						echo "'".$value."'";
					}

					/*
					$tmp=1;
					for($k=1;$k<=sizeof(strlen($max_value));$k++){
						$tmp*=10;
					}
					$max_value=$tmp;
					*/

					$color=(isset($chart[$i]['color']) && $chart[$i]['color']!="") ? $chart[$i]['color'] : "primary";
				?>
	        ];
	        const categories = [
	        <?php
					for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
						if($j>0){ echo ",";}
						echo "'".$chart[$i]['label'][$j]."'";
					}
				?>	
	        ];

	        const options = {
	          series: [{
	            name: 'series1',
	            data: data
	          }],
	          chart: {
	            height: 250,
	            type: 'area',
	            toolbar: {
	              show: false
	            }
	          },
	          dataLabels: {
	            enabled: true
	          },
	          legend: {
	            show: false
	          },
	          stroke: {
	            curve: 'smooth',
	            show: true,
	            width: 3,
	            colors: ['var(--tw-<?php echo $color; ?>)']
	          },
	          xaxis: {
	            categories: categories,
	            axisBorder: {
	              show: true,
	            },
	            maxTicks: 12,
	            axisTicks: {
	              show: true
	            },
	            labels: {
	              style: {
	                colors: 'var(--tw-gray-500)',
	                fontSize: '12px'
	              }
	            },
	            crosshairs: {
	              position: 'front',
	              stroke: {
	                color: 'var(--tw-<?php echo $color; ?>)',
	                width: 1,
	                dashArray: 3
	              }
	            },
	            tooltip: {
	              enabled: false,
	              formatter: undefined,
	              offsetY: 0,
	              style: {
	                fontSize: '12px'
	              }
	            }
	          },
	          yaxis: {
	            min: 0,
	            max: <?php echo $max_value; ?>,
	            tickAmount: 5, // This will create 5 ticks: 0, 20, 40, 60, 80, 100
	            axisTicks: {
	              show: false
	            },
	            labels: {
	              style: {
	                colors: 'var(--tw-gray-500)',
	                fontSize: '12px'
	              },
	              formatter: (value) => {
	                return `${value}`;
	              }
	            }
	          },
	          tooltip: {
	            enabled: true,
	            custom({series, seriesIndex, dataPointIndex, w}) {
	              const number = parseInt(series[seriesIndex][dataPointIndex]);
	              const month = w.globals.seriesX[seriesIndex][dataPointIndex-1];
	              const monthName = categories[month];

	              const formatter = new Intl.NumberFormat('en-US', {
	                style: 'currency',
	                currency: 'USD',
	              });

	              //const formattedNumber = formatter.format(number);
	              const formattedNumber = number;

	              return (
	                `
	              <div class="flex flex-col gap-2 p-3.5">
	               <div class="font-medium text-2sm text-gray-600">
	                ${monthName} 
	               </div>
	               <div class="flex items-center gap-1.5">
	                <div class="font-semibold text-md text-gray-900">
	                 ${formattedNumber}
	                </div>
	               </div>
	              </div>
	              `
	              );
	            }
	          },
	          markers: {
	            size: 0,
	            colors: 'var(--tw-<?php echo $color; ?>-light)',
	            strokeColors: 'var(--tw-<?php echo $color; ?>)',
	            strokeWidth: 4,
	            strokeOpacity: 1,
	            strokeDashArray: 0,
	            fillOpacity: 1,
	            discrete: [],
	            shape: "circle",
	            radius: 2,
	            offsetX: 0,
	            offsetY: 0,
	            onClick: undefined,
	            onDblClick: undefined,
	            showNullDataPoints: true,
	            hover: {
	              size: 8,
	              sizeOffset: 0
	            }
	          },
	          fill: {
	            gradient: {
	              enabled: true,
	              opacityFrom: 0.25,
	              opacityTo: 0
	            }
	          },
	          grid: {
	            borderColor: 'var(--tw-gray-200)',
	            strokeDashArray: 5,
	            clipMarkers: false,
	            yaxis: {
	              lines: {
	                show: true
	              }
	            },
	            xaxis: {
	              lines: {
	                show: false
	              }
	            },
	          },
	        };

	        const element = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
	        if (!element) return;

	        const chart = new ApexCharts(element, options);
	        chart.render();
	      }
	      
	  	}

	<?php } ?>

<?php } ?>

</script>