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

$codUnidade = "";
if (isset($_GET['unidade'])) {

if ($_GET['unidade'] == "") {
//echo "TURMA EM BRANCO";	
header("Location: turmasAlunosVinculados.php?nada"); 
exit;
}
$codUnidade = anti_injection($_GET['unidade']);
$codUnidade = (int)$codUnidade;
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
$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_multietapa, vinculo_aluno_datatransferencia, vinculo_aluno_conselho, vinculo_aluno_reprovado_faltas,
etapa_id, etapa_nome, etapa_nome_abrev,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa, turma_ano_letivo, turma_multisseriada,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome  
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
LEFT JOIN smc_etapa ON etapa_id = vinculo_aluno_multietapa 
WHERE vinculo_aluno_boletim = '1' AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivoDesc = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_ano = '$anoLetivo' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivoDesc = mysql_query($query_AnoLetivoDesc, $SmecelNovo) or die(mysql_error());
$row_AnoLetivoDesc = mysql_fetch_assoc($AnoLetivoDesc);
$totalRows_AnoLetivoDesc = mysql_num_rows($AnoLetivoDesc);



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
                <title>RELATÓRIO DE ATAS FINAIS - TURMA<?php echo $row_AlunoBoletim['turma_nome']; ?>- ANO LETIVO<?php echo $row_AlunoBoletim['turma_ano_letivo']; ?>-<?php echo $row_EscolaLogada['escola_nome']; ?></title>
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
                <body onload="self.print();">
                <div style="page-break-inside: avoid;">
                  <div class="ls-box1 ls-txt-center">
                  <table>
                    <tr>
                      <td width="150" class="ls-txt-center"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?>
                        <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="70%" />
                        <?php } else { ?>
                        <img src="../../img/brasao_republica.png" alt="" width="70%" />
                        <?php } ?>
                        <br></td>
                      <td class="ls-txt-left"><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
                        <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
                        ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?> <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?><br>
                        CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></small></td>
                    </tr>
                  </table>
                  <p>
                  <h2 class="ls-txt-center">ATA DE RESULTADOS DA <?php echo $codUnidade; ?>ª UNIDADE</h2>
                  </p>
                  <p>
                  <div style="text-align:justify; line-height:150%;"> Apuração das notas finais da <strong><?php echo $codUnidade; ?>ª Unidade</strong> do Ano Letivo de <strong><?php echo $row_AnoLetivoDesc['ano_letivo_ano']; ?></strong> dos alunos da turma <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong>, turno <strong><?php echo $row_AlunoBoletim['turma_turno_nome']; ?></strong>, deste estabelecimento de ensino, com os seguintes resultados: <br>
                  </div>
                  </p>
                  <?php 
  $contaAprovadosEscola = 0;
  $contaReprovadosEscola = 0;
  $contaTransferidosEscola = 0;
  $contaDesistentesEscola = 0;
  $contaFalecidosEscola = 0;
  $contaOutrosEscola = 0;
  ?>
                  <?php 
		$contaSituacao = 0;
		?>
                  <table width="100%" class="ls-sm-space ls-table-striped bordasimples">
                    <?php
		   
		   //mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_disciplinasMatrizCab = "
			SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome 
			FROM smc_matriz_disciplinas
			INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
			WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
			$disciplinasMatrizCab = mysql_query($query_disciplinasMatrizCab, $SmecelNovo) or die(mysql_error());
			$row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab);
			$totalRows_disciplinasMatrizCab = mysql_num_rows($disciplinasMatrizCab);
		   
		   ?>
                    <tr>
                      <th>Nº</th>
                      <th width="">ALUNO(A)</th>
                      <th width="50" align="center">NASCIMENTO</th>
                      <?php do { ?>
                        <th width="" height="180px" align="center" style="border:1px solid #808080; border-right:1px solid #808080; font-size:10px;"><div class="foo"><?php echo $row_disciplinasMatrizCab['disciplina_nome']; ?></div></th>
                        <?php } while ($row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab)); ?>
                      <th align="center" style="border:1px solid #808080; border-right:1px solid #808080; font-size:10px;"><div class="foo">RESULTADO</div></th>
                    </tr>
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
                      <?php
		   
		   //mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_disciplinasMatriz = "
			SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_reprova, disciplina_id, disciplina_nome 
			FROM smc_matriz_disciplinas
			INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
			WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
			$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
			$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
			$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);
		   
		   ?>
                      <tr>
                      <td align="center" width="25px"><?php 
			echo $contaAlunos;
			$contaAlunos++;		
			?></td>
                      <td width="40%" align="left"><?php echo $row_AlunoBoletim['aluno_nome']; ?>
                          <?php if ($row_AlunoBoletim['vinculo_aluno_conselho']=="S") { ?>
                          *
                          <?php } ?>
                          <?php if (($row_AlunoBoletim['turma_multisseriada']==1) && ($row_AlunoBoletim['vinculo_aluno_multietapa']==0)) { ?>
                          <b class="ls-txt-right">* informe a etapa do aluno na turma multi</b>
                          <?php } else { ?>
                          <b style="float:right"><?php echo $row_AlunoBoletim['etapa_nome_abrev']; ?>&nbsp;</b>
                          <?php } ?></td>
                      <td width="10%" align="center"><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></td>
                      <?php if (($row_AlunoBoletim['vinculo_aluno_situacao'] <> "1") || ($row_AlunoBoletim['vinculo_aluno_reprovado_faltas']=="S")) { ?>
                      <?php
		
		switch ($row_AlunoBoletim['vinculo_aluno_situacao']){
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
                      <?php if ($row_AlunoBoletim['vinculo_aluno_reprovado_faltas']=="S") { ?>
                      <td align="center" style="border:1px solid #808080; border-right:1px solid #808080; letter-spacing:15px; font-size:9px;" colspan="<?php echo $totalRows_disciplinasMatriz + 1; ?>">REPROVADO POR FALTAS</td>
                      <?php } else { ?>
                      <td align="center" style="border:1px solid #808080; border-right:1px solid #808080; letter-spacing:15px; font-size:9px;" colspan="<?php echo $totalRows_disciplinasMatriz + 1; ?>"><?php echo $row_AlunoBoletim['vinculo_aluno_situacao_nome']; ?></td>
                      <?php } ?>
                      <?php } else { ?>
                      <?php do { ?>
                        <div style="display:none;">
                        <?php $tmu = 0; ?>
                        <?php for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                        <?php $ru = 0; ?>
                        <?php for ($a = 1; $a <= $row_CriteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                        <?php 
				//mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '$codUnidade' AND nota_num_avaliacao = '$a'";
				$nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
				$row_nota = mysql_fetch_assoc($nota);
				$totalRows_nota = mysql_num_rows($nota);
				//echo exibeTraco($row_nota['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_av']);
				$ru = $ru + $row_nota['nota_valor'];
				?>
                        <?php } ?>
                        <?php $mu = mediaUnidade($ru,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_media_min_periodo'],$row_CriteriosAvaliativos['ca_calculo_media_periodo'],$row_CriteriosAvaliativos['ca_qtd_av_periodos'],$row_CriteriosAvaliativos['ca_digitos']); ?>
                        <?php $tmu = $tmu + $mu; ?>
                        <?php } ?>
                        <?php $tp = totalPontos($tmu,$row_CriteriosAvaliativos['ca_digitos']); ?>
                        <?php $mc = mediaCurso($tp,$row_CriteriosAvaliativos['ca_arredonda_media'],$row_CriteriosAvaliativos['ca_aproxima_media'],$row_CriteriosAvaliativos['ca_min_media_aprovacao_final'],$row_CriteriosAvaliativos['ca_qtd_periodos'],$row_CriteriosAvaliativos['ca_digitos']); ?>
                        <?php 
					//mysql_select_db($database_SmecelNovo, $SmecelNovo);
					$query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_AlunoBoletim[vinculo_aluno_id]' AND nota_id_disciplina = '$row_disciplinasMatriz[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
					$notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
					$row_notaAf = mysql_fetch_assoc($notaAf);
					$totalRows_notaAf = mysql_num_rows($notaAf);
					$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_CriteriosAvaliativos['ca_nota_min_recuperacao_final']);
				?>
                      </div>
                        <td><?php 
					
					//echo $mc;
					
					
					if ($row_disciplinasMatriz['matriz_disciplina_reprova']=="S") { 
					
					echo $mc;
					
					/*
					
					if ($af<>"-") {
					  
					  echo "(".$af.")";
					  
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
						  
						  */
					
					} else {
				echo "**";
				}
					
			//echo $mu;
			/*		
			if ($af<>"-") {
			  echo "(".$af.")";
			  
			  
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
					*/
					
					
					//echo resultadoFinal($mc, $af, $row_CriteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_CriteriosAvaliativos['ca_min_media_aprovacao_final']);
				 ?></td>
                        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                      <td><?php 
				if ($contaSituacao > 0) { 
					echo "<span style='color:red;'>CON</span>";
					//$contaReprovados++;		
				} else { 
					echo "APR"; 
					//$contaAprovados++;
				} 
				
				$contaSituacao = 0;
				
				?></td>
                      <?php } ?>
                    </tr>
                      <?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim)); ?>
                      </div>
                    
                  </table>
                  <small><i>*Aprovado pelo Conselho de Classe;</i></small> <small><i>**Componente não reprova;</i></small> <small><i>(Nota entre parênteses indica que o aluno fez Avaliação Final)</i></small>
                  <p>
                  <div style="text-align:justify; line-height:150%;"> E, para constar, eu_____________________________________________________________, secretário(a) escolar autorizado(a), lavrarei a presente Ata que vai assinada por mim e pelo(a) diretor(a) do estabelecimento de ensino. <br>
                    <table width="100%" class="ls-txt-center">
                      <tr>
                        <td width="50%"><p>________________________________________________<br>
                            Secretário(a) Escolar</p></td>
                        <td width="50%"><p>________________________________________________<br>
                            Diretor(a) Escolar</p></td>
                      </tr>
                    </table>
                  </div>
                  </p>
                </div>
                
                <!-- CONTEÚDO --> 
                
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