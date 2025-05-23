<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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
	
	<script type="text/javascript" src="webcam/webcam.js"></script>
        <script type="text/javascript">
            //Configurando o arquivo que vai receber a imagem
            webcam.set_api_url('webcam/upload.php');

            //Setando a qualidade da imagem (1 - 100)
            webcam.set_quality(85);

            //Habilitando o som de click
            webcam.set_shutter_sound(true);

            //Definindo a função que será chamada após o termino do processo
            webcam.set_hook('onComplete', 'my_completion_handler');

            //Função para tirar snapshot
            function take_snapshot() {
                document.getElementById('upload_results').innerHTML = '<h3>Salvando imagem...</h3>';
                webcam.snap();
            }

            //Função callback que será chamada após o final do processo
            
			function my_completion_handler(msg) {
                if (msg.match(/(http\:\/\/\S+)/)) {
                    var htmlResult = '<h3>Imagem cadastrada!</h3>';
                    htmlResult += '<img src="'+msg+'" /><br>';
                    htmlResult += msg;
                    document.getElementById('upload_results').innerHTML = htmlResult;
                    document.getElementById('upload_nome').innerHTML = msg;
					$('#upload_nome').append(msg);
                    webcam.reset();
                }
                else {
                    alert("PHP Erro: " + msg);
                }
            }
			
			/*
			function my_completion_handler(msg) {
            // extract URL out of PHP output
            if (msg.match(/(images\S+)/)) {
                // show JPEG image in page
                document.getElementById('upload_results').innerHTML ='<h1>Upload Successful!</h1>';
                document.getElementById('teste').value = msg;
                 // show JPEG image in page


            $('#img').append("<img src="+msg+" class=\"img\">");
            setTimeout(function() {
             $('.img').remove();
            }, 4000);

            // reset camera for another shot
            webcam.reset();
            }
            else {
                alert("PHP Error: " + msg);
            }}
			*/
			
        </script>
	
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>



    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">MODELO</h1>
		<!-- CONTEÚDO -->
		<script type="text/javascript">
            //Instanciando a webcam. O tamanho pode ser alterado
            document.write(webcam.get_html(300, 400));
        </script>
		
		<form>
            <input type="button" value="Configurar" onClick="webcam.configure()">
            &nbsp;&nbsp;
            <input type="button" value="Tirar Foto" onClick="take_snapshot()">
            &nbsp;&nbsp;
            <input type="button" value="Reset" onClick="webcam.reset()">
            <input type="text" id="upload_nome" value="">
        </form>
        <div id="upload_results"></div>
		

		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>

   

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
