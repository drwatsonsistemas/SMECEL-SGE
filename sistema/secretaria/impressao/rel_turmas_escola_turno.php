<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/anti_injection.php'); ?>

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listaEscolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, 
escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio 
FROM smc_escola
WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_ue = '1' AND escola_situacao = '1'";
$listaEscolas = mysql_query($query_listaEscolas, $SmecelNovo) or die(mysql_error());
$row_listaEscolas = mysql_fetch_assoc($listaEscolas);
$totalRows_listaEscolas = mysql_num_rows($listaEscolas);

//FILTRO
//$tipo = "todos";
$qry_escola = "";

if (isset($_GET['cod'])) {
    $cod = anti_injection($_GET['cod']);
    $qry_escola = " AND escola_id = '$cod' ";
}
//FILTRO

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, 
escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, 
escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio 
FROM smc_escola WHERE escola_id_sec = '$row_Secretaria[sec_id]' AND escola_ue = '1' AND escola_situacao = '1' $qry_escola
ORDER BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

//VERIFICA SE CONJUNTO VAZIO
if ($totalRows_Escolas < 1) {
    $red = "index.php?erro";
    header(sprintf("Location: %s", $red));
    break;
}
//VERIFICA SE CONJUNTO VAZIO

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

    <h2 class="ls-txt-center">RELATÓRIO</h2>
    <br>
    <h3 class="ls-txt-center">Relação de turmas por escola (turno)</h3>
    <br>


    <?php
    $num = 1;
    $turmasEscola = 0;
    do { ?>
        <?php
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Turmas = "
        SELECT turma_etapa, etapa_nome, turma_turno, COUNT(*) AS total_turmas,
        CASE turma_turno
            WHEN 0 THEN 'INTEGRAL'
            WHEN 1 THEN 'MATUTINO'
            WHEN 2 THEN 'VESPERTINO'
            WHEN 3 THEN 'NOTURNO'
        END AS turma_turno_nome
        FROM smc_turma 
        INNER JOIN smc_etapa ON turma_etapa = etapa_id 
        WHERE turma_id_escola = $row_Escolas[escola_id] 
        AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
        GROUP BY turma_etapa, turma_turno
        ORDER BY turma_etapa, turma_turno ASC";
        $Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
        $row_Turmas = mysql_fetch_assoc($Turmas);
        $totalRows_Turmas = mysql_num_rows($Turmas);

        $turmasEscola = $turmasEscola + $totalRows_Turmas;

        ?>

        <div class="ls-box">
            <h2 class="ls-title-3"><?php echo $row_Escolas['escola_nome']; ?></h2>

            <?php if ($totalRows_Turmas > 0) { ?>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Turno</th>
                            <th class="ls-txt-center">Quantidade de Turmas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do { ?>
                            <tr>
                                <td><?php echo $row_Turmas['etapa_nome']; ?></td>
                                <td><?php echo $row_Turmas['turma_turno_nome']; ?></td>
                                <td class="ls-txt-center"><?php echo $row_Turmas['total_turmas']; ?></td>
                            </tr>
                        <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>Nenhuma turma cadastrada.</p>
            <?php } ?>
        </div>



    <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>

    <p class="ls-txt-right"><small>SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em <?php echo date("d/m/Y à\s H\hi"); ?></small></p>

    <p>TOTAL DE TURMAS: <?php echo $turmasEscola; ?></p>

    <hr>


    <!-- CONTEUDO -->


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"
        type="text/javascript"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($listaEscolas);

mysql_free_result($Turmas);

mysql_free_result($Escolas);
?>