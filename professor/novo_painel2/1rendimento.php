<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escolas = sprintf("
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
WHERE ch_lotacao_professor_id = ".ID_PROFESSOR." AND turma_ano_letivo = ".ANO_LETIVO."
GROUP BY escola_id
ORDER BY escola_nome ASC
", GetSQLValueString($row_ProfLogado['func_id'], "int"));
$escolas = mysql_query($query_escolas, $SmecelNovo) or die(mysql_error());
$row_escolas = mysql_fetch_assoc($escolas);
$totalRows_escolas = mysql_num_rows($escolas);

if (isset($_GET['escola'])) {
   			$escola = anti_injection($_GET['escola']);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_turmas = "
		SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
		ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, escola_id, escola_nome,
		CASE turma_turno
		WHEN 0 THEN 'INTEGRAL'
		WHEN 1 THEN 'MATUTINO'
		WHEN 2 THEN 'VESPERTINO'
		WHEN 3 THEN 'NOTURNO'
		END AS turma_turno_nome 
		FROM smc_ch_lotacao_professor
		INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
		INNER JOIN smc_escola ON escola_id = '$escola'
		WHERE turma_ano_letivo = ".ANO_LETIVO." AND ch_lotacao_escola = '$escola' AND ch_lotacao_professor_id = ".ID_PROFESSOR."
		GROUP BY turma_id
		ORDER BY turma_turno, turma_etapa, turma_nome ASC";
		$turmas = mysql_query($query_turmas, $SmecelNovo) or die(mysql_error());
		$row_turmas = mysql_fetch_assoc($turmas);
		$totalRows_turmas = mysql_num_rows($turmas);

}

if (isset($_GET['turma'])) {
   			$turma = anti_injection($_GET['turma']);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_componente = "
		SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
		ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, disciplina_id, disciplina_nome
		FROM smc_ch_lotacao_professor
		INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
		INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
		WHERE turma_ano_letivo = ".ANO_LETIVO." AND ch_lotacao_turma_id = '$turma' AND ch_lotacao_professor_id = ".ID_PROFESSOR."
		GROUP BY disciplina_id
		ORDER BY disciplina_nome ASC";
		$componente = mysql_query($query_componente, $SmecelNovo) or die(mysql_error());
		$row_componente = mysql_fetch_assoc($componente);
		$totalRows_componente = mysql_num_rows($componente);
		
}

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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">RENDIMENTO</h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>



<div class="ls-box1">
<hr>

<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
  <a href="#" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false"><?php if (isset($_GET['escola'])) { echo substr($row_escolas['escola_nome'],0,30); } else { ?>UNIDADE ESCOLAR (<?php echo $totalRows_escolas; ?>)<?php } ?></a>
  <ul class="ls-dropdown-nav" aria-hidden="true">
    <?php 
	do { 
		$vinculo_q = "SELECT * FROM smc_vinculo WHERE vinculo_id_escola = '$row_escolas[escola_id]' AND vinculo_id_funcionario = '$row_ProfLogado[func_id]' AND vinculo_status = '1' AND vinculo_acesso = 'N'";
		$vinculo = mysql_query($vinculo_q, $SmecelNovo) or die(mysql_error());
		$vinculo_row = mysql_fetch_assoc($vinculo);
		$vinculo_total = mysql_num_rows($vinculo);	
		if($vinculo_total == 0){
	?>
      <li><a href="rendimento.php?escola=<?php echo $row_escolas['escola_id']; ?>"><?php echo substr($row_escolas['escola_nome'],0,33); ?>...</a></li>
	<?php } } while ($row_escolas = mysql_fetch_assoc($escolas)); ?>
      <li><a class="ls-color-danger ls-divider" href="rendimento.php">LIMPAR</a></li>
  </ul>
</div>


<?php 



?>


<?php if (isset($_GET['escola'])) { ?>

<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
  <a href="#" style="background-color:#06C;" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false"><?php if (isset($_GET['turma'])) { echo substr($row_componente['turma_nome'],0,30); } else { ?>TURMAS (<?php echo $totalRows_turmas; ?>)<?php } ?></a>
  <ul class="ls-dropdown-nav" aria-hidden="true">
    <?php do { ?>
      <li><a href="rendimento.php?escola=<?php echo $escola; ?>&turma=<?php echo $row_turmas['turma_id']; ?>"><?php echo $row_turmas['turma_nome']; ?></a></li>
	<?php } while ($row_turmas = mysql_fetch_assoc($turmas)); ?>
      <li><a class="ls-color-danger ls-divider" href="rendimento.php">LIMPAR</a></li>
  </ul>
</div>


<?php } ?>



<?php 



?>

<?php if (isset($_GET['turma'])) { ?>

<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
  <a href="#" style="background-color:#066;" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false">COMPONENTES (<?php echo $totalRows_componente; ?>)</a>
  <ul class="ls-dropdown-nav" aria-hidden="true">
    <?php do { ?>
      <li><a href="rendimento_alunos.php?escola=<?php echo $escola; ?>&etapa=<?php echo $row_componente['turma_etapa']; ?>&componente=<?php echo $row_componente['disciplina_id']; ?>&turma=<?php echo $turma; ?>"><?php echo $row_componente['disciplina_nome']; ?></a></li>
	<?php } while ($row_componente = mysql_fetch_assoc($componente)); ?>
  </ul>
</div>


<?php } ?>


</div>
        
	
    
    
    
    
    
    
    
    
    
    
    
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
mysql_free_result($escolas);
?>
