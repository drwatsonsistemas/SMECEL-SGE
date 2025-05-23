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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_escola SET escola_nome=%s, escola_cep=%s, escola_endereco=%s, escola_num=%s, escola_bairro=%s, escola_telefone1=%s, escola_telefone2=%s, escola_email=%s, escola_inep=%s, escola_cnpj=%s, escola_ue=%s, escola_localizacao=%s, escola_situacao=%s, escola_unidade_executora=%s, escola_caixa_ux_prestacao_contas=%s WHERE escola_id=%s",
                       GetSQLValueString($_POST['escola_nome'], "text"),
                       GetSQLValueString($_POST['escola_cep'], "text"),
                       GetSQLValueString($_POST['escola_endereco'], "text"),
                       GetSQLValueString($_POST['escola_num'], "text"),
                       GetSQLValueString($_POST['escola_bairro'], "text"),
                       GetSQLValueString($_POST['escola_telefone1'], "text"),
                       GetSQLValueString($_POST['escola_telefone2'], "text"),
                       GetSQLValueString($_POST['escola_email'], "text"),
                       GetSQLValueString($_POST['escola_inep'], "text"),
                       GetSQLValueString($_POST['escola_cnpj'], "text"),
                       GetSQLValueString($_POST['escola_ue'], "text"),
                       GetSQLValueString($_POST['escola_localizacao'], "text"),
                       GetSQLValueString($_POST['escola_situacao'], "text"),
                       GetSQLValueString($_POST['escola_unidade_executora'], "text"),
                       GetSQLValueString($_POST['escola_caixa_ux_prestacao_contas'], "text"),
                       GetSQLValueString($_POST['escola_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "escolas.php?editada&nome=$_POST[escola_nome]";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

require_once('funcoes/usuLogado.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_EscolasEditar = "-1";
if (isset($_GET['codigo'])) {
  $colname_EscolasEditar = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolasEditar = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_localizacao, escola_situacao, escola_unidade_executora, escola_caixa_ux_prestacao_contas FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_id = %s", GetSQLValueString($colname_EscolasEditar, "int"));
$EscolasEditar = mysql_query($query_EscolasEditar, $SmecelNovo) or die(mysql_error());
$row_EscolasEditar = mysql_fetch_assoc($EscolasEditar);
$totalRows_EscolasEditar = mysql_num_rows($EscolasEditar);

if ($totalRows_EscolasEditar < 1) {
	$semEscolas = "escolas.php?erro";
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
    <h1 class="ls-title-intro ls-ico-home">EDITAR ESCOLA</h1>
    <div class="ls-box ls-board-box">
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
        <label class="ls-label col-md-12">
        <b class="ls-label-text">NOME DA ESCOLA</b>
        <input type="text" name="escola_nome" value="<?php echo htmlentities($row_EscolasEditar['escola_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32" required>
        </label>
        <label class="ls-label col-md-4"> <b class="ls-label-text">CEP</b>
          <input type="text" name="escola_cep" value="<?php echo htmlentities($row_EscolasEditar['escola_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="cep">
        </label>
        <label class="ls-label col-md-8"> <b class="ls-label-text">ENDEREÇO</b>
          <input type="text" name="escola_endereco" value="<?php echo htmlentities($row_EscolasEditar['escola_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-3"> <b class="ls-label-text">NÚMERO</b>
          <input type="text" name="escola_num" value="<?php echo htmlentities($row_EscolasEditar['escola_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-9"> <b class="ls-label-text">BAIRRO</b>
          <input type="text" name="escola_bairro" value="<?php echo htmlentities($row_EscolasEditar['escola_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-4"> <b class="ls-label-text">TELEFONE 1</b>
          <input type="text" name="escola_telefone1" value="<?php echo htmlentities($row_EscolasEditar['escola_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
        </label>
        <label class="ls-label col-md-4"> <b class="ls-label-text">TELEFONE 2</b>
          <input type="text" name="escola_telefone2" value="<?php echo htmlentities($row_EscolasEditar['escola_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="celular">
        </label>
        <label class="ls-label col-md-4"> <b class="ls-label-text">E-MAIL</b>
          <input type="text" name="escola_email" value="<?php echo htmlentities($row_EscolasEditar['escola_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-6"> <b class="ls-label-text">INEP</b>
          <input type="text" name="escola_inep" value="<?php echo htmlentities($row_EscolasEditar['escola_inep'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="inep">
        </label>
        <label class="ls-label col-md-6"> <b class="ls-label-text">CNPJ</b>
          <input type="text" name="escola_cnpj" value="<?php echo htmlentities($row_EscolasEditar['escola_cnpj'], ENT_COMPAT, 'utf-8'); ?>" size="32" class="cnpj">
        </label>
        <label class="ls-label col-md-4">
        <b class="ls-label-text">TIPO</b>
        <div class="ls-custom-select">
          <select name="escola_ue" class="ls-select">
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_EscolasEditar['escola_ue'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>UNIDADE ESCOLAR</option>
            <option value="0" <?php if (!(strcmp(0, htmlentities($row_EscolasEditar['escola_ue'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DEPARTAMENTO/SETOR</option>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-4">
        <b class="ls-label-text">SITUAÇÃO</b>
        <div class="ls-custom-select">
          <select name="escola_situacao" class="ls-select">
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_EscolasEditar['escola_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>EM ATIVIDADE</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_EscolasEditar['escola_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>PARALISADA</option>
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_EscolasEditar['escola_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>EXTINTA</option>
          </select>
        </div>
        </label>
       <label class="ls-label col-md-4">
        <b class="ls-label-text">LOCALIZAÇÃO</b>
        <div class="ls-custom-select">
          <select name="escola_localizacao" class="ls-select" required>
            <option value="U" <?php if (!(strcmp("U", htmlentities($row_EscolasEditar['escola_localizacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>URBANA</option>
            <option value="R" <?php if (!(strcmp("R", htmlentities($row_EscolasEditar['escola_localizacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>RURAL</option>
          </select>
        </div>
        </label>
        <hr>
		<label class="ls-label col-md-6">
        <b class="ls-label-text">UNIDADE EXECUTORA</b>
        <div class="ls-custom-select">
          <select name="escola_unidade_executora" class="ls-select">
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolasEditar['escola_unidade_executora'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>POSSUI</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolasEditar['escola_unidade_executora'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NÃO POSSUI</option>
          </select>
        </div>
        </label>
		<label class="ls-label col-md-6">
        <b class="ls-label-text">PRESTAÇÃO DE CONTAS</b>
        <div class="ls-custom-select">
          <select name="escola_caixa_ux_prestacao_contas" class="ls-select">
            <option value="" <?php if (!(strcmp("", htmlentities($row_EscolasEditar['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>></option>
            <option value="S" <?php if (!(strcmp("S", htmlentities($row_EscolasEditar['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ADIMPLENTE</option>
            <option value="N" <?php if (!(strcmp("N", htmlentities($row_EscolasEditar['escola_caixa_ux_prestacao_contas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>INADIMPLENTE</option>
          </select>
        </div>
        </label>
		<hr>
        <p class="ls-label col-md-12">
          <input type="submit" value="SALVAR" class="ls-btn-primary">
          <a href="escolas.php" class="ls-btn">CANCELAR</a> 
        </p>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="escola_id" value="<?php echo $row_EscolasEditar['escola_id']; ?>">
      </form>
      <p>&nbsp;</p>
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

mysql_free_result($EscolasEditar);
?>