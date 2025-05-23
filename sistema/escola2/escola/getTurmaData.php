<?php
require_once('../../Connections/SmecelNovo.php');

if (isset($_POST['turma_id'])) {
    $turma_id = intval($_POST['turma_id']);

    // Busca matriz_aula_dia
    $query_matriz = "SELECT matriz_aula_dia FROM smc_matriz WHERE matriz_id = (SELECT turma_matriz_id FROM smc_turma WHERE turma_id = $turma_id)";
    $matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
    $row_matriz = mysql_fetch_assoc($matriz);

    // Busca disciplinas
    $query_disciplinas = "
        SELECT disciplina_id, disciplina_nome, disciplina_eixo_nome 
        FROM smc_matriz_disciplinas 
        INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
        LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id 
        WHERE matriz_disciplina_id_matriz = (SELECT turma_matriz_id FROM smc_turma WHERE turma_id = $turma_id)";
    $disciplinas = mysql_query($query_disciplinas, $SmecelNovo) or die(mysql_error());
    $disciplinas_array = [];
    while ($row = mysql_fetch_assoc($disciplinas)) {
        $disciplinas_array[] = [
            'id' => $row['disciplina_id'],
            'nome' => $row['disciplina_nome'] . ($row['disciplina_eixo_nome'] ? " - (" . $row['disciplina_eixo_nome'] . ")" : "")
        ];
    }

    $response = [
        'matriz_aula_dia' => $row_matriz['matriz_aula_dia'],
        'disciplinas' => $disciplinas_array
    ];

    echo json_encode($response);
}
?>