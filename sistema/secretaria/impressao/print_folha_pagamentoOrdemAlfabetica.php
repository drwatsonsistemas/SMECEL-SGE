<?php require_once('../../../Connections/SmecelNovo.php'); ?>
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

    $logoutGoTo = "../../../index.php?exit";
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

$MM_restrictGoTo = "../../../index.php?acessorestrito";
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

if (isset($_GET['folha']) && !empty($_GET['folha'])) {
    $folha = $_GET['folha'];
} else {
    die(header('Location: index.php?erro'));
}

if (isset($_GET['escola']) && !empty($_GET['escola'])) {
    $escola = $_GET['escola'];
} else {
    die(header('Location: index.php?erro'));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
    v.vinculo_id, 
    v.vinculo_id_escola, 
    v.vinculo_acesso, 
    v.vinculo_id_funcionario, 
    v.vinculo_id_funcao, 
    v.vinculo_data_inicio,
    v.vinculo_carga_horaria, 
    DATE_FORMAT(v.vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, 
    v.vinculo_obs,
    f.func_id, 
    f.func_nome, 
    f.func_regime,
    es.escola_id,
    es.escola_nome,
    CASE f.func_regime
        WHEN 1 THEN 'EFETIVO'
        WHEN 2 THEN 'TEMPORÁRIO'
        WHEN 3 THEN 'COMISSIONADO'
        WHEN 4 THEN 'NOMEADO'
        WHEN 5 THEN 'TERCEIRIZADO'
    END AS func_regime_nome,
    fc.funcao_id, 
    fc.funcao_nome,

    -- Contagem de faltas por tipo (dias e aulas)
    COUNT(DISTINCT CASE WHEN ff.faltas_tipo_dia_aula = 1 THEN ff.faltas_func_id END) AS total_faltas_dias,  -- Faltas por dias
    COUNT(DISTINCT CASE WHEN ff.faltas_tipo_dia_aula = 2 THEN ff.faltas_func_id END) AS total_faltas_aulas, -- Faltas por aulas

    -- Contagem de extras por tipo (dias e aulas)
    COUNT(DISTINCT CASE WHEN fe.folha_extra_tipo = 1 THEN fe.folha_extra_id END) AS total_extras_dias,  -- Extras por dias
    COUNT(DISTINCT CASE WHEN fe.folha_extra_tipo = 2 THEN fe.folha_extra_id END) AS total_extras_aulas, -- Extras por aulas
    
    fl_lanc.folha_lanc_obs,  -- Observação do lançamento da folha
    fl.folha_hash,
    fl.folha_mes,
    fl.folha_ano,
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
FROM smc_vinculo v
INNER JOIN smc_func f ON f.func_id = v.vinculo_id_funcionario
INNER JOIN smc_funcao fc ON fc.funcao_id = v.vinculo_id_funcao
INNER JOIN smc_folha fl ON fl.folha_hash = '$folha' -- Junta com a folha filtrando pelo hash
INNER JOIN smc_folha_lancamento fl_lanc ON fl_lanc.folha_lanc_id_vinculo = v.vinculo_id -- Junta com o lançamento da folha
INNER JOIN smc_escola es ON es.escola_id = v.vinculo_id_escola
-- LEFT JOIN para faltas e extras com filtro de data correto
LEFT JOIN smc_faltas_func ff ON ff.faltas_func_id_func = v.vinculo_id_funcionario 
    AND ff.faltas_func_data BETWEEN fl.folha_data_de AND fl.folha_data_ate
LEFT JOIN smc_folha_extra fe ON fe.folha_extra_id_func = v.vinculo_id_funcionario 
    AND fe.folha_extra_data BETWEEN fl.folha_data_de AND fl.folha_data_ate

WHERE v.vinculo_id_escola = '$escola'
AND v.vinculo_status = 1
GROUP BY v.vinculo_id, f.func_nome, fc.funcao_nome
ORDER BY f.func_nome ASC;  -- Ordena em ordem alfabética pelo nome do funcionário
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

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
    <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
    <link rel="stylesheet" type="text/css" href="../css/impressao.css">
    <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>

    <style>
        @page {
            size: landscape;
        }
    </style>
</head>

<body onload="self.print();">

    <table class="bordasimples1" width="100%">
        <tr>
            <td class="ls-txt-center" width="60"></td>
            <td class="ls-txt-center">
                <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
                    <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>"
                        alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>"
                        title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>" width="60" />
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

    <h2 class="ls-txt-center">FOLHA DE PAGAMENTO - <?= $row_ListaVinculos['folha_mes_nome'] ?>
        <?= $row_ListaVinculos['folha_ano'] ?>
    </h2>
    <br>
    <h3 class="ls-txt-center"><?php echo $row_ListaVinculos['escola_nome']; ?></h3>
    <br>
    <?php if ($totalRows_ListaVinculos > 0) { // Show if recordset not empty ?>
        <table class="bordasimples" width="100%">
            <thead>
                <tr>
                    <th rowspan="2" width="50"></th>
                    <th rowspan="2">NOME</th>
                    <th rowspan="2" class="ls-txt-center">FUNÇÃO</th>
                    <th rowspan="2" class="ls-txt-center">SITUAÇÃO</th>
                    <th rowspan="2" class="ls-txt-center" width="30">CH</th>
                    <th colspan="2" class="ls-txt-center">EXTRAS</th> <!-- Extras no topo -->
                    <th colspan="2" class="ls-txt-center">FALTAS</th> <!-- Faltas no topo -->
                    <th colspan="2" class="ls-txt-center">OBSERVAÇÃO/JUSTIFICATIVA</th>
                </tr>
                <tr>
                    <!-- Cabeçalhos de subtópicos "Dias" e "Aulas" para Extras e Faltas -->
                    <th class="ls-txt-center">DIAS</th>
                    <th class="ls-txt-center">AULAS</th>
                    <th class="ls-txt-center">DIAS</th>
                    <th class="ls-txt-center">AULAS</th>
                </tr>
            </thead>
            <tbody>
                <?php $num = 0 ?>
                <?php do { ?>
                    <tr>
                        <td class="ls-txt-center"><?= ++$num ?></td>
                        <td><?= $row_ListaVinculos['func_nome'] ?></td>
                        <td class="ls-txt-center"><?= $row_ListaVinculos['funcao_nome'] ?></td>
                        <td class="ls-txt-center"><?= $row_ListaVinculos['func_regime_nome'] ?></td>
                        <td class="ls-txt-center"><?= $row_ListaVinculos['vinculo_carga_horaria'] ?></td>
                        <td class="ls-txt-center">
                            <?php echo ($row_ListaVinculos['total_extras_dias'] != 0) ? $row_ListaVinculos['total_extras_dias'] : ''; ?>
                        </td> <!-- Extras - Dias -->
                        <td class="ls-txt-center">
                            <?php echo ($row_ListaVinculos['total_extras_aulas'] != 0) ? $row_ListaVinculos['total_extras_aulas'] : ''; ?>
                        </td> <!-- Extras - Aulas -->
                        <td class="ls-txt-center">
                            <?php echo ($row_ListaVinculos['total_faltas_dias'] != 0) ? $row_ListaVinculos['total_faltas_dias'] : ''; ?>
                        </td> <!-- Faltas - Dias -->
                        <td class="ls-txt-center">
                            <?php echo ($row_ListaVinculos['total_faltas_aulas'] != 0) ? $row_ListaVinculos['total_faltas_aulas'] : ''; ?>
                        </td> <!-- Faltas - Aulas -->

                        <td class="ls-txt-center"><?= $row_ListaVinculos['folha_lanc_obs'] ?></td>
                    </tr>
                <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
                <!-- Outras linhas de dados -->
            </tbody>
        </table>


        <br>
        <p class="ls-txt-right"><small>SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em
                <?php echo date("d/m/Y à\s H\hi"); ?></small></p>

    <?php } else { ?>
        <hr>
        <div class="ls-alert-warning"><strong>Atenção:</strong> Nenhum funcionário cadastrado.</div>
    <?php } // Show if recordset not empty ?>


    <?php include_once "../notificacoes.php"; ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
        type="text/javascript"></script>
    <script src="../js/maiuscula.js"></script>
    <script src="../js/semAcentos.js"></script>
    <script src="../js/buscarTabela.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($ListaVinculos);
?>