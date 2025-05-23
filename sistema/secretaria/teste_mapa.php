<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Google Maps </title>
 <script src="http://maps.google.com/maps/api/js?key=AIzaSyBtM38OqsFbYPjwQEcWkWW1T6ed6heKD4Y&sensor=false"
            type="text/javascript"></script>
</head>
<body>
<div class="content">

<h1><b>Roteiro Definido</b></h1>
<hr/>
<div style="float: left;">
<h2><b>Indicações</b></h2>
  <dl>
    <dt><b>Nome do Local</b></dt>
    <dd>-------------</dd>

    <dt><b>Tipo de Local</b></dt>
    <dd>-------------</dd>

    <dt><b>Moradado Local</b></dt>
    <dd>---------------</dd>

    <dt><b>Código Postal e Localidade</b></dt>
    <dd>--------------------------</dd>
  </dl></div>



    <div id="map" style="width: 100%; height: 400px;"></div>

    <script type="text/javascript">
        var locations = [

          ['Parque estacionamento', -16.1444719, -39.6065786, 3],
          ['Praia do Norte', -16.0971017, -39.7244523, 2],
          ['Navio Gil Eanes', -16.019934, -39.753680, 1]
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: new google.maps.LatLng(-16.019934, -39.753680),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }
    </script>
</body>
</html>
 </div>