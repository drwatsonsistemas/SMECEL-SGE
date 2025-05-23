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
//require_once('funcoes/anoLetivo.php');

/*
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);
*/

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivo = "SELECT * FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' AND ano_letivo_aberto = 'S' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivo = mysql_query($query_AnoLetivo, $SmecelNovo) or die(mysql_error());
$row_AnoLetivo = mysql_fetch_assoc($AnoLetivo);
$totalRows_AnoLetivo = mysql_num_rows($AnoLetivo);



if ($totalRows_AnoLetivo < 1) {
	$redireciona = "anoletivonovo.php";
	header(sprintf("Location: %s", $redireciona));
	}



$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
    if ($_GET['ano'] == "") {
        header("Location: index.php?nada"); 
        exit;
    }
    $anoLetivo = anti_injection($_GET['ano']);
    $anoLetivo = (int)$anoLetivo;
}

// Adiciona filtro de localização
$filtro_localizacao = "";
if (isset($_GET['localizacao']) && $_GET['localizacao'] != "") {
    $filtro_localizacao = " AND escola_localizacao = '" . $_GET['localizacao'] . "'";
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

if ($row_Secretaria['sec_bloqueada']=="S") { 

  $logoutGoToBlock = "../../index.php?fin";
  if ($logoutGoToBlock) {
    //header("Location: $logoutGoToBlock");
    //exit;
  }

}

if ($totalRows_Secretaria == 0) {
  
/*  
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  
*/	
  $logoutGoTo = "../../index.php?ops";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, escola_id, escola_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND escola_situacao = '1'" . $filtro_localizacao;
$MatriculasAtivas = mysql_query($query_MatriculasAtivas, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivas = mysql_fetch_assoc($MatriculasAtivas);
$totalRows_MatriculasAtivas = mysql_num_rows($MatriculasAtivas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivasEspeciais = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, escola_id, escola_situacao, aluno_id, aluno_aluno_com_deficiencia 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND escola_situacao = '1' AND aluno_aluno_com_deficiencia = '1'" . $filtro_localizacao;
$MatriculasAtivasEspeciais = mysql_query($query_MatriculasAtivasEspeciais, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivasEspeciais = mysql_fetch_assoc($MatriculasAtivasEspeciais);
$totalRows_MatriculasAtivasEspeciais = mysql_num_rows($MatriculasAtivasEspeciais);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivasEspeciaisLaudo = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, escola_id, escola_situacao, aluno_id, aluno_aluno_com_deficiencia, aluno_laudo 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND escola_situacao = '1' AND aluno_aluno_com_deficiencia = '1' AND aluno_laudo = '1'" . $filtro_localizacao;
$MatriculasAtivasEspeciaisLaudo = mysql_query($query_MatriculasAtivasEspeciaisLaudo, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivasEspeciaisLaudo = mysql_fetch_assoc($MatriculasAtivasEspeciaisLaudo);
$totalRows_MatriculasAtivasEspeciaisLaudo = mysql_num_rows($MatriculasAtivasEspeciaisLaudo);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasAtivasTransporteEscolar = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, escola_id, escola_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND escola_situacao = '1' AND vinculo_aluno_transporte = 'S'" . $filtro_localizacao;
$MatriculasAtivasTransporteEscolar = mysql_query($query_MatriculasAtivasTransporteEscolar, $SmecelNovo) or die(mysql_error());
$row_MatriculasAtivasTransporteEscolar = mysql_fetch_assoc($MatriculasAtivasTransporteEscolar);
$totalRows_MatriculasAtivasTransporteEscolar = mysql_num_rows($MatriculasAtivasTransporteEscolar);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolasAtivas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_ue = '1' AND escola_situacao = '1' AND escola_id_sec = '$row_Secretaria[sec_id]'" . $filtro_localizacao;
$EscolasAtivas = mysql_query($query_EscolasAtivas, $SmecelNovo) or die(mysql_error());
$row_EscolasAtivas = mysql_fetch_assoc($EscolasAtivas);
$totalRows_EscolasAtivas = mysql_num_rows($EscolasAtivas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculasPorEtapa = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_etapa, turma_ano_letivo, etapa_id, etapa_nome, etapa_nome_abrev, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE turma_ano_letivo = '$anoLetivo' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1'" . $filtro_localizacao . "
GROUP BY etapa_id, etapa_nome, etapa_nome_abrev
ORDER BY etapa_id";
$MatriculasPorEtapa = mysql_query($query_MatriculasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa);
$totalRows_MatriculasPorEtapa = mysql_num_rows($MatriculasPorEtapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapa = "
SELECT 
turma_id, turma_etapa, etapa_id, etapa_nome, etapa_nome_abrev, turma_ano_letivo, turma_id_sec, turma_id_escola, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_ano_letivo = '$anoLetivo' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1'" . $filtro_localizacao . "
GROUP BY etapa_id, etapa_nome, etapa_nome_abrev
ORDER BY etapa_id";
$TurmasPorEtapa = mysql_query($query_TurmasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa);
$totalRows_TurmasPorEtapa = mysql_num_rows($TurmasPorEtapa);

// mysql_select_db($database_SmecelNovo, $SmecelNovo);
// $query_MatriculasPorEscolas = "
// SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
// vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
// vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
// vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, escola_id, escola_nome, escola_ue, escola_situacao, COUNT(*) AS total 
// FROM smc_vinculo_aluno
// INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
// WHERE escola_ue = '1' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_situacao = '1' AND escola_situacao = '1'" . $filtro_localizacao . "
// GROUP BY escola_id, escola_nome, escola_ue, escola_situacao";
// $MatriculasPorEscolas = mysql_query($query_MatriculasPorEscolas, $SmecelNovo) or die(mysql_error());
// $row_MatriculasPorEscolas = mysql_fetch_assoc($MatriculasPorEscolas);
// $totalRows_MatriculasPorEscolas = mysql_num_rows($MatriculasPorEscolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$anoLetivo' AND escola_situacao = '1'" . $filtro_localizacao;
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAee = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$anoLetivo' AND escola_situacao = '1' AND turma_tipo_atendimento = '2'" . $filtro_localizacao;
$TurmasAee = mysql_query($query_TurmasAee, $SmecelNovo) or die(mysql_error());
$row_TurmasAee = mysql_fetch_assoc($TurmasAee);
$totalRows_TurmasAee = mysql_num_rows($TurmasAee);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasAc = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, escola_id, escola_situacao
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_id_sec = '$row_Secretaria[sec_id]' AND turma_ano_letivo = '$anoLetivo' AND escola_situacao = '1' AND turma_tipo_atendimento = '3'" . $filtro_localizacao;
$TurmasAc = mysql_query($query_TurmasAc, $SmecelNovo) or die(mysql_error());
$row_TurmasAc = mysql_fetch_assoc($TurmasAc);
$totalRows_TurmasAc = mysql_num_rows($TurmasAc);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
func_id, func_id_sec 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_escola ON escola_id = vinculo_id_escola
WHERE func_id_sec = '$row_Secretaria[sec_id]'" . $filtro_localizacao;
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSituacao = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, COUNT(vinculo_aluno_id) AS total,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'ATIVOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao,
CASE vinculo_aluno_situacao
WHEN 1 THEN ''
WHEN 2 THEN 'green'
WHEN 3 THEN 'red'
WHEN 4 THEN 'silver'
WHEN 5 THEN 'grey'
END AS cor 
FROM smc_vinculo_aluno 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_sec = '$row_Secretaria[sec_id]'" . $filtro_localizacao . "
GROUP BY vinculo_aluno_situacao ASC";
$AlunosSituacao = mysql_query($query_AlunosSituacao, $SmecelNovo) or die(mysql_error());
$row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao);
$totalRows_AlunosSituacao = mysql_num_rows($AlunosSituacao);
?>

<!DOCTYPE html>
<html class="ls-theme-green">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

  <style>
    .float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:40px;
	right:40px;
	background-color:#25d366;
	color:#FFF;
	border-radius:50px;
	text-align:center;
  font-size:30px;
	box-shadow: 2px 2px 3px #999;
  z-index:100;
}

.my-float{
	margin-top:16px;
}
  </style>
<script src="js/locastyle.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
		
		
		['Data', 'Matrículas', 'Transferências'],
		
		<?php
				$date_fim = date("Y-m-d"); //Data final
				$date_ini = date("Y-m-d", strtotime("-10 days",strtotime($date_fim)));; //Data inicial
				$contaMatriculas = 0;
				$contaTransferencias = 0;
				
				while (strtotime($date_ini) <= strtotime($date_fim)) {
					
					mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_Matriculas = "
					SELECT vinculo_aluno_data, vinculo_aluno_situacao, vinculo_aluno_datatransferencia 
					FROM smc_vinculo_aluno
					WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_data = '$date_ini' AND vinculo_aluno_id_sec = $row_UsuarioLogado[usu_sec]";
					$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
					$row_Matriculas = mysql_fetch_assoc($Matriculas);
					$totalRows_Matriculas = mysql_num_rows($Matriculas);
						
					mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_MatriculasT = "
					SELECT vinculo_aluno_data, vinculo_aluno_situacao, vinculo_aluno_datatransferencia
					FROM smc_vinculo_aluno
					WHERE vinculo_aluno_situacao = '2' AND vinculo_aluno_datatransferencia = '$date_ini' AND vinculo_aluno_id_sec = $row_UsuarioLogado[usu_sec]";
					$MatriculasT = mysql_query($query_MatriculasT, $SmecelNovo) or die(mysql_error());
					$row_MatriculasT = mysql_fetch_assoc($MatriculasT);
					$totalRows_MatriculasT = mysql_num_rows($MatriculasT);
						
				
				?>
				
				 ['<?php echo date("d/m", strtotime($date_ini)); ?>',  <?php echo $totalRows_Matriculas; ?>,  <?php echo $totalRows_MatriculasT; ?>],
				
				<?php 
				
				$date_ini = date ("Y-m-d", strtotime("+1 day", strtotime($date_ini)));
				$contaMatriculas = $contaMatriculas + $totalRows_Matriculas;
				$contaTransferencias = $contaTransferencias + $totalRows_MatriculasT;
				
				}		
		
			?>

		  
		  
        ]);
		
        var options = {
			vAxis: {minValue: 0},
			legend: {position: 'bottom', maxLines: 3},
   		    animation:{
				startup: true,	
				duration: 1000,
				easing: 'linear'
      		}			
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div_matriculas'));
        chart.draw(data, options);
      }
    </script>

	<script type="text/javascript">

	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        // Create the data table.
        var data = google.visualization.arrayToDataTable([
          ['SITUAÇÃO', 'TOTAL'],
		<?php $mat_total = 0; ?>
		<?php do { ?>
		  ["<?php echo $row_AlunosSituacao['vinculo_aluno_situacao']; ?> (<?php echo $row_AlunosSituacao['total']; ?>)", <?php echo $row_AlunosSituacao['total']; ?>],
		  <?php $mat_total = $mat_total + $row_AlunosSituacao['total']; ?>
    	<?php } while ($row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao)); ?>
		
        ]);
        var options = {
          title: 'Matrículas por situação (<?php echo $mat_total; ?>)'
		  };
        var chart = new google.visualization.PieChart(document.getElementById('alunos_situacao'));
        chart.draw(data, options);
      }
	  
    </script>

<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
<a href="https://api.whatsapp.com/send?phone=557398685288" class="float" target="_blank">
<i class="fa fa-whatsapp my-float"></i>
</a>
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ANO LETIVO <?php echo $anoLetivo; ?></h1>
        
    

		<?php if ($row_Secretaria['sec_aviso_bloqueio']=="S") { ?>
        <div class="ls-alert-danger"><strong>Atenção:</strong> Existem pendências financeiras em relação ao sistema. Favor entrar em contato com o setor responsável.</div>
        <?php } ?>

		
		<?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Ops! </strong> Isso não deveria ter acontecido. Um e-mail foi enviado ao administrador do sistema! </div>
        <?php } ?>


        <!-- <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
                <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
                <ul class="ls-dropdown-nav">
                  
                  <li><a href="anos_anteriores.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano'];; ?>" target="" title="Diários">ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></a></li>

                  <?php do { ?>

                    <li><a href="anos_anteriores.php?ano=<?php echo $row_Ano['ano_letivo_ano']; ?>" target="" title="Diários">ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?></a></li>

                  <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>
                  
                </ul>
              </div>	 -->

              <div class="ls-box ">
        <form method="get" class="ls-form-horizontal">
            <div class="row">
                <div class="col-md-4">
                    <label class="ls-label">
                        <b class="ls-label-text">FILTRAR POR LOCALIZAÇÃO</b>
                        <div class="ls-custom-select">
                            <select name="localizacao" class="ls-select" onchange="this.form.submit()">
                                <option value="">TODAS AS ESCOLAS</option>
                                <option value="U" <?php if (isset($_GET['localizacao']) && $_GET['localizacao'] == 'U') echo 'selected'; ?>>ESCOLAS URBANAS</option>
                                <option value="R" <?php if (isset($_GET['localizacao']) && $_GET['localizacao'] == 'R') echo 'selected'; ?>>ESCOLAS DO CAMPO</option>
                            </select>
                        </div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="ls-label">
                        <b class="ls-label-text">ANO LETIVO</b>
                        <div class="ls-custom-select">
                            <select name="ano" class="ls-select" onchange="this.form.submit()">
                                <option value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>" <?php if (!isset($_GET['ano']) || $_GET['ano'] == $row_AnoLetivo['ano_letivo_ano']) echo 'selected'; ?>>
                                    <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
                                </option>
                                <?php
                                // Resetar o ponteiro para o início
                                mysql_data_seek($Ano, 0);
                                while ($row_Ano = mysql_fetch_assoc($Ano)) { ?>
                                    <option value="<?php echo $row_Ano['ano_letivo_ano']; ?>" <?php if (isset($_GET['ano']) && $_GET['ano'] == $row_Ano['ano_letivo_ano']) echo 'selected'; ?>>
                                        <?php echo $row_Ano['ano_letivo_ano']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </div>
	
	<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em <strong><?php echo date("d/m/Y"); ?></strong></p>
    <h2 class="ls-title-3">Informações da Rede de Ensino em <?php echo $anoLetivo; ?></h2>
  </header>

  <div id="sending-stats" class="row ls-clearfix">
    
    <div class="col-sm-6 col-md-2" style="background-color:#D9FFB3;">
      <div class="ls-box" style="background-color:#D9FFB3;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">ESCOLAS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-theme"><span class="count"><?php echo $totalRows_EscolasAtivas; ?></span> <small>EM FUNCIONAMENTO</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?>
              <a href="escolas.php?ano=<?php echo $anoLetivo; ?>" class="ls-btn ls-btn-xs">Ver escolas</a>
          <?php } ?>
        </div>
      </div>
    </div>	

    <div class="col-sm-6 col-md-2" style="background-color:#FFC4C4;">
      <div class="ls-box" style="background-color:#FFC4C4;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TURMAS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-danger"><span class="count"><?php echo $totalRows_Turmas; ?></span> <small>CADASTRADAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?>
              <a href="turmas.php?ano=<?php echo $anoLetivo; ?>" class="ls-btn ls-btn-xs">Ver Turmas</a>
          <?php } ?>
        </div>
      </div>
    </div>


<div class="col-sm-6 col-md-2" style="background-color:#e1bee7;">
      <div class="ls-box" style="background-color:#e1bee7;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TURMAS</h6>
        </div>
        <div class="ls-box-body">
          <div class="col-xs-6">
            <strong class="ls-color-danger"><?php echo $totalRows_TurmasAee; ?></strong>
            <small>AEE</small>
          </div>
          <div class="col-xs-6">
            <strong class="ls-color-danger"><?php echo $totalRows_TurmasAc; ?></strong>
            <small>A. COMP.</small>
          </div>

        </div>
        		<div class="ls-box-footer">
          <!--<?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?><a href="turmas.php" class="ls-btn ls-btn-xs">Ver Turmas</a><?php } ?>-->
        </div>
      </div>
