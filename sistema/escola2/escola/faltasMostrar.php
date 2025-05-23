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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Matricula = "-1";
if (isset($_GET['c'])) {
  $colname_Matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada FROM smc_vinculo_aluno WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasContar = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa, COUNT(faltas_alunos_data) AS total 
FROM smc_faltas_alunos
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]'
GROUP BY faltas_alunos_data
ORDER BY faltas_alunos_data DESC";
$faltasContar = mysql_query($query_faltasContar, $SmecelNovo) or die(mysql_error());
$row_faltasContar = mysql_fetch_assoc($faltasContar);
$totalRows_faltasContar = mysql_num_rows($faltasContar);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $dataInicial = $_POST['falta_data_inicial'];
  $dataFinal = $_POST['falta_data_final'];

  $dataInicio = new DateTime($dataInicial);
  $dataFim = new DateTime($dataFinal);

  while ($dataInicio <= $dataFim) {
    $dataAtual = $dataInicio->format('Y-m-d');

    $updateSQL = sprintf("UPDATE smc_faltas_alunos SET faltas_alunos_justificada='%s', faltas_alunos_justificativa='%s' WHERE faltas_alunos_data='%s' AND faltas_alunos_matricula_id=%d",
      mysql_real_escape_string($_POST['faltas_alunos_justificada']),
      mysql_real_escape_string($_POST['justificativa']),
      mysql_real_escape_string($dataAtual),
      $row_faltasContar['faltas_alunos_matricula_id']);

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    $dataInicio->modify('+1 day');
  }

  $updateGoTo = "faltasMostrar.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header("Location: " . $updateGoTo);
}

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

      <h1 class="ls-title-intro ls-ico-home">RELATÓRIO DE FALTAS</h1>
      <!-- CONTEÚDO -->

      <a href="matriculaExibe.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn">VOLTAR</a>
      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">JUSTIFICAR FALTAS POR INTERVALO</button>
      
      <hr>

      <?php if ($totalRows_faltasContar > 0) { // Show if recordset not empty ?>







              <?php $numFaltas = 0; ?>
              <?php 
              $faltasJustificadas = 0;
              do { ?>


              <div class="ls-box">
                <h5 class="ls-title-3"><?php echo inverteData($row_faltasContar['faltas_alunos_data']); ?></h5>    


              
                    <?php 
                    mysql_select_db($database_SmecelNovo, $SmecelNovo);
                    $query_mostrarDisciplinas = "
                    SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, 
                    faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa,
                    disciplina_id, disciplina_nome 
                    FROM smc_faltas_alunos
                    INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
                    WHERE faltas_alunos_data = '$row_faltasContar[faltas_alunos_data]' AND faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]'";
                    $mostrarDisciplinas = mysql_query($query_mostrarDisciplinas, $SmecelNovo) or die(mysql_error());
                    $row_mostrarDisciplinas = mysql_fetch_assoc($mostrarDisciplinas);
                    $totalRows_mostrarDisciplinas = mysql_num_rows($mostrarDisciplinas);
                    ?>


                    <table class="ls-table ls-xs-space ls-table-striped">

                    <?php do { ?>

                      <tr>

                      <td><?php echo $row_mostrarDisciplinas['faltas_alunos_numero_aula']; ?> - <?php echo $row_mostrarDisciplinas['disciplina_nome']; ?></td>
                      <td width="80"><?php if ($row_mostrarDisciplinas['faltas_alunos_justificada']=="N") { echo "<span class=\"ls-ico-close ls-color-danger\"></span> "; } else { echo "<span class=\"ls-ico-checkmark ls-color-success\"></span>"; } ?></td>
                      <td><a href="faltaAlunoEditar.php?falta=<?php echo $row_mostrarDisciplinas['faltas_alunos_id']; ?>&c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-btn ls-btn-xs">Editar justificativa</a></td>
                      <td><?php echo $row_mostrarDisciplinas['faltas_alunos_justificativa']; ?></td>



                    </tr>


                      <?php 
                      if ($row_mostrarDisciplinas['faltas_alunos_justificada']=="S") {
                        $faltasJustificadas++;
                      }
                      ?>

                    <?php } while ($row_mostrarDisciplinas = mysql_fetch_assoc($mostrarDisciplinas)); ?>   

                    </table>
                


                  <?php $numFaltas = $numFaltas + $row_faltasContar['total']; ?>
              

                    </div>

              <?php } while ($row_faltasContar = mysql_fetch_assoc($faltasContar)); ?>





        <span class="ls-tag-success">JUSTIFICADAS: <strong><?php echo $faltasJustificadas; ?></strong></span> 
        <span class="ls-tag-warning">SEM JUSTIFICATIVA: <strong><?php echo $numFaltas - $faltasJustificadas; ?></strong></span>
        <span class="ls-tag-danger">TOTAL DE FALTAS: <strong><?php echo $numFaltas; ?></strong></span>
        <span class="ls-tag">TOTAL DE DIAS: <strong><?php echo $totalRows_faltasContar; ?></strong></span>
        
        
        <hr>
      <?php } else { ?>
        <hr>
        <div class="ls-box">Nada para mostrar</div>
      <?php } // Show if recordset not empty ?>
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

  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">JUSTIFICAR FALTAS POR INTERVALO DE DATA</h4>
      </div>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-inline" data-ls-module="form">
        <div class="ls-modal-body" id="myModalBody">
          <div class="ls-alert-info"><strong>Atenção:</strong> Esta funcionalidade justifica TODAS as aulas dentro do intervalo de datas selecionado.</div>

          <label class="ls-label col-md-6 col-sm-12">
            <input type="date" placeholder="INFORME A DATA INICIAL" name="falta_data_inicial" value="<?php echo date("Y-m-d"); ?>" class="ls-field " required>
          </label>

          <label class="ls-label col-md-6 col-sm-12">
            <input type="date" placeholder="INFORME A DATA FINAL" name="falta_data_final" value="<?php echo date("Y-m-d"); ?>" class="ls-field" required>
          </label>

          <label class="ls-label col-md-12 col-sm-12">
            <input type="text" name="justificativa" placeholder="Texto da justificativa" class="ls-field" required>
          </label>

        </div>
        <div class="ls-modal-footer ">
          <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
          <button type="submit" class="ls-btn-primary">JUSTIFICAR</button>
          <input type="hidden" name="faltas_alunos_justificada" value="S">
          <input type="hidden" name="MM_update" value="form1">
        </div>
      </form>
    </div>

  </div><!-- /.modal -->

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Matricula);

mysql_free_result($mostrarDisciplinas);

mysql_free_result($faltasContar);
?>
