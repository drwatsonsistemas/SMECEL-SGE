<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

if ($_GET['turma'] != '') {
	$turma = $_GET['turma'];
} else {
	header('Location: index.php');
	exit;
}

// Prepare and execute queries with PDO
try {
	$sql = "
		SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_ano_letivo,
			   matriz_id, matriz_aula_dia
		FROM smc_turma 
		INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
		WHERE turma_id = :turma AND turma_ano_letivo = :anoLetivo;
	";
	$stmt = $SmecelNovo->prepare($sql);
	$stmt->bindValue(':turma', $turma, PDO::PARAM_INT);
	$stmt->bindValue(':anoLetivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
	$stmt->execute();
	$row_Turmas = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row_Turmas === false) {
		header('Location: index.php');
		exit;
	}

	// Another PDO query for matriz
	$sql = "
		SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, matriz_disciplina_reprova, disciplina_id, disciplina_nome
		FROM smc_matriz_disciplinas
		INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
		WHERE matriz_disciplina_id_matriz = :matrizId;
	";
	$stmt = $SmecelNovo->prepare($sql);
	$stmt->bindValue(':matrizId', $row_Turmas['turma_matriz_id'], PDO::PARAM_INT);
	$stmt->execute();
	$row_Matriz = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$numero_dia = date("21-08-2023");
	$numero_dia_semana = date("w", strtotime($numero_dia));
} catch (PDOException $e) {
	die("Error: " . $e->getMessage());
}
?>
<?php
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// ... (PDO connection and queries as before)

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$semana = date("w", strtotime($_POST['plano_aula_data']));
	$dataCad = date('Y-m-d H:i:s');
	$hash = md5(uniqid(""));

	try {
		$sql = "
            INSERT INTO smc_plano_aula (plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_publicado, plano_aula_num_aula, plano_aula_num_dia, plano_aula_hash)
            VALUES (:turmaId, :disciplinaId, :professorId, :data, :dataCadastro, :texto, :publicado, :aulaNum, :diaNum, :hash);
        ";

		$stmt = $SmecelNovo->prepare($sql);
		$stmt->bindValue(':turmaId', $row_Turmas['turma_id'], PDO::PARAM_INT);
		$stmt->bindValue(':disciplinaId', $_POST['plano_aula_id_disciplina'], PDO::PARAM_INT);
		$stmt->bindValue(':professorId', $row_ProfLogado['func_id'], PDO::PARAM_INT);
		$stmt->bindValue(':data', $_POST['plano_aula_data'], PDO::PARAM_STR); // Use PARAM_STR for dates
		$stmt->bindValue(':dataCadastro', $dataCad, PDO::PARAM_STR);
		$stmt->bindValue(':texto', $_POST['plano_aula_texto'], PDO::PARAM_STR);
		$stmt->bindValue(':publicado', "N", PDO::PARAM_STR);
		$stmt->bindValue(':aulaNum', $_POST['plano_aula_num_aula'], PDO::PARAM_INT);
		$stmt->bindValue(':diaNum', $semana, PDO::PARAM_INT);
		$stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
		$stmt->execute();

		$insertGoTo = "aulas_avulsas.php?true";
		if (isset($_SERVER['QUERY_STRING'])) {
			$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
			$insertGoTo .= $_SERVER['QUERY_STRING'];
		}
		header(sprintf("Location: %s", $insertGoTo));
		exit; // Important: Add exit after header redirect
	} catch (PDOException $e) {
		// Handle the error appropriately. For debugging:
		echo "Error inserting record: " . $e->getMessage();
		// Or log the error: error_log($e->getMessage());
		// Or redirect to an error page: header("Location: error.php");
		exit; // Important: Stop execution after error handling
	}
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
	<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
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
			<h1 class="ls-title-intro ls-ico-home">AULAS</h1>

			<div id="linkResultado"></div>

		</div>
		<?php //include_once "inc/footer.php"; ?>
	</main>
	<?php include_once "inc/notificacoes.php"; ?>
	<div class="ls-modal" id="modal_cadastrarAula" data-modal-blocked>
		<div class="ls-modal-box">
			<div class="ls-modal-header">
				<button data-dismiss="modal">&times;</button>
				<h4 class="ls-modal-title">REGISTRAR AULA AVULSA</h4>
			</div>
			<div class="ls-modal-body" id="myModalBody">
				<form method="post" name="form1" action="<?php echo $editFormAction; ?>"
					class="ls-form ls-form-horizontal row" onsubmit="disableButton()">
					<fieldset>
						<label class="ls-label col-md-6">
							<b class="ls-label-text">DATA</b>
							<p class="ls-label-info">Data da aplicação da aula</p>
							<input type="date" name="plano_aula_data" value="<?php echo date("Y-m-d") ?>" required
								autocomplete="off">
						</label>
						<label class="ls-label col-md-6">
							<b class="ls-label-text">AULA</b>
							<p class="ls-label-info">Número da aula no dia</p>
							<div class="ls-custom-select">
								<select class="ls-select" name="plano_aula_num_aula" required>
									<option value="">Escolha...</option>
									<?php
									for ($i = 1; $i <= $row_Turmas['matriz_aula_dia']; $i++) {
										echo "<option value=$i>" . $i . "ª AULA</option>";
									}
									?>

								</select>
							</div>
						</label>
						<label class="ls-label col-md-12">
							<b class="ls-label-text">COMPONENTE</b>
							<p class="ls-label-info"></p>
							<div class="ls-custom-select">
								<select class="ls-select" name="plano_aula_id_disciplina" required>
									<option value="">Escolha...</option>

									<?php
									foreach ($row_Matriz as $disciplina) {
										echo "<option value='" . $disciplina['disciplina_id'] . "'>" . $disciplina['disciplina_nome'] . "</option>";
									}
									?>

								</select>
							</div>

						</label>
						<label class="ls-label col-md-12">
							<b class="ls-label-text">ASSUNTO</b>
							<p class="ls-label-info">Digite o tema da aula aplicada</p>
							<input type="text" name="plano_aula_texto" value="" required autocomplete="off">
						</label>

					</fieldset>
					<input type="hidden" name="MM_insert" value="form1">
					<input type="hidden" name="plano_aula_num_aula" value="" disabled>
			</div>
			<div class="ls-modal-footer">
				<input class="ls-btn-primary" id="btnSalvar" type="submit" value="SALVAR">
				<a href="aulas_calendario.php" class="ls-btn ls-float-right">VOLTAR</a>
				</form>
			</div>
		</div>
	</div>
	<!-- /.modal -->

	<!-- We recommended use jQuery 1.10 or up -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/sweetalert2.min.js"></script>



	<script type="application/javascript">
		locastyle.modal.open("#modal_cadastrarAula");

		function disableButton() {
			document.getElementById("btnSalvar").disabled = true;
		}
	</script>




</body>

</html>
<?php

?>