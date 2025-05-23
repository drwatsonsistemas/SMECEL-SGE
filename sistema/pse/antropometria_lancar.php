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
  $insertSQL = sprintf("INSERT INTO sms_pse_antropometria (antrop_id_aluno, antrop_id_matricula, antrop_data, antrop_altura, antrop_peso) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['antrop_id_aluno'], "int"),
                       GetSQLValueString($_POST['antrop_id_matricula'], "int"),
                       GetSQLValueString(inverteData($_POST['antrop_data']), "date"),
                       GetSQLValueString($_POST['antrop_altura'], "double"),
                       GetSQLValueString($_POST['antrop_peso'], "double"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "antropometria.php?lancado";
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

    <p><a class="ls-btn" href="antropometria.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>">VOLTAR</a></p>
    
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
  <div class="col-md-4 col-sm-6">
  		  <h3 class="ls-title-3 ls-color-theme"><?php echo $row_vinculo['aluno_nome']; ?></h3>
          <h6 class="ls-title-6"><?php echo inverteData($row_vinculo['aluno_nascimento']); ?> (<?php echo idade($row_vinculo['aluno_nascimento']); ?> anos)</h6>
          <h6 class="ls-title-6">FILIAÇÃO: <?php echo $row_vinculo['aluno_filiacao1']; ?> </h6>
		  <?php if ($row_vinculo['aluno_filiacao2']<>"") { ?><h6 class="ls-title-6">FILIAÇÃO:  <?php echo $row_vinculo['aluno_filiacao2']; ?></h6><?php } ?>
          <h6 class="ls-title-6">CPF: <?php echo $row_vinculo['aluno_cpf']; ?></h6>
          <h6 class="ls-title-6">NIS: <?php echo $row_vinculo['aluno_nis']; ?></h6>
          <br><br>
  </div>
  
  <div class="col-md-6 col-sm-12">
   <form id="enviar_antropometria" method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row ls-box ls-box-gray">
  
  <label class="ls-label col-md-12">
    <b class=" ls-txt-center">DATA</b>
    <input type="text" name="antrop_data" value="<?php echo date("d-m-Y"); ?>" class="datepicker ls-txt-center" size="32"  required>
  </label>
  
  <label class="ls-label col-md-6">
    <b class=" ls-txt-center">PESO (kg)</b>
    <input type="tel" id="antrop_peso" name="antrop_peso" value="" size="32" class="peso ls-txt-center" autocomplete="off" required>
  </label>
  
  <label class="ls-label col-md-6">
    <b class=" ls-txt-center">ALTURA (metros)</b>
   <input type="tel" id="antrop_altura" name="antrop_altura" value="" size="32" class="altura ls-txt-center" autocomplete="off" required>
  </label>
  
  <div class="ls-actions-btn ls-txt-center">
  	<input id="btn_antropometria" type="submit" value="SALVAR" class="ls-btn-primary ls-btn-block">
  </div>
  
      <input type="hidden" name="antrop_id_aluno" value="<?php echo $row_vinculo['vinculo_aluno_id_aluno']; ?>">
      <input type="hidden" name="antrop_id_matricula" value="<?php echo $row_vinculo['vinculo_aluno_id']; ?>">
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
