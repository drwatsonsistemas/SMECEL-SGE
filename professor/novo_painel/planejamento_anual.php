<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

try {
  // Consulta Etapa
  $stmtEtapa = $SmecelNovo->prepare("
      SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
      turma_id, turma_ano_letivo, turma_etapa, etapa_id, etapa_nome, etapa_nome_abrev 
      FROM smc_ch_lotacao_professor
      INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
      INNER JOIN smc_etapa ON etapa_id = turma_etapa
      WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
      GROUP BY etapa_id
  ");
  $stmtEtapa->execute([':professor_id' => ID_PROFESSOR, ':ano_letivo' => ANO_LETIVO]);
  $row_Etapa = $stmtEtapa->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Etapa = $stmtEtapa->rowCount();

  // Consulta Componentes
  $stmtComponentes = $SmecelNovo->prepare("
      SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
      turma_id, turma_ano_letivo, turma_etapa, etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
      FROM smc_ch_lotacao_professor
      INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
      INNER JOIN smc_etapa ON etapa_id = turma_etapa
      INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
      WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
      GROUP BY disciplina_id
      ORDER BY disciplina_nome
  ");
  $stmtComponentes->execute([':professor_id' => ID_PROFESSOR, ':ano_letivo' => ANO_LETIVO]);
  $row_Componentes = $stmtComponentes->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Componentes = $stmtComponentes->rowCount();

  // Consulta Escola
  $stmtEscola = $SmecelNovo->prepare("
      SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
      turma_id, turma_ano_letivo, escola_id, escola_nome
      FROM smc_ch_lotacao_professor
      INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
      INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
      WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
      GROUP BY escola_id
  ");
  $stmtEscola->execute([':professor_id' => ID_PROFESSOR, ':ano_letivo' => ANO_LETIVO]);
  $row_Escola = $stmtEscola->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Escola = $stmtEscola->rowCount();
 

  // Consulta Planejamento Anual
  $stmtPlanejamentoAnual = $SmecelNovo->prepare("
      SELECT plano_anual_id, plano_anual_id_prof, plano_anual_id_etapa, plano_anual_id_componente, plano_anual_id_escola, plano_anual_ano, plano_anual_texto, plano_anual_link, plano_anual_hash,
      etapa_id, etapa_nome, disciplina_id, disciplina_nome, escola_id, escola_nome 
      FROM smc_plano_anual
      INNER JOIN smc_etapa ON etapa_id = plano_anual_id_etapa
      INNER JOIN smc_disciplina ON disciplina_id = plano_anual_id_componente
      INNER JOIN smc_escola ON escola_id = plano_anual_id_escola
      WHERE plano_anual_id_prof = :professor_id
  ");
  $stmtPlanejamentoAnual->execute([':professor_id' => ID_PROFESSOR]);
  $row_PlanejamentoAnual = $stmtPlanejamentoAnual->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_PlanejamentoAnual = $stmtPlanejamentoAnual->rowCount();


  // Inserção no banco
  if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $professor = ID_PROFESSOR;
    $ano_letivo = ANO_LETIVO;
    $hash = md5(date("YmdHis") . $professor);

    $stmtInsert = $SmecelNovo->prepare("
          INSERT INTO smc_plano_anual (plano_anual_id_prof, plano_anual_id_etapa, plano_anual_id_componente, plano_anual_id_escola, plano_anual_ano, plano_anual_hash)
          VALUES (:professor_id, :etapa_id, :componente_id, :escola_id, :ano_letivo, :hash)
      ");
    $stmtInsert->execute([
      ':professor_id' => $professor,
      ':etapa_id' => $_POST['plano_anual_id_etapa'],
      ':componente_id' => $_POST['plano_anual_id_componente'],
      ':escola_id' => $_POST['plano_anual_id_escola'],
      ':ano_letivo' => $ano_letivo,
      ':hash' => $hash
    ]);

    header("Location: planejamento_anual_texto.php?plano=$hash");
    exit;
  }

  // Exclusão no banco
  if ((isset($_GET['plano_del'])) && ($_GET['plano_del'] != "")) {
    $professorDel = ID_PROFESSOR;

    $stmtDelete = $SmecelNovo->prepare("
          DELETE FROM smc_plano_anual WHERE plano_anual_id_prof = :professor_id AND plano_anual_hash = :hash
      ");
    $stmtDelete->execute([
      ':professor_id' => $professorDel,
      ':hash' => $_GET['plano_del']
    ]);

    header("Location: planejamento_anual.php?deletado");
    exit;
  }
} catch (PDOException $e) {
  die("Erro: " . $e->getMessage());
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
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO ANUAL</h1>
      <div class="col-md-12">

        <?php if (isset($_GET["deletado"])) { ?>

          <div class="ls-alert-success"><strong>Sucesso!</strong> Planejamento deletado!</div>

        <?php } ?>


        <p>
          <a href="planejamento_mapa.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
          <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">INSERIR PLANO
            ANUAL</button>
        </p>
        <hr>
      </div>






      <?php if ($totalRows_PlanejamentoAnual > 0) { ?>

        <table class="ls-table">
          <thead>
            <tr>
              <th class="ls-txt-center">ETAPA</th>
              <th class="ls-txt-center">COMPONENTE/C.EXPERIÊNCIA</th>
              <th class="ls-txt-center">ESCOLA</th>
              <th width="100" class="ls-txt-left">TEXTO</th>
              <th width="100" class="ls-txt-left">ARQUIVO</th>
              <th width="50" class="ls-txt-left"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($row_PlanejamentoAnual as $planejamento) { ?>
              <tr>
                <td class="ls-txt-center"><?php echo $planejamento['etapa_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $planejamento['disciplina_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $planejamento['escola_nome']; ?></td>
                <td class="ls-txt-left">
                  <a href="planejamento_anual_texto.php?plano=<?php echo $planejamento['plano_anual_hash']; ?>"
                    class="ls-ico-text"></a>
                  <?php if (!empty($planejamento['plano_anual_texto'])) {
                    echo "<i class='ls-ico-checkmark-circle ls-color-success'></i>";
                  } ?>
                </td>
                <td class="ls-txt-left">
                  <a href="planejamento_anual_link.php?plano=<?php echo $planejamento['plano_anual_hash']; ?>"
                    class="ls-ico-attachment"></a>
                  <?php if (!empty($planejamento['plano_anual_link'])) {
                    echo "<i class='ls-ico-checkmark-circle ls-color-success'></i>";
                  } ?>
                  <?php if (!empty($planejamento['plano_anual_link'])) { ?>
                    <a href="https://www.smecel.com.br/professor/plano_anual/<?php echo $planejamento['plano_anual_link']; ?>"
                      target="_blank" class='ls-ico-search'></a>
                  <?php } ?>
                </td>
                <td class="ls-txt-left">
                  <a href="planejamento_anual.php?plano_del=<?php echo $planejamento['plano_anual_hash']; ?>"
                    class="ls-ico-remove ls-color-danger"></a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

      <?php } else { ?>

        <p>Nenhum plano cadastrado para este ano letivo</p>

      <?php } ?>


      <p>&nbsp;</p>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>

  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">INSERIR PLANEJAMENTO ANUAL</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <p>


        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">


          <label class="ls-label col-md-12">
            <b class="ls-label-text">ESCOLA</b>
            <div class="ls-custom-select">
              <select name="plano_anual_id_escola" class="ls-select" required>
                <option value="" selected>Escolha...</option>
                <?php foreach($row_Escola as $escola) {?>
                  <option value="<?php echo $escola['escola_id'] ?>"><?php echo $escola['escola_nome'] ?></option>
                <?php } ?>
              </select>
            </div>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">ETAPA</b>
            <div class="ls-custom-select">
              <select name="plano_anual_id_etapa" class="ls-select" required>
                <option value="" selected>Escolha...</option>
                <?php foreach($row_Etapa as $etapa) {?>
                  <option value="<?php echo $etapa['turma_etapa'] ?>"><?php echo $etapa['etapa_nome_abrev'] ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">COMPONENTE/CAMPO DE EXPERIÊNCIA</b>
            <div class="ls-custom-select">
              <select name="plano_anual_id_componente" class="ls-select" required>
                <option value="" selected>Escolha...</option>
                <?php foreach($row_Componentes as $componente) {?>
                  <option value="<?php echo $componente['ch_lotacao_disciplina_id'] ?>">
                    <?php echo $componente['disciplina_nome'] ?></option>
                <?php } ?>
              </select>
            </div>
          </label>

          <input type="hidden" name="plano_anual_id_prof" value="">
          <input type="hidden" name="plano_anual_ano" value="">
          <input type="hidden" name="plano_anual_hash" value="">
          <input type="hidden" name="MM_insert" value="form1">



          </p>
      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn ls-float-right" data-dismiss="modal">SAIR</button>
        <input type="submit" value="SALVAR E PROSSEGUIR" class="ls-btn-primary">
      </div>
      </form>
    </div>
  </div><!-- /.modal -->

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>
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
<?php
mysql_free_result($PlanejamentoAnual);

mysql_free_result($Etapa);
mysql_free_result($Componentes);
?>