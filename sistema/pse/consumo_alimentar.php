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
              <a href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-info">S. BUC.</a>
              <a href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-success ls-active">C. ALIM.</a>
              <a href="antropometria.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-warning">ANTROP.</a>
            </div>
            
            <h4 class="ls-title-4 ls-txt-center">CONSUMO ALIMENTAR</h4>
    
    <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center ls-display-none-xs" width="40"></th>
        <th class="ls-txt-center ls-display-none-xs" width="60">MAT</th>
        <th>ALUNO</th>
        <th class="ls-txt-center ls-display-none-xs" width="200"></th>
        <th class="ls-txt-center" width="40"></th>
      </tr>
      </thead>
      <tbody>
      <?php $num = 1; do { ?>
      
        <tr>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $num; $num++; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
          <td><a class="ls-ico-upload ls-ico-right" href="consumo_alimentar_lancar.php?aluno=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>"><?php echo $row_Alunos['aluno_nome']; ?></a></td>
          <td class="ls-txt-center ls-display-none-xs" width="120">
          
          <?php
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria = sprintf("SELECT * FROM sms_pse_consumo_alimentar WHERE cons_alim_id_matricula = %s", GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"));
			$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
			$row_antropometria = mysql_fetch_assoc($antropometria);
			$totalRows_antropometria = mysql_num_rows($antropometria);
			
			if ($totalRows_antropometria > 0) { echo date("d/m/Y à\s H\hi", strtotime($row_antropometria['cons_alim_datetime'])); } 
			
			
		  
		  ?>
          
          </td>
          <td class="ls-txt-center">
          <?php if ($totalRows_antropometria > 0) { echo "<span class=\"ls-ico-checkmark ls-color-success\"></span>"; } ?>
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
mysql_free_result($Alunos);
?>
