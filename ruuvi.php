<?php
// CACHE START
$cachefile = 'ruuvi-cached.html';
$cachetime = 300;
if ( file_exists( $cachefile ) && time() - $cachetime < filemtime( $cachefile ) ) {
	// echo '<!-- Amazing hand crafted super cache by rolle, generated ' . date( 'H:i', filemtime( $cachefile ) ) . ' -->';
	include( $cachefile );
	exit;
}
ob_start();
// CACHE START
?>
<!DOCTYPE html>
<html lang="fi">
  <head>
    <meta charset="UTF-8">
    <title>Ruuvi raw data</title>
  </head>
<body>

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
$result = $database->query('SELECT LAST(temperature) FROM ruuvi_measurements GROUP BY TIME(10m), "name" ORDER BY DESC LIMIT 1');
$points = $result->getPoints();

$sauna_temp = $points[0]['last'];
$sauna_name = $points[0]['name'];
$sauna_rawtime = strtotime($points[0]['time'] . ' UTC');
$sauna_time = date("H:i", $sauna_rawtime);

$makuuhuone_temp = $points[1]['last'];
$makuuhuone_name = $points[1]['name'];
$makuuhuone_rawtime = strtotime($points[1]['time'] . ' UTC');
$makuuhuone_time = date("H:i", $makuuhuone_rawtime);

$parveke_temp = $points[2]['last'];
$parveke_name = $points[2]['name'];
$parveke_rawtime = strtotime($points[2]['time'] . ' UTC');
$parveke_time = date("H:i", $parveke_rawtime);

$olohuone_temp = $points[3]['last'];
$olohuone_name = $points[3]['name'];
$olohuone_rawtime = strtotime($points[3]['time'] . ' UTC');
$olohuone_time = date("H:i", $olohuone_rawtime);

//echo '<div class="temps">';
//echo '<span class="parveke"><b>' . $parveke_name . '</b>: ' . $parveke_temp . ' °C, </span>';
//echo '<span class="makuuhuone"><b>' . $makuuhuone_name . '</b>: ' . $makuuhuone_temp . ' °C, </span>';
//echo '<span class="olohuone"><b>' . $olohuone_name . '</b>: ' . $olohuone_temp . ' °C, </span>';
//echo '<span class="sauna"><b>' . $sauna_name . '</b>: ' . $sauna_temp . ' °C</span><span class="measured-time"> (Mitattu klo ' . $parveke_time . ')</span>';
//echo '</div>';

echo '<div class="temps">Ulkona: ' . $makuuhuone_temp . ' °C, ' . $parveke_name . ': ' . $parveke_temp . ' °C, ' . $olohuone_name . ': ' . $olohuone_temp . ' °C, ' . $sauna_name . ': ' . $sauna_temp . ' °C (Mitattu klo ' . $parveke_time . ')</div>';

?>

</body>
</html>
<?php
// CACHE END
$fp = fopen( $cachefile, 'w' );
fwrite( $fp, ob_get_contents() );
fclose( $fp );
ob_end_flush();
// CACHE END
