<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php


header("Location: index.php");


//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php?saiu";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "6";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT *
FROM smc_aluno
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, 
vinculo_aluno_vacina_data_retorno, turma_id, turma_nome, turma_turno, turma_tipo_atendimento, turma_etapa, escola_id, escola_foto_aluno 
FROM smc_vinculo_aluno 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);



//Upload

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	
	
// Aqui incluimos a classe upload
include('../../sistema/funcoes/class.upload.php');
// Instanciamos o objeto Upload
$handle = new Upload($_FILES['fotoTexto']);
$hash_aluno = $_POST['aluno_hash'];
$aluno_id = $_POST['aluno_id'];


if ($handle->uploaded) 
{ 
// Definimos as configurações desejadas da imagem maior
$handle->image_resize = true;
$handle->image_ratio_crop = true;
//$handle->image_ratio_y = true;
$handle->image_x = 270;
$handle->image_y = 360;
$handle->file_max_size = '128M';

//$handle->image_watermark = 'watermark.png';
//$handle->image_watermark_x = -10;
//$handle->image_watermark_y = -10;
//$handle->file_name_body_add = "_rastro101";
$handle->file_new_name_body   = 'fotoaluno_'.$aluno_id.'_'.md5(date('YmdHis'));
$handle->mime_check = true;
// Definimos a pasta para onde a imagem maior será armazenada
$handle->Process('../../aluno/fotos2/');
// Em caso de sucesso no upload podemos fazer outras ações como insert em um banco de cados
$nome_da_imagem = $handle->file_dst_name;
	
if ($handle->processed) 
{
	
	
  $updateSQL = sprintf("UPDATE smc_aluno SET aluno_foto2 = '$nome_da_imagem' WHERE aluno_hash=%s",
                       GetSQLValueString($_POST['aluno_hash'], "text"));
  
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
    
  $updateGoTo = "foto.php";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
  
	
  //$updateGoTo = "celular.php?aluno=".$_POST['aluno_hash'];


	
	
// Eximos a informação de sucesso ou qualquer outra ação de sua escolha
//echo '<fieldset>';
//echo ' <legend><b>Imagem enviada com sucesso!</b></legend>';
//echo '<br /></fieldset>';	
}
}
else 
{

$updateGoTo1 = "foto.php";

  header(sprintf("Location: %s", $updateGoTo1));}

} 

//FINAL UP

//Upload
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />

  
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>


</head>
<body class="indigo lighten-5">

<?php include "menu_top.php"?>

