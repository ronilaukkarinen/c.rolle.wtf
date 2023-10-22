<?php
date_default_timezone_set( 'Europe/Helsinki' );
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );
?>
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function(){

  var mc = {
    '-9000to-10': 'freezing',
    '-10to1'    : 'below-zero',
    '1.01to15'  : 'warm-ish',
    '15to21.99' : 'just-right',
    '22to900'   : 'warming',
    '25to900'   : 'red',
  };

function between(x, min, max) {
  return x >= min && x <= max;
}

var dc;
var first;
var second;
var th;

$('.value').each(function(index) {
    th = $(this);
    dc = parseInt($(this).attr('data-color'), 10);

    $.each(mc, function(name, value) {

      first = parseInt(name.split('to')[0],10);
      second = parseInt(name.split('to')[1],10);
      // console.log(between(dc, first, second));

      if ( between(dc, first, second) ) {
        th.addClass(value);
      }

    });
  });
});
</script>
<?php
// Define name
$ruuvitag_name = 'Sauna';

require __DIR__ . '/vendor/autoload.php';
$database = InfluxDB\Client::fromDSN( sprintf( 'influxdb://rolle:FiZG24rG4wmgYqL8LqoxRX2fr37mhs@%s:%s/%s', 'localhost', 8086, 'ruuvi' ) );
$client = $database->getClient();
$database = $client->selectDB( 'ruuvi' );
$result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 2h GROUP BY time(2h), "name" ORDER BY DESC LIMIT 1' );

// Get only sauna temp
$result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h AND "name" = \'Sauna\' GROUP BY time(2h), "name" ORDER BY DESC LIMIT 60' );

$points = $result->getPoints();

// If empty, print error and don't continue
if ( empty( $points ) ) {
  echo '<p style="margin:0;color:#ec1b4b;padding:20px;font-size:32px;font-weight:bolder;">Jokin meni vikaan.</p>';
  return;
}
?>

<!-- Get current temperature -->
<div class="temp">
  <span class="label"><?php echo $ruuvitag_name; ?></span>
  <span class="value" data-color="<?php echo number_format( (float) ( $points[0]['last'] ), 2 ); ?>"><?php echo number_format( (float) ( $points[0]['last'] ), 2 ); ?> <span class="unit">°C</span></span>

  <?php
  // Define temp
  $ruuvitag_temp = number_format( (float) ( $points[0]['last'] ), 2 );

  // Re-define name
  $ruuvitag_name = strtolower( $ruuvitag_name );
  ?>

  <!-- Get chart -->
  <div id="chart-<?php echo $ruuvitag_name; ?>-mobile" class="mobile"></div>
  <div id="chart-<?php echo $ruuvitag_name; ?>-desktop" class="desktop"></div>

  <!-- Create custom apexchart for the last hour between 1 minute intervals for the current tag only -->
  <script>
    // Set colors based on from .freezing to .just-right
    if ( <?php echo $ruuvitag_temp; ?> <= -10 ) {
      var tempcolor = '#96cde4';
    } else if ( <?php echo $ruuvitag_temp; ?> <= 1 ) {
      var tempcolor = '#1b8aec';
    } else if ( <?php echo $ruuvitag_temp; ?> <= 15 ) {
      var tempcolor = '#1bec9f';
    } else if ( <?php echo $ruuvitag_temp; ?> <= 21.99 ) {
      var tempcolor = '#1ccc5c';
    } else if ( <?php echo $ruuvitag_temp; ?> <= 25 ) {
      var tempcolor = '#ea7662';
    } else {
      var tempcolor = '#ec1b4b';
    }

    var options = {
      chart: {
        height: 150,
        width: 320,
        type: 'line',
        zoom: {
          enabled: false
        },
        toolbar: {
          show: false
        },
        animations: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false
      },
      stroke: {
        curve: 'smooth',
        width: 2,
        colors: [tempcolor]
      },
      tooltip: {
        enabled: true,
        theme: 'dark',
        background: '#1b1b1b'
      },
      series: [{
        name: 'Lämpötila',
        data: [
          <?php
          $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h AND "name" = \'Sauna\' GROUP BY time(1m), "name" ORDER BY DESC LIMIT 60' );
          $points = $result->getPoints();

          // Reverse array
          $points = array_reverse( $points );

          foreach ( $points as $point ) {
            echo $point['last'] . ',';
          }
          ?>
        ]
      }],
      grid: {
        borderColor: 'transparent',
        row: {
          colors: ['transparent', 'transparent'],
          opacity: 0.2
        }
      },
      yaxis: {
        show: false,
        lines: {
          show: false
        }
      },
      xaxis: {
        lines: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: false
        },
        show: false,
        categories: [
          <?php
          $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h AND "name" = \'Sauna\' GROUP BY time(1m), "name" ORDER BY DESC LIMIT 60' );
          $points = $result->getPoints();

          // Reverse array
          $points = array_reverse( $points );

          foreach ( $points as $point ) {
            echo "'" . date( 'H:i', strtotime( $point['time'] ) ) . "',";
          }
          ?>
        ]
      }
    }

    var chart = new ApexCharts(
      document.querySelector("#chart-<?php echo strtolower( $ruuvitag_name ); ?>-mobile"),
      options
    );

    chart.render();
  </script>

  <!-- Desktop chart -->
  <script>
    var options_desktop = {
      chart: {
        height: 450,
        width: '100%',
        type: 'line',
        zoom: {
          enabled: false
        },
        toolbar: {
          show: false
        },
        animations: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false
      },
      stroke: {
        curve: 'smooth',
        width: 2,
        colors: [tempcolor]
      },
      tooltip: {
        enabled: true,
        theme: 'dark',
        background: '#1b1b1b'
      },
      series: [{
        name: 'Lämpötila',
        data: [
          <?php
          // Get points for the last 12 hours
          $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 12h AND "name" = \'Sauna\' GROUP BY time(1m), "name" ORDER BY DESC LIMIT 720' );
          $points = $result->getPoints();

          // Reverse array
          $points = array_reverse( $points );

          foreach ( $points as $point ) {
            echo $point['last'] . ',';
          }
          ?>
        ]
      }],
      grid: {
        borderColor: 'transparent',
        row: {
          colors: ['transparent', 'transparent'],
          opacity: 0.2
        }
      },
      yaxis: {
        min: 15,
        max: 100,
        show: false,
        lines: {
          show: false
        }
      },
      xaxis: {
        lines: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: false
        },
        show: false,
        categories: [
          <?php
          // Get points for the last 12 hours
          $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 12h AND "name" = \'Sauna\' GROUP BY time(1m), "name" ORDER BY DESC LIMIT 720' );
          $points = $result->getPoints();

          // Reverse array
          $points = array_reverse( $points );

          foreach ( $points as $point ) {
            echo "'" . date( 'H:i', strtotime( $point['time'] ) ) . "',";
          }
          ?>
        ]
      }
    }

    var chart_desktop = new ApexCharts(
      document.querySelector("#chart-<?php echo strtolower( $ruuvitag_name ); ?>-desktop"),
      options_desktop
    );

    chart_desktop.render();
  </script>

</div>
