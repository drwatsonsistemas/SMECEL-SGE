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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_professores = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs, func_nome, funcao_id, funcao_docencia 
FROM 
smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao
WHERE vinculo_id_escola = '$row_UsuLogado[usu_escola]' AND funcao_docencia = 'S' 
";
$professores = mysql_query($query_professores, $SmecelNovo) or die(mysql_error());
$row_professores = mysql_fetch_assoc($professores);
$totalRows_professores = mysql_num_rows($professores);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$escola = $row_UsuLogado['usu_escola'];
$hash = md5(date("YmdHis").$row_UsuLogado['usu_escola']);	
	
$insertSQL = sprintf("INSERT INTO comun_esc_prof_topico (com_topico_id_escola, com_topico_id_prof, com_topico_texto, com_topico_hash) VALUES ('$escola', %s, %s, '$hash')",
                       //GetSQLValueString($_POST['com_topico_id_escola'], "int"),
                       GetSQLValueString($_POST['com_topico_id_prof'], "int"),
                       GetSQLValueString($_POST['com_topico_texto'], "text")
                       //GetSQLValueString($_POST['com_topico_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
 
 $hash2 = md5($hash.$row_UsuLogado['usu_id']); 
  
  $insertSQL2 = sprintf("INSERT INTO comun_esc_prof_item (com_item_hash_topico, com_item_tipo_res, com_tipo_id_coord, com_tipo_texto, com_tipo_hash) VALUES ('$hash', 'C', '$row_UsuLogado[usu_id]', %s, '$hash2')",
                       //GetSQLValueString($_POST['com_topico_id_escola'], "int"),
                       //GetSQLValueString($_POST['com_topico_id_prof'], "int"),
                       GetSQLValueString($_POST['com_topico_texto'], "text")
                       //GetSQLValueString($_POST['com_topico_hash'], "text")
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result2 = mysql_query($insertSQL2, $SmecelNovo) or die(mysql_error());

  
  

  $insertGoTo = "comunicacao_todas.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
 
        <h1 class="ls-title-intro ls-ico-home">COMUNICAÇÃO</h1>
		<!-- CONTEÚDO -->
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
          <table align="center">
            <tr valign="baseline">
              <td nowrap align="right">Com_topico_id_prof:</td>
              <td><select name="com_topico_id_prof">
                <?php 
do {  
?>
                <option value="<?php echo $row_professores['vinculo_id_funcionario']?>" ><?php echo $row_professores['func_nome']?></option>
                <?php
} while ($row_professores = mysql_fetch_assoc($professores));
?>
              </select></td>
            <tr>
            <tr valign="baseline">
              <td nowrap align="right" valign="top">Com_topico_texto:</td>
              <td><textarea name="com_topico_texto" cols="50" rows="5"></textarea></td>
            </tr>
            <tr valign="baseline">
              <td nowrap align="right">&nbsp;</td>
              <td><input type="submit" value="Insert record"></td>
            </tr>
          </table>
          <input type="hidden" name="com_topico_id_escola" value="<?php echo $row_professores['vinculo_id_escola']; ?>">
          <input type="hidden" name="com_topico_hash" value="">
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
mysql_free_result($professores);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
