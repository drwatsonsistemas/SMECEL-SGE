<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
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

$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int)$codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
}

$stCod = "";
if (isset($_GET['st'])) {	
  $stCod = anti_injection($_GET['st']);
  $stCod = (int)$stCod;
}

if(isset($_GET['ano'])){
	$anoLetivo = anti_injection($_GET['ano']);
	$anoLetivo = (int) $anoLetivo;
} else {
	$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
}

	$st = "1";
	$stqry = "AND vinculo_aluno_situacao = $st ";
	if (isset($_GET['st'])) {
	
	if ($_GET['st'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
	$st = anti_injection($_GET['st']);
	$st = (int)$st;
	$stqry = "AND vinculo_aluno_situacao = $st ";
	}
	
				  $nomeFiltro = "Matriculados";
			  if (isset($_GET['st'])) {
					switch ($_GET['st']) {
							case 1:
						$nomeFiltro = "Matriculados";
								break;
							case 2:
						$nomeFiltro = "Transferidos";
								break;
							case 3:
						$nomeFiltro = "Desistentes";
								break;
							case 4:
						$nomeFiltro = "Falecidos";
								break;
							case 5:
						$nomeFiltro = "Outros";
								break;
							default:
							   echo "Matriculados";
					}	
			  }

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_matriz_id,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$anoLetivo' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);


if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
aluno_id, aluno_nome, aluno_nome_social,aluno_foto, turma_id, turma_nome, turma_id_escola, turma_ano_letivo,

		CASE vinculo_aluno_situacao
		WHEN 1 THEN 'MATRICULADO'
		WHEN 2 THEN 'TRANSFERIDO'
		WHEN 3 THEN 'DESISTENTE'
		WHEN 4 THEN 'FALECIDO'
		WHEN 5 THEN 'OUTROS'		
		END AS vinculo_aluno_situacao 
 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = '$row_ExibirTurmas[turma_id]'
WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' AND turma_ano_letivo = '$anoLetivo' AND vinculo_aluno_ano_letivo = '$anoLetivo'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_ExibirTurmas[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

$rec = 1;
if ($row_CriteriosAvaliativos['ca_rec_paralela']=="S") { 
$rec = 2;
}
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
	<title>Alunos por turma | SMECEL - Sistema de Gestão Escolar</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">	<script src="js/locastyle.js"></script>
	<style>
table.bordasimples {
	border-collapse: collapse;
	font-size:7px;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:2px;
	font-size:9px;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:2px;
	font-size:9px;
}
</style>
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
	</head>
	<body onload="alert('Atenção: Configure sua impressora para o tamanho A4 e formato HORIZONTAL');self.print();">
<div class="container-fluid1"> 
      
      <!-- CONTEÚDO -->
      
      <?php if ($totalRows_Alunos > 0) { ?>
      <div class="ls-box1 ls-sm-space" style="page-break-after: always;"> <span class="ls-float-left" style="margin-right:15px;"> <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="75px" /> </span> <strong>
        <h2><?php echo $row_EscolaLogada['escola_nome']; ?> ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h2>
        </strong><br>
    <br>
    <strong>PROFESSOR(A): _______________________________________________________________________ </strong>&nbsp;&nbsp; <strong>COMPONENTE CURRICULAR: ____________________________________________________</strong><br>
    <br>
    <h1 class="ls-txt-center"> <?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno']; ?> - MAPA DE NOTAS</h1>
    <br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
          <thead>
        <tr>
              <th colspan="2"></th>
              <?php $tmu = 0; ?>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
              <th colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+$rec; ?>" class="center"><?php echo $p; ?>º PERÍODO</th>
              <?php } ?>
              <th colspan="4" class="ls-txt-center">RESULTADO</th>
            </tr>
        <tr>
              <th colspan="2" class="ls-txt-center">ALUNO</th>
              <?php $tmu = 0; ?>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
              <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
              <th width="30" class="ls-txt-center"><?php echo $c; ?>ª</th>
              <?php } ?>
              <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?>
              <th class="ls-txt-center" width="30">RP</th>
              <?php } ?>
              <th width="30" class="ls-txt-center">MU</th>
              <?php } ?>
              <th width="30" class="ls-txt-center">TP</th>
              <th width="30" class="ls-txt-center">MC</th>
              <th width="30" class="ls-txt-center">NR</th>
              <th width="30" class="ls-txt-center">RES</th>
            </tr>
      </thead>
          <tbody>
        <?php $num = 1; do { ?>
          <tr>
            <td width="25" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
            <td style="padding:0 5px;"><?php echo current( str_word_count($row_Alunos['aluno_nome_social']!= ""?
            $row_Alunos['aluno_nome_social'] : $row_Alunos["aluno_nome"],2)); ?>
              <?php $word = explode(" ", trim($row_Alunos['aluno_nome'])); echo $word[count($word)-1]; ?>
              <span class="ls-float-right">(<?php echo $row_Alunos['vinculo_aluno_situacao']; ?>)</span></td>
            <?php $tmu = 0; ?>
            <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
            <?php $ru = 0; ?>
            <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
            <td width="30" class="center"></td>
            <?php } ?>
            <?php if ($row_Criterios['ca_rec_paralela']=="S") { ?>
            <td class="ls-txt-center" width="30"></td>
            <?php } ?>
            <td width="30" class="center"></td>
            <?php } ?>
              </td>
            <td width="30" class="ls-txt-center"></td>
            <td width="30" class="ls-txt-center"></td>
            <td width="30" class="ls-txt-center"></td>
            <td width="30" class="ls-txt-center"></td>
          </tr>
          <?php include_once('relatorios_rodape.php') ?>
          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        <?php for ($nu = $num; $nu <= 40; $nu++) { ?>
        <tr>
              <td width="25" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
              <td style="padding:0 5px;">&nbsp;</td>
              <?php $tmu = 0; ?>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
              <?php $ru = 0; ?>
              <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
              <td width="30" class="center"></td>
              <?php } ?>
              <?php if ($row_Criterios['ca_rec_paralela']=="S") { ?>
              <td class="ls-txt-center" width="30"></td>
              <?php } ?>
              <td width="30" class="center"></td>
              <?php } ?>
                </td>
              <td width="30" class="ls-txt-center"></td>
              <td width="30" class="ls-txt-center"></td>
              <td width="30" class="ls-txt-center"></td>
              <td width="30" class="ls-txt-center"></td>
            </tr>
        <?php } ?>
      </tbody>
        </table>
  </div>
      <?php } else { ?>
      <div class="ls-box ls-sm-space" style="page-break-after: always;">
    <p class="ls-txt-center"><small><i>Nenhum aluno vinculado na turma.</i></small></p>
  </div>
      <?php } ?>
      
      <!-- CONTEÚDO --> 
    </div>


</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirTurmas);
?>
