<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

$colname_Escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
$colname_Target = isset($_GET['target']) ? anti_injection($_GET['target']) : "-1";
$colname_Turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$data = isset($_GET['data']) ? anti_injection($_GET['data']) : date("Y-m-d");
$colname_aula = isset($_GET['aula']) ? anti_injection($_GET['aula']) : "-1";

$semana = date("w", strtotime($data));
$diasemana = array('DOMINGO', 'SEGUNDA', 'TERÇA', 'QUARTA', 'QUINTA', 'SEXTA', 'SÁBADO');
$dia_semana_nome = $diasemana[$semana];

try {
  $stmtAula = $SmecelNovo->prepare(
    "SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
        plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_num_aula,
        plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
        plano_aula_video, plano_aula_meet, plano_aula_sicrona_hora, plano_aula_sicrona_minuto, plano_aula_google_form, 
        plano_aula_google_form_tempo, plano_aula_publicado, plano_aula_hash,
        turma_id, turma_nome, turma_etapa 
        FROM smc_plano_aula 
        INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
        WHERE plano_aula_hash = :aula_hash"
  );
  $stmtAula->execute([':aula_hash' => $colname_aula]);
  $row_aula = $stmtAula->fetch(PDO::FETCH_ASSOC);

  $stmtTurmas = $SmecelNovo->prepare(
    "SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
        ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, disciplina_id, disciplina_nome, disciplina_nome_abrev, escola_id, escola_nome,
        CASE turma_turno
        WHEN 0 THEN 'INT'
        WHEN 1 THEN 'MAT'
        WHEN 2 THEN 'VES'
        WHEN 3 THEN 'NOT'
        END AS turma_turno_nome 
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
        INNER JOIN smc_escola ON escola_id = ch_lotacao_escola	
        WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_professor_id = :professor_id AND ch_lotacao_dia = :semana AND ch_lotacao_disciplina_id = :disciplina_id AND ch_lotacao_turma_id = :turma_id
        ORDER BY ch_lotacao_escola, turma_turno, ch_lotacao_aula ASC"
  );
  $stmtTurmas->execute([
    ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano'],
    ':professor_id' => $row_Vinculos['vinculo_id_funcionario'],
    ':semana' => $semana,
    ':disciplina_id' => $row_aula['plano_aula_id_disciplina'],
    ':turma_id' => $row_aula['plano_aula_id_turma']
  ]);
  $row_Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Turmas = count($row_Turmas);

} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
  exit;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $aulaNum = $_POST['aula_num'];

  try {
    foreach ($aulaNum as $aula_num => $value) {
      $dataCad = date('Y-m-d H:i:s');
      $hash = md5(uniqid(""));

      $stmtInsert = $SmecelNovo->prepare(
        "INSERT INTO smc_plano_aula (plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_num_aula, plano_aula_num_dia, plano_aula_hash) 
                VALUES (:habilidade, :turma_id, :disciplina_id, :professor_id, :data, :data_cadastro, :texto, :conteudo, :num_aula, :num_dia, :hash)"
      );
      $stmtInsert->execute([
        ':habilidade' => $_POST['plano_aula_id_habilidade'],
        ':turma_id' => $_POST['plano_aula_id_turma'],
        ':disciplina_id' => $_POST['plano_aula_id_disciplina'],
        ':professor_id' => $_POST['plano_aula_id_professor'],
        ':data' => $_POST['plano_aula_data'],
        ':data_cadastro' => $dataCad,
        ':texto' => $_POST['plano_aula_texto'],
        ':conteudo' => $_POST['plano_aula_conteudo'],
        ':num_aula' => $value,
        ':num_dia' => $semana,
        ':hash' => $hash
      ]);
    }

    $insertGoTo = "aulas_data.php?aula_duplicada";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
    exit;

  } catch (PDOException $e) {
    echo "Erro ao inserir: " . $e->getMessage();
    exit;
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
      <h1 class="ls-title-intro ls-ico-home">DUPLICAR AULA <?php echo $row_aula['plano_aula_id']; ?></h1>
      <p><a
          href="aulas.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=aulas&data=<?php echo $data; ?>"
          class="ls-btn ls-ico-chevron-left">Voltar</a></p>

      <div class="ls-box-filter">
        <h5 class="ls-title-5"><?php echo $row_aula['plano_aula_texto']; ?></h5>
        <p><strong><?php echo date("d/m/Y", strtotime($row_aula['plano_aula_data'])); ?></strong> -
          <?php echo $row_aula['turma_nome']; ?></strong>
        </p>
      </div>


      <p>&nbsp;</p>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>

  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">DUPLICAR AULA</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <div class="ls-box-filter">
          <h5 class="ls-title-5"><?php echo $row_aula['plano_aula_texto']; ?></h5>
          <p><strong><?php echo date("d/m/Y", strtotime($row_aula['plano_aula_data'])); ?></strong> -
            <?php echo utf8_decode($row_aula['turma_nome']); ?></strong>
          </p>
        </div>
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
          <fieldset>


            <div class="ls-label col-md-12">
              <p>Copiar conteúdo para as aulas:</p>


              <?php
              foreach ($row_Turmas as $turma) {
                ?>
                <label class="ls-label-text">
                  <input type="checkbox" name="aula_num[]" value="<?= htmlspecialchars($turma['ch_lotacao_aula']) ?>"
                    <?= ($turma['ch_lotacao_aula'] == $row_aula['plano_aula_num_aula']) ? "disabled" : "" ?>>
                  <?= htmlspecialchars($turma['ch_lotacao_aula']) ?>ª AULA |
                  <?= htmlspecialchars($turma['disciplina_nome_abrev']) ?>   <?= utf8_decode($turma['turma_nome']) ?>,
                  <?= htmlspecialchars($turma['turma_turno_nome']) ?>
                </label>
                <?php
              }

              ?>



            </div>



          </fieldset>
          <input type="hidden" name="plano_aula_id_turma" value="<?php echo $row_aula['plano_aula_id_turma']; ?>">
          <input type="hidden" name="plano_aula_id_habilidade"
            value="<?php echo $row_aula['plano_aula_id_habilidade']; ?>">
          <input type="hidden" name="plano_aula_id_disciplina"
            value="<?php echo $row_aula['plano_aula_id_disciplina']; ?>">
          <input type="hidden" name="plano_aula_id_professor"
            value="<?php echo $row_aula['plano_aula_id_professor']; ?>">
          <input type="hidden" name="plano_aula_data" value="<?php echo $row_aula['plano_aula_data']; ?>">
          <input type="hidden" name="plano_aula_data_cadastro" value="<?php echo date("Y-m-d H:i:s"); ?>">
          <input type="hidden" name="plano_aula_texto" value="<?php echo $row_aula['plano_aula_texto']; ?>">
          <input type="hidden" name="plano_aula_conteudo" value="<?php echo $row_aula['plano_aula_conteudo']; ?>">
          <input type="hidden" name="plano_aula_hash" value="<?php echo $row_aula['plano_aula_hash']; ?>" size="32">
          <input type="hidden" name="MM_insert" value="form1">
      </div>
      <div class="ls-modal-footer">
        <input type="submit" value="DUPLICAR" class="ls-btn">
        <a href="aulas_data.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=aulas&data=<?php echo $data; ?>"
          class="ls-btn-danger">CANCELAR</a>
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

    locastyle.modal.open("#myAwesomeModal");


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
mysql_free_result($aula);

mysql_free_result($Turmas);
?>