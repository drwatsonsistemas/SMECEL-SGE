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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
if ($totalRows_EscolaLogada < 1) {
	//echo "ESCOLA PARALIZADA";	
	header("Location: index.php?doLogout=true"); 
 	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasListar = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_nome FROM smc_turma INNER JOIN smc_etapa ON etapa_id = turma_etapa WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY turma_etapa ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Transporte = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia FROM smc_vinculo_aluno WHERE vinculo_aluno_transporte = 'S' AND vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1'  AND vinculo_aluno_dependencia = 'N'";
$Transporte = mysql_query($query_Transporte, $SmecelNovo) or die(mysql_error());
$row_Transporte = mysql_fetch_assoc($Transporte);
$totalRows_Transporte = mysql_num_rows($Transporte);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSituacao = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia, COUNT(vinculo_aluno_id) AS total,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'ATIVOS'
WHEN 2 THEN 'TRANSFERIDOS'
WHEN 3 THEN 'DESISTENTES'
WHEN 4 THEN 'FALECIDOS'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao,
CASE vinculo_aluno_situacao
WHEN 1 THEN ''
WHEN 2 THEN 'green'
WHEN 3 THEN 'red'
WHEN 4 THEN 'silver'
WHEN 5 THEN 'grey'
END AS cor 
FROM smc_vinculo_aluno 
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_dependencia = 'N'
GROUP BY vinculo_aluno_situacao ASC";
$AlunosSituacao = mysql_query($query_AlunosSituacao, $SmecelNovo) or die(mysql_error());
$row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao);
$totalRows_AlunosSituacao = mysql_num_rows($AlunosSituacao);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosTurno = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia,
turma_id, turma_nome, turma_turno, COUNT(turma_turno) AS total,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MANHÃ'
WHEN 2 THEN 'TARDE'
WHEN 3 THEN 'NOITE'
END AS turma_turno 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_situacao = '1' AND vinculo_aluno_dependencia = 'N'
GROUP BY turma_turno ASC
";
$AlunosTurno = mysql_query($query_AlunosTurno, $SmecelNovo) or die(mysql_error());
$row_AlunosTurno = mysql_fetch_assoc($AlunosTurno);
$totalRows_AlunosTurno = mysql_num_rows($AlunosTurno);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarAlunosVincular = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_situacao, vinculo_aluno_dependencia 
FROM smc_vinculo_aluno 
WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND vinculo_aluno_dependencia = 'N'";
$ListarAlunosVincular = mysql_query($query_ListarAlunosVincular, $SmecelNovo) or die(mysql_error());
$row_ListarAlunosVincular = mysql_fetch_assoc($ListarAlunosVincular);
$totalRows_ListarAlunosVincular = mysql_num_rows($ListarAlunosVincular);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, func_id, func_nome, funcao_id, funcao_nome 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosZonaRural = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_localizacao, COUNT(aluno_localizacao) AS total_localizacao, vinculo_aluno_dependencia,
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao_nome  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_dependencia = 'N'
GROUP BY aluno_localizacao
";
$AlunosZonaRural = mysql_query($query_AlunosZonaRural, $SmecelNovo) or die(mysql_error());
$row_AlunosZonaRural = mysql_fetch_assoc($AlunosZonaRural);
$totalRows_AlunosZonaRural = mysql_num_rows($AlunosZonaRural);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSexo = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_sexo, COUNT(aluno_sexo) AS total_sexo, vinculo_aluno_dependencia,
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo_descricao  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND vinculo_aluno_dependencia = 'N'
GROUP BY aluno_sexo
";
$AlunosSexo = mysql_query($query_AlunosSexo, $SmecelNovo) or die(mysql_error());
$row_AlunosSexo = mysql_fetch_assoc($AlunosSexo);
$totalRows_AlunosSexo = mysql_num_rows($AlunosSexo);

