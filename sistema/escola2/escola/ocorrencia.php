<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>

<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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

$colname_Oc = "-1";
if (isset($_GET['oc'])) {
  $colname_Oc = (int)$_GET['oc'];
  $colname_Oc = anti_injection($colname_Oc);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_OC = "
SELECT ocorrencia_id, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_id_professor, ocorrencia_data, ocorrencia_status, ocorrencia_descricao, turma_id, turma_nome, escola_id, escola_nome, func_id, func_nome
FROM smc_ocorrencia_turma
LEFT JOIN smc_turma ON turma_id = ocorrencia_id_turma
LEFT JOIN smc_escola ON escola_id = ocorrencia_id_escola
LEFT JOIN smc_func ON func_id = ocorrencia_id_professor
WHERE ocorrencia_id = '$colname_Oc' AND ocorrencia_status = '0' ORDER BY ocorrencia_data DESC
";
$OC = mysql_query($query_OC, $SmecelNovo) or die(mysql_error());
$row_OC = mysql_fetch_assoc($OC);
$totalRows_OC = mysql_num_rows($OC);

if($totalRows_OC > 0){
  $updateSQL = sprintf("UPDATE smc_ocorrencia_turma SET ocorrencia_status=1 WHERE ocorrencia_id=%s",
   GetSQLValueString($colname_Oc, "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_OC1 = "
SELECT ocorrencia_id, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_id_professor, ocorrencia_data, ocorrencia_status, ocorrencia_descricao, turma_id, turma_nome, escola_id, escola_nome, func_id, func_nome
FROM smc_ocorrencia_turma
LEFT JOIN smc_turma ON turma_id = ocorrencia_id_turma
LEFT JOIN smc_escola ON escola_id = ocorrencia_id_escola
LEFT JOIN smc_func ON func_id = ocorrencia_id_professor
WHERE ocorrencia_id = '$colname_Oc' ORDER BY ocorrencia_data DESC
";
$OC1 = mysql_query($query_OC1, $SmecelNovo) or die(mysql_error());
$row_OC1 = mysql_fetch_assoc($OC1);
$totalRows_OC1 = mysql_num_rows($OC1);
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
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

      <h1 class="ls-title-intro ls-ico-home">OCORRENCIA</h1>
      <!-- CONTEÚDO -->

      <a href="ocorrencias.php" class="ls-btn">VOLTAR</a>

      <hr>

      <h3>Professor(a): <?php echo $row_OC1['func_nome']; ?> </h3><br>
      <h4>Turma: <?php echo $row_OC1['turma_nome']; ?> </h4><br>
      <h4>Data: <?php echo inverteData($row_OC1['ocorrencia_data']); ?> </h4>
      <hr>

      <div class="ls-box">
        <h3>Descrição da ocorrência: </h3>
        <?php echo $row_OC1['ocorrencia_descricao']; ?>
      </div>      

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

mysql_free_result($OC1);

mysql_free_result($EscolaLogada);
?>
