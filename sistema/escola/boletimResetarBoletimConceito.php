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

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
  SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_multisseriada,
  etapa_id, etapa_nome, etapa_id_filtro 
  FROM smc_turma 
  INNER JOIN smc_etapa ON etapa_id = turma_etapa
  WHERE etapa_id_filtro = '1' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND  turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);


$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
  SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
  vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
  vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, 
  vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, vinculo_aluno_dependencia, vinculo_aluno_reprovado_faltas, aluno_id, aluno_nome,
  turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, 
  etapa_id, etapa_nome, etapa_id_filtro, 
  matriz_id, matriz_nome, matriz_criterio_avaliativo
  FROM smc_vinculo_aluno
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  INNER JOIN smc_etapa ON etapa_id = turma_etapa
  INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
  WHERE etapa_id_filtro = '1' AND vinculo_aluno_boletim = '1' AND vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_id_turma = %s", GetSQLValueString($colname_Alunos, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_Alunos[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_acompanhamento = "
SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
FROM smc_acomp_proc_aprend
WHERE acomp_id_matriz = '$row_Alunos[matriz_id]'
AND acomp_id_crit = '$row_Criterios[ca_id]'
";
$acompanhamento = mysql_query($query_acompanhamento, $SmecelNovo) or die(mysql_error());
$row_acompanhamento = mysql_fetch_assoc($acompanhamento);
$totalRows_acompanhamento = mysql_num_rows($acompanhamento);

$matriz = $row_Alunos['matriz_id'];
$numPeriodos = $row_Criterios['ca_qtd_periodos'];

if ($row_UsuLogado['usu_insert']=="N") {

  header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
  die();
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

      <h1 class="ls-title-intro ls-ico-home">CADASTRAR BOLETIM DA TURMA</h1>
      <!-- CONTEÚDO -->
      <div class="ls-box">TURMA: <?php echo $row_Turma['turma_nome']; ?></div>


      <?php if ($totalRows_Alunos > 0) { ?>

        <table class="ls-table">
          <tr>
            <td width="110">MATRICULA</td>
            <td>ALUNO</td>
            <td></td>
          </tr>
          <?php do { ?>

            <?php 



            $idVinculo = $row_Alunos['vinculo_aluno_id']; 

            $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim=%s WHERE vinculo_aluno_id=%s",
             GetSQLValueString(0, "int"),
             GetSQLValueString($idVinculo, "int"));

            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

            $deleteSQL = sprintf("DELETE FROM smc_conceito_aluno WHERE conc_matricula_id=%s",
             GetSQLValueString($idVinculo, "int"));

            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());


//	exit; 


            ?>

            <tr>
              <td><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
              <td><?php echo $row_Alunos['aluno_nome']; ?></td>
              <td>



                <span class="ls-ico-spinner ls-color-warning"> BOLETIM RESETADO</span>


              </td>
            </tr>
          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        </table>
        
      <?php } else { ?>
        <p>Nenhum boletim gerado.</p>
      <?php } ?>
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
mysql_free_result($Turma);

mysql_free_result($Alunos);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
