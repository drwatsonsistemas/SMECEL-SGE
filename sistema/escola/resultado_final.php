<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
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


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {

  if ($_GET['ano'] == "") {
    //echo "TURMA EM BRANCO"; 
    header("Location: resultado_final.php?nada");
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
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
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_resultado_consolidado, turma_tipo_atendimento,
matriz_id, matriz_criterio_avaliativo, ca_id, ca_qtd_periodos, ca_questionario_conceitos,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VESP'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome 
FROM smc_turma
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$anoLetivo'
AND ca_forma_avaliacao='N' AND turma_resultado_consolidado='N' AND turma_tipo_atendimento = 1
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

$query_AlunosAprovadosConselho = "
SELECT 
COUNT(vinculo_aluno_id) AS alunos_aprovados_conselho, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer,vinculo_aluno_resultado_final,turma_id, turma_matriz_id, turma_resultado_consolidado 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND
vinculo_aluno_resultado_final = '1' AND vinculo_aluno_conselho='S'
ORDER BY vinculo_aluno_id_turma ASC";
$AprovadosC = mysql_query($query_AlunosAprovadosConselho, $SmecelNovo) or die(mysql_error());
$row_AprovadosC = mysql_fetch_assoc($AprovadosC);
$totalRows_AprovadosC = mysql_num_rows($AprovadosC);


$query_AlunosMatriculados = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim,vinculo_aluno_situacao, COUNT(vinculo_aluno_situacao) AS aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, vinculo_aluno_conselho_parecer,vinculo_aluno_resultado_final,turma_id, turma_matriz_id,turma_tipo_atendimento,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS aluno_total_descricao_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_ano_letivo = '$anoLetivo'
AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]'
AND turma_tipo_atendimento = 1
GROUP BY vinculo_aluno_situacao";
$Matriculados = mysql_query($query_AlunosMatriculados, $SmecelNovo) or die(mysql_error());
$row_Matriculados = mysql_fetch_assoc($Matriculados);
$totalRows_row_Matriculados = mysql_num_rows($Matriculados);

//APROVADOS E REPROVADOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosAprovados = "
SELECT 
    CASE vinculo_aluno_resultado_final
        WHEN 1 THEN 'APROVADO'
        WHEN 2 THEN 'REPROVADO'
    END AS aluno_total_descricao,
    COUNT(*) AS aluno_total
FROM smc_vinculo_aluno
WHERE vinculo_aluno_boletim = '1'
  AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]'
  AND vinculo_aluno_ano_letivo = '$anoLetivo'
  AND vinculo_aluno_resultado_final IN (1, 2)
  AND vinculo_aluno_situacao = 1
GROUP BY vinculo_aluno_resultado_final;
";
$Aprovados = mysql_query($query_AlunosAprovados, $SmecelNovo) or die(mysql_error());
$row_Aprovados = mysql_fetch_assoc($Aprovados);
$totalRows_Aprovados = mysql_num_rows($Aprovados);


//APROVADOS, REPROVADOS E DESISTENTES
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosAprovadosD = "
SELECT 
vinculo_aluno_id, 
vinculo_aluno_id_aluno, 
vinculo_aluno_id_turma, 
vinculo_aluno_id_escola, 
vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, 
vinculo_aluno_data, 
vinculo_aluno_hash, 
vinculo_aluno_verificacao, 
vinculo_aluno_boletim, 
vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia,
vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, 
vinculo_aluno_vacina_data_retorno, 
vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer,
vinculo_aluno_resultado_final,
turma_id, 
turma_matriz_id,
CASE vinculo_aluno_resultado_final
WHEN 1 THEN 'APROVADO'
WHEN 2 THEN 'REPROVADO'
END AS aluno_total_descricao,
COUNT(*) AS aluno_total,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS aluno_total_descricao_situacao 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' 
AND vinculo_aluno_ano_letivo = '$anoLetivo' 
AND vinculo_aluno_situacao IN (1, 3)
GROUP BY vinculo_aluno_id";
$AprovadosD = mysql_query($query_AlunosAprovadosD, $SmecelNovo) or die(mysql_error());
$row_AprovadosD = mysql_fetch_assoc($AprovadosD);
$totalRows_AprovadosD = mysql_num_rows($AprovadosD);


