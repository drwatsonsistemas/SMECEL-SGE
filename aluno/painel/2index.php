<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php //include('funcoes/logins.php'); ?>
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
	
  $logoutGoTo = "../index.php?saiu";
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
$MM_authorizedUsers = "6";
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

$MM_restrictGoTo = "../index.php?err";
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, 
aluno_filiacao1, aluno_filiacao2 
aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge,  
aluno_foto, aluno_endereco, aluno_numero, aluno_bairro, aluno_telefone, aluno_celular,aluno_email, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_tel_mae, aluno_tel_pai,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf 
FROM smc_aluno
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome,
turma_id, turma_nome, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Colegas = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
 vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
 vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
 vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto 
 FROM smc_vinculo_aluno
 INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
 WHERE vinculo_aluno_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' 
 ORDER BY RAND() LIMIT 0,9
 ";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);

$dataa = date('Y-m-d');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_publicado = 'S' AND plano_aula_data <= '$dataa'
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL)
ORDER BY plano_aula_data DESC
";
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atividade = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash, 
plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite,
func_id, func_nome, 
disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' 
AND plano_aula_atividade_resposta_obrigatoria = 'S' 
AND plano_aula_publicado = 'S' 
AND plano_aula_data <= '$dataa'
AND plano_aula_atividade_resposta_obrigatoria_data_limite >= '$dataa'
ORDER BY plano_aula_atividade_resposta_obrigatoria_data_limite ASC
";
$Atividade = mysql_query($query_Atividade, $SmecelNovo) or die(mysql_error());
$row_Atividade = mysql_fetch_assoc($Atividade);
$totalRows_Atividade = mysql_num_rows($Atividade);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_NovasAulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_publicado, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_publicado = 'S' AND plano_aula_data <= NOW()
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL)
";
$NovasAulas = mysql_query($query_NovasAulas, $SmecelNovo) or die(mysql_error());
$row_NovasAulas = mysql_fetch_assoc($NovasAulas);
$totalRows_NovasAulas = mysql_num_rows($NovasAulas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasVisualizadas = "
SELECT 
visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, plano_aula_id, plano_aula_id_turma, 
plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_texto, plano_aula_conteudo, 
plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
FROM smc_visualiza_aula
INNER JOIN smc_plano_aula ON plano_aula_id = visualiza_aula_id_aula
WHERE visualiza_aula_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND plano_aula_publicado = 'S' AND plano_aula_data <= NOW() 
AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL)
GROUP BY visualiza_aula_id_aula
";
$AulasVisualizadas = mysql_query($query_AulasVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AulasVisualizadas = mysql_fetch_assoc($AulasVisualizadas);
$totalRows_AulasVisualizadas = mysql_num_rows($AulasVisualizadas);

$novasAulas = $totalRows_NovasAulas - $totalRows_AulasVisualizadas;

if ($totalRows_AulasVisualizadas > $totalRows_NovasAulas) {
	
	$rest = $totalRows_AulasVisualizadas - $totalRows_NovasAulas;
	
	$novasAulas = $novasAulas + $rest;
	
	}
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "
SELECT aviso_turma_id, aviso_turma_id_turma, aviso_turma_id_escola, aviso_turma_data, DATE_FORMAT(aviso_turma_data, '%d/%m/%Y') AS aviso_turma_data, aviso_turma_hora, aviso_turma_texto, aviso_turma_ano 
FROM smc_aviso_turma 
WHERE aviso_turma_ano = '$row_Matricula[vinculo_aluno_ano_letivo]' AND aviso_turma_id_escola = '$row_Matricula[vinculo_aluno_id_escola]' AND (aviso_turma_id_turma = '0' OR aviso_turma_id_turma = '$row_Matricula[vinculo_aluno_id_turma]')
ORDER BY aviso_turma_id DESC
LIMIT 0,6";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);
 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $row_AlunoLogado['aluno_nome']; ?> - <?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?> - <?php echo $row_Matricula['escola_nome']; ?></title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

<link href='../../sistema/calendario/lib/main.css' rel='stylesheet' />
<script src='../../sistema/calendario/lib/main.js'></script>
<script src='../../sistema/calendar/core/locales/pt-br.js'></script>

    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {	
		  initialDate: '<?php echo date("Y-m-d"); ?>',	
          initialView: 'dayGridMonth',
		  aspectRatio: 1,
		  navLinks: true, // can click day/week names to navigate views
		  editable: false,
		  eventLimit: true, // allow "more" link when too many events
		  locale: 'pt-BR',
		  
		  
		  
		  events: [
		  
		  
		  
		  
		  
	  <?php do { ?>
	  
	    <?php 
   
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Visualizou = "SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora FROM smc_visualiza_aula WHERE visualiza_aula_id_aula = $row_Conteudo[plano_aula_id] AND visualiza_aula_id_matricula = '$row_Matricula[vinculo_aluno_id]'";
		$Visualizou = mysql_query($query_Visualizou, $SmecelNovo) or die(mysql_error());
		$row_Visualizou = mysql_fetch_assoc($Visualizou);
		$totalRows_Visualizou = mysql_num_rows($Visualizou);
   
		?>
	  
	  
	  
	  {
          title: '<?php echo $row_Conteudo['disciplina_nome']; ?> - <?php echo $row_Conteudo['plano_aula_texto']; ?> - <?php echo $row_Conteudo['func_nome']; ?>',
          start: '<?php echo $row_Conteudo['plano_aula_data']; ?>',
		  <?php if ($totalRows_Visualizou > 0) { ?>color: '#558b2f',<?php } else { ?>color: '#b71c1c',<?php } ?>
		  <?php if ($row_Conteudo['plano_aula_data'] <= date('Y-m-d')) { ?>url: '<?php echo URL_BASE.'aluno/painel/aulas_conteudo.php?aula='.$row_Conteudo['plano_aula_hash']; ?>',<?php } else { ?>color: '#cacaca',<?php } ?>

        },
	  
	  <?php //mysql_free_result($Visualizou); ?>
	  <?php } while ($row_Conteudo = mysql_fetch_assoc($Conteudo)); ?>
		  
		  
		  

  ]
		  
        });
        calendar.render();
      });

    </script>

