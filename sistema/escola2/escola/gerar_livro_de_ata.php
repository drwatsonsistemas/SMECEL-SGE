<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/calculos.php"; ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome  
FROM smc_turma
WHERE turma_etapa NOT IN (1,2,3) 
AND turma_id_escola = '$row_UsuLogado[usu_escola]' 
AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC
";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);


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
	table.bordasimples tr td {border:1px solid #808080; padding:5px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:5px; font-size:9px;}
	
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
 
        <h1 class="ls-title-intro ls-ico-home">ATAS FINAIS</h1>
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

<div style="page-break-inside: avoid;">
  <div class="ls-box ls-box">
        <div class="ls-txt-center">
 		<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
	</div>
    
    <br>
    		
	<p><h2 class="ls-txt-center">ATA DE RESULTADOS FINAIS</h2></p>
    
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
$query_Matriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome, matriz_id, matriz_criterio_avaliativo 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
INNER JOIN smc_matriz ON matriz_id = matriz_disciplina_id_matriz
WHERE matriz_disciplina_id_matriz = '$row_Turmas[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterio = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, 
ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, 
ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes 
FROM smc_criterios_avaliativos
WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'
";
$Criterio = mysql_query($query_Criterio, $SmecelNovo) or die(mysql_error());
$row_Criterio = mysql_fetch_assoc($Criterio);
$totalRows_Criterio = mysql_num_rows($Criterio);
?>

          <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Matricula = "
			SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
			vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
			vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
			vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento,
				CASE vinculo_aluno_situacao
				WHEN 1 THEN 'MATRICULADO'
				WHEN 2 THEN 'TRANSFERIDO'
				WHEN 3 THEN 'DESISTENTE'
				WHEN 4 THEN 'FALECIDO'
				WHEN 5 THEN 'OUTROS'
				END AS vinculo_aluno_situacao_nome 
			FROM smc_vinculo_aluno
			INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
			WHERE vinculo_aluno_id_turma = '$row_Turmas[turma_id]'
			ORDER BY aluno_nome ASC";
			$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
			$row_Matricula = mysql_fetch_assoc($Matricula);
			$totalRows_Matricula = mysql_num_rows($Matricula);
		  ?>
          
          
        <table class="ls-sm-space ls-table-striped bordasimples" width="100%">
        <thead>
        	<tr>
            	<th></th>
            	<th>ALUNO</th>
                <th width="80" align="center">NASCIMENTO</th>
                
                <?php do { ?>
                
                	<th width="60" class="" height="180px" align="center"><div class="foo"><?php echo $row_Matriz['disciplina_nome']; ?></div></th>
                
                <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
                
                <th width="60" class="" height="180px" align="center" style="background-color:#F4F4F4;"><div class="foo"><strong>RESULTADO</strong></div></th>
                
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
            
		<?php 
		$num = 1;
		do { 
		?>
        
			<?php
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_MatrizAluno = "
                SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
                FROM smc_matriz_disciplinas 
                INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
                WHERE matriz_disciplina_id_matriz = '$row_Turmas[turma_matriz_id]'";
                $MatrizAluno = mysql_query($query_MatrizAluno, $SmecelNovo) or die(mysql_error());
                $row_MatrizAluno = mysql_fetch_assoc($MatrizAluno);
                $totalRows_MatrizAluno = mysql_num_rows($MatrizAluno);
            ?>
        
        <tr>
        	<td class="ls-txt-center" width="30"><?php echo $num; $num++; ?></td>
            <td class="ls-txt-left"><?php echo $row_Matricula['aluno_nome']; ?></td>
            <td width="80" align="center"><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></td>
            
            <?php if ($row_Matricula['vinculo_aluno_situacao'] == "1") { ?>
            
            <?php 
				$contaSituacao = 0;
				?>
                        
                <?php do { ?>
                
                	<td width="60" class="ls-txt-center">
                    <?php $tmu = 0; ?>
                    <?php for ($p = 1; $p <= $row_Criterio['ca_qtd_periodos']; $p++) { ?>
                    
                    	  <?php $ru = 0; ?>
                    	  <?php for ($a = 1; $a <= $row_Criterio['ca_qtd_av_periodos']; $a++) { ?>
                          
								<?php            
                                    mysql_select_db($database_SmecelNovo, $SmecelNovo);
                                    $query_Nota = "
                                    SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, 
                                    nota_max, nota_min, nota_valor, nota_hash 
                                    FROM smc_nota
                                    WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' 
									AND nota_id_disciplina = '$row_MatrizAluno[matriz_disciplina_id_disciplina]' 
									AND nota_periodo = '$p' 
									AND nota_num_avaliacao = '$a'";
                                    $Nota = mysql_query($query_Nota, $SmecelNovo) or die(mysql_error());
                                    $row_Nota = mysql_fetch_assoc($Nota);
                                    $totalRows_Nota = mysql_num_rows($Nota);

                                ?>
                          
                           <?php 
						   //echo exibeTraco($row_Nota['nota_valor'],$row_Criterio['ca_nota_min_av']); 
						   $ru = $ru + $row_Nota['nota_valor'];
						   ?> 
                            
                           
                          <?php } ?>
                     
                          <div style="display:none">
						  <?php $mu = mediaUnidade($ru,$row_Criterio['ca_arredonda_media'],$row_Criterio['ca_aproxima_media'],$row_Criterio['ca_media_min_periodo'],$row_Criterio['ca_calculo_media_periodo'],$row_Criterio['ca_qtd_av_periodos']); ?>
                          <?php $tmu = $tmu + $mu; ?>
                          </div>
                          
                    <?php } ?>
                    	  <div style="display:none">
                          <?php $tp = totalPontos($tmu); ?>
						  </div>
                    <?php
						mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_notaAf = "
						SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, 
						nota_max, nota_min, nota_valor, nota_hash 
						FROM smc_nota 
						WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' 
						AND nota_id_disciplina = '$row_MatrizAluno[matriz_disciplina_id_disciplina]' 
						AND nota_periodo = '99' 
						AND nota_num_avaliacao = '99'";
						$notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
						$row_notaAf = mysql_fetch_assoc($notaAf);
						$totalRows_notaAf = mysql_num_rows($notaAf);
					?>

					<div style="display:none">
                    <?php $mc = mediaCurso($tp,$row_Criterio['ca_arredonda_media'],$row_Criterio['ca_aproxima_media'],$row_Criterio['ca_min_media_aprovacao_final'],$row_Criterio['ca_qtd_periodos']); ?>
					<?php $af = avaliacaoFinal($row_notaAf['nota_valor'],$row_Criterio['ca_nota_min_recuperacao_final']); ?>
                    </div>
                    
                    <?php 
					//Exibe resultado
					
						  	if ($mc >= (float)$row_Criterio['ca_min_media_aprovacao_final']) {
							   echo $mc;
							} else {
								
								if ($af<>"-") { 
								
									echo "(".$af.")";
								
									if ($af < (float)$row_Criterio['ca_nota_min_recuperacao_final']) {
											$contaSituacao++;
										}
								
								} else {
									echo "-";
									$contaSituacao++;
									}
								
								}
						?>
                                        
					
                    <div style="display:none">
					<?php echo resultadoFinal($mc, $af, $row_Criterio['ca_nota_min_recuperacao_final'], $row_Criterio['ca_min_media_aprovacao_final']); ?>
                    </div>
                    
                    </td>
                    
                
                <?php } while ($row_MatrizAluno = mysql_fetch_assoc($MatrizAluno)); ?>            

                    <td width="60" class="ls-txt-center" style="background-color:#F4F4F4;"><strong>
                    
                <?php 
				if ($contaSituacao > 0) { 
					echo "<span style='color:red;'>CON</span>";
					$contaReprovados++;		 
				} else { 
					echo "APR"; 
					$contaAprovados++;
				} 
				?>
                    
                    </strong></td>
            
            
        </tr>
        
        <?php } else { ?>
        	
            
            	<td colspan="<?php echo $totalRows_MatrizAluno; ?>" class="ls-txt-center" style="letter-spacing:15px;"><?php echo $row_Matricula['vinculo_aluno_situacao_nome']; ?></td>
            	<td class="ls-txt-center">-</td>
                
                
                <?php
				
				switch ($row_Matricula['vinculo_aluno_situacao']){
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
            
        <?php } ?>


		<?php } while ($row_Matricula = mysql_fetch_assoc($Matricula)); ?>
        
        
        
        
                
        </tbody>
        </table>
        
        	<small><i>* = Componente Curricular com aprovação do Conselho de Classe</i></small><br>
	<small><i>() = Nota entre parênteses indica que o aluno fez Avaliação Final</i></small>
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
				<td class="ls-txt-center"><?php echo $contaAprovados; ?></td>
				<td class="ls-txt-center"><?php echo $contaReprovados; ?></td>
				<td class="ls-txt-center"><?php echo $contaTransferidos; ?></td>
				<td class="ls-txt-center"><?php echo $contaDesistentes; ?></td>
				<td class="ls-txt-center"><?php echo $contaFalecidos; ?></td>
				<td class="ls-txt-center"><?php echo $contaOutros; ?></td>
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
</div>

<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?> 

<div style="page-break-inside: avoid;">
	
	<div class="ls-box ls-box ls-txt-center">
	
	<?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="60px" /><?php } ?><br>
		<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
		<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
		ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> 
		<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
	
		<p><h2 class="ls-txt-center">ATA DE RESULTADOS FINAIS</h2></p>
		
		<p>
			<table  width="100%" class="ls-sm-space ls-table-striped bordasimples">
			<thead>
				<tr>
					<th colspan="6">TOTALIZADOR DA ESCOLA</th>
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
          title: 'TOTALIZADOR DA ESCOLA'
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
 
    <script language="Javascript">	
 $(document).ready(function(){
          setTimeout('$("#preload").fadeOut(100)', 1500);
      });
</script>
  </body>
</html>
<?php
mysql_free_result($UsuLogado);
mysql_free_result($Criterio);
//mysql_free_result($Nota);
//mysql_free_result($notaAf);
mysql_free_result($Matriz);
mysql_free_result($MatrizAluno);
mysql_free_result($Turmas);
mysql_free_result($Matricula);
mysql_free_result($EscolaLogada);
?>