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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"> 
<script src="js/locastyle.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>


</style>

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body onload="1self.print();">







		<!-- CONTEÚDO -->

		<div class="ls-txt-center">
		<p class="ls-txt-center">
		<h2><?php echo $row_Turma['turma_nome']; ?> - <?php echo $unidade; ?>ª UNIDADE</h2>
		<h4>RENDIMENTO POR DISCIPLINA<br><?php echo $row_EscolaLogada['escola_nome']; ?></h4>
		</p>
		</div>



  
<?php do { ?>


<?php

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Boletim = "
SELECT 
boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, 
boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia 
FROM smc_boletim_disciplinas INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno WHERE boletim_id_disciplina = $row_Matriz_disciplinas[matriz_disciplina_id_disciplina] AND vinculo_aluno_id_turma = '$colname_Turma' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1'";
$Boletim = mysql_query($query_Boletim, $SmecelNovo) or die(mysql_error());
$row_Boletim = mysql_fetch_assoc($Boletim);
$totalRows_Boletim = mysql_num_rows($Boletim);

if ($totalRows_Boletim < 1) {
		
	//echo "TURMA EM BRANCO";	
	//header("Location: turmaListar.php?semdados"); 
 	//exit;
}

?>

<?php 
$aprovado = 0;
$reprovado = 0;
?>
  
  <?php do { ?>

      <span style="display:none;"><?php $mv1 = mediaUnidade($row_Boletim['boletim_1v'.$unidade.''],$row_Boletim['boletim_2v'.$unidade.''],$row_Boletim['boletim_3v'.$unidade.''],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?></span>
      
      <?php 
		if($mv1 < (float)$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']) {
	    $reprovado = $reprovado+1;
			
	 	} else {
		$aprovado = $aprovado+1;		
			
	  	}; 
	  ?>
  
  <?php } while ($row_Boletim = mysql_fetch_assoc($Boletim)); ?>
  
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
  
 
	

 
 	
	<div style="width:300px; height:190px; float:left; text-align: center; display:block;">
		<div id="piechart_<?php echo $row_Matriz_disciplinas['disciplina_id']; ?>" style="text-align: center; float:left; margin-left:5px; display:block;"></div>
	</div>
	

 
 

<?php } while ($row_Matriz_disciplinas = mysql_fetch_assoc($Matriz_disciplinas)); ?>
		
		
	
		<!-- CONTEÚDO -->



    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	
	<script type='text/javascript'>

 

$(document).ready(function(){
   
		
    setTimeout('window.print()', 3000);
 
 });



</script>


	
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