<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}

  #calendar {
    max-width: 900px;
    margin: 0 auto;
	font-size:11px;
  }
</style>


</head>
<body class="indigo lighten-5">

<?php include "menu_top.php"?>

<div class="container">

	  <?php if ($novasAulas > 0) { ?>
	  <blockquote class="flow-text red lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny pulse red lighten-2"><i class="tiny material-icons">add_alert</i></a> 
	  Você tem <strong><?php echo $novasAulas; ?></strong> aula(s) pendente(s) <a href="aulasNovas.php">Ver</a>
	  </blockquote>
	  <?php } else { ?>
	  <blockquote class="flow-text green lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny green lighten-2"><i class="tiny material-icons">check_circle</i></a> 
	  Parabéns! Você não tem aulas pendentes.
	  </blockquote>
	  <?php } ?>
	  
	  <?php if ($totalRows_Atividade > 0) { ?>
	  <blockquote class="flow-text orange lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny pulse orange lighten-2"><i class="tiny material-icons">description</i></a> 
	  Você possui <strong><?php echo $totalRows_Atividade; ?></strong> atividade(s) dentro do prazo
	  <hr>
	  <table>
	  <tr>
		<th>ATIVIDADE</th>
		<th>DATA LIMITE</th>
		<th>SITUAÇÃO</th>
	  </tr>
	  <?php do { 
	  
	  
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtividadeResposta = "
SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, 
plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora 
FROM smc_plano_aula_anexo_atividade
WHERE plano_aula_anexo_atividade_id_aluno = '$row_Matricula[vinculo_aluno_id]' AND plano_aula_anexo_atividade_id_atividade = '$row_Atividade[plano_aula_id]'";
$AtividadeResposta = mysql_query($query_AtividadeResposta, $SmecelNovo) or die(mysql_error());
$row_AtividadeResposta = mysql_fetch_assoc($AtividadeResposta);
$totalRows_AtividadeResposta = mysql_num_rows($AtividadeResposta);
	  
	  
	  ?>
	  
	  <tr>
	  <td>
	  <a href="aulas_conteudo.php?aula=<?php echo $row_Atividade['plano_aula_hash']; ?>"><?php echo $row_Atividade['plano_aula_texto']; ?></a><br>
	  <small><?php echo $row_Atividade['disciplina_nome']; ?> / <?php echo $row_Atividade['func_nome']; ?></small>
	  </td>
	  <td><?php echo inverteData($row_Atividade['plano_aula_atividade_resposta_obrigatoria_data_limite']); ?></td>
	  <td><?php if ($totalRows_AtividadeResposta == 0) { ?> <span class="red-text"><i class="material-icons tiny left">add_alert</i> PENDENTE </span>	<?php } else { ?><span class="green-text"><i class="material-icons tiny left">check_circle</i> ENVIADO</span><?php } ?></td>
	  </tr>
	  
	  <?php } while ($row_Atividade = mysql_fetch_assoc($Atividade)); ?>
	  </table>
	  </blockquote>
	  <?php } ?>
	  
	  
	  
	  
	  

        <?php if (isset($_GET["erro"])) { ?>
		<blockquote class="flow-text red lighten-4" style="padding: 10px;">
          <p class="red-text"><i class="material-icons">sentiment_very_dissatisfied</i>
            A ação anterior gerou um erro. O suporte foi comunicado.
		  </p>
		  </blockquote>
        <?php } ?>

  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 truncate hide-on-small-only">
      <p>
        <?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } ?>
		
		<br>
		
		<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small>
		
      </p>
	  
	<?php include "menu_esq.php"; ?>

    </div>
        
    <div class="col s12 m6">
	
		<div class="row">
			<div class="col s4 show-on-small" style="display:none;">
				<p>
				<?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
				<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
				<?php } else { ?>
				<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
				<?php } ?>
				</p>
			</div>
			
			<div class="col s8 m12">
			
			  <h5>Bem-vindo(a),
				<?php $nome = explode(" ",$row_AlunoLogado['aluno_nome']); echo $nome[0]; ?>
			  </h5>
			  
			  <blockquote><h6><strong><?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?><br><?php echo $row_Matricula['escola_nome']; ?></strong></h6></blockquote>

			  
			</div>
		
		</div>
        

		  <?php if ($totalRows_Avisos > 0) { // Show if recordset not empty ?>
          
            <?php do { ?>
			
				<div class="card-panel grey lighten-5">
                
				<h6 class="right">Aviso emitido em <?php echo $row_Avisos['aviso_turma_data']; ?> às <?php echo $row_Avisos['aviso_turma_hora']; ?> <i class="tiny material-icons">add_alert</i></h6><br>
                <span><?php echo $row_Avisos['aviso_turma_texto']; ?></span>
				</div>
							  
              <?php } while ($row_Avisos = mysql_fetch_assoc($Avisos)); ?>
          
          <?php } else { ?>
          <div class="card-panel">Você não tem nenhum aviso.</div>
          <?php } // Show if recordset not empty ?>

      	  	  
	  
	  		<p>	
			  <a href="calendario.php" class="waves-effect waves-light btn"><i class="material-icons left">today</i>CALENDÁRIO</a>
			  <a href="disciplinas.php" class="waves-effect waves-light purple accent-3 btn"><i class="material-icons left">apps</i>COMPONENTES</a>
			  <a href="logins.php" class="waves-effect waves-light blue btn"><i class="material-icons left">done_all</i>LOGINS</a>
			</p>  
            

      <div class="card-panel indigo lighten-5">
          <h5>Dados de contato</h5>
          <i>Mantenha seus dados de contato atualizados</i>
          <p><?php echo $row_AlunoLogado['aluno_endereco']; ?>, <?php echo $row_AlunoLogado['aluno_numero']; ?> - <?php echo $row_AlunoLogado['aluno_bairro']; ?></p>
          <p>
          <?php echo $row_AlunoLogado['aluno_telefone']; ?> 
          <?php echo $row_AlunoLogado['aluno_celular']; ?>
          <?php echo $row_AlunoLogado['aluno_emergencia_tel1']; ?>
          <?php echo $row_AlunoLogado['aluno_emergencia_tel2']; ?>
          <?php echo $row_AlunoLogado['aluno_tel_mae']; ?>
          <?php echo $row_AlunoLogado['aluno_tel_pai']; ?>
          </p>
          <p>
          <?php echo $row_AlunoLogado['aluno_email']; ?>
          </p>
          <i>Caso tenha mudado de endereço ou telefone, clique no botão abaixo</i>
          <hr>
          <a class="btn-small" href="editar_dados.php">MODIFICAR</a>
      </div>            
	  
	  <h4>Calendário de aulas</h4>
	  
	  
	  <div id='calendar'></div>
	  

      
    </div>
    
    <div class="col s12 m4">
	
	

	
    
    <h6><strong>Colegas de sala</strong> <small>(<a href="colegas.php">ver todos</a>)</small></h6>
    
		<p>
		<div class="row">
		<?php do { ?>

                
				<div class="col s4 center-align truncate">

				<a href="#">
                

                
				  <?php if ($row_Colegas['aluno_foto']=="") { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable"><br>
                  <?php } else { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Colegas['aluno_foto']; ?>" width="100%" class="hoverable">
                  <?php } ?>

                 <div style="font-size:10px;">
                  <?php echo current( str_word_count($row_Colegas['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_Colegas['aluno_nome'])); echo $word[count($word)-1]; ?>
                 </div><br>
                 
                 </a>
                 
                 </div>
				 
                  
                  
          <?php } while ($row_Colegas = mysql_fetch_assoc($Colegas)); ?>
          </div>
		  </p>
         
          <br>
   
    </div>
    
    
  </div>
  
  

  
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.dropdown-trigger').dropdown();
		});
	</script>
	
<?php if (isset($_GET["bemvindo"])) {

if ($novasAulas > 0) {

	?>
	 <!-- Modal Structure -->
  <div id="modalBemVindo" class="modal">
    <div class="modal-content">
      <h4>Olá, <?php $nome = explode(" ",$row_AlunoLogado['aluno_nome']); echo $nome[0]; ?>! <i class="material-icons blue-text darken-2">sentiment_very_satisfied</i></h4>
      <p>Seja bem-vindo(a) novamente!</p>
	  
	  <blockquote class="flow-text red lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny pulse red lighten-2"><i class="tiny material-icons">add_alert</i></a> 
	  Você tem <strong><?php echo $novasAulas; ?></strong> aula(s) pendente(s) <a href="aulasNovas.php">Ver</a>
	  </blockquote>
	  
      <p>Faça todas aulas com muita calma e atenção e não deixe acumular.</p>
      <p>Boa sorte!</p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a>
    </div>
  </div>
<script type="text/javascript">
$(document).ready(function(){
    $('#modalBemVindo').modal({
		dismissible: false
	});
    $('#modalBemVindo').modal('open');
  });
</script>              
<?php } } ?>
	
</body>
</html>
<?php


mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);

mysql_free_result($Colegas);

mysql_free_result($Conteudo);

mysql_free_result($AulasVisualizadas);

mysql_free_result($Visualizou);

mysql_free_result($Atividade);

?>
