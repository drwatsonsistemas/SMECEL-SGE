<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php";
include "fnc/anti_injection.php";
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-117872281-1');
</script>
<title>PROFESSOR | <?php echo htmlspecialchars($row_ProfLogado['func_nome'], ENT_QUOTES, 'UTF-8'); ?> | SMECEL - Sistema de Gestão Escolar</title>
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
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
<script src='<?php echo URL_BASE; ?>sistema/js/pt-br.js'></script>


</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">MAPA DE AULAS</h1>
    <p>
        <a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a>

        <hr>

        <a href="aulas_data.php" class="ls-btn-primary ls-btn-lg ls-ico-plus ls-btn-block">REGISTRAR AULA</a>
        <a href="aulas_avulsas.php" class="ls-btn-primary ls-btn-lg ls-ico-plus ls-btn-block">REGISTRAR AULA AVULSA</a>
        <a href="mapa_aulas.php" class="ls-btn-primary ls-btn-lg ls-ico-folder-open ls-btn-block">MAPA DE AULAS</a>
        <a href="aulas_calendario_ver.php" class="ls-btn-primary ls-btn-lg ls-ico-folder-open ls-btn-block">CALENDÁRIO DE AULAS</a>

        
    </p>
<br>
    <div class="ls-alert-warning"><strong>Registrar aula:</strong> Sistema segue quadro de horários registrado na escola e o professor só consegue registrar as aulas conforme horário estabelecido previamente.<br><br><strong>Registrar aula avulsa:</strong> Professor consegue registrar aula em qualquer turma, componente ou campo de experiência na escola, independente do quadro de horários.</div>

  </div>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="js/locastyle.js"></script>
<script src="js/sweetalert2.min.js"></script>
</body>
</html>
