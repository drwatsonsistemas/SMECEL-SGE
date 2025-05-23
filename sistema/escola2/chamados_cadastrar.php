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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
			
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: chamados.php?permissao"));
		break;
	}
	
	//$time = date('YmdHis');
	$time = time();
	
  $insertSQL = sprintf("INSERT INTO smc_chamados (chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_visualizado, chamado_numero) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$time')",
                       GetSQLValueString($_POST['chamado_id_sec'], "int"),
                       GetSQLValueString($_POST['chamado_id_escola'], "int"),
                       GetSQLValueString($_POST['chamado_id_usuario'], "int"),
                       GetSQLValueString($_POST['chamado_id_telefone'], "text"),
                       GetSQLValueString($_POST['chamado_data_abertura'], "date"),
                       GetSQLValueString($_POST['chamado_categoria'], "text"),
                       GetSQLValueString($_POST['chamado_situacao'], "text"),
                       GetSQLValueString($_POST['chamado_titulo'], "text"),
                       GetSQLValueString($_POST['chamado_texto'], "text"),
                       GetSQLValueString($_POST['chamado_visualizado'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "chamados.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
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
     
    <h1 class="ls-title-intro ls-ico-home">ABRIR CHAMADO</h1>
    <!-- CONTEÚDO -->
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
      
      <label class="ls-label col-md-6" required> <b class="ls-label-text">SOLICITANTE</b>
        <input type="text" name="" value="<?php echo $row_UsuLogado['usu_nome']; ?>" size="32" disabled>
      </label>
      
      <label class="ls-label col-md-6" required> <b class="ls-label-text">E-MAIL</b>
        <input type="text" name="" value="<?php echo $row_UsuLogado['usu_email']; ?>" size="32" disabled>
      </label>
      
      <label class="ls-label col-md-6" required> <b class="ls-label-text">TELEFONE/CELULAR</b>
        <input type="text" name="chamado_id_telefone" value="" size="32" class="celular">
      </label>
      
      <label class="ls-label col-md-6"> <b class="ls-label-text">TIPO DE CHAMADO</b>
      <div class="ls-custom-select">
        <select name="chamado_categoria" class="ls-select" required>
          <option value="">-</option>
          <option value="DÚVIDA" <?php if (!(strcmp("DÚVIDA", ""))) {echo "SELECTED";} ?>>DÚVIDA</option>
          <option value="SUPORTE" <?php if (!(strcmp("SUPORTE", ""))) {echo "SELECTED";} ?>>SUPORTE</option>
          <option value="SUGESTÃO" <?php if (!(strcmp("SUGESTÃO", ""))) {echo "SELECTED";} ?>>SUGESTÃO</option>
          <option value="ELOGIO" <?php if (!(strcmp("ELOGIO", ""))) {echo "SELECTED";} ?>>ELOGIO</option>
          <option value="CRÍTICA" <?php if (!(strcmp("CRÍTICA", ""))) {echo "SELECTED";} ?>>CRÍTICA</option>
          <option value="OUTROS" <?php if (!(strcmp("OUTROS", ""))) {echo "SELECTED";} ?>>OUTROS</option>
        </select>
      </div>
      </label>
      
      <label class="ls-label col-md-12"> <b class="ls-label-text">TÍTULO/ASSUNTO</b>
        <input type="text" name="chamado_titulo" value="" size="32" required>
      </label>
      
      <label class="ls-label col-md-12"> <b class="ls-label-text">TEXTO DETALHADO</b>
        <textarea name="chamado_texto" id="mytextarea" cols="50" rows="10" required>
        
        </textarea>
      </label>
      
      <label class="ls-label col-md-12">
      <div class="ls-actions-btn">
        <input class="ls-btn" type="submit" value="REGISTRAR CHAMADO">
        <a href="chamados.php" class="ls-btn-danger">VOLTAR</a>
      </div>
      </label>
      
      <input type="hidden" name="chamado_id_sec" value="<?php echo $row_UsuLogado['usu_sec']; ?>">
      <input type="hidden" name="chamado_id_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
      <input type="hidden" name="chamado_id_usuario" value="<?php echo $row_UsuLogado['usu_id']; ?>">
      <input type="hidden" name="chamado_data_abertura" value="<?php echo date('Y-m-d'); ?>">
      <input type="hidden" name="chamado_situacao" value="A">
      <input type="hidden" name="chamado_visualizado" value="N">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    <p>&nbsp;</p>
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
      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="../../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
		<script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>
		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="langs/pt_BR.js"></script>


    <script>

	tinymce.init({
	  selector: '#mytextarea',
	  height: 400,
	  toolbar: 'bold italic | bullist numlist | image | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
	  plugins : 'advlist autolink link autolink image imagetools lists charmap print preview paste',
	  statusbar: false,
	  menubar: false,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
	});

</script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
