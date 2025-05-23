<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$colname_turma = "-1";
if (isset($_GET['turma'])) {
  $colname_turma = $_GET['turma'];
}

$colname_Verifica = "-1";
if (isset($_GET['cb'])) {
  $colname_Verifica = $_GET['cb'];
}

$colname_DisciplinaExcluir = "-1";
if (isset($_GET['cb'])) {
  $colname_DisciplinaExcluir = $_GET['cb'];
}

$c = "-1";
if (isset($_GET['c'])) {
  $c = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Verifica = sprintf("
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina,
vinculo_aluno_id, vinculo_aluno_id_escola
FROM smc_boletim_disciplinas 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno
WHERE 
vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND boletim_id = %s", GetSQLValueString($colname_Verifica, "int"));
$Verifica = mysql_query($query_Verifica, $SmecelNovo) or die(mysql_error());
$row_Verifica = mysql_fetch_assoc($Verifica);
$totalRows_Verifica = mysql_num_rows($Verifica);

if ($totalRows_Verifica == "") {
	
	header("Location:boletimVer.php?c=$c&erro");
	
} else {
	
if ((isset($_GET['cb'])) && ($_GET['cb'] != "")) {	
	
	$deleteSQL = sprintf("DELETE FROM smc_boletim_disciplinas WHERE boletim_id=%s",
                       GetSQLValueString($_GET['cb'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
  
  
  
  // ** REGISTRO DE LOG DE USUÁRIO **
	$usu = $row_UsuLogado['usu_id'];
	$esc = $row_UsuLogado['usu_escola'];
	$detalhes = $_POST['detalhes'];
	date_default_timezone_set('America/Bahia');
	$dat = date('Y-m-d H:i:s');

	$sql = "
	INSERT INTO smc_registros (
	registros_id_escola, 
	registros_id_usuario, 
	registros_tipo, 
	registros_complemento, 
	registros_data_hora
	) VALUES (
	'$esc', 
	'$usu', 
	'15', 
	'$colname_turma', 
	'$dat')
	";
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **

  
  

  $deleteGoTo = "boletimVerAntigo.php?excluido";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
	
}







/*
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_DisciplinaExcluir = sprintf("
SELECT 
boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_escola 
FROM 
smc_boletim_disciplinas
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno
WHERE vinculo_aluno_id_escola = $row_UsuLogado[usu_escola] AND boletim_id = %s", GetSQLValueString($colname_DisciplinaExcluir, "int"));
$DisciplinaExcluir = mysql_query($query_DisciplinaExcluir, $SmecelNovo) or die(mysql_error());
$row_DisciplinaExcluir = mysql_fetch_assoc($DisciplinaExcluir);
$totalRows_DisciplinaExcluir = mysql_num_rows($DisciplinaExcluir);


if ((isset($_GET['cb'])) && ($_GET['cb'] != "")) {

		
  $deleteSQL = sprintf("DELETE FROM smc_boletim_disciplinas WHERE boletim_id=%s",
                       GetSQLValueString($_GET['cb'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "boletimVer.php?excluido";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

*/

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
    <meta name="description" content="Sistema de Gestão Escolar.">
    <link href="https://assets.locaweb.com.br/locastyle/3.8.4/stylesheets/locastyle.css1" rel="stylesheet" type="text/css">
    <link rel="icon" sizes="192x192" href="img/icone.png">
    <link rel="apple-touch-icon" href="img/icone.png">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>	
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">MODELO</h1>
		<!-- CONTEÚDO -->
		
		

		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Verifica);

mysql_free_result($DisciplinaExcluir);
?>
