<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php
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
$MM_authorizedUsers = "7";
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

//Upload

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	

include('../../sistema/funcoes/class.upload.php');

$handle = new Upload($_FILES['fotoTexto']);
$func_id = $_POST['func_id'];


if ($handle->uploaded) 
{ 

$handle->image_resize = true;
$handle->image_ratio_crop = true;

$handle->image_x = 270;
$handle->image_y = 360;
$handle->file_max_size = '128M';


$handle->file_new_name_body = 'fotofunc_'.$func_id.'_'.md5(date('YmdHis'));
$handle->mime_check = true;

$handle->Process('../fotos/');

$nome_da_imagem = $handle->file_dst_name;
	
if ($handle->processed) 
{
	
	
  $updateSQL = sprintf("UPDATE smc_func SET func_foto = '$nome_da_imagem' WHERE func_id=%s",
                       GetSQLValueString($_POST['func_id'], "text"));

  
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
  $updateGoTo = "foto.php?editada";
  

  header(sprintf("Location: %s", $updateGoTo));
  
	}
}
else 
{

$updateGoTo1 = "foto.php?editada";

  header(sprintf("Location: %s", $updateGoTo1));}
} 

//final upload


$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

$linkSegue = "foto.php?editada";

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
<title><?php echo $row_ProfLogado['func_nome']?> - </title>

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>

<!--Let browser know website is optimized for mobile-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
</head>

<body class="indigo lighten-5">
<?php include ("menu_top.php"); ?>
<div class="section no-pad-bot" id="index-banner">
  <div class="container">
    <div class="row white" style="margin: 10px 0;">
      <div class="col s12 m2 hide-on-small-only">
	  
        
		<p> 
		
        <?php if ($row_ProfLogado['func_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%" class="hoverable">
        <?php } ?>		
		
		<br>
		
          <small style="font-size:14px;"> <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
          <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
          </small> 
		  
		  </p>
        <?php include "menu_esq.php"; ?>
      </div>
      <div class="col s12 m10">
        <h5>Foto:</h5>
        <hr>
        <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
        <div class="card-panel">
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
			  
              <div class="col s6 m6"> <small>PREVIEW</small> <img id="exibeFoto" src="<?php echo '../fotos/' ?>semfoto.jpg" alt="Preview"  width="100%" /> </div>
              <div class="col s6 m6"> <small>IMAGEM ATUAL</small>
                <div style="max-width:400px;" class="ls-txt-center">
                  <?php if ($row_ProfLogado['func_foto']=="") { ?>
                  <img src="<?php echo '../fotos/' ?>semfoto.jpg" width="100%">
                  <?php } else { ?>
                  <img src="<?php echo '../fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%">
                  <?php } ?>
                </div>
                <input type="hidden" name="func_id" value="<?php echo $row_ProfLogado['func_id']; ?>" />
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
        </div>
      </div>

    </div>
  </div>
</div>

 <?php include ("rodape.php"); ?>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript" src="../js/app.js"></script> 

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

<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script> 



<?php if (isset($_GET["editada"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">IMAGEM SALVA COM SUCESSO</button>'});
</script>
  <?php } ?>
  
</body>
</html>
<?php
mysql_free_result($ProfLogado);
?>