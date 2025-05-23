<?php
require_once('../../Connections/SmecelNovoPDO.php'); // Make sure this file correctly sets up the PDO connection
include "conf/session.php";
include "fnc/anti_injection.php";

if (isset($_GET['turma'])) {
	$turma = anti_injection($_GET['turma']);
} else {
	header('Location: index.php');
	exit; // Important: Add exit after header redirect
}

try {

	$sql = "
        SELECT pa.*, d.disciplina_id, d.disciplina_nome
        FROM smc_plano_aula AS pa
        INNER JOIN smc_disciplina AS d ON d.disciplina_id = pa.plano_aula_id_disciplina
        WHERE pa.plano_aula_id_professor = :professorId AND pa.plano_aula_id_turma = :turmaId
    ";

	$stmt = $SmecelNovo->prepare($sql);
	$stmt->bindValue(':professorId', $row_ProfLogado['func_id'], PDO::PARAM_INT);
	$stmt->bindValue(':turmaId', $turma, PDO::PARAM_INT); // Bind the sanitized $turma
	$stmt->execute();

	$row_Aulas = $stmt->fetch(PDO::FETCH_ASSOC);
	$totalRows_Aulas = $stmt->rowCount(); // Use rowCount() for PDO

} catch (PDOException $e) {
	// Handle the error appropriately
	echo "Database Error: " . $e->getMessage();
	// Or log the error: error_log($e->getMessage());
	exit;
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
			<p><a href="aulas_avulsas.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

			<?php
			if ($totalRows_Aulas > 0) {

				?>
				<table class="ls-table ls-sm-space">
					<th class="ls-txt-left" width="10%">CÓD</th>
					<th class="ls-txt-left">DATA</th>
					<th class="ls-txt-left">DISCIPLINA</th>
					<th class="ls-txt-left">CONTEÚDO</th>
					<th></th>
					<?php
					foreach ($stmt as $row_Aulas) { // Use foreach to iterate the PDOStatement
						$data = date("d/m/Y", strtotime($row_Aulas['plano_aula_data']));
						?>
						<tr>
							<td class="ls-txt-left"><b><?php echo $row_Aulas['plano_aula_id']; ?></b></td>
							<td class="ls-txt-left"><?php echo $data; ?></td>
							<td class="ls-txt-left"><?php echo $row_Aulas['disciplina_nome']; ?></td>
							<td class="ls-txt-left"><?php echo $row_Aulas['plano_aula_texto']; ?></td>
							<td class="ls-txt-right">
								<a href="javascript:void(0);"
									onclick="confirmaExclusao('<?= $row_Aulas['plano_aula_hash']; ?>', <?= $turma; ?>)"
									class="ls-sm-margin-top ls-btn-danger ls-btn-xs ls-ico-remove"></a>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			} else {
				echo '<hr><div class="ls-alert-info">Nenhuma aula cadastrada nessa turma.</div>';
			}
			?>




		</div>
		<?php //include_once "inc/footer.php"; ?>
	</main>
	<?php include_once "inc/notificacoes.php"; ?>
	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/locastyle.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>
	<script>
		function confirmaExclusao(hash, turma) {
			var resposta = confirm("Deseja realmente remover essa aula? A exclusão não poderá ser desfeita.");
			if (resposta == true) {
				window.location.href = "aula_avulsa_deletar.php?aula=" + hash + "&turma=" + turma;
			}
		}
	</script>
	<?php if (isset($_GET["deletado"])) { ?>
		<script>
			Swal.fire({
				//position: 'top-end',
				icon: 'success',
				title: 'Aula deletada com sucesso',
				showConfirmButton: false,
				timer: 1500
			})
		</script>
	<?php } ?>





</html>