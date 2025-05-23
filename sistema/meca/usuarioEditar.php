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
  $updateSQL = sprintf("UPDATE smc_usu SET usu_nome=%s, usu_email=%s, usu_senha=%s, usu_tipo=%s, usu_escola=%s, usu_status=%s WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_nome'], "text"),
                       GetSQLValueString($_POST['usu_email'], "text"),
                       GetSQLValueString($_POST['usu_senha'], "text"),
                       GetSQLValueString($_POST['usu_tipo'], "int"),
                       GetSQLValueString($_POST['usu_escola'], "int"),
                       GetSQLValueString($_POST['usu_status'], "int"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "usuarioExibir.php?editado";
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email FROM smc_escola ORDER BY escola_nome ASC";
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

$colname_Usuario = "-1";
if (isset($_GET['c'])) {
  $colname_Usuario = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuario = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_id = %s", GetSQLValueString($colname_Usuario, "int"));
$Usuario = mysql_query($query_Usuario, $SmecelNovo) or die(mysql_error());
$row_Usuario = mysql_fetch_assoc($Usuario);
$totalRows_Usuario = mysql_num_rows($Usuario);
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
    
        <h1>Editar Usuário</h1>
    
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
      <fieldset>
      
            <div class="small-12 columns">
            <label>Nome completo
            <input type="text" name="usu_nome" value="<?php echo htmlentities($row_Usuario['usu_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
			</div>
            <div class="small-6 columns">
            <label>E-mail
            <input type="text" name="usu_email" value="<?php echo htmlentities($row_Usuario['usu_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
			</div>
            <div class="small-6 columns">
            <label>Senha
            <input type="password" name="usu_senha" value="<?php echo htmlentities($row_Usuario['usu_senha'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
			</div>
            <div class="small-4 columns">
            <label>Tipo
            <select name="usu_tipo">
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Usuario['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - Administração</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Usuario['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - Usuário Escolar</option>
              <option value="3" <?php if (!(strcmp(3, htmlentities($row_Usuario['usu_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - Mecanografia</option>
            </select>
            </label>
			</div>
            <div class="small-6 columns">
            <label>Escola/Setor
            <select name="usu_escola">
                <option value="-1" selected>Escolha...</option>
              <?php 
do {  
?>
              <option value="<?php echo $row_Escola['escola_id']?>" <?php if (!(strcmp($row_Escola['escola_id'], htmlentities($row_Usuario['usu_escola'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Escola['escola_nome']?></option>
              <?php
} while ($row_Escola = mysql_fetch_assoc($Escola));
?>
            </select>
            </label>
			</div>
            <div class="small-2 columns">
            <label>STATUS
            <select name="usu_status">
              <option value="1" <?php if (!(strcmp(1, htmlentities($row_Usuario['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - Ativo</option>
              <option value="2" <?php if (!(strcmp(2, htmlentities($row_Usuario['usu_status'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - Bloqueado</option>
            </select>
            </label>
            </div>
            
            <div class="small-12 columns">
            <hr>
            <input type="submit" value="ATUALIZAR DADOS" class="button"> <a href="usuarioExibir.php" class="button warning">VOLTAR</a>
			<a href="javascript:func()" onclick="confirmacao('<?php echo $row_Usuario['usu_id']; ?>')"class="button alert right">EXCLUIR</a>

			</div>



        <input type="hidden" name="usu_id" value="<?php echo $row_Usuario['usu_id']; ?>">
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="usu_id" value="<?php echo $row_Usuario['usu_id']; ?>">
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
  <script language="Javascript">
function confirmacao(id) {
     var resposta = confirm("Deseja remover esse cadastro?");
 
     if (resposta == true) {
          window.location.href = "usuarioExcluir.php?c="+id;
     }
}
</script>
  
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Escola);

mysql_free_result($Usuario);
?>
