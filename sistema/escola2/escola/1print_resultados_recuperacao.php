<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php //include('fnc/notas.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
<?php include "fnc/calculos.php"; ?>
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

$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int)$codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
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
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

if ($totalRows_AlunoBoletim == "") {
	//echo "TURMA EM BRANCO";	
	//header("Location: turmasAlunosVinculados.php?nada"); 
 	
	echo "<h3><center>Sem dados.<br><a href=\"javascript:window.close()\">Fechar</a></center></h3>";
	echo "";
	
	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_AlunoBoletim[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);


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
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">            <script src="js/locastyle.js"></script>
                <style>
table.bordasimples {
	border-collapse: collapse;
	font-size:7px;
}
table.bordasimples tr td {
	border:1px solid #808080;
	padding:2px;
	font-size:12px;
}
table.bordasimples tr th {
	border:1px solid #808080;
	padding:2px;
	font-size:9px;
}



.foo {
	writing-mode: vertical-lr;
	-webkit-writing-mode: vertical-lr;
	-ms-writing-mode: vertical-lr;/* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */
	
  }

</style>
                <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
                <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
                </head>
                <body onload="alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">
                <div class="container-fluid">
                  
				  
				<div style="page-break-inside: avoid;">
				
				<div class="ls-box ls-txt-center">
				
	  <?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
      <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" />
      <?php } else { ?>
      <img src="../../img/brasao_republica.png" alt="" width="60px" />
      <?php } ?>
      <br>
      <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
      <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
      ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
      CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>
      <p>
      <h2 class="ls-txt-center">RESULTADO PARCIAL | ANO LETIVO <?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?></h2>
	  
	  <h3 class="ls-txt-center"><?php echo $row_AlunoBoletim['turma_nome']; ?></h3>
	  
	  </p>
				


   
				   
				 <hr>  
				   

				
				<table width="100%" class="ls-sm-space ls-table-striped bordasimples">
				
				  <?php
				   
				   //mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_disciplinasMatrizCab = "
					SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
					FROM smc_matriz_disciplinas
					INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
					WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
					$disciplinasMatrizCab = mysql_query($query_disciplinasMatrizCab, $SmecelNovo) or die(mysql_error());
					$row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab);
					$totalRows_disciplinasMatrizCab = mysql_num_rows($disciplinasMatrizCab);
				   
				   ?>
				
				
				   <tr>
				   <th width="350px"></th>
				   <?php do { ?>
				   
				   <th width="" height="180px" align="center" style="border:0px solid #808080; border-right:1px solid #808080; font-size:12px;"><div class="foo"><?php echo $row_disciplinasMatrizCab['disciplina_nome']; ?></div></th>
				   
				   <?php } while ($row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab)); ?>
				   </tr>
  
  
  
				  <?php do { ?>
                   
                   <?php
				   
				   //mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_disciplinasMatriz = "
					SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
					FROM smc_matriz_disciplinas
					INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
					WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
					$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
					$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
					$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);
				   
				   ?>
                   
                   
				   <tr>
						<td><?php echo $row_AlunoBoletim['aluno_nome']; ?></td>
					

						  <?php do { ?>
						  
						  <div style="display:none;">
                        
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <?php $ru = 0; ?>
                          <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                    
						 
                        <?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                        $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                        $row_nota = mysql_fetch_assoc($nota);
                        $totalRows_nota = mysql_num_rows($nota);
                        echo exibeTraco($row_nota['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av']);
						$ru = $ru + $row_nota['nota_valor'];
                        ?>
                        
                       
                          <?php } ?>
                          
                            <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos']); ?>
                            <?php $tmu = $tmu + $mu; ?>
                            
                          <?php } ?>
                         
                            <?php $tp = totalPontos($tmu); ?>
                            
                          
                        <?php $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_qtd_periodos']); ?>
                            
                          
                          <?php 
                        //mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                        $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                        $row_notaAf = mysql_fetch_assoc($notaAf);
                        $totalRows_notaAf = mysql_num_rows($notaAf);
						echo $af = avaliacaoFinal($row_notaAf['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']);
                        ?>
                         
						 
						 </div>


						 <td>
                         
						 <?php 
							echo resultadoFinal($mc, $af, $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
						 ?>
						 </td>
                        
                       
                     
                       <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>						
						
						
				   
				   
				   </tr>
				   



                  
                    <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
                   </div>
                  </table>
                  
                  
                  </div>
                  
                  <!-- CONTEÚDO --> 
                </div>
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script>
                </body>
                </html>
				<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($AlunoBoletim);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);

mysql_free_result($disciplinasMatriz);
?>