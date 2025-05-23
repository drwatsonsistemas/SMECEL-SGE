<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php 

$colname_Matricula = isset($_GET['cod']) ? $_GET['cod'] : "-1";
$query_Matricula = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
    vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
    vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
    vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    WHERE vinculo_aluno_hash = :cod";
$stmt = $SmecelNovo->prepare($query_Matricula);
$stmt->bindParam(':cod', $colname_Matricula, PDO::PARAM_STR);
$stmt->execute();
$row_Matricula = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Matricula = $stmt->rowCount();

$colname_Disciplina = isset($_GET['disciplina']) ? $_GET['disciplina'] : "-1";
$query_Disciplina = "
    SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_id_campos_exp 
    FROM smc_disciplina 
    WHERE disciplina_id = :disciplina";
$stmt = $SmecelNovo->prepare($query_Disciplina);
$stmt->bindParam(':disciplina', $colname_Disciplina, PDO::PARAM_INT);
$stmt->execute();
$row_Disciplina = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Disciplina = $stmt->rowCount();

$colname_Turma = isset($_GET['turma']) ? $_GET['turma'] : "-1";
$query_Turma = "
    SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
    FROM smc_turma 
    WHERE turma_id = :turma";
$stmt = $SmecelNovo->prepare($query_Turma);
$stmt->bindParam(':turma', $colname_Turma, PDO::PARAM_INT);
$stmt->execute();
$row_Turma = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Turma = $stmt->rowCount();

$query_Matriz = "
    SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo 
    FROM smc_matriz 
    WHERE matriz_id = :matriz";
