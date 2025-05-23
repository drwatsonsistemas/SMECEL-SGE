<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/notas.php'); ?>
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

$filtraTurno = "";
$buscaTurno = "";
$tituloTurno = "TODAS AS TURMAS";
if (isset($_GET['turno'])) {
	
	if ($_GET['turno'] < 3) {
	//echo "TURMA EM BRANCO";	
	//header("Location: turmasAlunosVinculados.php?nada"); 
 	//exit;
	}
	
  $filtraTurno = anti_injection($_GET['turno']);
  $filtraTurno = (int)$filtraTurno;
	
	switch ($filtraTurno) {
    case 1:
	$buscaTurno = "turma_turno = 1 AND ";
	$tituloTurno = "TURMAS DO TURNO MATUTINO";
        break;
    case 2:
 	$buscaTurno = "turma_turno = 2 AND ";
	$tituloTurno = "TURMAS DO TURNO VESPERTINO";
        break;
    case 3:
 	$buscaTurno = "turma_turno = 3 AND ";
	$tituloTurno = "TURMAS DO TURNO NOTURNO";
        break;
	default:
	$buscaTurno = "";
	$tituloTurno = "TODAS AS TURMAS";
	break;
 }
	

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
WHERE $buscaTurno turma_etapa NOT IN (1,2,3) AND turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma ORDER BY turma_turno, turma_etapa, turma_nome ASC";
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

	<style>
	table.bordasimples {border-collapse: collapse; font-size:7px; }
	table.bordasimples tr td {border:1px solid #808080; padding:2px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:2px; font-size:9px;}
	.foo { 

 	writing-mode: vertical-rl;
	 -webkit-writing-mode: vertical-rl;
	 -ms-writing-mode: vertical-rl;

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
        <h1 class="ls-title-intro ls-ico-home">GRÁFICO DE RENDIMENTOS</h1>
		<!-- CONTEÚDO -->
		
		
 



    <!-- CONTEÚDO -->
	

		  <?php 
		  $contaAprovadosEscola = 0;
		  $contaReprovadosEscola = 0;
		  $contaTransferidosEscola = 0;
		  $contaDesistentesEscola = 0;
		  $contaFalecidosEscola = 0;
		  $contaOutrosEscola = 0;
		  ?>

    <?php do { ?>
    
    
    

    
    
	
	<div style="page-break-inside: avoid; display:none">
	
	<div class="ls-box ls-box ls-txt-center">
	
	<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
	
	<p><h2 class="ls-txt-center">GRÁFICO DE RENDIMENTOS</h2></p>
	<p>
	<div style="text-align:justify; line-height:150%;">
	<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('Aos %d dias do mês de %B do ano de %Y', strtotime($row_AnoLetivo['ano_letivo_fim']));
?> terminou-se o processo de apuração das notas finais e nota global do Ano Letivo de <strong><?php echo $row_Turmas['turma_ano_letivo']; ?></strong> dos alunos da turma <strong><?php echo $row_Turmas['turma_nome']; ?></strong>, turno <strong><?php echo $row_Turmas['turma_turno_nome']; ?></strong>, deste estabelecimento de ensino, com os seguintes resultados:
	<br>
	</div>	
	</p>  
	  
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
	
      <table width="100%" class="ls-sm-space ls-table-striped bordasimples">	
	    <thead>
          <tr>
            <th>Nº</th>
			<th width="40%">NOME DO(A) ALUNO(A)</th>
            <th width="10%" align="center">NASCIMENTO</th>
            <th>
			<table width="100%">
			<tr>
			<?php
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_CabecalhoDisciplinas = "
				SELECT 
				matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_ordem, disciplina_nome, disciplina_nome_abrev 
				FROM smc_matriz_disciplinas
				INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
				WHERE matriz_disciplina_id_matriz = $row_Turmas[turma_matriz_id]	
				ORDER BY disciplina_ordem ASC";
				$CabecalhoDisciplinas = mysql_query($query_CabecalhoDisciplinas, $SmecelNovo) or die(mysql_error());
				$row_CabecalhoDisciplinas = mysql_fetch_assoc($CabecalhoDisciplinas);
				$totalRows_CabecalhoDisciplinas = mysql_num_rows($CabecalhoDisciplinas);			
			?>
            <?php do { ?>
            <td width="8%" height="180px" align="center" style="border:0px solid #808080; border-right:1px solid #808080; font-size:12px;"><div class="foo"><?php echo $row_CabecalhoDisciplinas['disciplina_nome']; ?></div></td>
            <?php } while ($row_CabecalhoDisciplinas = mysql_fetch_assoc($CabecalhoDisciplinas)); ?>
			<td width="8%" height="180px" align="center" style="border:0px solid #808080; border-right:1px solid #808080; font-size:12px; background-color:#D7D6D6;"><div class="foo">RESULTADO</div></td>
			</tr>
            </table>
            </th>
          </tr>
        </thead>
        <tbody>
		  <?php 
		  $contaAlunos = 1; 
		  $contaAprovados = 0;
		  $contaReprovados = 0;
		  $contaTransferidos = 0;
		  $contaDesistentes = 0;
		  $contaFalecidos = 0;		  
		  $contaOutros = 0;
		  ?>
          <?php do { ?>
            <tr>
			  <td align="center" width="25px"><?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?>
			  </td>	
              <td width="40%" align="left"><?php echo $row_Matriculas['aluno_nome']; ?></td>
              <td width="10%" align="center"><?php echo inverteData($row_Matriculas['aluno_nascimento']); ?></td>
              <td>
			  
			  <?php
					  mysql_select_db($database_SmecelNovo, $SmecelNovo);
					  $query_Notas = "SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, boletim_conselho, disciplina_id, disciplina_nome, disciplina_ordem FROM smc_boletim_disciplinas INNER JOIN smc_disciplina ON disciplina_id = boletim_id_disciplina WHERE boletim_id_vinculo_aluno = $row_Matriculas[vinculo_aluno_id] ORDER BY disciplina_ordem ASC";
					  $Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
					  $row_Notas = mysql_fetch_assoc($Notas);
					  $totalRows_Notas = mysql_num_rows($Notas);
					?>
                
				
				<table width="100%">
				
				<?php if ($row_Matriculas['vinculo_aluno_situacao'] == "1") { ?>
				
				<tr>
				
				<?php 
				$contaSituacao = 0;
				?>
                
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
				
				<?php do { ?>
				<td align="center" width="8%" style="border:0px solid #808080; border-right:1px solid #808080;"><strong>
				  
                   
				  
				  <div style="display:none">
				  <?php echo $row_Notas['disciplina_nome']; ?>
				  <?php $mv1 = mediaUnidade($row_Notas['boletim_1v1'],$row_Notas['boletim_2v1'],$row_Notas['boletim_3v1'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv2 = mediaUnidade($row_Notas['boletim_1v2'],$row_Notas['boletim_2v2'],$row_Notas['boletim_3v2'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv3 = mediaUnidade($row_Notas['boletim_1v3'],$row_Notas['boletim_2v3'],$row_Notas['boletim_3v3'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $mv4 = mediaUnidade($row_Notas['boletim_1v4'],$row_Notas['boletim_2v4'],$row_Notas['boletim_3v4'],$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo']); ?>
				  <?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?>
				  <?php $af = avaliacaoFinal($row_Notas['boletim_af']); ?>
				  </div>
				  
				  
				  <?php 
				  
				  //$mc = arredonda(mediaCurso($tp));
				  $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
				  
				  
				  
				  if ($af<>"-") {
					  echo $af;
					  
					  if ($af < (float)$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']) {
						  $contaSituacao++;
						  }
					  
					  } else {
						  echo $mc;
						  //echo $row_CriteriosAvaliativos['ca_min_media_aprovacao_final'];
						  //echo 6.0;
						  
						  if ($mc < (float)$row_CriteriosAvaliativos['ca_min_media_aprovacao_final']) {
							  $contaSituacao++;
							  }
						  
						  }
				  /*
				  
				  if ($mc >= $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']) {
					  echo $mc;
				  } else {
					  echo $af = avaliacaoFinal($row_Notas['boletim_af']);
					  
					  if ($af < $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']) {						  
						$contaSituacao++;  
					  }
					  
				  }
				  */
				  
				  if ($row_Notas['boletim_conselho']=="1") { echo "*"; } 
				  
				  ?>
				  </strong></td>
                  <?php } while ($row_Notas = mysql_fetch_assoc($Notas)); ?>
				
				<td align="center" width="8%" style="border:0px solid #808080; border-right:1px solid #808080;background-color:#D7D6D6;"><strong>
				<?php 
				if ($contaSituacao > 0) { 
					echo "<span style='color:red;'>CON</span>";
					$contaReprovados++;		
				} else { 
					echo "APR"; 
					$contaAprovados++;
				} 
				?></strong></td>
				</tr>
				
				
				<?php } else { ?>
				
				<tr><td align="center" style="border:0px solid #808080; border-right:1px solid #808080; letter-spacing:15px;"><small><?php echo $row_Matriculas['vinculo_aluno_situacao_nome']; ?></small></td></tr>
				
				<?php
				
				switch ($row_Matriculas['vinculo_aluno_situacao']){
					case "2":
						$contaTransferidos++;
					break;
					
					case "3":
						$contaDesistentes++;
					break;

					case "4":
						$contaFalecidos++;
					break;
					
					case "5":
						$contaOutros++;
					break;	
				}
				
				?>
				
				<?php //$contaOutros++; ?>
				
				
				<?php } ?>
				
								  

				</table>
				  
				  
				  </td>
				  
				  
            </tr>
            <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
        </tbody>
      </table>
	<small><i>*COMPONENTE CURRICULAR com aprovação do Conselho de Classe</i></small>
	<br><br>
		<table  width="100%" class="ls-sm-space ls-table-striped bordasimples">
			<thead>
			<tr>
			<th colspan="6">TOTALIZADOR DA TURMA</th>
				</tr>
				<tr>
				<th width="16%">APROVADOS</th>
				<th width="16%">REPROVADOS</th>
				<th width="16%">TRANSFERIDOS</th>
				<th width="16%">DESISTENTES</th>
				<th width="16%">FALECIDOS</th>
				<th width="16%">OUTROS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				<td><?php echo $contaAprovados; ?></td>
				<td><?php echo $contaReprovados; ?></td>
				<td><?php echo $contaTransferidos; ?></td>
				<td><?php echo $contaDesistentes; ?></td>
				<td><?php echo $contaFalecidos; ?></td>
				<td><?php echo $contaOutros; ?></td>
				</tr>					
		</tbody>
		</table>
		
		
		<?php 
		  $contaAprovadosEscola = $contaAprovadosEscola + $contaAprovados;
		  $contaReprovadosEscola = $contaReprovadosEscola + $contaReprovados;
		  $contaTransferidosEscola = $contaTransferidosEscola + $contaTransferidos;
		  $contaDesistentesEscola = $contaDesistentesEscola + $contaDesistentes;
		  $contaFalecidosEscola = $contaFalecidosEscola + $contaFalecidos;
		  $contaOutrosEscola = $contaOutrosEscola + $contaOutros;
		?>

		
		
	<br><br>
	<p>
	<div style="text-align:justify; line-height:150%;">
	E, para constar, eu_____________________________________________________________, secretário(a) escolar autorizado(a), lavrarei a presente Ata que vai assinada por mim e pelo(a) diretor(a) do estabelecimento de ensino.
	<hr>
	<table width="100%" class="ls-txt-center">
	<tr>
		<td width="50%"><p>________________________________________________<br>Secretário(a) Escolar</p></td>
		<td width="50%"><p>________________________________________________<br>Diretor(a) Escolar</p></td>	
	</tr>
	</table>
	
	<br>
	</div>
	</p>
	</div>

	</div>
	
	<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
	

<div style="page-break-inside: avoid;">

<div data-ls-module="dropdown" class="ls-dropdown">
  <a href="#" class="ls-btn-primary">TURNO</a>

  <ul class="ls-dropdown-nav">
      <li><a href="grafico_rendimentos_final.php">TODOS</a></li>
      <li><a href="grafico_rendimentos_final.php?turno=1">MATUTINO</a></li>
      <li><a href="grafico_rendimentos_final.php?turno=2">VESPERTINO</a></li>
      <li><a href="grafico_rendimentos_final.php?turno=3">NOTURNO</a></li>
  </ul>
</div>
	
	<div class="ls-box1 ls-txt-center">
	
	
		<p><h2 class="ls-txt-center">GRÁFICO DE RENDIMENTOS</h2></p>
		<p><h4 class="ls-txt-center"><?php echo $tituloTurno; ?></h4></p>

	<hr>
	
		<p>
			<table  width="100%" class="ls-sm-space ls-table-striped bordasimples">
			<thead>
				<tr>
					<th colspan="6"><?php echo $tituloTurno; ?></th>
				</tr>
				<tr>
				<th width="16%">APROVADOS</th>
				<th width="16%">REPROVADOS</th>
				<th width="16%">TRANSFERIDOS</th>
				<th width="16%">DESISTENTES</th>
				<th width="16%">FALECIDOS</th>
				<th width="16%">OUTROS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				<td><?php echo $contaAprovadosEscola; ?></td>
				<td><?php echo $contaReprovadosEscola; ?></td>
				<td><?php echo $contaTransferidosEscola; ?></td>
				<td><?php echo $contaDesistentesEscola; ?></td>
				<td><?php echo $contaFalecidosEscola; ?></td>
				<td><?php echo $contaOutrosEscola; ?></td>
				</tr>					
		</tbody>
		</table>  
	  </p>
	  
	  <p>
	  
	  
	  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['SITUAÇÃO', 'TOTAL'],
          ['APROVADOS',     <?php echo $contaAprovadosEscola; ?>],
          ['REPROVADOS',      <?php echo $contaReprovadosEscola; ?>],
          ['TRANSFERIDOS',      <?php echo $contaTransferidosEscola; ?>],
          ['DESISTENTES',      <?php echo $contaDesistentesEscola; ?>],
          ['FALECIDOS',      <?php echo $contaFalecidosEscola; ?>],
          ['OUTROS',  <?php echo $contaOutrosEscola; ?>]
        ]);

        var options = {
          title: 'TOTALIZADOR - <?php echo $tituloTurno; ?>'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
	<div id="piechart" style="width: 900px; height: 500px;"></div>
	  
	  
	  </p>
	  
	  
	  
	  
    </div>
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

mysql_free_result($EscolaLogada);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($Notas);

mysql_free_result($CabecalhoDisciplinas);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);
?>