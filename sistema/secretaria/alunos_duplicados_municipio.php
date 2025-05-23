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
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <style>
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
        }
        #loading-message {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
        }
        #warning-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .print-button {
            margin: 20px 0;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            #resultado-consulta {
                display: block !important;
            }
        }
    </style>
</head>
<body>
    <?php include_once("menu_top.php"); ?>
    <?php include_once "menu.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-search">Possíveis Alunos Duplicados</h1>
            
            <!-- Loading Overlay -->
            <div id="loading-overlay">
                <div id="lottie-container" style="width: 200px; height: 200px;"></div>
                <div id="loading-message">
                    <p>A consulta pode demorar um pouco, vá tomar um café ☕</p>
                    <p id="warning-message">NÃO feche essa página!</p>
                </div>
            </div>

            <!-- Botão para Iniciar Consulta -->
            <div class="ls-actions-btn no-print">
                <button class="ls-btn ls-btn-primary" id="iniciar-consulta">
                    <i class="ls-ico-search"></i> Iniciar Investigação
                </button>
            </div>

            <!-- Botão de Impressão -->
            <div class="print-button no-print" style="display: none;">
                <button class="ls-btn ls-btn-primary" onclick="window.print()">
                    <i class="ls-ico-print"></i> Imprimir Resultados
                </button>
            </div>

            <!-- CONTEUDO -->
            <div id="resultado-consulta">
                <!-- O conteúdo será carregado via AJAX -->
            </div>
        </div>
    </main>
    <?php include_once "notificacoes.php"; ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.7.14/lottie.min.js"></script>
    <script>
        $(document).ready(function() {
            // Carrega a animação Lottie
            var animation = lottie.loadAnimation({
                container: document.getElementById('lottie-container'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: 'images/lotties/coffe-waiting.json'
            });

            // Função para fazer a consulta
            function fazerConsulta() {
                // Mostra o overlay de carregamento
                $('#loading-overlay').css('display', 'flex');
                // Esconde o botão de iniciar
                $('.ls-actions-btn').hide();
                // Mostra o botão de imprimir
                $('.print-button').show();

                // Faz a requisição AJAX
                $.ajax({
                    url: 'buscar_alunos_duplicados.php',
                    method: 'GET',
                    data: {
                        sec_id: '<?php echo $row_Secretaria['sec_id']; ?>'
                    },
                    success: function(response) {
                        $('#resultado-consulta').html(response);
                        $('#loading-overlay').hide();
                    },
                    error: function() {
                        $('#resultado-consulta').html('<p class="ls-alert-danger">Erro ao carregar os dados. Por favor, tente novamente.</p>');
                        $('#loading-overlay').hide();
                        // Mostra o botão de iniciar novamente em caso de erro
                        $('.ls-actions-btn').show();
                        $('.print-button').hide();
                    }
                });
            }

            // Adiciona o evento de clique no botão
            $('#iniciar-consulta').click(function() {
                fazerConsulta();
            });
        });
    </script>
</body>
</html>
<?php
// mysql_free_result($UsuarioLogado);
// mysql_free_result($Secretaria);
// mysql_free_result($Escolas);
?>