<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = anti_injection($_GET['disciplina']);
}

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = anti_injection($_GET['cod']);
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
}

// Using PDO to fetch Matricula
$query_Matricula = "
  SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
  vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
  vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
  vinculo_aluno_vacina_atualizada, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  WHERE vinculo_aluno_hash = :matricula";

$stmt_Matricula = $SmecelNovo->prepare($query_Matricula);
$stmt_Matricula->bindParam(':matricula', $colname_Matricula, PDO::PARAM_STR);
$stmt_Matricula->execute();
$row_Matricula = $stmt_Matricula->fetch(PDO::FETCH_ASSOC);

// Fetch Parecer
$query_Parecer = "
SELECT p_ind_id, p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_data_cadastro, p_ind_periodo 
FROM smc_parecer_individual_professor
WHERE p_ind_id_prof = :prof_id AND p_ind_mat_aluno = :aluno_id
ORDER BY p_ind_periodo ASC";
$stmt_Parecer = $SmecelNovo->prepare($query_Parecer);
$stmt_Parecer->bindParam(':prof_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
$stmt_Parecer->bindParam(':aluno_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
$stmt_Parecer->execute();
$row_Parecer = $stmt_Parecer->fetch(PDO::FETCH_ASSOC);
$totalRows_Parecer = $stmt_Parecer->rowCount();
// Fetch Turma
$query_Turma = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id = :turma_id";
$stmt_Turma = $SmecelNovo->prepare($query_Turma);
$stmt_Turma->bindParam(':turma_id', $colname_Turma, PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);


// Fetch Matriz
$query_Matriz = "
SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo 
FROM smc_matriz WHERE matriz_id = :matriz_id";
$stmt_Matriz = $SmecelNovo->prepare($query_Matriz);
$stmt_Matriz->bindParam(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
$stmt_Matriz->execute();
$row_Matriz = $stmt_Matriz->fetch(PDO::FETCH_ASSOC);

// Fetch Criterios
$query_Criterios = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito 
FROM smc_criterios_avaliativos WHERE ca_id = :ca_id";
$stmt_Criterios = $SmecelNovo->prepare($query_Criterios);
$stmt_Criterios->bindParam(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt_Criterios->execute();
$row_Criterios = $stmt_Criterios->fetch(PDO::FETCH_ASSOC);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = "
    UPDATE smc_parecer_individual_professor 
    SET p_ind_texto = :texto, p_ind_periodo = :periodo 
    WHERE p_ind_id = :id";

  $stmt_Update = $SmecelNovo->prepare($updateSQL);
  $stmt_Update->bindParam(':texto', $_POST['p_ind_texto'], PDO::PARAM_STR);
  $stmt_Update->bindParam(':periodo', $_POST['p_ind_periodo'], PDO::PARAM_STR);
  $stmt_Update->bindParam(':id', $_POST['p_ind_id'], PDO::PARAM_INT);
  $stmt_Update->execute();

  $updateGoTo = "parecer_individual.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = "
    INSERT INTO smc_parecer_individual_professor 
    (p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_periodo) 
    VALUES (:prof_id, :aluno_id, :texto, :periodo)";

  $stmt_Insert = $SmecelNovo->prepare($insertSQL);
  $stmt_Insert->bindParam(':prof_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
  $stmt_Insert->bindParam(':aluno_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
  $stmt_Insert->bindParam(':texto', $_POST['p_ind_texto'], PDO::PARAM_STR);
  $stmt_Insert->bindParam(':periodo', $_POST['p_ind_periodo'], PDO::PARAM_STR);
  $stmt_Insert->execute();

  $insertGoTo = "parecer_individual.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_GET['parecer'])) && ($_GET['parecer'] != "")) {
  $deleteSQL = "
    DELETE FROM smc_parecer_individual_professor 
    WHERE p_ind_id = :id AND p_ind_id_prof = :prof_id AND p_ind_mat_aluno = :aluno_id";

  $stmt_Delete = $SmecelNovo->prepare($deleteSQL);
  $stmt_Delete->bindParam(':id', $_GET['parecer'], PDO::PARAM_INT);
  $stmt_Delete->bindParam(':prof_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
  $stmt_Delete->bindParam(':aluno_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
  $stmt_Delete->execute();

  $deleteGoTo = "parecer_individual.php?cod=$colname_Matricula&disciplina=$colname_Disciplina&turma=$colname_Turma&deletado";
  header(sprintf("Location: %s", $deleteGoTo));
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      <p><a
          href="rendimento_alunos.php?escola=<?php echo $row_Matricula['vinculo_aluno_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>"
          class="ls-btn ls-ico-chevron-left">Voltar</a></p>


      <blockquote>
        <span style="margin-right:10px; text-align:center; float:left;">
          <?php if ($row_Matricula['aluno_foto'] == "") { ?>
            <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
          <?php } else { ?>
            <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0"
              width="50">
          <?php } ?>
          <?php //echo $row_Alunos['aluno_nome']; ?>
        </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
        Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong><br>
        <p>&nbsp;</p>
      </blockquote>
      <hr>

      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">REGISTRAR PARECER</button>

      <hr>

      <?php if (isset($_GET['editado'])) { ?>
        <div class="ls-alert-info">Parecer editado com sucesso!</div><?php } ?>

      <?php if ($totalRows_Parecer > 0) { // Show if recordset not empty ?>


        <?php do { ?>
          <div class="ls-box" id="parecer_<?php echo $row_Parecer['p_ind_id']; ?>">
            <a href="parecer_individual.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&parecer=<?php echo $row_Parecer['p_ind_id']; ?>"
              class="ls-ico-remove ls-float-right ls-color-danger" parecer="<?php echo $row_Parecer['p_ind_id']; ?>"
              aluno="<?php echo $row_Matricula['vinculo_aluno_id']; ?>"
              professor="<?php echo $row_ProfLogado['func_id']; ?>"></a>
            <a data-ls-module="modal" data-target="#myAwesomeModal<?php echo $row_Parecer['p_ind_id']; ?>"
              class="ls-ico-pencil ls-float-right ls-color-warning" parecer="<?php echo $row_Parecer['p_ind_id']; ?>"
              aluno="<?php echo $row_Matricula['vinculo_aluno_id']; ?>"
              professor="<?php echo $row_ProfLogado['func_id']; ?>"></a>
            <strong><?php echo $row_Parecer['p_ind_periodo'] == "0" ? "PARECER INICIAL" : $row_Parecer['p_ind_periodo'] . "º PERÍODO"; ?></strong>
            <p><?php echo $row_Parecer['p_ind_texto']; ?></p>
          </div>

          <div class="ls-modal" id="myAwesomeModal<?php echo $row_Parecer['p_ind_id'] ?>">
            <div class="ls-modal-box">
              <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">REGISTRAR PARECER</h4>
              </div>
              <div class="ls-modal-body" id="myModalBody">
                <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form row">
                  <label class="ls-label">
                    <b class="ls-label-text">PARECER</b>
                    <textarea id="rel_avaliativo" class="materialize-textarea" name="p_ind_texto" cols="50" rows="3"
                      required><?php echo $row_Parecer['p_ind_texto']; ?></textarea>
                  </label>

                  <div class="ls-custom-select">
                    <select name="p_ind_periodo" class="ls-select">
                      <option value="0" <?php echo ($row_Parecer['p_ind_periodo'] == 0) ? "selected" : ""; ?>>PARECER INICIAL
                      </option>
                      <?php for ($i = 1; $i <= $row_Criterios['ca_qtd_periodos']; $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php echo ($row_Parecer['p_ind_periodo'] == $i) ? "selected" : ""; ?>><?php echo $i; ?>º PERÍODO/UNIDADE</option>
                      <?php } ?>
                    </select>
                  </div>

                  <input type="hidden" name="MM_update" value="form2">
                  <input type="hidden" name="p_ind_id" value="<?php echo $row_Parecer['p_ind_id']; ?>">
              </div>

              <div class="ls-modal-footer">
                <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
                <input type="submit" value="SALVAR" class="ls-btn-primary">
              </div>
              </form>
            </div>
          </div><!-- /.modal -->

        <?php } while ($row_Parecer = $stmt_Parecer->fetch(PDO::FETCH_ASSOC)); ?>


      <?php } else { ?>
        <hr>
        Nenhum parecer cadastrado.

      <?php } // Show if recordset not empty ?>

    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>

  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">REGISTRAR PARECER</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <p>



        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">


          <label class="ls-label">
            <b class="ls-label-text">PARECER</b>
            <textarea id="rel_avaliativo" class="materialize-textarea" name="p_ind_texto" cols="50" rows="3"
              required></textarea>
          </label>

          <div class="ls-custom-select">
            <select name="p_ind_periodo" class="ls-select">

              <option value="0">PARECER INICIAL</option>

              <?php for ($i = 1; $i < $row_Criterios['ca_qtd_periodos'] + 1; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, ""))) {
                     echo "SELECTED";
                   } ?>><?php echo $i; ?>º
                  PERÍODO/UNIDADE</option>
              <?php } ?>
            </select>
          </div>

          <input type="hidden" name="MM_insert" value="form1">

          </p>
      </div>
      <div class="ls-modal-footer">
        <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
        <input type="submit" value="SALVAR" class="ls-btn-primary">
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