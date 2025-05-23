<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

// Inicializa a sessão, se ainda não estiver iniciada
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout do usuário **
$logoutAction = $_SERVER['PHP_SELF'] . '?doLogout=true';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') {
  $logoutAction .= '&' . htmlentities($_SERVER['QUERY_STRING']);
}

if (isset($_GET['doLogout']) && $_GET['doLogout'] == 'true') {
  // Limpa as variáveis de sessão para deslogar completamente o usuário
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = '../../index.php?saiu=true';
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

// Verifica novamente se a sessão está ativa
if (!isset($_SESSION)) {
  session_start();
}

$MM_authorizedUsers = '1,2,99';
$MM_donotCheckaccess = 'false';

// *** Restrição de Acesso à Página ***
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
  $isValid = false;

  // Verifica se o usuário está logado
  if (!empty($UserName)) {
    // Divide os valores de usuários e grupos em arrays
    $arrUsers = explode(',', $strUsers);
    $arrGroups = explode(',', $strGroups);

    // Verifica se o nome de usuário ou grupo está autorizado
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
    if (in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
    if (($strUsers == '') && false) {
      $isValid = true;
    }
  }
  return $isValid;
}

// Redirecionamento se o usuário não for autorizado
$MM_restrictGoTo = '../../index.php?err=true';
if (!isset($_SESSION['MM_Username']) || !isAuthorized('', $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])) {
  $MM_qsChar = '?';
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, '?')) {
    $MM_qsChar = '&';
  }
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
    $MM_referrer .= '?' . $_SERVER['QUERY_STRING'];
  }
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . 'accesscheck=' . urlencode($MM_referrer);
  header('Location: ' . $MM_restrictGoTo);
  exit;
}
?>
