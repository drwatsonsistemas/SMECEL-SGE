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
  $updateSQL = sprintf("UPDATE smc_sec SET sec_nome=%s, sec_prefeitura=%s, sec_cep=%s, sec_uf=%s, sec_cidade=%s, sec_endereco=%s, sec_num=%s, sec_bairro=%s, sec_telefone1=%s, sec_telefone2=%s, sec_email=%s, sec_nome_secretario=%s WHERE sec_id=%s",
                       GetSQLValueString($_POST['sec_nome'], "text"),
                       GetSQLValueString($_POST['sec_prefeitura'], "text"),
                       GetSQLValueString($_POST['sec_cep'], "text"),
                       GetSQLValueString($_POST['sec_uf'], "text"),
                       GetSQLValueString($_POST['sec_cidade'], "text"),
                       GetSQLValueString($_POST['sec_endereco'], "text"),
                       GetSQLValueString($_POST['sec_num'], "text"),
                       GetSQLValueString($_POST['sec_bairro'], "text"),
                       GetSQLValueString($_POST['sec_telefone1'], "text"),
                       GetSQLValueString($_POST['sec_telefone2'], "text"),
                       GetSQLValueString($_POST['sec_email'], "text"),
                       GetSQLValueString($_POST['sec_nome_secretario'], "text"),
                       GetSQLValueString($_POST['sec_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "dados.php?atualizado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "" : "?";
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Dados = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario FROM smc_sec WHERE sec_id = '$row_UsuarioLogado[usu_sec]'";
$Dados = mysql_query($query_Dados, $SmecelNovo) or die(mysql_error());
$row_Dados = mysql_fetch_assoc($Dados);
$totalRows_Dados = mysql_num_rows($Dados);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Atualizar dados</title>
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
  
  
     <h1>Atualizar dados da Secretaria</h1>
   
<?php if (isset($_GET["atualizado"])) { ?>   
  <div data-alert class="alert-box success radius">
  Dados atualizados com sucesso.
  <a href="index.php" class="label">Voltar</a>
  <a href="#" class="close">&times;</a>
  </div>
<?php } ?>

  
  
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <fieldset>
	    <legend>Dados da Secretaria</legend>

          <label>Nome da secretaria	
          <input type="text" name="sec_nome" value="<?php echo htmlentities($row_Dados['sec_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
		  </label>
          <label>Nome da Prefeitura	
          <input type="text" name="sec_prefeitura" value="<?php echo htmlentities($row_Dados['sec_prefeitura'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>CEP	
          <input type="text" name="sec_cep" value="<?php echo htmlentities($row_Dados['sec_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>UF	
          <input type="text" name="sec_uf" value="<?php echo htmlentities($row_Dados['sec_uf'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Cidade	
          <input type="text" name="sec_cidade" value="<?php echo htmlentities($row_Dados['sec_cidade'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Endereço	
          <input type="text" name="sec_endereco" value="<?php echo htmlentities($row_Dados['sec_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Número	
          <input type="text" name="sec_num" value="<?php echo htmlentities($row_Dados['sec_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Bairro	
          <input type="text" name="sec_bairro" value="<?php echo htmlentities($row_Dados['sec_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Telefone 1	
          <input type="text" name="sec_telefone1" value="<?php echo htmlentities($row_Dados['sec_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Telefone 2	
          <input type="text" name="sec_telefone2" value="<?php echo htmlentities($row_Dados['sec_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>E-mail da secretaria	
          <input type="text" name="sec_email" value="<?php echo htmlentities($row_Dados['sec_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label>Nome do(a) Secretário(a) 
          <input type="text" name="sec_nome_secretario" value="<?php echo htmlentities($row_Dados['sec_nome_secretario'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <input type="submit" value="Atualizar dados" class="button">
      
      <input type="hidden" name="sec_id" value="<?php echo $row_Dados['sec_id']; ?>">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="sec_id" value="<?php echo $row_Dados['sec_id']; ?>">
      
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

mysql_free_result($Dados);
?>
