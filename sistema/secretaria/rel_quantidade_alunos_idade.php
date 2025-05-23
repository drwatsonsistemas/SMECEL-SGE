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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {

	if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
		$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
	}

	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int) $anoLetivo;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosIdade = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,  vinculo_aluno_id_sec,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento,escola_id, escola_situacao,turma_id, turma_tipo_atendimento,
YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) AS idade, COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_tipo_atendimento = '1' AND vinculo_aluno_id_sec= '$row_UsuarioLogado[usu_sec]' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_situacao = '1' AND escola_situacao = '1'
GROUP BY idade";
$VinculosIdade = mysql_query($query_VinculosIdade, $SmecelNovo) or die(mysql_error());
$row_VinculosIdade = mysql_fetch_assoc($VinculosIdade);
$totalRows_VinculosIdade = mysql_num_rows($VinculosIdade);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosIdadeTot = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,  vinculo_aluno_id_sec,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento,turma_id, turma_tipo_atendimento,
YEAR(CURRENT_DATE) - YEAR(aluno_nascimento) - (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(aluno_nascimento, '%m%d')) AS idade 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_tipo_atendimento = '1' AND vinculo_aluno_id_sec= '$row_UsuarioLogado[usu_sec]' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_situacao = '1'";
$VinculosIdadeTot = mysql_query($query_VinculosIdadeTot, $SmecelNovo) or die(mysql_error());
$row_VinculosIdadeTot = mysql_fetch_assoc($VinculosIdadeTot);
$totalRows_VinculosIdadeTot = mysql_num_rows($VinculosIdadeTot);

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
    <h1 class="ls-title-intro ls-ico-home">Quantidade de alunos por idade</h1>
    <!-- CONTEUDO -->
    
    <a href="relatorios_alunos.php" class="ls-btn-primary ls-ico-chevron-left ">Voltar</a>
    <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
          <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
          <ul class="ls-dropdown-nav">

            <li>
              <a href="rel_quantidade_alunos_idade.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano']?>" target="" title="Diários">
                ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
              </a>
            </li>

            <?php do { ?>
              <li>
                <a href="rel_quantidade_alunos_idade.php?ano=<?php echo $row_Ano['ano_letivo_ano']?>" target="" title="Diários">
                  ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
                </a>
              </li>
            <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>

          </ul>
        </div>
    
		<!-- CONTEÚDO -->
        <table width="100%" class="ls-table ls-no-hover ls-table-striped ls-table-bordered ls-bg-header">
          <thead>
		  <tr>
            <th class="ls-txt-center" width="100">IDADE (ANOS)</th>
            <th class="ls-txt-center" width="100">TOTAL DE ALUNOS</th>
            <th class="ls-txt-center"></th>
          </tr>
		  <thead>
		  <tbody>
          <?php 
		  $totalAlunos = 0;
		  do { 
		  ?>
            <tr>
              <td class="ls-txt-center"><?php echo $row_VinculosIdade['idade']; ?></td>
              <td class="ls-txt-center">
			  <strong><?php 
			  echo $row_VinculosIdade['total'];
			  $totalAlunos = $totalAlunos + $row_VinculosIdade['total'];
			  ?></strong>
			  </td>
			  <?php 
			  
			  $perc = (($row_VinculosIdade['total']/$totalRows_VinculosIdadeTot)*100);
			  //echo $percentual = number_format($perc, 2)." %";
			  $percent = number_format($perc, 2);
			  
			  //echo $totalRows_VinculosIdadeTot; ?> 
              
              <td><div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo $percent; ?>" class="ls-animated ls-left-percentage"></div></td>
              
            </tr>
            <?php } while ($row_VinculosIdade = mysql_fetch_assoc($VinculosIdade)); ?>
            </tbody>
		</table>
		<div class="ls-box">
		<p class="ls-txt-center">Total de alunos matriculados: <?php echo $totalAlunos; ?></p>
        </div>    
    
    
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
?>