<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('fnc/idade.php'); ?>
<?php include('fnc/idadeSerie.php'); ?>

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
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

// Conectar ao banco
mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Ano letivo atual
$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
// Consulta para o relatório de distorção idade-série
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Distorcao = "
SELECT 
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN vinculo_aluno_multietapa
        ELSE turma_etapa 
    END AS etapa_id,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN 
            (SELECT etapa_nome FROM smc_etapa WHERE etapa_id = vinculo_aluno_multietapa)
        ELSE 
            (SELECT etapa_nome FROM smc_etapa WHERE etapa_id = turma_etapa)
    END AS etapa_nome,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN 
            (SELECT etapa_idade FROM smc_etapa WHERE etapa_id = vinculo_aluno_multietapa)
        ELSE 
            (SELECT etapa_idade FROM smc_etapa WHERE etapa_id = turma_etapa)
    END AS etapa_idade,
    aluno_nascimento,
    COUNT(*) AS total_alunos
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_tipo_atendimento = 1 
    AND vinculo_aluno_situacao = '1'
    AND escola_situacao = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_id_escola = '$row_EscolaLogada[escola_id]'
GROUP BY 
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN vinculo_aluno_multietapa
        ELSE turma_etapa 
    END,
    aluno_nascimento
ORDER BY etapa_id";
$Distorcao = mysql_query($query_Distorcao, $SmecelNovo) or die(mysql_error());

// Processar dados para distorção
$dados_etapas = [];
while ($row = mysql_fetch_assoc($Distorcao)) {
  $etapa_id = $row['etapa_id'];
  $idade = idade($row['aluno_nascimento']);
  $idade_esperada = $row['etapa_idade'];
  $diferenca = $idade - $idade_esperada;

  if (!isset($dados_etapas[$etapa_id])) {
    $dados_etapas[$etapa_id] = [
      'nome' => $row['etapa_nome'],
      'idade_esperada' => $idade_esperada,
      'matriculas' => 0,
      'idades' => array_fill(0, 17, 0), // 0 a 16 anos
      'mais_3_anos' => 0,
      'total_distorcao' => 0
    ];
  }

  $dados_etapas[$etapa_id]['matriculas'] += $row['total_alunos'];
  if ($idade >= 0 && $idade <= 16) {
    $dados_etapas[$etapa_id]['idades'][$idade] += $row['total_alunos'];
  }
  if ($diferenca > 3 && $idade_esperada != 99) { // Mais de 3 anos (exceto EJA)
    $dados_etapas[$etapa_id]['mais_3_anos'] += $row['total_alunos'];
    $dados_etapas[$etapa_id]['total_distorcao'] += $row['total_alunos'];
  } elseif ($diferenca > 1 && $idade_esperada != 99) { // Distorção de 1 a 3 anos
    $dados_etapas[$etapa_id]['total_distorcao'] += $row['total_alunos'];
  }
}

// Mapear etapas para o relatório
$etapas_quadro = [
  1 => 'Berçário/Maternal',
  2 => 'Pré',
  14 => '1º Ano',
  15 => '2º Ano',
  16 => '3º Ano',
  17 => '4º Ano',
  18 => '5º Ano',
  19 => '6º Ano',
  20 => '7º Ano',
  21 => '8º Ano',
  22 => '9º Ano',
  36 => 'EJA I Eixo 1,2,3',
  37 => 'EJA II Eixo 4',
  38 => 'EJA II Eixo 5'
];

// Inicializar todas as etapas, mesmo as que não têm dados
foreach ($etapas_quadro as $etapa_id => $nome) {
  if (!isset($dados_etapas[$etapa_id])) {
    $dados_etapas[$etapa_id] = [
      'nome' => $nome,
      'idade_esperada' => getIdadeEsperada($etapa_id),
      'matriculas' => 0,
      'idades' => array_fill(0, 17, 0),
      'mais_3_anos' => 0,
      'total_distorcao' => 0
    ];
  } else {
    // Ensure the etapa_nome matches the mapped name
    $dados_etapas[$etapa_id]['nome'] = $nome;
  }
}

// Função auxiliar para definir idades esperadas// Adjust getIdadeEsperada
function getIdadeEsperada($etapa_id)
{
  $idades_esperadas = [
    1 => 3,  // Creche (adjusted to match smc_etapa)
    2 => 5,  // Pré-escola
    14 => 6, // 1º Ano
    15 => 7, // 2º Ano
    16 => 8, // 3º Ano
    17 => 9, // 4º Ano
    18 => 10, // 5º Ano
    19 => 11, // 6º Ano
    20 => 12, // 7º Ano
    21 => 13, // 8º Ano
    22 => 14, // 9º Ano
    36 => 99, // EJA I
    37 => 99, // EJA II Eixo 4
    38 => 99  // EJA II Eixo 5
  ];
  return isset($idades_esperadas[$etapa_id]) ? $idades_esperadas[$etapa_id] : 0;
}
?>
<!DOCTYPE html>
<html class="ls-theme-green">

