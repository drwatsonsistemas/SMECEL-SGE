<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php 

$target = "-1";
if (isset($_GET['target'])) {
  $target = anti_injection($_GET['target']);
}

switch ($target) {
    case "aulas":
        $link_target = "aulas.php";
        $nome_target = "REGISTRAR AULAS";
        break;
    case "frequencia":
        $link_target = "frequencia.php";
        $nome_target = "REGISTRAR FREQUÊNCIA";
        break;
    case "planejamento":
        $link_target = "planejamento.php";
        $nome_target = "REGISTRAR PLANEJAMENTO";
        break;
    case "rendimento":
        $link_target = "rendimento.php";
        $nome_target = "REGISTRAR RENDIMENTO";
        break;
    default:
       header("Location:index.php?err");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escolas = sprintf("
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
WHERE ch_lotacao_professor_id = %s AND turma_ano_letivo = ".ANO_LETIVO."
GROUP BY escola_id
ORDER BY escola_nome ASC
", GetSQLValueString($row_ProfLogado['func_id'], "int"));
$escolas = mysql_query($query_escolas, $SmecelNovo) or die(mysql_error());
$row_escolas = mysql_fetch_assoc($escolas);
$totalRows_escolas = mysql_num_rows($escolas);

$colname_escola = "-1";
if (isset($_GET['escola'])) {
  $colname_escola = anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim FROM smc_escola WHERE escola_id = %s", GetSQLValueString($colname_escola, "int"));
$escola = mysql_query($query_escola, $SmecelNovo) or die(mysql_error());
$row_escola = mysql_fetch_assoc($escola);
$totalRows_escola = mysql_num_rows($escola);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_vinculos = sprintf("
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_nome, turma_turno, turma_ano_letivo, 
CASE turma_turno
WHEN 0 THEN 'INT'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VES'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
WHERE ch_lotacao_professor_id = %s AND ch_lotacao_escola = '$row_escola[escola_id]' AND turma_ano_letivo = ".ANO_LETIVO."
GROUP BY ch_lotacao_turma_id
ORDER BY turma_turno, turma_nome ASC
", GetSQLValueString($row_ProfLogado['func_id'], "int"));
$vinculos = mysql_query($query_vinculos, $SmecelNovo) or die(mysql_error());
$row_vinculos = mysql_fetch_assoc($vinculos);
$totalRows_vinculos = mysql_num_rows($vinculos);



if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
  $semana = date("w", strtotime($data));
  $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
  $dia_semana_nome = $diasemana[$semana];
  $data = date("Y-m-d", strtotime($data));
} else {
	$data = date("Y-m-d");
	$semana = date("w", strtotime($data));
	$diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
	$dia_semana_nome = $diasemana[$semana];
	$data = date("Y-m-d", strtotime($data));

}
//echo $data;


   if (isset($_GET['escola'])) {
   			$escola = anti_injection($_GET['escola']);
   		} else {
   			$escola = "";
	   }
	   
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, disciplina_id, disciplina_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_escola = '$escola' AND ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]' AND ch_lotacao_dia = '$semana'
ORDER BY turma_turno, ch_lotacao_aula ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);
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
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

<h3><?php echo $nome_target; ?></h3>

<div class="ls-box-filter">
  <form action="escola.php" class="ls-form ls-form-inline">
    <label class="ls-label col-md-3 col-sm-4">
      <b class="ls-label-text">DATA</b>
      <input type="date" name="data" class="" id="data" value="<?php echo $data; ?>" autocomplete="off"  onchange="this.form.submit()">
    </label>
      <input type="hidden" name="target" value="<?php echo $target; ?>">
      <input type="hidden" name="" value="">
      <input type="hidden" name="" value="">
  </form>
</div>
    
<div class="ls-box">
<?php if ($totalRows_escola>0) { ?>
<h4><?php echo $row_escola['escola_nome']; ?></h4>
<?php } else { ?>
<h5>Escolha uma Unidade Escolar</h5>
<?php } ?>
</div>
      
<div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary ">UNIDADE ESCOLAR</a>
  	<ul class="ls-dropdown-nav ls-pos-right">
  	<?php do { ?>
	<li><a href="escola.php?escola=<?php echo $row_escolas['escola_id']; ?>&target=<?php echo $target; ?>&data=<?php echo $data; ?>"><?php echo substr($row_escolas['escola_nome'],0,30); ?>...</a></li>
	<?php } while ($row_escolas = mysql_fetch_assoc($escolas)); ?>
	<li><a class="ls-color-danger ls-divider" href="escola.php?target=<?php echo $target; ?>">LIMPAR</a></li>
  </ul>
</div>

<div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary <?php if ($totalRows_vinculos==0) { ?>ls-disabled<?php } ?>">TURMA</a>
  	<ul class="ls-dropdown-nav">
  	<?php do { ?>
	<li><a href="<?php echo $link_target; ?>?escola=<?php echo $row_escola['escola_id']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&target=<?php echo $target; ?>&data=<?php echo $data; ?>"><?php echo $row_Turmas['ch_lotacao_aula']; ?>ª - <?php echo $row_Turmas['turma_nome']; ?> <?php echo $row_Turmas['turma_turno_nome']; ?></a></li>
	<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
  </ul>
</div>
        
    
    
    
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
mysql_free_result($escola);

mysql_free_result($turma);

mysql_free_result($vinculos);
?>