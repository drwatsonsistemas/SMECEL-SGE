<?php
require_once('../../../Connections/SmecelNovoPDO.php');
require_once('../../escola/fnc/idade.php');
require_once('../funcoes/anti_injection.php');

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

if (!isset($_SESSION)) {
    session_start();
}
$MM_authorizedUsers = "1,99";
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

if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = htmlspecialchars($theValue, ENT_QUOTES, 'UTF-8');

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

function limparCacheExpirado($cacheDir, $cacheLifetime) {
    if (!is_dir($cacheDir)) {
        return; // Diretório não existe
    }

    $arquivos = scandir($cacheDir);
    $agora = time();

    foreach ($arquivos as $arquivo) {
        $caminhoArquivo = $cacheDir . DIRECTORY_SEPARATOR . $arquivo;

        // Ignora diretórios e arquivos que não sejam JSON
        if (!is_file($caminhoArquivo) || pathinfo($caminhoArquivo, PATHINFO_EXTENSION) !== 'json') {
            continue;
        }

        // Exclui o arquivo se estiver expirado
        if ($agora - filemtime($caminhoArquivo) > $cacheLifetime) {
            unlink($caminhoArquivo);
        }
    }
}

require_once('../funcoes/usuLogadoPDO.php');

$cacheDir = 'cache/';
$cacheLifetime = 300; // Tempo de validade do cache (5 minutos)
limparCacheExpirado($cacheDir, $cacheLifetime); // Limpa os arquivos expirados

// Verifica e sanitiza os parâmetros recebidos via POST
$etapa = isset($_POST['etapa']) ? (int) $_POST['etapa'] : 99;
$ano = isset($_POST['ano']) ? (int) $_POST['ano'] : date('Y'); // Ano atual como padrão
$escola = isset($_POST['escola']) ? (int) $_POST['escola'] : 99;

// Filtros na consulta
$qry_etapa = $etapa !== 99 ? "AND turma_etapa = :etapa_id" : ""; // Inclui todas as etapas por padrão
$qry_escola = $escola !== 99 ? "AND escola_id = :escola_id" : ""; // Inclui todas as escolas por padrão

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}
$cacheKey = "etapa_{$etapa}_ano_{$ano}_escola_{$escola}";
$cacheFile = "{$cacheDir}/{$cacheKey}.json";

// Configuração da conexão para latin1
$SmecelNovo->exec("SET NAMES 'latin1'");

// Consulta ao banco de dados
$query = "
    SELECT 
        etapa_nome, aluno_nome, aluno_nascimento, turma_nome, turma_id_sec, escola_nome
    FROM smc_vinculo_aluno
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_escola ON escola_id = turma_id_escola
    INNER JOIN smc_etapa ON etapa_id = turma_etapa
    WHERE turma_tipo_atendimento = '1' 
      AND vinculo_aluno_situacao = '1'
      AND turma_id_sec = :id_sec
      AND (:ano = 0 OR turma_ano_letivo = :ano)
      $qry_etapa
      $qry_escola
    ORDER BY aluno_nome ASC, etapa_nome, turma_nome";

$stmt = $SmecelNovo->prepare($query);

if ($etapa !== 99) {
    $stmt->bindValue(':etapa_id', $etapa, PDO::PARAM_INT);
}
if ($escola !== 99) {
    $stmt->bindValue(':escola_id', $escola, PDO::PARAM_INT);
}
$stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
$stmt->bindValue(':id_sec', $row_UsuarioLogado['usu_sec'], PDO::PARAM_INT);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converte os dados para UTF-8 apenas onde necessário
foreach ($alunos as &$aluno) {
    foreach ($aluno as $key => $value) {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $aluno[$key] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }
    }
}
unset($aluno); // Evita problemas com referências

// Processa os resultados
$response = [];
if (!empty($alunos)) {
    $response['total_alunos'] = count($alunos);
    $etapas = [];
    foreach ($alunos as $aluno) {
        $etapas[$aluno['etapa_nome']][] = [
            'aluno_nome' => $aluno['aluno_nome'],
            'idade' => idade($aluno['aluno_nascimento']),
            'turma_nome' => $aluno['turma_nome'],
            'escola_nome' => $aluno['escola_nome']
        ];
    }
    $response['etapas'] = $etapas;
} else {
    $response['total_alunos'] = 0;
    $response['etapas'] = [];
}

// Grava no cache somente se o ano for inferior ao atual
if ($ano < date('Y')) {
    file_put_contents($cacheFile, json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// Retorna o JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);