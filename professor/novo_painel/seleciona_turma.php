<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

try {
	// Configurações iniciais
	$query = "";
	$order_clause = "ORDER BY escola_nome ASC";
	if (isset($_GET['escola'])) {
		$escola_id = anti_injection($_GET['escola']);
		$order_clause = "ORDER BY (escola_id = :escola_id) DESC, escola_nome ASC";
	}

	// Consulta: Escolas
	$query_escolas = "
        SELECT 
        ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
        escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
        WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
        GROUP BY escola_id
        $order_clause
    ";
	$stmt_escolas = $SmecelNovo->prepare($query_escolas);
	$stmt_escolas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
	$stmt_escolas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
	if (isset($_GET['escola'])) {
		$stmt_escolas->bindValue(':escola_id', $escola_id, PDO::PARAM_INT);
	}
	$stmt_escolas->execute();
	$row_escolas = $stmt_escolas->fetchAll(PDO::FETCH_ASSOC);

	if (isset($_GET['escola'])) {
		$escola = anti_injection($_GET['escola']);

		// Consulta: Turmas
		$query_turmas = "
            SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
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
            WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_escola = :escola_id AND ch_lotacao_professor_id = :professor_id
            GROUP BY turma_id
            ORDER BY turma_turno, turma_etapa, turma_nome ASC
        ";
		$stmt_turmas = $SmecelNovo->prepare($query_turmas);
		$stmt_turmas->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
		$stmt_turmas->bindValue(':escola_id', $escola, PDO::PARAM_INT);
		$stmt_turmas->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
		$stmt_turmas->execute();
		$row_turmas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);
	}

	if (isset($_GET['turma'])) {
		$turma = anti_injection($_GET['turma']);

		// Consulta: Componentes
		$query_componente = "
            SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
            ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, turma_etapa, turma_matriz_id, disciplina_id, disciplina_nome
            FROM smc_ch_lotacao_professor
            INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
            INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
            WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_turma_id = :turma_id AND ch_lotacao_professor_id = :professor_id
            GROUP BY disciplina_id
            ORDER BY disciplina_nome ASC
        ";
		$stmt_componente = $SmecelNovo->prepare($query_componente);
		$stmt_componente->bindValue(':ano_letivo', ANO_LETIVO, PDO::PARAM_INT);
		$stmt_componente->bindValue(':turma_id', $turma, PDO::PARAM_INT);
		$stmt_componente->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
		$stmt_componente->execute();
		$row_componente = $stmt_componente->fetchAll(PDO::FETCH_ASSOC);
	}
} catch (PDOException $e) {
	die("Erro ao conectar com o banco de dados: " . $e->getMessage());
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
			<h1 class="ls-title-intro ls-ico-home">SELECIONAR TURMA</h1>
			<p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>



			<div class="ls-box1">
				<hr>

				<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
					<a href="#" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false">
						<?php if (isset($_GET['escola']) && isset($row_escolas[0]['escola_nome'])) {
							echo substr($row_escolas[0]['escola_nome'], 0, 30);
						} else { ?>
							UNIDADE ESCOLAR (<?php echo count($row_escolas); ?>)
						<?php } ?>
					</a>
					<ul class="ls-dropdown-nav" aria-hidden="true">
						<?php
						if (!empty($row_escolas)) {
							foreach ($row_escolas as $escola) {
								if (isset($escola['escola_id'])) {
									$query_vinculo = "
                        SELECT * FROM smc_vinculo 
                        WHERE vinculo_id_escola = :escola_id 
                          AND vinculo_id_funcionario = :func_id 
                          AND vinculo_status = '1' 
                          AND vinculo_acesso = 'N'
                    ";
									$stmt_vinculo = $SmecelNovo->prepare($query_vinculo);
									$stmt_vinculo->bindValue(':escola_id', $escola['escola_id'], PDO::PARAM_INT);
									$stmt_vinculo->bindValue(':func_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
									$stmt_vinculo->execute();
									$vinculo_total = $stmt_vinculo->rowCount();

									if ($vinculo_total == 0 && isset($escola['escola_nome'])) { ?>
										<li><a href="seleciona_turma.php?escola=<?php echo $escola['escola_id']; ?>">
												<?php echo substr($escola['escola_nome'], 0, 33); ?>...</a></li>
									<?php }
								}
							}
						} ?>
						<li><a class="ls-color-danger ls-divider" href="seleciona_turma.php">LIMPAR</a></li>
					</ul>
				</div>


				<?php if (isset($_GET['escola'])) { ?>
					<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
						<a href="#" style="background-color:#06C;" class="ls-btn-primary ls-btn-block ls-btn-lg"
							role="combobox" aria-expanded="false">
							<?php if (isset($_GET['turma'])) {
								echo substr($row_componente[0]['turma_nome'], 0, 30);
							} else { ?>
								TURMAS (<?php echo count($row_turmas); ?>)
							<?php } ?>
						</a>
						<ul class="ls-dropdown-nav" aria-hidden="true">
							<?php foreach ($row_turmas as $turma) { ?>
								<li><a
										href="alunos.php?escola=<?php echo htmlspecialchars($escola['escola_id'], ENT_QUOTES, 'UTF-8'); ?>&turma=<?php echo htmlspecialchars($turma['turma_id'], ENT_QUOTES, 'UTF-8'); ?>">
										<?php echo htmlspecialchars($turma['turma_nome'], ENT_QUOTES, 'UTF-8'); ?></a>
								</li>
							<?php } ?>
							<li><a class="ls-color-danger ls-divider" href="seleciona_turma.php">LIMPAR</a></li>
						</ul>
					</div>
				<?php } ?>

				
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