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

mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Busca informações da escola
$query_EscolaLogada = "
SELECT escola_id, escola_nome, escola_tema 
FROM smc_escola 
WHERE escola_id = '{$row_UsuLogado['usu_escola']}'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);

// Busca os alunos da turma
$colname_Alunos = $_GET['turma'];
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_nome, turma_matriz_id, matriz_criterio_avaliativo, ca_qtd_periodos, ca_qtd_av_periodos
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo
WHERE vinculo_aluno_boletim = '0' AND vinculo_aluno_id_turma = {$colname_Alunos}";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$totalRows_Alunos = mysql_num_rows($Alunos);

if ($totalRows_Alunos > 0) {
    while ($row_Alunos = mysql_fetch_assoc($Alunos)) {
        $idVinculo = $row_Alunos['vinculo_aluno_id'];
        $matrizId = $row_Alunos['turma_matriz_id'];

        // Atualiza status do boletim
        $updateSQL = sprintf(
            "UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim='1' WHERE vinculo_aluno_id=%s",
            GetSQLValueString($idVinculo, "int")
        );
        mysql_query($updateSQL, $SmecelNovo);

        // Insere disciplinas e notas para o aluno
        $queryDisciplinas = "SELECT matriz_disciplina_id_disciplina FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = $matrizId";
        $disciplinas = mysql_query($queryDisciplinas, $SmecelNovo);

        while ($row_disciplinas = mysql_fetch_assoc($disciplinas)) {
            $idDisciplina = $row_disciplinas['matriz_disciplina_id_disciplina'];
            
            // Verifica se a disciplina tem recuperação paralela
            $queryCriterios = "SELECT ca_rec_paralela FROM smc_criterios_avaliativos WHERE ca_id = '{$row_Alunos['matriz_criterio_avaliativo']}'";
            $criterios = mysql_query($queryCriterios, $SmecelNovo);
            $row_criterios = mysql_fetch_assoc($criterios);
            
            for ($p = 1; $p <= $row_Alunos['ca_qtd_periodos']; $p++) { // Exemplo: 4 períodos
                for ($a = 1; $a <= $row_Alunos['ca_qtd_av_periodos']; $a++) { // Exemplo: 3 avaliações por período
                    $hash = md5($idVinculo . $idDisciplina . $p . $a);
                    $insertNota = "INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_hash) VALUES ('$idVinculo', '$idDisciplina', '$p', '$a', '$hash')";
                    mysql_query($insertNota, $SmecelNovo);
                }
                
                // Se tiver recuperação paralela, cria o registro para ela
                if ($row_criterios['ca_rec_paralela'] == 'S') {
                    $hashRecPar = md5($idVinculo . $idDisciplina . $p . '98'); // 98 é o código para recuperação paralela
                    $insertNotaRecPar = "INSERT INTO smc_nota (nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_hash) VALUES ('$idVinculo', '$idDisciplina', '$p', '98', '$hashRecPar')";
                    mysql_query($insertNotaRecPar, $SmecelNovo);
                }
            }
        }
    }

    echo "Boletins gerados com sucesso para todos os alunos da turma.";
} else {
    echo "Nenhum boletim foi gerado. Verifique se todos os alunos já têm boletins gerados.";
}
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
<main class="ls-main ">
    <div class="container-fluid">
        <h1 class="ls-title-intro">Boletins da Turma</h1>
        <p>Processo concluído. Verifique os boletins gerados.</p>
        <a href="turmaListar.php" class="ls-btn-primary">Voltar</a>
    </div>
</main>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>
</body>
</html>
