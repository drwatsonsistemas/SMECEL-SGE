<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
<?php
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
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
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
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id = '$row_Alunos[turma_id_escola]'");
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

if($totalRows_Escola=="") {
	//header("Location:../index.php?loginErr");
}

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
  <html class="<?php echo TEMA; ?>" lang="pt-br">
  <head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-117872281-1');
    </script>
    <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <style>
      @page { size: auto;  margin: 5mm; }
      table {
       width:100%;
       border-collapse: collapse;
     }
     table a {
       display:block;
       padding:3px;
     }
     th, td {
       padding:5px;
       border:1px solid #ccc;
     }
     tr, td {
       padding:2px;
       height:9px;
       line-height:9px;
     }

     .aluno {
       background-color: #ddd;
       border-radius: 0%;
       height: 40px;
       object-fit: cover;
       width: 40px;
     }

     .nota-paralela {
      color: #B9A016;
    }
    .nota-paralela-apr{
      color: #2881AC;
    }

    .nota-paralela-rep{
      color: #D75553;
    }

    .nota-apr{
      color: #388f39;
    }
  </style>
</head>
<body onload="print();alert('Configure a impressora para a orientação paisagem')">



  <hr>
  <h3 class="ls-txt-center">MAPA DE NOTAS</h3>
  <?php if ($totalRows_Alunos==0) { ?>
    NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
  <?php } else { ?>
    <br>
    <h4 class="ls-txt-center"><?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?> - <?php echo $row_ProfLogado['func_nome']; ?></h4><br>

    <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
      <thead>
        <tr class="">
          <th colspan="3" class=""></th>
          <?php $tmu = 0; ?>
          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
            <?php
            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $query_PeriodosBloqueio1 = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash 
            FROM smc_unidades
            WHERE per_unid_id_sec = '$row_Secretaria[sec_id]' AND per_unid_periodo = '$p' AND per_unid_id_ano = '$row_AnoLetivo[ano_letivo_id]'";
            $PeriodosBloqueio1 = mysql_query($query_PeriodosBloqueio1, $SmecelNovo) or die(mysql_error());
            $row_PeriodosBloqueio1 = mysql_fetch_assoc($PeriodosBloqueio1);
            $totalRows_PeriodosBloqueio1 = mysql_num_rows($PeriodosBloqueio1);
            ?>
            <th colspan="2" class="ls-txt-center" width="15"><?php echo $p; ?>º PERÍODO <br>
            </th>
          <?php } ?>
          <th colspan="4" class="ls-txt-center">RESULTADO</th>
        </tr>
        <tr class="">
          <th  class="ls-txt-center"></th>
          <th  class="ls-txt-center">MAT</th>
          <th class="ls-txt-center">IDENTIFICAÇÃO</th>
          <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
            <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">TT.</th>
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
        $pMaxQualitativa=0;
        $pMaxQuantitativa=0;
        $pMaxParalela=0;

        do { ?>


          <tr>
            <td width="15" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
            <td width="10" class="" style="padding:0 5px;">
              <?= $row_Alunos['vinculo_aluno_id'] ?>
            </td>
            <td width="150" class="" style="padding:0 5px;">
              <?php echo current( str_word_count($row_Alunos['aluno_nome'],2)); ?>
              <?php $word = explode(" ", trim($row_Alunos['aluno_nome'])); echo $word[count($word)-1]; ?></td>
              <?php $tmu = 0; ?>
              <?php $aluno_periodo_atende_condicao_paralela = false; ?>
              <?php $pontuacaoTotalAnoLetivo1 = 0; ?>
              <?php $pontuacaoTotalAnoLetivo2 = 0; ?>
              <?php $pontuacaoTotalAnoLetivo3 = 0; ?>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                <?php
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_PeriodosBloqueio = "SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash 
                FROM smc_unidades
                WHERE per_unid_id_sec = '$row_Secretaria[sec_id]' AND per_unid_periodo = '$p' AND per_unid_id_ano = '$row_AnoLetivo[ano_letivo_id]'";
                $PeriodosBloqueio = mysql_query($query_PeriodosBloqueio, $SmecelNovo) or die(mysql_error());
                $row_PeriodosBloqueio = mysql_fetch_assoc($PeriodosBloqueio);
                $totalRows_PeriodosBloqueio = mysql_num_rows($PeriodosBloqueio);
                ?>

                <?php $ru = 0; ?>
                <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                  <td width="15" class="ls-txt-center"></td>
                <?php } ?>

                <?php 
                $query_qualitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                  FROM smc_notas_qq 
                  WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                  AND qq_tipo_criterio='1'", 
                  GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"), 
                  GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
                  GetSQLValueString($p, "int"));
                $qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
                $totalRows_qualitativo = mysql_num_rows($qualitativo); 
                $somaPontuacaoQualitativo = 0;
                while($row_qualitativo = mysql_fetch_assoc($qualitativo)){
                  $somaPontuacaoQualitativo += $row_qualitativo['qq_nota'];
                }

                $query_quantitativo = sprintf("SELECT qq_id, qq_id_matricula, qq_id_componente, qq_id_periodo, qq_tipo_criterio, qq_id_criterio, qq_nota 
                  FROM smc_notas_qq 
                  WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s
                  AND qq_tipo_criterio='2'", 
                  GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"), 
                  GetSQLValueString($row_Disciplina['disciplina_id'], "int"),
                  GetSQLValueString($p, "int"));
                $quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
                $totalRows_quantitativo = mysql_num_rows($quantitativo); 
                $somaPontuacaoQuantitativo = 0;
                while($row_quantitativo = mysql_fetch_assoc($quantitativo)){
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
                  GetSQLValueString($p, "int"));
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
            <?= number_format($totalTrimestre, 2, '.', '') ?>
          </td>
          <input type="hidden" name="aluno_id" value="<?= $row_Alunos['vinculo_aluno_id'] ?>">
          
          
          <td width="15" class="ls-txt-center ls-display-none-xs "
          aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" periodo="<?= $p ?>">
          <?= number_format($recuperacao, 2, '.', '') ?>
        </td>

      <?php } ?>

      <td width="15" class="ls-txt-center"><span class="ls-text-md"><?= $pontuacaoTotalAnoLetivo1+$pontuacaoTotalAnoLetivo2+$pontuacaoTotalAnoLetivo3 ?></span></td>
      <td width="15" inputmode="numeric" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" class="ls-txt-center">
        <span class="ls-text-md">

          <?php
          $pontuacaoTotal = $pontuacaoTotalAnoLetivo1+$pontuacaoTotalAnoLetivo2+$pontuacaoTotalAnoLetivo3;
          if (($row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) && $row_AnoLetivo['ano_letivo_resultado_final'] <> "") { 

            if($pontuacaoTotal < 60){
              echo "<span style='color: red;'>REP</span>";

            }else{
              echo "<span style='color: green;'>APR</span>";
            }
          }else{
            echo '-'; 
          }
          ?>

        </span>
      </td>
    </tr>


  <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
  <?php
