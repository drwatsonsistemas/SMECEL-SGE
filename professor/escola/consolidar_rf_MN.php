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

if (isset($_POST) and !empty($_POST)) {

	$codTurma = "";
	$buscaTurma = "";
	if (isset($_POST['id_turma'])) {

		if ($_POST['id_turma'] == "") {
			//echo "TURMA EM BRANCO";	
			header("Location: turmasAlunosVinculados.php?nada");
			exit;
		}

		$codTurma = anti_injection($_POST['id_turma']);
		$codTurma = (int) $codTurma;
		$buscaTurma = "AND turma_id = $codTurma ";
	}

	include "usuLogado.php";
	include "fnc/anoLetivo.php";

	$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
	if (isset($_POST['ano_letivo'])) {

		if ($_POST['ano_letivo'] == "") {
			//echo "TURMA EM BRANCO";	
			header("Location: turmasAlunosVinculados.php?nada");
			exit;
		}

		$anoLetivo = anti_injection($_POST['ano_letivo']);
		$anoLetivo = (int) $anoLetivo;
	}

	function arredondarNota($nota)
	{
		$decimal = round($nota - floor($nota), 2); // Arredonda para 2 casas decimais

		if ($decimal >= 0.75) {
			return ceil($nota);
		} elseif ($decimal >= 0.5 && $decimal < 0.75) {
			return floor($nota) + 0.5;
		} elseif ($decimal >= 0.3 && $decimal < 0.5) {
			return floor($nota) + 0.5;
		} else {
			return floor($nota);
		}
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

	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_AlunoBoletim = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_conselho, vinculo_aluno_conselho_reprovado,
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO(A)'
WHEN 2 THEN '<span class=\"ls-color-danger\">TRANSFERIDO(A)</span>'
WHEN 3 THEN '<span class=\"ls-color-danger\">DESISTENTE</span>'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_boletim = 1 AND vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' $buscaTurma
ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC";
	$AlunoBoletim = mysql_query($query_AlunoBoletim, $SmecelNovo) or die(mysql_error());
	$row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim);
	$totalRows_AlunoBoletim = mysql_num_rows($AlunoBoletim);

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



	//mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_disciplinasMatrizCab = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome, disciplina_ata 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
	$disciplinasMatrizCab = mysql_query($query_disciplinasMatrizCab, $SmecelNovo) or die(mysql_error());
	$row_disciplinasMatrizCab = mysql_fetch_assoc($disciplinasMatrizCab);
	$totalRows_disciplinasMatrizCab = mysql_num_rows($disciplinasMatrizCab);

	?>



	<?php

	$matriculado = 0;
	$transferido = 0;
	$desistente = 0;
	$falecido = 0;
	$outros = 0;

	$aprovados_turma = 0;
	$aprovados_turma_conselho = 0;
	$reprovados_turma = 0;
	$aprovados_escola = 0;
	$reprovados_escola = 0;
	?>

	<?php
	$num = 1;
	do { ?>

		<?php

		$res = 0;
		?>



		<?php

		//mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_disciplinasMatriz = "
	SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome, disciplina_ata  
	FROM smc_matriz_disciplinas
	INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
	WHERE matriz_disciplina_id_matriz = '$row_AlunoBoletim[turma_matriz_id]'";
		$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
		$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
		$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

		?>


		<?php do { ?>

			<?php
			$totalTrimestre = 0;
			$totalTrimestreSemRecuperacao = 0;
			$pontuacaoTotalAnoLetivo1 = 0;
			$pontuacaoTotalAnoLetivo2 = 0;
			$pontuacaoTotalAnoLetivo3 = 0;

			for ($p = 1; $p <= $row_CriteriosAvaliativos['ca_qtd_periodos']; $p++) {
				// Consultas ao banco de dados para obter as notas qualitativas, quantitativas, paralela e de recuperação
				$query_qualitativo = sprintf(
					"SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='1'",
					GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
					GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
					GetSQLValueString($p, "int")
				);
				$qualitativo = mysql_query($query_qualitativo, $SmecelNovo) or die(mysql_error());
				$somaPontuacaoQualitativo = 0;
				while ($row_qualitativo = mysql_fetch_assoc($qualitativo)) {
					$somaPontuacaoQualitativo += floatval($row_qualitativo['qq_nota']);
				}

				$query_quantitativo = sprintf(
					"SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='2'",
					GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
					GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
					GetSQLValueString($p, "int")
				);
				$quantitativo = mysql_query($query_quantitativo, $SmecelNovo) or die(mysql_error());
				$somaPontuacaoQuantitativo = 0;
				while ($row_quantitativo = mysql_fetch_assoc($quantitativo)) {
					$somaPontuacaoQuantitativo += floatval($row_quantitativo['qq_nota']);
				}

				$query_paralela = sprintf(
					"SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='3'",
					GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
					GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
					GetSQLValueString($p, "int")
				);
				$paralela = mysql_query($query_paralela, $SmecelNovo) or die(mysql_error());
				$notaParalela = 0;
				if ($row_paralela = mysql_fetch_assoc($paralela)) {
					$notaParalela = floatval($row_paralela['qq_nota']);
				}

				$query_recuperacao = sprintf(
					"SELECT qq_nota FROM smc_notas_qq WHERE qq_id_matricula = %s AND qq_id_componente = %s AND qq_id_periodo = %s AND qq_tipo_criterio='4'",
					GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
					GetSQLValueString($row_disciplinasMatriz['disciplina_id'], "int"),
					GetSQLValueString($p, "int")
				);
				$recuperacao = mysql_query($query_recuperacao, $SmecelNovo) or die(mysql_error());
				$notaRecuperacao = 0;
				if ($row_recuperacao = mysql_fetch_assoc($recuperacao)) {
					$notaRecuperacao = floatval($row_recuperacao['qq_nota']);
				}

				// Ajustando valores de acordo com os períodos
				switch ($p) {
					case '1':
					case '2':
						$mediaTrimestre = 18; // Média necessária para os períodos 1 e 2
						break;
					case '3':
						$mediaTrimestre = 24; // Média necessária para o período 3
						break;
				}

				// Soma da nota qualitativa com a quantitativa, ou com a paralela se existir
				$totalTrimestreSemRecuperacao = $somaPontuacaoQualitativo + ($notaParalela > 0 && $notaParalela > $somaPontuacaoQuantitativo ? $notaParalela : $somaPontuacaoQuantitativo);

				// Definindo a nota total do trimestre
				$totalTrimestre = $notaRecuperacao > 0 ? $notaRecuperacao : $totalTrimestreSemRecuperacao;

				$totalTrimestre = arredondarNota($totalTrimestre);
				$totalTrimestreSemRecuperacao = arredondarNota($totalTrimestreSemRecuperacao);

				switch ($p) {
					case '1':
						$pontuacaoTotalAnoLetivo1 = $totalTrimestre;
						break;
					case '2':
						$pontuacaoTotalAnoLetivo2 = $totalTrimestre;
						break;
					case '3':
						$pontuacaoTotalAnoLetivo3 = $totalTrimestre;
						break;
				}


				// Verifica aprovação com paralela ou recuperação
				if ($totalTrimestre >= $mediaTrimestre) {
					$classeNota = 'nota-apr';
					$alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
				} else if ($notaParalela > 0 && $notaParalela >= 8.1 && $totalTrimestreSemRecuperacao >= $mediaTrimestre) {
					$classeNota = 'nota-paralela-apr';
					$alunos_aprovados[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
				} else {
					$alunos_reprovados_paralela[] = array('aluno_id' => $row_AlunoBoletim['vinculo_aluno_id'], 'periodo' => $p);
				}
			}

			$pontuacaoTotal = $pontuacaoTotalAnoLetivo1 + $pontuacaoTotalAnoLetivo2 + $pontuacaoTotalAnoLetivo3;
			if ($row_disciplinasMatriz['matriz_disciplina_reprova'] == "S") {
				if (($pontuacaoTotal < 60)) {
					$res++;
				}
			} ?>


		<?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>


		<?php

		// Inicializa o status de resultado do aluno
		$vinculo_aluno_resultado_final = 0;
		$vinculo_aluno_reprovado_recuperacao_final = 0;

		// Verifica se o aluno está matriculado
		if ($row_AlunoBoletim['vinculo_aluno_situacao'] == '1') {
			// Verifica se o aluno foi aprovado
			if ($res == 0) {
				$vinculo_aluno_resultado_final = 1; // Aprovado
			} else {
				$vinculo_aluno_resultado_final = 2; // Reprovado
				$vinculo_aluno_reprovado_recuperacao_final = 1;
			}

			// Se o aluno foi aprovado pelo conselho
			if ($row_AlunoBoletim['vinculo_aluno_conselho'] == "S") {
				$vinculo_aluno_resultado_final = 1; // Atualiza para "aprovado"
				$aprovados_turma_conselho++;
			}

			if($row_AlunoBoletim['vinculo_aluno_conselho_reprovado'] == "S"){
				$vinculo_aluno_resultado_final = 2;
			}

			// Atualiza o resultado no banco de dados
			$updateSQL = sprintf(
				"UPDATE smc_vinculo_aluno SET vinculo_aluno_resultado_final = %s AND vinculo_aluno_reprovado_recuperacao_final = %s  WHERE vinculo_aluno_id = %s AND vinculo_aluno_id_turma = %s AND vinculo_aluno_id_escola = %s",
				GetSQLValueString($vinculo_aluno_resultado_final, "text"),
				GetSQLValueString($vinculo_aluno_reprovado_recuperacao_final, "text"),
				GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id'], "int"),
				GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id_turma'], "int"),
				GetSQLValueString($row_AlunoBoletim['vinculo_aluno_id_escola'], "int")
			);
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

			// Contabiliza o resultado
			if ($vinculo_aluno_resultado_final == 1) {
				$aprovados_turma++;
			} elseif ($vinculo_aluno_resultado_final == 2) {
				$reprovados_turma++;
			}
		} elseif ($row_AlunoBoletim['vinculo_aluno_situacao'] == '2') {
			// Caso o aluno esteja transferido, não altera o status
			// echo $row_AlunoBoletim['vinculo_aluno_situacao_nome'];
		}

		?>


	<?php } while ($row_AlunoBoletim = mysql_fetch_assoc($AlunoBoletim));

	$timestamp = date('Y-m-d h:i:s');
	$updateSQL = sprintf(
		"UPDATE smc_turma SET turma_resultado_consolidado='S', turma_data_consolidado='$timestamp' WHERE turma_id=%s AND turma_id_escola=%s",
		GetSQLValueString($codTurma, "int"),
		GetSQLValueString($row_EscolaLogada['escola_id'], "int")
	);
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


	echo "<script>location.reload()</script>";


	mysql_free_result($UsuLogado);

	mysql_free_result($EscolaLogada);

	mysql_free_result($AlunoBoletim);

	mysql_free_result($CriteriosAvaliativos);

	mysql_free_result($Matriz);

	mysql_free_result($disciplinasMatriz);
}
?>