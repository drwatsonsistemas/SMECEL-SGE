<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php 

// Verifica se é uma requisição AJAX
// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
//     die('Acesso direto não permitido');
// }

// Obtém o ID da secretaria
$sec_id = isset($_GET['sec_id']) ? intval($_GET['sec_id']) : 0;

if ($sec_id <= 0) {
    die('Parâmetros inválidos');
}

// Seleciona o banco de dados
mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Monta a consulta
$query = "
SELECT
    p.Tipo_Semelhanca,
    a1.aluno_id   AS id1,
    a1.aluno_nome AS Nome1,
    a1.aluno_cpf  AS CPF1,
    a1.aluno_identidade AS RG1,
    -- vínculos do aluno 1
    (
      SELECT GROUP_CONCAT(DISTINCT CONCAT(e.escola_nome,' – ',t.turma_nome) SEPARATOR '; ')
      FROM smc_vinculo_aluno va
      JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
      JOIN smc_escola e ON e.escola_id = t.turma_id_escola
      WHERE va.vinculo_aluno_id_aluno = a1.aluno_id
        AND va.vinculo_aluno_situacao = '1'
        AND va.vinculo_aluno_id_sec     = '$sec_id'
    ) AS Vinculos1,
    a2.aluno_id   AS id2,
    a2.aluno_nome AS Nome2,
    a2.aluno_cpf  AS CPF2,
    a2.aluno_identidade AS RG2,
    -- vínculos do aluno 2
    (
      SELECT GROUP_CONCAT(DISTINCT CONCAT(e.escola_nome,' – ',t.turma_nome) SEPARATOR '; ')
      FROM smc_vinculo_aluno va
      JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
      JOIN smc_escola e ON e.escola_id = t.turma_id_escola
      WHERE va.vinculo_aluno_id_aluno = a2.aluno_id
        AND va.vinculo_aluno_situacao = '1'
        AND va.vinculo_aluno_id_sec     = '$sec_id'
    ) AS Vinculos2
FROM (
  -- pega apenas os pares de IDs que satisfazem um dos critérios
  SELECT DISTINCT
    x1.aluno_id AS id1,
    x2.aluno_id AS id2,
    CASE
      WHEN REPLACE(REPLACE(REPLACE(x1.aluno_cpf, '.', ''), '-', ''), '/', '')
         = REPLACE(REPLACE(REPLACE(x2.aluno_cpf, '.', ''), '-', ''), '/', '')
      THEN 'CPF igual'
      WHEN REPLACE(REPLACE(x1.aluno_identidade, '.', ''), '-', '')
         = REPLACE(REPLACE(x2.aluno_identidade, '.', ''), '-', '')
      THEN 'RG igual'
      ELSE 'Nome idêntico'
    END AS Tipo_Semelhanca
  FROM smc_aluno x1
  JOIN smc_vinculo_aluno v1
    ON v1.vinculo_aluno_id_aluno = x1.aluno_id
   AND v1.vinculo_aluno_situacao = '1'
   AND v1.vinculo_aluno_id_sec     = '$sec_id'
  JOIN smc_aluno x2
    ON x1.aluno_id < x2.aluno_id
  JOIN smc_vinculo_aluno v2
    ON v2.vinculo_aluno_id_aluno = x2.aluno_id
   AND v2.vinculo_aluno_situacao = '1'
   AND v2.vinculo_aluno_id_sec     = '$sec_id'
  WHERE
    (
      -- CPF igual (apenas quando ambos têm CPF não vazio)
      REPLACE(REPLACE(REPLACE(x1.aluno_cpf, '.', ''), '-', ''), '/', '')
      = REPLACE(REPLACE(REPLACE(x2.aluno_cpf, '.', ''), '-', ''), '/', '')
      AND x1.aluno_cpf  <> ''
      AND x2.aluno_cpf  <> ''
    )
    OR
    (
      -- RG igual (apenas quando ambos têm RG não vazio)
      REPLACE(REPLACE(x1.aluno_identidade, '.', ''), '-', '')
      = REPLACE(REPLACE(x2.aluno_identidade, '.', ''), '-', '')
      AND x1.aluno_identidade <> ''
      AND x2.aluno_identidade <> ''
    )
    OR
    (
      -- Nome idêntico (apenas quando não vazio)
      x1.aluno_nome = x2.aluno_nome
      AND x1.aluno_nome <> ''
      AND x2.aluno_nome <> ''
    )
) AS p
-- traz os dados dos alunos para exibição
JOIN smc_aluno a1 ON a1.aluno_id = p.id1
JOIN smc_aluno a2 ON a2.aluno_id = p.id2
ORDER BY
  FIELD(p.Tipo_Semelhanca, 'CPF igual','RG igual','Nome idêntico'),
  a1.aluno_nome";

$result = mysql_query($query, $SmecelNovo) or die(mysql_error());
// conta quantos pares vieram
$totalRows = mysql_num_rows($result);

if ($totalRows > 0) {
    echo '<table class="ls-table">';
    echo '<thead>';
    echo '  <tr>';
    echo '    <th>Tipo de Semelhança</th>';
    echo '    <th>ID 1</th>';
    echo '    <th>Aluno 1</th>';
    echo '    <th>CPF 1</th>';
    echo '    <th>RG 1</th>';
    echo '    <th>Vínculos 1</th>';
    echo '    <th>ID 2</th>';
    echo '    <th>Aluno 2</th>';
    echo '    <th>CPF 2</th>';
    echo '    <th>RG 2</th>';
    echo '    <th>Vínculos 2</th>';
    echo '  </tr>';
    echo '</thead>';
    echo '<tbody>';

    // percorre cada par encontrado
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr>';
        echo '  <td>' . htmlspecialchars($row['Tipo_Semelhanca']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['id1']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Nome1']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['CPF1']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['RG1']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Vinculos1']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['id2']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Nome2']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['CPF2']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['RG2']) . '</td>';
        echo '  <td>' . htmlspecialchars($row['Vinculos2']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>Nenhum possível aluno duplicado encontrado.</p>';
}

mysql_free_result($result);
?> 