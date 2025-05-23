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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Preparacao = "SELECT preparacao_id, preparacao_id_sec, preparacao_nome_preparacao, preparacao_modo_preparo, preparacao_hash FROM smc_me_preparacao WHERE preparacao_id_sec = '$row_Secretaria[sec_id]' ORDER BY preparacao_nome_preparacao ASC";
$Preparacao = mysql_query($query_Preparacao, $SmecelNovo) or die(mysql_error());
$row_Preparacao = mysql_fetch_assoc($Preparacao);
$totalRows_Preparacao = mysql_num_rows($Preparacao);

$colname_Editar = "-1";
if (isset($_GET['editar'])) {
  $colname_Editar = $_GET['editar'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Editar = sprintf("
SELECT preparacao_id, preparacao_id_sec, preparacao_nome_preparacao, preparacao_modo_preparo, preparacao_hash 
FROM smc_me_preparacao 
WHERE preparacao_id_sec = '$row_Secretaria[sec_id]' AND preparacao_hash = %s", GetSQLValueString($colname_Editar, "text"));
$Editar = mysql_query($query_Editar, $SmecelNovo) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);

if (isset($_GET['editar'])) {
if ($totalRows_Editar  < 1) {
	$red = "index.php?erro&cod=preparacao.php";
	header(sprintf("Location: %s", $red));
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE smc_me_preparacao SET preparacao_nome_preparacao=%s, preparacao_modo_preparo=%s WHERE preparacao_id=%s",
                       GetSQLValueString($_POST['preparacao_nome_preparacao'], "text"),
                       GetSQLValueString($_POST['preparacao_modo_preparo'], "text"),
                       GetSQLValueString($_POST['preparacao_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "preparacao.php?editado";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$hash = md5(time('YmdHis').$_POST['preparacao_id_sec']);
	
  $insertSQL = sprintf("INSERT INTO smc_me_preparacao (preparacao_id_sec, preparacao_nome_preparacao, preparacao_modo_preparo, preparacao_hash) VALUES (%s, %s, %s, '$hash')",
                       GetSQLValueString($_POST['preparacao_id_sec'], "text"),
                       GetSQLValueString($_POST['preparacao_nome_preparacao'], "text"),
                       GetSQLValueString($_POST['preparacao_modo_preparo'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "preparacao_itens.php?preparacao=$hash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
  
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
<h1 class="ls-title-intro ls-ico-home">PREPARAÇÕES</h1>
    <!-- CONTEUDO -->
    
<p><button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus"> CADASTRAR PREPARAÇÃO</button></p>

	<?php if (isset($_GET["editado"])) { ?>
      <div class="ls-alert-success">Preparação editada com sucesso.</div>
    <?php } ?>
    <?php if ($totalRows_Preparacao > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-bg-header">
    <thead>
      <tr>
        <th width="50"></th>
        <th width="50"></th>
        <th>NOME DA PREPARAÇÃO</th>
        <th width="100" class="ls-txt-center">ADD. ÍTENS</th>
        </tr>
    </thead>
    <tbody>
      <?php $num = 1; do { ?>
        <tr>
          <td class="ls-txt-center"><strong><?php echo $num; ?></strong></td>
          <td class="ls-txt-center"><a href="preparacao.php?editar=<?php echo $row_Preparacao['preparacao_hash']; ?>"><span class="ls-ico-pencil2"></span></a></td>
          <td><?php echo $row_Preparacao['preparacao_nome_preparacao']; ?></td>
          <td class="ls-txt-center"><a href="preparacao_itens.php?preparacao=<?php echo $row_Preparacao['preparacao_hash']; ?>" title="Clique aqui para adicionar os alimentos que compõem essa preparação"><span class="ls-ico-origins ls-ico-right"></span></a></td>
        </tr>
        <?php $num++; } while ($row_Preparacao = mysql_fetch_assoc($Preparacao)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  
      <div class="ls-alert-info"><strong>Atenção:</strong> Clique no botão "CADASTRAR PREPARAÇÃO" para inserir uma nova preparação.</div>

  
  <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>


<div class="ls-modal" data-modal-blocked1 id="myAwesomeModalEditar">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EDITAR PREPARAÇÃO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">    
    
    <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form row">
      
	<label class="ls-label col-md-12">
      <b class="ls-label-text">PREPARAÇÃO</b>
      <p class="ls-label-info">Ex.: Mingau de milho, Risoto, Etc.</p>
      <input type="text" name="preparacao_nome_preparacao" value="<?php echo htmlentities($row_Editar['preparacao_nome_preparacao'], ENT_COMPAT, 'utf-8'); ?>" size="32">
    </label>      
      
  <label class="ls-label col-md-12">
    <b class="ls-label-text">MODO DE PREPARO</b>
    <p class="ls-label-info">Informe as etapas para preparação deste prato.</p>
  <textarea name="preparacao_modo_preparo" cols="50" rows="5"><?php echo htmlentities($row_Editar['preparacao_modo_preparo'], ENT_COMPAT, 'utf-8'); ?></textarea>
  </label>
      
	 <label class="ls-label col-md-12">    
     <p><input type="submit" value="SALVAR" class="ls-btn-primary ls-float-right"></p>
     </label>          
      
      <input type="hidden" name="MM_update" value="form2">
      <input type="hidden" name="preparacao_id" value="<?php echo $row_Editar['preparacao_id']; ?>">

    </form>
    
      
    </div>

  </div>
</div><!-- /.modal -->    



<div class="ls-modal" data-modal-blocked1 id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR PREPARAÇÃO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
      
         
    <label class="ls-label col-md-12">
      <b class="ls-label-text">PREPARAÇÃO</b>
      <p class="ls-label-info">Ex.: Mingau de milho, Risoto, Etc.</p>
       <input type="text" name="preparacao_nome_preparacao" value="" size="32" required>
    </label>
         
  <label class="ls-label col-md-12">
    <b class="ls-label-text">MODO DE PREPARO</b>
      <p class="ls-label-info">Informe as etapas para preparação deste prato.</p>
  <textarea name="preparacao_modo_preparo" cols="50" rows="2" class="ls-textarea-autoresize"></textarea>
  </label>
  
    <label class="ls-label col-md-12">    
     <p><input type="submit" value="PROSSEGUIR >>" class="ls-btn-primary ls-float-right"></p>
     </label>     
          
      <input type="hidden" name="preparacao_id_sec" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
      
      
    </div>

  </div>
</div><!-- /.modal -->

<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<?php if (isset($_GET["editar"])) { ?>
  <script>
		locastyle.modal.open("#myAwesomeModalEditar");
    </script>
  <?php } ?>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Preparacao);

mysql_free_result($Editar);
?>