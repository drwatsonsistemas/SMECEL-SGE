<?php

require_once('../Connections/SmecelNovoPDO.php'); // Aqui está a nova conexão PDO.

session_start();

function anti_injection($sql) {
    $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
    $sql = trim($sql);
    $sql = strip_tags($sql);
    return $sql;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = isset($_POST['codigo']) ? anti_injection($_POST['codigo']) : null;
    $email = isset($_POST['email']) ? anti_injection($_POST['email']) : null;
    $senha = isset($_POST['senha']) ? anti_injection($_POST['senha']) : null;

    if (!$codigo || !$email || !$senha) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Todos os campos são obrigatórios.'
        ]);
        exit;
    }

    try {
        // Verifica login
        $query = "SELECT func_id, func_email, func_senha, func_senha_ativa, func_usu_tipo 
                  FROM smc_func 
                  WHERE func_id = :codigo AND func_email = :email AND func_senha = :senha AND func_senha_ativa = '1'";
        
        $stmt = $SmecelNovo->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuário ou senha inválidos.'
            ]);
            exit;
        }

        // Sucesso no login
        $_SESSION['MM_Username'] = $codigo;
        $_SESSION['MM_UserGroup'] = $user['func_usu_tipo'];

        // Registra o login
        date_default_timezone_set('America/Bahia');
        $dat = date('Y-m-d H:i:s');
        
        $logQuery = "INSERT INTO smc_login_professor (login_professor_id_professor, login_professor_data_hora) 
                     VALUES (:codigo, :data_hora)";
        $logStmt = $SmecelNovo->prepare($logQuery);
        $logStmt->bindParam(':codigo', $codigo);
        $logStmt->bindParam(':data_hora', $dat);
        $logStmt->execute();

        echo json_encode([
            'status' => 'success',
            'redirect' => 'novo_painel/index.php'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro no servidor. Por favor, tente novamente mais tarde.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método de requisição inválido.'
    ]);
}
?>
