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
    
require_once('../../Connections/SmecelNovo.php');
include "fnc/session.php";
include "usuLogado.php";

// Função para log
function logDebug($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log .= " - Dados: " . print_r($data, true);
    }
    error_log($log . "\n", 3, "matricula_debug.log");
}

// Log dos dados recebidos
logDebug("Dados recebidos via POST", $_POST);

if (!isset($_POST['turma_id']) || !isset($_POST['alunos']) || !is_array($_POST['alunos'])) {
    logDebug("Erro: Dados inválidos recebidos");
    echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']);
    exit;
}

$turma_id = $_POST['turma_id'];
$alunos = $_POST['alunos'];
$ano_letivo = $_POST['ano_letivo'];
$escola_id = $_POST['escola_id'];
$sec_id = $_POST['sec_id'];

logDebug("Dados processados", [
    'turma_id' => $turma_id,
    'alunos' => $alunos,
    'ano_letivo' => $ano_letivo,
    'escola_id' => $escola_id,
    'sec_id' => $sec_id
]);

mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Verificar se o usuário tem permissão para inserir
if ($row_UsuLogado['usu_insert'] != "S") {
    logDebug("Erro: Usuário sem permissão", ['usu_id' => $row_UsuLogado['usu_id']]);
    echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para realizar esta operação.']);
    exit;
}

// Verificar se a turma existe e tem vagas disponíveis
$query_turma = sprintf(
    "SELECT turma_id, turma_total_alunos, 
     (SELECT COUNT(*) FROM smc_vinculo_aluno WHERE vinculo_aluno_id_turma = %s AND vinculo_aluno_ano_letivo = %s) as alunos_matriculados 
     FROM smc_turma 
     WHERE turma_id = %s",
    GetSQLValueString($turma_id, "int"),
    GetSQLValueString($ano_letivo, "text"),
    GetSQLValueString($turma_id, "int")
);

logDebug("Query de verificação da turma", $query_turma);

$result_turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($result_turma);

logDebug("Resultado da verificação da turma", $row_turma);

if (!$row_turma) {
    logDebug("Erro: Turma não encontrada", ['turma_id' => $turma_id]);
    echo json_encode(['status' => 'error', 'message' => 'Turma não encontrada.']);
    exit;
}

$vagas_disponiveis = $row_turma['turma_total_alunos'] - $row_turma['alunos_matriculados'];
logDebug("Vagas disponíveis", [
    'total_vagas' => $row_turma['turma_total_alunos'],
    'alunos_matriculados' => $row_turma['alunos_matriculados'],
    'vagas_disponiveis' => $vagas_disponiveis,
    'alunos_para_matricular' => count($alunos)
]);

if (count($alunos) > $vagas_disponiveis) {
    logDebug("Erro: Vagas insuficientes");
    echo json_encode(['status' => 'error', 'message' => 'Não há vagas suficientes na turma.']);
    exit;
}

// Iniciar transação
mysql_query("START TRANSACTION", $SmecelNovo);
logDebug("Iniciando transação");

