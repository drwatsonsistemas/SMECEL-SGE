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
aluno_endereco, aluno_numero, aluno_bairro, aluno_municipio, aluno_uf, aluno_cep,
turma_id, turma_nome, turma_etapa, etapa_id, etapa_id_filtro,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
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
<main class="ls-main ">
<div class="container-fluid">
 
<h1 class="ls-title-intro ls-ico-home">MATRÍCULA Nº <strong><?php echo $row_Matricula['vinculo_aluno_id']; ?></strong> - Ano Letivo <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></h1>
<!-- CONTEÚDO -->

<div class="row">
  <div class="col-md-12">
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
  <div class="col-sm-12"> <a href="vinculoAlunoExibirTurma.php" class="ls-btn-primary">Voltar</a>
    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">LISTAGEM</a>
      <ul class="ls-dropdown-nav">
        <li><a href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_Matricula['turma_id']; ?>">Relação de alunos da turma <?php echo $row_Matricula['turma_nome']; ?></a></li>
        <li><a href="vinculoAlunoExibirTurma.php">Relação de turmas da escola</a></li>
      </ul>
    </div>
  </div>
  <hr>
  <div class="col-md-2 col-sm-12 ls-txt-center">
    <div class="">
      <?php if ($row_Matricula['aluno_foto'] == "") { ?>
      <img src="../../aluno/fotos/semfoto.jpg" width="100%">
      <?php } else { ?>
      <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" width="100%">
      <?php } ?>
      <br>
      <small><a class="" href="celular.php?aluno=<?php echo htmlentities($row_Matricula['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">ALTERAR FOTO</a></small> </div>
  </div>
  <div class="col-md-10 col-sm-12">
    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn">CERTIFICADOS E DECLARAÇÕES</a>
      <ul class="ls-dropdown-nav">
        <li><a class="ls-ico-windows" href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Ficha de Matrícula do Aluno</a></li>
        <li><a class="ls-ico-insert-template" href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Comprovante de Matrícula</a></li>
        <li><a class="ls-ico-insert-template" href="print_declaracao_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de Matrícula</a></li>
        <li><a class="ls-ico-insert-template" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de transferência (aprovado)</a></li>
        <li><a class="ls-ico-insert-template" href="print_declaracao_transferencia_desistente.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de transferência (desistente)</a></li>
        <li><a class="ls-ico-envelop" href="print_declaracao_escola_publica.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de estudo em Escola Pública</a></li>
        <li><a class="ls-ico-envelop" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Declaração de Conclusão de Curso</a></li>
        <li><a class="ls-ico-envelop" href="print_declaracao_transferencia_conservado.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank"> Emitir Declaração de aluno conservado</a></li>
        <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?>
        <li><a class="ls-ico-export" href="print_declaracao_transferencia_em_curso.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Declaração de Transferência em Curso</a></li>
        <?php } ?>
      </ul>
    </div>
    <br>
    <br>
    <div class="row">
      <div class="ls-box">
        <header class="ls-info-header">
          <h5 class="ls-title-5">Dados do(a) aluno(a)</h5>
        </header>
        <p><strong>NOME:</strong> <?php echo $row_Matricula['aluno_nome']; ?></p>
        <p><strong>NASCIMENTO:</strong> <?php echo inverteData($row_Matricula['aluno_nascimento']); ?> (<?php echo idade($row_Matricula['aluno_nascimento']); ?> anos)</p>
        <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao1']; ?></p>
        <p><strong>FILIAÇÃO:</strong> <?php echo $row_Matricula['aluno_filiacao2']; ?></p>
        <p>
          <?php //echo "<strong>ENDEREÇO:</strong> <a href=\"{$maps}\" target=\"_blank\">VER NO MAPA</a>"; ?>
        </p>
        
        <!--
          https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo $row_Matricula['aluno_endereco']; ?>, <?php echo $row_Matricula['aluno_numero']; ?>, <?php echo $row_Matricula['aluno_bairro']; ?>, <?php echo $row_Matricula['aluno_uf']; ?>, <?php echo $row_Matricula['aluno_municipio']; ?>, <?php echo $row_Matricula['aluno_cep']; ?>&key=AIzaSyBtM38OqsFbYPjwQEcWkWW1T6ed6heKD4Y
          --> 
        
        <a class="ls-ico-pencil ls-btn-primary ls-float-right ls-btn-xs" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR DADOS</a> </div>
      <div class="ls-box">
        <header class="ls-info-header">
          <h5 class="ls-title-5">Informações da matrícula</h5>
        </header>
        <p><strong>MATRÍCULA:</strong> <?php echo $row_Matricula['vinculo_aluno_id']; ?> </p>
        <p><strong>TURMA:</strong> <?php echo $row_Matricula['turma_nome']; ?> </p>
        <p><strong>TURNO:</strong> <?php echo $row_Matricula['turma_turno_nome']; ?></p>
        <p><strong>SITUAÇÃO:</strong> <?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?>
          <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?>
          <span class="ls-background-danger"> - TRANSFERÊNCIA EM <?php echo inverteData($row_Matricula['vinculo_aluno_datatransferencia']); ?></span>
          <?php } ?>
        </p>
        <p><strong>TRANSPORTE ESCOLAR:</strong> <?php echo $row_Matricula['vinculo_aluno_transporte_nome']; ?></p>
        <a class="ls-ico-pencil ls-btn-primary ls-float-right ls-btn-xs" class="ls-ico-pencil2" href="vinculoAlunoEditar.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">EDITAR MATRÍCULA</a> </div>
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
                <h6 class="ls-title-4">BOLETIM</h6>
              </div>
              <?php if ($row_Matricula['vinculo_aluno_boletim']==0) { ?>
              <div class="ls-box-body"> <span class="ls-board-data ls-transparent-25"> <strong><span class="ls-ico-cancel-circle"></span> <small>boletim não gerado</small></strong> </span> </div>
              <div class="ls-box-footer">
                <?php if ($row_Matricula['etapa_id_filtro'] == 1) { echo "<p>(Etapa não possui avaliação por nota)</p>"; }  else { ?>
                <a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Gerar</a>
                <?php } ?>
              </div>
              <?php } ?>
              <?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?>
              <div class="ls-box-body"> <span class="ls-board-data"> <strong><span class="ls-ico-checkmark-circle ls-color-success"></span> <small class="ls-color-success">boletim gerado</small></strong> </span> </div>
              <div class="ls-box-footer"> <a href="boletimVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Visualizar</a> </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="ls-box">
        <div class="row">
          <div class="col-md-2 ls-txt-center"> <span class="ls-ico-alone ls-ico-screen"></span> </div>
          <div class="col-md-10">
            <h3 class="ls-title-5"><strong>Dados de acesso ao painel do aluno</strong></h3>
            <p> Data de nascimento: <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong><br>
              Código: <strong><?php echo str_pad($row_Matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
              Senha: <strong><?php echo substr($row_Matricula['aluno_hash'],0,5); ?></strong> </p>
          </div>
        </div>
      </div>
      
	  
      <hr>
    </div>
  </div>
  
  <!-- CONTEÚDO --> 
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

mysql_free_result($Ocorrencia);

mysql_free_result($Matricula);
?>
