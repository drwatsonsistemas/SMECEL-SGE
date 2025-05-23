<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php 

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula); 

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = $_GET['disciplina'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_id_campos_exp FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp WHERE campos_exp_id = '$row_Disciplina[disciplina_id_campos_exp]'";
$Campos = mysql_query($query_Campos, $SmecelNovo) or die(mysql_error());
$row_Campos = mysql_fetch_assoc($Campos);
$totalRows_Campos = mysql_num_rows($Campos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens 
WHERE conceito_itens_id_conceito = '$row_Criterios[ca_grupo_conceito]'";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

do { 
	$conceito[] = $row_GrupoConceitos['conceito_itens_peso']."|".$row_GrupoConceitos['conceito_itens_legenda']."|".$row_GrupoConceitos['conceito_itens_descricao'];
} while ($row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos));

$colname_Periodo = "1";
if (isset($_GET['periodo'])) {
  $colname_Periodo = $_GET['periodo'];
} else {
	$colname_Periodo = "1";
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
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}

th, td {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $row_Turma['turma_id']; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

 <blockquote class="ls-box ls-lg-space ls-ico-user-add ls-ico-bg"> 
    <span style="margin-right:10px; text-align:center; float:left;">
    <?php if ($row_Matricula['aluno_foto']=="") { ?>
    <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
    <?php } else { ?>
    <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0" width="50">
    <?php } ?>
    <?php //echo $row_Alunos['aluno_nome']; ?>
    </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
    Disciplina: <strong><?php echo $row_Disciplina['disciplina_nome']; ?></strong><br>
    Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong> 
 </blockquote>   




<hr>

<div class="ls-group-btn ls-group-active">
<?php for ($i = 1; $i <= $row_Criterios['ca_qtd_periodos']; $i++) { ?>
      <a class="ls-btn-primary <?php if ($colname_Periodo <> $i) { echo "ls-active"; } ?>" href="conceito.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º</a>
<?php } ?>
</div>
<hr>

<h3 class="ls-box"><?php echo $colname_Periodo; ?>º PERÍODO</h3>

<?php do { ?>

<div class="ls-box">
  <h4 class="ls-title-2"><?php echo utf8_encode($row_Campos['campos_exp_nome']); ?></h4>
  <p><?php echo utf8_encode($row_Campos['campos_exp_mais']); ?></p>
</div>



  
  	<?php
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Objetivos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id_campos_exp = '$row_Campos[campos_exp_id]' AND campos_exp_obj_faixa_et_cod = '$row_Criterios[ca_grupo_etario]' ORDER BY campos_exp_obj_campos_exp ASC";
	$Objetivos = mysql_query($query_Objetivos, $SmecelNovo) or die(mysql_error());
	$row_Objetivos = mysql_fetch_assoc($Objetivos);
	$totalRows_Objetivos = mysql_num_rows($Objetivos);

	?>
	<?php do { ?>
    
    
  <div data-ls-module="collapse" data-target="#ob_<?php echo $row_Objetivos['campos_exp_obj_campos_id']; ?>" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title"><?php echo utf8_encode($row_Objetivos['campos_exp_obj_campos_exp']); ?></h3>
    </a>
    <div class="ls-collapse-body" id="ob_<?php echo $row_Objetivos['campos_exp_obj_campos_id']; ?>">
      <p>
        <?php echo utf8_encode( $row_Objetivos['campos_exp_obj_abordagem']); ?>
      </p>
    </div>
  </div>	
    
      
    <?php
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Conceito = "
	SELECT conc_id, conc_acomp_id, conc_matricula_id, conc_periodo, conc_avaliacao,
	acomp_id, acomp_id_obj_aprend, acomp_descricao, 
	campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_campos_exp 
	FROM smc_conceito_aluno
	INNER JOIN smc_acomp_proc_aprend ON acomp_id = conc_acomp_id
	INNER JOIN smc_campos_exp_objetivos ON campos_exp_obj_id = acomp_id_obj_aprend
	WHERE conc_matricula_id = '$row_Matricula[vinculo_aluno_id]' 
	AND conc_periodo = '$colname_Periodo'
	AND acomp_id_obj_aprend = '$row_Objetivos[campos_exp_obj_id]'
	";
	$Conceito = mysql_query($query_Conceito, $SmecelNovo) or die(mysql_error());
	$row_Conceito = mysql_fetch_assoc($Conceito);
	$totalRows_Conceito = mysql_num_rows($Conceito);
	
	
	if($totalRows_Conceito == 0) {
		//header("Location:index.php?erro");
	}

	?>  
  <table class="ls-table ls-table-striped">
  <tbody>
  <?php do { ?>
    <tr>
      <td width="100%">
	  <p><strong><?php echo $row_Conceito['acomp_descricao']; ?></strong></p>
	  
      <?php foreach ($conceito as &$valor) { ?>
      <?php $valor1 = explode("|", $valor); ?>
		
    <p>
    <small>
    <label class="ls-label-text">
      <input class="" periodo="<?php echo $colname_Periodo; ?>" matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" name="<?php echo $row_Conceito['conc_acomp_id']; ?>" value="<?php echo $valor1[0]; ?>" type="radio" <?php if ( $row_Conceito['conc_avaliacao']==$valor1[0]) { echo "checked"; } ?> />
      <span><?php echo $valor1[2]; ?> (<?php echo $valor1[1]; ?>) </span>
    </label>
    </small>
    </p>		
		
		
		<?php } ?>
      </td>
    </tr>
    <?php } while ($row_Conceito = mysql_fetch_assoc($Conceito)); ?>
    </tbody>
</table>
<br>
    
    <?php } while ($row_Objetivos = mysql_fetch_assoc($Objetivos)); ?>
    


<?php } while ($row_Campos = mysql_fetch_assoc($Campos)); ?>
<br>





	<div id="status"></div>

        

    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="text/javascript">
				
$(document).ready(function(){
$("input").click(function(){
	
	
	
	var objeto 		= $(this).attr('name');
	var valor 	= $(this).attr('value');
	var matricula = $(this).attr('matricula'); 
	var periodo = $(this).attr('periodo'); 

	//alert(matricula + "-" + objeto + "-" + valor + "-" + periodo);
	
	


	
	$.ajax({
		type : 'POST',
        url  : 'fnc/lancaConceito.php',
        data : {
			objeto			:objeto,
			valor			:valor,
			matricula		:matricula,
			periodo			:periodo
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},15000);
				
				}
		})
	
	  });
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