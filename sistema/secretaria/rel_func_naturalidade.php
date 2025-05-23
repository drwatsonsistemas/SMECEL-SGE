<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../escola/fnc/idade.php"; ?>
<?php require_once('funcoes/inverteData.php'); ?>

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


$graduacaoQry = "";

if (isset($_GET['nivel'])) {
  
  $graducao = $_GET['nivel'];


switch ($graducao) {
	
	case 1:
	$graduacaoQry = " AND func_escolaridade = '1' ";
	break;
	
	case 2:
	$graduacaoQry = " AND func_escolaridade = '2' ";
	break;
	
	case 3:
	$graduacaoQry = " AND func_escolaridade = '3' ";
	break;
	
	case 4:
	$graduacaoQry = " AND func_escolaridade = '4' ";
	break;
	
	case 5:
	$graduacaoQry = " AND func_escolaridade = '5' ";
	break;
	
	default:
	$graduacaoQry = "";
	
	}


}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosGraduacao = "
SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, 
func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, 
func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, 
func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, 
func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, 
func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, 
func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, 
func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, 
func_senha, func_senha_ativa, func_carga_horaria_semanal, funcao_id, funcao_nome, id_regime, regime_nome
FROM smc_func
INNER JOIN smc_regime ON id_regime= func_regime
INNER JOIN smc_funcao ON funcao_id = func_cargo
WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_situacao = '1' $graduacaoQry
ORDER BY func_nome ASC
";
$FuncionariosGraduacao = mysql_query($query_FuncionariosGraduacao, $SmecelNovo) or die(mysql_error());
$row_FuncionariosGraduacao = mysql_fetch_assoc($FuncionariosGraduacao);
$totalRows_FuncionariosGraduacao = mysql_num_rows($FuncionariosGraduacao);
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
    <h1 class="ls-title-intro ls-ico-home">FUNCIONÁRIOS POR NATURALIDADE</h1>
    <!-- CONTEUDO -->
    
<div class="ls-actions-btn">
    <a href="impressao/rel_func_naturalidade.php" class="ls-btn" target="_blank">IMPRIMIR</a>
  </div>
      
<?php if ($totalRows_FuncionariosGraduacao > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th width="50"></th>
        <th>FUNCIONÁRIO</th>
        <th width="150">NASCIMENTO</th>
        <th>NATURALIDADE</th>
        <th width="50">UF</th>
        <th>SITUAÇÃO</th>
        </tr>
    </thead>
    <tbody>
      <?php $num = 1; 
	  do { ?>
        <tr>
          <td><?php echo $num; $num++ ?></td>
          <td><?php echo $row_FuncionariosGraduacao['func_nome']; ?></td>
          <td><?php echo inverteData($row_FuncionariosGraduacao['func_data_nascimento']); ?></td>
          <td><?php echo $row_FuncionariosGraduacao['func_municipio_nascimento']; ?></td>
          <td><?php echo $row_FuncionariosGraduacao['func_uf_nascimento']; ?></td>
          <td><?php echo $row_FuncionariosGraduacao['regime_nome']; ?></td>
        </tr>
        <?php } while ($row_FuncionariosGraduacao = mysql_fetch_assoc($FuncionariosGraduacao)); ?>
    </tbody>
  </table>

<hr>

<p>TOTAL: <?php echo $totalRows_FuncionariosGraduacao; ?></p>



  <?php } // Show if recordset not empty ?>
    
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

mysql_free_result($FuncionariosGraduacao);
?>