if(!isset($_COOKIE['aviso1'])){
	//MARCAR COM "S" PARA EXIBIR ALERTA POPUP
	//$exibeaviso = "S";
	$exibeaviso = "N";
	setcookie('aviso1','S',time()+(3600*2));
} else {
	$exibeaviso = "N";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atualizacoes = "SELECT atualizacoes_id, atualizacoes_painel, atualizacoes_modulo, atualizacoes_texto, atualizacoes_data FROM smc_atualizacoes WHERE (atualizacoes_painel <> '99' AND atualizacoes_painel <> '1') ORDER BY atualizacoes_id DESC LIMIT 0,1";
$Atualizacoes = mysql_query($query_Atualizacoes, $SmecelNovo) or die(mysql_error());
$row_Atualizacoes = mysql_fetch_assoc($Atualizacoes);
$totalRows_Atualizacoes = mysql_num_rows($Atualizacoes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtualizacoesVisualizadas = "SELECT atualizacao_ver_id, atualizacao_ver_cod_atualizacao, atualizacao_ver_cod_usuario, atualizacao_ver_sec, atualizacao_ver_escola, atualizacao_ver_professor, atualizacao_ver_aluno, atualizacao_ver_data FROM smc_atualizacao_ver WHERE atualizacao_ver_cod_atualizacao = '$row_Atualizacoes[atualizacoes_id]' AND atualizacao_ver_sec = '$row_UsuLogado[usu_sec]' AND atualizacao_ver_cod_usuario = '$row_UsuLogado[usu_id]'";
$AtualizacoesVisualizadas = mysql_query($query_AtualizacoesVisualizadas, $SmecelNovo) or die(mysql_error());
$row_AtualizacoesVisualizadas = mysql_fetch_assoc($AtualizacoesVisualizadas);
$totalRows_AtualizacoesVisualizadas = mysql_num_rows($AtualizacoesVisualizadas);

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
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">    <link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
	<script type="text/javascript">

	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        // Create the data table.
        var data = google.visualization.arrayToDataTable([
          ['TURNO', 'TOTAL'],
		<?php do { ?>
		  ['<?php echo $row_AlunosTurno['turma_turno']; ?> (<?php echo $row_AlunosTurno['total']; ?>)', <?php echo $row_AlunosTurno['total']; ?>],
    	<?php } while ($row_AlunosTurno = mysql_fetch_assoc($AlunosTurno)); ?>
        ]);
        var options = {
          title: ''
		  };
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
	  
    </script>
    


	<script type="text/javascript">

	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        // Create the data table.
        var data = google.visualization.arrayToDataTable([
          ['SITUAÇÃO', 'TOTAL'],
		
		<?php do { ?>
		  ["<?php echo $row_AlunosSituacao['vinculo_aluno_situacao']; ?> (<?php echo $row_AlunosSituacao['total']; ?>)", <?php echo $row_AlunosSituacao['total']; ?>],
    	<?php } while ($row_AlunosSituacao = mysql_fetch_assoc($AlunosSituacao)); ?>
		
        ]);
        var options = {
          title: ''
		  };
        var chart = new google.visualization.PieChart(document.getElementById('columnchart_values'));
        chart.draw(data, options);
      }
	  
    </script>

	
  
  
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
		['ZONA', 'TOTAL'],
		
		<?php do { ?>
		['<?php echo $row_AlunosZonaRural['aluno_localizacao_nome']; ?> (<?php echo $row_AlunosZonaRural['total_localizacao']; ?>)', <?php echo $row_AlunosZonaRural['total_localizacao']; ?>],
		<?php } while ($row_AlunosZonaRural = mysql_fetch_assoc($AlunosZonaRural)); ?>

        ]);

        var options = {
          title: ''
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_zona_rural'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
		['GÊNERO', 'TOTAL'],
		
		<?php do { ?>
		['<?php echo $row_AlunosSexo['aluno_sexo_descricao']; ?> (<?php echo $row_AlunosSexo['total_sexo']; ?>)', <?php echo $row_AlunosSexo['total_sexo']; ?>],
		<?php } while ($row_AlunosSexo = mysql_fetch_assoc($AlunosSexo)); ?>

        ]);

        var options = {
          title: ''
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_sexo'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
		
		['Data', 'Matrículas'],
		
		<?php
				$date_fim = date("Y-m-d"); //Data final
				$date_ini = date("Y-m-d", strtotime("-10 days",strtotime($date_fim)));; //Data inicial
				$contaMatriculas = 0;
				
				while (strtotime($date_ini) <= strtotime($date_fim)) {
					
					mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_Matriculas = "
					SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
					vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
					vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
					vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada 
					FROM smc_vinculo_aluno
					WHERE vinculo_aluno_data = '$date_ini' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]'";
					$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
					$row_Matriculas = mysql_fetch_assoc($Matriculas);
					$totalRows_Matriculas = mysql_num_rows($Matriculas);
						
				
				?>
				
				 ['<?php echo date("d/m", strtotime($date_ini)); ?>',  <?php echo $totalRows_Matriculas; ?>],
				
				<?php 
				
				$date_ini = date ("Y-m-d", strtotime("+1 day", strtotime($date_ini)));
				$contaMatriculas = $contaMatriculas + $totalRows_Matriculas;
				}		
		
			?>

		  
		  
        ]);
		
        var options = {
			vAxis: {minValue: 0},
			legend: {position: 'bottom', maxLines: 3},
   		    animation:{
				startup: true,	
				duration: 1000,
				easing: 'linear'
      		}			
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div_matriculas'));
        chart.draw(data, options);
      }
    </script>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
    <body>
    
    <?php include_once ("menu-top.php"); ?>

      <?php include_once ("menu-esc.php"); ?>


      <main class="ls-main">
      <div class="container-fluid">
      


        <h1 class="ls-title-intro ls-ico-home">Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
       
        
        <?php if(($row_UsuLogado['usu_senha']=="123456") || ($row_UsuLogado['usu_senha']=="12345")) { ?>
        <div class="ls-alert-danger"><strong>Atenção!</strong><br>
          Você está usando uma senha de acesso muito simples. Evite senhas como "123456", números de telefone, nomes de pessoas ou outras combinações. <a href="dados.php">Clique aqui para alterar</a></div>
        <?php } ?>
     
  
  <div class="ls-box-filter">

    <form id="form_busca" autocomplete="off" action="redireciona.php" method="get" class="ls-form ls-form-inline row">
	<label class="ls-label col-md-12">
    <b class="ls-label-text">LOCALIZAR ALUNO</b>
	  <input id="inputString" type="text" class="validate" value="" placeholder="DIGITE O NOME DO ALUNO OU O NOME DA MÃE DO ALUNO" onkeyup="lookup(this.value);" onblur="fill();" autofocus />
  </label>
  	<input type="hidden" id="ano" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>" name="ano" />	
	  <input type="hidden" id="campoBusca" value="" name="matricula" />
	  <div class="suggestionsBox" id="suggestions" style="display: none; margin-top:0px; width:100%; margin-top:0px; position: relative; border:none;">
	  <div class="suggestionList" id="autoSuggestionsList"> </div>
	  </div>
    </form>


