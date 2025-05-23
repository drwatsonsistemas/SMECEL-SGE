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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf(
    "INSERT INTO smc_usu_escolas (usu_escola_id_usu, usu_escola_id_escola) VALUES (%s, %s)",
    GetSQLValueString($_POST['usu_escola_id_usu'], "int"),
    GetSQLValueString($_POST['usu_escola_id_escola'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "usuario_incluir_escola.php?vinculado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Usuario = "-1";
if (isset($_GET['codigo'])) {
  $colname_Usuario = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Usuario = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_sec = '$row_UsuarioLogado[usu_sec]' AND usu_id = %s", GetSQLValueString($colname_Usuario, "int"));
$Usuario = mysql_query($query_Usuario, $SmecelNovo) or die(mysql_error());
$row_Usuario = mysql_fetch_assoc($Usuario);
$totalRows_Usuario = mysql_num_rows($Usuario);

if ($totalRows_Usuario < 1 || $row_Usuario['usu_tipo'] <> 2) {
  $redireciona = "index.php?erro";
  header(sprintf("Location: %s", $redireciona));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaEscolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY escola_nome ASC";
$ListaEscolas = mysql_query($query_ListaEscolas, $SmecelNovo) or die(mysql_error());
$row_ListaEscolas = mysql_fetch_assoc($ListaEscolas);
$totalRows_ListaEscolas = mysql_num_rows($ListaEscolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolasVinculadas = "
SELECT usu_escola_id, usu_escola_id_usu, usu_escola_id_escola, escola_id, escola_nome
FROM smc_usu_escolas
INNER JOIN smc_escola ON escola_id = usu_escola_id_escola
WHERE usu_escola_id_usu = '$row_Usuario[usu_id]'
";
$EscolasVinculadas = mysql_query($query_EscolasVinculadas, $SmecelNovo) or die(mysql_error());
$row_EscolasVinculadas = mysql_fetch_assoc($EscolasVinculadas);
$totalRows_EscolasVinculadas = mysql_num_rows($EscolasVinculadas);
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
      <h1 class="ls-title-intro ls-ico-home">VINCULAR ESCOLA</h1>
      <div class="ls-box ls-board-box">
        <!-- CONTEUDO -->


        <h3><small>Usuário: </small><br><?php echo $row_Usuario['usu_nome']; ?>
          (<?php echo $row_Usuario['usu_email']; ?>)</h3>

        <hr>


        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">

          <label class="ls-label col-md-12">
            <b class="ls-label-text">ESCOLA</b>
            <p class="ls-label-info">Vincule o usuário à escola/setor abaixo:</p>
            <div class="ls-custom-select">

              <select name="usu_escola_id_escola" required>
                <option value="">Escolha...</option>
                <?php do { ?>
                  <option value="<?php echo $row_ListaEscolas['escola_id'] ?>">
                    <?php echo $row_ListaEscolas['escola_nome'] ?>
                  </option>
                <?php } while ($row_ListaEscolas = mysql_fetch_assoc($ListaEscolas)); ?>
              </select>

            </div>
          </label>

          <label class="ls-label col-md-12">
            <input type="submit" value="VINCULAR" class="ls-btn-primary">
            <a href="usuarios.php" class="ls-btn">Voltar</a>
          </label>

          <input type="hidden" name="usu_escola_id_usu" value="<?php echo $row_Usuario['usu_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
        </form>
        <p>&nbsp;</p>

        <?php if (isset($_GET["deletado"])) { ?>
          <div class="ls-alert-warning ls-dismissable">
            <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            Vínculo excluído com sucesso.
          </div>
        <?php } ?>

        <div class="ls-box1"> ESCOLAS VINCULADAS PARA ESTE USUÁRIO
          <?php if ($totalRows_EscolasVinculadas > 0) { // Show if recordset not empty ?>
            <table border="0" class="ls-table ls-sm-space">
              <thead>
                <tr>
                  <th>ESCOLA</th>
                  <th width="40"></th>
                </tr>
              </thead>
              <tbody>
                <?php do { ?>
                  <tr>
                    <td><?php echo $row_EscolasVinculadas['escola_nome']; ?></td>
                    <td>

                      <a href="javascript:func()"
                        onclick="confirmaExclusao('<?php echo $colname_Usuario; ?>','<?php echo $row_EscolasVinculadas['usu_escola_id']; ?>')"
                        class="ls-ico-remove ls-color-danger"></a>


                    </td>
                  </tr>
                <?php } while ($row_EscolasVinculadas = mysql_fetch_assoc($EscolasVinculadas)); ?>
              </tbody>
            </table>
          <?php } else { ?>
            <hr>
            <i>Nenhuma escola vinculada.</i>

          <?php } // Show if recordset not empty ?>
        </div>

        <!-- CONTEUDO -->
      </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/locastyle.js"></script>

  <script language="Javascript">

    window.onload = function () {
      if (!localStorage.getItem('popupRecomendacaoExibido')) {
        Swal.fire({
          title: 'Dica Rápida',
          html: 'Não sabe como alterar a escola principal do usuário? <a href="https://www.youtube.com/watch?v=OZIuJiRPi9k" target="_blank">Assista aqui</a>',
          icon: 'info',
          position: 'top-end',
          toast: true,
          showConfirmButton: false,
          showCloseButton: true,
          timer: 5000,
          timerProgressBar: true,
          didClose: () => {
            localStorage.setItem('popupRecomendacaoExibido', 'true');
          }
        });
      }
    };

    function confirmaExclusao(codigo, escola) {
      var resposta = confirm("Deseja realmente remover este vínculo?");
      if (resposta == true) {
        window.location.href = "usuario_escola_excluir.php?codigo=" + codigo + "&escola=" + escola;
      }
    }
  </script>

</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Usuario);

mysql_free_result($ListaEscolas);

mysql_free_result($EscolasVinculadas);
?>