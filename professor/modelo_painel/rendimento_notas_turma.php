<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>
<?php

$colname_Disciplina = "-1";
if (isset($_GET['di'])) {
  $colname_Disciplina = $_GET['di'];
}

$colname_disciplina = "-1";
if (isset($_GET['turma'])) {
  $colname_disciplina = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplina = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
ch_lotacao_escola, turma_id, turma_nome, disciplina_id, disciplina_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]' AND ch_lotacao_turma_id = %s
GROUP BY ch_lotacao_disciplina_id
", GetSQLValueString($colname_disciplina, "int"));
$disciplina = mysql_query($query_disciplina, $SmecelNovo) or die(mysql_error());
$row_disciplina = mysql_fetch_assoc($disciplina);
$totalRows_disciplina = mysql_num_rows($disciplina);





$colname_Disciplinas = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplinas = $_GET['disciplina'];
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash,
vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo, turma_etapa 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_disciplina ON disciplina_id = $colname_Disciplinas
INNER JOIN smc_turma ON turma_id = '$colname_Turma'
WHERE vinculo_aluno_id_turma = '$colname_Turma' AND turma_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);


if (($row_Alunos['turma_etapa']=="1") || ($row_Alunos['turma_etapa']=="2") || ($row_Alunos['turma_etapa']=="3")) {
	$linkAvaliar = "conceito";
	$nomeAvaliar = "CONCEITO"; 
	} else {
		$linkAvaliar = "aluno";
		$nomeAvaliar = "NOTAS"; 
		}
		


if ($totalRows_Alunos == 0) {
	//header("Location:index.php?erro");
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" href="css/sweetalert2.min.css">

<style>

.aluno {
  background-color: #ddd;
  border-radius: 100%;
  height: 50px;
  object-fit: cover;
  width: 50px;  
}
</style>

</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

    

    <?php do { ?>
      <a href="rendimento_notas_turma.php?escola=<?php echo $row_disciplina['ch_lotacao_escola']; ?>&turma=<?php echo $colname_Turma; ?>&disciplina=<?php echo $row_disciplina['disciplina_id']; ?>&target=rendimento&data=<?php echo date("Y-m-d"); ?>&nova"><?php echo $row_disciplina['turma_nome']; ?> - <?php echo $row_disciplina['disciplina_nome']; ?></a><br>
    <?php } while ($row_disciplina = mysql_fetch_assoc($disciplina)); ?>
    
    


<?php if ($totalRows_Alunos==0) { ?>

NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>

<?php } else { ?>

<a href="turmas.php?cod=<?php echo $row_Alunos['turma_id_escola']; ?>&disciplina=<?php echo $row_Alunos['disciplina_id']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
<a href="mapa_notas.php?disciplina=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" class="waves-effect waves-light btn-small orange btn right"><i class="material-icons left">map</i> MAPA DE NOTAS</a>
<!--<a href="plano_aula.php?disciplina=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" class="waves-effect waves-light btn-small btn right"><i class="material-icons left">map</i> CONTEÚDO DAS AULAS</a>-->






<table class="ls-table">

<?php do { ?>

<tr>

<td width="70px">
	<?php if ($row_Alunos['aluno_foto']=="") { ?>
    <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="hoverable aluno circle" border="0" width="100%">
    <?php } else { ?>
    <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>" class="hoverable aluno circle" border="0" width="100%">
    <?php } ?>
</td>

<td>

<?php echo $row_Alunos['aluno_nome']; ?>

</td>

<td>

<a class="ls-btn-primary ls-btn-xs" href="<?php echo $linkAvaliar; ?>.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" id="<?php echo $row_Alunos['boletim_id']; ?>"><?php echo $nomeAvaliar; ?></a> 
<a class="ls-btn-primary ls-btn-xs" href="rel_avaliativo.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" id="<?php echo $row_Alunos['boletim_id']; ?>">PARECER</a>


<?php if ( $row_Alunos['vinculo_aluno_rel_aval'] <> "") { ?>
<a class="ls-btn" href="rel_avaliativo.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" id="<?php echo $row_Alunos['boletim_id']; ?>"><i class="material-icons green-text">textsms</i></a>	
<?php } ?>

</td>


</tr>

<?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>

</table>






<?php } ?>

    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
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
mysql_free_result($disciplina);
?>
