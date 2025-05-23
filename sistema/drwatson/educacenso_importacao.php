<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '512M');


// Configuração da conexão com o banco de dados
$host = 'smecel.vpshost11322.mysql.dbaas.com.br';
$username = 'smecel';
$password = 'Drw4tson@smece';
$dbname = 'smecel';

//$host = 'localhost';
//$username = 'root';
//$password = '';
//$dbname = 'smecel1';

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Erro na conexão: " . $mysqli->connect_error);
}

// Caminho do arquivo TXT
$arquivo_txt = 'itagimirim.txt';
if (!file_exists($arquivo_txt)) {
    die("Arquivo $arquivo_txt não encontrado!");
}

// Query de inserção
$query = "
    INSERT INTO smc_aluno (
        aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento,
        aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade,
        aluno_pais, aluno_uf_nascimento, aluno_municipio_nascimento_ibge,
        aluno_aluno_com_deficiencia, aluno_num_matricula_modelo_novo, aluno_cep,
        aluno_localizacao, aluno_hash
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die("Erro ao preparar a query: " . $mysqli->error);
}

// Funções auxiliares
function converterData($data) {
    if ($data && strlen($data) == 10) {
        return DateTime::createFromFormat('d/m/Y', $data)->format('Y-m-d');
    }
    return null;
}

function extrairUF($cod_ibge) {
    if ($cod_ibge && strlen($cod_ibge) >= 2) {
        return substr($cod_ibge, 0, 2);
    }
    return null;
}

function formatarCEP($cep) {
    if ($cep && strlen($cep) == 8) {
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    return $cep;
}

// Contador para linhas processadas/inseridas
$linhas_processadas = 0;

$handle = fopen($arquivo_txt, 'r');
if ($handle) {
    while (($linha = fgets($handle)) !== false) {
        $campos = explode('|', trim($linha));
        
        // Processa apenas linhas que iniciam com "30" e que possuam pelo menos 44 campos
        if (count($campos) < 44 || $campos[0] !== '30') {
            continue;
        }
        
        // Consulta para obter a UF a partir da tabela smc_municipio
        $query_check1 = "SELECT municipio_sigla_uf FROM smc_municipio WHERE municipio_cod_ibge = ?";
        $stmt_check1 = $mysqli->prepare($query_check1);
        $stmt_check1->bind_param('s', $campos[14]);
        $stmt_check1->execute();
        $result1 = $stmt_check1->get_result();
        $row1 = $result1->fetch_assoc();
        $stmt_check1->close();

        // Mapeamento dos campos
        $aluno_cod_inep                  = $campos[3];
        $aluno_cpf                       = $campos[4] ?: null;
        $aluno_nome                      = $campos[5];
        $aluno_nascimento                = converterData($campos[6]);
        $aluno_filiacao1                 = $campos[8];
        $aluno_filiacao2                 = $campos[9];
        $aluno_sexo                      = (int)$campos[10];
        $aluno_raca                      = (int)$campos[11];
        $aluno_nacionalidade             = (int)$campos[12];
        $aluno_pais                      = (int)$campos[13];
        $aluno_uf_nascimento             = $row1['municipio_sigla_uf'];
        $aluno_municipio_nascimento_ibge = $campos[14];
        $aluno_aluno_com_deficiencia     = (int)$campos[15];
        $aluno_num_matricula_modelo_novo  = $campos[39] ?: null;
        $aluno_cep                       = formatarCEP($campos[41]);
        $aluno_localizacao               = (int)$campos[43];
        $aluno_hash                      = md5($aluno_nome . $aluno_nascimento . $aluno_cod_inep);

        // Verifica se o registro já existe no banco (comparando INEP e, se disponível, CPF)
        if ($aluno_cpf !== null && $aluno_cpf !== '') {
            $query_check = "SELECT COUNT(*) as total FROM smc_aluno WHERE aluno_cod_inep = ? OR aluno_cpf = ?";
            $stmt_check = $mysqli->prepare($query_check);
            if (!$stmt_check) {
                die("Erro ao preparar a query de verificação: " . $mysqli->error);
            }
            $stmt_check->bind_param('ss', $aluno_cod_inep, $aluno_cpf);
        } else {
            $query_check = "SELECT COUNT(*) as total FROM smc_aluno WHERE aluno_cod_inep = ?";
            $stmt_check = $mysqli->prepare($query_check);
            if (!$stmt_check) {
                die("Erro ao preparar a query de verificação: " . $mysqli->error);
            }
            $stmt_check->bind_param('s', $aluno_cod_inep);
        }
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row   = $result->fetch_assoc();
        if ($row['total'] > 0) {
            $stmt_check->close();
            continue;
        }
        $stmt_check->close();

        // Bind dos parâmetros e execução do INSERT
        $stmt->bind_param(
            'sssssssssssssssss',
            $aluno_cod_inep, $aluno_cpf, $aluno_nome, $aluno_nascimento,
            $aluno_filiacao1, $aluno_filiacao2, $aluno_sexo, $aluno_raca, $aluno_nacionalidade,
            $aluno_pais, $aluno_uf_nascimento, $aluno_municipio_nascimento_ibge,
            $aluno_aluno_com_deficiencia, $aluno_num_matricula_modelo_novo, $aluno_cep,
            $aluno_localizacao, $aluno_hash
        );

        if ($stmt->execute()) {
            $linhas_processadas++;
        }
    }
    fclose($handle);
} else {
    die("Erro ao abrir o arquivo $arquivo_txt!");
}

$stmt->close();
$mysqli->close();

// Exibe apenas o resumo final
echo "<p>Processamento concluído!</p>";
echo "<p>Total de linhas processadas e inseridas: $linhas_processadas</p>";

?>