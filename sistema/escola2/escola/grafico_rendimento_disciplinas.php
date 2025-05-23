<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('fnc/inverteData.php'); ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$unidade = "";
if (isset($_GET['unidade'])) {
	
	if ($_GET['unidade'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $unidade = anti_injection($_GET['unidade']);
  $unidade = (int)$unidade;

}



$colname_Turma = "";
if (isset($_GET['turma'])) {
	
	if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $colname_Turma = anti_injection($_GET['turma']);
  $colname_Turma = (int)$colname_Turma;
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ($totalRows_Turma < 1) {
		
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?semdados"); 
 	exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaVinculos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada 
FROM smc_vinculo_aluno
WHERE vinculo_aluno_id_turma = '$colname_Turma' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1'";
$ContaVinculos = mysql_query($query_ContaVinculos, $SmecelNovo) or die(mysql_error());
$row_ContaVinculos = mysql_fetch_assoc($ContaVinculos);
$totalRows_ContaVinculos = mysql_num_rows($ContaVinculos);

if ($totalRows_ContaVinculos < 1) {
		
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?semdados"); 
 	exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

	  include('fnc/notas.php');
	  $av1 = "AV1";
	  $av2 = "AV2";
	  $av3 = "AV3";
	  $av1_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av2_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $av3_max = $row_CriteriosAvaliativos['ca_nota_max_av'];
	  $cancelaLink = "";


if ($totalRows_Matriz < 1) {
		
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?semdados"); 
 	exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz_disciplinas = "SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome FROM smc_matriz_disciplinas INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina WHERE matriz_disciplina_id_matriz = $row_Matriz[matriz_id]";
$Matriz_disciplinas = mysql_query($query_Matriz_disciplinas, $SmecelNovo) or die(mysql_error());
$row_Matriz_disciplinas = mysql_fetch_assoc($Matriz_disciplinas);
$totalRows_Matriz_disciplinas = mysql_num_rows($Matriz_disciplinas);

if ($totalRows_Matriz_disciplinas < 1) {
		
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?semdados"); 
 	exit;
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<style>
	table.bordasimples {border-collapse: collapse; font-size:7px; }
	table.bordasimples tr td {border:1px solid #808080; padding:2px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:2px; font-size:9px;}
	.foo { 

 	writing-mode: vertical-lr;
	 -webkit-writing-mode: vertical-lr;
	 -ms-writing-mode: vertical-lr;

/* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */
	
  }
</style>

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
 
        <h1 class="ls-title-intro ls-ico-home">GRÁFICO DE RENDIMENTO</h1>
		<!-- CONTEÚDO -->
<p><a href="turmaListar.php" class="ls-btn-primary">VOLTAR</a>		
<a href="print_grafico_rendimento_disciplinas.php?turma=<?php echo $colname_Turma; ?>&unidade=<?php echo $unidade; ?>" target="_blank" class="ls-btn-primary">IMPRIMIR</a></p>		
	
<div class="ls-box">
<div class="col-sm-6">
<p><h3>RENDIMENTO POR DISCIPLINA</h3></p>
<p><h4><?php echo $row_Turma['turma_nome']; ?> - <?php echo $unidade; ?>ª UNIDADE</h4></p>
</div>

<div class="col-sm-6">

<div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary"><?php echo $unidade; ?>ª UNIDADE</a>

  <ul class="ls-dropdown-nav">
      <li><a href="grafico_rendimento_disciplinas.php?turma=<?php echo $colname_Turma; ?>&unidade=1">1ª UNIDADE</a></li>
      <li><a href="grafico_rendimento_disciplinas.php?turma=<?php echo $colname_Turma; ?>&unidade=2">2ª UNIDADE</a></li>
      <li><a href="grafico_rendimento_disciplinas.php?turma=<?php echo $colname_Turma; ?>&unidade=3">3ª UNIDADE</a></li>
      <li><a href="grafico_rendimento_disciplinas.php?turma=<?php echo $colname_Turma; ?>&unidade=4">4ª UNIDADE</a></li>
  </ul>
</div>

</div>

</div>

<div class="ls-box">
<?php do { ?>


<?php

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = "SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada
FROM smc_vinculo_aluno 
WHERE vinculo_aluno_id_turma = '$row_Turma[turma_id]'";
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

?>

<?php 
$aprovado = 0;
$reprovado = 0;
?>
  
  <?php do { ?>

      <span style="display:none;">
	  <?php 
	  
	  ?>
      </span>
      
      <p>
      <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
      <?php 
		 
		 
		 
		 
		 
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_nota = "
		SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash 
		FROM smc_nota 
		WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' 
		AND nota_id_disciplina = '$row_Matriz_disciplinas[disciplina_id]'
		AND nota_num_avaliacao = '$a'
		AND nota_periodo = '$unidade'
		
		";
		$nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
		$row_nota = mysql_fetch_assoc($nota);
		$totalRows_nota = mysql_num_rows($nota);
		
		//echo exibeTraco($row_nota['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']);
		//$ru = $ru + $row_nota['nota_valor'];
		 
		 
		 echo $row_nota['nota_valor']."-";
		 

	  ?>
      <?php } ?>
      </p>

      
        
  <?php } while ($row_matricula = mysql_fetch_assoc($matricula)); ?>
  
  <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Situação', 'Percentual'],
          ['Na média (<?php echo $aprovado; ?>)', <?php echo $aprovado; ?>],
          ['Abaixo (<?php echo $reprovado; ?>)', <?php echo $reprovado; ?>]
        ]);

        var options = {
          title: '<?php echo $row_Matriz_disciplinas['disciplina_nome']; ?>',
		  legend: {position: 'bottom'}
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_<?php echo $row_Matriz_disciplinas['disciplina_id']; ?>'));

        chart.draw(data, options);
      }
    </script>
  
 

 <div class="col-sm-3 ls-txt-center" style="border:#CCC solid 1px; padding:3px;page-break-inside: avoid;">
 	<div id="piechart_<?php echo $row_Matriz_disciplinas['disciplina_id']; ?>" style="width:400; height:300"></div>
 </div> 

<?php } while ($row_Matriz_disciplinas = mysql_fetch_assoc($Matriz_disciplinas)); ?>
		
</div>		
		
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

mysql_free_result($ContaVinculos);

mysql_free_result($EscolaLogada);
?>