try {
    foreach ($alunos as $aluno_id) {
        logDebug("Processando aluno", ['aluno_id' => $aluno_id]);

        // Buscar dados do vínculo anterior do aluno
        $query_vinculo_anterior = sprintf(
            "SELECT 
                v.vinculo_aluno_transporte,
                v.vinculo_aluno_da_casa,
                v.vinculo_aluno_vacina_atualizada,
                v.vinculo_aluno_internet,
                v.vinculo_aluno_repetente,
                v.vinculo_aluno_saida,
                v.vinculo_aluno_ponto_id,
                v.vinculo_aluno_id_cuidador,
                v.vinculo_aluno_nao_reprova
            FROM smc_vinculo_aluno v
            INNER JOIN smc_turma t ON t.turma_id = v.vinculo_aluno_id_turma
            WHERE v.vinculo_aluno_id_aluno = %s 
            AND v.vinculo_aluno_ano_letivo = %s
            AND t.turma_tipo_atendimento = 1
            ORDER BY v.vinculo_aluno_id DESC 
            LIMIT 1",
            GetSQLValueString($aluno_id, "int"),
            GetSQLValueString($ano_letivo - 1, "text")
        );

        logDebug("Query de busca do vínculo anterior", $query_vinculo_anterior);
        
        $result_vinculo_anterior = mysql_query($query_vinculo_anterior, $SmecelNovo);
        $row_vinculo_anterior = mysql_fetch_assoc($result_vinculo_anterior);

        logDebug("Dados do vínculo anterior", $row_vinculo_anterior);

        // Verificar se o aluno já está matriculado na turma
        $query_verifica = sprintf(
            "SELECT COUNT(*) as total FROM smc_vinculo_aluno 
             WHERE vinculo_aluno_id_aluno = %s 
             AND vinculo_aluno_id_turma = %s 
             AND vinculo_aluno_ano_letivo = %s",
            GetSQLValueString($aluno_id, "int"),
            GetSQLValueString($turma_id, "int"),
            GetSQLValueString($ano_letivo, "text")
        );

        logDebug("Query de verificação do aluno", $query_verifica);

        $result_verifica = mysql_query($query_verifica, $SmecelNovo) or die(mysql_error());
        $row_verifica = mysql_fetch_assoc($result_verifica);

        logDebug("Resultado da verificação do aluno", $row_verifica);

        if ($row_verifica['total'] > 0) {
            logDebug("Aluno já matriculado, pulando", ['aluno_id' => $aluno_id]);
            continue;
        }

        $hash = md5($aluno_id . time());
        $codVerificacao = generateRandomString() . '-' . generateRandomString() . '-' . generateRandomString() . '-' . generateRandomString();

        // Usar dados do vínculo anterior se disponível, senão usar valores padrão
        $transporte = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_transporte'] : 'N';
        $da_casa = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_da_casa'] : 'C';
        $vacina_atualizada = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_vacina_atualizada'] : 'S';
        $internet = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_internet'] : 'S';
        $repetente = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_repetente'] : 'N';
        $saida = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_saida'] : 1;
        $ponto_id = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_ponto_id'] : 'NULL';
        $cuidador_id = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_id_cuidador'] : 'NULL';
        $nao_reprova = $row_vinculo_anterior ? $row_vinculo_anterior['vinculo_aluno_nao_reprova'] : 'N';

        $insertSQL = sprintf(
            "INSERT INTO smc_vinculo_aluno (
                vinculo_aluno_id_aluno, 
                vinculo_aluno_id_turma, 
                vinculo_aluno_id_escola, 
                vinculo_aluno_id_sec, 
                vinculo_aluno_ano_letivo, 
                vinculo_aluno_data, 
                vinculo_aluno_hash, 
                vinculo_aluno_verificacao,
                vinculo_aluno_transporte,
                vinculo_aluno_da_casa,
                vinculo_aluno_vacina_atualizada,
                vinculo_aluno_internet,
                vinculo_aluno_repetente,
                vinculo_aluno_saida,
                vinculo_aluno_ponto_id,
                vinculo_aluno_id_cuidador,
                vinculo_aluno_nao_reprova
            ) VALUES (
                %s, %s, %s, %s, %s, %s, %s, %s, 
                %s, %s, %s, %s, %s, %s, %s, %s, %s
            )",
            GetSQLValueString($aluno_id, "int"),
            GetSQLValueString($turma_id, "int"),
            GetSQLValueString($escola_id, "int"),
            GetSQLValueString($sec_id, "int"),
            GetSQLValueString($ano_letivo, "text"),
            GetSQLValueString(date('Y-m-d'), "date"),
            GetSQLValueString($hash, "text"),
            GetSQLValueString($codVerificacao, "text"),
            GetSQLValueString($transporte, "text"),
            GetSQLValueString($da_casa, "text"),
            GetSQLValueString($vacina_atualizada, "text"),
            GetSQLValueString($internet, "text"),
            GetSQLValueString($repetente, "text"),
            GetSQLValueString($saida, "int"),
            $ponto_id === 'NULL' ? 'NULL' : GetSQLValueString($ponto_id, "int"),
            $cuidador_id === 'NULL' ? 'NULL' : GetSQLValueString($cuidador_id, "int"),
            GetSQLValueString($nao_reprova, "text")
        );

        logDebug("Query de inserção do aluno", $insertSQL);

        $result_insert = mysql_query($insertSQL, $SmecelNovo);
        if (!$result_insert) {
            logDebug("Erro na inserção do aluno", ['error' => mysql_error()]);
            throw new Exception("Erro ao inserir aluno: " . mysql_error());
        }

        logDebug("Aluno inserido com sucesso", ['aluno_id' => $aluno_id]);

        // Registrar a ação no log
        $sql_log = sprintf(
            "INSERT INTO smc_registros (
                registros_id_escola, 
                registros_id_usuario, 
                registros_tipo, 
                registros_complemento, 
                registros_data_hora
            ) VALUES (%s, %s, '9', %s, %s)",
            GetSQLValueString($escola_id, "int"),
            GetSQLValueString($row_UsuLogado['usu_id'], "int"),
            GetSQLValueString("Aluno ID: $aluno_id matriculado na turma ID: $turma_id", "text"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date")
        );

        logDebug("Query de log", $sql_log);

        $result_log = mysql_query($sql_log, $SmecelNovo);
        if (!$result_log) {
            logDebug("Erro ao registrar log", ['error' => mysql_error()]);
            throw new Exception("Erro ao registrar log: " . mysql_error());
        }

        logDebug("Log registrado com sucesso");
    }

    // Confirmar transação
    mysql_query("COMMIT", $SmecelNovo);
    logDebug("Transação confirmada com sucesso");
    echo json_encode(['status' => 'success', 'message' => 'Matrículas realizadas com sucesso.']);

} catch (Exception $e) {
    // Reverter transação em caso de erro
    mysql_query("ROLLBACK", $SmecelNovo);
    logDebug("Erro na transação", ['error' => $e->getMessage()]);
    echo json_encode(['status' => 'error', 'message' => 'Erro ao realizar matrículas: ' . $e->getMessage()]);
}

// Função auxiliar para gerar string aleatória
function generateRandomString($length = 4) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?> 