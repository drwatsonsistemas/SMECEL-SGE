<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('../funcoes/url_base.php'); ?>

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
//Upload

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
		if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		break;
	}
	
// Aqui incluimos a classe upload
include('../funcoes/class.upload.php');
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
$handle->Process('../../aluno/fotos/');
// Em caso de sucesso no upload podemos fazer outras ações como insert em um banco de cados
$nome_da_imagem = $handle->file_dst_name;
	
if ($handle->processed) 
{
	
	
  $updateSQL = sprintf("UPDATE smc_aluno SET aluno_foto = '$nome_da_imagem' WHERE aluno_hash=%s",
                       GetSQLValueString($_POST['aluno_hash'], "text"));

  
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
  $updateGoTo = "celular.php?aluno=$hash_aluno&salva#$hash_aluno";
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

$updateGoTo1 = "celular.php?aluno=$hash_aluno&sem";

  header(sprintf("Location: %s", $updateGoTo1));}

} 

//FINAL UP

//Upload





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

$colname_aluno = "-1";
if (isset($_GET['aluno'])) {
  $colname_aluno = $_GET['aluno'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aluno = sprintf("SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, aluno_recebe_bolsa_familia, aluno_foto FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($colname_aluno, "text"));
$aluno = mysql_query($query_aluno, $SmecelNovo) or die(mysql_error());
$row_aluno = mysql_fetch_assoc($aluno);
$totalRows_aluno = mysql_num_rows($aluno);

if (isset($_GET['cmatricula'])) {

  $cmatricula = $_GET['cmatricula'];
  $linkSegue = "matriculaExibe.php?cmatricula=".$cmatricula;

} else {

$linkSegue = "vinculoAlunoExibirTurmaFoto.php#".$row_aluno['aluno_hash'];
	
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
	
	<style>
	
input[type='file'] {
  display: none
}

.input-wrapper label {
  background-color: #3498db;
  border-radius: 5px;
  color: #fff;
  padding: 10px;
  cursor: pointer;
  
}

.input-wrapper label:hover {
  background-color: #2980b9
}

.resultado_foto {
	display: block;
	margin: 10px 0;
	padding: 10px 0;
}



.progress { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; margin-top:10px; }
.bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
.percent { position:absolute; display:inline-block; top:3px; left:48%; }


	
	</style>
	

	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>




		<!-- CONTEÚDO -->
		
<div class="ls-modal" data-modal-blocked id="myAwesomeModal" style="top:0px;">
  <div class="ls-modal-box ls-modal-small">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title ls-txt-center">FOTO DE <?php echo $row_aluno['aluno_nome']; ?></h4>
	  <h5 class="ls-modal-title ls-txt-center"><small>Clique no botão abaixo para tirar uma foto pelo celular</small></h5>
	  	
	</div>
	
    <div class="ls-modal-body" id="myModalBody">
	
	
			
<form method="post" enctype="multipart/form-data" action="celular.php" name="form1" id="form1" class="ls-form ls-form-inline row">


<div class="row">

<div class="col-md-12">

<?php if (isset($_GET["sem"])) { ?>

                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                   ESCOLHA UMA IMAGEM PARA ENVIAR
                </div>
        <?php } ?>
		
		
		<?php if (isset($_GET["salva"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-camera"></i> FOTO CADASTRADA COM SUCESSO. <a href="vinculoAlunoExibirTurmaFoto.php#<?php echo $row_aluno['aluno_hash']; ?>">VOLTAR</a>
                </div>
        <?php } ?>
</div> 

</div>

<div class="row">


<div class="col-md-12">



<label class="ls-label col-xs-6">
<div class='input-wrapper'>
  <label for='input-file'>
    <i class="ls-ico-camera"></i> Escolher foto
  </label>
  <input id='input-file' type='file' value='' name="fotoTexto" accept="image/*" />
  <small class="resultado_foto" id='file-name' style="display:none;"></small>
</div>
</label>



</div>


</div>

<div class="row">


<div class="col-md-6 col-xs-6">

<small>PREVIEW</small>
<img id="exibeFoto" src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" alt="Preview"  width="100%" />


</div>

<div class="col-md-6 col-xs-6">
<small>IMAGEM ATUAL</small>

	<div style="max-width:400px;" class="ls-txt-center">
	 
	 
	 <?php if ($row_aluno['aluno_foto']=="") { ?>
	 
			<img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" width="100%">
			<?php } else { ?>
			<img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_aluno['aluno_foto']; ?>" width="100%">
			
	<?php } ?>
	 
	 </div>

</div>

</div>




</div>



   <input type="hidden" name="aluno_hash" value="<?php echo $row_aluno['aluno_hash']; ?>" />
   <input type="hidden" name="aluno_id" value="<?php echo $row_aluno['aluno_id']; ?>" />
   <input type="hidden" name="MM_insert" value="form1" />
	
	
	<div class="row">
<div class="col-md-12">

<div class="ls-modal-footer">
     
<input type="submit" name="enviarFoto" id="btnEnviar" value="SALVAR FOTO" class="ls-btn-primary ls-btn-lg ls-float-right" />
<a href="vinculoAlunoExibirTurmaFoto.php#<?php echo $row_aluno['aluno_hash']; ?>" id="btnVoltar" class="ls-btn-danger ls-btn-lg">VOLTAR</a>

	<div class="progress">
        <div class="bar"></div >
        <div class="percent">0%</div >
    </div>
	<small><div id="aviso"></div></small>
	<small><div id="status"></div></small>
    
		 
 
 </div>




</div>
</div>
	
	
</form>


</div>

	
	</div>
  </div>
</div><!-- /.modal -->	 
		
		
		<!-- CONTEÚDO -->


   

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
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

<script>
  locastyle.modal.open("#myAwesomeModal");
</script>

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($aluno);
?>
