<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_ano_letivo SET ano_letivo_ano=%s, ano_letivo_inicio=%s, ano_letivo_fim=%s, ano_letivo_aberto=%s WHERE ano_letivo_id=%s",
                       GetSQLValueString($_POST['ano_letivo_ano'], "text"),
                       GetSQLValueString(inverteData($_POST['ano_letivo_inicio']), "date"),
                       GetSQLValueString(inverteData($_POST['ano_letivo_fim']), "date"),
                       GetSQLValueString(isset($_POST['ano_letivo_aberto']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['ano_letivo_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "anoletivo.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_AnoLetivo = "-1";
if (isset($_GET['codigo'])) {
  $colname_AnoLetivo = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = sprintf("SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Secretaria[sec_id]' AND ano_letivo_id = %s", GetSQLValueString($colname_AnoLetivo, "int"));
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo);


if ($totalRows_AnoLetivo < 1) {
	$semEscolas = "index.php?erro";
	header(sprintf("Location: %s", $semEscolas));
	}
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

  <link rel="stylesheet" type="text/css" href="css/locastyle.css">

  <script src="js/locastyle.js"></script>

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
    <h1 class="ls-title-intro ls-ico-home">EDITAR ANO LETIVO</h1>
	<div class="ls-box ls-board-box">
    
    
    
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>"  class="ls-form-horizontal">


        <label class="ls-label col-md-12">
        <b class="ls-label-text">ANO LETIVO</b>
        <p class="ls-label-info">Informe o ano no formato AAAA</p>
        <input type="text" name="ano_letivo_ano" value="<?php echo htmlentities($row_AnoLetivo['ano_letivo_ano'], ENT_COMPAT, 'utf-8'); ?>" size="4" class="ls-no-style-input1" readonly> 
        </label>

        <label class="ls-label col-md-6">
        <b class="ls-label-text">INÍCIO DO ANO LETIVO</b>
        <p class="ls-label-info">Informe a data</p>
             <input type="text" name="ano_letivo_inicio" value="<?php echo htmlentities(inverteData($row_AnoLetivo['ano_letivo_inicio']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder"> 
        </label>

        <label class="ls-label col-md-6">
        <b class="ls-label-text">TÉRMINO DO ANO LETIVO</b>
        <p class="ls-label-info">Informe a data</p>
             <input type="text" name="ano_letivo_fim" value="<?php echo htmlentities(inverteData($row_AnoLetivo['ano_letivo_fim']), ENT_COMPAT, 'utf-8'); ?>" size="32" class="placeholder">  
        </label>
        

        <label class="ls-label col-md-12">
        <b class="ls-label-text">ANO LETIVO ABERTO?</b>
		<input type="checkbox" name="ano_letivo_aberto" value=""  <?php if (!(strcmp(htmlentities($row_AnoLetivo['ano_letivo_aberto'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>        
        <p class="ls-label-info">Marque se o Ano Letivo estiver aberto no Sistema</p>
        </label>


        <label class="ls-label col-md-12">
          <input type="submit" value="SALVAR" class="ls-btn-primary">
          <a href="anoletivo.php" class="ls-btn">CANCELAR</a> </label>


        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ano_letivo_id" value="<?php echo $row_AnoLetivo['ano_letivo_id']; ?>">
      </form>
      
      
      
      
      <p>&nbsp;</p>
	</div>
  </div>
</main>

<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($AnoLetivo);
?>