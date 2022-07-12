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
  $ruuvitag_temp = round( $point['last'], 2 );
  $ruuvitag_name = $point['name'];
  $ruuvitag_timestamp = strtotime( $point['time'] . ' UTC' );
  $ruuvitag_time = date( 'H:i:s', $timestamp );

  if ( ! empty( $ruuvitag_name ) ) {

    // Urls
    if ( 'makuuhuone' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/F3:1F:24:E5:A3:DE';
    } elseif ( 'olohuone' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/FB:27:EB:CA:8C:DA';
    } elseif ( 'lastenhuone' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/FA:D6:C7:D7:93:A8';
    } elseif ( 'parveke' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/C9:35:08:07:91:89';
    } elseif ( 'sauna' === strtolower( $ruuvitag_name ) ) {
      $ruuvitag_url = 'https://station.ruuvi.com/#/D0:25:AB:39:9E:F1';
    }

    echo $ruuvitag_name . ': ' . $ruuvitag_temp . ' °C, ';
  }
}
echo 'Mitattu: ' . date( 'd.m.Y H:i:s' );
