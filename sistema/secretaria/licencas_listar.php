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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_tipo_licenca = "SELECT licenca_id, licenca_nome, licenca_obs FROM smc_licenca_tipo";
$tipo_licenca = mysql_query($query_tipo_licenca, $SmecelNovo) or die(mysql_error());
$row_tipo_licenca = mysql_fetch_assoc($tipo_licenca);
$totalRows_tipo_licenca = mysql_num_rows($tipo_licenca);

$filtro = "";
$textoLicenca = "";

// Verificando e validando 'concluido'
if (isset($_GET['concluido'])) {
  $cod = strtoupper(trim($_GET['concluido'])); // Padronizando em maiúsculas e removendo espaços

  switch ($cod) {
    case "S":
      $filtro .= " AND lancamento_retorno = 'S' ";
      $textoLicenca = "(CONCLUÍDAS)";
      break;
    case "N":
      $filtro .= " AND lancamento_retorno = 'N' ";
      $textoLicenca = "(EM ABERTO)";
      break;
    default:
      // Condição para mostrar todos os status de conclusão
      $filtro .= " AND (lancamento_retorno = 'N' OR lancamento_retorno = 'S') ";
      $textoLicenca = "(TODAS)";
      break;
  }
}

// Verificando e validando 'tipo', aplicando apenas se estiver definido e não vazio
if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
  $tipo = mysql_real_escape_string(trim($_GET['tipo'])); // Escapando entrada para evitar SQL injection
  $filtro .= " AND lancamento_tipo = '$tipo' ";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Montagem da query
$query_listarLicenca = "
SELECT 
  lancamento_id, lancamento_id_funcionario, lancamento_tipo, lancamento_data_saida, lancamento_data_retorno, lancamento_observacoes, lancamento_retorno, 
  func_id, func_nome, func_id_sec, 
  licenca_id, licenca_nome 
FROM smc_licenca 
INNER JOIN smc_func ON func_id = lancamento_id_funcionario 
INNER JOIN smc_licenca_tipo ON lancamento_tipo = licenca_id
WHERE func_id_sec = '".mysql_real_escape_string($row_Secretaria['sec_id'])."' $filtro
ORDER BY func_nome ASC";

// Executando a query
$listarLicenca = mysql_query($query_listarLicenca, $SmecelNovo) or die(mysql_error());

// Capturando os resultados
$row_listarLicenca = mysql_fetch_assoc($listarLicenca);
$totalRows_listarLicenca = mysql_num_rows($listarLicenca);



function dias($d1, $d2)
{
  $d1 = (is_string($d1) ? strtotime($d1) : $d1);
  $d2 = (is_string($d2) ? strtotime($d2) : $d2);
  $diff_secs = abs($d1 - $d2);
  return floor($diff_secs / (3600 * 24));
}


