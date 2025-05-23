<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/alunosConta.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php include "fnc/session.php"; ?>
<?php
$data = $_COOKIE['data']; // Fulano
$dataBase = $_COOKIE['dataBase']; // Fulano
$diasemana_numero = $_COOKIE['diasemana_numero']; // Fulano
$dia_semana_nome = $_COOKIE['dia_semana_nome']; // Fulano
//initialize the session

?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasListar = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_nome, etapa_limite_turma FROM smc_turma INNER JOIN smc_etapa ON etapa_id = turma_etapa WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);



if ($totalRows_TurmasListar == "") {
	//echo "TODOS OS CAMPOS EM BRANCO";	
	header("Location: turmaCadastrar.php?nova"); 
 	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaAlunos = "SELECT turma_id, turma_id_escola, sum(turma_total_alunos) as totalAlunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$ContaAlunos = mysql_query($query_ContaAlunos, $SmecelNovo) or die(mysql_error());
$row_ContaAlunos = mysql_fetch_assoc($ContaAlunos);
$totalRows_ContaAlunos = mysql_num_rows($ContaAlunos);

/*
$diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');

if ((isset($_POST["MM_busca"])) && ($_POST["MM_busca"] == "form2")) {

$data = $_POST['data'];
$dataBase = inverteData($_POST['data']);
$diasemana_numero = date('w', strtotime($dataBase));
$_SESSION['data'] = $dataBase;
$_SESSION['dia_semana'] = $diasemana_numero;
$_SESSION['dia_semana_nome'] = $diasemana[$diasemana_numero];

setcookie('data', $dataBase);

} else {
	
	  $data = date("d/m/Y");
	  $dataBase = date("Y-m-d");
	  $diasemana_numero = date('w', strtotime($dataBase));
	  $_SESSION['data'] = $dataBase;
	  $_SESSION['dia_semana'] = $diasemana_numero;
	  $_SESSION['dia_semana_nome'] = $diasemana[$diasemana_numero];

	
	if (!isset($_COOKIE['data'])) {
      setcookie('data', $dataBase);	  
    } 
	
	
}


if (!isset($_SESSION['data'])) {
  $insertGoTo = "definirData.php";	
  header(sprintf("Location: %s", $insertGoTo));
}

*/



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
    <h1 class="ls-title-intro ls-ico-calendar">FREQUÊNCIA</h1>
    <?php if (isset($_GET["turmaexcluida"])) { ?>
      <p>
      <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> TURMA EXCLUIDA COM SUCESSO. </div>
      </p>
      <?php } ?>
    <?php if (isset($_GET["nada"])) { ?>
      <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA. </div>
      <?php } ?>
    <?php if (isset($_GET["permissao"])) { ?>
      <div class="ls-alert-danger ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO. </div>
      <?php } ?>
    <?php if (isset($_GET["editada"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> TURMA EDITADA COM SUCESSO. </div>
      <?php } ?>
    <?php if (isset($_GET["dataDefinida"])) { ?>
      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Data para lançamento de faltas definida para <strong><?php echo $dia_semana_nome; ?>, <?php echo inverteData($data); ?></strong> </div>
      <?php } ?>
    <div class="ls-box ls-lg-space">
      <h2><strong><?php echo $dia_semana_nome; ?></strong> - <?php echo inverteData($data); ?></h2>
    </div>
    <div class="ls-box">
      <div class="col-sm-12">
        <p>Escolha uma data para lançamento das faltas:</p>
        <form action="definirData.php" class="ls-form ls-form-inline row" data-ls-module="form" method="post">
          <label class="ls-label col-md-8 col-sm-12">
          <div class="ls-prefix-group"> <b class="ls-label-text">Data</b>
            <input type="date" name="data" class="1datepicker" id="datepicker" placeholder="dd/mm/aaaa" autocomplete="off" required>
            <a class="ls-label-text-prefix ls-ico-calendar" data-trigger-calendar="#datepicker" href="#"></a> </div>
          </label>
          <label class="ls-label col-md-4 col-sm-12">
            <input type="submit" value="DEFINIR DATA" class="ls-btn">
          </label>
          <input type="hidden" name="MM_busca" value="form2">
        </form>
      </div>
    </div>
    <div class="ls-box">
      <div class="col-sm-12">
        <p>Escolha uma turma para exibir os alunos:</p>
        <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-primary">RELAÇÃO DE TURMAS</a>
          <ul class="ls-dropdown-nav">
            <?php do { ?>
              <li><a href="alunosFrequencia.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>"><?php echo $row_TurmasListar['turma_nome']; ?></a></li>
              <?php } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar)); ?>
          </ul>
        </div>
      </div>
    </div>
    <p>Total de turmas: <?php echo $totalRows_TurmasListar; ?></p>
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
      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="js/pikaday.js"></script> 
<script>
	//locastyle.modal.open("#myAwesomeModal");
	locastyle.datepicker.newDatepicker('#dataInicio, #datepicker1, #datepicker2');
	</script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($TurmasListar);

mysql_free_result($ContaAlunos);
?>
