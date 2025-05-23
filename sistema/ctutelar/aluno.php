<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>



<?php
$colname_aluno = "-1";
if (isset($_GET['aluno'])) {
  $colname_aluno = anti_injection($_GET['aluno']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aluno = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, 
vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, 
aluno_id, aluno_nome, aluno_foto, aluno_nascimento, aluno_cpf, aluno_filiacao1, aluno_filiacao2, aluno_endereco, aluno_numero, aluno_bairro, aluno_cep, aluno_localizacao,
aluno_telefone, aluno_celular, aluno_email, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_tel_pai, aluno_tel_mae 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_aluno, "text"));
$aluno = mysql_query($query_aluno, $SmecelNovo) or die(mysql_error());
$row_aluno = mysql_fetch_assoc($aluno);
$totalRows_aluno = mysql_num_rows($aluno);

if ($totalRows_aluno==0) {
	header("Location:index.php?ops");
	exit;
	}

$hoje = date("Y-m-d");
$periodo_label = "HOJE";


$periodo = "";

if (isset($_GET['periodo']) && $_GET['periodo']<>"") {
  
  $periodo = $_GET['periodo'];
  
  switch ($periodo) {
    case 1:
        $data_final = $hoje;
		$data_inicio = $hoje;
		$periodo_label = "HOJE";
        break;
    case 2:
        $data_final = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
		$periodo_label = "ONTEM";
        break;
    case 5:
        $data_final = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-5 days",strtotime($hoje)));
		$periodo_label = "OS ÚLTIMOS 5 DIAS";
        break;
    case 7:
        $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-7 days",strtotime($hoje)));
		$periodo_label = "A ÚLTIMA SEMANA";
        break;
    case 30:
        $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-30 days",strtotime($hoje)));
		$periodo_label = "O ÚLTIMO MÊS";
        break;
    case 180:
        $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-180 days",strtotime($hoje)));
		$periodo_label = "OS ÚLTIMOS 6 MESES";
        break;
    case 365:
        $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
		$data_inicio = date('Y-m-d', strtotime("-365 days",strtotime($hoje)));
		$periodo_label = "O ÚLTIMO ANO";
        break;
	default:
        $data_final = $hoje;
		$data_inicio = $hoje;
		$periodo_label = "HOJE";
		break;
  }
  
  
  
  
} else {

if ((isset($_GET['data_final']) && ($_GET['data_final']<>"")) && (isset($_GET['data_inicio']) && ($_GET['data_inicio']<>""))) {
	
  $data_final = date("Y-m-d", strtotime($_GET['data_final']));
  $data_inicio = date("Y-m-d", strtotime($_GET['data_inicio']));
  
} else {
	
	$data_final = date("Y-m-d");
	$data_inicio = date("Y-m-d");
	
	}

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_id_turma, vinculo_aluno_hash,
COUNT(*) AS total_faltas
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_hash = '$colname_aluno' AND faltas_alunos_data BETWEEN '$data_inicio' AND '$data_final'
GROUP BY faltas_alunos_data
ORDER BY faltas_alunos_data DESC
";
$faltas = mysql_query($query_faltas, $SmecelNovo) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasComponente = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
disciplina_id, disciplina_nome, COUNT(faltas_alunos_disciplina_id) AS total_faltas_componente 
FROM smc_faltas_alunos
INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
WHERE faltas_alunos_matricula_id = '$row_faltas[faltas_alunos_matricula_id]' AND faltas_alunos_data BETWEEN '$data_inicio' AND '$data_final'
GROUP BY faltas_alunos_disciplina_id
";
$faltasComponente = mysql_query($query_faltasComponente, $SmecelNovo) or die(mysql_error());
$row_faltasComponente = mysql_fetch_assoc($faltasComponente);
$totalRows_faltasComponente = mysql_num_rows($faltasComponente);

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
<title>SMECEL - Sistema de Gestão Escolar</title>
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

  $(document).ready(function() {

    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
      },
      defaultDate: Date(),
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
	  
	  <?php do { ?>
  		{
          title: '<?php echo $row_faltas['total_faltas']; ?> faltas',
          start: '<?php echo $row_faltas['faltas_alunos_data']; ?>',
		  end:	 '<?php echo $row_faltas['faltas_alunos_data']; ?>',	
		  color: '#FF0000'
        },
	  <?php } while ($row_faltas = mysql_fetch_assoc($faltas)); ?>
      ]
    });

  });

</script>
<style>


  #calendar {
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
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?> - Aluno</h1>

<a href="alunos_faltas.php?periodo=<?php echo $periodo; ?>&data_inicio=<?php echo $data_inicio; ?>&data_final=<?php echo $data_final; ?>" class="ls-btn-primary">VOLTAR</a>
<br><br>
<p>FILTRO PARA <?php echo $periodo_label; ?></p>

