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
	$codTurma = (int) $codTurma;
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
WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma ORDER BY turma_turno, turma_etapa, turma_nome ASC";
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
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'UA-117872281-1');
	</script>

	<title>MOVIMENTO ESCOLAR ANUAL | SMECEL - Sistema de Gestão Escolar</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">

	<script src="js/locastyle.js"></script>
	<style>
		table.bordasimples {
			border-collapse: collapse;
			font-size: 7px;
		}

		table.bordasimples tr td {
			border: 1px solid #808080;
			padding: 2px;
			font-size: 12px;
		}

		table.bordasimples tr th {
			border: 1px solid #808080;
			padding: 2px;
			font-size: 16px;
		}

		.foo {

			writing-mode: vertical-lr;
			-webkit-writing-mode: vertical-lr;
			-ms-writing-mode: vertical-lr;

			/* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */

		}

		@media print {
			@page {
				size: A4;
				size: landscape;
			}
		}
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="self.print();">




	<!-- CONTEÚDO -->






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

				<?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
						src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
						width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt=""
						width="60px" /><?php } ?><br>
				<strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
				<small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
					ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>,
					<?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?>
					<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP:
					<?php echo $row_EscolaLogada['escola_cep']; ?><br>
					CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> 	<?php echo $row_EscolaLogada['escola_email']; ?>
					<?php echo $row_EscolaLogada['escola_telefone1']; ?></small>

				<p>
				<h2 class="ls-txt-center">RESULTADOS FINAIS - ANO LETIVO <?php echo $row_Turmas['turma_ano_letivo']; ?></h2>
				</p>





				<table width="100%" class="ls-sm-space ls-table-striped bordasimples">
					<thead>
						<tr>
							<th rowspan="3" width="250px" align="center">TURMAS</th>
							<th colspan="4" align="center">MATRÍCULAS</th>
							<th colspan="4" align="center">DESISTENTES</th>
							<th colspan="4" align="center">FALECIDOS</th>
							<th colspan="4" align="center">TRANSFERIDOS</th>
							<th colspan="4" align="center">APROVADOS</th>
							<th colspan="4" align="center">REPROVADOS</th>
							<th rowspan="3" align="center">TOTAL</th>
						</tr>
						<tr>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
							<th colspan="2" align="center">M</th>
							<th colspan="2" align="center">F</th>
						</tr>
						<tr>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
							<th align="center">N</th>
							<th align="center">R</th>
						</tr>
					</thead>
					<tbody>
						<?php do { ?>
							<?php
							mysql_select_db($database_SmecelNovo, $SmecelNovo);

							$query_Matriculas = "
								SELECT 
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' THEN 1 ELSE 0 END) AS MasculinoRepetenteS,
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' THEN 1 ELSE 0 END) AS MasculinoRepetenteN,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' THEN 1 ELSE 0 END) AS FemininoRepetenteS,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' THEN 1 ELSE 0 END) AS FemininoRepetenteN
								FROM smc_vinculo_aluno 
								INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
								WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
								AND vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
							$row_Matriculas = mysql_fetch_assoc($Matriculas);

							$MatriculasMasculinoRepetenteS = $row_Matriculas['MasculinoRepetenteS'];
							$MatriculasMasculinoRepetenteN = $row_Matriculas['MasculinoRepetenteN'];
							$MatriculasFemininoRepetenteS = $row_Matriculas['FemininoRepetenteS'];
							$MatriculasFemininoRepetenteN = $row_Matriculas['FemininoRepetenteN'];

							$query_Desistentes = "
								SELECT 
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 3 THEN 1 ELSE 0 END) AS MasculinoDesistenteN,
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 3 THEN 1 ELSE 0 END) AS MasculinoDesistenteS,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 3 THEN 1 ELSE 0 END) AS FemininoDesistenteN,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 3 THEN 1 ELSE 0 END) AS FemininoDesistenteS
								FROM smc_vinculo_aluno 
								INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
								WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
								AND vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Desistentes = mysql_query($query_Desistentes, $SmecelNovo) or die(mysql_error());

							// Verifique se a consulta foi executada corretamente
							if (!$Desistentes) {
								die('Query Inválida: ' . mysql_error());
							}

							// Pegue os resultados
							$row_Desistentes = mysql_fetch_assoc($Desistentes);



							$MatriculasMasculinoDesistenteS = $row_Desistentes['MasculinoDesistenteS'];
							$MatriculasMasculinoDesistenteN = $row_Desistentes['MasculinoDesistenteN'];
							$MatriculasFemininoDesistenteS = $row_Desistentes['FemininoDesistenteS'];
							$MatriculasFemininoDesistenteN = $row_Desistentes['FemininoDesistenteN'];

							$query_Falecidos = "
								SELECT 
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 4 THEN 1 ELSE 0 END) AS MasculinoFalecidoS,
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 4 THEN 1 ELSE 0 END) AS MasculinoFalecidoN,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 4 THEN 1 ELSE 0 END) AS FemininoFalecidoS,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 4 THEN 1 ELSE 0 END) AS FemininoFalecidoN
								FROM smc_vinculo_aluno 
								INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
								WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
								AND vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Falecidos = mysql_query($query_Falecidos, $SmecelNovo) or die(mysql_error());

							// Verifique se a consulta foi executada corretamente
							if (!$Falecidos) {
								die('Query Inválida: ' . mysql_error());
							}

							// Pegue os resultados
							$row_Falecidos = mysql_fetch_assoc($Falecidos);

							$MatriculasMasculinoFalecidoS = $row_Falecidos['MasculinoFalecidoS'];
							$MatriculasMasculinoFalecidoN = $row_Falecidos['MasculinoFalecidoN'];
							$MatriculasFemininoFalecidoS = $row_Falecidos['FemininoFalecidoS'];
							$MatriculasFemininoFalecidoN = $row_Falecidos['FemininoFalecidoN'];



							$query_Transferidos = "
								SELECT 
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 2 THEN 1 ELSE 0 END) AS MasculinoTransferidoS,
								SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 2 THEN 1 ELSE 0 END) AS MasculinoTransferidoN,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 2 THEN 1 ELSE 0 END) AS FemininoTransferidoS,
								SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 2 THEN 1 ELSE 0 END) AS FemininoTransferidoN
								FROM smc_vinculo_aluno 
								INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
								WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
								AND vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Transferidos = mysql_query($query_Transferidos, $SmecelNovo) or die(mysql_error());

							// Verifique se a consulta foi executada corretamente
							if (!$Transferidos) {
								die('Query Inválida: ' . mysql_error());
							}

							// Pegue os resultados
							$row_Transferidos = mysql_fetch_assoc($Transferidos);

							$MatriculasMasculinoTransferidoS = $row_Transferidos['MasculinoTransferidoS'];
							$MatriculasMasculinoTransferidoN = $row_Transferidos['MasculinoTransferidoN'];
							$MatriculasFemininoTransferidoS = $row_Transferidos['FemininoTransferidoS'];
							$MatriculasFemininoTransferidoN = $row_Transferidos['FemininoTransferidoN'];

							$query_Aprovados = "SELECT 
    SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 1 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS MasculinoAprovadoS,
    SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 1 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS MasculinoAprovadoN,
    SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 1 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS FemininoAprovadoS,
    SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 1 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS FemininoAprovadoN
