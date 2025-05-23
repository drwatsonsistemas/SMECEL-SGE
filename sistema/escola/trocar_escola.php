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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_usu SET usu_escola=%s WHERE usu_id=%s",
                       GetSQLValueString($_POST['usu_escola'], "int"),
                       GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "trocar_escola.php?mudou";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarEscolas = "
SELECT usu_escola_id, usu_escola_id_usu, usu_escola_id_escola, escola_id, escola_nome 
FROM smc_usu_escolas
INNER JOIN smc_escola ON escola_id = usu_escola_id_escola
WHERE usu_escola_id_usu = '$row_UsuLogado[usu_id]'
ORDER BY escola_nome ASC";
$ListarEscolas = mysql_query($query_ListarEscolas, $SmecelNovo) or die(mysql_error());
$row_ListarEscolas = mysql_fetch_assoc($ListarEscolas);
$totalRows_ListarEscolas = mysql_num_rows($ListarEscolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EditarUsuEscola = "SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_id = '$row_UsuLogado[usu_id]'";
$EditarUsuEscola = mysql_query($query_EditarUsuEscola, $SmecelNovo) or die(mysql_error());
$row_EditarUsuEscola = mysql_fetch_assoc($EditarUsuEscola);
$totalRows_EditarUsuEscola = mysql_num_rows($EditarUsuEscola);

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
 
        <h1 class="ls-title-intro ls-ico-home">TROCAR ESCOLA</h1>
		<!-- CONTEÚDO -->
        
        
              <?php if (isset($_GET["mudou"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VOCÊ ALTEROU O LOGIN PARA <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong>.
                </div>
              <?php } ?>
        
        
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
          
              <label class="ls-label col-md-12">
        <b class="ls-label-text">ALTERAR</b>
        <p class="ls-label-info">Escolha na lista abaixo uma escola/setor para alterar seu login.</p>
        <div class="ls-custom-select">
              <select name="usu_escola" required>
                <?php do { ?>
                <option value="<?php echo $row_ListarEscolas['usu_escola_id_escola']?>" <?php if (!(strcmp($row_ListarEscolas['usu_escola_id_escola'], htmlentities($row_UsuLogado['usu_escola'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_ListarEscolas['escola_nome']?></option>
                <?php } while ($row_ListarEscolas = mysql_fetch_assoc($ListarEscolas)); ?>
              </select>
              </div>
              </label>
              
          <label class="ls-label col-md-12">
          <input type="submit" value="MUDAR ESCOLA" class="ls-btn-primary">
          <a href="index.php" class="ls-btn">Voltar</a>
          </label>
              
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
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
mysql_free_result($UsuLogado);

mysql_free_result($ListarEscolas);

mysql_free_result($EditarUsuEscola);

mysql_free_result($EscolaLogada);
?>
