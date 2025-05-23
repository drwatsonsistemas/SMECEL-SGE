<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/anti_injection.php'); ?>
<?php include('../funcoes/idade.php'); ?>
<?php include('../funcoes/idadeSerie.php'); ?>

<?php
// Inicializar sessão
if (!isset($_SESSION)) {
  session_start();
}

// Ação de logout
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../../../index.php?exit";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

// Restringir acesso
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
  $isValid = False;
  if (!empty($UserName)) {
    $arrUsers = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
    if (in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
    if (($strUsers == "") && false) {
      $isValid = true;
    }
  }
  return $isValid;
}

$MM_restrictGoTo = "../../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}

// Função GetSQLValueString
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

// Usuário logado
$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

// Ano letivo
require_once('../funcoes/anoLetivo.php');
$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
  if ($_GET['ano'] == "") {
    $anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
  }
  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}

$data_corte = date('Y-m-d', strtotime("last wednesday of may $anoLetivo"));


// Secretaria
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

// Consulta para o relatório de distorção idade-série
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Distorcao = "
SELECT 
    aluno_id,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN vinculo_aluno_multietapa
        ELSE turma_etapa 
    END AS etapa_id,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN e2.etapa_nome
        ELSE e1.etapa_nome
    END AS etapa_nome,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN e2.etapa_idade
        ELSE e1.etapa_idade
    END AS etapa_idade,
    aluno_nascimento
    
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
LEFT JOIN smc_etapa e1 ON e1.etapa_id = turma_etapa
LEFT JOIN smc_etapa e2 ON e2.etapa_id = vinculo_aluno_multietapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE turma_tipo_atendimento = 1 
    AND vinculo_aluno_situacao = 1
    AND escola_situacao = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_id_sec = '$row_Secretaria[sec_id]'
    AND vinculo_aluno_data <= '$data_corte'
ORDER BY etapa_id
";
$Distorcao = mysql_query($query_Distorcao, $SmecelNovo) or die(mysql_error());

// Processar dados para distorção
$dados_etapas = [];
$ids = [];
while ($row = mysql_fetch_assoc($Distorcao)) {
    $ids[] = $row['aluno_id'];
    $etapa_id = $row['etapa_id'];
    
    // Calcular idade na data de corte
    $nascimento = new DateTime($row['aluno_nascimento']);
    $data_corte_obj = new DateTime($data_corte);
    $idade = $nascimento->diff($data_corte_obj)->y;
    
    $idade_esperada = $row['etapa_idade'];
    $diferenca = $idade - $idade_esperada;

    // Inicializa a etapa se não existir
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

    // Conta o aluno na etapa
    $dados_etapas[$etapa_id]['matriculas'] += 1;

    // Conta o aluno na faixa de idade
    if ($idade >= 0 && $idade <= 16) {
        $dados_etapas[$etapa_id]['idades'][$idade] += 1;
    }

    // Conta distorção
    if ($diferenca > 3 && $idade_esperada != 99) { // Mais de 3 anos (exceto EJA)
        $dados_etapas[$etapa_id]['mais_3_anos'] += 1;
        $dados_etapas[$etapa_id]['total_distorcao'] += 1;
    } elseif ($diferenca > 1 && $idade_esperada != 99) { // Distorção de 1 a 3 anos
        $dados_etapas[$etapa_id]['total_distorcao'] += 1;
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
        <?php if ($row_Secretaria['sec_logo'] <> "") { ?>
          <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>"
            alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>"
            title="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" width="60" />
        <?php } else { ?>
          <img src="../../../img/brasao_republica.png" width="60">
        <?php } ?>
        <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
        <?php echo $row_Secretaria['sec_nome']; ?>
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
        <td>
          <?php
            // Soma correta: total de alunos únicos processados
            echo count(array_unique($ids));
          ?>
        </td>
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
  <p class="ls-txt-center"><?php echo $row_Secretaria['sec_nome'] ?> - <?php echo strtoupper(date('m/Y')); ?></p>
  
  <div class="ls-txt-center">
    <small>Data de corte: <?php echo date('d/m/Y', strtotime($data_corte)); ?></small><br>
    <small><i>Relatório impresso em <?php echo date('d/m/Y'); ?> às <?php echo date('H:i:s'); ?>. <br>SMECEL -
      Sistema de Gestão Escolar</i></small>
  </div>

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
mysql_free_result($UsuarioLogado);
mysql_free_result($Secretaria);
mysql_free_result($Distorcao);

?>