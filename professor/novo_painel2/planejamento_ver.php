<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";
include('../../sistema/funcoes/inverteData.php');

$colname_Escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";

try {
	// Consultar planejamentos antigos
	$stmtAC = $SmecelNovo->prepare(
		"SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_turma, ac_id_etapa, ac_ano_letivo, ac_data_inicial, 
        ac_data_final, ac_conteudo, ac_criacao, ac_status, ac_correcao, ac_feedback, disciplina_id, disciplina_nome, 
        etapa_id, etapa_nome, etapa_nome_abrev, escola_id, escola_nome, turma_id, turma_nome, 
        CASE ac_status 
            WHEN 0 THEN '<span class=\"ls-tag-primary\">NOVO</span>' 
            WHEN 1 THEN '<span class=\"ls-tag-primary\">VISUALIZADO</span>' 
        END AS ac_status, 
        CASE ac_correcao 
            WHEN 0 THEN '' 
            WHEN 1 THEN '<span class=\"ls-tag-warning\">NECESSITA CORREÇÃO</span>' 
            WHEN 2 THEN '<span class=\"ls-tag-success\">CORREÇÃO REALIZADA</span>' 
        END AS ac_correcao  
        FROM smc_ac 
        LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente  
        LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa 
        LEFT JOIN smc_escola ON escola_id = ac_id_escola 
        LEFT JOIN smc_turma ON turma_id = ac_id_turma 
        WHERE ac_id_professor = :professor_id AND ac_ano_letivo = :ano_letivo 
        ORDER BY ac_data_inicial DESC"
	);
	$stmtAC->execute([
		':professor_id' => $row_ProfLogado['func_id'],
		':ano_letivo' => $row_AnoLetivo['ano_letivo_ano']
	]);
	$resultAC = $stmtAC->fetchAll(PDO::FETCH_ASSOC);
	$totalRows_AC = count($resultAC);

	// Consultar planejamentos novos
	$stmtNewPlan = $SmecelNovo->prepare(
		"SELECT p.smc_id_planejamento, 
				p.smc_planejamento_data_inicial, 
				p.smc_planejamento_data_final, 
				p.smc_ano_letivo, 
				p.smc_id_professor, 
				p.planejamento_status,
				p.smc_planejamento_correcao,
				p.smc_id_escola,  -- Adicionando o ID da escola
				p.smc_id_turma,   -- Adicionando o ID da turma
				e.escola_nome,
				t.turma_nome,
				CASE p.planejamento_status 
					WHEN 0 THEN '<span class=\"ls-tag-primary\">NOVO</span>' 
					WHEN 1 THEN '<span class=\"ls-tag-primary\">VISUALIZADO</span>' 
				END AS status_formatado,
				CASE smc_planejamento_correcao 
					WHEN 0 THEN '' 
					WHEN 1 THEN '<span class=\"ls-tag-warning\">NECESSITA CORREÇÃO</span>' 
					WHEN 2 THEN '<span class=\"ls-tag-success\">CORREÇÃO REALIZADA</span>' 
				END AS planejamento_correcao 
		FROM smc_planejamento p
		LEFT JOIN smc_escola e ON p.smc_id_escola = e.escola_id
		LEFT JOIN smc_turma t ON p.smc_id_turma = t.turma_id
		WHERE p.smc_id_professor = :professor_id 
		  AND p.smc_ano_letivo = :ano_letivo 
		ORDER BY p.smc_planejamento_data_inicial DESC"
	);

	$stmtNewPlan->execute([
		':professor_id' => $row_ProfLogado['func_id'],
		':ano_letivo' => $row_AnoLetivo['ano_letivo_ano']
	]);
	$resultNewPlan = $stmtNewPlan->fetchAll(PDO::FETCH_ASSOC);
	$totalRows_NewPlan = count($resultNewPlan);



} catch (PDOException $e) {
	die("Erro ao consultar planejamento: " . $e->getMessage());
}

