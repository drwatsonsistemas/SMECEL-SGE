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

//include ("fnc/secLogada.php");

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

switch($nt)
{
    case 'boletim_1v1';
		$nomeNota = '<span class="ls-tag-info">Avaliação 1 - I Unidade</span>';
		break;
    case 'boletim_2v1';
		$nomeNota = '<span class="ls-tag-primary">Avaliação 2 - I Unidade</span>';
		break;
    case 'boletim_3v1';
		$nomeNota = '<span class="ls-tag-warning">Avaliação 3 - I Unidade</span>';
		break;
    case 'boletim_1v2';
		$nomeNota = '<span class="ls-tag-info">Avaliação 1 - II Unidade</span>';
		break;
    case 'boletim_2v2';
		$nomeNota = '<span class="ls-tag-primary">Avaliação 2 - II Unidade</span>';
		break;
    case 'boletim_3v2';
		$nomeNota = '<span class="ls-tag-warning">Avaliação 3 - II Unidade</span>';
		break;
    case 'boletim_1v3';
		$nomeNota = '<span class="ls-tag-info">Avaliação 1 - III Unidade</span>';
		break;
    case 'boletim_2v3';
		$nomeNota = '<span class="ls-tag-primary">Avaliação 2 - III Unidade</span>';
		break;
    case 'boletim_3v3';
		$nomeNota = '<span class="ls-tag-warning">Avaliação 3 - III Unidade</span>';
		break;
    case 'boletim_1v4';
		$nomeNota = '<span class="ls-tag-info">Avaliação 1 - IV Unidade</span>';
		break;
    case 'boletim_2v4';
		$nomeNota = '<span class="ls-tag-primary">Avaliação 2 - IV Unidade</span>';
		break;
    case 'boletim_3v4';
		$nomeNota = '<span class="ls-tag-warning">Avaliação 3 - IV Unidade</span>';
		break;
    case 'boletim_af';
		$nomeNota = '<span class="ls-tag-danger">Avaliação Final - Recuperação</span>';
		break;
    default;
        header("Location:boletimVer.php?c=$hash&erro");
    break;
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	$cod = $_POST['hash'];
	
  $updateSQL = sprintf("UPDATE smc_boletim_disciplinas SET $nt=%s, boletim_conselho=%s WHERE boletim_id=%s",
                       GetSQLValueString($_POST[$nt], "double"),
                       GetSQLValueString($_POST['boletim_conselho'], "int"),
                       GetSQLValueString($_POST['boletim_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
// ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $_POST['usu_id'];
	$esc = $_POST['usu_escola'];
	$detalhes = $_POST['detalhes'];
	$nota = $_POST[$nt];
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
	'13', 
	'($detalhes, NOTA: $nota)', 
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
SELECT 
boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho, 
disciplina_id, disciplina_nome, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_hash,
aluno_id, aluno_nome,
turma_id, turma_nome, turma_matriz_id 
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
	header("Location:boletimCadastrar.php?erro");
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Notas[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

	  include('fnc/notas.php');
	  $av1 = "AV1";
	  $av2 = "AV2";
	  $av3 = "AV3";
	  $av1_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av2_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av3_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $cancelaLink = "";


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

  


  


    <main class="ls-main ">
      <div class="container-fluid">
 
        
		<!-- CONTEÚDO -->
		
		
<div class="ls-modal" data-modal-blocked id="myAwesomeModal" style="top:-100px;">
  <div class="ls-modal-box ls-modal-small">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">INSERIR NOTA DA DISCIPLINA</h4><br>
	  <p>Aluno(a): <strong><?php echo $row_Notas['aluno_nome']; ?></strong></p>
	  <p>Turma: <strong><?php echo $row_Notas['turma_nome']; ?></strong></p>
	  <p>Disciplina: <strong><?php echo $row_Notas['disciplina_nome']; ?></strong></p>
	  <p><?php echo $nomeNota; ?></p>
    </div>
    <div class="ls-modal-body" id="myModalBody">

	



<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="ls-form ls-form-inline">
<fieldset>
          
	  <label class="ls-label col-md-8">
	  <b class="ls-label-text">NOTA:</b>
      <input type="number" size="4" max="10" step="0.5" placeholder="Informe uma nota" class="ls-field-lg nota" name="<?php echo $nt;?>" value="<?php echo htmlentities($row_Notas[$nt], ENT_COMPAT, 'utf-8'); ?>">
	  <p class="ls-label-info"></p>
	  </label>
	  
	  
	  <label class="ls-label col-md-12" style="display:<?php if ($nt<>"boletim_af") { echo "none"; }?>">
	  <div class="ls-alert-warning">
	  <b class="ls-label-text">CONSELHO DE CLASSE</b>
      <p class="ls-label-info">
	  <input type="checkbox" name="boletim_conselho" value="1" <?php if (!(strcmp(htmlentities($row_Notas['boletim_conselho'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?>>
	  Marque se aprovado pelo Conselho de Classe nessa disciplina</p>
      </div>
	  </label>
	  
	  
	  
	  
	  
	  <div class="ls-actions-btn ls-group-btn ls-group-active">
      <input type="submit" value="GRAVAR NOTA" class="ls-btn-primary ls-btn-lg">
	  <a href="boletimVer.php?c=<?php echo $hash; ?>" class="ls-btn-primary-danger ls-btn-lg">CANCELAR</a>
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
      </div>
    </main>

    
	

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
