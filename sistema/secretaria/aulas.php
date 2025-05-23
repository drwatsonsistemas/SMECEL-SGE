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

$maxRows_Aulas = 50;
$pageNum_Aulas = 0;
if (isset($_GET['pageNum_Aulas'])) {
  $pageNum_Aulas = $_GET['pageNum_Aulas'];
}
$startRow_Aulas = $pageNum_Aulas * $maxRows_Aulas;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "
SELECT * ,turma_id, turma_id_sec, turma_id_escola, turma_nome, turma_ano_letivo, escola_id, escola_nome, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY plano_aula_data DESC";
$query_limit_Aulas = sprintf("%s LIMIT %d, %d", $query_Aulas, $startRow_Aulas, $maxRows_Aulas);
$Aulas = mysql_query($query_limit_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);

if (isset($_GET['totalRows_Aulas'])) {
  $totalRows_Aulas = $_GET['totalRows_Aulas'];
} else {
  $all_Aulas = mysql_query($query_Aulas);
  $totalRows_Aulas = mysql_num_rows($all_Aulas);
}
$totalPages_Aulas = ceil($totalRows_Aulas/$maxRows_Aulas)-1;
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
    <h1 class="ls-title-intro ls-ico-home">AULAS PUBLICADAS</h1>
    <!-- CONTEUDO -->
    
<?php if ($row_Aulas > 0) { ?>
	<table class="ls-table ls-sm-space">
      <thead>
      <tr>
        <th width="80">DATA</th>
        <th>AULA</th>
        <th>ESCOLA/TURMA</th>
        <th>PROFESSOR/COMPONENTE</th>
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo date("d/m", strtotime($row_Aulas['plano_aula_data'])); ?></td>
          <td><a href="aula_ver.php?codigo=<?php echo $row_Aulas['plano_aula_hash']; ?>" target="_blank"><span class="ls-ico-export"></span>&nbsp;<?php echo $row_Aulas['plano_aula_texto']; ?></a></td>
          <td><strong><?php echo $row_Aulas['escola_nome']; ?></strong><br><?php echo $row_Aulas['turma_nome']; ?></td>
          <td><strong><?php echo $row_Aulas['func_nome']; ?></strong><br><?php echo $row_Aulas['disciplina_nome']; ?></td>
        </tr>
        <?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
    	</tbody>
    </table>
	
	
	
<p>&nbsp;</p>


<ul class="ls-pager">


<?php if ($pageNum_Aulas > 0) { // Show if not first page ?>
  <li><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, 0, $queryString_Aulas); ?>">&laquo;  Primeira</a></li>
<?php } // Show if not first page ?>

<?php if ($pageNum_Aulas > 0) { // Show if not first page ?>
  <li><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, max(0, $pageNum_Aulas - 1), $queryString_Aulas); ?>">&laquo;  Anterior</a></li>
<?php } // Show if not first page ?>

<?php if ($pageNum_Aulas < $totalPages_Aulas) { // Show if not last page ?>
  <li><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, min($totalPages_Aulas, $pageNum_Aulas + 1), $queryString_Aulas); ?>">Próximo &raquo;</a></li>
<?php } // Show if not last page ?>

<?php if ($pageNum_Aulas < $totalPages_Aulas) { // Show if not last page ?>
  <li><a href="<?php printf("%s?pageNum_Aulas=%d%s", $currentPage, $totalPages_Aulas, $queryString_Aulas); ?>">Última &raquo;</a></li>
<?php } // Show if not last page ?>
        
</ul>

<p>Total de páginas <?php echo $totalPages_Aulas; ?></p>


<?php } else { ?>

<p>Nenhuma aula cadastrada.</p>

<?php } ?>


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

mysql_free_result($Aulas);
?>