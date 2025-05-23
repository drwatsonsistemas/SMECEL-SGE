<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
<?php require_once "fnc/notasQQ.php"; ?>
<?php
$colname_Disciplina = "-1";
if (isset($_GET['componente'])) {
    $colname_Disciplina = $_GET['componente'];
}

$query_Disciplina = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, 
                    disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
                    FROM smc_disciplina WHERE disciplina_id = :disciplina_id";
$stmt = $SmecelNovo->prepare($query_Disciplina);
$stmt->bindValue(':disciplina_id', (int)$colname_Disciplina, PDO::PARAM_INT);
$stmt->execute();
$row_Disciplina = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Disciplina = $stmt->rowCount();

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
    $colname_Turma = $_GET['turma'];
}

$query_Turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, 
                turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
                FROM smc_turma WHERE turma_id = :turma_id";
$stmt = $SmecelNovo->prepare($query_Turma);
$stmt->bindValue(':turma_id', (int)$colname_Turma, PDO::PARAM_INT);
$stmt->execute();
$row_Turma = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Turma = $stmt->rowCount();

$query_Alunos = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, 
           vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
           vinculo_aluno_total_pontos_qq, aluno_id, aluno_nome, aluno_foto, 
           disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, 
           turma_ano_letivo,
           CASE vinculo_aluno_situacao
               WHEN 1 THEN 'MATRICULADO'
               WHEN 2 THEN 'TRANSFERIDO(A)'
               WHEN 3 THEN 'DESISTENTE'
               WHEN 4 THEN 'FALECIDO(A)'
               WHEN 5 THEN 'OUTROS'
           END AS vinculo_aluno_situacao_nome  
    FROM smc_vinculo_aluno
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_disciplina ON disciplina_id = :disciplina_id
    INNER JOIN smc_turma ON turma_id = :turma_id
    WHERE vinculo_aluno_id_turma = :turma_id2 
    AND turma_ano_letivo = :ano_letivo 
    AND vinculo_aluno_ano_letivo = :ano_letivo2
    ORDER BY aluno_nome";
$stmt_Alunos  = $SmecelNovo->prepare($query_Alunos);
$stmt_Alunos ->bindValue(':disciplina_id', (int)$colname_Disciplina, PDO::PARAM_INT);
$stmt_Alunos ->bindValue(':turma_id', $colname_Turma, PDO::PARAM_STR);
$stmt_Alunos ->bindValue(':turma_id2', $colname_Turma, PDO::PARAM_STR);
$stmt_Alunos ->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_STR);
$stmt_Alunos ->bindValue(':ano_letivo2', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_STR);
$stmt_Alunos ->execute();
$row_Alunos = $stmt_Alunos ->fetch(PDO::FETCH_ASSOC);
$totalRows_Alunos = $stmt_Alunos->rowCount();

if ($totalRows_Alunos == 0) {
    //header("Location:index.php?erro");
}

$query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, 
                escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, 
                escola_inep, escola_cnpj, escola_logo, escola_ue 
                FROM smc_escola WHERE escola_id = :escola_id";
$stmt = $SmecelNovo->prepare($query_Escola);
$stmt->bindValue(':escola_id', $row_Alunos['turma_id_escola'], PDO::PARAM_STR);
$stmt->execute();
$row_Escola = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Escola = $stmt->rowCount();

if ($totalRows_Escola == "") {
    //header("Location:../index.php?loginErr");
}

$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, 
                matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, 
                matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, 
                matriz_aula_dia, matriz_criterio_avaliativo 
                FROM smc_matriz WHERE matriz_id = :matriz_id";
$stmt = $SmecelNovo->prepare($query_Matriz);
$stmt->bindValue(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_STR);
$stmt->execute();
$row_Matriz = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Matriz = $stmt->rowCount();

$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, 
                   ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, 
                   ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, 
                   ca_aproxima_media, ca_min_pontos_aprovacao_final, 
                   ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, 
                   ca_detalhes, ca_digitos 
                   FROM smc_criterios_avaliativos WHERE ca_id = :ca_id";
