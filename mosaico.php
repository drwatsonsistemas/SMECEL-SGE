<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mosaico estudantil</title>

<style>
body {
	margin:0px;
	background-color:#000;
}
</style>

<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">


    

</head>

<body>


<?php 


/*

$diretoria = "aluno/fotos/"; // esta linha não precisas é só um exemplo do conteudo que a variável vai ter

// selecionar só .jpg
$imagens = glob($diretoria . "*.jpg");

shuffle($imagens);

$num = 0;
$id_imagem = 1;
// fazer echo de cada imagem
foreach($imagens as $imagem){

  $aleatorio = rand(20,20);

  //echo '<img id="imagem_'.$id_imagem.'" style="float:left" src="'.$imagem.'" width="'.$aleatorio.'%"/>';
  
  echo '<b id="imagem_'.$id_imagem.'"></b>';
	

  $num++;
  $id_imagem++;

	if ($num == 11) {
		break;
	}

}

*/


?>

<?php for ($i = 1; $i <= 160; $i++) { ?>

<div id="imagem_<?php echo $i; ?>"></div>

<?php } ?>


</body>


<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
<script>

var tid = setTimeout(mycode, 500);
function mycode() {
var aleatorio = Math.floor(Math.random() * 160 + 1);
jQuery.ajax({
type: "POST",
url: "gera_mosaico.php",
success: function (data) {

	$("#imagem_" + aleatorio).fadeOut(4000);;
	$("#imagem_" + aleatorio).hide();
	$("#imagem_" + aleatorio).html(data).fadeIn('1000');
	$("#imagem_" + aleatorio).fadeIn('1000');
}
});	
  tid = setTimeout(mycode, 500); // repeat myself
}
function abortTimer() { // to be called when you want to stop the timer
  clearTimeout(tid);
}

/*
$(function() {
  setTimeout(function(){ 
    $("body").append(" o timeout aconteceu.");
	alert("oi");
  }, 3000);
});
$(document).ready(function () {
setTimeout(function () {
var aleatorio = Math.floor(Math.random() * 40 + 1);
jQuery.ajax({
type: "POST",
url: "gera_mosaico.php",
success: function (data) {
	$("#imagem_" + aleatorio).html(data);
}
});
}, 1000); 
});
*/

</script>


</html>