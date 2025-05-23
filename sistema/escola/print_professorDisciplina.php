<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosProfessor = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
func_id, func_nome, funcao_id, funcao_nome, funcao_docencia 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' AND funcao_docencia = 'S'
";
$VinculosProfessor = mysql_query($query_VinculosProfessor, $SmecelNovo) or die(mysql_error());
$row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor);
$totalRows_VinculosProfessor = mysql_num_rows($VinculosProfessor);

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
	<style>
	
	table.bordasimples {border-collapse: collapse;}
	table.bordasimples tr td {border-bottom:1px dotted #000000; padding:5px;}
	
	</style>
</head>
  <body onload="self.print();">


		<!-- CONTEÚDO -->
		
		
<?php do { ?>
<div style="page-break-inside: avoid;">	

  <?php
  	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_CargaHoraria = "
	SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
	ch_lotacao_escola, turma_id, turma_nome, turma_id_escola, turma_ano_letivo, turma_turno, disciplina_id, disciplina_nome,
	CASE ch_lotacao_dia
	WHEN 1 THEN 'SEG'
	WHEN 2 THEN 'TER'
	WHEN 3 THEN 'QUA'
	WHEN 4 THEN 'QUI'
	WHEN 5 THEN 'SEX'
	END AS ch_lotacao_dia_nome,
	CASE ch_lotacao_dia
	WHEN 1 THEN 'ls-bg-white'
	WHEN 2 THEN 'ls-background-info'
	WHEN 3 THEN 'ls-background-success'
	WHEN 4 THEN 'ls-background-warning'
	WHEN 5 THEN 'ls-background-danger'
	END AS ch_lotacao_dia_cor,
	CASE turma_turno
	WHEN 0 THEN 'INT'
	WHEN 1 THEN 'MAT'
	WHEN 2 THEN 'VES'
	WHEN 3 THEN 'NOT'
	END AS turma_turno_nome
	FROM smc_ch_lotacao_professor
	INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
	INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
	WHERE ch_lotacao_professor_id = '$row_VinculosProfessor[func_id]' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
	ORDER BY ch_lotacao_dia, turma_turno, ch_lotacao_aula ASC
	";
	$CargaHoraria = mysql_query($query_CargaHoraria, $SmecelNovo) or die(mysql_error());
	$row_CargaHoraria = mysql_fetch_assoc($CargaHoraria);
	$totalRows_CargaHoraria = mysql_num_rows($CargaHoraria);
  ?>
  
<div class="ls-box ls-board-box">
<header class="ls-info-header">
	<p>DISTRIBUIÇÃO DE CARGA HORÁRIA</p>
    <h1 class="ls-title-2"><?php echo $row_VinculosProfessor['func_nome']; ?></h1>
    <p class="ls-float-right ls-float-none-xs ls-small-info"><?php echo $row_VinculosProfessor['funcao_nome']; ?></p>
  </header>
  
<?php if ($totalRows_CargaHoraria > 0) { // Show if recordset not empty ?>

<table class="ls-sm-space bordasimples" width="100%" style="font-size:9px;">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">DIA</th>
        <th class="ls-txt-center">AULA</th>
        <th class="ls-txt-center">TURMA</th>
        <th class="ls-txt-center">TURNO</th>
        <th class="ls-txt-center">DISCIPLINA</th>
        </tr>
    </thead>
    <tbody>
      <?php 
	  $num = 1;
	  do { ?>
        <tr class="<?php echo $row_CargaHoraria['ch_lotacao_dia_cor']; ?>">
          <td class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
          <td class="ls-txt-center"><?php echo $row_CargaHoraria['ch_lotacao_dia_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_CargaHoraria['ch_lotacao_aula']; ?>ª</td>
          <td class="ls-txt-center"><?php echo $row_CargaHoraria['turma_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_CargaHoraria['turma_turno_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_CargaHoraria['disciplina_nome']; ?></td>
        </tr>
        <?php } while ($row_CargaHoraria = mysql_fetch_assoc($CargaHoraria)); ?>
    </tbody>    
    
  </table>
  <p>AULAS: <?php echo $totalRows_CargaHoraria; ?></p>
  <?php } else { ?>
  <p>NENHUMA AULA VINCULADA PARA ESTE PROFESSOR</p>
  <?php } // Show if recordset not empty ?>
  
  
  
</div>  
</div>
<?php } while ($row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor)); ?>
<!-- CONTEÚDO -->
      

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($CargaHoraria);

mysql_free_result($VinculosProfessor);

mysql_free_result($EscolaLogada);
?>
