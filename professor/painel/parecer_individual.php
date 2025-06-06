﻿<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>



<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../index.php?saiu";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "7";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False;

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) {
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
    if (($strUsers == "") && false) {
      $isValid = true;
    }
  }
  return $isValid;
}

$MM_restrictGoTo = "../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}
?>
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


$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

if ($totalRows_ProfLogado == "") {
  header("Location:../index.php?loginErr");
}


$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = anti_injection($_GET['disciplina']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";

$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Parecer = "
SELECT p_ind_id, p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_data_cadastro, p_ind_periodo 
FROM smc_parecer_individual_professor
WHERE p_ind_id_prof = '$row_ProfLogado[func_id]' AND p_ind_mat_aluno = '$row_Matricula[vinculo_aluno_id]'
ORDER BY p_ind_periodo ASC
";
$Parecer = mysql_query($query_Parecer, $SmecelNovo) or die(mysql_error());
$row_Parecer = mysql_fetch_assoc($Parecer);
$totalRows_Parecer = mysql_num_rows($Parecer);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


  $insertSQL = sprintf(
    "INSERT INTO smc_parecer_individual_professor (p_ind_id_prof, p_ind_mat_aluno, p_ind_texto, p_ind_periodo) VALUES ('$row_ProfLogado[func_id]', '$row_Matricula[vinculo_aluno_id]', %s, %s)",
    //GetSQLValueString($_POST['p_ind_id_prof'], "int"),
    //GetSQLValueString($_POST['p_ind_mat_aluno'], "int"),
    GetSQLValueString($_POST['p_ind_texto'], "text"),
    GetSQLValueString($_POST['p_ind_periodo'], "text")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "parecer_individual.php?cadastrado";

  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_GET['parecer'])) && ($_GET['parecer'] != "")) {
  $deleteSQL = sprintf(
    "DELETE FROM smc_parecer_individual_professor WHERE p_ind_id=%s AND p_ind_id_prof = '$row_ProfLogado[func_id]' AND p_ind_mat_aluno = '$row_Matricula[vinculo_aluno_id]'",
    GetSQLValueString($_GET['parecer'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "parecer_individual.php?cod=$colname_Matricula&disciplina=$colname_Disciplina&turma=$colname_Turma&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title><?php echo $row_ProfLogado['func_nome'] ?> - </title>

  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="../css/app.css" media="screen,projection" />

  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 5px;
      height: 15px;
      line-height: 15px;
    }
  </style>

</head>

<body class="indigo lighten-5">

  <?php include("menu_top.php"); ?>

  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <div class="row white" style="margin: 10px 0;">

        <div class="col s12 m2 hide-on-small-only">

          <p>
            <?php if ($row_ProfLogado['func_foto'] == "") { ?>
              <img src="<?php echo URL_BASE . 'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
            <?php } else { ?>
              <img src="<?php echo URL_BASE . 'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%"
                class="hoverable">
            <?php } ?>

            <br>
            <small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small>
            <small style="font-size:14px;">
              <?php echo current(str_word_count($row_ProfLogado['func_nome'], 2)); ?>
              <?php $word = explode(" ", trim($row_ProfLogado['func_nome']));
              echo $word[count($word) - 1]; ?>
            </small>

          </p>

          <?php include "menu_esq.php"; ?>


        </div>

        <div class="col s12 m10">
          <h5>Parecer individual:</h5>
          <hr>
          <p> <a href="alunos.php?disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>"
              class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>
              Voltar</a>


          <blockquote>
            <span style="margin-right:10px; text-align:center; float:left;">
              <?php if ($row_Matricula['aluno_foto'] == "") { ?>
                <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
              <?php } else { ?>
                <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class=""
                  border="0" width="50">
              <?php } ?>
              <?php //echo $row_Alunos['aluno_nome']; ?>
            </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
            Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong><br>
            <p>&nbsp;</p>
          </blockquote>


          <!-- Modal Trigger -->


          <!-- Modal Structure -->
          <div id="modal1" class="modal">
            <div class="modal-content">
              <h4>LANÇAR PARECER INDIVIDUAL</h4>

              <form method="post" name="form1" action="<?php echo $editFormAction; ?>">


                <div class="input-field col s12">
                  <textarea id="rel_avaliativo" class="materialize-textarea" name="p_ind_texto" cols="50" rows="3"
                    required></textarea>
                  <label for="rel_avaliativo">TEXTO</label>
                </div>

                <div class="input-field col s12 m12">
                  <select name="p_ind_periodo">
                    <?php for ($i = 1; $i < $row_Criterios['ca_qtd_periodos'] + 1; $i++) { ?>
                      <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, ""))) {
                           echo "SELECTED";
                         } ?>>
                        <?php echo $i; ?>º PERÍODO/UNIDADE</option>
                    <?php } ?>
                  </select>
                  <label>PERÍODO</label>
                </div>



                <input type="hidden" name="MM_insert" value="form1">

            </div>
            <div class="modal-footer">
              <input type="submit" value="SALVAR" class="btn right">
              <a href="#!" class="modal-close waves-effect waves-green btn-flat left">FECHAR</a>
            </div>
            </form>
          </div>

          <hr>

          <a class="waves-effect waves-light btn modal-trigger" href="#modal1">LANÇAR PARECER</a>

          <?php if ($totalRows_Parecer > 0) { // Show if recordset not empty ?>


            <?php do { ?>

              <div class="card-panel blue-text text-darken-2" id="parecer_<?php echo $row_Parecer['p_ind_id']; ?>">

                <a href="parecer_individual.php?cod=<?php echo $colname_Matricula; ?>&disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>&parecer=<?php echo $row_Parecer['p_ind_id']; ?>"
                  class="deletar" parecer="<?php echo $row_Parecer['p_ind_id']; ?>"
                  aluno="<?php echo $row_Matricula['vinculo_aluno_id']; ?>"
                  professor="<?php echo $row_ProfLogado['func_id']; ?>"><i
                    class="material-icons right red-text">delete_forever</i></a>

                <strong><?php echo $row_Parecer['p_ind_periodo']; ?>º PERÍODO </strong>
                <?php echo $row_Parecer['p_ind_texto']; ?>


              </div>

            <?php } while ($row_Parecer = mysql_fetch_assoc($Parecer)); ?>

          <?php } else { ?>
            <hr>
            Nenhum parecer cadastrado.

          <?php } // Show if recordset not empty ?>
          <div id="status"></div>
        </div>
        <div class="col s12 m12">
        </div>
      </div>
    </div>
  </div>

  <?php include("rodape.php"); ?>

  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="../js/materialize.min.js"></script>
  <script type="text/javascript" src="../js/app.js"></script>
  <script src="//cdn.tinymce.com/4/tinymce.min.js1"></script>
  <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js"
    referrerpolicy="origin"></script>
  <script src="langs/pt_BR.js"></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $(".dropdown-trigger").dropdown();
      $('.sidenav').sidenav();
      $('.modal').modal();
      $('select').formSelect();
    });
  </script>


  <script>
    tinymce.init({
      selector: '#rel_avaliativo',
      plugins: 'paste advlist autolink lists link image charmap print preview hr anchor pagebreak table',
      toolbar_mode: 'floating',
      toolbar: 'undo redo | formatselect | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | table',
      paste_as_text: true,
      paste_text_sticky: true,
      paste_text_sticky_default: true,
      table_appearance_options: false,
      menubar: false,
      statusbar: false,
      height: 300,
      setup: function (editor) {
        editor.on('change', function () {
          tinymce.triggerSave();
        });
      }
    });
  </script>

  <?php if (isset($_GET['cadastrado'])) { ?>

    <script>
      M.toast({
        html: '<i class="material-icons green-text">check_circle</i>&nbsp;<button class="btn-flat toast-action"> Parecer cadastrado com sucesso</button>'
      });
    </script>

  <?php } ?>

  <?php if (isset($_GET['deletado'])) { ?>

    <script>
      M.toast({
        html: '<i class="material-icons green-text">check_circle</i>&nbsp;<button class="btn-flat toast-action"> Parecer deletado com sucesso</button>'
      });
    </script>

  <?php } ?>



</body>

</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($Parecer);
?>