</div>      
        
        
        
        <div class="ls-box ls-board-box">

          <div id="sending-stats" class="row">
            <div class="col-sm-6 col-md-3">
              <div class="ls-box" style="background-color:#063;">
                <div class="ls-box-head ls-background-primary1">
                  <h6 class="ls-title-4" style="color:#FFFFFF;">FUNCIONÁRIOS VINCULADOS</h6>
                </div>
                <div class="ls-box-body ls-background-primary1"> <strong style="color:#FFFFFF;"><span class="count"><?php echo $totalRows_ListaVinculos ?></span></strong> <small style="color:#FFFFFF;">funcionários vinculados</small> </div>
                <div class="ls-box-footer ls-background-primary1"> <a href="funcListar.php" aria-label="Ver vínculos" class="ls-btn ls-btn-sm" title="Ver vínculos">Ver vínculos</a> </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="ls-box" style="background-color:#903;">
                <div class="ls-box-head ls-background-info1">
                  <h6 class="ls-title-4" style="color:#FFFFFF;">TURMAS CADASTRADAS</h6>
                </div>
                <div class="ls-box-body ls-background-info1"> <strong style="color:#FFFFFF;"><span class="count"><?php echo $totalRows_TurmasListar ?></span></strong> <small style="color:#FFFFFF;">turmas cadastradas</small> </div>
                <div class="ls-box-footer ls-background-info1"> <a href="turmaListar.php" aria-label="Ver turmas" class="ls-btn ls-btn-sm" title="Ver turmas">Ver turmas</a> </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="ls-box" style="background-color:#099;">
                <div class="ls-box-head ls-background-success1">
                  <h6 class="ls-title-4" style="color:#FFFFFF;">ALUNOS</h6>
                </div>
                <div class="ls-box-body ls-background-success1"> <strong style="color:#FFFFFF;"><span class="count"><?php echo $totalRows_ListarAlunosVincular ?></span></strong> <small style="color:#FFFFFF;">matrículas ativas</small> </div>
                <div class="ls-box-footer ls-background-success1"> <a href="vinculoAlunoExibirTurma.php" aria-label="Ver alunos" class="ls-btn ls-btn-sm" title="Ver alunos">Ver alunos</a> </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-3">
              <div class="ls-box" style="background-color:#C90;">
                <div class="ls-box-head ls-background-warning1">
                  <h6 class="ls-title-4" style="color:#FFFFFF;">TRANSPORTE ESCOLAR</h6>
                </div>
                <div class="ls-box-body ls-background-warning1"> <strong style="color:#FFFFFF;"><span class="count"><?php echo $totalRows_Transporte ?></span></strong> <small style="color:#FFFFFF;">alunos que utilizam transporte escolar</small> </div>
                <div class="ls-box-footer ls-background-warning1"> <a href="vinculoAlunoExibirTurma.php" aria-label="Ver alunos" class="ls-btn ls-btn-sm" title="Ver alunos">Ver alunos</a> </div>
              </div>
            </div>
          </div>
        </div>
        <?php if (($totalRows_AlunosSexo == 0) || ($totalRows_AlunosZonaRural == 0) || ($totalRows_AlunosSituacao == 0) || ($totalRows_AlunosTurno == 0)) { ?>
        <?php } else { ?>
        <div class="row">
          <div class="col-md-12">
            <div class="ls-box">
              <h5 class="ls-title-3 ls-txt-center"><?php echo $contaMatriculas; ?> matrículas realizadas nos últimos 10 dias</h5>
              <div id="chart_div_matriculas" style="width: 100%; height: 400px;"></div>
            </div>
          </div>
        </div>
        <p>&nbsp;</p>
        <div class="row">
          <div class="col-md-6">
            <div class="ls-box">
              <h5 class="ls-title-3 ls-txt-center">Matrículas por turno</h5>
              <div id="chart_div"></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="ls-box">
              <h5 class="ls-title-3 ls-txt-center">Matrículas por situação</h5>
              <div id="columnchart_values"></div>
            </div>
          </div>
        </div>
        <p>&nbsp;</p>
        <div class="row">
          <div class="col-md-6">
            <div class="ls-box">
              <h5 class="ls-title-3 ls-txt-center">Matrículas por zona de residência</h5>
              <div id="piechart_zona_rural"></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="ls-box">
              <h5 class="ls-title-3 ls-txt-center">Matrículas por gênero</h5>
              <div id="piechart_sexo"></div>
            </div>
          </div>
        </div>
        <?php } ?>
        <hr>
        <p>&nbsp;</p>
        <div class="ls-box1" style="display:none;">
          <h5 class="ls-title-3">Calendário de eventos</h5>
          <p>
            <iframe src="../../agenda-views.php" style="border:none;" width="100%" height="800px;"></iframe>
          </p>
        </div>
        <hr>
      </div>
    </main>


      <?php include_once ("menu-dir.php"); ?>



    <span data-ls-module="modal" data-target="#censo"></span>
    <div class="ls-modal" id="censo">
      <div class="ls-modal-box">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">CENSO ESCOLAR</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
          <p><strong>Prezado usuário,</strong></p>
          <p>O período para coleta do Censo Escolar já começou.</p>
          <p>Para facilitar o preenchimento das informações no sistema Educacenso, sugere-se que você realize o preenchimento dos cadastros na seguinte ordem: Gestor, Escola, Turma, Aluno e Profissional Escolar. Depois de preencher uma turma, preencha as informações de alunos e profissionais escolares dessa turma.
          <p>No <a href="https://censobasico.inep.gov.br/censobasico/#/" target="_blank">site do Censo Escolar</a>, você encontrará materiais que irão auxiliar na declaração das informações ao Censo.</p>
        </div>
        <div class="ls-modal-footer">
          <button class="ls-btn-primary" data-dismiss="modal">FECHAR</button>
        </div>
      </div>
    </div>
    <!-- /.modal --> 

    <!-- We recommended use jQuery 1.10 or up --> 
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="js/locastyle.js"></script>
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script> 


<script type="text/javascript">
	function lookup(inputString) {
		ano = <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>;
		if(inputString.length == 0) {
			$('#suggestions').hide();
		} else {
			$.post("busca_aluno.php", {queryString: ""+inputString+"", ano: ano}, function(data){
				if(data.length > 5) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	}
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
	
	function exibe(thisValue) {
		$('#campoBusca').val(thisValue);
		$('#inputString').val("Redirecionando...");
		$("#form_busca").submit();
		}
</script>
<script type="text/javascript">
$('html').bind('keypress', function(e) {
   if(e.keyCode == 13) {
      return false;
   }
});
</script>
     
    <script type="text/javascript">

<?php 
if ($exibeaviso == "S" ) {
?>

	locastyle.modal.open("#censo");
	
<?php } ?>
</script>
<script type="text/javascript">
$('.count').each(function () {
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: 6000,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now));
        }
    });
});
</script>

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Transporte);

mysql_free_result($Matriculas);

mysql_free_result($AlunosSituacao);

mysql_free_result($AlunosTurno);

mysql_free_result($ListarAlunosVincular);
?>
