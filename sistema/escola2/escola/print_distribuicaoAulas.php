<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/preencheHorario.php"; ?>

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
$query_VinculosProfessor = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
func_id, func_nome, funcao_id, funcao_nome, funcao_docencia 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' AND funcao_docencia = 'S'
";
$VinculosProfessor = mysql_query($query_VinculosProfessor, $SmecelNovo) or die(mysql_error());
$row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor);
$totalRows_VinculosProfessor = mysql_num_rows($VinculosProfessor);

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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

	<style>
	
	table.bordasimples {border-collapse: collapse;}
	table.bordasimples tr td {border-bottom:1px dotted #000000; padding:9px;}
	
	</style>

</head>
  <body>
	<!-- CONTEÚDO -->
		
  
		
<?php do { ?>
<div style="page-break-inside: avoid;">	

<div class="ls-box ls-board-box">
<header class="ls-info-header">
    <h2 class="ls-title-3"><?php echo $row_VinculosProfessor['func_nome']; ?></h2>
    <p class="ls-float-right ls-float-none-xs ls-small-info"><?php echo $row_VinculosProfessor['funcao_nome']; ?></p>
  </header>
  

  <h4 class="ls-txt-center">INTEGRAL</h4>
<table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "0" ); ?></td>
        </tr>
    </tbody>    
    
  </table>


  <h4 class="ls-txt-center">MATUTINO</h4>
<table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "1" ); ?></td>
        </tr>
    </tbody>    
    
  </table>

  <h4 class="ls-txt-center">VESPERTINO</h4>
<table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "2" ); ?></td>
        </tr>
    </tbody>    
    
  </table>

  <h4 class="ls-txt-center">NOTURNO</h4>
<table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "3" ); ?></td>
        </tr>
    </tbody>    
    
  </table>
  
</div>  
</div>
<?php } while ($row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor)); ?>
<!-- CONTEÚDO -->



    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

//mysql_free_result($CargaHoraria);

mysql_free_result($VinculosProfessor);

mysql_free_result($EscolaLogada);
?>
