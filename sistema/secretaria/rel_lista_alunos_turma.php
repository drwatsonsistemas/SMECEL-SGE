<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
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
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
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
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome FROM smc_escola WHERE escola_id_sec = '{$row_UsuarioLogado['usu_sec']}'";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
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

  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/impressao.css">

  <script src="js/locastyle.js"></script>  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();alert('Configure a impressora para o formato PAISAGEM')">

  <!-- CONTEUDO -->



  <table class="bordasimples1" width="100%">
    <tr>
      <td class="ls-txt-center" width="60"></td>
      <td class="ls-txt-center">
        <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
          <img src="../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
        <?php } else { ?>
          <img src="../../img/brasao_republica.png" width="60">
        <?php } ?>
        <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
        <?php echo $row_Secretaria['sec_nome']; ?>
      </td>
      <td class="ls-txt-center" width="60"></td>
    </tr>
  </table>
  <br>

  <h2 class="ls-txt-center">RELATÓRIO GERAL</h2>
  <br>
  <h3 class="ls-txt-center">Alunos por Etapa de Ensino</h3>
  <br>  

  <?php while ($row_Escolas = mysql_fetch_assoc($Escolas)) {
    echo '<h2 class="ls-txt-center">' . $row_Escolas['escola_nome'] . '</h2>';

    $query_Vinculo = "
    SELECT aluno_nome, turma_nome, escola_nome, aluno_nascimento
    FROM smc_vinculo_aluno
    INNER JOIN smc_turma ON smc_vinculo_aluno.vinculo_aluno_id_turma = smc_turma.turma_id
    INNER JOIN smc_aluno ON smc_vinculo_aluno.vinculo_aluno_id_aluno = smc_aluno.aluno_id
    INNER JOIN smc_escola ON smc_turma.turma_id_escola = smc_escola.escola_id
    WHERE smc_escola.escola_id = '{$row_Escolas['escola_id']}' AND smc_turma.turma_ano_letivo = '{$row_AnoLetivo['ano_letivo_ano']}'
    ORDER BY smc_turma.turma_nome, smc_aluno.aluno_nome ASC
    ";
    $Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());

    echo '<table class="ls-table1 ls-sm-space bordasimples" width="100%">
    <thead>
    <tr>
    <th><h2>Aluno</h2></th>
    <th><h2>Turma</h2></th>
    <th width="30%"></th>
    </tr>
    </thead>
    <tbody>';

    $totalRows_Vinculo = mysql_num_rows($Vinculo);

    if ($totalRows_Vinculo > 0) {
      while ($row_Vinculo = mysql_fetch_assoc($Vinculo)) {
        echo '<tr>
        <td>' . $row_Vinculo['aluno_nome'] . '</td>
        <td>' . $row_Vinculo['turma_nome'] . '</td>
        <td></td>
        </tr>';
      }

      echo '<tr>
      <td colspan="4"><i class="ls-float-right">Total de alunos vinculados nesta escola: <strong>' . $totalRows_Vinculo . '</strong></i></td>
      </tr>';
    } else {
      echo '<tr>
      <td colspan="4">- Nenhum aluno vinculado nesta escola</td>
      </tr>';
    }

    echo '</tbody></table><hr>';
  }
  ?>


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