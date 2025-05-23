<?php require_once('../../../Connections/SmecelNovo.php'); ?>
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

require_once('../funcoes/usuLogado.php');
require_once('../funcoes/anoLetivo.php');

// Recuperando os parâmetros do período da URL
$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : null;
$fim = isset($_GET['fim']) ? $_GET['fim'] : null;

if (!$inicio || !$fim) {
    die(header("Location: ../registros.php?nada"));
}

$inicio = mysql_real_escape_string($inicio);
$fim = mysql_real_escape_string($fim);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Registros = "
SELECT registros_id, registros_id_escola, registros_id_usuario, registros_tipo, registros_complemento, registros_data_hora, escola_id, escola_nome, escola_id_sec, usu_id, usu_nome 
FROM smc_registros
INNER JOIN smc_escola ON escola_id = registros_id_escola 
INNER JOIN smc_usu ON usu_id = registros_id_usuario
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]'
AND registros_data_hora BETWEEN '$inicio' AND '$fim'
ORDER BY registros_id ASC";

$Registros = mysql_query($query_Registros, $SmecelNovo) or die(mysql_error());
$row_Registros = mysql_fetch_assoc($Registros);
$totalRows_Registros = mysql_num_rows($Registros);

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
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>RELATÓRIO DETALHADO DE ATIVIDADES POR PERÍODO | <?php echo $row_Secretaria['sec_prefeitura']; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" type="text/css" href="../css/impressao.css">

  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

<style>

* {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	background:transparent !important;
	color:#000 !important;
	text-shadow:none !important;
	filter:none !important;
	-ms-filter:none !important;
}	
table.bordasimples {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	border-collapse: collapse;
	font-size:10px;
	color:#000;
}
table.bordasimples tr td {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	border:1px dotted #000000;
	padding:5px;
	font-size:10px;
	vertical-align:text-top;
}
table.bordasimples tr th {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	border:1px dotted #000000;
	padding:5px;
	font-size:10px;
	vertical-align:text-top;
}
hr {
	border-top: 1px solid #000000;
	height: 0px;
	margin:20px;
}
small {
	font-size: 9px;
	}

</style>

</head>
<body onload="self.print();">

 
 <table class="" width="100%">
   <tr>
     <td class="ls-txt-center" width="60"></td>
     <td class="ls-txt-center">
       <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
        <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
      <?php } else { ?>
        <img src="../../../img/brasao_republica.png" width="60">
      <?php } ?>
      <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
      <?php echo $row_Secretaria['sec_nome']; ?>
    </td>
    <td class="ls-txt-center" width="60"></td>
  </tr>
</table>
<br>
<h2 class="ls-txt-center">RELATÓRIO DETALHADO DE ATIVIDADES POR PERÍODO AAAAAAAAAAAAAAAAAAAAAAAAAAA</h2>
<br>
<h3 class="ls-txt-center">De <?php echo date("d/m/Y", strtotime($inicio)); ?> até <?php echo date("d/m/Y", strtotime($fim)); ?></h3>
<br>
<hr>
<br>


<?php if ($totalRows_Registros > 0) { // Show if recordset not empty ?>


          <table width="100%" class="ls-table1 ls-sm-space1 ls-table-striped1 ls-bg-header1 bordasimples">
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
                  <td><strong><?php echo $row_Registros['usu_nome']; ?></strong><br><?php echo $row_Registros['escola_nome']; ?>
                  </td>
                  <td align="center">
                    <strong><?php echo registros($row_Registros['registros_tipo']); ?></strong><br><?php echo $row_Registros['registros_complemento']; ?>
                  </td>
                </tr>
              <?php } while ($row_Registros = mysql_fetch_assoc($Registros)); ?>
            </tbody>
          </table>
          <hr>

          <p class="ls-txt-right"><small>SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em <?php echo date("d/m/Y à\s H\hi"); ?></small></p>

         
        <?php } else { ?>

          <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum registro encontrado.</div>

        <?php } // Show if recordset not empty ?>
        <!-- CONTEUDO -->

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
