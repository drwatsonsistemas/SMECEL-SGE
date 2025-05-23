<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_verificar = "SELECT aluno_id, aluno_usu_tipo, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_pais, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, aluno_recebe_bolsa_familia, aluno_foto, aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao, aluno_sangue_tipo, aluno_sangue_rh, aluno_nome_responsavel_legal, aluno_cpf_responsavel_legal, aluno_grau_responsavel_legal FROM smc_aluno";
$verificar = mysql_query($query_verificar, $SmecelNovo) or die(mysql_error());
$row_verificar = mysql_fetch_assoc($verificar);
$totalRows_verificar = mysql_num_rows($verificar);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>



</body>
</html>
<?php
mysql_free_result($verificar);
?>
