<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

<?php

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AC = "
SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_criacao, ac_status, ac_correcao, ac_feedback,
disciplina_id, disciplina_nome, etapa_id, etapa_nome, etapa_nome_abrev, escola_id, escola_nome,
CASE ac_status
WHEN 0 THEN '<span class=\"ls-tag-primary\">NOVO</span>'
WHEN 1 THEN '<span class=\"ls-tag-primary\">VISUALIZADO</span>'
END AS ac_status,
CASE ac_correcao
WHEN 0 THEN ''
WHEN 1 THEN '<span class=\"ls-tag-warning\">NECESSITA CORREÇÃO</span>'
WHEN 2 THEN '<span class=\"ls-tag-success\">CORREÇÃO REALIZADA</span>'
END AS ac_correcao  
FROM smc_ac
LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
LEFT JOIN smc_escola ON escola_id = ac_id_escola
WHERE ac_id_professor = '$row_ProfLogado[func_id]' AND ac_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY ac_data_inicial DESC
";
$AC = mysql_query($query_AC, $SmecelNovo) or die(mysql_error());
$row_AC = mysql_fetch_assoc($AC);
$totalRows_AC = mysql_num_rows($AC);

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
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="planejamento.php" class="ls-btn-primary">NOVO PLANEJAMENTO</a> <a href="planejamento_mapa.php" class="ls-btn-primary ls-ico-calendar ls-ico-right"></a></p>
    <hr>
    <h3 class="ls-title-3">LISTA DE PLANEJAMENTO</h3>

    <h1 id="status"></h1>
<!-- Modal Trigger --> 
        <?php if ($totalRows_AC > 0) { // Show if recordset not empty ?>
  <table class="ls-table">
    <thead>
      <tr>
        <th class="ls-txt-center" width="130">INTERVALO</th>
        <th class="ls-txt-center" width="80">DIAS</th>
        <th class="ls-txt-center ls-display-none-xs">ESCOLA</th>
        <th class="ls-txt-center ls-display-none-xs" width="200">COMPONENTE</th>
        <th class="ls-txt-center ls-display-none-xs" width="100">ETAPA</th>
        <th class="ls-txt-center" width="150">STATUS</th>
        <th class="ls-txt-center" width="150">CORREÇÃO</th>
        <th class="ls-txt-center" width="50"></th>
        <th class="ls-txt-center" width="50"></th>
        </tr>
    </thead>
    <tbody>
      <?php $num = $totalRows_AC; do { ?>
        <tr id="linha-<?php echo $row_AC['ac_id']; ?>">
          <td class="ls-txt-center"><?php echo inverteData($row_AC['ac_data_inicial']); ?><br><?php echo inverteData($row_AC['ac_data_final']); ?></td>
          <td class="ls-txt-center"><?php $diferenca = strtotime($row_AC['ac_data_final']) - strtotime($row_AC['ac_data_inicial']); echo $dias = floor($diferenca / (60 * 60 * 24))+1; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_AC['escola_nome']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_AC['disciplina_nome']; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_AC['etapa_nome_abrev']; ?></td>
          <td class="ls-txt-center"><?php echo $row_AC['ac_status']; ?></td>
          <td class="ls-txt-center"><?php echo $row_AC['ac_correcao']; ?></td>
          <td class="ls-txt-center">
          
          <a href="planejamento_editar.php?escola=<?php echo $colname_Escola; ?>&etapa=<?php echo $row_AC['etapa_id']; ?>&componente=<?php echo $row_AC['disciplina_id']; ?>&ac=<?php echo $row_AC['ac_id']; ?>" class="ls-btn ls-ico-pencil2"></a>
         
          
          </td>
          <td class="center">
		            
          <a href="#" id="<?php echo $row_AC['ac_id']; ?>" prof="<?php echo $row_ProfLogado['func_id']; ?>" class="ls-btn-danger ls-ico-remove deletar"></a>
		  
		  </td>
        </tr>
        <?php } while ($row_AC = mysql_fetch_assoc($AC)); ?>
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
		  title: 'Deletar este planejamento?',
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
        url  : 'crud/planejamento/delete.php',
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