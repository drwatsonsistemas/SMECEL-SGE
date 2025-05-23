<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "fnc/exibeHorario.php"; ?>
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

$colname_Turma = "-1";
if (isset($_GET['c'])) {
  $colname_Turma = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_matriz_id FROM smc_turma WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);


if ($totalRows_Turma == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?nada"); 
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaProfessor = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, func_id, func_nome, funcao_id, funcao_docencia
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = $row_EscolaLogada[escola_id] AND funcao_docencia = 'S'
ORDER BY func_nome ASC";
$ListaProfessor = mysql_query($query_ListaProfessor, $SmecelNovo) or die(mysql_error());
$row_ListaProfessor = mysql_fetch_assoc($ListaProfessor);
$totalRows_ListaProfessor = mysql_num_rows($ListaProfessor);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = "SELECT disciplina_id, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina ORDER BY disciplina_nome ASC";
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaHorarios = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
func_id, func_nome, disciplina_id, disciplina_nome,
CASE ch_lotacao_dia
WHEN 1 THEN '<span style=\"color:#3CB371\">SEGUNDA-FEIRA</span>'
WHEN 2 THEN '<span style=\"color:#DAA520\">TERÇA-FEIRA</span>'
WHEN 3 THEN '<span style=\"color:#800080\">QUARTA-FEIRA</span>'
WHEN 4 THEN '<span style=\"color:#CD5C5C\">QUINTA-FEIRA</span>'
WHEN 5 THEN '<span style=\"color:#4682B4\">SEXTA-FEIRA</span>'
END AS ch_lotacao_dia_descricao
FROM smc_ch_lotacao_professor 
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id 
LEFT JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id 
WHERE ch_lotacao_turma_id = $row_Turma[turma_id] 
ORDER BY ch_lotacao_dia, ch_lotacao_aula ASC";
$ListaHorarios = mysql_query($query_ListaHorarios, $SmecelNovo) or die(mysql_error());
$row_ListaHorarios = mysql_fetch_assoc($ListaHorarios);
$totalRows_ListaHorarios = mysql_num_rows($ListaHorarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = $row_Turma[turma_matriz_id]  ";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarDisciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome, disciplina_nome_abrev, disciplina_bncc 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
WHERE matriz_disciplina_id_matriz = '$row_matriz[matriz_id]'";
$ListarDisciplinas = mysql_query($query_ListarDisciplinas, $SmecelNovo) or die(mysql_error());
$row_ListarDisciplinas = mysql_fetch_assoc($ListarDisciplinas);
$totalRows_ListarDisciplinas = mysql_num_rows($ListarDisciplinas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarDisciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome, disciplina_nome_abrev, disciplina_bncc 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
WHERE matriz_disciplina_id_matriz = '$row_matriz[matriz_id]'
ORDER BY disciplina_nome ASC";
$ListarDisciplinasMatriz = mysql_query($query_ListarDisciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_ListarDisciplinasMatriz = mysql_fetch_assoc($ListarDisciplinasMatriz);
$totalRows_ListarDisciplinasMatriz = mysql_num_rows($ListarDisciplinasMatriz);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarTurmas = "SELECT 
turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_descricao
FROM smc_turma 
WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ListarTurmas = mysql_query($query_ListarTurmas, $SmecelNovo) or die(mysql_error());
$row_ListarTurmas = mysql_fetch_assoc($ListarTurmas);
$totalRows_ListarTurmas = mysql_num_rows($ListarTurmas);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: turmaListar.php?permissao"));
    exit;
  }

  $codTurma = $_POST['ch_lotacao_turma_id'];
  $dia = $_POST['ch_lotacao_dia'];

  for ($i = 1; $i <= $row_matriz['matriz_aula_dia']; $i++) {
    if (isset($_POST['ch_lotacao_aula_'.$i])) {

      $horarios = sprintf("SELECT * FROM smc_ch_lotacao_professor 
        WHERE 
        ch_lotacao_dia = '$dia' AND ch_lotacao_turma_id = '$codTurma'
        AND ch_lotacao_aula = %s",
        GetSQLValueString($i, "int"));
      $horarioTurma = mysql_query($horarios, $SmecelNovo) or die(mysql_error());
      $totalRows_horarioTurma = mysql_num_rows($horarioTurma);
            if ($totalRows_horarioTurma == 0) { // Verifica se o horário já está ocupado
              $insertSQL = sprintf("INSERT INTO smc_ch_lotacao_professor (ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_obs, ch_lotacao_escola) VALUES (%s, %s, %s, %s, %s, %s, %s)",
               GetSQLValueString($codTurma, "int"),
               GetSQLValueString($dia, "int"),
               GetSQLValueString($i, "int"),
               GetSQLValueString($_POST['ch_lotacao_professor_id'], "int"),
               GetSQLValueString($_POST['ch_lotacao_disciplina_id'], "int"),
               GetSQLValueString($_POST['ch_lotacao_obs'], "text"),
               GetSQLValueString($_POST['ch_lotacao_escola'], "int"));
              mysql_select_db($database_SmecelNovo, $SmecelNovo);
              $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
            } else {
                // Se o horário já estiver ocupado, redireciona para uma página de erro
              $includeGoTo = "horariosEditar.php?c=$codTurma&horarioCadastrado=true";
              header(sprintf("Location: %s", $includeGoTo));
              exit;
            }
          }
        }

    // Redirecionar para a página de sucesso se todas as inserções forem bem-sucedidas
        $includeGoTo = "horariosEditar.php?c=$codTurma&turmaCadastrada=true";
        header(sprintf("Location: %s", $includeGoTo));
        exit;
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
        <link rel="stylesheet" type="text/css" href="css/locastyle.css">        <link rel="stylesheet" type="text/css" href="css/preloader.css">
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
            <h1 class="ls-title-intro ls-ico-numbered-list">CADASTRO DE HORÁRIOS</h1>

            <!-- CONTEÚDO --> 

            <a href="turmaListar.php" class="ls-btn">VOLTAR</a>

            <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-primary">Mudar turma</a>
              <ul class="ls-dropdown-nav">
                <?php do { ?>
                  <li><a href="horariosEditar.php?c=<?php echo $row_ListarTurmas['turma_id']; ?>"><?php echo $row_ListarTurmas['turma_nome']; ?> - <?php echo $row_ListarTurmas['turma_turno_descricao']; ?></a>
                  <?php } while ($row_ListarTurmas = mysql_fetch_assoc($ListarTurmas)); ?>
                </ul>
              </div>
              <?php if (isset($_GET["turmaExcluida"])) { ?>
                <p>

                  <div class="ls-alert-info ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Horário excluído com sucesso. </div>
                </p>
              <?php } ?>
              <?php if (isset($_GET["turmaCadastrada"])) { ?>
                <p>

                  <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Horário cadastrado com sucesso. </div>
                </p>
              <?php } ?>

               <?php if (isset($_GET["horarioCadastrado"])) { ?>
                <p>

                  <div class="ls-alert-warning ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Esse horário já existe na grade. </div>
                </p>
              <?php } ?>
              <br>
              <br>



              <div class="ls-box ls-lg-space ls-ico-list2 ls-ico-bg">
                <h1 class="ls-title-1 ls-color-theme"><?php echo $row_Turma['turma_nome']; ?></h1>
                <p> Matriz: <?php echo $row_matriz['matriz_nome']; ?> | 
                  Dias letivos: <?php echo $row_matriz['matriz_dias_letivos']; ?> | 
                  Semanas letivas: <?php echo $row_matriz['matriz_semanas_letivas']; ?> | 
                  Dias na semana: <?php echo $row_matriz['matriz_dias_semana']; ?> | 
                  Minutos por aula: <?php echo $row_matriz['matriz_minutos_aula']; ?> | 
                  Aulas por dia: <?php echo $row_matriz['matriz_aula_dia']; ?> </p>
                  <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">CADASTRAR HORÁRIO</button>
                </div>







                <div class="ls-box">
                  <h5 class="ls-title-3">QUADRO DE HORÁRIOS</h5>
                  <table class="ls-table ls-no-hover ls-table-striped ls-sm-space ls-table-bordered ls-bg-header">
                    <thead>
                      <tr>
                        <th class="ls-txt-center" width="40px"></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">SEGUNDA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEG</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">TERÇA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">TER</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">QUARTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUA</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">QUINTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUI</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">SEXTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEX</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 


                      mysql_select_db($database_SmecelNovo, $SmecelNovo);
                      $query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = $row_matriz[matriz_id]";
                      $matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
                      $row_matriz = mysql_fetch_assoc($matriz);
                      $totalRows_matriz = mysql_num_rows($matriz);


                      ?>
                      <?php for ($a = 1; $a <= $row_matriz['matriz_aula_dia']; $a++) { ?>
                        <tr>
                          <td class="ls-txt-center"><?php echo $a; ?></td>
                          <td class="ls-txt-center"><?php echo exibeHorario($row_Turma['turma_id'],1,$a); ?></td>
                          <td class="ls-txt-center"><?php echo exibeHorario($row_Turma['turma_id'],2,$a); ?></td>
                          <td class="ls-txt-center"><?php echo exibeHorario($row_Turma['turma_id'],3,$a); ?></td>
                          <td class="ls-txt-center"><?php echo exibeHorario($row_Turma['turma_id'],4,$a); ?></td>
                          <td class="ls-txt-center"><?php echo exibeHorario($row_Turma['turma_id'],5,$a); ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <?php
                  mysql_select_db($database_SmecelNovo, $SmecelNovo);
                  $query_Funcionarios = "
                  SELECT 
                  ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_turma_id, func_id, func_nome 
                  FROM smc_ch_lotacao_professor
                  INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
                  WHERE ch_lotacao_turma_id = $row_Turma[turma_id]
                  GROUP BY ch_lotacao_professor_id ASC";
                  $Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
                  $row_Funcionarios = mysql_fetch_assoc($Funcionarios);
                  $totalRows_Funcionarios = mysql_num_rows($Funcionarios);
                  ?>
                  <?php if ($totalRows_Funcionarios > 0) { ?>
                    <div class="ls-box ls-xs-space"> <small>|
                      <?php do { ?>
                        <b><?php echo $row_Funcionarios['ch_lotacao_professor_id'] ?></b>-<?php echo $row_Funcionarios['func_nome'] ?> |
                      <?php } while ($row_Funcionarios = mysql_fetch_assoc($Funcionarios)); ?>
                    </small> </div>
                  <?php } ?>
                </div>


                <div class="ls-box">
                  <h5 class="ls-title-3">DISTRIBUIÇÃO ANALÍTICA</h5>
                  <?php if ($totalRows_ListaHorarios > 0) { // Show if recordset not empty ?>
                    <table width="100%" class="ls-table ls-no-hover ls-sm-space ls-table-bordered ls-bg-header">
                      <thead>
                        <tr>
                          <th class="ls-txt-center" width="120">DIA</th>
                          <th class="ls-txt-center" width="120">AULA</th>
                          <th class="ls-txt-center">PROFESSOR</th>
                          <th class="ls-txt-center">DISCIPLINA</th>
                          <th class="ls-txt-center" width="50"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php do { ?>
                          <tr>
                            <td class="ls-txt-left"><?php echo $row_ListaHorarios['ch_lotacao_dia_descricao']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_ListaHorarios['ch_lotacao_aula']; ?>º HORÁRIO</td>
                            <td class="ls-txt-center"><?php echo $row_ListaHorarios['func_nome']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_ListaHorarios['disciplina_nome']; ?></td>
                            <td class="ls-txt-center"><a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_ListaHorarios['ch_lotacao_id']; ?>','<?php echo $colname_Turma; ?>')" class="ls-ico-remove ls-ico-right"></a></td>
                          </tr>
                        <?php } while ($row_ListaHorarios = mysql_fetch_assoc($ListaHorarios)); ?>
                      </tbody>
                    </table>

                        <p>Total de aulas semanais: <strong><?php echo $totalRows_ListaHorarios; ?></strong></p>

                  <?php } else { ?>
                    <p> Nenhum horário cadastrado nessa turma.</p>
                  <?php } // Show if recordset not empty ?>
                </div> 



                <div class="ls-box">
                  <h5 class="ls-title-3">ACOMPANHAMENTO DA MATRIZ</h5>
                  <?php if ($totalRows_ListarDisciplinas > 0) { // Show if recordset not empty ?>
                    <?php $totalCh = 0; ?>
                    <table class="ls-table ls-no-hover ls-sm-space ls-table-bordered ls-bg-header">
                      <thead>
                        <tr>
                          <th></th>
                          <th width="100" class="ls-txt-center">AULAS</th>
                          <th width="100" class="ls-txt-center">MATRIZ</th>
                          <th width="50"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php do { ?>
                          <?php
                          mysql_select_db($database_SmecelNovo, $SmecelNovo);
                          $query_HorarioReg = "
                          SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
                          ch_lotacao_escola 
                          FROM smc_ch_lotacao_professor
                          WHERE ch_lotacao_turma_id = '$row_Turma[turma_id]' AND ch_lotacao_disciplina_id = '$row_ListarDisciplinas[disciplina_id]'
                          ";
                          $HorarioReg = mysql_query($query_HorarioReg, $SmecelNovo) or die(mysql_error());
                          $row_HorarioReg = mysql_fetch_assoc($HorarioReg);
                          $totalRows_HorarioReg = mysql_num_rows($HorarioReg);
                          ?>
                          <?php $cargaSemana = $row_ListarDisciplinas['matriz_disciplina_ch_ano']/$row_matriz['matriz_semanas_letivas']; ?>
                          <?php $totalCh = $totalCh + $row_ListarDisciplinas['matriz_disciplina_ch_ano']; ?>
                          <tr>
                            <td><?php echo $row_ListarDisciplinas['disciplina_nome']; ?></td>
                            <td class="ls-txt-center"><?php echo $totalRows_HorarioReg; ?></td>
                            <td class="ls-txt-center"><?php echo $cargaSemana; ?></td>
                            <td class="ls-txt-center"><?php if ($totalRows_HorarioReg==$cargaSemana) { ?>
                              <span class="ls-ico-checkmark-circle ls-color-success"></span>
                            <?php } else { ?>
                              <span class="ls-ico-info ls-color-warning"></span>
                            <?php } ?>
                          </td>

                        </tr>

                      <?php } while ($row_ListarDisciplinas = mysql_fetch_assoc($ListarDisciplinas)); ?>
                      <tr>
                        <td><strong>AULAS NA SEMANA</strong></td>
                        <td class="ls-txt-center"><strong><?php echo $totalRows_ListaHorarios ?></strong></td>
                        <td class="ls-txt-center"><strong><?php echo $totalCh/$row_matriz['matriz_semanas_letivas']; ?></strong></td>
                        <td class="ls-txt-center"></td>
                      </tr>
                    </tbody>

                  </table>
                <?php } else { ?>
                  Nenhuma disciplina cadastrada.
                <?php } // Show if recordset not empty ?>
              </div>
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
                <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
                <li><a href="#">&gt; Guia</a></li>
                <li><a href="#">&gt; Wiki</a></li>
              </ul>
            </nav>
          </aside>
          <div class="ls-modal" id="myAwesomeModal">
            <div class="ls-modal-box">
              <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">NOVO HORÁRIO</h4>
              </div>
              <div class="ls-modal-body" id="myModalBody">
                <form method="post" class="ls-form ls-form-horizontal row" name="form1" action="<?php echo $editFormAction; ?>" data-ls-module="form">
                  <fieldset>
                    <label class="ls-label col-md-12">
                      <b class="ls-label-text">DIA DA SEMANA</b><br>
                      <p>
                        <label class="ls-label-text">
                          <input type="radio" name="ch_lotacao_dia" value="1"  required />
                        SEG </label>
                        <label class="ls-label-text">
                          <input type="radio" name="ch_lotacao_dia" value="2" />
                        TER </label>
                        <label class="ls-label-text">
                          <input type="radio" name="ch_lotacao_dia" value="3" />
                        QUA </label>
                        <label class="ls-label-text">
                          <input type="radio" name="ch_lotacao_dia" value="4" />
                        QUI </label>
                        <label class="ls-label-text">
                          <input type="radio" name="ch_lotacao_dia" value="5" />
                        SEX </label>
                      </p>
                    </label>
                    <label class="ls-label col-md-12">
                      <b class="ls-label-text">PERÍODO/HORÁRIO</b><br>
                      <p>
                        <?php for ($a = 1; $a <= $row_matriz['matriz_aula_dia']; $a++) { ?>
                          <label class="ls-label-text">
                            <input type="checkbox" name="ch_lotacao_aula_<?php echo $a; ?>" value="<?php echo $a; ?>" />
                            <?php echo $a; ?>º </label>
                          <?php } ?>
                        </p>
                      </label>
                      <label class="ls-label col-md-12">
                        <b class="ls-label-text">PROFESSOR</b>
                        <div class="ls-custom-select">
                          <select name="ch_lotacao_professor_id" required>
                            <option value="" >Escolha...</option>
                            <?php do {  ?>
                              <option value="<?php echo $row_ListaProfessor['vinculo_id_funcionario']?>" ><?php echo $row_ListaProfessor['func_nome']?></option>
                            <?php } while ($row_ListaProfessor = mysql_fetch_assoc($ListaProfessor)); ?>
                          </select>
                        </div>
                      </label>
                      <label class="ls-label col-md-12">
                        <b class="ls-label-text">DISCIPLINA/COMPONENTE</b>
                        <div class="ls-custom-select">
                          <select name="ch_lotacao_disciplina_id" required>
                            <option value="" >Escolha...</option>
                            <?php do {  ?>
                              <option value="<?php echo $row_ListarDisciplinasMatriz['disciplina_id']?>" ><?php echo $row_ListarDisciplinasMatriz['disciplina_nome']?></option>
                            <?php } while ($row_ListarDisciplinasMatriz = mysql_fetch_assoc($ListarDisciplinasMatriz)); ?>
                          </select>
                        </div>
                      </label>
                      <label class="ls-label col-md-12"> <b class="ls-label-text">OBSERVAÇÃO</b>
                        <textarea name="ch_lotacao_obs" cols="50" rows="2"></textarea>
                      </label>
                      <input type="hidden" name="ch_lotacao_turma_id" value="<?php echo $row_Turma['turma_id']; ?>">
                      <input type="hidden" name="ch_lotacao_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
                      <input type="hidden" name="MM_insert" value="form1">
                    </fieldset>
                  </div>
                  <div class="ls-modal-footer"> <a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
                    <input type="submit" value="CADASTRAR HORÁRIO" class="ls-btn-primary">
                  </div>
                </form>
              </div>
            </div>
            <!-- /.modal --> 

            <!-- We recommended use jQuery 1.10 or up --> 
            <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
            <script src="js/locastyle.js"></script> 
            <script language="Javascript">
             function confirmaExclusao(id,turma) {
               var resposta = confirm("Deseja realmente remover este horário?");
               if (resposta == true) {
                 window.location.href = "cargahorariaexcluir.php?c="+id+"&turma="+turma;
               }
             }
           </script>
         </body>
         </html>
         <?php
         mysql_free_result($UsuLogado);

         mysql_free_result($EscolaLogada);

         mysql_free_result($Turma);

         mysql_free_result($ListaProfessor);

         mysql_free_result($Disciplina);

         mysql_free_result($HorarioReg);

         mysql_free_result($ListarTurmas);

         mysql_free_result($ListaHorarios);
         ?>
