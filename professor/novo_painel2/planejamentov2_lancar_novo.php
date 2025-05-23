<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

$turma = "-1";
if (isset($_GET['turma']) AND $_GET['turma'] <> '') {
  $turma = anti_injection($_GET['turma']);
}else{
    die(header("Location: index.php?error"));
}

$escola = "-1";
if (isset($_GET['escola'])AND $_GET['escola'] <> '') {
  $escola = anti_injection($_GET['escola']);
}else{
    die(header("Location: index.php?error"));
}

$componente = "-1";
if (isset($_GET['componente'])AND $_GET['componente'] <> '') {
  $componente = anti_injection($_GET['componente']);
}else{
    die(header("Location: index.php?error"));
}



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $queryInsert = "INSERT INTO smc_planejamento (smc_id_professor, smc_id_escola, smc_id_turma, smc_ano_letivo, smc_planejamento_data_inicial, smc_planejamento_data_final, smc_id_periodo) VALUES (:professor, :escola, :turma, :ano_letivo, :data_inicial, :data_final, :periodo)";
  $stmtInsert = $SmecelNovo->prepare($queryInsert);
  $stmtInsert->bindValue(':professor', ID_PROFESSOR, PDO::PARAM_INT);
  $stmtInsert->bindValue(':escola', $escola, PDO::PARAM_INT);
  $stmtInsert->bindValue(':turma', $turma, PDO::PARAM_INT);
  $stmtInsert->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_STR);
  $stmtInsert->bindValue(':data_inicial', $_POST['plan_data_inicial'], PDO::PARAM_STR);
  $stmtInsert->bindValue(':data_final', $_POST['plan_data_final'], PDO::PARAM_STR);
  $stmtInsert->bindValue(':periodo', $_POST['plan_periodo'], PDO::PARAM_STR);
  $stmtInsert->execute();
  $plan = $SmecelNovo->lastInsertId();

  $queryInsertComponente = "INSERT INTO smc_planejamento_componente (smc_id_planejamento, smc_id_componente) VALUES (:plan, :componente)";
  $stmtInsertComponente = $SmecelNovo->prepare($queryInsertComponente);
  $stmtInsertComponente->bindValue(':plan', $plan, PDO::PARAM_INT);
  $stmtInsertComponente->bindValue(':componente', $componente, PDO::PARAM_INT);
  $stmtInsertComponente->execute();

  $insertGoTo = "planejamentov2_editar.php?escola=$escola&turma=$turma&plan=$plan";
  header(sprintf("Location: %s", $insertGoTo));
}

$query_periodo = "SELECT * FROM smc_unidades WHERE per_unid_id_ano = :ano_letivo AND per_unid_id_sec = :sec_id ORDER BY per_unid_periodo ASC";
$stmtPeriodos = $SmecelNovo->prepare($query_periodo);
$stmtPeriodos->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_id'], PDO::PARAM_INT);
$stmtPeriodos->bindValue(':sec_id', $row_Secretaria['sec_id'], PDO::PARAM_INT);
$stmtPeriodos->execute();
$rowPeriodos = $stmtPeriodos->fetchAll(PDO::FETCH_ASSOC);
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

  <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <h4 class="ls-modal-title">NOVO PLANEJAMENTO</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <form method="post" name="form2" class="ls-form ls-form-horizontal row" action="<?php echo $editFormAction; ?>">
          <label class="ls-label col-xs-12">
            <b class="ls-label-text">Período</b>
            <p class="ls-label-info">Informe o período</p>
            <div class="ls-custom-select">
              <select class="ls-select" name="plan_periodo" required>
                <option value="">SELECIONE O PERÍODO</option>
                <?php foreach ($rowPeriodos as $periodo): ?>
                  <option value="<?= $periodo['per_unid_id'] ?>"><?= $periodo['per_unid_periodo'] ?>°</option>
                <?php endforeach; ?>
              </select>
            </div>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">DE</b>
            <p class="ls-label-info">Informe a data inicial</p>
            <input type="date" name="plan_data_inicial" size="32" required>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">ATÉ</b>
            <p class="ls-label-info">Informe a data final</p>
            <input type="date" name="plan_data_final" size="32" required>
          </label>
          <input type="submit" class="ls-btn-primary ls-btn-lg ls-btn-block" value="REGISTRAR PLANEJAMENTO">
          <input type="hidden" name="MM_insert" value="form2">
        </form>
      </div>
    </div>
  </div><!-- /.modal -->

  <main class="ls-main">
    <div class="container-fluid">

    </div>
    </div>







    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>

    <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js"
      referrerpolicy="origin"></script>
    <script src="langs/pt_BR.js"></script>
    <script>
      locastyle.modal.open("#myAwesomeModal");

      tinymce.init({
        selector: 'textarea',
        resize: true,
        theme_advanced_source_editor_width: '100%',

        mobile: {
          menubar: false
        },

        images_upload_url: 'postAcceptor.php',
        automatic_uploads: true,
        imagetools_proxy: 'proxy.php',

        //plugins: 'emoticons',
        //toolbar: 'emoticons',

        //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',

        height: 200,
        toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
        plugins: ['textcolor', 'advlist autolink link image imagetools lists charmap print preview paste emoticons',
          'advlist autolink lists link image imagetools charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table paste code help wordcount'],
        //force_br_newlines : false,
        //force_p_newlines : false,
        //forced_root_block : '',	
        statusbar: false,
        language: 'pt_BR',
        menubar: false,
        paste_as_text: true,
        content_css: '//www.tinymce.com/css/codepen.min.css'
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

