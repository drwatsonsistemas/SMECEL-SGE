<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

$colname_Vinculo = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$colname_Escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
$colname_Aula = isset($_GET['aulanum']) ? anti_injection($_GET['aulanum']) : "-1";
$data = isset($_GET['data']) ? anti_injection($_GET['data']) : date("Y-m-d");

$semana = date("w", strtotime($data));
$diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
$dia_semana_nome = $diasemana[$semana];
$data = date("Y-m-d", strtotime($data));

function diaSemana($data)
{
  $semana = date("w", strtotime($data));
  $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
  return $diasemana[$semana];
}

try {
  $stmtVinculo = $SmecelNovo->prepare(
    "SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, 
    ch_lotacao_obs, ch_lotacao_escola, disciplina_id, disciplina_nome, disciplina_cor_fundo, turma_id, turma_nome, turma_turno, turma_id_escola, 
    escola_id, escola_nome, 
    CASE turma_turno 
      WHEN 0 THEN 'INTEGRAL' 
      WHEN 1 THEN 'MATUTINO' 
      WHEN 2 THEN 'VESPERTINO' 
      WHEN 3 THEN 'NOTURNO' 
    END AS turma_turno 
    FROM smc_ch_lotacao_professor 
    INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id 
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
    INNER JOIN smc_escola ON escola_id = turma_id_escola 
    WHERE ch_lotacao_id = :colname_Vinculo"
  );
  $stmtVinculo->execute([':colname_Vinculo' => $colname_Vinculo]);
  $row_Vinculo = $stmtVinculo->fetch(PDO::FETCH_ASSOC);

  $stmtAulas = $SmecelNovo->prepare(
    "SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_texto, 
    plano_aula_data, plano_aula_data_cadastro, plano_aula_publicado, plano_aula_hash 
    FROM smc_plano_aula 
    WHERE plano_aula_id_turma = :turma_id AND plano_aula_id_disciplina = :disciplina_id 
    ORDER BY plano_aula_data DESC"
  );
  $stmtAulas->execute([
    ':turma_id' => $row_Vinculo['turma_id'],
    ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id']
  ]);
  $row_Aulas = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Aulas = $stmtAulas->rowCount();

  $stmtTurma = $SmecelNovo->prepare(
    "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
    FROM smc_turma 
    WHERE turma_ano_letivo = :ano_letivo AND turma_id = :turma_id"
  );
  $stmtTurma->execute([
    ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano'],
    ':turma_id' => $row_Vinculo['ch_lotacao_turma_id']
  ]);
  $row_Turma = $stmtTurma->fetch(PDO::FETCH_ASSOC);
  $totalRows_Turma = $stmtTurma->rowCount();

  if ($totalRows_Turma == 0) {
    header("Location:index.php?erro");
    exit;
  }

  $stmtAulasMinistradasTotal = $SmecelNovo->prepare(
    "SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
    plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
    FROM smc_plano_aula 
    WHERE plano_aula_id_turma = :turma_id AND plano_aula_id_disciplina = :disciplina_id 
    ORDER BY plano_aula_data DESC, plano_aula_id DESC"
  );
  $stmtAulasMinistradasTotal->execute([
    ':turma_id' => $row_Vinculo['ch_lotacao_turma_id'],
    ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id']
  ]);
  $row_AulasMinistradasTotal = $stmtAulasMinistradasTotal->fetchAll(PDO::FETCH_ASSOC);

  $stmtDisciplinaMatriz = $SmecelNovo->prepare(
    "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano 
    FROM smc_matriz_disciplinas 
    WHERE matriz_disciplina_id_matriz = :matriz_id AND matriz_disciplina_id_disciplina = :disciplina_id"
  );
  $stmtDisciplinaMatriz->execute([
    ':matriz_id' => $row_Turma['turma_matriz_id'],
    ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id']
  ]);
  $row_disciplinaMatriz = $stmtDisciplinaMatriz->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
  exit;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$erroAssunto = false;

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $dataCad = date('Y-m-d H:i:s');
  $hash = md5(uniqid(""));
  $plano_aula_texto = $_POST['plano_aula_texto'];
  $plano_aula_data = $_POST['plano_aula_data'];
  
  // Validação do campo plano_aula_texto
  if (strlen($plano_aula_texto) > 255) {
    $erroAssunto = true; // Marca erro para exibir no formulário
    
  } else {
    try {
      $stmtInsert = $SmecelNovo->prepare(
        "INSERT INTO smc_plano_aula (plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_publicado, plano_aula_num_aula, plano_aula_num_dia, plano_aula_hash) 
        VALUES (:turma_id, :disciplina_id, :professor_id, :data, :data_cadastro, :texto, 'N', :num_aula, :num_dia, :hash)"
      );
      $stmtInsert->execute([
        ':turma_id' => $row_Vinculo['ch_lotacao_turma_id'],
        ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id'],
        ':professor_id' => $row_Vinculo['ch_lotacao_professor_id'],
        ':data' => $plano_aula_data,
        ':data_cadastro' => $dataCad,
        ':texto' => $plano_aula_texto,
        ':num_aula' => $row_Vinculo['ch_lotacao_aula'],
        ':num_dia' => $row_Vinculo['ch_lotacao_dia'],
        ':hash' => $hash
      ]);

      $insertGoTo = "aulas_data.php";
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
  <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
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
      <h1 class="ls-title-intro ls-ico-home">AULAS</h1>

      <div id="linkResultado"></div>

    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <div class="ls-modal" id="modal_cadastrarAula" data-modal-blocked>
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">REGISTRAR AULA</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row"
          onsubmit="disableButton()">
          <fieldset>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">DATA</b>
              <p class="ls-label-info">Data da aplicação da aula</p>
              <input type="date" name="plano_aula_data" value="<?php echo $data; ?>" required autocomplete="off">
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">AULA</b>
              <p class="ls-label-info">Número da aula no dia</p>
              <input type="text" value="<?php echo $row_Vinculo['ch_lotacao_aula']; ?>ª AULA" disabled>
            </label>
            <label class="ls-label col-md-12 <?php echo $erroAssunto ? 'ls-error' : ''; ?>">
              <b class="ls-label-text">ASSUNTO</b>
              <p class="ls-label-info">Digite o tema da aula aplicada (máximo 255 caracteres)</p>
              <input type="text" name="plano_aula_texto" value=""
                maxlength="255" required autocomplete="off" class="ls-field">
              <?php if ($erroAssunto) { ?>
                <small class="ls-help-message">O assunto excede o limite de 255 caracteres.</small>
              <?php } ?>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">COMPONENTE</b>
              <p class="ls-label-info"></p>
              <input type="text" name="" value="<?php echo $row_Vinculo['disciplina_nome']; ?>" disabled>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">TURMA</b>
              <p class="ls-label-info"></p>
              <input type="text" name=""
                value="<?php echo $row_Vinculo['turma_nome']; ?>, <?php echo $row_Vinculo['turma_turno']; ?>" disabled>
            </label>
          </fieldset>
          <input type="hidden" name="MM_insert" value="form1">
          <input type="hidden" name="plano_aula_num_aula" value="<?php echo $colname_Aula; ?>" disabled>
      </div>
      <div class="ls-modal-footer">
        <input class="ls-btn-primary" id="btnSalvar" type="submit" value="SALVAR">
        <a href="aulas_data.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Vinculo; ?>&target=aulas&data=<?php echo $data; ?>&voltar"
          class="ls-btn ls-float-right">VOLTAR</a>
        </form>
      </div>
    </div>
  </div>
  <!-- /.modal -->

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>


  <?php if (isset($_GET["nova"])) { ?>
    <script type="application/javascript">
      locastyle.modal.open("#modal_cadastrarAula");

      function disableButton() {
        document.getElementById("btnSalvar").disabled = true;
      }
    </script>
  <?php } ?>



</body>

</html>
<?php


mysql_free_result($Vinculo);

mysql_free_result($Aulas);
?>