$stmt = $SmecelNovo->prepare($query_Matriz);
$stmt->bindParam(':matriz', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
$stmt->execute();
$row_Matriz = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Matriz = $stmt->rowCount();

$query_Criterios = "
    SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito 
    FROM smc_criterios_avaliativos 
    WHERE ca_id = :criterio";
$stmt = $SmecelNovo->prepare($query_Criterios);
$stmt->bindParam(':criterio', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt->execute();
$row_Criterios = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Criterios = $stmt->rowCount();

$query_Campos = "
    SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos 
    FROM smc_campos_exp 
    WHERE campos_exp_id = :campos_exp";
$stmt = $SmecelNovo->prepare($query_Campos);
$stmt->bindParam(':campos_exp', $row_Disciplina['disciplina_id_campos_exp'], PDO::PARAM_INT);
$stmt->execute();
$row_Campos = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Campos = $stmt->rowCount();

$query_Questionario = "
    SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
    FROM smc_questionario_conceitos
    WHERE quest_conc_id_matriz = :matriz
    AND quest_conc_id_comp = :disciplina
    ORDER BY quest_conc_descricao ASC";
$stmt = $SmecelNovo->prepare($query_Questionario);
$stmt->bindParam(':matriz', $row_Matriz['matriz_id'], PDO::PARAM_INT);
$stmt->bindParam(':disciplina', $row_Disciplina['disciplina_id'], PDO::PARAM_INT);
$stmt->execute();
$questionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Questionario = count($questionarios);

$query_GrupoConceitos = "
    SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
    FROM smc_conceito_itens 
    WHERE conceito_itens_id_conceito = :grupo_conceito";
$stmt = $SmecelNovo->prepare($query_GrupoConceitos);
$stmt->bindParam(':grupo_conceito', $row_Criterios['ca_grupo_conceito'], PDO::PARAM_INT);
$stmt->execute();
$grupo_conceitos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_GrupoConceitos = count($grupo_conceitos);

$conceito = [];
foreach ($grupo_conceitos as $row) {
    $conceito[] = "{$row['conceito_itens_peso']}|{$row['conceito_itens_legenda']}|{$row['conceito_itens_descricao']}";
}

$colname_Periodo = "1";
if (isset($_GET['periodo'])) {
  $colname_Periodo = $_GET['periodo'];
} else {
	$colname_Periodo = "1";
	}


$query_AlterarStatus = "
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
    WHERE vinculo_aluno_boletim = '0' AND vinculo_aluno_id_escola = :escola AND vinculo_aluno_hash = :hash";
$stmt = $SmecelNovo->prepare($query_AlterarStatus);
$stmt->bindParam(':escola', $row_Turma['turma_id_escola'], PDO::PARAM_INT);
$stmt->bindParam(':hash', $colname_Matricula, PDO::PARAM_STR);
$stmt->execute();
$row_AlterarStatus = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_AlterarStatus = $stmt->rowCount();

$query_Criterios1 = "
    SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito 
    FROM smc_criterios_avaliativos 
    WHERE ca_id = :criterio1";
$stmt = $SmecelNovo->prepare($query_Criterios1);
$stmt->bindParam(':criterio1', $row_AlterarStatus['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt->execute();
$row_Criterios1 = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Criterios1 = $stmt->rowCount();

$query_acompanhamento = "
    SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
    FROM smc_acomp_proc_aprend
    WHERE acomp_id_matriz = :matriz AND acomp_id_crit = :crit";
$stmt = $SmecelNovo->prepare($query_acompanhamento);
$stmt->bindParam(':matriz', $row_AlterarStatus['matriz_id'], PDO::PARAM_INT);
$stmt->bindParam(':crit', $row_Criterios1['ca_id'], PDO::PARAM_INT);
$stmt->execute();
$acompanhamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_acompanhamento = count($acompanhamento);

$query_questionario1 = "
    SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
    FROM smc_questionario_conceitos
    WHERE quest_conc_id_matriz = :matriz";
$stmt = $SmecelNovo->prepare($query_questionario1);
$stmt->bindParam(':matriz', $row_AlterarStatus['matriz_id'], PDO::PARAM_INT);
$stmt->execute();
$questionario1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_questionario1 = count($questionario1);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["MM_update"]) && $_POST["MM_update"] == "form1") {
    $updateSQL = "
        UPDATE smc_vinculo_aluno 
        SET vinculo_aluno_boletim = :boletim 
        WHERE vinculo_aluno_id = :aluno";
    $stmt = $SmecelNovo->prepare($updateSQL);
    $stmt->bindParam(':boletim', $_POST['vinculo_aluno_boletim'], PDO::PARAM_INT);
    $stmt->bindParam(':aluno', $_POST['aluno'], PDO::PARAM_INT);
    $stmt->execute();

    foreach ($questionario1 as $quest) {
        for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) {
            $queryInsert = "
                INSERT INTO smc_conceito_ef (conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo) 
                VALUES (:quest, :matr, :periodo)";
            $stmtInsert = $SmecelNovo->prepare($queryInsert);
            $stmtInsert->bindParam(':quest', $quest['quest_conc_id'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':matr', $_POST['aluno'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':periodo', $p, PDO::PARAM_INT);
            $stmtInsert->execute();
        }
    }

    $updateGoTo = "conceitoEf.php?boletimcadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
    exit();
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

<?php if ($row_Matricula['vinculo_aluno_boletim'] == 1) { ?>

<div class="ls-group-btn ls-group-active">
<?php for ($i = 1; $i <= $row_Criterios['ca_qtd_periodos']; $i++) { ?>
      <a class="ls-btn-primary <?php if ($colname_Periodo <> $i) { echo "ls-active"; } ?>" href="conceitoEf.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º</a>
<?php } ?>
</div>

<hr>

<h3><?php echo $row_Disciplina['disciplina_nome']; ?> - <?php echo $colname_Periodo; ?>º PERÍODO</h3>

<table class="ls-table ls-table-striped">
<tbody>
<?php foreach ($questionarios as $row_Questionario) { ?>

<tr>
      <td width="100%">
      <p><strong><?php echo $row_Questionario['quest_conc_descricao']; ?></strong></p>

<?php
$query_AvaConceito = "
SELECT conc_ef_id, conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac 
FROM smc_conceito_ef
WHERE conc_ef_id_quest = :quest AND conc_ef_id_matr = :matricula AND conc_ef_periodo = :periodo";
$stmt = $SmecelNovo->prepare($query_AvaConceito);
$stmt->bindParam(':quest', $row_Questionario['quest_conc_id'], PDO::PARAM_INT);
$stmt->bindParam(':matricula', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
$stmt->bindParam(':periodo', $colname_Periodo, PDO::PARAM_INT);
$stmt->execute();
$row_AvaConceito = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_AvaConceito = $stmt->rowCount();
?>

<p>
    <small>
    <label class="ls-label-text">
      <input class="" periodo="<?php echo $colname_Periodo; ?>" q="<?php echo $row_Questionario['quest_conc_id']; ?>" matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" name="<?php echo $row_AvaConceito ? $row_AvaConceito['conc_ef_id'] : 0; ?>" value="NULL" type="radio" <?php if (empty($row_AvaConceito['conc_ef_avaliac'])) { echo "checked"; } ?> />
      <span class="ls-color-warning">SEM REGISTRO</span>
    </label>
    </small>
</p>

<?php foreach ($conceito as &$valor) { ?>
<?php $valor1 = explode("|", $valor); ?>
<p>
    <small>
    <label class="ls-label-text">
      <input class="" periodo="<?php echo $colname_Periodo; ?>" q="<?php echo $row_Questionario['quest_conc_id']; ?>" matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" name="<?php echo $row_AvaConceito ? $row_AvaConceito['conc_ef_id'] : 0; ?>" value="<?php echo $valor1[0]; ?>" type="radio" <?php if ($row_AvaConceito['conc_ef_avaliac'] == $valor1[0]) { echo "checked"; } ?> />
      <span><?php echo $valor1[2]; ?> (<?php echo $valor1[1]; ?>) </span>
    </label>
    </small>
</p>
<?php } ?>

</td>
</tr>

<?php } ?>
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
        <input type="text" value="<?php echo $row_AlterarStatus['turma_nome']; ?> - <?php if ($row_AlterarStatus['turma_turno'] == 1) { echo "MATUTINO"; } elseif ($row_AlterarStatus['turma_turno'] == 2) { echo "VESPERTINO"; } elseif ($row_AlterarStatus['turma_turno'] == 3) { echo "NOTURNO"; } else { echo "INTEGRAL"; } ?>" class="ls-field" disabled>
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
