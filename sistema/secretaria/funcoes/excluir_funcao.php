<?php
// Evite qualquer espaço ou linha em branco antes deste ponto
require('../../../Connections/SmecelNovo.php');

// Certifique-se de que não há saída antes deste header
ob_start(); // Inicia o buffer de saída
header('Content-Type: application/json');

// Conexão com o banco
mysql_select_db($database_SmecelNovo, $SmecelNovo);

$response = array();

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = mysql_real_escape_string($_POST['id']);
    
    $query = "DELETE FROM smc_funcao WHERE funcao_id = '$id'";
    $result = mysql_query($query);
    
    if ($result) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Erro ao excluir a função: ' . mysql_error();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'ID da função não fornecido';
}

// Limpa qualquer saída anterior e envia apenas o JSON
ob_end_clean(); // Limpa o buffer e desliga o buffering
echo json_encode($response);
exit; // Encerra a execução
?>