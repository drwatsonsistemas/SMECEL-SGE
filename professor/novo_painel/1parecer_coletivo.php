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
vinculo_aluno_vacina_atualizada, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto
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
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    <p><a href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a></p>


<blockquote> 
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

<div class="ls-box">
    <span class="">
    	Preencha o campo abaixo separando os períodos (bimestre/unidade) por parágrafo. 
    </span>
  </div>



    <form class="ls-form">
    
    <label class="ls-label">
    <b class="ls-label-text">Texto</b>
    <textarea id="rel_avaliativo" class="materialize-textarea"><?php echo $row_Matricula['vinculo_aluno_rel_aval']; ?></textarea>
  </label>
  

      <div class="ls-actions-btn">
      <input type="hidden" value="<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" id="matricula"/>
      <a href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn"> Voltar</a>
      <button id="salvar" type="button" class="ls-btn-primary">SALVAR</button>
      </div>
      
    </form>
    
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
	  <script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>
	  <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
      <script src="langs/pt_BR.js"></script>
<script type="text/javascript">
				
$(document).ready(function(){
$("#salvar").click(function(){
	
	
	var conteudo = $("#rel_avaliativo").val();
	var matricula = $("#matricula").val();
	
	
	$.ajax({
		type : 'POST',
        url  : 'fnc/lancaRel.php',
        data : {
			conteudo				:conteudo,
			matricula				:matricula
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
			 		  
		   


$(function() {
   $(document).on('click', 'input[type=text]', function() {
     this.select();
   });
 });	 

</script>

   <script>
    tinymce.init({
      selector: '#rel_avaliativo',
      plugins: 'paste advlist autolink lists link image charmap print preview hr anchor pagebreak table',
      toolbar_mode: 'floating',
	  toolbar: 'undo redo | formatselect | ' +
	  'bold italic backcolor | alignleft aligncenter ' +
	  'alignright alignjustify | bullist numlist outdent indent | ' +
	  'removeformat | table',
    paste_as_text: true,
    paste_text_sticky: true,
    paste_text_sticky_default: true,
	  table_appearance_options: false,
	  menubar: false,
	  statusbar: false,
	  height: 400,
	  setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
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