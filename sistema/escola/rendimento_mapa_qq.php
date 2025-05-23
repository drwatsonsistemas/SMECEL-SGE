<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php include ('../../sistema/funcoes/inverteData.php'); ?>
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
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos, ca_forma_avaliacao FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

$link = "rendimento_mapa";
if ($row_Criterios['ca_forma_avaliacao'] == "Q") {
    $link = "rendimento_mapa_qq";
} else {
    $link = "rendimento_mapa";
}

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

    <style>
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
</head>

<body>
    <?php include_once ("menu-top.php"); ?>
    <?php include_once ("menu-esc.php"); ?>
    <main class="ls-main ">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">MAPA DE NOTAS</h1>
            <!-- CONTEÚDO -->

            <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary">TURMA/COMPONENTE</a>

                <ul class="ls-dropdown-nav">
                    <?php do { ?>
                        <li><a
                            href="<?= $link ?>.php?componente=<?php echo $row_turmas['disciplina_id']; ?>&turma=<?php echo $row_turmas['turma_id']; ?>"><?php echo $row_turmas['turma_nome']; ?>
                            | <?php echo $row_turmas['turma_turno_nome']; ?> |
                            <?php echo $row_turmas['disciplina_nome']; ?> | <?php echo $row_turmas['func_nome']; ?></a>
                        </li>
                    <?php } while ($row_turmas = mysql_fetch_assoc($turmas)); ?>
                </ul>
            </div>

            <a href="rendimento_mapa_imprimir_qq.php?componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>"
                class="ls-btn-primary" target="_blank">IMPRIMIR TELA</a>

                <hr>

                <?php if ($totalRows_Alunos == 0) { ?>
                    Escolha uma turma/componente
                <?php } else { ?>
                    <br>

                    <h3><?php echo $row_Alunos['disciplina_nome']; ?> | <?php echo $row_Alunos['turma_nome']; ?></h3>
                    <hr>
                    <div class="ls-group-btn ls-group-active">
                        <?php do { ?>
                            <a href="<?= $link ?>.php?componente=<?php echo $row_disciplinas['disciplina_id']; ?>&turma=<?php echo $colname_Turma; ?>"
                                class="ls-btn ls-btn-xs <?php if ($row_disciplinas['disciplina_id'] == $colname_Disciplina) {
                                    echo " ls-active";
                                } ?>"><?php echo $row_disciplinas['disciplina_nome']; ?></a>
                            <?php } while ($row_disciplinas = mysql_fetch_assoc($disciplinas)); ?>
                        </div>

                        <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="3" class="ls-txt-center">IDENTIFICAÇÃO</th>
                                    <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                                        <th colspan="4" class="ls-txt-center" width="15"><?php echo $p; ?>º PERÍODO</th>
                                    <?php } ?>
                                    <th colspan="3" class="ls-txt-center ls-display-none-xs">RESULTADO</th>
                                </tr>
                                <tr>
                                    <th width="15" class="ls-txt-center">Nº</th>
                                    <th width="30" class="ls-txt-center">Foto</th>
                                    <th width="150" class="ls-txt-center">Nome</th>
                                    <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                                        <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">QNT.</th>
                                        <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">QLT.</th>
                                        <th width="15" style="background-color:#cbd3da" class="ls-txt-center">PRL.</th>
                                        <th width="15" style="background-color:#a0a0a0"class="ls-txt-center"><span style="color:#fe4127">REC.</span></th>
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
                                <?php do { ?>
                                    <tr>
                                        <td width="15" class="ls-txt-center"><strong><?php echo $num++; ?></strong></td>
                                        <td width="30" class="" style="padding:0 5px;">
                                            <?php if ($row_Alunos['aluno_foto'] == "") { ?>
                                                <img src="../../aluno/fotos/semfoto.jpg" class="aluno ls-txt-left ls-float-left" border="0"
                                                width="100%">
                                            <?php } else { ?>
                                                <img src="<?php echo '../../aluno/fotos/' . $row_Alunos['aluno_foto']; ?>"
                                                class="hoverable aluno ls-float-left" border="0" width="100%">
                                            <?php } ?>
                                        </td>
                                        <td width="150" class="" style="padding:0 5px;"><?php echo $row_Alunos['aluno_nome']; ?></td>
                                        <?php $tmu = 0; ?>
                                        <?php $aluno_periodo_atende_condicao_paralela = false; ?>
                                        <?php $pontuacaoTotalAnoLetivo1 = 0; ?>
                                        <?php $pontuacaoTotalAnoLetivo2 = 0; ?>
                                        <?php $pontuacaoTotalAnoLetivo3 = 0; ?>
                                        <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                                            <?php
                                            $query_qualitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                                                FROM smc_notas_qq 
                                                WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                                                AND qq_tipo_criterio='1'",
                                                GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"),
                                                GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
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
                                                GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"),
                                                GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
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

                                    //PARALELA
                                            $query_paralela = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                                                FROM smc_notas_qq 
                                                WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                                                AND qq_tipo_criterio='3'",
                                                GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"),
                                                GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
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
                                              GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"), 
                                              GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
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
                                              $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                                              $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);
                                              if ($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0) {
                                                if($totalRows_recuperacao > 0){
                                                  $totalTrimestre = $recuperacao;
                                                  $totalTrimestre = arredondarNota($totalTrimestre);
                                                  $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                                                  $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);

                                                  if($totalTrimestre < 18){
                                                    $
                                                    $aluno_periodo_atende_condicao_reprovado = true;
                                                    $alunos_reprovados_paralela[] = array(
                                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                                      'periodo' => $p
                                                  );
                                                }elseif($totalTrimestre >= 18){
                                                    $aluno_periodo_atende_condicao_aprovado = true;
                                                    $alunos_aprovados[] = array(
                                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                                      'periodo' => $p
                                                  );
                                                }
                                            }else{

                                              if ($somaPontuacaoQuantitativo < 8.1 && $totalTrimestre < 18) {
                                                if ($totalRows_paralela > 0) {
                                                  if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                                                    $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                                                    $totalTrimestre = arredondarNota($totalTrimestre);
                                                    $pontuacaoTotalAnoLetivo1 = $totalTrimestre;
                                                    $pontuacaoTotalAnoLetivo1 = arredondarNota($pontuacaoTotalAnoLetivo1);
                                                    if ($totalTrimestre >= 18) {
                                                      $aluno_periodo_atende_condicao_paralela = false;
                                                      $aluno_periodo_atende_condicao_reprovado = false;
                                                      $aluno_periodo_atende_condicao_aprovado_paralela = true;
                                                      $alunos_aprovados_paralela[] = array(
                                                        'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                                        'periodo' => $p
                                                    );
                                                  } else {
                                                   $aluno_periodo_atende_condicao_paralela = false;
                                                   $aluno_periodo_atende_condicao_reprovado = true;
                                                   $alunos_reprovados_paralela[] = array(
                                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                                      'periodo' => $p
                                                  );
                                               }
                                           } else {
                                              $aluno_periodo_atende_condicao_reprovado = true;
                                              $alunos_reprovados_paralela[] = array(
                                                'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                                'periodo' => $p
                                            );
                                          }
                                      } else {
                                        $aluno_periodo_atende_condicao_paralela = true;
                                        $alunos_na_paralela[] = array(
                                          'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                          'periodo' => $p
                                      );
                                    }
                                }else {
                                  if ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre < 18) {
                                    $aluno_periodo_atende_condicao_reprovado = true;
                                    $alunos_reprovados_paralela[] = array(
                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                      'periodo' => $p
                                  );
                                } elseif ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre >= 18) {
                                    $aluno_periodo_atende_condicao_reprovado = false;
                                    $aluno_periodo_atende_condicao_paralela = false;
                                    $aluno_periodo_atende_condicao_aprovado = true;
                                    $alunos_aprovados[] = array(
                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                                      'periodo' => $p
                                  );
                                }else{
                                    $aluno_periodo_atende_condicao_reprovado = false;
                                    $aluno_periodo_atende_condicao_paralela = false;
                                    $aluno_periodo_atende_condicao_aprovado = true;
                                    $alunos_aprovados[] = array(
                                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
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
                            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                            'periodo' => $p
                        );
                      }elseif($totalTrimestre >= 18){
                          $aluno_periodo_atende_condicao_reprovado = false;
                          $aluno_periodo_atende_condicao_paralela = false;
                          $aluno_periodo_atende_condicao_aprovado = true;
                          $alunos_aprovados[] = array(
                            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                            'periodo' => $p
                        );
                      }
                  }else{

                    if ($somaPontuacaoQuantitativo < 8.1 && $totalTrimestre < 18) {
                      if ($totalRows_paralela > 0) {
                        if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                          $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                          $totalTrimestre = arredondarNota($totalTrimestre);
                          $pontuacaoTotalAnoLetivo2 = $totalTrimestre;
                          $pontuacaoTotalAnoLetivo2 = arredondarNota($pontuacaoTotalAnoLetivo2);
                          if ($totalTrimestre >= 18) {
                            $aluno_periodo_atende_condicao_paralela = false;
                            $aluno_periodo_atende_condicao_reprovado = false;
                            $aluno_periodo_atende_condicao_aprovado_paralela = true;
                            $alunos_aprovados_paralela[] = array(
                              'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                              'periodo' => $p
                          );
                        } else {
                         $aluno_periodo_atende_condicao_paralela = false;
                         $aluno_periodo_atende_condicao_reprovado = true;
                         $alunos_reprovados_paralela[] = array(
                            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                            'periodo' => $p
                        );
                     }
                 } else {
                    $aluno_periodo_atende_condicao_reprovado = true;
                    $alunos_reprovados_paralela[] = array(
                      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                      'periodo' => $p
                  );
                }
            } else {
              $aluno_periodo_atende_condicao_paralela = true;
              $alunos_na_paralela[] = array(
                'aluno_id' => $row_Alunos['vinculo_aluno_id'],
                'periodo' => $p
            );
          }
      }else {
        if ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre < 18) {
          $aluno_periodo_atende_condicao_reprovado = true;
          $alunos_reprovados_paralela[] = array(
            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
            'periodo' => $p
        );
      } elseif ($somaPontuacaoQuantitativo >= 8.1 && $totalTrimestre >= 18) {
          $aluno_periodo_atende_condicao_reprovado = false;
          $aluno_periodo_atende_condicao_paralela = false;
          $aluno_periodo_atende_condicao_aprovado = true;
          $alunos_aprovados[] = array(
            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
            'periodo' => $p
        );
      }else{
          $aluno_periodo_atende_condicao_reprovado = false;
          $aluno_periodo_atende_condicao_paralela = false;
          $aluno_periodo_atende_condicao_aprovado = true;
          $alunos_aprovados[] = array(
            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
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
          'aluno_id' => $row_Alunos['vinculo_aluno_id'],
          'periodo' => $p
      );
    }elseif($totalTrimestre >= 24){
        $aluno_periodo_atende_condicao_aprovado = true;
        $aluno_periodo_atende_condicao_reprovado = false;
        $aluno_periodo_atende_condicao_paralela = false;
        $alunos_aprovados[] = array(
          'aluno_id' => $row_Alunos['vinculo_aluno_id'],
          'periodo' => $p
      );
    }
}else{

  if ($somaPontuacaoQuantitativo < 10.8 && $totalTrimestre < 24) {
    if ($totalRows_paralela > 0) {
      if ($paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
        $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
        $totalTrimestre = arredondarNota($totalTrimestre);
        $pontuacaoTotalAnoLetivo3 = $totalTrimestre;
        $pontuacaoTotalAnoLetivo3 = arredondarNota($pontuacaoTotalAnoLetivo3);
        if ($totalTrimestre >= 24) {
          $aluno_periodo_atende_condicao_aprovado_paralela = true;
          $aluno_periodo_atende_condicao_paralela = false;
          $aluno_periodo_atende_condicao_reprovado = false;
          $alunos_aprovados_paralela[] = array(
            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
            'periodo' => $p
        );
      } else {
          $aluno_periodo_atende_condicao_paralela = false;
          $aluno_periodo_atende_condicao_reprovado = true;
          $alunos_reprovados_paralela[] = array(
            'aluno_id' => $row_Alunos['vinculo_aluno_id'],
            'periodo' => $p
        );
      }
  } else {
    $aluno_periodo_atende_condicao_reprovado = true;
    $alunos_reprovados_paralela[] = array(
      'aluno_id' => $row_Alunos['vinculo_aluno_id'],
      'periodo' => $p
  );
}
} else {

  $aluno_periodo_atende_condicao_paralela = true;
  $alunos_na_paralela[] = array(
    'aluno_id' => $row_Alunos['vinculo_aluno_id'],
    'periodo' => $p
            ); // Adiciona o ID do aluno e o período ao array
}
} else {
    if($somaPontuacaoQuantitativo >= 10.8 && $totalTrimestre < 24){
      $aluno_periodo_atende_condicao_reprovado = true;
      $alunos_reprovados_paralela[] = array(
        'aluno_id' => $row_Alunos['vinculo_aluno_id'],
        'periodo' => $p
    );
  }elseif ($somaPontuacaoQuantitativo >= 10.8 && $totalTrimestre >= 24) {
      $aluno_periodo_atende_condicao_reprovado = false;
      $aluno_periodo_atende_condicao_paralela = false;
      $alunos_aprovados[] = array(
        'aluno_id' => $row_Alunos['vinculo_aluno_id'],
        'periodo' => $p
    );
  }else{
      $aluno_periodo_atende_condicao_reprovado = false;
      $aluno_periodo_atende_condicao_paralela = false;
      $alunos_aprovados[] = array(
        'aluno_id' => $row_Alunos['vinculo_aluno_id'],
        'periodo' => $p
    );
  }
}

}

}
}

