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

$maxRows_ListaAvisos = 200;
$pageNum_ListaAvisos = 0;
if (isset($_GET['pageNum_ListaAvisos'])) {
  $pageNum_ListaAvisos = $_GET['pageNum_ListaAvisos'];
}
$startRow_ListaAvisos = $pageNum_ListaAvisos * $maxRows_ListaAvisos;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaAvisos = "
SELECT aviso_turma_id, aviso_turma_id_turma, aviso_turma_id_escola, aviso_turma_data, date_format(aviso_turma_data, '%d/%m/%Y') as data, aviso_turma_texto, turma_id, turma_nome
FROM smc_aviso_turma 
LEFT JOIN smc_turma ON turma_id = aviso_turma_id_turma 
WHERE aviso_turma_id_escola = '$row_UsuLogado[usu_escola]'
ORDER BY aviso_turma_id DESC";
$query_limit_ListaAvisos = sprintf("%s LIMIT %d, %d", $query_ListaAvisos, $startRow_ListaAvisos, $maxRows_ListaAvisos);
$ListaAvisos = mysql_query($query_limit_ListaAvisos, $SmecelNovo) or die(mysql_error());
$row_ListaAvisos = mysql_fetch_assoc($ListaAvisos);

if (isset($_GET['totalRows_ListaAvisos'])) {
  $totalRows_ListaAvisos = $_GET['totalRows_ListaAvisos'];
} else {
  $all_ListaAvisos = mysql_query($query_ListaAvisos);
  $totalRows_ListaAvisos = mysql_num_rows($all_ListaAvisos);
}
$totalPages_ListaAvisos = ceil($totalRows_ListaAvisos/$maxRows_ListaAvisos)-1;

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
 
    <h1 class="ls-title-intro ls-ico-home">AVISOS</h1>
    <!-- CONTEÚDO --> 

              <?php if (isset($_GET["cadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <span class="ls-ico-checkmark-circle"> AVISO CADASTRADO COM SUCESSO.</span>
                </div>
              <?php } ?>

              <?php if (isset($_GET["editado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <span class="ls-ico-checkmark-circle"> AVISO EDITADO E SALVO COM SUCESSO.</span>
                </div>
              <?php } ?>

              <?php if (isset($_GET["nada"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <span class="ls-ico-cancel-circle"> ALGO ERRADO ACONTECEU. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.</span>
                </div>
              <?php } ?>


    <a href="aviso_turma_cadastrar.php" class="ls-btn-primary">EMITIR NOVO AVISO</a>
    
    
    <?php if ($totalRows_ListaAvisos > 0) { // Show if recordset not empty ?>
      <table class="ls-table">
        <thead>
          <tr>
            <th><?php echo $totalRows_ListaAvisos; ?> AVISOS</th>
            <th width="100"></th>
          </tr>
        </thead>
        <tbody>
          <?php 
		  do { ?>
            <tr>
              <td><strong>Turma:</strong> <?php if ($row_ListaAvisos['aviso_turma_id_turma']<>0) { echo $row_ListaAvisos['turma_nome']; } else { echo "TODAS"; } ?><br><strong>Data: </strong><?php echo $row_ListaAvisos['data']; ?>
                <p><?php echo $row_ListaAvisos['aviso_turma_texto']; ?></p></td>
                
             <td><a href="aviso_turma_editar.php?codigo=<?php echo $row_ListaAvisos['aviso_turma_id']; ?>" class="ls-ico-pencil2">Editar</a></td>   
             </tr>
            <?php } while ($row_ListaAvisos = mysql_fetch_assoc($ListaAvisos)); ?>
        </tbody>
      </table>
      <?php } else { ?>
      <hr>
      <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum aviso cadastrado.</div>
      <?php } // Show if recordset not empty ?>
    <!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($ListaAvisos);

mysql_free_result($EscolaLogada);
?>
