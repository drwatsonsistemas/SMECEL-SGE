<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = anti_injection($_GET['disciplina']);
}

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = anti_injection($_GET['cod']);
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Parecer = "
SELECT p_ind_id, p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_data_cadastro, p_ind_periodo 
FROM smc_parecer_individual_professor
WHERE p_ind_id_prof = '$row_ProfLogado[func_id]' AND p_ind_mat_aluno = '$row_Matricula[vinculo_aluno_id]'
ORDER BY p_ind_periodo ASC
";
$Parecer = mysql_query($query_Parecer, $SmecelNovo) or die(mysql_error());
$row_Parecer = mysql_fetch_assoc($Parecer);
$totalRows_Parecer = mysql_num_rows($Parecer);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


  $insertSQL = sprintf("INSERT INTO smc_parecer_individual_professor (p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_periodo) VALUES ('$row_ProfLogado[func_id]', '$row_Matricula[vinculo_aluno_id]', %s, %s)",
                       //GetSQLValueString($_POST['p_ind_id_prof'], "int"),
                       //GetSQLValueString($_POST['p_ind_mat_aluno'], "int"),
                       GetSQLValueString($_POST['p_ind_texto'], "text"),
                       GetSQLValueString($_POST['p_ind_periodo'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "parecer_individual.php?cadastrado";
  
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_GET['parecer'])) && ($_GET['parecer'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_parecer_individual_professor WHERE p_ind_id=%s AND p_ind_id_prof = '$row_ProfLogado[func_id]' AND p_ind_mat_aluno = '$row_Matricula[vinculo_aluno_id]'",
                       GetSQLValueString($_GET['parecer'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "parecer_individual.php?cod=$colname_Matricula&disciplina=$colname_Disciplina&turma=$colname_Turma&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
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
    <p><a href="rendimento_alunos.php?escola=<?php echo $row_Matricula['vinculo_aluno_id_escola']; ?>&etapa=<?php echo $row_Turma['turma_etapa']; ?>&componente=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

    
	<blockquote> 
    <span style="margin-right:10px; text-align:center; float:left;">
    <?php if ($row_Matricula['aluno_foto']=="") { ?>
    <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
    <?php } else { ?>
    <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0" width="50">
    <?php } ?>
    <?php //echo $row_Alunos['aluno_nome']; ?>
    </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
    Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong><br>
    <p>&nbsp;</p> 
    </blockquote>
    <hr>
        
    <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">REGISTRAR PARECER</button>
    
    <hr>
    
    <?php if ($totalRows_Parecer > 0) { // Show if recordset not empty ?>



      <?php do { ?>
      

      <div class="ls-box" id="parecer_<?php echo $row_Parecer['p_ind_id']; ?>">
        <a href="parecer_individual.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&parecer=<?php echo $row_Parecer['p_ind_id']; ?>" class="ls-ico-remove ls-float-right ls-color-danger" parecer="<?php echo $row_Parecer['p_ind_id']; ?>" aluno="<?php echo $row_Matricula['vinculo_aluno_id']; ?>" professor="<?php echo $row_ProfLogado['func_id']; ?>"></a>
        <strong><?php echo $row_Parecer['p_ind_periodo']; ?>º PERÍODO </strong>
		<p><?php echo $row_Parecer['p_ind_texto']; ?></p>
        </div>
        
        
        <?php } while ($row_Parecer = mysql_fetch_assoc($Parecer)); ?>

  <?php } else { ?>
  <hr>
  Nenhum parecer cadastrado.
  
  <?php } // Show if recordset not empty ?>
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">REGISTRAR PARECER</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
      
      
      
       <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">
      
        
            <label class="ls-label">
            <b class="ls-label-text">PARECER</b>
            <textarea id="rel_avaliativo" class="materialize-textarea" name="p_ind_texto" cols="50" rows="3" required></textarea>
            </label>      
        
            <div class="ls-custom-select">
            <select name="p_ind_periodo" class="ls-select">
            <?php for ($i=1; $i < $row_Criterios['ca_qtd_periodos']+1; $i++ ) { ?>
              <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, ""))) {echo "SELECTED";} ?>><?php echo $i; ?>º PERÍODO/UNIDADE</option>
            <?php } ?>  
            </select>
            </div>
            
          <input type="hidden" name="MM_insert" value="form1">
      
      </p>
    </div>
    <div class="ls-modal-footer">
      <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
      <input type="submit" value="SALVAR" class="ls-btn-primary">
    </div></form>
    
  </div>
</div><!-- /.modal -->

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
</body>
</html>