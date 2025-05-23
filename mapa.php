<?php
/**
 * gMaps Class
 *
 * Pega as informações de latitude, longitude e zoom de um endereço usando a API do Google Maps
 *
 * @author Thiago Belem <contato@thiagobelem.net>
 
class gMaps {
  private $mapsKey;
  function __construct($key = null) {
    if (!is_null($key)) {
      $this->mapsKey = $key;
    }
  }
  function carregaUrl($url) {
    if (function_exists('curl_init')) {
      $cURL = curl_init($url);
      curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
      $resultado = curl_exec($cURL);
      curl_close($cURL);
    } else {
      $resultado = file_get_contents($url);
    }
    if (!$resultado) {
      trigger_error('Não foi possível carregar o endereço: <strong>' . $url . '</strong>');
    } else {
      return $resultado;
    }
  }
  function geoLocal($endereco) {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?key={$this->mapsKey}&address=" . urlencode($endereco);
    $data = json_decode($this->carregaUrl($url));
    
    //if ($data->status === 'OK') {
	if ($data->status === 'OK') {
      return $data->results[0]->geometry->location;
    } else {
      return false;
    }
  }
}

// Instancia a classe
$gmaps = new gMaps('AIzaSyAbZsF339JiVZUxOWssg2SEgJGY5QUoG5M');
// Pega os dados (latitude, longitude e zoom) do endereço:
$endereco = 'Av. Brasil, 1453, Rio de Janeiro, RJ';
$dados = $gmaps->geoLocal($endereco);
// Exibe os dados encontrados:
print_r($dados);

*/
?>
<html>
<head>

<script src="http://maps.google.com/maps?file=api&v=2&key=AIzaSyAbZsF339JiVZUxOWssg2SEgJGY5QUoG5M" type="text/javascript"></script>
<script>


if (GBrowserIsCompatible()) {
    var map = new GMap2(document.getElementById("googleMap"));
    var lat = -16.087973; // Latitude do marcador
    var lon = -39.616063; // Longitude do marcador
    var zoom = 1; // Zoom

    map.addControl(new GMapTypeControl());
    map.addControl(new GLargeMapControl());
    map.setCenter(new GLatLng(lat, lon), zoom);

    var marker = new GMarker(new GLatLng(lat,lon));

    GEvent.addListener(marker, "click", function() {
      marker.openInfoWindowHtml("Texto");
    });

    map.addOverlay(marker);
    map.setCenter(point, zoom);
  }


</script>

<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>


<div id="googleMap"></div>





</body>






</html>