<?php require_once('../Connections/SmecelNovo.php'); ?>
<?php include('../sistema/escola/fnc/inverteData.php'); ?>
<?php //include('../sistema/escola/fnc/notas.php'); ?>
<?php include('../sistema/funcoes/url_base.php'); ?>




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
$query_Boletim = sprintf("SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
aluno_id, aluno_nome, aluno_nascimento, aluno_hash, aluno_foto, aluno_filiacao1,
turma_id, turma_nome, turma_matriz_id
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
$query_EscolaLogada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_cnpj, escola_inep, escola_logo FROM smc_escola WHERE escola_id = '$row_Boletim[vinculo_aluno_id_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

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

	  include('../sistema/escola/fnc/notas.php');

?>
<!DOCTYPE html>
<html class="ls-theme-green">
<head>
<title>Boletim do(a) aluno(a) <?php echo $row_Boletim['aluno_nome']; ?>. Emitido em <?php echo date('d/m/Y'); ?></title>
<meta name="description" content="Boletim do(a) aluno(a) <?php echo $row_Boletim['aluno_nome']; ?>. Emitido em <?php echo date('d/m/Y'); ?>.">
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>

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
<body onload="self.print();">

<main class="">
  <div class="container-fluid">
    <!-- CONTEÚDO -->
	<BR>
	<p>
		

		<span class="ls-float-left" style="margin-right:20px;">
		<img src="<?php echo URL_BASE.'img/logo/' ?><?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /></span>
		<?php echo $row_EscolaLogada['escola_nome']; ?><br>
		<small><?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> - CEP <?php echo $row_EscolaLogada['escola_cep']; ?></small>
		<small>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> - INEP: <?php echo $row_EscolaLogada['escola_inep']; ?></small><br>
		<small>E-MAIL: <?php echo $row_EscolaLogada['escola_email']; ?> TEL: <?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?></small>
		<hr>
		
		<div class="ls-box">
		
		<span class="ls-float-right" style="margin-left:20px;">
		<?php if($row_Boletim['aluno_foto']=="") { ?>
			<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
			<?php } else { ?>
			<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Boletim['aluno_foto']; ?>" style="margin:1mm;width:15mm;">
		<?php } ?>
		</span>
		
		
		<small>
		Aluno(a): <strong><?php echo $row_Boletim['aluno_nome']; ?></strong><br>
		Nascimento: <strong><?php echo inverteData($row_Boletim['aluno_nascimento']); ?></strong><br>
		Filiação: <strong><?php echo $row_Boletim['aluno_filiacao1']; ?></strong><br>
		Turma: <strong><?php echo $row_Boletim['turma_nome']; ?></strong>
		</small>
		</div>
</p>

	
	
	
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
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v1'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv1 = mediaUnidade($row_Disciplinas['boletim_1v1'],$row_Disciplinas['boletim_2v1'],$row_Disciplinas['boletim_3v1'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v2'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv2 = mediaUnidade($row_Disciplinas['boletim_1v2'],$row_Disciplinas['boletim_2v2'],$row_Disciplinas['boletim_3v2'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v3'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv3 = mediaUnidade($row_Disciplinas['boletim_1v3'],$row_Disciplinas['boletim_2v3'],$row_Disciplinas['boletim_3v3'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?></strong></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_1v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_2v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center"><?php exibeTraco($row_Disciplinas['boletim_3v4'],$row_CriteriosAvaliativos['ca_nota_min_av']); ?></td>
            <td class="ls-txt-center" style="border-right-width:2px;"><strong><?php $mv4 = mediaUnidade($row_Disciplinas['boletim_1v4'],$row_Disciplinas['boletim_2v4'],$row_Disciplinas['boletim_3v4'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?></strong></td>
            <td class="ls-txt-center"><strong><?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?></strong></td>
            <td class="ls-txt-center"><strong><?php echo $mc = mediaCurso($tp, $row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?></strong></td>
            <td class="ls-txt-center"><strong><?php echo $af = avaliacaoFinal($row_Disciplinas['boletim_af'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']); ?></strong></td>
            <td class="ls-txt-center" style="border-right-width:2px;" width="5px"><strong><?php echo $rf = resultadoFinal($mc,$af,$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']); ?><?php if($row_Disciplinas['boletim_conselho']=="1") { echo "*"; } ?></strong></td>
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
	APR = Aluno foi aprovado;<br>
			APR* = Um asterisco (*) significa que o aluno foi aprovado na disciplina pelo Conselho de Classe;<br>
			CON = Aluno foi conservado;<br>
			<br>
	
<p class="ls-box">
					<small>
					Instruções para acesso ao painel do aluno:<br>
	
					<strong>DADOS DE ACESSO</strong><br>
					Nascimento: <strong><?php echo inverteData($row_Boletim['aluno_nascimento']); ?></strong> - 
					Código de acesso: <strong><?php echo str_pad($row_Boletim['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong> - 
					Senha de acesso: <strong><?php echo substr($row_Boletim['aluno_hash'],0,5); ?></strong><br>
					</small>
					Acesse o site www.smecel.com.br, clique em "Área do Aluno" e informe os dados acima
					</p>
	
	
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
