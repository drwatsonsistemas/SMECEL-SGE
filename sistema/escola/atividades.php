<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/inverteData.php"; ?>
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

$currentPage = $_SERVER["PHP_SELF"];

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

$codEscola = $row_UsuLogado['usu_escola'];

$maxRows_ListaAtividades = 30;
$pageNum_ListaAtividades = 0;
if (isset($_GET['pageNum_ListaAtividades'])) {
  $pageNum_ListaAtividades = $_GET['pageNum_ListaAtividades'];
}
$startRow_ListaAtividades = $pageNum_ListaAtividades * $maxRows_ListaAtividades;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaAtividades = "SELECT smc_ativ_id, smc_ativ_data, smc_ativ_hora, smc_ativ_id_escola, smc_ativ_id_turma, smc_ativ_qtd, smc_ativ_folhas, smc_ativ_duplex, smc_ativ_caminho, smc_ativ_hash, smc_ativ_obs, turma_id, turma_nome FROM smc_atividade INNER JOIN smc_turma ON turma_id = smc_ativ_id_turma WHERE smc_ativ_id_escola = '$codEscola' ORDER BY smc_ativ_id DESC";
$query_limit_ListaAtividades = sprintf("%s LIMIT %d, %d", $query_ListaAtividades, $startRow_ListaAtividades, $maxRows_ListaAtividades);
$ListaAtividades = mysql_query($query_limit_ListaAtividades, $SmecelNovo) or die(mysql_error());
$row_ListaAtividades = mysql_fetch_assoc($ListaAtividades);

if (isset($_GET['totalRows_ListaAtividades'])) {
  $totalRows_ListaAtividades = $_GET['totalRows_ListaAtividades'];
} else {
  $all_ListaAtividades = mysql_query($query_ListaAtividades);
  $totalRows_ListaAtividades = mysql_num_rows($all_ListaAtividades);
}
$totalPages_ListaAtividades = ceil($totalRows_ListaAtividades/$maxRows_ListaAtividades)-1;

$queryString_ListaAtividades = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ListaAtividades") == false && 
        stristr($param, "totalRows_ListaAtividades") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ListaAtividades = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ListaAtividades = sprintf("&totalRows_ListaAtividades=%d%s", $totalRows_ListaAtividades, $queryString_ListaAtividades);
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
 
        <h1 class="ls-title-intro ls-ico-home">ATIVIDADES</h1>
		<!-- CONTEÚDO -->
        <?php if ($totalRows_ListaAtividades > 0) { // Show if recordset not empty ?>
  <table  class="ls-table" width="100%">
    <thead>
      <tr>
        <th class="ls-txt-center">COD</th>
        <th class="ls-txt-center">DATA</th>
        <th class="ls-txt-center">TURMA</th>
        <th class="ls-txt-center">QTD</th>
        <th class="ls-txt-center">OBS</th>
        <th></th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td class="ls-txt-center">#<?php echo $row_ListaAtividades['smc_ativ_id']; ?></td>
          <td class="ls-txt-center"><?php echo inverteData($row_ListaAtividades['smc_ativ_data']); ?></td>
          <td><?php echo $row_ListaAtividades['turma_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_ListaAtividades['smc_ativ_qtd']; ?></td>
          <td><small><?php echo $row_ListaAtividades['smc_ativ_obs']; ?></small></td>
          <td><!--
		  <a href="https://docs.google.com/gview?url=https://www.smecel.com.br/atividades/<?php echo $row_ListaAtividades['smc_ativ_id_escola']; ?>/<?php echo $row_ListaAtividades['smc_ativ_id_turma']; ?>/<?php echo $row_ListaAtividades['smc_ativ_caminho']; ?>&amp;embedded=true" target="_blank">
		  <img src="https://www.orobo.pe.gov.br/imagens/content/Icones_128X128/busca.png" width="20" border="0">
		  </a>-->
		  </td>
        </tr>
        <?php } while ($row_ListaAtividades = mysql_fetch_assoc($ListaAtividades)); ?>
    </tbody>
  </table>
        <table border="0" class="ls-txt-center">
          <tr>
            <td><?php if ($pageNum_ListaAtividades > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_ListaAtividades=%d%s", $currentPage, 0, $queryString_ListaAtividades); ?>" class="ls-btn-primary">Primeiro</a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_ListaAtividades > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_ListaAtividades=%d%s", $currentPage, max(0, $pageNum_ListaAtividades - 1), $queryString_ListaAtividades); ?>" class="ls-btn-primary">Anterior</a>
                <?php } // Show if not first page ?></td>
            <td><?php if ($pageNum_ListaAtividades < $totalPages_ListaAtividades) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_ListaAtividades=%d%s", $currentPage, min($totalPages_ListaAtividades, $pageNum_ListaAtividades + 1), $queryString_ListaAtividades); ?>" class="ls-btn-primary">Pr&oacute;ximo</a>
                <?php } // Show if not last page ?></td>
            <td><?php if ($pageNum_ListaAtividades < $totalPages_ListaAtividades) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_ListaAtividades=%d%s", $currentPage, $totalPages_ListaAtividades, $queryString_ListaAtividades); ?>" class="ls-btn-primary">&Uacute;ltimo</a>
                <?php } // Show if not last page ?></td>
          </tr>
        </table>
          <?php } else { ?>
          
			<div class="ls-alert-info"><strong>Atenção:</strong> Nenhuma atividade cadastrada até o momento.</div>
          
          <?php } // Show if recordset not empty ?>
<hr>
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

mysql_free_result($ListaAtividades);
?>
