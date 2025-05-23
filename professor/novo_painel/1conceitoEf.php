<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
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
$query_Questionario = "
SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
FROM smc_questionario_conceitos
WHERE quest_conc_id_matriz = '$row_Matriz[matriz_id]' AND quest_conc_id_comp = '$row_Disciplina[disciplina_id]'
ORDER BY quest_conc_descricao ASC
";
$Questionario = mysql_query($query_Questionario, $SmecelNovo) or die(mysql_error());
$row_Questionario = mysql_fetch_assoc($Questionario);
$totalRows_Questionario = mysql_num_rows($Questionario);

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









$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

 $colname_AlterarStatus = "-1";
if (isset($_GET['cod'])) {
  $colname_AlterarStatus = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlterarStatus = sprintf("
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1,
turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, 
etapa_id, etapa_nome, 
matriz_id, matriz_nome, matriz_criterio_avaliativo
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
WHERE vinculo_aluno_boletim = '0' AND (vinculo_aluno_id_escola = '$row_Turma[turma_id_escola]' AND vinculo_aluno_hash = %s)", GetSQLValueString($colname_AlterarStatus, "text"));
$AlterarStatus = mysql_query($query_AlterarStatus, $SmecelNovo) or die(mysql_error());
$row_AlterarStatus = mysql_fetch_assoc($AlterarStatus);
$totalRows_AlterarStatus = mysql_num_rows($AlterarStatus);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios1 = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_AlterarStatus[matriz_criterio_avaliativo]'";
$Criterios1 = mysql_query($query_Criterios1, $SmecelNovo) or die(mysql_error());
$row_Criterios1 = mysql_fetch_assoc($Criterios1);
$totalRows_Criterios1 = mysql_num_rows($Criterios1);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_acompanhamento = "
SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
FROM smc_acomp_proc_aprend
WHERE acomp_id_matriz = '$row_AlterarStatus[matriz_id]'
AND acomp_id_crit = '$row_Criterios1[ca_id]'
";
$acompanhamento = mysql_query($query_acompanhamento, $SmecelNovo) or die(mysql_error());
$row_acompanhamento = mysql_fetch_assoc($acompanhamento);
$totalRows_acompanhamento = mysql_num_rows($acompanhamento);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_questionario1 = "
SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
FROM smc_questionario_conceitos
WHERE quest_conc_id_matriz = '$row_AlterarStatus[matriz_id]'
";
$questionario1 = mysql_query($query_questionario1, $SmecelNovo) or die(mysql_error());
$row_questionario1 = mysql_fetch_assoc($questionario1);
$totalRows_questionario1 = mysql_num_rows($questionario1);

$numPeriodos = $row_Criterios['ca_qtd_periodos'];


if ($totalRows_AlterarStatus==0) {
	//header("Location:conceito.php?erro");	
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
  $matriz = $_POST['matriz'];	
  $idVinculo = $_POST['aluno'];	
  

	
  $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim=%s WHERE vinculo_aluno_id=%s",
                       GetSQLValueString($_POST['vinculo_aluno_boletim'], "int"),
                       GetSQLValueString($_POST['aluno'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  
  
do { 
//Loop disciplinas

		for ($p = 1; $p <= $numPeriodos; $p++) {
		//Loop períodos	
			$query = mysql_query("INSERT INTO smc_conceito_ef (conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo) VALUES ('$row_questionario1[quest_conc_id]', '$idVinculo','$p')");
			}


} while ($row_questionario1 = mysql_fetch_assoc($questionario1));
  

  $updateGoTo = "conceitoEf.php?boletimcadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
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

<?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?>

<div class="ls-group-btn ls-group-active">
<?php for ($i = 1; $i <= $row_Criterios['ca_qtd_periodos']; $i++) { ?>
      <a class="ls-btn-primary <?php if ($colname_Periodo <> $i) { echo "ls-active"; } ?>" href="conceitoEf.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º</a>
<?php } ?>
</div>

<hr>

<h3><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $colname_Periodo; ?>º PERÍODO</h3>




<table class="ls-table ls-table-striped">
<tbody>
<?php do { ?>



<tr>
      <td width="100%">
	  <p><strong><?php echo $row_Questionario['quest_conc_descricao']; ?></strong></p>
      
      <?php 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AvaConceito = "
SELECT conc_ef_id, conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac 
FROM smc_conceito_ef
WHERE conc_ef_id_quest = '$row_Questionario[quest_conc_id]' AND conc_ef_id_matr = '$row_Matricula[vinculo_aluno_id]' AND conc_ef_periodo = '$colname_Periodo'
";
$AvaConceito = mysql_query($query_AvaConceito, $SmecelNovo) or die(mysql_error());
$row_AvaConceito = mysql_fetch_assoc($AvaConceito);
$totalRows_AvaConceito = mysql_num_rows($AvaConceito);
?>
              <p>
    <small>
    <label class="ls-label-text">
      <input class="" periodo="<?php echo $colname_Periodo; ?>" q="<?php echo $row_Questionario['quest_conc_id']; ?>" matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" name="<?php echo $row_AvaConceito['conc_ef_id']; ?>" value="NULL" type="radio" <?php if ( $row_AvaConceito['conc_ef_avaliac']=="") { echo "checked"; } ?> />
      <span class="ls-color-warning">SEM REGISTRO</span>
    </label>
    </small>
    </p>
      
      <?php foreach ($conceito as &$valor) { ?>
      <?php $valor1 = explode("|", $valor); ?>
		
    <p>
    <small>
    <label class="ls-label-text">
      <input class="" periodo="<?php echo $colname_Periodo; ?>" q="<?php echo $row_Questionario['quest_conc_id']; ?>" matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" name="<?php echo $row_AvaConceito['conc_ef_id']; ?>" value="<?php echo $valor1[0]; ?>" type="radio" <?php if ( $row_AvaConceito['conc_ef_avaliac']==$valor1[0]) { echo "checked"; } ?> />
      <span><?php echo $valor1[2]; ?> (<?php echo $valor1[1]; ?>) </span>
    </label>
    </small>
    </p>		
		
		
		<?php } ?>
        

      
      
      </td>
</tr>

  
<?php } while ($row_Questionario = mysql_fetch_assoc($Questionario)); ?>
</tbody>
</table>
  
  
  


	<div id="status"></div>

 <?php } else { ?>
 
<div class="ls-alert-info"><strong>Atenção:</strong> O questionário de conceitos para este aluno ainda não foi gerado pela escola.</div> 

    <div class="col-sm-12">
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal row" data-ls-module="form">
      <fieldset>
        <div class="row">
          <label class="ls-label col-md-4"> <b class="ls-label-text">Nome</b>
            <input type="text" value="<?php echo $row_AlterarStatus['aluno_nome']; ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-4"> <b class="ls-label-text">Nascimento</b>
            <input type="text" value="<?php echo date("d/m/Y", strtotime($row_AlterarStatus['aluno_nascimento'])); ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-4"> <b class="ls-label-text">Filiação</b>
            <input type="text" value="<?php echo $row_AlterarStatus['aluno_filiacao1']; ?>" class="ls-field" disabled>
          </label>
        </div>
        <div class="row">
          <label class="ls-label col-md-3"> <b class="ls-label-text">Turma</b>
            <input type="text" value="<?php echo $row_AlterarStatus['turma_nome']; ?> - <?php if ($row_AlterarStatus['turma_turno']==1) { echo "MATUTINO"; } else if ($row_AlterarStatus['turma_turno']==2) { echo "VESPERTINO"; } else if ($row_AlterarStatus['turma_turno']==3) { echo "NOTURNO"; } else { echo "INTEGRAL"; } ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-3"> <b class="ls-label-text">Matriz</b>
            <input type="text" value="<?php echo $row_AlterarStatus['matriz_nome']; ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-4"> <b class="ls-label-text">Etapa</b>
            <input type="text" value="<?php echo $row_AlterarStatus['etapa_nome']; ?>" class="ls-field" disabled>
          </label>
          <label class="ls-label col-md-2"> <b class="ls-label-text">Ano Letivo</b>
            <input type="text" value="<?php echo $row_AlterarStatus['vinculo_aluno_ano_letivo']; ?>" class="ls-field" disabled>
          </label>
        </div>
      </fieldset>
      <div class="ls-actions-btn">
        <input type="submit" value="GERAR QUESTIONÁRIO DE CONCEITOS" class="ls-btn-primary">
        <a class="ls-btn-danger" href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $row_Turma['turma_id']; ?>">Cancelar</a> </div>
      <input type="hidden" name="vinculo_aluno_boletim" value="1">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="matriz" value="<?php echo $row_AlterarStatus['matriz_id']; ?>">
      <input type="hidden" name="aluno" value="<?php echo $row_AlterarStatus['vinculo_aluno_id']; ?>">
      <input type="hidden" name="detalhes" value="<?php echo $row_AlterarStatus['aluno_nome']; ?> - <?php echo $row_AlterarStatus['turma_nome']; ?>">
    </form>
    </div>
    <p>&nbsp;</p>


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
<script type="text/javascript">
				
$(document).ready(function(){
$("input").click(function(){
	
	
	
	var objeto 		= $(this).attr('name');
	var valor 		= $(this).attr('value');
	var matricula 	= $(this).attr('matricula'); 
	var periodo 	= $(this).attr('periodo');  
	var q 			= $(this).attr('q');  

	//alert(matricula + "-" + objeto + "-" + valor + "-" + periodo);
	
	


	
	$.ajax({
		type : 'POST',
        url  : 'fnc/lancaConceitoEf.php',
        data : {
			objeto			:objeto,
			valor			:valor,
			matricula		:matricula,
			periodo			:periodo,
			q				:q
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
<?php
mysql_free_result($Disciplina);

mysql_free_result($AvaConceito);

mysql_free_result($Questionario);
?>
