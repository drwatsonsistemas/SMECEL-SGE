<?php

// Substitua 'SUA_CHAVE_API' pela sua chave de API real.
$apiKey = '';

// Texto que será enviado para o ChatGPT
$prompt = "Resuma o texto abaixo em um único parágrafo de no máximo 10 palavras e em seguida traduza este resumo para o inglês:

Space Exploration Technologies Corp., cujo nome comercial é SpaceX, é uma fabricante estadunidense de sistemas aeroespaciais, transporte espacial e comunicações com sede em Hawthorne, Califórnia. A SpaceX foi fundada em 2002 por Elon Musk com o objetivo de reduzir os custos de transporte espacial para permitir a colonização de Marte. A SpaceX fabrica os veículos de lançamento Falcon 9 e Falcon Heavy, vários tipos motores de foguetes, cápsulas de carga Dragon, espaçonaves tripuladas e satélites de comunicação Starlink.

As conquistas da SpaceX incluem o primeiro foguete de combustível líquido com financiamento privado a alcançar a órbita (Falcon 1 em 2008), a primeira empresa privada a lançar, orbitar e recuperar com sucesso uma espaçonave (Dragon em 2010), a primeira empresa privada a enviar uma espaçonave para a Estação Espacial Internacional (Dragon em 2012), a primeira decolagem vertical e pouso propulsivo vertical para um foguete orbital (Falcon 9 em 2015), a primeira reutilização de um foguete orbital (Falcon 9 em 2017) e a primeira empresa privada para enviar astronautas para a órbita e para a Estação Espacial Internacional (SpaceX Crew Dragon Demo-2 em 2020). A SpaceX já lançou e reutilizou a série de foguetes Falcon 9 mais de 100 vezes.

A SpaceX está desenvolvendo uma megaconstelação de satélite chamada Starlink para fornecer serviço comercial de internet. Em janeiro de 2020, a constelação Starlink se tornou a maior constelação de satélites do mundo. A SpaceX também está desenvolvendo o Starship, um sistema de lançamento superpesado, totalmente reutilizável e com financiamento privado, para voos espaciais interplanetários. O Starship pretende se tornar o veículo orbital primário da SpaceX assim que estiver operacional, suplantando a frota Falcon 9, Falcon Heavy e Dragon existentes. O Starship será totalmente reutilizável e terá a maior capacidade de carga útil de qualquer foguete orbital já em sua estreia de seu vôo orbital programado para o primeiro semestre de 2O22.";

// Preparando os dados da solicitação
$data = array(
    'model' => 'gpt-3.5-turbo',
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
    echo "Resposta:\n\n" . htmlspecialchars($responseDecoded['choices'][0]['message']['content']);
} else {
    echo "Não foi possível obter uma resposta da OpenAI.";
}

?>
