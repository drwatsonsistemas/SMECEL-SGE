<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php"; 
include "fnc/anti_injection.php"; 

$ANO_LETIVO = ANO_LETIVO;
$ID_PROFESSOR = ID_PROFESSOR;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Define variables
$escola = "-1";
if (isset($_GET['escola'])) {
    $escola = anti_injection($_GET['escola']);
}

$turma = "-1";
if (isset($_GET['turma'])) {
    $turma = anti_injection($_GET['turma']);
}

// Prepare and execute query for turmas
$query_turmas = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
           ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, 
           turma_turno, turma_etapa, escola_id, escola_nome,
           CASE turma_turno
               WHEN 0 THEN 'INTEGRAL'
               WHEN 1 THEN 'MATUTINO'
               WHEN 2 THEN 'VESPERTINO'
               WHEN 3 THEN 'NOTURNO'
           END AS turma_turno_nome 
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    INNER JOIN smc_escola ON escola_id = :escola
    WHERE turma_ano_letivo = :ano_letivo 
          AND ch_lotacao_escola = :escola 
          AND ch_lotacao_professor_id = :professor 
          AND turma_id = :turma
    GROUP BY turma_id
    ORDER BY turma_turno, turma_etapa, turma_nome ASC
";

// Prepare and execute the PDO statement
$stmt_turmas = $SmecelNovo->prepare($query_turmas);
$stmt_turmas->execute([
    ':escola' => $escola,
    ':ano_letivo' => $ANO_LETIVO,
    ':professor' => $ID_PROFESSOR,
    ':turma' => $turma
]);

// Fetch results
$turmas = $stmt_turmas->fetch(PDO::FETCH_ASSOC);
$totalRows_turmas = count($turmas);

// Prepare and execute query for escolas
$query_escolas = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
           ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, escola_id, escola_nome, 
           turma_id, turma_nome, turma_turno, turma_ano_letivo
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
    WHERE ch_lotacao_professor_id = :professor 
          AND turma_ano_letivo = :ano_letivo 
          AND escola_id = :escola
    GROUP BY escola_id
    ORDER BY escola_nome ASC
";

// Prepare and execute the PDO statement
$stmt_escolas = $SmecelNovo->prepare($query_escolas);
$stmt_escolas->execute([
    ':professor' => $ID_PROFESSOR,
    ':ano_letivo' => $ANO_LETIVO,
    ':escola' => $escola
]);

// Fetch results
$escolas = $stmt_escolas->fetchAll(PDO::FETCH_ASSOC);
$totalRows_escolas = count($escolas);

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MM_update']) && $_POST['MM_update'] == 'form1') {
    // Prepare the insert statement
    $insertSQL = "
        INSERT INTO smc_ocorrencia_turma (ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_data, 
                                          ocorrencia_id_professor, ocorrencia_descricao) 
        VALUES (:turma, :escola, :data, :professor, :descricao)
    ";

    // Prepare the statement
    $stmt_insert = $SmecelNovo->prepare($insertSQL);
    $stmt_insert->execute([
        ':turma' => $turma,
        ':escola' => $escola,
        ':data' => $_POST['ocorrencia_data'],
        ':professor' => $ID_PROFESSOR,
        ':descricao' => $_POST['ocorrencia_descricao']
    ]);

    // Redirect after insert
    $insertGoTo = "ocorrencia.php";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header("Location: $insertGoTo");
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
		function gtag(){dataLayer.push(arguments);}
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
	<link rel="stylesheet" type="text/css" href="css/locastyle.css">	<link rel="stylesheet" href="css/sweetalert2.min.css">
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

</head>
<body>
	<?php include_once "inc/navebar.php"; ?>
	<?php include_once "inc/sidebar.php"; ?>
	<main class="ls-main">
		<div class="container-fluid">
			<h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
			<p><a href="ocorrencia.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
			<hr>
			<div class="ls-alert-info">
				<strong>Atenção:</strong> O formulário de ocorrência só pode ser preenchido para casos de:
				<ul style="margin-left:16px">
					<li>Desacato ao professor</li>
					<li>Agressão física e verbal ao professor e aos colegas</li>
					<li>Quebrar patrimônio público</li>
					<li>Etc.</li>
				</ul>
				Caso a ocorrência se refira a uma justificativa de falta ou a um incidente envolvendo um aluno específico, esta deverá ser redirecionada para a coordenação.
			</div>
			<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">

				<fieldset>
					<label class="ls-label col-md-6">
						<b class="ls-label-text">Turma</b>
						<input type="text" name="" value="<?= $turmas['turma_nome'] ?>" disabled readonly>
					</label>
					<label class="ls-label col-md-6">
						<b class="ls-label-text">Escola</b>
						<input type="text" name="" value="<?= $turmas['escola_nome'] ?>" disabled readonly>
					</label>
				</fieldset>
				<label class="ls-label col-md-12">
					<b class="ls-label-text">DATA</b>
					<p class="ls-label-info">Informe a data da ocorrência</p>
					<input type="date" name="ocorrencia_data" value="" size="32" required>
				</label>
				<fieldset>
					<label class="ls-label col-md-12 ">
						<b class="ls-label-text">Descrição</b>
						<textarea name="ocorrencia_descricao" id="summernote"  rows="4"></textarea>
					</fieldset>

					<div class="ls-actions-btn">
						<input type="submit" class="ls-btn-primary" value="REGISTRAR">
						<input type="hidden" name="MM_update" value="form1" />
					</div>

				</form>








			</div>
			<?php //include_once "inc/footer.php"; ?>
		</main>
		<?php include_once "inc/notificacoes.php"; ?>


		<!-- We recommended use jQuery 1.10 or up --> 
		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
		<script src="js/locastyle.js"></script> 
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
		<script src="js/sweetalert2.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
		<!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>--> 
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script>
			$('#summernote').summernote({
				placeholder: 'Descreva a ocorrência',
				tabsize: 2,
				height: 120,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'underline', 'clear']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', []],
					['view', []]
					]
			});
		</script>
		<script>
			$('#disciplinas').select2({
    width: '100%' // Definindo a largura como 100%
  });

      /*tinymce.init({
       selector: 'textarea',

       mobile: {
        menubar: false
      },

      images_upload_url: 'postAcceptor.php',
      automatic_uploads: true,
      imagetools_proxy: 'proxy.php',

	  //plugins: 'emoticons',
	  //toolbar: 'emoticons',

	  //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',

      height: 200,
      toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
      plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
        'advlist autolink lists link image imagetools charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'],
	  //force_br_newlines : false,
	  //force_p_newlines : false,
	  //forced_root_block : '',	
      statusbar: false,
      language: 'pt_BR',
      menubar: false,
      paste_as_text: true,
      content_css: '//www.tinymce.com/css/codepen.min.css'
    });*/

</script>

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
