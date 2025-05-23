<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('fnc/anti_injection.php'); ?>
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


$diasemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');

$data = date('Y-m-d');
if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
}


//$data = date('Y-m-d');
$diasemana_numero = date('w', strtotime($data));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AulasHoje = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
turma_id, turma_nome, turma_ano_letivo, turma_turno,
CASE turma_turno
	WHEN 0 THEN 'INTEGRAL'
	WHEN 1 THEN 'MATUTINO'
	WHEN 2 THEN 'VESPERTINO'
	WHEN 3 THEN 'NOTUNO'
	END AS turma_turno_nome,
func_id, func_nome,
disciplina_id, disciplina_nome,
COUNT(*) AS total_dia 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_dia = $diasemana_numero
GROUP BY func_id, disciplina_id, turma_id 
ORDER BY func_nome ASC
";
$AulasHoje = mysql_query($query_AulasHoje, $SmecelNovo) or die(mysql_error());
$row_AulasHoje = mysql_fetch_assoc($AulasHoje);
$totalRows_AulasHoje = mysql_num_rows($AulasHoje);
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

      <h1 class="ls-title-intro ls-ico-home">Aulas postadas em <?php echo date("d/m/Y", strtotime($data)); ?>
        (<?php echo $diasemana[$diasemana_numero]; ?>)
      </h1>
      <!-- CONTEÚDO -->

      <div class="ls-box-filter">
        <form action="ava_verifica_aulas.php" class="ls-form ls-form-inline">
          <label class="ls-label col-md-3 col-sm-4">
            <b class="ls-label-text">Data</b>
            <input type="date" name="data" class="" value="<?php echo $data; ?>">
          </label>

          <div class="ls-actions-btn">
            <button type="submit" class="ls-btn">Filtrar</button>
            <a href="ava_verifica_aulas.php" class="ls-btn">Hoje</a>
          </div>
        </form>
      </div>

      <?php
      // Primeiro, calcular os totais de "sim" e "não"
      $sim = 0;
      $nao = 0;

      // Verificar se há resultados antes de calcular os totais
      if (mysql_num_rows($AulasHoje) > 0) {
        while ($row_AulasHoje = mysql_fetch_assoc($AulasHoje)) {
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_Postadas = "
        SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, 
        plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite,
        plano_aula_video, plano_aula_meet, plano_aula_sicrona_hora, plano_aula_sicrona_minuto, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
        plano_aula_hash FROM smc_plano_aula
        WHERE plano_aula_id_turma = '$row_AulasHoje[turma_id]' AND plano_aula_id_disciplina = '$row_AulasHoje[disciplina_id]' 
        AND plano_aula_id_professor = '$row_AulasHoje[func_id]' AND plano_aula_data = '$data'
        ";
          $Postadas = mysql_query($query_Postadas, $SmecelNovo) or die(mysql_error());
          $totalRows_Postadas = mysql_num_rows($Postadas);

          // Contar "sim" e "não"
          if ($totalRows_Postadas == $row_AulasHoje['total_dia']) {
            $sim++;
          } else {
            $nao++;
          }
        }
      } else {
        echo "<p>Nenhum dado encontrado para calcular os totais.</p>";
      }

      // Resetar o ponteiro novamente para exibir a tabela
      mysql_data_seek($AulasHoje, 0);

      // Exibir os totais no topo
      ?>
      <p class="ls-txt-right">
        <span class="ls-ico-checkmark ls-color-success ls-tag-success" style="font-size:20px"><?php echo $sim; ?></span>
        <span class="ls-ico-question ls-color-danger ls-tag-danger" style="font-size:20px"><?php echo $nao; ?></span>
      </p>

      <!-- Tabela -->
      <table class="ls-table ls-sm-space ls-table-striped" width="100%">
        <thead>
          <tr>
            <th>PROFESSOR</th>
            <th class="ls-txt-center">COMPONENTE</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center">TURNO</th>
            <th class="ls-txt-center">AULAS</th>
            <th class="ls-txt-center">POSTADAS</th>
            <th class="ls-txt-center">RESULTADO</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysql_num_rows($AulasHoje) > 0) {
            while ($row_AulasHoje = mysql_fetch_assoc($AulasHoje)) {
              // Depuração: Verificar o valor de func_nome
              if (empty($row_AulasHoje['func_nome'])) {
                echo "<!-- Debug: func_nome está vazio para este registro -->";
              }

              mysql_select_db($database_SmecelNovo, $SmecelNovo);
              $query_Postadas = "
                SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, 
                plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, plano_aula_atividade_resposta_obrigatoria_data_limite,
                plano_aula_video, plano_aula_meet, plano_aula_sicrona_hora, plano_aula_sicrona_minuto, plano_aula_google_form, plano_aula_google_form_tempo, plano_aula_publicado, 
                plano_aula_hash FROM smc_plano_aula
                WHERE plano_aula_id_turma = '$row_AulasHoje[turma_id]' AND plano_aula_id_disciplina = '$row_AulasHoje[disciplina_id]' 
                AND plano_aula_id_professor = '$row_AulasHoje[func_id]' AND plano_aula_data = '$data'
                ";
              $Postadas = mysql_query($query_Postadas, $SmecelNovo) or die(mysql_error());
              $row_Postadas = mysql_fetch_assoc($Postadas);
              $totalRows_Postadas = mysql_num_rows($Postadas);
              ?>
              <tr>
                <td><?php echo !empty($row_AulasHoje['func_nome']) ? $row_AulasHoje['func_nome'] : 'Nome não disponível'; ?>
                </td>
                <td class="ls-txt-center"><?php echo $row_AulasHoje['disciplina_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_AulasHoje['turma_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_AulasHoje['turma_turno_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_AulasHoje['total_dia']; ?></td>
                <td class="ls-txt-center"><?php echo $totalRows_Postadas; ?></td>
                <td class="ls-txt-center">
                  <?php if ($totalRows_Postadas == $row_AulasHoje['total_dia']) { ?>
                    <span class="ls-ico-checkmark ls-color-success" style="font-size:20px"></span>
                  <?php } else { ?>
                    <span class="ls-ico-question ls-color-danger" style="font-size:10px"></span>
                  <?php } ?>
                </td>
              </tr>
            <?php
            }
          } else {
            echo "<tr><td colspan='7' class='ls-txt-center'>Nenhuma aula encontrada para este dia.</td></tr>";
          }
          ?>
        </tbody>
      </table>


      <hr>


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
mysql_free_result($Postadas);

mysql_free_result($AulasHoje);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>