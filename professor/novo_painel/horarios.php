<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "../../sistema/escola/fnc/preencheHorario.php"; ?>

<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosProfessor = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs,
func_id, func_nome, funcao_id, funcao_nome, funcao_docencia 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE vinculo_id_funcionario = ".ID_PROFESSOR."
";
$VinculosProfessor = mysql_query($query_VinculosProfessor, $SmecelNovo) or die(mysql_error());
$row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor);
$totalRows_VinculosProfessor = mysql_num_rows($VinculosProfessor);
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
    <h1 class="ls-title-intro ls-ico-home">Ditribuição de Aulas</h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

    
<?php do { ?>
<div class="ls-box ls-board-box">
<header class="ls-info-header">
    <h2 class="ls-title-3"><?php echo $row_VinculosProfessor['func_nome']; ?></h2>
    <p class="ls-float-right ls-float-none-xs ls-small-info"><?php echo $row_VinculosProfessor['funcao_nome']; ?></p>
  </header>
  

  <h4 class="ls-txt-center">INTEGRAL</h4>
  <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "0" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "0" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "0" ); ?></td>
        </tr>
    </tbody>    
    
  </table>


  <h4 class="ls-txt-center">MATUTINO</h4>
  <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "1" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "1" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "1" ); ?></td>
        </tr>
    </tbody>    
    
  </table>

  <h4 class="ls-txt-center">VESPERTINO</h4>
  <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "2" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "2" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "2" ); ?></td>
        </tr>
    </tbody>    
    
  </table>

  <h4 class="ls-txt-center">NOTURNO</h4>
  <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
    <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center">SEGUNDA</th>
        <th class="ls-txt-center">TERÇA</th>
        <th class="ls-txt-center">QUARTA</th>
        <th class="ls-txt-center">QUINTA</th>
        <th class="ls-txt-center">SEXTA</th>
        </tr>
    </thead>
    <tbody>
        <tr class="">
          <td class="ls-txt-center"><strong>1ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "1", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "1", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>2ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "2", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "2", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>3ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "3", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "3", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>4ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "4", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "4", "3" ); ?></td>
        </tr>
        <tr class="">
          <td class="ls-txt-center"><strong>5ª</strong></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "1", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "2", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "3", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "4", "5", "3" ); ?></td>
          <td class="ls-txt-center"><?php echo preencheHorario( $row_VinculosProfessor['vinculo_id_funcionario'], $row_AnoLetivo['ano_letivo_ano'], "5", "5", "3" ); ?></td>
        </tr>
    </tbody>    
    
  </table>
  
  
  
</div>  
<?php } while ($row_VinculosProfessor = mysql_fetch_assoc($VinculosProfessor)); ?>

        

    
    
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