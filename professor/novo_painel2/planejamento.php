<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php
// Inicialize as variáveis necessárias
$escola_id = isset($_GET['escola']) ? anti_injection($_GET['escola']) : null;
$order_clause = $escola_id ? "ORDER BY (escola_id = :escola_id) DESC, escola_nome ASC" : "ORDER BY escola_nome ASC";

// Consulta para escolas
$query_escolas = "
    SELECT 
        ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
        escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
    WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
    GROUP BY escola_id
    $order_clause";

$stmt_escolas = $SmecelNovo->prepare($query_escolas);
$stmt_escolas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
$stmt_escolas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
if ($escola_id) {
	$stmt_escolas->bindValue(':escola_id', intval($escola_id), PDO::PARAM_INT);
}
$stmt_escolas->execute();
$escolas = $stmt_escolas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para turmas se escola foi fornecida
if ($escola_id) {
	$query_turmas = "
        SELECT 
            ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
            ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, escola_id, escola_nome,
            CASE turma_turno
                WHEN 0 THEN 'INTEGRAL'
                WHEN 1 THEN 'MATUTINO'
                WHEN 2 THEN 'VESPERTINO'
                WHEN 3 THEN 'NOTURNO'
            END AS turma_turno_nome 
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
        WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_escola = :escola AND ch_lotacao_professor_id = :professor_id
        GROUP BY turma_id
        ORDER BY turma_turno, turma_etapa, turma_nome ASC";

	$stmt_turmas = $SmecelNovo->prepare($query_turmas);
	$stmt_turmas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
	$stmt_turmas->bindValue(':escola', $escola_id, PDO::PARAM_INT);
	$stmt_turmas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
	$stmt_turmas->execute();
	$turmas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);
}

// Consulta para componentes se turma foi fornecida
if (isset($_GET['turma'])) {
	$turma_id = anti_injection($_GET['turma']);

	$query_componente = "
        SELECT 
            ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
            ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, turma_matriz_id, disciplina_id, disciplina_nome
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
        WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_turma_id = :turma AND ch_lotacao_professor_id = :professor_id
        GROUP BY disciplina_id
        ORDER BY disciplina_nome ASC";

	$stmt_componente = $SmecelNovo->prepare($query_componente);
	$stmt_componente->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
	$stmt_componente->bindValue(':turma', $turma_id, PDO::PARAM_INT);
	$stmt_componente->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
	$stmt_componente->execute();
	$componentes = $stmt_componente->fetchAll(PDO::FETCH_ASSOC);
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
	<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
	<link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
			<p><a href="planejamento_mapa.php" class="ls-btn ls-ico-chevron-left">Voltar</a><a href="planejamentov2.php"
					class="ls-btn-primary">PLANEJAMENTO DE AULA QUINZENAL</a></p>




			<div class="ls-box1">
				<hr>

				<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
					<a href="#" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false">
						<?php
						$escolaSelecionada = isset($_GET['escola']) ? array_filter($escolas, function ($e) {
							return $e['escola_id'] == $_GET['escola'];
						}) : null;

						echo isset($_GET['escola']) && $escolaSelecionada ? substr(current($escolaSelecionada)['escola_nome'], 0, 30) : "UNIDADE ESCOLAR (" . count($escolas) . ")";
						?>
					</a>
					<ul class="ls-dropdown-nav" aria-hidden="true">
						<?php foreach ($escolas as $escola_item) { ?>
							<li><a href="planejamento.php?escola=<?php echo $escola_item['escola_id']; ?>">
									<?php echo substr($escola_item['escola_nome'], 0, 33); ?>...</a>
							</li>
						<?php } ?>
						<li><a class="ls-color-danger ls-divider" href="planejamento.php">LIMPAR</a></li>
					</ul>
				</div>

				<?php if (isset($_GET['escola'])) { ?>
					<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
						<a href="#" style="background-color:#06C;" class="ls-btn-primary ls-btn-block ls-btn-lg"
							role="combobox" aria-expanded="false">
							<?php
							$turmaSelecionada = isset($_GET['turma']) ? array_filter($turmas, function ($t) {
								return $t['turma_id'] == $_GET['turma'];
							}) : null;

							echo isset($_GET['turma']) && $turmaSelecionada ? substr(current($turmaSelecionada)['turma_nome'], 0, 30) : "TURMAS (" . count($turmas) . ")";
							?>
						</a>
						<ul class="ls-dropdown-nav" aria-hidden="true">
							<?php foreach ($turmas as $turma) { ?>
								<li><a
										href="planejamento.php?escola=<?php echo $_GET['escola']; ?>&turma=<?php echo $turma['turma_id']; ?>">
										<?php echo $turma['turma_nome']; ?></a>
								</li>
							<?php } ?>
							<li><a class="ls-color-danger ls-divider" href="planejamento.php">LIMPAR</a></li>
						</ul>
					</div>
				<?php } ?>

				<?php if (isset($_GET['turma'])) { ?>
					<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
						<a href="#" style="background-color:#066;" class="ls-btn-primary ls-btn-block ls-btn-lg"
							role="combobox" aria-expanded="false">COMPONENTES/CAMPOS DE EXPERIÊNCIA
							(<?php echo count($componentes); ?>)</a>
						<ul class="ls-dropdown-nav" aria-hidden="true">
							<?php foreach ($componentes as $componente_item) { ?>
								<?php
								$query_MatrizDisciplinas = "
                        SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_eixo, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome, disciplina_cor_fundo, disciplina_eixo_id, disciplina_eixo_nome
                        FROM smc_matriz_disciplinas
                        INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
                        LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id
                        WHERE matriz_disciplina_id_matriz = :matriz_id AND disciplina_id = :disciplina_id";
								$stmt_MatrizDisciplinas = $SmecelNovo->prepare($query_MatrizDisciplinas);
								$stmt_MatrizDisciplinas->bindValue(':matriz_id', $componente_item['turma_matriz_id'], PDO::PARAM_INT);
								$stmt_MatrizDisciplinas->bindValue(':disciplina_id', $componente_item['disciplina_id'], PDO::PARAM_INT);
								$stmt_MatrizDisciplinas->execute();
								$matrizDisciplinas = $stmt_MatrizDisciplinas->fetch(PDO::FETCH_ASSOC);

								$disciplinaNome = $componente_item['disciplina_nome'];
								if ($matrizDisciplinas && $matrizDisciplinas['disciplina_eixo_nome']) {
									$disciplinaNome .= " - ({$matrizDisciplinas['disciplina_eixo_nome']})";
								}
								?>
								<li><a href="planejamento_lancar_novo.php?escola=<?php echo $_GET['escola']; ?>&etapa=<?php echo $componente_item['turma_etapa']; ?>&componente=<?php echo $componente_item['disciplina_id']; ?>">
										<?php echo $disciplinaNome; ?></a>
								</li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
			</div>

		</div>
		<?php //include_once "inc/footer.php"; ?>
	</main>
	<?php include_once "inc/notificacoes.php"; ?>
	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
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