<?php
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php"; 
include "fnc/anti_injection.php"; 

// Verifica se o formulário foi submetido
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

    // Inclusão da classe de upload
    include('../../sistema/funcoes/class.upload.php');

    $handle = new Upload($_FILES['fotoTexto']);
    $func_id = $_POST['func_id'];

    // Verifica se o arquivo foi enviado e processa a imagem
    if ($handle->uploaded) {

        // Redimensionamento e outras configurações
        $handle->image_resize = true;
        $handle->image_ratio_crop = true;
        $handle->image_x = 270;
        $handle->image_y = 360;
        $handle->file_max_size = '128M';
        $handle->file_new_name_body = 'fotofunc_'.$func_id.'_'.md5(date('YmdHis'));
        $handle->mime_check = true;

        // Processa o arquivo para o diretório especificado
        $handle->Process('../fotos/');

        // Se o upload for bem-sucedido, atualiza a foto no banco de dados
        if ($handle->processed) {

            // Nome da imagem
            $nome_da_imagem = $handle->file_dst_name;

            try {
                // Preparar consulta para atualizar a foto no banco de dados
                $updateSQL = "UPDATE smc_func SET func_foto = :func_foto WHERE func_id = :func_id";
                $stmt = $SmecelNovo->prepare($updateSQL);

                // Vincula os parâmetros ao statement
                $stmt->bindParam(':func_foto', $nome_da_imagem, PDO::PARAM_STR);
                $stmt->bindParam(':func_id', $func_id, PDO::PARAM_INT);

                // Executa a atualização
                $stmt->execute();

                // Redireciona para a página de foto com a flag 'editada'
                $updateGoTo = "foto.php?editada";
                header("Location: $updateGoTo");
                exit;

            } catch (PDOException $e) {
                // Caso ocorra um erro na consulta, mostra a mensagem de erro
                die("Erro ao atualizar a foto: " . $e->getMessage());
            }
        }
    } else {
        // Se o upload falhou, redireciona de volta para a página de foto
        $updateGoTo1 = "foto.php?editada";
        header("Location: $updateGoTo1");
        exit;
    }
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
<title>PROFESSOR |<?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
  
    <h1 class="ls-title-intro ls-ico-home">ALTERAR FOTO</h1>
    
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    
    <form method="post" enctype="multipart/form-data" action="foto.php" name="form1" id="form1" class="ls-form row">
      <div class="row1">
        <div class="col-xs-12">
          <p><small>Escolha uma foto ou tire do celular</small></p>
          <input id='input-file' type='file' value='' name="fotoTexto" accept="image/*" />
          <small class="resultado_foto" id='file-name' style="display:none;"></small> </div>
      </div>
      <hr>
      <div class="row1">
        <div class="col-xs-6"> <small>PREVIEW</small> <img id="exibeFoto" src="<?php echo '../fotos/' ?>semfoto.jpg" alt="Preview"  width="100%" /> </div>
        <div class="col-xs-6"> <small>ATUAL</small>
          <div style="max-width:400px;" class="ls-txt-center">
            <?php if ($row_ProfLogado['func_foto']=="") { ?>
            <img src="<?php echo '../fotos/' ?>semfoto.jpg" width="100%">
            <?php } else { ?>
            <img src="<?php echo '../fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%">
            <?php } ?>
          </div>
        </div>
      </div>
      <hr><br>
      <div class="col-xs-12">
      <br>
        <input type="submit" name="enviarFoto" id="btnEnviar" value="SALVAR FOTO" class="ls-btn-sucess" />
        <input type="hidden" name="func_id" value="<?php echo $row_ProfLogado['func_id']; ?>" />
        <input type="hidden" name="MM_insert" value="form1" />
        <br><br>
      </div>
      <div class="col-xs-12">
        <div class="progress">
          <div class="bar"></div >
          <div class="percent"></div >
        </div>
        <small>
        <div id="aviso"></div>
        </small> <small>
        <div id="status"></div>
        </small> </div>
    </form>
    
  </div>
  <?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js1"></script> 
<script src="https://malsup.github.com/jquery.form.js1"></script> 
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
<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>