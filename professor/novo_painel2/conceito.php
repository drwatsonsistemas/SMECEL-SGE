<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php"; 
include "fnc/anti_injection.php"; 

// Configuração inicial
$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
    $colname_Matricula = $_GET['cod'];
}

// Conexão PDO (assumindo que $SmecelNovo é sua conexão PDO configurada)
$query_Matricula = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
    vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
    vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
    vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    WHERE vinculo_aluno_hash = :hash";
$stmt_Matricula = $SmecelNovo->prepare($query_Matricula);
$stmt_Matricula->bindParam(':hash', $colname_Matricula, PDO::PARAM_STR);
$stmt_Matricula->execute();
$row_Matricula = $stmt_Matricula->fetch(PDO::FETCH_ASSOC);
$totalRows_Matricula = $stmt_Matricula->rowCount();

// Disciplina
$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
    $colname_Disciplina = $_GET['disciplina'];
}
$query_Disciplina = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, 
    disciplina_nome, disciplina_nome_abrev, disciplina_id_campos_exp 
    FROM smc_disciplina WHERE disciplina_id = :disciplina_id";
$stmt_Disciplina = $SmecelNovo->prepare($query_Disciplina);
$stmt_Disciplina->bindParam(':disciplina_id', $colname_Disciplina, PDO::PARAM_INT);
$stmt_Disciplina->execute();
$row_Disciplina = $stmt_Disciplina->fetch(PDO::FETCH_ASSOC);
$totalRows_Disciplina = $stmt_Disciplina->rowCount();

// Turma
$colname_Turma = "-1";
if (isset($_GET['turma'])) {
    $colname_Turma = $_GET['turma'];
}
$query_Turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, 
    turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo 
    FROM smc_turma WHERE turma_id = :turma_id";
$stmt_Turma = $SmecelNovo->prepare($query_Turma);
$stmt_Turma->bindParam(':turma_id', $colname_Turma, PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);
$totalRows_Turma = $stmt_Turma->rowCount();

// Matriz
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, 
    matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, 
    matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo 
    FROM smc_matriz WHERE matriz_id = :matriz_id";
$stmt_Matriz = $SmecelNovo->prepare($query_Matriz);
$stmt_Matriz->bindParam(':matriz_id', $row_Turma['turma_matriz_id'], PDO::PARAM_INT);
$stmt_Matriz->execute();
$row_Matriz = $stmt_Matriz->fetch(PDO::FETCH_ASSOC);
$totalRows_Matriz = $stmt_Matriz->rowCount();

// Critérios
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, 
    ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, 
    ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, 
    ca_detalhes, ca_grupo_etario, ca_grupo_conceito 
    FROM smc_criterios_avaliativos WHERE ca_id = :ca_id";
