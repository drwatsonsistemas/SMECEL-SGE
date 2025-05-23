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
  $updateSQL = sprintf("UPDATE smc_chamados SET chamado_situacao=%s WHERE chamado_id=%s",
                       GetSQLValueString($_POST['chamado_situacao'], "text"),
                       GetSQLValueString($_POST['chamado_id'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


if ($_POST['chamado_situacao']=="F") {

  $updateGoTo = "chamados.php?encerrado";
  
} else {
	$updateGoTo = "";
			$updateSQL1 = "UPDATE smc_chamados SET chamado_visualizado = 'N' WHERE chamado_id = '$_POST[chamado_id]'";
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
  			$Result2 = mysql_query($updateSQL1, $SmecelNovo) or die(mysql_error());

	}
	
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_ticket (ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_visualizado) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ticket_id_chamado'], "int"),
                       GetSQLValueString($_POST['ticket_id_usuario'], "int"),
                       GetSQLValueString($_POST['ticket_data'], "date"),
                       GetSQLValueString($_POST['ticket_texto'], "text"),
                       GetSQLValueString($_POST['ticket_visualizado'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}





require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Chamado = "-1";
if (isset($_GET['chamado'])) {
  $colname_Chamado = $_GET['chamado'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Chamado = sprintf("
SELECT chamado_id, chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, 
chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado, chamado_numero, usu_id, usu_nome 
FROM smc_chamados 
INNER JOIN smc_usu ON usu_id = chamado_id_usuario
WHERE chamado_id_sec = '$row_Secretaria[sec_id]' AND chamado_numero = %s", GetSQLValueString($colname_Chamado, "text"));
$Chamado = mysql_query($query_Chamado, $SmecelNovo) or die(mysql_error());
$row_Chamado = mysql_fetch_assoc($Chamado);
$totalRows_Chamado = mysql_num_rows($Chamado);

if ($totalRows_Chamado == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: index.php?erro"); 
 	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ticket = "
SELECT ticket_id, ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_imagem, ticket_visualizado, usu_id, usu_nome 
FROM smc_ticket 
INNER JOIN smc_usu ON usu_id = ticket_id_usuario
WHERE ticket_id_chamado = '$row_Chamado[chamado_id]'";
$Ticket = mysql_query($query_Ticket, $SmecelNovo) or die(mysql_error());
$row_Ticket = mysql_fetch_assoc($Ticket);
$totalRows_Ticket = mysql_num_rows($Ticket);
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
            .anexo-preview img {
            max-width: 200px;
            margin: 10px;
            border: 1px solid #ccc;
            padding: 5px;
        }
        .anexo-link {
            display: block;
            margin: 10px 0;
        }
  </style>
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">DETALHES DO CHAMADO #<?php echo $row_Chamado['chamado_numero']; ?></h1>
    
    <!-- CONTEUDO -->
    
    
    <div class="ls-box ls-board-box">
      <header class="ls-info-header">
        <h2 class="ls-title-3">TÍTULO: <?php echo $row_Chamado['chamado_titulo']; ?></h2>
        <p class="ls-float-right ls-float-none-xs ls-small-info"> CATEGORIA: <span class="ls-tag"><?php echo $row_Chamado['chamado_categoria']; ?></span> </p>
      </header>
      <p>AUTOR: <strong><?php echo $row_Chamado['usu_nome']; ?></strong> CADASTRO: <strong><?php echo date("d/m/Y", strtotime($row_Chamado['chamado_data_abertura'])); ?></strong></p>
      <p> <?php echo nl2br($row_Chamado['chamado_texto']); ?> </p>

      <?php
                $anexos = json_decode($row_Chamado['chamado_imagem'], true);
                if (!empty($anexos)) {
                    echo '<div class="anexo-preview">';
                    foreach ($anexos as $anexo) {
                        $file_path = "../../chamados_anexo/" . $anexo;
                        $file_ext = strtolower(pathinfo($anexo, PATHINFO_EXTENSION));
                        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<a href='$file_path' target='_blank'><img src='$file_path' alt='Anexo'></a>";
                        } else {
                            echo "<a href='$file_path' class='ls-btn anexo-link' target='_blank'>Baixar $anexo</a>";
                        }
                    }
                    echo '</div>';
                }
                ?>
    </div>
    
    <?php if ($totalRows_Ticket > 0) { // Show if recordset not empty ?>
      <?php 
	  $resposta = 1;
	  do { ?>
        <div class="ls-box ls-board-box ls-box-gray">
          
            <p><strong>#<?php echo $resposta; $resposta++; ?> <?php echo $row_Ticket['usu_nome']; ?> respondeu em <?php echo date("d/m/Y", strtotime($row_Ticket['ticket_data'])); ?>:</strong></p>
          
          
          <p><?php echo nl2br($row_Ticket['ticket_texto']); ?></p>
        </div>
        <?php } while ($row_Ticket = mysql_fetch_assoc($Ticket)); ?>
      <?php } // Show if recordset not empty ?>
      
    
    
  <br>
  
  <?php if ($row_Chamado['chamado_situacao']=="F") { ?>
  <div class="ls-alert-info"><strong>Atenção:</strong> 
  Este chamado já foi finalizado. Caso necessite fazer uma nova interação, clique no botão abaixo para reativar o chamado e escrever uma nova mensagem.
  </div>
  <?php } ?>
    
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="ls-form">
  
  <?php if ($row_Chamado['chamado_situacao']=="A") { ?>
  <span data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">NOVA RESPOSTA</span>
  <?php } ?>
  <a href="chamados.php" class="ls-btn">VOLTAR</a>
  
  <input type="submit" value="<?php if ($row_Chamado['chamado_situacao']=="A") { ?>ENCERRAR ESTE CHAMADO<?php } else { ?>REABRIR ESTE CHAMADO<?php } ?>" class="ls-btn-dark ls-float-right" />
  
  <input type="hidden" name="chamado_situacao" value="<?php if ($row_Chamado['chamado_situacao']=="A") { ?>F<?php } else { ?>A<?php } ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="chamado_id" value="<?php echo $row_Chamado['chamado_id']; ?>" />
</form>
    
    
    <div class="ls-modal" id="myAwesomeModal">
      <div class="ls-modal-box ls-modal-large">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">MENSAGEM COMPLEMENTAR AO CHAMADO #<?php echo $row_Chamado['chamado_numero']; ?></h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
          <div class="ls-box"><small><?php echo $row_Chamado['chamado_texto']; ?></small></div>
          <p>
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
            <label class="ls-label col-md-12">
            <b class="ls-label-text">TEXTO</b>
            <p class="ls-label-info">Informe detalhes que complementem o chamado</p>
            <textarea name="ticket_texto" id="mytextarea" cols="50" rows="5"></textarea>
            </label>
            <input type="hidden" name="ticket_id_chamado" value="<?php echo $row_Chamado['chamado_id']; ?>">
            <input type="hidden" name="ticket_id_usuario" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
            <input type="hidden" name="ticket_data" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="ticket_visualizado" value="N">
            <input type="hidden" name="MM_insert" value="form1">
            <label class="ls-label col-md-12">
              <input type="submit" class="ls-btn-primary" value="SALVAR">
              <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
            </label>
          </form>
          </p>
        </div>
        <div class="ls-modal-footer"> </div>
      </div>
    </div>
    <!-- /.modal -->
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    
    <!-- CONTEUDO -->    
    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

<script src="//cdn.tinymce.com/4/tinymce.min.js1"></script> 
		<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="langs/pt_BR.js"></script>

<script>

	tinymce.init({
	  selector: '#mytextarea',
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
mysql_free_result($UsuarioLogado);

mysql_free_result($Chamado);

mysql_free_result($Ticket);

mysql_free_result($Secretaria);
?>