<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>

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
$query_listaEscolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, 
escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio 
FROM smc_escola
WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_ue = '1' AND escola_situacao = '1'";
$listaEscolas = mysql_query($query_listaEscolas, $SmecelNovo) or die(mysql_error());
$row_listaEscolas = mysql_fetch_assoc($listaEscolas);
$totalRows_listaEscolas = mysql_num_rows($listaEscolas);

//FILTRO
//$tipo = "todos";
$qry_escola = "";

if (isset($_GET['cod'])) {
  $cod = anti_injection($_GET['cod']);
  $qry_escola = " AND escola_id = '$cod' ";
  }
//FILTRO

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, 
escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, 
escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio 
FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_ue = '1' AND escola_situacao = '1' $qry_escola
ORDER BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

//VERIFICA SE CONJUNTO VAZIO
if ( $totalRows_Escolas < 1) {
$red = "index.php?erro";
header(sprintf("Location: %s", $red));
die;
}
//VERIFICA SE CONJUNTO VAZIO

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
    <h1 class="ls-title-intro ls-ico-home">TURMAS POR ESCOLA</h1>
     
    <!-- CONTEUDO -->
    
    
 <div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary">ESCOLA</a>

  <ul class="ls-dropdown-nav">
  <li><a href="rel_turmas.php">TODAS</a></li>
    <?php do { ?>
      <li><a href="rel_turmas.php?cod=<?php echo $row_listaEscolas['escola_id']; ?>"><?php echo $row_listaEscolas['escola_nome']; ?></a></li>
    <?php } while ($row_listaEscolas = mysql_fetch_assoc($listaEscolas)); ?>
  </ul>
</div>

<a href="impressao/rel_turmas.php?<?php echo $url_atual[1]; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>    

    
<?php 
$num = 1;
$turmasEscola = 0;
do { ?>
    <?php 
    	mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Turmas = "
		SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
		turma_turno, turma_total_alunos, turma_ano_letivo,
		CASE turma_turno
		WHEN 0 THEN 'INTEGRAL'
		WHEN 1 THEN 'MATUTINO'
		WHEN 2 THEN 'VESPERTINO'
		WHEN 3 THEN 'NOTURNO'
		END AS turma_turno_nome 
		FROM smc_turma WHERE turma_id_escola = '$row_Escolas[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
		ORDER BY turma_turno, turma_etapa, turma_nome ASC";
		$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
		$row_Turmas = mysql_fetch_assoc($Turmas);
		$totalRows_Turmas = mysql_num_rows($Turmas);
		
		$turmasEscola = $turmasEscola + $totalRows_Turmas;
		
	?>
  
  <div class="ls-box ls-board-box">
  
  <h2 class="ls-title-3"><?php echo $row_Escolas['escola_nome']; ?></h2>
 
  <?php if ($totalRows_Turmas > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th width="50"></th>
        <th>TURMA</th>
        <th class="ls-txt-center">TURNO</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $num; $num++; ?></td>
          <td><?php echo $row_Turmas['turma_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_Turmas['turma_turno_nome']; ?></td>
        </tr>
        <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
    </tbody>
  </table>
  <?php } else { ?>
  <p>Nenhuma turma cadastrada.</p>
  <?php } // Show if recordset not empty ?>
  </div>  
    
    	
    
	<?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
    
    
    <p>TOTAL DE TURMAS: <?php echo $turmasEscola; ?></p>
    
    <hr>
    
    
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

mysql_free_result($listaEscolas);

mysql_free_result($Turmas);

mysql_free_result($Escolas);
?>