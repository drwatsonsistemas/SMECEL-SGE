<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";
include('../../sistema/funcoes/inverteData.php');

try {
	// Definir o parâmetro da escola
	$colname_Escola = "-1";
	if (isset($_GET['escola'])) {
		$colname_Escola = anti_injection($_GET['escola']);
	}

	// Preparar e executar a consulta com PDO
	$query_OC = "
    SELECT 
        ocorrencia_id, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_id_professor, ocorrencia_data, ocorrencia_descricao, ocorrencia_status, 
        turma_id, turma_nome, escola_id, escola_nome
    FROM smc_ocorrencia_turma
    LEFT JOIN smc_turma ON turma_id = ocorrencia_id_turma
    LEFT JOIN smc_escola ON escola_id = ocorrencia_id_escola
    WHERE ocorrencia_id_professor = :professor_id
    ORDER BY ocorrencia_data DESC
    ";

	// Preparar a consulta PDO
	$stmt = $SmecelNovo->prepare($query_OC);
	$stmt->bindParam(':professor_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
	$stmt->execute();

	// Obter os resultados
	$ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Verificar se há resultados
	if ($ocorrencias) {
		// Se houver ocorrências, podemos iterar sobre elas
		foreach ($ocorrencias as $ocorrencia) {
			// Exemplo de como acessar os dados, por exemplo:
			echo "<p>Ocorrência ID: " . htmlspecialchars($ocorrencia['ocorrencia_id']) . "</p>";
			echo "<p>Turma: " . htmlspecialchars($ocorrencia['turma_nome']) . "</p>";
			echo "<p>Data: " . date("d/m/Y", strtotime($ocorrencia['ocorrencia_data'])) . "</p>";
			echo "<p>Descrição: " . nl2br(htmlspecialchars($ocorrencia['ocorrencia_descricao'])) . "</p>";
		}
	} else {
		echo "<p>Nenhuma ocorrência encontrada.</p>";
	}

} catch (PDOException $e) {
	// Se houver erro com a consulta
	echo "Erro ao buscar as ocorrências: " . $e->getMessage();
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
			<h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
			<p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="nova_ocorrencia.php"
					class="ls-btn-primary">NOVA OCORRÊNCIA</a>
				<!--<a href="planejamento_turma.php" class="ls-btn-primary">PLANEJAMENTO POR TURMA</a>-->
			</p>

			<hr>
			<h3 class="ls-title-3">LISTA DE OCORRÊNCIAS</h3>

			<h1 id="status"></h1>
			<!-- Modal Trigger -->
			<?php if (!empty($ocorrencias)) { // Show if recordset not empty ?>
				<table class="ls-table">
					<thead>
						<tr>
							<th class="ls-txt-center" width="130">DATA</th>
							<th class="ls-txt-center ls-display-none-xs">ESCOLA</th>
							<th class="ls-txt-center ls-display-none-xs" width="150">TURMA</th>
							<th class="ls-txt-center ls-display-none-xs" width="150">STATUS</th>
							<th class="ls-txt-center" width="50"></th>
							<th class="ls-txt-center" width="50"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Utiliza o método fetchAll() para obter todos os resultados de uma vez
					
						foreach ($ocorrencias as $ocorrencia) {
							?>
							<tr id="linha-<?php echo $ocorrencia['ocorrencia_id']; ?>">
								<td class="ls-txt-center"><?php echo inverteData($ocorrencia['ocorrencia_data']); ?></td>
								<td class="ls-txt-center ls-display-none-xs">
									<?php echo htmlspecialchars($ocorrencia['escola_nome']); ?>
								</td>
								<td class="ls-txt-center ls-display-none-xs">
									<?php echo htmlspecialchars($ocorrencia['turma_nome']); ?>
								</td>
								<td class="ls-txt-center">
									<?php if ($ocorrencia['ocorrencia_status'] == 0) { ?>
										<a class="ls-tag-primary">NOVO</a>
									<?php } else { ?>
										<a class="ls-tag-success">VISUALIZADO</a>
									<?php } ?>
								</td>
								<td class="ls-txt-center">
									<a target="_blank"
										href="ocorrencia_imprimir.php?oc=<?php echo $ocorrencia['ocorrencia_id']; ?>&escola=<?php echo $ocorrencia['ocorrencia_id_escola']; ?>"
										title="Imprimir" class="ls-btn ls-ico-multibuckets"></a>
								</td>
								<td class="center">
									<a href="#" id="<?php echo $ocorrencia['ocorrencia_id']; ?>"
										prof="<?php echo $row_ProfLogado['func_id']; ?>"
										class="ls-btn-danger ls-ico-remove deletar"></a>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>

				</table>
			<?php } else { // Show if recordset not empty ?>
				<br>
				<div class="ls-alert-info">Nenhuma ocorrência registrada</div>
			<?php } ?>
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

		$(document).ready(function () {
			$(".deletar").on('click', function () {


				var id = $(this).attr('id');
				var prof = $(this).attr('prof');


				Swal.fire({
					title: 'Deletar esta ocorrência?',
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
							url: 'crud/ocorrencia/delete.php',
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