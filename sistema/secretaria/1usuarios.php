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

$status = 1;
$status_query = " AND usu_status = '1' ";
if (isset($_GET['status'])) {
$status = $_GET['status'];
	switch ($status) {
    case 1:
        $status = 1;
		$status_query = " AND usu_status = '1' ";
        break;
    case 2:
        $status = 2;
		$status_query = " AND usu_status = '2' ";
        break;
    default:
       $status = 1;
	   $status_query = " AND usu_status = '1' ";
	}
  }


$tipo = 0;
$tipo_query = "";
if (isset($_GET['tipo'])) {
$tipo = $_GET['tipo'];
	switch ($tipo) {
    case 1:
        $tipo = 1;
		$tipo_query = " AND usu_tipo = '1' ";
        break;
    case 2:
        $tipo = 2;
		$tipo_query = " AND usu_tipo = '2' ";
        break;
    default:
       $tipo = 0;
	   $tipo_query = "";
	}
  }

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuarios = "
SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, escola_id, escola_nome,
CASE usu_tipo
WHEN 1 THEN 'SECRETARIA'
WHEN 2 THEN 'ESCOLAR'
WHEN 3 THEN 'MECANOGRAFIA'
WHEN 4 THEN 'PSE'
END AS usu_tipo_desc
FROM smc_usu
LEFT JOIN smc_escola ON escola_id = usu_escola 
WHERE usu_sec = '$row_Secretaria[sec_id]' $tipo_query $status_query 
ORDER BY usu_nome ASC
";
$Usuarios = mysql_query($query_Usuarios, $SmecelNovo) or die(mysql_error());
$row_Usuarios = mysql_fetch_assoc($Usuarios);
$totalRows_Usuarios = mysql_num_rows($Usuarios);

	$url_atual = "$_SERVER[REQUEST_URI]";
	$url_atual = explode("?", $url_atual);
	
	if (isset($url_atual[1])) {
	if ($url_atual[1]=="") {
		$url_atual[1]="";
		} else {
	$url_atual[1]=$url_atual[1];
	}
	} else {
		
		$url_atual[1]="";
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
    <h1 class="ls-title-intro ls-ico-home">USUÁRIOS ATIVOS DO SISTEMA</h1>
    <div class="ls-box ls-board-box">
      <?php if (isset($_GET["cadastrado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Aviso:</strong> Usuário cadastrado com sucesso! Os dados de login foram enviados para o e-mail do usuário. Solicite que o mesmo verifique a caixa postal.</div>
        <?php } ?>
      <?php if (isset($_GET["editado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Aviso:</strong> Usuário editado com sucesso! </div>
        <?php } ?>
      <?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Aviso:</strong> Ocorreu um erro na ação anterior. Um e-mail foi enviado ao administrador do sistema! </div>
        <?php } ?>
      <a href="usuarios_cadastrar.php" class="ls-btn-primary ls-ico-plus">CADASTRAR NOVO USUÁRIO</a>
      <a href="impressao/rel_usuarios.php?<?php echo $url_atual[1]; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>
      
	  <div class="ls-group-btn ls-group-active ls-float-right"> 
	  <a href="usuarios.php" class="ls-btn-primary <?php if ($tipo=="0") { ?> ls-active<?php } ?>">TODOS</a> 
	  <a href="usuarios.php?tipo=1" class="ls-btn-primary <?php if ($tipo=="1") { ?> ls-active<?php } ?>">SECRETARIA</a> 
	  <a href="usuarios.php?tipo=2" class="ls-btn-primary <?php if ($tipo=="2") { ?> ls-active<?php } ?>">ESCOLAS</a> 
	  </div>
      
      <a href="usuarios_inativos.php" class="ls-btn">Ver inativos</a> 
	  
	  
	  
	  
	  <?php if ($totalRows_Usuarios > 0) { // Show if recordset not empty ?>
      <table class="ls-table ls-sm-space">
        <thead>
          <tr>
            <th width="50"></th>
            <th>USUÁRIO / login</th>
            <th width="120" class="ls-txt-center">TIPO</th>
            <th width="450" class="ls-txt-center">UNIDADE ESCOLAR/SETOR</th>
            <th width="50"></th>
          </tr>
        </thead>
        <?php 
$num = 1;		
?>
        <?php do { ?>
          <tr>
            <td><?php 
		
		echo str_pad($num, 3, "0", STR_PAD_LEFT);
		
		//echo $num;
		$num++;
		 ?></td>
            <td><strong><?php echo $row_Usuarios['usu_nome']; ?></strong><br><small><?php echo $row_Usuarios['usu_email']; ?></small></td>
            <td class="ls-txt-center"><?php echo $row_Usuarios['usu_tipo_desc']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Usuarios['escola_nome']; ?> <?php if ($row_Usuarios['usu_tipo']==2) { ?><a href="usuario_incluir_escola.php?codigo=<?php echo $row_Usuarios['usu_id']; ?>" class="ls-ico-plus"></a><?php } ?></td>
            <td><a href="usuarios_editar.php?codigo=<?php echo $row_Usuarios['usu_id']; ?>" class="ls-ico-edit-admin ls-float-right">Editar</a></td>
          </tr>
          <?php } while ($row_Usuarios = mysql_fetch_assoc($Usuarios)); ?>
      </table>
      <p class="ls-float-right">Total de Usuários no sistema: <?php echo $totalRows_Usuarios; ?></p>
      
      <p>
      <a href="impressao/rel_usuarios.php?<?php echo $url_atual[1]; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>
      </p>
      
      <?php } else { ?>
      <hr>
      <div class="ls-alert-warning"><strong>Atenção:</strong> Nenhum usuário cadastrado.</div>
      <?php } ?>
    </div>
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

mysql_free_result($Usuarios);
?>