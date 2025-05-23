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

// Consulta para listar escolas ativas
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT escola_id, escola_nome
FROM smc_escola
WHERE escola_situacao = '1'
    AND escola_id_sec = '$row_Secretaria[sec_id]'
ORDER BY escola_nome
";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

// Obter o filtro de escola selecionada
$escola_filtro = isset($_GET['escola_id']) ? mysql_real_escape_string($_GET['escola_id']) : '';

// Consulta para alunos com matrículas duplicadas ou mais
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunosDuplicados = "
SELECT 
    va.vinculo_aluno_id_aluno,
    a.aluno_nome,
    COUNT(*) AS total_enrollments,
    GROUP_CONCAT(t.turma_nome SEPARATOR ', ') AS turmas,
    e.escola_nome
FROM smc_vinculo_aluno va
INNER JOIN smc_aluno a ON a.aluno_id = va.vinculo_aluno_id_aluno
INNER JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
INNER JOIN smc_escola e ON e.escola_id = t.turma_id_escola
WHERE t.turma_tipo_atendimento = '1'
    AND va.vinculo_aluno_situacao = '1'
    AND va.vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
    AND va.vinculo_aluno_id_sec = '$row_Secretaria[sec_id]'
" . ($escola_filtro ? "AND e.escola_id = '$escola_filtro'" : "") . "
GROUP BY va.vinculo_aluno_id_aluno, a.aluno_nome, e.escola_nome
HAVING COUNT(*) > 1
ORDER BY total_enrollments DESC, a.aluno_nome
";
$AlunosDuplicados = mysql_query($query_AlunosDuplicados, $SmecelNovo) or die(mysql_error());
$row_AlunosDuplicados = mysql_fetch_assoc($AlunosDuplicados);
$totalRows_AlunosDuplicados = mysql_num_rows($AlunosDuplicados);
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

    <title>SMECEL - Alunos com Múltiplas Matrículas</title>
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
            <h1 class="ls-title-intro ls-ico-search">Alunos com Múltiplas Matrículas</h1>
            <!-- Filtro por Escola -->
            <form method="GET" action="alunos_matriculas_duplicadas.php" class="ls-form ls-form-inline">
                <label class="ls-label col-md-4 col-xs-12">
                    <b class="ls-label-text">Filtrar por Escola</b>
                    <div class="ls-custom-select">
                        <select name="escola_id" class="ls-select" onchange="this.form.submit()">
                            <option value="">Todas as Escolas</option>
                            <?php 
                            mysql_data_seek($Escolas, 0);
                            while ($row_EscolaOption = mysql_fetch_assoc($Escolas)) { ?>
                                <option value="<?php echo $row_EscolaOption['escola_id']; ?>" 
                                    <?php if ($escola_filtro == $row_EscolaOption['escola_id']) echo 'selected'; ?>>
                                    <?php echo $row_EscolaOption['escola_nome']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </label>
            </form>
            <!-- CONTEUDO -->
            <?php if ($totalRows_AlunosDuplicados > 0) { ?>
                <table class="ls-table">
                    <thead>
                        <tr>
                            <th>ID do Aluno</th>
                            <th>Nome do Aluno</th>
                            <th>Escola</th>
                            <th>Total de Matrículas</th>
                            <th>Turmas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do { ?>
                            <tr>
                                <td><?php echo $row_AlunosDuplicados['vinculo_aluno_id_aluno']; ?></td>
                                <td><?php echo $row_AlunosDuplicados['aluno_nome']; ?></td>
                                <td><?php echo $row_AlunosDuplicados['escola_nome']; ?></td>
                                <td><?php echo $row_AlunosDuplicados['total_enrollments']; ?></td>
                                <td><?php echo $row_AlunosDuplicados['turmas']; ?></td>
                            </tr>
                        <?php } while ($row_AlunosDuplicados = mysql_fetch_assoc($AlunosDuplicados)); ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>Nenhum aluno encontrado com múltiplas matrículas<?php echo $escola_filtro ? " na escola selecionada." : "."; ?></p>
            <?php } ?>
            <p> </p>
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
mysql_free_result($Escolas);
mysql_free_result($AlunosDuplicados);
?>