<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
	
  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,99";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $time = time();
  $error_message = "";
  $success_message = "";

  // Processar o upload do arquivo
  $upload_dir = realpath(dirname(__FILE__) . "/../..") . "/chamados_anexo/"; // Caminho ajustado para a raiz do projeto
  $file_names = [];

  if (isset($_FILES['chamado_imagem']) && !empty($_FILES['chamado_imagem']['name'][0])) {
      $file_count = count($_FILES['chamado_imagem']['name']);
      if ($file_count > 8) {
          $error_message = "Você pode enviar no máximo 8 arquivos.";
      } else {
          for ($i = 0; $i < $file_count; $i++) {
              if ($_FILES['chamado_imagem']['error'][$i] == 0) {
                  $file_tmp = $_FILES['chamado_imagem']['tmp_name'][$i];
                  $file_ext = strtolower(pathinfo($_FILES['chamado_imagem']['name'][$i], PATHINFO_EXTENSION));
                  $file_name = $time . "_" . rand(1000, 9999) . "_$i." . $file_ext;
                  $file_destination = $upload_dir . $file_name;

                  if (!file_exists($upload_dir)) {
                      if (!mkdir($upload_dir, 0777, true)) {
                          $error_message = "Falha ao criar o diretório de upload.";
                          break;
                      }
                  }

                  if (!is_writable($upload_dir)) {
                      $error_message = "Diretório de upload não é gravável.";
                      break;
                  }

                  if (move_uploaded_file($file_tmp, $file_destination)) {
                      $file_names[] = $file_name;
                  } else {
                      $error_message = "Falha ao mover o arquivo: " . $_FILES['chamado_imagem']['name'][$i];
                      break;
                  }
              } else {
                  $error_message = "Erro no upload do arquivo: " . $_FILES['chamado_imagem']['name'][$i];
                  break;
              }
          }
      }
  }

  if (empty($error_message)) {
      $file_names_json = json_encode($file_names);
      $insertSQL = sprintf(
          "INSERT INTO smc_chamados (chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_visualizado, chamado_numero, chamado_imagem) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$time', %s)",
          GetSQLValueString($_POST['chamado_id_sec'], "int"),
          GetSQLValueString($_POST['chamado_id_escola'], "int"),
          GetSQLValueString($_POST['chamado_id_usuario'], "int"),
          GetSQLValueString($_POST['chamado_id_telefone'], "text"),
          GetSQLValueString($_POST['chamado_data_abertura'], "date"),
          GetSQLValueString($_POST['chamado_categoria'], "text"),
          GetSQLValueString($_POST['chamado_situacao'], "text"),
          GetSQLValueString($_POST['chamado_titulo'], "text"),
          GetSQLValueString($_POST['chamado_texto'], "text"),
          GetSQLValueString($_POST['chamado_visualizado'], "text"),
          GetSQLValueString($file_names_json, "text")
      );

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

      $insertGoTo = "chamados.php?cadastrado";
      if (isset($_SERVER['QUERY_STRING'])) {
          $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
          $insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $insertGoTo));
      exit;
  } else {
      // Exibir mensagem de erro, se houver
      echo '<div class="ls-alert-danger">' . $error_message . '</div>';
  }
}


require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);
?>

<!DOCTYPE html>
<html class="ls-theme-green">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css">
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ABRIR CHAMADO</h1>
    <!-- CONTEUDO -->
    
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal" enctype="multipart/form-data">
      
      <label class="ls-label col-md-6" required> <b class="ls-label-text">SOLICITANTE</b>
        <input type="text" name="" value="<?php echo $row_UsuarioLogado['usu_nome']; ?>" size="32" disabled>
      </label>
      
      <label class="ls-label col-md-6" required> <b class="ls-label-text">E-MAIL</b>
        <input type="text" name="" value="<?php echo $row_UsuarioLogado['usu_email']; ?>" size="32" disabled>
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

      <label
          class="ls-label col-md-12">
          <b class="ls-label-text">ANEXOS (MÁXIMO 8 ARQUIVOS - IMAGEM, PDF, ETC)</b>
          <input type="file" name="chamado_imagem[]" accept="image/*,.pdf" multiple />
          <small class="ls-help-message">Você pode enviar até 8 arquivos.</small>
        </label>
      
      <label class="ls-label col-md-12">
      <div class="ls-actions-btn">
        <input class="ls-btn" type="submit" value="REGISTRAR CHAMADO">
        <a href="chamados.php" class="ls-btn-danger">VOLTAR</a>
      </div>
      </label>
      
      <input type="hidden" name="chamado_id_sec" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
      <input type="hidden" name="chamado_id_escola" value="">
      <input type="hidden" name="chamado_id_usuario" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
      <input type="hidden" name="chamado_data_abertura" value="<?php echo date('Y-m-d'); ?>">
      <input type="hidden" name="chamado_situacao" value="A">
      <input type="hidden" name="chamado_visualizado" value="N">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    <p>&nbsp;</p>
    
    
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="../../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
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
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>