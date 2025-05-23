<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php 
//include('fnc/anoLetivo.php'); 
//$anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano']+1;
?>

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
  $idTurma = $_POST['vinculo_aluno_id_turma'];
	
  $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_id_turma=%s, vinculo_aluno_situacao=%s, vinculo_aluno_transporte=%s, vinculo_aluno_datatransferencia=%s WHERE vinculo_aluno_id=%s",
                       GetSQLValueString($_POST['vinculo_aluno_id_turma'], "int"),
                       GetSQLValueString($_POST['vinculo_aluno_situacao'], "text"),
                       GetSQLValueString($_POST['vinculo_aluno_transporte'], "text"),
                       GetSQLValueString(inverteData($_POST['vinculo_aluno_datatransferencia']), "date"),
                       GetSQLValueString($_POST['vinculo_aluno_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
// ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $_POST['usu_id'];
	$esc = $_POST['usu_escola'];
	$detalhes = $_POST['detalhes'];
	
	$situacao1 = $_POST['vinculo_aluno_situacao'];
	
	
	switch($situacao1)
{
    case '1';
		$situacao = 'MATRICULADO';
		break;
    case '2';
		$situacao = 'TRANSFERIDO';
		break;
    case '3';
		$situacao = 'DEIXOU DE FREQUENTAR';
		break;
    case '4';
		$situacao = 'FALECIDO';
		break;
    case '5';
		$situacao = 'OUTROS';
		break;
}
	
	
	
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
	'16', 
	'($detalhes SITUACAO: $situacao)', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

  $updateGoTo = "matriculaExibeRematricula.php?vinculoEditado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

include "usuLogado.php";
include "fnc/anoLetivo.php";
$anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano']+1;

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_ano_letivo = '$anoLetivoRematricula' AND turma_id_escola = '$row_UsuLogado[usu_escola]'
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

$colname_VinculoEditar = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_VinculoEditar = $_GET['cmatricula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculoEditar = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia, aluno_id, aluno_nome, turma_id, turma_nome 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_VinculoEditar, "text"));
$VinculoEditar = mysql_query($query_VinculoEditar, $SmecelNovo) or die(mysql_error());
$row_VinculoEditar = mysql_fetch_assoc($VinculoEditar);
$totalRows_VinculoEditar = mysql_num_rows($VinculoEditar);

if ($totalRows_VinculoEditar == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
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
  <body onLoad="javascript:mudarTurmaNao()">
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">EDITAR VÍNCULO</h1>
		<!-- CONTEÚDO -->
        
<div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EDITAR REMATRÍCULA <?php echo $anoLetivoRematricula; ?></h4><br>
	  <h5><?php echo $row_VinculoEditar['aluno_nome']; ?> - <?php echo $row_VinculoEditar['turma_nome']; ?></h5>
	  <h5></h5>
    </div>
    <div class="ls-modal-body" id="myModalBody">
		
		
		
		<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
		<fieldset>
		
		
		
		
		<label class="ls-label col-md-12 ls-txt-right">
		<div class="ls-box ls-xs-space">
            <b class="ls-label-text"><small>MUDAR ALUNO DE TURMA?</small></b>
            <label class="ls-label-text">
              <input type="radio" id="mudandodeturma" name="mudandodeturma" value="1" onclick="javascript:mudarTurmaNao();" checked>
              <small>NÃO</small> </label>
            <label class="ls-label-text">
              <input type="radio" id="mudandodeturma" name="mudandodeturma" value="2" onclick="javascript:mudarTurmaSim();">
              <small>SIM</small> </label>
			  </div>
            </label>
			
		
		
		<label class="ls-label col-sm-12 vinculo_aluno_id_turma">
		<b class="ls-label-text">TURMA</b>
		<div class="ls-custom-select ls-field-lg">
			  <select name="vinculo_aluno_id_turma" id="vinculo_aluno_id_turma" class="ls-select" required>
                <?php do { ?>
                <option value="<?php echo $row_Turmas['turma_id']?>" <?php if (!(strcmp($row_Turmas['turma_id'], htmlentities($row_VinculoEditar['vinculo_aluno_id_turma'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_Turmas['turma_nome']?> - <?php echo $anoLetivoRematricula; ?></option>
                <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
              </select>
			  </div>
		  </label>
		
		
              <label class="ls-label col-md-12 vinculo_aluno_situacao"><b class="ls-label-text">SITUAÇÃO</b>
			  <div class="ls-custom-select ls-field-lg">
			  <select name="vinculo_aluno_situacao" id="vinculo_aluno_situacao">
                <option value="1" <?php if (!(strcmp(1, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>MATRICULADO</option>
                <option value="2" <?php if (!(strcmp(2, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>TRANSFERIDO</option>
                <option value="3" <?php if (!(strcmp(3, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>DEIXOU DE FREQUENTAR</option>
                <option value="4" <?php if (!(strcmp(4, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>FALECIDO</option>
                <option value="5" <?php if (!(strcmp(5, htmlentities($row_VinculoEditar['vinculo_aluno_situacao'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>OUTROS</option>
              </select>
			  </div>
			  </label>

              <label class="ls-label col-md-12 vinculo_aluno_datatransferencia"><b class="ls-label-text">DATA DA OCORRÊNCIA</b>
			  <input type="text" name="vinculo_aluno_datatransferencia" id="vinculo_aluno_datatransferencia" class="ls-field-lg date" value="<?php echo htmlentities(inverteData($row_VinculoEditar['vinculo_aluno_datatransferencia']), ENT_COMPAT, 'utf-8'); ?>" size="32">
			  </label>
			  
			  
			<label class="ls-label col-sm-12 vinculo_aluno_transporte">
            <b class="ls-label-text">UTILIZA TRANSPORTE ESCOLAR?</b><br>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" value="S" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_transporte'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
              Sim </label>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" value="N" <?php if (!(strcmp(htmlentities($row_VinculoEditar['vinculo_aluno_transporte'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>>
              Não </label>
            </label>
			
			<div class="ls-modal-footer">
			
			<input type="submit" value="SALVAR ALTERAÇÃO" class="ls-btn-primary ls-btn-lg">
			<a href="matriculaExibeRematricula.php?cmatricula=<?php echo $row_VinculoEditar['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-lg">CANCELAR</a> 
			<a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_VinculoEditar['vinculo_aluno_hash']; ?>','<?php echo $row_VinculoEditar['aluno_nome']; ?>')" class="ls-btn-danger ls-float-right ls-btn-lg">EXCLUIR VINCULO</a> 
			
	  
			</div>
              
			  
          </fieldset>
		  
		  <div class="ls-actions-btn">
			  
              </div>
			  
		  <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="vinculo_aluno_id" value="<?php echo $row_VinculoEditar['vinculo_aluno_id']; ?>">
		  
      		<input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="<?php echo $row_VinculoEditar['aluno_nome']; ?> - <?php echo $row_VinculoEditar['turma_nome']; ?>">

		  
		  
        </form>
		
	</div>
  </div>
</div><!-- /.modal -->
		
		
		
        <p>&nbsp;</p>
        <!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	<script type="text/javascript" src="../js/jquery.mask.min.js"></script>
	<script src="js/mascara.js"></script>
		<script>
	locastyle.modal.open("#myAwesomeModal");
	</script>

	<script language="Javascript">
	function confirmaExclusao(id, nome) {
     var resposta = confirm("Deseja realmente remover o vínculo deste aluno?");
     	if (resposta == true) {
     	     window.location.href = "matriculaExcluir.php?hash="+id+"&nome="+nome;
    	 }
	}
	</script>
	
	<script type="text/javascript">
function mudarTurmaSim()
{
	/*
	document.getElementById("vinculo_aluno_situacao").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_datatransferencia").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_transporte").disabled = true; //Habilitando
	document.getElementById("vinculo_aluno_id_turma").disabled = false; //Habilitando
	*/
	$(".vinculo_aluno_situacao").css("display", "none");
	$(".vinculo_aluno_datatransferencia").css("display", "none");
	$(".vinculo_aluno_transporte").css("display", "none");
	$(".vinculo_aluno_id_turma").css("display", "block");
	
}
function mudarTurmaNao()
{
	/*
	document.getElementById("vinculo_aluno_situacao").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_datatransferencia").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_transporte").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_id_turma").disabled = true; //Habilitando
	*/
	$(".vinculo_aluno_situacao").css("display", "block");
	$(".vinculo_aluno_datatransferencia").css("display", "block");
	$(".vinculo_aluno_transporte").css("display", "block");
	$(".vinculo_aluno_id_turma").css("display", "none");
	

}
</script>

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($VinculoEditar);
?>
