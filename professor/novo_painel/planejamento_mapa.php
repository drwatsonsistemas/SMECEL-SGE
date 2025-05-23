<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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


  <link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.min.css' rel='stylesheet' />
  <link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
  <script src='<?php echo URL_BASE; ?>sistema/js/moment.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/jquery.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/fullcalendar.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/pt-br.js'></script>


</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
      <p>
        <a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
        <hr>
        <a href="planejamento.php" class="ls-btn-primary ls-btn-lg ls-btn-block">PLANEJAMENTO</a>
        <a href="planejamento_turma.php" class="ls-btn-primary ls-btn-lg ls-btn-block">PLANEJAMENTO POR TURMA</a>
        <a href="planejamentov2.php" class="ls-btn-primary ls-btn-lg ls-btn-block">PLANEJAMENTO QUINZENAL</a>
        <a href="planejamento_anual.php" class="ls-btn-primary ls-btn-lg ls-btn-block">PLANEJAMENTO ANUAL</a>
        <a href="planejamento_ver.php" class="ls-btn-primary ls-ico ls-btn-lg ls-btn-block">LISTAGEM</a>
        <a href="planejamento_mapa_ver.php" class="ls-btn-primary ls-ico ls-btn-lg ls-btn-block">MAPA DE PLANEJAMENTOS</a>
      </p>



    </div>
    <?php //include_once "inc/footer.php"; ?>
  </main>
  <?php include_once "inc/notificacoes.php"; ?>
  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js1"></script>
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