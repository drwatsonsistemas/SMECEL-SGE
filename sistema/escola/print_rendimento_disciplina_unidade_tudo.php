<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "fnc/calculos.php"; ?>
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
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$unidade = "";
if (isset($_GET['unidade'])) {
	
	if ($_GET['unidade'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $unidade = anti_injection($_GET['unidade']);
  $unidade = (int)$unidade;

}

$colname_Turma = "";
if (isset($_GET['turma'])) {
	
	if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $colname_Turma = anti_injection($_GET['turma']);
  $colname_Turma = (int)$colname_Turma;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer 
FROM smc_turma 
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_escola = %s
ORDER BY turma_turno, turma_etapa, turma_nome ASC
", GetSQLValueString($row_EscolaLogada['escola_id'], "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);
if ($totalRows_Turma < 1) {
	header("Location: turmaListar.php?semdados"); 
 	exit;
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
<title><?php echo $row_Turma['turma_nome']; ?>-<?php echo $row_EscolaLogada['escola_nome']; ?>-<?php echo $unidade; ?>Período</title>
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
                
				
				
				<?php do { ?>
<?php

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
	$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
	$row_Matriz = mysql_fetch_assoc($Matriz);
	$totalRows_Matriz = mysql_num_rows($Matriz);
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
	$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
	$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
	$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

	
?>
                  <div class="ls-box" style="page-break-inside: avoid;">
                  
                  <h3 class="ls-txt-center"><?php echo $row_EscolaLogada['escola_nome']; ?></h3>
                  <br>
                  <h2 class="ls-txt-center"><?php echo $row_Turma['turma_nome']; ?></h2>
                  <br>
                  
                  
                  
                  
                  
                  <?php for ($i = 1; $i <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                  <?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriz_disciplinas = "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome, disciplina_nome_abrev FROM smc_matriz_disciplinas INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina WHERE matriz_disciplina_id_matriz = $row_Matriz[matriz_id]";
	$Matriz_disciplinas = mysql_query($query_Matriz_disciplinas, $SmecelNovo) or die(mysql_error());
	$row_Matriz_disciplinas = mysql_fetch_assoc($Matriz_disciplinas);
	$totalRows_Matriz_disciplinas = mysql_num_rows($Matriz_disciplinas);
?>
                  <div class="ls-box"  style="page-break-inside: avoid;">
                  
                  <h3 class="ls-txt-center"><?php echo $i; ?>º PERÍODO</h3>
                  <br><br>
                  <!-- CONTEÚDO -->
                  
                  <div class="row">
                  
                      <?php do { ?>
                        <div class="col-md-4" style="width:20%;">
                        <?php 

	$aprovado = 0;
	$reprovado = 0;
 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Aluno = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = '$row_Turma[turma_id]'";
	$Aluno = mysql_query($query_Aluno, $SmecelNovo) or die(mysql_error());
	$row_Aluno = mysql_fetch_assoc($Aluno);
	$totalRows_Aluno = mysql_num_rows($Aluno);

?>
                        <div class="ls-txt-center">
                            <h5 class="ls-txt-center"><?php echo $row_Matriz_disciplinas['disciplina_nome_abrev']; ?></h5>
                            <div class="ls-display-none" style="display:none;">
                            <?php do { ?>
                              <?php
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Aluno[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Matriz_disciplinas[disciplina_id]' AND nota_periodo = '$i'";
	$Nota = mysql_query($query_Nota, $SmecelNovo) or die(mysql_error());
	$row_Nota = mysql_fetch_assoc($Nota);
	$totalRows_Nota = mysql_num_rows($Nota);	
?>
                              -<?php echo $row_Aluno['vinculo_aluno_id_aluno']; ?><br>
                              <?php $ru = 0; ?>
                              <?php do { ?>
                                --<?php echo $row_Nota['nota_periodo']; ?>(<?php echo $row_Nota['nota_num_avaliacao']; ?>): <?php echo $row_Nota['nota_valor']; ?><br>
                                <?php $ru = $ru + $row_Nota['nota_valor']; ?>
                                <?php } while ($row_Nota = mysql_fetch_assoc($Nota)); ?>
                              ---Média:
                              <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos']); ?>
                              -
                              <?php
			if ($mu >= (int)$row_CriteriosAvaliativos['ca_media_min_periodo']) {
				echo "APROVADO";
				$aprovado++;
				
				} else {
					echo "REPROVADO";
					$reprovado++;
				
					}
			?>
                          <?php } while ($row_Aluno = mysql_fetch_assoc($Aluno)); ?>
                          </div>
                            <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['TIPO', '%'],
          ['Atingiram',     <?php echo $aprovado; ?>],
          ['Não atingiram',      <?php echo $reprovado; ?>]
        ]);

        var options = {
         //title: 'Percentual de rendimento da turma',
		  legend: {
			  position: 'bottom'
			  }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_<?php echo $row_Matriz_disciplinas['disciplina_id']; ?>_<?php echo $row_Turma['turma_id']; ?>_<?php echo $i; ?>'));

        chart.draw(data, options);
      }
    </script>
                            <div id="piechart_<?php echo $row_Matriz_disciplinas['disciplina_id']; ?>_<?php echo $row_Turma['turma_id']; ?>_<?php echo $i; ?>" style="width: 100%; height: 100%; float:"></div>
                            <p class="ls-txt-center"> <span class="ls-ico-shaft-up ls-ico-right ls-color-info"></span>&nbsp; <?php echo $aprovado; ?> &nbsp;&nbsp; <span class="ls-ico-shaft-down ls-ico-right ls-color-danger"></span>&nbsp; <?php echo $reprovado; ?> </p>
                          </div>
                      </div>
                        <?php } while ($row_Matriz_disciplinas = mysql_fetch_assoc($Matriz_disciplinas)); ?>
                    </div>
                    </div>
                  <?php } ?>
                </div>
                <hr>
                  <?php } while ($row_Turma = mysql_fetch_assoc($Turma)); ?>
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script> 
                <script>
                
					$(document).ready(function() {
						
						window.print();
        				return false;					  
					
					});
				
                
                </script>
                </body>
                </html>
				<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turma);

mysql_free_result($Aluno);

mysql_free_result($Nota);

mysql_free_result($EscolaLogada);
?>
				