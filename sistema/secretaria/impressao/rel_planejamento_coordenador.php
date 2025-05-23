<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include "../../escola/fnc/idade.php"; ?>
<?php require_once('../funcoes/inverteData.php'); ?>

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

    $logoutGoTo = "../../../index.php?exit";
    if ($logoutGoTo) {
        header("Location: $logoutGoTo");
        exit;
    }
}

// Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
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

$MM_restrictGoTo = "../../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", "1,99", $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
    $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);
require_once('../funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

// Verificar se o ID do planejamento foi fornecido
$colname_Planejamento = "-1";
if (isset($_GET['id'])) {
    $colname_Planejamento = (int) $_GET['id'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Planejamento = sprintf("
    SELECT 
    pc.id_planejamento, pc.id_coordenador, pc.data_inicio, pc.data_fim, pc.temas_integradores, pc.atividade_promovida, 
    pc.atividades_diarias, pc.intervencao_pedagogica, pc.intervencao_pedagogica_obs, pc.acoes_cnca_sabe, pc.acompanhamento_projetos, 
    pc.monitoramento_avaliacao, pc.monitoramento_avaliacao_obs, pc.formacao_continuada, pc.competencias_socioemocionais, pc.atendimento_familias, 
    pc.avaliacao_processual, pc.data_criacao, usu_id, usu_nome, escola_id, escola_id_sec, escola_nome 
FROM smc_planejamento_coordenador pc
INNER JOIN smc_usu ON usu_id = pc.id_coordenador
LEFT JOIN smc_escola ON escola_id = pc.id_escola
    WHERE pc.id_planejamento = %s AND escola_id_sec = %s
", GetSQLValueString($colname_Planejamento, "int"), GetSQLValueString($row_UsuarioLogado['usu_sec'], "int"));
$Planejamento = mysql_query($query_Planejamento, $SmecelNovo) or die(mysql_error());
$row_Planejamento = mysql_fetch_assoc($Planejamento);
$totalRows_Planejamento = mysql_num_rows($Planejamento);

// Verificar se o planejamento existe
if ($totalRows_Planejamento == 0) {
    die("Planejamento não encontrado.");
}
?>

<!DOCTYPE html>
<html class="ls-theme-green">

<head>
    <title>SMECEL - Impressão de Planejamento do Coordenador</title>
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
        @media print {

            /* Remove o rodapé por padrão */
            #footer {
                display: none;
            }

            /* Exibe o rodapé apenas na última página */
            body::after {
                content: "";
                display: block;
                page-break-after: always;
                /* Garante a quebra de página antes do rodapé */
            }

            #footer {
                display: block;
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #333;
                color: white;
                text-align: center;
                padding: 10px 0;
            }

            .signature-space {
                margin-top: 50px;
                text-align: center;
            }

            .signature-line {
                border-top: 1px solid #000;
                width: 300px;
                margin: 10px auto;
            }
        }

        .signature-space {
            margin-top: 50px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 10px auto;
        }
    </style>
</head>

<body onload="self.print();alert('Configure a impressora para o formato PAISAGEM')">

    <table class="bordasimples1" width="100%">
        <tr>
            <td class="ls-txt-center" width="60"></td>
            <td class="ls-txt-center">
                <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
                    <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>"
                        alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>"
                        title="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" width="60" />
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

    <h2 class="ls-txt-center">PLANEJAMENTO QUINZENAL DA COORDENAÇÃO PEDAGÓGICA</h2>
    <br>
    <h3 class="ls-txt-center">Escola: <?php echo $row_Planejamento['escola_nome']; ?></h3>
    <h3 class="ls-txt-center">Coordenador: <?php echo $row_Planejamento['usu_nome']; ?></h3>
    <br>

    <table class="bordasimples" width="100%">
        <tbody>
            <tr>
                <td><strong>Período:</strong></td>
                <td><?php echo date('d/m/Y', strtotime($row_Planejamento['data_inicio'])) . ' a ' . date('d/m/Y', strtotime($row_Planejamento['data_fim'])); ?>
                </td>
            </tr>
            <tr>
                <td><strong>Temas Integradores:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['temas_integradores']); ?></td>
            </tr>
            <tr>
                <td><strong>Atividade Promovida:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['atividade_promovida']); ?></td>
            </tr>
            <tr>
                <td><strong>Atividades Diárias Propostas:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['atividades_diarias']); ?></td>
            </tr>
            <tr>
                <td><strong>Intervenção Pedagógica:</strong></td>
                <td>
                    <?php echo htmlspecialchars($row_Planejamento['intervencao_pedagogica']); ?>
                    <?php if($row_Planejamento['intervencao_pedagogica_obs'] <> '') echo '<br>Observação: ' . htmlspecialchars($row_Planejamento['intervencao_pedagogica_obs']) ?>
                </td>
            </tr>
            <tr>
                <td><strong>Ações para CNCA/SABE/SAEB/LEEI:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['acoes_cnca_sabe']); ?></td>
            </tr>
            <tr>
                <td><strong>Acompanhamento de Projetos:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['acompanhamento_projetos']); ?></td>
            </tr>
            <tr>
                <td><strong>Monitoramento e Avaliação:</strong></td>
                <td>
                    <?php echo htmlspecialchars($row_Planejamento['monitoramento_avaliacao']); ?>
                    <?php if($row_Planejamento['monitoramento_avaliacao_obs'] <> '') echo '<br>Observação: ' . htmlspecialchars($row_Planejamento['monitoramento_avaliacao_obs']) ?>
                </td>
            </tr>
            <tr>
                <td><strong>Formação Continuada:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['formacao_continuada']); ?></td>
            </tr>
            <tr>
                <td><strong>Competências Socioemocionais:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['competencias_socioemocionais']); ?></td>
            </tr>
            <tr>
                <td><strong>Atendimento às Famílias:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['atendimento_familias']); ?></td>
            </tr>
            <tr>
                <td><strong>Avaliação Processual:</strong></td>
                <td><?php echo htmlspecialchars($row_Planejamento['avaliacao_processual']); ?></td>
            </tr>
            <tr>
                <td><strong>Data de Criação:</strong></td>
                <td><?php echo date("d/m/Y - H:i", strtotime($row_Planejamento['data_criacao'])); ?></td>
            </tr>
        </tbody>
    </table>

    <br>
    <div class="signature-space">
        <p>________________________________________</p>
        <p>Coordenação Pedagógica</p>
    </div>

    <hr>
    <p class="ls-txt-right"><small>SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em
            <?php echo date("d/m/Y à\s H\hi"); ?></small></p>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
        type="text/javascript"></script>
</body>

</html>

<?php
mysql_free_result($UsuarioLogado);
mysql_free_result($Secretaria);
mysql_free_result($Planejamento);
?>