<div class="container">
  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 hide-on-small-only">
      <p>



        <?php 
        if (!empty($row_AlunoLogado['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_AlunoLogado['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_AlunoLogado['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>


				<br>
		<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small>
      </p>

	<?php include "menu_esq.php"; ?>

    </div>
        
    <div class="col s12 m10">
      
 <h5><strong>Foto do aluno</strong></h5>
 <hr>
 <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>voltar</a> 


<?php if ($row_Matricula['escola_foto_aluno']=="S") { ?>


<p>

<div class="card-panel green lighten-5">
  <strong>ATENÇÃO 
  <p>Este campo é exclusivo para o cadastro da foto de identificação do aluno.</p></strong>

  <i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não cadastrar fotos de objetos, animais, ou outras pessoas.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não cadastrar fotos que tenham algum tipo de conotação sexual.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não incluir fotos com sinais obscenos ou ofensivos.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não cadastrar imagens com texto, marcas, ou logos comerciais.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não usar fotos editadas com filtros ou montagens exageradas.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não usar máscaras, óculos escuros ou chapéus que escondam o rosto.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não cadastrar selfies em ângulos extremos que dificultem a visualização.<br><br>

<i class="material-icons red-text left tinny">do_not_disturb_alt</i> Não cadastrar fotos com fantasias ou acessórios chamativos.<br><br>

<i class="material-icons green-text left tinny">done</i> Cadastrar a foto que mostre nitidamente o rosto do aluno.<br><br>

<i class="material-icons green-text left tinny">done</i> Utilizar, se possível, uma parede de cor clara no fundo.<br><br>

<i class="material-icons green-text left tinny">done</i> Garantir que a foto seja tirada em um local bem iluminado.<br><br>

<i class="material-icons green-text left tinny">done</i> Se possível, utilize o uniforme da escola na foto.<br><br>

<i class="material-icons green-text left tinny">done</i> Certificar-se de que o rosto esteja centralizado e bem enquadrado.<br><br>

<i class="material-icons green-text left tinny">done</i> Manter uma expressão natural, como um sorriso leve ou neutro.<br><br>

<i class="material-icons green-text left tinny">done</i> Preferir uma foto recente, que represente a aparência atual do aluno.<br><br>




</div>

</p>
    
<p>		

          <form method="post" enctype="multipart/form-data" action="foto.php" name="form1" id="form1" class="">
            <div class="row">
              <div class="col s12">
			  
	<small>Escolha uma foto ou tire do celular</small>		  
	<div class="file-field input-field">
      <div class="btn">
        <span><i class="material-icons">photo_camera</i></span>
        <input id='input-file' type='file' value='' name="fotoTexto" accept="image/*" />
		<small class="resultado_foto" id='file-name' style="display:none;"></small>
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text">
      </div>
    </div>
			  
			  
				
              </div>
            </div>
            <div class="row">
			  
              <div class="col s6 m6"> 
			  
			  <small>PREVIEW</small>
			  <img id="exibeFoto" src="<?php echo '../../aluno/fotos2/' ?>semfoto.jpg" alt="Preview"  width="100%" />
			  
			  </div>
              <div class="col s6 m6"> 
				<small>IMAGEM ATUAL</small>
					<div style="max-width:400px;" class="ls-txt-center">

					 <?php if ($row_AlunoLogado['aluno_foto2']=="") { ?>
            <img src="<?php echo '../../aluno/fotos2/' ?>semfoto.jpg" width="100%">
            <?php } else { ?>
              <img src="<?php echo '../../aluno/fotos2/' ?><?php echo $row_AlunoLogado['aluno_foto2']; ?>" width="100%">
              <?php } ?>
					
          
          
          </div>
					
					    <input type="hidden" name="aluno_hash" value="<?php echo $row_AlunoLogado['aluno_hash']; ?>" />
						<input type="hidden" name="aluno_id" value="<?php echo $row_AlunoLogado['aluno_id']; ?>" />
						<input type="hidden" name="MM_insert" value="form1" />
              </div>
			 

            </div>
            <div class="row">
              <div class="col s12">
                <input type="submit" name="enviarFoto" id="btnEnviar" value="SALVAR FOTO" class="btn" />
                <a href="index.php" id="btnVoltar" class="btn-flat">VOLTAR</a>
                <div class="progress">
                  <div class="bar"></div >
                  <div class="percent">0%</div >
                </div>
                <small>
                <div id="aviso"></div>
                </small> <small>
                <div id="status"></div>
                </small> </div>
            </div>
          </form>

</p>   

<?php } else { ?>   

<div class="card-panel green lighten-5"><strong>ATENÇÃO</strong> <p>Para trocar sua foto entre em contato com a secretaria da escola.</p></div>


<?php } ?>   
      
    </div>
    
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.dropdown-trigger').dropdown();
		});
	</script>
	
	
	<script type="text/javascript">	
	var $input    = document.getElementById('input-file'),
    $fileName = document.getElementById('file-name');
	$input.addEventListener('change', function(){
	$fileName.textContent = this.value;
	});
	</script> 
<script type="text/javascript">	
	
function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#exibeFoto').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#input-file").change(function() {
  readURL(this);
});

</script> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
<script src="https://malsup.github.com/jquery.form.js"></script> 
<script>

(function() {
    
var bar = $('.bar');
var percent = $('.percent');
var status = $('#status');
var aviso = $('#aviso');
   
$('form').ajaxForm({
    beforeSend: function() {
        status.empty();
        var percentVal = '0%';
        bar.width(percentVal)
        percent.html(percentVal);
		//location.reload();
    },
    uploadProgress: function(event, position, total, percentComplete) {
        var percentVal = percentComplete + '%';
        bar.width(percentVal)
        percent.html(percentVal);
		aviso.html("Após o carregamento total (100%), aguarde alguns instantes.");
		$("#btnEnviar").attr("disabled", true);
		$("#input-file").attr("disabled", true);
		$("#btnVoltar").css("visibility", "hidden");

    },
    success: function() {
        var percentVal = '100%';
        bar.width(percentVal)
        percent.html(percentVal);
		//alert("Imagem enviada com sucesso. Redirecionando...");
		//window.location.href = "vinculoAlunoExibirTurmaFoto.php#<?php echo $row_aluno['aluno_hash']; ?>";
		
    },
	complete: function(xhr) {
		aviso.html("");
		status.html("Redirecionando...");
		
		//location.reload();
		//alert("Imagem enviada com sucesso. Redirecionando...");
		window.location.href = "<?php echo $linkSegue; ?>";

		
	}
}); 

})();       
</script>
	
	
</body>
</html>
<?php
mysql_free_result($AlunoLogado);

mysql_free_result($Matricula);
?>
