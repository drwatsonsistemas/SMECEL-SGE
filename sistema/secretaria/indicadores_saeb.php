<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../index.php?exit";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT * FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AI = "SELECT * FROM smc_aprovacao_saeb_iniciais_municipio WHERE CO_MUNICIPIO = $row_Secretaria[sec_ibge_municipio]";
$Ibge_AI = mysql_query($query_Ibge_AI, $SmecelNovo) or die(mysql_error());
$row_Ibge_AI = mysql_fetch_assoc($Ibge_AI);
$totalRows_Ibge_AI = mysql_num_rows($Ibge_AI);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AF = "SELECT * FROM smc_aprovacao_saeb_finais_municipio WHERE CO_MUNICIPIO = $row_Secretaria[sec_ibge_municipio]";
$Ibge_AF = mysql_query($query_Ibge_AF, $SmecelNovo) or die(mysql_error());
$row_Ibge_AF = mysql_fetch_assoc($Ibge_AF);
$totalRows_Ibge_AF = mysql_num_rows($Ibge_AF);

?>

<!DOCTYPE html>
<html class="ls-theme-green">
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-chart-bar-up">INDICADORES SAEB</h1>
    <!-- CONTEUDO -->

    <div class="ls-group-btn ls-group-active">
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_aprovacao.php">Taxa de Aprovação</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right ls-active" href="indicadores_saeb.php">Indicadores SAEB</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_ideb.php">Indicadores IDEB</a>
    </div>  

    <br><br>

    <div class="ls-box">CÓDIGO DO MUNICÍPIO: <strong><?php echo $row_Secretaria['sec_ibge_municipio']; ?></strong></div>
    
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
      <span class="ls-btn-primary ls-btn-block ls-no-radius">MAT</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">POR</span><br>
      <span class="ls-btn-primary ls-btn-block ls-no-radius">MEDIA</span><br>
      </div>  
      <div class="col-md-6 ls-no-padding-left">
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_NOTA_MATEMATICA_'.$year.'']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AI['VL_NOTA_PORTUGUES_'.$year.'']; ?>&nbsp;</span><br>
        <span class="ls-btn ls-btn-block ls-no-radius"><?php echo number_format($row_Ibge_AI['VL_NOTA_MEDIA_'.$year.''], 2, '.', ' '); ?>&nbsp;</span><br>
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
<span class="ls-btn-primary ls-btn-block ls-no-radius">MAT</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">POR</span><br>
<span class="ls-btn-primary ls-btn-block ls-no-radius">MEDIA</span><br>
</div>  
<div class="col-md-6 ls-no-padding-left">
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_NOTA_MATEMATICA_'.$year.'']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo $row_Ibge_AF['VL_NOTA_PORTUGUES_'.$year.'']; ?>&nbsp;</span><br>
  <span class="ls-btn ls-btn-block ls-no-radius"><?php echo number_format($row_Ibge_AF['VL_NOTA_MEDIA_'.$year.''], 2, '.', ' '); ?>&nbsp;</span><br>
</div>  
</div>

</td>

<?php } ?>

</tr>

</table>

</div>

    
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>