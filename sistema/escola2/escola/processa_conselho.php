<?php
require_once('../../Connections/SmecelNovo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
// Define o cabeçalho para garantir que a resposta seja JSON
header('Content-Type: application/json');

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

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    'status' => 'error',
    'message' => 'Método de requisição inválido. Use POST.'
  ]);
  exit;
}

// Verifica se os parâmetros necessários foram enviados
if (!isset($_POST['vinculo_aluno_id'])) {
  echo json_encode([
    'status' => 'error',
    'message' => 'ID do aluno não fornecido.'
  ]);
  exit;
}


// Coleta os dados do POST
$resultadoFinal = '';
$vinculoAlunoId = $_POST['vinculo_aluno_id'];
$conselho = isset($_POST['vinculo_aluno_conselho']) ? $_POST['vinculo_aluno_conselho'] : 'N';
$reprovado = isset($_POST['vinculo_aluno_conselho_reprovado']) ? $_POST['vinculo_aluno_conselho_reprovado'] : 'N';
$parecer = isset($_POST['vinculo_aluno_conselho_parecer']) ? $_POST['vinculo_aluno_conselho_parecer'] : '';

if ($conselho == "S") {
  $resultadoFinal = 1;
}
if ($reprovado == "S") {
  $resultadoFinal = 2;
}

// Atualiza os dados do conselho no banco de dados

$updateSQL = sprintf(
  "UPDATE smc_vinculo_aluno 
     SET vinculo_aluno_conselho = %s, vinculo_aluno_conselho_reprovado = %s, vinculo_aluno_conselho_parecer = %s, vinculo_aluno_resultado_final = %s 
     WHERE vinculo_aluno_id = %s",
  GetSQLValueString($conselho, "text"),
  GetSQLValueString($reprovado, "text"),
  GetSQLValueString($parecer, "text"),
  GetSQLValueString($resultadoFinal, "int"),
  GetSQLValueString($vinculoAlunoId, "int")
);

if (!mysql_query($updateSQL, $SmecelNovo)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Erro ao atualizar o conselho: ' . mysql_error()
  ]);
  exit;
}

// Ajusta as notas se o conselho for aprovado
if ($conselho === 'S') {
  $queryNotaMinima = sprintf(
    "SELECT ca_nota_min_recuperacao_final 
         FROM smc_criterios_avaliativos 
         WHERE ca_id = (
           SELECT matriz_criterio_avaliativo 
           FROM smc_matriz 
           WHERE matriz_id = (
             SELECT turma_matriz_id 
             FROM smc_turma 
             WHERE turma_id = (
               SELECT vinculo_aluno_id_turma 
               FROM smc_vinculo_aluno 
               WHERE vinculo_aluno_id = %s
             )
           )
         )",
    GetSQLValueString($vinculoAlunoId, "int")
  );

  $notaMinimaResult = mysql_query($queryNotaMinima, $SmecelNovo);
  if (!$notaMinimaResult) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao buscar a nota mínima: ' . mysql_error()]);
    exit;
  }

  $notaMinimaRow = mysql_fetch_assoc($notaMinimaResult);
  $notaMinima = (float) $notaMinimaRow['ca_nota_min_recuperacao_final'];

  $queryDisciplinas = sprintf(
    "SELECT matriz_disciplina_id_disciplina 
         FROM smc_matriz_disciplinas 
         WHERE matriz_disciplina_id_matriz = (
           SELECT turma_matriz_id 
           FROM smc_turma 
           WHERE turma_id = (
             SELECT vinculo_aluno_id_turma 
             FROM smc_vinculo_aluno 
             WHERE vinculo_aluno_id = %s
           )
         )",
    GetSQLValueString($vinculoAlunoId, "int")
  );

  $disciplinasResult = mysql_query($queryDisciplinas, $SmecelNovo);
  if (!$disciplinasResult) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao buscar disciplinas: ' . mysql_error()]);
    exit;
  }

  while ($disciplinaRow = mysql_fetch_assoc($disciplinasResult)) {
    $disciplinaId = $disciplinaRow['matriz_disciplina_id_disciplina'];

    $queryNotaFinal = sprintf(
      "SELECT nota_id, nota_valor 
             FROM smc_nota 
             WHERE nota_id_matricula = %s 
               AND nota_id_disciplina = %s 
               AND nota_periodo = 99 
               AND nota_num_avaliacao = 99",
      GetSQLValueString($vinculoAlunoId, "int"),
      GetSQLValueString($disciplinaId, "int")
    );

    $notaFinalResult = mysql_query($queryNotaFinal, $SmecelNovo);
    if ($notaFinalResult && mysql_num_rows($notaFinalResult) > 0) {
      $notaFinalRow = mysql_fetch_assoc($notaFinalResult);
      $notaId = $notaFinalRow['nota_id'];
      $notaValorAtual = $notaFinalRow['nota_valor'] !== null ? (float) $notaFinalRow['nota_valor'] : null;

      // Atualiza somente se a nota atual for menor que a mínima
      if ($notaValorAtual !== null && $notaValorAtual < $notaMinima) {
        $updateNota = sprintf(
          "UPDATE smc_nota 
                     SET nota_valor = %s 
                     WHERE nota_id = %s",
          GetSQLValueString($notaMinima, "double"),
          GetSQLValueString($notaId, "int")
        );

        if (!mysql_query($updateNota, $SmecelNovo)) {
          echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar nota final: ' . mysql_error()]);
          exit;
        }
      }
    }
  }
}

// Resposta de sucesso
echo json_encode([
  'status' => 'success',
  'message' => 'Operação realizada com sucesso.'
]);

