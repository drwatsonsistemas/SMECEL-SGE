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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if(isset($_GET['codigo']) && !empty($_GET['codigo'])) {
    $hash = $_GET['codigo'];
}else{
    die(header('Location: index.php?erro'));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

    if ($row_UsuLogado['usu_insert'] == "N") {
        header(sprintf("Location: funcListar.php?permissao"));
        exit;
    }

    $updateSQL = sprintf(
    "UPDATE smc_folha SET folha_mes = %s, folha_data_de = %s, folha_data_ate = %s, folha_aberta = %s WHERE folha_hash = %s",
        GetSQLValueString($_POST['folha_mes'], "text"),
        GetSQLValueString($_POST['folha_data_de'], "date"),
        GetSQLValueString($_POST['folha_data_ate'], "date"),
        GetSQLValueString($_POST['folha_aberta'], "text"),
        GetSQLValueString($hash, "text")
    );
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    $insertGoTo = "folha_pagamento.php?editado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));



}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$query_Folhas = "SELECT * FROM smc_folha WHERE folha_id_sec = $row_UsuarioLogado[usu_sec] AND folha_hash = '$hash'";
$Folhas = mysql_query($query_Folhas, $SmecelNovo) or die(mysql_error());
$row_Folhas = mysql_fetch_assoc($Folhas);
$totalRows_Folhas = mysql_num_rows($Folhas);

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
            <h1 class="ls-title-intro ls-ico-home">EDITAR FOLHA DE PAGAMENTO</h1>

            <div class="ls-modal" id="myAwesomeModal" data-modal-blocked>
                <div class="ls-modal-box">
                    <div class="ls-modal-header">
                        <button data-dismiss="modal">&times;</button>
                        <h4 class="ls-modal-title">FOLHA DE PAGAMENTO</h4>
                    </div>
                    <div class="ls-modal-body" id="myModalBody">
                        <form method="post" name="form1" action="<?php echo $editFormAction; ?>"
                            class="ls-form ls-form-horizontal row">

                            <!-- Mês de referência
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Mês de Referência</b>
                                <input type="number" class="ls-no-spin" name="folha_mes"
                                    placeholder="Digite o mês (1-12)" value="<?= $row_Folhas['folha_mes'] ?>" required>
                            </label> -->

                            <label class="ls-label col-md-12">
                            <b class="ls-label-text">Mês de Referência</b>
                                <div class="ls-custom-select">
                                <select class="ls-select" name="folha_mes">
                                <option value=""> Informe o mês de referência </option>
                                    <option value="1" <?php if ($row_Folhas['folha_mes'] == '1') { echo "selected"; } ?>> Janeiro </option>
                                    <option value="2" <?php if ($row_Folhas['folha_mes'] == '2') { echo "selected"; } ?>> Fevereiro </option>
                                    <option value="3" <?php if ($row_Folhas['folha_mes'] == '3') { echo "selected"; } ?>> Março </option>
                                    <option value="4" <?php if ($row_Folhas['folha_mes'] == '4') { echo "selected"; } ?>> Abril </option>
                                    <option value="5" <?php if ($row_Folhas['folha_mes'] == '5') { echo "selected"; } ?>> Maio </option>
                                    <option value="6" <?php if ($row_Folhas['folha_mes'] == '6') { echo "selected"; } ?>> Junho </option>
                                    <option value="7" <?php if ($row_Folhas['folha_mes'] == '7') { echo "selected"; } ?>> Julho </option>
                                    <option value="8" <?php if ($row_Folhas['folha_mes'] == '8') { echo "selected"; } ?>> Agosto </option>
                                    <option value="9" <?php if ($row_Folhas['folha_mes'] == '9') { echo "selected"; } ?>> Setembro </option>
                                    <option value="10" <?php if ($row_Folhas['folha_mes'] == '10') { echo "selected"; } ?>> Outubro </option>
                                    <option value="11" <?php if ($row_Folhas['folha_mes'] == '11') { echo "selected"; } ?>> Novembro </option>
                                    <option value="12" <?php if ($row_Folhas['folha_mes'] == '12') { echo "selected"; } ?>> Dezembro </option>
                                </select>
                            </div>
                            </label>


                            <!-- Data Inicial -->
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Data Inicial</b>
                                <input type="date" name="folha_data_de" value="<?= $row_Folhas['folha_data_de'] ?>" required>
                            </label>

                            <!-- Data Final -->
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Data Final</b>
                                <input type="date" name="folha_data_ate" value="<?= $row_Folhas['folha_data_ate'] ?>" required>
                            </label>

                            <!-- Folha Fechada (Sim/Não) -->
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Folha Aberta?</b>
                                <div class="ls-custom-select">
                                    <select name="folha_aberta" required>
                                        <option value="S" <?php if ($row_Folhas['folha_aberta'] == "S") echo "selected"; ?>>Sim</option>
                                        <option value="N" <?php if ($row_Folhas['folha_aberta'] == "N") echo "selected"; ?>>Não</option>
                                    </select>
                                </div>
                            </label>

                            <!-- Botões -->
                            <div class="ls-modal-footer">
                                <input type="submit" value="EDITAR FOLHA" class="ls-btn-primary" tabindex="4">
                                <input type="hidden" name="MM_update" value="form1">
                            </div>
                        </form>
                    </div>
                </div><!-- /.modal -->




                <p>&nbsp;</p>
                <!-- CONTEUDO -->
            </div>
    </main>
    <?php include_once "notificacoes.php"; ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script>
        locastyle.modal.open("#myAwesomeModal");
    </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>