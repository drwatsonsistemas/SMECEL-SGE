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
  $updateSQL = sprintf("UPDATE smc_curso_formacao_topicos SET curso_form_topic_titulo=%s, curso_form_topic_texto=%s, curso_form_topic_aberto=%s WHERE curso_form_topic_id=%s",
                       GetSQLValueString($_POST['curso_form_topic_titulo'], "text"),
                       GetSQLValueString($_POST['curso_form_topic_texto'], "text"),
                       GetSQLValueString($_POST['curso_form_topic_aberto'], "text"),
                       GetSQLValueString($_POST['curso_form_topic_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "formacao_topico_editar.php?editado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Formacao = "-1";
if (isset($_GET['formacao'])) {
  $colname_Formacao = $_GET['formacao'];
}

$colname_Topico = "-1";
if (isset($_GET['topico'])) {
  $colname_Topico = $_GET['topico'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Topico = sprintf("SELECT curso_form_topic_id, curso_form_topic_id_form, curso_form_topic_titulo, curso_form_topic_texto, curso_form_topic_aberto, curso_form_data FROM smc_curso_formacao_topicos WHERE curso_form_topic_id = %s", GetSQLValueString($colname_Topico, "int"));
$Topico = mysql_query($query_Topico, $SmecelNovo) or die(mysql_error());
$row_Topico = mysql_fetch_assoc($Topico);
$totalRows_Topico = mysql_num_rows($Topico);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "SELECT curso_form_anexo_id, curso_form_anexo_id_topico, curso_form_anexo_descricao, curso_form_anexo_arquivo FROM smc_curso_formacao_anexo WHERE curso_form_anexo_id_topico = '$colname_Topico'";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT curso_form_com_id, curso_form_com_id_func, curso_form_com_id_topico, curso_form_com_texto, curso_form_com_data,
func_id, func_nome, func_foto 
FROM smc_curso_formacao_comentarios
INNER JOIN smc_func
ON func_id = curso_form_com_id_func
WHERE curso_form_com_id_topico = '$row_Topico[curso_form_topic_id]'
ORDER BY curso_form_com_id ASC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);



if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	
	/*	
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: secretaria.php?permissao"));
		break;
	}
	*/
	
//CADASTRO DA LOGO
include('../funcoes/class.upload.php');

$handle = new Upload($_FILES['logo']);

if ($handle->uploaded) 
{ 

$nome = uniqid();
$handle->file_name_body_pre = $nome.'_';
$handle->mime_check 			 = true;



$handle->Process('../../anexos/formacao/professores/'.$colname_Formacao.'/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

$descricao = $_POST['descricao'];

  //$insertSQL = "UPDATE smc_sec SET sec_logo='$nome_do_arquivo' WHERE sec_id='$row_Secretaria[sec_id]'";
  $insertSQL = "INSERT INTO smc_curso_formacao_anexo (curso_form_anexo_id_topico, curso_form_anexo_descricao, curso_form_anexo_arquivo) VALUES ('$colname_Topico', '$descricao', '$nome_do_arquivo')";

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "formacao_topico_editar.php";
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
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>

.prof {
	background-color: #ddd;
	border-radius: 100%;
	height: 80px;
	object-fit: cover;
	width: 80px;
}
</style>
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-chart-bar-up">TÓPICOS</h1>
    <!-- CONTEUDO -->
  
  
  <a href="formacao_topicos.php?formacao=<?php echo $colname_Formacao; ?>" class="ls-btn-primary">Voltar</a>  
     
 <br><br>
 
 
<div class="ls-box">
  <h5 class="ls-title-3"><?php echo $row_Topico['curso_form_topic_titulo']; ?></h5>
  <p><?php echo $row_Topico['curso_form_topic_texto']; ?></p>
  
  <?php if ($row_Topico['curso_form_topic_aberto']=="N") { ?>
  
  <div class="ls-alert-info"><strong>Atenção:</strong> Este tópico está fechado.</div>
  
  <?php } ?>
<button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn ls-btn-xs">Editar Tópico</button>   
</div>
 
<div class="ls-box">
<h5 class="ls-title-3">Anexos</h5>
 <?php if ($totalRows_Anexos > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
  <thead>
    <tr>
      <th width="500">NOME</th>
      <th>ARQUIVOS</th>
      <th  class="ls-txt-center" width="100">BAIXAR</th>
    </tr>
	</thead>
    <tbody>
    <?php do { ?>
      <tr>
        <td><?php echo $row_Anexos['curso_form_anexo_descricao']; ?></td>
        <td><?php echo $row_Anexos['curso_form_anexo_arquivo']; ?></td>
        <td class="ls-txt-center"><a target="_blank" href="../../anexos/formacao/professores/<?php echo $colname_Formacao; ?>/<?php echo $row_Anexos['curso_form_anexo_arquivo']; ?>"><span class="ls-ico-download ls-ico-right"></span></a></td>
      </tr>
      <?php } while ($row_Anexos = mysql_fetch_assoc($Anexos)); ?>
  </table>
  </tbody> 
   <?php } else { ?>
 	<p>Nenhum anexo enviado.</p>
  <?php } // Show if recordset not empty ?>
    <button data-ls-module="modal" data-target="#arquivoAnexo" class="ls-btn-primary ls-btn-xs">INSERIR ANEXO <span class="ls-ico-attachment ls-ico-right"></span> </button>    
  </div>
  
  <div class="ls-box">
  <h5 class="ls-title-3">Comentários</h5>
  <?php if ($totalRows_Comentarios > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <?php do { ?>
      <tr>
        <td width="120">
          
          <?php if ($row_Comentarios['func_foto']=="") { ?>
          <img src="<?php echo '../../professor/fotos/' ?>semfoto.jpg" width="100%" class="prof">
          <?php } else { ?>
          <img src="<?php echo '../../professor/fotos/' ?><?php echo $row_Comentarios['func_foto']; ?>" width="100%" class="prof">
          <?php } ?>
          
        </td>
        <td><strong><?php echo $row_Comentarios['func_nome']; ?></strong>
          <p>
            <?php echo $row_Comentarios['curso_form_com_texto']; ?>
            <small><?php echo date("d/m/y - H:i", strtotime($row_Comentarios['curso_form_com_data'])); ?></small>
          </p>
        </td>
      </tr>
      <?php } while ($row_Comentarios = mysql_fetch_assoc($Comentarios)); ?>
  </table>
  <?php } else { ?>
  <p>Nenhum comentário.</p>
  <?php } // Show if recordset not empty ?>
  </div>
  
  
<div class="ls-modal" id="arquivoAnexo">
          <form method="post" enctype="multipart/form-data" name="form2" action="<?php echo $editFormAction; ?>" autocomplete="off">
            <div class="ls-modal-box">
              <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">ENVIE UM ARQUIVO EM ANEXO</h4>
              </div>
              <div class="ls-modal-body" id="myModalBody">
                <p>
                <label class="ls-label col-md-12"> <b class="ls-label-text">ARQUIVO</b>
                  <input type="file" name="logo" value="" required>
                </label>
                <label class="ls-label col-md-12"> <b class="ls-label-text">DESCRIÇÃO</b>
                  <input type="text" name="descricao" value="" required>
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
    
    
    <p>&nbsp;</p>
<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EDITAR TÓPICO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      

       <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      
      <fieldset>
      

        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Tíulo</b>
         <input type="text" name="curso_form_topic_titulo" value="<?php echo htmlentities($row_Topico['curso_form_topic_titulo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
        </label>
                
        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Descrição</b>
         <textarea id="formacao_textarea" name="curso_form_topic_texto" cols="50" rows="5"><?php echo htmlentities($row_Topico['curso_form_topic_texto'], ENT_COMPAT, 'utf-8'); ?></textarea>
        </label>
        
  </fieldset>      
  
    <div class="ls-label col-md-12">
      <p>Status da formação:</p>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_topic_aberto" value="S" <?php if (!(strcmp(htmlentities($row_Topico['curso_form_topic_aberto'], ENT_COMPAT, 'utf-8'),"S"))) {echo "checked=\"checked\"";} ?>>
        Aberto
      </label>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_topic_aberto" value="N" <?php if (!(strcmp(htmlentities($row_Topico['curso_form_topic_aberto'], ENT_COMPAT, 'utf-8'),"N"))) {echo "checked=\"checked\"";} ?>>
        Fechado
      </label>
    </div>    
           
      
      
        <input type="hidden" name="curso_form_topic_id" value="<?php echo $row_Topico['curso_form_topic_id']; ?>">
      <input type="hidden" name="MM_update" value="form1">
            
      </p>
      
    </div>
    <div class="ls-modal-footer">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <button type="submit" class="ls-btn-primary">SALVAR</button>
      </form>
    </div>
  </div>
</div><!-- /.modal --> 
    

<p>&nbsp;</p>
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="langs/pt_BR.js"></script>


    <script>

	tinymce.init({
	  selector: '#formacao_textarea',
	  height: 300,
	  toolbar: 'bold italic | bullist numlist | image emoticons | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
	  plugins : 'advlist autolink link autolink image imagetools lists charmap print preview paste emoticons',
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

mysql_free_result($Topico);

mysql_free_result($Anexos);

mysql_free_result($Comentarios);
?>