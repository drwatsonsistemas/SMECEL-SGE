<?php
/** Consultando ChatGPT / OpenIA
 * Carlos Rolim
 * SOLOWEB - soloweb.com.br 
 */

$OPENAI_API_KEY = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pergunta'])) {
    $pergunta = trim($_POST['pergunta']);
    echo "<p>Sua Pergunta foi: " . htmlspecialchars($pergunta) . "</p>";

    echo "<p>Resposta da OpenAI: </p>";

    $ch = curl_init();
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $OPENAI_API_KEY
    );

    $postData = array(
        'model' => 'text-davinci-003',
        'prompt' => str_replace('"', '', $pergunta),
        'temperature' => 0.9,
        'max_tokens' => 100,
        'top_p' => 1,
        'frequency_penalty' => 0.0,
        'presence_penalty' => 0.0,
        'stop' => array(' Human:', ' AI:')
    );

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    // Adicionar essas opções para resolver o problema de SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    // Use a path absoluta para o arquivo cacert.pem
    //curl_setopt($ch, CURLOPT_CAINFO, 'https://www.smecel.com.br/cacert.pem');
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo '<p>Erro no cURL: ' . curl_error($ch) . '</p>';
    } else {
        $decoded_json = json_decode($result, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<p>" . htmlspecialchars($decoded_json['choices'][0]['text']) . "</p>";
        } else {
            echo '<p>Erro ao decodificar JSON: ' . json_last_error_msg() . '</p>';
        }
    }

    curl_close($ch);
} else {
    echo '<p>Por favor, envie uma pergunta.</p>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ChatGPT PHP</title>
</head>
<body>
    <form action="chat_gpt.php" method="post">
        <label for="pergunta">Informe sua pergunta:</label>
        <input type="text" id="pergunta" name="pergunta">
        <input type="submit" value="Enviar">
    </form>
</body>
</html>
