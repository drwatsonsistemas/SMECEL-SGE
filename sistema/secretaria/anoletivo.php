<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>


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





mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$aberto = "";
if (isset($_GET['aberto'])) {
  $aberto = $_GET['aberto'];
  switch ($aberto) {
    case "S":
      $aberto = "ano_letivo_aberto = 'S' AND ";
      break;
    case "N":
      $aberto = "ano_letivo_aberto = 'N' AND ";
      break;
    default:
      $aberto = "";
  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AnoLetivoExibe = "
SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula,
CASE ano_letivo_aberto
WHEN 'S' THEN '<span class=\"ls-tag-info\">ABERTO</span>'
WHEN 'N' THEN '<span class=\"ls-tag-warning\">FECHADO</span>'
END AS ano_letivo_situacao 
FROM smc_ano_letivo
WHERE $aberto ano_letivo_id_sec = '$row_Secretaria[sec_id]' 
ORDER BY ano_letivo_ano DESC";
$AnoLetivoExibe = mysql_query($query_AnoLetivoExibe, $SmecelNovo) or die(mysql_error());
$row_AnoLetivoExibe = mysql_fetch_assoc($AnoLetivoExibe);
$totalRows_AnoLetivoExibe = mysql_num_rows($AnoLetivoExibe);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibeAnos = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Secretaria[sec_id]' ORDER BY ano_letivo_ano DESC";
$ExibeAnos = mysql_query($query_ExibeAnos, $SmecelNovo) or die(mysql_error());
$row_ExibeAnos = mysql_fetch_assoc($ExibeAnos);
$totalRows_ExibeAnos = mysql_num_rows($ExibeAnos);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {


  $verifica = $_POST['ano_letivo_ano'];

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_ExibeAnosBusca = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_aberto, ano_letivo_id_sec, ano_letivo_data_rematricula FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_Secretaria[sec_id]' AND ano_letivo_id = '$verifica'";
  $ExibeAnosBusca = mysql_query($query_ExibeAnosBusca, $SmecelNovo) or die(mysql_error());
  $row_ExibeAnosBusca = mysql_fetch_assoc($ExibeAnosBusca);
  $totalRows_ExibeAnosBusca = mysql_num_rows($ExibeAnosBusca);

  if ($totalRows_ExibeAnosBusca > 0) {

    $updateSQL2 = sprintf(
      "UPDATE smc_ano_letivo SET ano_letivo_aberto='N' WHERE ano_letivo_id<>%s AND ano_letivo_id_sec = '$row_Secretaria[sec_id]'",
      GetSQLValueString($_POST['ano_letivo_ano'], "text"),
      GetSQLValueString($_POST['ano_letivo_id'], "int")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result2 = mysql_query($updateSQL2, $SmecelNovo) or die(mysql_error());


    $updateSQL = sprintf(
      "UPDATE smc_ano_letivo SET ano_letivo_aberto='S' WHERE ano_letivo_id=%s AND ano_letivo_id_sec = '$row_Secretaria[sec_id]'",
      GetSQLValueString($_POST['ano_letivo_ano'], "text"),
      GetSQLValueString($_POST['ano_letivo_id'], "int")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  }

  $updateGoTo = "anoletivo.php?alterado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_checaAno = "SELECT * FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$_POST[ano_letivo_id_sec]' AND ano_letivo_ano = '$_POST[ano_letivo_ano]'";
  $checaAno = mysql_query($query_checaAno, $SmecelNovo) or die(mysql_error());
  $row_checaAno = mysql_fetch_assoc($checaAno);
  $totalRows_checaAno = mysql_num_rows($checaAno);

  if ($totalRows_checaAno < 1) {

    if (isset($_POST['ano_letivo_aberto'])) {

      $mudaOutros = "UPDATE smc_ano_letivo SET ano_letivo_aberto='N' WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]'";
      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result2 = mysql_query($mudaOutros, $SmecelNovo) or die(mysql_error());

    }

    $insertSQL = sprintf(
      "INSERT INTO smc_ano_letivo (ano_letivo_ano, ano_letivo_inicio, ano_letivo_fim, ano_letivo_data_rematricula, ano_letivo_aberto, ano_letivo_id_sec) VALUES (%s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['ano_letivo_ano'], "text"),
      GetSQLValueString($_POST['ano_letivo_inicio'], "date"),
      GetSQLValueString($_POST['ano_letivo_fim'], "date"),
      GetSQLValueString($_POST['ano_letivo_data_rematricula'], "date"),
      GetSQLValueString(isset($_POST['ano_letivo_aberto']) ? "true" : "", "defined", "'S'", "'N'"),
      GetSQLValueString($_POST['ano_letivo_id_sec'], "int")
    );


    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());




    $insertGoTo = "anoletivo.php?cadastrado";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));

  } else {

    header("Location: anoletivo.php?existe");

  }



}
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

  <script src="js/locastyle.js"></script>

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top.php"); ?>

  <?php include_once "menu.php"; ?>

  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>


      <div class="ls-box">
        <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-inline">

          <label class="ls-label col-md-4">
            <div class="ls-custom-select">
              <select name="ano_letivo_ano" class="ls-select" required>
                <option value="">ESCOLHA</option>
                <?php do { ?>
                  <option value="<?php echo $row_ExibeAnos['ano_letivo_id'] ?>" <?php if (!(strcmp($row_ExibeAnos['ano_letivo_id'], htmlentities($row_ExibeAnos['ano_letivo_ano'], ENT_COMPAT, 'utf-8')))) {
                       echo "SELECTED";
                     } ?>><?php echo $row_ExibeAnos['ano_letivo_ano'] ?></option>
                <?php } while ($row_ExibeAnos = mysql_fetch_assoc($ExibeAnos)); ?>
              </select>
            </div>
          </label>

          <input type="submit" value="ALTERAR" class="ls-btn-primary">
          <input type="hidden" name="MM_update" value="form2">
          <input type="hidden" name="ano_letivo_id" value="<?php echo $row_ExibeAnos['ano_letivo_id']; ?>">
        </form>

      </div>

      <div class="ls-box ls-board-box">

        <?php if ($row_Secretaria['sec_aviso_bloqueio'] != "S") { ?>
          <button data-ls-module="modal" data-target="#modalLarge" class="ls-btn-primary ls-ico-plus">CADASTRAR ANO
            LETIVO</button>

        <?php } else { ?>
          <div class="ls-alert-danger">
            <strong>Atenção:</strong> A criação de um novo ano letivo só é permitida após a renovação do contrato.
          </div>
        <?php } ?>
        <p>&nbsp;</p>
        <div class="ls-group-btn ls-group-active ls-float-right">
          <a href="anoletivo.php" class="ls-btn-primary <?php if ($aberto == "") { ?> ls-active<?php } ?>">TODOS</a>
          <a href="anoletivo.php?aberto=S"
            class="ls-btn-primary <?php if (isset($_GET['aberto']) && $_GET['aberto'] == "S") { ?> ls-active<?php } ?>">ABERTO</a>
          <a href="anoletivo.php?aberto=N"
            class="ls-btn-primary <?php if (isset($_GET['aberto']) && $_GET['aberto'] == "N") { ?> ls-active<?php } ?>">FECHADO</a>
        </div>

        <?php if ($totalRows_AnoLetivoExibe > 0) { // Show if recordset not empty ?>
          <table class="ls-table">
            <thead>
              <tr>
                <th class="ls-txt-center">ANO LETIVO</th>
                <th class="ls-txt-center">INÍCIO</th>
                <th class="ls-txt-center">TÉRMINO</th>
                <th class="ls-txt-center">SITUAÇÃO</th>
                <th class="ls-txt-center">PERÍODOS/UNIDADES</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <?php $contAnoLetivo = 0; ?>
            <?php do { ?>
              <tr class="<?php if ($row_AnoLetivoExibe['ano_letivo_aberto'] == "N") { ?> ls-background-danger<?php } ?>">
                <td class="ls-txt-center"><?php echo $row_AnoLetivoExibe['ano_letivo_ano']; ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_AnoLetivoExibe['ano_letivo_inicio']); ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_AnoLetivoExibe['ano_letivo_fim']); ?></td>
                <td
                  class="ls-txt-center<?php if ($row_AnoLetivoExibe['ano_letivo_aberto'] == "N") { ?> ls-background-danger<?php } ?>">
                  <?php echo $row_AnoLetivoExibe['ano_letivo_situacao']; ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Secretaria['sec_aviso_bloqueio'] != "S") { ?>
                    <a href="anoletivo_editar_periodos.php?codigo=<?php echo $row_AnoLetivoExibe['ano_letivo_id']; ?>"
                      class="ls-ico-plus"></a>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Secretaria['sec_aviso_bloqueio'] != "S") { ?>
                    <a href="anoletivo_editar.php?codigo=<?php echo $row_AnoLetivoExibe['ano_letivo_id']; ?>"
                      class="ls-ico-edit-admin ">Editar</a>
                  </td>
                <?php } ?>
                <td class="ls-txt-center"><a
                    href="anos_anteriores.php?ano=<?php echo $row_AnoLetivoExibe['ano_letivo_ano']; ?>"
                    class="ls-ico-eye ">Ver números</a></td>
              </tr>
              <?php
              if ($row_AnoLetivoExibe['ano_letivo_aberto'] == "S") {
                $contAnoLetivo++;
              }
              ?>
            <?php } while ($row_AnoLetivoExibe = mysql_fetch_assoc($AnoLetivoExibe)); ?>
          </table>

          <?php if ($contAnoLetivo > 1) { ?>
            <div class="ls-alert-warning ls-dismissable">
              <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              <strong>Atenção: </strong> Você tem <strong><?php echo $contAnoLetivo; ?></strong> Anos Letivos abertos no
              sistema. Deixe apenas um!
            </div>
          <?php } ?>


          <?php if (isset($_GET["existe"])) { ?>
            <div class="ls-alert-warning ls-dismissable">
              <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              <strong>Atenção: </strong> Já existe um Ano Letivo com este ano cadastrado!
            </div>
          <?php } ?>


        <?php } else { ?>

          <hr>
          <div class="ls-alert-warning ls-dismissable">
            <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            <strong>Atenção: </strong>Nenhum registro de Ano Letivo encontrado!
          </div>

        <?php } // Show if recordset not empty ?>


        <div class="ls-modal" id="modalLarge">
          <div class="ls-modal-large">
            <div class="ls-modal-header">
              <button data-dismiss="modal">&times;</button>
              <h4 class="ls-modal-title">CADASTRAR NOVO ANO LETIVO</h4>
            </div>
            <div class="ls-modal-body">
              <p>


              <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">

                <label class="ls-label col-md-12">
                  <b class="ls-label-text">ANO LETIVO</b>
                  <p class="ls-label-info">Informe o ano no formato AAAA</p>
                  <div class="ls-custom-select">
                    <select name="ano_letivo_ano" required>
                      <option value="<?php echo date("Y") + 3; ?>"><?php echo date("Y") + 3; ?></option>
                      <option value="<?php echo date("Y") + 2; ?>"><?php echo date("Y") + 2; ?></option>
                      <option value="<?php echo date("Y") + 1; ?>" selected><?php echo date("Y") + 1; ?></option>
                      <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                      <option value="<?php echo date("Y") - 1; ?>"><?php echo date("Y") - 1; ?></option>
                      <option value="<?php echo date("Y") - 2; ?>"><?php echo date("Y") - 2; ?></option>
                      <option value="<?php echo date("Y") - 3; ?>"><?php echo date("Y") - 3; ?></option>
                    </select>
                  </div>
                </label>

                <label class="ls-label col-md-4">
                  <b class="ls-label-text">INÍCIO DO ANO LETIVO</b>
                  <span data-ls-module="popover"
                    data-content="Escolha o período desejado e clique em 'Filtrar'."></span>
                  <input type="date" name="ano_letivo_inicio" class="" id="" autocomplete="off" required>
                </label>


                <label class="ls-label col-md-4">
                  <b class="ls-label-text">TÉRMINO DO ANO LETIVO</b>
                  <span data-ls-module="popover"
                    data-content="Clique em 'Filtrar' para exibir  o período selecionado."></span>
                  <input type="date" name="ano_letivo_fim" class="" id="" autocomplete="off" required>
                </label>

                <label class="ls-label col-md-4">
                  <b class="ls-label-text">INÍCIO REMATRÍCULA</b>
                  <span data-ls-module="popover"
                    data-content="Clique em 'Filtrar' para exibir  o período selecionado."></span>
                  <input type="date" name="ano_letivo_data_rematricula" class="" id="" autocomplete="off" required>
                </label>


                <label class="ls-label col-md-12">
                  <b class="ls-label-text">ANO LETIVO ABERTO</b>
                  <input type="checkbox" name="ano_letivo_aberto" value="" checked>
                  <p class="ls-label-info">Marque se já quiser iniciar o Ano Letivo no Sistema</p>
                </label>




                </p>
            </div>
            <div class="ls-modal-footer">
              <a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
              <button type="submit" class="ls-btn-primary">SALVAR</button>
            </div>

            <input type="hidden" name="ano_letivo_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
            <input type="hidden" name="MM_insert" value="form1">
            </form>


          </div>
        </div>




        <p>&nbsp;</p>
      </div>
    </div>
  </main>

  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="../js/jquery.mask.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
  <script src="js/mascara.js"></script>
  <script src="js/maiuscula.js"></script>
  <script src="js/semAcentos.js"></script>
  <script>
    $(".datepicker").datepicker({
      startView: 1,
      minViewMode: 1,
      maxViewMode: 3,
      format: "dd/mm/yyyy",
      changeMonth: true,
      changeYear: true
    });
  </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($ExibeAnos);

mysql_free_result($AnoLetivo);
?>