FROM smc_vinculo_aluno v
INNER JOIN smc_aluno a ON a.aluno_id = v.vinculo_aluno_id_aluno 
INNER JOIN smc_turma t ON t.turma_id = v.vinculo_aluno_id_turma
INNER JOIN smc_etapa e ON e.etapa_id = t.turma_etapa
WHERE v.vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
AND v.vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Aprovados = mysql_query($query_Aprovados, $SmecelNovo) or die(mysql_error());

							// Verifique se a consulta foi executada corretamente
							if (!$Aprovados) {
								die('Query Inválida: ' . mysql_error());
							}

							// Pegue os resultados
							$row_Aprovados = mysql_fetch_assoc($Aprovados);

							$MatriculasMasculinoAprovadoS = $row_Aprovados['MasculinoAprovadoS'];
							$MatriculasMasculinoAprovadoN = $row_Aprovados['MasculinoAprovadoN'];
							$MatriculasFemininoAprovadoS = $row_Aprovados['FemininoAprovadoS'];
							$MatriculasFemininoAprovadoN = $row_Aprovados['FemininoAprovadoN'];

							$query_Reprovados = "SELECT 
    SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 2 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS MasculinoReprovadoS,
    SUM(CASE WHEN aluno_sexo = 1 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 2 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS MasculinoReprovadoN,
    SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'S' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 2 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS FemininoReprovadoS,
    SUM(CASE WHEN aluno_sexo = 2 AND vinculo_aluno_repetente = 'N' AND vinculo_aluno_situacao = 1 
        AND vinculo_aluno_resultado_final = 2 AND vinculo_aluno_nao_reprova != 'S' 
        AND e.etapa_ef != 'EI' THEN 1 ELSE 0 END) AS FemininoReprovadoN
FROM smc_vinculo_aluno v
INNER JOIN smc_aluno a ON a.aluno_id = v.vinculo_aluno_id_aluno 
INNER JOIN smc_turma t ON t.turma_id = v.vinculo_aluno_id_turma
INNER JOIN smc_etapa e ON e.etapa_id = t.turma_etapa
WHERE v.vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] 
AND v.vinculo_aluno_id_turma = $row_Turmas[turma_id]";

							$Reprovados = mysql_query($query_Reprovados, $SmecelNovo) or die(mysql_error());

							// Verifique se a consulta foi executada corretamente
							if (!$Reprovados) {
								die('Query Inválida: ' . mysql_error());
							}

							// Pegue os resultados
							$row_Reprovados = mysql_fetch_assoc($Reprovados);



							$MatriculasMasculinoReprovadoS = $row_Reprovados['MasculinoReprovadoS'];
							$MatriculasMasculinoReprovadoN = $row_Reprovados['MasculinoReprovadoN'];
							$MatriculasFemininoReprovadoS = $row_Reprovados['FemininoReprovadoS'];
							$MatriculasFemininoReprovadoN = $row_Reprovados['FemininoReprovadoN'];

							// Select the database
							mysql_select_db($database_SmecelNovo, $SmecelNovo);

							// Query to get the etapa_ef for the current class
							$query_EtapaTurma = "
    SELECT e.etapa_ef 
    FROM smc_turma t
    INNER JOIN smc_etapa e ON e.etapa_id = t.turma_etapa
    WHERE t.turma_id = $row_Turmas[turma_id]";
							$EtapaTurma = mysql_query($query_EtapaTurma, $SmecelNovo) or die(mysql_error());
							$row_EtapaTurma = mysql_fetch_assoc($EtapaTurma);
							$etapa_ef = $row_EtapaTurma['etapa_ef'];

							// Check if the class is preschool (etapa_ef = 'EI')
							if ($etapa_ef == 'EI') {
								$MatriculasMasculinoAprovadoS = "-";
								$MatriculasMasculinoAprovadoN = "-";
								$MatriculasFemininoAprovadoS = "-";
								$MatriculasFemininoAprovadoN = "-";
								$MatriculasMasculinoReprovadoS = "-";
								$MatriculasMasculinoReprovadoN = "-";
								$MatriculasFemininoReprovadoS = "-";
								$MatriculasFemininoReprovadoN = "-";
							} else {
								// Use query results for non-preschool classes
								$MatriculasMasculinoAprovadoS = isset($row_Aprovados['MasculinoAprovadoS']) ? $row_Aprovados['MasculinoAprovadoS'] : 0;
								$MatriculasMasculinoAprovadoN = isset($row_Aprovados['MasculinoAprovadoN']) ? $row_Aprovados['MasculinoAprovadoN'] : 0;
								$MatriculasFemininoAprovadoS = isset($row_Aprovados['FemininoAprovadoS']) ? $row_Aprovados['FemininoAprovadoS'] : 0;
								$MatriculasFemininoAprovadoN = isset($row_Aprovados['FemininoAprovadoN']) ? $row_Aprovados['FemininoAprovadoN'] : 0;
								$MatriculasMasculinoReprovadoS = isset($row_Reprovados['MasculinoReprovadoS']) ? $row_Reprovados['MasculinoReprovadoS'] : 0;
								$MatriculasMasculinoReprovadoN = isset($row_Reprovados['MasculinoReprovadoN']) ? $row_Reprovados['MasculinoReprovadoN'] : 0;
								$MatriculasFemininoReprovadoS = isset($row_Reprovados['FemininoReprovadoS']) ? $row_Reprovados['FemininoReprovadoS'] : 0;
								$MatriculasFemininoReprovadoN = isset($row_Reprovados['FemininoReprovadoN']) ? $row_Reprovados['FemininoReprovadoN'] : 0;
							}

							?>
							<tr>
								<td align="center"><?= $row_Turmas['turma_nome'] ?></td>
								<td align="center"><?= $MatriculasMasculinoRepetenteN ?></td>
								<td align="center"><?= $MatriculasMasculinoRepetenteS ?></td>
								<td align="center"><?= $MatriculasFemininoRepetenteN ?></td>
								<td align="center"><?= $MatriculasFemininoRepetenteS ?></td>
								<td align="center"><?= $MatriculasMasculinoDesistenteN ?></td>
								<td align="center"><?= $MatriculasMasculinoDesistenteS ?></td>
								<td align="center"><?= $MatriculasFemininoDesistenteS ?></td>
								<td align="center"><?= $MatriculasFemininoDesistenteN ?></td>
								<td align="center"><?= $MatriculasMasculinoFalecidoN ?></td>
								<td align="center"><?= $MatriculasMasculinoFalecidoS ?></td>
								<td align="center"><?= $MatriculasFemininoFalecidoN ?></td>
								<td align="center"><?= $MatriculasFemininoFalecidoS ?></td>
								<td align="center"><?= $MatriculasMasculinoTransferidoN ?></td>
								<td align="center"><?= $MatriculasMasculinoTransferidoS ?></td>
								<td align="center"><?= $MatriculasFemininoTransferidoS ?></td>
								<td align="center"><?= $MatriculasFemininoTransferidoN ?></td>
								<td align="center"><?= $MatriculasMasculinoAprovadoN ?></td>
								<td align="center"><?= $MatriculasMasculinoAprovadoS ?></td>
								<td align="center"><?= $MatriculasFemininoAprovadoN ?></td>
								<td align="center"><?= $MatriculasFemininoAprovadoS ?></td>
								<td align="center"><?= $MatriculasMasculinoReprovadoN ?></td>
								<td align="center"><?= $MatriculasMasculinoReprovadoS ?></td>
								<td align="center"><?= $MatriculasFemininoReprovadoN ?></td>
								<td align="center"><?= $MatriculasFemininoReprovadoS ?></td>
								<td align="center"></td>
							</tr>
						<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
					</tbody>
				</table>




				<br>

				<small></i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL -
					Sistema de Gestão Escolar</i></small>

			</div>

		</div>

	<?php } ?>






	<!-- CONTEÚDO -->



	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
		type="text/javascript"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Turmas);

mysql_free_result($Matriculas);

mysql_free_result($CriteriosAvaliativos);

mysql_free_result($Matriz);
?>