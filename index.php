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
  font-size: 42px;
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
}

.below-zero {
  color: #1b8aec;
}

.warm-ish {
  color: #1bec9f;
}

.warming {
  color: #ea7662;
}

.red {
  color: #ec1b4b;
}

.just-right {
  color: #1ccc5c;
}

@media (max-width: 500px) {
  .lastupdated,
  .temp {
    padding: 20px 15px;
  }

  .temp .value {
    font-size: 62px;
  }
}
</style>

</head>
<body>
  <div class="temps"></div>

  <p class="lastupdated"><span class="text">Viimeksi päivitetty</span> <span class="time"><span class="timestamp" id="value">0</span> s</span> <span class="text">sitten</span></p>

<script>
var obj = document.getElementById('value');
var current = parseInt(obj.innerHTML);
var secondtimer = setInterval(function(){
  current++;
  obj.innerHTML = current;
}, 1000);

jQuery( document ).ready(function() {
  const seconds = 1;

  jQuery('.temps').fadeIn();
  jQuery('.temps').load('https://c.rolle.wtf/temps.php');

  var refreshId = setInterval(function() {
    jQuery(".temps").load('https://c.rolle.wtf/temps.php');
    document.getElementById('value').innerHTML = '0';
    current = 0
  }, seconds * 1000);
});
</script>
</body>
</html>
