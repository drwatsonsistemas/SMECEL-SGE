<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
  $dataCad = date('Y-m-d H:i:s');
  $hash = md5(uniqid(""));
  
  $insertSQL = sprintf("INSERT INTO smc_plano_aula (plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo, plano_aula_hash) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['plano_aula_id_habilidade'], "int"),
                       GetSQLValueString($_POST['plano_aula_id_turma'], "int"),
                       GetSQLValueString($_POST['plano_aula_id_disciplina'], "int"),
                       GetSQLValueString($_POST['plano_aula_id_professor'], "int"),
                       GetSQLValueString($_POST['plano_aula_data'], "date"),
                       GetSQLValueString($dataCad, "date"),
                       GetSQLValueString($_POST['plano_aula_texto'], "text"),
                       GetSQLValueString($_POST['plano_aula_conteudo'], "text"),
                       GetSQLValueString($hash, "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "aulas.php?aula_duplicada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php


$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = anti_injection($_GET['escola']);
} else {
	//header("Location:chamada.php");
	}

$colname_Target = "-1";
if (isset($_GET['target'])) {
  $colname_Target = anti_injection($_GET['target']);
} else {
	//header("Location:chamada.php");
	}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
} else {
	//header("Location:chamada.php");
	}
	

if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
  $semana = date("w", strtotime($data));
  $diasemana = array('DOMINGO', 'SEGUNDA', 'TERÇA', 'QUARTA', 'QUINTA', 'SEXTA', 'SÁBADO');
  $dia_semana_nome = $diasemana[$semana];
} else {
	$data = date("Y-m-d");
	//header("Location:chamada.php");
}


$colname_aula = "-1";
if (isset($_GET['aula'])) {
  $colname_aula = $_GET['aula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_aula = sprintf("
SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_data_cadastro, plano_aula_texto, plano_aula_conteudo,
plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite, 
plano_aula_video, plano_aula_meet, plano_aula_sicrona_hora, plano_aula_sicrona_minuto, plano_aula_google_form, 
plano_aula_google_form_tempo, plano_aula_publicado, plano_aula_hash,
turma_id, turma_nome, turma_etapa 
FROM smc_plano_aula 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE plano_aula_hash = %s", GetSQLValueString($colname_aula, "text"));
$aula = mysql_query($query_aula, $SmecelNovo) or die(mysql_error());
$row_aula = mysql_fetch_assoc($aula);
$totalRows_aula = mysql_num_rows($aula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turmas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
turma_parecer, turma_multisseriada,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = ".ID_PROFESSOR." AND turma_ano_letivo = ".ANO_LETIVO." AND turma_etapa = '$row_aula[turma_etapa]'
GROUP BY ch_lotacao_turma_id
ORDER BY turma_turno, turma_nome ASC
";
$turmas = mysql_query($query_turmas, $SmecelNovo) or die(mysql_error());
$row_turmas = mysql_fetch_assoc($turmas);
$totalRows_turmas = mysql_num_rows($turmas);
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
<title>PROFESSOR |<?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
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
    <h1 class="ls-title-intro ls-ico-home">DUPLICAR AULA <?php echo $row_aula['plano_aula_id']; ?></h1>
    <p><a href="aulas.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=aulas&data=<?php echo $data; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    
    <div class="ls-box-filter">
    <h5 class="ls-title-5"><?php echo $row_aula['plano_aula_texto']; ?></h5>
	<p><strong><?php echo date("d/m/Y", strtotime($row_aula['plano_aula_data'])); ?></strong> - <?php echo $row_aula['turma_nome']; ?></strong></p>
    </div>
    

    <p>&nbsp;</p>
  </div>
  <?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>

<div class="ls-modal" id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">DUPLICAR AULA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    <div class="ls-box-filter">
    <h5 class="ls-title-5"><?php echo $row_aula['plano_aula_texto']; ?></h5>
	<p><strong><?php echo date("d/m/Y", strtotime($row_aula['plano_aula_data'])); ?></strong> - <?php echo $row_aula['turma_nome']; ?></strong></p>
    </div>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-inline row">
      <fieldset>
        <label class="ls-label col-md-12">
        <b class="ls-label-text">Duplicar aula na turma: </b>
        <div class="ls-custom-select">
          <select class="ls-select" name="plano_aula_id_turma">
            <?php do { ?>
            <option value="<?php echo $row_turmas['turma_id']?>" ><?php echo $row_turmas['turma_nome']?>, <?php echo $row_turmas['turma_turno_nome']?></option>
            <?php } while ($row_turmas = mysql_fetch_assoc($turmas)); ?>
          </select>
        </div>
        </label>
      </fieldset>
      <input type="hidden" name="plano_aula_id_habilidade" value="<?php echo $row_aula['plano_aula_id_habilidade']; ?>">
      <input type="hidden" name="plano_aula_id_disciplina" value="<?php echo $row_aula['plano_aula_id_disciplina']; ?>">
      <input type="hidden" name="plano_aula_id_professor" value="<?php echo $row_aula['plano_aula_id_professor']; ?>">
      <input type="hidden" name="plano_aula_data" value="<?php echo $row_aula['plano_aula_data']; ?>">
      <input type="hidden" name="plano_aula_data_cadastro" value="<?php echo date("Y-m-d H:i:s"); ?>">
      <input type="hidden" name="plano_aula_texto" value="<?php echo $row_aula['plano_aula_texto']; ?>">
      <input type="hidden" name="plano_aula_conteudo" value="<?php echo $row_aula['plano_aula_conteudo']; ?>">
      <input type="hidden" name="plano_aula_hash" value="<?php echo $row_aula['plano_aula_hash']; ?>" size="32">
      <input type="hidden" name="MM_insert" value="form1">
    </div>
    <div class="ls-modal-footer">
        <input type="submit" value="DUPLICAR" class="ls-btn">
        <a href="aulas.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=aulas&data=<?php echo $data; ?>" class="ls-btn-danger">CANCELAR</a>
    </div></form>
  </div>
</div><!-- /.modal -->


<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 
<script type="application/javascript">

locastyle.modal.open("#myAwesomeModal");


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
mysql_free_result($vinculos);

mysql_free_result($aula);

mysql_free_result($turmas);
?>
