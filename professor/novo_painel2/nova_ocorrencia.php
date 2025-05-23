<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";
include('../../sistema/funcoes/inverteData.php');

$ANO_LETIVO = ANO_LETIVO;
$ID_PROFESSOR = ID_PROFESSOR;
// Buscar escolas
$query_escolas = "
SELECT 
    ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
    ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
    escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
GROUP BY escola_id
ORDER BY escola_nome ASC
";

$stmt = $SmecelNovo->prepare($query_escolas);
$stmt->execute([
	':professor_id' => $ID_PROFESSOR,
	':ano_letivo' => $ANO_LETIVO
]);

$escolas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_escolas = count($escolas);

// Se uma escola foi selecionada
if (isset($_GET['escola'])) {
	$escola = anti_injection($_GET['escola']);

	// Buscar turmas para a escola selecionada
	$query_turmas = "
    SELECT 
        ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
        ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, 
        turma_ano_letivo, turma_turno, turma_etapa, escola_id, escola_nome,
        CASE turma_turno
            WHEN 0 THEN 'INTEGRAL'
            WHEN 1 THEN 'MATUTINO'
            WHEN 2 THEN 'VESPERTINO'
            WHEN 3 THEN 'NOTURNO'
        END AS turma_turno_nome 
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    INNER JOIN smc_escola ON escola_id = :escola_id
    WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_escola = :escola_id AND ch_lotacao_professor_id = :professor_id
    GROUP BY turma_id
    ORDER BY turma_turno, turma_etapa, turma_nome ASC
    ";

	$stmt = $SmecelNovo->prepare($query_turmas);
	$stmt->execute([
		':escola_id' => $escola,
		':ano_letivo' => $ANO_LETIVO,
		':professor_id' => $ID_PROFESSOR
	]);

	$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$totalRows_turmas = count($turmas);
}

// Se uma turma foi selecionada
if (isset($_GET['turma'])) {
	$turma = anti_injection($_GET['turma']);

	// Buscar componentes para a turma selecionada
	$query_componente = "
    SELECT 
        ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
        ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, 
        turma_ano_letivo, turma_turno, turma_etapa, disciplina_id, disciplina_nome
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
    WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_turma_id = :turma_id AND ch_lotacao_professor_id = :professor_id
    GROUP BY disciplina_id
    ORDER BY disciplina_nome ASC
    ";

	$stmt = $SmecelNovo->prepare($query_componente);
	$stmt->execute([
		':ano_letivo' => $ANO_LETIVO,
		':turma_id' => $turma,
		':professor_id' => $ID_PROFESSOR
	]);

	$componente = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$totalRows_componente = count($componente);
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
</head>

<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">OCORRÊNCIA</h1>
			<p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>



			<div class="ls-box1">
				<hr>

				<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
					<a href="#" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false">
						<?php
						// Verifica se o parâmetro 'escola' está definido e exibe o nome da escola correspondente
						if (isset($_GET['escola'])) {
							echo substr($escolas[0]['escola_nome'], 0, 30);
						} else {
							?>
							UNIDADE ESCOLAR (<?php echo $totalRows_escolas; ?>)
							<?php
						}
						?>
					</a>
					<ul class="ls-dropdown-nav" aria-hidden="true">
						<?php
						// Utiliza o PDO para listar as escolas
						foreach ($escolas as $row_escolas) {
							// Consulta para verificar o vínculo entre o professor e a escola
							$vinculo_q = "
        SELECT * FROM smc_vinculo 
        WHERE vinculo_id_escola = :escola_id 
        AND vinculo_id_funcionario = :professor_id 
        AND vinculo_status = '1' 
        AND vinculo_acesso = 'N'";

							// Prepara e executa a consulta PDO
							$stmt_vinculo = $SmecelNovo->prepare($vinculo_q);
							$stmt_vinculo->execute([
								':escola_id' => $row_escolas['escola_id'],
								':professor_id' => $row_ProfLogado['func_id']
							]);

							$vinculo_total = $stmt_vinculo->rowCount(); // Verifica a quantidade de registros
						
							// Se o vínculo não existir, exibe a opção para nova ocorrência
							if ($vinculo_total == 0) {
								?>
								<li><a
										href="nova_ocorrencia.php?escola=<?php echo $row_escolas['escola_id']; ?>"><?php echo substr($row_escolas['escola_nome'], 0, 33); ?>...</a>
								</li>
								<?php
							}
						}
						?>
						<li><a class="ls-color-danger ls-divider" href="rendimento.php">LIMPAR</a></li>
					</ul>
				</div>


				<?php if (isset($_GET['escola'])) { ?>

					<div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
						<a href="#" style="background-color:#06C;" class="ls-btn-primary ls-btn-block ls-btn-lg"
							role="combobox" aria-expanded="false">
							<?php
							if (isset($_GET['turma'])) {
								// Exibe o nome da turma selecionada, limitado a 30 caracteres
								echo substr($row_componente['turma_nome'], 0, 30);
							} else {
								?>
								TURMAS (<?php echo $totalRows_turmas; ?>)
							<?php } ?>
						</a>
						<ul class="ls-dropdown-nav" aria-hidden="true">
							<?php
							// Loop pelas turmas, utilizando PDO para buscar os dados
							if ($totalRows_turmas > 0) {
								foreach ($turmas as $row_turma) {
									?>
									<li>
										<a
											href="ocorrencia_cad.php?escola=<?php echo $escola; ?>&turma=<?php echo $row_turma['turma_id']; ?>">
											<?php echo $row_turma['turma_nome']; ?>
										</a>
									</li>
								<?php
								}
							} else {
								?>
								<li><a href="#">Nenhuma turma disponível</a></li>
							<?php } ?>
							<li><a class="ls-color-danger ls-divider" href="rendimento.php">LIMPAR</a></li>
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
<?php
mysql_free_result($escolas);
?>