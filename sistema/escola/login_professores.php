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

$maxRows_LogProfessores = 100;
$pageNum_LogProfessores = 0;
if (isset($_GET['pageNum_LogProfessores'])) {
  $pageNum_LogProfessores = $_GET['pageNum_LogProfessores'];
}
$startRow_LogProfessores = $pageNum_LogProfessores * $maxRows_LogProfessores;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_LogProfessores = "
SELECT login_professor_id, login_professor_id_professor, login_professor_data_hora, 
vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_escola, func_id, func_nome, func_email 
FROM smc_login_professor
INNER JOIN smc_vinculo ON vinculo_id_funcionario = login_professor_id_professor
INNER JOIN smc_func ON func_id = login_professor_id_professor
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
GROUP BY login_professor_data_hora 
ORDER BY login_professor_id DESC";
$query_limit_LogProfessores = sprintf("%s LIMIT %d, %d", $query_LogProfessores, $startRow_LogProfessores, $maxRows_LogProfessores);
$LogProfessores = mysql_query($query_limit_LogProfessores, $SmecelNovo) or die(mysql_error());
$row_LogProfessores = mysql_fetch_assoc($LogProfessores);

if (isset($_GET['totalRows_LogProfessores'])) {
  $totalRows_LogProfessores = $_GET['totalRows_LogProfessores'];
} else {
  $all_LogProfessores = mysql_query($query_LogProfessores);
  $totalRows_LogProfessores = mysql_num_rows($all_LogProfessores);
}
$totalPages_LogProfessores = ceil($totalRows_LogProfessores/$maxRows_LogProfessores)-1;
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
 
        <h1 class="ls-title-intro ls-ico-home">Registro de acesso ao Painel do Professor</h1>
		<!-- CONTEÚDO -->
        <?php if ($totalRows_LogProfessores > 0) { // Show if recordset not empty ?>
  <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center">
		
				  	<div class="row">
			<div class="col-md-1 col-sm-12">

			</div>
			<div class="col-md-3 col-sm-12">
DATA/HORA
			</div>
			<div class="col-md-4 col-sm-12">
PROFESSOR
			</div>
			<div class="col-md-4 col-sm-12">
E-MAIL
			</div>
		  </div>
		
		</th>
        </tr>
    </thead>
    <tbody>
      <?php $num = 1; ?>
      <?php do { ?>
        <tr>
          <td class="ls-txt-center1">
		  
		  	<div class="row">
			<div class="col-md-1 col-sm-12">
<?php echo $row_LogProfessores['login_professor_id']; ?>
			</div>
			<div class="col-md-3 col-sm-12">
<?php echo date('d/m/Y à\s H\hi ', strtotime($row_LogProfessores['login_professor_data_hora'])); ?>
			</div>
			<div class="col-md-4 col-sm-12">
<strong><?php echo $row_LogProfessores['func_nome']; ?></strong>
			</div>
			<div class="col-md-4 col-sm-12">
<?php echo $row_LogProfessores['func_email']; ?>
			</div>
		  </div>
		  
		  </td>

        </tr>
        <?php } while ($row_LogProfessores = mysql_fetch_assoc($LogProfessores)); ?>
    </tbody>
  </table>
  
  <hr>
      <table width="100%">
    <tr>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LogProfessores > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_LogProfessores=%d%s", $currentPage, 0, $queryString_LogProfessores); ?>">PRIMEIRA</a>
          <?php } // Show if not first page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LogProfessores > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_LogProfessores=%d%s", $currentPage, max(0, $pageNum_LogProfessores - 1), $queryString_LogProfessores); ?>">ANTERIOR</a>
          <?php } // Show if not first page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LogProfessores < $totalPages_LogProfessores) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_LogProfessores=%d%s", $currentPage, min($totalPages_LogProfessores, $pageNum_LogProfessores + 1), $queryString_LogProfessores); ?>">PRÓXIMA</a>
          <?php } // Show if not last page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LogProfessores < $totalPages_LogProfessores) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_LogProfessores=%d%s", $currentPage, $totalPages_LogProfessores, $queryString_LogProfessores); ?>">ÚLTIMA</a>
          <?php } // Show if not last page ?></td>
    </tr>
  </table>
  <hr>
  
  <?php } else { ?>
  
  Nenhum login registrado no Painel o Professor. 
  
  <?php } // Show if recordset not empty ?>
  
  
  


  
  
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

mysql_free_result($LogProfessores);
?>
