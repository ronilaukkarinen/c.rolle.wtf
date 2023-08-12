<?php
date_default_timezone_set( 'Europe/Helsinki' );
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );
?>
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
<script>
  const labels = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
  ];

  const data = {
    labels: labels,
    datasets: [{
      label: 'My First dataset',
      backgroundColor: 'rgb(255, 99, 132)',
      borderColor: 'rgb(255, 99, 132)',
      data: [0, 10, 5, 2, 20, 30, 45],
    }]
  };

  const config = {
    type: 'line',
    data: data,
    options: {}
  };
</script>
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
        <span class="value" data-color="<?php echo $ruuvitag_temp; ?>"><?php echo $ruuvitag_temp; ?> <span class="unit">°C</span></span>
        <span class="label"><?php echo $ruuvitag_name; ?></span>
      </a>
    </div>

<?php
  }
}
