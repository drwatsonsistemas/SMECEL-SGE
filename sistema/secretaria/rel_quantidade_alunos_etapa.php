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

// Secretaria
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

// Anos Letivos
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {
  if ($_GET['ano'] == "") {
    $anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
  }
  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}

// Consulta para contar alunos sem etapa vinculada em turmas multisseriadas
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_SemEtapa = "
SELECT COUNT(*) AS total_sem_etapa
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND turma_etapa = 23 
    AND vinculo_aluno_multietapa IS NULL
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$SemEtapa = mysql_query($query_SemEtapa, $SmecelNovo) or die(mysql_error());
$row_SemEtapa = mysql_fetch_assoc($SemEtapa);
$total_sem_etapa = $row_SemEtapa['total_sem_etapa'];

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasEducacaoInfantil = "
SELECT 
    COUNT(DISTINCT va.vinculo_aluno_id_aluno) AS total 
FROM smc_vinculo_aluno va
INNER JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
WHERE va.vinculo_aluno_situacao = 1 
    AND (t.turma_etapa IN (1, 2, 3) 
         OR (t.turma_etapa = 23 AND va.vinculo_aluno_multietapa IN (1, 2, 3) AND va.vinculo_aluno_multietapa IS NOT NULL))
    AND t.turma_id_sec = '$row_Secretaria[sec_id]'
    AND t.turma_tipo_atendimento = '1'
    AND va.vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasEducacaoInfantil = mysql_query($query_TotalMatriculasEducacaoInfantil, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasEducacaoInfantil = mysql_fetch_assoc($TotalMatriculasEducacaoInfantil);
// Ensino Fundamental - Anos Iniciais (14, 15, 16, 17, 18)
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasFundamentalIniciais = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND (turma_etapa IN (14, 15, 16, 17, 18) 
         OR (turma_etapa = 23 AND vinculo_aluno_multietapa IN (14, 15, 16, 17, 18) AND vinculo_aluno_multietapa IS NOT NULL))
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasFundamentalIniciais = mysql_query($query_TotalMatriculasFundamentalIniciais, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasFundamentalIniciais = mysql_fetch_assoc($TotalMatriculasFundamentalIniciais);

// Ensino Fundamental - Anos Finais (19, 20, 21, 22)
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasFundamentalFinais = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND (turma_etapa IN (19, 20, 21, 22) 
         OR (turma_etapa = 23 AND vinculo_aluno_multietapa IN (19, 20, 21, 22) AND vinculo_aluno_multietapa IS NOT NULL))
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasFundamentalFinais = mysql_query($query_TotalMatriculasFundamentalFinais, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasFundamentalFinais = mysql_fetch_assoc($TotalMatriculasFundamentalFinais);

// Ensino Fundamental - Multisseriada (23) - apenas os não redistribuídos
$query_TotalMatriculasFundamentalMultisseriada = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND turma_etapa = 23 
    AND vinculo_aluno_multietapa IS NOT NULL 
    AND vinculo_aluno_multietapa NOT IN (1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 36, 37, 38)
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasFundamentalMultisseriada = mysql_query($query_TotalMatriculasFundamentalMultisseriada, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasFundamentalMultisseriada = mysql_fetch_assoc($TotalMatriculasFundamentalMultisseriada);

// EJA - Ensino Fundamental - Anos Iniciais (36)
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasEJAIniciais = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND (turma_etapa = 36 
         OR (turma_etapa = 23 AND vinculo_aluno_multietapa = 36 AND vinculo_aluno_multietapa IS NOT NULL))
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasEJAIniciais = mysql_query($query_TotalMatriculasEJAIniciais, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasEJAIniciais = mysql_fetch_assoc($TotalMatriculasEJAIniciais);

// EJA - Ensino Fundamental - Anos Finais (37)
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasEJAFinais = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND (turma_etapa = 37 
         OR (turma_etapa = 23 AND vinculo_aluno_multietapa = 37 AND vinculo_aluno_multietapa IS NOT NULL))
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasEJAFinais = mysql_query($query_TotalMatriculasEJAFinais, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasEJAFinais = mysql_fetch_assoc($TotalMatriculasEJAFinais);

// EJA - Ensino Fundamental - Anos Iniciais e Finais (38)
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalMatriculasEJAIniciaisEFinais = "
SELECT 
    COUNT(*) AS total 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND (turma_etapa = 38 
         OR (turma_etapa = 23 AND vinculo_aluno_multietapa = 38 AND vinculo_aluno_multietapa IS NOT NULL))
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_tipo_atendimento = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
";
$TotalMatriculasEJAIniciaisEFinais = mysql_query($query_TotalMatriculasEJAIniciaisEFinais, $SmecelNovo) or die(mysql_error());
$row_TotalMatriculasEJAIniciaisEFinais = mysql_fetch_assoc($TotalMatriculasEJAIniciaisEFinais);

// Calcular o Total Geral
$TotalGeral =
  $row_TotalMatriculasEducacaoInfantil['total'] +
  $row_TotalMatriculasFundamentalIniciais['total'] +
  $row_TotalMatriculasFundamentalFinais['total'] +
  $row_TotalMatriculasFundamentalMultisseriada['total'] +
  $row_TotalMatriculasEJAIniciais['total'] +
  $row_TotalMatriculasEJAFinais['total'] +
  $row_TotalMatriculasEJAIniciaisEFinais['total'];

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
      <h1 class="ls-title-intro ls-ico-home">QUANTIDADE DE MATRÍCULAS POR ETAPA</h1>
      <div class="ls-box ls-board-box">
        <!-- CONTEUDO -->
        <div data-ls-module="dropdown" class="ls-dropdown ls-float-right1">
          <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
          <ul class="ls-dropdown-nav">
            <li>
              <a href="rel_quantidade_alunos_etapa.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano'] ?>" target=""
                title="Diários">
                ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
              </a>
            </li>
            <?php 
            mysql_data_seek($Ano, 0); // Resetar o ponteiro do resultado
            while ($row_Ano = mysql_fetch_assoc($Ano)) { ?>
              <li>
                <a href="rel_quantidade_alunos_etapa.php?ano=<?php echo $row_Ano['ano_letivo_ano'] ?>" target=""
                  title="Diários">
                  ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
                </a>
              </li>
            <?php } ?>
          </ul>
        </div>
        <h3 class="ls-txt-center">Etapas de ensino</h3>
        <table class="ls-table ls-table-striped">
          <thead>
            <tr>
              <th width="60" class="ls-txt-center"></th>
              <th class="ls-txt-center">ETAPA</th>
              <th width="200" class="ls-txt-center">TOTAL DE MATRÍCULAS</th>
            </tr>
          </thead>
          <tr>
            <td class="ls-txt-center">1</td>
            <td class="ls-txt-center">Educação Infantil</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasEducacaoInfantil['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center">2</td>
            <td class="ls-txt-center">Ensino Fundamental - Anos Iniciais</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasFundamentalIniciais['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center">3</td>
            <td class="ls-txt-center">Ensino Fundamental - Anos Finais</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasFundamentalFinais['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center">5</td>
            <td class="ls-txt-center">EJA - Ensino Fundamental - Anos Iniciais</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasEJAIniciais['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center">6</td>
            <td class="ls-txt-center">EJA - Ensino Fundamental - Anos Finais</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasEJAFinais['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center">7</td>
            <td class="ls-txt-center">EJA - Ensino Fundamental - Anos Iniciais e Anos Finais</td>
            <td class="ls-txt-center"><?php echo $row_TotalMatriculasEJAIniciaisEFinais['total']; ?></td>
          </tr>
          <tr>
            <td class="ls-txt-center"></td>
            <td class="ls-txt-center">Total</td>
            <td class="ls-txt-center"><?php echo $TotalGeral - $total_sem_etapa; ?></td>
          </tr>
        </table>
        <?php if ($total_sem_etapa > 0) { ?>
          <small>Existem <?php echo $total_sem_etapa; ?> alunos em turmas multietapa sem etapa vinculada.</small>
        <?php } ?>

        <div class="ls-actions-btn">
          <a href="impressao/rel_quantidade_alunos_etapa.php<?php if (isset($_GET['ano'])) { echo "?ano=" . $_GET['ano']; } ?>"
            class="ls-btn" target="_blank">IMPRIMIR</a>
        </div>
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
mysql_free_result($TotalMatriculasEducacaoInfantil);
mysql_free_result($TotalMatriculasFundamentalIniciais);
mysql_free_result($TotalMatriculasFundamentalFinais);
mysql_free_result($TotalMatriculasFundamentalMultisseriada);
mysql_free_result($TotalMatriculasEJAIniciais);
mysql_free_result($TotalMatriculasEJAFinais);
mysql_free_result($TotalMatriculasEJAIniciaisEFinais);
mysql_free_result($SemEtapa);
?>