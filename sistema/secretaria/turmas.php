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

// Adiciona filtro de localização
$filtro_localizacao = "";
if (isset($_GET['localizacao']) && $_GET['localizacao'] != "") {
  $filtro_localizacao = " AND escola_localizacao = '" . $_GET['localizacao'] . "'";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapa = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_id_filtro, etapa_nome, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma 
left JOIN smc_etapa ON etapa_id = turma_etapa
left JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND  turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'" . $filtro_localizacao . "
GROUP BY turma_etapa
";
$TurmasPorEtapa = mysql_query($query_TurmasPorEtapa, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa);
$totalRows_TurmasPorEtapa = mysql_num_rows($TurmasPorEtapa);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TurmasPorEtapaFiltro = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, etapa_id, etapa_id_filtro, etapa_nome, 
etapa_filtro_id, etapa_filtro_nome, COUNT(*) AS total, escola_id, escola_situacao 
FROM smc_turma 
LEFT JOIN smc_etapa ON etapa_id = turma_etapa
LEFT JOIN smc_etapa_filtro ON etapa_filtro_id = etapa_id_filtro
LEFT JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND  turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'" . $filtro_localizacao . "
GROUP BY etapa_filtro_id
";
$TurmasPorEtapaFiltro = mysql_query($query_TurmasPorEtapaFiltro, $SmecelNovo) or die(mysql_error());
$row_TurmasPorEtapaFiltro = mysql_fetch_assoc($TurmasPorEtapaFiltro);
$totalRows_TurmasPorEtapaFiltro = mysql_num_rows($TurmasPorEtapaFiltro);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_id_filtro, etapa_nome, COUNT(*) AS total, escola_id, escola_situacao, escola_localizacao, escola_inep, escola_nome 
FROM smc_turma 
left JOIN smc_etapa ON etapa_id = turma_etapa
left JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' AND turma_tipo_atendimento = '1'" . $filtro_localizacao . "
GROUP BY turma_id_escola
ORDER BY escola_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

// Atualiza a contagem de turmas por localização
$total_urbana = 0;
$total_campo = 0;
$total_geral = 0;

if ($totalRows_Turmas > 0) {
  do {
    if ($row_Turmas['escola_localizacao'] == 'U') {
      $total_urbana += $row_Turmas['total'];
    } else {
      $total_campo += $row_Turmas['total'];
    }
    $total_geral += $row_Turmas['total'];
  } while ($row_Turmas = mysql_fetch_assoc($Turmas));

  // Reset do ponteiro do resultado
  mysql_data_seek($Turmas, 0);
  $row_Turmas = mysql_fetch_assoc($Turmas);
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
      <h1 class="ls-title-intro ls-ico-home">TURMAS <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

      <a href="index.php" class="ls-btn-primary">VOLTAR</a>
      

      <div class="ls-box ls-md-margin-top">
        <form method="get" class="ls-form-horizontal">
          <label class="ls-label col-md-4">
            <b class="ls-label-text">FILTRAR POR LOCALIZAÇÃO</b>
            <div class="ls-custom-select">
              <select name="localizacao" class="ls-select" onchange="this.form.submit()">
                <option value="">TODAS AS ESCOLAS</option>
                <option value="U" <?php if (isset($_GET['localizacao']) && $_GET['localizacao'] == 'U')
                  echo 'selected'; ?>>ESCOLAS URBANAS</option>
                <option value="R" <?php if (isset($_GET['localizacao']) && $_GET['localizacao'] == 'R')
                  echo 'selected'; ?>>ESCOLAS DO CAMPO</option>
              </select>
            </div>
          </label>
          <?php if (isset($_GET['ano'])) { ?>
            <input type="hidden" name="ano" value="<?php echo $_GET['ano']; ?>">
          <?php } ?>
        </form>
      </div>

      <div class="ls-box ls-board-box">
        <div class="row">
          <div class="col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TOTAL DE TURMAS</h6>
              </div>
              <div class="ls-box-body">
                <span class="ls-board-data">
                  <strong class="ls-color-theme"><?php echo $total_geral; ?></strong>
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS URBANAS</h6>
              </div>
              <div class="ls-box-body">
                <span class="ls-board-data">
                  <strong class="ls-color-info"><?php echo $total_urbana; ?></strong>
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ls-box">
              <div class="ls-box-head">
                <h6 class="ls-title-4">TURMAS DO CAMPO</h6>
              </div>
              <div class="ls-box-body">
                <span class="ls-board-data">
                  <strong class="ls-color-success"><?php echo $total_campo; ?></strong>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong></p>
          <h2 class="ls-title-3">Turmas por Unidade Escolar</h2>
        </header>

        <?php if ($totalRows_Turmas > 0) { ?>
          <table class="ls-table ls-sm-space" width="100%">
            <thead>
              <tr>
                <th width="50"></th>
                <th width="100">INEP</th>
                <th>ESCOLA</th>
                <th>LOCALIZAÇÃO</th>
                <th class="ls-txt-center">TOTAL DE TURMAS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $n = 1;
              $total = 0;
              do { ?>
                <tr>
                  <td class="ls-txt-center"><strong><?php echo $n;
                  $n++; ?></strong></td>
                  <td><?php echo $row_Turmas['escola_inep']; ?></td>
                  <td><?php echo $row_Turmas['escola_nome']; ?></td>
                  <td><?php echo $row_Turmas['escola_localizacao'] == 'U' ? 'URBANA' : 'DO CAMPO'; ?></td>
                  <td class="ls-txt-center"><?php echo $row_Turmas['total']; ?></td>
                </tr>
                <?php $total = $total + $row_Turmas['total']; ?>
              <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
              <tr>
                <td></td>
                <td class=""><strong>TOTAL</strong></td>
                <td></td>
                <td></td>
                <td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
              </tr>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="ls-alert-info"><strong>Atenção:</strong> Sem turmas cadastradas.</div>
        <?php } ?>
      </div>

      <div class="ls-box ls-board-box">
        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong></p>
          <h2 class="ls-title-3">Turmas por Etapa de Ensino</h2>
        </header>

        <?php if ($totalRows_TurmasPorEtapa > 0) { ?>
          <table class="ls-table ls-sm-space" width="100%">
            <thead>
              <tr>
                <th width="50"></th>
                <th>ETAPA DE ENSINO</th>
                <th class="ls-txt-center">TOTAL DE TURMAS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $n = 1;
              $total = 0;
              while ($row_TurmasPorEtapa = mysql_fetch_assoc($TurmasPorEtapa)) { ?>
                <tr>
                  <td class="ls-txt-center"><strong><?php echo $n;
                  $n++; ?></strong></td>
                  <td><?php echo $row_TurmasPorEtapa['etapa_nome']; ?></td>
                  <td class="ls-txt-center"><a
                      href="turmas_etapa_ver.php?etapa_id=<?php echo $row_TurmasPorEtapa['etapa_id']; ?>" target="_blank"
                      rel="noopener noreferrer"><?php echo $row_TurmasPorEtapa['total']; ?></a></td>
                </tr>
                <?php $total = $total + $row_TurmasPorEtapa['total']; ?>
              <?php } ?>
              <tr>
                <td></td>
                <td class=""><strong>TOTAL</strong></td>
                <td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
              </tr>

            </tbody>
          </table>
        <?php } else { ?>
          <div class="ls-alert-info"><strong>Atenção:</strong> Sem turmas cadastradas.</div>
        <?php } ?>


      </div>


      <div class="ls-box ls-board-box">

        <header class="ls-info-header">
          <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em
            <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong></p>
          <h2 class="ls-title-3">Turmas por filtro de Etapa</h2>
        </header>

        <?php if ($totalRows_TurmasPorEtapaFiltro > 0) { ?>
          <table class="ls-table ls-sm-space" width="100%">
            <thead>
              <tr>
                <th width="50"></th>
                <th>FILTRO DE ETAPA</th>
                <th class="ls-txt-center">TOTAL DE TURMAS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $n = 1;
              $total = 0;
              while ($row_TurmasPorEtapaFiltro = mysql_fetch_assoc($TurmasPorEtapaFiltro)) { ?>
                <tr>
                  <td class="ls-txt-center"><strong><?php echo $n;
                  $n++; ?></strong></td>
                  <td><?php echo $row_TurmasPorEtapaFiltro['etapa_filtro_nome']; ?></td>
                  <td class="ls-txt-center"><?php echo $row_TurmasPorEtapaFiltro['total']; ?></td>
                </tr>
                <?php $total = $total + $row_TurmasPorEtapaFiltro['total']; ?>
              <?php } ?>
              <tr>
                <td></td>
                <td class=""><strong>TOTAL</strong></td>
                <td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
              </tr>

            </tbody>
          </table>
        <?php } else { ?>
          <div class="ls-alert-info"><strong>Atenção:</strong> Sem turmas cadastradas.</div>
        <?php } ?>



      </div>



    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($TurmasPorEtapa);

mysql_free_result($Turmas);
?>