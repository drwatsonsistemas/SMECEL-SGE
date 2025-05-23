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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Registros = 100;
$pageNum_Registros = 0;
if (isset($_GET['pageNum_Registros'])) {
  $pageNum_Registros = $_GET['pageNum_Registros'];
}
$startRow_Registros = $pageNum_Registros * $maxRows_Registros;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Registros = "
SELECT registros_id, registros_id_escola, registros_id_usuario, registros_tipo, registros_complemento, registros_data_hora, escola_id, escola_nome, escola_id_sec, usu_id, usu_nome 
FROM smc_registros
INNER JOIN smc_escola ON escola_id = registros_id_escola 
INNER JOIN smc_usu ON usu_id = registros_id_usuario
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]'
ORDER BY registros_id DESC";
$query_limit_Registros = sprintf("%s LIMIT %d, %d", $query_Registros, $startRow_Registros, $maxRows_Registros);
$Registros = mysql_query($query_limit_Registros, $SmecelNovo) or die(mysql_error());
$row_Registros = mysql_fetch_assoc($Registros);

if (isset($_GET['totalRows_Registros'])) {
  $totalRows_Registros = $_GET['totalRows_Registros'];
} else {
  $all_Registros = mysql_query($query_Registros);
  $totalRows_Registros = mysql_num_rows($all_Registros);
}
$totalPages_Registros = ceil($totalRows_Registros / $maxRows_Registros) - 1;

$queryString_Registros = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (
      stristr($param, "pageNum_Registros") == false &&
      stristr($param, "totalRows_Registros") == false
    ) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Registros = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Registros = sprintf("&totalRows_Registros=%d%s", $totalRows_Registros, $queryString_Registros);


