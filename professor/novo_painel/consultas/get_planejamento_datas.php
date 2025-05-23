<?php
require_once('../../../Connections/SmecelNovoPDO.php');

if (isset($_GET['plan_id'])) {
    $plan_id = $_GET['plan_id'];
    
    $stmt = $SmecelNovo->prepare("
        SELECT 
            DATE_FORMAT(smc_planejamento_data_inicial, '%Y-%m-%d') as data_inicial,
            DATE_FORMAT(smc_planejamento_data_final, '%Y-%m-%d') as data_final
        FROM smc_planejamento 
        WHERE smc_id_planejamento = :plan_id
    ");
    
    $stmt->execute([':plan_id' => $plan_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID do planejamento não fornecido']);
}
?> 