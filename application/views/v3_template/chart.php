
    <script src="https://cdn.jsdelivr.net/npm/react@17.0.2/umd/react.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-dom@17.0.2/umd/react-dom.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prop-types@15.8.1/prop-types.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-apexcharts@1.7.0/dist/react-apexcharts.iife.min.js"></script>


    
<?php for($i=0;$i<(sizeof($chart));$i++){ ?> 

  <?php
    $color="'#CC2D31', '#337AB7', '#3C7639', '#F26321', '#E0AA0F', '#E3066F', '#00B3DC', '#72216D', '#333333'";
    if(isset($chart[$i]['color'])){ $color=$chart[$i]['color']; }
  ?> 

  
  <?php if($chart[$i]['type']=="line"){ ?>

  <?php
    $color="'#CC2D31', '#337AB7', '#3C7639', '#F26321', '#E0AA0F', '#E3066F', '#00B3DC', '#72216D', '#333333'";
    if(isset($chart[$i]['color'])){ $color=$chart[$i]['color']; }
  ?> 

    <script type="text/babel">
      const ApexChart = () => {
        const [state, setState] = React.useState({
          
            series: [
              <?php
                $max_value=0;
              ?>

              <?php for($j=0;$j<(sizeof($chart[$i]['value']));$j++){ ?> 
              {
                name: "<?php echo $chart[$i]['value'][$j]['label']; ?>",
                data: [
                  <?php
                    for($k=0;$k<(sizeof($chart[$i]['value'][$j]['value']));$k++){
                      if($k>0){ echo ",";}
                      $value=$chart[$i]['value'][$j]['value'][$k];
                      if($value>$max_value){ $max_value=$value; }
                      echo "'".$value."'";
                    }
                  ?>
                ]
              },
              <?php } ?>
            ],
            options: {
              
              title: {
                text: '<?php echo $chart[$i]['title']; ?>',
                align: 'center'
              },
              xaxis: {
                  categories: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
                      if($j>0){ echo ",";}
                      echo "'".$chart[$i]['label'][$j]."'";
                    }
                    ?>  
                  ],
                  title: { text: '' }
              },
              yaxis: {
                  title: { text: '' },
                  min: 0, max: <?php echo $max_value; ?>
              },
              chart: {
                height: <?php echo $chart[$i]['height']; ?>,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
              },
              colors: [<?php echo $color; ?>],
              dataLabels: { enabled: true, },
              stroke: { curve: 'smooth', width: 3 },
              grid: {
                borderColor: '#DDD',
                row: {
                  colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                  opacity: 0.5
                },
              },
              markers: { size: 1 },
              legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: 10,
                offsetX: -5,
                height: '<?php echo $chart[$i]['height']; ?>px'
              }
            },
        });

        

        return (
          <div>
            <div id="chart">
                <ReactApexChart options={state.options} series={state.series} type="line" height={<?php echo $chart[$i]['height']; ?>} />
              </div>
            <div id="html-dist"></div>
          </div>
        );
      }

      const domContainer = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
      ReactDOM.render(<ApexChart />, domContainer);
    </script>


  <?php }elseif($chart[$i]['type']=="donut"){ ?>

  <?php
    $color="'#CC2D31', '#337AB7', '#3C7639', '#F26321', '#E0AA0F', '#E3066F', '#00B3DC', '#72216D', '#333333'";
    if(isset($chart[$i]['color'])){ $color=$chart[$i]['color']; }
  ?> 

    <script type="text/babel">
      const ApexChart = () => {
        const [state, setState] = React.useState({
          
            series: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['value']));$j++){
                      if($j>0){ echo ",";}
                      echo "".$chart[$i]['value'][$j]."";
                    }
                    ?>  
            ],
            options: {
              labels: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
                      if($j>0){ echo ",";}
                      echo "'".$chart[$i]['label'][$j]."'";
                    }
                    ?>  
              ],
              title: {
                text: '<?php echo $chart[$i]['title']; ?>',
                align: 'center'
              },
              chart: { type: 'donut', height: <?php echo $chart[$i]['height']; ?> },
              colors: [<?php echo $color; ?>],
              responsive: [{
                breakpoint: 480,
                options: {
                  chart: {
                    width: <?php echo $chart[$i]['height']; ?>
                  },
                  legend: {
                    position: 'bottom'
                  }
                }
              }]
            },
          
          
        });

        

        return (
          <div>
            <div id="chart">
                <ReactApexChart options={state.options} series={state.series} type="donut" />
              </div>
            <div id="html-dist"></div>
          </div>
        );
      }

      const domContainer = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
      ReactDOM.render(<ApexChart />, domContainer);
    </script>
  
  <?php }elseif($chart[$i]['type']=="column"){ ?>

  <?php
    $color="'#CC2D31', '#337AB7', '#3C7639', '#F26321', '#E0AA0F', '#E3066F', '#00B3DC', '#72216D', '#333333'";
    if(isset($chart[$i]['color'])){ $color=$chart[$i]['color']; }
  ?> 

  <script type="text/babel">
      const ApexChart = () => {
        const [state, setState] = React.useState({
          
            series: [
              <?php
                $max_value=0;
              ?>

              <?php for($j=0;$j<(sizeof($chart[$i]['value']));$j++){ ?> 
              {
                name: "<?php echo $chart[$i]['value'][$j]['label']; ?>",
                data: [
                  <?php
                    for($k=0;$k<(sizeof($chart[$i]['value'][$j]['value']));$k++){
                      if($k>0){ echo ",";}
                      $value=$chart[$i]['value'][$j]['value'][$k];
                      if($value>$max_value){ $max_value=$value; }
                      echo "'".$value."'";
                    }
                  ?>
                ]
              },
              <?php } ?>
            ],
            options: {
              title: {
                text: '<?php echo $chart[$i]['title']; ?>',
                align: 'center'
              },
              chart: {
                type: 'bar',
                height: <?php echo $chart[$i]['height']; ?>,
                zoom: { enabled: false },
                toolbar: { show: false }
              },
              colors: [<?php echo $color; ?>],
              plotOptions: {
                bar: {
                  horizontal: false,
                  columnWidth: '65%',
                  borderRadius: 5,
                  borderRadiusApplication: 'end'
                },
              },
              dataLabels: {
                enabled: false
              },
              stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
              },
              xaxis: {
                  categories: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
                      if($j>0){ echo ",";}
                      echo "'".$chart[$i]['label'][$j]."'";
                    }
                    ?>  
                  ],
                  title: { text: '' }
              },
              yaxis: {
                title: {
                  text: ''
                }
              },
              fill: {
                opacity: 1
              },
              tooltip: {
                y: {
                  formatter: function (val) {
                    return "" + val + ""
                  }
                }
              },
              legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true
              }
            },
          
          
        });

        

        return (
          <div>
            <div id="chart">
                <ReactApexChart options={state.options} series={state.series} type="bar" height={<?php echo $chart[$i]['height']; ?>} />
              </div>
            <div id="html-dist"></div>
          </div>
        );
      }

      const domContainer = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
      ReactDOM.render(<ApexChart />, domContainer);
    </script>

  <?php }elseif($chart[$i]['type']=="column_stacked"){ ?>

  <?php
    $color="'#CC2D31', '#337AB7', '#3C7639', '#F26321', '#E0AA0F', '#E3066F', '#00B3DC', '#72216D', '#333333'";
    if(isset($chart[$i]['color'])){ $color=$chart[$i]['color']; }
  ?> 

    <script type="text/babel">
      const ApexChart = () => {
        const [state, setState] = React.useState({
          
            series: [
              <?php
                $max_value=0;
              ?>

              <?php for($j=0;$j<(sizeof($chart[$i]['value']));$j++){ ?> 
              {
                name: "<?php echo $chart[$i]['value'][$j]['label']; ?>",
                data: [
                  <?php
                    for($k=0;$k<(sizeof($chart[$i]['value'][$j]['value']));$k++){
                      if($k>0){ echo ",";}
                      $value=$chart[$i]['value'][$j]['value'][$k];
                      if($value>$max_value){ $max_value=$value; }
                      echo "'".$value."'";
                    }
                  ?>
                ]
              },
              <?php } ?>
            ],
            options: {
              xaxis: {
                  categories: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
                      if($j>0){ echo ",";}
                      echo "'".$chart[$i]['label'][$j]."'";
                    }
                    ?>  
                  ],
                  title: { text: '' }
              },
              yaxis: {
                stepSize: <?php echo $max_value; ?>
              },
              title: {
                text: '<?php echo $chart[$i]['title']; ?>',
                align: 'center'
              },
              chart: {
                type: 'bar',
                height: <?php echo $chart[$i]['height']; ?>,
                stacked: true,
                zoom: { enabled: false },
                toolbar: { show: false }
              },
              colors: [<?php echo $color; ?>],
              responsive: [{
                breakpoint: 480,
                options: {
                  legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                  }
                }
              }],
              plotOptions: {
                bar: {
                  horizontal: false,
                  borderRadius: 10,
                  borderRadiusApplication: 'end', // 'around', 'end'
                  borderRadiusWhenStacked: 'last', // 'all', 'last'
                  dataLabels: {
                    total: {
                      enabled: true,
                      style: {
                        fontSize: '13px',
                        fontWeight: 900
                      }
                    }
                  }
                },
              },
              legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: 0,
                showForSingleSeries: false,
                height: '<?php echo $chart[$i]['height']; ?>px',
              },
              fill: {
                opacity: 1
              }
            },
          
          
        });

        

        return (
          <div>
            <div id="chart">
                <ReactApexChart options={state.options} series={state.series} type="bar" height={<?php echo $chart[$i]['height']; ?>} />
              </div>
            <div id="html-dist"></div>
          </div>
        );
      }

      const domContainer = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
      ReactDOM.render(<ApexChart />, domContainer);
    </script>

 <?php }elseif($chart[$i]['type']=="radar"){ ?>

    <script type="text/babel">
      const ApexChart = () => {
        const [state, setState] = React.useState({
          
            series: [
              <?php
                $max_value=0;
              ?>

              <?php for($j=0;$j<(sizeof($chart[$i]['value']));$j++){ ?> 
              {
                name: "<?php echo $chart[$i]['value'][$j]['label']; ?>",
                data: [
                  <?php
                    for($k=0;$k<(sizeof($chart[$i]['value'][$j]['value']));$k++){
                      if($k>0){ echo ",";}
                      $value=$chart[$i]['value'][$j]['value'][$k];
                      if($value>$max_value){ $max_value=$value; }
                      echo "'".$value."'";
                    }
                  ?>
                ]
              },
              <?php } ?>
            ],
            options: {
              xaxis: {
                  categories: [
                    <?php
                    for($j=0;$j<(sizeof($chart[$i]['label']));$j++){
                      if($j>0){ echo ",";}
                      echo "'".$chart[$i]['label'][$j]."'";
                    }
                    ?>  
                  ],
                  title: { text: '' }
              },
              chart: {
                height: 350,
                type: 'radar',
                zoom: { enabled: false },
                toolbar: { show: false },
                dropShadow: {
                  enabled: true,
                  blur: 1,
                  left: 1,
                  top: 1
                }
              },
              colors: [<?php echo $color; ?>],
              title: {
                text: 'Radar Chart - Multi Series',
                align: 'center'
              },
              stroke: {
                width: 2
              },
              fill: {
                opacity: 0.1
              },
              markers: {
                size: 0
              },
              yaxis: {
                stepSize: <?php echo $max_value; ?>
              },
              legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -30,
                showForSingleSeries: false,
                height: '<?php echo $chart[$i]['height']; ?>px',
              },
            },
          
          
        });

        

        return (
          <div>
            <div id="chart">
                <ReactApexChart options={state.options} series={state.series} type="radar" height={<?php echo $chart[$i]['height']; ?>} />
              </div>
            <div id="html-dist"></div>
          </div>
        );
      }

      const domContainer = document.querySelector('#<?php echo $chart[$i]['name']; ?>');
      ReactDOM.render(<ApexChart />, domContainer);
    </script>

  <?php } ?>
  
<?php } ?>

      