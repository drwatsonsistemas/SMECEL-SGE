<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

$colname_Vinculo = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$data = isset($_GET['data']) ? anti_injection($_GET['data']) : date("Y-m-d");

$semana = date("w", strtotime($data));
$diasemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$dia_semana_nome = $diasemana[$semana];
$data = date("Y-m-d", strtotime($data));

function diaSemana($data)
{
  $semana = date("w", strtotime($data));
  $diasemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
  return $diasemana[$semana];
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $dataCad = date('Y-m-d H:i:s');
  $hash = md5(uniqid(""));

  $insertSQL = "
        INSERT INTO smc_plano_aula 
        (plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_publicado, plano_aula_num_aula, plano_aula_num_dia, plano_aula_hash) 
        VALUES 
        (:turma_id, :disciplina_id, :professor_id, :data, :data_cadastro, :texto, :publicado, :num_aula, :num_dia, :hash)";

  $stmt = $SmecelNovo->prepare($insertSQL);
  $stmt->execute([
    ':turma_id' => $row_Vinculo['ch_lotacao_turma_id'],
    ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id'],
    ':professor_id' => $row_Vinculo['ch_lotacao_professor_id'],
    ':data' => $_POST['plano_aula_data'],
    ':data_cadastro' => $dataCad,
    ':texto' => $_POST['plano_aula_texto'],
    ':publicado' => 'N',
    ':num_aula' => $row_Vinculo['ch_lotacao_aula'],
    ':num_dia' => $row_Vinculo['ch_lotacao_dia'],
    ':hash' => $hash,
  ]);

  $insertGoTo = "aula_editar.php?aula=$hash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
  exit;
}

// Consulta Vinculo
$query_Vinculo = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, 
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
    WHERE ch_lotacao_id = :turma_id";

$stmtVinculo = $SmecelNovo->prepare($query_Vinculo);
$stmtVinculo->execute([':turma_id' => $colname_Vinculo]);
$row_Vinculo = $stmtVinculo->fetch(PDO::FETCH_ASSOC);


// Consulta Turma
$query_Turma = "
    SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
    FROM smc_turma 
    WHERE turma_ano_letivo = :ano_letivo AND turma_id = :turma_id";

$stmtTurma = $SmecelNovo->prepare($query_Turma);
$stmtTurma->execute([
  ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano'],
  ':turma_id' => $row_Vinculo['ch_lotacao_turma_id'],
]);
$row_Turma = $stmtTurma->fetch(PDO::FETCH_ASSOC);

if (!$row_Turma) {
  header("Location: index.php?erro");
  exit;
}

// Consulta Aulas Ministradas
$query_AulasMinistradasTotal = "
    SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
           plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_publicado, plano_aula_hash 
    FROM smc_plano_aula
    WHERE plano_aula_id_turma = :turma_id AND plano_aula_id_disciplina = :disciplina_id
    ORDER BY plano_aula_data DESC, plano_aula_id DESC";

$stmtAulasTotal = $SmecelNovo->prepare($query_AulasMinistradasTotal);
$stmtAulasTotal->execute([
  ':turma_id' => $row_Vinculo['ch_lotacao_turma_id'],
  ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id'],
]);
$row_AulasMinistradasTotal = $stmtAulasTotal->fetchAll(PDO::FETCH_ASSOC);
$totalRows_AulasMinistradasTotal = $stmtAulasTotal->rowCount();
// Consulta Disciplina Matriz
$query_disciplinaMatriz = "
    SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano 
    FROM smc_matriz_disciplinas
    WHERE matriz_disciplina_id_matriz = :matriz_id AND matriz_disciplina_id_disciplina = :disciplina_id";

$stmtDisciplinaMatriz = $SmecelNovo->prepare($query_disciplinaMatriz);
$stmtDisciplinaMatriz->execute([
  ':matriz_id' => $row_Turma['turma_matriz_id'],
  ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id'],
]);
$row_disciplinaMatriz = $stmtDisciplinaMatriz->fetch(PDO::FETCH_ASSOC);

