<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Calendario = "
SELECT ce_id, ce_id_sec, ce_ano, ce_data, ce_tipo, ce_descricao,
CASE ce_tipo
WHEN 1 THEN 'DIA LETIVO' 
WHEN 2 THEN 'SAB. LETIVO'
WHEN 3 THEN 'FER. NAC.'
WHEN 4 THEN 'FER. MUN.'
WHEN 5 THEN 'REC. JUNINO'
WHEN 6 THEN 'REC. DE NATAL'
WHEN 7 THEN 'JORNADA PEDAGÓGICA'
WHEN 8 THEN 'ENCONTRO P/ PLANEJAMENTO'
WHEN 9 THEN 'CONSELHO DE CLASSE'
WHEN 10 THEN 'ESTUDO DE RECUPERAÇÃO'
WHEN 11 THEN 'ANO LETIVO 2020'
WHEN 12 THEN 'ANO LETIVO 2021'
WHEN 13 THEN 'PLANTÃO PEDAGÓGICO'
WHEN 14 THEN 'CONSELHO DE CLASSE'
WHEN 15 THEN 'RESULTADOS FINAIS'
END AS ce_tipo_nome,
CASE ce_tipo
WHEN 1 THEN '#228B22' 
WHEN 2 THEN '#008000'
WHEN 3 THEN '#B8860B'
WHEN 4 THEN '#DAA520'
WHEN 5 THEN '#DEB887'
WHEN 6 THEN '#D2B48C'
WHEN 7 THEN '#F08080'
WHEN 8 THEN '#E9967A'
WHEN 9 THEN '#FF8C00'
WHEN 10 THEN '#FF4500'
WHEN 11 THEN '#F0E68C'
WHEN 12 THEN '#D8BFD8'
WHEN 13 THEN '#EEE8AA'
WHEN 14 THEN '#BA55D3'
WHEN 15 THEN '#6B8E23'
END AS ce_cor
FROM smc_calendario_escolar
WHERE ce_id_sec = ".SEC_ID." AND ce_ano = ".ANO_LETIVO."";
$Calendario = mysql_query($query_Calendario, $SmecelNovo) or die(mysql_error());
$row_Calendario = mysql_fetch_assoc($Calendario);
$totalRows_Calendario = mysql_num_rows($Calendario);
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
          title: '<?php echo $row_Calendario['ce_tipo_nome']; ?> <?php echo $row_Calendario['ce_descricao']; ?>',
          start: '<?php echo $row_Calendario['ce_data']; ?>',
		  end:	 '<?php echo $row_Calendario['ce_data']; ?>',	
		  color: '<?php echo $row_Calendario['ce_cor']; ?>',
		  borderColor: '<?php echo $row_Calendario['ce_cor']; ?>',
		  description: '<?php echo $row_Calendario['ce_descricao']; ?>',
        },
    <?php } while ($row_Calendario = mysql_fetch_assoc($Calendario)); ?>
      ],
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
    <h1 class="ls-title-intro ls-ico-home">CALENDÁRIO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    <div class="row">
      <div class="col-md-10 col-sm-12">
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
mysql_free_result($Calendario);
?>