<head>
  <title>SMECEL - Relatório de Distorção Idade-Série Municipal</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" type="text/css" href="../css/impressao.css">
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <style>
    table.bordasimples {
      border-collapse: collapse;
      width: 100%;
    }

    table.bordasimples tr td,
    table.bordasimples tr th {
      border: 1px solid #808080 !important;
      padding: 5px;
      text-align: center;
      font-size: 12px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    table.bordasimples tr th {
      font-size: 10px;
      background-color:rgb(240, 216, 82) !important;
      text-align: center;
      vertical-align: middle;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .distorcao-vermelha {
      background-color:rgb(248, 188, 188) !important;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    @media print {
      @page {
        size: A4 landscape;
      }

      .distorcao-vermelha {
        background-color: #ffcccc !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body onload="self.print();">
  <table class="bordasimples1" width="100%">
    <tr>
      <td class="ls-txt-center" width="60"></td>
      <td class="ls-txt-center">
      <?php if ($row_EscolaLogada['escola_logo'] <> "") { ?>
                <img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="60px" />
            <?php } else { ?>
                <img src="../../img/brasao_republica.png" alt="" width="60px" />
            <?php } ?><br>
            <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
            <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
                ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>,
                <?php echo $row_EscolaLogada['escola_num']; ?>,
                <?php echo $row_EscolaLogada['escola_bairro']; ?>
                <?php echo $row_EscolaLogada['sec_cidade']; ?>-
                <?php echo $row_EscolaLogada['sec_uf']; ?> CEP:
                <?php echo $row_EscolaLogada['escola_cep']; ?><br>
                CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
                <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>

      </td>
      <td class="ls-txt-center" width="60"></td>
    </tr>
  </table>
  <br>
  <h2 class="ls-txt-center">RELATÓRIO</h2>
  <br>
  <h3 class="ls-txt-center">QUADRO DISTORÇÃO IDADE-SÉRIE - ANO LETIVO <?php echo $anoLetivo; ?></h3>
  <br>
  <table class="bordasimples ls-sm-space" width="100%">
    <thead>
      <tr>
        <th style="min-width: 100px;">SÉRIE / IDADE</th>
        <th style="min-width: 70px;">MATRÍCULA</th>
        <?php
        // Ajustando as faixas etárias conforme a listagem
        for ($i = 0; $i <= 16; $i++) {
          if ($i == 0) {
            $label = "0 à 1 Ano e 6 Meses";
          } elseif ($i == 1) {
            $label = "1 Ano/7 M. à 2 Anos/11M";
          } elseif ($i == 2) {
            $label = "1 Ano/7 M. à 2 Anos/11M"; // Continua a mesma faixa para idade 2
          } elseif ($i == 3) {
            $label = "3 Anos à 3 Anos e 11 Meses";
          } else {
            $label = "$i <br> ANOS";
          }
          echo "<th style='min-width: 50px;'>$label</th>";
        }
        ?>
        <th style="min-width: 100px;">Mais de 16 ANOS</th>
        <th style="min-width: 100px;">Total de alunos com idade superior à série respectivas (B)</th>
        <th style="min-width: 100px;">Taxa de Distorção (B/A) x 100</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total_matriculas = 0;
      $total_distorcao = 0;
      foreach ($etapas_quadro as $etapa_id => $nome) {
        if (!isset($dados_etapas[$etapa_id])) {
          continue;
        }
        $d = $dados_etapas[$etapa_id];
        $taxa = $d['matriculas'] > 0 ? ($d['total_distorcao'] / $d['matriculas']) * 100 : 0;
        $idade_esperada = $d['idade_esperada'];
        echo "<tr>";
        echo "<td>$nome</td>";
        echo "<td>{$d['matriculas']}</td>";
        foreach ($d['idades'] as $idade => $idade_count) {
          $classe = ($idade == $idade_esperada + 1 && $idade_esperada != 99 && $idade_count > 0) ? 'distorcao-vermelha' : '';
          echo "<td class='$classe'>$idade_count</td>";
        }
        echo "<td>{$d['mais_3_anos']}</td>";
        echo "<td>{$d['total_distorcao']}</td>";
        echo "<td>" . number_format($taxa, 2) . "</td>";
        echo "</tr>";
        $total_matriculas += $d['matriculas'];
        $total_distorcao += $d['total_distorcao'];
      }
      $taxa_total = $total_matriculas > 0 ? ($total_distorcao / $total_matriculas) * 100 : 0;
      ?>
      <tr>
        <td>TOTAL</td>
        <td><?php echo $total_matriculas; ?></td>
        <?php
        for ($i = 0; $i <= 16; $i++) {
          $soma_idade = 0;
          foreach ($dados_etapas as $etapa) {
            $soma_idade += $etapa['idades'][$i];
          }
          echo "<td>$soma_idade</td>";
        }
        ?>
        <td><?php echo array_sum(array_column($dados_etapas, 'mais_3_anos')); ?></td>
        <td><?php echo $total_distorcao; ?></td>
        <td><?php echo number_format($taxa_total, 2); ?></td>
      </tr>
    </tbody>
  </table>
  <br>
  <p class="ls-txt-center"><?php echo $row_EscolaLogada['sec_cidade'] ?> - <?php echo strtoupper(date('m/Y')); ?></p>
  <p class="ls-txt-right">Relatório impresso em <?php echo date("d/m/Y \à\s H:i"); ?><br>SMECEL | Sistema de Gestão
    Escolar</p>

  <p style="font-size:11px; margin-top:10px;">
    <strong>OBSERVAÇÃO:</strong> O quadro vermelho equivale ao 1º ano de distorção, a partir dele sucessivamente. O EJA
    não tem distorção mas é necessário fazer o quadro.<br>
    <span style="font-size:10px;">
      Turmas por Faixa Etária: CRECHE: Bebês: 0 a 1 ano e 6 meses; Crianças bem pequenas: 1 ano e 7 meses à 2 anos e 11
      meses; 3 anos à 3 anos e 11 meses. Pré-Escola: Pré-I - 4 Anos; Pré-II - 5 Anos
    </span>
  </p>

  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"></script>
</body>

</html>

<?php
mysql_free_result($Distorcao);
?>