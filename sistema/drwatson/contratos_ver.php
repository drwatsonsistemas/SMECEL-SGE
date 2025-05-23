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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$colname_Contrato = "-1";
if (isset($_GET['sec'])) {
  $colname_Contrato = $_GET['sec'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Contrato = sprintf("SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec WHERE sec_id = %s", GetSQLValueString($colname_Contrato, "int"));
$Contrato = mysql_query($query_Contrato, $SmecelNovo) or die(mysql_error());
$row_Contrato = mysql_fetch_assoc($Contrato);
$totalRows_Contrato = mysql_num_rows($Contrato);

$colname_Usuarios = "-1";
if (isset($_GET['sec'])) {
  $colname_Usuarios = $_GET['sec'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuarios = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_tipo = '1' AND usu_sec = %s", GetSQLValueString($colname_Usuarios, "int"));
$Usuarios = mysql_query($query_Usuarios, $SmecelNovo) or die(mysql_error());
$row_Usuarios = mysql_fetch_assoc($Usuarios);
$totalRows_Usuarios = mysql_num_rows($Usuarios);
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
    <h1 class="ls-title-intro ls-ico-home">DETALHES</h1>
    <div class="ls-box ls-board-box"> 
    <!-- CONTEUDO -->
    
    <header class="ls-info-header">
    <h2 class="ls-title-3">DADOS DA PREFEITURA</h2>
    </header>
    
    <strong><?php echo $row_Contrato['sec_nome']; ?></strong><br>
	<?php echo $row_Contrato['sec_prefeitura']; ?><br><br>
	
    <?php echo $row_Contrato['sec_endereco']; ?>, <?php echo $row_Contrato['sec_num']; ?> - <?php echo $row_Contrato['sec_bairro']; ?><br>
	<?php echo $row_Contrato['sec_cidade']; ?>, <?php echo $row_Contrato['sec_uf']; ?> - <?php echo $row_Contrato['sec_cep']; ?><br>
    
	<?php echo $row_Contrato['sec_telefone1']; ?> / <?php echo $row_Contrato['sec_telefone2']; ?><br>
    <?php echo $row_Contrato['sec_email']; ?><br><br>   
    Dirigente de Educação: <br><?php echo $row_Contrato['sec_nome_secretario']; ?><br>
    
    </div>
    
    <div class="ls-box ls-board-box"> 
    
    <header class="ls-info-header">
    <h2 class="ls-title-3">DADOS DOS USUÁRIOS</h2>
    </header>
    
    <a href="usuarios_cadastrar_sec.php?sec=<?php echo $row_Contrato['sec_id']; ?>" class="ls-btn-primary ls-ico-plus" aria-expanded="false">CADASTRAR USUÁRIO PRINCIPAL</a>
    <?php if ($totalRows_Usuarios > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <thead>
      <tr>
        <th>NOME</th>
        <th>E-MAIL</th>
        <th>CADASTRO</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_Usuarios['usu_nome']; ?></td>
          <td><?php echo $row_Usuarios['usu_email']; ?></td>
          <td><?php echo date("d/m/Y", strtotime($row_Usuarios['usu_cadastro'])); ?></td>
        </tr>
        <?php } while ($row_Usuarios = mysql_fetch_assoc($Usuarios)); ?>
    </tbody>
  </table>
  <p>Acima estão listados os usuários que possuem perfil de acesso ao painel da secretaria de educação.</p>
  <?php } else { ?>
  
  <p>Nenhum usuário principal cadastrado</p>
  
  <?php } // Show if recordset not empty ?>
    </div>
    
        <a href="contratos.php" class="ls-btn-primary" aria-expanded="false">VOLTAR</a>
	<p>&nbsp;</p>
    
        
<!-- CONTEUDO -->   
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

mysql_free_result($Contrato);

mysql_free_result($Usuarios);
?>