<div class="ls-box-filter">
  <form action="aluno.php" class="ls-form ls-form-inline">
  <input type="hidden" name="aluno" value="<?php echo $colname_aluno; ?>">
    <label class="ls-label col-md-4 col-sm-4">
      <b class="ls-label-text">Período</b>
      <div class="ls-custom-select">
        <select name="periodo" id="select_period" class="ls-select">
            <option value="">Período</option>
            <option value="1">Hoje</option>
            <option value="2">Ontem</option>
            <option value="5">Últimos 5 dias</option>
            <option value="7">Última semana</option>
            <option value="30">Últimos 30 dias</option>
            <option value="180">Últimos 6 meses</option>
            <option value="365">Últimos 12 meses</option>
        </select>
      </div>
    </label>
    <label class="ls-label col-md-2 col-sm-2">
      <input type="date" name="data_inicio" class="" id="" value="<?php echo $data_inicio; ?>" autocomplete="off">
    </label>
    <label class="ls-label col-md-2 col-sm-2">
      <input type="date" name="data_final" class="" id="" value="<?php echo $data_final; ?>" autocomplete="off">
    </label>
    <label class="ls-label col-md-1 col-sm-1">
      <input type="submit" class="ls-btn-primary" value="Filtrar">
    </label>

  </form>
</div>  
    
 <div class="ls-box-group">
  <div class="ls-box ls-md-space">
    <div class="row">
      <div class="col-md-2 ls-txt-center">
	    <?php if ($row_aluno['aluno_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_aluno['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } ?>
      </div>
      <div class="col-md-10">
          <h1 class="ls-title-1 ls-color-theme"><?php echo $row_aluno['aluno_nome']; ?>, <?php echo idade($row_aluno['aluno_nascimento']); ?> anos</h1>
          <h3 class="ls-title-5"><strong>Filho(a) de <?php echo $row_aluno['aluno_filiacao1']; ?> <?php if ($row_aluno['aluno_filiacao2']<>"") { ?>e <?php echo $row_aluno['aluno_filiacao2']; ?><?php } ?></strong></h3>
          <p><strong><?php echo $row_aluno['aluno_endereco']; ?>, <?php if ($row_aluno['aluno_localizacao']==2) { echo "ZONA RURAL"; } else { ?> <?php echo $row_aluno['aluno_numero']; ?>, <?php echo $row_aluno['aluno_bairro']; ?>, <?php echo "ZONA URBANA"; } ?>, <?php echo $row_aluno['aluno_cep']; ?></strong></p>
      	  <p><strong>Contatos: </strong><br><br>
          Tel 1: <strong><?php echo $row_aluno['aluno_telefone']; ?></strong><br>
          Tel 2: <strong><?php echo $row_aluno['aluno_celular']; ?></strong><br>
          Pai: <strong><?php echo $row_aluno['aluno_tel_pai']; ?></strong><br>
          Mãe: <strong><?php echo $row_aluno['aluno_tel_mae']; ?></strong><br>
          Emergência 1: <strong><?php echo $row_aluno['aluno_emergencia_tel1']; ?></strong><br>
          Emergência 2: <strong><?php echo $row_aluno['aluno_emergencia_tel2']; ?></strong><br>
          Email: <strong><?php echo $row_aluno['aluno_email']; ?></strong><br>
          </p>
      </div>
    </div>
  </div>

<hr>

<div class="row">
  <div class="col-md-8 col-sm-12">
  	<div id='calendar'></div>
    <p>&nbsp;</p>  
<p>&nbsp;</p>  
<p>&nbsp;</p>  

  </div>
  
  
  <div class="col-md-4 col-sm-12">
  
  <?php if ($totalRows_faltasComponente == 0) { ?>
  
  <small>NENHUMA FALTA REGISTRADA</small>
    <?php } else { ?>
    
  
  <table class="ls-table">
  <tr><th class="ls-txt-center">COMPONENTE</th><th width="150" class="ls-txt-center">AULAS/FALTAS</th></tr>
  
  <?php do { ?>
  <tr>
  	<td class="ls-txt-center"><?php echo $row_faltasComponente['disciplina_nome']; ?></td><td class="ls-txt-center"><?php echo $row_faltasComponente['total_faltas_componente']; ?></td>
  </tr>
  <?php } while ($row_faltasComponente = mysql_fetch_assoc($faltasComponente)); ?>
  </table>

  <?php } ?>
  </div>
</div>


  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js1"></script> 
<script src="css/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>


<script type="application/javascript">
</script>
</body>
</html>
<?php
mysql_free_result($aluno);
mysql_free_result($faltasComponente);
mysql_free_result($faltas);
?>