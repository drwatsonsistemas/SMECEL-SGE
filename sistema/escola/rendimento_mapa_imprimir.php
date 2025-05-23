<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "fnc/calculos.php"; ?>
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

$colname_Disciplina = "-1";
if (isset($_GET['componente'])) {
  $colname_Disciplina = $_GET['componente'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_disciplina ON disciplina_id = $colname_Disciplina
INNER JOIN smc_turma ON turma_id = '$colname_Turma'
WHERE vinculo_aluno_id_turma = '$colname_Turma' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

if ($totalRows_Alunos == 0) {
	//header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turmas = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
turma_id, turma_nome, turma_turno, turma_ano_letivo, disciplina_id, disciplina_nome, func_id, func_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_ch_lotacao_professor 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE ch_lotacao_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_turma_id, ch_lotacao_disciplina_id
ORDER BY turma_turno, turma_nome ASC";
$turmas = mysql_query($query_turmas, $SmecelNovo) or die(mysql_error());
$row_turmas = mysql_fetch_assoc($turmas);
$totalRows_turmas = mysql_num_rows($turmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
WHERE matriz_disciplina_id_matriz = '$row_Turma[turma_matriz_id]'";
$disciplinas = mysql_query($query_disciplinas, $SmecelNovo) or die(mysql_error());
$row_disciplinas = mysql_fetch_assoc($disciplinas);
$totalRows_disciplinas = mysql_num_rows($disciplinas);
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
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" type="text/css" href="css/preloader.css">
            <script src="js/locastyle.js"></script>
                <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
                <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
                <style>
table.bordasimples {
	border-collapse: collapse;
	font-size:10px;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:3px;
	font-size:10px;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:3px;
	font-size:10px;
	font-weight:bold;
	height:25px;
}
</style>
                </head>
                <body onLoad="print()">
                    
		<div class="ls-box1">
		<span class="ls-float-left" style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="80px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
		<?php echo $row_EscolaLogada['escola_nome']; ?><br>
		<small>
		<?php echo $row_EscolaLogada['escola_endereco']; ?>, 
		<?php echo $row_EscolaLogada['escola_num']; ?> - 
		<?php echo $row_EscolaLogada['escola_bairro']; ?> - 
		<?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
		<?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
		</small>
		</div>
                    
                    <?php if ($totalRows_Alunos==0) { ?>
                    Escolha uma turma/componente
                    <?php } else { ?>
                    
                    <p>
                    <br>
                    <h3 class="ls-txt-center">MAPA DE NOTAS</h3>
                    <br>
                    <h3 class="ls-txt-center"><?php echo $row_Alunos['disciplina_nome']; ?> | <?php echo $row_Alunos['turma_nome']; ?></h3>
                    <br>
                    </p>
<table class="bordasimples" width="100%">
                      <thead>
                        <tr class="">
                          <th colspan="2" rowspan="2" class="ls-display-none-xs">IDENTIFICAÇÃO</th>
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                          <th colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+1; ?>" class="ls-txt-center ls-display-none-xs" width="15"><?php echo $p; ?>º PERÍODO</th>
                          <?php } ?>
                          <th colspan="4" class="ls-txt-center ls-display-none-xs">RESULTADO</th>
                        </tr>
                        <tr class="">
                          
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                          <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs"><?php echo $c; ?>ª</th>
                          <?php } ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs">MU</th>
                          <?php } ?>
                          <th width="15" class="ls-txt-center ls-display-none-xs">TP</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">MC</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">NR</th>
                          <th width="15" class="ls-txt-center ls-display-none-xs">RES</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $num = 1; do { ?>
                          <tr>
                            <td width="15" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
                            
                            <td width="150" class="" style="padding:0 5px;"><?php echo $row_Alunos['aluno_nome']; ?></td>
                            <?php $tmu = 0; ?>
                            <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                            <?php $ru = 0; ?>
                            <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                            <td width="15" class="ls-txt-center ls-display-none-xs" style=" <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>"><?php
								mysql_select_db($database_SmecelNovo, $SmecelNovo);
								$query_Notas = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Alunos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
								$Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
								$row_Notas = mysql_fetch_assoc($Notas);
								$totalRows_Notas = mysql_num_rows($Notas);
								$ru = $ru + $row_Notas['nota_valor'];
							  ?>
                              <?php echo $row_Notas['nota_valor']; ?></td>
                            <?php } ?>
                            <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md">
                              <?php $mu = mediaUnidade($ru,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_media_min_periodo'],$row_Criterios['ca_calculo_media_periodo'],$row_Criterios['ca_qtd_av_periodos'],$row_Criterios['ca_digitos']); ?>
                              </span>
                              <?php $tmu = $tmu + $mu; ?></td>
                            <?php } ?>
                              </td>
                            <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md">
                              <?php $tp = totalPontos($tmu,$row_Criterios['ca_digitos']); ?>
                            </span></td>
                            <td width="15" class="ls-txt-center ls-display-none-xs"><span class="ls-text-md">
                              <?php $mc = mediaCurso($tp,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_qtd_periodos'],$row_Criterios['ca_digitos']); ?>
                            </span></td>
                            <td width="15" class="ls-txt-center ls-display-none-xs"><?php 
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Alunos[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
				$notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
				$row_notaAf = mysql_fetch_assoc($notaAf);
				$totalRows_notaAf = mysql_num_rows($notaAf);
  				$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_digitos']);
			?>
                              <span class="ls-text-md"><?php echo $row_notaAf['nota_valor']; ?></span></td>
                            <td width="15" class="ls-txt-center ls-display-none-xs"><?php 
				
					$resultado = resultadoFinal($mc, $af, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_digitos']);				
				
					if ($resultado == "APR") { echo "<small class='light-green lighten-2'>APR</small>"; } else { echo "<small class='pink accent-1'>CON</small>"; }
				
				?></td>
                          </tr>
                          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>

      </tbody>
                    </table>
                    <br>
                    <p> LEGENDA: </p>
                    <p> <strong>MU</strong>: MÉDIA DA UNIDADE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>MC</strong>: MÉDIA DO CURSO - <strong>NR</strong>: NOTA DE RECUPERAÇÃO - <strong>RES</strong>: RESULTADO FINAL </p>
                    <hr>
                        <?php } ?>

                    
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script>
                </body>
                </html>
				<?php
mysql_free_result($Disciplina);

mysql_free_result($disciplinas);

mysql_free_result($turmas);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
