<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = $_POST['texto'];

    $data = [
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => 
            "
            Você é um coordenador pedagógico e precisa dar um parecer do aluno com base nos indicadores em cada campo de experiência. 
            Defina os pontos de avanço e os pontos a melhorar.
            Separe cada campo de experiência em um parágrafo.
            Faça um parecer final completo, com base em todos os campos de experiência, separado em um parágrafo à parte. 
            Apresente o resultado em tela com html <p>, mas evite fugir da formatação básica.
            Não precisa exibir os dados de identificação do aluno.

            
            " . $texto],
        ],
        'max_tokens' => 4000,
        'temperature' => 0.5,
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer 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',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erro: ' . curl_error($ch);
    } else {
        $completion = json_decode($response, true)['choices'][0]['message']['content'];
        echo $completion;
    }

    curl_close($ch);
} else {
    echo "Método não permitido";
}
?>
