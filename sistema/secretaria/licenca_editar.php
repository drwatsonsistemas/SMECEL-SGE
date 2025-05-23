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

$colname_Licenca = "-1";
if (isset($_GET['licenca'])) {
  $colname_Licenca = $_GET['licenca'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Licenca = sprintf("
SELECT lancamento_id, lancamento_id_funcionario, lancamento_tipo, lancamento_data_saida, lancamento_data_retorno, lancamento_observacoes, lancamento_retorno, func_id, func_nome 
FROM smc_licenca 
INNER JOIN smc_func ON func_id = lancamento_id_funcionario 
WHERE lancamento_id = %s", GetSQLValueString($colname_Licenca, "int"));
$Licenca = mysql_query($query_Licenca, $SmecelNovo) or die(mysql_error());
$row_Licenca = mysql_fetch_assoc($Licenca);
$totalRows_Licenca = mysql_num_rows($Licenca);

if ($totalRows_Licenca < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_tipos = "SELECT licenca_id, licenca_nome, licenca_obs FROM smc_licenca_tipo ORDER BY licenca_nome ASC";
$tipos = mysql_query($query_tipos, $SmecelNovo) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_licenca SET lancamento_id_funcionario=%s, lancamento_tipo=%s, lancamento_data_saida=%s, lancamento_data_retorno=%s, lancamento_observacoes=%s, lancamento_retorno=%s WHERE lancamento_id=%s",
                       GetSQLValueString($_POST['lancamento_id_funcionario'], "int"),
                       GetSQLValueString($_POST['lancamento_tipo'], "int"),
                       GetSQLValueString($_POST['lancamento_data_saida'], "date"),
                       GetSQLValueString($_POST['lancamento_data_retorno'], "date"),
                       GetSQLValueString($_POST['lancamento_observacoes'], "text"),
                       GetSQLValueString(isset($_POST['lancamento_retorno']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['lancamento_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "titulacao_cadastrar.php?cod=" . $row_Licenca['lancamento_id_funcionario'] . "&licencaEditada";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
    <h1 class="ls-title-intro ls-ico-home">EDITAR LICENÇA</h1>
    <!-- CONTEUDO -->
    
    <h2><?php echo $row_Licenca['func_nome']; ?></h2>
    <p>&nbsp;</p>
    
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
      <fieldset>
      
      <label class="ls-label col-md-12">
          <b class="ls-label-text">TIPO</b>
          <div class="ls-custom-select">
            <select name="lancamento_tipo" class="ls-select" required>
              <option value="-1">-</option>
            <?php do {  ?>
            <option value="<?php echo $row_tipos['licenca_id']?>" <?php if (!(strcmp($row_tipos['licenca_id'], htmlentities($row_Licenca['lancamento_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_tipos['licenca_nome']?></option>
            <?php } while ($row_tipos = mysql_fetch_assoc($tipos)); ?>
            </select>
          </div>
          </label>
      
          <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">DATA DE INÍCIO/SAÍDA</b>
            <input type="date" name="lancamento_data_saida" value="<?php echo htmlentities($row_Licenca['lancamento_data_saida'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">DATA DE RETORNO/PREVISTO</b>
            <input type="date" name="lancamento_data_retorno" value="<?php echo htmlentities($row_Licenca['lancamento_data_retorno'], ENT_COMPAT, 'utf-8'); ?>" size="32">
          </label>
          <label class="ls-label col-sm-12"> <b class="ls-label-text">OBSERVAÇÕES</b>
            <textarea name="lancamento_observacoes" cols="50" rows="5"><?php echo htmlentities($row_Licenca['lancamento_observacoes'], ENT_COMPAT, 'utf-8'); ?></textarea>
          </label>
          
          <div class="ls-label col-md-12">
            <label class="ls-label-text">
              <input type="checkbox" name="lancamento_retorno" value=""  <?php if (!(strcmp(htmlentities($row_Licenca['lancamento_retorno'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
              MARQUE SE O PERÍODO DE LICENÇA JÁ FOI CONCLUÍDO </label>
          </div>          
          
          <div class="ls-modal-footer"> 
          	<a class="ls-btn ls-float-right" href="titulacao_cadastrar.php?cod=<?php echo $row_Licenca['lancamento_id_funcionario']; ?>">CANCELAR</a>
            <input type="submit" class="ls-btn-primary" value="ATUALIZAR">
          </div>
          
      <input type="hidden" name="lancamento_id" value="<?php echo $row_Licenca['lancamento_id']; ?>">
      <input type="hidden" name="lancamento_id_funcionario" value="<?php echo htmlentities($row_Licenca['lancamento_id_funcionario'], ENT_COMPAT, 'utf-8'); ?>">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="lancamento_id" value="<?php echo $row_Licenca['lancamento_id']; ?>">
    </fieldset>
    </form>
    
    



    
    
    
    
    
    
    
    <p>&nbsp;</p>
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

mysql_free_result($Licenca);

mysql_free_result($tipos);

mysql_free_result($Titulos);
?>