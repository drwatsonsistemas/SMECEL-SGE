<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php
$colname_EditarAula = "-1";
if (isset($_GET['aula'])) {
  $colname_EditarAula = anti_injection($_GET['aula']);
}
$escola = "-1";
if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
}
$turma = "-1";
if (isset($_GET['turma'])) {
  $turma = anti_injection($_GET['turma']);
}
$target = "-1";
if (isset($_GET['target'])) {
  $target = anti_injection($_GET['target']);
}
$data = "-1";
if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
}




mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EditarAula = sprintf("
SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto,
plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_etapa, turma_turno, turma_id_escola, etapa_id, etapa_id_filtro, etapa_nome, etapa_ano_ef,
escola_id, escola_nome, disciplina_id, disciplina_nome, disciplina_cor_fundo,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno 
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_hash = %s", GetSQLValueString($colname_EditarAula, "text"));
$EditarAula = mysql_query($query_EditarAula, $SmecelNovo) or die(mysql_error());
$row_EditarAula = mysql_fetch_assoc($EditarAula);
$totalRows_EditarAula = mysql_num_rows($EditarAula);

$disciplina = $row_EditarAula['plano_aula_id_disciplina']; 

$etapa_ano = $row_EditarAula['etapa_ano_ef'];
//$etapa_ano = "4,5";

$consulta = " AND bncc_ef_ano IN ('$etapa_ano')";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Habilidades = "
SELECT bncc_ef_id, bncc_ef_area_conhec_id, bncc_ef_comp_id, bncc_ef_componente, bncc_ef_ano, 
bncc_ef_campos_atuacao, bncc_ef_eixo, bncc_ef_un_tematicas, bncc_ef_prat_ling, bncc_ef_obj_conhec, 
bncc_ef_habilidades, bncc_ef_comentarios, bncc_ef_poss_curr 
FROM smc_bncc_ef
WHERE bncc_ef_comp_id = '$disciplina' $consulta";
$Habilidades = mysql_query($query_Habilidades, $SmecelNovo) or die(mysql_error());
$row_Habilidades = mysql_fetch_assoc($Habilidades);
$totalRows_Habilidades = mysql_num_rows($Habilidades);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE smc_plano_aula SET plano_aula_id_habilidade=%s, plano_aula_data=%s, plano_aula_texto=%s, plano_aula_hash=%s WHERE plano_aula_id=%s",
                       GetSQLValueString($_POST['plano_aula_id_habilidade'], "int"),
					   GetSQLValueString($_POST['plano_aula_data'], "date"),
                       GetSQLValueString($_POST['plano_aula_texto'], "text"),
                       GetSQLValueString($_POST['plano_aula_hash'], "text"),
                       GetSQLValueString($_POST['plano_aula_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "aulas.php?escola=$escola&turma=$turma&target=aulas&data=$data&salvo";
  //$updateGoTo = "aulas.php?salvo";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
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
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">EDITANDO AULA <?php echo $row_EditarAula['plano_aula_id']; ?></h1>
    <p><a href="aulas.php?escola=<?php echo $escola; ?>&turma=<?php echo $turma; ?>&data=<?php echo $data; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
    <div class="ls-box-filter">
      <h5 class="ls-title-5"><?php echo $row_EditarAula['escola_nome']; ?></h5>
      <p> <strong><?php echo $row_EditarAula['turma_nome']; ?>, <?php echo $row_EditarAula['turma_turno']; ?> - <span class="ls-" style="color:<?php echo $row_EditarAula['disciplina_cor_fundo']; ?>"><?php echo $row_EditarAula['disciplina_nome']; ?></span> </strong> </p>
    </div>
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      
      
      <label class="ls-label col-md-3 col-xs-12"> <b class="ls-label-text">DATA</b>
        <input type="date" name="plano_aula_data" value="<?php echo htmlentities($row_EditarAula['plano_aula_data'], ENT_COMPAT, 'utf-8'); ?>"  required autocomplete="off">
      </label>
      
      <label class="ls-label col-md-9 col-xs-12"> <b class="ls-label-text">ASSUNTO</b>
        <input type="text" name="plano_aula_texto" value="<?php echo htmlentities($row_EditarAula['plano_aula_texto'], ENT_COMPAT, 'utf-8'); ?>" required autocomplete="off">
      </label>

      <label class="ls-label col-md-12">
      
      <table class="ls-table ls-sm-space ls-table-striped ls-no-hover ls-table-bordered">
      <thead>
        <tr>
          <th>HABILIDADE</th>
          <th width="40"></th>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td>
            <label class="ls-label-text">
             <input class="ls-float-left" type="radio" name="plano_aula_id_habilidade" value="<?php echo $row_Habilidades['bncc_ef_id']; ?>" <?php if (!(strcmp(htmlentities($row_EditarAula['plano_aula_id_habilidade'], ENT_COMPAT, 'utf-8'),$row_Habilidades['bncc_ef_id']))) {echo "checked=\"checked\"";} ?>>
             <p>&nbsp;<?php echo $row_Habilidades['bncc_ef_habilidades']; ?></p>
             </label>
            </td>

            <td>
            <a href="#" class="ls-ico-help ls-float-right"  data-ls-module="popover" data-placement="left" data-content="<strong><?php echo $row_Habilidades['bncc_ef_obj_conhec']; ?></strong><p>Componente: <?php echo $row_Habilidades['bncc_ef_componente']; ?>;<br> Campo de atuação: <?php echo $row_Habilidades['bncc_ef_campos_atuacao']; ?>; <br>Eixo: <?php echo $row_Habilidades['bncc_ef_eixo']; ?>; <br>Unidades Temáticas: <?php echo $row_Habilidades['bncc_ef_un_tematicas']; ?>; <br>Práticas de Linguagem: <?php echo $row_Habilidades['bncc_ef_prat_ling']; ?>; <br>Anos: <?php echo $row_Habilidades['bncc_ef_ano']; ?></p>" data-title="<?php echo $row_Habilidades['bncc_ef_habilidades']; ?>"></a>
            </td>
          </tr>
          <?php } while ($row_Habilidades = mysql_fetch_assoc($Habilidades)); ?>
        <tr>
          <td><label class="ls-label-text">
              <input type="radio" name="plano_aula_id_habilidade" value="0" <?php if (!(strcmp(htmlentities($row_EditarAula['plano_aula_id_habilidade'], ENT_COMPAT, 'utf-8'),0))) {echo "checked=\"checked\"";} ?>>
            Nenhuma opção
            </label>
          </td>
          <td></td>
        </tr>
      </tbody>
      </table>
      
      </label>
      </fieldset>
      <input type="hidden" name="plano_aula_id" value="<?php echo $row_EditarAula['plano_aula_id']; ?>">
      <input type="hidden" name="plano_aula_hash" value="<?php echo htmlentities($row_EditarAula['plano_aula_hash'], ENT_COMPAT, 'utf-8'); ?>">
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="plano_aula_id" value="<?php echo $row_EditarAula['plano_aula_id']; ?>">
      
      <div class="ls-actions-btn">
        <input class="ls-btn-primary" type="submit" value="SALVAR">
        <a href="aulas.php?escola=<?php echo $escola; ?>&turma=<?php echo $turma; ?>&data=<?php echo $data; ?>" class="ls-btn-danger">CANCELAR</a> 
      </div>
      
    </form>
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
mysql_free_result($Habilidades);

mysql_free_result($EditarAula);
?>