</div>


    <div class="col-sm-6 col-md-2" style="background-color:#FFE8DD;">
      <div class="ls-box" style="background-color:#FFE8DD;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">MATRÍCULAS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-warning"><span class="count"><?php echo $totalRows_MatriculasAtivas; ?></span> <small>ATIVAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?>
              <a href="matriculas.php?ano=<?php echo $anoLetivo; ?>" class="ls-btn ls-btn-xs">Ver Matrículas</a>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-2" style="background-color:#DEF;">
      <div class="ls-box" style="background-color:#DEF;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">ALUNOS ESPECIAIS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-info"><span class="count"><?php echo $totalRows_MatriculasAtivasEspeciais; ?></span> <small><?php echo number_format((($totalRows_MatriculasAtivasEspeciais/$totalRows_MatriculasAtivas)*100),2,'.',''); ?>%</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
        <small><?php echo $totalRows_MatriculasAtivasEspeciaisLaudo; ?> aluno(as) com laudo.</small>
          <!-- <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?><a href="matriculas.php" class="ls-btn ls-btn-xs">Ver Matrículas</a><?php } ?> -->
        </div>
      </div>
    </div>



    <div class="col-sm-6 col-md-2" style="background-color:#D9FFD9;">
      <div class="ls-box" style="background-color:#D9FFD9;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TRANSPORTE ESCOLAR</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-success"><span class="count"><?php echo $totalRows_MatriculasAtivasTransporteEscolar; ?></span> <small><?php echo number_format((($totalRows_MatriculasAtivasTransporteEscolar/$totalRows_MatriculasAtivas)*100),2,'.',''); ?>%</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <!-- <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?><a href="matriculas.php" class="ls-btn ls-btn-xs">Ver Matrículas</a><?php } ?> -->
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-2" style="background-color:#F4F4F4;">
      <div class="ls-box" style="background-color:#F4F4F4;">
        <div class="ls-box-head">
          <h6 class="ls-title-4">FUNCIONÁRIOS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-dark"><span class="count"><?php echo $totalRows_Funcionarios; ?></span> <small>VINCULADOS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
          <?php if ($row_UsuarioLogado['usu_m_administrativo']=="S") { ?><a href="funcionarios_vinculados.php" class="ls-btn ls-btn-xs">Ver Funcionários</a><?php } ?>
        </div>
      </div>
    </div>

  
  </div>

    
    
