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
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_logs = 100;
$pageNum_logs = 0;
if (isset($_GET['pageNum_logs'])) {
  $pageNum_logs = $_GET['pageNum_logs'];
}
$startRow_logs = $pageNum_logs * $maxRows_logs;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_logs = "
SELECT log_id, log_id_usu, log_id_escola, log_data_hora, escola_id, escola_nome, escola_id_sec, usu_id, usu_nome, usu_sec, usu_tipo 
FROM smc_log
LEFT JOIN smc_escola ON escola_id = log_id_escola
INNER JOIN smc_usu ON usu_id = log_id_usu
WHERE usu_sec =  '$row_UsuarioLogado[usu_sec]' AND usu_tipo <> '99'
ORDER BY log_id DESC";
$query_limit_logs = sprintf("%s LIMIT %d, %d", $query_logs, $startRow_logs, $maxRows_logs);
$logs = mysql_query($query_limit_logs, $SmecelNovo) or die(mysql_error());
$row_logs = mysql_fetch_assoc($logs);

if (isset($_GET['totalRows_logs'])) {
  $totalRows_logs = $_GET['totalRows_logs'];
} else {
  $all_logs = mysql_query($query_logs);
  $totalRows_logs = mysql_num_rows($all_logs);
}
$totalPages_logs = ceil($totalRows_logs/$maxRows_logs)-1;

$queryString_logs = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_logs") == false && 
        stristr($param, "totalRows_logs") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_logs = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_logs = sprintf("&totalRows_logs=%d%s", $totalRows_logs, $queryString_logs);
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
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">REGISTROS DE ACESSO AO SISTEMA</h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->

<div class="ls-group-btn">

    
    
    <table border="0">
  <tr>
    <td width="100"><?php if ($pageNum_logs > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_logs=%d%s", $currentPage, 0, $queryString_logs); ?>" class="ls-btn">Primeiro</a>
        <?php } // Show if not first page ?></td>
    <td width="100"><?php if ($pageNum_logs > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_logs=%d%s", $currentPage, max(0, $pageNum_logs - 1), $queryString_logs); ?>" class="ls-btn">Anterior</a>
        <?php } // Show if not first page ?></td>
    <td width="100"><?php if ($pageNum_logs < $totalPages_logs) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_logs=%d%s", $currentPage, min($totalPages_logs, $pageNum_logs + 1), $queryString_logs); ?>" class="ls-btn">Pr&oacute;ximo</a>
        <?php } // Show if not last page ?></td>
    <td width="100"><?php if ($pageNum_logs < $totalPages_logs) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_logs=%d%s", $currentPage, $totalPages_logs, $queryString_logs); ?>" class="ls-btn">&Uacute;ltimo</a>
        <?php } // Show if not last page ?></td>
  </tr>
</table>
    
</div>    
    

    <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
      <thead>
      <tr>
        <th width="70"></th>
        <th width="180" class="ls-txt-center">DATA/HORA</th>
        <th width="250" class="ls-txt-center">USUÁRIO</th>
        <th class="ls-txt-center">ESCOLA</th>
        
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
        <tr>
          <td>#<?php echo $row_logs['log_id']; ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/Y - H\hi", strtotime($row_logs['log_data_hora'])); ?><?php //echo $row_logs['log_data_hora']; ?></td>
          <td><a href="logs_usuario.php?usuario=<?php echo $row_logs['usu_id']; ?>"><?php echo $row_logs['usu_nome']; ?></a></td>
          <td><a href="logs_unidade.php?escola=<?php echo $row_logs['escola_id']; ?>"><?php echo $row_logs['escola_nome']; ?></a></td>
          
        </tr>
        <?php } while ($row_logs = mysql_fetch_assoc($logs)); ?>
    	</tbody>
    </table>

    <hr>
    
    Registros <?php echo ($startRow_logs + 1) ?> a <?php echo min($startRow_logs + $maxRows_logs, $totalRows_logs) ?> de <?php echo $totalRows_logs ?>
    

    <!-- CONTEUDO -->    
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($logs);
?>