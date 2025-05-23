<?php 
require_once('../../../Connections/SmecelNovo.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $documento_id = intval($_POST['id']); // Pega o ID do documento

    // Busca o caminho do arquivo antes de deletar
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query = sprintf("SELECT arquivo_path FROM smc_aluno_documentos WHERE id = %d", $documento_id);
    $result = mysql_query($query, $SmecelNovo);

    if ($row = mysql_fetch_assoc($result)) {
        $arquivoPath = '../aluno/documentos/' . $row['arquivo_path'];

        // Deleta o documento do banco de dados
        $deleteSQL = sprintf("DELETE FROM smc_aluno_documentos WHERE id = %d", $documento_id);
        if (mysql_query($deleteSQL, $SmecelNovo)) {
            // Deleta o arquivo do servidor, se existir
            if (file_exists($arquivoPath)) {
                unlink($arquivoPath);
            }
            echo json_encode(["success" => true]);
            exit;
        } else {
            echo json_encode(["success" => false, "error" => "Erro ao deletar do banco."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "error" => "Documento não encontrado."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "error" => "Requisição inválida."]);
    exit;
}
?>