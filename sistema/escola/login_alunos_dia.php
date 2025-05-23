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

$currentPage = $_SERVER["PHP_SELF"];

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

$maxRows_LoginAlunos = 100;
$pageNum_LoginAlunos = 0;
if (isset($_GET['pageNum_LoginAlunos'])) {
  $pageNum_LoginAlunos = $_GET['pageNum_LoginAlunos'];
}
$startRow_LoginAlunos = $pageNum_LoginAlunos * $maxRows_LoginAlunos;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_LoginAlunos = "
SELECT login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip,
vinculo_aluno_id_aluno, vinculo_aluno_id_escola, vinculo_aluno_id_turma, vinculo_aluno_ano_letivo, aluno_id, aluno_nome, turma_id, 
turma_nome, turma_turno, DATE_FORMAT(login_aluno_data_hora, '%d/%m/%Y') AS nova_data, COUNT(*) AS total, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_login_aluno
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id_aluno = login_aluno_id_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY nova_data
ORDER BY login_aluno_id DESC
";
$query_limit_LoginAlunos = sprintf("%s LIMIT %d, %d", $query_LoginAlunos, $startRow_LoginAlunos, $maxRows_LoginAlunos);
$LoginAlunos = mysql_query($query_limit_LoginAlunos, $SmecelNovo) or die(mysql_error());
$row_LoginAlunos = mysql_fetch_assoc($LoginAlunos);

if (isset($_GET['totalRows_LoginAlunos'])) {
  $totalRows_LoginAlunos = $_GET['totalRows_LoginAlunos'];
} else {
  $all_LoginAlunos = mysql_query($query_LoginAlunos);
  $totalRows_LoginAlunos = mysql_num_rows($all_LoginAlunos);
}
$totalPages_LoginAlunos = ceil($totalRows_LoginAlunos/$maxRows_LoginAlunos)-1;

$queryString_LoginAlunos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_LoginAlunos") == false && 
        stristr($param, "totalRows_LoginAlunos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_LoginAlunos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_LoginAlunos = sprintf("&totalRows_LoginAlunos=%d%s", $totalRows_LoginAlunos, $queryString_LoginAlunos);
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
 
        <h1 class="ls-title-intro ls-ico-home">Login aluno por dia</h1>
		<!-- CONTEÚDO -->
        
  <?php if ($totalRows_LoginAlunos > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <thead>
      <tr>
        <th class="ls-txt-center">
		
		  <div class="row">
			<div class="col-md-6 col-sm-6 ls-txt-center">
DATA
			</div>
			<div class="col-md-6 col-sm-6 ls-txt-center">
TOTAL DE ACESSOS
			</div>
		  </div>
		
		</th>
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td>
		  
		  <div class="row">
			<div class="col-md-6 col-sm-6 ls-txt-center">
			<?php echo $row_LoginAlunos['nova_data']; ?>
			</div>
			<div class="col-md-6 col-sm-6 ls-txt-center">
			<strong><?php echo $row_LoginAlunos['total']; ?></strong>
			</div>
		  </div>
		  
		  
        </tr>
        <?php } while ($row_LoginAlunos = mysql_fetch_assoc($LoginAlunos)); ?>
    </tbody>
  </table>

<hr>

  <table width="100%">
    <tr>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LoginAlunos > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_LoginAlunos=%d%s", $currentPage, 0, $queryString_LoginAlunos); ?>">PRIMEIRA</a>
          <?php } // Show if not first page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LoginAlunos > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_LoginAlunos=%d%s", $currentPage, max(0, $pageNum_LoginAlunos - 1), $queryString_LoginAlunos); ?>">ANTERIOR</a>
          <?php } // Show if not first page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LoginAlunos < $totalPages_LoginAlunos) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_LoginAlunos=%d%s", $currentPage, min($totalPages_LoginAlunos, $pageNum_LoginAlunos + 1), $queryString_LoginAlunos); ?>">PRÓXIMA</a>
          <?php } // Show if not last page ?></td>
      <td class="ls-txt-center" width="25%"><?php if ($pageNum_LoginAlunos < $totalPages_LoginAlunos) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_LoginAlunos=%d%s", $currentPage, $totalPages_LoginAlunos, $queryString_LoginAlunos); ?>">ÚLTIMA</a>
          <?php } // Show if not last page ?></td>
    </tr>
  </table>


<hr>

  <?php } else { ?>
  
  <p>Nenhum login de aluno registrado.</p>
  
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

mysql_free_result($LoginAlunos);

mysql_free_result($EscolaLogada);
?>
