<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../../funcoes/url_base.php'); ?>

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
	
  $logoutGoTo = "../../index.php?saiu=true";
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
$MM_authorizedUsers = "2,99";
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

$MM_restrictGoTo = "../../index.php?err=true";
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

$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_logo FROM smc_escola WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_aluno SET aluno_hash=%s, aluno_foto=%s WHERE aluno_id=%s",
                       GetSQLValueString($_POST['aluno_hash'], "text"),
                       GetSQLValueString($_POST['aluno_foto'], "text"),
                       GetSQLValueString($_POST['aluno_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "../alunoEditar.php?fotoEditada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#foto";
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_AlunoFoto = "-1";
if (isset($_GET['hash'])) {
  $colname_AlunoFoto = $_GET['hash'];
}

$colname_Cmatricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_Cmatricula = $_GET['cmatricula'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoFoto = sprintf("SELECT aluno_id, aluno_nome, aluno_nascimento, aluno_hash, aluno_foto FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($colname_AlunoFoto, "text"));
$AlunoFoto = mysql_query($query_AlunoFoto, $SmecelNovo) or die(mysql_error());
$row_AlunoFoto = mysql_fetch_assoc($AlunoFoto);
$totalRows_AlunoFoto = mysql_num_rows($AlunoFoto);

if ($totalRows_AlunoFoto == "") {
	echo "TURMA EM BRANCO";	
	header("Location: ../turmasAlunosVinculados.php?nada"); 
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

        <title>Cadastro de foto do aluno -SMECEL</title>
        <meta charset="utf-8">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <meta name="description" content="Sistema de Gestão Escolar.">
        <link href="https://assets.locaweb.com.br/locastyle/3.8.4/stylesheets/locastyle.css" rel="stylesheet" type="text/css">
        <link rel="icon" sizes="192x192" href="../img/icone.png">
        <link rel="apple-touch-icon" href="../img/icone.png">
        <script type="text/javascript" src="webcam.js"></script>
        <script type="text/javascript">
		
		
		
		
            //Configurando o arquivo que vai receber a imagem
            webcam.set_api_url('upload.php?nomeFoto=<?php echo $row_AlunoFoto['aluno_id']; ?>');

            //Setando a qualidade da imagem (1 - 100)
            webcam.set_quality(90);

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
				
				    var htmlResult = '<img src="<?php echo URL_BASE.'aluno/fotos/' ?>'+msg+'" />';
                    htmlResult += '<p class="ls-txt-center">Pré-visualização da imagem</p>';
                    //htmlResult += msg;
                    document.getElementById('upload_results').innerHTML = htmlResult;
					//$('#upload_nome').append(msg);
					document.getElementById('nome_aluno').value = msg;
                    webcam.reset();

				
                /*
				if (msg.match(/(http\:\/\/\S+)/)) {
                    var htmlResult = '<img src="'+msg+'" />';
                    htmlResult += '<p class="ls-txt-center">Pré-visualização da imagem</p>';
                    //htmlResult += msg;
                    document.getElementById('upload_results').innerHTML = htmlResult;
					//$('#upload_nome').append(msg);
					document.getElementById('nome_aluno').value = msg;
                    webcam.reset();
                }
                else {
                    alert("PHP Erro: " + msg);
                }
				*/
				
				
            }
			
        </script>
        <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
        <body>
        <div class="ls-modal" data-modal-blocked id="modalFoto">
          <div class="ls-modal-box">
            <div class="ls-modal-body" id="myModalBody">
              <p><strong>Aluno(a): <?php echo $row_AlunoFoto['aluno_nome']; ?></strong>
			  <br><small>*Utilize uma webcam e o navegador Mozilla Firefox na versão mais atual com o Flash habilitado</small>
			  </p>
			  

              <div class="row">
                <div class="col-lg-6 ls-box ls-xs-space ls-txt-center"> 
                  <div style="height:405px; width:270px; background-color:#d3d3d3;">
				  <script type="text/javascript">
						//Instanciando a webcam. O tamanho pode ser alterado
						document.write(webcam.get_html(270, 360));
					</script>
                  <p>
				  
                    <div class="ls-group-btn">
                      <button type="button" class="ls-btn ls-btn-xs" onClick="take_snapshot()">Tirar Foto</button>
                      <button type="button" class="ls-btn ls-ico-cog ls-btn-xs" value="" onClick="webcam.configure()"></button>
                      <button type="button" class="ls-btn ls-ico-remove ls-btn-xs" onClick="webcam.reset()"></button>
                    </div>
                  
				  </p>
				  </div>
                </div>
                <div class="col-lg-6 ls-box ls-xs-space ls-txt-center">
                  <div id="upload_results" style="height:405px; width:270px; background-color:#d3d3d3;"></div>
                </div>
				
                <div class="col-lg-12 ls-txt-right">
				<p>
                  <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                    <input type="submit" value="SALVAR FOTO" class="ls-btn-primary ls-btn-xs">
					<a class="ls-btn ls-btn-xs" href="../alunoEditar.php?hash=<?php echo $row_AlunoFoto['aluno_hash']; ?>&cmatricula=<?php echo $colname_Cmatricula; ?>#foto">CANCELAR</a>
                    <input type="hidden" name="aluno_hash" value="<?php echo htmlentities($row_AlunoFoto['aluno_hash'], ENT_COMPAT, 'UTF-8'); ?>">
                    <input type="hidden" name="MM_update" value="form1">
                    <input type="hidden" name="aluno_id" value="<?php echo $row_AlunoFoto['aluno_id']; ?>">
                    <input type="hidden" id="nome_aluno" name="aluno_foto" value="<?php echo htmlentities($row_AlunoFoto['aluno_foto'], ENT_COMPAT, 'UTF-8'); ?>" size="32">
                   
				  </form>
				  </p>
				 
                  
                </div>
              </div>

            </div>
			
          </div>
        </div>
        <!-- /.modal -->

        <p>&nbsp;</p>
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
        <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
<script type="text/javascript" src="js/preloader.js"></script> 
        <script>
	locastyle.modal.open("#modalFoto");
	</script>
</body>
</html>
<?php
mysql_free_result($AlunoFoto);
?>
