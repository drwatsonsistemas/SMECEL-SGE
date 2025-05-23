<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/inverteData.php"; ?>
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
  $updateSQL = sprintf("UPDATE smc_calendario_escolar SET ce_tipo=%s, ce_descricao=%s WHERE ce_id=%s",
                       GetSQLValueString($_POST['ce_tipo'], "text"),
                       GetSQLValueString($_POST['ce_descricao'], "text"),
                       GetSQLValueString($_POST['ce_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "calendario_escolar.php?editado";
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

$colname_calendarioListar = "-1";
if (isset($_GET['codigo'])) {
  $colname_calendarioListar = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_calendarioListar = sprintf("SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao FROM smc_calendario_escolar WHERE ce_id = %s", GetSQLValueString($colname_calendarioListar, "int"));
$calendarioListar = mysql_query($query_calendarioListar, $SmecelNovo) or die(mysql_error());
$row_calendarioListar = mysql_fetch_assoc($calendarioListar);
$totalRows_calendarioListar = mysql_num_rows($calendarioListar);

if ($totalRows_calendarioListar < 1) {
	$redireciona = "calendario_escolar.php?erro";
	header(sprintf("Location: %s", $redireciona));
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

    
    
    <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CALENDÁRIO ESCOLAR - EDITAR</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      
      
         
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
        
        <label class="ls-label col-md-12">
        <b class="ls-label-text">DATA</b>
        <p class="ls-label-info"></p>
        <input type="text" name="" value="<?php echo inverteData($row_calendarioListar['ce_data']); ?>" size="32" disabled>
        </label>
        
        <label class="ls-label col-md-12">
        <b class="ls-label-text">EVENTO</b>
        <p class="ls-label-info">Informe o tipo de evento</p>
        <div class="ls-custom-select">
          <select name="ce_tipo" required>
            <option value="1" <?php if (!(strcmp(1, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - DIA LETIVO</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - SÁBADO LETIVO</option>
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3 - FERIADO NACIONAL</option>
            <option value="4" <?php if (!(strcmp(4, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4 - FERIADO MUNICIPAL</option>
            <option value="5" <?php if (!(strcmp(5, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5 - RECESSO JUNINO</option>
            <option value="6" <?php if (!(strcmp(6, htmlentities($row_calendarioListar['ce_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6 - RECESSO DE NATAL</option>
          </select>
        </div>
        </label>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">DESCRIÇÃO</b>
        <p class="ls-label-info">Informe apenas se precisar descrever o feriado. Ex.: PROCLAMAÇÃO DA REPÚBLICA</p>
        <input type="text" name="ce_descricao" value="<?php echo htmlentities($row_calendarioListar['ce_descricao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
        <label class="ls-label col-md-6">
         
        </label>
		
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="ce_id" value="<?php echo $row_calendarioListar['ce_id']; ?>">
    
      
      
      
      
      </p>
    </div>
    <div class="ls-modal-footer">
       	<button type="submit" class="ls-btn-primary">SALVAR</button>
    	<a href="calendario_escolar.php" class="ls-btn">VOLTAR</a>
        <a href="javascript:func()" onclick="confirmacao('<?php echo $colname_calendarioListar; ?>')" class="ls-btn-danger ls-float-right">EXCLUIR</a>        
    </div>
    </form>
  </div>
</div><!-- /.modal -->
    
    
    


<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script language="Javascript">
function confirmacao(cod) {
     var resposta = confirm("Remover este registro?");
 
     if (resposta == true) {
          window.location.href = "calendario_escolar_excluir.php?codigo="+cod;
     }
}
</script>

<script language="Javascript">
locastyle.modal.open("#myAwesomeModal");
</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($calendarioListar);
?>