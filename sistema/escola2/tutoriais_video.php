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
$query_Tutoriais = "
SELECT tutoriais_video_id, tutoriais_video_titulo, tutoriais_video_url, tutoriais_video_painel,
CASE tutoriais_video_painel
WHEN 1 THEN '<span class=ls-tag-primary>SECRETARIA</span>'
WHEN 2 THEN '<span class=ls-tag-success>ESCOLA</span>'
WHEN 3 THEN '<span class=ls-tag-info>PROFESSOR</span>'
WHEN 4 THEN '<span class=ls-tag-warning>ALUNO</span>'
WHEN 5 THEN '<span class=ls-tag-danger>PORTARIA</span>'
WHEN 6 THEN '<span class=ls-tag>PSE</span>'
END AS tutoriais_video_painel_descricao 
FROM smc_tutoriais_video
WHERE 
tutoriais_video_painel IN (2, 3, 4, 5, 6)
ORDER BY tutoriais_video_painel, tutoriais_video_titulo ASC";
$Tutoriais = mysql_query($query_Tutoriais, $SmecelNovo) or die(mysql_error());
$row_Tutoriais = mysql_fetch_assoc($Tutoriais);
$totalRows_Tutoriais = mysql_num_rows($Tutoriais);
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
<style>
  df-messenger {
   --df-messenger-bot-message: white;
   --df-messenger-button-titlebar-color: #075e54;
   --df-messenger-chat-background-color: #ece5dd;
   --df-messenger-font-color: black;
   --df-messenger-send-icon: #878fac;
   --df-messenger-user-message: #dcf8c6;
  }
</style>
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Tutoriais (Vídeo)</h1>
		<!-- CONTEÚDO -->
		
		

  
<table class="ls-table">
	<thead>
      <tr>
        <th>Título</th>
        <th width="100">Painel</th>
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
      <?php $link = explode("=", $row_Tutoriais['tutoriais_video_url']); ?>

       
        <tr>
          <td><a style="cursor:pointer" data-ls-module="modal" data-action-type="link" data-action="" data-content='<iframe width="100%" height="320" src="https://www.youtube.com/embed/<?php echo $link[1]; ?>" title="<?php echo $row_Tutoriais['tutoriais_video_titulo']; ?>" frameborder="0" allow="" allowfullscreen></iframe>' data-title="<?php echo $row_Tutoriais['tutoriais_video_titulo']; ?>" data-class="ls-btn-danger" data-save="FECHAR" data-close="CANCELAR"><?php echo $row_Tutoriais['tutoriais_video_titulo']; ?></a></td>
          <td><?php echo $row_Tutoriais['tutoriais_video_painel_descricao']; ?></td>
        </tr>
        
        <?php } while ($row_Tutoriais = mysql_fetch_assoc($Tutoriais)); ?>
        </tbody>
    </table>
    
    
    		
		
		
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
	


<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  chat-icon="https:&#x2F;&#x2F;storage.googleapis.com&#x2F;cloudprod-apiai&#x2F;d6fbe379-bf37-4b42-b738-5ec0762d62da_x.png"
  intent="WELCOME"
  chat-title="Smecel-FAQ"
  agent-id="553bbcb9-1afc-4a31-9d38-44ae05426572"
  language-code="pt-br"
></df-messenger>

 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Tutoriais);
?>
