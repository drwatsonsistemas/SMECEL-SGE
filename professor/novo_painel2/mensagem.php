<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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

$colname_Mensagemtopico = "-1";
if (isset($_GET['msg'])) {
  $colname_Mensagemtopico = $_GET['msg'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Mensagemtopico = sprintf("SELECT com_topico_id, com_topico_id_escola, com_topico_id_prof, com_topico_data, com_topico_texto, com_topico_hash, com_topico_atualizacao, com_topico_visualizado, com_topico_quem FROM comun_esc_prof_topico WHERE com_topico_hash = %s", GetSQLValueString($colname_Mensagemtopico, "text"));
$Mensagemtopico = mysql_query($query_Mensagemtopico, $SmecelNovo) or die(mysql_error());
$row_Mensagemtopico = mysql_fetch_assoc($Mensagemtopico);
$totalRows_Mensagemtopico = mysql_num_rows($Mensagemtopico);

$colname_mensagem = "-1";
if (isset($_GET['msg'])) {
  $colname_mensagem = $_GET['msg'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_mensagem = sprintf("
SELECT com_item_id, com_item_hash_topico, com_item_tipo_res, com_item_id_prof, com_tipo_id_coord, com_tipo_data, com_tipo_texto, com_tipo_visualizada, com_tipo_hash, func_id, func_nome, usu_id, usu_nome 
FROM comun_esc_prof_item 
LEFT JOIN smc_func ON func_id = com_item_id_prof
LEFT JOIN smc_usu ON usu_id = com_tipo_id_coord
WHERE com_item_hash_topico = %s", GetSQLValueString($colname_mensagem, "text"));
$mensagem = mysql_query($query_mensagem, $SmecelNovo) or die(mysql_error());
$row_mensagem = mysql_fetch_assoc($mensagem);
$totalRows_mensagem = mysql_num_rows($mensagem);



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$hash = md5(date("YmdHis").$row_ProfLogado['func_id']);
	
  $insertSQL = sprintf("INSERT INTO comun_esc_prof_item (com_item_hash_topico, com_item_tipo_res, com_item_id_prof, com_tipo_texto, com_tipo_hash) VALUES (%s, %s, %s, %s, '$hash')",
                       GetSQLValueString($_POST['com_item_hash_topico'], "text"),
                       GetSQLValueString($_POST['com_item_tipo_res'], "text"),
                       GetSQLValueString($_POST['com_item_id_prof'], "int"),
                       GetSQLValueString($_POST['com_tipo_texto'], "text")
                       //GetSQLValueString($_POST['com_tipo_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
$updateSQL2 = "UPDATE comun_esc_prof_topico SET com_topico_visualizado = 'N', com_topico_quem = 'P' WHERE com_topico_hash = '$colname_mensagem'";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result2 = mysql_query($updateSQL2, $SmecelNovo) or die(mysql_error());

  
  

  $insertGoTo = "mensagem.php?enviada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



$updateSQL = "UPDATE comun_esc_prof_topico SET com_topico_visualizado = 'S' WHERE com_topico_hash = '$colname_mensagem' AND com_topico_quem = 'C'";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result2 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
	
  $deleteSQL = sprintf("DELETE FROM comun_esc_prof_item WHERE com_item_hash_topico = '$colname_mensagem' AND com_tipo_hash=%s",
                       GetSQLValueString($_GET['del'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "mensagem.php?msg=$colname_mensagem&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


?>

<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">MENSAGENS</h1>
    <p><a href="mensagens.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

        
	<table class="ls-table ls-table-striped">
	<?php do { ?>
	  <tr>
          <td>
          
          <p><small><?php if ($row_mensagem['com_item_tipo_res']=="P") { ?><span class="ls-ico-bullhorn ls-color-success ls-ico-left"></span> <?php echo $row_mensagem['func_nome']; ?><?php } else { ?><span class="ls-ico-checkmark-circle ls-color-success ls-ico-left"></span> <?php echo $row_mensagem['usu_nome']; ?><?php } ?> </small></p>
          <p><strong><?php echo nl2br($row_mensagem['com_tipo_texto']); ?></strong></p>
          <p class="ls-txt-right"><small><?php echo date("d/m/Y - H:i", strtotime($row_mensagem['com_tipo_data'])); ?></small></p>

          
          </td>
          <td width="50"><?php if ($row_mensagem['com_item_tipo_res']=="P") { ?><a href="mensagem.php?msg=<?php echo $row_mensagem['com_item_hash_topico']; ?>&del=<?php echo $row_mensagem['com_tipo_hash']; ?>" class="ls-ico-remove ls-color-danger"></a><?php } ?></td>
      </tr>
	<?php } while ($row_mensagem = mysql_fetch_assoc($mensagem)); ?>
    </table>
   
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
      
      <label class="ls-label col-md-12">
      <textarea name="com_tipo_texto" cols="50" rows="5"></textarea>
      </label>
     
      
      <div class="ls-actions-btn">
      <input type="submit" value="RESPONDER" class="ls-btn-primary">
      </div>
      
      <input type="hidden" name="com_item_hash_topico" value="<?php echo $colname_mensagem; ?>">
      <input type="hidden" name="com_item_tipo_res" value="P">
      <input type="hidden" name="com_item_id_prof" value="<?php echo $row_ProfLogado['func_id']; ?>">
      <input type="hidden" name="com_tipo_hash" value="<?php echo $colname_mensagem; ?>">
      <input type="hidden" name="MM_insert" value="form1">
    </form>
    
    <p>&nbsp;</p>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>
<?php
mysql_free_result($mensagem);

mysql_free_result($Mensagemtopico);
?>
