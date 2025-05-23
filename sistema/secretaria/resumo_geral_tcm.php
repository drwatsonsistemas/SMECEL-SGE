<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/anti_injection.php"; ?>
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

$row_AnoLetivo['ano_letivo_ano'] = $row_AnoLetivo['ano_letivo_ano'];
$consulta = " ano_letivo_ano = '$row_AnoLetivo[ano_letivo_ano]' AND ";

	if (isset($_GET['ano'])) {
	
		if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
		header("Location: index.php?nada"); 
		exit;
		}
	
	
	$row_AnoLetivo['ano_letivo_ano'] = anti_injection($_GET['ano']);
	$row_AnoLetivo['ano_letivo_ano'] = (int)$row_AnoLetivo['ano_letivo_ano'];
	$consulta = " ano_letivo_ano = '$row_AnoLetivo[ano_letivo_ano]' AND ";
	
}




mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE $consulta ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoMenu = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$AnoMenu = mysql_query($query_AnoMenu, $SmecelNovo) or die(mysql_error());
$row_AnoMenu = mysql_fetch_assoc($AnoMenu);
$totalRows_AnoMenu = mysql_num_rows($AnoMenu);

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

      

  </style>
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Resumo para o TCM - Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <!-- CONTEUDO -->
    
        <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
                <a href="#" class="ls-btn">ANO LETIVO: <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></a>
                <ul class="ls-dropdown-nav">
                  

                  <?php do { ?>

                    <li><a href="resumo_geral_tcm.php?ano=<?php echo $row_AnoMenu['ano_letivo_ano']; ?>" target="" title="Diários">ANO LETIVO <?php echo $row_AnoMenu['ano_letivo_ano']; ?></a></li>

                  <?php } while ($row_AnoMenu = mysql_fetch_assoc($AnoMenu)); ?>
                  
                </ul>
              </div>
              
              <hr>     


  
  <?php 
  
  do { 
    
    //3.26 - Número de alunos matriculados dos Anos Iniciais por turno: manhã
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_MatriculasAtivas_326 = "
    SELECT * FROM smc_vinculo_aluno
    INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    WHERE turma_etapa IN (14,15,16,17,18) AND turma_turno = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
    $MatriculasAtivas_326 = mysql_query($query_MatriculasAtivas_326, $SmecelNovo) or die(mysql_error());
    $row_MatriculasAtivas_326 = mysql_fetch_assoc($MatriculasAtivas_326);
    $totalRows_MatriculasAtivas_326 = mysql_num_rows($MatriculasAtivas_326);

    //3.27 - Número de alunos matriculados dos Anos Iniciais por turno: tarde
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_MatriculasAtivas_327 = "
    SELECT * FROM smc_vinculo_aluno
    INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    WHERE turma_etapa IN (14,15,16,17,18) AND turma_turno = '2' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
    $MatriculasAtivas_327 = mysql_query($query_MatriculasAtivas_327, $SmecelNovo) or die(mysql_error());
    $row_MatriculasAtivas_327 = mysql_fetch_assoc($MatriculasAtivas_327);
    $totalRows_MatriculasAtivas_327 = mysql_num_rows($MatriculasAtivas_327);
    
//3.28 - Quantidade de turmas dos Anos Iniciais: Até 24 alunos
//3.29 - Quantidade de turmas dos Anos Iniciais: De 25 a 30 alunos
//3.30 - Quantidade de turmas dos Anos Iniciais: De 31 a 33 alunos
//3.31 - Quantidade de turmas dos Anos Iniciais: Acima de 33 alunos
   

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_MatriculasPorEtapa = "
    SELECT * FROM smc_turma
    WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_tipo_atendimento = '1' AND turma_etapa IN (14,15,16,17,18)
    GROUP BY turma_id";
    $MatriculasPorEtapa = mysql_query($query_MatriculasPorEtapa, $SmecelNovo) or die(mysql_error());
    $row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa);
    $totalRows_MatriculasPorEtapa = mysql_num_rows($MatriculasPorEtapa);

    $turmasAlunosMais24 = 0;
    $turmasAlunos25a30 = 0;
    $turmasAlunos31a33 = 0;
    $turmasAlunosAcima33 = 0;
    
    do {
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_Conta = "
    SELECT * FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = '$row_MatriculasPorEtapa[turma_id]' AND vinculo_aluno_situacao = '1'
    ";
    $Conta = mysql_query($query_Conta, $SmecelNovo) or die(mysql_error());
    $row_Conta = mysql_fetch_assoc($Conta);
    $totalRows_Conta = mysql_num_rows($Conta);
    
    
    if ($totalRows_Conta <= 24) {
      $turmasAlunosMais24++;
    } else if (($totalRows_Conta >= 25) && ($totalRows_Conta <= 30)) {
      $turmasAlunos25a30++;
    } else if (($totalRows_Conta >= 31) && ($totalRows_Conta <= 33)) {
      $turmasAlunos31a33++;
    } else if ($totalRows_Conta > 33) {
      $turmasAlunosAcima33++;
    }


    } while ($row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa));
    
