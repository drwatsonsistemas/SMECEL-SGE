<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php //include "fnc/alunosConta.php"; ?>
<?php include "fnc/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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
include "fnc/alunosConta.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {

  if ($_GET['ano'] == "") {
    //echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada");
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}


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
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer, turma_matriz_id, turma_tipo_atendimento, 
etapa_id, etapa_nome, etapa_limite_turma, turma_multisseriada, matriz_id, matriz_nome, matriz_ativa,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL' 
WHEN 1 THEN 'MATUTINO' 
WHEN 2 THEN 'VESPERTINO' 
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_descricao 
FROM smc_turma 
LEFT JOIN smc_etapa ON etapa_id = turma_etapa
LEFT JOIN smc_matriz ON matriz_id = turma_matriz_id 
WHERE turma_tipo_atendimento = '2' AND turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$anoLetivo' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$TurmasListar = mysql_query($query_TurmasListar, $SmecelNovo) or die(mysql_error());
$row_TurmasListar = mysql_fetch_assoc($TurmasListar);
$totalRows_TurmasListar = mysql_num_rows($TurmasListar);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContaAlunos = "SELECT turma_id, turma_id_escola, sum(turma_total_alunos) as totalAlunos, turma_ano_letivo, turma_tipo_atendimento FROM smc_turma WHERE turma_tipo_atendimento = '1' AND turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = '$anoLetivo'";
$ContaAlunos = mysql_query($query_ContaAlunos, $SmecelNovo) or die(mysql_error());
$row_ContaAlunos = mysql_fetch_assoc($ContaAlunos);
$totalRows_ContaAlunos = mysql_num_rows($ContaAlunos);
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

      <h1 class="ls-title-intro ls-ico-home">Listar Turmas de AEE - Ano Letivo <?php echo $anoLetivo; ?></h1>



      <?php if (isset($_GET["turmaexcluida"])) { ?>
        <p>
        <div class="ls-alert-info ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          TURMA EXCLUIDA COM SUCESSO.
        </div>
        </p>
      <?php } ?>

      <?php if (isset($_GET["nada"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
        </div>
      <?php } ?>

      <?php if (isset($_GET["permissao"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          ESTE USUÁRIO NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
        </div>
      <?php } ?>

      <?php if (isset($_GET["semdados"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          SEM INFORMAÇÕES PARA VISUALIZAR.
        </div>
      <?php } ?>

      <?php if (isset($_GET["editada"])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          TURMA EDITADA COM SUCESSO.
        </div>
      <?php } ?>

      <?php if (isset($_GET["cadastrado"])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          TURMA CADASTRADA COM SUCESSO.
        </div>
      <?php } ?>

      <?php if (isset($_GET["remanejadas"])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          TURMAS REMANEJADAS COM SUCESSO.
        </div>
      <?php } ?>


      <a class="ls-btn-primary ls-ico-windows" href="turmaCadastrar.php"> Cadastrar Turma</a>
      <a class="ls-btn-primary ls-ico-paint-format" href="print_turmaListar.php" target="_blank"> Imprimir</a>
      <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
        <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
        <ul class="ls-dropdown-nav">

          <li>
            <a href="turmaListarAee.php?ano=<?php echo $anoLetivo;
            if (isset($_GET['st']))
              echo '&st=' . $_GET['st']; ?>" target="" title="Diários">
              ANO LETIVO <?php echo $anoLetivo; ?>
            </a>
          </li>

          <?php do { ?>
            <li>
              <a href="turmaListarAee.php?ano=<?php echo $row_Ano['ano_letivo_ano'];
              if (isset($_GET['st']))
                echo '&st=' . $_GET['st']; ?>" target="" title="Diários">
                ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
              </a>
            </li>
          <?php } while ($row_Ano = mysql_fetch_assoc($Ano)); ?>

        </ul>
      </div>

      <!--
            <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary ls-ico-menu2">ATAS DE RESULTADOS FINAIS</a>
                <ul class="ls-dropdown-nav">
        
          <li><a href="print_atas_finais_unidade.php?unidade=1" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">TODAS AS TURMAS - 1ª UNIDADE</a></li>
          <li><a href="print_atas_finais_unidade.php?unidade=2" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">TODAS AS TURMAS - 2ª UNIDADE</a></li>
          <li><a href="print_atas_finais_unidade.php?unidade=3" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">TODAS AS TURMAS - 3ª UNIDADE</a></li>
          <li><a href="print_atas_finais_unidade.php?unidade=4" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">TODAS AS TURMAS - 4ª UNIDADE</a></li>
                    
                </ul>
              </div>
              -->

      <hr>

      <div class="ls-box ls-sm-space">

        <?php if ($totalRows_TurmasListar > 0) { // Show if recordset not empty ?>
          <table class="ls-table ls-table-striped ls-sm-space">
            <thead>
              <tr>
                <th class="ls-txt-center" width="40px">Nº</th>
                <th class="ls-txt-center" width="100px">CÓD <a href="#" class="ls-ico-help" data-trigger="hover"
                    data-ls-module="popover" data-placement="right"
                    data-content="Clique para visualizar os alunos da turma." data-title="Atenção"></a></th>
                <th class="ls-txt-left">TURMA</th>
                <th class="ls-txt-center hidden-xs">MATRIZ</th>
                <th class="ls-txt-center hidden-xs" width="100">TURNO</th>
                <th class="ls-txt-center hidden-xs" width="100"></th>
                <th class="ls-txt-center hidden-xs" width="100">%</th>
                <th class="ls-txt-center hidden-xs" width="100">RELATÓRIOS</th>
                <th class="ls-txt-center hidden-xs" width="80">OPÇÕES</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $contagem = 1;
              $totalAlunos = 0;
              do { ?>

                <tr>
                  <td class="ls-txt-center">
                    <?php
                    echo $contagem;
                    $contagem++;
                    ?>
                  </td>
                  <td class="ls-txt-center"><a href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_TurmasListar['turma_id']; ?><?php if (isset($_GET['ano'])) {
                       echo "&ano=$anoLetivo";
                     } ?>"><?php echo $row_TurmasListar['turma_id']; ?></a>
                  </td>
                  <td class="ls-txt-left">
                    <?php echo $row_TurmasListar['turma_nome']; ?>
                    <?php if ($row_TurmasListar['turma_multisseriada'] == "1") { ?><br><i>(multisseriada)</i><?php } ?>
                  </td>
                  <td class="ls-txt-center hidden-xs">
                    <?php if ($row_TurmasListar['matriz_id'] == 0) {
                      echo "Não se aplica";
                    } else {
                      echo $row_TurmasListar['matriz_nome'];
                    } ?>
                    <?php if ($row_TurmasListar['matriz_ativa'] == "N") { ?><a href="#" class="ls-ico-help ls-float-right"
                        data-trigger="hover" data-ls-module="popover" data-placement="left"
                        data-content="Esta matriz está <strong class='ls-color-danger'>desatualizada</strong>. Clique em EDITAR TURMA e selecione a matriz correta."
                        data-title="ATENÇÃO"></a><?php } ?>
                  </td>
                  <td class="ls-txt-center hidden-xs"><?php echo $row_TurmasListar['turma_turno_descricao']; ?></td>

                  <td class="ls-txt-left hidden-xs">

                    <?php //if ($row_TurmasListar['turma_total_alunos']>0) { ?>
                    <?php

                    if ($row_TurmasListar['turma_total_alunos'] <> "") {
                      $limiteAlunos = $row_TurmasListar['turma_total_alunos'];
                    } else {
                      $limiteAlunos = $row_TurmasListar['etapa_limite_turma'];
                    }
                    $alunosTurma = alunosConta($row_TurmasListar['turma_id'], $anoLetivo);
                    $perc = (($alunosTurma / $limiteAlunos) * 100);
                    $percentual = number_format($perc, 0);
                    if ($percentual > 100) {
                      $excedeu = $percentual - 100;
                      $percentual = 100;
                    }
                    ?>

                    <span
                      class="<?php if ($alunosTurma > $limiteAlunos) { ?>ls-tag-danger<?php } else if ($alunosTurma == $limiteAlunos) { ?>ls-tag-warning<?php } else { ?>ls-tag-success<?php } ?>">
                      <?php


                      echo $alunosTurma;
                      $totalAlunos = $totalAlunos + $alunosTurma;
                      ?>/<?php echo $limiteAlunos; ?>


                    </span>
                    <?php // } ?>

                  </td>
                  <td class="ls-txt-center hidden-xs">


                    <div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo $percentual; ?>"
                      class="ls-animated"></div>

                  </td>






                  <td class="ls-txt-center hidden-xs">

                    <div data-ls-module="dropdown" class="ls-dropdown ls-pos-right">
                      <a href="#" class="ls-btn ls-btn-xs"></a>
                      <ul class="ls-dropdown-nav">


                        <li><a href="print_mapa_de_frequencia.php?ct=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-text" target="_blank" title="Mapa de frequencia">Mapa de frequência</a> </li>
                        <li><a href="print_mapa_de_frequencia_avulso.php?ct=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-text" target="_blank" title="Mapa de frequencia avulso">Mapa de frequência
                            (avulso)</a> </li>
                        <li><a href="print_mapa_de_notas.php?ct=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-text" target="_blank" title="Mapa de Notas">Mapa de notas</a> </li>
                        <li><a href="print_mapa_de_conteudo.php?ct=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-text" target="_blank" title="Registro de conteúdo">Registro de conteúdo</a> </li>


                        <li><a href="parecer_turma.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-text ls-divider" target="_blank" title="Parecer da Turma">Parecer da Turma</a>
                        </li>

                        <li><a href="print_resultados_recuperacao.php?ct=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-book" target="_blank" title="Resultado parcial">Resultado Parcial</a> </li>

                        <li><a
                            href="rendimento_disciplina_unidade.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>&unidade=1"
                            class="ls-ico-book ls-divider" title="Gerar Ata de Resultados Finais">Rendimento por Unidade</a>
                        </li>

                        <!--  
                <li><a href="print_atas_finais_unidade.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>&unidade=1" class="ls-ico-book ls-divider" target="_blank" title="Gerar Ata de Resultados Finais">Ata da 1ª Unidade</a> </li>
                <li><a href="print_atas_finais_unidade.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>&unidade=2" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">Ata da 2ª Unidade</a> </li>
                <li><a href="print_atas_finais_unidade.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>&unidade=3" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">Ata da 3ª Unidade</a> </li>
                <li><a href="print_atas_finais_unidade.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>&unidade=4" class="ls-ico-book" target="_blank" title="Gerar Ata de Resultados Finais">Ata da 4ª Unidade</a> </li>
                <li><a href="print_atas_finais.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>" class="ls-ico-book ls-divider" target="_blank" title="Gerar Ata de Resultados Finais">Ata de Resultados Finais</a> </li>					  
                -->
                      </ul>
                    </div>

                  </td>

                  <td class="ls-txt-center hidden-xs">

                    <div data-ls-module="dropdown" class="ls-dropdown ls-pos-right">
                      <a href="#" class="ls-btn ls-btn-xs"></a>
                      <ul class="ls-dropdown-nav">
                        <li><a href="horariosEditar.php?c=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-hours ls-divider">Editar horários</a></li>
                        <li><a href="turmaEditar.php?c=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-edit-admin">Editar turma</a></li>
                        <li><a href="turmaEditar_caracteristicas.php?c=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-edit-admin">Editar características</a></li>
                        <li><a href="boletimCadastrarBoletimTurma.php?turma=<?php echo $row_TurmasListar['turma_id']; ?>"
                            class="ls-ico-edit-admin">Gerar boletim da turma</a></li>
                        <li><a href="javascript:func()"
                            onclick="confirmaExclusao('<?php echo $row_TurmasListar['turma_id']; ?>', '<?php echo htmlspecialchars($row_TurmasListar['turma_nome'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo $row_TurmasListar['turma_tipo_atendimento']; ?>);"
                            class="ls-ico-cancel-circle ls-divider ls-color-danger">Excluir turma</a></li>
                      </ul>
                    </div>

                  </td>

                </tr>
              <?php } while ($row_TurmasListar = mysql_fetch_assoc($TurmasListar)); ?>
            </tbody>
          </table>
          <p>TOTAL DE ALUNOS MATRICULADOS: <?php echo $totalAlunos; ?></p>
        </div>


      <?php } else { ?>
        <div class="ls-alert-info"><strong>Atenção:</strong> Nenhuma turma cadastrada.</div>
        Cadastre as novas turmas deste Ano Letivo ou faça o remanejamento das turmas do Ano Letivo anterior <a
          href="turmas_remanejar.php">clicando aqui</a>.
      <?php } // Show if recordset not empty ?>
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
    function confirmaExclusao(c, turma, tipo_atendimento) {
      var turmaEncoded = encodeURIComponent(turma);
      var resposta = confirm("Deseja realmente remover a turma " + turma + "? Se escolher SIM, os vínculos desta turma também serão excluídos.");
      if (resposta == true) {
        window.location.href = "turmaExcluir.php?c=" + c + "&turma=" + turmaEncoded + "&tipo_atendimento=" + tipo_atendimento;
      }
    }
  </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($TurmasListar);

mysql_free_result($ContaAlunos);
?>