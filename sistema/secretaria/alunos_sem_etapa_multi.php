<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
<?php include('funcoes/idade.php'); ?>

<?php
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  // To fully log out a visitor we need to clear the session variables
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

// Restrict Access To Page
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
  $isValid = False;
  if (!empty($UserName)) {
    $arrUsers = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
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
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}

// Função GetSQLValueString
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

// Escolas
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_nome FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_situacao = '1' ORDER BY escola_nome";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
  if ($_GET['ano'] == "") {
    $anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
  }
  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}

// Filtro por escola
$escolaFiltro = isset($_GET['escola']) ? (int)$_GET['escola'] : 0; // 0 para "Todas"

// Consulta para buscar alunos sem etapa vinculada em turmas multisseriadas, agrupados por escola
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosSemEtapa = "
SELECT 
    va.vinculo_aluno_id,
    va.vinculo_aluno_id_aluno,
    a.aluno_nome,
    a.aluno_cpf,
    a.aluno_nascimento,
    t.turma_id,
    t.turma_nome,
    t.turma_etapa,
    e.escola_nome,
    va.vinculo_aluno_multietapa
FROM smc_vinculo_aluno va
INNER JOIN smc_aluno a ON a.aluno_id = va.vinculo_aluno_id_aluno
INNER JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
INNER JOIN smc_escola e ON e.escola_id = t.turma_id_escola
WHERE va.vinculo_aluno_situacao = 1 
    AND t.turma_tipo_atendimento = 1 
    AND e.escola_situacao = '1'
    AND va.vinculo_aluno_ano_letivo = '$anoLetivo'
    AND t.turma_ano_letivo = '$anoLetivo'
    AND t.turma_id_sec = '$row_UsuarioLogado[usu_sec]'          
		AND t.turma_multisseriada = 1
    AND (va.vinculo_aluno_multietapa IS NULL OR va.vinculo_aluno_multietapa = 0)
ORDER BY e.escola_nome, t.turma_nome, a.aluno_nome";
$AlunosSemEtapa = mysql_query($query_AlunosSemEtapa, $SmecelNovo) or die(mysql_error());
$totalRows_AlunosSemEtapa = mysql_num_rows($AlunosSemEtapa);
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

  <title>SMECEL - Turmas Multisseriadas com Alunos sem Etapa Vinculada por Escola</title>
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
      <h1 class="ls-title-intro ls-ico-users">Turmas Multisseriadas com Alunos sem Etapa Vinculada por Escola</h1>
      <div class="ls-box ls-board-box">
        <!-- Filtros -->
        <div class="ls-clearfix">
          <!-- <div class="ls-float-right1" style="margin-left: 10px;">
            <div data-ls-module="dropdown" class="ls-dropdown">
              <a href="#" class="ls-btn">ANO LETIVO: <?php echo $anoLetivo; ?></a>
              <ul class="ls-dropdown-nav">
                <li>
                  <a href="pagina.php?ano=<?php echo $row_AnoLetivo['ano_letivo_ano'] ?>&escola=<?php echo $escolaFiltro; ?>" target="" title="Diários">
                    ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano']; ?>
                  </a>
                </li>
                <?php 
                mysql_data_seek($Ano, 0); // Resetar o ponteiro do resultado
                while ($row_Ano = mysql_fetch_assoc($Ano)) { ?>
                  <li>
                    <a href="pagina.php?ano=<?php echo $row_Ano['ano_letivo_ano'] ?>&escola=<?php echo $escolaFiltro; ?>" target="" title="Diários">
                      ANO LETIVO <?php echo $row_Ano['ano_letivo_ano']; ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div> -->
          <div class="ls-float-right1">
            <div data-ls-module="dropdown" class="ls-dropdown">
              <a href="#" class="ls-btn">ESCOLA: <?php echo ($escolaFiltro > 0) ? htmlspecialchars($row_Escolas['escola_nome']) : 'Todas'; ?></a>
              <ul class="ls-dropdown-nav">
                <li>
                  <a href="alunos_sem_etapa_multi.php?ano=<?php echo $anoLetivo; ?>&escola=0" target="" title="Todas">
                    Todas
                  </a>
                </li>
                <?php 
                mysql_data_seek($Escolas, 0); // Resetar o ponteiro do resultado
                while ($row_Escolas = mysql_fetch_assoc($Escolas)) { ?>
                  <li>
                    <a href="alunos_sem_etapa_multi.php?ano=<?php echo $anoLetivo; ?>&escola=<?php echo $row_Escolas['escola_id']; ?>" target="" title="<?php echo htmlspecialchars($row_Escolas['escola_nome']); ?>">
                      <?php echo htmlspecialchars($row_Escolas['escola_nome']); ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>
        <br>
        <h3 class="ls-txt-center">Alunos sem Etapa Vinculada (Total: <?php echo $totalRows_AlunosSemEtapa; ?>)</h3>
        <?php if ($totalRows_AlunosSemEtapa > 0) { ?>
          <table class="ls-table ls-table-striped">
            <thead>
              <tr>
                <th>Escola</th>
                <th>Turma</th>
                <th>Aluno</th>
                <th>CPF</th>
                <th>Data de Nascimento</th>
                <th>Idade</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row_AlunosSemEtapa = mysql_fetch_assoc($AlunosSemEtapa)) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($row_AlunosSemEtapa['escola_nome']); ?></td>
                  <td><?php echo htmlspecialchars($row_AlunosSemEtapa['turma_nome']); ?></td>
                  <td><?php echo htmlspecialchars($row_AlunosSemEtapa['aluno_nome']); ?></td>
                  <td><?php echo htmlspecialchars($row_AlunosSemEtapa['aluno_cpf']); ?></td>
                  <td><?php echo htmlspecialchars($row_AlunosSemEtapa['aluno_nascimento']); ?></td>
                  <td>
                    <?php 
                    if (!empty($row_AlunosSemEtapa['aluno_nascimento'])) {
                      echo idade($row_AlunosSemEtapa['aluno_nascimento']) . " anos";
                    } else {
                      echo "Não informada";
                    }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
          <!-- <div class="ls-actions-btn">
            <a href="impressao/pagina_impressao.php<?php if (isset($_GET['ano'])) { echo "?ano=" . $_GET['ano']; } ?><?php if (isset($_GET['escola'])) { echo "&escola=" . $_GET['escola']; } ?>"
              class="ls-btn" target="_blank">IMPRIMIR</a>
          </div> -->
        <?php } else { ?>
            <br><br>
          <div class="ls-alert-success">
            <strong>Parabéns!</strong> Não há alunos sem etapa vinculada em turmas multisseriadas<?php if ($escolaFiltro > 0) { echo " na escola selecionada"; } ?>.
          </div>
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
mysql_free_result($Ano);
mysql_free_result($Escolas);
mysql_free_result($AlunosSemEtapa);