//4.25 - Número de alunos matriculados dos Anos Finais por turno: manhã
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivas_425 = "
SELECT * FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_etapa IN (19,20,21,22) AND turma_turno = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
$MatriculasAtivas_425 = mysql_query($query_MatriculasAtivas_425, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivas_425 = mysql_fetch_assoc($MatriculasAtivas_425);
$totalRows_MatriculasAtivas_425 = mysql_num_rows($MatriculasAtivas_425);

//4.26 - Número de alunos matriculados dos Anos Finais por turno: tarde
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivas_426 = "
SELECT * FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_etapa IN (19,20,21,22) AND turma_turno = '2' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_Ano[ano_letivo_ano]' AND escola_situacao = '1'";
$MatriculasAtivas_426 = mysql_query($query_MatriculasAtivas_426, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivas_426 = mysql_fetch_assoc($MatriculasAtivas_426);
$totalRows_MatriculasAtivas_426 = mysql_num_rows($MatriculasAtivas_426);


//4.27 - Quantidade de turmas dos Anos Finais: Até 30 alunos
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorEtapa427 = "
SELECT * FROM smc_turma
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_tipo_atendimento = '1' AND turma_etapa IN (19,20,21,22)
GROUP BY turma_id";
$MatriculasPorEtapa427 = mysql_query($query_MatriculasPorEtapa427, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorEtapa427 = mysql_fetch_assoc($MatriculasPorEtapa427);
$totalRows_MatriculasPorEtapa427 = mysql_num_rows($MatriculasPorEtapa427);

$ate30_427 = 0;
$de31ate35_427 = 0;
$de36a39_427 = 0;
$acima39_427 = 0;

do {

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conta427 = "
SELECT * FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = '$row_MatriculasPorEtapa427[turma_id]' AND vinculo_aluno_situacao = '1'
";
$Conta427 = mysql_query($query_Conta427, $SmecelNovo) or die(mysql_error());
$row_Conta427 = mysql_fetch_assoc($Conta427);
$totalRows_Conta427 = mysql_num_rows($Conta427);


if ($totalRows_Conta427 <= 30) {
  $ate30_427++;
} else if (($totalRows_Conta427 >= 31) && ($totalRows_Conta427 <= 35)) {
  $de31ate35_427++;
} else if (($totalRows_Conta427 >= 36) && ($totalRows_Conta427 <= 39)) {
  $de36a39_427++;
} else if ($totalRows_Conta427 > 39) {
  $acima39_427++;
}


} while ($row_MatriculasPorEtapa427 = mysql_fetch_assoc($MatriculasPorEtapa427));




//1.25 - Quantidade de turmas da Creche: Até 13 alunos
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EI = "
SELECT * FROM smc_turma
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_tipo_atendimento = '1' AND turma_etapa IN (1)
GROUP BY turma_id";
$EI = mysql_query($query_EI, $SmecelNovo) or die(mysql_error());
$row_EI = mysql_fetch_assoc($EI);
$totalRows_EI = mysql_num_rows($EI);

$EIate13 = 0;
$EIde14a20 = 0;
$EIde21a25 = 0;
$EIacima25 = 0;

do {

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaEI = "
SELECT * FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = '$row_EI[turma_id]' AND vinculo_aluno_situacao = '1'
";
$ContaEI = mysql_query($query_ContaEI, $SmecelNovo) or die(mysql_error());
$row_ContaEI = mysql_fetch_assoc($ContaEI);
$totalRows_ContaEI = mysql_num_rows($ContaEI);



if ($totalRows_ContaEI <= 13) {
  $EIate13++;
} else if (($totalRows_ContaEI >= 14) && ($totalRows_ContaEI <= 20)) {
  $EIde14a20++;
} else if (($totalRows_ContaEI >= 21) && ($totalRows_ContaEI <= 25)) {
  $EIde21a25++;
} else if ($totalRows_ContaEI > 25) {
  $EIacima25++;
}


} while ($row_EI = mysql_fetch_assoc($EI));



//2.23 - Quantidade de turmas da Pré-escola: Até 13 alunos
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_PE = "
SELECT * FROM smc_turma
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_tipo_atendimento = '1' AND turma_etapa IN (2)
GROUP BY turma_id";
$PE = mysql_query($query_PE, $SmecelNovo) or die(mysql_error());
$row_PE = mysql_fetch_assoc($PE);
$totalRows_PE = mysql_num_rows($PE);

$PEate22 = 0;
$PEde23a25 = 0;
$PEde26a30 = 0;
$PEacima30 = 0;

do {

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaPE = "
SELECT * FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = '$row_PE[turma_id]' AND vinculo_aluno_situacao = '1'
";
$ContaPE = mysql_query($query_ContaPE, $SmecelNovo) or die(mysql_error());
$row_ContaPE = mysql_fetch_assoc($ContaPE);
$totalRows_ContaPE = mysql_num_rows($ContaPE);



if ($totalRows_ContaPE <= 22) {
  $PEate22++;
} else if (($totalRows_ContaPE >= 23) && ($totalRows_ContaPE <= 25)) {
  $PEde23a25++;
} else if (($totalRows_ContaPE >= 26) && ($totalRows_ContaPE <= 30)) {
  $PEde26a30++;
} else if ($totalRows_ContaPE > 30) {
  $PEacima30++;
}


} while ($row_PE = mysql_fetch_assoc($PE));


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresGradCreche = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '3' AND turma_etapa IN (1)
GROUP BY ch_lotacao_professor_id";
$ProfessoresGradCreche = mysql_query($query_ProfessoresGradCreche, $SmecelNovo) or die(mysql_error());
$row_ProfessoresGradCreche = mysql_fetch_assoc($ProfessoresGradCreche);
$totalRows_ProfessoresGradCreche = mysql_num_rows($ProfessoresGradCreche);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresPosGradCreche = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '4' AND turma_etapa IN (1)
GROUP BY ch_lotacao_professor_id";
$ProfessoresPosGradCreche = mysql_query($query_ProfessoresPosGradCreche, $SmecelNovo) or die(mysql_error());
$row_ProfessoresPosGradCreche = mysql_fetch_assoc($ProfessoresPosGradCreche);
$totalRows_ProfessoresPosGradCreche = mysql_num_rows($ProfessoresPosGradCreche);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VagasNaCreche = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_reprovado, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, vinculo_aluno_dependencia, vinculo_aluno_reprovado_faltas, vinculo_aluno_repetente, vinculo_aluno_resultado_final,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND turma_etapa IN (1)
";
$VagasNaCreche = mysql_query($query_VagasNaCreche, $SmecelNovo) or die(mysql_error());
$row_VagasNaCreche = mysql_fetch_assoc($VagasNaCreche);
$totalRows_VagasNaCreche = mysql_num_rows($VagasNaCreche);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresGradPre = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '3' AND turma_etapa IN (2)
GROUP BY ch_lotacao_professor_id";
$ProfessoresGradPre = mysql_query($query_ProfessoresGradPre, $SmecelNovo) or die(mysql_error());
$row_ProfessoresGradPre = mysql_fetch_assoc($ProfessoresGradPre);
$totalRows_ProfessoresGradPre = mysql_num_rows($ProfessoresGradPre);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresPosGradPre = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '4' AND turma_etapa IN (2)
GROUP BY ch_lotacao_professor_id";
$ProfessoresPosGradPre = mysql_query($query_ProfessoresPosGradPre, $SmecelNovo) or die(mysql_error());
$row_ProfessoresPosGradPre = mysql_fetch_assoc($ProfessoresPosGradPre);
$totalRows_ProfessoresPosGradPre = mysql_num_rows($ProfessoresPosGradPre);

//4.5 - Quantidade de professores dos Anos Finais: Possuem formação de Graduação
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresGradAnosFinais = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa, turma_id_escola,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '3' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id";
$ProfessoresGradAnosFinais = mysql_query($query_ProfessoresGradAnosFinais, $SmecelNovo) or die(mysql_error());
$row_ProfessoresGradAnosFinais = mysql_fetch_assoc($ProfessoresGradAnosFinais);
$totalRows_ProfessoresGradAnosFinais = mysql_num_rows($ProfessoresGradAnosFinais);

//4.6 - Quantidade de professores dos Anos Finais: Possuem formação de Pós-Graduação
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresPosGradAnosFinais = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '4' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id";
$ProfessoresPosGradAnosFinais = mysql_query($query_ProfessoresPosGradAnosFinais, $SmecelNovo) or die(mysql_error());
$row_ProfessoresPosGradAnosFinais = mysql_fetch_assoc($ProfessoresPosGradAnosFinais);
$totalRows_ProfessoresPosGradAnosFinais = mysql_num_rows($ProfessoresPosGradAnosFinais);

//4.6 - Quantidade de professores dos Anos Finais: Possuem formação de Pós-Graduação EFETIVOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresPosGradAnosFinaisEF = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '4' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id AND func_regime = '1'";
$ProfessoresPosGradAnosFinaisEF = mysql_query($query_ProfessoresPosGradAnosFinaisEF, $SmecelNovo) or die(mysql_error());
$row_ProfessoresPosGradAnosFinaisEF = mysql_fetch_assoc($ProfessoresPosGradAnosFinaisEF);
$totalRows_ProfessoresPosGradAnosFinaisEF = mysql_num_rows($ProfessoresPosGradAnosFinaisEF);

//4.6 - Quantidade de professores dos Anos Finais: Possuem formação de Pós-Graduação TEMPORARIOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresPosGradAnosFinaisTEMP = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_escolaridade = '4' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id AND func_regime = '2'";
$ProfessoresPosGradAnosFinaisTEMP = mysql_query($query_ProfessoresPosGradAnosFinaisTEMP, $SmecelNovo) or die(mysql_error());
$row_ProfessoresPosGradAnosFinaisTEMP = mysql_fetch_assoc($ProfessoresPosGradAnosFinaisTEMP);
$totalRows_ProfessoresPosGradAnosFinaisTEMP = mysql_num_rows($ProfessoresPosGradAnosFinaisTEMP);

//28 - Quantidade de professores dos Anos Iniciais: EFETIVOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresAnosIniciaisEFE = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_regime = '1' AND turma_etapa IN (14,15,16,17,18) 
GROUP BY ch_lotacao_professor_id";
$ProfessoresAnosIniciaisEFE = mysql_query($query_ProfessoresAnosIniciaisEFE, $SmecelNovo) or die(mysql_error());
$row_ProfessoresAnosIniciaisEFE = mysql_fetch_assoc($ProfessoresAnosIniciaisEFE);
$totalRows_ProfessoresAnosIniciaisEFE = mysql_num_rows($ProfessoresAnosIniciaisEFE);


//29 - Quantidade de professores dos Anos Iniciais: TEMPORARIOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresAnosIniciaisTEMP = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND func_regime = '2' AND turma_etapa IN (14,15,16,17,18) 
GROUP BY ch_lotacao_professor_id";
$ProfessoresAnosIniciaisTEMP = mysql_query($query_ProfessoresAnosIniciaisTEMP, $SmecelNovo) or die(mysql_error());
$row_ProfessoresAnosIniciaisTEMP = mysql_fetch_assoc($ProfessoresAnosIniciaisTEMP);
$totalRows_ProfessoresAnosIniciaisTEMP = mysql_num_rows($ProfessoresAnosIniciaisTEMP);

//39 - Quantidade de professores dos Anos Iniciais: EFETIVOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresAnosFinaisEFE = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND func_regime = '1' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id";
$ProfessoresAnosFinaisEFE = mysql_query($query_ProfessoresAnosFinaisEFE, $SmecelNovo) or die(mysql_error());
$row_ProfessoresAnosFinaisEFE = mysql_fetch_assoc($ProfessoresAnosFinaisEFE);
$totalRows_ProfessoresAnosFinaisEFE = mysql_num_rows($ProfessoresAnosFinaisEFE);

//40 - Quantidade de professores dos Anos Iniciais: TEMPORARIOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfessoresAnosFinaisTEMP = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_sec, turma_ano_letivo, turma_etapa,
func_id, func_escolaridade, func_regime
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE turma_ano_letivo = '$row_Ano[ano_letivo_ano]' AND func_regime = '2' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_etapa IN (19,20,21,22)
GROUP BY ch_lotacao_professor_id";
$ProfessoresAnosFinaisTEMP = mysql_query($query_ProfessoresAnosFinaisTEMP, $SmecelNovo) or die(mysql_error());
$row_ProfessoresAnosFinaisTEMP = mysql_fetch_assoc($ProfessoresAnosFinaisTEMP);
$totalRows_ProfessoresAnosFinaisTEMP = mysql_num_rows($ProfessoresAnosFinaisTEMP);
?>



<div class="ls-box">
  <h5 class="ls-title-5">Ano Letivo <?php echo $row_Ano['ano_letivo_ano']; ?></h5>


 <table class="ls-table ls-xs-space">
	<tbody>

 <tr><td><strong>1.9 - Quantidade de professores da Creche que: Possuem formação do tipo Graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresGradCreche; ?></td></tr>
 <tr><td><strong>1.10 - Quantidade de professores da Creche que: Possuem formação do tipo Pós-graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresPosGradCreche; ?></td></tr>

 <tr><td><strong>1.24 - Número de vagas na Creche ofertadas em <?php echo $row_Ano['ano_letivo_ano']; ?>:</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_VagasNaCreche; ?></td></tr>

 <tr><td><strong>1.25 - Quantidade de turmas de Creche: Até 13 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $EIate13; ?></td></tr>
 <tr><td><strong>1.26 - Quantidade de turmas de Creche: De 14 a 20 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $EIde14a20; ?></td></tr>
 <tr><td><strong>1.27 - Quantidade de turmas de Creche: De 21 a 25 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $EIde21a25; ?></td></tr>
 <tr><td><strong>1.28 - Quantidade de turmas de Creche: Acima de 25 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $EIacima25; ?></td></tr>

 <tr><td><strong>2.7 - Quantidade de professores da Pré-escola que: Possuem formação do tipo Graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresGradPre; ?></td></tr>
 <tr><td><strong>2.8 - Quantidade de professores da Pré-escola que: Possuem formação do tipo Pós-graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresPosGradPre; ?></td></tr>


 <tr><td><strong>2.23 - Quantidade de turmas de Pré-escola: Até 22 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $PEate22; ?></td></tr>
 <tr><td><strong>2.24 - Quantidade de turmas de Pré-escola: De 23 a 25 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $PEde23a25; ?></td></tr>
 <tr><td><strong>2.25 - Quantidade de turmas de Pré-escola: De 26 a 30 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $PEde26a30; ?></td></tr>
 <tr><td><strong>2.26 - Quantidade de turmas de Pré-escola: Acima de 30 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $PEacima30; ?></td></tr>


 <tr><td><strong>3.26 - Número de alunos matriculados dos Anos Iniciais por turno: manhã</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_MatriculasAtivas_326; ?></td></tr>
 <tr><td><strong>3.27 - Número de alunos matriculados dos Anos Iniciais por turno: tarde</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_MatriculasAtivas_327; ?></td></tr>
 <tr><td><strong>3.28 - Quantidade de turmas dos Anos Iniciais: Até 24 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $turmasAlunosMais24; ?></td></tr>
 <tr><td><strong>3.29 - Quantidade de turmas dos Anos Iniciais: De 25 a 30 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $turmasAlunos25a30; ?></td></tr>
 <tr><td><strong>3.30 - Quantidade de turmas dos Anos Iniciais: De 31 a 33 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $turmasAlunos31a33; ?></td></tr>
 <tr><td><strong>3.31 - Quantidade de turmas dos Anos Iniciais: Acima de 33 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $turmasAlunosAcima33; ?></td></tr>
 <tr><td><strong>4.25 - Número de alunos matriculados dos Anos Finais por turno: manhã</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_MatriculasAtivas_425; ?></td></tr>
 <tr><td><strong>4.26 - Número de alunos matriculados dos Anos Finais por turno: tarde</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_MatriculasAtivas_426; ?></td></tr>
 <tr><td><strong>4.27 - Quantidade de turmas dos Anos Finais: Até 30 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $ate30_427; ?></td></tr>
 <tr><td><strong>4.28 - Quantidade de turmas dos Anos Finais: De 31 a 35 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $de31ate35_427; ?></td></tr>
 <tr><td><strong>4.29 - Quantidade de turmas dos Anos Finais: De 36 a 39 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $de36a39_427; ?></td></tr>
 <tr><td><strong>4.30 - Quantidade de turmas dos Anos Finais: Acima de 39 alunos</strong></td><td width="100" class="ls-txt-center"><?php echo $acima39_427; ?></td></tr>
 <tr><td><strong>4.5 - Quantidade de professores dos Anos Finais: Possuem formação de Graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresGradAnosFinais; ?></td></tr>
 <tr><td><strong>4.6 - Quantidade de professores dos Anos Finais: Possuem formação de Pós-Graduação</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresPosGradAnosFinais; ?></td></tr>
 <tr><td><strong>28 - Quantidade de professores dos Anos Iniciais: Efetivos</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresAnosIniciaisEFE; ?></td></tr>
 <tr><td><strong>29 - Quantidade de professores dos Anos Iniciais: Temporários</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresAnosIniciaisTEMP; ?></td></tr>
 <tr><td><strong>39 - Quantidade de professores dos Anos Finais: Efetivos</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresAnosFinaisEFE; ?></td></tr>
 <tr><td><strong>40 - Quantidade de professores dos Anos Finais: Temporários</strong></td><td width="100" class="ls-txt-center"><?php echo $totalRows_ProfessoresAnosFinaisTEMP; ?></td></tr>
</tbody>


 </table>



</div>



  <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>  
   
    
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

mysql_free_result($VagasNaCreche);

mysql_free_result($ProfessoresGradCreche);
?>