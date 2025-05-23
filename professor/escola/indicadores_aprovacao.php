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
SELECT *
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AI = "SELECT * FROM smc_aprovacao_saeb_iniciais_escola WHERE ID_ESCOLA = $row_EscolaLogada[escola_inep]";
$Ibge_AI = mysql_query($query_Ibge_AI, $SmecelNovo) or die(mysql_error());
$row_Ibge_AI = mysql_fetch_assoc($Ibge_AI);
$totalRows_Ibge_AI = mysql_num_rows($Ibge_AI);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AF = "SELECT * FROM smc_aprovacao_saeb_finais_escola WHERE ID_ESCOLA = $row_EscolaLogada[escola_inep]";
$Ibge_AF = mysql_query($query_Ibge_AF, $SmecelNovo) or die(mysql_error());
$row_Ibge_AF = mysql_fetch_assoc($Ibge_AF);
$totalRows_Ibge_AF = mysql_num_rows($Ibge_AF);


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
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">TAXA DE APROVAÇÃO</h1>
		<!-- CONTEÚDO -->

    <div class="ls-group-btn ls-group-active">
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right ls-active" href="indicadores_aprovacao.php">Taxa de Aprovação</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_saeb.php">Indicadores SAEB</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_ideb.php">Indicadores IDEB</a>
    </div>  

    <br><br>


    <div class="ls-box">INEP ESCOLA: <strong><?php echo $row_EscolaLogada['escola_inep']; ?></strong></div>
		
		
    <div class="ls-box">

      <h3>TAXA DE APROVAÇÃO - ANOS INICIAIS</h3>

    <table class="ls-table">

    <tr>

      <?php for ($year = 2005; $year <= 2023; $year += 2) { ?>

      <th class="ls-txt-center"><?php echo $year; ?></th>

      <?php } ?>
      
    </tr>

    <tr>

      <?php for ($year = 2005; $year <= 2023; $year += 2) { ?>

      <td class="ls-txt-center">

      <div class="row">  
      <div class="col-md-6 ls-no-padding-right">
      <span class="ls-btn-primary ls-btn-block ls-no-radius">1º ao 5º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">1º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">2º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">3º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">4º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">5º</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">P</span><br>
      </div>  
      <div class="col-md-6 ls-no-padding-left">
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_SI_4']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_SI']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_1']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_2']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_3']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_APROVACAO_'.$year.'_4']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo number_format($row_Ibge_AI['VL_INDICADOR_REND_'.$year.''], 2, '.', ' '); ?>&nbsp;</span><br>
      </div>  
      </div>
      
      </td>

      <?php } ?>
      
    </tr>

    </table>

    </div>



    <div class="ls-box">

<h3>TAXA DE APROVAÇÃO - ANOS FINAIS</h3>

<table class="ls-table">

<tr>

<?php for ($year = 2005; $year <= 2023; $year += 2) { ?>

<th class="ls-txt-center"><?php echo $year; ?></th>

<?php } ?>

</tr>

<tr>

<?php for ($year = 2005; $year <= 2023; $year += 2) { ?>

<td class="ls-txt-center">

<div class="row">  
<div class="col-md-6 ls-no-padding-right">
<span class="ls-btn-primary ls-btn-block ls-no-radius">6º ao 9º</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">6º</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">7º</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">8º</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">9º</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">P</span><br>
</div>  
<div class="col-md-6 ls-no-padding-left">
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_APROVACAO_'.$year.'_SI_4']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_APROVACAO_'.$year.'_1']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_APROVACAO_'.$year.'_2']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_APROVACAO_'.$year.'_3']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_APROVACAO_'.$year.'_4']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo number_format($row_Ibge_AF['VL_INDICADOR_REND_'.$year.''], 2, '.', ' '); ?>&nbsp;</span><br>
</div>  
</div>

</td>

<?php } ?>

</tr>

</table>

</div>
		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
