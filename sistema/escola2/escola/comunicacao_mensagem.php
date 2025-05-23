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

$colname_mensagens = "-1";
if (isset($_GET['msg'])) {
  $colname_mensagens = $_GET['msg'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_mensagens = sprintf("
SELECT com_item_id, com_item_hash_topico, com_item_tipo_res, com_item_id_prof, com_tipo_id_coord, com_tipo_data, com_tipo_texto, com_tipo_visualizada, com_tipo_hash, func_id, func_nome, usu_id, usu_nome 
FROM comun_esc_prof_item 
LEFT JOIN smc_func ON func_id = com_item_id_prof
LEFT JOIN smc_usu ON usu_id = com_tipo_id_coord
WHERE com_item_hash_topico = %s", GetSQLValueString($colname_mensagens, "text"));
$mensagens = mysql_query($query_mensagens, $SmecelNovo) or die(mysql_error());
$row_mensagens = mysql_fetch_assoc($mensagens);
$totalRows_mensagens = mysql_num_rows($mensagens);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$escola = $row_UsuLogado['usu_escola'];
$hash = md5(date("YmdHis").$row_UsuLogado['usu_escola']);	

$prof = $row_mensagens['com_item_id_prof'];
	
  $insertSQL = sprintf("INSERT INTO comun_esc_prof_item (com_item_hash_topico, com_item_tipo_res, com_item_id_prof, com_tipo_id_coord, com_tipo_texto, com_tipo_hash) VALUES ('$colname_mensagens', 'C', '$prof', '$row_UsuLogado[usu_id]', %s, '$hash')",
                       //GetSQLValueString($_POST['com_item_hash_topico'], "text"),
                       //GetSQLValueString($_POST['com_item_tipo_res'], "text"),
                       //GetSQLValueString($_POST['com_item_id_prof'], "int"),
                       //GetSQLValueString($_POST['com_tipo_id_coord'], "int"),
                       GetSQLValueString($_POST['com_tipo_texto'], "text")
                       //GetSQLValueString($_POST['com_tipo_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $updateSQL2 = "UPDATE comun_esc_prof_topico SET com_topico_visualizado = 'N', com_topico_quem = 'C' WHERE com_topico_hash = '$colname_mensagens'";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result2 = mysql_query($updateSQL2, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "comunicacao_mensagem.php?cadastrada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$updateSQL = "UPDATE comun_esc_prof_topico SET com_topico_visualizado = 'S' WHERE com_topico_hash = '$colname_mensagens' AND com_topico_quem = 'P'";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result2 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
	
  $deleteSQL = sprintf("DELETE FROM comun_esc_prof_item WHERE com_item_hash_topico = '$colname_mensagens' AND com_tipo_hash=%s",
                       GetSQLValueString($_GET['del'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "comunicacao_mensagem.php?msg=$colname_mensagens&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

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
 
        <h1 class="ls-title-intro ls-ico-home">MENSAGEM</h1>
		<!-- CONTEÚDO -->
            <p><a href="comunicacao_todas.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

		
		
<table class="ls-table ls-table-striped">
	<?php do { ?>
	  <tr>
          <td>
		  
		  <p><small><?php if ($row_mensagens['com_item_tipo_res']=="P") { ?><span class="ls-ico-bullhorn ls-color-success ls-ico-left"></span> <?php echo $row_mensagens['func_nome']; ?><?php } else { ?><span class="ls-ico-checkmark-circle ls-color-success ls-ico-left"></span> <?php echo $row_mensagens['usu_nome']; ?><?php } ?> </small></p>
          <p><strong><?php echo nl2br($row_mensagens['com_tipo_texto']); ?></strong></p>
          <p class="ls-txt-right"><small><?php echo date("d/m/Y - H:i", strtotime($row_mensagens['com_tipo_data'])); ?></small></p>
          
          </td>
          <td width="50"><?php if ($row_mensagens['com_item_tipo_res']=="C") { ?><a href="comunicacao_mensagem.php?msg=<?php echo $row_mensagens['com_item_hash_topico']; ?>&del=<?php echo $row_mensagens['com_tipo_hash']; ?>" class="ls-ico-remove ls-color-danger"></a><?php } ?></td>
      </tr>
	<?php } while ($row_mensagens = mysql_fetch_assoc($mensagens)); ?>
    </table>

        
        
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
          
          <label class="ls-label col-md-12">
          <textarea name="com_tipo_texto" cols="50" rows="5"></textarea>
          </label>
          
          <div class="ls-actions-btn">
          <input type="submit" value="RESPONDER" class="ls-btn-primary">
          </div>
          
          <input type="hidden" name="com_item_hash_topico" value="<?php echo $row_mensagens['com_item_hash_topico']; ?>">
          <input type="hidden" name="com_item_tipo_res" value="C">
          <input type="hidden" name="com_item_id_prof" value="<?php echo $row_mensagens['com_item_id_prof']; ?>">
          <input type="hidden" name="com_tipo_id_coord" value="<?php echo $row_mensagens['com_tipo_id_coord']; ?>">
          <input type="hidden" name="com_tipo_hash" value="">
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
mysql_free_result($mensagens);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