?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">GRÁFICO DE RESULTADO FINAL</h1>
      <!-- CONTEÚDO -->
      <a href="index.php" class="ls-btn">Voltar</a>
      <div data-ls-module="dropdown" class="ls-dropdown ">
        <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
        <ul class="ls-dropdown-nav">

          <li><a href="resultado_final.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano'];
          ; ?>" target="" title="Diários">ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></a></li>

          <?php do { ?>

            <li><a href="resultado_final.php?ano=<?php echo $row_Ano['ano_letivo_ano']; ?>" target="" title="Diários">ANO
                LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?></a></li>

          <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>

        </ul>

      </div>
      <a href="resultado_rendimento.php" target="_blank" class="ls-btn-primary">RESULTADO DE RENDIMENTO</a>
      <br>
      <hr>
      <?php if ($row_Turmas) { ?>
        
        
        <div class="ls-alert-danger">
          <strong>Atenção! </strong>Existem turmas que ainda não foram consolidadas:<br><br>

          <?php do { ?>
            -<?= $row_Turmas['turma_nome'] ?><br>
          <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
          <br>
          Para consolidar acesse <strong>FECHAMENTO > CONSOLIDAR RESULTADOS FINAIS</strong>
        </div>
        <br>


      <?php } ?>
      <div class="ls-alert-warning">
        <strong>Atenção: </strong>
        A consolidação deve ser realizada apenas após o lançamento de todas as notas e a finalização do Conselho
        Escolar. Certifique-se de que ambos os processos foram concluídos para garantir que os resultados reflitam a
        realidade com precisão.
      </div>


      <p>
        <?php
        if ($totalRows_Aprovados > 0 || $totalRows_row_Matriculados > 0) {
          do {
            echo $row_Aprovados['aluno_total_descricao'] . ": " . $row_Aprovados['aluno_total'] . " | ";
          } while ($row_Aprovados = mysql_fetch_assoc($Aprovados));

          ?>
        </p>
        <p>
          <?php
          do {
            echo $row_Matriculados['aluno_total_descricao_situacao'] . ": " . $row_Matriculados['aluno_situacao'] . " | ";
          } while ($row_Matriculados = mysql_fetch_assoc($Matriculados));

        } else {
          ?>
        <div class="ls-alert-warning">
          Sem dados.
        </div>
        <?php
        }
        ?>
      </p>
      <p>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($Aprovados, 0);
              while ($row_Aprovados = mysql_fetch_assoc($Aprovados)) {
                echo "['{$row_Aprovados['aluno_total_descricao']} ({$row_Aprovados['aluno_total']})', {$row_Aprovados['aluno_total']}],";
              }
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
          }
        </script>

        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($Matriculados, 0);
              // Segundo loop para gerar os dados para o gráfico
              while ($row_Matriculados = mysql_fetch_assoc($Matriculados)) {
                echo "['{$row_Matriculados['aluno_total_descricao_situacao']} ({$row_Matriculados['aluno_situacao']})', {$row_Matriculados['aluno_situacao']}],";
              }
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_matriculados'));

            chart.draw(data, options);
          }
        </script>

        <script type="text/javascript">
          google.charts.load('current', { 'packages': ['corechart'] });
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['SITUAÇÃO', 'TOTAL'],
              <?php
              // Reinicie o ponteiro do resultado da consulta
              mysql_data_seek($AprovadosD, 0);
              // Inicialize variáveis para armazenar os totais
              $aprovados_total = 0;
              $reprovados_total = 0;
              $desistentes_total = 0;
              // Loop para calcular os totais
              while ($row_AprovadosD = mysql_fetch_assoc($AprovadosD)) {
                if ($row_AprovadosD['aluno_total_descricao'] == 'APROVADO') {
                  $aprovados_total += $row_AprovadosD['aluno_total'];
                } elseif ($row_AprovadosD['aluno_total_descricao'] == 'REPROVADO') {
                  $reprovados_total += $row_AprovadosD['aluno_total'];
                } elseif ($row_AprovadosD['aluno_total_descricao_situacao'] == 'DESISTENTES') {
                  $desistentes_total += $row_AprovadosD['aluno_total'];
                }
              }
              // Imprimir os totais no formato adequado para o gráfico
              echo "['APROVADOS ($aprovados_total)', {$aprovados_total}],";
              echo "['REPROVADOS ($reprovados_total)', {$reprovados_total}],";
              echo "['DESISTENTES ($desistentes_total)', {$desistentes_total}],";
              ?>
            ]);

            var options = {
              title: 'GRÁFICO POR SITUAÇÃO'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_matriculados_desistentes'));

            chart.draw(data, options);
          }
        </script>

      <div class="row">

        <div class="col-md-12 col-sm-12">
          <div id="piechart" style="width: 100%; height: 500px;"></div>
        </div>
        <div class="col-md-12 col-sm-12">
          <div id="piechart_matriculados" style="width: 100%; height: 500px;"></div>
        </div>

        <div class="col-md-12 col-sm-12">
          <div id="piechart_matriculados_desistentes" style="width: 100%; height: 500px;"></div>
        </div>

      </div>
      </p>




      <!-- CONTEÚDO -->
    </div>
  </main>
  <aside class="ls-notification">
    <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
      <h3 class="ls-title-2">Notificações</h3>
      <ul>
        <?php include "notificacoes.php"; ?>
      </ul>
    </nav>
    <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
      <h3 class="ls-title-2">Feedback</h3>
      <ul>
        <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
      </ul>
    </nav>
    <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
      <h3 class="ls-title-2">Ajuda</h3>
      <ul>
        <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial
            (Vídeos)</a> </li>
        <li><a href="#">&gt; Guia</a></li>
        <li><a href="#">&gt; Wiki</a></li>
      </ul>
    </nav>
  </aside>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);
mysql_free_result($EscolaLogada);

?>