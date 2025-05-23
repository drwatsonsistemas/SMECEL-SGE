<?php
// Configurações do banco de dados
$dbHost = '186.202.152.242';        // Endereço do servidor do banco de dados
$dbName = 'smecel1';    // Nome do banco de dados
$dbUser = 'smecel1';          // Usuário do banco de dados
$dbPassword = 'Drw4atson@smec';        // Senha do banco de dados

try {
    // Configuração de conexão PDO
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4"; // DSN (Data Source Name)
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Exibe erros como exceções
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Define o modo padrão de busca
        PDO::ATTR_EMULATE_PREPARES => false, // Desativa emulação de consultas preparadas
    ];

    // Cria a conexão
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

    // Testa a conexão
    // echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    // Captura e exibe erros de conexão
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}


// Cabeçalhos necessários
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Obter os dados enviados pelo AutoResponder
$data = json_decode(file_get_contents("php://input"));

// Verificar se os dados estão completos
if(
    !empty($data->query) &&
    !empty($data->appPackageName) &&
    !empty($data->messengerPackageName) &&
    !empty($data->query->sender) &&
    !empty($data->query->message)
){
    // Processar a mensagem recebida
    $sender = $data->query->sender;
    $message = htmlspecialchars($data->query->message, ENT_QUOTES, 'UTF-8');

    $message = utf8_decode($message);

//
//SELECT r.id, r.pergunta_padrao, r.resposta_oficial, MATCH (v.variacao) AGAINST (:message1 IN NATURAL LANGUAGE MODE) AS relevancia FROM smc_faq_respostas r LEFT JOIN smc_faq_variacoes_perguntas v ON r.id = v.id_resposta WHERE MATCH (v.variacao) AGAINST (:message2 IN NATURAL LANGUAGE MODE) GROUP BY r.id ORDER BY relevancia DESC, r.pergunta_padrao ASC LIMIT 1
	$stmt = $pdo->prepare("SELECT v.id_resposta AS id, v.variacao AS pergunta_padrao, r.resposta_oficial, MATCH (v.variacao) AGAINST (:message1 IN NATURAL LANGUAGE MODE) AS relevancia FROM smc_faq_variacoes_perguntas v INNER JOIN smc_faq_respostas r ON v.id_resposta = r.id WHERE MATCH (v.variacao) AGAINST (:message2 IN NATURAL LANGUAGE MODE) ORDER BY relevancia DESC, pergunta_padrao ASC LIMIT 1");
	$stmt->execute([':message1' => $message, ':message2' => $message]);
	$result = $stmt->fetch();

    if ($result > 0) {
        $resposta = $result['resposta_oficial'];
        $pergunta = $result['pergunta_padrao'];
        $resposta = utf8_decode($resposta);
        $pergunta = utf8_decode($pergunta);

        // Definir o código de resposta - 200 sucesso
    http_response_code(200);

    // Enviar uma ou múltiplas respostas para o AutoResponder
    echo json_encode(array("replies" => array(
        array("message" => "*" . $pergunta . "* \n\n" . $resposta),
        array("message" => "*Este resultado te ajudou?* \n\nDigite: \n\n1 - SIM, quero encerrar \n2 - NÃO, quero tentar novamente \n3 - NÃO, quero falar com um atendente")
    )));

    } else {
        $resposta = "Tente novamente reformulando sua pergunta.";
        $pergunta = "Não encontramos resultados para essa questão.";

        // Definir o código de resposta - 200 sucesso
    http_response_code(200);

    // Enviar uma ou múltiplas respostas para o AutoResponder
    echo json_encode(array("replies" => array(
        array("message" => $pergunta . " " . $resposta)
    )));
            
    }

    
}
else{
    // Definir o código de resposta - 400 bad request
    http_response_code(400);

    // Enviar mensagem de erro
    echo json_encode(array("replies" => array(
        array("message" => "Erro: Dados incompletos."),
        array("message" => "Por favor, verifique as informações enviadas.")
    )));
}
?>
