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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_conceito_itens (conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['conceito_itens_id_conceito'], "int"),
                       GetSQLValueString($_POST['conceito_itens_descricao'], "text"),
                       GetSQLValueString($_POST['conceito_itens_legenda'], "text"),
                       GetSQLValueString($_POST['conceito_itens_peso'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Conceito = "-1";
if (isset($_GET['hash'])) {
  $colname_Conceito = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conceito = sprintf("SELECT conceito_id, conceito_id_sec, conceito_descricao, conceito_observacao, conceito_hash FROM smc_conceito WHERE conceito_hash = %s", GetSQLValueString($colname_Conceito, "text"));
$Conceito = mysql_query($query_Conceito, $SmecelNovo) or die(mysql_error());
$row_Conceito = mysql_fetch_assoc($Conceito);
$totalRows_Conceito = mysql_num_rows($Conceito);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ItensConceito = "SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso FROM smc_conceito_itens WHERE conceito_itens_id_conceito = '$row_Conceito[conceito_id]' ORDER BY conceito_itens_peso ASC";
$ItensConceito = mysql_query($query_ItensConceito, $SmecelNovo) or die(mysql_error());
$row_ItensConceito = mysql_fetch_assoc($ItensConceito);
$totalRows_ItensConceito = mysql_num_rows($ItensConceito);

if ((isset($_GET['item'])) && ($_GET['item'] != "")) {
	
	
	
  $deleteSQL = sprintf("DELETE FROM smc_conceito_itens WHERE conceito_itens_id=%s",
                       GetSQLValueString($_GET['item'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "conceito_cad_itens.php?hash=$colname_Conceito&deletado";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
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
    <h1 class="ls-title-intro ls-ico-home">ITENS DE CONCEITO</h1>
    <!-- CONTEUDO -->
    
<div class="ls-alert-info ls-dismissable">
  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
  <strong>Atenção:</strong> Cadastre cada ítem iniciando do <strong>MENOR</strong> para o <strong>MAIOR</strong> valor de conceito. 
</div>
    
    <h3>GRUPO DE CONCEITO "<?php echo $row_Conceito['conceito_descricao']; ?>"</h3>
    
    <hr>
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>"  class="ls-form ls-form-horizontal row">
      
      <label class="ls-label col-md-6 col-sm-12">
          <b class="ls-label-text">DESCRIÇÃO</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe o ítem de um grupo de conceitos. Ex.: Aproximado; Nivelado; Atingiu; Etc." data-title="AJUDA"></a>
          <input type="text" name="conceito_itens_descricao" value="" size="32" required>
      </label>
      
      <label class="ls-label col-md-4 col-sm-12">
          <b class="ls-label-text">LEGENDA</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informe a legenda para o ítem. Ex.: A; Aprox; N; Etc." data-title="AJUDA"></a>
          <input type="text" name="conceito_itens_legenda" value="" size="32" required>
      </label>
      
      <label class="ls-label col-md-2 col-sm-12">
          <b class="ls-label-text">ORDEM</b>
          <a href="#" class="ls-ico-help ls-float-right" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Descreva o número de sequencia para o item" data-title="AJUDA"></a>
          <input type="text" name="conceito_itens_peso" value="<?php echo $totalRows_ItensConceito+1; ?>" size="32">
      </label>
      
      <div class="ls-actions-btn">
            <input type="submit" value="CADASTRAR" class="ls-btn-primary">
            <a class="ls-btn-danger" href="conceitos.php">VOLTAR</a>
      </div> 
      
      <input type="hidden" name="conceito_itens_id_conceito" value="<?php echo $row_Conceito['conceito_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    <p>&nbsp;</p>
    <?php if ($totalRows_ItensConceito > 0) { // Show if recordset not empty ?>
      <table class="ls-table">
        <thead>
          <tr>
            <th>CONCEITO</th>
            <th>PESO</th>
            <th width="50"></th>
          </tr>
        </thead>
        <tbody>
          <?php do { ?>
            <tr>
              <td><?php echo $row_ItensConceito['conceito_itens_legenda']; ?> - <?php echo $row_ItensConceito['conceito_itens_descricao']; ?></td>
              <td><?php echo $row_ItensConceito['conceito_itens_peso']; ?></td>
              <td><a href="javascript:func()" onclick="confirmaExclusao('<?php echo $colname_Conceito; ?>','<?php echo $row_ItensConceito['conceito_itens_id']; ?>')" class="ls-btn-primary-danger ls-btn-xs ls-ico-remove"></a></td>
            </tr>
            <?php } while ($row_ItensConceito = mysql_fetch_assoc($ItensConceito)); ?>
        </tbody>
      </table>
      <?php } else { ?>
      
      NENHUM CONCEITO CADASTRADO
      
      <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

	<script language="Javascript">
	function confirmaExclusao(hash,codigo) {
     var resposta = confirm("Deseja realmente remover esse ítem?");
     	if (resposta == true) {
     	     window.location.href = "conceito_cad_itens.php?hash="+hash+"&item="+codigo;
    	 }
	}
	</script>
    
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Conceito);

mysql_free_result($ItensConceito);
?>