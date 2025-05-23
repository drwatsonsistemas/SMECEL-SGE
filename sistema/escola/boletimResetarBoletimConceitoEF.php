<?php 
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

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
?>
<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/sessionPDO.php"; ?>

<?php
include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";
// Verifica permissões do usuário
if ($row_UsuLogado['usu_insert'] == "N") {
    header("Location: vinculoAlunoExibirTurma.php?permissao");
    exit;
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

// Consulta a turma
$colname_Turma = isset($_GET['turma']) ? $_GET['turma'] : "-1";
$query_Turma = "
    SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_ano_letivo, turma_matriz_id
    FROM smc_turma 
    WHERE turma_id = :turma AND turma_id_escola = :escola";
$stmt = $SmecelNovo->prepare($query_Turma);
$stmt->bindParam(':turma', $colname_Turma, PDO::PARAM_INT);
$stmt->bindParam(':escola', $row_UsuLogado['usu_escola'], PDO::PARAM_INT);
$stmt->execute();
$row_Turma = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Turma = $stmt->rowCount();

// Consulta os alunos com boletim gerado
$query_Alunos = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_boletim, aluno_nome
    FROM smc_vinculo_aluno
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    WHERE vinculo_aluno_id_turma = :turma AND vinculo_aluno_boletim = 1";
$stmt = $SmecelNovo->prepare($query_Alunos);
$stmt->bindParam(':turma', $colname_Turma, PDO::PARAM_INT);
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalRows_Alunos = count($alunos);

// Processamento do reset
if ($totalRows_Alunos > 0) {
    foreach ($alunos as $row_Alunos) {
        $idVinculo = $row_Alunos['vinculo_aluno_id'];

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
    }
}
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
    <title>SMECEL - Resetar Boletim Conceito EF</title>
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
    <?php include_once "menu-top.php"; ?>
    <?php include_once "menu-esc.php"; ?>

    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">RESETAR BOLETIM CONCEITO EF</h1>
            <div class="ls-box">TURMA: <?php echo $row_Turma['turma_nome']; ?></div>

            <?php if ($totalRows_Alunos > 0) { ?>
                <table class="ls-table">
                    <tr>
                        <th width="110">MATRÍCULA</th>
                        <th>ALUNO</th>
                        <th>STATUS</th>
                    </tr>
                    <?php foreach ($alunos as $row_Alunos) { ?>
                        <tr>
                            <td><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
                            <td><?php echo $row_Alunos['aluno_nome']; ?></td>
                            <td><span class="ls-ico-spinner ls-color-warning">BOLETIM RESETADO</span></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>Nenhum boletim gerado para resetar.</p>
            <?php } ?>

            <p><a href="turmaListar.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>
        </div>
    </main>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
</body>
</html>