$stmt = $SmecelNovo->prepare($query_Criterios);
$stmt->bindValue(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_STR);
$stmt->execute();
$row_Criterios = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Criterios = $stmt->rowCount();

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
            - Primeiro deve ser lançado as notas do quantitativo e qualitativo, e por último a nota paralela, pois esta depende da soma dos dois.<br>
            - O campo de recuperação ficará disponível caso o aluno seja reprovado em um dos trimestres. No entanto, se o aluno for aprovado na soma das médias dos três trimestres, ele estará dispensado de realizar a recuperação e não deverá ser inserida nenhuma nota no campo de recuperação.
          </div>
        </div>
        <br>

        <div class="ls-box">
          <p><small>Ao registrar as notas, clique em "REGISTRAR NOTAS" para registrar as notas dos alunos.</small></p>
          <a onclick="window.location.reload();" class="ls-btn-primary">REGISTRAR NOTAS</a> 
      </div>
      <br>
      
        <h5><?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?></h5>
      

        <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto" width="100%">
  <thead>
    <tr class="">
      <th colspan="3" class=""></th>
      <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
        <?php
        $query_PeriodosBloqueio1 = "
          SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, 
                 per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash 
          FROM smc_unidades
          WHERE per_unid_id_sec = :idSec AND per_unid_periodo = :periodo AND per_unid_id_ano = :idAno";
        $stmt_PeriodosBloqueio1 = $SmecelNovo->prepare($query_PeriodosBloqueio1);
        $stmt_PeriodosBloqueio1->bindParam(':idSec', $row_Secretaria['sec_id'], PDO::PARAM_INT);
        $stmt_PeriodosBloqueio1->bindParam(':periodo', $p, PDO::PARAM_INT);
        $stmt_PeriodosBloqueio1->bindParam(':idAno', $row_AnoLetivo['ano_letivo_id'], PDO::PARAM_INT);
        $stmt_PeriodosBloqueio1->execute();
        $row_PeriodosBloqueio1 = $stmt_PeriodosBloqueio1->fetch(PDO::FETCH_ASSOC);
        $totalRows_PeriodosBloqueio1 = $stmt_PeriodosBloqueio1->rowCount();
        ?>
        <th colspan="4" class="ls-txt-center" width="15"><?php echo $p; ?>º PERÍODO <br>
          <small>LIMITE: <?php echo ($totalRows_PeriodosBloqueio1 > 0) ? date("d/m/y", strtotime($row_PeriodosBloqueio1['per_unid_data_bloqueio'])) : "-"; ?>
            <?php if (($totalRows_PeriodosBloqueio1 > 0) && ($row_PeriodosBloqueio1['per_unid_data_bloqueio'] < date("Y-m-d"))) { ?>
              <br><span class="">Período atingido</span>
            <?php } ?>
          </small>
        </th>
      <?php } ?>
      <th colspan="2" class="ls-txt-center">RESULTADO</th>
    </tr>
    <tr class="">
      <th colspan="3" class="ls-txt-center">IDENTIFICAÇÃO</th>
      <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
        <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">QNT.</th>
        <th width="15" style="background-color:#e4e9ec" class="ls-txt-center">QLT.</th>
        <th width="15" style="background-color:#cbd3da" class="ls-txt-center">PRL.</th>
        <th width="15" style="background-color:#a0a0a0" class="ls-txt-center"><span style="color:#fe4127">REC.</span></th>
      <?php } ?>
      <th width="15" style="background-color:#ccde9e" class="ls-txt-center">TP.</th>
      <th width="15" style="background-color:#ccde9e" class="ls-txt-center">RF.</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Inicialização fora do loop
    $alunos_na_paralela = [];
    $alunos_aprovados_paralela = [];
    $alunos_reprovados_paralela = [];
    $alunos_aprovados = [];
    $num = 1;

    // Certifique-se de que $stmt_Alunos está definido antes
    do {
    ?>
      <tr>
        <td width="15" class="ls-txt-center"><strong><?php echo $num++; ?></strong></td>
        <td width="40" class="" style="padding:0 5px;">
          <?php if (empty($row_Alunos['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE . '/aluno/fotos/semfoto.jpg'; ?>" class="aluno ls-txt-left ls-float-left" border="0" width="100%">
          <?php } else { ?>
            <img src="<?php echo URL_BASE . '/aluno/fotos/' . $row_Alunos['aluno_foto']; ?>" class="hoverable aluno ls-float-left" border="0" width="100%">
          <?php } ?>
        </td>
        <td width="150" class="" style="padding:0 5px;">
          <a href="qq_aluno.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $row_Disciplina['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>&ref=mapa" class="ls-ico-export">
            <?php echo $row_Alunos['aluno_nome']; ?>
          </a>
          <?php if ($row_Alunos['vinculo_aluno_situacao'] != "1") { ?>
            <br><span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
          <?php } ?>
        </td>
        <?php
        $pontuacaoTotalAnoLetivo1 = 0;
        $pontuacaoTotalAnoLetivo2 = 0;
        $pontuacaoTotalAnoLetivo3 = 0;
        $aluno_periodo_atende_condicao_reprovado = false;
        for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) {
          $somaPontuacaoQualitativo = 0;
        $somaPontuacaoQuantitativo = 0;
        $paralela = 0;
        $recuperacao = 0;
        $aluno_periodo_atende_condicao_paralela = false;
        $aluno_periodo_atende_condicao_reprovado = false;
        $aluno_periodo_atende_condicao_aprovado = false;
        $aluno_periodo_atende_condicao_aprovado_paralela = false;

          // Consulta PeriodosBloqueio
          $query_PeriodosBloqueio = "
            SELECT per_unid_id, per_unid_id_ano, per_unid_id_sec, per_unid_periodo, per_unid_data_inicio, 
                   per_unid_data_fim, per_unid_data_bloqueio, per_unid_hash 
            FROM smc_unidades
            WHERE per_unid_id_sec = :secId AND per_unid_periodo = :periodo AND per_unid_id_ano = :anoLetivoId";
          $stmt_PeriodosBloqueio = $SmecelNovo->prepare($query_PeriodosBloqueio);
          $stmt_PeriodosBloqueio->bindParam(':secId', $row_Secretaria['sec_id'], PDO::PARAM_INT);
          $stmt_PeriodosBloqueio->bindParam(':periodo', $p, PDO::PARAM_INT);
          $stmt_PeriodosBloqueio->bindParam(':anoLetivoId', $row_AnoLetivo['ano_letivo_id'], PDO::PARAM_INT);
          $stmt_PeriodosBloqueio->execute();
          $row_PeriodosBloqueio = $stmt_PeriodosBloqueio->fetch(PDO::FETCH_ASSOC);
          $totalRows_PeriodosBloqueio = $stmt_PeriodosBloqueio->rowCount();

          // Consulta Qualitativo
          $query_qualitativo = "SELECT qq_nota FROM smc_notas_qq 
                                WHERE qq_id_matricula = :matricula AND qq_id_componente = :componente 
                                AND qq_id_periodo = :periodo AND qq_tipo_criterio = '1'";
          $stmt = $SmecelNovo->prepare($query_qualitativo);
          $stmt->bindValue(':matricula', (int)$row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
          $stmt->bindValue(':componente', (int)$row_Disciplina['disciplina_id'], PDO::PARAM_INT);
          $stmt->bindValue(':periodo', (int)$p, PDO::PARAM_INT);
          $stmt->execute();
          $totalRows_qualitativo = $stmt->rowCount();
          while ($row_qualitativo = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $somaPontuacaoQualitativo += (float)$row_qualitativo['qq_nota'];
          }

          // Consulta Quantitativo
          $query_quantitativo = "SELECT qq_nota FROM smc_notas_qq 
                                 WHERE qq_id_matricula = :matricula AND qq_id_componente = :componente 
                                 AND qq_id_periodo = :periodo AND qq_tipo_criterio = '2'";
          $stmt = $SmecelNovo->prepare($query_quantitativo);
          $stmt->bindValue(':matricula', (int)$row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
          $stmt->bindValue(':componente', (int)$row_Disciplina['disciplina_id'], PDO::PARAM_INT);
          $stmt->bindValue(':periodo', (int)$p, PDO::PARAM_INT);
          $stmt->execute();
          $totalRows_quantitativo = $stmt->rowCount();
          while ($row_quantitativo = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $somaPontuacaoQuantitativo += (float)$row_quantitativo['qq_nota'];
          }

          // Consulta Paralela
          $query_paralela = "SELECT qq_nota FROM smc_notas_qq 
                             WHERE qq_id_matricula = :matricula AND qq_id_componente = :componente 
                             AND qq_id_periodo = :periodo AND qq_tipo_criterio = '3'";
          $stmt = $SmecelNovo->prepare($query_paralela);
          $stmt->bindValue(':matricula', (int)$row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
          $stmt->bindValue(':componente', (int)$row_Disciplina['disciplina_id'], PDO::PARAM_INT);
          $stmt->bindValue(':periodo', (int)$p, PDO::PARAM_INT);
          $stmt->execute();
          $row_paralela = $stmt->fetch(PDO::FETCH_ASSOC);
          $totalRows_paralela = $stmt->rowCount();
          $paralela = $row_paralela ? floatval($row_paralela['qq_nota']) : 0;

          // Consulta Recuperação Final
          $query_recuperacao = "SELECT qq_nota FROM smc_notas_qq 
                                WHERE qq_id_matricula = :matricula AND qq_id_componente = :componente 
                                AND qq_id_periodo = :periodo AND qq_tipo_criterio = '4'";
          $stmt = $SmecelNovo->prepare($query_recuperacao);
          $stmt->bindValue(':matricula', (int)$row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
          $stmt->bindValue(':componente', (int)$row_Disciplina['disciplina_id'], PDO::PARAM_INT);
          $stmt->bindValue(':periodo', (int)$p, PDO::PARAM_INT);
          $stmt->execute();
          $row_recuperacao = $stmt->fetch(PDO::FETCH_ASSOC);
          $totalRows_recuperacao = $stmt->rowCount();
          $recuperacao = $row_recuperacao ? floatval($row_recuperacao['qq_nota']) : 0;

          if ($p == 1 || $p == 2) {
            $pMaxQuantitativa = 13.5;
            $pMaxQualitativa = 16.5;
            $pMaxParalela = 13.5;
            $pMaxRecuperacao = 30;
            $minQuantitativo = 8.1;
            $minTrimestre = 18;

            $totalTrimestre = $somaPontuacaoQualitativo + $somaPontuacaoQuantitativo;
            if ($totalRows_paralela > 0 && $paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
            }
            $totalTrimestre = arredondarNota($totalTrimestre);
            $pontuacaoTotalAnoLetivo = ($p == 1) ? 'pontuacaoTotalAnoLetivo1' : 'pontuacaoTotalAnoLetivo2';
            $$pontuacaoTotalAnoLetivo = $totalTrimestre;

            //echo "Período $p - Antes da Lógica: Total Trimestre: " . $totalTrimestre . " - Aluno: " . $row_Alunos['aluno_nome'] . "<br>";

            if ($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0) {
              if ($totalRows_recuperacao > 0) {
                $totalTrimestre = $recuperacao;
                $totalTrimestre = arredondarNota($totalTrimestre);
                $$pontuacaoTotalAnoLetivo = $totalTrimestre;

                if ($totalTrimestre < $minTrimestre) {
                  $aluno_periodo_atende_condicao_reprovado = true;
                  $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                } else {
                  $aluno_periodo_atende_condicao_reprovado = false;
                  $aluno_periodo_atende_condicao_aprovado = true;
                  $alunos_aprovados[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                }
              } else {
                if ($somaPontuacaoQuantitativo < $minQuantitativo && $totalTrimestre < $minTrimestre) {
                  if ($totalRows_paralela > 0 && $paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                    $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                    $totalTrimestre = arredondarNota($totalTrimestre);
                    $$pontuacaoTotalAnoLetivo = $totalTrimestre;

                    if ($totalTrimestre >= $minTrimestre) {
                      $aluno_periodo_atende_condicao_aprovado_paralela = true;
                      $alunos_aprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                      $aluno_periodo_atende_condicao_reprovado = false;
                    } else {
                      $aluno_periodo_atende_condicao_reprovado = true;
                      $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                    }
                  } else {
                    $aluno_periodo_atende_condicao_paralela = true;
                    $alunos_na_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                    $aluno_periodo_atende_condicao_reprovado = true;
                  }
                } elseif ($somaPontuacaoQuantitativo >= $minQuantitativo && $totalTrimestre >= $minTrimestre) {
                  $aluno_periodo_atende_condicao_aprovado = true;
                  $alunos_aprovados[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                  $aluno_periodo_atende_condicao_reprovado = false;
                } else {
                  $aluno_periodo_atende_condicao_reprovado = true;
                  $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                }
              }
            }
          }

          if ($p == 3) {
            $pMaxQuantitativa = 18;
            $pMaxQualitativa = 22;
            $pMaxParalela = 18;
            $pMaxRecuperacao = 40;
            $minQuantitativo = 10.8;
            $minTrimestre = 24;

            $totalTrimestre = $somaPontuacaoQualitativo + $somaPontuacaoQuantitativo;
            if ($totalRows_paralela > 0 && $paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
            }
            $totalTrimestre = arredondarNota($totalTrimestre);
            $pontuacaoTotalAnoLetivo3 = $totalTrimestre;

            //echo "Período $p - Antes da Lógica: Total Trimestre: " . $totalTrimestre . " - Aluno: " . $row_Alunos['aluno_nome'] . "<br>";

            if ($totalRows_qualitativo > 0 || $totalRows_quantitativo > 0) {
              if ($totalRows_recuperacao > 0) {
                $totalTrimestre = $recuperacao;
                $totalTrimestre = arredondarNota($totalTrimestre);
                $pontuacaoTotalAnoLetivo3 = $totalTrimestre;

                if ($totalTrimestre < $minTrimestre) {
                  $aluno_periodo_atende_condicao_reprovado = true;
                  $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                } else {
                  $aluno_periodo_atende_condicao_reprovado = false;
                  $aluno_periodo_atende_condicao_aprovado = true;
                  $alunos_aprovados[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                }
              } else {
                if ($somaPontuacaoQuantitativo < $minQuantitativo && $totalTrimestre < $minTrimestre) {
                  if ($totalRows_paralela > 0 && $paralela > 0 && $paralela > $somaPontuacaoQuantitativo) {
                    $totalTrimestre = $somaPontuacaoQualitativo + $paralela;
                    $totalTrimestre = arredondarNota($totalTrimestre);
                    $pontuacaoTotalAnoLetivo3 = $totalTrimestre;

                    if ($totalTrimestre >= $minTrimestre) {
                      $aluno_periodo_atende_condicao_aprovado_paralela = true;
                      $alunos_aprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                      $aluno_periodo_atende_condicao_reprovado = false;
                    } else {
                      $aluno_periodo_atende_condicao_reprovado = true;
                      $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                    }
                  } else {
                    $aluno_periodo_atende_condicao_paralela = true;
                    $alunos_na_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                    $aluno_periodo_atende_condicao_reprovado = true;
                  }
                } elseif ($somaPontuacaoQuantitativo >= $minQuantitativo && $totalTrimestre >= $minTrimestre) {
                  $aluno_periodo_atende_condicao_aprovado = true;
                  $alunos_aprovados[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                  $aluno_periodo_atende_condicao_reprovado = false;
                } else {
                  $aluno_periodo_atende_condicao_reprovado = true;
                  $alunos_reprovados_paralela[] = ['aluno_id' => $row_Alunos['vinculo_aluno_id'], 'periodo' => $p];
                }
              }
            }
          }
        ?>
          <td width="15" class="ls-txt-center">
        <input style="width: 35px" type="text" inputmode="numeric" class="ls-no-style-input ls-txt-center ls-text-md nota" 
               periodo="<?= $p ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" 
               aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" name="quantitativo" 
               pontuacaoMax="<?= $pMaxQuantitativa ?>" value="<?= number_format($somaPontuacaoQuantitativo, 2, '.', ''); ?>" 
               <?php if ($totalRows_PeriodosBloqueio > 0 && $row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d")) { ?>disabled="" readonly<?php } ?>>
      </td>
      <input type="hidden" name="aluno_id" value="<?= $row_Alunos['vinculo_aluno_id'] ?>">
      <td width="15" class="ls-txt-center">
        <input style="width: 35px" type="text" inputmode="numeric" class="ls-no-style-input ls-txt-center ls-text-md nota" 
               periodo="<?= $p ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" 
               aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" name="qualitativo" 
               pontuacaoMax="<?= $pMaxQualitativa ?>" value="<?= number_format($somaPontuacaoQualitativo, 2, '.', ''); ?>" 
               <?php if ($totalRows_PeriodosBloqueio > 0 && $row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d")) { ?>disabled="" readonly<?php } ?>>
      </td>
      <td width="15" class="ls-txt-center" 
          <?php if (!$aluno_periodo_atende_condicao_paralela) { echo 'style="background-color: #CCCCCC"'; } ?>>
        <input style="width: 35px" type="text" inputmode="numeric" class="ls-no-style-input ls-txt-center ls-text-md nota" 
              periodo="<?= $p ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" 
              aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" name="paralela" 
              id="paralela_<?= $row_Alunos['vinculo_aluno_id'] ?>_<?= $p ?>" 
              pontuacaoMax="<?= $pMaxParalela ?>" value="<?= number_format($paralela, 2, '.', ''); ?>" 
              <?php if (!$aluno_periodo_atende_condicao_paralela || 
                        ($totalRows_PeriodosBloqueio > 0 && $row_PeriodosBloqueio['per_unid_data_bloqueio'] < date("Y-m-d"))) { 
                  ?>disabled="" readonly<?php } ?>>
      </td>
      <td width="15" class="ls-txt-center" 
    <?php if (!$aluno_periodo_atende_condicao_reprovado || $totalRows_recuperacao > 1) { echo 'style="background-color: #CCCCCC"'; } ?>>
  <input style="width: 35px" type="text" inputmode="numeric" class="ls-no-style-input ls-txt-center ls-text-md nota" 
         periodo="<?= $p ?>" componente="<?php echo $row_Disciplina['disciplina_id']; ?>" 
         aluno_id="<?= $row_Alunos['vinculo_aluno_id'] ?>" name="recuperacaoFinal" 
         pontuacaoMax="<?= $pMaxRecuperacao ?>" value="<?= number_format($recuperacao, 2, '.', ''); ?>" 
         <?php if (!$aluno_periodo_atende_condicao_reprovado || $totalRows_recuperacao > 1) { ?>disabled="" readonly<?php } ?>>
</td>
        <?php } ?>
        <?php 
    $pontuacaoTotalAno = $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3;
    if ($pontuacaoTotalAno != $row_Alunos['vinculo_aluno_total_pontos_qq']) {
        $updateQuery = "UPDATE smc_vinculo_aluno 
                        SET vinculo_aluno_total_pontos_qq = :pontuacaoTotal 
                        WHERE vinculo_aluno_id = :alunoId";
        $stmtUpdate = $SmecelNovo->prepare($updateQuery);
        $stmtUpdate->bindParam(':pontuacaoTotal', round($pontuacaoTotalAno, 2), PDO::PARAM_STR);
        $stmtUpdate->bindParam(':alunoId', $row_Alunos['vinculo_aluno_id'], PDO::PARAM_INT);
        $stmtUpdate->execute();
    }
    ?>
        <td width="15" class="ls-txt-center"><span class="ls-text-md"><?= number_format($pontuacaoTotalAno, 2, '.', ''); ?></span></td>
    <td width="15" class="ls-txt-center" disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>">
      <span class="ls-text-md">
        <?php
        if (!empty($row_AnoLetivo['ano_letivo_resultado_final']) && $row_AnoLetivo['ano_letivo_resultado_final'] <= date("Y-m-d")) {
            echo ($pontuacaoTotalAno < 60) ? "<span style='color: red;'>REP</span>" : "<span style='color: green;'>APR</span>";
        } else {
            echo '-';
        }
        ?>
      </span>
    </td>
      </tr>
    <?php } while ($row_Alunos = $stmt_Alunos->fetch(PDO::FETCH_ASSOC)); ?>
            <?php } ?>
    <?php
    // Scripts para estilização
    echo '<script>$(document).ready(function() {';
    foreach ($alunos_na_paralela as $aluno_info) {
      echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_info['aluno_id'] . '\'][periodo=\'' . $aluno_info['periodo'] . '\']").addClass("nota-paralela");';
      echo '$("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_info['aluno_id'] . '\'][periodo=\'' . $aluno_info['periodo'] . '\']").addClass("nota-paralela");';
    }
    foreach ($alunos_aprovados_paralela as $aluno_apr_prl) {
      echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");';
      echo '$("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");';
      echo '$("input[name=\'paralela\'][aluno_id=\'' . $aluno_apr_prl['aluno_id'] . '\'][periodo=\'' . $aluno_apr_prl['periodo'] . '\']").addClass("nota-paralela-apr");';
    }
    foreach ($alunos_reprovados_paralela as $aluno_rep_prl) {
      echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");';
      echo '$("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");';
      echo '$("input[name=\'paralela\'][aluno_id=\'' . $aluno_rep_prl['aluno_id'] . '\'][periodo=\'' . $aluno_rep_prl['periodo'] . '\']").addClass("nota-paralela-rep");';
    }
    foreach ($alunos_aprovados as $aluno_apr) {
      echo '$("input[name=\'qualitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");';
      echo '$("input[name=\'quantitativo\'][aluno_id=\'' . $aluno_apr['aluno_id'] . '\'][periodo=\'' . $aluno_apr['periodo'] . '\']").addClass("nota-apr");';
    }
    echo '});</script>';
    ?>
  </tbody>
  <script type="text/javascript">
$(document).ready(function() {
    // Configuração do Toast com Swal.mixin
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    // Liberar campos de paralela
    $('#liberarParalela').on('click', function() {
        $('input[name="paralela"]').each(function() {
            $(this).removeAttr('disabled').removeAttr('readonly');
            $(this).closest('td').css('background-color', '');
        });
        Toast.fire({
            icon: 'info',
            title: 'Campos de paralela liberados'
        });
    });

    // Liberar campos de recuperação final
    $('#liberarRecuperacao').on('click', function() {
        $('input[name="recuperacaoFinal"]').each(function() {
            $(this).removeAttr('disabled').removeAttr('readonly');
            $(this).closest('td').css('background-color', '');
        });
        Toast.fire({
            icon: 'info',
            title: 'Campos de recuperação liberados'
        });
    });

    // Máscara e evento de foco
    $('input.nota').off('focus').on('focus', function() {
        $(this).data('valorAnterior', $(this).val());
    });

    // Evento de blur para lançamento de notas
    $('input.nota').on('blur', function() {
        var tipo = $(this).attr('name');
        var periodo = $(this).attr('periodo');
        var componente = $(this).attr('componente');
        var valor = $(this).val();
        var valorAnterior = $(this).data('valorAnterior');
        var alunoId = $(this).attr('aluno_id');
        var pontuacaoMax = $(this).attr('pontuacaoMax');
        var $input = $(this);

        if (valor !== valorAnterior) {
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
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var $row = $input.closest('tr');
                        $row.find('.ls-text-md').eq(0).text(response.total_pontos); // Atualiza TP
                        $row.find('.ls-text-md').eq(1).html(response.resultado_final); // Atualiza RF

                        // Atualiza status
                        $row.find('input.nota').removeClass('nota-paralela nota-paralela-apr nota-paralela-rep nota-apr');
                        if (response.status) {
                            $row.find('input.nota[periodo="' + periodo + '"]').addClass(response.status);
                        }

                        // Libera/desabilita campos paralela e recuperação
                        var $paralelaInput = $row.find('input[name="paralela"][periodo="' + periodo + '"]');
                        var $recuperacaoInput = $row.find('input[name="recuperacaoFinal"][periodo="' + periodo + '"]');

                        if (response.liberarParalela) {
                            $paralelaInput.removeAttr('disabled').removeAttr('readonly');
                            $paralelaInput.closest('td').css('background-color', '');
                        } else {
                            $paralelaInput.attr('disabled', 'disabled').attr('readonly', 'readonly');
                            $paralelaInput.closest('td').css('background-color', '#CCCCCC');
                        }

                        if (response.liberarRecuperacao) {
                            $recuperacaoInput.removeAttr('disabled').removeAttr('readonly');
                            $recuperacaoInput.closest('td').css('background-color', '');
                        } else {
                            $recuperacaoInput.attr('disabled', 'disabled').attr('readonly', 'readonly');
                            $recuperacaoInput.closest('td').css('background-color', '#CCCCCC');
                        }

                        // Toast de sucesso
                        Toast.fire({
                            icon: 'success',
                            title: 'Nota registrada com sucesso'
                        });
                    } else {
                        // Toast de erro
                        Toast.fire({
                            icon: 'error',
                            title: response.message || 'Erro ao salvar a nota'
                        });
                        $input.val(valorAnterior);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Erro AJAX:', xhr.status, status, error);
                    // Toast de erro de comunicação
                    Toast.fire({
                        icon: 'error',
                        title: 'Falha na comunicação com o servidor'
                    });
                    $input.val(valorAnterior);
                }
            });
        }
    });
});
</script>
<br><br>
<p> LEGENDA: </p>
<p> <strong>QNT</strong>: QUANTITATIVO - <strong>QLT</strong>: QUALITATIVO - <strong>PRL</strong>: PARALELA - <strong>REC</strong>: RECUPERAÇÃO DO TRIMESTRE - <strong>TP</strong>: TOTAL DE PONTOS - <strong>RF</strong>: RESULTADO FINAL</p>
<br><br> <button id="liberarParalela" class="ls-btn-primary">LIBERAR CAMPOS DE PARALELA</button>
        <button id="liberarRecuperacao" class="ls-btn-primary">LIBERAR CAMPOS DE RECUPERAÇÃO FINAL</button>
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