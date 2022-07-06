<?php
date_default_timezone_set( 'Europe/Helsinki' );
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );

require __DIR__ . '/vendor/autoload.php';
$database = InfluxDB\Client::fromDSN( sprintf( 'influxdb://rolle:FiZG24rG4wmgYqL8LqoxRX2fr37mhs@%s:%s/%s', 'localhost', 8086, 'ruuvi' ) );
$client = $database->getClient();
$database = $client->selectDB( 'ruuvi' );
$result = $database->query( 'SELECT last(temperature) FROM ruuvi_measurements WHERE time > now() - 1h GROUP BY time(1h), "name" ORDER BY DESC LIMIT 1' );

$points = $result->getPoints();

// Tags
$sauna = $points[0];
$parveke = $points[1];
$olohuone = $points[2];
$makuuhuone = $points[3];

// Display
$sauna_temp = $sauna['last'];
$sauna_name = $sauna['name'];
$sauna_rawtime = strtotime( $sauna['time'] . ' UTC' );
$sauna_time = date( 'H:i:s', $sauna_rawtime );

$makuuhuone_temp = $makuuhuone['last'];
$makuuhuone_name = $makuuhuone['name'];
$makuuhuone_rawtime = strtotime( $makuuhuone['time'] . ' UTC' );
$makuuhuone_time = date( 'H:i:ss', $makuuhuone_rawtime );

$parveke_temp = $parveke['last'];
$parveke_name = $parveke['name'];
$parveke_rawtime = strtotime( $parveke['time'] . ' UTC' );
$parveke_time = date( 'H:i:s', $parveke_rawtime );

$olohuone_temp = $olohuone['last'];
$olohuone_name = $olohuone['name'];
$olohuone_rawtime = strtotime( $olohuone['time'] . ' UTC' );
$olohuone_time = date( 'H:i:s', $olohuone_rawtime );

if ( ! empty( $parveke_temp ) ) echo $parveke_name . ': ' . $parveke_temp . ' °C, ';
if ( ! empty( $makuuhuone_temp ) ) echo $makuuhuone_name . ': ' . $makuuhuone_temp . ' °C, ';
if ( ! empty( $olohuone_temp ) ) echo $olohuone_name . ': ' . $olohuone_temp . ' °C, ';
if ( ! empty( $sauna_temp ) ) echo $sauna_name . ': ' . $sauna_temp . ' °C, ';
echo 'Mitattu: ' . date( 'd.m.Y H:i:s' );
