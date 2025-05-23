<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '512M');

// Configuração da conexão com o banco de dados
//$host = 'smecel.vpshost11322.mysql.dbaas.com.br';
//$username = 'smecel';
//$password = 'Drw4tson@smece';
//$dbname = 'smecel';

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'smecel1';

// Funções
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

// Conectar ao banco de dados
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Erro na conexão: " . $mysqli->connect_error);
}
echo "Conexão ao banco de dados estabelecida com sucesso!<br>";

// Caminho do arquivo TXT
$arquivo_txt = 'canavieiras.txt';
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

// Contador para linhas processadas
$linhas_processadas = 0;

$handle = fopen($arquivo_txt, 'r');
if ($handle) {
    while (($linha = fgets($handle)) !== false) {
        $campos = explode('|', trim($linha));
        if (count($campos) < 44 || $campos[0] !== '30') {
            if (count($campos) >= 1 && $campos[0] !== '30') {
                echo "Linha ignorada (tipo de registro não é 30): " . $campos[0] . "<br>";
            } else {
                echo "Linha inválida (menos campos que o esperado): $linha<br>";
            }
            continue;
        }

        // Mapear os campos
        $aluno_cod_inep = $campos[3];
        $aluno_cpf = $campos[4] ?: null;
        $aluno_nome = $campos[5];
        $aluno_nascimento = converterData($campos[6]);
        $aluno_filiacao1 = $campos[8];
        $aluno_filiacao2 = $campos[9];
        $aluno_sexo = (int)$campos[10];
        $aluno_raca = (int)$campos[11];
        $aluno_nacionalidade = (int)$campos[12];
        $aluno_pais = (int)$campos[13];
        $aluno_uf_nascimento = "BA";
        $aluno_municipio_nascimento_ibge = $campos[14];
        $aluno_aluno_com_deficiencia = (int)$campos[15];
        $aluno_num_matricula_modelo_novo = $campos[39] ?: null;
        $aluno_cep = formatarCEP($campos[41]);
        $aluno_localizacao = (int)$campos[43];
        $aluno_hash = md5($aluno_nome . $aluno_nascimento . $aluno_cod_inep);

        echo "Processando: $aluno_cod_inep|$aluno_cpf|$aluno_nome|$aluno_nascimento|$aluno_filiacao1|$aluno_filiacao2|$aluno_sexo|$aluno_raca|$aluno_nacionalidade|$aluno_pais|$aluno_uf_nascimento|$aluno_municipio_nascimento_ibge|$aluno_aluno_com_deficiencia|$aluno_num_matricula_modelo_novo|$aluno_cep|$aluno_localizacao|$aluno_hash<br>";

        $stmt->bind_param(
            'sssssssssssssssss',
            $aluno_cod_inep, $aluno_cpf, $aluno_nome, $aluno_nascimento,
            $aluno_filiacao1, $aluno_filiacao2, $aluno_sexo, $aluno_raca, $aluno_nacionalidade,
            $aluno_pais, $aluno_uf_nascimento, $aluno_municipio_nascimento_ibge,
            $aluno_aluno_com_deficiencia, $aluno_num_matricula_modelo_novo, $aluno_cep,
            $aluno_localizacao, $aluno_hash
        );

        if ($stmt->execute()) {
            echo " | Aluno '$aluno_nome' inserido com sucesso!<br>";
            $linhas_processadas++;
        } else {
            echo "Erro ao inserir '$aluno_nome': " . $stmt->error . "<br>";
        }
    }
    fclose($handle);
    echo "Total de linhas processadas e inseridas: $linhas_processadas<br>";
} else {
    die("Erro ao abrir o arquivo $arquivo_txt!");
}

$stmt->close();
$mysqli->close();
echo "Processamento concluído!";
?>