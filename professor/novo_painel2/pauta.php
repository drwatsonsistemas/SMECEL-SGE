<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

<?php

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = anti_injection($_GET['escola']);
}

// Utilizando PDO para a consulta
$query_AC = "
SELECT *, escola_id, escola_nome
FROM smc_pauta
LEFT JOIN smc_escola ON escola_id = pauta_id_escola
WHERE pauta_id_professor = :func_id AND pauta_ano_letivo = :ano_letivo
ORDER BY pauta_data_inicial DESC
";

$stmt = $SmecelNovo->prepare($query_AC);
$stmt->bindParam(':func_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
$stmt->bindParam(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
$stmt->execute();

// Obtendo os resultados
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_AC = $stmt->rowCount();

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
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="atv_comp.php" class="ls-btn-primary">NOVA PAUTA</a></p>
    <?php if(isset($_GET['editado'])){ ?>
      <div class="ls-alert-success">Pauta editada com sucesso!</div>
    <?php } ?>

    <hr>
  
    <h3 class="ls-title-3">LISTA DE PAUTAS</h3>

    <h1 id="status"></h1>
<!-- Modal Trigger --> 
        <?php if ($totalRows_AC > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <thead>
      <tr>
        <th class="ls-txt-center" width="130">INTERVALO</th>
        <th class="ls-txt-center" width="60">DIAS</th>
        <th class="ls-txt-center ls-display-none-xs">ESCOLA</th>
        <th class="ls-txt-center" width="130">POSSUI OBS?</th>
        <th class="ls-txt-center" width="50"></th>
        <th class="ls-txt-center" width="50"></th>
        <th class="ls-txt-center" width="50"></th>
        </tr>
    </thead>
    <tbody>
      <?php $num = $totalRows_AC; foreach ($resultados as $row_AC) { ?>
        <tr id="linha-<?php echo $row_AC['pauta_id']; ?>">
          <td class="ls-txt-center"><?php echo inverteData($row_AC['pauta_data_inicial']); ?><br><?php echo inverteData($row_AC['pauta_data_final']); ?></td>
          <td class="ls-txt-center"><?php $diferenca = strtotime($row_AC['pauta_data_final']) - strtotime($row_AC['pauta_data_inicial']); echo $dias = floor($diferenca / (60 * 60 * 24))+1; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_AC['escola_nome']; ?></td>
          <td class="ls-txt-center"><?php echo $row_AC['pauta_retorno_coord'] != '' ? '
          <button data-ls-module="modal" data-target="#myAwesomeModal'.$row_AC['pauta_id'].'" class="ls-btn-primary">Ver</button>
          ' : ''; ?></td>
          <td class="ls-txt-center">
          
          <a href="atv_comp_editar.php?escola=<?php echo $row_AC['pauta_id_escola']; ?>&pauta=<?php echo $row_AC['pauta_id']; ?>" class="ls-btn ls-ico-pencil2"></a>
         
          
          </td>
          <td class="center">
		            
          <a href="#" id="<?php echo $row_AC['pauta_id']; ?>" prof="<?php echo $row_ProfLogado['func_id']; ?>" class="ls-btn-danger ls-ico-remove deletar"></a>
		  
		  </td>
        </tr>
        <div class="ls-modal" id="myAwesomeModal<?php echo $row_AC['pauta_id']  ?>">
            <div class="ls-modal-box">
              <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">Parecer da coordenação pedagógica</h4>
              </div>
              <div class="ls-modal-body" id="myModalBody">
                <p><?php echo $row_AC['pauta_retorno_coord'] ?></p>
              </div>
              <div class="ls-modal-footer">
                <button data-dismiss="modal" class="ls-btn-primary">OK</button>
              </div>
            </div>
          </div><!-- /.modal -->
        <?php } ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
    
    
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

$(document).ready(function() {
  $(".deletar").on('click', function() {

	  
	var id 		= $(this).attr('id');
	var prof 	= $(this).attr('prof');
	
	
		Swal.fire({
		  title: 'Deletar esta pauta?',
		  text: "Esta ação não poderá ser desfeita.",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Sim, deletar!'
		}).then((result) => {
		  if (result.isConfirmed) {
			  
			  
		$.ajax({
		type : 'POST',
        url  : 'crud/pauta/delete.php',
        data : {
			id				:id,
			prof			:prof
			},
			success:function(data){
				
				$("#linha-"+id).remove();
				
				$('#status').html(data);
				
				setTimeout(function(){
					
					
					
					
					  //location.reload();					
					},2000);
				
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