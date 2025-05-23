<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/print_exibeHorario.php"; ?>
<?php //include('../../sistema/escola/fnc/notas.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
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

$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, 
aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, 
aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, 
aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, 
aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, 
aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, 
aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, 
aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, 
aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, 
aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, 
aluno_hash, aluno_recebe_bolsa_familia, aluno_foto FROM smc_aluno WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);

if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}

$colname_Matricula = "-1";
if (isset($_GET['hash'])) {
  $colname_Matricula = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma,
 vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
 vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao, 
 vinculo_aluno_datatransferencia, 
 escola_id, escola_nome,
 turma_id, turma_nome 
 FROM smc_vinculo_aluno
 INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
 INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
 WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Horarios = "SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola FROM smc_ch_lotacao_professor";
$Horarios = mysql_query($query_Horarios, $SmecelNovo) or die(mysql_error());
$row_Horarios = mysql_fetch_assoc($Horarios);
$totalRows_Horarios = mysql_num_rows($Horarios);

$colname_Boletim = "-1";
if (isset($_GET['hash'])) {
  $colname_Boletim = $_GET['hash'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Boletim = sprintf("SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, aluno_id, aluno_nome, aluno_nascimento, turma_id, turma_nome, turma_matriz_id
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Boletim, "text"));
$Boletim = mysql_query($query_Boletim, $SmecelNovo) or die(mysql_error());
$row_Boletim = mysql_fetch_assoc($Boletim);
$totalRows_Boletim = mysql_num_rows($Boletim);

if($totalRows_Boletim=="") {
	header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho,
disciplina_id, disciplina_nome 
FROM smc_boletim_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina
WHERE boletim_id_vinculo_aluno = $row_Boletim[vinculo_aluno_id]
ORDER BY disciplina_nome ASC
";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Colegas = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
 vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
 vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
 vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto 
 FROM smc_vinculo_aluno
 INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
 WHERE vinculo_aluno_id_turma = $row_Matricula[vinculo_aluno_id_turma] 
 ORDER BY aluno_nome ASC
 ";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Boletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa, disciplina_id, disciplina_nome,
CASE faltas_alunos_justificada
WHEN 'S' THEN 'SIM'
WHEN 'N' THEN 'NÃO'
END AS faltas_alunos_justificada_nome 
FROM smc_faltas_alunos
INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]'
ORDER BY faltas_alunos_data DESC, faltas_alunos_numero_aula ASC
";
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencias = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
END AS ocorrencia_tipo_nome 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]' AND ocorrencia_id_turma = '$row_Matricula[vinculo_aluno_id_turma]'
ORDER BY ocorrencia_id DESC";
$Ocorrencias = mysql_query($query_Ocorrencias, $SmecelNovo) or die(mysql_error());
$row_Ocorrencias = mysql_fetch_assoc($Ocorrencias);
$totalRows_Ocorrencias = mysql_num_rows($Ocorrencias);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "
SELECT aviso_turma_id, aviso_turma_id_turma, aviso_turma_id_escola, aviso_turma_data, DATE_FORMAT(aviso_turma_data, '%d/%m/%Y') AS aviso_turma_data, aviso_turma_hora, aviso_turma_texto, aviso_turma_ano 
FROM smc_aviso_turma 
WHERE aviso_turma_ano = '$row_Matricula[vinculo_aluno_ano_letivo]' AND aviso_turma_id_escola = '$row_Matricula[vinculo_aluno_id_escola]' AND (aviso_turma_id_turma = '0' OR aviso_turma_id_turma = '$row_Matricula[vinculo_aluno_id_turma]')
ORDER BY aviso_turma_id DESC";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);

	  include('../../sistema/escola/fnc/notas.php');
	  $av1 = "AV1";
	  $av2 = "AV2";
	  $av3 = "AV3";
	  $av1_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av2_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av3_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $cancelaLink = "";
	  
	  
	  
	  
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
<title><?php echo $row_AlunoLogado['aluno_nome']; ?>-<?php echo $row_Matricula['escola_nome']; ?>-<?php echo $row_Matricula['turma_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<style>
table {
	width:100%;
	border-collapse: collapse;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<nav class="blue darken-4" role="navigation">
  <div class="nav-wrapper container"> <a id="logo-container" href="index.php" class="brand-logo"> <i class="material-icons">home</i> SMECEL</a>
    <ul class="right hide-on-med-and-down">
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="dados.php">MEUS DADOS</a></li>
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
    </ul>
    <ul id="nav-mobile" class="sidenav">
      <li><a class="waves-effect waves-light btn-flat modal-trigger" href="dados.php">MEUS DADOS</a></li>
      <li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
    </ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a> </div>
</nav>
<div class="container">
  <br><a href="index.php" class="waves-effect waves-light btn-flat"><i class="material-icons left">navigate_before</i>VOLTAR</a>
  <h3>ANO LETIVO <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></h3>
  <br>
  <ul class="collapsible">
    <li>
      <div class="collapsible-header"><i class="material-icons">filter_drama</i>DADOS</div>
      <div class="collapsible-body"><span>
        <div class="col s12">
          <div class="col s12 card-panel teal lighten-4">
            <table class="">
              <tbody>
                <tr>
                  <td><small>ALUNO</small><br>
                    <?php echo $row_AlunoLogado['aluno_nome']; ?><br>
                    <small>NASCIMENTO</small><br>
                    <?php echo inverteData($row_AlunoLogado['aluno_nascimento']); ?><br>
                    <small>FILIAÇÃO</small><br>
                    <?php echo $row_AlunoLogado['aluno_filiacao1']; ?></td>
                  <td><small>TELEFONE</small><br>
                    <?php echo $row_AlunoLogado['aluno_telefone']; ?><br>
                    <small>CELULAR</small><br>
                    <?php echo $row_AlunoLogado['aluno_celular']; ?><br>
                    <small>E-MAIL</small><br>
                    <?php echo $row_AlunoLogado['aluno_email']; ?></td>
                  <td><small>ENDEREÇO</small><br>
                    <?php echo $row_AlunoLogado['aluno_endereco']; ?>, <?php echo $row_AlunoLogado['aluno_numero']; ?><br>
                    <small>BAIRRO</small><br>
                    <?php echo $row_AlunoLogado['aluno_bairro']; ?><br>
                    <small>CIDADE</small><br>
                    <?php echo $row_AlunoLogado['aluno_municipio']; ?>-<?php echo $row_AlunoLogado['aluno_uf']; ?></td>
                  <td><?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
                    <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="90px">
                    <?php } else { ?>
                    <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="90px">
                    <?php } ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">assignment_ind</i>MATRÍCULA</div>
      <div class="collapsible-body"><span>
        <div class="col s12">
          <div class="col s12 card-panel teal lighten-4">
            <table class="">
              <tbody>
                <tr>
                  <td><small>ESCOLA</small><br>
                    <?php echo $row_Matricula['escola_nome']; ?></td>
                  <td><small>TURMA</small><br>
                    <?php echo $row_Matricula['turma_nome']; ?></td>
                  <td><small>ANO LETIVO</small><br>
                    <?php echo $row_Matricula['vinculo_aluno_ano_letivo']; ?></td>
                </tr>
                <tr>
                  <td><small>DATA DA MATRÍCULA</small><br>
                    <?php echo inverteData($row_Matricula['vinculo_aluno_data']); ?></td>
                  <td><small>BOLETIM</small><br>
                    <?php 
			
			if ($row_Matricula['vinculo_aluno_boletim']==0) {
				echo "<i>Nenhum boletim cadastrado</i>";
				} else { 
					echo "BOLETIM GERADO";
							} ?></td>
                  <td><small>SITUAÇÃO</small><br>
                    <?php echo $row_Matricula['vinculo_aluno_situacao']; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">event_available</i>BOLETIM</div>
      <div class="collapsible-body"><span>
        <div class="col s12">
          <div class="col s12 card-panel teal lighten-4">
            <?php 
			
			if ($row_Matricula['vinculo_aluno_boletim']==0) {
				echo "<blockquote><i>Nenhum boletim cadastrado</i></blockquote>";
				} else { ?>
            <table class="responsive-table striped bordasimples" width="100%">
              <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th colspan="4" class="center-align">I UNIDADE</th>
                  <th colspan="4" class="center-align">II UNIDADE</th>
                  <th colspan="4" class="center-align">III UNIDADE</th>
                  <th colspan="4" class="center-align">IV UNIDADE</th>
                  <th colspan="4" class="center-align">RESULTADO FINAL</th>
                </tr>
                <tr>
                  <th style="padding: 7px 0;" class="center-align">DISCIPLINAS</th>
                  <th width="40px" class="center-align">AV1</th>
                  <th width="40px" class="center-align">AV2</th>
                  <th width="40px" class="center-align">AV3</th>
                  <th width="40px" class="center-align">RU</th>
                  <th width="40px" class="center-align">AV1</th>
                  <th width="40px" class="center-align">AV2</th>
                  <th width="40px" class="center-align">AV3</th>
                  <th width="40px" class="center-align">RU</th>
                  <th width="40px" class="center-align">AV1</th>
                  <th width="40px" class="center-align">AV2</th>
                  <th width="40px" class="center-align">AV3</th>
                  <th width="40px" class="center-align">RU</th>
                  <th width="40px" class="center-align">AV1</th>
                  <th width="40px" class="center-align">AV2</th>
                  <th width="40px" class="center-align">AV3</th>
                  <th width="40px" class="center-align">RU</th>
                  <th width="40px" class="center-align">TP</th>
                  <th width="40px" class="center-align">MC</th>
                  <th width="40px" class="center-align">AF</th>
                  <th width="40px" class="center-align">RF</th>
                </tr>
              </thead>
              <tbody>
                <?php do { ?>
                  <tr>
                    <td style="border-right-width:2px;"><?php echo $row_Disciplinas['disciplina_nome']; ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_1v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_2v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_3v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align" style="border-right-width:2px;"><strong>
                      <?php $mv1 = mediaUnidade($row_Disciplinas['boletim_1v1'],$row_Disciplinas['boletim_2v1'],$row_Disciplinas['boletim_3v1'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
                      </strong></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_1v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_2v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_3v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align" style="border-right-width:2px;"><strong>
                      <?php $mv2 = mediaUnidade($row_Disciplinas['boletim_1v2'],$row_Disciplinas['boletim_2v2'],$row_Disciplinas['boletim_3v2'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
                      </strong></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_1v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_2v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_3v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align" style="border-right-width:2px;"><strong>
                      <?php $mv3 = mediaUnidade($row_Disciplinas['boletim_1v3'],$row_Disciplinas['boletim_2v3'],$row_Disciplinas['boletim_3v3'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
                      </strong></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_1v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_2v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align"><?php exibeTraco($row_Disciplinas['boletim_3v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
                    <td class="center-align" style="border-right-width:2px;"><strong>
                      <?php $mv4 = mediaUnidade($row_Disciplinas['boletim_1v4'],$row_Disciplinas['boletim_2v4'],$row_Disciplinas['boletim_3v4'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
                      </strong></td>
                    <td class="center-align"><strong>
                      <?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?>
                      </strong></td>
                    <td class="center-align"><strong><?php echo $mc = mediaCurso($tp, $row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?></strong></td>
                    <td class="center-align"><strong><?php echo $af = avaliacaoFinal($row_Disciplinas['boletim_af'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']); ?></strong></td>
                    <td class="center-align" style="border-right-width:2px;" width="5px"><strong> <?php echo $rf = resultadoFinal($mc,$af,$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?>
                      <?php //echo $rf = resultadoFinal($mc,$af,$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?>
                      <?php if($row_Disciplinas['boletim_conselho']=="1") { echo "*"; } ?>
                      </strong></td>
                  </tr>
                  <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
              </tbody>
            </table>
            <br>
            APR = Aluno foi aprovado;<br>
            APR* = Um asterisco (*) significa que o aluno foi aprovado na disciplina pelo Conselho de Classe;<br>
            CON = Aluno foi conservado;<br>
            <br>
            <br>
            <table width="100%" class="bordasimples">
              <tr>
                <td>AF - Avaliação Final</td>
                <td>MC - Média do Curso</td>
                <td>RF - Resultado Final</td>
                <td>RU - Resultado Unidade</td>
                <td>TP - Total de Pontos</td>
              </tr>
            </table>
            <p class="center"> <a href="impressaoboletim.php?c=<?php echo $row_Matricula['vinculo_aluno_verificacao']; ?>" class="waves-effect waves-light btn" target="_blank">IMPRIMIR BOLETIM</a></p>
            <div class="divider"></div>
            <?php	} ?>
          </div>
        </div>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">access_time</i>HORÁRIOS</div>
      <div class="collapsible-body"><span>
        <table class="responsive-table striped">
          <thead>
            <tr>
              <th class="center-align" width="100px">AULA</th>
              <th class="center-align">SEGUNDA</th>
              <th class="center-align">TERÇA</th>
              <th class="center-align">QUARTA</th>
              <th class="center-align">QUINTA</th>
              <th class="center-align">SEXTA</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="center-align">1ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,1); ?></td>
            </tr>
            <tr>
              <td class="center-align">2ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,2); ?></td>
            </tr>
            <tr>
              <td class="center-align">3ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,3); ?></td>
            </tr>
            <tr>
              <td class="center-align">4ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,4); ?></td>
            </tr>
            <tr>
              <td class="center-align">5ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,5); ?></td>
            </tr>
          </tbody>
        </table>
        <?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_turma_id, func_id, func_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE ch_lotacao_turma_id = $row_Matricula[turma_id]
GROUP BY ch_lotacao_professor_id ASC";
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);
?>
        <?php if ($totalRows_Funcionarios > 0) { ?>
        <small>
        <?php do { ?>
          (<b><?php echo $row_Funcionarios['ch_lotacao_professor_id'] ?></b>) <?php echo $row_Funcionarios['func_nome'] ?>;<br>
          <?php } while ($row_Funcionarios = mysql_fetch_assoc($Funcionarios)); ?>
        </small>
        <?php } ?>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">phone_in_talk</i>AVISOS <span class="new badge" data-badge-caption="AVISO(S)"><?php echo $totalRows_Avisos ?></span></div>
      <div class="collapsible-body"><span>
        <?php if ($totalRows_Avisos > 0) { // Show if recordset not empty ?>
          <ul class="collapsible popout">
            <?php do { ?>
              <li>
                <div class="collapsible-header"><i class="material-icons">add_alert</i><strong>#<?php echo $row_Avisos['aviso_turma_id']; ?></strong>&nbsp;Aviso emitido em <?php echo $row_Avisos['aviso_turma_data']; ?> às <?php echo $row_Avisos['aviso_turma_hora']; ?></div>
                <div class="collapsible-body"><span><?php echo $row_Avisos['aviso_turma_texto']; ?></span></div>
              </li>
              <?php } while ($row_Avisos = mysql_fetch_assoc($Avisos)); ?>
          </ul>
          <?php } else { ?>
          <div class="card-panel">Você não tem nenhum aviso.</div>
          <?php } // Show if recordset not empty ?>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">assignment_late</i>OCORRÊNCIAS<span class="new badge" data-badge-caption="OCORRÊNCIA(S)"><?php echo $totalRows_Ocorrencias ?></span></div>
      <div class="collapsible-body"><span>
        <?php if ($totalRows_Ocorrencias > 0) { ?>
        <?php do { ?>
          <div class="card-panel"> Data da ocorrência: <?php echo inverteData($row_Ocorrencias['ocorrencia_data']); ?> - <?php echo $row_Ocorrencias['ocorrencia_hora']; ?><br>
            Tipo: <?php echo $row_Ocorrencias['ocorrencia_tipo_nome']; ?><br>
            <?php if ($row_Ocorrencias['ocorrencia_tipo']=="2") { ?>
            Data do afastamento: <?php echo inverteData($row_Ocorrencias['ocorrencia_afastamento_de']); ?> até <?php echo $row_Ocorrencias['ocorrencia_afastamento_ate']; ?><br>
            Dias de afastamento: <?php echo inverteData($row_Ocorrencias['ocorrencia_total_dias']); ?><br>
            <?php } ?>
            Descrição: <?php echo $row_Ocorrencias['ocorrencia_descricao']; ?><br>
          </div>
          <?php } while ($row_Ocorrencias = mysql_fetch_assoc($Ocorrencias)); ?>
        <?php } else { ?>
        <div class="card-panel"> Nenhuma ocorrência encontrada </div>
        <?php } ?>
        </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">directions_run</i>FALTAS<span class="new badge" data-badge-caption="FALTA(S)"><?php echo $totalRows_Faltas ?></span></div>
      <div class="collapsible-body"><span>
        <?php if ($totalRows_Faltas > 0) { // Show if recordset not empty ?>
          <table class="centered striped">
            <thead>
              <tr>
                <th>DATA</th>
                <th>AULA</th>
                <th>COMPONENTE CURRICULAR</th>
                <th>JUSTIFICADA</th>
                <th>JUSTIFICATIVA</th>
              </tr>
            </thead>
            <tbody>
              <?php do { ?>
                <tr>
                  <td><?php echo inverteData($row_Faltas['faltas_alunos_data']); ?></td>
                  <td><?php echo $row_Faltas['faltas_alunos_numero_aula']; ?>ª AULA</td>
                  <td><?php echo $row_Faltas['disciplina_nome']; ?></td>
                  <td><?php echo $row_Faltas['faltas_alunos_justificada_nome']; ?></td>
                  <td><?php echo $row_Faltas['faltas_alunos_justificativa']; ?></td>
                </tr>
                <?php } while ($row_Faltas = mysql_fetch_assoc($Faltas)); ?>
            </tbody>
          </table>
          <?php } else { ?>
          <i>Nenhuma falta lançada.</i>
          <?php } // Show if recordset not empty ?>
        </span></div>
    </li>
	    <li>
      <div class="collapsible-header"><i class="material-icons">people</i>COLEGAS DE TURMA</div>
      <div class="collapsible-body"><span>
        
		<?php do { ?>
          <div class="row left">
            <div class="col s12 m12 left">
              <div class="card left">
                <div class="card-image">
                  <?php if ($row_Colegas['aluno_foto']=="") { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" >
                  <?php } else { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Colegas['aluno_foto']; ?>" width="100%">
                  <?php } ?>
                  <span class="card-title"> <?php echo current( str_word_count($row_Colegas['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_Colegas['aluno_nome'])); echo $word[count($word)-1]; ?>
                  </span> </div>
              </div>
            </div>
          </div>
          <?php } while ($row_Colegas = mysql_fetch_assoc($Colegas)); ?>
		  
        <br>
        <?php echo $totalRows_Colegas; ?> alunos na turma. </span></div>
    </li>

  </ul>
</div>
<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.collapsible').collapsible();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($AlunoLogado);

mysql_free_result($Horarios);

mysql_free_result($Faltas);

mysql_free_result($Ocorrencias);

mysql_free_result($Avisos);

mysql_free_result($Colegas);

mysql_free_result($Matricula);
?>
