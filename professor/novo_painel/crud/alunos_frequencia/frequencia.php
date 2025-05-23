<?php

if (isset($_POST["matricula"])) {

    require_once('../../../../Connections/SmecelNovoPDO.php');

    extract($_POST);

    // Verificar se a falta já está registrada
    $query_Verifica = "
        SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, 
               faltas_alunos_numero_aula, faltas_alunos_data 
        FROM smc_faltas_alunos 
        WHERE faltas_alunos_matricula_id = :matricula AND faltas_alunos_data = :data AND faltas_alunos_numero_aula = :aula_numero";
    $stmt = $SmecelNovo->prepare($query_Verifica);
    $stmt->execute([
        ':matricula' => $matricula,
        ':data' => $data,
        ':aula_numero' => $aula_numero
    ]);
    $row_Verifica = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($matricula)) {
        echo "<script>Swal.fire({ position: 'top-end', icon: 'error', title: 'Ops...', text: 'Isso não deveria ter ocorrido', showConfirmButton: false, timer: 1000 })</script>";
        exit;
    } elseif ($row_Verifica) {
        // Deletar a falta existente
        $deleteSQL = "DELETE FROM smc_faltas_alunos WHERE faltas_alunos_id = :id";
        $stmt = $SmecelNovo->prepare($deleteSQL);
        $stmt->execute([':id' => $row_Verifica['faltas_alunos_id']]);

        echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta excluída', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
        exit;
    } else {
        if ($multi == "s") {
            // Registrar faltas para múltiplas aulas
            $query_Outras = "
                SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, 
                       ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, 
                       disciplina_id, turma_turno
                FROM smc_ch_lotacao_professor
                INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
                INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
                WHERE turma_ano_letivo = :ano 
                  AND ch_lotacao_dia = :dia 
                  AND ch_lotacao_professor_id = :prof 
                  AND ch_lotacao_turma_id = :turma
                ORDER BY turma_turno, ch_lotacao_aula ASC";

            $stmt = $SmecelNovo->prepare($query_Outras);
            $stmt->execute([
                ':ano' => $ano,
                ':dia' => $dia,
                ':prof' => $prof,
                ':turma' => $turma
            ]);

            $row_Outras = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = 0;
            foreach ($row_Outras as $outra) {
                // Verificar se já existe falta para essa aula
                $query_Verifica = "
                    SELECT faltas_alunos_id 
                    FROM smc_faltas_alunos 
                    WHERE faltas_alunos_matricula_id = :matricula AND faltas_alunos_data = :data AND faltas_alunos_numero_aula = :aula_numero";

                $stmt = $SmecelNovo->prepare($query_Verifica);
                $stmt->execute([
                    ':matricula' => $matricula,
                    ':data' => $data,
                    ':aula_numero' => $outra['ch_lotacao_aula']
                ]);

                if (!$stmt->fetch()) {
                    $insertSQL = "
                        INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) 
                        VALUES (:matricula, :disciplina, :aula_numero, :data)";

                    $stmt = $SmecelNovo->prepare($insertSQL);
                    $stmt->execute([
                        ':matricula' => $matricula,
                        ':disciplina' => $outra['ch_lotacao_disciplina_id'],
                        ':aula_numero' => $outra['ch_lotacao_aula'],
                        ':data' => $data
                    ]);

                    $count++;
                }
            }

            echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta(s) registrada(s)', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
            exit;
        } else {
            // Registrar falta para uma única aula
            $insertSQL = "
                INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) 
                VALUES (:matricula, :disciplina, :aula_numero, :data)";

            $stmt = $SmecelNovo->prepare($insertSQL);
            $stmt->execute([
                ':matricula' => $matricula,
                ':disciplina' => $disciplina,
                ':aula_numero' => $aula_numero,
                ':data' => $data
            ]);

            echo "<script>Swal.fire({ position: 'top-end', icon: 'success', title: 'Falta registrada', text: '$aluno', showConfirmButton: false, timer: 1000 })</script>";
            exit;
        }
    }
} else {
    echo "Como é que você veio parar aqui?<br>";

    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    echo get_client_ip();

    header("Location:../../index.php?err");
}
?>
