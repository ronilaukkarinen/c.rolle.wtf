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
    '-10to0'    : 'below-zero',
    '0to15'     : 'warm-ish',
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
require __DIR__ . '/vendor/autoload.php';
$database = InfluxDB\Client::fromDSN( sprintf( 'influxdb://rolle:FiZG24rG4wmgYqL8LqoxRX2fr37mhs@%s:%s/%s', 'localhost', 8086, 'ruuvi' ) );
$client = $database->getClient();
$database = $client->selectDB( 'ruuvi' );
$result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 2h GROUP BY time(2h), "name" ORDER BY DESC LIMIT 1' );

$points = $result->getPoints();

// If empty, print error and don't continue
if ( empty( $points ) ) {
  echo '<p style="margin:0;color:#ec1b4b;padding:20px;font-size:32px;font-weight:bolder;">Jokin meni vikaan.</p>';
  return;
}

// Loop through tags
sort( $points );
foreach ( $points as $point ) {
  $ruuvitag_temp = number_format( (float) ( $point['last'] ), 2 );
  $ruuvitag_name = $point['name'];

  if ( ! empty( $ruuvitag_name ) && '0.00' !== $ruuvitag_temp ) {

    // Urls
    if ( 'makuuhuone' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/F3:1F:24:E5:A3:DE';
    } elseif ( 'olohuone' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/FB:27:EB:CA:8C:DA';
    } elseif ( 'terassi' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/FA:D6:C7:D7:93:A8';
    } elseif ( 'katto' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/F3:19:89:68:A3:45';
    } elseif ( 'sauna' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/D0:25:AB:39:9E:F1';
    }
    ?>

    <div class="temp">
      <a href="<?php echo $ruuvitag_url; ?>">
        <span class="label"><?php echo $ruuvitag_name; ?></span>
        <span class="value" data-color="<?php echo $ruuvitag_temp; ?>"><?php echo $ruuvitag_temp; ?> <span class="unit">°C</span></span>
      </a>

      <div id="chart-<?php echo strtolower( $ruuvitag_name ); ?>"></div>
        <!-- Create custom apexchart for the last hour between 1 minute intervals for the current tag only -->
        <script>
        // Set colors based on from .freezing to .just-right
        if ( <?php echo $ruuvitag_temp; ?> <= -10 ) {
          var tempcolor = '#96cde4';
        } else if ( <?php echo $ruuvitag_temp; ?> <= 0 ) {
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
              $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h AND "name" = \'' . $ruuvitag_name . '\' GROUP BY time(1m), "name" ORDER BY DESC LIMIT 60' );
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
              $result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h GROUP BY time(1m), "name" ORDER BY DESC LIMIT 60' );
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
          document.querySelector("#chart-<?php echo strtolower( $ruuvitag_name ); ?>"),
          options
        );

        chart.render();
      </script>
    </div>

<?php
  }
}
