<?php
require_once('../../../Connections/SmecelNovo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $id = intval($_POST['id']);
    $type = $_POST['type'];

    if ($type === 'like') {
        $query = "UPDATE smc_faq_respostas SET likes = likes + 1 WHERE id = $id";
    } elseif ($type === 'dislike') {
        $query = "UPDATE smc_faq_respostas SET dislikes = dislikes + 1 WHERE id = $id";
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de feedback inválido']);
        exit;
    }

    if (mysql_query($query, $SmecelNovo)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar feedback no banco de dados']);
    }
}
?>