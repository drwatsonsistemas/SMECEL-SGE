<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/exibeHorarioSecretaria.php"; ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasListar = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_matriz_id, etapa_id, etapa_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_turma 
LEFT JOIN smc_etapa ON etapa_id = turma_etapa 
WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);

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

      <h1 class="ls-title-intro ls-ico-home">GRADE DE HORÁRIOS</h1>
      <!-- CONTEÚDO -->

      <?php if (isset($_GET["nada"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          <span class="ls-ico-cancel-circle ls-ico-left"></span>
          Ocorreu um erro na ação anterior. Um e-mail foi enviado ao administrador do sistema.
        </div>
      <?php } ?>

      <?php if (isset($_GET["permissao"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          <span class="ls-ico-cancel-circle ls-ico-left"></span>
          VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
        </div>
      <?php } ?>


      <?php if ($totalRows_TurmasListar > 0) { // Show if recordset not empty ?>

        <?php do { ?>

          <a href="horariosEditar.php?c=<?php echo $row_TurmasListar['turma_id']; ?>"
            class="ls-ico-pencil2 ls-float-right ls-ico-right">Editar horário</a>
          <h3><?php echo $row_TurmasListar['turma_nome']; ?> - <?php echo $row_TurmasListar['turma_turno_nome']; ?></h3>



          <table class="ls-table ls-no-hover ls-table-striped ls-table-bordered ls-bg-header">
            <thead>
              <tr>
                <th class="ls-txt-center" width="40px"></th>
                <th class="ls-txt-center"><span class="ls-display-none-xs">SEGUNDA</span><span
                    class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEG</span></th>
                <th class="ls-txt-center"><span class="ls-display-none-xs">TERÇA</span><span
                    class="ls-display-none-sm ls-display-none-md ls-display-none-lg">TER</span></th>
                <th class="ls-txt-center"><span class="ls-display-none-xs">QUARTA</span><span
                    class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUA</span></th>
                <th class="ls-txt-center"><span class="ls-display-none-xs">QUINTA</span><span
                    class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUI</span></th>
                <th class="ls-txt-center"><span class="ls-display-none-xs">SEXTA</span><span
                    class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEX</span></th>
              </tr>
            </thead>
            <tbody>

              <?php

              if (!empty($row_TurmasListar['turma_matriz_id'])) {
                // Monta a query com segurança, convertendo para inteiro
                $matriz_id = (int) $row_TurmasListar['turma_matriz_id'];

                $query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, 
                          matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, 
                          matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo
                   FROM smc_matriz 
                   WHERE matriz_id = $matriz_id";

                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
                $row_matriz = mysql_fetch_assoc($matriz);
                $totalRows_matriz = mysql_num_rows($matriz);

                // Só faz o loop se realmente existir o campo 'matriz_aula_dia'
                if (!empty($row_matriz['matriz_aula_dia'])) {
                  for ($a = 1; $a <= $row_matriz['matriz_aula_dia']; $a++) {
                    ?>
                    <tr>
                      <td class="ls-txt-center"><?php echo $a; ?></td>
                      <td class="ls-txt-center"><?php echo exibeHorario($row_TurmasListar['turma_id'], 1, $a); ?></td>
                      <td class="ls-txt-center"><?php echo exibeHorario($row_TurmasListar['turma_id'], 2, $a); ?></td>
                      <td class="ls-txt-center"><?php echo exibeHorario($row_TurmasListar['turma_id'], 3, $a); ?></td>
                      <td class="ls-txt-center"><?php echo exibeHorario($row_TurmasListar['turma_id'], 4, $a); ?></td>
                      <td class="ls-txt-center"><?php echo exibeHorario($row_TurmasListar['turma_id'], 5, $a); ?></td>
                    </tr>
                    <?php
                  }
                } else {
                  echo '<br><div class="ls-alert-warning">Algo inesperado aconteceu. Entre em contato com um administrador do sistema.</div>';
                }

              } else {
                // Caso a turma não tenha uma matriz associada, lide com essa situação:
                // Exemplo: exibir mensagem ao usuário ou simplesmente não mostrar a tabela.
                echo '<br><div class="ls-alert-warning">Nenhuma matriz foi selecionada para esta turma.</div>';
              }

              ?>


            </tbody>
          </table>

          <?php
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_Funcionarios = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_turma_id, func_id, func_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE ch_lotacao_turma_id = $row_TurmasListar[turma_id]
GROUP BY ch_lotacao_professor_id ASC";
          $Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
          $row_Funcionarios = mysql_fetch_assoc($Funcionarios);
          $totalRows_Funcionarios = mysql_num_rows($Funcionarios);
          ?>

          <?php if ($totalRows_Funcionarios > 0) { ?>

            <div class="ls-box ls-xs-space">
              <small>|
                <?php do { ?>
                  <b><?php echo $row_Funcionarios['ch_lotacao_professor_id'] ?></b>-<?php echo $row_Funcionarios['func_nome'] ?>
                  |
                <?php } while ($row_Funcionarios = mysql_fetch_assoc($Funcionarios)); ?>
              </small>
            </div>

          <?php } ?>

        <?php } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar)); ?>

      <?php } ?>

      <p class="ls-txt-center"><a class="ls-btn ls-ico-paint-format" href="print_grade.php" target="_blank">Versão para
          impressão</a></p>

      <p>-</p>




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

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($matriz);

mysql_free_result($EscolaLogada);

mysql_free_result($TurmasListar);

mysql_free_result($Funcionarios);
?>