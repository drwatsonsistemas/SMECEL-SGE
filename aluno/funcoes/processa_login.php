<?php
require_once('../../Connections/SmecelNovo.php');
include_once('../../sistema/funcoes/inverteData.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    echo json_encode([
        'status' => 'error',
        'message' => 'Desativamos temporariamente o acesso ao painel do aluno. Tente em outro momento!'
    ]);
    exit;
    */

    session_start();

    // Função anti-injection
    function anti_injection($sql) {
        $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
        $sql = trim($sql);
        $sql = strip_tags($sql);
        $sql = addslashes($sql);
        return $sql;
    }

    $loginUsername = anti_injection($_POST['codigo']);
    $password = anti_injection($_POST['senha']);
    $nascimento = inverteData($_POST['nascimento']);

    mysql_select_db($database_SmecelNovo, $SmecelNovo);

    // Consulta no banco de dados
    $LoginRS__query = sprintf(
        "SELECT aluno_id, aluno_hash, aluno_nascimento, aluno_usu_tipo 
         FROM smc_aluno 
         WHERE aluno_nascimento='%s' AND aluno_id='%s'",
        $nascimento,
        $loginUsername
    );

    $LoginRS = mysql_query($LoginRS__query, $SmecelNovo);
    if (!$LoginRS) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao executar a consulta no banco de dados: ' . mysql_error()
        ]);
        exit;
    }

    $loginRow = mysql_fetch_assoc($LoginRS);
    $loginFoundUser = mysql_num_rows($LoginRS);

    if ($loginFoundUser) {
        $senhaBanco = substr($loginRow['aluno_hash'], 0, 5);
        if ($senhaBanco === $password) {
            $_SESSION['MM_Username'] = $loginUsername;
            $_SESSION['MM_UserGroup'] = $loginRow['aluno_usu_tipo'];

            date_default_timezone_set('America/Bahia');
            $dat = date('Y-m-d H:i:s');

            $sqlLog = sprintf(
                "INSERT INTO smc_login_aluno (login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ano) 
                 VALUES ('%s', '%s', '%s')",
                $loginUsername,
                $dat,
                date('Y') // Exemplo de ano letivo atual
            );
            mysql_query($sqlLog, $SmecelNovo);

            echo json_encode([
                'status' => 'success',
                'redirect' => 'painel/index.php?bemvindo',
                'message' => 'Login realizado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Senha incorreta. Por favor, tente novamente!'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuário ou data de nascimento não encontrado.'
        ]);
    }
    exit;
}
?>
