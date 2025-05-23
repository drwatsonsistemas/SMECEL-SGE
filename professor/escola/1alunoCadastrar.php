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

include "usuLogado.php";

include "fnc/anoLetivo.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
		if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		break;
	}
	
		$geraHash = md5(time().$_POST['aluno_nome'].$_POST['escola_id']);
	
  $insertSQL = sprintf("
  INSERT INTO smc_aluno (aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_pais, aluno_uf_nascimento, 
  aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, 
  aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
  aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, 
  aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
  aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_recebe_bolsa_familia, 
  aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao, 
  aluno_sangue_tipo, aluno_sangue_rh, aluno_nome_responsavel_legal, aluno_cpf_responsavel_legal, aluno_grau_responsavel_legal, aluno_hash) 
  VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$geraHash')",
                       GetSQLValueString($_POST['aluno_cod_inep'], "text"),
                       GetSQLValueString($_POST['aluno_cpf'], "text"),
                       GetSQLValueString($_POST['aluno_nome'], "text"),
                       GetSQLValueString(inverteData($_POST['aluno_nascimento']), "date"),
                       GetSQLValueString($_POST['aluno_filiacao1'], "text"),
                       GetSQLValueString($_POST['aluno_filiacao2'], "text"),
                       GetSQLValueString($_POST['aluno_sexo'], "int"),
                       GetSQLValueString($_POST['aluno_raca'], "int"),
                       GetSQLValueString($_POST['aluno_nacionalidade'], "int"),
                       GetSQLValueString($_POST['aluno_pais'], "int"),
                       GetSQLValueString($_POST['aluno_uf_nascimento'], "text"),
                       GetSQLValueString($_POST['aluno_municipio_nascimento_ibge'], "text"),
                       GetSQLValueString($_POST['aluno_aluno_com_deficiencia'], "int"),
                       GetSQLValueString($_POST['aluno_nis'], "text"),
                       GetSQLValueString($_POST['aluno_identidade'], "text"),
                       GetSQLValueString($_POST['aluno_emissor'], "text"),
                       GetSQLValueString($_POST['aluno_uf_emissor'], "text"),
                       GetSQLValueString(inverteData($_POST['aluno_data_espedicao']), "date"),
                       GetSQLValueString($_POST['aluno_tipo_certidao'], "int"),
                       GetSQLValueString($_POST['aluno_termo'], "text"),
                       GetSQLValueString($_POST['aluno_folhas'], "text"),
                       GetSQLValueString($_POST['aluno_livro'], "text"),
                       GetSQLValueString(inverteData($_POST['aluno_emissao_certidao']), "date"),
                       GetSQLValueString($_POST['aluno_uf_cartorio'], "text"),
                       GetSQLValueString($_POST['aluno_mucicipio_cartorio'], "text"),
                       GetSQLValueString($_POST['aluno_nome_cartorio'], "text"),
                       GetSQLValueString($_POST['aluno_num_matricula_modelo_novo'], "text"),
                       GetSQLValueString($_POST['aluno_localizacao'], "int"),
                       GetSQLValueString($_POST['aluno_cep'], "text"),
                       GetSQLValueString($_POST['aluno_endereco'], "text"),
                       GetSQLValueString($_POST['aluno_numero'], "text"),
                       GetSQLValueString($_POST['aluno_complemento'], "text"),
                       GetSQLValueString($_POST['aluno_bairro'], "text"),
                       GetSQLValueString($_POST['aluno_uf'], "text"),
                       GetSQLValueString($_POST['aluno_municipio'], "text"),
                       GetSQLValueString($_POST['aluno_telefone'], "text"),
                       GetSQLValueString($_POST['aluno_celular'], "text"),
                       GetSQLValueString($_POST['aluno_email'], "text"),
                       GetSQLValueString($_POST['aluno_sus'], "text"),
					   GetSQLValueString($_POST['aluno_tipo_deficiencia'], "text"),
					   GetSQLValueString($_POST['aluno_laudo'], "int"),
					   GetSQLValueString($_POST['aluno_alergia'], "int"),
					   GetSQLValueString($_POST['aluno_alergia_qual'], "text"),
					   GetSQLValueString($_POST['aluno_destro'], "int"),
					   GetSQLValueString($_POST['aluno_emergencia_avisar'], "text"),
					   GetSQLValueString($_POST['aluno_emergencia_tel1'], "text"),
					   GetSQLValueString($_POST['aluno_emergencia_tel2'], "text"),
					   GetSQLValueString($_POST['aluno_prof_mae'], "text"),
					   GetSQLValueString($_POST['aluno_tel_mae'], "text"),
					   GetSQLValueString($_POST['aluno_escolaridade_mae'], "int"),
					   GetSQLValueString($_POST['aluno_rg_mae'], "text"),
					   GetSQLValueString($_POST['aluno_cpf_mae'], "text"),
					   GetSQLValueString($_POST['aluno_prof_pai'], "text"),
					   GetSQLValueString($_POST['aluno_tel_pai'], "text"),
					   GetSQLValueString($_POST['aluno_escolaridade_pai'], "int"),
					   GetSQLValueString($_POST['aluno_rg_pai'], "text"),
					   GetSQLValueString($_POST['aluno_cpf_pai'], "text"),
					   GetSQLValueString($_POST['aluno_recebe_bolsa_familia'], "int"),
					   GetSQLValueString(isset($_POST['aluno_def_bvisao']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_cegueira']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_auditiva']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_fisica']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_intelectual']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_surdez']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_surdocegueira']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_autista']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['aluno_def_superdotacao']) ? "true" : "", "defined","1","0"),
					   GetSQLValueString($_POST['aluno_sangue_tipo'], "text"),
					   GetSQLValueString($_POST['aluno_sangue_rh'], "text"),					   
					   GetSQLValueString($_POST['aluno_nome_responsavel_legal'], "text"),					   
					   GetSQLValueString($_POST['aluno_cpf_responsavel_legal'], "text"),					   
					   GetSQLValueString($_POST['aluno_grau_responsavel_legal'], "text"),					   
					   GetSQLValueString($_POST['aluno_hash'], "text")					   
					   );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
