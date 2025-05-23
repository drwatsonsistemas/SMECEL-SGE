<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, 
ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_ano_letivo, escola_id, escola_nome,
	CASE ch_lotacao_dia
	WHEN 1 THEN 'SEGUNDA'
	WHEN 2 THEN 'TERÇA'
	WHEN 3 THEN 'QUARTA'
	WHEN 4 THEN 'QUINTA'
	WHEN 5 THEN 'SEXTA'
	END AS ch_lotacao_dia_nome,
	CASE ch_lotacao_dia
	WHEN 1 THEN 'purple'
	WHEN 2 THEN 'blue'
	WHEN 3 THEN 'red'
	WHEN 4 THEN 'green'
	WHEN 5 THEN 'orange'
	END AS ch_lotacao_dia_cor,
	CASE turma_turno
	WHEN 0 THEN 'INT'
	WHEN 1 THEN 'MAT'
	WHEN 2 THEN 'VES'
	WHEN 3 THEN 'NOT'
	END AS turma_turno_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
WHERE turma_ano_letivo = ".ANO_LETIVO." AND ch_lotacao_professor_id = ".ID_PROFESSOR."
ORDER BY ch_lotacao_dia, turma_turno, ch_lotacao_aula ASC
");
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

if($totalRows_ProfLogado=="") {
	header("Location:index.php?erro");
}

if($totalRows_Vinculo=="") {
	header("Location:index.php?erro");
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
    <h1 class="ls-title-intro ls-ico-home">Grade analítica</h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="grade.php" class="ls-btn">Grade por turma</a></p>

    
<table class="ls-table ls-sm-space">
<thead>
  <tr>
    <th width="50"></th>
    <th width="120" class="center">DIA</th>
    <th width="80" class="center">AULA</th>
    <th width="250" class="center">COMPONENTE</th>
    <th class="center">TURMA</th>
    <th width="80" class="center">TURNO</th>
    <th class="center">ESCOLA</th>
  </tr>
  </thead>
  <tbody>
  <?php $cod = 1; do { ?>
    <tr style="color:<?php echo $row_Vinculo['ch_lotacao_dia_cor']; ?>">
      <td class="center"><strong><?php echo $cod; $cod++; ?></strong></td>
      <td class="center"><?php echo $row_Vinculo['ch_lotacao_dia_nome']; ?></td>
      <td class="center"><?php echo $row_Vinculo['ch_lotacao_aula']; ?>ª</td>
      <td class="center"><?php echo $row_Vinculo['disciplina_nome']; ?></td>
      <td class="center"><?php echo $row_Vinculo['turma_nome']; ?></td>
      <td class="center"><?php echo $row_Vinculo['turma_turno_nome']; ?></td>
      <td class="center"><?php echo $row_Vinculo['escola_nome']; ?></td>
    </tr>
    <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>
	</tbody>
</table>

<p><?php echo $totalRows_Vinculo; ?> aulas/semana.</p>        

<hr>
    
    
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