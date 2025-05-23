<!DOCTYPE html>
<html class="ls-theme-blue">
<head>
<style>

.mes {
	display: block;
	float: left;
	width:320px;
	height: auto;
	margin: 5px;
}

.dia {
	
	//display:block;
	float:left;
	margin: 3px 3px 0 0;
	padding: 2px;
	width: 10%;	
	height:30px; 
	color:black;
	background-color: yellow;
	text-align: left;
	border:#000 1px solid;
	font-size:12px;
}

.semana {
	//display:block;
	float:left;
	margin: 3px 3px 0 0;
	padding: 2px;
	width: 10%;	
	height:30px; 
	color:white;
	background-color: orange;
	text-align: left;
	border:#000 1px solid;

}

.nome-mes {
	display:block;
}

.limpa {
	clear:left;
}

</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php


function nomeMes($numero) {
	
switch ($numero) {
	case 1:
		$nomeMes = "JANEIRO";
		break;
	case 2:
		$nomeMes = "FEVEREIRO";
		break;
	case 3:
		$nomeMes = "MARÇO";
		break;
	case 4:
		$nomeMes = "ABRIL";
		break;
	case 5:
		$nomeMes = "MAIO";
		break;
	case 6:
		$nomeMes = "JUNHO";
		break;
	case 7:
		$nomeMes = "JULHO";
		break;
	case 8:
		$nomeMes = "AGOSTO";
		break;
	case 9:
		$nomeMes = "SETEMBRO";
		break;
	case 10:
		$nomeMes = "OUTUBRO";
		break;
	case 11:
		$nomeMes = "NOVEMBRO";
		break;
	case 12:
		$nomeMes = "DEZEMBRO";
		break;
}

return $nomeMes;	
	
}
	

$meses = 12;
$ano = 2020;


for ($mes = 1; $mes <= $meses; $mes++) {


echo "<div class='mes'>";
echo "<div class=\"nome-mes\">".nomeMes($mes)."</div>";

$primeiroDia = $ano.'-'.$mes.'-1';

$diasemanaNumero = (date('w', strtotime($primeiroDia)));

$dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

	echo "<div class='semana'>"; 
	echo "D";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "S";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "T";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "Q";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "Q";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "S";
	echo "</div>";

	echo "<div class='semana'>"; 
	echo "S";
	echo "</div>";


for ($fds = 1; $fds <= $diasemanaNumero; $fds++) {
	 
	echo "<div class='dia'>"; 
	echo "";
	echo "</div>"; 
 }



 for ($dia = 1; $dia <= $dias; $dia++) {
	 
	echo "<a href=\"#\"><div class='dia'>"; 
	echo $dia;
	echo "</div></a>"; 
 }
 
 
echo "<br>";

echo "<br class=\"1limpa\">";
echo "</div>"; 

}

?>
</body>
</html>