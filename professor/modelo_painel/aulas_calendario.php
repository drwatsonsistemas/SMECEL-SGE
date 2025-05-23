<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TodasAulas = "
SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_publicado, plano_aula_hash, disciplina_id, disciplina_nome, disciplina_cor_fundo 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_professor = ".ID_PROFESSOR."
ORDER BY plano_aula_data DESC
";
$TodasAulas = mysql_query($query_TodasAulas, $SmecelNovo) or die(mysql_error());
$row_TodasAulas = mysql_fetch_assoc($TodasAulas);
$totalRows_TodasAulas = mysql_num_rows($TodasAulas);
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" href="css/sweetalert2.min.css">

<link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.min.css' rel='stylesheet' />
<link href='<?php echo URL_BASE; ?>sistema/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='<?php echo URL_BASE; ?>sistema/js/moment.min.js'></script>
<script src='<?php echo URL_BASE; ?>sistema/js/jquery.min.js'></script>
<script src='<?php echo URL_BASE; ?>sistema/js/fullcalendar.min.js'></script>
<script src='<?php echo URL_BASE; ?>sistema/js/pt-br.js'></script>

<script>

  $(document).ready(function() {

    $('#aulas_calendario').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
      },
	  views: {
       listWeek: {buttonText: 'Agenda'},

   },   
      defaultDate: Date(),
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
	<?php do { ?>
	  {
          title: '<?php echo $row_TodasAulas['disciplina_nome']; ?>',
          start: '<?php echo $row_TodasAulas['plano_aula_data']; ?>',
		  end:	 '<?php echo $row_TodasAulas['plano_aula_data']; ?>',	
		  color: '<?php echo $row_TodasAulas['disciplina_cor_fundo']; ?>'
        },
    <?php } while ($row_TodasAulas = mysql_fetch_assoc($TodasAulas)); ?>
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
    <h1 class="ls-title-intro ls-ico-home">MAPA DE AULAS</h1>
    
    <p>
    <a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
    <a href="selecionar.php?target=aulas" class="ls-btn-primary ls-ico-plus">REGISTRAR AULA</a>
    </p>
    
    <br>



<div class="row">
  <div class="col-md-12 col-sm-12">
  	<div id='aulas_calendario'></div>
    <p>&nbsp;</p>  
 

  </div>
    
    
        

    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js1"></script> 
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
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
mysql_free_result($TodasAulas);
?>