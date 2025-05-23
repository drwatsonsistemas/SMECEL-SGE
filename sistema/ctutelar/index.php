<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php

$hoje = date("Y-m-d");
$ontem = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
$semana = date('Y-m-d', strtotime("-7 days",strtotime($hoje)));
$mes = date('Y-m-d', strtotime("-30 days",strtotime($hoje)));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = ".SEC_ID." AND faltas_alunos_data BETWEEN '$hoje' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltas = mysql_query($query_faltas, $SmecelNovo) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasOntem = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = ".SEC_ID." AND faltas_alunos_data BETWEEN '$ontem' AND '$ontem'
GROUP BY faltas_alunos_matricula_id
";
$faltasOntem = mysql_query($query_faltasOntem, $SmecelNovo) or die(mysql_error());
$row_faltasOntem = mysql_fetch_assoc($faltasOntem);
$totalRows_faltasOntem = mysql_num_rows($faltasOntem); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasSemana = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = ".SEC_ID." AND faltas_alunos_data BETWEEN '$semana' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltasSemana = mysql_query($query_faltasSemana, $SmecelNovo) or die(mysql_error());
$row_faltasSemana = mysql_fetch_assoc($faltasSemana);
$totalRows_faltasSemana = mysql_num_rows($faltasSemana); 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasMes = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = ".SEC_ID." AND faltas_alunos_data BETWEEN '$mes' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltasMes = mysql_query($query_faltasMes, $SmecelNovo) or die(mysql_error());
$row_faltasMes = mysql_fetch_assoc($faltasMes);
$totalRows_faltasMes = mysql_num_rows($faltasMes); 

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
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home1"><img src="images/logo.png" width="45"> CONSELHO TUTELAR - Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
        

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em <strong><?php echo date("d/m/Y à\s H:i\h"); ?></strong></p>
    <h2 class="ls-title-3">Resumo de faltas no período</h2>
  </header>

  <div id="sending-stats" class="row">
    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">HOJE</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_faltas; ?><small>FALTAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
        <a href="alunos_faltas.php?periodo=1&data_inicio=&data_final=" class="ls-btn ls-btn-xs">VISUALIZAR</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">ONTEM</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_faltasOntem; ?><small>FALTAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
        <a href="alunos_faltas.php?periodo=2&data_inicio=&data_final=" class="ls-btn ls-btn-xs">VISUALIZAR</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">SEMANA</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_faltasSemana; ?><small>FALTAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
        <a href="alunos_faltas.php?periodo=7&data_inicio=&data_final=" class="ls-btn ls-btn-xs">VISUALIZAR</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">MÊS</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong><?php echo $totalRows_faltasMes; ?><small>FALTAS</small></strong>
          </span>
        </div>
        <div class="ls-box-footer">
        <a href="alunos_faltas.php?periodo=30&data_inicio=&data_final=" class="ls-btn ls-btn-xs">VISUALIZAR</a>
        </div>
      </div>
    </div>




  </div>

</div>    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="css/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
  <?php if (isset($_GET["ops"])) { ?>
  <script type="application/javascript">
	Swal.fire({
	  icon: 'error',
	  title: 'Oops...',
	  text: 'Algo deu errado!'
	})
 </script>
  <?php } ?>
</body>
</html>
<?php
mysql_free_result($faltas);
mysql_free_result($faltasOntem);
mysql_free_result($faltasSemana);
mysql_free_result($faltasMes);
?>