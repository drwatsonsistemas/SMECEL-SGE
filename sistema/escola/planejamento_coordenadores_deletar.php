<?php
// Evitar qualquer saída antes do JSON
ob_start(); // Inicia o buffer de saída para capturar qualquer saída não intencional

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

// Incluir dependências
require('../../Connections/SmecelNovo.php');
include "fnc/session.php";
include "usuLogado.php";

// Definir o cabeçalho como JSON
header('Content-Type: application/json; charset=UTF-8');

// Inicializar a resposta
$response = array('success' => false, 'message' => '');

try {
    // Log para confirmar que o arquivo foi chamado
    error_log("Arquivo planejamento_coordenadores_deletar.php foi chamado. ID: " . (isset($_POST['id']) ? $_POST['id'] : 'N/A'));

    // Verificar se o ID foi enviado
    if (!isset($_POST['id'])) {
        throw new Exception('ID do planejamento não fornecido.');
    }

    // Verificar se $row_UsuLogado['usu_id'] está definido
    if (!isset($row_UsuLogado['usu_id'])) {
        throw new Exception('Usuário logado não encontrado. Verifique a sessão.');
    }

    // Excluir o planejamento
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $deleteSQL = sprintf(
        "DELETE FROM smc_planejamento_coordenador WHERE id_planejamento = %s AND id_coordenador = %s",
        GetSQLValueString($_POST['id'], "int"),
        GetSQLValueString($row_UsuLogado['usu_id'], "int")
    );

    $Result = mysql_query($deleteSQL, $SmecelNovo);

    if ($Result === false) {
        throw new Exception('Erro ao executar a query: ' . mysql_error());
    }

    if (mysql_affected_rows($SmecelNovo) > 0) {
        $response['success'] = true;
        $response['message'] = 'Planejamento excluído com sucesso.';
    } else {
        throw new Exception('Nenhum planejamento encontrado para excluir ou você não tem permissão.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Limpar qualquer saída não intencional e enviar apenas o JSON
ob_end_clean();
echo json_encode($response);
exit;
?>