<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/session.php"; ?>

<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;    
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

include "usuLogado.php";
include "fnc/anoLetivo.php";
include "fnc/anti_injection.php"; // Incluindo para sanitizar o parâmetro GET

mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Busca informações da escola logada
$query_EscolaLogada = "
SELECT escola_id, escola_nome, escola_tema 
FROM smc_escola 
WHERE escola_id = '{$row_UsuLogado['usu_escola']}'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);

// Verifica permissão do usuário
if ($row_UsuLogado['usu_insert'] == "N") {
    header("Location: turmaListar.php?permissao");
    exit;
}

// Busca todos os alunos da turma com boletim ativo
$colname_Turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : -1;
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_nome
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_boletim = '1' 
AND vinculo_aluno_id_escola = '{$row_EscolaLogada['escola_id']}' 
AND vinculo_aluno_id_turma = " . GetSQLValueString($colname_Turma, "int");
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$totalRows_Alunos = mysql_num_rows($Alunos);

// Processa todos os alunos encontrados
if ($totalRows_Alunos > 0) {
    while ($row_Alunos = mysql_fetch_assoc($Alunos)) {
        $idVinculo = $row_Alunos['vinculo_aluno_id'];

        // Atualiza o status do boletim para 0
        $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim=%s WHERE vinculo_aluno_id=%s",
            GetSQLValueString(0, "int"),
            GetSQLValueString($idVinculo, "int"));
        mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

        // Remove os conceitos associados
        $deleteSQL = sprintf("DELETE FROM smc_conceito_aluno WHERE conc_matricula_id=%s",
            GetSQLValueString($idVinculo, "int"));
        mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());
    }
    $mensagem = "Boletins de todos os $totalRows_Alunos alunos da turma foram desativados com sucesso.";
    $redirect = "turmaListar.php?sucesso";
} else {
    $mensagem = "Nenhum aluno com boletim ativo foi encontrado nesta turma.";
    $redirect = "turmaListar.php?erro";
}

// Libera os resultados
mysql_free_result($EscolaLogada);
mysql_free_result($Alunos);

?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMECEL - Sistema de Gestão Escolar</title>
    <link rel="stylesheet" href="css/locastyle.css">
    <script src="js/locastyle.js"></script>
</head>
<body>
<?php include_once("menu-top.php"); ?>
<?php include_once("menu-esc.php"); ?>
<main class="ls-main">
    <div class="container-fluid">
        <h1 class="ls-title-intro">Desativar Boletins da Turma</h1>
        <p><?php echo $mensagem; ?></p>
        <a href="<?php echo $redirect; ?>" class="ls-btn-primary">Voltar</a>
    </div>
</main>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>
</body>
</html>