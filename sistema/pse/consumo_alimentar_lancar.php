<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php include "../funcoes/inverteData.php"; ?>
<?php include "../funcoes/idade.php"; ?>
<?php

setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = $_GET['turma'];
}

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = $_GET['escola'];
}
$colname_vinculo = "-1";
if (isset($_GET['aluno'])) {
  $colname_vinculo = $_GET['aluno'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_vinculo = sprintf("
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, 
vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval,
aluno_id, aluno_nome, aluno_foto, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_cpf, aluno_nis
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_vinculo, "text"));
$vinculo = mysql_query($query_vinculo, $SmecelNovo) or die(mysql_error());
$row_vinculo = mysql_fetch_assoc($vinculo);
$totalRows_vinculo = mysql_num_rows($vinculo);

$firstDate = $row_vinculo['aluno_nascimento'];
$secondDate = date("Y-m-d");

$dateDifference = abs(strtotime($secondDate) - strtotime($firstDate));

$years  = floor($dateDifference / (365 * 60 * 60 * 24));
$months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
$days   = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *24) / (60 * 60 * 24));

$years." year,  ".$months." months and ".$days." days";

$anos = $years*12;

$meses = $months+$anos;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	
if ($meses < 6) {
	
  $insertSQL = sprintf("
  INSERT INTO sms_pse_consumo_alimentar (
  
  	cons_alim_id_aluno, 
	cons_alim_id_matricula, 
	cons_alim_data, 
	
	cons_alim_tomou_leite_peito_1, 
	cons_alim_mingau_1, 
	cons_alim_agua_cha_1, 
	cons_alim_leite_vaca_1, 
	cons_alim_formula_infantil_1, 
	cons_alim_suco_fruta_1, 
	cons_alim_fruta_1, 
	cons_alim_comida_sal_1, 
	cons_alim_outros_alimentos_bebidas_1,
	
	
	) VALUES (
	
	%s, %s, %s, 
	
	%s, %s, %s, %s, %s, %s, %s, %s, %s 
	
	
	)",
                       
					   GetSQLValueString($_POST['cons_alim_id_aluno'], "int"),
                       GetSQLValueString($_POST['cons_alim_id_matricula'], "int"),
                       GetSQLValueString(inverteData($_POST['cons_alim_data']), "date"),
					   
                       GetSQLValueString($_POST['cons_alim_tomou_leite_peito_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_mingau_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_agua_cha_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_leite_vaca_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_formula_infantil_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_suco_fruta_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_fruta_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_comida_sal_1'], "text"),
                       GetSQLValueString($_POST['cons_alim_outros_alimentos_bebidas_1'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

}

if (($meses >= 6) && ($meses <= 23)) {
	
  $insertSQL = sprintf("
  INSERT INTO sms_pse_consumo_alimentar (
  
  	cons_alim_id_aluno, 
	cons_alim_id_matricula, 
	cons_alim_data, 
	
	cons_alim_leite_peito_2, 
	cons_alim_fruta_inteira_2, 
	cons_alim_quantas_vezes_fruta_2, 
	cons_alim_comida_sal_2, 
	cons_alim_quantas_vezes_sal_2, 
	cons_alim_oferecida_2, 
	cons_alim_outro_leite_2, 
	cons_alim_mingau_leite_2, 
	cons_alim_iogurte_2, 
	cons_alim_legumes_2, 
	cons_alim_vegetal_2, 
	cons_alim_verdura_folha_2, 
	cons_alim_carne_boi_2,
	cons_alim_figado_2, 
	cons_alim_feijao_2,
	cons_alim_arroz_2, 
	cons_alim_hamburguer_2, 
	cons_alim_bebidas_adoc_2, 
	cons_alim_macarrao_inst_2, 
	cons_alim_bisc_recheado_2
	
	) VALUES (
	
	%s, %s, %s, 
	
	
	%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s 
	
	
	)",
                       
					   GetSQLValueString($_POST['cons_alim_id_aluno'], "int"),
                       GetSQLValueString($_POST['cons_alim_id_matricula'], "int"),
                       GetSQLValueString(inverteData($_POST['cons_alim_data']), "date"),
					   
                       
					   GetSQLValueString($_POST['cons_alim_leite_peito_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_fruta_inteira_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_quantas_vezes_fruta_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_comida_sal_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_quantas_vezes_sal_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_oferecida_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_outro_leite_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_mingau_leite_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_iogurte_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_legumes_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_vegetal_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_verdura_folha_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_carne_boi_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_figado_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_feijao_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_arroz_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_hamburguer_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_bebidas_adoc_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_macarrao_inst_2'], "text"),
                       GetSQLValueString($_POST['cons_alim_bisc_recheado_2'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

}

if ($meses >= 24) {
	
  $insertSQL = sprintf("
  INSERT INTO sms_pse_consumo_alimentar (
  
  	cons_alim_id_aluno, 
	cons_alim_id_matricula, 
	cons_alim_data, 
	
	cons_alim_refeicoes_assistindo_3, 
	cons_alim_quais_ref_cafe_3, 
	cons_alim_quais_ref_lanche_3, 
	cons_alim_quais_ref_almoco_3, 
	cons_alim_quais_ref_lanche_tarde_3, 
	cons_alim_quais_ref_jantar_3, 
	cons_alim_quais_ref_ceia_3, 
	cons_alim_feijao_3, 
	cons_alim_frutas_3,
	cons_alim_verduras_3, 
	cons_alim_hamburguer_3, 
	cons_alim_bebidas_3, 
	cons_alim_macarrao_inst_3, 
	cons_alim_bisc_recheado_3
	
	) VALUES (
	
	%s, %s, %s, 
		
	%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
	
	)",
                       
					   GetSQLValueString($_POST['cons_alim_id_aluno'], "int"),
                       GetSQLValueString($_POST['cons_alim_id_matricula'], "int"),
                       GetSQLValueString(inverteData($_POST['cons_alim_data']), "date"),
					                          
					   GetSQLValueString($_POST['cons_alim_refeicoes_assistindo_3'], "text"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_cafe_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_lanche_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_almoco_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_lanche_tarde_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_jantar_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString(isset($_POST['cons_alim_quais_ref_ceia_3']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['cons_alim_feijao_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_frutas_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_verduras_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_hamburguer_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_bebidas_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_macarrao_inst_3'], "text"),
                       GetSQLValueString($_POST['cons_alim_bisc_recheado_3'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

}


  $insertGoTo = "consumo_alimentar.php?lancado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

function idadeMeses ($idade) {
	$DataInicial = getdate(strtotime($idade));
	$DataFinal = getdate(strtotime(date("Y-m-d")));
	$Dif = ($DataFinal[0] - $DataInicial[0]) / 86400;
	return $meses = round($Dif/30);
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
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

    <p><a class="ls-btn" href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>">VOLTAR</a></p>
    
    <h4 class="ls-title-4 ls-txt-center">ANTROPOMETRIA</h4>
    
    <hr>
    
<div class="row1">

  <div class="col-md-2 col-sm-4">
  			<?php if ($row_vinculo['aluno_foto'] == "") { ?>
                  <img src="../../aluno/fotos/semfoto.jpg" width="100%" class="">
                  <?php } else { ?>
                  <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_vinculo['aluno_foto']; ?>" width="100%" class="">
            <?php } ?>
            <p>&nbsp;</p>
  </div>
  <div class="col-md-10 col-sm-8">
  		  <h3 class="ls-title-3 ls-color-theme"><?php echo $row_vinculo['aluno_nome']; ?></h3>
          <h5 class="ls-title-5"><?php echo inverteData($row_vinculo['aluno_nascimento']); ?> (<?php echo idade($row_vinculo['aluno_nascimento']); ?> anos)</h5>
          <h6 class="ls-title-6">FILIAÇÃO: <?php echo $row_vinculo['aluno_filiacao1']; ?> </h6>
		  <?php if ($row_vinculo['aluno_filiacao2']<>"") { ?><h6 class="ls-title-6">FILIAÇÃO:  <?php echo $row_vinculo['aluno_filiacao2']; ?></h6><?php } ?>
          <h6 class="ls-title-6">CPF: <?php echo $row_vinculo['aluno_cpf']; ?></h6>
          <h6 class="ls-title-6">NIS: <?php echo $row_vinculo['aluno_nis']; ?></h6>
          <br><br>
  </div>
  
  <div class="col-md-12 col-sm-12">
  
  
  
  <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row ls-field-lg">
      
      <label class="ls-label col-md-3">
      <b class="ls-label-text">Data da avaliação</b>
      <input type="text" name="cons_alim_data" value="<?php echo date("d/m/Y"); ?>" class="validate date">
    </label>
      
      
      <?php if ($meses < 6) { ?>
      
      <div class="ls-label col-md-12"><p class="ls-title-5">CRIANÇAS MENORES DE 6 MESES</div>
      
      <div class="ls-label col-md-12">
      <p>A criança ontem tomou leite de peito?</p>
      <label><input name="cons_alim_tomou_leite_peito_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_tomou_leite_peito_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_tomou_leite_peito_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <h6><strong>Ontem a criança consumiu:</strong></h6>           
      <p>Mingau</p>            
      <label><input name="cons_alim_mingau_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_mingau_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_mingau_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Água/chá</p>            
      <label><input name="cons_alim_agua_cha_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_agua_cha_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_agua_cha_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Leite de vaca</p>            
      <label><input name="cons_alim_leite_vaca_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_leite_vaca_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_leite_vaca_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Fórmula infantil</p>            
      <label><input name="cons_alim_formula_infantil_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_formula_infantil_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_formula_infantil_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Suco de fruta</p>            
      <label><input name="cons_alim_suco_fruta_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_suco_fruta_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_suco_fruta_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Fruta</p>            
      <label><input name="cons_alim_fruta_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_fruta_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_fruta_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Comida de sal (de panela, papa ou sopa)</p>            
      <label><input name="cons_alim_comida_sal_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_comida_sal_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_comida_sal_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Outros alimentos/bebidas</p>            
      <label><input name="cons_alim_outros_alimentos_bebidas_1" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_outros_alimentos_bebidas_1" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_outros_alimentos_bebidas_1" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <?php } ?>

      
   
      
      
      
       <?php if (($meses >= 6) && ($meses <= 23)) { ?>
       
      <div class="ls-label col-md-12"><p class="ls-title-5">CRIANÇAS DE 6 A 23 MESES</div>
      
	  <div class="ls-label col-md-12">
      <p>A criança ontem tomou leite do peito?</p>            
      <label><input name="cons_alim_leite_peito_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_leite_peito_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_leite_peito_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Ontem, a criança comeu fruta inteira, em pedaço ou amassada?</p>            
      <label><input name="cons_alim_fruta_inteira_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_fruta_inteira_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_fruta_inteira_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Se sim, quantas vezes?</p>            
      <label><input name="cons_alim_quantas_vezes_fruta_2" type="radio" value="1" />&nbsp;1 vez</label>
      <label><input name="cons_alim_quantas_vezes_fruta_2" type="radio" value="2" />&nbsp;2 vezes</label>
      <label><input name="cons_alim_quantas_vezes_fruta_2" type="radio" value="3" />&nbsp;3 vezes ou mais</label>
      <label><input name="cons_alim_quantas_vezes_fruta_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Ontem a criança comeu comida de sal (de panela, papa ou sopa)?</p>            
      <label><input name="cons_alim_comida_sal_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_comida_sal_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_comida_sal_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Se sim, quantas vezes?</p>            
      <label><input name="cons_alim_quantas_vezes_sal_2" type="radio" value="1" />&nbsp;1 vez</label>
      <label><input name="cons_alim_quantas_vezes_sal_2" type="radio" value="2" />&nbsp;2 vezes</label>
      <label><input name="cons_alim_quantas_vezes_sal_2" type="radio" value="3" />&nbsp;3 vezes ou mais</label>
      <label><input name="cons_alim_quantas_vezes_sal_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
     
      <div class="ls-label col-md-12">
      <p>Se sim, essa comida foi oferecida:</p>            
      <label><input name="cons_alim_oferecida_2" type="radio" value="1" />&nbsp;Em pedaços</label>
      <label><input name="cons_alim_oferecida_2" type="radio" value="2" />&nbsp;Amassada</label>
      <label><input name="cons_alim_oferecida_2" type="radio" value="3" />&nbsp;Passada na peneira</label>
      <label><input name="cons_alim_oferecida_2" type="radio" value="4" />&nbsp;Liquidificada</label>
      <label><input name="cons_alim_oferecida_2" type="radio" value="5" />&nbsp;Só o caldo</label>
      <label><input name="cons_alim_oferecida_2" type="radio" value="6" />&nbsp;Não sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p><strong>Ontem a criança consumiu:</strong></p>           
	  <p>Outro leite que não o leite do peito</p>            
      <label><input name="cons_alim_outro_leite_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_outro_leite_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_outro_leite_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Mingau com leite</p>            
      <label><input name="cons_alim_mingau_leite_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_mingau_leite_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_mingau_leite_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
            
      <div class="ls-label col-md-12">
      <p>Iogurte</p>            
      <label><input name="cons_alim_iogurte_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_iogurte_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_iogurte_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Legumes (não considerar os utilizados como temperos, nem batata, mandioca/aipim/macaxeira, cará e inhame)</p>            
      <label><input name="cons_alim_legumes_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_legumes_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_legumes_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Vegetal ou fruta de cor alaranjada (abóbora ou jerimum, cenoura, mamão, manga) ou folhas verdes-escuras (couve, caruru, beldroega, bertalha, espinafre, mostarda)</p>            
      <label><input name="cons_alim_vegetal_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_vegetal_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_vegetal_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Verdura de folha (alface, acelga, repolho)</p>            
      <label><input name="cons_alim_verdura_folha_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_verdura_folha_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_verdura_folha_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Carne (boi, frango, peixe, porco, miúdos, outras) ou ovo</p>            
      <label><input name="cons_alim_carne_boi_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_carne_boi_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_carne_boi_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Fígado</p>            
      <label><input name="cons_alim_figado_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_figado_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_figado_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Feijão</p>            
      <label><input name="cons_alim_feijao_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_feijao_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_feijao_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Arroz, batata, inhame, aipim/macaxeira/mandioca, farinha ou macarrão (sem ser instantâneo)</p>            
      <label><input name="cons_alim_arroz_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_arroz_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_arroz_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Hambúrguer e/ou embutidos (presunto, mortadela, salame, linguiça, salsicha)</p>            
      <label><input name="cons_alim_hamburguer_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_hamburguer_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_hamburguer_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Bebidas adoçadas (refrigerante, suco de caixinha, suco em pó, água de coco de caixinha, xaropes de guaraná/groselha, suco de fruta com adição de açúcar)</p>            
      <label><input name="cons_alim_bebidas_adoc_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_bebidas_adoc_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_bebidas_adoc_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Macarrão instantâneo, salgadinhos de pacote ou biscoitos salgados</p>            
      <label><input name="cons_alim_macarrao_inst_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_macarrao_inst_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_macarrao_inst_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Biscoito recheado, doces ou guloseimas (balas, pirulitos, chiclete, caramelo, gelatina)</p>            
      <label><input name="cons_alim_bisc_recheado_2" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_bisc_recheado_2" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_bisc_recheado_2" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <?php } ?>

      
      
      
      
      
      
      <?php if ($meses >= 24) { ?>
      
      <div class="ls-label col-md-12"><p class="ls-title-5">CRIANÇAS COM 2 ANOS OU MAIS, ADOLESCENTES, ADULTOS, GESTANTES E IDOSOS</p></div>
	  
      <div class="ls-label col-md-12">
      <p>Você tem costume de realizar as refeições assistindo à TV, mexendo no computador e/ou celular?</p>            
      <label class="ls-label-text"><input name="cons_alim_refeicoes_assistindo_3" type="radio" value="S" />&nbsp;Sim</label>
      <label class="ls-label-text"><input name="cons_alim_refeicoes_assistindo_3" type="radio" value="N" />&nbsp;Não</label>
      <label class="ls-label-text"><input name="cons_alim_refeicoes_assistindo_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Quais refeições você faz ao longo do dia?</p>            
      <label><input type="checkbox" name="cons_alim_quais_ref_cafe_3" value="" >&nbsp;Café da manhã</label>
      <label><input type="checkbox" name="cons_alim_quais_ref_lanche_3" value="" >&nbsp;Lanche da manhã</label>
      <label><input type="checkbox" name="cons_alim_quais_ref_almoco_3" value="" >&nbsp;Almoço</label>
      <label><input type="checkbox" name="cons_alim_quais_ref_lanche_tarde_3" value="" >&nbsp;Lanche da tarde</label>
      <label><input type="checkbox" name="cons_alim_quais_ref_jantar_3" value="" >&nbsp;Jantar</label>
      <label><input type="checkbox" name="cons_alim_quais_ref_ceia_3" value="" >&nbsp;Ceia</label>
      </div>

      <div class="ls-label col-md-12">
      <p><strong>Ontem, você consumiu:</strong></p>  
      <p>Feijão</p>            
      <label><input name="cons_alim_feijao_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_feijao_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_feijao_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Frutas frescas (não considerar suco de frutas)</p>            
      <label><input name="cons_alim_frutas_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_frutas_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_frutas_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Verduras e/ou legumes (não considerar batata, mandioca, aipim, macaxeira, cará e inhame)</p>            
      <label><input name="cons_alim_verduras_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_verduras_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_verduras_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Hambúrguer e/ou embutidos (presunto, mortadela, salame, linguiça, salsicha)</p>            
      <label><input name="cons_alim_hamburguer_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_hamburguer_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_hamburguer_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Bebidas adoçadas (refrigerante, suco de caixinha, suco em pó, água de coco de caixinha, xaropes de guaraná/groselha, suco de fruta com adição de açúcar)</p>            
      <label><input name="cons_alim_bebidas_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_bebidas_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_bebidas_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Macarrão instantâneo, salgadinhos de pacote ou biscoitos salgados</p>            
      <label><input name="cons_alim_macarrao_inst_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_macarrao_inst_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_macarrao_inst_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <div class="ls-label col-md-12">
      <p>Biscoito recheado, doces ou guloseimas (balas, pirulitos, chiclete, caramelo, gelatina)</p>            
      <label><input name="cons_alim_bisc_recheado_3" type="radio" value="S" />&nbsp;Sim</label>
      <label><input name="cons_alim_bisc_recheado_3" type="radio" value="N" />&nbsp;Não</label>
      <label><input name="cons_alim_bisc_recheado_3" type="radio" value="I" />&nbsp;Não Sabe</label>
      </div>
      
      <?php } ?>

        <div class="ls-actions-btn">    
    <input type="submit" value="SALVAR" class="ls-btn-primary">
      <a class="ls-btn" href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>">Voltar</a>
      
  </div>
  
  

        <input type="hidden" name="cons_alim_id_aluno" value="<?php echo $row_vinculo['vinculo_aluno_id_aluno']; ?>">
	    <input type="hidden" name="cons_alim_id_matricula" value="<?php echo $row_vinculo['vinculo_aluno_id']; ?>">

      <input type="hidden" name="MM_insert" value="form1">
    </form>
  
  

  </div>
  
  
  
</div>
    
    
    
    

    

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
<script type="text/javascript" src="../js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="../../js/mascara.js"></script> 

<script>


$(document).ready(function(){
  $('.peso').mask('000.0', {reverse: true, placeholder: "00.0"});
  $('.altura').mask('0.00', {reverse: false,  placeholder: "0.00"});
  $('.placeholder').mask('00/00/0000');
  $('.date').mask("00/00/0000", {placeholder: "__/__/____"});
});	

	$(document).ready(function(){           
        $("#enviar_antropometria").submit(function(event){
			$('#btn_antropometria').attr({value:"Salvando..."});
            $("#btn_antropometria").attr('disabled', 'disabled');
        });
    });
   
</script>


</body>
</html>
<?php
mysql_free_result($vinculo);
?>