// Consulta para escolas
$query_escolas = "
    SELECT 
        ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
        escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
    WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
    GROUP BY escola_id";

$stmt_escolas = $SmecelNovo->prepare($query_escolas);
$stmt_escolas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
$stmt_escolas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
$stmt_escolas->execute();
$escolas = $stmt_escolas->fetchAll(PDO::FETCH_ASSOC);
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
</head>

<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
			<p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="planejamento.php"
					class="ls-btn-primary">NOVO PLANEJAMENTO</a> <a href="planejamento_turma.php"
					class="ls-btn-primary">PLANEJAMENTO POR TURMA</a> <a href="planejamento_mapa.php"
					class="ls-btn-primary ls-ico-calendar ls-ico-right"></a></p>

			<hr>
			<h3 class="ls-title-3">LISTA DE PLANEJAMENTO</h3>
			<hr>
			<h1 id="status"></h1>
			<div class="ls-tabs-btn">
				<ul class="ls-tabs-btn-nav">
					<?php if ($totalRows_NewPlan > 0) { ?>
						<li class="col-md-3 col-sm-6 col-xs-6 ls-active">
							<label class="ls-btn" data-ls-module="button" data-target="#tabNovos">
								Planejamentos (Nova versão) <input type="radio" name="btn" checked>
							</label>
						</li>
					<?php } ?>
					<li class="col-md-3 col-sm-6 col-xs-6 <?php echo ($totalRows_NewPlan == 0) ? 'ls-active' : ''; ?>">
						<label class="ls-btn" data-ls-module="button" data-target="#tabAntigos">
							Planejamentos <input type="radio" name="btn" <?php echo ($totalRows_NewPlan == 0) ? 'checked' : ''; ?>>
						</label>
					</li>
				</ul>

				<div class="ls-tabs-container">
					<?php if ($totalRows_NewPlan > 0) { ?>
						<div id="tabNovos" class="ls-tab-content ls-active">
							<h3>Planejamentos Novos</h3>
							<table class="ls-table">
								<thead>
									<tr>
										<th class="ls-txt-center" width="130">INTERVALO</th>
										<th class="ls-txt-center" width="350">ESCOLA</th>
										<th class="ls-txt-center" width="200">TURMA</th>
										<th class="ls-txt-center">STATUS</th>
										<th class="ls-txt-center">CORREÇÃO</th>
										<th class="ls-txt-center" width="100">AÇÕES</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($resultNewPlan as $plan) { ?>
										<tr id="linhav2-<?php echo $plan['smc_id_planejamento']; ?>">
											<td class="ls-txt-center">
												<?php echo date('d/m/Y', strtotime($plan['smc_planejamento_data_inicial'])); ?>
												até
												<?php echo date('d/m/Y', strtotime($plan['smc_planejamento_data_final'])); ?>
											</td>
											<td class="ls-txt-center">
												<?php echo htmlspecialchars($plan['escola_nome'], ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<td class="ls-txt-center">
												<?php echo htmlspecialchars($plan['turma_nome'], ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<td class="ls-txt-center">
												<?php echo $plan['status_formatado']; ?>
											</td>
											<td class="ls-txt-center">
												<?php echo $plan['planejamento_correcao'] ?>
											</td>
											<td class="ls-txt-center">
												<div data-ls-module="dropdown" class="ls-dropdown ls-pos-right">
													<a href="#" class="ls-btn ls-btn-xs"></a>
													<ul class="ls-dropdown-nav">
														<li>
															<a target="_blank"
																href="planejamentov2_imprimir.php?escola=<?php echo $plan['smc_id_escola']; ?>&plan=<?php echo $plan['smc_id_planejamento']; ?>&turma=<?php echo $plan['smc_id_turma']; ?>">Imprimir
																planejamento</a>
														</li>
														<li>
															<a href="planejamentov2_editar.php?escola=<?php echo $plan['smc_id_escola']; ?>&plan=<?php echo $plan['smc_id_planejamento']; ?>&turma=<?php echo $plan['smc_id_turma']; ?>"
																class="">Editar
																planejamento</a>
														</li>
														<li>
															<a href="#" data-ls-module="modal"
																data-target="#modalDuplicarPlanejamento"
																class="duplicar-planejamento"
																data-plan-id="<?php echo $plan['smc_id_planejamento']; ?>"
																data-prof-id="<?php echo $row_ProfLogado['func_id']; ?>">Duplicar
																planejamento</a>
														</li>
														<li>
															<a href=" #" id="<?php echo $plan['smc_id_planejamento']; ?>"
																prof=" <?php echo $row_ProfLogado['func_id']; ?>"
																class="s-ico-remove deletar1">Deletar</a>
														</li>
													</ul>
												</div>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>

						</div>
					<?php } ?>
					<div id="tabAntigos" class="ls-tab-content
						<?php echo ($totalRows_NewPlan == 0) ? 'ls-active' : ''; ?>">
						<h3>Planejamentos Antigos</h3>
						<table class="ls-table">
							<thead>
								<tr>
									<th class="ls-txt-center" width="130">INTERVALO</th>
									<th class="ls-txt-center ls-display-none-xs" width="60">DIAS</th>
									<th class="ls-txt-center" width="350">ESCOLA</th>
									<th class="ls-txt-center " width="200">COMPONENTE</th>
									<th class="ls-txt-center" width="200">ETAPA (TURMA)</th>
									<th class="ls-txt-center ls-display-none-xs" width="130">STATUS</th>
									<th class="ls-txt-center" width="130">CORREÇÃO</th>
									<th class="ls-txt-center" width="100">AÇÕES</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($resultAC as $plan) {
									$link = empty($plan['ac_id_turma']) ?
										"planejamento_editar.php?escola={$plan['ac_id_escola']}&etapa={$plan['etapa_id']}&componente={$plan['ac_id_componente']}&ac={$plan['ac_id']}" :
										"planejamento_editar_turma.php?escola={$plan['ac_id_escola']}&etapa={$plan['etapa_id']}&componente={$plan['ac_id_componente']}&ac={$plan['ac_id']}&turma={$plan['ac_id_turma']}";
									?>
									<tr id="linha-<?php echo $plan['ac_id']; ?>">
										<td class=" ls-txt-center">
											<?php echo date('d/m/Y', strtotime($plan['ac_data_inicial'])); ?>
											até
											<?php echo date('d/m/Y', strtotime($plan['ac_data_final'])); ?>
										</td>
										<td class="ls-txt-center ls-display-none-xs">
											<?php $diferenca = strtotime($plan['ac_data_final']) - strtotime($plan['ac_data_inicial']);
											echo $dias = floor($diferenca / (60 * 60 * 24)) + 1; ?>
										</td>
										<td class="ls-txt-center">
											<?php echo htmlspecialchars($plan['escola_nome'], ENT_QUOTES, 'UTF-8'); ?>
										</td>
										<td class="ls-txt-center">
											<?php echo htmlspecialchars($plan['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>
										</td>
										<td class="ls-txt-center">
											<?php echo mb_convert_encoding($plan['etapa_nome'], 'UTF-8', 'ISO-8859-1'); ?>
											(<?php echo htmlspecialchars($plan['turma_nome'], ENT_QUOTES, 'UTF-8'); ?>)
										</td>
										<td class=" ls-txt-center ls-display-none-xs"> <span
												class="ls-tag-primary"><?php echo $plan['ac_status']; ?>
											</span>
										</td>
										<td class="ls-txt-center">
											<?php echo ($plan['ac_correcao'] != '') ? '<span class="ls-tag-warning">' . $plan['ac_correcao'] . '</span>' : ''; ?>
										</td>
										<td class="ls-txt-center">
											<div data-ls-module="dropdown" class="ls-dropdown ls-pos-right">
												<a href="#" class="ls-btn ls-btn-xs"></a>
												<ul class="ls-dropdown-nav">
													<li>
														<a target="_blank"
															href="planejamento_imprimir.php?escola=<?php echo $plan['ac_id_escola']; ?>&ac=<?php echo $plan['ac_id']; ?>&etapa=<?php echo $plan['etapa_id']; ?>&turma=<?php echo $plan['ac_id_turma']; ?>">Imprimir
															planejamento</a>
													</li>
													<li>
														<a href="<?php echo $link; ?>" class="">Editar
															planejamento</a>
													</li>
													<li>
														<a href="#" id="<?php echo $plan['ac_id']; ?>"
															prof=" <?php echo $row_ProfLogado['func_id']; ?>"
															class="s-ico-remove deletar">Deletar</a>
													</li>
												</ul>
											</div>
										</td>

									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>

				</div>
			</div>




		</div>
		<?php //include_once "inc/footer.php"; ?>
	</main>

	<!-- Modal de Duplicação -->
	<div class="ls-modal" id="modalDuplicarPlanejamento">
		<div class="ls-modal-box">
			<div class="ls-modal-header">
				<h4 class="ls-modal-title">Duplicar Planejamento</h4>
				<button data-dismiss="modal" class="ls-close-modal ls-ico-close"></button>
			</div>
			<div class="ls-modal-body">
				<form id="formDuplicarPlanejamento" method="POST"
					action="crud/planejamento/duplicar_planejamento_quinzenal.php" class="ls-form">
					<input type="hidden" name="plan_id" id="plan_id">
					<input type="hidden" name="professor_id" id="professor_id">

					<label class="ls-label col-md-4">
						<span class="ls-label-text">Escola</span>
						<div class="ls-custom-select">
							<select name="escola_id" id="escola_id" class="ls-select" required
								onchange="carregarTurmas()">
								<option value="">Selecione uma escola</option>
								<?php foreach ($escolas as $escola) { ?>
									<option value="<?php echo $escola['escola_id']; ?>">
										<?php echo htmlspecialchars($escola['escola_nome']); ?>
									</option>
								<?php } ?>
							</select>
						</div>
					</label>

					<label class="ls-label col-md-4">
						<span class="ls-label-text">Turma</span>
						<div class="ls-custom-select">
							<select name="turma_id" id="turma_id" class="ls-select" required
								onchange="carregarComponentes()">
								<option value="">Selecione uma turma</option>
							</select>
						</div>
					</label>

					<label class="ls-label col-md-4">
						<span class="ls-label-text">Componente</span>
						<div class="ls-custom-select">
							<select name="componente_id" id="componente_id" class="ls-select" required>
								<option value="">Selecione um componente</option>
							</select>
						</div>
					</label>

					<label class="ls-label col-md-6">
						<span class="ls-label-text">Observação (opcional)</span>
						<textarea name="observacao" id="observacao" class="ls-textarea" rows="4"
							placeholder="Digite uma observação, se necessário"></textarea>
					</label>
				</form>
			</div>
			<div class="ls-modal-footer">
				<button type="button" class="ls-btn ls-btn-default" data-dismiss="modal">Cancelar</button>
				<button type="submit" form="formDuplicarPlanejamento" class="ls-btn ls-btn-primary">Duplicar</button>
			</div>
		</div>
	</div>

	<?php include_once "inc/notificacoes.php"; ?>
	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>

	<!-- duplicar planejamento quinzenal -->
	<script>
		document.querySelectorAll('.duplicar-planejamento').forEach(button => {
			button.addEventListener('click', function (e) {
				e.preventDefault();
				const planId = this.getAttribute('data-plan-id');
				const profId = this.getAttribute('data-prof-id');

				document.getElementById('plan_id').value = planId;
				document.getElementById('professor_id').value = profId;

				console.log('Botão clicado - plan_id:', planId, 'professor_id:', profId);
			});
		});

		// Adicionar log ao enviar o formulário
		document.getElementById('formDuplicarPlanejamento').addEventListener('submit', function (e) {
			const componenteId = document.getElementById('componente_id').value;
			console.log('Formulário enviado - componente_id:', componenteId);
		});

		function carregarTurmas() {
			const escolaId = document.getElementById('escola_id').value;
			const professorId = document.getElementById('professor_id').value;
			const turmaSelect = document.getElementById('turma_id');
			turmaSelect.innerHTML = '<option value="">Carregando...</option>';

			fetch(`consultas/get_turmas.php?escola_id=${escolaId}&professor_id=${professorId}&ano_letivo=<?php echo ANO_LETIVO; ?>`)
				.then(response => response.json())
				.then(turmas => {
					console.log('Turmas recebidas:', turmas);
					turmaSelect.innerHTML = '<option value="">Selecione uma turma</option>';
					turmas.forEach(turma => {
						turmaSelect.innerHTML += `<option value="${turma.turma_id}">${turma.turma_nome} (${turma.turma_turno_nome})</option>`;
					});
				})
				.catch(error => console.error('Erro ao carregar turmas:', error));
		}

		function carregarComponentes() {
			const turmaId = document.getElementById('turma_id').value;
			const professorId = document.getElementById('professor_id').value;
			const componenteSelect = document.getElementById('componente_id');
			componenteSelect.innerHTML = '<option value="">Carregando...</option>';

			fetch(`consultas/get_componentes.php?turma_id=${turmaId}&professor_id=${professorId}&ano_letivo=<?php echo ANO_LETIVO; ?>`)
				.then(response => {
					return response.text().then(text => {
						console.log('Resposta bruta de get_componentes.php:', text);
						return text;
					});
				})
				.then(text => JSON.parse(text))
				.then(componentes => {
					console.log('Componentes parsed:', componentes);
					componenteSelect.innerHTML = '<option value="">Selecione um componente</option>';
					componentes.forEach(comp => {
						componenteSelect.innerHTML += `<option value="${comp.disciplina_id}">${comp.disciplina_nome}</option>`;
					});
				})
				.catch(error => console.error('Erro ao carregar componentes:', error));
		}
	</script>
	<script type="application/javascript">

		$(document).ready(function () {
			$(".deletar").on('click', function () {


				var id = $(this).attr('id');
				var prof = $(this).attr('prof');


				Swal.fire({
					title: 'Deletar este planejamento?',
					text: "Esta ação não poderá ser desfeita.",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Sim, deletar!'
				}).then((result) => {
					if (result.isConfirmed) {


						$.ajax({
							type: 'POST',
							url: 'crud/planejamento/delete.php',
							data: {
								id: id,
								prof: prof
							},
							success: function (data) {

								$("#linha-" + id).remove();

								$('#status').html(data);

								setTimeout(function () {




									//location.reload();					
								}, 2000);

							}
						})

						return true;







					}
				})




			});
		});

		$(document).ready(function () {
			$(".deletar1").on('click', function () {


				var id = $(this).attr('id');
				var prof = $(this).attr('prof');


				Swal.fire({
					title: 'Deletar este planejamento?',
					text: "Esta ação não poderá ser desfeita.",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Sim, deletar!'
				}).then((result) => {
					if (result.isConfirmed) {


						$.ajax({
							type: 'POST',
							url: 'crud/planejamento/deletev2.php',
							data: {
								id: id,
								prof: prof
							},
							success: function (data) {

								$("#linhav2-" + id).remove();

								$('#status').html(data);

								setTimeout(function () {




									//location.reload();					
								}, 2000);

							}
						})

						return true;







					}
				})




			});
		});


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