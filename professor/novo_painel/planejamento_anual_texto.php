<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
$colname_PlanejamentoAnual = "-1";
if (isset($_GET['plano'])) {
  $colname_PlanejamentoAnual = $_GET['plano'];
}

// Alterando para uso de PDO
$query_PlanejamentoAnual = "SELECT plano_anual_id, plano_anual_id_prof, plano_anual_id_etapa, plano_anual_id_componente, plano_anual_id_escola, plano_anual_ano, plano_anual_texto, plano_anual_link, plano_anual_hash FROM smc_plano_anual WHERE plano_anual_hash = :plano_anual_hash";
$stmt = $SmecelNovo->prepare($query_PlanejamentoAnual);
$stmt->bindParam(':plano_anual_hash', $colname_PlanejamentoAnual, PDO::PARAM_STR);
$stmt->execute();
$row_PlanejamentoAnual = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_PlanejamentoAnual = $stmt->rowCount();

if ($totalRows_PlanejamentoAnual == 0) {
    header("Location: planejamento_anual.php?erro");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    $updateSQL = "UPDATE smc_plano_anual SET plano_anual_texto = :plano_anual_texto WHERE plano_anual_hash = :plano_anual_hash";
    
    // Preparar a consulta de atualização
    $stmt = $SmecelNovo->prepare($updateSQL);
    $stmt->bindParam(':plano_anual_texto', $_POST['plano_anual_texto'], PDO::PARAM_STR);
    $stmt->bindParam(':plano_anual_hash', $_POST['plano_anual_hash'], PDO::PARAM_STR);
    $stmt->execute();
    
    // Redirecionar para a página após a atualização
    $updateGoTo = "planejamento_anual.php?texto";
    header(sprintf("Location: %s", $updateGoTo));
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
      
      <label class="ls-label">
    <b class="ls-label-text">PLANEJAMENTO</b>
    <textarea name="plano_anual_texto" cols="50" rows="15"><?php echo htmlentities($row_PlanejamentoAnual['plano_anual_texto'], ENT_COMPAT, 'utf-8'); ?></textarea>
    </label>
    
      
  <div class="ls-actions-btn">
    <input type="submit" value="SALVAR" class="ls-btn-primary">
    <button class="ls-btn-danger">VOLTAR</button>
  </div>
      
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="plano_anual_hash" value="<?php echo $row_PlanejamentoAnual['plano_anual_hash']; ?>">
    </form>
    
    <p>&nbsp;</p>
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

<!--<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>-->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script> 
<script src="langs/pt_BR.js"></script> 
<script>
	

	$(document).ready(function() {
      $('textarea').summernote({
        placeholder: 'Digite aqui...',
        tabsize: 2,
        height: 400,
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
    });
</script>

</body>
</html>

