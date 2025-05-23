<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php
try {
  // Define a query
  $query_planejamentos = "
        SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, 
        ac_objeto_conhecimento, ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, ac_da_participar, ac_da_explorar, ac_da_expressar, 
        ac_da_conhecerse, ac_ce_eo, ac_ce_ts, ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di, disciplina_id, disciplina_nome, disciplina_cor_fundo 
        FROM smc_ac 
        INNER JOIN smc_disciplina ON disciplina_id = ac_id_componente
        WHERE ac_ano_letivo = :ano_letivo AND ac_id_professor = :professor_id
        ORDER BY ac_id DESC";

  // Prepara a consulta
  $stmt = $SmecelNovo->prepare($query_planejamentos);

  // Bind dos valores
  $stmt->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
  $stmt->bindValue(':professor_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);

  // Executa a consulta
  $stmt->execute();

  // Armazena os resultados
  $planejamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_planejamentos = count($planejamentos);
} catch (PDOException $e) {
  die("Erro ao buscar os planejamentos: " . $e->getMessage());
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


  <link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.min.css' rel='stylesheet' />
  <link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
  <script src='<?php echo URL_BASE; ?>sistema/js/moment.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/jquery.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/fullcalendar.min.js'></script>
  <script src='<?php echo URL_BASE; ?>sistema/js/pt-br.js'></script>

  <script>

    $(document).ready(function () {

      $('#aulas_calendario').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay,listWeek'
        },
        views: {
          listWeek: { buttonText: 'Agenda' },

        },
        defaultDate: Date(),
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        eventLimit: true, // allow "more" link when too many events
        events: [
          <?php foreach ($planejamentos as $planejamento): ?>
      {
              title: '<?php echo htmlspecialchars($planejamento['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>',
              start: '<?php echo htmlspecialchars($planejamento['ac_data_inicial'], ENT_QUOTES, 'UTF-8'); ?> 00:00:00',
              end: '<?php echo htmlspecialchars($planejamento['ac_data_final'], ENT_QUOTES, 'UTF-8'); ?> 23:59:59',
              color: '<?php echo htmlspecialchars($planejamento['disciplina_cor_fundo'], ENT_QUOTES, 'UTF-8'); ?>',
              url: 'planejamento_editar.php?escola=<?php echo htmlspecialchars($planejamento['ac_id_escola'], ENT_QUOTES, 'UTF-8'); ?>&etapa=<?php echo htmlspecialchars($planejamento['ac_id_etapa'], ENT_QUOTES, 'UTF-8'); ?>&componente=<?php echo htmlspecialchars($planejamento['ac_id_componente'], ENT_QUOTES, 'UTF-8'); ?>&ac=<?php echo htmlspecialchars($planejamento['ac_id'], ENT_QUOTES, 'UTF-8'); ?>'
            },
          <?php endforeach; ?>

        ]
      });

    });

  </script>
  <style>
    #aulas_calendario {
      font-size: small;
      max-width: 100%;
      margin: 0 auto;
    }
  </style>

</head>

<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
      <p>
        <a href="planejamento_mapa.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
      </p>
      <hr>




      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div id='aulas_calendario'></div>
          <p>&nbsp;</p>
        </div>
      </div>


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