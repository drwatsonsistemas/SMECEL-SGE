<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

include "usuLogado.php";
include "fnc/anoLetivo.php";
include "fnc/anti_injection.php";

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

$colname_AlterarStatus = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_AlterarStatus = anti_injection($_GET['cmatricula']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlterarStatus = sprintf("
  SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
  aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1,
  turma_id, turma_nome, turma_turno, turma_etapa, turma_matriz_id, 
  etapa_id, etapa_nome, 
  matriz_id, matriz_nome, matriz_criterio_avaliativo
  FROM smc_vinculo_aluno 
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  INNER JOIN smc_etapa ON etapa_id = turma_etapa
  INNER JOIN smc_matriz ON matriz_id = turma_matriz_id 
  WHERE vinculo_aluno_boletim = '1' AND (vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_hash = %s)", GetSQLValueString($colname_AlterarStatus, "text"));
$AlterarStatus = mysql_query($query_AlterarStatus, $SmecelNovo) or die(mysql_error());
$row_AlterarStatus = mysql_fetch_assoc($AlterarStatus);
$totalRows_AlterarStatus = mysql_num_rows($AlterarStatus);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_etario, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_AlterarStatus[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_acompanhamento = "
SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
FROM smc_acomp_proc_aprend
WHERE acomp_id_matriz = '$row_AlterarStatus[matriz_id]'
AND acomp_id_crit = '$row_Criterios[ca_id]'
";
$acompanhamento = mysql_query($query_acompanhamento, $SmecelNovo) or die(mysql_error());
$row_acompanhamento = mysql_fetch_assoc($acompanhamento);
$totalRows_acompanhamento = mysql_num_rows($acompanhamento);

$numPeriodos = $row_Criterios['ca_qtd_periodos'];




if ($totalRows_AlterarStatus==0) {
  header("Location:vinculoAlunoExibirTurma.php?erro");  
}


  if ($row_UsuLogado['usu_insert']=="N") {
    header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
    die;
  }
  $idVinculo = $row_AlterarStatus['vinculo_aluno_id']; 
  
  $updateSQL = sprintf("UPDATE smc_vinculo_aluno SET vinculo_aluno_boletim=%s WHERE vinculo_aluno_id=%s",
   GetSQLValueString(0, "int"),
   GetSQLValueString($idVinculo, "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $deleteSQL = sprintf("DELETE FROM smc_conceito_aluno WHERE conc_matricula_id=%s",
   GetSQLValueString($idVinculo, "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result2 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());


$updateGoTo = "matriculaExibe.php?resetado";
if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $updateGoTo));



?>

<?php
mysql_free_result($UsuLogado);

mysql_free_result($Criterios);

mysql_free_result($acompanhamento);

mysql_free_result($EscolaLogada);

mysql_free_result($AlterarStatus);

//mysql_free_result($VerAluno);
?>
