<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

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
$MM_authorizedUsers = "99";
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
  $updateSQL = sprintf("UPDATE smc_usu SET usu_nome=%s, usu_email=%s, usu_senha=%s, usu_status=%s WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_nome'], "text"),
                       GetSQLValueString($_POST['usu_email'], "text"),
                       GetSQLValueString($_POST['usu_senha'], "text"),
                       GetSQLValueString($_POST['usu_status'], "int"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "usuarios.php?alterado=true";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$colname_UsuEditar = "-1";
if (isset($_GET['usuario'])) {
  $colname_UsuEditar = $_GET['usuario'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuEditar = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, usu_insert, usu_update, usu_delete, usu_m_ava, usu_m_administrativo, usu_m_formacao, usu_m_transporte, usu_m_merenda, usu_m_patrimonio, usu_m_relatorios, usu_m_graficos, usu_m_configuracoes, usu_foto, usu_aceite_lgpd, usu_aceite_lgpd_data FROM smc_usu WHERE usu_id = %s", GetSQLValueString($colname_UsuEditar, "int"));
$UsuEditar = mysql_query($query_UsuEditar, $SmecelNovo) or die(mysql_error());
$row_UsuEditar = mysql_fetch_assoc($UsuEditar);
$totalRows_UsuEditar = mysql_num_rows($UsuEditar);


?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
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
    <h1 class="ls-title-intro ls-ico-home">MODELO</h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->
    
    
       <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
        <label class="ls-label col-md-12">
        <b class="ls-label-text">NOME DO USUÁRIO</b>
        <p class="ls-label-info">Informe o nome completo</p>
        <input type="text" name="usu_nome" value="<?php echo htmlentities($row_UsuEditar['usu_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">E-MAIL</b>
        <p class="ls-label-info">Informe o e-mail que será utilizado como login</p>
        <input type="text" name="usu_email" value="<?php echo htmlentities($row_UsuEditar['usu_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-6">
        <b class="ls-label-text">SENHA</b>
        <p class="ls-label-info">Modifique ou deixe a senha antiga</p>
        <div class="ls-prefix-group">
          <input type="password" name="usu_senha" id="password_field" value="<?php echo htmlentities($row_UsuEditar['usu_senha'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field" href="#"> </a> </div>
        </label>
        
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Status</b>
              <div class="ls-custom-select">
                  <select name="usu_status" class="ls-custom" >
                    <option value="1" <?php if (!(strcmp(1, htmlentities($row_UsuEditar['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ATIVO</option>
                    <option value="2" <?php if (!(strcmp(2, htmlentities($row_UsuEditar['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>INATIVO</option>
                  </select>      
                  </div>
            </label>
        
        <label class="ls-label col-md-12">
          <input type="submit" value="ATUALIZAR" class="ls-btn-primary">
          <a href="usuarios.php" class="ls-btn">CANCELAR</a> </label>

      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="usu_id" value="<?php echo $row_UsuEditar['usu_id']; ?>">
      </form>
    
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
    </div>
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

mysql_free_result($UsuEditar);
?>