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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_sec SET sec_nome=%s, sec_prefeitura=%s, sec_cep=%s, sec_uf=%s, sec_cidade=%s, sec_endereco=%s, sec_num=%s, sec_bairro=%s, sec_telefone1=%s, sec_telefone2=%s, sec_email=%s, sec_nome_secretario=%s, sec_termo_matricula=%s WHERE sec_id=%s",
   GetSQLValueString($_POST['sec_nome'], "text"),
   GetSQLValueString($_POST['sec_prefeitura'], "text"),
   GetSQLValueString($_POST['sec_cep'], "text"),
   GetSQLValueString($_POST['sec_uf'], "text"),
   GetSQLValueString($_POST['sec_cidade'], "text"),
   GetSQLValueString($_POST['sec_endereco'], "text"),
   GetSQLValueString($_POST['sec_num'], "text"),
   GetSQLValueString($_POST['sec_bairro'], "text"),
   GetSQLValueString($_POST['sec_telefone1'], "text"),
   GetSQLValueString($_POST['sec_telefone2'], "text"),
   GetSQLValueString($_POST['sec_email'], "text"),
   GetSQLValueString($_POST['sec_nome_secretario'], "text"),
   GetSQLValueString($_POST['sec_termo_matricula'], "text"),
   GetSQLValueString($_POST['sec_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "dados.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo, sec_termo_matricula FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);




if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	

	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: secretaria.php?permissao"));
		break;
	}
	
//CADASTRO DA LOGO
  include('../funcoes/class.upload.php');

  $handle = new Upload($_FILES['logo']);

  if ($handle->uploaded) 
  { 

    $handle->file_new_name_body 	 = $novo_nome;
    $handle->mime_check 			 = true;
    $handle->image_resize            = true;
    $handle->image_ratio	         = false;
    $handle->image_x                 = 200;
    $handle->image_y                 = 200;
    $handle->Process('../../img/logo/secretaria/');

// Miniatura
    $handle->mime_check 			 = true;
    $handle->image_resize            = true;
    $handle->image_ratio           	 = false;
    $handle->image_x                 = 400;
    $handle->image_y       			 = 400;
    $handle->image_overlay_color   = '#FFFFFF';
    $handle->image_overlay_opacity = 70;
		//$handle->jpeg_quality            = 20;
    $handle->file_new_name_body		 = $novo_nome;
    $handle->Process('../../img/marcadagua/secretaria/');

    if ($handle->processed) 
    {

      $nome_do_arquivo = $handle->file_dst_name;

      $insertSQL = "UPDATE smc_sec SET sec_logo='$nome_do_arquivo' WHERE sec_id='$row_Secretaria[sec_id]'";

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

      $insertGoTo = "dados.php?foto";
      if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $insertGoTo));
    } 
    else 
    {
      echo '<span class="alert panel">';
      echo ' Erro ao enviar arquivo: ' . $handle->error . '';
      echo '</span>';
    }
  }

}

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
  <script src="js/locastyle.js"></script>  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">EDITAR DADOS</h1>

      <?php if (isset($_GET["editado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Dados salvos com sucesso! </div>
      <?php } ?>

      <div class="ls-box ls-board-box">

       <div class="col-sm-6">

        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
          <fieldset>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Nome da Secretaria</b>
              <p class="ls-label-info">Informe o nome ou SIGLA da Secretaria de Educação</p>
              <input type="text" name="sec_nome" value="<?php echo htmlentities($row_Secretaria['sec_nome'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Nome da Prefeitura</b>
              <p class="ls-label-info">Digite seu nome da prefeitura</p>
              <input type="text" name="sec_prefeitura" value="<?php echo htmlentities($row_Secretaria['sec_prefeitura'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">CEP</b>
              <p class="ls-label-info">CEP</p>
              <input type="text" name="sec_cep" value="<?php echo htmlentities($row_Secretaria['sec_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">UF</b>
              <p class="ls-label-info">UF</p>
              <input type="text" name="sec_uf" value="<?php echo htmlentities($row_Secretaria['sec_uf'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Cidade</b>
              <p class="ls-label-info">Cidade</p>
              <input type="text" name="sec_cidade" value="<?php echo htmlentities($row_Secretaria['sec_cidade'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Endereço</b>
              <p class="ls-label-info">Endereço completo</p>
              <input type="text" name="sec_endereco" value="<?php echo htmlentities($row_Secretaria['sec_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Número</b>
              <p class="ls-label-info">Número</p>
              <input type="text" name="sec_num" value="<?php echo htmlentities($row_Secretaria['sec_num'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Bairro</b>
              <p class="ls-label-info">Bairro</p>
              <input type="text" name="sec_bairro" value="<?php echo htmlentities($row_Secretaria['sec_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Telefone</b>
              <p class="ls-label-info">Informe um número de telefone</p>
              <input type="text" name="sec_telefone1" value="<?php echo htmlentities($row_Secretaria['sec_telefone1'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Telefone</b>
              <p class="ls-label-info">Informe um número de telefone</p>
              <input type="text" name="sec_telefone2" value="<?php echo htmlentities($row_Secretaria['sec_telefone2'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">E-mail</b>
              <p class="ls-label-info">Informe o e-mail de contato da secretaria</p>
              <input type="text" name="sec_email" value="<?php echo htmlentities($row_Secretaria['sec_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">DME</b>
              <p class="ls-label-info">Informe o nome do Dirigente Municipal de Educação</p>
              <input type="text" name="sec_nome_secretario" value="<?php echo htmlentities($row_Secretaria['sec_nome_secretario'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>

            <label class="ls-label col-md-12">
              <b class="ls-label-text">TEXTO DO TERMO</b>
              <p class="ls-label-info">Informe o texto do Termo de Comprisso que aparecerá no formulário de matrícula</p>
              <textarea id="summernote" name="sec_termo_matricula" class="ls-textarea-autoresize"  rows="10"><?php echo htmlentities($row_Secretaria['sec_termo_matricula'], ENT_COMPAT, 'utf-8'); ?></textarea>
            </label>


            <div class="ls-actions-btn">
              <input type="submit" value="SALVAR DADOS" class="ls-btn-primary">
              <a href="index.php" class="ls-btn-danger">VOLTAR</a>
            </div>

            <input type="hidden" name="MM_update" value="form1">
            <input type="hidden" name="sec_id" value="<?php echo $row_Secretaria['sec_id']; ?>">

          </fieldset>
        </form>

      </div>

      <div class="col-sm-6">

       <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
         <p>Brasão da Secretaria de Educação<br>
          <img src="../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" /></p>
          <p>Marca D'água da Secretaria de Educação<br>
            <img src="../../img/marcadagua/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" /></p>
          <?php } else { ?>

           <b>Nenhum brasão cadastrado.</b><br><br>
           Os relatórios serão gerados com o Brasão Oficial da República.<br>
           <img src="../../img/brasao_republica.png" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" width="100px" />


         <?php } ?>
         <hr>

         <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">Cadastrar/Alterar Brasão da Secretaria</button>

       </div>

       <p>&nbsp;</p>





     </div>
   </div>
 </main>



 <div class="ls-modal" id="myAwesomeModal">
  <form method="post" enctype="multipart/form-data" name="form2" action="<?php echo $editFormAction; ?>" autocomplete="off">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">ESCOLHA O BRASÃO DA SECRETARIA</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <p>
          <div class="ls-alert-info"><strong>Atenção:</strong> <br>
            1 - Envie uma imagem com a mesma proporção de altura e largura. <br>
          2 - A imagem enviada também irá gerar a imagem de marca d'água da Secretaria de Educação.</div>
          <label class="ls-label col-md-12"> <b class="ls-label-text">IMAGEM</b>
            <input type="file" name="logo" value="" required>
          </label>
          <input type="hidden" name="MM_insert2" value="form2">
          <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
        </p>
      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
        <input type="submit" value="CADASTRAR IMAGEM" class="ls-btn-primary">
      </div>
    </div>
  </form>
</div>
<!-- /.modal --> 


<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
 $('#summernote').summernote({
  placeholder: 'Digite aqui...',
  tabsize: 2,
  height: 200,
  toolbar: [
    ['style', ['style']],
    ['font', ['bold', 'underline', 'clear']],
    ['color', ['color']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['table', ['table']],
    ['insert', []],
    ['view', []]
    ]
});
</script>
<script src="langs/pt_BR.js"></script>




</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>