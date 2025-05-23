<?php
if (!isset($_SESSION)) {
    session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
    $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
    // Limpar as variáveis de sessão para realizar o logout completo
    $_SESSION['MM_Username'] = NULL;
    $_SESSION['MM_UserGroup'] = NULL;
    $_SESSION['PrevUrl'] = NULL;
    unset($_SESSION['MM_Username']);
    unset($_SESSION['MM_UserGroup']);
    unset($_SESSION['PrevUrl']);

    $logoutGoTo = "../index.php?saiu";
    if ($logoutGoTo) {
        header("Location: $logoutGoTo");
        exit;
    }
}

$MM_authorizedUsers = "7";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
    $isValid = false;

    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);

        if (in_array($UserName, $arrUsers)) {
            $isValid = true;
        }
        if (in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
    }

    return $isValid;
}

$MM_restrictGoTo = "../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    }
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

include('../../sistema/funcoes/url_base.php');

// Obter informações do professor logado
$colname_ProfLogado = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

try {
    $query_ProfLogado = "SELECT func_id, func_id_sec, func_nome, func_email, func_foto, func_sexo, func_data_nascimento 
                         FROM smc_func 
                         WHERE func_id = :func_id";
    $stmt = $SmecelNovo->prepare($query_ProfLogado);
    $stmt->bindParam(':func_id', $colname_ProfLogado);
    $stmt->execute();
    $row_ProfLogado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row_ProfLogado) {
        header("Location:../index.php?loginErr");
        exit;
    }

    // Obter informações da Secretaria
    $query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, 
                         sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo 
                         FROM smc_sec 
                         WHERE sec_id = :sec_id";
    $stmt = $SmecelNovo->prepare($query_Secretaria);
    $stmt->bindParam(':sec_id', $row_ProfLogado['func_id_sec']);
    $stmt->execute();
    $row_Secretaria = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row_Secretaria['sec_bloqueada'] == "S") {
        // Bloqueia acesso caso a secretaria esteja bloqueada
        $_SESSION['MM_Username'] = NULL;
        $_SESSION['MM_UserGroup'] = NULL;
        $_SESSION['PrevUrl'] = NULL;
        unset($_SESSION['MM_Username']);
        unset($_SESSION['MM_UserGroup']);
        unset($_SESSION['PrevUrl']);

        header("Location: ../../index.php?fin");
        exit;
    }

    // Obter vínculos do professor
    $query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, sec_id, sec_prefeitura 
                       FROM smc_vinculo 
                       INNER JOIN smc_sec ON sec_id = vinculo_id_sec 
                       WHERE vinculo_id_funcionario = :func_id";
    $stmt = $SmecelNovo->prepare($query_Vinculos);
    $stmt->bindParam(':func_id', $row_ProfLogado['func_id']);
    $stmt->execute();
    $row_Vinculos = $stmt->fetch(PDO::FETCH_ASSOC);

    include "fnc/anoLetivo.php";

    define("TEMA", "ls-theme-cyanogen");
    define("PAINEL", "PROFESSORES");
    define("SEC_ID", $row_Vinculos['vinculo_id_sec']);
    define("PREFEITURA", $row_Vinculos['sec_prefeitura']);
    define("ANO_LETIVO", isset($row_AnoLetivo['ano_letivo_ano']) ? $row_AnoLetivo['ano_letivo_ano'] : null);
    define("ID_PROFESSOR", $row_ProfLogado['func_id']);
} catch (PDOException $e) {
    die("Erro ao acessar o banco de dados: " . $e->getMessage());
}
?>
