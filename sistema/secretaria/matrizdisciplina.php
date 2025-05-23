<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,99";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf(
    "INSERT INTO smc_matriz_disciplinas (matriz_disciplina_id_matriz, matriz_disciplina_ch_ano, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, matriz_disciplina_eixo) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['matriz_disciplina_id_matriz'], "int"),
    GetSQLValueString($_POST['matriz_disciplina_ch_ano'], "text"),
    GetSQLValueString(isset($_POST['matriz_disciplina_reprova']) ? "true" : "", "defined", "'S'", "'N'"),
    GetSQLValueString($_POST['matriz_disciplina_id_disciplina'], "int"),
    GetSQLValueString($_POST['matriz_disciplina_eixo'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "matrizdisciplina.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Matriz = "-1";
if (isset($_GET['hash'])) {
  $colname_Matriz = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("
  SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_criterio_avaliativo, ca_id, ca_descricao, ca_forma_avaliacao, ca_questionario_conceitos, ca_etapa_id 
  FROM smc_matriz
  INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
  WHERE matriz_hash = %s", GetSQLValueString($colname_Matriz, "text"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

if ($totalRows_Matriz < 1) {
  $semEscolas = "index.php?erro";
  header(sprintf("Location: %s", $semEscolas));
}




mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarDisciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_eixo, matriz_disciplina_id_disciplina, disciplina_cor_fundo, matriz_disciplina_ch_ano, matriz_disciplina_reprova, disciplina_id, disciplina_nome, disciplina_nome_abrev, disciplina_bncc, disciplina_eixo_id, disciplina_eixo_nome,
CASE matriz_disciplina_reprova
WHEN 'S' THEN 'SIM'
WHEN 'N' THEN 'NÃO'
END AS matriz_disciplina_reprova 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id
WHERE matriz_disciplina_id_matriz = '$row_Matriz[matriz_id]'
AND 
";
$ListarDisciplinas = mysql_query($query_ListarDisciplinas, $SmecelNovo) or die(mysql_error());
$row_ListarDisciplinas = mysql_fetch_assoc($ListarDisciplinas);
$totalRows_ListarDisciplinas = mysql_num_rows($ListarDisciplinas);

if ((isset($_GET['cod'])) && ($_GET['cod'] != "")) {

  $hash2 = "-1";
  if (isset($_GET['hash'])) {
    $hash2 = $_GET['hash'];
  }


  $matriz = $row_Matriz['matriz_id'];

  $deleteSQL = sprintf(
    "DELETE FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = '$matriz' AND matriz_disciplina_id=%s",

    GetSQLValueString($_GET['cod'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "matrizdisciplina.php?hash=$hash2&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$educacaoInfantil = " WHERE disciplina_bncc = '' ";

if ($row_Matriz['matriz_id_etapa'] == "1") {
  $educacaoInfantil = " WHERE disciplina_bncc = 'S' ";
} else {
  $educacaoInfantil = " WHERE disciplina_bncc IS NULL ";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT disciplina_id, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_area_conhecimento_id, area_conhecimento_id, area_conhecimento_nome 
FROM smc_disciplina 
INNER JOIN smc_area_conhecimento ON area_conhecimento_id = disciplina_area_conhecimento_id
$educacaoInfantil
ORDER BY disciplina_nome ASC";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

$query_Eixos = "
SELECT *
FROM smc_disciplina_eixos 
ORDER BY disciplina_eixo_nome ASC";
$Eixos = mysql_query($query_Eixos, $SmecelNovo) or die(mysql_error());
$row_Eixos = mysql_fetch_assoc($Eixos);
$totalRows_Eixos = mysql_num_rows($Eixos);
?>

<!DOCTYPE html>
<html class="ls-theme-green">

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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
    /* Corrigir o tamanho do Select2 dentro do .ls-custom-select */
    .ls-custom-select .select2-container {
      width: 100% !important;
    }

    /* Ajusta a altura para alinhar corretamente */
    .select2-container .select2-selection--single {
      height: 40px !important;
      line-height: 40px !important;
    }
  </style>
</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">CADASTRAR DISCIPLINAS</h1>
      <div class="ls-box ls-board-box">

        <h3 class="ls-title-1">MATRIZ: <?php echo $row_Matriz['matriz_nome']; ?></h3>
        <h5 class="ls-title-5">Criterio Avaliativo: <?php echo $row_Matriz['ca_descricao']; ?></h5>
        <hr>

        <a href="matriz.php" class="ls-btn">VOLTAR</a>

        <button data-ls-module="modal" data-target="#myAwesomeModal"
          class="ls-btn-primary ls-ico-plus"><?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?>CAMPO DE
            EXPERIÊNCIA<?php } else { ?>COMPONENTE CURRICULAR<?php } ?></button>

        <?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?><a
            href="bncc_camp_exp_cad.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>"
            class="ls-btn-primary ls-ico-plus">ACOMPANHAMENTO DO PROCESSO DE APRENDIZAGEM</a><?php } ?>



        <hr>

        <?php if ($totalRows_ListarDisciplinas > 0) { // Show if recordset not empty ?>
          <?php $totalCh = 0; ?>
          <table class="ls-table">

            <tr>
              <th><?php echo $totalRows_ListarDisciplinas ?>   <?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?>CAMPO(S)
                  DE EXPERIÊNCIA(S)<?php } else { ?>COMPONENTE(S) CURRICULAR(ES)<?php } ?></th>
              <th>EIXO</th>
              <th></th>
              <th class="ls-txt-center">C/H ANUAL</th>
              <th width="100">REPROVA</th>
              <th width="50"></th>
              <th width="50"></th>
            </tr>

            <?php do { ?>
              <tr>
                <td><span
                    style="color:<?php echo $row_ListarDisciplinas['disciplina_cor_fundo']; ?>;"><?php echo $row_ListarDisciplinas['disciplina_nome']; ?></span>
                </td>
                <td>
                  <span><?php if ($row_ListarDisciplinas['disciplina_eixo_id'] != '') {
                    echo $row_ListarDisciplinas['disciplina_eixo_nome'];
                  } ?></span>
                </td>
                <td><?php if ($row_Matriz['ca_questionario_conceitos'] == "S") { ?><a
                      href="questionario_conceitos.php?matriz=<?php echo $row_Matriz['matriz_hash']; ?>&comp=<?php echo $row_ListarDisciplinas['disciplina_id']; ?>"
                      class="ls-btn-primary ls-ico-plus ls-btn-xs">Questionário de conceitos</a><?php } else { ?><?php } ?>
                </td>
                <td class="ls-txt-center"><?php echo $row_ListarDisciplinas['matriz_disciplina_ch_ano']; ?></td>
                <td class="ls-txt-center"><?php echo $row_ListarDisciplinas['matriz_disciplina_reprova']; ?></td>
                <td><a
                    href="matriz_disciplina_editar.php?editar=<?php echo $row_ListarDisciplinas['matriz_disciplina_id']; ?>&hash=<?php echo $row_Matriz['matriz_hash']; ?>"
                    class="ls-ico-right ls-ico-edit-admin"> </a></td>

                <td><a href="javascript:func()"
                    onclick="confirmacao('hash=<?php echo $row_Matriz['matriz_hash']; ?>&cod=<?php echo $row_ListarDisciplinas['matriz_disciplina_id']; ?>', '<?php echo $row_ListarDisciplinas['disciplina_nome']; ?>')"
                    class="ls-ico-right ls-ico-cancel-circle"> </a></td>
              </tr>
              <?php $totalCh = $totalCh + $row_ListarDisciplinas['matriz_disciplina_ch_ano']; ?>
            <?php } while ($row_ListarDisciplinas = mysql_fetch_assoc($ListarDisciplinas)); ?>

            <tr>
              <td></td>
              <td class="ls-txt-center"><strong>CARGA HORÁRIA TOTAL ANUAL</strong></td>
              <td class="ls-txt-center"><strong><?php echo $totalCh; ?></strong></td>
              <td></td>
              <td></td>
            </tr>



          <?php } else { ?>
            Nenhuma disciplina cadastrada.
          <?php } // Show if recordset not empty ?>

        </table>
        <?php if (isset($_GET["deletado"])) { ?>
          <div class="ls-alert-warning ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            Excluido com sucesso! </div>
        <?php } ?>
        <p>&nbsp;</p>
      </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>


  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">INSERIR <?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?>CAMPO DE
            EXPERIÊNCIA<?php } else { ?>COMPONENTE CURRICULAR<?php } ?></h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">


        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">

          <label class="ls-label col-md-12">
            <b class="ls-label-text">
              <?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?>
                CAMPO DE EXPERIÊNCIA
              <?php } else { ?>
                COMPONENTE CURRICULAR
              <?php } ?>
            </b>
            <div class="ls-custom-select">
              <select class="select2" name="matriz_disciplina_id_disciplina" required>
                <option value="">--ESCOLHA</option>
                <?php do { ?>
                  <option value="<?php echo $row_Disciplinas['disciplina_id'] ?>">
                    <?php echo $row_Disciplinas['disciplina_nome'] ?>
                    (<?php echo $row_Disciplinas['area_conhecimento_nome'] ?>)
                  </option>
                <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
              </select>
            </div>
          </label>


          <label class="ls-label col-md-12">
            <b class="ls-label-text">EIXO</b>
            <div class="ls-custom-select">
              <select name="matriz_disciplina_eixo">
                <option value="">--ESCOLHA</option>
                <?php do { ?>
                  <option value="<?php echo $row_Eixos['disciplina_eixo_id'] ?>">
                    <?php echo $row_Eixos['disciplina_eixo_nome'] ?>
                  </option>
                <?php } while ($row_Eixos = mysql_fetch_assoc($Eixos)); ?>
              </select>
            </div>
            <p class="ls-label-info"> Caso não utilize, não é necessário selecionar um eixo.</p>
          </label>


          <label class="ls-label col-md-12">
            <b class="ls-label-text">C/H ANUAL (TOTAL)</b>

            <input type="text" value="" name="matriz_disciplina_ch_ano" required></input>

          </label>

          <label class="ls-label col-md-12">
            <b class="ls-label-text">Componente reprova? <input type="checkbox" name="matriz_disciplina_reprova"
                value="S" checked></b>

            <p class="ls-label-info"> Desmarque a opção se este Componente Curricular não reprova o Ano Letivo</p>
          </label>



          <input type="hidden" name="matriz_disciplina_id_matriz" value="<?php echo $row_Matriz['matriz_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">






      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</button>
        <button type="submit" class="ls-btn-primary">SALVAR</button>
        <a href="matriz_disciplina_personalizada.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>"
          class="ls-btn-primary ls-ico-plus">ADICIONAR CAMPO DE EXPERIÊNCIA PERSONALIZADO</a>
      </div>
      </form>
    </div>
  </div><!-- /.modal -->

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script language="Javascript">

    function confirmacao(id, nome) {
      var resposta = confirm("Remover a disciplina " + nome + " dessa Matriz Curricular?");

      if (resposta == true) {
        window.location.href = "matrizdisciplina.php?" + id;
      }
    }

    $(document).ready(function () {
      $('.select2').select2({
        width: '100%' // Garante que a largura seja ajustada corretamente
      });
    });

  </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Disciplinas);

mysql_free_result($Matriz);

mysql_free_result($ListarDisciplinas);
?>