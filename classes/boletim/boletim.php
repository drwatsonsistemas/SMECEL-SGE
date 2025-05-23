<?php require_once('../Connections/SmecelNovo.php'); ?>
<?php include('../sistema/escola/fnc/inverteData.php'); ?>
<?php include('../sistema/escola/fnc/notas.php'); ?>



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



$colname_Boletim = "-1";
if (isset($_GET['c'])) {
  $colname_Boletim = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Boletim = sprintf("SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, aluno_id, aluno_nome, aluno_nascimento, turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_verificacao = %s", GetSQLValueString($colname_Boletim, "text"));
$Boletim = mysql_query($query_Boletim, $SmecelNovo) or die(mysql_error());
$row_Boletim = mysql_fetch_assoc($Boletim);
$totalRows_Boletim = mysql_num_rows($Boletim);

if($totalRows_Boletim=="") {
	
	header("Location:index.php?erro");
	
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, 
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
$query_EscolaLogada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_cnpj, escola_inep FROM smc_escola WHERE escola_id = '$row_Boletim[vinculo_aluno_id_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
?>
<!DOCTYPE html>
<html class="ls-theme-green">
<head>
<title>Boletim do(a) aluno(a) <?php echo $row_Boletim['aluno_nome']; ?>. Emitido em <?php echo date('d/m/Y'); ?></title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Boletim do(a) aluno(a) <?php echo $row_Boletim['aluno_nome']; ?>. Emitido em <?php echo date('d/m/Y'); ?>.">
<link href="http://assets.locaweb.com.br/locastyle/3.8.4/stylesheets/locastyle.css" rel="stylesheet" type="text/css">
<link rel="icon" sizes="192x192" href="http://gogreenengenharia.com.br/wp-content/uploads/2015/04/icone-educacao.png">
<link rel="apple-touch-icon" href="http://gogreenengenharia.com.br/wp-content/uploads/2015/04/icone-educacao.png">
<style>
table.bordasimples {
	border-collapse: collapse;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:5px 2px;
	}
table.bordasimples {
	font-size:20px;
}
</style>
</head>
<body>

<main class="">
  <div class="container-fluid">
    <!-- CONTEÚDO -->
	<BR>
	<div class="ls-box">
  
	<table style="font-size:10px;" width="100%">
	  <tr>
		<td style="padding:3px 0;">Escola: <?php echo $row_EscolaLogada['escola_nome']; ?></td>
		<td>Endereço: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?></td>
		<td>ITAGIMIRIM-BA CEP:<?php echo $row_EscolaLogada['escola_cep']; ?></td>
	  </tr>
	  <tr>
		<td style="padding:3px 0;">CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?></td>
		<td>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?></td>
		<td>E-MAIL: <?php echo $row_EscolaLogada['escola_email']; ?> TEL: <?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> </td>
	  </tr>
	  <tr>
		<td style="padding:3px 0;">Aluno(a): <strong><?php echo $row_Boletim['aluno_nome']; ?></strong></td>
		<td>Nascimento: <strong><?php echo inverteData($row_Boletim['aluno_nascimento']); ?></strong></td>
		<td>Turma: <strong><?php echo $row_Boletim['turma_nome']; ?></strong></td>
	  </tr>
	</table>
	
	</div>
	
	
	<h1 class="ls-ico-text ls-txt-center">BOLETIM ESCOLAR <?php echo $row_Boletim['vinculo_aluno_ano_letivo']; ?></h1>
	
	<p><br></p>	

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
          <th style="padding: 7px 0;" class="ls-txt-center">DISCIPLINAS</th>
          <th width="40px" class="ls-txt-center">AV1</th>
          <th width="40px" class="ls-txt-center">AV2</th>
          <th width="40px" class="ls-txt-center">AV3</th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center">AV1</th>
          <th width="40px" class="ls-txt-center">AV2</th>
          <th width="40px" class="ls-txt-center">AV3</th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center">AV1</th>
          <th width="40px" class="ls-txt-center">AV2</th>
          <th width="40px" class="ls-txt-center">AV3</th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center">AV1</th>
          <th width="40px" class="ls-txt-center">AV2</th>
          <th width="40px" class="ls-txt-center">AV3</th>
          <th width="40px" class="ls-txt-center">RU</th>
          <th width="40px" class="ls-txt-center">TP</th>
          <th width="40px" class="ls-txt-center">MC</th>
          <th width="40px" class="ls-txt-center">AF</th>
          <th width="40px" class="ls-txt-center">RF</th>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td style="border-right-width:2px;"><?php echo $row_Disciplinas['disciplina_nome']; ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v1']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v1']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v1']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv1 = mediaUnidade($row_Disciplinas['boletim_1v1'],$row_Disciplinas['boletim_2v1'],$row_Disciplinas['boletim_3v1']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v2']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v2']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v2']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv2 = mediaUnidade($row_Disciplinas['boletim_1v2'],$row_Disciplinas['boletim_2v2'],$row_Disciplinas['boletim_3v2']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v3']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v3']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v3']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv3 = mediaUnidade($row_Disciplinas['boletim_1v3'],$row_Disciplinas['boletim_2v3'],$row_Disciplinas['boletim_3v3']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v4']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v4']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v4']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv4 = mediaUnidade($row_Disciplinas['boletim_1v4'],$row_Disciplinas['boletim_2v4'],$row_Disciplinas['boletim_3v4']); ?></strong></td>
            <td class="ls-txt-center"><strong><?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?></strong></td>
            <td class="ls-txt-center"><strong><?php echo $mc = mediaCurso($tp); ?></strong></td>
            <td class="ls-txt-center"><strong><?php echo $af = avaliacaoFinal($row_Disciplinas['boletim_af']); ?></strong></td>
            <td class="ls-txt-center" style="border-right-width:2px;" width="5px"><strong><?php resultadoFinal($mc,$af); ?></strong></td>
          </tr>
          <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
      </tbody>
    </table>
	<br>
	<table width="100%" class="ls-sm-space bordasimples">
		<tr>
			<td>AF - Avaliação Final</td>
			<td>MC - Média do Curso</td>
			<td>RF - Resultado Final</td>
			<td>RU - Resultado Unidade</td>
			<td>TP - Total de Pontos</td>
		</tr>
	</table>
	<br>
	<p class="ls-txt-center" style="font-size:10px;">Este documento foi emido em <?php echo date('d-m-Y'); ?> às <?php echo date('H:i'); ?>. <br>Para comprovar sua autenticidade, acesse <strong>www.smecel.com.br/boletim</strong> e informe o código <strong><?php echo $row_Boletim['vinculo_aluno_verificacao']; ?></strong></p>
	
    <!-- CONTEÚDO --> 
  </div>
</main>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="http://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php

mysql_free_result($EscolaLogada);

mysql_free_result($Boletim);

mysql_free_result($Disciplinas);
?>
