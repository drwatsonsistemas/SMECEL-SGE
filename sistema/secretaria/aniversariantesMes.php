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


$mes = date("m");

//$mes = "-1";
if (isset($_GET['mes'])) {
  $mes = $_GET['mes'];
}

if ($mes == '01' ) {
	$mesAnterior = 12;
	} else {
		$mesAnterior = $mes-01;
		}
	
if ($mes == '12' ) {
	$mesSeguinte = 01;
	} else {
		$mesSeguinte = $mes+01;
		}

		

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aniversariantesMes = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, func_id, func_nome, func_data_nascimento,
DATE_FORMAT(func_data_nascimento, '%m%d') AS aniversario, DATE_FORMAT(func_data_nascimento, '%d/%m') AS data_aniversario, 
DATE_FORMAT(func_data_nascimento, '%d') as dia_aniversario, DATE_FORMAT(func_data_nascimento, '%m') as mes_aniversario
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
WHERE vinculo_id_sec = '$row_UsuarioLogado[usu_sec]' AND Month(func_data_nascimento) = '$mes' ORDER BY aniversario, func_nome ASC";
$aniversariantesMes = mysql_query($query_aniversariantesMes, $SmecelNovo) or die(mysql_error());
$row_aniversariantesMes = mysql_fetch_assoc($aniversariantesMes);
$totalRows_aniversariantesMes = mysql_num_rows($aniversariantesMes);

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
    <h1 class="ls-title-intro ls-ico-home">ANIVERSARIANTES DO MÊS</h1>
    <!-- CONTEUDO -->
    
		<a href="aniversariantesMes.php?mes=<?php echo $mesAnterior; ?>" class="ls-btn-primary ls-ico-chevron-left"> MÊS ANTERIOR</a>
        <a href="aniversariantesMes.php?mes=<?php echo $mesSeguinte; ?>" class="ls-btn-primary">PRÓXIMO MÊS <span class="ls-ico-chevron-right"></span></a>
        
        <hr>
		
		<h1 class="panel">MÊS: <?php echo $mes; ?>/<?php echo date("Y"); ?></h1>
        <table width="100%" class="ls-table ls-no-hover ls-table-striped ls-bg-header">
          <thead>
          <tr>
            <th class="ls-txt-center" width="20px"></th>
            <th class="ls-txt-center" width="120px">DATA</th>
            <th>NOME</th>
          </tr>
          </thead>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php if ($row_aniversariantesMes['dia_aniversario'] == date("d") AND $row_aniversariantesMes['mes_aniversario'] == date("m")) { ?> <span class="ls-ico-star ls-color-theme"></span> <?php } ?></td>
              <td class="ls-txt-center"><?php echo $row_aniversariantesMes['data_aniversario']; ?></td>
              <td><?php echo $row_aniversariantesMes['func_nome']; ?></td>
            </tr>
            <?php } while ($row_aniversariantesMes = mysql_fetch_assoc($aniversariantesMes)); ?>
        </table>    
    
    
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
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
?>