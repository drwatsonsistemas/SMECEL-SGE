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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

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
  <style>

      th { border: dotted 1px; }
      td { border: dotted 1px; }
      tr { border: dotted 1px; }

  </style>
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">RESUMO GERAL</h1>
    <!-- CONTEUDO -->
    
    
  <table class="ls-table ls-xs-space ls-table-striped ls-table-bordered ls-bg-header"> 

    <tr>
    <th class="ls-txt-center">ANO LETIVO</th>
    <th colspan="3" class="ls-txt-center">TURMAS</th>
    <th colspan="3" class="ls-txt-center">ALUNOS</th>
    <th colspan="2" class="ls-txt-center">PROFESSORES</th>

    </tr>
      
    <tr>
    <th class="ls-txt-center"></th>
    <th class="ls-txt-center">ESCOLARIZAÇÃO</th>
    <th class="ls-txt-center">AEE</th>
    <th class="ls-txt-center">AC</th>
    <th class="ls-txt-center">MATRICULADOS</th>
    <th class="ls-txt-center">TRANSFERIDOS</th>
    <th class="ls-txt-center">DESISTENTES</th>
    <th class="ls-txt-center">EFETIVOS</th>
    <th class="ls-txt-center">TEMPORÁRIOS</th>
    </tr>
  
  <?php 
  
  do { 
    
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'
";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAEE = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '2'
";
$TurmasAEE = mysql_query($query_TurmasAEE, $SmecelNovo) or die(mysql_error());
$row_TurmasAEE = mysql_fetch_assoc($TurmasAEE);
$totalRows_TurmasAEE = mysql_num_rows($TurmasAEE);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAC = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '3'
";
$TurmasAC = mysql_query($query_TurmasAC, $SmecelNovo) or die(mysql_error());
$row_TurmasAC = mysql_fetch_assoc($TurmasAC);
$totalRows_TurmasAC = mysql_num_rows($TurmasAC);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivas = "
SELECT * FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1' AND turma_tipo_atendimento = 1";
$MatriculasAtivas = mysql_query($query_MatriculasAtivas, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivas = mysql_fetch_assoc($MatriculasAtivas);
$totalRows_MatriculasAtivas = mysql_num_rows($MatriculasAtivas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasTransferidos = "
SELECT * FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '2' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
$MatriculasTransferidos = mysql_query($query_MatriculasTransferidos, $SmecelNovo) or die(mysql_error());
$row_MatriculasTransferidos = mysql_fetch_assoc($MatriculasTransferidos);
$totalRows_MatriculasTransferidos = mysql_num_rows($MatriculasTransferidos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasDesistentes = "
SELECT * FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '3' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
$MatriculasDesistentes = mysql_query($query_MatriculasDesistentes, $SmecelNovo) or die(mysql_error());
$row_MatriculasDesistentes = mysql_fetch_assoc($MatriculasDesistentes);
$totalRows_MatriculasDesistentes = mysql_num_rows($MatriculasDesistentes);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Professores = "
SELECT *, turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao, func_id, func_situacao, func_regime 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
INNER JOIN smc_escola ON escola_id = turma_id_escola
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id 
WHERE func_regime = '1' AND turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'
GROUP BY ch_lotacao_professor_id
";
$Professores = mysql_query($query_Professores, $SmecelNovo) or die(mysql_error());
$row_Professores = mysql_fetch_assoc($Professores);
$totalRows_Professores = mysql_num_rows($Professores);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresContratados = "
SELECT *, turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao, func_id, func_situacao, func_regime 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
INNER JOIN smc_escola ON escola_id = turma_id_escola
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id 
WHERE func_regime = '2' AND turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'
GROUP BY ch_lotacao_professor_id
";
$ProfessoresContratados = mysql_query($query_ProfessoresContratados, $SmecelNovo) or die(mysql_error());
$row_ProfessoresContratados = mysql_fetch_assoc($ProfessoresContratados);
$totalRows_ProfessoresContratados = mysql_num_rows($ProfessoresContratados);



    ?>

    <tr>
    <td class="ls-txt-center"><strong><?php echo $row_Ano['ano_letivo_ano']; ?></strong></td>
    <td class="ls-txt-center"><?php echo $totalRows_Turmas; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_TurmasAEE; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_TurmasAC; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_MatriculasAtivas; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_MatriculasTransferidos; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_MatriculasDesistentes; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_Professores; ?></td>
    <td class="ls-txt-center"><?php echo $totalRows_ProfessoresContratados; ?></td>
    </tr>

  <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>  
  </table>  
    
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