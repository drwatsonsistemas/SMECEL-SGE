<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php

//ALUNOS
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, turma_id, turma_turno
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_sec = %s AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND turma_turno <> 3
", GetSQLValueString(SEC_ID, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

//SAUDE BUCAL
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_saude_bucal = "
SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
FROM sms_pse_saude_bucal
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = pse_s_bucal_matricula_id
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."'
";
$saude_bucal = mysql_query($query_saude_bucal, $SmecelNovo) or die(mysql_error());
$row_saude_bucal = mysql_fetch_assoc($saude_bucal);
$totalRows_saude_bucal = mysql_num_rows($saude_bucal);

//ANTROPOMETRIA
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_antropometria = "
SELECT antrop_id, antrop_id_aluno, antrop_id_matricula, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_situacao 
FROM sms_pse_antropometria 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = antrop_id_matricula
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."'
";
$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
$row_antropometria = mysql_fetch_assoc($antropometria);
$totalRows_antropometria = mysql_num_rows($antropometria);


//CONSUMO ALIMENTAR
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_calimentar = "
SELECT cons_alim_id, cons_alim_id_aluno, cons_alim_id_matricula,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_situacao
FROM sms_pse_consumo_alimentar
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = cons_alim_id_matricula
WHERE vinculo_aluno_ano_letivo = '".ANO_LETIVO."' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_id_sec = '".SEC_ID."'
";
$calimentar = mysql_query($query_calimentar, $SmecelNovo) or die(mysql_error());
$row_calimentar = mysql_fetch_assoc($calimentar);
$totalRows_calimentar = mysql_num_rows($calimentar);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, 
escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, 
escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim,
CASE escola_localizacao
WHEN 'U' THEN 'ZONA URBANA' 
WHEN 'R' THEN 'ZONA RURAL'
END AS escola_localizacao
FROM smc_escola
WHERE escola_id_sec = ".SEC_ID." AND escola_situacao = '1' AND escola_ue = '1'
ORDER BY escola_nome ASC
";
$escolas = mysql_query($query_escolas, $SmecelNovo) or die(mysql_error());
$row_escolas = mysql_fetch_assoc($escolas);
$totalRows_escolas = mysql_num_rows($escolas);
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
    <h1 class="ls-title-intro ls-ico-home1"><img src="../../img/logo_pse.png" width="45"> PROGRAMA SAÚDE NA ESCOLA - Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
           

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em <strong><?php echo date("d/m/Y"); ?></strong></p>
    <h2 class="ls-title-3">Acompanhamento no município</h2>
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
    
    <table class="ls-table">
      <thead>
      <tr>
        <th class="ls-txt-center" width="40"></th>
        <th class="ls-txt-center" width="70"></th>        
        <th>UNIDADE ESCOLAR</th>
        <th class="ls-txt-center" width="30"></th>        
      </tr>
      </thead>
      <tbody>
      <?php $num=1; do { ?>
        <tr>
          <td><?php echo $num; $num++; ?></td>
          <td><?php if ($row_escolas['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_escolas['escola_logo']; ?>" width="100%"><?php } ?></td>
          <td>
          <a href="turmas.php?escola=<?php echo $row_escolas['escola_id']; ?>">
		  <?php echo $row_escolas['escola_nome']; ?></a><br>
          <small>
		  <?php echo $row_escolas['escola_endereco']; ?>, 
		  <?php echo $row_escolas['escola_num']; ?>, 
		  <?php echo $row_escolas['escola_bairro']; ?>, 
		  <?php echo $row_escolas['escola_cep']; ?>, 
		  <?php echo $row_escolas['escola_localizacao']; ?><br>
          <?php echo $row_escolas['escola_telefone1']; ?> <?php echo $row_escolas['escola_telefone2']; ?><br>
          <?php echo $row_escolas['escola_email']; ?>
          </small>
          </td>
          <td><a class="ls-ico-circle-right" href="turmas.php?escola=<?php echo $row_escolas['escola_id']; ?>"></a></td>
        </tr>
        <?php } while ($row_escolas = mysql_fetch_assoc($escolas)); ?>
        </tbody>
    </table>    
    
    
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