// Consulta Aulas
$query_Aulas = "
    SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_texto, 
           plano_aula_data, plano_aula_data_cadastro, plano_aula_publicado, plano_aula_hash 
    FROM smc_plano_aula
    WHERE plano_aula_id_turma = :turma_id AND plano_aula_id_disciplina = :disciplina_id
    ORDER BY plano_aula_data DESC";

$stmtAulas = $SmecelNovo->prepare($query_Aulas);
$stmtAulas->execute([
  ':turma_id' => $row_Vinculo['turma_id'],
  ':disciplina_id' => $row_Vinculo['ch_lotacao_disciplina_id'],
]);
$row_Aulas = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Aulas = $stmtAulas->rowCount();


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
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">

  <style>
@media (max-width: 768px) {
  .ls-collapse-header {
    padding: 0.5rem;
    margin: 0.2rem;
    font-size: 1rem;
  }
  .ls-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }
  .ls-modal-box {
    width: 90%;
  }
  .ls-label {
    width: 100%;
    margin-bottom: 1rem;
  }
  .ls-collapse-title span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 85%;
  }
}
</style>

</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">AULAS</h1>

      <p>

        <a href="mapa_aulas.php" class="ls-btn ls-ico-chevron-left">VOLTAR</a>
        <a href="aulas_por_data.php?aula=&escola=<?php echo $row_Vinculo['escola_id']; ?>&turma=<?php echo $colname_Vinculo; ?>"
          class="ls-btn">VER AULAS AGRUPADAS POR DATA</a>
      </p>

      <?php $diferenca = $row_disciplinaMatriz['matriz_disciplina_ch_ano'] - $totalRows_AulasMinistradasTotal; ?>
      <?php $resultado = $diferenca;
      if ($diferenca < 0) {
        $resultado = $diferenca * (-1);
      } ?>

      <div class="ls-box-filter">
        <h5 class="ls-title-5"><?php echo $row_Vinculo['escola_nome']; ?></h5>
        <p><strong><?php echo $row_Vinculo['turma_nome']; ?>, <?php echo $row_Vinculo['turma_turno']; ?></strong> <br>
          <span class=""><?php echo htmlspecialchars($row_Vinculo['disciplina_nome']); ?></span>
          | <?php echo $totalRows_Aulas; ?> aula(s)
          registradas | <?php echo $row_disciplinaMatriz['matriz_disciplina_ch_ano']; ?> aulas necessárias para compor a
          matriz da turma.
        </p>
      </div>

      <div class="ls-box-filter <?php if ($diferenca > 0) {
        echo "ls-color-danger";
      } else if ($diferenca < 0) {
        echo "ls-color-success";
      } else {
        echo "ls-color-success";
      } ?> lighten-2">
        <span class=""><?php if ($diferenca <> 0) {
          echo $resultado;
        } else {
          echo "&nbsp;";
        } ?> aulas
          <?php if ($diferenca > 0) {
            echo "pendentes";
          } else if ($diferenca < 0) {
            echo "excedentes";
          } else {
            echo "concluídas";
          } ?>
          (<?php echo $totalRows_AulasMinistradasTotal;
          ; ?> aulas postadas)</span>
      </div>

      <?php if ($totalRows_Aulas > 0) { ?>

        <div class="ls-collapse-group">
          <?php $num = $totalRows_Aulas; ?>
          <?php foreach ($row_Aulas as $aula): ?>
            <div id="aula_<?php echo $aula['plano_aula_id']; ?>" data-ls-module="collapse"
              data-target="#accordeon<?php echo $aula['plano_aula_id']; ?>" class="ls-collapse">
              <a href="#" class="ls-collapse-header"
                style="border-left:<?php echo $row_Vinculo['disciplina_cor_fundo']; ?> solid 5px; padding:5px; margin:3px;">
                <p class="ls-collapse-title">
                  <small>
                    <?php echo date("d/m/Y", strtotime($aula['plano_aula_data'])) . " | " . diaSemana($aula['plano_aula_data']); ?>
                    | <?php echo $aula['plano_aula_id']; ?>
                  </small><br>
                  <span style="display:block; width:90%; color:<?php echo $row_Vinculo['disciplina_cor_fundo']; ?>">
                    <span class="ls-tag"><?php echo str_pad($num, 3, "0", STR_PAD_LEFT);
                    $num--; ?></span>
                    <?php echo $aula['plano_aula_texto']; ?>
                  </span>
                </p>
              </a>
              <div class="ls-collapse-body" id="accordeon<?php echo $aula['plano_aula_id']; ?>">
                <a href="aula_editar.php?aula=<?php echo $aula['plano_aula_hash']; ?>&escola=<?php echo $row_Vinculo['turma_id_escola']; ?>&turma=<?php echo $colname_Vinculo; ?>&target=aulas&data=<?php echo $data; ?>"
                  class="ls-btn ls-ico-pencil2">EDITAR</a>
                <!--<a href="aula_duplicar.php?aula=<?php echo $aula['plano_aula_hash']; ?>&escola=<?php echo $row_Vinculo['turma_id_escola']; ?>&turma=<?php echo $colname_Vinculo; ?>&target=aulas&data=<?php echo $data; ?>" class="ls-btn ls-ico-windows">DUPLICAR</a>-->
                <a class="ls-btn-primary-danger ls-ico-remove ls-float-right delete-btn"
                  id="<?php echo $aula['plano_aula_hash']; ?>" cod="<?php echo $aula['plano_aula_id']; ?>"></a>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      <?php } else { ?>
        <div class="ls-alert-info">Nenhuma aula cadastrada ate o momento.</div>
      <?php } ?>

      <p>&nbsp;</p>




      <p>&nbsp;</p>
      <div id="linkResultado"></div>
    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <div class="ls-modal" id="modal_cadastrarAula">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">REGISTRAR AULA</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
          <fieldset>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">DATA</b>
              <p class="ls-label-info">Informe a data em que a aula foi aplicada</p>
              <input type="date" name="plano_aula_data" value="<?php echo $data; ?>" required autocomplete="off">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">ASSUNTO</b>
              <p class="ls-label-info">Digite o tema da aula aplicada</p>
              <input type="text" name="plano_aula_texto" value="" required autocomplete="off">
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
      </div>
      <div class="ls-modal-footer">
        <input class="ls-btn-primary" type="submit" value="SALVAR E PROSSEGUIR >>">
        <span class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</span>
        </form>
      </div>
    </div>
  </div>
  <!-- /.modal -->

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/sweetalert2.min.js"></script>

  <script type="application/javascript">
    $(document).on('click', '.delete-btn', function () {
      var aula = $(this).attr('id');
      var id = $(this).attr('cod');
      Swal.fire({
        title: 'Deletar esta aula?',
        text: "Você não poderá reverter a exclusão.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir!'
      }).then((result) => {
        if (result.isConfirmed) {
          jQuery.ajax({
            type: "POST",
            url: "crud/aulas/delete.php",
            data: { aula: aula },
            cache: true,
            success: function (data) {
              $("#aula_" + id).hide();
              $("#linkResultado").html(data);
            }
          });

          //Swal.fire(
          // 'Deletado!',
          // 'Atualizando....',
          //'success'
          //)


        }
      })
    });
  </script>


  <?php if (isset($_GET["salvo"])) { ?>
    <script type="application/javascript">
      Swal.fire({
        //position: 'top-end',
        icon: 'success',
        title: 'Salvo com sucesso',
        showConfirmButton: false,
        timer: 1500
      })
    </script>
  <?php } ?>


  <?php if (isset($_GET["aula_duplicada"])) { ?>
    <script type="application/javascript">
      Swal.fire({
        //position: 'top-end',
        icon: 'success',
        title: 'Aula duplicada com sucesso',
        showConfirmButton: false,
        timer: 1500
      })
    </script>
  <?php } ?>

  <?php if (isset($_GET["nova"])) { ?>
    <script type="application/javascript">
      locastyle.modal.open("#modal_cadastrarAula");
    </script>
  <?php } ?>



</body>

</html>