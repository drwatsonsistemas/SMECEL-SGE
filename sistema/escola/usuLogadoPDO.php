<?php
try {
    // Verificar se o usuário está logado
    $colname_UsuLogado = isset($_SESSION['MM_Username']) ? $_SESSION['MM_Username'] : "-1";

    // Consulta para obter os dados do usuário logado
    $stmtUsuLogado = $SmecelNovo->prepare("SELECT * FROM smc_usu WHERE usu_email = :usu_email");
    $stmtUsuLogado->bindValue(':usu_email', $colname_UsuLogado, PDO::PARAM_STR);
    $stmtUsuLogado->execute();
    $row_UsuLogado = $stmtUsuLogado->fetch(PDO::FETCH_ASSOC);
    $totalRows_UsuLogado = $stmtUsuLogado->rowCount();

    // Redirecionar para aceite.php caso LGPD não tenha sido aceito
    if ($row_UsuLogado && $row_UsuLogado['usu_aceite_lgpd'] == "N") {
        header("Location: aceite.php");
        exit;
    }

    // Verificar se os dados obrigatórios estão preenchidos
    if (empty($row_UsuLogado['usu_contato']) || empty($row_UsuLogado['usu_cargo'])) {
        // Evitar loop de redirecionamento para dados.php
        if (!strpos($_SERVER['PHP_SELF'], 'dados.php')) {
            header("Location: dados.php?preencher");
            exit;
        }
    }
} catch (PDOException $e) {
    die("Erro ao consultar o usuário logado: " . $e->getMessage());
}
?>
