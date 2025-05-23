<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$colname_Data = "-1";
if (isset($_POST['data'])) {
  $colname_Data = $_POST['data'];
}

$colname_Hora = "-1";
if (isset($_POST['hora'])) {
  $colname_Hora = $_POST['hora'];
}

$colname_Turma = "-1";
if (isset($_POST['ct'])) {
  $colname_Turma = $_POST['ct'];
}

$colname_Alunos = "-1";
if (isset($_POST['aluno'])) {
  $colname_Alunos = $_POST['aluno'];
}

if ($_POST['aluno']=="") {
	
	$insertGoTo = "index.php";	
header(sprintf("Location: %s", $insertGoTo));
	
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasListar = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = $colname_Turma AND turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);


	}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
  <head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

    <title>SMECEL - Sistema de Gestão Escolar</title>

    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Sistema de Gestão Escolar.">
    <link href="https://assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css" rel="stylesheet" type="text/css">
    <link href="css/app.css" rel="stylesheet" type="text/css">
    <link rel="icon" sizes="192x192" href="img/icone.png">
    <link rel="apple-touch-icon" href="img/icone.png">
	
	<style>
	body {font-size:12px;}
	#quebra {
    page-break-before: always;
	}
	

	</style>
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="alert('Atenção: Configure sua impressora para o tamanho A4 e formato RETRATO');self.print();">    




		
		
<?php 

   foreach ($colname_Alunos as $aluno=>$value) {
			 //echo "<br>Aluno: ".$value." - Turma: ".$row_TurmasListar['turma_nome'];
			 
			 ?>
			 
			 
			 
				<div style="text-align:justify; display:block; width:48%; height:245px; float:left; padding:2mm; margin:0 1mm 1mm 0; border:dotted 1px #000000;page-break-inside: avoid;">
				<h3 style="text-align:center"><small><?php echo $row_EscolaLogada['escola_nome']; ?></small></h3><br>
				<h4 style="text-align:center">COMUNICADO</h4>
				<br>
				<p>Senhores pais ou responsáveis,</p>
				<p>Solicitamos sua presença na escola, no dia <strong><?php echo $colname_Data; ?> às <?php echo $colname_Hora; ?></strong> para tratarmos de assuntos referente a vida escolar de <strong><?php echo $value; ?></strong> - <?php echo $row_TurmasListar['turma_nome']; ?>.</p>
				<p>O mesmo só entrará na presença do responsável.</p>
				<p>A direção.</p>
				
<p style="text-align:right">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('%d de %B de %Y', strtotime('today'));
?>
</p>
				
				
				</div>
				<span id="quebra"></span>		 
			 
			 
			 
<?php
        }		
?>
		
		
		
	

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
