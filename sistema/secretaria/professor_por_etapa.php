<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/anti_injection.php"; ?>
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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
    if ($_GET['ano'] == "") {
        header("Location: index.php?nada"); 
        exit;
    }
    $anoLetivo = anti_injection($_GET['ano']);
    $anoLetivo = (int)$anoLetivo;
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7)";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);
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
      <h1 class="ls-title-intro ls-ico-home">PROFESSOR POR ETAPA</h1>
      <!-- CONTEUDO -->

          <!-- CONTEUDO -->
    <div class="1ls-box">
        <div class="1ls-box-filter">
            <div class="ls-float-right">
                <a href="impressao/rel_professor_por_etapa.php?ano=<?php echo $anoLetivo; ?>" class="ls-btn" target="_blank" aria-expanded="false">IMPRIMIR</a>
            </div>
            <div class="ls-float-left">
                <form method="get" action="">
                    <label class="ls-label">
                        <b class="ls-label-text">ANO LETIVO</b>
                        <div class="ls-custom-select ls-field-lg">
                            <select name="ano" class="ls-select" onchange="this.form.submit()">
                                <option value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>" <?php if (!isset($_GET['ano']) || $_GET['ano'] == $row_AnoLetivo['ano_letivo_ano']) echo 'selected'; ?>>
                                    <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
                                </option>
                                <?php
                                // Resetar o ponteiro para o início
                                mysql_data_seek($Ano, 0);
                                while ($row_Ano = mysql_fetch_assoc($Ano)) { ?>
                                    <option value="<?php echo $row_Ano['ano_letivo_ano']; ?>" <?php if (isset($_GET['ano']) && $_GET['ano'] == $row_Ano['ano_letivo_ano']) echo 'selected'; ?>>
                                        <?php echo $row_Ano['ano_letivo_ano']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </label>
                </form>
            </div>
        </div>
    </div>


      <?php do { ?>

        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th>
                <?php echo $row_Etapas['etapa_nome_abrev']; ?> (<?php echo $row_Etapas['etapa_nome']; ?>)
              </th>
              <th>
                Turma
              </th>
              <th>
                Escola
              </th>
              <th>
                Telefone
              </th>
            </tr>
          </thead>

          <?php
          mysql_select_db($database_SmecelNovo, $SmecelNovo);
          $query_Vinculo = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
    turma_id, turma_nome, turma_etapa, turma_id_sec, turma_id_escola, turma_ano_letivo, func_id, func_nome,func_telefone, func_celular1,func_id_sec, escola_id, escola_id_sec, escola_nome 
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
    INNER JOIN smc_escola ON escola_id = turma_id_escola
    WHERE turma_etapa = '$row_Etapas[etapa_id]' AND turma_id_sec = '$row_UsuarioLogado[usu_sec]' AND turma_ano_letivo = '$anoLetivo'
    GROUP BY func_id
    ORDER BY func_nome ASC
    ";
          $Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
          $row_Vinculo = mysql_fetch_assoc($Vinculo);
          $totalRows_Vinculo = mysql_num_rows($Vinculo);
          ?>

          <tbody>

            <?php if ($totalRows_Vinculo > 0) { ?>
              <?php do { ?>

                <tr>
                  <td>- <?php echo $row_Vinculo['func_nome']; ?></td>
                  <td><?php echo $row_Vinculo['turma_nome'] ?></td>
                  <td><?php echo $row_Vinculo['escola_nome']; ?></td>
                  <td><?php echo $row_Vinculo['func_celular1']; ?></td>
                </tr>

              <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>

              <tr>
                <td class="ls-float-right">Total de profissionais vinculados nesta etapa:
                  <strong><?php echo $totalRows_Vinculo; ?></strong>
                </td>
              </tr>


            <?php } else { ?>
              <tr>
                <td>- Nenhum professor vinculado nesta etapa</td>
              </tr>
            <?php } ?>
          </tbody>



        </table>
      <?php } while ($row_Etapas = mysql_fetch_assoc($Etapas)); ?>


      <p>&nbsp;</p>
      <!-- CONTEUDO -->
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

mysql_free_result($Etapas);

mysql_free_result($Vinculo);
?>