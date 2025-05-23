<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('fnc/idade.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
//ini_set(allow_url_fopen, 'On'); //pode ser assim 
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
	
  $logoutGoTo = "../../index.php?saiu=true";
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
$MM_authorizedUsers = "1,2,99";
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

$MM_restrictGoTo = "../../index.php?err=true";
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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FaltasAulas = "SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada FROM smc_faltas_alunos";
$FaltasAulas = mysql_query($query_FaltasAulas, $SmecelNovo) or die(mysql_error());
$row_FaltasAulas = mysql_fetch_assoc($FaltasAulas);
$totalRows_FaltasAulas = mysql_num_rows($FaltasAulas);

$colname_Matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_Matricula = $_GET['cmatricula'];
  $cmatricula = GetSQLValueString($colname_Matricula, "text");
}


if (!isset($_GET['cmatricula'])) {
  header("Location: vinculoAlunoExibirTurma.php?erro");
  exit;
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_hash, aluno_foto, aluno_filiacao1, aluno_filiacao2,
aluno_endereco, aluno_numero, aluno_bairro, aluno_municipio, aluno_uf, aluno_cep, matriz_id, matriz_criterio_avaliativo, ca_id, ca_questionario_conceitos,
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao,
aluno_telefone, aluno_celular, aluno_email, aluno_emergencia_tel1, aluno_emergencia_tel2,
turma_id, turma_nome, turma_etapa, turma_matriz_id, etapa_id, etapa_id_filtro,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO(A)'
WHEN 2 THEN '<span class=\"ls-color-danger\">TRANSFERIDO(A)</span>'
WHEN 3 THEN '<span class=\"ls-color-danger\">DESISTENTE</span>'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome,
CASE vinculo_aluno_transporte
WHEN 'S' THEN 'UTILIZA'
WHEN 'N' THEN 'NÃO UTILIZA'
END AS vinculo_aluno_transporte_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id  
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
WHERE vinculo_aluno_hash = $cmatricula AND vinculo_aluno_id_escola = $row_EscolaLogada[escola_id]";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

if ($totalRows_Matricula == 0) { 
header("Location: vinculoAlunoExibirTurma.php?erro");
    exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, 
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
END AS ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
COUNT(ocorrencia_tipo) AS ocorrencia_total 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = $row_Matricula[vinculo_aluno_id_aluno] AND ocorrencia_ano_letivo = $row_AnoLetivo[ano_letivo_ano]
GROUP BY ocorrencia_tipo";
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FaltasAulas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
faltas_alunos_data, faltas_alunos_justificada 
FROM smc_faltas_alunos
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]'";
$FaltasAulas = mysql_query($query_FaltasAulas, $SmecelNovo) or die(mysql_error());
$row_FaltasAulas = mysql_fetch_assoc($FaltasAulas);
$totalRows_FaltasAulas = mysql_num_rows($FaltasAulas);

$lugar = $row_Matricula['aluno_endereco']."+".$row_Matricula['aluno_numero']."+".$row_Matricula['aluno_bairro']."+".$row_Matricula['aluno_municipio']."+".$row_Matricula['aluno_uf']."+".$row_Matricula['aluno_cep'];
$lugar = strtolower($lugar);
$lugar = str_replace("/","",$lugar); 
$lugar = str_replace(" ","+",$lugar); 
$maps = "https://www.google.com.br/maps/place/".$lugar;
// https://www.google.com.br/maps/place/Av. Paulista, 1578 - Bela Vista, São Paulo - SP


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosAnteriores = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, 
vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, vinculo_aluno_internet, 
vinculo_aluno_multietapa, vinculo_aluno_rel_aval,
turma_id, turma_nome, 
escola_id, escola_nome 
FROM 
smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE vinculo_aluno_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]'
ORDER BY vinculo_aluno_ano_letivo DESC
";
$VinculosAnteriores = mysql_query($query_VinculosAnteriores, $SmecelNovo) or die(mysql_error());
$row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores);
$totalRows_VinculosAnteriores = mysql_num_rows($VinculosAnteriores);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_LogsAcesso = "
SELECT 
login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip, login_aluno_ano 
FROM smc_login_aluno
WHERE login_aluno_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]'
ORDER BY login_aluno_id DESC
";
$LogsAcesso = mysql_query($query_LogsAcesso, $SmecelNovo) or die(mysql_error());
$row_LogsAcesso = mysql_fetch_assoc($LogsAcesso);
$totalRows_LogsAcesso = mysql_num_rows($LogsAcesso);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once ("menu-top.php"); ?>
<?php include_once ("menu-esc.php"); ?>
<main class="ls-main">
<div class="container-fluid">
 
