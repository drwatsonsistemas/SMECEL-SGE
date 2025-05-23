
<?php require_once('../Connections/SmecelNovo.php'); ?>
<?php include "../sistema/funcoes/anti_injection.php"; ?>
<?php include "../sistema/funcoes/inverteData.php"; ?>
<?php
header("Location: index.php");
exit;
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




$nascimentoBusca = "";
if (isset($_POST['nascimento'])) {
  $nascimentoBusca = anti_injection(inverteData($_POST['nascimento']));
}

$alunoBusca = "";
if (isset($_POST['aluno'])) {
  $alunoBusca = anti_injection(trim($_POST['aluno']));
}

$maeBusca = "";
if (isset($_POST['mae'])) {	
  $maeBusca = anti_injection(trim($_POST['mae']));
}


if($nascimentoBusca != ""){ $nascimento_no_where = "AND aluno_nascimento LIKE '%".$nascimentoBusca."%'"; } else { $nascimento_no_where = ""; }
if($alunoBusca != ""){ $aluno_no_where = "AND aluno_nome LIKE '%".$alunoBusca."%'"; } else { $aluno_no_where = ""; }
if($maeBusca != ""){ $mae_no_where = "AND aluno_filiacao1 LIKE '%".$maeBusca."%'"; } else { $mae_no_where = ""; }


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_buscaAluno = "
SELECT
  aluno_id, aluno_nome, aluno_nascimento, aluno_cpf, aluno_filiacao1, aluno_hash 
FROM 
  smc_aluno
WHERE
  aluno_id > 0 
  $nascimento_no_where 
  $aluno_no_where
  $mae_no_where  
";
$buscaAluno = mysql_query($query_buscaAluno, $SmecelNovo) or die(mysql_error());
$row_buscaAluno = mysql_fetch_assoc($buscaAluno);
$totalRows_buscaAluno = mysql_num_rows($buscaAluno);
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
  <title>Painel do Aluno - SMECEL</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="../css/animate.css"  media="screen,projection"/>  
  <link type="text/css" rel="stylesheet" href="css/app.css"  media="screen,projection"/>
  <style>
body {
	display: flex;
	min-height: 100vh;
	flex-direction: column;
}
main {
	flex: 1 0 auto;
}
body {
	background: #fff;
	margin: 0px;
}
.input-field input[type=date]:focus + label,  .input-field input[type=text]:focus + label,  .input-field input[type=email]:focus + label,  .input-field input[type=password]:focus + label {
	color: #e91e63;
}
.input-field input[type=date]:focus,  .input-field input[type=text]:focus,  .input-field input[type=email]:focus,  .input-field input[type=password]:focus {
	border-bottom: 2px solid #e91e63;
	box-shadow: none;
}
.container {
	width: 100vw;
	height: 100vh;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center
}
.box {
	width: 300px;
	height: 400px;
}
</style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>

