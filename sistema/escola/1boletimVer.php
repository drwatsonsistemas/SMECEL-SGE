<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php //include('fnc/notas.php'); ?>
<?php include "fnc/session.php"; ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_boletim_disciplinas (boletim_id_vinculo_aluno, boletim_id_disciplina) VALUES (%s, %s)",
                       GetSQLValueString($_POST['boletim_id_vinculo_aluno'], "int"),
                       GetSQLValueString($_POST['boletim_id_disciplina'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
  
// ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $_POST['usu_id'];
	$esc = $_POST['usu_escola'];
	$detalhes = $_POST['detalhes'];
	date_default_timezone_set('America/Bahia');
	$dat = date('Y-m-d H:i:s');

	$sql = "
	INSERT INTO smc_registros (
	registros_id_escola, 
	registros_id_usuario, 
	registros_tipo, 
	registros_complemento, 
	registros_data_hora
	) VALUES (
	'$esc', 
	'$usu', 
	'14', 
	'($detalhes)', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

  
  

  $insertGoTo = "boletimVer.php?cadastrada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

//include ("fnc/secLogada.php");

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_cnpj, escola_inep FROM smc_escola WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaDisciplinas = "SELECT disciplina_id, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina ORDER BY disciplina_nome ASC";
$ListaDisciplinas = mysql_query($query_ListaDisciplinas, $SmecelNovo) or die(mysql_error());
$row_ListaDisciplinas = mysql_fetch_assoc($ListaDisciplinas);
$totalRows_ListaDisciplinas = mysql_num_rows($ListaDisciplinas);

$colname_Boletim = "-1";
if (isset($_GET['c'])) {
  $colname_Boletim = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Boletim = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
aluno_id, aluno_nome, aluno_nascimento, 
turma_id, turma_nome, turma_matriz_id 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_hash = %s", GetSQLValueString($colname_Boletim, "text"));
$Boletim = mysql_query($query_Boletim, $SmecelNovo) or die(mysql_error());
$row_Boletim = mysql_fetch_assoc($Boletim);
$totalRows_Boletim = mysql_num_rows($Boletim);

if ($totalRows_Boletim == 0) {
	header("Location:index.php");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT 
boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho, 
disciplina_id, disciplina_nome, disciplina_ordem 
FROM smc_boletim_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina
WHERE boletim_id_vinculo_aluno = $row_Boletim[vinculo_aluno_id]
ORDER BY disciplina_ordem ASC
";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

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

	  include('fnc/notas.php');
	  $av1 = "AV1";
	  $av2 = "AV2";
	  $av3 = "AV3";
	  $av1_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av2_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av3_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $cancelaLink = "";

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
<style>
table.bordasimples {
	border-collapse: collapse;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:8px 2px;
	}
table.bordasimples {
	font-size:20px;
}

.preload {
  position: fixed;
  z-index: 99999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0.9;
  background-color: #FFFFFF;
  background-image: url('img/carregando.gif');
  background-size: 408px 272px;
  background-position: center;
  background-repeat: no-repeat;
}


</style>
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
 

	
	<p></p>	
<a href="matriculaExibe.php?cmatricula=<?php echo $colname_Boletim; ?>" class="ls-btn-primary">Voltar</a>
	<p></p>	

	<div class="ls-box">
<table style="font-size:16px;" width="100%">
	  <tr>
		<td style="padding:3px 0;">Aluno(a): <strong><?php echo $row_Boletim['aluno_nome']; ?></strong></td>
		<td>Nascimento: <strong><?php echo inverteData($row_Boletim['aluno_nascimento']); ?></strong></td>
		<td>Turma: <strong><?php echo $row_Boletim['turma_nome']; ?></strong></td>
	  </tr>
	</table>
</div>


	
			  <?php if (isset($_GET["excluido"])) { ?>
                <p><div class="ls-alert-info ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Disciplina excluída com sucesso.
                </div></p>
              <?php } ?>

			  <?php if (isset($_GET["cadastrada"])) { ?>
                <p><div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Disciplina cadastrada com sucesso.
                </div></p>
              <?php } ?>

			  <?php if (isset($_GET["boletimcadastrado"])) { ?>
                <p><div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  Boletim gerado com sucesso.
                </div></p>
              <?php } ?>

	
		<h2 class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR</h2>

	<p><br></p>	
	

  <?php if ($totalRows_Disciplinas > 0) { ?>	
  <table class="ls-sm-space ls-table-striped bordasimples" width="100%">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th colspan="4" class="ls-txt-center">I UNIDADE</th>
          <th colspan="4" class="ls-txt-center">II UNIDADE</th>
          <th colspan="4" class="ls-txt-center">III UNIDADE</th>
          <th colspan="4" class="ls-txt-center">IV UNIDADE</th>
          <th colspan="4" class="ls-txt-center">RESULTADO FINAL</th>
        </tr>
        <tr>
          <th style="padding: 7px 0;" class="ls-txt-center">COMPONENTE CURRICULAR</th>
          <th width="40px" class="ls-txt-center"><?php echo $av1; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av2; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av3; ?></th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center"><?php echo $av1; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av2; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av3; ?></th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center"><?php echo $av1; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av2; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av3; ?></th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center"><?php echo $av1; ?></th>
          <th width="40px" class="ls-txt-center"><?php echo $av2; ?></th>
          <th width="40px" class="ls-txt-center">3ºV</th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center">TP</th>
          <th width="40px" class="ls-txt-center">MC</th>
          <th width="40px" class="ls-txt-center">AF</th>
          <th width="40px" class="ls-txt-center">RF</th>
          <th width="20px" class="ls-txt-center"></th>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td style="border-right-width:2px;"><strong><a class="ls-ico-edit-admin ls-float-right" href="lancarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>"></a> <?php echo $row_Disciplinas['disciplina_nome']; ?></strong></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=1v1" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_1v1']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_1v1']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_1v1']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_1v1']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=2v1" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_2v1']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_2v1']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_2v1']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_2v1']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=3v1" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_3v1']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_3v1']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_3v1']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_3v1']); ?></a></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv1 = mediaUnidade($row_Disciplinas['boletim_1v1'],$row_Disciplinas['boletim_2v1'],$row_Disciplinas['boletim_3v1'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo']);$row_CriteriosAvaliativos['ca_media_min_periodo'] ?></strong></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=1v2" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_1v2']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_1v2']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_1v2']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_1v2']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=2v2" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_2v2']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_2v2']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_2v2']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_2v2']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=3v2" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_3v2']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_3v2']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_3v2']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_3v2']); ?></a></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv2 = mediaUnidade($row_Disciplinas['boletim_1v2'],$row_Disciplinas['boletim_2v2'],$row_Disciplinas['boletim_3v2'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo']); ?></strong></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=1v3" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_1v3']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_1v3']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_1v3']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_1v3']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=2v3" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_2v3']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_2v3']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_2v3']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_2v3']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=3v3" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_3v3']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_3v3']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_3v3']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_3v3']); ?></a></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv3 = mediaUnidade($row_Disciplinas['boletim_1v3'],$row_Disciplinas['boletim_2v3'],$row_Disciplinas['boletim_3v3'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo']); ?></strong></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=1v4" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_1v4']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_1v4']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_1v4']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_1v4']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=2v4" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_2v4']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_2v4']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_2v4']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_2v4']); ?></a></td>
            <td class="ls-txt-center"><a <?php echo $cancelaLink?>href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=3v4" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_3v4']<$row_CriteriosAvaliativos['ca_nota_min_av'])&&($row_Disciplinas['boletim_3v4']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_3v4']>=$row_CriteriosAvaliativos['ca_nota_min_av']) { echo " ls-btn-primary"; } ?>"><?php exibeTracoBloco($row_Disciplinas['boletim_3v4']); ?></a></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv4 = mediaUnidade($row_Disciplinas['boletim_1v4'],$row_Disciplinas['boletim_2v4'],$row_Disciplinas['boletim_3v4'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo']); ?></strong></td>
            <td class="ls-txt-center"><strong><?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?></strong></td>
            <td class="ls-txt-center"><strong><?php echo $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?></strong></td>
            <td class="ls-txt-center"><strong><a href="editarNotas.php?cd=<?php echo $row_Disciplinas['boletim_id']; ?>&c=<?php echo $colname_Boletim; ?>&n=af" class="ls-btn-xs ls-btn-block<?php if (($row_Disciplinas['boletim_af']<6)&&($row_Disciplinas['boletim_af']>0)) { echo " ls-btn-primary-danger"; } ?><?php if ($row_Disciplinas['boletim_af']>=6) { echo " ls-btn-primary"; } ?>"><?php echo $af = avaliacaoFinal($row_Disciplinas['boletim_af']); ?></a></strong></td>
            <td class="ls-txt-center" style="border-right-width:2px;" width="5px"><strong><?php resultadoFinal($mc,$af); ?><?php if($row_Disciplinas['boletim_conselho']=="1") { echo "*"; } ?></strong></td>
            <td class="ls-txt-center" style="border:none; background-color:#FFFFFF;">
			
			
			<a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_Disciplinas['boletim_id']; ?>','<?php echo $colname_Boletim; ?>','<?php echo $row_Disciplinas['disciplina_nome']; ?>')" class="ls-ico-remove" style="text-decoration:none; color: inherit !important;"></a>
			
			</td>
		  </tr>
          <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
      </tbody>
    </table>
	<small><i>*COMPONENTE CURRICULAR em que o aluno foi aprovado pelo Conselho de Classe</i></small>
    <?php } else { ?>
	<p class="ls-alert-info">Nenhuma disciplina cadastrada.</p>
	<?php } ?>
	<br>
	<p class="ls-txt-center">
	<button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-ico-plus ls-btn">Cadastrar disciplina</button>
	<a href="../../boletim/impressaoboletim.php?c=<?php echo $row_Boletim['vinculo_aluno_verificacao']; ?>" target="_blank" class="ls-ico-paint-format ls-btn">Imprimir Boletim</a>
	</p>
	
    


<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR DISCIPLINA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
	  
	  <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">

	  <label class="ls-label col-md-8 col-sm-8">
      <b class="ls-label-text">DISCIPLINA</b>
      <div class="ls-custom-select ls-field-lg">
	  <select name="boletim_id_disciplina" class="ls-field-lg" required>
          <option value="">Escolha...</option>
		<?php do { ?>
          <option value="<?php echo $row_ListaDisciplinas['disciplina_id']?>"><?php echo $row_ListaDisciplinas['disciplina_nome']?></option>
        <?php } while ($row_ListaDisciplinas = mysql_fetch_assoc($ListaDisciplinas)); ?>
 	  </select>
	  </div>
    </label>

	<input type="hidden" name="boletim_id_vinculo_aluno" value="<?php echo $row_Boletim['vinculo_aluno_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
	      	
			<input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="ALUNO(A): <?php echo $row_Boletim['aluno_nome']; ?>, TURMA: <?php echo $row_Boletim['turma_nome']; ?>">

	  
	  </p>
    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn ls-float-right ls-btn-lg" data-dismiss="modal">CANCELAR</button>
      <input type="submit" value="CADASTRAR" class="ls-btn-primary ls-btn-lg">
    </div>
	</form>
  </div>
</div><!-- /.modal -->	
	
	
	
    <p>&nbsp;</p>
<!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 

<script language="Javascript">
	function confirmaExclusao(cb,c,turma) {
     var resposta = confirm("Deseja realmente remover a disciplina "+turma+" deste Boletim?");
     	if (resposta == true) {
     	     window.location.href = "disciplinaBoletimExcluir.php?cb="+cb+"&c="+c+"&turma="+turma;
    	 }
	}
	</script>
	<script language="Javascript">	
 $(document).ready(function(){
          setTimeout('$("#preload").fadeOut(100)', 1500);
      });
</script>

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ListaDisciplinas);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);

//mysql_free_result($Secretaria);

mysql_free_result($Boletim);

mysql_free_result($Disciplinas);
?>