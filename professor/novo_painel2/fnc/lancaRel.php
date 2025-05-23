<?php require_once('../../../Connections/SmecelNovoPDO.php'); ?>
<?php include('anti_injection.php'); ?>

<?php

// Extracting variables from POST request
$variaves = extract($_POST);

// Checking if 'matricula' is empty
if (empty($matricula)) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Oops...', text: 'Informe o texto', showConfirmButton: true, timer: 5000 })</script>";
} else {
    // Preventing SQL injection
    $matricula = anti_injection($matricula);
    $conteudo = anti_injection($conteudo);

    try {
        // Using PDO to update the database
        $query = "UPDATE smc_vinculo_aluno SET vinculo_aluno_rel_aval = :conteudo WHERE vinculo_aluno_hash = :matricula";
        $stmt = $SmecelNovo->prepare($query);

        // Binding parameters
        $stmt->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
        $stmt->bindValue(':matricula', $matricula, PDO::PARAM_STR);

        // Executing the statement
        $stmt->execute();

        // If the update was successful
        if ($stmt->rowCount() > 0) {
            echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Relatório salvo com sucesso', showConfirmButton: false, timer: 2000 })</script>";
        } else {
            echo "<script>Swal.fire({ icon: 'error', title: 'Oops...', text: 'Não foi possível salvar o relatório', showConfirmButton: true, timer: 5000 })</script>";
        }
    } catch (PDOException $e) {
        // If there was an error with the database query
        echo "<script>Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível salvar as informações. Tente novamente.', showConfirmButton: true, timer: 5000 })</script>";
    }
}
?>
