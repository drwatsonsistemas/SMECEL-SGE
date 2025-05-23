<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
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
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

require_once('../funcoes/usuLogado.php');
require_once('../funcoes/anoLetivo.php');
require_once('../funcoes/inverteData.php');
require_once '../funcoes/SimpleXLSXGen.php'; // Certifique-se de que o caminho está correto

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_id_sec = '$row_UsuarioLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaAlunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
aluno_id, aluno_nome, aluno_nascimento,aluno_sexo, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia, aluno_laudo,aluno_cpf, aluno_raca, aluno_alergia, aluno_alergia_qual,aluno_localizacao,aluno_nome_responsavel_legal, aluno_filiacao1,aluno_filiacao2,aluno_grau_responsavel_legal,aluno_endereco, te_ponto_id,
te_ponto_id_sec,
te_ponto_descricao,
te_ponto_endereco,
te_ponto_num,
te_ponto_bairro,
te_ponto_latitude,
te_ponto_longitude,
turma_id, turma_nome, turma_etapa, turma_turno, turma_tipo_atendimento, etapa_id, etapa_id_filtro, etapa_filtro_id, etapa_filtro_nome,escola_id, escola_situacao,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo_nome,
CASE aluno_localizacao
WHEN 1 THEN 'ÁREA URBANA'
WHEN 2 THEN 'ÁREA RURAL'
END AS aluno_localizacao_nome,
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDIGENA'
WHEN 6 THEN 'NAO DECLARADA'
ELSE 'NAO DECLARADA'
END AS aluno_raca_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE etapa_id_filtro
WHEN 1 THEN 'INFANTIL'
WHEN 3 THEN 'FUNDAMENTAL'
WHEN 7 THEN 'EJA'
END AS etapa_id_filtro_nome
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
LEFT JOIN smc_te_ponto ON vinculo_aluno_ponto_id = te_ponto_id
INNER JOIN smc_etapa ON turma_etapa = etapa_id
INNER JOIN smc_etapa_filtro ON etapa_filtro_id = etapa_id_filtro
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
WHERE  vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND vinculo_aluno_id_sec = '$row_UsuarioLogado[usu_sec]' 
AND vinculo_aluno_transporte = 'S' AND turma_tipo_atendimento = 1 AND escola_situacao = 1
ORDER BY turma_turno, turma_nome, aluno_nome
";
$ListaAlunos = mysql_query($query_ListaAlunos, $SmecelNovo) or die(mysql_error());
$row_ListaAlunos = mysql_fetch_assoc($ListaAlunos);
$totalRows_ListaAlunos = mysql_num_rows($ListaAlunos);


use Shuchkin\SimpleXLSXGen;

$data = [
  ['OBRIGATORIO_NOME', 'OBRIGATORIO_DATA_NASCIMENTO', 'OBRIGATORIO_SEXO', 'OBRIGATORIO_COR', 'OBRIGATORIO_LOCALIZACAO', 'OBRIGATORIO_NIVEL_ENSINO', 'OBRIGATORIO_TURNO_ENSINO', 'OPTATIVO_CPF', 'OPTATIVO_NOME_RESPONSAVEL', 'OPTATIVO_GRAU_PARENTESCO', 'OPTATIVO_ENDERECO', 'OPTATIVO_LATITUDE', 'OPTATIVO_LONGITUDE'],
];

do {
  $exibirNome="";
  $parentesco=$row_ListaAlunos['aluno_grau_responsavel_legal'];

  if($row_ListaAlunos['aluno_nome_responsavel_legal'] != ''){
    $exibirNome=$row_ListaAlunos['aluno_nome_responsavel_legal'];
    switch ($parentesco) {
      case 1:
      $parentesco="IRMÃO/IRMÃ";
      break;
      case 2:
      $parentesco="TIO/TIA";
      case 3:
      $parentesco="AVÔ/AVÓ";
      case 4:
      $parentesco="OUTRO";
      break;
      default:
      $parentesco="OUTRO";
      break;
    }
  }else if($row_ListaAlunos['aluno_filiacao1'] != ''){
    $exibirNome=$row_ListaAlunos['aluno_filiacao1'];
    $parentesco= "MÃE";
  }else{
    $exibirNome=$row_ListaAlunos['aluno_filiacao2'];
    $parentesco="PAI";
  }

  
  $data_nascimento = inverteData($row_ListaAlunos['aluno_nascimento']);
  $data[] = [
    $row_ListaAlunos['aluno_nome'], 
    $data_nascimento, 
    $row_ListaAlunos['aluno_sexo_nome'],
    $row_ListaAlunos['aluno_raca_nome'],
    $row_ListaAlunos['aluno_localizacao_nome'],
    $row_ListaAlunos['etapa_id_filtro_nome'],
    $row_ListaAlunos['turma_turno_nome'],
    $row_ListaAlunos['aluno_cpf'],
    $exibirNome,
    $parentesco,
    $row_ListaAlunos['aluno_endereco'],
    $row_ListaAlunos['te_ponto_latitude'],
    $row_ListaAlunos['te_ponto_longitude']
  ];
} while ($row_ListaAlunos = mysql_fetch_assoc($ListaAlunos));


$xlsx = SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs('exportacao_sete.xlsx');

?>