function registros($cod)
{

  switch ($cod) {
    case '1';
      $situacao = 'ATUALIZOU OS DADOS DA UNIDADE ESCOLAR';
      break;
    case '2';
      $situacao = 'ATUALIZOU OS DADOS DE USUÁRIO';
      break;
    case '3';
      $situacao = 'CADASTROU UM NOVO OFÍCIO';
      break;
    case '4';
      $situacao = 'EDITOU UM OFÍCIO';
      break;
    case '5';
      $situacao = 'EDITOU UMA AULA NA GRADE DE HORÁRIOS';
      break;
    case '6';
      $situacao = 'CADASTROU UMA AULA NA GRADE DE HORÁRIOS';
      break;
    case '7';
      $situacao = 'EXCLUIU UMA AULA NA GRADE DE HORÁRIO';
      break;
    case '8';
      $situacao = 'CADASTROU UM ALUNO NO SISTEMA';
      break;
    case '9';
      $situacao = 'VINCULOU UM ALUNO NO SISTEMA';
      break;
    case '10';
      $situacao = 'EDITOU OS DADOS DE UM ALUNO';
      break;
    case '11';
      $situacao = 'CADASTROU/GEROU BOLETIM DE ALUNO';
      break;
    case '12';
      $situacao = 'EDITOU AS NOTAS NO BOLETIM';
      break;
    case '13';
      $situacao = 'EDITOU NOTA INDIVIDUAL NO BOLETIM';
      break;
    case '14';
      $situacao = 'INSERIU UMA NOVA DISCIPLINA NO BOLETIM';
      break;
    case '15';
      $situacao = 'EXCLUIU UMA DISCIPLINA NO BOLETIM';
      break;
    case '16';
      $situacao = 'EDITOU O VÍNCULO DO ALUNO';
      break;
    case '17';
      $situacao = 'EXCLUIU O VÍNCULO DO ALUNO';
      break;
    case '18';
      $situacao = 'CADASTROU A OCORRÊNCIA DE UM ALUNO';
      break;
    case '19';
      $situacao = 'EXCLUIU A OCORRÊNCIA DE UM ALUNO';
      break;
    case '20';
      $situacao = 'CADASTROU UMA TURMA';
      break;
    case '21';
      $situacao = 'EDITOU UMA TURMA';
      break;
    case '22';
      $situacao = 'EXCLUIU UMA TURMA';
      break;
    case '23';
      $situacao = 'VINCULOU UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
      break;
    case '24';
      $situacao = 'EDITOU O VÍNCULO DE UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
      break;
    case '25';
      $situacao = 'EXCLUIU O VÍNCULO DE UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
      break;
  }

  echo $situacao;
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
      <h1 class="ls-title-intro ls-ico-home">REGISTRO DE ATIVIDADES</h1>
      <div class="ls-box ls-board-box">
        <!-- CONTEUDO -->


        <?php if ($totalRows_Registros > 0) { // Show if recordset not empty ?>
          <div class="ls-group-btn">



            <table border="0">
              <tr>
                <td width="100"><?php if ($pageNum_Registros > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Registros=%d%s", $currentPage, 0, $queryString_Registros); ?>"
                      class="ls-btn">Primeiro</a>
                  <?php } // Show if not first page ?>
                </td>
                <td width="100"><?php if ($pageNum_Registros > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_Registros=%d%s", $currentPage, max(0, $pageNum_Registros - 1), $queryString_Registros); ?>"
                      class="ls-btn">Anterior</a>
                  <?php } // Show if not first page ?>
                </td>
                <td width="100"><?php if ($pageNum_Registros < $totalPages_Registros) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Registros=%d%s", $currentPage, min($totalPages_Registros, $pageNum_Registros + 1), $queryString_Registros); ?>"
                      class="ls-btn">Pr&oacute;ximo</a>
                  <?php } // Show if not last page ?>
                </td>
                <td width="100"><?php if ($pageNum_Registros < $totalPages_Registros) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_Registros=%d%s", $currentPage, $totalPages_Registros, $queryString_Registros); ?>"
                      class="ls-btn">&Uacute;ltimo</a>
                  <?php } // Show if not last page ?>
                </td>

                <td style="text-align: end;">
                  <a href="#" id="atividades-periodo" class="ls-btn">IMPRIMIR</a>
                </td>

              </tr>
            </table>

          </div>


          <table width="100%" class="ls-table ls-sm-space ls-table-striped ls-bg-header">
            <thead>
              <tr>
                <th align="center" width="150">DATA/HORA</th>
                <th align="center">USUÁRIO</th>
                <th align="center">DETALHES</th>
              </tr>
            </thead>
            <tbody>
              <?php do { ?>
                <tr>
                  <td align="center"><?php echo date("H\hi - d/m/Y", strtotime($row_Registros['registros_data_hora'])); ?>
                  </td>
                  <td><a
                      href="registros_usuario.php?usuario=<?php echo $row_Registros['usu_id']; ?>"><strong><?php echo $row_Registros['usu_nome']; ?></strong></a><br><?php echo $row_Registros['escola_nome']; ?>
                  </td>
                  <td align="center">
                    <strong><?php echo registros($row_Registros['registros_tipo']); ?></strong><br><?php echo $row_Registros['registros_complemento']; ?>
                  </td>
                </tr>
              <?php } while ($row_Registros = mysql_fetch_assoc($Registros)); ?>
            </tbody>
          </table>
          <hr>

          Registros <?php echo ($startRow_Registros + 1) ?> a
          <?php echo min($startRow_Registros + $maxRows_Registros, $totalRows_Registros) ?> de
          <?php echo $totalRows_Registros ?>
        <?php } else { ?>

          <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum registro encontrado.</div>

        <?php } // Show if recordset not empty ?>
        <!-- CONTEUDO -->
      </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.getElementById("atividades-periodo").addEventListener("click", async function () {
        const { value } = await Swal.fire({
          title: "Selecione o período",
          html: `
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label for="start-date" style="width: 40%;">Data de Início:</label>
            <input type="date" id="start-date" class="swal2-input" style="width: 55%;" required>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label for="end-date" style="width: 40%;">Data de Fim:</label>
            <input type="date" id="end-date" class="swal2-input" style="width: 55%;" required>
          </div>
        </div>
      `,
          focusConfirm: false,
          showCancelButton: true,
          preConfirm: () => {
            const startDate = document.getElementById("start-date").value;
            const endDate = document.getElementById("end-date").value;
            if (!startDate || !endDate) {
              Swal.showValidationMessage("Preencha ambas as datas.");
              return false;
            }
            if (endDate < startDate) {
              Swal.showValidationMessage("A data final não pode ser menor que a inicial.");
              return false;
            }
            return { startDate, endDate };
          }
        });

        if (value) {
          window.open(`impressao/registros_imprimir.php?inicio=${value.startDate}&fim=${value.endDate}`, "_blank");
        }
      });
    });
  </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Registros);
?>