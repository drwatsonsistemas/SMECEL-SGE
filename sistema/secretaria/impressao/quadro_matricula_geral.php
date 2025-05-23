<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/anti_injection.php'); ?>

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

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
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
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}

// Função GetSQLValueString
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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
  $anoLetivo = (int)$anoLetivo;
}

// Secretaria
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

// Consulta de matrículas por etapa, idade e sexo, com redistribuição de multisseriados
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "
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
    SUM(CASE WHEN TIMESTAMPDIFF(MONTH, aluno_nascimento, CURDATE()) <= 6 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino0a6Meses,
    SUM(CASE WHEN TIMESTAMPDIFF(MONTH, aluno_nascimento, CURDATE()) <= 6 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino0a6Meses,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 0 AND TIMESTAMPDIFF(MONTH, aluno_nascimento, CURDATE()) > 6 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino0Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 0 AND TIMESTAMPDIFF(MONTH, aluno_nascimento, CURDATE()) > 6 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino0Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 1 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino1Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 1 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino1Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 2 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino2Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 2 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino2Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 3 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino3Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 3 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino3Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 4 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino4Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 4 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino4Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 5 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino5Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 5 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino5Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 6 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino6Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 6 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino6Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 7 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino7Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 7 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino7Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 8 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino8Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 8 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino8Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 9 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino9Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 9 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino9Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 10 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino10Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 10 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino10Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 11 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino11Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 11 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino11Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 12 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino12Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 12 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino12Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 13 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino13Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 13 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino13Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 14 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino14Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 14 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino14Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 15 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino15Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 15 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino15Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 16 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS Feminino16Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) = 16 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS Masculino16Ano,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) > 16 AND aluno_sexo = 2 THEN 1 ELSE 0 END) AS FemininoMais16,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, aluno_nascimento, CURDATE()) > 16 AND aluno_sexo = 1 THEN 1 ELSE 0 END) AS MasculinoMais16,
    SUM(CASE WHEN aluno_sexo = 2 THEN 1 ELSE 0 END) AS TotalFeminino,
    SUM(CASE WHEN aluno_sexo = 1 THEN 1 ELSE 0 END) AS TotalMasculino,
    COUNT(*) AS TotalGeral
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_situacao = 1 
    AND turma_tipo_atendimento = 1 
    AND escola_situacao = '1'
    AND vinculo_aluno_ano_letivo = '$anoLetivo'
    AND turma_ano_letivo = '$anoLetivo'
    AND turma_id_sec = '$row_Secretaria[sec_id]'
GROUP BY 
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN vinculo_aluno_multietapa
        ELSE turma_etapa 
    END,
    CASE 
        WHEN (turma_multisseriada = 1 AND vinculo_aluno_multietapa IS NOT NULL) THEN 
            (SELECT etapa_nome FROM smc_etapa WHERE etapa_id = vinculo_aluno_multietapa)
        ELSE 
            (SELECT etapa_nome FROM smc_etapa WHERE etapa_id = turma_etapa)
    END
ORDER BY etapa_id
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());

// Processar dados
$dados_etapas = [];
$total_0a6meses_f = 0; $total_0a6meses_m = 0;
$total_0ano_f = 0; $total_0ano_m = 0;
$total_1ano_f = 0; $total_1ano_m = 0;
$total_2ano_f = 0; $total_2ano_m = 0;
$total_3ano_f = 0; $total_3ano_m = 0;
$total_4ano_f = 0; $total_4ano_m = 0;
$total_5ano_f = 0; $total_5ano_m = 0;
$total_6ano_f = 0; $total_6ano_m = 0;
$total_7ano_f = 0; $total_7ano_m = 0;
$total_8ano_f = 0; $total_8ano_m = 0;
$total_9ano_f = 0; $total_9ano_m = 0;
$total_10ano_f = 0; $total_10ano_m = 0;
$total_11ano_f = 0; $total_11ano_m = 0;
$total_12ano_f = 0; $total_12ano_m = 0;
$total_13ano_f = 0; $total_13ano_m = 0;
$total_14ano_f = 0; $total_14ano_m = 0;
$total_15ano_f = 0; $total_15ano_m = 0;
$total_16ano_f = 0; $total_16ano_m = 0;
$total_mais16_f = 0; $total_mais16_m = 0;
$total_feminino = 0; $total_masculino = 0;
$total_geral = 0;

