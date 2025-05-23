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

$colname_escola = "-1";
if (isset($_GET['inep'])) {
  $colname_escola = $_GET['inep'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_inep = %s", GetSQLValueString($colname_escola, "int"));
$escola = mysql_query($query_escola, $SmecelNovo) or die(mysql_error());
$row_escola = mysql_fetch_assoc($escola);
$totalRows_escola = mysql_num_rows($escola);

if ($totalRows_escola == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: index.php"); 
 	exit;
	}


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
    <title><?php echo $row_escola['escola_nome']; ?> - SMECEL</title>
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
	
	
	
	
	
<nav class="blue darken-4" role="navigation">
    <div class="nav-wrapper container">
      <a id="logo-container" href="index.php" class="brand-logo"><i class="material-icons">home</i></a>
      <ul class="right hide-on-med-and-down">
        <li><a class="waves-effect waves-light btn-flat white-text" href="aluno/"><i class="material-icons left">people</i>ÁREA DO ALUNO</a></li>
		<li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="#login"><i class="material-icons left">lock_outline</i>LOGIN</a></li>
      </ul>

      <ul id="nav-mobile" class="sidenav">
        <li><a class="waves-effect waves-light btn-flat" href="aluno/"><i class="material-icons left">people</i>ÁREA DO ALUNO</a></li>
        <li><a class="waves-effect waves-light btn-flat modal-trigger" href="#login"><i class="material-icons left">lock_outline</i>LOGIN</a></li>
      </ul>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>

   <div class="row">
      	<a href="index.php"><img src="img/fundo_quadro_smecel.jpg" width="100%"></a>
  </div> 
  

  <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        <div class="col s12 center">
        
        <img src="img/logo/<?php echo $row_escola['escola_logo']; ?>" class="responsive-img hoverable">
        
        <h5><?php echo $row_escola['escola_nome']; ?></h5>
        <p><?php echo $row_escola['escola_endereco']; ?>, <?php echo $row_escola['escola_num']; ?> - <?php echo $row_escola['escola_bairro']; ?></p>
        <p>Telefone(s): <?php echo $row_escola['escola_telefone1']; ?> <?php echo $row_escola['escola_telefone2']; ?></p>
        <p><?php echo $row_escola['escola_email']; ?></p>
          
          
        </div>
      </div>

    </div>
  </div>

  <div id="index-banner" class="parallax-container z-depth-2">
    <div class="section no-pad-bot">
      <div class="container">
        <h1 class="header center teal-text text-lighten-2"><img src="" class="responsive-img"></h1>
        <div class="row center">
          <h5 class="header col s12 light"></h5>
        </div>
        <div class="row center">
          
        </div>
        <br><br>

      </div>
    </div>
    <div class="parallax"><img src="" alt=""></div>
  </div>
  
<div class="container">
    <div class="section">

      <div class="row">
        <div class="col s12 center">
          <h3><i class="mdi-content-send brown-text"></i></h3>
          <h5>Mais escolas do município</h5>
          
          
          
      <?php do { ?>
  		<a href="escola.php?inep=<?php echo $row_Escolas['escola_inep']; ?>"><img src="img/logo/<?php echo $row_Escolas['escola_logo']; ?>" class="responsive-img" title="<?php echo $row_Escolas['escola_nome']; ?>" alt="<?php echo $row_Escolas['escola_nome']; ?>" width="120px"></a>
	  <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
          
        </div>
      </div>

    </div>
  </div>


<div class="container">
<div class="row">
	<div class="col s12 center">
      	
    </div>
</div>
</div>


  <footer class="page-footer blue darken-4">
    <div class="container">
      <div class="row">
        <div class="col l12 s12">
          <h5 class="white-text">SMECEL - Sistema de Gestão Escolar Online</h5>
          <p class="grey-text text-lighten-4">
          Avenida 13 de Maio, 437, Centro, Itagimirim-Ba, CEP: 45850-000<br>
          (73) 3289-2109 | E-mail: secretaria@smecel.com.br<br>
          Horário de atendimento: Seg-Sex das 8h às 14h</p>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text"></h5>
          <ul>
            <li><a class="white-text" href="#!"></a></li>
          </ul>
        </div>
        <div class="col l3 s12">
          <h5 class="white-text"></h5>
          <ul>
            <li><a class="white-text" href="#!"></a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Dr. Watson Informática Ltda. <a class="brown-text text-lighten-3" href="#"></a>
      </div>
    </div>
  </footer> 
  
  

  <!-- Modal Structure -->
  <div id="login" class="modal">
    <div class="modal-content">
      <h4>LOGIN</h4>
      <p></p>
	  
		<div class="row">
		<form class="col s12" method="post" action="">
		
		<div class="row">
			<div class="input-field col s12">
			<i class="material-icons prefix">email</i>
				<input type="email" id="usuario" name="usuario" value="" placeholder="E-mail" required>
				<label for="usuario">E-mail</label>
			</div>
			<div class="input-field col s12">
			<i class="material-icons prefix">lock_outline</i>
				<input type="password" id="senha" name="senha" value="" placeholder="Senha" required>
				<label for="senha">Senha</label>
			</div>
			<input type="button" id="btnEntrar" value="ACESSAR" class="waves-effect waves-light btn" />
		
		<div id="resultado">
        </div>
		</div>
		
		      <?php if (isset($_GET["err"])) { ?>
              
                <div class="card-panel red lighten-2">Usuário e/ou senha inválidos.</div>
                
              <?php } ?>
              
              <?php if (isset($_GET["saiu"])) { ?>
              
               <div class="card-panel green lighten-2">Você saiu do sistema em segurança.</div>
                
             
              <?php } ?>
		
		</form>
		</div>
	  
  
	  
	  
    </div>
    <div class="modal-footer">
	  <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">FECHAR</a>
      <a href="lembrarsenha.php" class="waves-effect waves-effect waves-green btn-flat modal-trigger">Recuperar senha</a>
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
		var upperLimit = 261;
		var randomNum = Math.floor((Math.random() * upperLimit) + 1);    
		 $(".parallax-container").css("background","url('imagens/" + randomNum + ".jpg') no-repeat center center fixed");//<--changed path
		 $(".parallax-container").css("-webkit-background-size","cover");
		 $(".parallax-container").css("-moz-background-size","cover");
		 $(".parallax-container").css("-o-background-size","cover");
		 $(".parallax-container").css("background-size","cover");
		 $(".parallax-container").css("-webkit-transition","background 4s ease-in-out");
		 $(".parallax-container").css("-moz-transition","background 4s ease-in-out");
		 $(".parallax-container").css("-o-transition","background 4s ease-in-out");
		 $(".parallax-container").css("-ms-transition","background 4s ease-in-out");
		 $(".parallax-container").css("transition","background 4s ease-in-out");
		 $(".parallax-container").css("-webkit-backface-visibility","hidden");

		});
		
	  </script>
	  
	<script type="text/javascript">
    $(document).ready(function(){
       
		
		//Login
		$("#btnEntrar").click(function(event){
            var envio = $.post("login.php", { 
            usuario: $("#usuario").val(), 
            senha: $("#senha").val() 
            })
            envio.done(function(data) {
                $("#resultado").html(data);
            })
            envio.fail(function() { alert("Erro na requisição"); }) 
        });
		
    });
	
	
	 
	
	</script>
	  
	  
    </body>
  </html>
  <?php
mysql_free_result($Escolas);

mysql_free_result($escola);
?>
