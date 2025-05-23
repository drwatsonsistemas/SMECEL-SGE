<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
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

$colname_Vinculo = "-1";
if (isset($_GET['cod'])) {
  $colname_Vinculo = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs, func_id, func_nome, func_cpf, funcao_id, funcao_nome 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_id = %s", GetSQLValueString($colname_Vinculo, "int"));
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

if ($totalRows_Vinculo == 0) {
	$erro = "funcListar.php?erro";
	header(sprintf("Location: %s", $erro));
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = "
SELECT ocorrencia_id, ocorrencia_func_vinculo_id, ocorrencia_func_data, ocorrencia_func_texto, ocorrencia_func_tipo, ocorrencia_func_obs,
CASE ocorrencia_func_tipo 
WHEN 1 THEN 'ADVERTENCIA'
WHEN 2 THEN 'SUSPENSAO'
END ocorrencia_func_tipo_nome
FROM smc_ocorrencias_func
WHERE ocorrencia_func_vinculo_id = '$row_Vinculo[vinculo_id_funcionario]'
ORDER BY ocorrencia_func_data DESC 
";
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);


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
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"> 
<script src="js/locastyle.js"></script>
<style>

body {
  font-size: 12px;
    background-image:url(<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/marcadagua/brasao_republica.png<?php } ?>);
	background-repeat:no-repeat;
	background-position:center center;
	z-index:-999;
}
p { margin-bottom: 1px; }

page {
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
}
page[size="A4"] {
  width: 21cm;
  height: 29.7cm;
  padding: 5px;
 
}
page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;
}
@media print {
  body,
  page {
    margin: 0;
    box-shadow: 0;

  }
}

</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>

<!-- CONTEÚDO -->
<div class="ls-txt-center" style="margin:10px 0;">
<?php
	$url_atual = "$_SERVER[REQUEST_URI]";
	$url_atual = explode("/", $url_atual);
?>
<a href="imprimir/<?php echo $url_atual[3]; ?>" class="ls-btn-primary" target="_self">IMPRIMIR</a>
</div>
<page size="A4" style="padding:30px;">

<table>
	<tr>
		<td width="20%"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></td>
		<td width="80%">
			<p><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></p>
			<p>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -</p>
			<p>ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?></p>
			<p><?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?></p>
			<p>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?></p>
			<p><?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></p>
		</td>
	</tr>
</table>


<div class="row"><div class="col-xs-12"><p></p></div></div>

<div class="row">
  <div class="col-xs-12 ls-txt-center">
  
	<br><br><br><br><br><p><h1>CARTA DE ADVERTÊNCIA DISCIPLINAR</h1></p><br><br><br>
	
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
  <p style="line-height: 180%; text-align:justify; font-size:14px;">Nome: <?= $row_Vinculo['func_nome'] ?></p>
  <p style="line-height: 180%; text-align:justify; font-size:14px;">CPF: <?= $row_Vinculo['func_cpf'] ?></p> 
  <p style="line-height: 180%; text-align:justify; font-size:14px;">Função: <?= $row_Vinculo['funcao_nome'] ?></p> 

  <br><br><br>
  </div>
</div>


<div class="row">
  <div class="col-xs-12">
  <p style="line-height: 180%; text-align:justify; font-size:16px;">
    Vimos pela presente, aplicar-lhe advertência disciplinar pelo seguinte fato:</p><br> 
  </div>
</div>

<div class="row"><div class="col-xs-12">
    <p style="line-height: 180%; text-align:justify; font-size:16px;">
        <?= $row_Ocorrencia['ocorrencia_func_texto'] ?>
    </p>
</div></div>

<div class="row"><div class="col-xs-12">
<br><p style="line-height: 180%; text-align:justify; font-size:16px;">
Solicitamos que adeque seu comportamento às normas e costumes da Unidade de Ensino, afim de manter a disciplina necessária para uma boa convivência com seus colegas de trabalho. Deixamos registrado também que a reincidência nessa atitude poderá acarretar uma suspensão disciplinar ou até mesmo uma rescisão do contrato de trabalho por justa causa.
Por isso, evite voltar a praticar tal ato, para que as medidas cabíveis de acordo com a legislação em vigor não precisem ser tomadas.
</p><br><br><br><br><br><br>
</div>
<p style="text-align:right">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php 
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('%d de %B de %Y', strtotime('today'));
?>
</p>
<div class="row"><div class="col-xs-12"><p><br><br><br><br></p></div></div>
<p style="text-align:center">_________________________________________________________<br>Assinatura do Chefe imediato</p>
<div class="row"><div class="col-xs-12"><p></p></div></div>

<div class="row"><div class="col-xs-12"><p><br><br><br><br></p></div></div>
<p style="text-align:center">_________________________________________________________<br>Assinatura do Empregado</p>
<div class="row"><div class="col-xs-12"><p><br><br><br><br><br><br><br></p></div></div>

</div>
</div>



</page>



<!-- CONTEÚDO --> 

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>
 

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

?>