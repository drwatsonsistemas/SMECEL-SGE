<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
  $semana = date("w", strtotime($data));
  $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
  $dia_semana_nome = $diasemana[$semana];
  $data = date("Y-m-d", strtotime($data));
} else {
	$data = date("Y-m-d");
	$semana = date("w", strtotime($data));
	$diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
	$dia_semana_nome = $diasemana[$semana];
	$data = date("Y-m-d", strtotime($data));

}


		$link_target = "aulas.php";
        $nome_target = "REGISTRAR AULAS";
		$tabela_turma = "ch_lotacao_id";
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Turmas = "
		SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
		ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, disciplina_id, disciplina_nome, disciplina_nome_abrev, escola_id, escola_nome,
		CASE turma_turno
		WHEN 0 THEN 'INT'
		WHEN 1 THEN 'MAT'
		WHEN 2 THEN 'VES'
		WHEN 3 THEN 'NOT'
		END AS turma_turno_nome 
		FROM smc_ch_lotacao_professor
		INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
		INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
		INNER JOIN smc_escola ON escola_id = ch_lotacao_escola	
		WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]' AND ch_lotacao_dia = '$semana'
		ORDER BY ch_lotacao_escola, turma_turno, ch_lotacao_aula ASC";
		$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
		$row_Turmas = mysql_fetch_assoc($Turmas);
		$totalRows_Turmas = mysql_num_rows($Turmas);

		$aulaNum = 1;
		$filtraData = 1;


	
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
<style>
.list-icons .li {
	height:180px;
	width:100%;
	display:block;
	padding-top:20px;
	margin-top: 20px;
	font-size:14px;
	text-align:center;
	background-color:rgba(0,0,0,0.03);
	cursor:pointer;
	color:rgba(0,0,0,0.5)
}
.list-icons .li:hover {
	color:black;
	background-color:rgba(0,0,0,0.08);
	font-weight:bold
}
.list-icons .li span:before {
	display:block;
	font-size:30px;
	margin-bottom:5px
}
</style>
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
  
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="aulas_calendario.php?target=aulas" class="ls-btn ls-ico-chevron-left">Voltar</a>     <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">ALTERAR DATA</button></p>
    
    <div class="ls-box ls-txt-center"><h6 class="ls-title-4">Aulas do dia <?php echo date("d/m/y", strtotime($data)); ?> | <?php echo $dia_semana_nome; ?></h6></div>
    
    <?php if($totalRows_Turmas>0) { ?>
    
    <div class="row1 1list-icons">
      <?php do { ?>
      
      <?php 

		$vinculo_q = "SELECT * FROM smc_vinculo WHERE vinculo_id_escola = '$row_Turmas[escola_id]' AND vinculo_id_funcionario = '$row_ProfLogado[func_id]' AND vinculo_status = '1' AND vinculo_acesso = 'N'";
		$vinculo = mysql_query($vinculo_q, $SmecelNovo) or die(mysql_error());
		$vinculo_row = mysql_fetch_assoc($vinculo);
		$vinculo_total = mysql_num_rows($vinculo);
			

		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_aulas = "
		SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_num_aula, plano_aula_texto, plano_aula_hash 
		FROM smc_plano_aula
		WHERE plano_aula_id_turma = '$row_Turmas[ch_lotacao_turma_id]' AND plano_aula_id_disciplina = '$row_Turmas[ch_lotacao_disciplina_id]' AND plano_aula_data = '$data' AND plano_aula_id_professor = '$row_Vinculos[vinculo_id_funcionario]' AND plano_aula_num_aula = '$row_Turmas[ch_lotacao_aula]'
		";
		$aulas = mysql_query($query_aulas, $SmecelNovo) or die(mysql_error());
		$row_aulas = mysql_fetch_assoc($aulas);
		$totalRows_aulas = mysql_num_rows($aulas);	  
	  ?>
      
      
      
      
 <div class="ls-list" <?php echo ($totalRows_aulas>0) ? " style='background-color:#CAFFB0'" : "style='background-color:#FCC'"; ?>>
  <header class="ls-list-header">
    <div class="ls-list-title col-md-9">
      <h5 class="ls-title-6"><?php echo ($totalRows_aulas>0) ? "<span class='ls-ico-checkbox-checked ls-color-success'></span>" : "<span class='ls-ico-checkbox-unchecked ls-color-danger'></span>"; ?> <?php if ($aulaNum == 1) { echo $row_Turmas['ch_lotacao_aula']."ª"; } ?> 
	  <?php echo $row_Turmas['disciplina_nome_abrev']; ?> | <?php echo $row_Turmas['turma_nome']; ?> <?php echo $row_Turmas['turma_turno_nome']; ?> | <?php echo $row_Turmas['escola_nome']; ?></h5>
      <small><?php echo $row_aulas['plano_aula_texto']; ?>&nbsp;</small>
    </div>
    <div class="col-md-3 ls-txt-right">
    <?php if ($vinculo_total==0) { ?>
    <?php if ($totalRows_aulas==0) { ?>
    
      <a href="aulas.php?aula=<?php echo $row_aulas['plano_aula_hash']; ?>&escola=<?php echo $row_Turmas['ch_lotacao_escola']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&data=<?php echo $data; ?>" class="ls-btn ls-ico-search"></a>

      <a href="aulas_data_cadastrar.php?escola=<?php echo $row_Turmas['ch_lotacao_escola']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&target=aulas&aulanum=<?php echo $row_Turmas['ch_lotacao_aula']; ?>&data=<?php echo $data; ?>&nova" class="ls-btn-primary ls-ico-plus"></a>
      
    <?php } else { ?>
    
      <a href="aulas.php?aula=<?php echo $row_aulas['plano_aula_hash']; ?>&escola=<?php echo $row_Turmas['ch_lotacao_escola']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&data=<?php echo $data; ?>" class="ls-btn ls-ico-search"></a>

      <a href="aula_duplicar_data.php?aula=<?php echo $row_aulas['plano_aula_hash']; ?>&escola=<?php echo $row_Turmas['ch_lotacao_escola']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&target=aulas&data=<?php echo $data; ?>&nova" class="ls-btn ls-ico-windows"></a>
      
      <a href="aula_editar_data.php?aula=<?php echo $row_aulas['plano_aula_hash']; ?>&escola=<?php echo $row_Turmas['ch_lotacao_escola']; ?>&turma=<?php echo $row_Turmas['ch_lotacao_id']; ?>&target=aulas&data=<?php echo $data; ?>&nova" class="ls-btn ls-ico-pencil2"></a>
      
      <a class="ls-btn-primary-danger ls-ico-remove ls-float-right delete-btn" id="<?php echo $row_aulas['plano_aula_hash']; ?>" cod="<?php echo $row_aulas['plano_aula_id']; ?>"></a>	
        
    <?php } ?>
	<?php }else { ?>
		Acesso bloqueado
	<?php } ?>
    </div>
  </header>
</div> 

          
        <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
        
    </div>
    
    <hr>
    <p><span style="background-color:#CAFFB0; display:block; width:12px; height:12px; float:left; margin:5px;">&nbsp;</span>Aula com conteúdo inserido</p>    
    <p><span style="background-color:#FCC; display:block; width:12px; height:12px; float:left; margin:5px;">&nbsp;</span>Aula sem conteúdo</p>    

    
    <?php } else { ?>
    <p class="">Nenhuma turma vinculada nesta data.</p>
    <?php } ?>
    
  </div>
  <div id="linkResultado"></div>
  <?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">ESCOLHA UMA DATA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      <form action="aulas_data.php" class="ls-form">
        <input type="hidden" name="escola" value="">
        <input type="hidden" name="turma" value="">
        <input type="hidden" name="target" value="">
        <label class="ls-label col-md-12 col-xs-12"> <b class="ls-label-text">DATA</b>
          <input type="date" name="data" class="" id="data" value="<?php echo $data; ?>" autocomplete="off"  onchange="this.form.submit()">
        </label>
        <input type="hidden" name="alterada" value="true">
      </form>
      </p>
    </div>
    <div class="ls-modal-footer">
      <button type="" class="ls-btn ls-btn-primary ls-btn-block" data-dismiss="modal">FECHAR</button>
    </div>
  </div>
</div>
<!-- /.modal --> 

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 

<script type="application/javascript">
$(document).on('click', '.delete-btn', function(){
	var aula = $(this).attr('id');
	var id = $(this).attr('cod');
		
	Swal.fire({
	  title: 'Deletar esta aula?',
	  text: "Você não poderá reverter a exclusão.",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Sim, excluir!'
	}).then((result) => {
	  if (result.isConfirmed) {
					jQuery.ajax({
						type: "POST",
						url: "crud/aulas/delete.php",
						data: {aula: aula},
						cache: true,
						success: function (data)
						{
							$("#aula_"+id).hide();
							
							$("#linkResultado").html(data);
							
							setTimeout(function () {
								window.location.reload(1);
							}, 1500);
							
						}
					});
		  
		//Swal.fire(
		 // 'Deletado!',
		 // 'Atualizando....',
		  //'success'
		//)
		
		
	  }
	})
	});
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
<?php
mysql_free_result($aulas);
?>
