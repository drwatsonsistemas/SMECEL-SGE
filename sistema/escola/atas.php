<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/anti_injection.php"; ?>

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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
	
  if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada"); 
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int)$anoLetivo;
}





mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer,
matriz_id, matriz_criterio_avaliativo, ca_id, ca_qtd_periodos, ca_questionario_conceitos, ca_forma_avaliacao,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VESP'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome 
FROM smc_turma
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$anoLetivo'
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

$link = "print_resultados_atas.php";	
$linkUnidade = "print_resultados_atas_unidade.php";	
$linkUnificado = "print_resultados_atas_unificado.php";

switch ($anoLetivo) {
	
	case "2018";
	case "2019";
	$link = "print_atas_finais.php";
	break;
	
	default:
	$link = "print_resultados_atas.php";	
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

      <h1 class="ls-title-intro ls-ico-home">Relatório de Atas e Resultados Finais - <?php echo $anoLetivo; ?></h1>
      <!-- CONTEÚDO -->



      <div data-ls-module="dropdown" class="ls-dropdown">
        <a href="#" class="ls-btn">ESCOLHA O ANO</a>
        <ul class="ls-dropdown-nav">


          <?php do { ?>

            <li><a href="atas.php?ano=<?php echo $row_Ano['ano_letivo_ano']; ?>" target="_blank" title="Gerar Ata de Resultados Finais">ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?></a></li>

          <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>



        </ul>
      </div>

      <a href="print_resultados_atas_6.php?ano=<?php echo $anoLetivo; ?>" target="_blank" class="ls-btn 1ls-float-right">Total de alunos aprovados por média em <?php echo $anoLetivo; ?></a>


      <table class="ls-table ls-sm-space ls-table-striped ">
        <tr>
        	<th width="50" class="ls-txt-center">Nº</th>
        	<th class="ls-txt-left">TURMA</th>
        	<th width="100" class="ls-txt-center hidden-xs">TURNO</th>
        	<th class="ls-txt-center hidden-xs">ATA POR UNIDADE</th>
          <th width="100" class="ls-txt-center">ATAS FINAIS UNIFICADAS</th>
          <th width="100" class="ls-txt-center">ATAS FINAIS</th>
          <th width="100" class="ls-txt-center hidden-xs">RESULTADOS FINAIS</th>
        </tr>
        
        <?php 
        $num = 1;
        do { ?>
          <?php

          if($row_Turmas['ca_forma_avaliacao'] == "Q"){
            $linkUnidade = "print_resultados_atas_unidade_qq.php";
            $linkUnificado = "print_resultados_atas_unificado_qq.php"; 
          }
          ?>
          <tr>
           <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
           <td class="ls-txt-left"><?php echo $row_Turmas['turma_nome']; ?></td>
           <td class="ls-txt-center hidden-xs"><?php echo $row_Turmas['turma_turno_nome']; ?></td>
           <td class="ls-txt-center hidden-xs">

            <?php for ($p = 1; $p <= $row_Turmas['ca_qtd_periodos']; $p++) { ?>

              <a href="<?php echo $linkUnidade; ?>?ct=<?php echo $row_Turmas['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>&unidade=<?php echo $p; ?>" class="ls-btn ls-btn-xs" target="_blank"><?php echo $p; ?>ª</a> 
            <?php } ?>

            
          </td>
          <?php 
          if ($row_Turmas['ca_questionario_conceitos'] == "S") {
            
            $link = "print_resultados_atas_conceitos.php";
          } else {
            if($row_Turmas['ca_forma_avaliacao'] == "Q"){
              $link = "print_resultados_atas_qq.php";
              $linkAtasConservados = "print_resultados_conservados_qq.php";
            }else{
              $link = "print_resultados_atas.php";
              $linkAtasConservados = "print_resultados_conservados.php";
            }
            
          }
          ?>
          <td class="ls-txt-center"><a href="<?= $linkUnificado ?>?ct=<?php echo $row_Turmas['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>" target="_blank" class="ls-btn ls-btn-xs"><span class="ls-ico-text"></span></a></td>
          <td class="ls-txt-center"><a href="<?php echo $link; ?>?ct=<?php echo $row_Turmas['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>" target="_blank" class="ls-btn ls-btn-xs"><span class="ls-ico-text"></span></a></td>
          <td class="ls-txt-center hidden-xs"><a href="<?= $linkAtasConservados ?>?ct=<?php echo $row_Turmas['turma_id']; ?>&ano=<?php echo $anoLetivo; ?>" target="_blank" class="ls-btn ls-btn-xs"><span class="ls-ico-text2"></span></a></td>
        </tr>
      <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
    </table>  

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

mysql_free_result($Turmas);

mysql_free_result($EscolaLogada);
?>
