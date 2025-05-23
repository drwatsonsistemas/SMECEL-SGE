<?php
require_once('../../../Connections/SmecelNovo.php');
require_once('session.php');


$aluno_logado = $row_AlunoLogado['aluno_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se o ID da postagem foi passado
    if (isset($_POST['id_postagem'])) {
        
        $id_postagem = intval($_POST['id_postagem']);
        
        // Query para deletar a postagem com o ID fornecido
        $query = "DELETE FROM smc_aluno_postagem WHERE id_aluno_postagem_id_aluno = $aluno_logado AND id_aluno_postagem = $id_postagem";
        $result = mysql_query($query, $SmecelNovo);

        $row_result = mysql_fetch_assoc($result);
        $totalRows_result = mysql_num_rows($result);



        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Postagem deletada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar a postagem.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID da postagem não fornecido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
?>