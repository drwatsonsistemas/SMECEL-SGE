<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";
include "../../sistema/escola/fnc/inverteData.php";

try {
	// Parâmetros recebidos via GET
	$colname_Escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
	$colname_Aluno = isset($_GET['aluno']) ? anti_injection($_GET['aluno']) : "-1";
	$colname_Target = isset($_GET['target']) ? anti_injection($_GET['target']) : "-1";
	$colname_Turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
	$colname_Aula = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
	$data = isset($_GET['data']) ? anti_injection($_GET['data']) : null;

	if ($data) {
		$semana = date("w", strtotime($data));
		$diasemana = ['DOMINGO', 'SEGUNDA', 'TERÇA', 'QUARTA', 'QUINTA', 'SEXTA', 'SÁBADO'];
		$dia_semana_nome = $diasemana[$semana];
	}

	$todas = isset($_GET['todas']) ? anti_injection($_GET['todas']) : "s";
	$codTurma = isset($_GET['ct']) ? anti_injection($_GET['ct']) : "";
	if ($codTurma === "") {
		header("Location: turmasAlunosVinculados.php?nada");
		exit;
	}
	$codTurma = (int) $codTurma;

	// Consulta: Escola Logada
	$stmtEscolaLogada = $SmecelNovo->prepare("
        SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
               escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,
               sec_id, sec_cidade, sec_uf
        FROM smc_escola
        INNER JOIN smc_sec ON sec_id = escola_id_sec
        WHERE escola_id = :escola
    ");
	$stmtEscolaLogada->execute(['escola' => $colname_Escola]);
	$row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);

	// Consulta: Ano Letivo
	$anoLetivo = isset($row_AnoLetivo['ano_letivo_ano']) ? $row_AnoLetivo['ano_letivo_ano'] : null;
	if (isset($_GET['ano']) && $_GET['ano'] !== "") {
		$anoLetivo = (int) anti_injection($_GET['ano']);
	}

	// Consulta: Aluno Boletim
	$stmtAlunoBoletim = $SmecelNovo->prepare("
        SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
               vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash,
               vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
               aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_foto, aluno_hash,
               turma_id, turma_nome, turma_matriz_id, turma_turno, turma_etapa
        FROM smc_vinculo_aluno
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
        INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
        WHERE vinculo_aluno_situacao = '1'
          AND vinculo_aluno_ano_letivo = :anoLetivo
          AND vinculo_aluno_id_escola = :escola
          AND vinculo_aluno_id = :aluno
          AND turma_id = :turma
        ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC, aluno_nome ASC
    ");
	$stmtAlunoBoletim->execute([
		'anoLetivo' => $anoLetivo,
		'escola' => $colname_Escola,
		'aluno' => $colname_Aluno,
		'turma' => $codTurma
	]);
	$row_AlunoBoletim = $stmtAlunoBoletim->fetch(PDO::FETCH_ASSOC);

	if (!$row_AlunoBoletim) {
		echo "<h3><center>Sem dados.<br><a href=\"javascript:window.close()\">Fechar</a></center></h3>";
		exit;
	}

	// Consulta: Matriz
	$stmtMatriz = $SmecelNovo->prepare("
        SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash,
               matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia,
               matriz_criterio_avaliativo
        FROM smc_matriz
        WHERE matriz_id = :matrizId
    ");
	$stmtMatriz->execute(['matrizId' => $row_AlunoBoletim['turma_matriz_id']]);
	$row_Matriz = $stmtMatriz->fetch(PDO::FETCH_ASSOC);

	// Consulta: Critérios Avaliativos
	$stmtCriteriosAvaliativos = $SmecelNovo->prepare("
        SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av,
               ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media,
               ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes
        FROM smc_criterios_avaliativos
        WHERE ca_id = :criterioId
    ");
	$stmtCriteriosAvaliativos->execute(['criterioId' => $row_Matriz['matriz_criterio_avaliativo']]);
	$row_CriteriosAvaliativos = $stmtCriteriosAvaliativos->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

// Funções auxiliares
function diffMonth($from, $to)
{
	$fromYear = date("Y", strtotime($from));
	$fromMonth = date("m", strtotime($from));
	$toYear = date("Y", strtotime($to));
	$toMonth = date("m", strtotime($to));
	return $fromYear == $toYear ? ($toMonth - $fromMonth) + 1 : (12 - $fromMonth) + 1 + $toMonth;
}

function nomeMes($numero)
{
	$meses = ["JAN", "FEV", "MAR", "ABR", "MAI", "JUN", "JUL", "AGO", "SET", "OUT", "NOV", "DEZ"];
	return isset($meses[$numero - 1]) ? $meses[$numero - 1] : "";
}
?>

<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());

		gtag('config', 'UA-117872281-1');
	</script>
	<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
	<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">
	<link rel="stylesheet" href="css/sweetalert2.min.css">
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
			font-size: 9px;
		}
	</style>
</head>

<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>


			<p><a href="frequencia.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=<?php echo $colname_Target; ?>&data=<?php echo $data; ?>&todas=s"
					class="ls-btn ls-ico-chevron-left">Voltar</a></p>


			<?php do { ?>

				<div style="page-break-inside: avoid;">

					<?php
					// Prepara consulta para buscar faltas gerais
					$stmtFaltas = $SmecelNovo->prepare(
						"SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, 
                    faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa 
             FROM smc_faltas_alunos
             WHERE faltas_alunos_data BETWEEN :inicio AND :fim
             AND faltas_alunos_matricula_id = :matricula"
					);
					$stmtFaltas->execute([
						'inicio' => $row_AnoLetivo['ano_letivo_inicio'],
						'fim' => $row_AnoLetivo['ano_letivo_fim'],
						'matricula' => $row_AlunoBoletim['vinculo_aluno_id']
					]);
					$faltas = $stmtFaltas->fetchAll(PDO::FETCH_ASSOC);

					// Prepara consulta para buscar faltas agrupadas por disciplina
					$stmtFaltasA = $SmecelNovo->prepare(
						"SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, 
                    disciplina_id, disciplina_nome, COUNT(*) AS total
             FROM smc_faltas_alunos
             INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
             WHERE faltas_alunos_data BETWEEN :inicio AND :fim
             AND faltas_alunos_justificada = 'N'
             AND faltas_alunos_matricula_id = :matricula
             GROUP BY faltas_alunos_disciplina_id"
					);
					$stmtFaltasA->execute([
						'inicio' => $row_AnoLetivo['ano_letivo_inicio'],
						'fim' => $row_AnoLetivo['ano_letivo_fim'],
						'matricula' => $row_AlunoBoletim['vinculo_aluno_id']
					]);
					$faltasAgrupadas = $stmtFaltasA->fetchAll(PDO::FETCH_ASSOC);
					$totalRows_FaltasA = $stmtFaltasA->rowCount();
					// Processa as datas de faltas para o mapa
					$datas = [];
					foreach ($faltas as $falta) {
						$datas[] = $falta['faltas_alunos_data'] . "-" . $falta['faltas_alunos_numero_aula'] . "-" . $falta['faltas_alunos_justificada'];
					}

					// Cálculo de meses
					$totalMeses = diffMonth($row_AnoLetivo['ano_letivo_inicio'], $row_AnoLetivo['ano_letivo_fim']);
					$ano = $row_AnoLetivo['ano_letivo_ano'];
					$mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					$anoInicio = date("y", strtotime($row_AnoLetivo['ano_letivo_inicio']));
					?>


					<p>

					<div class="ls-box1">
						<span class="ls-float-right" style="margin-left:20px;">
							<?php if ($row_AlunoBoletim['aluno_foto'] == "") { ?>
								<img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" style="margin:1mm;width:15mm;">
							<?php } else { ?>
								<img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_AlunoBoletim['aluno_foto']; ?>"
									style="margin:1mm;width:15mm;">
							<?php } ?>
						</span> <small> Aluno(a): <strong><?php echo $row_AlunoBoletim['aluno_nome']; ?></strong><br>
							Nascimento:
							<strong><?php echo inverteData($row_AlunoBoletim['aluno_nascimento']); ?></strong><br>
							Filiação: <strong><?php echo $row_AlunoBoletim['aluno_filiacao1']; ?></strong><br>
							Turma: <strong><?php echo $row_AlunoBoletim['turma_nome']; ?></strong><br>
							Data da matrícula:
							<strong><?php echo date("d/m/Y", strtotime($row_AlunoBoletim['vinculo_aluno_data'])); ?></strong>
							<?php if ($row_AlunoBoletim['vinculo_aluno_situacao'] <> "1") { ?>Matrícula encerrada em
								<?php echo date("d/m/Y", strtotime($row_AlunoBoletim['vinculo_aluno_datatransferencia'])); ?>
							<?php } ?>
						</small>


					</div>
					</p>
					<p class="ls-ico-text ls-txt-center">FREQUENCIA ESCOLAR
						<?php echo $row_AlunoBoletim['vinculo_aluno_ano_letivo']; ?>
					</p>

					<?php if ($row_AlunoBoletim['vinculo_aluno_data'] > $row_AnoLetivo['ano_letivo_inicio']) { ?><br><strong><small>*Atenção:
								Esta matrícula foi realizada após o início do ano letivo<br><br></small></strong><?php } ?>


					<table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">

						<tr class="ls-txt-center">

							<!-- LINHA MESES -->
							<td>Meses</td>
							<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
								<td width="150"><?php echo nomeMes($mesInicio); ?>/<?php echo $anoInicio; ?></td>
								<?php $mesFalta[$mesInicio] = 0; ?>
								<?php
								if ($mesInicio == 12) {
									$mesInicio = 1;
									$anoInicio++;
								} else {
									$mesInicio++;
								}
								?>
							<?php } ?>
							<?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
						</tr>

						<!-- LINHA AULAS -->
						<tr class="ls-txt-center">
							<td>Aulas</td>
							<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
								<td width="">
									<div style="width:100%; padding:0; margin:0">
										<?php $perc = 100 / $row_Matriz['matriz_aula_dia']; ?>
										<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
											<div class=""
												style="width:<?php echo $perc; ?>%; float:left; border-left:#999 solid 1px; border-right:#999 solid 1px; padding:0; margin:0; background-color:#CCCCCC;">
												<?php echo $aulaCont; ?>
											</div>
										<?php } ?>
									</div>
								</td>
								<?php
								if ($mesInicio == 12) {
									$mesInicio = 1;
									$anoInicio++;
								} else {
									$mesInicio++;
								}
								?>
							<?php } ?>
							<?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>

						</tr>


						<!-- DIAS -->
						<?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
						<?php for ($diaCont = 1; $diaCont <= 31; $diaCont++) { ?>

							<tr class="ls-txt-center">
								<td><?php echo $diaCont; ?></td>
								<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
									<td width="">


										<?php $dataAgora = $anoInicio . "-" . str_pad($mesInicio, 2, "0", STR_PAD_LEFT) . "-" . str_pad($diaCont, 2, "0", STR_PAD_LEFT); ?>

										<?php for ($aulaCont = 1; $aulaCont <= $row_Matriz['matriz_aula_dia']; $aulaCont++) { ?>
											<div class=""
												style="width:<?php echo $perc; ?>%; float:left; border-left:#999 solid 1px; border-right:#999 solid 1px;">
												<?php
												if ($row_AlunoBoletim['vinculo_aluno_data'] > $dataAgora) {
													echo "<span class=\"ls-ico-minus\"></span>";
												} else {


													if (in_array($dataAgora . "-" . $aulaCont . "-N", $datas)) {

														//echo "<span class=\"ls-ico-close ls-color-danger\"></span>";
														echo "<strong><span class=\"ls-color-danger\">X</span></strong>";
														$mesFalta[$mesInicio]++;

													} else if (in_array($dataAgora . "-" . $aulaCont . "-S", $datas)) {
														echo "<strong><span class=\"ls-color-warning ls-ico-info\"></span></strong>";
													} else {

														echo "&#8226;";
													}





												}
												?>
											</div>
										<?php } ?>
									</td>
									<?php
									if ($mesInicio == 12) {
										$mesInicio = 1;
										$anoInicio++;
									} else {
										$mesInicio++;
									}
									?>
								<?php } ?>
								<?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
								<?php $anoInicio = date("Y", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
							</tr>
						<?php } ?>


						<!-- LINHA MESES RODAPÉ-->
						<tr class="ls-txt-center">

							<td>FALTAS</td>
							<?php for ($mesCont = 1; $mesCont <= $totalMeses; $mesCont++) { ?>
								<td width="150">Faltas: <strong><?php echo $mesFalta[$mesInicio]; ?></strong></td>
								<?php
								if ($mesInicio == 12) {
									$mesInicio = 1;
									$anoInicio++;
								} else {
									$mesInicio++;
								}
								?>
							<?php } ?>
							<?php $mesInicio = date("m", strtotime($row_AnoLetivo['ano_letivo_inicio'])); ?>
						</tr>

					</table>

					<br>

					<?php $totalFaltas = 0; ?>
					<?php if (!empty($faltasAgrupadas)) { ?>
						<table width="100%" class="ls-sm-space bordasimples" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<th>COMPONENTE</th>
								<th>TOTAL DE FALTAS</th>
							</tr>
							<?php foreach ($faltasAgrupadas as $falta) { ?>
								<tr>
									<td><?php echo $falta['disciplina_nome']; ?></td>
									<td class="ls-txt-center"><?php echo $falta['total']; ?></td>
								</tr>
								<?php $totalFaltas += $falta['total']; ?>
							<?php } ?>
							<tr>
								<td class="ls-txt-center">TOTAL DE FALTAS NO ANO (EXCETO AS JUSTIFICADAS)</td>
								<td class="ls-txt-center"><strong><?php echo $totalFaltas; ?></strong></td>
							</tr>
						</table>
					<?php } ?>







					<?php
					unset($datas); //exit; 
					//exit; 
					?>

				</div>

				<hr>
				<br><br><br><br>

			<?php } while ($row_AlunoBoletim = $stmtAlunoBoletim->fetch(PDO::FETCH_ASSOC)); ?>











		</div>
		<?php //include_once "inc/footer.php"; ?>
	</main>
	<?php include_once "inc/notificacoes.php"; ?>
	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>
	<script type="application/javascript">
		/*
		Swal.fire({
		  //position: 'top-end',
		  icon: 'success',
		  title: 'Tudo certo por aqui',
		  showConfirmButton: false,
		  timer: 1500
		})
		*/
	</script>
</body>

</html>