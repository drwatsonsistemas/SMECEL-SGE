<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php include "../funcoes/inverteData.php"; ?>
<?php include "../funcoes/idade.php"; ?>
<?php

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	
	
  $insertSQL = sprintf("INSERT INTO sms_pse_saude_bucal (pse_s_bucal_aluno_id, pse_s_bucal_matricula_id, pse_s_bucal_data, pse_s_bucal_qtd_dentes, pse_s_bucal_decidua, pse_s_bucal_permanente, pse_s_bucal_doenca_periodontal, pse_s_bucal_gengivite, pse_s_bucal_c1, pse_s_bucal_p1, pse_s_bucal_o1, pse_s_bucal_cpod1, pse_s_bucal_c2, pse_s_bucal_ei2, pse_s_bucal_o2, pse_s_bucal_ceod2, pse_s_bucal_observacoews, pse_s_bucal_inicio_tratamento, pse_s_bucal_final_tratamento, pse_s_bucal_inicio_tratamento_data_hora, pse_s_bucal_cirurgiao_dentista, pse_s_bucal_asb) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['pse_s_bucal_aluno_id'], "int"),
                       GetSQLValueString($_POST['pse_s_bucal_matricula_id'], "int"),
                       GetSQLValueString($_POST['pse_s_bucal_data'], "date"),
                       GetSQLValueString($_POST['pse_s_bucal_qtd_dentes'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_decidua'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_permanente'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_doenca_periodontal'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_gengivite'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_c1'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_p1'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_o1'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_cpod1'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_c2'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_ei2'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_o2'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_ceod2'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_observacoews'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_inicio_tratamento'], "date"),
                       GetSQLValueString($_POST['pse_s_bucal_final_tratamento'], "date"),
                       GetSQLValueString($_POST['pse_s_bucal_inicio_tratamento_data_hora'], "date"),
                       GetSQLValueString($_POST['pse_s_bucal_cirurgiao_dentista'], "text"),
                       GetSQLValueString($_POST['pse_s_bucal_asb'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "saude_bucal.php?lancado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
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

    <p><a class="ls-btn" href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>">VOLTAR</a></p>
    
    <h4 class="ls-title-4 ls-txt-center">SAÚDE BUCAL</h4>


  <div class="ls-box-group">
  <div class="ls-box ls-md-space">
    <div class="row">
      <div class="col-md-2 ls-txt-center">
      		<?php if ($row_vinculo['aluno_foto'] == "") { ?>
                  <img src="../../aluno/fotos/semfoto.jpg" width="100%" class="circle aluno">
                  <?php } else { ?>
                  <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_vinculo['aluno_foto']; ?>" width="100%" class="">
            <?php } ?>
      </div>
      <div class="col-md-10">
          <h1 class="ls-title-1 ls-color-theme"><?php echo $row_vinculo['aluno_nome']; ?></h1>
          <h5 class="ls-title-5"><?php echo inverteData($row_vinculo['aluno_nascimento']); ?> (<?php echo idade($row_vinculo['aluno_nascimento']); ?> anos)</h5>
          <h5 class="ls-title-5">FILIAÇÃO: <?php echo $row_vinculo['aluno_filiacao1']; ?> <?php if ($row_vinculo['aluno_filiacao2']<>"") { ?> | <?php echo $row_vinculo['aluno_filiacao2']; ?><?php } ?></h5>
          <h5 class="ls-title-5">CPF: <?php echo $row_vinculo['aluno_cpf']; ?></h5>
          <h5 class="ls-title-5">NIS: <?php echo $row_vinculo['aluno_nis']; ?></h5>
       
      </div>
    </div>
  </div>
    
    <hr>
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-inline row">
    
    
	<label class="ls-label col-md-12">
      <b class="ls-label-text">Data</b>
      <input type="date" name="pse_s_bucal_data" id="pse_s_bucal_data" value="<?php echo date('Y-m-d'); ?>" size="32" class="validate placeholder">
    </label>


	<label class="ls-label col-md-4">
      <b class="ls-label-text">Decíduos</b>
      <input type="tel" id="pse_s_bucal_decidua" name="pse_s_bucal_decidua" value="0" size="32" autocomplete="off" onClick="this.setSelectionRange(0, this.value.length)" onchange="avaliados()" required>
    </label>    

	<label class="ls-label col-md-4">
      <b class="ls-label-text">Permanentes</b>
      <input type="tel" id="pse_s_bucal_permanente" name="pse_s_bucal_permanente" value="0" size="32" autocomplete="off" onClick="this.setSelectionRange(0, this.value.length)"  onchange="avaliados()" required>
    </label>    


	<label class="ls-label col-md-4">
      <b class="ls-label-text">Qtd dentes avaliados</b>
      <input type="tel" id="pse_s_bucal_qtd_dentes" name="pse_s_bucal_qtd_dentes" value="0" size="32" autocomplete="off" readonly>
    </label>    

	<label class="ls-label col-md-12">
      <b class="ls-label-text">Periodontal?</b>
      <div class="ls-custom-select">
      <select class="ls-custom" name="pse_s_bucal_doenca_periodontal">
            <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>NÃO</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>SIM</option>
          </select>
      </div>
    </label>

	<label class="ls-label col-md-12">
      <b class="ls-label-text">Gengivite?</b>
      <div class="ls-custom-select">
          <select class="ls-custom" name="pse_s_bucal_gengivite">
            <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>NÃO</option>
            <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>SIM</option>
          </select>
      </div>
    </label>
    
    <label class="ls-label col-md-3">
      <b class="ls-label-text ">C</b>
      <input type="tel" name="pse_s_bucal_c1" id="pse_s_bucal_c1" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="permanente()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">P</b>
      <input type="tel" name="pse_s_bucal_p1" id="pse_s_bucal_p1" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="permanente()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">O</b>
      <input type="tel" name="pse_s_bucal_o1" id="pse_s_bucal_o1" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="permanente()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">CPO-D</b>
      <input type="tel" name="pse_s_bucal_cpod1" id="pse_s_bucal_cpod1" value="0" size="32" readonly>
    </label>

    <label class="ls-label col-md-3">
      <b class="ls-label-text">c</b>
      <input type="tel" name="pse_s_bucal_c2" id="pse_s_bucal_c2" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="leite()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">ei</b>
      <input type="tel" name="pse_s_bucal_ei2" id="pse_s_bucal_ei2" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="leite()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">o</b>
      <input type="tel" name="pse_s_bucal_o2" id="pse_s_bucal_o2" min="0" value="0" size="32" onClick="this.setSelectionRange(0, this.value.length)" onchange="leite()">
    </label>
    <label class="ls-label col-md-3">
      <b class="ls-label-text">ceo-d</b>
      <input type="tel" name="pse_s_bucal_ceod2" id="pse_s_bucal_ceod2" value="0" size="32" readonly>
    </label>

	<label class="ls-label col-md-6">
      <b class="ls-label-text">Início do tratamento</b>
      <input type="date" name="pse_s_bucal_inicio_tratamento" value="" size="32" class="validate placeholder">
    </label>

	<label class="ls-label col-md-6">
      <b class="ls-label-text">Final do tratamento</b>
       <input type="date" name="pse_s_bucal_final_tratamento" value="" size="32" class="validate placeholder">
    </label>

	<label class="ls-label col-md-6">
      <b class="ls-label-text">Cirurgião Dentista</b>
       <input type="text" name="pse_s_bucal_cirurgiao_dentista" value="" size="32">
    </label>

	<label class="ls-label col-md-6">
      <b class="ls-label-text">Auxiliar de Saúde Bucal</b>
       <input type="text" name="pse_s_bucal_asb" value="" size="32">
    </label>


	<label class="ls-label col-md-12">
    	<textarea id="pse_s_bucal_observacoews" name="pse_s_bucal_observacoews" class="materialize-textarea" rows="6" placeholder="Observações"></textarea>
  	</label>      
      
      
   <div class="ls-actions-btn">
    <input type="submit" value="INSERIR INFORMAÇÕES" class="ls-btn-primary">
    <a class="ls-btn-danger" href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>">VOLTAR</a>
  </div>
      

      <input type="hidden" name="pse_s_bucal_inicio_tratamento_data_hora" value="<?php echo date('Y-m-d H:i:s'); ?>" size="32">
      <input type="hidden" name="pse_s_bucal_aluno_id" value="<?php echo $row_vinculo['vinculo_aluno_id_aluno']; ?>">
      <input type="hidden" name="pse_s_bucal_matricula_id" value="<?php echo $row_vinculo['vinculo_aluno_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
      <p>&nbsp;</p>
    </form>
    
    
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

<script>

const input = document.querySelector("input");
input.addEventListener("click", function() {
    this.select();
})

function permanente(){    
    var pse_s_bucal_c1 = parseInt(document.getElementById('pse_s_bucal_c1').value, 10);
    var pse_s_bucal_p1 = parseInt(document.getElementById('pse_s_bucal_p1').value, 10);
    var pse_s_bucal_o1 = parseInt(document.getElementById('pse_s_bucal_o1').value, 10);
    document.getElementById('pse_s_bucal_cpod1').value = pse_s_bucal_c1 + pse_s_bucal_p1 + pse_s_bucal_o1;
}

function leite(){    
    var pse_s_bucal_c2 = parseInt(document.getElementById('pse_s_bucal_c2').value, 10);
    var pse_s_bucal_ei2 = parseInt(document.getElementById('pse_s_bucal_ei2').value, 10);
    var pse_s_bucal_o2 = parseInt(document.getElementById('pse_s_bucal_o2').value, 10);
    document.getElementById('pse_s_bucal_ceod2').value = parseInt(pse_s_bucal_c2 + pse_s_bucal_ei2 + pse_s_bucal_o2);
	
}

function avaliados(){    
    var pse_s_bucal_decidua = parseInt(document.getElementById('pse_s_bucal_decidua').value, 10);
    var pse_s_bucal_permanente = parseInt(document.getElementById('pse_s_bucal_permanente').value, 10);
    document.getElementById('pse_s_bucal_qtd_dentes').value = parseInt(pse_s_bucal_decidua + pse_s_bucal_permanente);
	
}


   
</script>


</body>
</html>
<?php
mysql_free_result($vinculo);
?>
