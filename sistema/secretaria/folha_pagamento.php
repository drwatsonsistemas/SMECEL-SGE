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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

    if ($row_UsuLogado['usu_insert'] == "N") {
        header(sprintf("Location: funcListar.php?permissao"));
        exit;
    }

    $hash = md5($row_UsuarioLogado['usu_sec'] . $_POST['folha_mes'] . date('YmdHis'));
    $insertSQL = sprintf(
        "INSERT INTO smc_folha (folha_id_sec, folha_mes, folha_ano, folha_data_de, folha_data_ate, folha_hash, folha_aberta) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($row_UsuarioLogado['usu_sec'], "int"),
        GetSQLValueString($_POST['folha_mes'], "text"),
        GetSQLValueString($row_AnoLetivo['ano_letivo_ano'], "text"),
        GetSQLValueString($_POST['folha_data_de'], "date"),
        GetSQLValueString($_POST['folha_data_ate'], "date"),
        GetSQLValueString($hash, "text"),
        GetSQLValueString($_POST['folha_aberta'], "text")
    );
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    $insertGoTo = "folha_pagamento.php?inserido";

    header(sprintf("Location: %s", $insertGoTo));



}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$query_Folhas = "SELECT *, 
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
 FROM smc_folha WHERE folha_id_sec = $row_UsuarioLogado[usu_sec]";
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
            <h1 class="ls-title-intro ls-ico-docs">FOLHA DE PAGAMENTO</h1>
            <!-- CONTEUDO -->
            <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-plus">ABRIR
                FOLHA</button>
            <hr>
            <?php if (isset($_GET['inserido'])) { ?>

                <div class="ls-alert-success">Folha de pagamento criada</div>

            <?php } ?>

            <?php if (isset($_GET['editado'])) { ?>

                <div class="ls-alert-success">Folha de pagamento editada</div>

            <?php } ?>

            <?php if (isset($_GET['excluido'])) { ?>

                <div class="ls-alert-success">Folha de pagamento excluída</div>

            <?php } ?>

            <?php if ($totalRows_Folhas > 0) { ?>
                <table class="ls-table ls-table-striped">
                    <thead>
                        <tr>
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
                                <td><?= $row_Folhas['folha_mes_nome'] ?></td>
                                <td class="hidden-xs"><?= $row_Folhas['folha_ano']; ?></td>
                                <td class="hidden-xs"><?= inverteData($row_Folhas['folha_data_de']); ?></td>
                                <td><?= inverteData($row_Folhas['folha_data_ate']); ?></td>
                                <td><?php
                                if ($row_Folhas['folha_aberta'] == "S") {
                                    echo "<b class=\"ls-color-success\">ABERTA <span class=\"ls-ico-checkmark-circle ls-ico-right\"></span></b>";
                                } else {
                                    echo "<b class=\"ls-color-warning\">FECHADA <span class=\"ls-ico-info ls-ico-right\"></span></b>";
                                }
                                ?></td>
                                <td class="ls-txt-right">
                                    <a href="folha_pagamento_escolas.php?folha=<?= $row_Folhas['folha_hash']; ?>" class="ls-btn-primary ls-ico-eye"></a>
                                    <div data-ls-module="dropdown" class="ls-dropdown">
                                        <a href="#" class="ls-btn-primary"></a>

                                        <ul class="ls-dropdown-nav">
                                            <li> <a
                                                    href="folha_pagamento_editar.php?codigo=<?php echo $row_Folhas['folha_hash']; ?>"><span
                                                        class="ls-ico-pencil"></span> Editar</a></li>
                                            <li><a title="Excluir" href="javascript:func()"
                                                    onclick="confirmacao('<?php echo $row_Folhas['folha_hash']; ?>')"><span
                                                        class="ls-ico-remove ls-color-danger"></span> Excluir</a></li>
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                            <?php } while ($row_Folhas = mysql_fetch_assoc($Folhas)); ?>
                        
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="ls-alert-warning">Nenhuma folha de pagamento encontrada</div>
            <?php } ?>

            <div class="ls-modal" id="myAwesomeModal">
                <div class="ls-modal-box">
                    <div class="ls-modal-header">
                        <button data-dismiss="modal">&times;</button>
                        <h4 class="ls-modal-title">FOLHA DE PAGAMENTO</h4>
                    </div>
                    <div class="ls-modal-body" id="myModalBody">
                        <form method="post" name="form1" action="<?php echo $editFormAction; ?>"
                            class="ls-form ls-form-horizontal row">

                            <!-- 
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Mês de Referência</b>
                                <input type="number" class="ls-no-spin" name="folha_mes"
                                    placeholder="Digite o mês (1-12)" required>
                            </label>Mês de referência -->

                            <label class="ls-label col-md-12">
                            <b class="ls-label-text">Mês de Referência</b>
                                <div class="ls-custom-select">
                                <select class="ls-select" name="folha_mes">
                                <option value=""> Informe o mês de referência </option>
                                    <option value="1"> Janeiro </option>
                                    <option value="2"> Fevereiro </option>
                                    <option value="3"> Março </option>
                                    <option value="4"> Abril </option>
                                    <option value="5"> Maio </option>
                                    <option value="6"> Junho </option>
                                    <option value="7"> Julho </option>
                                    <option value="8"> Agosto </option>
                                    <option value="9"> Setembro </option>
                                    <option value="10"> Outubro </option>
                                    <option value="11"> Novembro </option>
                                    <option value="12"> Dezembro </option>
                                </select>
                            </div>
                            </label>


                            <!-- Data Inicial -->
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Data Inicial</b>
                                <input type="date" name="folha_data_de" required>
                            </label>

                            <!-- Data Final -->
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">Data Final</b>
                                <input type="date" name="folha_data_ate" required>
                            </label>

                            <!-- Folha Fechada (Sim/Não) -->
                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">Folha Aberta?</b>
                                <div class="ls-custom-select">
                                    <select name="folha_aberta" required>
                                        <option value="S">Sim</option>
                                        <option value="N">Não</option>
                                    </select>
                                </div>
                            </label>

                            <!-- Botões -->
                            <div class="ls-modal-footer">
                                <button class="ls-btn ls-float-right" data-dismiss="modal"
                                    tabindex="5">CANCELAR</button>
                                <input type="submit" value="REGISTRAR FOLHA" class="ls-btn-primary" tabindex="4">
                                <input type="hidden" name="MM_insert" value="form1">
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

    <script language="Javascript">
        function confirmacao(id) {
            var resposta = confirm("Deseja remover esse registro? Você não poderá recuperar os dados após a exclusão.");

            if (resposta == true) {
                window.location.href = "folha_pagamento_excluir.php?c=" + id;
            }
        }
    </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>