$stmt_Criterios = $SmecelNovo->prepare($query_Criterios);
$stmt_Criterios->bindParam(':ca_id', $row_Matriz['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt_Criterios->execute();
$row_Criterios = $stmt_Criterios->fetch(PDO::FETCH_ASSOC);
$totalRows_Criterios = $stmt_Criterios->rowCount();

// Campos
$query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, 
    campos_exp_direitos FROM smc_campos_exp WHERE campos_exp_id = :campos_exp_id";
$stmt_Campos = $SmecelNovo->prepare($query_Campos);
$stmt_Campos->bindParam(':campos_exp_id', $row_Disciplina['disciplina_id_campos_exp'], PDO::PARAM_INT);
$stmt_Campos->execute();

$totalRows_Campos = $stmt_Campos->rowCount();

// Grupo Conceitos
$query_GrupoConceitos = "SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, 
    conceito_itens_legenda, conceito_itens_peso 
    FROM smc_conceito_itens 
    WHERE conceito_itens_id_conceito = :conceito_id";
$stmt_GrupoConceitos = $SmecelNovo->prepare($query_GrupoConceitos);
$stmt_GrupoConceitos->bindParam(':conceito_id', $row_Criterios['ca_grupo_conceito'], PDO::PARAM_INT);
$stmt_GrupoConceitos->execute();

$conceito = array();
while ($row_GrupoConceitos = $stmt_GrupoConceitos->fetch(PDO::FETCH_ASSOC)) {
    $conceito[] = $row_GrupoConceitos['conceito_itens_peso'] . "|" . 
                  $row_GrupoConceitos['conceito_itens_legenda'] . "|" . 
                  $row_GrupoConceitos['conceito_itens_descricao'];
}

// Período
$colname_Periodo = "1";
if (isset($_GET['periodo'])) {
    $colname_Periodo = $_GET['periodo'];
}

// Form Action
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Alterar Status
$colname_AlterarStatus = "-1";
if (isset($_GET['cod'])) {
    $colname_AlterarStatus = $_GET['cod'];
}
$query_AlterarStatus = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
    vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
    vinculo_aluno_verificacao, vinculo_aluno_boletim, aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1,
    turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, etapa_id, etapa_nome, 
    matriz_id, matriz_nome, matriz_criterio_avaliativo
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    INNER JOIN smc_etapa ON etapa_id = turma_etapa
    INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
    WHERE vinculo_aluno_boletim = '0' AND (vinculo_aluno_id_escola = :escola_id AND vinculo_aluno_hash = :hash)";
$stmt_AlterarStatus = $SmecelNovo->prepare($query_AlterarStatus);
$stmt_AlterarStatus->bindParam(':escola_id', $row_Turma['turma_id_escola'], PDO::PARAM_INT);
$stmt_AlterarStatus->bindParam(':hash', $colname_AlterarStatus, PDO::PARAM_STR);
$stmt_AlterarStatus->execute();
$row_AlterarStatus = $stmt_AlterarStatus->fetch(PDO::FETCH_ASSOC);
$totalRows_AlterarStatus = $stmt_AlterarStatus->rowCount();

// Critérios 1
$query_Criterios1 = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, 
    ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, 
    ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, 
    ca_detalhes, ca_grupo_etario, ca_grupo_conceito 
    FROM smc_criterios_avaliativos WHERE ca_id = :ca_id";
$stmt_Criterios1 = $SmecelNovo->prepare($query_Criterios1);
$stmt_Criterios1->bindParam(':ca_id', $row_AlterarStatus['matriz_criterio_avaliativo'], PDO::PARAM_INT);
$stmt_Criterios1->execute();
$row_Criterios1 = $stmt_Criterios1->fetch(PDO::FETCH_ASSOC);
$totalRows_Criterios1 = $stmt_Criterios1->rowCount();

// Acompanhamento
$query_acompanhamento = "
    SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
    FROM smc_acomp_proc_aprend
    WHERE acomp_id_matriz = :matriz_id AND acomp_id_crit = :crit_id";
$stmt_acompanhamento = $SmecelNovo->prepare($query_acompanhamento);
$stmt_acompanhamento->bindParam(':matriz_id', $row_AlterarStatus['matriz_id'], PDO::PARAM_INT);
$stmt_acompanhamento->bindParam(':crit_id', $row_Criterios1['ca_id'], PDO::PARAM_INT);
$stmt_acompanhamento->execute();
$row_acompanhamento = $stmt_acompanhamento->fetch(PDO::FETCH_ASSOC);
$totalRows_acompanhamento = $stmt_acompanhamento->rowCount();

$numPeriodos = $row_Criterios['ca_qtd_periodos'];

if ($totalRows_AlterarStatus == 0) {
    //header("Location:conceito.php?erro");
}

// Update Form
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    $matriz = $_POST['matriz'];
    $idVinculo = $_POST['aluno'];

    // Update vinculo_aluno
    $updateSQL = "UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim = :boletim WHERE vinculo_aluno_id = :vinculo_id";
    $stmt_update = $SmecelNovo->prepare($updateSQL);
    $stmt_update->bindParam(':boletim', $_POST['vinculo_aluno_boletim'], PDO::PARAM_INT);
    $stmt_update->bindParam(':vinculo_id', $_POST['aluno'], PDO::PARAM_INT);
    $stmt_update->execute();

    // Reset acompanhamento statement para loop
    $stmt_acompanhamento->execute();
    while ($row_acompanhamento = $stmt_acompanhamento->fetch(PDO::FETCH_ASSOC)) {
        for ($p = 1; $p <= $numPeriodos; $p++) {
            $insertSQL = "INSERT INTO smc_conceito_aluno (conc_acomp_id, conc_matricula_id, conc_periodo) 
                         VALUES (:acomp_id, :matricula_id, :periodo)";
            $stmt_insert = $SmecelNovo->prepare($insertSQL);
            $stmt_insert->bindParam(':acomp_id', $row_acompanhamento['acomp_id'], PDO::PARAM_INT);
            $stmt_insert->bindParam(':matricula_id', $idVinculo, PDO::PARAM_INT);
            $stmt_insert->bindParam(':periodo', $p, PDO::PARAM_INT);
            $stmt_insert->execute();
        }
    }

    $updateGoTo = "conceito.php?boletimcadastrado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
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
      <a class="ls-btn-primary <?php if ($colname_Periodo <> $i) { echo "ls-active"; } ?>" href="conceito.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º</a>
<?php } ?>
</div>
<hr>

<h3 class="ls-box"><?php echo $colname_Periodo; ?>º PERÍODO</h3>

<?php 
// Primeiro loop do Campos já foi convertido no código anterior
while ($row_Campos = $stmt_Campos->fetch(PDO::FETCH_ASSOC)) { 
?>


<div class="ls-box">
  <h4 class="ls-title-2"><?php echo htmlspecialchars(utf8_encode($row_Campos['campos_exp_nome']), ENT_QUOTES, 'UTF-8'); ?></h4>
  <p><?php echo htmlspecialchars(utf8_encode($row_Campos['campos_exp_mais']), ENT_QUOTES, 'UTF-8'); ?></p>
</div>

<?php
$query_Objetivos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, 
    campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, 
    campos_exp_obj_abordagem, campos_exp_obj_sugestoes 
    FROM smc_campos_exp_objetivos 
    WHERE campos_exp_obj_id_campos_exp = :campos_exp_id 
    AND campos_exp_obj_faixa_et_cod = :grupo_etario 
    ORDER BY campos_exp_obj_campos_exp ASC";
$stmt_Objetivos = $SmecelNovo->prepare($query_Objetivos);
$stmt_Objetivos->bindParam(':campos_exp_id', $row_Campos['campos_exp_id'], PDO::PARAM_INT);
$stmt_Objetivos->bindParam(':grupo_etario', $row_Criterios['ca_grupo_etario'], PDO::PARAM_INT);
$stmt_Objetivos->execute();
$totalRows_Objetivos = $stmt_Objetivos->rowCount();

?>

<?php while ($row_Objetivos = $stmt_Objetivos->fetch(PDO::FETCH_ASSOC)) { ?>
    
  <div data-ls-module="collapse" data-target="#ob_<?php echo $row_Objetivos['campos_exp_obj_id']; ?>" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title"><?php echo utf8_encode($row_Objetivos['campos_exp_obj_campos_exp']); ?></h3>
    </a>
    <div class="ls-collapse-body" id="ob_<?php echo $row_Objetivos['campos_exp_obj_id']; ?>">
      <p>
        <?php echo utf8_encode($row_Objetivos['campos_exp_obj_abordagem']); ?>
      </p>
    </div>
  </div>	
    
<?php
$query_Conceito = "
    SELECT conc_id, conc_acomp_id, conc_matricula_id, conc_periodo, conc_avaliacao,
    acomp_id, acomp_id_obj_aprend, acomp_descricao, 
    campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_campos_exp 
    FROM smc_conceito_aluno
    INNER JOIN smc_acomp_proc_aprend ON acomp_id = conc_acomp_id
    INNER JOIN smc_campos_exp_objetivos ON campos_exp_obj_id = acomp_id_obj_aprend
    WHERE conc_matricula_id = :matricula_id 
    AND conc_periodo = :periodo
    AND acomp_id_obj_aprend = :obj_aprend";
$stmt_Conceito = $SmecelNovo->prepare($query_Conceito);
$stmt_Conceito->bindParam(':matricula_id', $row_Matricula['vinculo_aluno_id'], PDO::PARAM_INT);
$stmt_Conceito->bindParam(':periodo', $colname_Periodo, PDO::PARAM_STR);
$stmt_Conceito->bindParam(':obj_aprend', $row_Objetivos['campos_exp_obj_id'], PDO::PARAM_INT);
$stmt_Conceito->execute();
$totalRows_Conceito = $stmt_Conceito->rowCount();

if ($totalRows_Conceito == 0) {
    //header("Location:index.php?erro");
}
?>

  <table class="ls-table ls-table-striped">
  <tbody>
  <?php while ($row_Conceito = $stmt_Conceito->fetch(PDO::FETCH_ASSOC)) { ?>
    <tr>
      <td width="100%">
	  <p><strong><?php echo $row_Conceito['acomp_descricao']; ?></strong></p>
	  
      <?php foreach ($conceito as &$valor) { ?>
      <?php $valor1 = explode("|", $valor); ?>
		
    <p>
    <small>
    <label class="ls-label-text">
      <input class="" 
             periodo="<?php echo $colname_Periodo; ?>" 
             matricula="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" 
             name="<?php echo $row_Conceito['conc_acomp_id']; ?>" 
             value="<?php echo $valor1[0]; ?>" 
             type="radio" 
             <?php if ($row_Conceito['conc_avaliacao'] == $valor1[0]) { echo "checked"; } ?> />
      <span><?php echo $valor1[2]; ?> (<?php echo $valor1[1]; ?>)</span>
    </label>
    </small>
    </p>		
		
<?php } ?>
      </td>
    </tr>
<?php } ?>
    </tbody>
</table>
<br>
    
<?php } // fim while Objetivos ?>

<?php } // fim while Campos ?>
<br>

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
        <a class="ls-btn-danger" href="rendimento_alunos.php?escola=<?php echo $row_Turma['turma_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $row_Turma['turma_id']; ?>">Cancelar</a> 
      </div>
      <input type="hidden" name="vinculo_aluno_boletim" value="1">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="matriz" value="<?php echo $row_AlterarStatus['matriz_id']; ?>">
      <input type="hidden" name="aluno" value="<?php echo $row_AlterarStatus['vinculo_aluno_id']; ?>">
      <input type="hidden" name="detalhes" value="<?php echo $row_AlterarStatus['aluno_nome']; ?> - <?php echo $row_AlterarStatus['turma_nome']; ?>">
    </form>
    </div>
    <p> </p>

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