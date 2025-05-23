<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_pauta SET pauta_retorno_coord=%s WHERE pauta_id=%s",
   GetSQLValueString($_POST['pauta_retorno_coord'], "text"),
   GetSQLValueString($_POST['pauta_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "pauta.php?feedback";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_feed_ac = "-1";
if (isset($_GET['ac'])) {
  $colname_feed_ac = $_GET['ac'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_feed_ac = sprintf("
  SELECT *, func_id, func_nome, pauta_atividade_id, pauta_descricao
  pauta_adc_atv_id, pauta_atv, pauta_id_pauta
  FROM smc_pauta 
  INNER JOIN smc_pauta_adiciona_atv ON pauta_id_pauta = pauta_id
  INNER JOIN smc_pauta_atividades ON pauta_atividade_id = pauta_atv
  LEFT JOIN smc_func ON func_id = pauta_id_professor
  WHERE pauta_id = %s", GetSQLValueString($colname_feed_ac, "int"));
$feed_ac = mysql_query($query_feed_ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($feed_ac);
$totalRows_feed_ac = mysql_num_rows($feed_ac);

if($totalRows_feed_ac == 0){
  header("Location: index.php");
  exit();
}

$turno = "";

switch ($row_Ac['pauta_turno']) {
  case 'MAT':
  $turno = "MATUTINO";
  break;
  case 'VESP':
  $turno = "VESPERTINO";
  break;
  case 'NOT':
  $turno = "NOTURNO";
  break;
  default:
  $turno = "SEM INFORMAÇÃO";
  break;
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
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
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
      <h1 class="ls-title-intro ls-ico-home">ACOMPANHAMENTO DE PAUTA</h1>
      <!-- CONTEÚDO -->

      <div class="ls-box">
       <h5 class="ls-title-5">Professor(a): <?php echo $row_Ac['func_nome']; ?> </h5>
       <h5 class="ls-title-6">Turno: <?php echo $turno; ?></h5>
     </div>

     <div class="ls-box ls-box-gray">
      <h4>PAUTA FORMATIVA/INFORMATIVA REALIZADA:</h4>
      <br>
      <?php
      $outros = ""; 
      if($row_Ac['pauta_atividade_outro'] != ""){ ?>

        <?php $outros = mb_convert_encoding($row_Ac['pauta_atividade_outro'], "UTF-8"); ?>
      <?php } ?>
      <span class="ls-tag"><?php echo $outros; ?></span>
      <?php 
      $pauta_retorno_coord = $row_Ac['pauta_retorno_coord'];
      $pauta_id = $row_Ac['pauta_id'];
      do {
        ?>
        <span class="ls-tag"><?= utf8_encode($row_Ac['pauta_descricao']); ?></span>
        <?php
      } while ($row_Ac = mysql_fetch_assoc($feed_ac));
      ?>
      



    </div>
    
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-box">

      <h5 class="ls-title-5">Parecer da Coordenação Pedagógica</h5>

      <label class="ls-label"> <b class="ls-label-text">Observação da Coordenação Pedagógica</b>
        <textarea name="pauta_retorno_coord" cols="50" rows="5"><?php echo htmlentities($pauta_retorno_coord, ENT_COMPAT, 'utf-8'); ?></textarea>
      </label>
      <div class="ls-actions-btn">
        <input class="ls-btn-primary" type="submit" value="REGISTRAR ACOMPANHAMENTO">
        <a href="pauta.php" class="ls-btn">VOLTAR</a>
      </div>
      <input type="hidden" name="pauta_id" value="<?php echo $pauta_id; ?>">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="pauta_id" value="<?php echo $pauta_id; ?>">
    </form>
    <p>&nbsp;</p>
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
      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($feed_ac);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
