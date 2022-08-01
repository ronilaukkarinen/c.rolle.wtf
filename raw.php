<?php
date_default_timezone_set( 'Europe/Helsinki' );
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );

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
    echo $ruuvitag_name . ': ' . $ruuvitag_temp . ' °C, ';
  }
}
echo 'Mitattu: ' . date( 'd.m.Y H:i:s' );
