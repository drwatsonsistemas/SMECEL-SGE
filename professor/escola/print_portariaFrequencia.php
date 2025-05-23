<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../funcoes/url_base.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ListarTurmas = mysql_query($query_ListarTurmas, $SmecelNovo) or die(mysql_error());
$row_ListarTurmas = mysql_fetch_assoc($ListarTurmas);
$totalRows_ListarTurmas = mysql_num_rows($ListarTurmas);

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
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}

$hoje = date('Y-m-d');

$dia = date("Y-m-d");
if (isset($_GET['data'])) {
  $dia = anti_injection($_GET['data']);
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
	font-size:10px;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:3px;
	font-size:10px;
}
table.bordasimples tr th {
	border:1px solid #808080;
	padding:3px;
	font-size:11px;
}
</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();">



<div class="ls-txt-center">
  <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
  <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" />
  <?php } else { ?>
  <img src="../../img/brasao_republica.png" alt="" width="60px" />
  <?php } ?>
  <br>
  <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
  <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
  ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
  CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
  <p>
  <br>
  <h4 class="ls-txt-center">FREQUÊNCIA DO DIA <strong><?php echo date("d/m/Y", strtotime($dia)); ?></strong></h4>
  </p>
  <hr>
</div>

<?php 
		$totalAlunosEscola = 0; 
		$totalAlunosEscolaFrequente = 0;
		?>
<?php do { ?>


  <?php 
		
		
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao,
		aluno_id, aluno_cod_inep, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_sexo, aluno_foto
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
  <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
  <?php $contaAlunos = 1; ?>
  
  <table class="ls-sm-space bordasimples" width="100%" style="">
    <thead>
    <tr>
    	<th colspan="3" style="background-color:#F3F3F3;"><h2 class="ls-txt-center"><?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?></h2></th>
    </tr>
      <tr>
        <th width="60">MAT</th>
        <th>ALUNO(A)</th>
        <th width="80">SITUAÇÃO</th>
      </tr>
    </thead>
    <tbody>
      <?php 
	$presentes = 0;
	do { ?>
    <?php //echo $row_ExibirAlunosVinculados['aluno_foto']; ?>
    <?php 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Catraca = "
		SELECT 
		catraca_id, catraca_id_matricula, catraca_data, catraca_hora, catraca_tipo
		FROM smc_catraca 
		WHERE catraca_id_matricula = '$row_ExibirAlunosVinculados[vinculo_aluno_id]' AND catraca_data = '$dia' 
		";
		$Catraca = mysql_query($query_Catraca, $SmecelNovo) or die(mysql_error());
		$row_Catraca = mysql_fetch_assoc($Catraca);
		$totalRows_Catraca = mysql_num_rows($Catraca);
		?>
    <tr>
      <td class="ls-txt-center"><?php echo $row_ExibirAlunosVinculados['vinculo_aluno_id']; ?></td>
      <td><?php echo$row_ExibirAlunosVinculados['aluno_nome']; ?></td>
      <td class="ls-txt-left"><?php if ($totalRows_Catraca > 0) { ?>
	  <?php $presentes++; ?>
        <small class="ls-color-success ls-ico-checkmark-circle ls-ico">PRESENTE</small>
        <?php } else { ?>
        <small class="ls-color-danger ls-ico-cancel-circle ls-ico">AUSENTE</small>
        <?php } ?></td>
    </tr>
    <?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
    <?php mysql_free_result($ExibirAlunosVinculados); ?>
    <tr>
      <td colspan="3" class="ls-txt-center"><small class="ls-ico-checkmark-circle ls-ico ls-color-success">PRESENTE</small>: <strong><?php echo $presentes; ?></strong>&nbsp;&nbsp;&nbsp; <small class="ls-ico-cancel-circle ls-ico ls-color-danger">AUSENTE</small>: <strong><?php echo $totalRows_ExibirAlunosVinculados - $presentes; ?></strong>&nbsp;&nbsp;&nbsp;
        TOTAL: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong></td>
    </tr>
      </tbody>
    
  </table>
  
  
  
  <br>
  
  <?php } else { ?>
  <p class="ls-txt-center"> <small><i>Nenhum aluno vinculado na turma.</i></small> <span class="ls-float-right"><a href="alunoPesquisar.php" class="ls-btn-primary ls-ico-user-add"> Vincular aluno</a></span> </p>
  <?php } ?>
  <?php 
		  $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados;
		  $totalAlunosEscolaFrequente = $totalAlunosEscolaFrequente + $presentes;

		  ?>
  <br>
  <?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>
  
  
<?php if ($codTurma == "") { ?>
<div class="ls-box ls-box-gray">
  <p><small class="ls-ico-checkmark-circle ls-ico ls-color-success">Alunos presentes na escola</small>: <strong><?php echo $totalAlunosEscolaFrequente; ?></strong>&nbsp;&nbsp;&nbsp;
  <small class="ls-ico-cancel-circle ls-ico ls-color-danger">Alunos ausentes na escola</small>: <strong><?php echo $totalAlunosEscola - $totalAlunosEscolaFrequente; ?></strong>&nbsp;&nbsp;&nbsp;
  Alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
</div>
<?php } ?>

<small></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ListarTurmas);

mysql_free_result($ExibirTurmas);

//mysql_free_result($ExibirAlunosVinculados);
?>
