<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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



include "usuLogado.php";

$colname_Matricula = "-1";
if (isset($_GET['c'])) {
  $colname_Matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
  SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
  vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
  vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho,vinculo_aluno_conselho_reprovado,  
  vinculo_aluno_conselho_parecer, aluno_id, aluno_nome, turma_id, turma_nome, turma_matriz_id, ca_id, ca_forma_avaliacao, matriz_id, matriz_criterio_avaliativo
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  LEFT JOIN smc_matriz ON matriz_id = turma_matriz_id  
  LEFT JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);
include "fnc/anoLetivo.php";

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$linkVoltar = "boletimLancar.php?c=$colname_Matricula";
if($row_Matricula['ca_forma_avaliacao'] == 'Q'){
  $linkVoltar = "boletimVerQQ.php?c=$colname_Matricula";
}

/*
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  // Atualiza os dados de conselho no banco de dados
  $updateSQL = sprintf(
    "UPDATE smc_vinculo_aluno SET vinculo_aluno_conselho=%s, vinculo_aluno_conselho_reprovado=%s, vinculo_aluno_conselho_parecer=%s WHERE vinculo_aluno_id=%s",
    GetSQLValueString(isset($_POST['vinculo_aluno_conselho']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString(isset($_POST['vinculo_aluno_conselho_reprovado']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString($_POST['vinculo_aluno_conselho_parecer'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  // Ajusta as notas de avaliação final para a nota mínima
  if (isset($_POST['vinculo_aluno_conselho']) && $_POST['vinculo_aluno_conselho'] === 'S') {
    // Obtem o ID do aluno
    $alunoId = $_POST['vinculo_aluno_id'];

    // Obtem a nota mínima do critério avaliativo da matriz
    $queryNotaMinima = sprintf(
      "SELECT ca_nota_min_recuperacao_final, ca_digitos
       FROM smc_criterios_avaliativos
       WHERE ca_id = (
         SELECT matriz_criterio_avaliativo
         FROM smc_matriz
         WHERE matriz_id = (
           SELECT turma_matriz_id
           FROM smc_turma
           WHERE turma_id = (
             SELECT vinculo_aluno_id_turma
             FROM smc_vinculo_aluno
             WHERE vinculo_aluno_id = %s
           )
         )
       )",
      GetSQLValueString($alunoId, "int")
    );

    $notaMinimaResult = mysql_query($queryNotaMinima, $SmecelNovo) or die(mysql_error());
    $notaMinimaRow = mysql_fetch_assoc($notaMinimaResult);

    $notaMinima = $notaMinimaRow['ca_nota_min_recuperacao_final'];
    $digitos = $notaMinimaRow['ca_digitos'];

    // Busca todas as disciplinas do aluno
    $queryDisciplinas = sprintf(
      "SELECT matriz_disciplina_id_disciplina
       FROM smc_matriz_disciplinas
       WHERE matriz_disciplina_id_matriz = (
         SELECT turma_matriz_id
         FROM smc_turma
         WHERE turma_id = (
           SELECT vinculo_aluno_id_turma
           FROM smc_vinculo_aluno
           WHERE vinculo_aluno_id = %s
         )
       )",
      GetSQLValueString($alunoId, "int")
    );

    $disciplinasResult = mysql_query($queryDisciplinas, $SmecelNovo) or die(mysql_error());
    while ($disciplinaRow = mysql_fetch_assoc($disciplinasResult)) {
      $disciplinaId = $disciplinaRow['matriz_disciplina_id_disciplina'];

      // Verifica se já existe uma nota de avaliação final para a disciplina
      $queryNotaFinal = sprintf(
        "SELECT nota_id, nota_valor
         FROM smc_nota
         WHERE nota_id_matricula = %s
           AND nota_id_disciplina = %s
           AND nota_periodo = 99
           AND nota_num_avaliacao = 99",
        GetSQLValueString($alunoId, "int"),
        GetSQLValueString($disciplinaId, "int")
      );

      $notaFinalResult = mysql_query($queryNotaFinal, $SmecelNovo) or die(mysql_error());
      $notaFinalRow = mysql_fetch_assoc($notaFinalResult);

      if ($notaFinalRow) {
        // Se a nota de avaliação final já existir, atualiza para a nota mínima
        $notaId = $notaFinalRow['nota_id'];
        $updateNota = sprintf(
          "UPDATE smc_nota
           SET nota_valor = %s
           WHERE nota_id = %s",
          GetSQLValueString($notaMinima, "double"),
          GetSQLValueString($notaId, "int")
        );

        mysql_query($updateNota, $SmecelNovo) or die(mysql_error());
      } else {
        // Se não existir, insere uma nova nota de avaliação final com a nota mínima
        $insertNota = sprintf(
          "INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_valor)
           VALUES (%s, %s, 99, 99, %s)",
          GetSQLValueString($alunoId, "int"),
          GetSQLValueString($disciplinaId, "int"),
          GetSQLValueString($notaMinima, "double")
        );

        mysql_query($insertNota, $SmecelNovo) or die(mysql_error());
      }
    }
  }

  // Redireciona após salvar
  // $updateGoTo = "matriculaExibe.php?cmatricula=$colname_Matricula&ano=$row_AnoLetivo[ano_letivo_ano]&aprovadoConselho";
  //header(sprintf("Location: %s", $updateGoTo));
}*/

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">RESULTADO DO CONSELHO</h1>
      <!-- CONTEÚDO -->

      <div class="ls-box">
        <strong>Aluno: </strong><?php echo $row_Matricula['aluno_nome']; ?><br>
        <strong>Turma: </strong><?php echo $row_Matricula['turma_nome']; ?>

      </div>

      <div class="ls-box">
        <div class="ls-alert-info">
          <b>Atenção:</b> Se o aluno foi aprovado diretamente, verifique se a opção "Reprovado pelo conselho" ou
          "Aprovado pelo conselho" está marcada. Se estiver, desmarque-a e clique em "Salvar".
        </div>
      </div>





      <form method="post" id="formConselho" class="ls-form row">


        <div class="ls-label col-md-12">
          <label class="ls-label-text">
            <input type="checkbox" name="vinculo_aluno_conselho" value="S" <?php if (!(strcmp(htmlentities($row_Matricula['vinculo_aluno_conselho'], ENT_COMPAT, 'utf-8'), "S"))) {
              echo "checked=\"checked\"";
            } ?>>
            Aprovado pelo Conselho de Classe
          </label>
        </div>

        <div class="ls-label col-md-12">
          <label class="ls-label-text">
            <input type="checkbox" name="vinculo_aluno_conselho_reprovado" value="S" <?php if (!(strcmp(htmlentities($row_Matricula['vinculo_aluno_conselho_reprovado'], ENT_COMPAT, 'utf-8'), "S"))) {
              echo "checked=\"checked\"";
            } ?>>
            Conservado pelo Conselho de Classe
          </label>
        </div>

        <div class="ls-label col-md-12">
          <label class="ls-label">
            <b class="ls-label-text">Observação</b>
            <textarea class="ls-textarea-autoresize" name="vinculo_aluno_conselho_parecer" cols="50"
              rows="5"><?php echo htmlentities($row_Matricula['vinculo_aluno_conselho_parecer'], ENT_COMPAT, 'utf-8'); ?></textarea>
          </label>
        </div>

        <div class="ls-actions-btn">
          <input id="btn-conselho" type="submit" class="ls-btn-primary" value="SALVAR">
          <a href="<?= $linkVoltar ?>" class="ls-btn">Voltar</a>
        </div>

        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="vinculo_aluno_id" value="<?php echo $row_Matricula['vinculo_aluno_id']; ?>">
      </form>
      <div id="resultado"></div>
      <p>&nbsp;</p>
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
  <script>
    $(document).ready(function () {
      $('#formConselho').submit(function (e) {
        e.preventDefault(); // Evita o envio padrão do formulário

        // Coleta os dados do formulário
        var formData = $(this).serialize();

        // Faz a requisição AJAX
        $.ajax({
          url: 'processa_conselho.php',
          type: 'POST',
          data: formData,
          dataType: 'json', // Espera a resposta no formato JSON
          success: function (response) {
            console.log(response)
            if (response.status === 'success') {
              $('#resultado').html(
                '<div class="ls-alert-success">Operação realizada com sucesso!</div>'
              );
            } else {
              $('#resultado').html(
                `<div class="ls-alert-danger">Erro: ${response.message}</div>`
              );
            }
          },
          error: function (xhr, status, error) {
            $('#resultado').html(
              `<div class="ls-alert-danger">Erro ao processar a solicitação: ${xhr.responseText || error}</div>`
            );
          },
        });
      });
    });
  </script>
  <script>
    $(document).ready(function () {
      $('input[name="vinculo_aluno_conselho"]').click(function () {
        $('input[name="vinculo_aluno_conselho_reprovado"]').prop('checked', false);
      });

      $('input[name="vinculo_aluno_conselho_reprovado"]').click(function () {
        $('input[name="vinculo_aluno_conselho"]').prop('checked', false);
      });
    });
  </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Matricula);

mysql_free_result($EscolaLogada);
?>