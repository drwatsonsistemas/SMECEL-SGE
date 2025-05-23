<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
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

$query_Regimes = "SELECT id_regime, regime_nome FROM smc_regime";
$Regimes = mysql_query($query_Regimes, $SmecelNovo) or die(mysql_error());
$rowRegimes = mysql_fetch_assoc($Regimes);
$totalRows_Regimes = mysql_num_rows($Regimes);

$regimeQry = "";
$regimeTitulo = "";
$regime = "0";  // Sem aspas, como inteiro

if (isset($_GET['regime']) && $_GET['regime'] != 0) {
  $regime = intval(anti_injection($_GET['regime']));
  $regimeQry = " AND func_regime = $regime";
}

$funcaoQry = "";
$funcaoTitulo = "";
$funcao = "0";

if (isset($_GET['funcao'])) {

  if ($_GET['funcao'] == "D") {
    $funcaoQry = " AND funcao_docencia = 'S'";
    $funcaoTitulo = "";
    $funcao = "D";
  } else {
    $funcao = anti_injection($_GET['funcao']);

    $funcaoQry = " AND func_cargo = '$funcao'";
    $funcaoTitulo = "";
  }

  if ($funcao == "0") {
    $funcaoQry = "";
    $funcaoTitulo = "";
  }

}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FuncionariosGraduacao = "
SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, 
func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, 
func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, 
func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, 
func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, 
func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, 
func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, 
func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, 
func_senha, func_senha_ativa, func_carga_horaria_semanal, funcao_id, funcao_nome, funcao_docencia, id_regime, regime_nome,
CASE func_escolaridade 
WHEN 1 THEN 'ENSINO FUNDAMENTAL'
WHEN 2 THEN 'ENSINO MÉDIO'
WHEN 3 THEN 'GRADUAÇÃO'
WHEN 4 THEN 'PÓS-GRADUAÇÃO'
WHEN 5 THEN 'MESTRADO'
END AS func_escolaridade_nome 
FROM smc_func
INNER JOIN smc_funcao ON funcao_id = func_cargo
INNER JOIN smc_regime ON func_regime = id_regime
WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_situacao = '1' $regimeQry $funcaoQry
ORDER BY func_nome ASC
";
$FuncionariosGraduacao = mysql_query($query_FuncionariosGraduacao, $SmecelNovo) or die(mysql_error());
$row_FuncionariosGraduacao = mysql_fetch_assoc($FuncionariosGraduacao);
$totalRows_FuncionariosGraduacao = mysql_num_rows($FuncionariosGraduacao);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_funcao = "SELECT funcao_id, funcao_secretaria_id, funcao_nome, funcao_observacoes, funcao_docencia FROM smc_funcao WHERE funcao_secretaria_id = '$row_UsuarioLogado[usu_sec]' ORDER BY funcao_nome ASC";
$funcao = mysql_query($query_funcao, $SmecelNovo) or die(mysql_error());
$row_funcao = mysql_fetch_assoc($funcao);
$totalRows_funcao = mysql_num_rows($funcao);
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
      <h1 class="ls-title-intro ls-ico-home">FUNCIONÁRIOS POR REGIME/FUNÇÃO</h1>
      <!-- CONTEUDO -->


      <div class="ls-box-filter">
        <form action="" class="ls-form ls-form-inline">

          <label class="ls-label col-md-4 col-sm-12">
            <b class="ls-label-text">REGIME</b>
            <div class="ls-custom-select">
              <select name="regime" class="ls-select">
                <option value="0">TODOS</option>
                <?php do { ?>
                  <option value="<?php echo $rowRegimes['id_regime']; ?>"><?php echo $rowRegimes['regime_nome']; ?>
                  </option>

                <?php } while ($rowRegimes = mysql_fetch_assoc($Regimes)); ?>
              </select>
            </div>
          </label>

          <label class="ls-label col-md-4 col-sm-12">
            <b class="ls-label-text">FUNÇÃO</b>
            <div class="ls-custom-select">
              <select name="funcao" class="ls-select">
                <option value="0">TODOS</option>
                <option value="D">DOCENTES (Todos os funcionários na função de docência)</option>
                <?php do { ?>
                  <option value="<?php echo $row_funcao['funcao_id']; ?>"><?php echo $row_funcao['funcao_nome']; ?>
                  </option>
                <?php } while ($row_funcao = mysql_fetch_assoc($funcao)); ?>
              </select>
            </div>
          </label>



          <div class="ls-actions-btn">
            <input type="submit" value="Buscar" class="ls-btn-success" title="FILTRAR">
            <a href="impressao/rel_func_regime.php?regime=<?php echo $regime; ?>&funcao=<?php echo $_GET['funcao']; ?>"
              class="ls-btn" target="_blank">IMPRIMIR</a>
            <a href="rel_func_regime.php" class="ls-btn">LIMPAR</a>
          </div>

        </form>



      </div>








      <hr>
      <h3>RELAÇÃO DE SERVIDORES <?php echo $regimeTitulo; ?></h3>

      <?php if ($totalRows_FuncionariosGraduacao > 0) { // Show if recordset not empty ?>
        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th width="50"></th>
              <th>FUNCIONÁRIO</th>
              <th>FUNÇÃO</th>
              <th width="150">REGIME</th>
            </tr>
          </thead>
          <tbody>
            <?php $num = 1;
            do { ?>
              <tr>
                <td><?php echo $num;
                $num++ ?></td>
                <td><?php echo $row_FuncionariosGraduacao['func_id'] . " - " . $row_FuncionariosGraduacao['func_nome']; ?>
                </td>
                <td><?php echo $row_FuncionariosGraduacao['funcao_nome']; ?></td>
                <td><?php echo $row_FuncionariosGraduacao['regime_nome']; ?></td>
              </tr>
            <?php } while ($row_FuncionariosGraduacao = mysql_fetch_assoc($FuncionariosGraduacao)); ?>
          </tbody>
        </table>

        <hr>

        <p>TOTAL: <?php echo $totalRows_FuncionariosGraduacao; ?></p>

      <?php } // Show if recordset not empty ?>

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

mysql_free_result($funcao);

mysql_free_result($FuncionariosGraduacao);
?>