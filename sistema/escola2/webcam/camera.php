<!doctype html>
<html lang="pt">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

 <title>Tutorial WebCam Blog Jonathas Guerra</title>

 <script type="text/javascript" src="webcam.js"></script>

 <script language="JavaScript">

 function bater_foto()
 {
 Webcam.snap(function(data_uri)
 {
 document.getElementById('results').innerHTML = '<img id="base64image" src="'+data_uri+'"/><button onclick="salvar_foto();">Upload desta Foto</button>';
 });
 }

 function mostrar_camera()
 {
 Webcam.set({
 width: 640,
 height: 480,
 dest_width: 640,
 dest_height: 480,
 crop_width: 300,
 crop_height: 400,
 image_format: 'jpeg',
 jpeg_quality: 100,
 flip_horiz: true
 });
 Webcam.attach('#minha_camera');
 }

 function salvar_foto()
 {
 document.getElementById("carregando").innerHTML="Salvando, aguarde...";
 var file = document.getElementById("base64image").src;
 var formdata = new FormData();
 formdata.append("base64image", file);
 var ajax = new XMLHttpRequest();
 ajax.addEventListener("load", function(event) { upload_completo(event);}, false);
 ajax.open("POST", "upload.php");
 ajax.send(formdata);
 }

 function upload_completo(event)
 {
 document.getElementById("carregando").innerHTML="";
 var image_return=event.target.responseText;
 var showup=document.getElementById("completado").src=image_return;
 var showup2=document.getElementById("carregando").innerHTML='<b>Upload feito:</b>';
 }
 window.onload= mostrar_camera;
 </script>
 <style type="text/css">
 .container
 {
 float: left;
 width:320px;
 height: 480px;
 margin-right: 5px;
 padding: 5px;
 }
 #camera
 {
 background: #ff6666;
 height: 480px;
 }
 #previa
 {
 background: #ffc865;
 height: 480px;
 }
 #salva
 {
 background: #4dea02;
 height: 480px;
 }
 </style>
</head>
<body>
 <div class="container" id="camera"><b>Câmera:</b>
 <div id="minha_camera"></div><form><input type="button" value="Tirar Foto" onClick="bater_foto()"></form>
 </div>
 <div class="container" id="previa">
 <b>Prévia:</b><div id="results"></div>
 </div>
 <div class="container" id="salva">
 <span id="carregando"></span><img id="completado" src=""/>
 </div>
</body>
</htm>