while ($row = mysql_fetch_assoc($Matriculas)) {
    $etapa_id = $row['etapa_id'];
    if (!isset($dados_etapas[$etapa_id])) {
        $dados_etapas[$etapa_id] = [
            'etapa_id' => $etapa_id,
            'etapa_nome' => $row['etapa_nome'],
            'Feminino0a6Meses' => 0,
            'Masculino0a6Meses' => 0,
            'Feminino0Ano' => 0,
            'Masculino0Ano' => 0,
            'Feminino1Ano' => 0,
            'Masculino1Ano' => 0,
            'Feminino2Ano' => 0,
            'Masculino2Ano' => 0,
            'Feminino3Ano' => 0,
            'Masculino3Ano' => 0,
            'Feminino4Ano' => 0,
            'Masculino4Ano' => 0,
            'Feminino5Ano' => 0,
            'Masculino5Ano' => 0,
            'Feminino6Ano' => 0,
            'Masculino6Ano' => 0,
            'Feminino7Ano' => 0,
            'Masculino7Ano' => 0,
            'Feminino8Ano' => 0,
            'Masculino8Ano' => 0,
            'Feminino9Ano' => 0,
            'Masculino9Ano' => 0,
            'Feminino10Ano' => 0,
            'Masculino10Ano' => 0,
            'Feminino11Ano' => 0,
            'Masculino11Ano' => 0,
            'Feminino12Ano' => 0,
            'Masculino12Ano' => 0,
            'Feminino13Ano' => 0,
            'Masculino13Ano' => 0,
            'Feminino14Ano' => 0,
            'Masculino14Ano' => 0,
            'Feminino15Ano' => 0,
            'Masculino15Ano' => 0,
            'Feminino16Ano' => 0,
            'Masculino16Ano' => 0,
            'FemininoMais16' => 0,
            'MasculinoMais16' => 0,
            'TotalFeminino' => 0,
            'TotalMasculino' => 0,
            'TotalGeral' => 0
        ];
    }

    // Somar os valores para a etapa
    $dados_etapas[$etapa_id]['Feminino0a6Meses'] += $row['Feminino0a6Meses'];
    $dados_etapas[$etapa_id]['Masculino0a6Meses'] += $row['Masculino0a6Meses'];
    $dados_etapas[$etapa_id]['Feminino0Ano'] += $row['Feminino0Ano'];
    $dados_etapas[$etapa_id]['Masculino0Ano'] += $row['Masculino0Ano'];
    $dados_etapas[$etapa_id]['Feminino1Ano'] += $row['Feminino1Ano'];
    $dados_etapas[$etapa_id]['Masculino1Ano'] += $row['Masculino1Ano'];
    $dados_etapas[$etapa_id]['Feminino2Ano'] += $row['Feminino2Ano'];
    $dados_etapas[$etapa_id]['Masculino2Ano'] += $row['Masculino2Ano'];
    $dados_etapas[$etapa_id]['Feminino3Ano'] += $row['Feminino3Ano'];
    $dados_etapas[$etapa_id]['Masculino3Ano'] += $row['Masculino3Ano'];
    $dados_etapas[$etapa_id]['Feminino4Ano'] += $row['Feminino4Ano'];
    $dados_etapas[$etapa_id]['Masculino4Ano'] += $row['Masculino4Ano'];
    $dados_etapas[$etapa_id]['Feminino5Ano'] += $row['Feminino5Ano'];
    $dados_etapas[$etapa_id]['Masculino5Ano'] += $row['Masculino5Ano'];
    $dados_etapas[$etapa_id]['Feminino6Ano'] += $row['Feminino6Ano'];
    $dados_etapas[$etapa_id]['Masculino6Ano'] += $row['Masculino6Ano'];
    $dados_etapas[$etapa_id]['Feminino7Ano'] += $row['Feminino7Ano'];
    $dados_etapas[$etapa_id]['Masculino7Ano'] += $row['Masculino7Ano'];
    $dados_etapas[$etapa_id]['Feminino8Ano'] += $row['Feminino8Ano'];
    $dados_etapas[$etapa_id]['Masculino8Ano'] += $row['Masculino8Ano'];
    $dados_etapas[$etapa_id]['Feminino9Ano'] += $row['Feminino9Ano'];
    $dados_etapas[$etapa_id]['Masculino9Ano'] += $row['Masculino9Ano'];
    $dados_etapas[$etapa_id]['Feminino10Ano'] += $row['Feminino10Ano'];
    $dados_etapas[$etapa_id]['Masculino10Ano'] += $row['Masculino10Ano'];
    $dados_etapas[$etapa_id]['Feminino11Ano'] += $row['Feminino11Ano'];
    $dados_etapas[$etapa_id]['Masculino11Ano'] += $row['Masculino11Ano'];
    $dados_etapas[$etapa_id]['Feminino12Ano'] += $row['Feminino12Ano'];
    $dados_etapas[$etapa_id]['Masculino12Ano'] += $row['Masculino12Ano'];
    $dados_etapas[$etapa_id]['Feminino13Ano'] += $row['Feminino13Ano'];
    $dados_etapas[$etapa_id]['Masculino13Ano'] += $row['Masculino13Ano'];
    $dados_etapas[$etapa_id]['Feminino14Ano'] += $row['Feminino14Ano'];
    $dados_etapas[$etapa_id]['Masculino14Ano'] += $row['Masculino14Ano'];
    $dados_etapas[$etapa_id]['Feminino15Ano'] += $row['Feminino15Ano'];
    $dados_etapas[$etapa_id]['Masculino15Ano'] += $row['Masculino15Ano'];
    $dados_etapas[$etapa_id]['Feminino16Ano'] += $row['Feminino16Ano'];
    $dados_etapas[$etapa_id]['Masculino16Ano'] += $row['Masculino16Ano'];
    $dados_etapas[$etapa_id]['FemininoMais16'] += $row['FemininoMais16'];
    $dados_etapas[$etapa_id]['MasculinoMais16'] += $row['MasculinoMais16'];
    $dados_etapas[$etapa_id]['TotalFeminino'] += $row['TotalFeminino'];
    $dados_etapas[$etapa_id]['TotalMasculino'] += $row['TotalMasculino'];
    $dados_etapas[$etapa_id]['TotalGeral'] += $row['TotalGeral'];

    // Acumular totais gerais
    $total_0a6meses_f += $row['Feminino0a6Meses'];
    $total_0a6meses_m += $row['Masculino0a6Meses'];
    $total_0ano_f += $row['Feminino0Ano'];
    $total_0ano_m += $row['Masculino0Ano'];
    $total_1ano_f += $row['Feminino1Ano'];
    $total_1ano_m += $row['Masculino1Ano'];
    $total_2ano_f += $row['Feminino2Ano'];
    $total_2ano_m += $row['Masculino2Ano'];
    $total_3ano_f += $row['Feminino3Ano'];
    $total_3ano_m += $row['Masculino3Ano'];
    $total_4ano_f += $row['Feminino4Ano'];
    $total_4ano_m += $row['Masculino4Ano'];
    $total_5ano_f += $row['Feminino5Ano'];
    $total_5ano_m += $row['Masculino5Ano'];
    $total_6ano_f += $row['Feminino6Ano'];
    $total_6ano_m += $row['Masculino6Ano'];
    $total_7ano_f += $row['Feminino7Ano'];
    $total_7ano_m += $row['Masculino7Ano'];
    $total_8ano_f += $row['Feminino8Ano'];
    $total_8ano_m += $row['Masculino8Ano'];
    $total_9ano_f += $row['Feminino9Ano'];
    $total_9ano_m += $row['Masculino9Ano'];
    $total_10ano_f += $row['Feminino10Ano'];
    $total_10ano_m += $row['Masculino10Ano'];
    $total_11ano_f += $row['Feminino11Ano'];
    $total_11ano_m += $row['Masculino11Ano'];
    $total_12ano_f += $row['Feminino12Ano'];
    $total_12ano_m += $row['Masculino12Ano'];
    $total_13ano_f += $row['Feminino13Ano'];
    $total_13ano_m += $row['Masculino13Ano'];
    $total_14ano_f += $row['Feminino14Ano'];
    $total_14ano_m += $row['Masculino14Ano'];
    $total_15ano_f += $row['Feminino15Ano'];
    $total_15ano_m += $row['Masculino15Ano'];
    $total_16ano_f += $row['Feminino16Ano'];
    $total_16ano_m += $row['Masculino16Ano'];
    $total_mais16_f += $row['FemininoMais16'];
    $total_mais16_m += $row['MasculinoMais16'];
    $total_feminino += $row['TotalFeminino'];
    $total_masculino += $row['TotalMasculino'];
    $total_geral += $row['TotalGeral'];
}

