<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/notas.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include "fnc/dataLocal.php"; ?>


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

$row_AnoLetivo['ano_letivo_ano'] = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
  $row_AnoLetivo['ano_letivo_ano'] = $_GET['ano'];
}


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


$codTurma = "";
$buscaTurma = "";
if (isset($_GET['turma'])) {
	
	if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['turma']);
  $codTurma = (int)$codTurma;
  $buscaTurma = " AND turma_id = $codTurma ";
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT 
turma_id, turma_id_escola, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_matriz_id,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_turma 
WHERE turma_etapa NOT IN (1,2,3) AND turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

if ($totalRows_Turmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?nada"); 
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

<title>RESULTADOS FINAIS - ANO LETIVO <?php echo $row_Turmas['turma_ano_letivo']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"> 
<script src="js/locastyle.js"></script>
	<style>
	table.bordasimples {border-collapse: collapse; font-size:7px; }
	table.bordasimples tr td {border:1px solid #808080; padding:2px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:2px; font-size:16px;}
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
<body onload="self.print();">




    <!-- CONTEÚDO -->
	
	

    



    <?php do { ?>
	
    <?php

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turmas[turma_matriz_id]'";
	$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
	$row_Matriz = mysql_fetch_assoc($Matriz);
	$totalRows_Matriz = mysql_num_rows($Matriz);
	
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
	$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
	$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
	$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);

//	  include('fnc/notas.php');

?>

	<?php 
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Matriculas = "
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
	vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao,
	CASE vinculo_aluno_situacao
	WHEN 1 THEN 'MATRICULADO'
	WHEN 2 THEN 'TRANSFERIDO'
	WHEN 3 THEN 'DESISTENTE'
	WHEN 4 THEN 'FALECIDO'
	WHEN 5 THEN 'OUTROS'
	END AS vinculo_aluno_situacao_nome,
	vinculo_aluno_datatransferencia, aluno_id, aluno_nome, aluno_nascimento 
	FROM smc_vinculo_aluno 
	INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
	WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_id_turma = $row_Turmas[turma_id] ORDER BY aluno_nome ASC";
	$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
	$row_Matriculas = mysql_fetch_assoc($Matriculas);
	$totalRows_Matriculas = mysql_num_rows($Matriculas);
	?>
	
	<?php if ($totalRows_Matriculas > 0) { ?>
	
	<div style="page-break-inside: avoid;">
	
	<div class="ls-box ls-box ls-txt-center">
	
	<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
	
	<p><h2 class="ls-txt-center">RESULTADOS FINAIS - ANO LETIVO <?php echo $row_Turmas['turma_ano_letivo']; ?></h2></p>
	<p><h3 class="ls-txt-center"><?php echo $row_Turmas['turma_nome']; ?>-<?php echo $row_Turmas['turma_turno_nome']; ?></h3></p>

	
	  
    
	
      <table width="100%" class="ls-sm-space ls-table-striped bordasimples">	
	    <thead>
          <tr>
            <th width="10px">Nº</th>
			<th width="350px" align="center">ALUNO(A)</th>
            <th width="25px" align="center">NASCIMENTO</th>
			<th align="center">RESULTADO</th>
          </tr>
        </thead>
        <tbody>
		  <?php 
		  
		  $contaAlunos = 1; 
		  $aprovados = 0;
		  $reprovados = 0;
		  
		  ?>
          <?php do { ?>
            <tr>
			  <td align="center" width="25px"><?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?>
			  </td>	
              <td align="left"><?php echo $row_Matriculas['aluno_nome']; ?> <span class="right" style="float:right"><?php if ($row_Matriculas['vinculo_aluno_situacao']<>1) { echo $row_Matriculas['vinculo_aluno_situacao_nome']; } ?></span></td>
              <td align="center"><?php echo inverteData($row_Matriculas['aluno_nascimento']); ?></td>
              <td>
			  
			  <?php
					  mysql_select_db($database_SmecelNovo, $SmecelNovo);
					  $query_Notas = "SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho, disciplina_id, disciplina_nome, disciplina_ordem FROM smc_boletim_disciplinas INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina WHERE boletim_id_vinculo_aluno = $row_Matriculas[vinculo_aluno_id] ORDER BY disciplina_ordem ASC";
					  $Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
					  $row_Notas = mysql_fetch_assoc($Notas);
					  $totalRows_Notas = mysql_num_rows($Notas);
					?>
                
				
				
				
				<?php if ($row_Matriculas['vinculo_aluno_situacao'] == "1") { ?>
				
				
				
				<?php 
				 $apr = 0;
				 $rep = 0;
				 $con = 0;
				?>

				<?php do { ?>
				
				  
				  
				  <div style="display:none">
				  <?php echo $row_Notas['disciplina_nome']; ?>
				  <?php $mv1 = mediaUnidade($row_Notas['boletim_1v1'],$row_Notas['boletim_2v1'],$row_Notas['boletim_3v1'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv2 = mediaUnidade($row_Notas['boletim_1v2'],$row_Notas['boletim_2v2'],$row_Notas['boletim_3v2'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv3 = mediaUnidade($row_Notas['boletim_1v3'],$row_Notas['boletim_2v3'],$row_Notas['boletim_3v3'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv4 = mediaUnidade($row_Notas['boletim_1v4'],$row_Notas['boletim_2v4'],$row_Notas['boletim_3v4'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?>
				  <?php $af = avaliacaoFinal($row_Notas['boletim_af'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']); ?>
				  </div>
				  
				  
				  <?php 
				  
				  //$mc = arredonda(mediaCurso($tp));
				  $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
				  
				  
						  if ($mc >= (float)$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']) {
							  
							  //echo $mc;
							  //echo "-";
							  //$apr++;
							  
							  
						  } else {
							  
							  //echo $af = avaliacaoFinal($row_Notas['boletim_af']);
							  //echo "<span style='color:red;'>REC</span>";
							  
							  if ($af < (float)$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']) {
							  
							  $rep = $rep+1;
							  
							  }
							  
						  }
				  
				  if ($row_Notas['boletim_conselho']=="1") {
					$con = $con+1;  
				  }
				  
				  ?>
				  
			
				  
				  
				  
				  

				  
                  <?php } while ($row_Notas = mysql_fetch_assoc($Notas)); ?>
				  
				  
  
				<?php
				
				
				
				$aprovados++;
				if ($rep > 0) {
					  $reprovados++;
					  echo "<span style='color:red;'>CONSERVADO</span>";
				} else {
					echo "APROVADO";
				}
				
				if ($con > 0) {
					echo " PELO CONSELHO";
				}
				
				?>
				
				  
				
				
			
				
				
				<?php } else { ?>
				
				<div align="center">-</div>
				
				<?php } ?>
				
				
				
				  
				  
				  </td>
            </tr>
            <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
			
				
			
        </tbody>
      </table>
	
	<br>
	<p>
	
	Aprovados: <?php echo $aprovados - $reprovados; ?> - Conservados: <?php echo $reprovados; ?> 


	</p>
		<small></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL - Sistema de Gestão Escolar</i></small>

	</div>

	</div>
	
	<?php } ?>
	
	<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
	  
	  
    
    
    <!-- CONTEÚDO --> 



<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($Notas);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);
?>
