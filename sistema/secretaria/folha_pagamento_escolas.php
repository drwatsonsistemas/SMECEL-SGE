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
require_once('funcoes/inverteData.php');

if (isset($_GET['folha']) && !empty($_GET['folha'])) {
    $folha = $_GET['folha'];
} else {
    die(header('Location: index.php?erro'));
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$query_Folhas = "SELECT * FROM smc_folha WHERE folha_id_sec = $row_UsuarioLogado[usu_sec]";
$Folhas = mysql_query($query_Folhas, $SmecelNovo) or die(mysql_error());
$row_Folhas = mysql_fetch_assoc($Folhas);
$totalRows_Folhas = mysql_num_rows($Folhas);

$query_FolhaEscolas = "SELECT *,
CASE folha_mes
WHEN '1' THEN 'Janeiro'
WHEN '2' THEN 'Fevereiro'
WHEN '3' THEN 'Março'
WHEN '4' THEN 'Abril'
WHEN '5' THEN 'Maio'
WHEN '6' THEN 'Junho'
WHEN '7' THEN 'Julho'
WHEN '8' THEN 'Agosto'
WHEN '9' THEN 'Setembro'
WHEN '10' THEN 'Outubro'
WHEN '11' THEN 'Novembro'
WHEN '12' THEN 'Dezembro'
END AS folha_mes_nome
FROM smc_folha 
INNER JOIN smc_folha_lancamento ON folha_lanc_id_folha = folha_id
INNER JOIN smc_escola ON escola_id = folha_lanc_id_escola
WHERE folha_hash = '$folha'
GROUP BY escola_id
";

$FolhaEscolas = mysql_query($query_FolhaEscolas, $SmecelNovo) or die(mysql_error());
$row_FolhaEscolas = mysql_fetch_assoc($FolhaEscolas);
$totalRows_FolhaEscolas = mysql_num_rows($FolhaEscolas);

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
            <h1 class="ls-title-intro ls-ico-docs">FOLHA DE PAGAMENTO - ESCOLAS</h1>
            <!-- CONTEUDO -->

            <a href="folha_pagamento.php" class="ls-btn-primary ls-ico-chevron-left">Voltar</a>
            <hr>

            <?php if ($totalRows_FolhaEscolas > 0) { ?>
                <?php
                $temFolhaAberta = false;
                    mysql_data_seek($FolhaEscolas, 0); // Reseta o ponteiro para o início dos resultados
                while ($row = mysql_fetch_assoc($FolhaEscolas)) {
                    if ($row['folha_aberta'] == 'S') {
                        $temFolhaAberta = true;
                        break;
                    }
                }
                //mysql_data_seek($FolhaEscolas, 0); // Reseta novamente para exibir os dados na tabela
                ?>

                <?php if ($temFolhaAberta) { ?>
                    <table class="ls-table ls-table-striped">
                        <thead>
                            <tr>
                                <th>Escola</th>
                                <th>Mês de referência</th>
                                <th class="hidden-xs">Ano de referência</th>
                                <th class="hidden-xs">Data de abertura</th>
                                <th>Data de fechamento</th>
                                <th>Situação</th>
                                <th class="ls-txt-right">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do { ?>
                                <tr>
                                    <td><?php echo $row_FolhaEscolas['escola_nome']; ?></td>
                                    <td><?= $row_FolhaEscolas['folha_mes_nome']; ?></td>
                                    <td class="hidden-xs"><?= $row_FolhaEscolas['folha_ano']; ?></td>
                                    <td class="hidden-xs"><?= inverteData($row_FolhaEscolas['folha_data_de']); ?></td>
                                    <td><?= inverteData($row_FolhaEscolas['folha_data_ate']); ?></td>
                                    <td><?php
                                    if ($row_FolhaEscolas['folha_aberta'] == "S") {
                                        echo "<b class=\"ls-color-success\">ABERTA <span class=\"ls-ico-checkmark-circle ls-ico-right\"></span></b>";
                                    } else {
                                        echo "<b class=\"ls-color-warning\">FECHADA <span class=\"ls-ico-info ls-ico-right\"></span></b>";
                                    }
                                    ?></td>
                                    <td class="ls-txt-right">
                                        <a href="folha_pagamento_visualizar.php?folha=<?= $row_FolhaEscolas['folha_hash']; ?>&escola=<?= $row_FolhaEscolas['escola_id']; ?>"
                                            class="ls-btn-primary ls-ico-eye"></a>
                                    </td>
                                </tr>
                            <?php } while ($row_FolhaEscolas = mysql_fetch_assoc($FolhaEscolas)); ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="ls-alert-info">Nenhuma escola abriu a folha de pagamento ainda.</div>
                <?php } ?>
            <?php } else { ?>
                <div class="ls-alert-warning">Nenhuma folha de pagamento encontrada.</div>
            <?php } ?>




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
?>