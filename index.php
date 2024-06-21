<?php // phpcs:disable
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
?>
<!doctype html>
<html>
<head>
<title>Lämpötilat</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style type="text/css">
body {
  background-color: #0d1117;
  color: #f0f6fc;
  font-family: 'Inter', -apple-system, 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif;
}

a {
  color: #fff;
  text-decoration: none;
  display: grid;
  transition: 200ms all;
}

a:hover {
  opacity: .6;
}

.temp > span {
  display: block;
  line-height: 1.2;
  margin: .5rem 0;
}

.lastupdated,
.temp {
  padding: 20px 30px;
  max-width: 320px;
}

.temp + .temp {
  padding-top: 0;
}

.temp .value {
  font-size: 80px;
  font-weight: 600;
}

.temp .label {
  font-size: 12px;
  opacity: .5;
  margin-bottom: 10px;
}

.lastupdated {
  font-size: 12px;
}

.lastupdated .text {
  opacity: .4;
}

.lastupdated .time {
  opacity: .6;
}

.temp .unit {
  font-size: 32px;
  opacity: .7;
  font-weight: 400;
}

.freezing {
  color: #96cde4;
  fill: #96cde4;
}

.below-zero {
  color: #1b8aec;
  fill: #1b8aec;
}

.warm-ish {
  color: #1bec9f;
  fill: #1bec9f;
}

.warming {
  color: #ea7662;
  fill: #ea7662;
}

.red {
  color: #ec1b4b;
  fill: #ec1b4b;
}

.just-right {
  color: #1ccc5c;
  fill: #1ccc5c;
}

.desktop {
  width: calc(100vw - 80px);
}

.mobile {
  width: calc(100vw - 50px);
}

@media (min-width: 501px) {
  .mobile {
    display: none;
  }
}

@media (max-width: 500px) {
  .desktop {
    display: none;
  }

  .lastupdated,
  .temp {
    padding: 20px 15px;
  }

  .temp .value {
    font-size: 62px;
  }
}

.apexcharts-tooltip {
  box-shadow: none !important;
  background: #000 !important;
}

.apexcharts-tooltip .apexcharts-tooltip-title {
  border: 0 !important;
  background: #000 !important;
  margin-bottom: 0 !important;
}

.apexcharts-tooltip-marker {
  display: none !important;
}

.apecharts-marker {
  fill: #0d1117 !important;
}
</style>

</head>
<body>
  <div class="temps">

    <div class="temp-makuuhuone"></div>
    <div class="temp-katto"></div>
    <div class="temp-olohuone"></div>
    <div class="temp-terassi"></div>
    <div class="temp-sauna"></div>

  </div>

  <p class="lastupdated"><span class="text">Viimeksi päivitetty</span> <span class="time"><span class="timestamp" id="value">0</span> s</span> <span class="text">sitten</span></p>

<script>
var obj = document.getElementById('value');
var current = parseInt(obj.innerHTML);
var secondtimer = setInterval(function(){
  current++;
  obj.innerHTML = current;
}, 1000);

jQuery( document ).ready(function() {
  const seconds = 10;

  jQuery('.temps').fadeIn();
  jQuery('.temp-makuuhuone').load('https://c.rolle.wtf/temp-makuuhuone.php');
  jQuery('.temp-katto').load('https://c.rolle.wtf/temp-katto.php');
  jQuery('.temp-olohuone').load('https://c.rolle.wtf/temp-olohuone.php');
  jQuery('.temp-terassi').load('https://c.rolle.wtf/temp-terassi.php');
  jQuery('.temp-sauna').load('https://c.rolle.wtf/temp-sauna.php');

  // Load data separately for x seconds with .load from temp-<tag>.php
  setInterval(function(){
    jQuery('.temp-makuuhuone').load('https://c.rolle.wtf/temp-makuuhuone.php');
    jQuery('.temp-katto').load('https://c.rolle.wtf/temp-katto.php');
    jQuery('.temp-olohuone').load('https://c.rolle.wtf/temp-olohuone.php');
    jQuery('.temp-terassi').load('https://c.rolle.wtf/temp-terassi.php');
    jQuery('.temp-sauna').load('https://c.rolle.wtf/temp-sauna.php');
    document.getElementById('value').innerHTML = '0';
    current = 0
  }, seconds * 1000);

});
</script>
</body>
</html>
