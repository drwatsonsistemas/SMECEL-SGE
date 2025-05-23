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
		header(sprintf("Location: turmaListar.php?permissao"));
		break;
	}
	
	
  $insertSQL = sprintf("INSERT INTO smc_aviso_turma (aviso_turma_id_turma, aviso_turma_id_escola, aviso_turma_data, aviso_turma_hora, aviso_turma_ano, aviso_turma_texto) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['aviso_turma_id_turma'], "int"),
                       GetSQLValueString($_POST['aviso_turma_id_escola'], "int"),
                       GetSQLValueString($_POST['aviso_turma_data'], "date"),
                       GetSQLValueString($_POST['aviso_turma_hora'], "text"),
                       GetSQLValueString($_POST['aviso_turma_ano'], "text"),
                       GetSQLValueString($_POST['aviso_turma_texto'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "aviso_turma.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarTurmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$ListarTurmas = mysql_query($query_ListarTurmas, $SmecelNovo) or die(mysql_error());
$row_ListarTurmas = mysql_fetch_assoc($ListarTurmas);
$totalRows_ListarTurmas = mysql_num_rows($ListarTurmas);

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
 
    <h1 class="ls-title-intro ls-ico-home">CADASTRAR AVISO PARA TURMA</h1>
    <!-- CONTEÚDO -->
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
      
      <label class="ls-label col-md-12">
      <b class="ls-label-text">TURMA</b>
      <div class="ls-custom-select">
        <select name="aviso_turma_id_turma" class="ls-select" required>
          <option value="0">TODAS</option>
          <?php do {  ?>
          <option value="<?php echo $row_ListarTurmas['turma_id']?>" ><?php echo $row_ListarTurmas['turma_nome']?></option>
          <?php } while ($row_ListarTurmas = mysql_fetch_assoc($ListarTurmas)); ?>
        </select>
      </div>
      </label>
      <label class="ls-label col-md-12"> 
      <b class="ls-label-text">MENSAGEM</b>
        <textarea name="aviso_turma_texto" cols="50" rows="5" id="avisoTurma"></textarea>
      </label>
      <div class="ls-actions-btn">
        <input type="submit" class="ls-btn" value="SALVAR">
        <a href="aviso_turma.php" class="ls-btn-danger">CANCELAR</a>
      </div>
      <input type="hidden" name="aviso_turma_data" value="<?php echo date('Y-m-d'); ?>">
      <input type="hidden" name="aviso_turma_hora" value="<?php echo date('H:i'); ?>">
      <input type="hidden" name="aviso_turma_id_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
      <input type="hidden" name="aviso_turma_ano" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    <p>&nbsp;</p>
    <!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
		<script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>
		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="langs/pt_BR.js"></script>

    <script>

	tinymce.init({
	  selector: '#avisoTurma',
	  height: 300,
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

mysql_free_result($ListarTurmas);

mysql_free_result($EscolaLogada);
?>