<main>
    <center>
    
    
    <div class="container">
    <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">
        

        
          <p>
          <img src="../img/logo_smecel_card.png" width="350px">
          </p>
		  
		  
  <?php if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {  ?>
		  
  <div style="width:350px; height:460px;">
  <?php if ($totalRows_buscaAluno > 0) { // Show if recordset not empty ?>
  <?php if (($nascimentoBusca == "") and ($cpfBusca == "") and ($maeBusca == "")) { ?>
  <div class=""><strong>Atenção: </strong><br> Informe um dos campos acima e clique em Recuperar Dados.</div>        
  <?php } else { ?>
      <div class="card-panel">
        <h5>Aluno encontrado:</h5>
		<p><?php echo $row_buscaAluno['aluno_nome']; ?></p>
		<hr>
		<h5>Dados de acesso:</h5>
		
		<p>
		Data de nascimento: <strong><?php echo inverteData($row_buscaAluno['aluno_nascimento']); ?></strong><br>
        Código: <strong><?php echo str_pad($row_buscaAluno['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
        Senha: <strong><?php echo substr($row_buscaAluno['aluno_hash'],0,5); ?></strong> 
		</p>
		
		<small>Anote essas informações em um local seguro. <a href="index.php" target="_blank" class="btn">Fazer login</a></small>
		
		
       </div>
  <?php } ?>
  <?php } else { ?>
  <div class="card-panel">
  
  <h5>Aluno não encontrado</h5>
  Dados informados:
  <p>
  NOME: <strong><?php echo $alunoBusca; ?></strong><br>
  Data de nascimento: <strong><?php echo inverteData($nascimentoBusca); ?></strong><br>
  Nome da mãe: <strong><?php echo $maeBusca; ?></strong>
  </p>
  <p><small>Verifique se os dados informados estão corretos.</small></p>
  <a href="recuperar.php" class="btn btn-small">Nova busca</a></div>
  </div>
  <?php } ?>
  </div>

		<?php } else { ?>

        <form class="col s12" action="<?php echo $loginFormAction; ?>" method="POST" name="form1" class="box" style="width:350px; height:460px;">
            
            
			<div class='input-field col s12'>
                <input class='' id='aluno' name="aluno" type="text" required>
                <label for='aluno'>Nome do Aluno (basta o primeiro nome)</label>
              </div>
			
			<div class='input-field col s12'>
                <input class='validate date' id='nascimento' name="nascimento" type="text" required autofocus>
                <label for='nascimento'>Data de Nascimento</label>
              </div>
              
            <div class='input-field col s12'>
                <input class='validate' id='mae' name="mae" type="text" required>
                <label for='mae'>Nome da mãe (basta o primeiro nome)</label>
              </div>

              
            <center>
            <div class='row'>
                <button name="entrar" value="entrar" type="submit" class='btn waves-effect indigo'>RECUPERAR DADOS</button>
                 <a class='btn btn-flat waves-effect' href="index.php">Voltar</a>
              </div>
			  
			  
			  <div class='input-field col s12'>
			  
			  <hr>
			  
			  <strong>Atenção:</strong>
			  
			  <small>É necessário preencher todos os campos acima. Os dados devem ser idênticos aos que foram cadastrados pela secretaria de sua escola.</small>

			  
			  </div>
			  
			  
           
          </center>
		  <input type="hidden" name="MM_insert" value="form1">
          </form>
  
  
		<?php } ?>



		  
      </div>
	  
	  
      </div>
      
  </center>
  </main>
  
 
<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="js/materialize.min.js"></script> 
<script type="text/javascript" src="../js/wow.min.js"></script> 
<script type="text/javascript" src="../js/pace.min.js"></script> 
<script type="text/javascript" src="../js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="../js/mascara.js"></script> 
<script type="text/javascript" src="../js/maiuscula.js"></script> 
<script type="text/javascript" src="../js/semAcentos.js"></script> 
		<script>
			new WOW().init();
		</script> 
		
	  

	  
<script>
		$(document).ready(function(){
		var upperLimit = <?php $diretorio = scandir("../imagens/"); $qtd = count($diretorio) - 2; echo ("$qtd"); ?>;;
		var randomNum = Math.floor((Math.random() * upperLimit) + 1);    
		 //$("body").css("background","url('../imagens/" + randomNum + ".jpg') no-repeat center center fixed");//<--changed path
		 $("body").css("background","url('../imagens/1a.jpg') no-repeat center center fixed");//<--changed path
		 $("body").css("-webkit-background-size","cover");
		 $("body").css("-moz-background-size","cover");
		 $("body").css("-o-background-size","cover");
		 $("body").css("background-size","cover");
		 //$("body").css("-webkit-transition","background 8s ease-in-out");
		 //$("body").css("-moz-transition","background 8s ease-in-out");
		 //$("body").css("-o-transition","background 8s ease-in-out");
		 //$("body").css("-ms-transition","background 8s ease-in-out");
		 //$("body").css("transition","background 8s ease-in-out");
		 $("body").css("-webkit-backface-visibility","hidden");

		});
	  </script>
	  
	  
</body>
</html>