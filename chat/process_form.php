<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $notas = ' 
    Você é um coordenador pedagógico e precisa analizar os dados de um aluno que obteve determinada média. 
    Se o aluno obteve uma média final igual ou maior do que 6.0 em cada componente curricular, ele está aprovado, caso contrário, ele está reprovado.
    A média final está na coluna MC.
    Se o aluno perdeu em algumas dos componentes curriculares, ele está automaticamente reprovado no ano letivo. 
    ';

    $frequencia = '
    Você é um coordenador pedagógico e precisa dar um parecer sobre os alunos que estão faltando com base no gráfico de frequência.
    Existem três situações: Frequente, em alerta e abaixo.
    Identifique quem está em alerta e quem está abaixo.
	Dê um parecer sobre a turma de uma maneira geral. 
    ';
	
	$diretor = '
    Você é o diretor de uma escola e deve conversar dando boas-vindas ao aluno.
	';
	

    $message = htmlspecialchars($_POST['message']);

    $apiKey = 'sk-proj-1SinU9b9DebTHDLWPSq8T3BlbkFJWvklJMYLnzWNHJRvwn6v';


// Texto que será enviado para o ChatGPT
$prompt = $diretor;
$prompt .= $message;

// Preparando os dados da solicitação
$data = array(
    'model' => 'gpt-4o-mini',
    'messages' => array(
        array(
            'role' => 'user',
            'content' => $prompt
        )
    ),
    'temperature' => 0.7
);

// Configuração e execução da chamada cURL para a API de chat da OpenAI
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
));

// Executar a requisição e verificar erros
$response = curl_exec($ch);
if ($response === false) {
    echo 'Erro ao conectar com a API do OpenAI: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Verificar o código de resposta HTTP
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo 'Erro na resposta da API do OpenAI: Código HTTP ' . $httpCode . ' - ' . $response;
    exit;
}

// Processamento da resposta
$responseDecoded = json_decode($response, true);
if (isset($responseDecoded['choices'][0]['message']['content'])) {
    echo htmlspecialchars($responseDecoded['choices'][0]['message']['content']);
} else {
    echo "Não foi possível obter uma resposta da OpenAI.";
}

    // Para este exemplo, simplesmente retornamos uma mensagem de agradecimento
 


} else {


    echo "Método de requisição inválido.";
}


?>

