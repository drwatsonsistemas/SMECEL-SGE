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
$query_Atualizacoes = "
SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data,
CASE atualizacoes_painel
WHEN 1 THEN '<span class=\"ls-tag-primary\">SECRETARIA DE EDUCAÇÃO</span>' 
WHEN 2 THEN '<span class=\"ls-tag-success\">PAINEL ESCOLA</span>' 
WHEN 3 THEN '<span class=\"ls-tag-info\">PAINEL DO PROFESSOR</span>' 
WHEN 4 THEN '<span class=\"ls-tag-warning\">PAINEL DO ALUNO</span>' 
WHEN 5 THEN '<span class=\"ls-tag-danger\">PORTARIA</span>' 
WHEN 6 THEN '<span class=\"ls-tag-danger\">PSE</span>' 
WHEN 99 THEN '<span class=\"ls-tag\">GPI</span>' 
END AS atualizacoes_painel
FROM smc_atualizacoes
WHERE (atualizacoes_painel <> '99' AND atualizacoes_painel <> '1')
ORDER BY atualizacoes_id DESC";
$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$row_Atualizacoes = mysql_fetch_assoc($Atualizacoes);
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesUltima = "
SELECT * FROM smc_atualizacoes WHERE (atualizacoes_painel <> '99' AND atualizacoes_painel <> '1') ORDER BY atualizacoes_id DESC LIMIT 0,1";
$AtualizacoesUltima = mysql_query($query_AtualizacoesUltima, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesUltima = mysql_fetch_assoc($AtualizacoesUltima);
$totalRows_AtualizacoesUltima = mysql_num_rows($AtualizacoesUltima);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesVisualizadas = "SELECT atualizacao_ver_id, atualizacao_ver_cod_atualizacao, atualizacao_ver_cod_usuario, atualizacao_ver_sec, atualizacao_ver_escola, atualizacao_ver_professor, atualizacao_ver_aluno, atualizacao_ver_data FROM smc_atualizacao_ver WHERE atualizacao_ver_cod_atualizacao = '$row_Atualizacoes[atualizacoes_id]' AND atualizacao_ver_sec = '$row_UsuLogado[usu_sec]' AND atualizacao_ver_cod_usuario = '$row_UsuLogado[usu_id]'";
$AtualizacoesVisualizadas = mysql_query($query_AtualizacoesVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesVisualizadas = mysql_fetch_assoc($AtualizacoesVisualizadas);
$totalRows_AtualizacoesVisualizadas = mysql_num_rows($AtualizacoesVisualizadas);

if ($totalRows_AtualizacoesVisualizadas == 0) {

$insertSQL = "
INSERT INTO smc_atualizacao_ver (
	atualizacao_ver_cod_atualizacao, 
	atualizacao_ver_cod_usuario, 
	atualizacao_ver_sec 
) VALUES ('$row_AtualizacoesUltima[atualizacoes_id]', '$row_UsuLogado[usu_id]', '$row_UsuLogado[usu_sec]')
";

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
	
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
 
        <h1 class="ls-title-intro ls-ico-home">ATUALIZAÇÕES</h1>
		<!-- CONTEÚDO -->
		
		
      <?php do { ?>
      <div data-ls-module="collapse" data-target="#<?php echo $row_Atualizacoes['atualizacoes_id']; ?>" class="ls-collapse <?php if ($row_AtualizacoesUltima['atualizacoes_id']==$row_Atualizacoes['atualizacoes_id']) {?>ls-collapse-opened<?php } ?>" aria-expanded="false"> 
      <small class="ls-collapse-title ls-collapse-header"> <?php echo $row_Atualizacoes['atualizacoes_painel']; ?> 
        <p> <?php echo date('d/m/Y - H\hi', strtotime($row_Atualizacoes['atualizacoes_data'])); ?> </p>
        </small>
        <div class="ls-collapse-body" id="<?php echo $row_Atualizacoes['atualizacoes_id']; ?>">
          <p> <i><small><b><?php echo $row_Atualizacoes['atualizacoes_modulo']; ?></b></small><br> <?php echo $row_Atualizacoes['atualizacoes_texto']; ?></i> </p>
        </div>
      </div>
      <?php } while ($row_Atualizacoes = mysql_fetch_assoc($Atualizacoes)); ?>
		
		
		
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
?>
