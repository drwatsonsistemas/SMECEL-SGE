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


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSB = sprintf("
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
WHERE vinculo_aluno_id_escola = %s AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."'
ORDER BY aluno_nome ASC
", GetSQLValueString($colname_Escolas_Turmas, "int"));
$AlunosSB = mysql_query($query_AlunosSB, $SmecelNovo) or die(mysql_error());
$row_AlunosSB = mysql_fetch_assoc($AlunosSB);
$totalRows_AlunosSB = mysql_num_rows($AlunosSB);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosANT = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_nascimento, turma_id, turma_nome, turma_turno, aluno_sexo,
CASE aluno_sexo
WHEN '1' THEN 'M'
WHEN '2' THEN 'F'
END AS aluno_sexo_legenda,  
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
WHERE vinculo_aluno_id_escola = %s AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '".ANO_LETIVO."'
ORDER BY aluno_nome ASC
", GetSQLValueString($colname_Escolas_Turmas, "int"));
$AlunosANT = mysql_query($query_AlunosANT, $SmecelNovo) or die(mysql_error());
$row_AlunosANT = mysql_fetch_assoc($AlunosANT);
$totalRows_AlunosANT = mysql_num_rows($AlunosANT);




function idadeTempo ($idade,$data) {
	if ($idade <> "") {
    // Declara a data! :P
    $data = $idade;
    // Separa em dia, mês e ano
    list($ano, $mes, $dia) = explode('-', $data);
    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
	} else {
		$idade = "-";
	}
    return $idade;
}

function imc ($peso,$altura,$sexo,$idade) {
	$imc = $peso/($altura*$altura);
	return number_format($imc, 1);
	}

function idadeMeses ($idade, $pesagem) {
	$DataInicial = getdate(strtotime($idade));
	$DataFinal = getdate(strtotime($pesagem));
	$Dif = ($DataFinal[0] - $DataInicial[0]) / 86400;
	return $meses = round($Dif/30);
}

$semPeso = 0;
$magrezaAcentuada = 0;
$magreza = 0;
$eutrofia = 0;
$riscoDeSobrepeso = 0;
$sobrepeso = 0;
$obesidade = 0;
$obesidadeGrave = 0;

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
          <h5 class="ls-title-3"><?php echo $nomeEscola = $row_Turmas['escola_nome']; ?></h5>
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
    
    
    

      <?php 
	  
	  $c = 0;
	  $o = 0;
	  $p = 0;
	  $pe = 0;
	  $ge = 0;
	  
	  $c2 = 0;
	  $o2 = 0;
	  $ei2 = 0;
	  
	  
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
			WHERE pse_s_bucal_matricula_id = '$row_AlunosSB[vinculo_aluno_id]'
			ORDER BY pse_s_bucal_id DESC
			";
			$saude_bucal = mysql_query($query_saude_bucal, $SmecelNovo) or die(mysql_error());
			$row_saude_bucal = mysql_fetch_assoc($saude_bucal);
			$totalRows_saude_bucal = mysql_num_rows($saude_bucal);
			
			$totalD = $row_saude_bucal['pse_s_bucal_c1']+$row_saude_bucal['pse_s_bucal_p1']+$row_saude_bucal['pse_s_bucal_o1'];
			$totald = $row_saude_bucal['pse_s_bucal_c2']+$row_saude_bucal['pse_s_bucal_ei2']+$row_saude_bucal['pse_s_bucal_o2'];
			
			$c = $c+$row_saude_bucal['pse_s_bucal_c1'];
			$o =$o+$row_saude_bucal['pse_s_bucal_o1'];
			$p = $p+$row_saude_bucal['pse_s_bucal_p1'];
			
			$c2 = $c2+$row_saude_bucal['pse_s_bucal_c2'];
			$o2 =$o2+$row_saude_bucal['pse_s_bucal_o2'];
			$ei2 = $ei2+$row_saude_bucal['pse_s_bucal_ei2'];
			
			if ($row_saude_bucal['pse_s_bucal_doenca_periodontal']==1) {
				$pe++;	
				}
			
			if ($row_saude_bucal['pse_s_bucal_gengivite']==1) {
				$ge++;	
				}
			

						
		?>
        
        

        <?php } while ($row_AlunosSB = mysql_fetch_assoc($AlunosSB)); ?>
    
    <div class="ls-box">
    	<h6 class="ls-title-6">RESULTADO DA AVALIAÇÃO BUCAL:</h6>
    	<h4 class="ls-title-4"><?php echo $nomeEscola; ?></h4>
        
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Situação', 'Quantidade'],
          ['Cariados (<?php echo $c; ?>)', <?php echo $c; ?>],
          ['Obturados (<?php echo $o; ?>)', <?php echo $o; ?>],
          ['Perdidos (<?php echo $p; ?>)', <?php echo $p; ?>]
        ]);

        var options = {
          title: 'PERMANENTES',
		  pieHole: 0.4,
        };
		


        var chart = new google.visualization.PieChart(document.getElementById('piechart_permanentes'));

        chart.draw(data, options);
      }
    </script>
    
         <p>PERMANENTES</p>
         
         <p class="ls-tag-warning">Cariados: <?php echo $c; ?></p>

         <p class="ls-tag-success">Obturados: <?php echo $o; ?></p>

         <p class="ls-tag-info">Perdidos: <?php echo $p; ?></p>
         
         <div id="piechart_permanentes" style="width: 100%; height: 500px;"></div>
         
         <br>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Situação', 'Quantidade'],
          ['Cariados (<?php echo $c2; ?>)', <?php echo $c2; ?>],
          ['Obturados (<?php echo $o2; ?>)', <?php echo $o2; ?>],
          ['Perdidos (<?php echo $ei2; ?>)', <?php echo $ei2; ?>]
        ]);

        var options = {
          title: 'DECIDUOS',
		  pieHole: 0.4,
        };
		
        var chart = new google.visualization.PieChart(document.getElementById('piechart_deciduos'));

        chart.draw(data, options);
      }
    </script>
    
         <p>DECÍDUOS</p>
         <p class="ls-tag-warning">Cariados: <?php echo $c2; ?></p>

         <p class="ls-tag-success">Obturados: <?php echo $o2; ?></p>

         <p class="ls-tag-info">Extração indicada: <?php echo $ei2; ?></p>
         
         <div id="piechart_deciduos" style="width: 100%; height: 500px;"></div>
         
    </div>
    
    
    
    
      <?php $num = 1; do { ?>
                  
          <?php
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria = sprintf("SELECT * FROM sms_pse_antropometria WHERE antrop_id_matricula = %s ORDER BY antrop_id DESC", GetSQLValueString($row_AlunosANT['vinculo_aluno_id'], "int"));
			$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
			$row_antropometria = mysql_fetch_assoc($antropometria);
			$totalRows_antropometria = mysql_num_rows($antropometria);
			

			
			$imc = "-";
			
			if ($totalRows_antropometria > 0) {
				
				
			$imc = imc($row_antropometria['antrop_peso'],$row_antropometria['antrop_altura'],1,1); 
			
			
			$idade_anos = idadeTempo($row_AlunosANT['aluno_nascimento'],$row_antropometria['antrop_data']);
			$idade_meses = idadeMeses($row_AlunosANT['aluno_nascimento'],$row_antropometria['antrop_data']);		
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_tabela_imc = "
			SELECT imc_id, imc_meses, imc_sexo, 
			imc_L, imc_M, imc_S, 
			imc_P01, imc_P1, imc_P3, imc_P5, imc_P10, imc_P15, imc_P25, imc_P50, imc_P75, imc_P85, imc_P90, imc_P95, imc_P97, imc_P99, imc_P999, 
			imc_SD4neg, imc_SD3neg, imc_SD2neg, imc_SD1neg, imc_SD0, imc_SD1, imc_SD2, imc_SD3, imc_SD4 
			FROM sms_pse_imc
			WHERE imc_meses = '$idade_meses' AND imc_sexo = '$row_AlunosANT[aluno_sexo]'";
			$tabela_imc = mysql_query($query_tabela_imc, $SmecelNovo) or die(mysql_error());
			$row_tabela_imc = mysql_fetch_assoc($tabela_imc);
			$totalRows_tabela_imc = mysql_num_rows($tabela_imc);
			
			if ($idade_meses < 60) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Risco sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
				
			} else if (($idade_meses >= 60) && ($idade_meses <= 120)){
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Risco sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
					
					
			} else if (($idade_meses >= 121) && ($idade_meses < 240)) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;

				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade grave&nbsp;</span>";
					$obesidadeGrave++;
					
				}
				
				
				} else if (($idade_meses >= 240) && ($idade_meses < 720)) {
					
				if ($imc < 18.5) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Baixo peso &nbsp;</span>";
					$magreza++;

				} else if (($imc >= 18.5) && ($imc < 25) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrófico &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc >= 25) && ($imc < 30) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc >= 30) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
				
				
				} else if ($idade_meses >= 720) {
					
					
					
				if ($imc <= 22) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Baixo peso &nbsp;</span>";
					$magreza++;

				} else if (($imc > 22) && ($imc < 27) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrófico &nbsp;</span>";
					$eutrofia++;
					
				} else if ($imc >= 27) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				}
					
						
				}
	 
				
				
			} else {
				
				$semPeso++;
				
				
				}
		  
		  ?>
          
          
        
        <?php } while ($row_AlunosANT = mysql_fetch_assoc($AlunosANT)); ?>

    
    
    <p>&nbsp;</p>
    
  
  
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Situação', 'Quantidade'],
          ['Magreza Acentuada (<?php echo $magrezaAcentuada; ?>)', <?php echo $magrezaAcentuada; ?>],
          ['Magreza (<?php echo $magreza; ?>)', <?php echo $magreza; ?>],
          ['Eutrofia (<?php echo $eutrofia; ?>)', <?php echo $eutrofia; ?>],
          ['Risco de sobrepeso (<?php echo $riscoDeSobrepeso; ?>)', <?php echo $riscoDeSobrepeso; ?>],
          ['Sobrepeso (<?php echo $sobrepeso; ?>)', <?php echo $sobrepeso; ?>],
          ['Obesidade (<?php echo $obesidade; ?>)', <?php echo $obesidade; ?>],
          ['Obesidade grave (<?php echo $obesidadeGrave; ?>)', <?php echo $obesidadeGrave; ?>]
        ]);

        var options = {
          title: 'Avaliação antropométrica',
		  pieHole: 0.4,
        };
		


        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
    
    <div class="ls-box">
    	<h6 class="ls-title-6">RESULTADO DA AVALIAÇÃO ANTROPOMÉTRICA:</h6>
    	<h4 class="ls-title-4"><?php echo $nomeEscola; ?></h4>  
    <div id="piechart" style="width: 100%; height: 500px;"></div>
  <table class="ls-table">
  <tr><td>SEM PESAGEM: <?php echo $semPeso; ?></td></tr>
  </table>

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