$usu = $_POST['usu_id'];
$esc = $_POST['escola_id'];
$detalhes = $_POST['aluno_nome'];
date_default_timezone_set('America/Bahia');
$dat = date('Y-m-d H:i:s');

$sql = "
INSERT INTO smc_registros (
registros_id_escola, 
registros_id_usuario, 
registros_tipo, 
registros_complemento, 
registros_data_hora
) VALUES (
'$esc', 
'$usu', 
'8', 
'($detalhes)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());

  
  
  

  $insertGoTo = "alunoCadastrado.php?c=$geraHash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_paises = "SELECT pais_id, pais_cod, pais_nome, pais_nacionalidade, pais_ddi FROM smc_paises ORDER BY pais_nome ASC";
$paises = mysql_query($query_paises, $SmecelNovo) or die(mysql_error());
$row_paises = mysql_fetch_assoc($paises);
$totalRows_paises = mysql_num_rows($paises);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
   <!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>
<main class="ls-main ">
  <div class="container-fluid">
 
    <h1 class="ls-title-intro ls-ico-home">Cadastro de Aluno</h1>
    <!-- CONTEÚDO -->
    
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
      <fieldset>
        <div id="ident" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-user">IDENTIFICAÇÃO</p>
          <label class="ls-label col-md-12"><b class="ls-label-text">Identificação única (gerado pelo Inep)</b>
            <input type="text" name="aluno_cod_inep" value="" size="32">
          </label>
          <label class="ls-label col-md-8"><b class="ls-label-text">Nome completo</b>
            <input type="text" name="aluno_nome" value="" size="32" required>
          </label>
          <label class="ls-label col-md-4"><b class="ls-label-text">Data de nascimento</b>
            <input type="text" name="aluno_nascimento" value="" size="32" class="date" required>
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">Filiação 1 <small>(preferencialmente nome da MÃE aqui)</small></b>
            <input type="text" name="aluno_filiacao1" value="" size="32" required>
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">Filiação 2</b>
            <input type="text" name="aluno_filiacao2" value="" size="32">
          </label>
          <p>.</p>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Sexo</b>
          <div class="ls-custom-select">
            <select name="aluno_sexo" class="ls-select" required>
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Masculino</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Feminino</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Cor/Raça</b>
          <div class="ls-custom-select">
            <select name="aluno_raca">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Branca</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Preta</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>Parda</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>Amarela</option>
              <option value="5" <?php if (!(strcmp(5, ""))) {echo "SELECTED";} ?>>Indígena</option>
              <option value="6" <?php if (!(strcmp(6, ""))) {echo "SELECTED";} ?>>Não declarada</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-4">
          <b class="ls-label-text">Nacionalidade</b>
          <div class="ls-custom-select">
            <select name="aluno_nacionalidade" id="aluno_nacionalidade">
              <option value="1" <?php if (!(strcmp(1, 1))) {echo "SELECTED";} ?>>Brasileira</option>
              <option value="2" <?php if (!(strcmp(2, 1))) {echo "SELECTED";} ?>>Brasileira - Nascido no exterior ou naturalizado</option>
              <option value="3" <?php if (!(strcmp(3, 1))) {echo "SELECTED";} ?>>Estrangeiro</option>
            </select>
          </div>
          </label>
          
          
          <label class="ls-label col-md-3">
          <b class="ls-label-text">País</b>
          <div class="ls-custom-select">          
          <select name="aluno_pais" id="aluno_pais" required>
              
              <option value="76">BRASIL</option>
              <?php do { ?>
              
              <option value="<?php echo $row_paises['pais_cod']; ?>"><?php echo $row_paises['pais_nome']; ?></option>
              
              <?php } while ($row_paises = mysql_fetch_assoc($paises)); ?>
              
          </select>    
          </div>
          </label>

          
          
          <label class="ls-label col-md-3">
          <b class="ls-label-text">UF de nascimento</b>
          <div class="ls-custom-select">
            <select name="aluno_uf_nascimento" id="aluno_uf_nascimento">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Município de nascimento</b>
          <div class="ls-custom-select">
            <select name="aluno_municipio_nascimento_ibge" id="aluno_municipio_nascimento_ibge">
              <option value="">Selecione um estado primeiro</option>
            </select>
          </div>
          </label>
        </div>
        <div id="docum" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-text">DOCUMENTAÇÃO</p>
          <label class="ls-label col-md-6"><b class="ls-label-text">Número do CPF</b>
            <input type="text" name="aluno_cpf" class="cpf" value="" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">NIS</b>
            <input type="text" name="aluno_nis" class="nis" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Cartão do SUS</b>
            <input type="text" name="aluno_sus" class="sus" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Número da identidade</b>
            <input type="text" name="aluno_identidade" value="" size="32">
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Órgão emissor</b>
            <input type="text" name="aluno_emissor" value="" size="32">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">UF emissor</b>
          <div class="ls-custom-select">
            <select name="aluno_uf_emissor">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Data de expedição</b>
            <input type="text" name="aluno_data_espedicao" value="" size="32" class="date">
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Modelo de certidão</b> <br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_tipo_certidao" value="1" onclick="javascript:modeloantigo_certidao();"/>
            Antigo </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_tipo_certidao" value="2" onclick="javascript:modelonovo_certidao();"/>
            Novo </label>
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Termos</b>
            <input type="text" name="aluno_termo" id="aluno_termo" value="" size="32">
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Folhas</b>
            <input type="text" name="aluno_folhas" id="aluno_folhas" value="" size="32">
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Livro</b>
            <input type="text" name="aluno_livro" id="aluno_livro" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Emissão</b>
            <input type="text" name="aluno_emissao_certidao" id="aluno_emissao_certidao" value="" size="32" class="date">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">Cartório</b>
          <div class="ls-custom-select">
            <select name="aluno_uf_cartorio" id="aluno_uf_cartorio">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-4"><b class="ls-label-text">Município</b>
            <input type="text" name="aluno_mucicipio_cartorio" id="aluno_mucicipio_cartorio" value="" size="32">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">Nome de Cartório</b>
            <input type="text" name="aluno_nome_cartorio" id="aluno_nome_cartorio" value="" size="32">
          </label>
          <label class="ls-label col-md-12"><b class="ls-label-text">Nº da matrícula modelo novo</b>
            <input type="text" name="aluno_num_matricula_modelo_novo" id="aluno_num_matricula_modelo_novo" value="" size="32" class="certidao">
          </label>
        </div>
        <div id="local" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-target">LOCALIZAÇÃO</p>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">Localização</b>
          <div class="ls-custom-select">
            <select name="aluno_localizacao" id="aluno_localizacao" required>
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>-</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Urbana</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>Rural</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">CEP</b>
            <input type="text" name="aluno_cep" id="aluno_cep" value="" size="32" class="cep">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">Endereço</b>
            <input type="text" name="aluno_endereco" id="aluno_endereco" value="" size="32">
          </label>
          <label class="ls-label col-md-2"><b class="ls-label-text">Número</b>
            <input type="text" name="aluno_numero" id="aluno_numero" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Complemento</b>
            <input type="text" name="aluno_complemento" id="aluno_complemento" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Bairro</b>
            <input type="text" name="aluno_bairro" id="aluno_bairro" value="" size="32">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">UF</b>
          <div class="ls-custom-select">
            <select name="aluno_uf" id="aluno_uf">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>ESCOLHA...</option>
              <option value="AC" <?php if (!(strcmp("AC", ""))) {echo "SELECTED";} ?>>AC</option>
              <option value="AL" <?php if (!(strcmp("AL", ""))) {echo "SELECTED";} ?>>AL</option>
              <option value="AP" <?php if (!(strcmp("AP", ""))) {echo "SELECTED";} ?>>AP</option>
              <option value="AM" <?php if (!(strcmp("AM", ""))) {echo "SELECTED";} ?>>AM</option>
              <option value="BA" <?php if (!(strcmp("BA", ""))) {echo "SELECTED";} ?>>BA</option>
              <option value="CE" <?php if (!(strcmp("CE", ""))) {echo "SELECTED";} ?>>CE</option>
              <option value="DF" <?php if (!(strcmp("DF", ""))) {echo "SELECTED";} ?>>DF</option>
              <option value="ES" <?php if (!(strcmp("ES", ""))) {echo "SELECTED";} ?>>ES</option>
              <option value="GO" <?php if (!(strcmp("GO", ""))) {echo "SELECTED";} ?>>GO</option>
              <option value="MA" <?php if (!(strcmp("MA", ""))) {echo "SELECTED";} ?>>MA</option>
              <option value="MT" <?php if (!(strcmp("MT", ""))) {echo "SELECTED";} ?>>MT</option>
              <option value="MS" <?php if (!(strcmp("MS", ""))) {echo "SELECTED";} ?>>MS</option>
              <option value="MG" <?php if (!(strcmp("MG", ""))) {echo "SELECTED";} ?>>MG</option>
              <option value="PA" <?php if (!(strcmp("PA", ""))) {echo "SELECTED";} ?>>PA</option>
              <option value="PB" <?php if (!(strcmp("PB", ""))) {echo "SELECTED";} ?>>PB</option>
              <option value="PR" <?php if (!(strcmp("PR", ""))) {echo "SELECTED";} ?>>PR</option>
              <option value="PE" <?php if (!(strcmp("PE", ""))) {echo "SELECTED";} ?>>PE</option>
              <option value="PI" <?php if (!(strcmp("PI", ""))) {echo "SELECTED";} ?>>PI</option>
              <option value="RJ" <?php if (!(strcmp("RJ", ""))) {echo "SELECTED";} ?>>RJ</option>
              <option value="RN" <?php if (!(strcmp("RN", ""))) {echo "SELECTED";} ?>>RN</option>
              <option value="RS" <?php if (!(strcmp("RS", ""))) {echo "SELECTED";} ?>>RS</option>
              <option value="RO" <?php if (!(strcmp("RO", ""))) {echo "SELECTED";} ?>>RO</option>
              <option value="RR" <?php if (!(strcmp("RR", ""))) {echo "SELECTED";} ?>>RR</option>
              <option value="SC" <?php if (!(strcmp("SC", ""))) {echo "SELECTED";} ?>>SC</option>
              <option value="SP" <?php if (!(strcmp("SP", ""))) {echo "SELECTED";} ?>>SP</option>
              <option value="SE" <?php if (!(strcmp("SE", ""))) {echo "SELECTED";} ?>>SE</option>
              <option value="TO" <?php if (!(strcmp("TO", ""))) {echo "SELECTED";} ?>>TO</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-4"><b class="ls-label-text">Município</b>
            <input type="text" name="aluno_municipio" id="aluno_municipio" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Telefone/Celular</b>
            <input type="text" name="aluno_telefone" value="" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Celular/WhatsApp</b>
            <input type="text" name="aluno_celular" value="" size="32" class="celular">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">E-mail</b>
            <input type="text" name="aluno_email" value="" size="32">
          </label>
        </div>
        <div id="medico" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-book">DADOS MÉDICOS</p>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Aluno com alguma deficiência?</b><br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_aluno_com_deficiencia" value="1" onclick="javascript:habilita_deficiencia();"/>
            Sim </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_aluno_com_deficiencia" value="2" onclick="javascript:desabilita_deficiencia();"/>
            Não </label>
          </label>
          <label class="ls-label col-md-7"><b class="ls-label-text">Descreva a deficiência:</b>
            <input type="text" name="aluno_tipo_deficiencia" value="" size="32" id="aluno_tipo_deficiencia">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">Possui Laudo Médico?</b> <br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_laudo" value="1" />
            Sim </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_laudo" value="2" />
            Não </label>
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Possui alguma alergia/intolerância?</b> <br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_alergia" value="1" onclick="javascript:habilita_alergia();" />
            Sim </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_alergia" value="2" onclick="javascript:desabilita_alergia();" />
            Não </label>
          </label>
          <label class="ls-label col-md-7"><b class="ls-label-text">Descreva a alergia/intolerância:</b>
            <input type="text" name="aluno_alergia_qual" value="" size="32" id="aluno_alergia_qual">
          </label>
          <label class="ls-label col-md-2">
          <b class="ls-label-text">Destro/Canhoto</b> <br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_destro" value="1" />
            Destro </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_destro" value="2" />
            Canhoto </label>
          </label>
          
          
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Tipo sanguíneo</b> <br>
          <div class="ls-custom-select">
            <select name="aluno_sangue_tipo">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>-</option>
              <option value="A" <?php if (!(strcmp("A", ""))) {echo "SELECTED";} ?>>A</option>
              <option value="B" <?php if (!(strcmp("B", ""))) {echo "SELECTED";} ?>>B</option>
              <option value="AB" <?php if (!(strcmp("AB", ""))) {echo "SELECTED";} ?>>AB</option>
              <option value="O" <?php if (!(strcmp("O", ""))) {echo "SELECTED";} ?>>O</option>
            </select>
          </div>
          </label>
          
          <label class="ls-label col-md-6">
          <b class="ls-label-text">Fator RH</b> <br>
          <div class="ls-custom-select">
            <select name="aluno_sangue_rh">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>-</option>
              <option value="+" <?php if (!(strcmp("+", ""))) {echo "SELECTED";} ?>>POSITIVO (+)</option>
              <option value="-" <?php if (!(strcmp("-", ""))) {echo "SELECTED";} ?>>NEGATIVO (-)</option>
            </select>
          </div>
          </label>
          
          
          
          <label class="ls-label col-md-6"><b class="ls-label-text">Em caso de emergência, avisar a:</b>
            <input type="text" name="aluno_emergencia_avisar" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Telefone 1:</b>
            <input type="text" name="aluno_emergencia_tel1" value="" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Telefone 2:</b>
            <input type="text" name="aluno_emergencia_tel2" value="" size="32" class="celular">
          </label>
        </div>
        
        <div id="deficiencia" class="ls-box ls-box-gray" style="display:none">
          <p class="ls-title-4 col-md-12 ls-ico-book">DEFICIÊNCIA, TEA OU HABILIDADES ESPECIAIS</p>
      
      <div class="ls-label col-md-12">
      <p>Deficiência:</p>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_bvisao" value="" /> Baixa visão</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_cegueira" value="" /> Cegueira</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_auditiva" value="" /> Deficiência auditiva</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_fisica" value="" /> Deficiência física</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_intelectual" value="" /> Deficiência intelectual</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_surdez" value="" /> Sudez</label>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_surdocegueira" value="" /> Surdocegueira</label>
      
      </div>
          
      <div class="ls-label col-md-12">
      <p>Transtorno do espectro autista:</p>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_autista" value="" /> Transtorno do espectro autista</label>
      
      </div>
          
      <div class="ls-label col-md-12">
      <p>Altas habilidades/superdotação:</p>
      <label class="ls-label-text"><input type="checkbox" name="aluno_def_superdotacao" value="" /> Altas habilidades/superdotação</label>
      
      </div>
          
          
        </div>  
        
        
        <div id="responsavel" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-users">DADOS DOS RESPONSÁVEIS</p>
		  
		  
		  
		  		<label class="ls-label col-md-4"> <b class="ls-label-text">Nome do responsável legal (caso não seja criado por pai/mãe):</b>
              <input type="text" name="aluno_nome_responsavel_legal" value="" size="32">
            </label>
		<label class="ls-label col-md-4"> <b class="ls-label-text">CPF do responsável legal:</b>
              <input type="text" maxlength="14" name="aluno_cpf_responsavel_legal" value="" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
            </label>
		
		        <label class="ls-label col-md-4">
        <b class="ls-label-text">Grau de parentesco do responsável legal:</b>
        <div class="ls-custom-select">
              <select name="aluno_grau_responsavel_legal">
			  <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>IRMÃO/IRMÃ</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>TIO/TIA</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>AVÔ/AVÓ</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>OUTRO</option>
          </select>
            </div>
        </label>
		
		  
		  
		  
		  
          <label class="ls-label col-md-6"><b class="ls-label-text">Profissão da mãe:</b>
            <input type="text" name="aluno_prof_mae" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Telefone da mãe:</b>
            <input type="text" name="aluno_tel_mae" value="" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Escolaridade da mãe:</b>
          <div class="ls-custom-select">
            <select name="aluno_escolaridade_mae">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>NÃO ESTUDOU</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>CONCLUIU O FUNDAMENTAL</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>CONCLUIU O ENSINO MÉDIO</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>CONCLUIU O NÍVEL SUPERIOR</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">RG da mãe:</b>
            <input type="text" name="aluno_rg_mae" value="" size="32">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">CPF da mãe:</b>
            <input type="text" name="aluno_cpf_mae" value="" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">Profissão do pai:</b>
            <input type="text" name="aluno_prof_pai" value="" size="32">
          </label>
          <label class="ls-label col-md-3"><b class="ls-label-text">Telefone do pai:</b>
            <input type="text" name="aluno_tel_pai" value="" size="32" class="celular">
          </label>
          <label class="ls-label col-md-3">
          <b class="ls-label-text">Escolaridade do pai:</b>
          <div class="ls-custom-select">
            <select name="aluno_escolaridade_pai">
              <option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>Escolha...</option>
              <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>NÃO ESTUDOU</option>
              <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>CONCLUIU O FUNDAMENTAL</option>
              <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>CONCLUIU O ENSINO MÉDIO</option>
              <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>CONCLUIU O NÍVEL SUPERIOR</option>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">RG do pai:</b>
            <input type="text" name="aluno_rg_pai" value="" size="32">
          </label>
          <label class="ls-label col-md-6"><b class="ls-label-text">CPF do pai:</b>
            <input type="text" name="aluno_cpf_pai" value="" size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
          </label>
        </div>
        <div id="social" class="ls-box ls-box-gray">
          <p class="ls-title-4 col-md-12 ls-ico-info">DADOS SOCIAIS</p>
          <label class="ls-label col-sm-12">
          <b class="ls-label-text">Aluno é beneficiário do Bolsa Família?</b> <br>
          <br>
          <label class="ls-label-text">
            <input type="radio" name="aluno_recebe_bolsa_familia" value="1"/>
            Sim </label>
          <label class="ls-label-text">
            <input type="radio" name="aluno_recebe_bolsa_familia" value="0"/>
            Não </label>
          </label>
        </div>
      </fieldset>
      <div class="ls-actions-btn">
        <input type="submit" value="CADASTRAR ALUNO" class="ls-btn-primary">
      </div>
      <input type="hidden" name="aluno_hash" value="">
      <input type="hidden" name="MM_insert" value="form1">
      <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
      <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
    </form>
    <br>
    
    <!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>

<script src="../../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/validarCPF.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script type="text/javascript">

/*    $(document).ready(function(){
        
 $('#aluno_nacionalidade').change(function(event){
   var nacionalidade = event.currentTarget.value;
   
 
 
 switch (nacionalidade) { 
	//BRASILEIRO
	case "1": 
		$("#aluno_pais").prop("disabled",true); 
		$("#aluno_uf_nascimento").prop("disabled",false); 
		$("#aluno_municipio_nascimento_ibge").prop("disabled",false);
		break;

	case "2": 
		$("#aluno_pais").prop("disabled",true); 
		$("#aluno_uf_nascimento").prop("disabled",true); 
		$("#aluno_municipio_nascimento_ibge").prop("disabled",true);
		break;
	
	case "3": 
		$("#aluno_pais").prop("disabled",false); 
		$("#aluno_uf_nascimento").prop("disabled",true); 
		$("#aluno_municipio_nascimento_ibge").prop("disabled",true); 
		
		break;
}
   
 });
		
    });*/


    </script>



<script type="text/javascript">
function habilita_deficiencia()
{
	document.getElementById("aluno_tipo_deficiencia").disabled = false; //Habilitando
	document.getElementById("deficiencia").style.display = "block"; //Habilitando
}
function desabilita_deficiencia()
{
	document.getElementById("aluno_tipo_deficiencia").disabled = true; //Desabilitando
	document.getElementById("deficiencia").style.display = "none"; //Desabilitando
}
</script> 
<script type="text/javascript">
function habilita_alergia()
{
	document.getElementById("aluno_alergia_qual").disabled = false; //Habilitando
}
function desabilita_alergia()
{
	document.getElementById("aluno_alergia_qual").disabled = true; //Desabilitando
}
</script> 
<script type="text/javascript">
function modeloantigo_certidao()
{
	document.getElementById("aluno_termo").disabled = false; //Habilitando
	document.getElementById("aluno_folhas").disabled = false; //Habilitando
	document.getElementById("aluno_livro").disabled = false; //Habilitando
	document.getElementById("aluno_emissao_certidao").disabled = false; //Habilitando
	document.getElementById("aluno_uf_cartorio").disabled = false; //Habilitando
	document.getElementById("aluno_mucicipio_cartorio").disabled = false; //Habilitando
	document.getElementById("aluno_nome_cartorio").disabled = false; //Habilitando
	document.getElementById("aluno_num_matricula_modelo_novo").disabled = true; //Habilitando
}
function modelonovo_certidao()
{
	document.getElementById("aluno_termo").disabled = true; //Habilitando
	document.getElementById("aluno_folhas").disabled = true; //Habilitando
	document.getElementById("aluno_livro").disabled = true; //Habilitando
	document.getElementById("aluno_emissao_certidao").disabled = true; //Habilitando
	document.getElementById("aluno_uf_cartorio").disabled = true; //Habilitando
	document.getElementById("aluno_mucicipio_cartorio").disabled = true; //Habilitando
	document.getElementById("aluno_nome_cartorio").disabled = true; //Habilitando
	document.getElementById("aluno_num_matricula_modelo_novo").disabled = false; //Habilitando
}
</script> 
<script type="text/javascript">
  //Popula campo cidades com base na escolha do campo estados
    $(document).ready(function(){
        $('#aluno_uf_nascimento').change(function(){
            $('#aluno_municipio_nascimento_ibge').load('cidades.php?estado='+$('#aluno_uf_nascimento').val());
      $("#aluno_municipio_nascimento_ibge").focus();
        });
    
        
        $('#aluno_municipio_nascimento_ibge').select2();
      });
    </script>
    
    
    
    
    

<script type="text/javascript">


	$("#aluno_cep").focusout(function(){
	//Aqui vai o código	
	
	$.ajax({
			//O campo URL diz o caminho de onde virá os dados
			//É importante concatenar o valor digitado no CEP
			url: 'https://viacep.com.br/ws/'+$(this).val()+'/json/unicode/',
			//Aqui você deve preencher o tipo de dados que será lido,
			//no caso, estamos lendo JSON.
			dataType: 'json',
			//SUCESS é referente a função que será executada caso
			//ele consiga ler a fonte de dados com sucesso.
			//O parâmetro dentro da função se refere ao nome da variável
			//que você vai dar para ler esse objeto.
			success: function(resposta){
				//Agora basta definir os valores que você deseja preencher
				//automaticamente nos campos acima.
				$("#aluno_endereco").val(resposta.logradouro.toUpperCase());
				$("#aluno_complemento").val(resposta.complemento.toUpperCase());
				$("#aluno_bairro").val(resposta.bairro.toUpperCase());
				$("#aluno_municipio").val(resposta.localidade.toUpperCase());
				$("#aluno_uf").val(resposta.uf.toUpperCase());
				//$("#sec_ibge_municipio").val(resposta.ibge);
				$("#aluno_num").val(resposta.numero.toUpperCase());
				//Vamos incluir para que o Número seja focado automaticamente
				//melhorando a experiência do usuário
				//$("#numero").focus();
			}
		});
		
	});
</script>



</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($paises);

mysql_free_result($EscolaLogada);
?>
