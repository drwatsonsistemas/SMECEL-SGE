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
WHEN 1 THEN '<span class=\"ls-tag-success\">MATRIC</span>'
WHEN 2 THEN '<span class=\"ls-tag-warning\">TRANSF</span>'
WHEN 3 THEN '<span class=\"ls-tag-warning\">DESIST</span>'
WHEN 4 THEN '<span class=\"ls-tag-warning\">FALECI</span>'
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

    <div class="ls-group-btn ls-group-active">
              <a href="alunos.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-ico-chevron-left">&nbsp;</a>
              <a href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-info ls-active">S. BUC.</a>
              <a href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-success">C. ALIM.</a>
              <a href="antropometria.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-warning">ANTROP.</a>
            </div>

			
            <h4 class="ls-title-4 ls-txt-center">SAÚDE BUCAL</h4>
                
    <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center ls-display-none-xs" width="40"></th>
        <th class="ls-txt-center ls-display-none-xs" width="60">MAT</th>
        <th>ALUNO</th>
        <th class="ls-txt-center" width="40"></th>
        
        <th class="ls-txt-center ls-display-none-xs" width="40">PE.</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">GE.</th>

        <th class="ls-txt-center ls-display-none-xs" width="40">C</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">P</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">O</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">D</th>
        
        
        
        <th class="ls-txt-center ls-display-none-xs" width="40">c</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">ei</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">o</th>
        <th class="ls-txt-center ls-display-none-xs" width="40">d</th>
        
        <th class="ls-txt-center ls-display-none-xs" width="40">T</th>
      </tr>
      </thead>
      <tbody>
      <?php 
	  
	  $c = 0;
	  $o = 0;
	  $p = 0;
	  $pe = 0;
	  $ge = 0;
	  
	  
	  ?>
      <?php $num = 1; do { ?>
      
        <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_saude_bucal = "
			SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id, pse_s_bucal_data, pse_s_bucal_qtd_dentes, pse_s_bucal_decidua, pse_s_bucal_permanente, pse_s_bucal_doenca_periodontal, pse_s_bucal_gengivite,
			CASE pse_s_bucal_doenca_periodontal
			WHEN 0 THEN '-'
			WHEN 1 THEN 'S'
			END AS pse_s_bucal_doenca_periodontal_nome, 
			CASE pse_s_bucal_gengivite
			WHEN 0 THEN '-'
			WHEN 1 THEN 'S'
			END AS pse_s_bucal_gengivite_nome, 
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
			
			$totalD = $row_saude_bucal['pse_s_bucal_c1']+$row_saude_bucal['pse_s_bucal_p1']+$row_saude_bucal['pse_s_bucal_o1'];
			$totald = $row_saude_bucal['pse_s_bucal_c2']+$row_saude_bucal['pse_s_bucal_ei2']+$row_saude_bucal['pse_s_bucal_o2'];
			
			$c = $c+$row_saude_bucal['pse_s_bucal_c1']+$row_saude_bucal['pse_s_bucal_c2'];
			$o =$o+$row_saude_bucal['pse_s_bucal_o1']+$row_saude_bucal['pse_s_bucal_o2'];
			$p = $p+$row_saude_bucal['pse_s_bucal_p1']+$row_saude_bucal['pse_s_bucal_ei2'];
			
			if ($row_saude_bucal['pse_s_bucal_doenca_periodontal']==1) {
				$pe++;	
				}
			
			if ($row_saude_bucal['pse_s_bucal_gengivite']==1) {
				$ge++;	
				}
			

						
		?>
        
        
        <tr>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $num; $num++; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
          <td><a class="ls-ico-upload ls-ico-right" href="saude_bucal_lancar.php?aluno=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>"><?php echo $row_Alunos['aluno_nome']; ?></a></td>
          <td class="ls-txt-center"><?php if ($totalRows_saude_bucal > 0) { ?><span class="ls-ico-checkmark ls-color-success"></span><?php } ?></td>
          
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_doenca_periodontal_nome']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_gengivite_nome']; ?></td>
          
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_c1']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_p1']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_o1']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php if ($totalD > 0) { ?><strong><?php echo $totalD; ?></strong><?php } ?></td>

          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_c2']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_ei2']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_o2']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php if ($totald > 0) { ?><strong><?php echo $totald; ?></strong><?php } ?></td>
          
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_saude_bucal['pse_s_bucal_qtd_dentes']; ?></td>
        </tr>
        <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        </tbody>
    </table>
    
         <p class="ls-tag-warning">Cariados: <?php echo $c; ?></p>

         <p class="ls-tag-success">Obturados: <?php echo $o; ?></p>

         <p class="ls-tag-info">Perdidos: <?php echo $p; ?></p>
         
         <p class="ls-tag-danger">Periodontal: <?php echo $pe; ?></p>
         
         <p class="ls-tag-theme">Gengivite: <?php echo $ge; ?></p>

    
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
<?php if (isset($_GET["lancado"])) { ?>
<script type="application/javascript">

Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Lançamento realizado',
  showConfirmButton: false,
  timer: 1500
})

</script>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($saude_bucal);

mysql_free_result($Alunos);
?>