// Mapear nomes das etapas, excluindo Multisseriada
$etapas_nomes = [
    1 => 'Berçário',
    2 => 'Maternal 1',
    3 => 'Maternal 2',
    4 => 'Pré I',
    5 => 'Pré II',
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
    37 => 'EJA II Eixo 4,5',
    38 => 'EJA Iniciais e Finais'
];
?>
<!DOCTYPE html>
<html class="ls-theme-green">
<head>
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'UA-117872281-1');
  </script>

  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
  <link rel="stylesheet" type="text/css" href="../css/impressao.css">
  <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <style>
    table.bordasimples {
      border-collapse: collapse;
      width: 100%;
    }
    table.bordasimples tr td, table.bordasimples tr th {
      border: 1px solid #808080 !important;
      padding: 5px;
      text-align: center;
      font-size: 12px;
    }
    table.bordasimples tr th {
      font-size: 14px;
      background-color: #f0f0f0 !important;
    }
    .feminino {
      background-color: #ffcccc !important;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .masculino {
      background-color: #cce5ff !important;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    @media print {
      @page {
        size: A4 landscape;
      }
      .feminino {
        background-color: #ffcccc !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .masculino {
        background-color: #cce5ff !important;
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
  <h3 class="ls-txt-center">QUADRO GERAL DE MATRÍCULAS POR ETAPA, IDADE E SEXO - ANO LETIVO <?php echo $anoLetivo; ?></h3>
  <br>
  <table class="bordasimples ls-sm-space" width="100%">
    <thead>
      <tr>
        <th rowspan="2" width="200px">ETAPAS</th>
        <th colspan="2">0 a 6 Meses</th>
        <th colspan="2">0 Ano</th>
        <th colspan="2">1 Ano</th>
        <th colspan="2">2 Anos</th>
        <th colspan="2">3 Anos</th>
        <th colspan="2">4 Anos</th>
        <th colspan="2">5 Anos</th>
        <th colspan="2">6 Anos</th>
        <th colspan="2">7 Anos</th>
        <th colspan="2">8 Anos</th>
        <th colspan="2">9 Anos</th>
        <th colspan="2">10 Anos</th>
        <th colspan="2">11 Anos</th>
        <th colspan="2">12 Anos</th>
        <th colspan="2">13 Anos</th>
        <th colspan="2">14 Anos</th>
        <th colspan="2">15 Anos</th>
        <th colspan="2">16 Anos</th>
        <th colspan="2">Mais 16</th>
        <th colspan="2">Total</th>
        <th rowspan="2">Total Geral</th>
      </tr>
      <tr>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
        <th class="feminino">F</th><th class="masculino">M</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($etapas_nomes as $etapa_id => $nome) { ?>
        <?php if (isset($dados_etapas[$etapa_id])) { $d = $dados_etapas[$etapa_id]; ?>
          <tr>
            <td><?php echo $nome; ?></td>
            <td class="feminino"><?php echo $d['Feminino0a6Meses']; ?></td>
            <td class="masculino"><?php echo $d['Masculino0a6Meses']; ?></td>
            <td class="feminino"><?php echo $d['Feminino0Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino0Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino1Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino1Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino2Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino2Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino3Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino3Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino4Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino4Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino5Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino5Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino6Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino6Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino7Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino7Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino8Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino8Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino9Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino9Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino10Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino10Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino11Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino11Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino12Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino12Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino13Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino13Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino14Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino14Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino15Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino15Ano']; ?></td>
            <td class="feminino"><?php echo $d['Feminino16Ano']; ?></td>
            <td class="masculino"><?php echo $d['Masculino16Ano']; ?></td>
            <td class="feminino"><?php echo $d['FemininoMais16']; ?></td>
            <td class="masculino"><?php echo $d['MasculinoMais16']; ?></td>
            <td class="feminino"><?php echo $d['TotalFeminino']; ?></td>
            <td class="masculino"><?php echo $d['TotalMasculino']; ?></td>
            <td><?php echo $d['TotalGeral']; ?></td>
          </tr>
        <?php } ?>
      <?php } ?>
      <tr>
        <td><strong>TOTAL GERAL</strong></td>
        <td class="feminino"><strong><?php echo $total_0a6meses_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_0a6meses_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_0ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_0ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_1ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_1ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_2ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_2ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_3ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_3ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_4ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_4ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_5ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_5ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_6ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_6ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_7ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_7ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_8ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_8ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_9ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_9ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_10ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_10ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_11ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_11ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_12ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_12ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_13ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_13ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_14ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_14ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_15ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_15ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_16ano_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_16ano_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_mais16_f; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_mais16_m; ?></strong></td>
        <td class="feminino"><strong><?php echo $total_feminino; ?></strong></td>
        <td class="masculino"><strong><?php echo $total_masculino; ?></strong></td>
        <td><strong><?php echo $total_geral; ?></strong></td>
      </tr>
    </tbody>
  </table>
  <br>
  <p class="ls-txt-center" style="font-size: 12px;"><?php echo $row_Secretaria['sec_nome'] ?> - <?php echo strtoupper(date('m/Y')); ?></p>
  <p class="ls-txt-right">Relatório impresso em <?php echo date("d/m/Y \à\s H:i"); ?><br>SMECEL | Sistema de Gestão Escolar</p>

  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);
mysql_free_result($Secretaria);
mysql_free_result($Matriculas);
?>