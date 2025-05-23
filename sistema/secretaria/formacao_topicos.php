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
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Formacao = sprintf("SELECT curso_form_id, curso_form_id_sec, curso_form_nome, curso_form_descricao, curso_form_data_inicio, curso_form_data_fim, curso_form_responsavel, curso_form_ch, curso_form_hash, curso_form_aberto FROM smc_curso_formacao WHERE curso_form_hash = %s", GetSQLValueString($colname_Formacao, "text"));
$Formacao = mysql_query($query_Formacao, $SmecelNovo) or die(mysql_error());
$row_Formacao = mysql_fetch_assoc($Formacao);
$totalRows_Formacao = mysql_num_rows($Formacao);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Topicos = "
SELECT curso_form_topic_id, curso_form_topic_id_form, curso_form_topic_titulo, curso_form_topic_texto, curso_form_topic_aberto, curso_form_data,
curso_form_com_id, curso_form_com_id_topico, COUNT(curso_form_com_id_topico) AS comentarios 
FROM smc_curso_formacao_topicos
LEFT JOIN smc_curso_formacao_comentarios
ON curso_form_com_id_topico = curso_form_topic_id
WHERE curso_form_topic_id_form = '$row_Formacao[curso_form_id]' 
GROUP BY curso_form_topic_id
ORDER BY curso_form_topic_id ASC";
$Topicos = mysql_query($query_Topicos, $SmecelNovo) or die(mysql_error());
$row_Topicos = mysql_fetch_assoc($Topicos);
$totalRows_Topicos = mysql_num_rows($Topicos);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO smc_curso_formacao_topicos (curso_form_topic_id_form, curso_form_topic_titulo, curso_form_topic_texto, curso_form_topic_aberto) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['curso_form_topic_id_form'], "int"),
                       GetSQLValueString($_POST['curso_form_topic_titulo'], "text"),
                       GetSQLValueString($_POST['curso_form_topic_texto'], "text"),
                       GetSQLValueString($_POST['curso_form_topic_aberto'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "formacao_topicos.php?inserido";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-chart-bar-up">TÓPICOS PARA A FORMAÇÃO</h1>
    <!-- CONTEUDO -->
 <a href="formacao.php" class="ls-btn-primary">Voltar</a>   

    <br><br>

<div class="ls-box">
  <h5 class="ls-title-3"><?php echo $row_Formacao['curso_form_nome']; ?></h5>
  <p><?php echo $row_Formacao['curso_form_descricao']; ?></p>  
  
  <?php if ($row_Formacao['curso_form_aberto']=="N") { ?>
  
  <div class="ls-alert-info"><strong>Atenção:</strong> Essa formação está fechada.</div>
  
  <?php } ?>
  
  <a href="formacao_editar.php?codigo=<?php echo $row_Formacao['curso_form_hash']; ?>" class="ls-btn">Editar</a>

  </div>    
 
   <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">Inserir Tópico</button>

<hr>
<?php if ($totalRows_Topicos > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
  <thead>
    <tr>
      <th width="50"></th>
      <th class="ls-txt-center">TÓPICO</th>
      <th class="ls-txt-center" width="150">COMENTÁRIOS</th>
      <th class="ls-txt-center" width="100">DATA</th>
      <th class="ls-txt-center" width="180">STATUS</th>
    </tr>
    </thead>
    <tbody>
    <?php 
	$num = 1; 
	do { ?>
      <tr>
        <td class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
        <td><a href="formacao_topico_editar.php?formacao=<?php echo $colname_Formacao; ?>&topico=<?php echo $row_Topicos['curso_form_topic_id']; ?>"><?php echo $row_Topicos['curso_form_topic_titulo']; ?></a></td>
        <td class="ls-txt-center"><?php echo $row_Topicos['comentarios']; ?></td>
        <td class="ls-txt-center"><?php if ($row_Topicos['curso_form_topic_aberto']=="S") { echo "ABERTO"; } else { echo "FECHADO"; } ?></td>
        <td class="ls-txt-center"><?php echo date("d/m/Y - H:i", strtotime($row_Topicos['curso_form_data'])); ?></td>
      </tr>
      <?php } while ($row_Topicos = mysql_fetch_assoc($Topicos)); ?>
  </table>
  </tbody>
  
  <?php } else { ?>
  
  
  <p>Nenhum tópico adicionado.</p>
  
  <?php } // Show if recordset not empty ?>
  
 
  
  
  
<p>&nbsp;</p>
<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">INSERIR TÓPICO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      

      <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      
      <fieldset>
      

        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Nome do tópico</b>
          <input type="text" name="curso_form_topic_titulo" value="" size="32">
        </label>
                
        <label class="ls-label col-sm-12">
          <b class="ls-label-text">Conteúdo</b>
          <p class="ls-label-info">Descreva detalhes do tópico</p>
          <textarea id="formacao_textarea" name="curso_form_topic_texto" cols="50" rows="5"></textarea>
        </label>
        
  </fieldset>      
  
    <div class="ls-label col-md-12">
      <p>Status da formação:</p>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_topic_aberto" class="ls-field-radio" value="S" <?php if (!(strcmp("S","S"))) {echo "checked=\"checked\"";} ?>>
        Aberto
      </label>
      <label class="ls-label-text">
        <input type="radio" name="curso_form_topic_aberto" class="ls-field-radio" value="N" <?php if (!(strcmp("S","N"))) {echo "checked=\"checked\"";} ?>>
        Fechado
      </label>
    </div>    
           
      
      
        <input type="hidden" name="curso_form_topic_id_form" value="<?php echo $row_Formacao['curso_form_id']; ?>">
        <input type="hidden" name="MM_insert" value="form2">
            
      </p>
      
    </div>
    <div class="ls-modal-footer">
      <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <button type="submit" class="ls-btn-primary">SALVAR</button>
      </form>
    </div>
  </div>
</div><!-- /.modal -->    
    <!-- CONTEUDO -->    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
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

mysql_free_result($Topicos);

mysql_free_result($Formacao);

?>