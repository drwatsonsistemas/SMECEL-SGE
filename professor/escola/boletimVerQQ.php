<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_matricula = "-1";
if (isset($_GET['c'])) {
  $colname_matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = sprintf("SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
  vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim,vinculo_aluno_conselho,vinculo_aluno_conselho_reprovado, 
  vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_reprovado_faltas,
  vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento 
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno
  ON aluno_id = vinculo_aluno_id_aluno
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_matricula, "text"));
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

if ($totalRows_matricula == 0) {
  header("Location:turmaListar.php?nada");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = '$row_matricula[vinculo_aluno_id_turma]'";
$turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($turma);
$totalRows_turma = mysql_num_rows($turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_turma[turma_matriz_id]'";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_turma[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_forma_avaliacao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

$rec = 0;
if ($row_criteriosAvaliativos['ca_rec_paralela'] == "S") {
  $rec = 1;
}


function arredondarNota($nota)
{
  $decimal = round($nota - floor($nota), 2); // Arredonda para 2 casas decimais

  if ($decimal >= 0.75) {
    return ceil($nota);
  } elseif ($decimal >= 0.5 && $decimal < 0.75) {
    return floor($nota) + 0.5;
  } elseif ($decimal >= 0.3 && $decimal < 0.5) {
    return floor($nota) + 0.5;
  } else {
    return floor($nota);
  }
}
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
<style>
  table.bordasimples {
    border-collapse: collapse;
    font-size: 11px;
  }

  table.bordasimples tr td {
    border: 1px solid #808080;
    padding: 3px;
    font-size: 11px;
  }

  table.bordasimples tr th {
    border: 1px solid #808080;
    padding: 3px;
    font-size: 11px;
  }

  .nota-paralela {
    color: #B9A016;
  }

  .nota-paralela-apr {
    color: #2881AC;
  }

  .nota-paralela-rep {
    color: #D75553;
  }

  .nota-apr {
    color: #388f39;
  }
</style>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">LANÇAMENTO DE NOTAS</h1>
      <!-- CONTEÚDO -->

      <div class="ls-box">
        <table style="font-size:14px;" width="100%">
          <tr>
            <td style="padding:3px 0;">Aluno(a): <strong><?php echo $row_matricula['aluno_nome']; ?></strong></td>
            <td>Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong></td>
            <td>Turma: <strong><?php echo $row_turma['turma_nome']; ?></strong></td>
          </tr>
        </table>
      </div>
      <?php if (isset($_GET["boletimcadastrado"])) { ?>
        <p>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Boletim gerado com sucesso. </div>
        </p>
      <?php } ?>
      <?php if ($row_matricula['vinculo_aluno_conselho'] == "S") { ?>
        <br>
        <div class="ls-alert-warning">Aluno APROVADO pelo Conselho de Classe.</div>
      <?php } ?>
      <?php if ($row_matricula['vinculo_aluno_conselho_reprovado'] == "S") { ?>
        <br>
        <div class="ls-alert-danger">Aluno REPROVADO pelo Conselho de Classe.</div>
      <?php } ?>
      <?php if ($row_matricula['vinculo_aluno_reprovado_faltas'] == "S") { ?>
        <br>
        <div class="ls-alert-danger">Aluno REPROVADO por falta.</div>
      <?php } ?>
      <table class="ls-table1 ls-sm-space bordasimples" width="100%">
        <thead>
          <tr height="30">
            <td width="150"></td>
            <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
              <th colspan="2" class="ls-txt-center" width="15"><?php echo $p; ?>º PERÍODO</th>
            <?php } ?>
            <th colspan="3" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
          </tr>
          <tr height="30">
            <th class="ls-txt-center" width="200">COMPONENTES CURRICULARES</th>
            <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
              <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">TT.</th>
              <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">REC.</th>
            <?php } ?>
            <th width="15" style="background-color:#ccde9e" class="ls-txt-center">TP.</th>
            <th width="15" style="background-color:#ccde9e" class="ls-txt-center">RF.</th>
          </tr>
        </thead>
        <tbody>
          <?php do { ?>
            <tr>
              <td width="150"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
              <?php
              $totalTrimestre = 0;
              $totalTrimestreSemRecuperacao = 0;
              $pontuacaoTotalAnoLetivo1 = 0;
              $pontuacaoTotalAnoLetivo2 = 0;
              $pontuacaoTotalAnoLetivo3 = 0;

              for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) {
                // Consultas ao banco de dados para obter as notas qualitativas, quantitativas, paralela e de recuperação
                $query_qualitativo = sprintf(
                  "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='1'",
                  GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                  GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                  GetSQLValueString($p, "int")
                );
                $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                $somaPontuacaoQualitativo = 0;
                while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
                  $somaPontuacaoQualitativo += floatval($row_qualitativo['qq_nota']);
                }

                $query_quantitativo = sprintf(
                  "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='2'",
                  GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                  GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                  GetSQLValueString($p, "int")
                );
                $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                $somaPontuacaoQuantitativo = 0;
                while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
                  $somaPontuacaoQuantitativo += floatval($row_quantitativo['qq_nota']);
                }

                $query_paralela = sprintf(
                  "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='3'",
                  GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                  GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                  GetSQLValueString($p, "int")
                );
                $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
                $notaParalela = 0;
                if ($row_paralela = mysql_fetch_assoc($paralela)) {
                  $notaParalela = floatval($row_paralela['qq_nota']);
                }

                $query_recuperacao = sprintf(
                  "SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='4'",
                  GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                  GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                  GetSQLValueString($p, "int")
                );
                $recuperacao = mysql_query($query_recuperacao, $SmecelNovo) or die(mysql_error());
                $notaRecuperacao = 0;
                if ($row_recuperacao = mysql_fetch_assoc($recuperacao)) {
                  $notaRecuperacao = floatval($row_recuperacao['qq_nota']);
                }

                // Ajustando valores de acordo com os períodos
                switch ($p) {
                  case '1':
                  case '2':
                    $mediaTrimestre = 18; // Média necessária para os períodos 1 e 2
                    break;
                  case '3':
                    $mediaTrimestre = 24; // Média necessária para o período 3
                    break;
                }

                // Soma da nota qualitativa com a quantitativa, ou com a paralela se existir
                $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + ($notaParalela > 0 && $notaParalela > $somaPontuacaoQuantitativo ? $notaParalela : $somaPontuacaoQuantitativo);

                // Definindo a nota total do trimestre
                $totalTrimestre = $notaRecuperacao > 0 ? $notaRecuperacao : $totalTrimestreSemRecuperacao;

                $totalTrimestre = arredondarNota($totalTrimestre);
                $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);

                switch ($p) {
                  case '1':
                    $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                    break;
                  case '2':
                    $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
                    break;
                  case '3':
                    $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
                    break;
                }

                // Inicializa a classe da nota como reprovado
                $classeNota = 'nota-paralela-rep';

                // Verifica aprovação com paralela ou recuperação
                if ($totalTrimestre >= $mediaTrimestre) {
                  $classeNota = 'nota-apr';
                  $alunos_aprovados[] = array('aluno_id' => $row_matricula['vinculo_aluno_id'], 'periodo' => $p);
                } else if ($notaParalela > 0 && $notaParalela >= 8.1 && $totalTrimestreSemRecuperacao >= $mediaTrimestre) {
                  $classeNota = 'nota-paralela-apr';
                  $alunos_aprovados[] = array('aluno_id' => $row_matricula['vinculo_aluno_id'], 'periodo' => $p);
                } else {
                  $alunos_reprovados_paralela[] = array('aluno_id' => $row_matricula['vinculo_aluno_id'], 'periodo' => $p);
                }
                $txtApr = "APR";
                $pontuacaoTotal = $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3;
                if( $pontuacaoTotal < 60 && $row_matricula['vinculo_aluno_conselho'] == "S") {
                  $pontuacaoTotal = 60;
                  $txtApr = "APR. CONSELHO";
                }
                ?>
                <td class="<?php echo $classeNota; ?> ls-txt-center"><?php echo $totalTrimestre; ?></td>
                <td class="ls-txt-center"><?php echo $notaRecuperacao; ?></td>
              <?php } ?>
              <td class="ls-txt-center">
                <?php echo $pontuacaoTotal; ?>
              </td>
              <td class="<?php echo $classeNota; ?> ls-txt-center">
                <?php
                
                ?>
                <?php
                if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") {

                  if ($pontuacaoTotal < 60) {
                    echo "<span style='color: red;'>REP</span>";

                  } else {
                    echo "<span style='color: green;'>$txtApr</span>";
                  }
                } else {
                  echo '-';
                }

                ?>
              </td>
            </tr>
          <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
        </tbody>
      </table>
      <br>
      <p> LEGENDA: </p>
      <p> <strong>TT</strong>: TOTAL DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>REC</strong>:
        RECUPERAÇÃO DO TRIMESTRE - <strong>RF</strong>: RESULTADO FINAL </p>
      <div class="ls-box">
        <a href="matriculaExibe.php?cmatricula=<?php echo $colname_matricula; ?>" class="ls-btn-primary">Voltar</a>
        <a href="boletimVerImprimirQQ.php?c=<?php echo $colname_matricula; ?>" class="ls-btn "
          target="_blank">IMPRIMIR</a>
        <a href="conselho_lancar.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right"
          id="conselho">CONSELHO DE CLASSE</a>
        <a href="reprovar_faltas.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right"
          id="conselho">REPROVAR POR FALTAS</a>
      </div>
    </div>
    <hr>
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

mysql_free_result($matricula);

mysql_free_result($turma);

mysql_free_result($disciplinasMatriz);

mysql_free_result($criteriosAvaliativos);


mysql_free_result($matriz);

mysql_free_result($EscolaLogada);
?>