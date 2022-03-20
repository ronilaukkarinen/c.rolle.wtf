<script src="https://code.jquery.com/jquery-latest.min.js"></script>
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
date_default_timezone_set('Europe/Helsinki');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
$host = 'localhost';
$port = '8086';
$dbname = 'ruuvi';
$client = new InfluxDB\Client($host, $port);
$database = InfluxDB\Client::fromDSN(sprintf('influxdb://user:pass@%s:%s/%s', $host, $port, $dbname));

$database = $client->selectDB('ruuvi');
$result = $database->query('SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 2h GROUP BY time(2h), "name" ORDER BY DESC LIMIT 1');
$points = $result->getPoints();

// Tags
$sauna = $points[0];
$parveke = $points[1];
$olohuone = $points[2];
$makuuhuone = $points[3];

// Display
$sauna_temp = $sauna['last'];
$sauna_name = $sauna['name'];
$sauna_rawtime = strtotime($sauna['time'] . ' UTC');
$sauna_time = date("H:i:s", $sauna_rawtime);

$makuuhuone_temp = $makuuhuone['last'];
$makuuhuone_name = $makuuhuone['name'];
$makuuhuone_rawtime = strtotime($makuuhuone['time'] . ' UTC');
$makuuhuone_time = date("H:i:ss", $makuuhuone_rawtime);

$parveke_temp = $parveke['last'];
$parveke_name = $parveke['name'];
$parveke_rawtime = strtotime($parveke['time'] . ' UTC');
$parveke_time = date("H:i:s", $parveke_rawtime);

$olohuone_temp = $olohuone['last'];
$olohuone_name = $olohuone['name'];
$olohuone_rawtime = strtotime($olohuone['time'] . ' UTC');
$olohuone_time = date("H:i:s", $olohuone_rawtime); ?>

  <div class="temp">
    <span class="value" data-color="<?php echo $parveke_temp; ?>"><?php echo $parveke_temp; ?> <span class="unit">°C</span></span>
    <span class="label"><?php echo $parveke_name; ?></span>
  </div>

  <div class="temp">
    <span class="value" data-color="<?php echo $makuuhuone_temp; ?>"><?php echo $makuuhuone_temp; ?> <span class="unit">°C</span></span>
    <span class="label"><?php echo $makuuhuone_name; ?></span>
  </div>

  <div class="temp">
    <span class="value" data-color="<?php echo $olohuone_temp; ?>"><?php echo $olohuone_temp; ?> <span class="unit">°C</span></span>
    <span class="label"><?php echo  $olohuone_name; ?></span>
  </div>

  <div class="temp">
    <span class="value" data-color="<?php echo $sauna_temp; ?>"><?php echo $sauna_temp; ?> <span class="unit">°C</span></span>
    <span class="label"><?php echo $sauna_name; ?></span>
  </div>

