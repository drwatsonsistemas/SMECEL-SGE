<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>

<?php include "fnc/session.php"; ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


include "usuLogado.php";
include "fnc/anoLetivo.php";

$colname_Matricula = "-1";
if (isset($_GET['c'])) {
  $colname_Matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
  SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
  vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
  vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
  vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome  
  FROM smc_vinculo_aluno
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);


$colname_Faltas = "-1";
if (isset($_GET['falta'])) {
  $colname_Faltas = $_GET['falta'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = sprintf("
  SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, 
  faltas_alunos_justificada, faltas_alunos_justificativa, disciplina_id, disciplina_nome 
  FROM smc_faltas_alunos 
  INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
  WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]' AND faltas_alunos_id = %s", GetSQLValueString($colname_Faltas, "int"));
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  //checkbox ativo
  if($_POST['faltas'] == true){
    
    $updateSQL = sprintf("UPDATE smc_faltas_alunos SET faltas_alunos_justificada=%s, faltas_alunos_justificativa=%s WHERE faltas_alunos_data=%s AND faltas_alunos_matricula_id=%s",
     GetSQLValueString($_POST['faltas_alunos_justificada'], "text"),
     GetSQLValueString($_POST['faltas_alunos_justificativa'], "text"),
     GetSQLValueString($_POST['faltas_alunos_data'], "date"),
     GetSQLValueString($row_Faltas['faltas_alunos_matricula_id'], "int"));
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  }else{

    $updateSQL = sprintf("UPDATE smc_faltas_alunos SET faltas_alunos_justificada=%s, faltas_alunos_justificativa=%s WHERE faltas_alunos_id=%s",
     GetSQLValueString($_POST['faltas_alunos_justificada'], "text"),
     GetSQLValueString($_POST['faltas_alunos_justificativa'], "text"),
     GetSQLValueString($_POST['faltas_alunos_id'], "int"));
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
  }

  $updateGoTo = "faltasMostrar.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">JUSTIFICAR FALTA</h1>
      <!-- CONTEÚDO -->



      <div class="ls-modal" data-modal-blocked id="cadFaltas">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">Justificar falta</h4>
          </div>
          <div class="ls-modal-body" id="myModalBody">

            <p>

              <div class="ls-alert-info">
                Registrar a justificativa do aluno <strong><?php echo $row_Matricula['aluno_nome']; ?></strong> para a falta da <strong><?php echo $row_Faltas['faltas_alunos_numero_aula']; ?>ª aula</strong> no dia <strong><?php echo inverteData($row_Faltas['faltas_alunos_data']); ?></strong>, na disciplina de <strong><?php echo $row_Faltas['disciplina_nome']; ?></strong>
              </div>

            </p>


            <p>




              <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">

               <label class="ls-label col-md-12">
                <b class="ls-label-text">Texto da justificativa</b>
                <input type="text" name="faltas_alunos_justificativa" value="<?php echo htmlentities($row_Faltas['faltas_alunos_justificativa'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </label>
              <label class="ls-label col-md-12">
                <input type="checkbox" name="faltas" checked>
                Justificar todas as faltas do dia <?php echo inverteData($row_Faltas['faltas_alunos_data']) ?>
              </label>




              <input type="hidden" name="faltas_alunos_id" value="<?php echo $row_Faltas['faltas_alunos_id']; ?>">
              <input type="hidden" name="faltas_alunos_data" value="<?php echo $row_Faltas['faltas_alunos_data']; ?>">
              <input type="hidden" name="faltas_alunos_justificada" value="S">
              <input type="hidden" name="MM_update" value="form1">




            </p>
          </div>
          <div class="ls-modal-footer">
            <button type="submit" class="ls-btn-primary">JUSTIFICAR</button>
            <a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_Faltas['faltas_alunos_id']; ?>','<?php echo $row_Matricula['vinculo_aluno_hash']; ?>')" class="ls-btn-primary-danger ls-ico-remove ls-float-right"></a>&nbsp;
            <a href="faltasMostrar.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn" >CANCELAR</a> &nbsp;
          </div>
        </form>
      </div>
    </div><!-- /.modal -->




    




    <!-- CONTEÚDO -->
  </div>
</main>

<aside class="ls-notification">
  <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
    <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
  </nav>

  <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
    <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
  </nav>

  <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
    <h3 class="ls-title-2">Ajuda</h3>
    <ul>
      <li class="ls-txt-center hidden-xs">
        <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
      </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>

<!-- We recommended use jQuery 1.10 or up -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>

<script language="Javascript">
	function confirmaExclusao(falta,codigo) {
   var resposta = confirm("Deseja realmente excluir este registro de falta?");
   if (resposta == true) {
     window.location.href = "faltaAlunoExcluir.php?falta="+falta+"&c="+codigo;
   }
 }
</script>

<script>
	locastyle.modal.open("#cadFaltas");
</script>


</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Faltas);

mysql_free_result($Matricula);

mysql_free_result($EscolaLogada);
?>
