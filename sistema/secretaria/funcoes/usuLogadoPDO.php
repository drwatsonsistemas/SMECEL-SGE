<?php
try {
    // Verificar se o usuário está logado
    $colname_UsuLogado = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

    // Consulta para obter os dados do usuário logado
    $stmtUsuLogado = $SmecelNovo->prepare("SELECT * FROM smc_usu WHERE usu_email = :usu_email");
    $stmtUsuLogado->bindValue(':usu_email', $colname_UsuLogado, PDO::PARAM_STR);
    $stmtUsuLogado->execute();
    $row_UsuarioLogado = $stmtUsuLogado->fetch(PDO::FETCH_ASSOC);

    // Salvar os dados do usuário na sessão para acesso global
    if ($row_UsuarioLogado) {
        $_SESSION['row_UsuarioLogado'] = $row_UsuarioLogado;

        // Redirecionar para aceite.php caso LGPD não tenha sido aceito
        if ($row_UsuarioLogado['usu_aceite_lgpd'] == "N") {
            header("Location: aceite.php");
            exit;
        }

        // Verificar se os dados obrigatórios estão preenchidos
        if (empty($row_UsuarioLogado['usu_contato']) || empty($row_UsuarioLogado['usu_cargo'])) {
            if (!strpos($_SERVER['PHP_SELF'], 'dados.php')) {
                header("Location: dados.php?preencher");
                exit;
            }
        }
    } else {
        // Usuário não encontrado
        die("Usuário não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao consultar o usuário logado: " . $e->getMessage());
}

?>