<h1 class="ls-title-intro ls-ico-home">MATRÍCULA Nº <strong><?php echo $row_Matricula['vinculo_aluno_id']; ?></strong> - Ano Letivo <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></h1>
<!-- CONTEÚDO -->

<div class="row">
<div class="col-sm-12">
    <?php if (isset($_GET["erro"])) { ?>
      <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA. </div>
      <?php } ?>
    <?php if (isset($_GET["dadosEditados"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OS DADOS DO ALUNO FORAM SALVOS COM SUCESSO. </div>
      <?php } ?>
    <?php if (isset($_GET["ocorrenciaRegistrada"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OCORRÊNCIA DO ALUNO REGISTRADO COM SUCESSO. </div>
      <?php } ?>
    <?php if (isset($_GET["boletimcadastrado"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> BOLETIM CADASTRADO COM SUCESSO. </div>
      <?php } ?>
    <?php if (isset($_GET["vinculoEditado"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> VÍNCULO DO ALUNO EDITADO COM SUCESSO. </div>
      <?php } ?>
    <?php if (isset($_GET["excluido"])) { ?>
      <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> VÍNCULO EXCLUIDO COM SUCESSO. </div>
      <?php } ?>
</div>
</div>


<div class="row">  
   <div class="col-sm-12">
  <a href="vinculoAlunoExibirTurma.php" class="ls-btn-primary">Voltar</a>
    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">LISTAGEM</a>
      <ul class="ls-dropdown-nav">
        <li><a href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_Matricula['turma_id']; ?>">Relação de alunos da turma <?php echo $row_Matricula['turma_nome']; ?></a></li>
        <li><a href="vinculoAlunoExibirTurma.php">Relação de turmas da escola</a></li>
      </ul>
    </div>
  <br><br>
  </div>
</div>  
 
 <div class="row">
  <div class="col-sm-12">

    <div class="row">
	
      <div class="ls-box">
        <header class="ls-info-header">
          <h5 class="ls-title-5">
          Aluno(a) <strong><?php echo $row_Matricula['aluno_nome']; ?></strong><br><br>
          Turma: <strong><?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?></strong><br><br>
          Ano Letivo: <strong><?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></strong>
          </h5>
        </header>
        
      <div class="col-md-2 col-sm-12">  
      <?php if ($row_Matricula['aluno_foto'] == "") { ?>
      <img src="../../aluno/fotos/semfoto.jpg" width="100%">
      <?php } else { ?>
      <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" width="100%">
      <?php } ?>
      <br>
      <small><a class="ls-tag" href="celular.php?aluno=<?php echo htmlentities($row_Matricula['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">ALTERAR FOTO</a></small> 
	  </div>	
        
  <div class="col-md-10 col-sm-12">
        
        
<ul class="ls-tabs-nav" id="awesome-dropdown-tab">
  <li class="ls-active"><a data-ls-module="tabs" href="#tab1">MATRÍCULA</a></li>
  <li><a data-ls-module="tabs" href="#tab2">DADOS</a></li>
  <li><a data-ls-module="tabs" href="#tab6">CONTATO</a></li>
  <li><a data-ls-module="tabs" href="#tab3">DECLARAÇÕES</a></li>
  <li><a data-ls-module="tabs" href="#tab4">DADOS DE ACESSO</a></li>
  <li><a data-ls-module="tabs" href="#tab5">VINCULOS ANTERIORES</a></li>
</ul>

<div class="ls-tabs-container" id="awesome-tab-content">
  <div id="tab1" class="ls-tab-content ls-active">
    <p>
    <p><strong>MATRÍCULA:</strong> <?php echo str_pad($row_Matricula['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?> </p>
        <p><strong>TURMA:</strong> <?php echo $row_Matricula['turma_nome']; ?> </p>
        <p><strong>TURNO:</strong> <?php echo $row_Matricula['turma_turno_nome']; ?></p>
        <p><strong>SITUAÇÃO:</strong> <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>
          <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?>
          <span class="ls-background-danger"> - TRANSFERÊNCIA EM <?php echo inverteData($row_Matricula['vinculo_aluno_datatransferencia']); ?></span>
          <?php } ?>
        </p>
        <p><strong>TRANSPORTE ESCOLAR:</strong> <?php echo $row_Matricula['vinculo_aluno_transporte_nome']; ?></p>
        <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" class="ls-ico-pencil2" href="vinculoAlunoEditar.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR MATRÍCULA</a>
    </p>
  </div>
  <div id="tab2" class="ls-tab-content">
    <p>
    
        <p><strong>NOME:</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
        <p><strong>NASCIMENTO:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?> (<?php echo idade($row_Matricula['aluno_nascimento']); ?> anos)</p>
        <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao1']; ?></p>
        <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao2']; ?></p>
        <p>
          <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>
        
        <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR DADOS</a> 
        </p>
    
    </p>
  </div>
  
  <div id="tab6" class="ls-tab-content">
    <p>
    
        <p><strong>ENDEREÇO:</strong> <?php echo $row_Matricula['aluno_endereco']; ?>, <?php echo $row_Matricula['aluno_numero']; ?> - <?php echo $row_Matricula['aluno_bairro']; ?> (<?php echo $row_Matricula['aluno_localizacao']; ?>)</p>
        <p><strong>E-MAIL:</strong> <?php echo $row_Matricula['aluno_email']; ?></p>
        <p><strong>TELEFONE(S):</strong> <br> <?php echo $row_Matricula['aluno_telefone']; ?> <br> <?php echo $row_Matricula['aluno_celular']; ?> <br> <?php echo $row_Matricula['aluno_emergencia_tel1']; ?> <br> <?php echo $row_Matricula['aluno_emergencia_tel2']; ?> <br></p>
        <p>
          <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>
        
        <a class="ls-ico-pencil ls-btn-primary ls-btn-xs" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR DADOS</a> 
        </p>
    
    </p>
  </div>

  <div id="tab3" class="ls-tab-content">

    
    
    
  <div class="ls-tabs-btn">  
  <ul class="ls-tabs-btn-nav">
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha de Matrícula</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="fichaIndividualAluno.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha Individual</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Comprovante de Matrícula</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de Matrícula</a></li>
    
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_parecer_aluno.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Parecer do aluno</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="imprimir/print_bolsa_familia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Dec. Bolsa Família</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="imprimir/print_bolsa_familia_faltas.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Dec. Bolsa Família c/faltas</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transferência (aprovado)</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_desistente.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Transferência (desistente)</a></li>
    
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_escola_publica.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração / Escola Pública</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Conclusão de Curso</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_conservado.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração aluno conservado</a></li>
    <li class="col-md-3 col-xs-6"><a class="ls-btn" href="print_declaracao_transferencia_em_curso.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração Transf. em Curso</a></li>
    

    
  </ul>
  </div>
    
    
    
    
    
    
  </div>
  <div id="tab4" class="ls-tab-content">
    <p>
    
            
            
            <div class="ls-box">
            <h3 class="ls-title-5"><strong>Dados de acesso ao painel do aluno</strong></h3>
              Nascimento: <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong><br>
              Código: <strong><?php echo str_pad($row_Matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
              Senha: <strong><?php echo substr($row_Matricula['aluno_hash'],0,5); ?></strong> 
            </div>
            
            <p>Total de acessos: <strong><?php echo $totalRows_LogsAcesso; ?></strong></p>
            <p>Último Acesso: <strong><?php echo date("d/m/Y à\s H:i", strtotime($row_LogsAcesso['login_aluno_data_hora'])); ?></strong></p>

    
    </p>
  </div>
  <div id="tab5" class="ls-tab-content">
    <p>
    
            <h3 class="ls-title-5"><strong>Vínculos anteriores</strong></h3>
            <table class="ls-table ls-sm-space" width="100%">
            <thead>
            	<tr>
                	<th width="100" class="ls-txt-center">MATRÍCULA</th>
                	<th width="100" class="ls-txt-center">ANO</th>
                	<th width="200" class="ls-txt-center">TURMA</th>
                	<th class="ls-txt-center">ESCOLA</th>
                	<th width="100"></th>
                </tr>
            </thead>
            
            <tbody>
            <?php do { ?>
            <tr>
					<td class="ls-txt-center"><?php echo str_pad($row_VinculosAnteriores['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></td>
                    <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?></td>
                    <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['turma_nome']; ?></td>
                    <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_nome']; ?></td>
                    <td class="ls-txt-center"><?php if ($row_VinculosAnteriores['escola_id'] == $row_Matricula['vinculo_aluno_id_escola']) { ?><a href="matriculaExibe.php?cmatricula=<?php echo $row_VinculosAnteriores['vinculo_aluno_hash']; ?>">VER</a><?php } ?></td>
              
              </tr>
			  <?php } while ($row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores)); ?>
              </tbody>
              </table>
</p>
  </div>
</div>
                


        </div>
        
        </div>

        
      <div class="ls-box ls-board-box">
        <div id="sending-stats" class="row ls-clearfix">
          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4 <?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>">OCORRÊNCIAS</h6>
              </div>
              <div class="ls-box-body"> <span class="ls-board-data"> <strong class="<?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>"><?php echo $totalRows_Ocorrencia; ?> <small class="<?php if ($totalRows_Ocorrencia > 0) { echo "ls-color-danger"; } ?>">ocorrência(s)</small></strong> </span> </div>
              <div class="ls-box-footer"> <a href="ocorrenciaExibe.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4 <?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>">FALTAS</h6>
              </div>
              <div class="ls-box-body"> <span class="ls-board-data"> <strong class="<?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>"><?php echo $totalRows_FaltasAulas ?> <small class="<?php if ($totalRows_FaltasAulas > 0) { echo "ls-color-danger"; } ?>">aulas no Ano Letivo</small></strong> </span> </div>
              <div class="ls-box-footer"> <a href="faltasMostrar.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">RENDIMENTO</h6>
              </div>
              <?php if ($row_Matricula['vinculo_aluno_boletim']==0) { ?>
              <div class="ls-box-body"> <span class="ls-board-data ls-transparent-25"> <strong><span class="ls-ico-cancel-circle"></span> <small>boletim não gerado</small></strong> </span> </div>
              <div class="ls-box-footer">
                <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                <a href="boletimCadastrarConceitos.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar qustionário - EI</a>
				<?php } else { ?>
                
                <?php if ($row_Matricula['ca_questionario_conceitos']=="S") { ?>
                
                <a href="boletimCadastrarConceitosEf.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar questionário - EF</a>
                
                <?php } else { ?>
                
                <a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Gerar boletim</a>
                
                <?php } ?>
                
                
				<?php } ?>
              </div>
              <?php } ?>
              <?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?>
              
              
              <?php if ($row_Matricula['etapa_id_filtro'] == 1) { ?>
                <div class="ls-box-body"> <span class="ls-board-data"> <strong><span class="ls-ico-checkmark-circle ls-color-success"></span> <small class="ls-color-success">relatório gerado</small></strong> </span> </div>
              <div class="ls-box-footer"> 
              <a href="conceitoVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> 
				<?php } else { ?>
                <div class="ls-box-body"> <span class="ls-board-data"> <strong><span class="ls-ico-checkmark-circle ls-color-success"></span> <small class="ls-color-success">boletim gerado</small></strong> </span> </div>
              <div class="ls-box-footer">
               
              
              
              
              
              
              <?php if ($row_Matricula['ca_questionario_conceitos']=="S") { ?>
                
                <a href="conceitoEfVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn-primary ls-btn-xs">Visualizar Questionário - EF</a> 
                
                <?php } else { ?>
                
                <a href="boletimVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> 
                
                <?php } ?>
              
              
				
				
				
				<?php } ?>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      

      
	  
      <hr>
    </div>
  </div>
  
  <!-- CONTEÚDO --> 
</div>
</div>
</div>

</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FaltasAulas);

mysql_free_result($LogsAcesso);

mysql_free_result($VinculosAnteriores);

mysql_free_result($Ocorrencia);

mysql_free_result($Matricula);
?>
