<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include ('fnc/inverteData.php'); ?>
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


function arredondarNota($nota) {
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
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">    <link rel="stylesheet" type="text/css" href="css/preloader.css">
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
    <?php include_once ("menu-top.php"); ?>
    <?php include_once ("menu-esc.php"); ?>
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
        <?php if ($row_matricula['vinculo_aluno_conselho']=="S") { ?>
          <br>
          <div class="ls-alert-warning">Aluno APROVADO pelo Conselho de Classe.</div>
        <?php } ?>
        <?php if ($row_matricula['vinculo_aluno_conselho_reprovado']=="S") { ?>
          <br>
          <div class="ls-alert-danger">Aluno REPROVADO pelo Conselho de Classe.</div>
        <?php } ?>
        <?php if ($row_matricula['vinculo_aluno_reprovado_faltas']=="S") { ?>
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
                <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">TT.</th> <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">REC.</th>
              <?php } ?>
              <th width="15" style="background-color:#ccde9e" class="ls-txt-center">TP.</th>
              <th width="15" style="background-color:#ccde9e" class="ls-txt-center">RF.</th>
            </tr>
          </thead>
          <tbody>
            <?php

            $alunos_na_paralela = array();
            $alunos_aprovados_paralela = array();
            $alunos_reprovados_paralela = array();
            $alunos_aprovados = array();
            $num = 1;
            $pMaxQualitativa = 0;
            $pMaxQuantitativa = 0;
            $pMaxParalela = 0;

            ?>
            <?php
            do { ?>
              <tr>
                <td width="150"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                <?php $tmu = 0; ?>
                <?php $aluno_periodo_atende_condicao_paralela = false; ?>
                <?php $pontuacaoTotalAnoLetivo1 = 0; ?>
                <?php $pontuacaoTotalAnoLetivo2 = 0; ?>
                <?php $pontuacaoTotalAnoLetivo3 = 0; ?>
                <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                  <?php
                  $query_qualitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                    FROM smc_notas_qq 
                    WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                    AND qq_tipo_criterio='1'",
                    GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                    GetSQLValueString($p, "int")
                  );
                  $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                  $totalRows_qualitativo = mysql_num_rows($qualitativo);
                  $somaPontuacaoQualitativo = 0;
                  while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
                    $somaPontuacaoQualitativo += $row_qualitativo['qq_nota'];
                  }

                  $query_quantitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                    FROM smc_notas_qq 
                    WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                    AND qq_tipo_criterio='2'",
                    GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                    GetSQLValueString($p, "int")
                  );
                  $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                  $totalRows_quantitativo = mysql_num_rows($quantitativo);
                  $somaPontuacaoQuantitativo = 0;
                  while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
                    $somaPontuacaoQuantitativo += $row_quantitativo['qq_nota'];
                  }

                // Convertendo valores para float
                  $somaPontuacaoQuantitativo = floatval($somaPontuacaoQuantitativo);
                  $somaPontuacaoQualitativo = floatval($somaPontuacaoQualitativo);
                  $totalTrimestre = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                  $totalTrimestreSemRecuperacao = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                //PARALELA
                  $query_paralela = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                    FROM smc_notas_qq 
                    WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                    AND qq_tipo_criterio='3'",
                    GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"),
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                    GetSQLValueString($p, "int")
                  );
                  $paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
                  $row_paralela = mysql_fetch_assoc($paralela);
                  $totalRows_paralela = mysql_num_rows($paralela);
                  $paralela = $row_paralela['qq_nota'];

                  $query_recuperacao = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                    FROM smc_notas_qq 
                    WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                    AND qq_tipo_criterio='4'", 
                    GetSQLValueString($row_matricula['vinculo_aluno_id'], "int"), 
                    GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
                    GetSQLValueString($p, "int"));
                  $recuperacao = mysql_query($query_recuperacao, $SmecelNovo) or die(mysql_error());
                  $row_recuperacao = mysql_fetch_assoc($recuperacao);
                  $totalRows_recuperacao = mysql_num_rows($recuperacao); 
                  $recuperacao = $row_recuperacao['qq_nota'];


                  if ($p == '1') {
                    $pMaxQuantitativa = 13.5;
                    $pMaxQualitativa = 16.5;
                    $pMaxParalela = 13.5;
                    $pMaxRecuperacao = $pMaxQualitativa+$pMaxQuantitativa;
                    $totalTrimestre = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                    $totalTrimestre = arredondarNota($totalTrimestre);
                    $totalTrimestreSemRecuperacao = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                    $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);
                    $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                    $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);
                    if ($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0) {
                      if($totalRows_recuperacao > 0){
                        $totalTrimestre = $recuperacao;
                        $totalTrimestre = arredondarNota($totalTrimestre);
                        $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                        $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);

                        if($totalTrimestre < 18){

                          $aluno_periodo_atende_condicao_reprovado = true;
                          $alunos_reprovados_paralela[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }elseif($totalTrimestre >= 18){
                          $aluno_periodo_atende_condicao_aprovado = true;
                          $alunos_aprovados[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }
                      }else{

                        if ($somaPontuacaoQuantitativo < 8.1 && $totalTrimestre < 18) {
                          if ($totalRows_paralela > 0) {
                            if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                              $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                              $totalTrimestre = arredondarNota($totalTrimestre);

                              $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + $paralela;
                              $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);
                              $pontuacaoTotalAnoLetivo1 = $totalTrimestre;

                              $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);
                              if ($totalTrimestre >= 18) {
                                $aluno_periodo_atende_condicao_paralela = false;
                                $aluno_periodo_atende_condicao_reprovado = false;
                                $aluno_periodo_atende_condicao_aprovado_paralela = true;
                                $alunos_aprovados_paralela[] = array(
                                  'aluno_id' => $row_matricula['vinculo_aluno_id'],
                                  'periodo' => $p
                                );
                              } else {
                               $aluno_periodo_atende_condicao_paralela = false;
                               $aluno_periodo_atende_condicao_reprovado = true;
                               $alunos_reprovados_paralela[] = array(
                                'aluno_id' => $row_matricula['vinculo_aluno_id'],
                                'periodo' => $p
                              );
                             }
                           } else {
                            $aluno_periodo_atende_condicao_reprovado = true;
                            $alunos_reprovados_paralela[] = array(
                              'aluno_id' => $row_matricula['vinculo_aluno_id'],
                              'periodo' => $p
                            );
                          }
                        } else {
                          $aluno_periodo_atende_condicao_paralela = true;
                          $alunos_na_paralela[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }
                      }else {
                        if ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre < 18) {
                          $aluno_periodo_atende_condicao_reprovado = true;
                          $alunos_reprovados_paralela[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        } elseif ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre >= 18) {
                          $aluno_periodo_atende_condicao_reprovado = false;
                          $aluno_periodo_atende_condicao_paralela = false;
                          $aluno_periodo_atende_condicao_aprovado = true;
                          $alunos_aprovados[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }else{
                          $aluno_periodo_atende_condicao_reprovado = false;
                          $aluno_periodo_atende_condicao_paralela = false;
                          $aluno_periodo_atende_condicao_aprovado = true;
                          $alunos_aprovados[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }
                      }

                    }
                  }
                }


                if ($p == '2') {
                  $pMaxQuantitativa = 13.5;
                  $pMaxQualitativa = 16.5;
                  $pMaxParalela = 13.5;
                  $pMaxRecuperacao = $pMaxQualitativa+$pMaxQuantitativa;
                  $totalTrimestre = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                  $totalTrimestre = arredondarNota($totalTrimestre);
                  $totalTrimestreSemRecuperacao = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                  $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);
                  $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
                  $pontuacaoTotalAnoLetivo2 = arredondarNota($pontuacaoTotalAnoLetivo2);
                  if ($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0) {

                    if($totalRows_recuperacao > 0){
                      $totalTrimestre = $recuperacao;
                      $totalTrimestre = arredondarNota($totalTrimestre);
                      $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
                      $pontuacaoTotalAnoLetivo2 = arredondarNota($pontuacaoTotalAnoLetivo2);

                      if($totalTrimestre < 18){
                        $aluno_periodo_atende_condicao_reprovado = true;
                        $alunos_reprovados_paralela[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }elseif($totalTrimestre >= 18){
                        $aluno_periodo_atende_condicao_reprovado = false;
                        $aluno_periodo_atende_condicao_paralela = false;
                        $aluno_periodo_atende_condicao_aprovado = true;
                        $alunos_aprovados[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }
                    }else{

                      if ($somaPontuacaoQuantitativo < 8.1 && $totalTrimestre < 18) {
                        if ($totalRows_paralela > 0) {
                          if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                            $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                            $totalTrimestre = arredondarNota($totalTrimestre);

                            $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + $paralela;
                            $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);

                            $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
                            $pontuacaoTotalAnoLetivo2 = arredondarNota($pontuacaoTotalAnoLetivo2);
                            if ($totalTrimestre >= 18) {
                              $aluno_periodo_atende_condicao_paralela = false;
                              $aluno_periodo_atende_condicao_reprovado = false;
                              $aluno_periodo_atende_condicao_aprovado_paralela = true;
                              $alunos_aprovados_paralela[] = array(
                                'aluno_id' => $row_matricula['vinculo_aluno_id'],
                                'periodo' => $p
                              );
                            } else {
                             $aluno_periodo_atende_condicao_paralela = false;
                             $aluno_periodo_atende_condicao_reprovado = true;
                             $alunos_reprovados_paralela[] = array(
                              'aluno_id' => $row_matricula['vinculo_aluno_id'],
                              'periodo' => $p
                            );
                           }
                         } else {
                          $aluno_periodo_atende_condicao_reprovado = true;
                          $alunos_reprovados_paralela[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }
                      } else {
                        $aluno_periodo_atende_condicao_paralela = true;
                        $alunos_na_paralela[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }
                    }else {
                      if ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre < 18) {
                        $aluno_periodo_atende_condicao_reprovado = true;
                        $alunos_reprovados_paralela[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      } elseif ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre >= 18) {
                        $aluno_periodo_atende_condicao_reprovado = false;
                        $aluno_periodo_atende_condicao_paralela = false;
                        $aluno_periodo_atende_condicao_aprovado = true;
                        $alunos_aprovados[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }else{
                        $aluno_periodo_atende_condicao_reprovado = false;
                        $aluno_periodo_atende_condicao_paralela = false;
                        $aluno_periodo_atende_condicao_aprovado = true;
                        $alunos_aprovados[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }
                    }

                  }

                }
              }

              if ($p == '3') {

                $pMaxQuantitativa=18;
                $pMaxQualitativa=22;
                $pMaxParalela=18;
                $pMaxRecuperacao = $pMaxQualitativa+$pMaxQuantitativa;
                $totalTrimestre = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                $totalTrimestre = arredondarNota($totalTrimestre);
                $totalTrimestreSemRecuperacao = $somaPontuacaoQuantitativo + $somaPontuacaoQualitativo;
                $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);
                $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
                $pontuacaoTotalAnoLetivo3 = arredondarNota($pontuacaoTotalAnoLetivo3);
                if($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0){

                  if($totalRows_recuperacao > 0){
                    $totalTrimestre = $recuperacao;
                    $totalTrimestre = arredondarNota($totalTrimestre);
                    $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
                    $pontuacaoTotalAnoLetivo3 = arredondarNota($pontuacaoTotalAnoLetivo3);

                    if($totalTrimestre < 24){
                      $aluno_periodo_atende_condicao_reprovado = true;
                      $alunos_reprovados_paralela[] = array(
                        'aluno_id' => $row_matricula['vinculo_aluno_id'],
                        'periodo' => $p
                      );
                    }elseif($totalTrimestre >= 24){
                      $aluno_periodo_atende_condicao_aprovado = true;
                      $aluno_periodo_atende_condicao_reprovado = false;
                      $aluno_periodo_atende_condicao_paralela = false;
                      $alunos_aprovados[] = array(
                        'aluno_id' => $row_matricula['vinculo_aluno_id'],
                        'periodo' => $p
                      );
                    }
                  }else{

                    if ($somaPontuacaoQuantitativo < 10.8 && $totalTrimestre < 24) {
                      if ($totalRows_paralela > 0) {
                        if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                          $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                          $totalTrimestre = arredondarNota($totalTrimestre);
                          $totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + $paralela;
                          $totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);
                          $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
                          $pontuacaoTotalAnoLetivo3 = arredondarNota($pontuacaoTotalAnoLetivo3);
                          if ($totalTrimestre >= 24) {
                            $aluno_periodo_atende_condicao_aprovado_paralela = true;
                            $aluno_periodo_atende_condicao_paralela = false;
                            $aluno_periodo_atende_condicao_reprovado = false;
                            $alunos_aprovados_paralela[] = array(
                              'aluno_id' => $row_matricula['vinculo_aluno_id'],
                              'periodo' => $p
                            );
                          } else {
                            $aluno_periodo_atende_condicao_paralela = false;
                            $aluno_periodo_atende_condicao_reprovado = true;
                            $alunos_reprovados_paralela[] = array(
                              'aluno_id' => $row_matricula['vinculo_aluno_id'],
                              'periodo' => $p
                            );
                          }
                        } else {
                          $aluno_periodo_atende_condicao_reprovado = true;
                          $alunos_reprovados_paralela[] = array(
                            'aluno_id' => $row_matricula['vinculo_aluno_id'],
                            'periodo' => $p
                          );
                        }
                      } else {

                        $aluno_periodo_atende_condicao_paralela = true;
                        $alunos_na_paralela[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
            ); // Adiciona o ID do aluno e o período ao array
                      }
                    } else {
                      if($somaPontuacaoQuantitativo >= 10.8 && $totalTrimestre < 24){
                        $aluno_periodo_atende_condicao_reprovado = true;
                        $alunos_reprovados_paralela[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }elseif ($somaPontuacaoQuantitativo >= 10.8 && $totalTrimestre >= 24) {
                        $aluno_periodo_atende_condicao_reprovado = false;
                        $aluno_periodo_atende_condicao_paralela = false;
                        $alunos_aprovados[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }else{
                        $aluno_periodo_atende_condicao_reprovado = false;
                        $aluno_periodo_atende_condicao_paralela = false;
                        $alunos_aprovados[] = array(
                          'aluno_id' => $row_matricula['vinculo_aluno_id'],
                          'periodo' => $p
                        );
                      }
                    }

                  }

                }
              }
              ?>
              <td class="ls-txt-center ls-display-none-xs" <?php if ($totalRows_quantitativo > 0)
              echo "aluno_id='{$row_matricula['vinculo_aluno_id']}' periodo='$p'"; ?>>
              <?= $totalRows_quantitativo > 0 ? number_format($totalTrimestreSemRecuperacao, 2, '.', '') : '-' ?>
            </td>
            <td class="ls-txt-center ls-display-none-xs" <?php if ($totalRows_quantitativo > 0)
            echo "aluno_id='{$row_matricula['vinculo_aluno_id']}' periodo='$p'"; ?>>
            <?= $totalRows_recuperacao > 0 ? number_format($totalTrimestre, 2, '.', '') : '-' ?>
          </td>
        <?php } ?>
        <?php 
        $pontuacaoTotal = $pontuacaoTotalAnoLetivo1+$pontuacaoTotalAnoLetivo2+$pontuacaoTotalAnoLetivo3;
        ?>
        <td class="ls-txt-center">
          <span class="ls-text-md" style="color:<?php if($pontuacaoTotal >= 60){echo 'blue';}else{echo '';} ?>"><?= $pontuacaoTotal ?></span>
        </td>
        <td width="15" inputmode="numeric" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" class="ls-txt-center"><span class="ls-text-md">

          <?php

          if($pontuacaoTotal < 60){
              //echo "<span style='color: red;'>REP</span>";
            echo "-";
          }else{
              //echo "<span style='color: green;'>APR</span>";
            echo "-";
          }
          ?>

        </span></td>
      </tr>
    <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
  </tbody>
</table>
<br>
<p> LEGENDA: </p>
<p> <strong>TT</strong>: TOTAL DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>REC</strong>: RECUPERAÇÃO DO TRIMESTRE - <strong>RF</strong>: RESULTADO FINAL </p>
<div class="ls-box">
  <a href="matriculaExibe.php?cmatricula=<?php echo $colname_matricula; ?>" class="ls-btn-primary">Voltar</a>
  <a href="boletimVerImprimirQQ.php?c=<?php echo $colname_matricula; ?>" class="ls-btn " target="_blank">IMPRIMIR</a>
  <a href="conselho_lancar.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right" id="conselho">CONSELHO DE CLASSE</a> 
  <a href="reprovar_faltas.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right" id="conselho">REPROVAR POR FALTAS</a> </div>
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