// Depois do loop, adicionamos a classe para os alunos que estão no array
  echo '<script>
  $(document).ready(function() {';
  foreach ($alunos_na_paralela as $aluno_info) {
    echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_info['aluno_id'] . '\'][periodo=\'' . $aluno_info['periodo'] . '\']").addClass("nota-paralela");
    $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_info['aluno_id'] . '\'][periodo=\'' . $aluno_info['periodo'] . '\']").addClass("nota-paralela");';
  }
  echo '});
  </script>';

  echo '<script>
  $(document).ready(function() {';
  foreach ($alunos_aprovados_paralela as $aluno_apr_prl) {
    echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");
    $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");
    $("input[name=\'paralela\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");';


  }
  echo '});
  </script>';

  echo '<script>
  $(document).ready(function() {';
  foreach ($alunos_reprovados_paralela as $aluno_rep_prl) {
    echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");
    $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");
    $("input[name=\'paralela\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");';


  }
  echo '});
  </script>';

  echo '<script>
  $(document).ready(function() {';
  foreach ($alunos_aprovados as $aluno_apr) {
    echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");
    $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");
    $("input[name=\'paralela\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");';


  }
  echo '});
  </script>';
  ?>
<?php } ?>
</tbody>

</table>
<br>
<p> LEGENDA: </p>
<p> <strong>TT.</strong>: TOTAL TRIMESTRE - <strong>REC</strong>: RECUPERAÇÃO DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>RF</strong>: RESULTADO FINAL</p>
<small>SMECEL - Sistema de Gestão Escolar | www.smecel.com.br</small><br>
<small>PROFESSOR(A) | <?php echo $row_ProfLogado['func_nome']; ?></small><br>
<small><?= date('d/m/Y H:i:s') ?></small>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script>
<script src="../../js/jquery.mask.js"></script> 
<script type="text/javascript" src="../js/app.js"></script>
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
<script type="text/javascript">


  $(document).ready(function(){
    $('.nota').mask('000.00', {reverse: true});
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
  });				

  $(document).ready(function() {
    $('.recarregar').click(function() {
      location.reload();
    });
  });  		  



  $(function() {
   $(document).on('click', 'input[type=text]', function() {
     this.select();
   });
 });	 

</script>

<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>