?>
<td width="15" class="ls-txt-center ls-display-none-xs "
aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" periodo="<?= $p ?>">
<?= number_format($somaPontuacaoQuantitativo, 2, '.', '') ?>
</td>
<td width="15" class="ls-txt-center ls-display-none-xs "
aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" periodo="<?= $p ?>">
<?= number_format($somaPontuacaoQualitativo, 2, '.', '') ?>
</td>
<td width="15" class="ls-txt-center ls-display-none-xs "
aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" periodo="<?= $p ?>">
<?= number_format($paralela, 2, '.', '') ?>
</td>
<td width="15" class="ls-txt-center ls-display-none-xs "
aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" periodo="<?= $p ?>">
<?= number_format($recuperacao, 2, '.', '') ?>
</td>
<?php } ?>

<td width="15" class="ls-txt-center"><span
    class="ls-text-md"><?= $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3 ?></span>
</td>
<td width="15" inputmode="numeric" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" class="ls-txt-center"><span class="ls-text-md">

    <?php
    $pontuacaoTotal = $pontuacaoTotalAnoLetivo1+$pontuacaoTotalAnoLetivo2+$pontuacaoTotalAnoLetivo3;
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
<?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>

</tbody>
</table>

<br>
<p> LEGENDA: </p>
<p> <strong>QNT</strong>: QUANTITATIVO - <strong>QLT</strong>: QUALITATIVO - <strong>PRL</strong>: PARALELA - <strong>REC</strong>: RECUPERAÇÃO DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>RF</strong>: RESULTADO FINAL</p>
<hr>
<?php } ?>


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
            <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php"
                class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
                <li><a href="#">&gt; Guia</a></li>
                <li><a href="#">&gt; Wiki</a></li>
            </ul>
        </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
    type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            <?php foreach ($alunos_na_paralela as $aluno_info): ?>
                $("td[aluno_id='<?= $aluno_info['aluno_id'] ?>'][periodo='<?= $aluno_info['periodo'] ?>']").addClass("nota-paralela");
            <?php endforeach; ?>

            <?php foreach ($alunos_aprovados_paralela as $aluno_apr_prl): ?>
                $("td[aluno_id='<?= $aluno_apr_prl['aluno_id'] ?>'][periodo='<?= $aluno_apr_prl['periodo'] ?>']").addClass("nota-paralela-apr");
            <?php endforeach; ?>

            <?php foreach ($alunos_reprovados_paralela as $aluno_rep_prl): ?>
                $("td[aluno_id='<?= $aluno_rep_prl['aluno_id'] ?>'][periodo='<?= $aluno_rep_prl['periodo'] ?>']").addClass("nota-paralela-rep");
            <?php endforeach; ?>

            <?php foreach ($alunos_aprovados as $aluno_apr): ?>
                $("td[aluno_id='<?= $aluno_apr['aluno_id'] ?>'][periodo='<?= $aluno_apr['periodo'] ?>']").addClass("nota-apr");
            <?php endforeach; ?>
        });
    </script>
</body>

</html>
<?php
mysql_free_result($Disciplina);

mysql_free_result($disciplinas);

mysql_free_result($turmas);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>