if ((isset($_GET['licenca'])) && ($_GET['licenca'] != "") && ($_GET['cod'] == $row_listarLicenca['func_id_sec'])) {

  $deleteSQL = sprintf(
    "DELETE FROM smc_licenca WHERE lancamento_id=%s",
    GetSQLValueString($_GET['licenca'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "licencas_listar.php?licencaDeletada";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$concluido = "";
if(isset($_GET['concluido'])) {
  $concluido = $_GET['concluido'];
}

$tipo = "";
if(isset($_GET['tipo'])){
  $tipo = $_GET['tipo'];
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
      <h1 class="ls-title-intro ls-ico-home">RELAÇÃO DE LICENÇAS</h1>
      <!-- CONTEUDO -->

      <div class="ls-box-filter">
        <form action="licencas_listar.php" class="ls-form ls-form-inline">

          <label class="ls-label col-md-3 col-sm-12">
            <b class="ls-label-text">Situação</b>
            <div class="ls-custom-select">
              <select name="concluido" id="concluido" class="ls-select">
                <option value="">Escolha</option>
                <option value="N" <?php if(isset($_GET['concluido'])&&$_GET['concluido'] == 'N'){echo 'selected';}?>>Em conclusão</option>
                <option value="S" <?php if(isset($_GET['concluido'])&&$_GET['concluido'] == 'S'){echo 'selected';}?>>Concluído</option>
              </select>
            </div>
          </label>

          <label class="ls-label col-md-3 col-sm-12">
            <b class="ls-label-text">Tipo</b>
            <div class="ls-custom-select">
              <select name="tipo" id="tipo" class="ls-select">
                <option value="">Escolha</option>


                <?php do { ?>
                  <option value="<?php echo $row_tipo_licenca['licenca_id']; ?>">
                    <?php echo $row_tipo_licenca['licenca_nome']; ?>
                  </option>
                <?php } while ($row_tipo_licenca = mysql_fetch_assoc($tipo_licenca)); ?>




              </select>
            </div>
          </label>






          <label class="ls-label col-md-3 col-sm-12">
            <input type="submit" class="ls-btn-primary" value="Filtrar">
            <a href="licencas_listar.php" class="ls-btn">Limpar</a>
          </label>

          <label class="ls-label col-md-3 col-sm-12">
            <a target="_blank" href="impressao/licencas_listar.php?concluido=<?= $concluido ?>&tipo=<?= $tipo ?>" class="ls-btn" style="float:right">Imprimir</a>
          </label>
        </form>

      </div>

      <?php if ($totalRows_listarLicenca > 0) { // Show if recordset not empty ?>
        <table border="0" width="100%" class="ls-table">
          <thead>
            <tr>
              <th>NOME</th>
              <th>TIPO LICENÇA</th>
              <th class="ls-txt-center" width="120">SAÍDA</th>
              <th class="ls-txt-center" width="120">RETORNO</th>
              <th class="ls-txt-center" width="100">DIAS</th>
              <th class="ls-txt-center" width="120">CONCLUÍDO</th>
              <th width="160" class=""></th>
            </tr>
          </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td><?php echo $row_listarLicenca['func_nome']; ?></td>
                <td><?php echo $row_listarLicenca['licenca_nome']; ?></td>
                <td class="ls-txt-center">
                  <?php echo date("d/m/Y", strtotime($row_listarLicenca['lancamento_data_saida'])); ?>
                </td>
                <td class="ls-txt-center">
                  <?php echo date("d/m/Y", strtotime($row_listarLicenca['lancamento_data_retorno'])); ?>
                </td>
                <td class="ls-txt-center">
                  <?php
                  echo dias($row_listarLicenca['lancamento_data_saida'], $row_listarLicenca['lancamento_data_retorno'])
                    ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_listarLicenca['lancamento_retorno'] == "S") { ?>SIM<?php } else { ?>NÃO<?php } ?>
                  <?php if (($row_listarLicenca['lancamento_data_retorno'] < date('Y-m-d')) && $row_listarLicenca['lancamento_retorno'] == "N") {
                    echo "<br><a href=\"#\" class=\"ls-tag-danger\">VENCEU</a>";
                  } ?>
                </td>
                <td class="ls-txt-center">
                  <a href="licenca_editar.php?licenca=<?php echo $row_listarLicenca['lancamento_id']; ?>"
                    class="ls-btn ls-ico-edit-admin ls-btn-xs"></a>
                  <a class="ls-btn ls-ico-remove ls-btn-xs" href="javascript:func()"
                    onclick="confirmaExclusaoLicenca('<?php echo $row_listarLicenca['func_id_sec']; ?>','<?php echo $row_listarLicenca['lancamento_id']; ?>')"></a>
                </td>
              </tr>
            <?php } while ($row_listarLicenca = mysql_fetch_assoc($listarLicenca)); ?>
          </tbody>
        </table>
      <?php } else { ?>

        <p>Nenhuma licença/afastamento cadastrado.</p>

      <?php } // Show if recordset not empty ?>



      <p>&nbsp;</p>
      <!-- CONTEUDO -->
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

  <script language="Javascript">
    function confirmaExclusaoLicenca(funcionario, licenca) {
      var resposta = confirm("Deseja realmente remover esse ítem?");
      if (resposta == true) {
        window.location.href = "licencas_listar.php?cod=" + funcionario + "&licenca=" + licenca;
      }
    }
  </script>

</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($tipo_licenca);
?>