<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>

<?php

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
    $colname_Escola = anti_injection($_GET['escola']);
} else {
    header("Location: index.php?erro");
    exit;
}


$colname_Disciplina = "-1";
if (isset($_GET['componente'])) {
  $colname_Disciplina = anti_injection($_GET['componente']);
}


$query_Disciplina = "
  SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
  FROM smc_disciplina 
  WHERE disciplina_id = :disciplina_id";
$stmt_Disciplina = $SmecelNovo->prepare($query_Disciplina);
$stmt_Disciplina->bindParam(':disciplina_id', $colname_Disciplina, PDO::PARAM_INT);
$stmt_Disciplina->execute();
$row_Disciplina = $stmt_Disciplina->fetch(PDO::FETCH_ASSOC);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
}

try {
  // Query ajustada para validar vínculo com a escola e lotação específica na turma e disciplina
  $query_validate_access = "
      SELECT 
          e.escola_id,
          t.turma_id,
          d.disciplina_id,
          (SELECT COUNT(*) 
           FROM smc_vinculo v 
           WHERE v.vinculo_id_escola = e.escola_id 
           AND v.vinculo_id_funcionario = :professor_id 
           AND v.vinculo_status = '1') AS has_vinculo,
          (SELECT COUNT(*) 
           FROM smc_ch_lotacao_professor l 
           INNER JOIN smc_turma t2 ON t2.turma_id = l.ch_lotacao_turma_id
           WHERE l.ch_lotacao_escola = e.escola_id 
           AND l.ch_lotacao_turma_id = :turma_id
           AND l.ch_lotacao_disciplina_id = :disciplina_id
           AND l.ch_lotacao_professor_id = :professor_id
           AND t2.turma_ano_letivo = :ano_letivo) AS has_lotacao
      FROM smc_escola e
      INNER JOIN smc_turma t ON t.turma_id_escola = e.escola_id
      INNER JOIN smc_disciplina d ON d.disciplina_id = :disciplina_id
      WHERE e.escola_id = :escola_id
      AND t.turma_id = :turma_id
      AND t.turma_ano_letivo = :ano_letivo";

  $stmt_validate = $SmecelNovo->prepare($query_validate_access);
  $stmt_validate->execute([
      ':professor_id' => $row_ProfLogado['func_id'],
      ':escola_id' => $colname_Escola,
      ':turma_id' => $colname_Turma,
      ':disciplina_id' => $colname_Disciplina,
      ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano']
  ]);
  $validation_result = $stmt_validate->fetch(PDO::FETCH_ASSOC);
 
  // Verificar se o professor tem permissão (vínculo e lotação na turma e disciplina)
  if (!$validation_result || 
      $validation_result['has_vinculo'] == 0 || 
      $validation_result['has_lotacao'] == 0) {
      header("Location: index.php?permissao");
      exit;
  }
} catch (PDOException $e) {
  echo $e;
  exit;
}
$query_Turma = "
  SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
  FROM smc_turma 
  WHERE turma_id = :turma_id";
$stmt_Turma = $SmecelNovo->prepare($query_Turma);
$stmt_Turma->bindParam(':turma_id', $colname_Turma, PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);

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
  INNER JOIN smc_disciplina ON disciplina_id = :colname_Disciplina
  INNER JOIN smc_turma ON turma_id = :colname_Turma
  WHERE vinculo_aluno_id_turma = :colname_Turma AND turma_ano_letivo = :anoLetivo AND vinculo_aluno_ano_letivo = :anoLetivo
  ORDER BY aluno_nome";
