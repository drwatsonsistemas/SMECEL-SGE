<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php
$colname_Escolas_Turmas = "-1";
if (isset($_GET['escola'])) {
  $colname_Escolas_Turmas = $_GET['escola'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_multisseriada,
escola_id, escola_nome,
CASE turma_turno 
WHEN 0 THEN 'INT' 
WHEN 1 THEN 'MAT' 
WHEN 2 THEN 'VES' 
WHEN 3 THEN 'NOT' 
END AS turma_turno_nome
FROM smc_turma 
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_ano_letivo = ".ANO_LETIVO." AND turma_id_escola = %s
ORDER BY turma_turno, turma_etapa, turma_nome ASC
", GetSQLValueString($colname_Escolas_Turmas, ""));
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

//ALUNOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, turma_id, turma_turno
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_sec = %s AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND turma_turno <> 3 AND vinculo_aluno_id_escola = '$colname_Escolas_Turmas'
", GetSQLValueString(SEC_ID, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

//SAUDE BUCAL
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_saude_bucal = "
SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
FROM sms_pse_saude_bucal
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = pse_s_bucal_matricula_id
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escolas_Turmas'
";
$saude_bucal = mysql_query($query_saude_bucal, $SmecelNovo) or die(mysql_error());
$row_saude_bucal = mysql_fetch_assoc($saude_bucal);
$totalRows_saude_bucal = mysql_num_rows($saude_bucal);

//ANTROPOMETRIA
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_antropometria = "
SELECT antrop_id, antrop_id_aluno, antrop_id_matricula, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao 
FROM sms_pse_antropometria 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = antrop_id_matricula
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escolas_Turmas'
";
$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
$row_antropometria = mysql_fetch_assoc($antropometria);
$totalRows_antropometria = mysql_num_rows($antropometria);


//CONSUMO ALIMENTAR
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_calimentar = "
SELECT cons_alim_id, cons_alim_id_aluno, cons_alim_id_matricula,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
FROM sms_pse_consumo_alimentar
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = cons_alim_id_matricula
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escolas_Turmas'
";
$calimentar = mysql_query($query_calimentar, $SmecelNovo) or die(mysql_error());
$row_calimentar = mysql_fetch_assoc($calimentar);
$totalRows_calimentar = mysql_num_rows($calimentar);

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
    <h1 class="ls-title-intro ls-ico-home">Turmas - Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
        
        <div class="ls-box">
          <h5 class="ls-title-3"><?php echo $row_Turmas['escola_nome']; ?></h5>
          <p></p>
        </div>
    
    <a href="escolas.php" class="ls-btn">VOLTAR</a>
    

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em <strong><?php echo date("d/m/Y"); ?></strong></p>
    <h2 class="ls-title-3">Acompanhamento na Unidade Escolar</h2>
  </header>

  <div id="sending-stats" class="row">
    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Matrículas ativas</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-info"><?php echo $totalRows_Alunos; ?></strong><small>aluno(as)</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <small>Apenas turno diurno</small>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Saúde Bucal</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-warning"><?php echo  number_format(($totalRows_saude_bucal / $totalRows_Alunos)*100, 1); ?>%</strong><small><?php echo $totalRows_saude_bucal; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo  number_format(($totalRows_saude_bucal / $totalRows_Alunos)*100, 2); ?>" class="ls-animated"></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4 color-default">Consumo Alimentar</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-theme"><?php echo number_format(($totalRows_calimentar / $totalRows_Alunos)*100, 1); ?>%</strong><small><?php echo $totalRows_calimentar; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo number_format(($totalRows_calimentar / $totalRows_Alunos)*100, 2); ?>" class="ls-animated"></div>
        </div>
      </div>
    </div>
    
    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Antopometria</h6>
        </div>
        <div class="ls-box-body">
          <span class="ls-board-data">
            <strong class="ls-color-success"><?php echo  number_format(($totalRows_antropometria / $totalRows_Alunos)*100, 1); ?>%</strong><small><?php echo $totalRows_antropometria; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo  number_format(($totalRows_antropometria / $totalRows_Alunos)*100, 2); ?>" class="ls-animated"></div>
        </div>
      </div>
    </div>

    
  </div>

</div>    


    <table class="ls-table ls-sm-space">
      <thead>
      <tr>
        <th class="ls-txt-center ls-display-none-xs" width="40"></th>
        <th width="200" class="ls-txt-center">TURMA</th>
        <th width="" class="ls-txt-center">SB</th>
        <th width="" class="ls-txt-center">CA</th>
        <th width="" class="ls-txt-center">AN</th>
      </tr>
      </thead>
      <tbody>
      <?php $num = 1; do { ?>
        <tr>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $num; $num++; ?></td>
          <td><a href="alunos.php?turma=<?php echo $row_Turmas['turma_id']; ?>&escola=<?php echo $row_Turmas['turma_id_escola']; ?>"><?php echo $row_Turmas['turma_nome']; ?></a><br><small><?php echo $row_Turmas['turma_turno_nome']; ?></small></td>
          
          
          <?php 
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Alunos1 = "
			SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
			vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
			vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
			vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval
			FROM smc_vinculo_aluno
			WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			";
			$Alunos1 = mysql_query($query_Alunos1, $SmecelNovo) or die(mysql_error());
			$row_Alunos1 = mysql_fetch_assoc($Alunos1);
			$totalRows_Alunos1 = mysql_num_rows($Alunos1);
			
			//SAUDE BUCAL
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_saude_bucal1 = "
			SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id,
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
			FROM sms_pse_saude_bucal
			INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = pse_s_bucal_matricula_id
			WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			";
			$saude_bucal1 = mysql_query($query_saude_bucal1, $SmecelNovo) or die(mysql_error());
			$row_saude_bucal1 = mysql_fetch_assoc($saude_bucal1);
			$totalRows_saude_bucal1 = mysql_num_rows($saude_bucal1);
			
			//ANTROPOMETRIA
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria1 = "
			SELECT antrop_id, antrop_id_aluno, antrop_id_matricula, 
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao 
			FROM sms_pse_antropometria 
			INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = antrop_id_matricula
			WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			";
			$antropometria1 = mysql_query($query_antropometria1, $SmecelNovo) or die(mysql_error());
			$row_antropometria1 = mysql_fetch_assoc($antropometria1);
			$totalRows_antropometria1 = mysql_num_rows($antropometria1);
			
			
			//CONSUMO ALIMENTAR
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_calimentar1 = "
			SELECT cons_alim_id, cons_alim_id_aluno, cons_alim_id_matricula,
			vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
			FROM sms_pse_consumo_alimentar
			INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = cons_alim_id_matricula
			WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			";
			$calimentar1 = mysql_query($query_calimentar1, $SmecelNovo) or die(mysql_error());
			$row_calimentar1 = mysql_fetch_assoc($calimentar1);
			$totalRows_calimentar1 = mysql_num_rows($calimentar1);
			
			
			
		  
		  ?>
          
          <td class="ls-txt-center"><?php $sb = number_format(($totalRows_saude_bucal1 / $totalRows_Alunos1)*100, 0); ?>	<span class="ls-display-none-lg ls-display-none-md ls-display-none-sm ls-tag"><small><?php echo $sb ?>%</small></span> <span class="ls-display-none-xs"><?php if ($sb>0) { echo "<div data-ls-module=\"progressBar\" role=\"progressbar\" aria-valuenow=\"$sb\" class=\"ls-animated\"></div>"; } else { echo "-"; } ; ?></span></td>
          <td class="ls-txt-center"><?php $ca = number_format(($totalRows_calimentar1 / $totalRows_Alunos1)*100, 0); ?>		<span class="ls-display-none-lg ls-display-none-md ls-display-none-sm ls-tag"><small><?php echo $ca ?>%</small></span> <span class="ls-display-none-xs"><?php if ($ca>0) { echo "<div data-ls-module=\"progressBar\" role=\"progressbar\" aria-valuenow=\"$ca\" class=\"ls-animated\"></div>"; } else { echo "-"; } ; ?></span></td>
          <td class="ls-txt-center"><?php $an = number_format(($totalRows_antropometria1 / $totalRows_Alunos1)*100, 0); ?>	<span class="ls-display-none-lg ls-display-none-md ls-display-none-sm ls-tag"><small><?php echo $an ?>%</small></span> <span class="ls-display-none-xs"><?php if ($an>0) { echo "<div data-ls-module=\"progressBar\" role=\"progressbar\" aria-valuenow=\"$an\" class=\"ls-animated\"></div>"; } else { echo "-"; } ; ?></span></td>
        </tr>
        <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
        </tbody>
    </table>
    
    <p>&nbsp;</p>
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="application/javascript">
/*
Swal.fire({
  Position: 'top-end',
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
mysql_free_result($Turmas);
?>
