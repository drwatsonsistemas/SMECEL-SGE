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
	
  $hora = date('Y-m-d H:i:s');
	
  $updateSQL = sprintf("UPDATE smc_atividade_correcao SET smc_ativ_corr_status=%s, smc_ativ_corr_data_vis='$hora', smc_ativ_corr_obs_final=%s WHERE smc_ativ_corr_id=%s",
                       GetSQLValueString($_POST['smc_ativ_corr_status'], "text"),
                       GetSQLValueString($_POST['smc_ativ_corr_obs_final'], "text"),
                       GetSQLValueString($_POST['smc_ativ_corr_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "atividadesCorrecao.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

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

$colname_EditarCorrecao = "-1";
if (isset($_GET['hash'])) {
  $colname_EditarCorrecao = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EditarCorrecao = sprintf("SELECT smc_ativ_corr_id, smc_ativ_corr_data, smc_ativ_corr_hora, smc_ativ_corr_id_escola, smc_ativ_corr_id_turma, smc_ativ_corr_caminho, smc_ativ_corr_hash, smc_ativ_corr_obs, smc_ativ_corr_status, smc_ativ_corr_data_vis, smc_ativ_corr_obs_final FROM smc_atividade_correcao WHERE smc_ativ_corr_hash = %s", GetSQLValueString($colname_EditarCorrecao, "text"));
$EditarCorrecao = mysql_query($query_EditarCorrecao, $SmecelNovo) or die(mysql_error());
$row_EditarCorrecao = mysql_fetch_assoc($EditarCorrecao);
$totalRows_EditarCorrecao = mysql_num_rows($EditarCorrecao);
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
 
        <h1 class="ls-title-intro ls-ico-home">VERIFICAR ATIVIDADE</h1>
		<!-- CONTEÚDO -->
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
          <table align="center" width="100%">
		  <tr>
			<td>
				<?php echo htmlentities($row_EditarCorrecao['smc_ativ_corr_obs'], ENT_COMPAT, 'utf-8'); ?>
			</td>
		  </tr>
		  
		  <tr>
			<td>
				<?php echo htmlentities($row_EditarCorrecao['smc_ativ_corr_data'], ENT_COMPAT, 'utf-8'); ?>
			</td>
		  </tr>
		  
		  <tr>
			<td>
				<?php echo htmlentities($row_EditarCorrecao['smc_ativ_corr_hora'], ENT_COMPAT, 'utf-8'); ?>
			</td>
		  </tr>
		  
		  <tr>
			<td>
				<a href="https://docs.google.com/gview?url=https://www.smecel.com.br/atividades/<?php echo $row_EditarCorrecao['smc_ativ_corr_id_escola']; ?>/<?php echo $row_EditarCorrecao['smc_ativ_corr_id_turma']; ?>/<?php echo $row_EditarCorrecao['smc_ativ_corr_caminho']; ?>&amp;embedded=true" target="_blank">
					<small>Se a imagem abaixo não aparecer, clique neste link</small>
				</a>
			</tr>
		  </tr>
		  
		  <tr>
		  <td>
		  
		  <iframe style="border:0;" src="https://docs.google.com/gview?url=https://www.smecel.com.br/atividades/<?php echo $row_EditarCorrecao['smc_ativ_corr_id_escola']; ?>/<?php echo $row_EditarCorrecao['smc_ativ_corr_id_turma']; ?>/<?php echo $row_EditarCorrecao['smc_ativ_corr_caminho']; ?>&amp;embedded=true" width="100%" height="600px" frameborder="0" scrolling="no"></iframe>	  
		  
		  </td>
		  </tr>
            <tr valign="baseline">
              <td>
              <strong>Observações sobre a correção da atividade acima</strong><br>
              <textarea name="smc_ativ_corr_obs_final" cols="50" rows="5"><?php echo htmlentities($row_EditarCorrecao['smc_ativ_corr_obs_final'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
            </tr>
            <tr valign="baseline">
              <td><input type="submit" value="VERIFICAR" class="ls-btn-primary"></td>
            </tr>
          </table>
          <input type="hidden" name="smc_ativ_corr_status" value="1">
          <input type="hidden" name="smc_ativ_corr_data_vis" value="<?php echo htmlentities($row_EditarCorrecao['smc_ativ_corr_data_vis'], ENT_COMPAT, 'utf-8'); ?>">
          <input type="hidden" name="MM_update" value="form1">
          <input type="hidden" name="smc_ativ_corr_id" value="<?php echo $row_EditarCorrecao['smc_ativ_corr_id']; ?>">
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

mysql_free_result($EscolaLogada);

mysql_free_result($EditarCorrecao);
?>
