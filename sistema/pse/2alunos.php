<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php
$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = $_GET['turma'];
}

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = $_GET['escola'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_nascimento, turma_id, turma_nome, turma_turno, 
CASE turma_turno 
WHEN 0 THEN 'INTEGRAL' 
WHEN 1 THEN 'MATUTINO' 
WHEN 2 THEN 'VESPERTINO' 
WHEN 3 THEN 'NOTURNO' 
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN '<span class=\"ls-tag-success\">MATRICULADO</span>'
WHEN 2 THEN '<span class=\"ls-tag-warning\">TRANSFERIDO</span>'
WHEN 3 THEN '<span class=\"ls-tag-warning\">DEIXOU DE FREQUENTAR</span>'
WHEN 4 THEN '<span class=\"ls-tag-warning\">FALECIDO</span>'
WHEN 5 THEN '<span class=\"ls-tag-warning\">OUTROS</span>'
END AS vinculo_aluno_situacao 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_turma = %s AND vinculo_aluno_situacao = '1'
ORDER BY aluno_nome ASC
", GetSQLValueString($colname_Alunos, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

//ALUNOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos1 = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, turma_id, turma_turno
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_sec = %s AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND turma_turno <> 3 AND vinculo_aluno_id_escola = '$colname_Escola' AND vinculo_aluno_id_turma = '$colname_Alunos'
", GetSQLValueString(SEC_ID, "int"));
$Alunos1 = mysql_query($query_Alunos1, $SmecelNovo) or die(mysql_error());
$row_Alunos1 = mysql_fetch_assoc($Alunos1);
$totalRows_Alunos1 = mysql_num_rows($Alunos1);

//SAUDE BUCAL
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_saude_bucal = "
SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
FROM sms_pse_saude_bucal
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = pse_s_bucal_matricula_id
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escola' AND vinculo_aluno_id_turma = '$colname_Alunos'
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
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escola' AND vinculo_aluno_id_turma = '$colname_Alunos'
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
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."' AND vinculo_aluno_id_escola = '$colname_Escola' AND vinculo_aluno_id_turma = '$colname_Alunos'
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
    <h1 class="ls-title-intro ls-ico-home">Alunos - Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

        <div class="ls-box">
          <h5 class="ls-title-3"><?php echo $row_Alunos['turma_nome']; ?> - <?php echo $row_Alunos['turma_turno_nome']; ?></h5>
        </div>

    
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
            <strong class="ls-color-info"><?php echo $totalRows_Alunos1; ?></strong><small>aluno(as)</small>
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
            <strong class="ls-color-warning"><?php echo  number_format(($totalRows_saude_bucal / $totalRows_Alunos1)*100, 1); ?>%</strong><small><?php echo $totalRows_saude_bucal; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo  number_format(($totalRows_saude_bucal / $totalRows_Alunos1)*100, 2); ?>" class="ls-animated"></div>
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
            <strong class="ls-color-theme"><?php echo number_format(($totalRows_calimentar / $totalRows_Alunos1)*100, 1); ?>%</strong><small><?php echo $totalRows_calimentar; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo number_format(($totalRows_calimentar / $totalRows_Alunos1)*100, 2); ?>" class="ls-animated"></div>
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
            <strong class="ls-color-success"><?php echo  number_format(($totalRows_antropometria / $totalRows_Alunos1)*100, 1); ?>%</strong><small><?php echo $totalRows_antropometria; ?> atendimentos</small>
          </span>
        </div>
        <div class="ls-box-footer">
          <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo  number_format(($totalRows_antropometria / $totalRows_Alunos1)*100, 2); ?>" class="ls-animated"></div>
        </div>
      </div>
    </div>


  </div>

</div>    

    		<div class="ls-group-btn ls-group-active">
              <a href="turmas.php?escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-ico-chevron-left">&nbsp;</a>
              <a href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-info">S. BUC.</a>
              <a href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-success">C. ALIM.</a>
              <a href="antropometria.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-warning">ANTROP.</a>
            </div>
            
    
    <h4 class="ls-title-4 ls-txt-center">VISÃO GERAL</h4>


<div data-ls-module="dropdown" class="ls-dropdown ls-float-right">
  <a href="" class="ls-btn ls-ico-paint-format  ls-ico-right"> Relatórios da turma </a>
  <ul class="ls-dropdown-nav">
      <li><a target="_blank" href="relatorios/rel_alunos.php?turma=<?php echo $colname_Alunos; ?>">Relação de Alunos</a></li>
      <li><a target="_blank" href="relatorios/rel_alunos_carteira_vacina.php?turma=<?php echo $colname_Alunos; ?>">Situação Vacinal</a></li>
      <li><a target="_blank" href="relatorios/rel_alunos_endereco_contato.php?turma=<?php echo $colname_Alunos; ?>&inep=">Dados de Contato</a></li>
      <li><a target="_blank" href="rel_alunos_bolsa_familia.php?turma=<?php echo $colname_Alunos; ?>&inep=">Bolsa-família</a></li>
      
      </ul>
</div>

    <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center ls-display-none-xs" width="40"></th>
        <th class="ls-txt-center ls-display-none-xs" width="60">MAT</th>
        <th>ALUNO</th>
        <th class="ls-txt-center" width="40">SB</th>
        <th class="ls-txt-center" width="40">CA</th>
        <th class="ls-txt-center" width="40">AN</th>
      </tr>
      </thead>
      <tbody>
      <?php $num = 1; do { ?>
        <tr>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $num; $num++; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
          <td><?php echo $row_Alunos['aluno_nome']; ?></td>
          <td class="ls-txt-center">
		   <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_saude_bucal = "
			SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id, pse_s_bucal_data, pse_s_bucal_qtd_dentes, pse_s_bucal_decidua, pse_s_bucal_permanente, pse_s_bucal_doenca_periodontal, pse_s_bucal_gengivite,
			pse_s_bucal_c1, pse_s_bucal_p1, pse_s_bucal_o1, pse_s_bucal_cpod1, 
			pse_s_bucal_c2, pse_s_bucal_ei2, pse_s_bucal_o2, pse_s_bucal_ceod2, 
			pse_s_bucal_observacoews, pse_s_bucal_inicio_tratamento, pse_s_bucal_final_tratamento, pse_s_bucal_inicio_tratamento_data_hora, 
			pse_s_bucal_cirurgiao_dentista, pse_s_bucal_asb 
			FROM sms_pse_saude_bucal
			WHERE pse_s_bucal_matricula_id = '$row_Alunos[vinculo_aluno_id]'
			ORDER BY pse_s_bucal_id DESC
			";
			$saude_bucal = mysql_query($query_saude_bucal, $SmecelNovo) or die(mysql_error());
			$row_saude_bucal = mysql_fetch_assoc($saude_bucal);
			$totalRows_saude_bucal = mysql_num_rows($saude_bucal);
			
			if ($totalRows_saude_bucal > 0) {
				echo "<span class=\"ls-ico-checkmark ls-color-success\"></span>";
				} else {
					echo "-";
					}
						
		?>
          </td>
          <td class="ls-txt-center">
		  <?php
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_calimentar = sprintf("SELECT * FROM sms_pse_consumo_alimentar WHERE cons_alim_id_matricula = %s", GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"));
			$calimentar = mysql_query($query_calimentar, $SmecelNovo) or die(mysql_error());
			$row_calimentar = mysql_fetch_assoc($calimentar);
			$totalRows_calimentar = mysql_num_rows($calimentar);
			
			if ($totalRows_calimentar > 0) {
				echo "<span class=\"ls-ico-checkmark ls-color-success\"></span>";
			} else {
					echo "-";
					}
		  
		  ?>
          </td>
          <td class="ls-txt-center">
		  <?php
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria = sprintf("SELECT * FROM sms_pse_antropometria WHERE antrop_id_matricula = %s ORDER BY antrop_id DESC", GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"));
			$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
			$row_antropometria = mysql_fetch_assoc($antropometria);
			$totalRows_antropometria = mysql_num_rows($antropometria);
			
			if ($totalRows_antropometria > 0) {
				echo "<span class=\"ls-ico-checkmark ls-color-success\"></span>";
			} else {
					echo "-";
					}
			
			 ?>
             </td>
        </tr>
        <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
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
mysql_free_result($Alunos);
?>
