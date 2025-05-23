<?php
require_once('../../../Connections/SmecelNovo.php');

if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Valida o ID recebido
    $id_faq = isset($_GET['id_faq']) ? intval($_GET['id_faq']) : 0;

    if ($id_faq <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Consulta ao banco de dados
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query = sprintf("SELECT id, pergunta_padrao, resposta_oficial, categoria FROM smc_faq_respostas WHERE id = %d", $id_faq);
    $result = mysql_query($query, $SmecelNovo);

    if (!$result) {
        echo json_encode(['error' => 'Erro na consulta ao banco de dados: ' . mysql_error()]);
        exit;
    }

    // Verifica se algum registro foi encontrado
    if (mysql_num_rows($result) === 0) {
        echo json_encode(['error' => 'Nenhum registro encontrado']);
        exit;
    }

    // Retorna o registro encontrado
    $data = mysql_fetch_assoc($result);
    echo json_encode($data);
    exit;
}
?>
