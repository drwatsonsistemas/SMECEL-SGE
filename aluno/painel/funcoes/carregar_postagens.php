<?php
require_once('../../../Connections/SmecelNovo.php');
include('data_post.php');
include('session.php');


mysql_select_db($database_SmecelNovo, $SmecelNovo);
header('Content-Type: application/json');

// Parâmetros para paginação
$limit = 6; // Número de postagens por vez
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Query para buscar as postagens com limite e deslocamento
$query_Publicacoes = "SELECT * FROM smc_aluno_postagem
INNER JOIN smc_aluno ON aluno_id = id_aluno_postagem_id_aluno
ORDER BY id_aluno_postagem DESC
LIMIT $limit OFFSET $offset";
$Publicacoes = mysql_query($query_Publicacoes, $SmecelNovo) or die(mysql_error());

$response = [];

while ($row_Publicacoes = mysql_fetch_assoc($Publicacoes)) {
    // Pegar o primeiro e o último nome
    $nome_completo = $row_Publicacoes['aluno_nome'];

    $postagem = $row_Publicacoes['id_aluno_postagem'];

    $id_aluno_postagem = $row_Publicacoes['id_aluno_postagem'];

    //$hash_aluno = $row_Publicacoes['aluno_hash'];

    $nome_array = explode(" ", trim($nome_completo));

    $primeiro_nome = ucfirst(strtolower($nome_array[0]));

    $ultimo_nome = ucfirst(strtolower($nome_array[count($nome_array) - 1]));

    $row_Publicacoes['texto'] = nl2br($row_Publicacoes['texto']);

    // Atualizar o nome no array de resposta
    $row_Publicacoes['aluno_nome'] = $primeiro_nome . ' ' . $ultimo_nome;

    //$row_Publicacoes['data_postagem'] = date('d/m/Y H:i', strtotime($row_Publicacoes['data_postagem']));

    $row_Publicacoes['data_postagem'] = time_ago($row_Publicacoes['data_postagem']);
    

    $response[] = $row_Publicacoes;
}

// Retorna as postagens em formato JSON
echo json_encode($response);
