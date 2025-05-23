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
aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO(A)'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome  
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
    <title>PROFESSOR |<?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <style>
      table {
       width:100%;
       border-collapse: collapse;
     }
     table a {
       display:block;
       padding:4px;
     }
     th, td {
       border:1px solid #ccc;
     }
     tr, td {
       padding:0;
       height:10px;
       line-height:10px;
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
<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>

  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

      <p>
        <a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
        <a href="rendimento_mapa_qq_print_detalhado.php?componente=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" target="_blank" class="ls-btn">RELATÓRIO DE NOTAS DETALHADO</a>
        <a href="rendimento_mapa_qq_print.php?componente=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" target="_blank" class="ls-btn">RELATÓRIO DE NOTAS</a>
      </p>



      <div class="ls-box">

        <p>COMPONENTE: <?php echo $row_Disciplina['disciplina_nome']; ?></p>
        <p>TURMA: <?php echo $row_Turma['turma_nome']; ?></p>
        <p><small>-Clique sobre a célula para lançar as notas dos alunos.<br>-Para lançar individualmente, clique sobre o nome do aluno.</small></p>
      </div>



      <div class="ls-box">
        <h5 class="ls-title-5">LEGENDAS:</h5>
        <p class="ls-tag-success">O ALUNO ESTÁ APROVADO NO TRIMESTRE</p><br>
        <p class="ls-tag-warning">O ALUNO ESTÁ NA PARALELA</p><br>
        <p class="ls-tag-info">O ALUNO ESTÁ APROVADO PELA PARALELA</p><br>
        <p class="ls-tag-danger">O ALUNO ESTÁ REPROVADO NO TRIMESTRE</p>
      </div>
      <?php if ($totalRows_Alunos==0) { ?>
        NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
      <?php } else { ?>
        <div class="ls-box" >
          <div class="ls-alert-info" style="color: #00529B; font-family: Arial, sans-serif; font-size: 16px;">
            <strong style="font-weight: bold;">Atenção:</strong> <br>
            - Verifique sempre se a nota foi registrada corretamente na célula. <br>
            - Não insira a nota de recuperação somente na soma dos 3 trimestres.<br>
            - Primeiro deve ser lançado as notas do quantitativo e qualitativo, e por último a nota paralela, pois esta depende da soma dos dois.
          </div>
        </div>
        <br>
        <h5><?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?></h5>



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
                <th colspan="4" class="ls-txt-center" width="15"><?php echo $p; ?>º PERÍODO <br>
                  <small>LIMITE: <?php if ($totalRows_PeriodosBloqueio1 > 0) { echo date("d/m/y", strtotime($row_PeriodosBloqueio1['per_unid_data_bloqueio'])); } else { echo "-"; } ?>
                  <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?>
                    <br><span class="">Período atingido</span>
                  <?php } ?>
                </small>
              </th>
            <?php } ?>
            <th colspan="4" class="ls-txt-center">RESULTADO</th>
          </tr>
          <tr class="">
            <th colspan="3" class="ls-txt-center">IDENTIFICAÇÃO</th>
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
          $pMaxQualitativa=0;
          $pMaxQuantitativa=0;
          $pMaxParalela=0;
          $pMaxRecuperacao=0;

          do { ?>


            <tr>
              <td width="15" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
              <td width="40" class="" style="padding:0 5px;">
                <?php if ($row_Alunos['aluno_foto'] == "") { ?>
                  <img src="<?php echo URL_BASE.'/aluno/fotos/' ?>semfoto.jpg" class="aluno ls-txt-left ls-float-left" border="0" width="100%">
                <?php } else { ?>
                  <img src="<?php echo URL_BASE.'/aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>" class="hoverable aluno ls-float-left" border="0" width="100%">
                <?php } ?>
              </td>
              <td width="150" class="" style="padding:0 5px;">
                <a href="qq_aluno.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $row_Disciplina['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>&ref=mapa" class="ls-ico-export">
                  <?php echo $row_Alunos['aluno_nome']; ?>
                </a>
                <?php if ($row_Alunos['vinculo_aluno_situacao'] <> "1") { ?>
                  <br>
                  <span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
                <?php } ?>
              </td>
              <?php $tmu = 0; ?>
              <?php $aluno_periodo_atende_condicao_paralela = false; ?>
              <?php $aluno_periodo_atende_condicao_reprovado = false; ?>
              <?php $aluno_periodo_atende_condicao_aprovado = false; ?>
              <?php $aluno_periodo_atende_condicao_aprovado_paralela = false; ?>
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
            <td width="15" class="ls-txt-center">
              <input style="width: 35px" type="text" inputmode="numeric" class="ls-no-style-input ls-txt-center ls-text-md nota" 
              periodo="<?= $p ?>" 
              componente="<?php echo $row_Disciplina['disciplina_id']; ?>" 
              aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" 
              name="quantitativo" 
              pontuacaoMax="<?= $pMaxQuantitativa ?>" 
              value="<?= number_format($somaPontuacaoQuantitativo, 2, '.', ''); ?>" 
              <?php if (($totalRows_PeriodosBloqueio > 0) && ($row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> 
               disabled="" readonly 
               <?php } ?>>
             </td>

             <input type="hidden" name="aluno_id" value="<?= $row_Alunos['vinculo_aluno_id'] ?>">
             <td width="15" class="ls-txt-center">
              <input style="width: 40px" aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" type="text" periodo="<?= $p ?>" pontuacaoMax="<?= $pMaxQualitativa ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" class="ls-no-style-input ls-txt-center ls-text-md nota
              " name="qualitativo" value="<?= number_format($somaPontuacaoQualitativo, 2, '.', '') ?>" <?php if (($totalRows_PeriodosBloqueio > 0) && ($row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> disabled="" readonly <?php } ?>>
            </td>

            <td width="15" class="ls-txt-center" <?php if (!$aluno_periodo_atende_condicao_paralela && $totalRows_paralela < 1) { echo 'style="background-color: #CCCCCC"'; } ?>>
              <input style="width: 35px" aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" type="text" periodo="<?= $p ?>" pontuacaoMax="<?= $pMaxParalela ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" class="ls-no-style-input ls-txt-center ls-text-md nota
              " name="paralela" value="<?= number_format($paralela, 2, '.', '') ?>" <?php if (!$aluno_periodo_atende_condicao_paralela && $totalRows_paralela < 1) { echo 'disabled="" readonly'; } ?> >
            </td>
            <td width="15" class="ls-txt-center" <?php if (!$aluno_periodo_atende_condicao_reprovado && $totalRows_recuperacao < 1) { echo 'style="background-color: #CCCCCC"'; } ?>>
              <input style="width: 35px" aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" type="text" periodo="<?= $p ?>" pontuacaoMax="<?= $pMaxRecuperacao ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" class="ls-no-style-input ls-txt-center ls-text-md nota
              " name="recuperacaoFinal" value="<?= number_format($recuperacao, 2, '.', '') ?>" <?php if (!$aluno_periodo_atende_condicao_reprovado && $totalRows_recuperacao < 1) { echo 'disabled="" readonly'; } ?> >
            </td>
          <?php } ?>
          <td width="15" class="ls-txt-center"><span class="ls-text-md"><?= $pontuacaoTotalAnoLetivo1+$pontuacaoTotalAnoLetivo2+$pontuacaoTotalAnoLetivo3 ?></span></td>
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
        $("input[name=\'paralela\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");
        $("input[name=\'recuperacaoFinal\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");';

      }
      echo '});
      </script>';

      echo '<script>
      $(document).ready(function() {';
      foreach ($alunos_reprovados_paralela as $aluno_rep_prl) {
        echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");
        $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");
        $("input[name=\'paralela\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");
        $("input[name=\'recuperacaoFinal\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");';


      }
      echo '});
      </script>';

      echo '<script>
      $(document).ready(function() {';
      foreach ($alunos_aprovados as $aluno_apr) {
        echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");
        $("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");
        $("input[name=\'paralela\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");
        $("input[name=\'recuperacaoFinal\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");';


      }
      echo '});
      </script>';
      ?>
    <?php } ?>
  </tbody>
  <script type="text/javascript">
    $(document).ready(function() {
      $('input.nota').off('focus').on('focus', function() {
        $(this).data('valorAnterior', $(this).val());
      });

      $('input.nota').off('blur').on('blur', function() {
        var tipo = $(this).attr('name');
        var periodo = $(this).attr('periodo');
        var componente = $(this).attr('componente');
        var valor = $(this).val();
        var valorAnterior = $(this).data('valorAnterior');
        var alunoId = $(this).closest('tr').find('input[name="aluno_id"]').val();
        var pontuacaoMax = $(this).attr('pontuacaoMax');

        if (valor != valorAnterior) {
          $.ajax({
            type: 'POST',
            url: 'fnc/lancaPontuacao.php',
            data: {
              tipo: tipo,
              periodo: periodo,
              valor: valor,
              alunoId: alunoId,
              componente: componente,
              pontuacaoMax: pontuacaoMax
            },
            success: function(data) {
              $('#status').html(data);

              setTimeout(function() {
                $("#status").html("");
                window.location.reload();
              }, 1500);
            }
          });
        }
      });
    });
  </script>
</table>
<a onclick="window.location.reload();" class="ls-btn-primary">REGISTRAR NOTAS</a>
<br><br>
<p> LEGENDA: </p>
<p> <strong>QNT</strong>: QUANTITATIVO - <strong>QLT</strong>: QUALITATIVO - <strong>PRL</strong>: PARALELA - <strong>REC</strong>: RECUPERAÇÃO DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>RF</strong>: RESULTADO FINAL</p>
</div>
<div id="status"></div>
<hr>

<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 

<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script>
<script src="../../js/jquery.mask.js"></script> 
<script type="text/javascript" src="../js/app.js"></script>

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