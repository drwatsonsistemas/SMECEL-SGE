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

$currentPage = $_SERVER["PHP_SELF"];

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$maxRows_Chamados = 20;
$pageNum_Chamados = 0;
if (isset($_GET['pageNum_Chamados'])) {
  $pageNum_Chamados = $_GET['pageNum_Chamados'];
}
$startRow_Chamados = $pageNum_Chamados * $maxRows_Chamados;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Chamados = "
SELECT chamado_id, chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, 
chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado, chamado_numero, sec_id, sec_prefeitura, escola_id, escola_nome, usu_id, usu_nome,
CASE chamado_situacao
WHEN 'A' THEN '<span class=\"ls-tag-info\">ABERTO</span>'
WHEN 'F' THEN '<span class=\"ls-tag-warning\">ENCERRADO</span>'
END AS chamado_situacao_nome 
FROM smc_chamados
INNER JOIN smc_sec ON sec_id = chamado_id_sec
LEFT JOIN smc_escola ON escola_id = chamado_id_escola
INNER JOIN smc_usu ON usu_id = chamado_id_usuario
WHERE chamado_situacao = 'A'
ORDER BY chamado_id DESC";
$query_limit_Chamados = sprintf("%s LIMIT %d, %d", $query_Chamados, $startRow_Chamados, $maxRows_Chamados);
$Chamados = mysql_query($query_limit_Chamados, $SmecelNovo) or die(mysql_error());
$row_Chamados = mysql_fetch_assoc($Chamados);

if (isset($_GET['totalRows_Chamados'])) {
  $totalRows_Chamados = $_GET['totalRows_Chamados'];
} else {
  $all_Chamados = mysql_query($query_Chamados);
  $totalRows_Chamados = mysql_num_rows($all_Chamados);
}
$totalPages_Chamados = ceil($totalRows_Chamados/$maxRows_Chamados)-1;

$queryString_Chamados = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Chamados") == false && 
        stristr($param, "totalRows_Chamados") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Chamados = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Chamados = sprintf("&totalRows_Chamados=%d%s", $totalRows_Chamados, $queryString_Chamados);
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
    <h1 class="ls-title-intro ls-ico-home">CHAMADOS</h1>
    <div class="1ls-box 1ls-board-box"> 
    <!-- CONTEUDO -->
    
    
     <?php if (isset($_GET["encerrado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> 
        <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> 
        <strong>Atenção:</strong> Chamado encerrado com sucesso!</div>
        <?php } ?>
        
        
  
<div class="ls-group-btn ls-group-active">
  <a href="chamados.php" class="ls-btn-primary ls-active">EM ABERTO</a>
  <a href="chamados_encerrados.php" class="ls-btn-primary">ENCERRADOS</a>
</div>      
        
  
  <?php if ($totalRows_Chamados > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th width="100" class="ls-txt-center">CHAMADO</th>
        <th width="100" class="ls-txt-center">STATUS</th>
        <th width="100" class="ls-txt-center">DATA</th>
        <th class="ls-txt-center">SOLICITANTE</th>
        <th width="120" class="ls-txt-center">TIPO</th>
        <th class="ls-txt-center">ASSUNTO</th>
        <th width="60" class="ls-txt-center"></th>
        <th width="60" class="ls-txt-center"></th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td class="ls-txt-center"><a href="chamados_ver.php?chamado=<?php echo $row_Chamados['chamado_numero']; ?>"><strong><?php echo $row_Chamados['chamado_numero']; ?></strong></a></td>
          <td class="ls-txt-center"><?php echo $row_Chamados['chamado_situacao_nome']; ?></td>
          <td class="ls-txt-center"><?php echo date('d/m/Y H\hi', $row_Chamados['chamado_numero']); ?><?php //echo date("d/m/Y", strtotime($row_Chamados['chamado_data_abertura'])); ?></td>
          <td class="ls-txt-center"><?php echo $row_Chamados['sec_prefeitura']; ?><br><small><?php echo $row_Chamados['escola_nome']; ?></small></td>
          <td class="ls-txt-center"><span class="ls-tag"><?php echo $row_Chamados['chamado_categoria']; ?></span></td>
          <td class="ls-txt-center"><?php echo $row_Chamados['chamado_titulo']; ?></td>
		  <td class="ls-txt-center"><a href="chamados_ver.php?chamado=<?php echo $row_Chamados['chamado_numero']; ?>" class="ls-ico-search">&nbsp;</a></td>          
          <td class="ls-txt-center">
            <?php 
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Tickets = "SELECT ticket_id, ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_imagem, ticket_visualizado FROM smc_ticket WHERE ticket_id_chamado = '$row_Chamados[chamado_id]' AND ticket_visualizado = 'N'";
			$Tickets = mysql_query($query_Tickets, $SmecelNovo) or die(mysql_error());
			$row_Tickets = mysql_fetch_assoc($Tickets);
			$totalRows_Tickets = mysql_num_rows($Tickets);
			?>
            
           
            
            <?php 
			if ($row_Chamados['chamado_visualizado']=="N") {
				echo "<span class=\"ls-tag-danger\">NOVO</span>";
				}
			?>
            
             <?php if ($totalRows_Tickets > 0) {	?>
            <span class="ls-tag-danger">
              +<?php echo $totalRows_Tickets; ?>
              </span>
            <?php }	?>
            
          </td>
        </tr>
        <?php } while ($row_Chamados = mysql_fetch_assoc($Chamados)); ?>
    </tbody>
  </table>
          
          
        <ul class="ls-pager">
          <li class="<?php if ($pageNum_Chamados > 0) { ?><?php } else { ?>ls-disabled<?php } ?>"><a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, max(0, $pageNum_Chamados - 1), $queryString_Chamados); ?>">&laquo; Anterior</a></li>
          <li class="<?php if ($pageNum_Chamados < $totalPages_Chamados) { ?><?php } else { ?>ls-disabled<?php }  ?>"><a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, min($totalPages_Chamados, $pageNum_Chamados + 1), $queryString_Chamados); ?>">Próxima &raquo;</a></li>
        </ul>
          <?php } else { ?>
          
          <div class="ls-alert-success ls-dismissable"> 
        <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> 
        <strong>Atenção:</strong> Não há nenhum chamado em aberto.</div>
          
          <?php } // Show if recordset not empty ?>
<!--
    <table border="0">
      <tr>
        <td><?php if ($pageNum_Chamados > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, 0, $queryString_Chamados); ?>">First</a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_Chamados > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, max(0, $pageNum_Chamados - 1), $queryString_Chamados); ?>">Previous</a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_Chamados < $totalPages_Chamados) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, min($totalPages_Chamados, $pageNum_Chamados + 1), $queryString_Chamados); ?>">Next</a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_Chamados < $totalPages_Chamados) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_Chamados=%d%s", $currentPage, $totalPages_Chamados, $queryString_Chamados); ?>">Last</a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table>
    -->
    
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

mysql_free_result($Tickets);

mysql_free_result($Chamados);
?>