<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

include ("fnc/secLogada.php");

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_Notas = "-1";
if (isset($_GET['cd'])) {
  $colname_Notas = $_GET['cd'];
}

$hash = "-1";
if (isset($_GET['c'])) {
  $hash = $_GET['c'];
}

$nt = "-1";
if (isset($_GET['n'])) {
  $nt = "boletim_".$_GET['n'];
}



if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	$cod = $_POST['hash'];
	
  $updateSQL = sprintf("UPDATE smc_boletim_disciplinas SET boletim_1v1=%s, boletim_2v1=%s, boletim_3v1=%s, boletim_1v2=%s, boletim_2v2=%s, boletim_3v2=%s, boletim_1v3=%s, boletim_2v3=%s, boletim_3v3=%s, boletim_1v4=%s, boletim_2v4=%s, boletim_3v4=%s WHERE boletim_id=%s",
                       GetSQLValueString($_POST['boletim_1v1'], "double"),
                       GetSQLValueString($_POST['boletim_2v1'], "double"),
                       GetSQLValueString($_POST['boletim_3v1'], "double"),
                       GetSQLValueString($_POST['boletim_1v2'], "double"),
                       GetSQLValueString($_POST['boletim_2v2'], "double"),
                       GetSQLValueString($_POST['boletim_3v2'], "double"),
                       GetSQLValueString($_POST['boletim_1v3'], "double"),
                       GetSQLValueString($_POST['boletim_2v3'], "double"),
                       GetSQLValueString($_POST['boletim_3v3'], "double"),
                       GetSQLValueString($_POST['boletim_1v4'], "double"),
                       GetSQLValueString($_POST['boletim_2v4'], "double"),
                       GetSQLValueString($_POST['boletim_3v4'], "double"),
                       GetSQLValueString($_POST['boletim_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
// ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $_POST['usu_id'];
	$esc = $_POST['usu_escola'];
	$detalhes = $_POST['detalhes'];
	date_default_timezone_set('America/Bahia');
	$dat = date('Y-m-d H:i:s');

	$sql = "
	INSERT INTO smc_registros (
	registros_id_escola, 
	registros_id_usuario, 
	registros_tipo, 
	registros_complemento, 
	registros_data_hora
	) VALUES (
	'$esc', 
	'$usu', 
	'12', 
	'($detalhes)', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **
  
  

  $updateGoTo = "boletimVerAntigo.php?c=$cod";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Notas = sprintf("
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, 
boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, 
disciplina_id, disciplina_nome, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_hash,
aluno_id, aluno_nome,
turma_id, turma_nome 
FROM smc_boletim_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = $row_UsuLogado[usu_escola] AND boletim_id = %s
", GetSQLValueString($colname_Notas, "int"));
$Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
$row_Notas = mysql_fetch_assoc($Notas);
$totalRows_Notas = mysql_num_rows($Notas);

if ($totalRows_Notas == 0) {
	header("Location:turmasAlunosVinculados.php?nada");
	}
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>

<!-- CONTEÚDO -->
		
		
<div class="ls-modal" data-modal-blocked id="myAwesomeModal" style="top:-100px;">
  <div class="ls-modal-box ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title ls-txt-center">INSERIR NOTAS NO BOLETIM</h4><br>
	  
	  <div class="ls-box ls-txt-center">
	  <h4><small>ALUNO(A):</small> <?php echo $row_Notas['aluno_nome']; ?>  <small>TURMA:</small> <strong><?php echo $row_Notas['turma_nome']; ?></strong> <small>DISCIPLINA:</small> <strong><?php echo $row_Notas['disciplina_nome']; ?></strong></h4>
	  </div>
	
	</div>
	
    <div class="ls-modal-body" id="myModalBody">

	



<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="ls-form ls-form-horizontal">
<fieldset>
          	  
	  <div class="row">
	  <div class="col-md-12">
	
	  <div class="col-md-6 ls-txt-center ls-alert-success">
	  <h3>I UNIDADE</h3>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av1; ?><input type="number" size="4" max="<?php echo $av1_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_1v1" value="<?php echo htmlentities($row_Notas['boletim_1v1'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av2; ?><input type="number" size="4" max="<?php echo $av2_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_2v1" value="<?php echo htmlentities($row_Notas['boletim_2v1'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av3; ?><input type="number" size="4" max="<?php echo $av3_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_3v1" value="<?php echo htmlentities($row_Notas['boletim_3v1'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  </div>
	  
	  <div class="col-md-6 ls-txt-center ls-alert-info">
	  <h3>II UNIDADE</h3>

	  <label class="ls-label col-md-4">
      <?php echo $av1; ?><input type="number" size="4" max="<?php echo $av1_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_1v2" value="<?php echo htmlentities($row_Notas['boletim_1v2'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av2; ?><input type="number" size="4" max="<?php echo $av2_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_2v2" value="<?php echo htmlentities($row_Notas['boletim_2v2'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av3; ?><input type="number" size="4" max="<?php echo $av3_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_3v2" value="<?php echo htmlentities($row_Notas['boletim_3v2'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  </div>
	  
 	  
	  <div class="col-md-6 ls-txt-center ls-alert-warning">
	  <h3>III UNIDADE</h3>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av1; ?><input type="number" size="4" max="<?php echo $av1_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_1v3" value="<?php echo htmlentities($row_Notas['boletim_1v3'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av2; ?><input type="number" size="4" max="<?php echo $av2_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_2v3" value="<?php echo htmlentities($row_Notas['boletim_2v3'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av3; ?><input type="number" size="4" max="<?php echo $av3_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_3v3" value="<?php echo htmlentities($row_Notas['boletim_3v3'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  </div>
	  <div class="col-md-6 ls-txt-center ls-alert-danger">
	  <h3>IV UNIDADE</h3>

	  <label class="ls-label col-md-4">
      <?php echo $av1; ?><input type="number" size="4" max="<?php echo $av1_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_1v4" value="<?php echo htmlentities($row_Notas['boletim_1v4'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av2; ?><input type="number" size="4" max="<?php echo $av2_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_2v4" value="<?php echo htmlentities($row_Notas['boletim_2v4'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  
	  <label class="ls-label col-md-4">
      <?php echo $av3; ?><input type="number" size="4" max="<?php echo $av3_max; ?>" step="0.5" placeholder="Nota" class="ls-field-lg nota" name="boletim_3v4" value="<?php echo htmlentities($row_Notas['boletim_3v4'], ENT_COMPAT, 'utf-8'); ?>">
	  </label>
	  </div>
	  	 
	   </div>
	   </div>
	  
	  
	  
	  <div class="ls-modal-footer">
      <input type="submit" value="GRAVAR NOTAS" class="ls-btn-primary ls-btn-lg">
	  <a href="boletimVer.php?c=<?php echo $hash; ?>" class="ls-btn-danger ls-btn-lg ls-float-right">Cancelar</a>
	  </div>	
		
	  <input type="hidden" name="MM_update" value="form1" />
	  
	  
  <input type="hidden" name="boletim_id" value="<?php echo $row_Notas['boletim_id']; ?>" />
  <input type="hidden" name="hash" value="<?php echo $row_Notas['vinculo_aluno_hash']; ?>" />
  
  		  	<input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="<?php echo $row_Notas['aluno_nome']; ?>, TURMA: <?php echo $row_Notas['turma_nome']; ?>, DISCIPLINA: <?php echo $row_Notas['disciplina_nome']; ?>">

  
  </fieldset>
</form>

	
	
	
	</div>
  </div>
</div><!-- /.modal -->
	
		
		
		

		

		
		
		
		<!-- CONTEÚDO -->




    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	
<script src="../js/jquery.mask.js"></script> 

<script type="text/javascript">

$(document).ready(function(){
	
	
  $('.nota').mask('00.0', {reverse: true});

  $('.money').mask('000.000.000.000.000,00', {reverse: true});

});

</script>
<script>
  locastyle.modal.open("#myAwesomeModal");
</script>	
	
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
