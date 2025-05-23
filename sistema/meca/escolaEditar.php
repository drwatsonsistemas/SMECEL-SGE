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
$MM_authorizedUsers = "1";
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
  $updateSQL = sprintf("UPDATE smc_escola SET escola_id_sec=%s, escola_nome=%s, escola_cep=%s, escola_endereco=%s, escola_num=%s, escola_bairro=%s, escola_telefone1=%s, escola_telefone2=%s, escola_email=%s WHERE escola_id=%s",
                       GetSQLValueString($_POST['escola_id_sec'], "int"),
                       GetSQLValueString($_POST['escola_nome'], "text"),
                       GetSQLValueString($_POST['escola_cep'], "text"),
                       GetSQLValueString($_POST['escola_endereco'], "text"),
                       GetSQLValueString($_POST['escola_num'], "text"),
                       GetSQLValueString($_POST['escola_bairro'], "text"),
                       GetSQLValueString($_POST['escola_telefone1'], "text"),
                       GetSQLValueString($_POST['escola_telefone2'], "text"),
                       GetSQLValueString($_POST['escola_email'], "text"),
                       GetSQLValueString($_POST['escola_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "escolaExibir.php?editado";
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

$colname_escolaEditar = "-1";
if (isset($_GET['c'])) {
  $colname_escolaEditar = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escolaEditar = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email FROM smc_escola WHERE escola_id = %s", GetSQLValueString($colname_escolaEditar, "int"));
$escolaEditar = mysql_query($query_escolaEditar, $SmecelNovo) or die(mysql_error());
$row_escolaEditar = mysql_fetch_assoc($escolaEditar);
$totalRows_escolaEditar = mysql_num_rows($escolaEditar);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>
<link rel="stylesheet" href="../../css/foundation.css">
<link rel="stylesheet" href="../../css/normalize.css">
  <!-- This is how you would link your custom stylesheet -->
  <link rel="stylesheet" href="../css/app-painel.css">
  <script src="../../js/vendor/modernizr.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>

<?php include "menu.php"; ?>

<div class="row">
	<div class="small-12 columns">
    
       <h1>Editar da Escola</h1>
    
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
    <fieldset>
    <legend>Dados da Escola</legend>
   
       	  <div class="small-12 columns">
          <label>Nome da Escola	
            <input type="text" name="escola_nome" value="<?php echo htmlentities($row_escolaEditar['escola_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-4 columns">
          <label>CEP	
            <input type="text" name="escola_cep" value="<?php echo htmlentities($row_escolaEditar['escola_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-8 columns">
          <label>Endereço	
            <input type="text" name="escola_endereco" value="<?php echo htmlentities($row_escolaEditar['escola_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-3 columns columns">
          <label>Número	
            <input type="text" name="escola_num" value="<?php echo htmlentities($row_escolaEditar['escola_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-9 columns">
          <label>Bairro	
            <input type="text" name="escola_bairro" value="<?php echo htmlentities($row_escolaEditar['escola_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-6 columns">
          <label>Telefone 1	
            <input type="text" name="escola_telefone1" value="<?php echo htmlentities($row_escolaEditar['escola_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 medium-6 large-6 columns">
          <label>Telefone 2	
            <input type="text" name="escola_telefone2" value="<?php echo htmlentities($row_escolaEditar['escola_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 columns">
          <label>E-mail	
            <input type="text" name="escola_email" value="<?php echo htmlentities($row_escolaEditar['escola_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          </div>
       	  <div class="small-12 columns">
            <input type="submit" value="SALVAR ALTERAÇÕES" class="button"> <a href="escolaExibir.php" class="button alert">VOLTAR</a>
          </div>


        <input type="hidden" name="escola_id" value="<?php echo $row_escolaEditar['escola_id']; ?>">
        <input type="hidden" name="escola_id_sec" value="<?php echo htmlentities($row_escolaEditar['escola_id_sec'], ENT_COMPAT, 'utf-8'); ?>">
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="escola_id" value="<?php echo $row_escolaEditar['escola_id']; ?>">
      </fieldset>
      </form>
      
      <p>&nbsp;</p>
	</div>
</div>


<script src="../../js/vendor/jquery.js"></script>
  <script src="../../js/foundation.min.js"></script>
  <script src="js/foundation/foundation.dropdown.js"></script>
<script>
    $(document).foundation();
  </script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($escolaEditar);
?>
