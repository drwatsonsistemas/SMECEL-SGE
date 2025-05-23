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
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, etapa_id, etapa_id_filtro,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VESP'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome,
turma_total_alunos, turma_ano_letivo, matriz_id, matriz_criterio_avaliativo, matriz_nome, ca_id, ca_questionario_conceitos 
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa 
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$anoLetivo' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC
";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

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
$query_CriteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_digitos, ca_grupo_conceito, ca_questionario_conceitos, ca_grupo_etario FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$CriteriosAvaliativos = mysql_query($query_CriteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_CriteriosAvaliativos = mysql_fetch_assoc($CriteriosAvaliativos);
$totalRows_CriteriosAvaliativos = mysql_num_rows($CriteriosAvaliativos);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivoDesc = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_ano = '$anoLetivo' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC LIMIT 1";
$AnoLetivoDesc = mysql_query($query_AnoLetivoDesc, $SmecelNovo) or die(mysql_error());
$row_AnoLetivoDesc = mysql_fetch_assoc($AnoLetivoDesc);
$totalRows_AnoLetivoDesc = mysql_num_rows($AnoLetivoDesc);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitosCab = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso ASC
";
$GrupoConceitosCab = mysql_query($query_GrupoConceitosCab, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitosCab = mysql_fetch_assoc($GrupoConceitosCab);
$totalRows_GrupoConceitosCab = mysql_num_rows($GrupoConceitosCab);


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
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">	<script src="js/locastyle.js"></script>
	<style>
		html{
			-webkit-print-color-adjust: exact;
		}
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
					$perc = number_format(100/$totalRows_GrupoConceitosCab, 0);
					$inicio = 0;
					$parc = $perc;
					$cont = 1;
					$ver = 0;
					?>
					<table>
						<?php do { ?>
							<tr>
								<td class="ls-txt-left">
									<span class="ls-tag-warning">
										De <?php echo $inicio; ?>% até <?php echo min($parc, 100); ?>%
										<?php $cont++; ?>
										<?php if ($cont == $totalRows_GrupoConceitosCab) { 
											$ver = 1; 
										}?>
										<?php $inicio = min($parc + 1, 100); $parc = min($parc + $perc + $ver, 100); ?>
									</span>
									<span class="ls-tag-info">
										<?php echo $row_GrupoConceitosCab['conceito_itens_legenda']; ?>: <?php echo $row_GrupoConceitosCab['conceito_itens_descricao']; ?>
									</span>
								</td>
							</tr>
						<?php } while ($row_GrupoConceitosCab = mysql_fetch_assoc($GrupoConceitosCab)); ?>
						<?php

						mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_GrupoConceitos = "
						SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
						FROM smc_conceito_itens
						WHERE conceito_itens_id_conceito = '$row_CriteriosAvaliativos[ca_grupo_conceito]'
						ORDER BY conceito_itens_peso DESC
						";
						$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
						$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
						$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

						?>
					</table>

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

						mysql_select_db($database_SmecelNovo, $SmecelNovo);
						$query_CamposCab = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp";
						$CamposCab = mysql_query($query_CamposCab, $SmecelNovo) or die(mysql_error());
						$row_CamposCab = mysql_fetch_assoc($CamposCab);
						$totalRows_CamposCab = mysql_num_rows($CamposCab);
						?>
						<tr>
							<th>Nº</th>
							<th width="">ALUNO(A)</th>
							<th width="50" align="center">NASCIMENTO</th>
							<?php do { ?>
								<th width="" height="180px" align="center" style="border:1px solid #808080; border-right:1px solid #808080; font-size:10px;"><div class="foo"><?php echo utf8_encode($row_CamposCab['campos_exp_nome']); ?></div></th>
							<?php } while ($row_CamposCab = mysql_fetch_assoc($CamposCab)); ?>
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
									<?php 
									mysql_select_db($database_SmecelNovo, $SmecelNovo);
									$query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp";
									$Campos = mysql_query($query_Campos, $SmecelNovo) or die(mysql_error());
									$row_Campos = mysql_fetch_assoc($Campos);
									$totalRows_Campos = mysql_num_rows($Campos);
									?>
									<?php do { ?>
										<?php
										mysql_select_db($database_SmecelNovo, $SmecelNovo);
										$query_Objetos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id_campos_exp = '$row_Campos[campos_exp_id]' AND campos_exp_obj_faixa_et_cod = '$row_CriteriosAvaliativos[ca_grupo_etario]'";
										$Objetos = mysql_query($query_Objetos, $SmecelNovo) or die(mysql_error());
										$row_Objetos = mysql_fetch_assoc($Objetos);
										$totalRows_Objetos = mysql_num_rows($Objetos);

										do {

											mysql_select_db($database_SmecelNovo, $SmecelNovo);
											$query_Acompanhamento = "
											SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
											FROM smc_acomp_proc_aprend
											WHERE acomp_id_matriz = '$row_Matriz[matriz_id]'
											AND acomp_id_crit = '$row_CriteriosAvaliativos[ca_id]'
											AND acomp_id_obj_aprend = '$row_Objetos[campos_exp_obj_id]'
											";
											$Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
											$row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
											$totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
											
											$total = 0;
											$pontos = 0;

											do {
												


												mysql_select_db($database_SmecelNovo, $SmecelNovo);
												$query_ConceitoAluno = "
												SELECT conc_id, conc_acomp_id, conc_matricula_id, conc_periodo, conc_avaliacao FROM smc_conceito_aluno
												WHERE conc_acomp_id = '$row_Acompanhamento[acomp_id]' AND conc_matricula_id = '$row_AlunoBoletim[vinculo_aluno_id]' AND conc_periodo = '$codUnidade'
												";
												$ConceitoAluno = mysql_query($query_ConceitoAluno, $SmecelNovo) or die(mysql_error());
												$row_ConceitoAluno = mysql_fetch_assoc($ConceitoAluno);
												$totalRows_ConceitoAluno = mysql_num_rows($ConceitoAluno);

												$pontos = $pontos + $row_ConceitoAluno['conc_avaliacao'];
												$total = $total + $row_GrupoConceitos['conceito_itens_peso'];
											} while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento));
											?>


											<?php
										} while ($row_Objetos = mysql_fetch_assoc($Objetos));
										?>
										<td>
											<?php if ( $pontos > 0) { echo number_format((($pontos/$total)*100),0)."%"; } else { echo "-"; } ?>
										</td>
									<?php } while ($row_Campos = mysql_fetch_assoc($Campos)); ?>

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