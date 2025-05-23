<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../funcoes/anoLetivo.php"; ?>
<?php include "../funcoes/inverteData.php"; ?>
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
	
  $logoutGoTo = "../../index.php";
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
$MM_authorizedUsers = "5,99";
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

$MM_restrictGoTo = "../../index.php?err";
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

$colname_Logado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Logado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_Logado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

include "../funcoes/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_ue = '1' AND escola_situacao = '1' ORDER BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$data = date('Y-m-d');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ultimas = "
SELECT 
catraca_id, catraca_id_matricula, catraca_data, catraca_hora, catraca_tipo,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_situacao, vinculo_aluno_ano_letivo,
aluno_id, aluno_nome, aluno_foto, turma_id, turma_nome
FROM smc_catraca
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = catraca_id_matricula
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE catraca_data = '$data' AND catraca_tipo = 'E'
ORDER BY catraca_id DESC
LIMIT 0,6";
$Ultimas = mysql_query($query_ultimas, $SmecelNovo) or die(mysql_error());
$row_Ultimas = mysql_fetch_assoc($Ultimas);
$totalRows_Ultimas = mysql_num_rows($Ultimas);


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
<body onload="javascript:foco();">
<nav class="blue darken-6" role="navigation">
  <div class="nav-wrapper container"> <a id="logo-container" href="index.php" class="brand-logo">SMECEL</a>
    <ul class="right hide-on-med-and-down">
      <li><?php echo $row_Logado['usu_nome']; ?></li>
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="dados.php">MEUS DADOS</a></li>
      <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">exit_to_app</i>SAIR</a></li>
    </ul>
    <ul id="nav-mobile" class="sidenav">
      <li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">exit_to_app</i>SAIR</a></li>
    </ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a> </div>
</nav>
<div class="container"> <br>
<a href="index.php" class="waves-effect waves-light btn-flat"><i class="material-icons left">arrow_back</i> VOLTAR</a>
<br>
  <div class="row valign-wrapper center-align card-panel green lighten-4">
    <div class="col s12 m3">
      <h2>ENTRADA</h2>
    </div>
    <div class="col s12 m9">
      <form method="post" name="form1" id="cadastra_entrada" action="">
        <div class="row1">
          
            <label>MATRÍCULA DO ALUNO
              <input type="text" name="registro_id_aluno" id="registro_id_aluno" autocomplete="off" class="center-align" style="font-size:6em; height:100px;">
              <input type="hidden" name="usuario" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
              <input type="hidden" name="secretaria" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
              <input type="hidden" name="escola" value="<?php echo $row_UsuarioLogado['usu_escola']; ?>">
              <input type="hidden" name="ano_letivo" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>">
              <input type="hidden" name="tipo" value="E">
            </label>
            <input type="submit" class="btn" value="Registrar Entrada">

          
        </div>
      </form>
    </div>
  </div>
  <div id="entradaResultado"></div>
  
  

  
  
  <!-- FIM CONTAINER --> 
</div>
<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="js/materialize.min.js"></script> 
<script type="text/javascript" src="../js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="../js/mascara.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$(".dropdown-trigger").dropdown();
		});
	</script> 
<script>


            jQuery('#cadastra_entrada').submit(function () {
                event.preventDefault();
                var dados = jQuery(this).serialize();

                jQuery.ajax({
                    type: "POST",
                    url: "inc/cadastraEntrada.php",
                    data: dados,
                    success: function (data)
                    {
                        $("input").prop('disabled', true);
                        $("select").prop('disabled', true);
                        $("textarea").prop('disabled', true);

                        $("#entradaResultado").html(data);

                        setTimeout(function () {
                            $("#cadastra_entrada").each(function () {
                                this.reset();
                            });

                            $("input").prop('disabled', false);
                            $("select").prop('disabled', false);
                            $("textarea").prop('disabled', false);
                            //$("#entradaResultado").html("");
                            $("#registro_id_aluno").html("");
                            $("#registro_id_aluno").focus();
                        }, 50);
                    }
                });

                return false;
            });

        </script> 
<script language="javascript">

function foco()

{

document.getElementById('registro_id_aluno').focus();

}

</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);
mysql_free_result($Escolas);
?>
