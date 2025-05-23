<?php
require_once('../../../Connections/SmecelNovo.php');
include('../../../sistema/funcoes/inverteData.php');
include('../../../sistema/funcoes/url_base.php');
include('../../../sistema/funcoes/idade.php');
include('../../../sistema/funcoes/anti_injection.php');
include('session.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
header('Content-Type: application/json');

$response = [];
$imagemNome = ""; // Inicializa a variável da imagem como string vazia

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Texto da publicação (opcional)
    $publicacaoTexto = isset($_POST['publicacao_aluno']) ? $_POST['publicacao_aluno'] : '';

    // Verifica se pelo menos o texto ou a imagem foi informado
    if (empty($publicacaoTexto) && (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK)) {
        $response = ['success' => false, 'message' => 'Por favor, insira uma imagem ou um texto para publicar.'];
        echo json_encode($response);
        exit;
    }

    // Processar a imagem, se existir
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Extensão do arquivo
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

        // Tipos permitidos
        $tiposPermitidos = ['jpg', 'jpeg', 'png'];

        // Verificar se a extensão é válida
        if (in_array($extensao, $tiposPermitidos)) {
            // Verificar o tipo MIME para garantir que é uma imagem
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $tipoMime = $finfo->file($_FILES['imagem']['tmp_name']);
            if (in_array($tipoMime, ['image/jpeg', 'image/png'])) {
                // Gerar um hash único para o nome da imagem
                $hashUnico = md5(uniqid(rand(), true));
                $imagemNome = $hashUnico . '.' . $extensao; // Nome único da imagem com a extensão original
                $imagemCaminho = '../../publicacoes/' . $imagemNome; // Caminho onde a imagem será salva

                // Mover o arquivo da imagem para o diretório de uploads
                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $imagemCaminho)) {
                    $response = ['success' => false, 'message' => 'Erro ao salvar a imagem.'];
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response = ['success' => false, 'message' => 'O arquivo enviado não é uma imagem válida.'];
                echo json_encode($response);
                exit;
            }
        } else {
            $response = ['success' => false, 'message' => 'Apenas imagens nos formatos JPG, JPEG e PNG são permitidas.'];
            echo json_encode($response);
            exit;
        }
    }

    // Gerar hash único para a postagem
    $postagem_hash = md5(uniqid(rand(), true) . $row_AlunoLogado['aluno_id'] . date('Y-m-d H:i:s'));

    // Inserção no banco de dados, imagem e texto são opcionais
    $insertSQL = sprintf("INSERT INTO smc_aluno_postagem (id_aluno_postagem_id_aluno, texto, imagem, postagem_hash) 
        VALUES (%s, %s, %s, %s)",
        GetSQLValueString($row_AlunoLogado['aluno_id'], 'int'),
        GetSQLValueString($publicacaoTexto, 'text'),
        GetSQLValueString($imagemNome, 'text'), // Pode ser uma string vazia se não houver imagem
        GetSQLValueString($postagem_hash, 'text')
    );

    $Result1 = mysql_query($insertSQL, $SmecelNovo);

    if ($Result1) {
        // Aqui vamos buscar os dados do aluno logado e retornar junto à resposta
        $alunoFoto = !empty($row_AlunoLogado['aluno_foto']) ? $row_AlunoLogado['aluno_foto'] : 'semfoto.jpg'; // Foto do aluno ou um padrão
        $dataPostagem = date('Y-m-d H:i:s'); // Data da postagem
        $dataPostagem = "Agora mesmo"; // Data da postagem

        $nome_completo = $row_AlunoLogado['aluno_nome'];
        $nome_array = explode(" ", trim($nome_completo));
        $primeiro_nome = ucfirst(strtolower($nome_array[0]));
        $ultimo_nome = ucfirst(strtolower($nome_array[count($nome_array) - 1]));

        $alunoNome = $primeiro_nome . ' ' . $ultimo_nome;
        // Preparar a resposta
        $response = [
            'success' => true,
            'message' => 'Publicação feita com sucesso!',
            'postagem' => [
                'aluno_nome' => $alunoNome,
                'aluno_foto' => $alunoFoto,
                'data_postagem' => $dataPostagem,
                'texto' => $publicacaoTexto,
                'imagem' => $imagemNome,
                'postagem' => $postagem_hash
            ]
        ];
    } else {
        $response = ['success' => false, 'message' => 'Erro ao fazer a publicação.'];
        echo json_encode($response);
        exit;
    }
} else {
    $response = ['success' => false, 'message' => 'Método de requisição não permitido.'];
    echo json_encode($response);
    exit;
}

// Retornar a resposta como JSON
echo json_encode($response);
?>