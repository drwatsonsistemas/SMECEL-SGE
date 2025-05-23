<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

try {
	// Consulta para buscar os horários
	$query_Horarios = "
        SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, 
               ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_matriz_id, 
               disciplina_id, disciplina_nome 
        FROM smc_ch_lotacao_professor 
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
        INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
        WHERE ch_lotacao_professor_id = :funcionario_id AND turma_ano_letivo = :ano_letivo
        GROUP BY ch_lotacao_turma_id, ch_lotacao_disciplina_id";

	$stmtHorarios = $SmecelNovo->prepare($query_Horarios);
	$stmtHorarios->bindValue(':funcionario_id', $row_Vinculos['vinculo_id_funcionario'], PDO::PARAM_INT);
	$stmtHorarios->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
	$stmtHorarios->execute();
	$Horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	die("Erro ao carregar os horários: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br" charset="utf-8">

<head>
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', 'UA-117872281-1');
	</script>
	<title>PROFESSOR | <?php echo htmlspecialchars($row_ProfLogado['func_nome'], ENT_QUOTES, 'UTF-8'); ?> | SMECEL -
		Sistema de Gestão Escolar</title>
	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">
	<link rel="stylesheet" href="css/sweetalert2.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

	<style>
		.info-text {
			text-align: center;
			font-size: 14px;
			color: #666;
			margin-bottom: 10px;
			display: none;
		}

		.info-text i {
			margin-left: 5px;
		}

		@media (max-width: 768px) {

			.info-text {
				display: block;
			}
			.ls-table {
				display: block;
				overflow-x: auto;
				white-space: nowrap;
			}
		}
	</style>
</head>

<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">RELATÓRIO DE AULAS POSTADAS | Ano letivo
				<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
			</h1>

			<p><a href="aulas_calendario.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

			<div class="info-text">Deslize para o lado para ver mais colunas <i class="fas fa-arrow-right"></i></div>
			<table class="ls-table">
				<tr>
					<th class="ls-txt-center">TURMA</th>
					<th class="ls-txt-center">COMPONENTE</th>
					<th class="ls-txt-center">AULAS REGISTRADAS</th>
					<th class="ls-txt-center">AULAS NECESSÁRIAS</th>
					<th class="ls-txt-center">RESULTADO</th>
					<th></th>
				</tr>
				<?php foreach ($Horarios as $row_Horarios): ?>
					<?php
					// Consultar vínculo
					$query_vinculo = "
            SELECT * FROM smc_vinculo 
            WHERE vinculo_id_escola = :escola AND vinculo_id_funcionario = :funcionario AND vinculo_status = '1' AND vinculo_acesso = 'N'";
					$stmtVinculo = $SmecelNovo->prepare($query_vinculo);
					$stmtVinculo->bindValue(':escola', $row_Horarios['ch_lotacao_escola'], PDO::PARAM_INT);
					$stmtVinculo->bindValue(':funcionario', $row_ProfLogado['func_id'], PDO::PARAM_INT);
					$stmtVinculo->execute();
					$vinculo_total = $stmtVinculo->rowCount();

					if ($vinculo_total == 0):
						// Consultar aulas
						$query_Aulas = "
                SELECT plano_aula_id 
                FROM smc_plano_aula 
                WHERE plano_aula_id_turma = :turma AND plano_aula_id_disciplina = :disciplina";
						$stmtAulas = $SmecelNovo->prepare($query_Aulas);
						$stmtAulas->bindValue(':turma', $row_Horarios['ch_lotacao_turma_id'], PDO::PARAM_INT);
						$stmtAulas->bindValue(':disciplina', $row_Horarios['ch_lotacao_disciplina_id'], PDO::PARAM_INT);
						$stmtAulas->execute();
						$totalRows_Aulas = $stmtAulas->rowCount();

						// Consultar disciplina na matriz
						$query_disciplinaMatriz = "
                SELECT matriz_disciplina_ch_ano 
                FROM smc_matriz_disciplinas 
                WHERE matriz_disciplina_id_matriz = :matriz AND matriz_disciplina_id_disciplina = :disciplina";
						$stmtDisciplinaMatriz = $SmecelNovo->prepare($query_disciplinaMatriz);
						$stmtDisciplinaMatriz->bindValue(':matriz', $row_Horarios['turma_matriz_id'], PDO::PARAM_INT);
						$stmtDisciplinaMatriz->bindValue(':disciplina', $row_Horarios['ch_lotacao_disciplina_id'], PDO::PARAM_INT);
						$stmtDisciplinaMatriz->execute();
						$row_disciplinaMatriz = $stmtDisciplinaMatriz->fetch(PDO::FETCH_ASSOC);

						// Calcular resultado
						$diferenca = $row_disciplinaMatriz['matriz_disciplina_ch_ano'] - $totalRows_Aulas;
						$resultado = abs($diferenca);
						$res = $diferenca === 0
							? "<span class='ls-tag-success'>Aulas postadas</span>"
							: ($diferenca > 0
								? "<span class='ls-tag-warning'>Faltam <b>{$resultado}</b> aula(s)</span>"
								: "<span class='ls-tag-warning'>Excedentes: <b>{$resultado}</b> aula(s)</span>");
						?>
						<tr>
							<td class="ls-txt-center">
								<?php echo htmlspecialchars($row_Horarios['turma_nome']); ?>
							</td>
							<td class="ls-txt-center">
								<?php echo htmlspecialchars($row_Horarios['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>
							</td>

							<td class="ls-txt-center"><?php echo $totalRows_Aulas; ?></td>
							<td class="ls-txt-center"><?php echo $row_disciplinaMatriz['matriz_disciplina_ch_ano']; ?></td>
							<td class="ls-txt-center"><?php echo $res; ?></td>
							<td><a
									href="aulas.php?aula=&escola=<?php echo $row_Horarios['ch_lotacao_escola']; ?>&turma=<?php echo $row_Horarios['ch_lotacao_id']; ?>">Ver
									aulas</a></td>
							<td><a
									href="relatorio_aulas.php?componente=<?php echo $row_Horarios['ch_lotacao_disciplina_id']; ?>&turma=<?php echo $row_Horarios['ch_lotacao_turma_id']; ?>">Relatório
									de aulas</a></td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
		</div>
	</main>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="js/locastyle.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>
</body>

</html>