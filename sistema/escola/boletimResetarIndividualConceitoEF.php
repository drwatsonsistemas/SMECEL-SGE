
<?php 
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
?>
<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "fnc/sessionPDO.php"; ?>
<?php// include "fnc/anti_injection.php"; ?>

<?php
include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";
// Verifica permissões do usuário
if ($row_UsuLogado['usu_insert'] == "N") {
    header("Location: vinculoAlunoExibirTurma.php?permissao");
    exit;
}

// Configura a ação do formulário
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Consulta a escola logada
$query_EscolaLogada = "
    SELECT escola_id, escola_id_sec, escola_nome, escola_tema 
    FROM smc_escola
    INNER JOIN smc_sec ON sec_id = escola_id_sec 
    WHERE escola_id = :escola";
$stmt = $SmecelNovo->prepare($query_EscolaLogada);
$stmt->bindParam(':escola', $row_UsuLogado['usu_escola'], PDO::PARAM_INT);
$stmt->execute();
$row_EscolaLogada = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_EscolaLogada = $stmt->rowCount();

var_dump($row_EscolaLogada);
// Consulta o aluno com base no hash
$colname_AlterarStatus = isset($_GET['cmatricula']) ? $_GET['cmatricula'] : "-1";
$query_AlterarStatus = "
    SELECT 
        vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
        vinculo_aluno_ano_letivo, vinculo_aluno_boletim, vinculo_aluno_hash,
        aluno_nome, aluno_nascimento, aluno_filiacao1,
        turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id,
        etapa_id, etapa_nome,
        matriz_id, matriz_nome, matriz_criterio_avaliativo
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
    INNER JOIN smc_etapa ON etapa_id = turma_etapa
    INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
    WHERE vinculo_aluno_boletim = 1 
    AND vinculo_aluno_id_escola = :escola 
    AND vinculo_aluno_hash = :hash";
$stmt = $SmecelNovo->prepare($query_AlterarStatus);
$stmt->bindParam(':escola', $row_EscolaLogada['escola_id'], PDO::PARAM_INT);
$stmt->bindParam(':hash', $colname_AlterarStatus, PDO::PARAM_STR);
$stmt->execute();
$row_AlterarStatus = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_AlterarStatus = $stmt->rowCount();
var_dump($row_AlterarStatus);
// Se não encontrar o aluno ou o boletim não estiver gerado, redireciona
if ($totalRows_AlterarStatus == 0) {
    header("Location: vinculoAlunoExibirTurma.php?erro");
    exit;
}

// Processa o reset
$idVinculo = $row_AlterarStatus['vinculo_aluno_id'];

// Atualiza o status do boletim para 0
$updateSQL = "
    UPDATE smc_vinculo_aluno 
    SET vinculo_aluno_boletim = 0 
    WHERE vinculo_aluno_id = :vinculo";
$stmt = $SmecelNovo->prepare($updateSQL);
$stmt->bindParam(':vinculo', $idVinculo, PDO::PARAM_INT);
$stmt->execute();

// Exclui os registros de conceitos EF
$deleteSQL = "
    DELETE FROM smc_conceito_ef 
    WHERE conc_ef_id_matr = :vinculo";
$stmt = $SmecelNovo->prepare($deleteSQL);
$stmt->bindParam(':vinculo', $idVinculo, PDO::PARAM_INT);
$stmt->execute();

// Redireciona após o reset
$updateGoTo = "matriculaExibe.php?resetado";
if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $updateGoTo));
exit;
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
    <title>SMECEL - Resetar Boletim Conceito EF (Individual)</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
    <!-- O conteúdo HTML não será exibido, pois o reset é automático e redireciona -->
</body>
</html>