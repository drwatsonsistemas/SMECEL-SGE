<?php require_once('Connections/SmecelNovo.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_ue = '1'";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);
?>
<!DOCTYPE html>
<html>
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
<title>SMECEL - Sistema de Gestão Escolar Municipal</title>

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>


<div id="modalSenha" class="modal">
    <div class="modal-content">

        <center>
        <img src="img/logo_smecel_card.png" width="300">
        </center>


        <form method="post" action="" class="col s12">
          <div class="row">
            <div class="input-field col s12"> <i class="material-icons prefix">email</i>
              <input type="email" id="email" name="email" value="" placeholder="Informe o e-mail para recuperar a senha" required>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <a type="button" id="btnSenha" class="waves-effect waves-light btn">Recuperar senha</a>
            </div>
          </div>
          <div id="resultadoSenha"> 
            <!-- Essa div irá receber os resultados --> 
          </div>
        </form>


    </div>
    <div class="modal-footer">
      <a href="index.php" class="waves-effect waves-green btn-flat">VOLTAR</a>
    </div>
  </div>
  





<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="js/materialize.min.js"></script> 
<script>
		$('.parallax').parallax();
		$('.sidenav').sidenav();
		$('.modal').modal();
			
    $(document).ready(function(){
        //Recuperar senha
		$("#btnSenha").click(function(event){
            var envio = $.post("senha.php", { 
            email: $("#email").val() 
            })
            envio.done(function(data) {
                $("#resultadoSenha").html(data);
            })
            envio.fail(function() { alert("Erro na requisição"); }) 
        });

		$('#modalSenha').modal({
		dismissible: false
	});
    $('#modalSenha').modal('open');
		
		
    });
	
	

	
	</script>
	 
	
</body>
</html>
<?php
mysql_free_result($Escolas);
?>