<?php if ($totalRows_MatriculasPorEtapa > 0) { ?>    
<div class="ls-box ls-board-box"> 
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	  <script type="text/javascript">
		google.charts.load("current", {packages:['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
		  var data = google.visualization.arrayToDataTable([
		  
				
	["ETAPA", "TOTAL", { role: "style" } ],		
	<?php do { ?>
	  ["<?php echo $row_MatriculasPorEtapa['etapa_nome_abrev']; ?>", <?php echo $row_MatriculasPorEtapa['total']; ?>, "#b87333"],
	<?php } while ($row_MatriculasPorEtapa = mysql_fetch_assoc($MatriculasPorEtapa)); ?>
			
			
		  ]);

		  var view = new google.visualization.DataView(data);
		  view.setColumns([0, 1,
						   { calc: "stringify",
							 sourceColumn: 1,
							 type: "string",
							 role: "annotation" },
						   2]);

		  var options = {
			title: 'MATRÍCULAS POR ETAPA DE ENSINO',
			subtitle: 'SOMENTE MATRÍCULAS ATIVAS',
			width: '100%',
			orientation: "horizontal",
			height: 600,
			fontSize: 10,
			histogram: { lastBucketPercentile: 5 },
			vAxis: { scaleType: 'mirrorLog' },
			bar: {groupWidth: "50%"},
			legend: { position: "none" },
		  };
		  var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
		  chart.draw(view, options);
	  }
	  </script>
	<div id="columnchart_values" style="width: 100%; height: 600px;"></div>
</div>
<?php } else { ?>
<div class="ls-box ls-board-box">GRÁFICO MATRÍCULAS POR ETAPA DE ENSINO<br><small>(Disponível após preenchimento de dados feito pelas Unidades Escolares)</small></div>
<?php } ?>

<?php if ($totalRows_TurmasPorEtapa > 0) { ?> 
<div class="ls-box ls-board-box"> 
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	  <script type="text/javascript">
		google.charts.load("current", {packages:['corechart']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
		  var data = google.visualization.arrayToDataTable([
		  
				
	["ETAPA", "TOTAL", { role: "style" } ],		
	<?php do { ?>
	  ["<?php echo $row_TurmasPorEtapa['etapa_nome_abrev']; ?>", <?php echo $row_TurmasPorEtapa['total']; ?>, "#a09555"],
	<?php } while ($row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa)); ?>
			
			
		  ]);

		  var view = new google.visualization.DataView(data);
		  view.setColumns([0, 1,
						   { calc: "stringify",
							 sourceColumn: 1,
							 type: "string",
							 role: "annotation" },
						   2]);

		  var options = {
			title: 'TURMAS POR ETAPA DE ENSINO',
			width: '100%',
			orientation: "horizontal",
			height: 600,
			fontSize: 10,
			histogram: { lastBucketPercentile: 5 },
			vAxis: { scaleType: 'mirrorLog' },
			bar: {groupWidth: "50%"},
			legend: { position: "none" },
		  };
		  var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values_1"));
		  chart.draw(view, options);
	  }
	  </script>
	<div id="columnchart_values_1" style="width: 100%; height: 600px;"></div>
</div>
<?php } else { ?>
<div class="ls-box ls-board-box">GRÁFICO TURMAS POR ETAPA DE ENSINO<br><small>(Disponível após preenchimento de dados feito pelas Unidades Escolares)</small></div>
<?php } ?>
    
    
</div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script type="text/javascript">
$('.count').each(function () {
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: 3000,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now));
        }
    });
});
</script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($MatriculasAtivas);

mysql_free_result($EscolasAtivas);

mysql_free_result($Atualizacoes);

mysql_free_result($AtualizacoesVisualizadas);

mysql_free_result($MatriculasPorEtapa);

mysql_free_result($MatriculasPorEscolas);

mysql_free_result($Turmas);

mysql_free_result($Funcionarios);
?>