$stmt_Alunos = $SmecelNovo->prepare($query_Alunos);
$stmt_Alunos->bindParam(':colname_Disciplina', $colname_Disciplina, PDO::PARAM_INT);
$stmt_Alunos->bindParam(':colname_Turma', $colname_Turma, PDO::PARAM_INT);
$stmt_Alunos->bindParam(':anoLetivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
$stmt_Alunos->execute();
$row_Alunos = $stmt_Alunos->fetch(PDO::FETCH_ASSOC);
$totalRows_Alunos = $stmt_Alunos->rowCount();

$query_Escola = "
  SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue 
  FROM smc_escola 
  WHERE escola_id = :escola_id";
$stmt_Escola = $SmecelNovo->prepare($query_Escola);
$stmt_Escola->bindParam(':escola_id', $row_Alunos['turma_id_escola'], PDO::PARAM_INT);
$stmt_Escola->execute();
$row_Escola = $stmt_Escola->fetch(PDO::FETCH_ASSOC);

$query_Matriz = "
  SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo
  FROM smc_matriz 
  WHERE matriz_id = :matriz_id";
$stmt_Matriz = $SmecelNovo->prepare($query_Matriz);
$stmt_Matriz->bindParam(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
$stmt_Matriz->execute();
$row_Matriz = $stmt_Matriz->fetch(PDO::FETCH_ASSOC);

$query_Criterios = "
  SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos, ca_rec_paralela
  FROM smc_criterios_avaliativos 
  WHERE ca_id = :ca_id";
$stmt_Criterios = $SmecelNovo->prepare($query_Criterios);
$stmt_Criterios->bindParam(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt_Criterios->execute();
$row_Criterios = $stmt_Criterios->fetch(PDO::FETCH_ASSOC);

$rec = 0;
if ($row_Criterios['ca_rec_paralela'] == "S") {
  $rec = 1;
}
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    table a {
      display: block;
      padding: 4px;
    }

    th,
    td {
      border: 1px solid #ccc;
    }

    tr,
    td {
      padding: 0;
      height: 10px;
      line-height: 10px;
    }

    .aluno {
      background-color: #ddd;
      border-radius: 0%;
      height: 40px;
      object-fit: cover;
      width: 40px;
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
        <a href="rendimento_mapa_print.php?componente=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>"
          target="_blank" class="ls-btn">IMPRIMIR</a>
      </p>

      <div class="ls-box">

        <p>COMPONENTE: <?php echo $row_Disciplina['disciplina_nome']; ?></p>
        <p>TURMA: <?php echo $row_Turma['turma_nome']; ?></p>
        <p><small>-Clique sobre a célula para lançar as notas dos alunos.<br>-Para lançar individualmente, clique sobre
            o nome do aluno.</small></p>
      </div>
      <hr>
      <?php if ($totalRows_Alunos == 0) { ?>
        NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
      <?php } else { ?>
        <br>
        <h5><?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?></h5>



        <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
          <thead>
            <tr>
              <th colspan="2" class=""></th>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                <?php
                $query_PeriodosBloqueio1 = "
                  SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, 
                        per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash
                  FROM smc_unidades
                  WHERE per_unid_id_sec = :sec_id AND per_unid_periodo = :periodo AND per_unid_id_ano = :ano_letivo_id";
                $stmt_PeriodosBloqueio1 = $SmecelNovo->prepare($query_PeriodosBloqueio1);
                $stmt_PeriodosBloqueio1->bindParam(':sec_id', $row_Secretaria['sec_id'], PDO::PARAM_INT);
                $stmt_PeriodosBloqueio1->bindParam(':periodo', $p, PDO::PARAM_INT);
                $stmt_PeriodosBloqueio1->bindParam(':ano_letivo_id', $row_AnoLetivo['ano_letivo_id'], PDO::PARAM_INT);
                $stmt_PeriodosBloqueio1->execute();
                $row_PeriodosBloqueio1 = $stmt_PeriodosBloqueio1->fetch(PDO::FETCH_ASSOC);
                $totalRows_PeriodosBloqueio1 = $stmt_PeriodosBloqueio1->rowCount();
                ?>
                <th colspan="<?php echo $row_Criterios['ca_qtd_av_periodos'] + ($row_Criterios['ca_rec_paralela'] == 'S' ? 1 : 0) + 1; ?>" class="ls-txt-center" width="15">
                  <?php echo $p; ?>º PERÍODO <br><small>LIMITE:
                    <?php if ($totalRows_PeriodosBloqueio1 > 0) {
                      echo date("d/m/y", strtotime($row_PeriodosBloqueio1['per_unid_data_bloqueio']));
                    } else {
                      echo "-";
                    } ?>
                    <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?>
                      <br><span class="">Período atingido</span>
                    <?php } ?>
                  </small>
                </th>
              <?php } ?>
              <th colspan="4" class="ls-txt-center">RESULTADO</th>
            </tr>
            <tr>
              <th colspan="2" class="ls-txt-center">IDENTIFICAÇÃO</th>
              <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
                  <th width="15" class="ls-txt-center"><?php echo $c; ?>ª</th>
                <?php } ?>
                <?php if ($row_Criterios['ca_rec_paralela'] == "S") { ?>
                  <th class="ls-txt-center" width="40">RP</th>
                <?php } ?>
                <th width="15" class="ls-txt-center">MU</th>
              <?php } ?>
              <th width="15" class="ls-txt-center">TP</th>
              <th width="15" class="ls-txt-center">MC</th>
              <th width="15" class="ls-txt-center">NR</th>
              <th width="15" class="ls-txt-center">RES</th>
            </tr>
          </thead>
          <tbody>
            <?php $num = 1; ?>
            <?php do { ?>
              <tr>
                <td width="15" class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
                <td width="150" class="" style="padding:0 5px;">
                  <a href="rendimento_aluno.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $row_Disciplina['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>&ref=mapa"
                    class="ls-ico-export">
                    <?php echo $row_Alunos['aluno_nome']; ?>
                  </a>
                  <?php if ($row_Alunos['vinculo_aluno_situacao'] != "1") { ?>
                    <br>
                    <span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
                  <?php } ?>
                </td>
                <?php $tmu = 0; ?>
                <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                  <?php $ru = 0; ?>
                  <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                    <td width="15" class="ls-txt-center">
                      <?php
                      $query_Notas = "
                        SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, 
                               nota_valor, nota_hash 
                        FROM smc_nota 
                        WHERE nota_id_matricula = :matricula_id AND nota_id_disciplina = :disciplina_id AND nota_periodo = :periodo AND nota_num_avaliacao = :avaliacao_num";
                      $stmt_Notas = $SmecelNovo->prepare($query_Notas);
                      $stmt_Notas->bindParam(':matricula_id', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
                      $stmt_Notas->bindParam(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
                      $stmt_Notas->bindParam(':periodo', $p, PDO::PARAM_INT);
                      $stmt_Notas->bindParam(':avaliacao_num', $a, PDO::PARAM_INT);
                      $stmt_Notas->execute();
                      $row_Notas = $stmt_Notas->fetch(PDO::FETCH_ASSOC);
                      $totalRows_Notas = $stmt_Notas->rowCount();
                      $ru += $row_Notas['nota_valor'];
                      ?>
                      <?php if ($row_Alunos['vinculo_aluno_situacao'] == "1") { ?>
                        <input type="text" inputmode="numeric" max="<?php echo $row_Criterios['ca_nota_max_av']; ?>"
                          notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" step="0.1"
                          name="<?php echo $row_Notas['nota_hash']; ?>" value="<?php if ($row_Notas['nota_valor'] != "") {
                            echo number_format($row_Notas['nota_valor'], $row_Criterios['ca_digitos']);
                          } ?>" notaAnterior="<?php if ($row_Notas['nota_valor'] != "") {
                            echo number_format($row_Notas['nota_valor'], $row_Criterios['ca_digitos']);
                          } ?>" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>"
                          decimal="<?php echo $row_Criterios['ca_digitos']; ?>"
                          class="ls-no-style-input ls-txt-center ls-text-md nota" style="display:block; width:100%; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) {
                            echo "color:blue;";
                          } else {
                            echo "color:red;";
                          } ?>" <?php if ($totalRows_Notas == 0) {
                            echo "disabled";
                          } ?> <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> disabled="" readonly <?php } ?>>
                      <?php } else { ?>
                        <span style="display:block; width:100%; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) {
                          echo "color:blue;";
                        } else {
                          echo "color:red;";
                        } ?>"><?php echo $row_Notas['nota_valor']; ?></span>
                      <?php } ?>
                    </td>
                  <?php } ?>
                  <?php if ($row_Criterios['ca_rec_paralela'] == "S") { ?>
                    <td width="40" class="ls-txt-center">
                      <?php
                      $query_notaRecPar = "
                        SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, 
                              nota_max, nota_min, nota_valor, nota_hash 
                        FROM smc_nota 
                        WHERE nota_id_matricula = :matricula_id 
                          AND nota_id_disciplina = :disciplina_id 
                          AND nota_periodo = :periodo 
                          AND nota_num_avaliacao = 98";
                      $stmt_notaRecPar = $SmecelNovo->prepare($query_notaRecPar);
                      $stmt_notaRecPar->bindValue(':matricula_id', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
                      $stmt_notaRecPar->bindValue(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
                      $stmt_notaRecPar->bindValue(':periodo', $p, PDO::PARAM_INT);
                      $stmt_notaRecPar->execute();
                      $row_notaRecPar = $stmt_notaRecPar->fetch(PDO::FETCH_ASSOC);
                      $totalRows_notaRecPar = $stmt_notaRecPar->rowCount();
                      ?>
                      <?php if ($row_Alunos['vinculo_aluno_situacao'] == "1") { ?>
                        <input type="text" inputmode="numeric" max="<?php echo $row_Criterios['ca_nota_max_av']; ?>"
                          notaMin="<?php echo $row_Criterios['ca_nota_min_recuperacao_final']; ?>" step="0.1"
                          name="<?php echo $row_notaRecPar['nota_hash']; ?>" value="<?php if ($row_notaRecPar['nota_valor'] <> "") {
                               echo number_format($row_notaRecPar['nota_valor'], $row_Criterios['ca_digitos']);
                             } ?>" notaAnterior="<?php if ($row_notaRecPar['nota_valor'] <> "") {
                                echo number_format($row_notaRecPar['nota_valor'], $row_Criterios['ca_digitos']);
                              } ?>" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>"
                          decimal="<?php echo $row_Criterios['ca_digitos']; ?>"
                          recParalela="S"
                          class="ls-no-style-input ls-txt-center ls-text-md nota" style=" display:block; width:100%; <?php if ($row_notaRecPar['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) {
                            echo "; color:blue;";
                          } else {
                            echo "; color:red;";
                          }
                          ; ?>"
                           <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?> disabled="" readonly <?php } ?>>
                      <?php } else { ?>
                        <span style=" display:block; width:100%; <?php if ($row_notaRecPar['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) {
                          echo "; color:blue;";
                        } else {
                          echo "; color:red;";
                        }
                        ; ?>"><?php echo $row_notaRecPar['nota_valor']; ?></span>
                      <?php } ?>
                    </td>
                  <?php } ?>
                  <td width="15" class="ls-txt-center">
                    <?php 
                    $mu = mediaUnidadeSemExibicao($ru, $row_Criterios['ca_arredonda_media'], $row_Criterios['ca_aproxima_media'], $row_Criterios['ca_media_min_periodo'], $row_Criterios['ca_calculo_media_periodo'], $row_Criterios['ca_qtd_av_periodos'], $row_Criterios['ca_digitos']); 
                    
                    // Verifica se tem recuperação paralela e se é maior que a média
                    if($row_Criterios['ca_rec_paralela'] == "S" && $row_notaRecPar && $row_notaRecPar['nota_valor'] !== null) {
                      $notaRecPar = $row_notaRecPar['nota_valor'];
                      if ($notaRecPar > $mu) {
                        $mu = $notaRecPar;
                      }
                    }

                    // Exibe o valor formatado
                    if ($mu == 0) {
                      echo "-";
                    } else {
                      if ($mu < $row_Criterios['ca_media_min_periodo']) {
                        echo "<span style='color:red;'>".number_format($mu, $row_Criterios['ca_digitos'], '.', '')."</span>";
                      } else {
                        echo "<span style='color:blue;'>".number_format($mu, $row_Criterios['ca_digitos'], '.', '')."</span>";
                      }
                    }
                    $tmu = $tmu + $mu;
                    ?>
                  </td>
                <?php } ?>
                <td width="15" class="ls-txt-center">
                  <span class="ls-text-md"><?php $tp = totalPontos($tmu, $row_Criterios['ca_digitos']); ?></span>
                </td>
                <td width="15" class="ls-txt-center">
                  <span class="ls-text-md"><?php $mc = mediaCurso($tp, $row_Criterios['ca_arredonda_media'], $row_Criterios['ca_aproxima_media'], $row_Criterios['ca_min_media_aprovacao_final'], $row_Criterios['ca_qtd_periodos'], $row_Criterios['ca_digitos']); ?></span>
                </td>
                <td width="15" class="ls-txt-center">
                  <?php
                  $query_notaAf = "
                  SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, 
                        nota_min, nota_valor, nota_hash 
                  FROM smc_nota 
                  WHERE nota_id_matricula = :matricula_id 
                    AND nota_id_disciplina = :disciplina_id 
                    AND nota_periodo = 99 
                    AND nota_num_avaliacao = 99";
                  $stmt_notaAf = $SmecelNovo->prepare($query_notaAf);
                  $stmt_notaAf->bindParam(':matricula_id', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
                  $stmt_notaAf->bindParam(':disciplina_id', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
                  $stmt_notaAf->execute();
                  $row_notaAf = $stmt_notaAf->fetch(PDO::FETCH_ASSOC);
                  $totalRows_notaAf = $stmt_notaAf->rowCount();
                  $af = avaliacaoFinal($row_notaAf['nota_valor'], $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_digitos']);
                  ?>
                  <span class="ls-text-md"><?php echo $row_notaAf['nota_valor']; ?></span>
                </td>
                <td width="15" class="ls-txt-center">
                  <?php
                  $resultado = resultadoFinal($mc, $af, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_min_media_aprovacao_final'], $row_Criterios['ca_digitos']);

                  if ($resultado == "APR") {
                    echo "<small class='light-green lighten-2'>APR</small>";
                  } else {
                    echo "<small class='pink accent-1'>CON</small>";
                  }
                  ?>
                </td>
              </tr>
              <?php } while ($row_Alunos = $stmt_Alunos->fetch(PDO::FETCH_ASSOC)); ?>
          <?php } ?>
        </tbody>
      </table>
      <br>
      <p> LEGENDA: </p>
      <p> <strong>MU</strong>: MÉDIA DA UNIDADE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>MC</strong>: MÉDIA DO
        CURSO - <strong>NR</strong>: NOTA DE RECUPERAÇÃO - <strong>RES</strong>: RESULTADO FINAL </p>
    </div>
    <div id="status"></div>
    <hr>

    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
  <script src="../../js/jquery.mask.js"></script>
  <script type="text/javascript" src="../js/app.js"></script>

  <script type="text/javascript">

$(document).ready(function(){
  <?php if ($row_Criterios['ca_digitos']=="1") { ?>
  $('.nota').mask('00.0', {reverse: true});
  <?php } else { ?>
    $('.nota').mask('00.00', {reverse: true});
  <?php } ?> 
  $('.money').mask('000.000.000.000.000,00', {reverse: true});
});			
				
        $(document).ready(function(){
        $("input").blur(function(){
        
          //var valor = parseFloat($(this).val());
          var id 				    = $(this).attr('name');
          var valor 			  = $(this).val();
          var notaAnterior 	= $(this).attr('notaAnterior');
          var notaMax 		  = $(this).attr('max');
          var notaMin 		  = $(this).attr('notaMin');
          var disciplina 		= $(this).attr('disciplina');
          var turma         = $(this).attr('turma');
          var escola        = $(this).attr('escola');
          var decimal       = $(this).attr('decimal');
          var consolidado   = $(this).attr('consolidado');
          var recParalela   = $(this).attr('recParalela');
        
          //var id = $(this).attr('name');
          //var valor = parseFloat($(this).val());
          //var notaAnterior = parseFloat($(this).attr('notaAnterior'));
          //var notaMax = parseFloat($(this).attr('max'));
          //var notaMin = parseFloat($(this).attr('notaMin'));
          //var disciplina = $(this).attr('disciplina');
          //var turma = $(this).attr('turma');
          //var escola = $(this).attr('escola');
          //var decimal       = $(this).attr('decimal');
          //var consolidado   = $(this).attr('consolidado');
        
          var valor1 = parseFloat(valor);
          var notaMin1 = parseFloat(notaMin);
        
          
          if (valor1 < notaMin1) {
            $(this).css("color", "red");
            } else {
              $(this).css("color", "blue");
              }
          
          
          if (valor != notaAnterior) {
          $.ajax({
            type : 'POST',
              url  : 'fnc/lancaNota.php',
              data : {
              id				:id,
              valor			:valor,
              notaMax			:notaMax,
              notaAnterior	:notaAnterior,
              disciplina		:disciplina,
              turma:turma,
              escola:escola,
              decimal:decimal,
              consolidado:consolidado,
              recParalela:recParalela
              },
              success:function(data){
                $('#status').html(data);
                
                setTimeout(function(){
                    $("#status").html("");					
                  },15000);
                
                }
            })
          }
          
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