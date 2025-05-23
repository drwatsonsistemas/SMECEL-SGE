<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('../funcoes/url_base.php'); ?>
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

function removerAcentos($string)
{
  return strtr(
    $string,
    'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿ',
    'AAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaaceeeeiiiinoooooouuuuyy'
  );
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  if ($row_UsuLogado['usu_update'] == "N") {
    header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
    exit;
  }
  // Verificar se a data é "00/00/0000"
  if ($_POST['aluno_nascimento'] == '00/00/0000') {
    // Captura a URL atual, incluindo a query string
    $queryString = $_SERVER['QUERY_STRING'];

    // Verifica se já existem parâmetros na query string
    if (!empty($queryString)) {
      // Adiciona o novo parâmetro dataInvalida
      $queryString .= '&dataInvalida';
    } else {
      // Caso não haja parâmetros, apenas adiciona o dataInvalida
      $queryString = 'dataInvalida';
    }

    // Redireciona de volta para a página atual com os parâmetros GET
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $queryString);
    exit;
  }


  $cpf = $_POST['aluno_cpf'];
  $cpfLimpo = preg_replace('/[^0-9]/', '', $cpf);

  $nomeAluno = mb_strtoupper(removerAcentos($_POST['aluno_nome']), 'UTF-8');
  $nomeAluno = str_replace("'", "", $nomeAluno);
  
  $filiacao1 = mb_strtoupper(removerAcentos($_POST['aluno_filiacao1']), 'UTF-8');
  $filiacao2 = mb_strtoupper(removerAcentos($_POST['aluno_filiacao2']), 'UTF-8');

  
  $updateSQL = sprintf("UPDATE smc_aluno SET aluno_cod_inep=%s, aluno_cpf=%s, aluno_nome=%s, aluno_nascimento=%s, aluno_filiacao1=%s, aluno_filiacao2=%s, 
    aluno_sexo=%s, aluno_raca=%s, aluno_nacionalidade=%s, aluno_pais=%s, aluno_uf_nascimento=%s, aluno_municipio_nascimento_ibge=%s, aluno_aluno_com_deficiencia=%s, aluno_nis=%s,
    aluno_identidade=%s, aluno_emissor=%s, aluno_uf_emissor=%s, aluno_data_espedicao=%s, aluno_tipo_certidao=%s, aluno_termo=%s, aluno_folhas=%s, aluno_livro=%s, 
    aluno_emissao_certidao=%s, aluno_uf_cartorio=%s, aluno_mucicipio_cartorio=%s, aluno_nome_cartorio=%s, aluno_num_matricula_modelo_novo=%s, aluno_localizacao=%s, 
    aluno_cep=%s, aluno_endereco=%s, aluno_numero=%s, aluno_complemento=%s, aluno_bairro=%s, aluno_uf=%s, aluno_municipio=%s, aluno_telefone=%s, aluno_celular=%s, 
    aluno_email=%s, aluno_sus=%s, aluno_tipo_deficiencia=%s, aluno_laudo=%s, aluno_alergia=%s, aluno_alergia_qual=%s, aluno_destro=%s, aluno_emergencia_avisar=%s, 
    aluno_emergencia_tel1=%s, aluno_emergencia_tel2=%s, aluno_prof_mae=%s, aluno_tel_mae=%s, aluno_escolaridade_mae=%s, aluno_rg_mae=%s, aluno_cpf_mae=%s, aluno_prof_pai=%s, 
    aluno_tel_pai=%s, aluno_escolaridade_pai=%s, aluno_rg_pai=%s, aluno_cpf_pai=%s, aluno_recebe_bolsa_familia=%s,
    aluno_def_bvisao=%s, aluno_def_cegueira=%s, aluno_def_auditiva=%s, aluno_def_fisica=%s, aluno_def_intelectual=%s, aluno_def_surdez=%s, aluno_def_surdocegueira=%s, 
    aluno_def_autista=%s, aluno_def_superdotacao=%s, aluno_sangue_tipo=%s, aluno_sangue_rh=%s, aluno_nome_responsavel_legal=%s, aluno_cpf_responsavel_legal=%s, aluno_grau_responsavel_legal=%s, aluno_observacao=%s, aluno_nis_mae=%s, aluno_sus_mae=%s, aluno_nis_pai=%s, aluno_sus_pai=%s, aluno_cid=%s, aluno_nome_social=%s  
    WHERE aluno_id=%s",
    GetSQLValueString($_POST['aluno_cod_inep'], "text"),
    GetSQLValueString($cpfLimpo, "text"),
    GetSQLValueString($nomeAluno, "text"),
    GetSQLValueString(inverteData($_POST['aluno_nascimento']), "date"),
    GetSQLValueString($filiacao1, "text"),
    GetSQLValueString($filiacao2, "text"),
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
    GetSQLValueString(isset($_POST['aluno_def_bvisao']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_cegueira']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_auditiva']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_fisica']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_intelectual']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_surdez']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_surdocegueira']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_autista']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString(isset($_POST['aluno_def_superdotacao']) ? "true" : "", "defined", "1", "0"),
    GetSQLValueString($_POST['aluno_sangue_tipo'], "text"),
    GetSQLValueString($_POST['aluno_sangue_rh'], "text"),
    GetSQLValueString($_POST['aluno_nome_responsavel_legal'], "text"),
    GetSQLValueString($_POST['aluno_cpf_responsavel_legal'], "text"),
    GetSQLValueString($_POST['aluno_grau_responsavel_legal'], "int"),
    GetSQLValueString($_POST['aluno_observacao'], "text"),
    GetSQLValueString($_POST['aluno_nis_mae'], "text"),
    GetSQLValueString($_POST['aluno_sus_mae'], "text"),
    GetSQLValueString($_POST['aluno_nis_pai'], "text"),
    GetSQLValueString($_POST['aluno_sus_pai'], "text"),
    GetSQLValueString($_POST['aluno_cid'], "text"),
    GetSQLValueString($_POST['aluno_nome_social'], "text"),
    GetSQLValueString($_POST['aluno_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());



  $usu = $_POST['usu_id'];
  $esc = $_POST['usu_escola'];
  $detalhes = $_POST['detalhes'];
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
'10', 
'($detalhes)', 
'$dat')
";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());



  $updateGoTo = "matriculaExibe.php?dadosEditados";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_alunoEditar = "-1";
if (isset($_GET['hash'])) {
  $colname_alunoEditar = $_GET['hash'];
}

$colname_matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_matricula = $_GET['cmatricula'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_alunoEditar = sprintf("
  SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, 
  aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_pais,
  aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, 
  aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, 
  aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, 
  aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, 
  aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, 
  aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, 
  aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, 
  aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, 
  aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae,aluno_nis_mae,aluno_sus_mae, aluno_prof_pai, aluno_tel_pai, 
  aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai,aluno_nis_pai,aluno_sus_pai, aluno_recebe_bolsa_familia, aluno_hash, aluno_foto,
  aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_cid,
  aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao, aluno_sangue_tipo, aluno_sangue_rh, aluno_nome_responsavel_legal, aluno_cpf_responsavel_legal, aluno_grau_responsavel_legal,aluno_observacao, aluno_nome_social,
  municipio_id, municipio_cod_ibge, municipio_nome, municipio_sigla_uf 
  FROM smc_aluno 
  LEFT JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge
  WHERE aluno_hash = %s", GetSQLValueString($colname_alunoEditar, "text"));
$alunoEditar = mysql_query($query_alunoEditar, $SmecelNovo) or die(mysql_error());
$row_alunoEditar = mysql_fetch_assoc($alunoEditar);
$totalRows_alunoEditar = mysql_num_rows($alunoEditar);

if ($totalRows_alunoEditar == "") {
  //echo "TURMA EM BRANCO";	
  header("Location: turmasAlunosVinculados.php?nada");
  exit;
}





?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>

  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .select2-container .select2-selection {
        border: 0px solid #ddd; /* Adicione bordas similares ao LWS */
        border-radius: 4px;
        height: 40px;
        padding: 2px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px; /* Alinhar texto verticalmente */
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>



  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">Editar dados do aluno</h1>
      <!-- CONTEÚDO -->

      <p>
      <h3>Aluno: <strong><?php echo htmlentities($row_alunoEditar['aluno_nome'], ENT_COMPAT, 'utf-8'); ?></strong></h3>
      </p>
      <br>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">
        <fieldset>
        <div id="ident" class="ls-box ls-box-gray">
  <p class="ls-title-4 col-md-12 ls-ico-user">IDENTIFICAÇÃO</p>

  <!-- Dados principais -->
  <div class="row">
    <label class="ls-label col-md-12">
      <b class="ls-label-text">Identificação única (código gerado pelo Inep)</b>
      <input type="text" 
             name="aluno_cod_inep"
             value="<?php echo htmlentities($row_alunoEditar['aluno_cod_inep'], ENT_COMPAT, 'utf-8'); ?>" 
             size="32">
    </label>

    <label class="ls-label col-md-6">
      <b class="ls-label-text">Nome completo</b>
      
      <input type="text" 
             name="aluno_nome"
             value="<?php echo htmlentities($row_alunoEditar['aluno_nome'], ENT_COMPAT, 'utf-8'); ?>" 
             size="32"
             required>
    </label>

    <label class="ls-label col-md-6">
      <b class="ls-label-text">Nome Social</b>
      
      <label class="ls-label-text">
        <input type="checkbox" 
               name="usar_nome_social" 
               id="usar_nome_social"
               <?php echo ($row_alunoEditar['aluno_nome_social'] != '') ? 'checked' : ''; ?>
               onchange="toggleNomeSocial()">
        Usar Nome Social
        <a href="#" 
           class="ls-ico-help" 
           data-ls-popover="open" 
           data-trigger="hover" 
           data-ls-module="popover"
           data-placement="top"
           data-content="O Nome Social é o nome pelo qual pessoas transgêneras, travestis ou não binárias preferem ser chamadas, refletindo sua identidade de gênero. Use este campo apenas se o aluno optar por um nome diferente do seu nome de registro."
           data-title="O que é Nome Social?"></a>
      </label>
      <input type="text" 
             name="aluno_nome_social"
             id="nome_social_input"
             value="<?php echo htmlentities($row_alunoEditar['aluno_nome_social'], ENT_COMPAT, 'utf-8'); ?>"
             size="32"
             style="margin-top: 5px;"
             <?php echo ($row_alunoEditar['aluno_nome_social'] == '') ? 'disabled' : ''; ?>>
    </label>
  </div>

  <!-- Dados de nascimento -->
  <div class="row">
    <label class="ls-label col-md-3">
      <b class="ls-label-text">Data de Nascimento</b>
      <input type="text" 
             name="aluno_nascimento"
             value="<?php echo htmlentities(inverteData($row_alunoEditar['aluno_nascimento']), ENT_COMPAT, 'utf-8'); ?>"
             size="32" 
             class="date" 
             required>
    </label>

    <label class="ls-label col-md-3">
      <b class="ls-label-text">UF de nascimento</b>
      <div class="ls-custom-select">
        <select name="aluno_uf_nascimento" id="aluno_uf_nascimento" required>
          <option value="">Escolha...</option>
          <?php
          $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
          foreach ($ufs as $uf) {
            echo "<option value='$uf'";
            if ($row_alunoEditar['aluno_uf_nascimento'] == $uf) echo " SELECTED";
            echo ">$uf</option>";
          }
          ?>
        </select>
      </div>
    </label>

    <label class="ls-label col-md-6">
      <b class="ls-label-text">Município de nascimento</b>
      <div class="ls-custom-select">
      <select name="aluno_municipio_nascimento_ibge" id="aluno_municipio_nascimento_ibge" class="ls-select" required data-cidade-salva="<?php echo $row_alunoEditar['aluno_municipio_nascimento_ibge']; ?>">
  <option value="<?php echo $row_alunoEditar['aluno_municipio_nascimento_ibge'] ?>">
    <?php echo $row_alunoEditar['municipio_nome']; ?> - <?php echo $row_alunoEditar['municipio_sigla_uf']; ?>
  </option>
</select>
      </div>
    </label>
  </div>

  <!-- Dados demográficos -->
  <div class="row">
    <label class="ls-label col-md-3">
      <b class="ls-label-text">Sexo</b>
      <div class="ls-custom-select">
        <select name="aluno_sexo" required>
          <option value="">Escolha...</option>
          <option value="1" <?php if ($row_alunoEditar['aluno_sexo'] == "1") echo "SELECTED"; ?>>Masculino</option>
          <option value="2" <?php if ($row_alunoEditar['aluno_sexo'] == "2") echo "SELECTED"; ?>>Feminino</option>
        </select>
      </div>
    </label>

    <label class="ls-label col-md-3">
      <b class="ls-label-text">Cor</b>
      <div class="ls-custom-select">
        <select name="aluno_raca" required>
          <option value="">Escolha...</option>
          <option value="1" <?php if ($row_alunoEditar['aluno_raca'] == "1") echo "SELECTED"; ?>>Branca</option>
          <option value="2" <?php if ($row_alunoEditar['aluno_raca'] == "2") echo "SELECTED"; ?>>Preta</option>
          <option value="3" <?php if ($row_alunoEditar['aluno_raca'] == "3") echo "SELECTED"; ?>>Parda</option>
          <option value="4" <?php if ($row_alunoEditar['aluno_raca'] == "4") echo "SELECTED"; ?>>Amarela</option>
          <option value="5" <?php if ($row_alunoEditar['aluno_raca'] == "5") echo "SELECTED"; ?>>Indígena</option>
          <option value="6" <?php if ($row_alunoEditar['aluno_raca'] == "6") echo "SELECTED"; ?>>Não declarada</option>
        </select>
      </div>
    </label>

    <label class="ls-label col-md-3">
      <b class="ls-label-text">Nacionalidade</b>
      <div class="ls-custom-select">
        <select name="aluno_nacionalidade" id="aluno_nacionalidade">
          <option value="1" <?php if ($row_alunoEditar['aluno_nacionalidade'] == "1") echo "SELECTED"; ?>>Brasileira</option>
          <option value="2" <?php if ($row_alunoEditar['aluno_nacionalidade'] == "2") echo "SELECTED"; ?>>Brasileira - Nascido no exterior ou naturalizado</option>
          <option value="3" <?php if ($row_alunoEditar['aluno_nacionalidade'] == "3") echo "SELECTED"; ?>>Estrangeira</option>
        </select>
      </div>
    </label>

    <label class="ls-label col-md-3">
      <b class="ls-label-text">País</b>
      <div class="ls-custom-select">
        <select name="aluno_pais" id="aluno_pais" required>
          <?php do { ?>
            <option value="<?php echo $row_paises['pais_cod'] ?>" 
                    <?php if ($row_paises['pais_cod'] == $row_alunoEditar['aluno_pais']) echo "SELECTED"; ?>>
              <?php echo $row_paises['pais_nome'] ?>
            </option>
          <?php } while ($row_paises = mysql_fetch_assoc($paises)); ?>
        </select>
      </div>
    </label>
  </div>
</div>

          <div id="filia" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ls-ico-users">FILIAÇÃO</p>

            <label class="ls-label col-md-4"> <b class="ls-label-text">*Filiação 1:</b>
              <input type="text" name="aluno_filiacao1"
                value="<?php echo htmlentities($row_alunoEditar['aluno_filiacao1'], ENT_COMPAT, 'utf-8'); ?>" size="32"
                required>
            </label>


            <label class="ls-label col-md-3"> <b class="ls-label-text">Profissão da mãe:</b>
              <input type="text" name="aluno_prof_mae"
                value="<?php echo htmlentities($row_alunoEditar['aluno_prof_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Telefone da mãe:</b>
              <input type="text" name="aluno_tel_mae" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_tel_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>


            <label class="ls-label col-md-3">
              <b class="ls-label-text">Escolaridade da mãe:</b>
              <div class="ls-custom-select">
                <select name="aluno_escolaridade_mae">
                  <option value="" <?php if (!(strcmp(-1, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>Escolha...</option>
                  <option value="1" <?php if (!(strcmp(1, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>NÃO ESTUDOU</option>
                  <option value="5" <?php if (!(strcmp(5, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO FUNDAMENTAL INCOMPLETO</option>
                  <option value="2" <?php if (!(strcmp(2, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO FUNDAMENTAL COMPLETO</option>
                  <option value="6" <?php if (!(strcmp(6, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO MÉDIO INCOMPLETO</option>
                  <option value="3" <?php if (!(strcmp(3, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO MÉDIO COMPLETO</option>
                  <option value="7" <?php if (!(strcmp(7, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO SUPERIOR INCOMPLETO</option>
                  <option value="4" <?php if (!(strcmp(4, htmlentities($row_alunoEditar['aluno_escolaridade_mae'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO SUPERIOR COMPLETO</option>
                </select>
              </div>
            </label>

            <hr>
            <hr><br><br>

            <label class="ls-label col-md-3"> <b class="ls-label-text">RG da mãe:</b>
              <input type="text" name="aluno_rg_mae"
                value="<?php echo htmlentities($row_alunoEditar['aluno_rg_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">CPF da mãe:</b>
              <input type="text" name="aluno_cpf_mae" maxlength="14" onblur="javascript: validarCPF(this);"
                onkeypress="javascript: mascara(this, cpf_mask);" class="cpf"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cpf_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">NIS da mãe:</b>
              <input type="text" name="aluno_nis_mae" maxlength="11"
                value="<?php echo htmlentities($row_alunoEditar['aluno_nis_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">SUS da mãe:</b>
              <input type="text" name="aluno_sus_mae" maxlength="16"
                value="<?php echo htmlentities($row_alunoEditar['aluno_sus_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>

            <label class="ls-label col-md-4"> <b class="ls-label-text">Filiação 2:</b>
              <input type="text" name="aluno_filiacao2"
                value="<?php echo htmlentities($row_alunoEditar['aluno_filiacao2'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>

            <label class="ls-label col-md-3"> <b class="ls-label-text">Profissão do pai:</b>
              <input type="text" name="aluno_prof_pai"
                value="<?php echo htmlentities($row_alunoEditar['aluno_prof_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Telefone do pai:</b>
              <input type="text" name="aluno_tel_pai" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_tel_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>



            <label class="ls-label col-md-3">
              <b class="ls-label-text">Escolaridade do pai:</b>
              <div class="ls-custom-select">
                <select name="aluno_escolaridade_pai">
                  <option value="" <?php if (!(strcmp(-1, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>Escolha...</option>
                  <option value="1" <?php if (!(strcmp(1, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>NÃO ESTUDOU</option>
                  <option value="5" <?php if (!(strcmp(5, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO FUNDAMENTAL INCOMPLETO</option>
                  <option value="2" <?php if (!(strcmp(2, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO FUNDAMENTAL COMPLETO</option>
                  <option value="6" <?php if (!(strcmp(6, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO MÉDIO INCOMPLETO</option>
                  <option value="3" <?php if (!(strcmp(3, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO MÉDIO COMPLETO</option>
                  <option value="7" <?php if (!(strcmp(7, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO SUPERIOR INCOMPLETO</option>
                  <option value="4" <?php if (!(strcmp(4, htmlentities($row_alunoEditar['aluno_escolaridade_pai'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ENSINO SUPERIOR COMPLETO</option>
                </select>
              </div>
            </label>

            <hr><br>
            <hr>

            <label class="ls-label col-md-3"> <b class="ls-label-text">RG do pai:</b>
              <input type="text" name="aluno_rg_pai"
                value="<?php echo htmlentities($row_alunoEditar['aluno_rg_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">CPF do pai:</b>
              <input type="text" name="aluno_cpf_pai" maxlength="14" onblur="javascript: validarCPF(this);"
                onkeypress="javascript: mascara(this, cpf_mask);" class="cpf"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cpf_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">NIS do pai:</b>
              <input type="text" name="aluno_nis_pai" maxlength="11"
                value="<?php echo htmlentities($row_alunoEditar['aluno_nis_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">SUS do pai:</b>
              <input type="text" name="aluno_sus_pai" maxlength="16"
                value="<?php echo htmlentities($row_alunoEditar['aluno_sus_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>

            <label class="ls-label col-md-12">
              <small>*Preferencialmente nome da mãe no campo Filiação 1</small>
            </label>
          </div>

          <div id="responsavel" class="ls-box ls-box-gray">

            <p class="ls-title-4 col-md-12 ls-ico-users">RESPONSÁVEL LEGAL</p>

            <label class="ls-label col-md-5"> <b class="ls-label-text">Responsável legal (caso não seja criado
                por pai/mãe):</b>
              <input type="text" name="aluno_nome_responsavel_legal"
                value="<?php echo htmlentities($row_alunoEditar['aluno_nome_responsavel_legal'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">CPF do responsável legal:</b>
              <input type="text" maxlength="14" name="aluno_cpf_responsavel_legal"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cpf_responsavel_legal'], ENT_COMPAT, 'utf-8'); ?>"
                size="32" onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);"
                class="cpf">
            </label>

            <label class="ls-label col-md-4">
              <b class="ls-label-text">Grau de parentesco do responsável legal:</b>
              <div class="ls-custom-select">
                <select name="aluno_grau_responsavel_legal">
                  <option value="">Escolha...</option>
                  <option value="1" <?php if (!(strcmp(1, htmlentities($row_alunoEditar['aluno_grau_responsavel_legal'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>IRMÃO/IRMÃ</option>
                  <option value="2" <?php if (!(strcmp(2, htmlentities($row_alunoEditar['aluno_grau_responsavel_legal'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>TIO/TIA</option>
                  <option value="3" <?php if (!(strcmp(3, htmlentities($row_alunoEditar['aluno_grau_responsavel_legal'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AVÔ/AVÓ</option>
                  <option value="4" <?php if (!(strcmp(4, htmlentities($row_alunoEditar['aluno_grau_responsavel_legal'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>OUTRO</option>
                </select>
              </div>
            </label>


            <hr><br>
            <hr>


          </div>


          <div id="docum" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-text">DOCUMENTAÇÃO</p>
            <label class="ls-label col-md-6"> <b class="ls-label-text">CPF:</b>
              <input type="text" name="aluno_cpf" maxlength="14"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cpf'], ENT_COMPAT, 'utf-8'); ?>"
                onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
            </label>
            <label class="ls-label col-md-6"> <b class="ls-label-text">NIS:</b>
              <input type="text" name="aluno_nis" class="nis"
                value="<?php echo htmlentities($row_alunoEditar['aluno_nis'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Cartão do SUS:</b>
              <input type="text" name="aluno_sus" class="sus"
                value="<?php echo htmlentities($row_alunoEditar['aluno_sus'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Número da Identidade:</b>
              <input type="text" name="aluno_identidade"
                value="<?php echo htmlentities($row_alunoEditar['aluno_identidade'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Órgão Emissor:</b>
              <input type="text" name="aluno_emissor"
                value="<?php echo htmlentities($row_alunoEditar['aluno_emissor'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">UF Emissor:</b>
              <div class="ls-custom-select">
                <select name="aluno_uf_emissor">
                  <option value="">Escolha...</option>
                  <option value="AC" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AC</option>
                  <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AL</option>
                  <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AP</option>
                  <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AM</option>
                  <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>BA</option>
                  <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>CE</option>
                  <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>DF</option>
                  <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ES</option>
                  <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>GO</option>
                  <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MA</option>
                  <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MT</option>
                  <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MS</option>
                  <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MG</option>
                  <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PA</option>
                  <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PB</option>
                  <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PR</option>
                  <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PE</option>
                  <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PI</option>
                  <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RJ</option>
                  <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RN</option>
                  <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RS</option>
                  <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RO</option>
                  <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RR</option>
                  <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SC</option>
                  <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SP</option>
                  <option value="SE" <?php if (!(strcmp("SE", htmlentities($row_alunoEditar['aluno_uf_nascimento'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SE</option>
                  <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_alunoEditar['aluno_uf_emissor'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>TO</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Expedição:</b>
              <input type="text" name="aluno_data_espedicao" class="date"
                value="<?php echo htmlentities(inverteData($row_alunoEditar['aluno_data_espedicao']), ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Modelo de Certidão:</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_tipo_certidao" onclick="javascript:modeloantigo_certidao();" value="1"
                  <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_tipo_certidao'], ENT_COMPAT, 'utf-8'), 1))) {
                    echo "checked=\"checked\"";
                  } ?>>
                Antigo</label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_tipo_certidao" onclick="javascript:modelonovo_certidao();" value="2"
                  <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_tipo_certidao'], ENT_COMPAT, 'utf-8'), 2))) {
                    echo "checked=\"checked\"";
                  } ?>>
                Novo </label>
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Termo:</b>
              <input type="text" name="aluno_termo" id="aluno_termo"
                value="<?php echo htmlentities($row_alunoEditar['aluno_termo'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Folhas:</b>
              <input type="text" name="aluno_folhas" id="aluno_folhas"
                value="<?php echo htmlentities($row_alunoEditar['aluno_folhas'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Livro:</b>
              <input type="text" name="aluno_livro" id="aluno_livro"
                value="<?php echo htmlentities($row_alunoEditar['aluno_livro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Emissão:</b>
              <input type="text" name="aluno_emissao_certidao" id="aluno_emissao_certidao" class="date"
                value="<?php echo htmlentities(inverteData($row_alunoEditar['aluno_emissao_certidao']), ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Cartorio:</b>
              <div class="ls-custom-select">
                <select name="aluno_uf_cartorio" id="aluno_uf_cartorio">
                  <option value="">Escolha...</option>
                  <option value="AC" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AC</option>
                  <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AL</option>
                  <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AP</option>
                  <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AM</option>
                  <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>BA</option>
                  <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>CE</option>
                  <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>DF</option>
                  <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ES</option>
                  <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>GO</option>
                  <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MA</option>
                  <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MT</option>
                  <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MS</option>
                  <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MG</option>
                  <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PA</option>
                  <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PB</option>
                  <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PR</option>
                  <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PE</option>
                  <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PI</option>
                  <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RJ</option>
                  <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RN</option>
                  <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RS</option>
                  <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RO</option>
                  <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RR</option>
                  <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SC</option>
                  <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SP</option>
                  <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_alunoEditar['aluno_uf_cartorio'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>TO</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-4"> <b class="ls-label-text">Município:</b>
              <input type="text" name="aluno_mucicipio_cartorio" id="aluno_mucicipio_cartorio"
                value="<?php echo htmlentities($row_alunoEditar['aluno_mucicipio_cartorio'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-6"> <b class="ls-label-text">Nome do Cartório:</b>
              <input type="text" name="aluno_nome_cartorio" id="aluno_nome_cartorio"
                value="<?php echo htmlentities($row_alunoEditar['aluno_nome_cartorio'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-12"> <b class="ls-label-text">Matrícula Modelo Novo:</b>
              <input type="text" name="aluno_num_matricula_modelo_novo" id="aluno_num_matricula_modelo_novo"
                class="certidao"
                value="<?php echo htmlentities($row_alunoEditar['aluno_num_matricula_modelo_novo'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
          </div>
          <div id="local" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-target">LOCALIZAÇÃO</p>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Localização:</b>
              <div class="ls-custom-select">
                <select name="aluno_localizacao" required>
                  <option value="">Escolha...</option>
                  <option value="1" <?php if (!(strcmp(1, htmlentities($row_alunoEditar['aluno_localizacao'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>Urbana</option>
                  <option value="2" <?php if (!(strcmp(2, htmlentities($row_alunoEditar['aluno_localizacao'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>Rural</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">CEP:</b>
              <input type="text" name="aluno_cep" id="aluno_cep"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cep'], ENT_COMPAT, 'utf-8'); ?>" size="32"
                class="cep">
            </label>
            <label class="ls-label col-md-6"> <b class="ls-label-text">Endereço:</b>
              <input type="text" name="aluno_endereco" id="aluno_endereco"
                value="<?php echo htmlentities($row_alunoEditar['aluno_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2"> <b class="ls-label-text">Número:</b>
              <input type="text" name="aluno_numero" id="aluno_numero"
                value="<?php echo htmlentities($row_alunoEditar['aluno_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Complemento:</b>
              <input type="text" name="aluno_complemento" id="aluno_complemento"
                value="<?php echo htmlentities($row_alunoEditar['aluno_complemento'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Bairro:</b>
              <input type="text" name="aluno_bairro" id="aluno_bairro"
                value="<?php echo htmlentities($row_alunoEditar['aluno_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">UF:</b>
              <div class="ls-custom-select">
                <select name="aluno_uf" id="aluno_uf">
                  <option value="">Escolha...</option>
                  <option value="AC" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AC</option>
                  <option value="AL" <?php if (!(strcmp("AL", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AL</option>
                  <option value="AP" <?php if (!(strcmp("AP", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AP</option>
                  <option value="AM" <?php if (!(strcmp("AM", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AM</option>
                  <option value="BA" <?php if (!(strcmp("BA", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>BA</option>
                  <option value="CE" <?php if (!(strcmp("CE", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>CE</option>
                  <option value="DF" <?php if (!(strcmp("DF", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>DF</option>
                  <option value="ES" <?php if (!(strcmp("ES", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>ES</option>
                  <option value="GO" <?php if (!(strcmp("GO", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>GO</option>
                  <option value="MA" <?php if (!(strcmp("MA", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MA</option>
                  <option value="MT" <?php if (!(strcmp("MT", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MT</option>
                  <option value="MS" <?php if (!(strcmp("MS", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MS</option>
                  <option value="MG" <?php if (!(strcmp("MG", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>MG</option>
                  <option value="PA" <?php if (!(strcmp("PA", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PA</option>
                  <option value="PB" <?php if (!(strcmp("PB", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PB</option>
                  <option value="PR" <?php if (!(strcmp("PR", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PR</option>
                  <option value="PE" <?php if (!(strcmp("PE", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PE</option>
                  <option value="PI" <?php if (!(strcmp("PI", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>PI</option>
                  <option value="RJ" <?php if (!(strcmp("RJ", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RJ</option>
                  <option value="RN" <?php if (!(strcmp("RN", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RN</option>
                  <option value="RS" <?php if (!(strcmp("RS", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RS</option>
                  <option value="RO" <?php if (!(strcmp("RO", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RO</option>
                  <option value="RR" <?php if (!(strcmp("RR", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>RR</option>
                  <option value="SC" <?php if (!(strcmp("SC", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SC</option>
                  <option value="SP" <?php if (!(strcmp("SP", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>SP</option>
                  <option value="TO" <?php if (!(strcmp("TO", htmlentities($row_alunoEditar['aluno_uf'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>TO</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-4"> <b class="ls-label-text">Município:</b>
              <input type="text" name="aluno_municipio" id="aluno_municipio"
                value="<?php echo htmlentities($row_alunoEditar['aluno_municipio'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Telefone/Celular:</b>
              <input type="text" name="aluno_telefone" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_telefone'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Celular/WhatsApp:</b>
              <input type="text" name="aluno_celular" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_celular'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
            <label class="ls-label col-md-6"> <b class="ls-label-text">E-mail:</b>
              <input type="text" name="aluno_email"
                value="<?php echo htmlentities($row_alunoEditar['aluno_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
          </div>
          <div id="medico" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-book">DADOS MÉDICOS</p>

            <label class="ls-label col-md-3">
              <b class="ls-label-text">Alergia e/ou intolerância?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_alergia" value="1" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_alergia'], ENT_COMPAT, 'utf-8'), 1))) {
                  echo "checked=\"checked\"";
                } ?>>
                Sim </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_alergia" value="2" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_alergia'], ENT_COMPAT, 'utf-8'), 2))) {
                  echo "checked=\"checked\"";
                } ?>>
                Não </label>
            </label>
            <label class="ls-label col-md-7"> <b class="ls-label-text">Descreva a alergia/intolerância:</b>
              <input type="text" name="aluno_alergia_qual" id="aluno_alergia_qual"
                value="<?php echo htmlentities($row_alunoEditar['aluno_alergia_qual'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Destro/Canhoto</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_destro" value="1" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_destro'], ENT_COMPAT, 'utf-8'), 1))) {
                  echo "checked=\"checked\"";
                } ?>>
                Destro </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_destro" value="2" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_destro'], ENT_COMPAT, 'utf-8'), 2))) {
                  echo "checked=\"checked\"";
                } ?>>
                Canhoto </label>
            </label>


            <label class="ls-label col-md-6">
              <b class="ls-label-text">Tipo Sanguíneo</b><br>

              <div class="ls-custom-select">
                <select name="aluno_sangue_tipo">
                  <option value="">Escolha...</option>
                  <option value="A" <?php if (!(strcmp("A", htmlentities($row_alunoEditar['aluno_sangue_tipo'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>A</option>
                  <option value="B" <?php if (!(strcmp("B", htmlentities($row_alunoEditar['aluno_sangue_tipo'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>B</option>
                  <option value="AB" <?php if (!(strcmp("AB", htmlentities($row_alunoEditar['aluno_sangue_tipo'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>AB</option>
                  <option value="O" <?php if (!(strcmp("O", htmlentities($row_alunoEditar['aluno_sangue_tipo'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>O</option>
                </select>
              </div>

            </label>

            <label class="ls-label col-md-6">
              <b class="ls-label-text">Fator RH</b><br>

              <div class="ls-custom-select">
                <select name="aluno_sangue_rh">
                  <option value="">Escolha...</option>
                  <option value="+" <?php if (!(strcmp("+", htmlentities($row_alunoEditar['aluno_sangue_rh'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>POSITIVO (+)</option>
                  <option value="-" <?php if (!(strcmp("-", htmlentities($row_alunoEditar['aluno_sangue_rh'], ENT_COMPAT, 'utf-8')))) {
                    echo "SELECTED";
                  } ?>>NEGATIVO (-)</option>
                </select>
              </div>

            </label>



            <label class="ls-label col-md-6"> <b class="ls-label-text">Em caso de emergência, avisar a:</b>
              <input type="text" name="aluno_emergencia_avisar"
                value="<?php echo htmlentities($row_alunoEditar['aluno_emergencia_avisar'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Telefone 1:</b>
              <input type="text" name="aluno_emergencia_tel1" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_emergencia_tel1'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-3"> <b class="ls-label-text">Telefone 2:</b>
              <input type="text" name="aluno_emergencia_tel2" class="celular"
                value="<?php echo htmlentities($row_alunoEditar['aluno_emergencia_tel2'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
          </div>


          <div id="deficiencia" class="ls-box ls-box-gray" style="display:block">
            <p class="ls-title-4 col-md-12 ls-ico-book">DEFICIÊNCIA, TEA OU HABILIDADES ESPECIAIS</p>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Aluno com deficiência?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_aluno_com_deficiencia" onclick="javascript:habilita_deficiencia();"
                  value="1" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_aluno_com_deficiencia'], ENT_COMPAT, 'utf-8'), 1))) {
                    echo "checked=\"checked\"";
                  } ?>>
                Sim </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_aluno_com_deficiencia" onclick="javascript:desabilita_deficiencia();"
                  value="2" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_aluno_com_deficiencia'], ENT_COMPAT, 'utf-8'), 2))) {
                    echo "checked=\"checked\"";
                  } ?>>
                Não </label>
            </label>
            <label class="ls-label col-md-7"> <b class="ls-label-text">Detalhar deficiência:</b>
              <input type="text" name="aluno_tipo_deficiencia" id="aluno_tipo_deficiencia"
                value="<?php echo htmlentities($row_alunoEditar['aluno_tipo_deficiencia'], ENT_COMPAT, 'utf-8'); ?>"
                size="32">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Possui Laudo Médico?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_laudo" value="1" id="laudo_medico_sim" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_laudo'], ENT_COMPAT, 'utf-8'), 1))) {
                  echo "checked=\"checked\"";
                } ?>>
                Sim </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_laudo" value="2" id="laudo_medico_nao" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_laudo'], ENT_COMPAT, 'utf-8'), 2))) {
                  echo "checked=\"checked\"";
                } ?>>
                Não </label>
            </label>
            <div class="ls-label col-md-12">
              <p>Deficiência:</p>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_bvisao" name="aluno_def_bvisao" value=""
                  <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_bvisao'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Baixa visão</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_cegueira" name="aluno_def_cegueira"
                  value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_cegueira'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Cegueira</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_auditiva" name="aluno_def_auditiva"
                  value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_auditiva'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Deficiência auditiva</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_fisica" name="aluno_def_fisica" value=""
                  <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_fisica'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Deficiência física</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_intelectual"
                  name="aluno_def_intelectual" value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_intelectual'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Deficiência intelectual</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_surdez" name="aluno_def_surdez" value=""
                  <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_surdez'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Surdez</label>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_surdocegueira"
                  name="aluno_def_surdocegueira" value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_surdocegueira'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Surdocegueira</label>
            </div>

            <div class="ls-label col-md-12">
              <p>Transtorno do espectro autista:</p>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_autista" name="aluno_def_autista"
                  value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_autista'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Transtorno do espectro autista</label>
            </div>

            <div class="ls-label col-md-12">
              <p>Altas habilidades/superdotação:</p>
              <label class="ls-label-text"><input type="checkbox" id="aluno_def_superdotacao"
                  name="aluno_def_superdotacao" value="" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_def_superdotacao'], ENT_COMPAT, 'utf-8'), "1"))) {
                    echo "checked=\"checked\"";
                  } ?> /> Altas habilidades/superdotação</label>
            </div>

            <label class="ls-label col-md-7"> <b class="ls-label-text">Informe o CID:</b>
              <input type="text" name="aluno_cid" id="aluno_cid"
                value="<?php echo htmlentities($row_alunoEditar['aluno_cid'], ENT_COMPAT, 'utf-8'); ?>" size="32">
            </label>
          </div>

          <div id="social" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-info">DADOS SOCIAIS</p>
            <label class="ls-label col-sm-12">
              <b class="ls-label-text">Aluno é beneficiário do Bolsa Família?</b><br>
              <br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_recebe_bolsa_familia" value="1" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_recebe_bolsa_familia'], ENT_COMPAT, 'utf-8'), 1))) {
                  echo "checked=\"checked\"";
                } ?>>
                Sim </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_recebe_bolsa_familia" value="0" <?php if (!(strcmp(htmlentities($row_alunoEditar['aluno_recebe_bolsa_familia'], ENT_COMPAT, 'utf-8'), 0))) {
                  echo "checked=\"checked\"";
                } ?>>
                Não </label>
            </label>
          </div>

          <div id="obs" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-pencil">OBSERVAÇÃO</p>

            <label class="ls-label">
              <b class="ls-label-text">Aparecerá na ficha individual do aluno</b>
              <textarea rows="4" name="aluno_observacao"
                class="ls-textarea-autoresize"><?php echo htmlentities($row_alunoEditar['aluno_observacao'], ENT_COMPAT, 'utf-8'); ?></textarea>
            </label>


          </div>
          <div id="foto" class="ls-box ls-box-gray">
            <div style="width:270px;height:360px;background-color:#d3d3d3;display:block;">
              <?php if ($row_alunoEditar['aluno_foto'] == "") { ?>
                <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg">
              <?php } else { ?>
                <img
                  src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo htmlentities($row_alunoEditar['aluno_foto'], ENT_COMPAT, 'utf-8'); ?>">
              <?php } ?>
            </div>
            <a class="ls-btn"
              href="webcam/index.php?hash=<?php echo htmlentities($row_alunoEditar['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo htmlentities($colname_matricula, ENT_COMPAT, 'utf-8'); ?>#foto">CADASTRAR
              / ALTERAR FOTO</a>
          </div>
        </fieldset>
        <div class="ls-actions-btn">
          <input type="submit" value="SALVAR ALTERAÇÕES" class="ls-btn-primary">
          <a href="matriculaExibe.php?cmatricula=<?php echo $colname_matricula; ?>" class="ls-btn">CANCELAR</a>
        </div>
        <input type="hidden" name="MM_update" value="form1">
        <input type="hidden" name="aluno_id" value="<?php echo $row_alunoEditar['aluno_id']; ?>">
        <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
        <input type="hidden" name="detalhes"
          value="<?php echo $row_alunoEditar['aluno_id']; ?> - <?php echo htmlentities($row_alunoEditar['aluno_nome'], ENT_COMPAT, 'utf-8'); ?>">
      </form>
      <p>&nbsp;</p>
      <!-- CONTEÚDO -->
    </div>
  </main>
  <aside class="ls-notification">
    <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
      <h3 class="ls-title-2">Notificações</h3>
      <ul>
        <?php include "notificacoes.php"; ?>
      </ul>
    </nav>
    <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
      <h3 class="ls-title-2">Feedback</h3>
      <ul>
        <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
      </ul>
    </nav>
    <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
      <h3 class="ls-title-2">Ajuda</h3>
      <ul>
        <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial
            (Vídeos)</a> </li>
        <li><a href="#">&gt; Guia</a></li>
        <li><a href="#">&gt; Wiki</a></li>
      </ul>
    </nav>
  </aside>

  <!-- We recommended use jQuery 1.10 or up -->

  <script src="js/locastyle.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="../../js/jquery.mask.js"></script>
  <script src="js/mascara.js"></script>
  <script src="js/validarCPF.js"></script>
  <script src="js/maiuscula.js"></script>
  <script src="js/semAcentos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
function toggleNomeSocial() {
  const checkbox = document.getElementById('usar_nome_social');
  const input = document.getElementById('nome_social_input');
  
  if (checkbox.checked) {
    input.disabled = false;
    input.focus();
  } else {
    input.disabled = true;
    input.value = '';
  }
}
</script>

  <?php if (isset($_GET['dataInvalida'])) { ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: "warning",
        title: "Data de nascimento inválida. Por favor, insira uma data válida."
      });
    </script>
  <?php } ?>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var nascimentoInput = document.querySelector('input[name="aluno_nascimento"]');

      nascimentoInput.addEventListener('input', function () {
        var data = nascimentoInput.value;

        // Verifique se a data é "00/00/0000"
        if (data === '00/00/0000') {
          //alert('Data de nascimento inválida. Por favor, insira uma data válida.');
          //nascimentoInput.value = ''; // Limpa o campo
          //nascimentoInput.focus();    // Coloca o foco no campo
        }
      });
    });
  </script>

  <script type="text/javascript">

    function habilita_deficiencia() {
      document.getElementById("aluno_tipo_deficiencia").disabled = false; //Habilitando
      document.getElementById("aluno_def_bvisao").disabled = false; //Habilitando
      document.getElementById("aluno_def_cegueira").disabled = false; //Habilitando
      document.getElementById("aluno_def_auditiva").disabled = false; //Habilitando
      document.getElementById("aluno_def_fisica").disabled = false; //Habilitando
      document.getElementById("aluno_def_intelectual").disabled = false; //Habilitando
      document.getElementById("aluno_def_surdez").disabled = false; //Habilitando
      document.getElementById("aluno_def_surdocegueira").disabled = false; //Habilitando
      document.getElementById("aluno_def_autista").disabled = false; //Habilitando
      document.getElementById("aluno_def_superdotacao").disabled = false; //Habilitando
      document.getElementById("laudo_medico_sim").disabled = false; //Habilitando
      document.getElementById("laudo_medico_nao").disabled = false; //Habilitando
      document.getElementById("aluno_cid").disabled = false; //Habilitando
    }
    function desabilita_deficiencia() {
      document.getElementById("aluno_tipo_deficiencia").disabled = true; //Desabilitando
      document.getElementById("aluno_def_bvisao").disabled = true; //Habilitando
      document.getElementById("aluno_def_cegueira").disabled = true; //Habilitando
      document.getElementById("aluno_def_auditiva").disabled = true; //Habilitando
      document.getElementById("aluno_def_fisica").disabled = true; //Habilitando
      document.getElementById("aluno_def_intelectual").disabled = true; //Habilitando
      document.getElementById("aluno_def_surdez").disabled = true; //Habilitando
      document.getElementById("aluno_def_surdocegueira").disabled = true; //Habilitando
      document.getElementById("aluno_def_autista").disabled = true; //Habilitando
      document.getElementById("aluno_def_superdotacao").disabled = true; //Habilitando
      document.getElementById("laudo_medico_sim").disabled = true; //Habilitando
      document.getElementById("laudo_medico_nao").disabled = true; //Habilitando
      document.getElementById("aluno_cid").disabled = true; //Habilitando
    }
    function modeloantigo_certidao() {
      document.getElementById("aluno_termo").disabled = false; //Habilitando
      document.getElementById("aluno_folhas").disabled = false; //Habilitando
      document.getElementById("aluno_livro").disabled = false; //Habilitando
      document.getElementById("aluno_emissao_certidao").disabled = false; //Habilitando
      document.getElementById("aluno_uf_cartorio").disabled = false; //Habilitando
      document.getElementById("aluno_mucicipio_cartorio").disabled = false; //Habilitando
      document.getElementById("aluno_nome_cartorio").disabled = false; //Habilitando
      document.getElementById("aluno_num_matricula_modelo_novo").disabled = true; //Habilitando
    }
    function modelonovo_certidao() {
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

  </script>
  <script language="Javascript">
    $(document).ready(function () {
      setTimeout('$("#preload").fadeOut(100)', 1500);
    });
  </script>
  <script type="text/javascript">
$(document).ready(function () {
    // Função para carregar cidades
    function carregarCidades(estado) {
        if (estado) {
            $.get('cidades.php?estado=' + estado, function (data) {
                console.log('Cidades carregadas:', data); // Depuração
                $('#aluno_municipio_nascimento_ibge').html(data);

                // Após carregar as cidades, seleciona a cidade salva
                const cidadeSalva = $('#aluno_municipio_nascimento_ibge').data('cidade-salva');
                if (cidadeSalva) {
                    $('#aluno_municipio_nascimento_ibge').val(cidadeSalva).trigger('change');
                }
            }).fail(function () {
                console.error('Erro ao carregar cidades.');
            });
        } else {
            $('#aluno_municipio_nascimento_ibge').html('<option value="">Selecione um estado primeiro</option>');
        }
    }

    // Evento de mudança no estado
    $('#aluno_uf_nascimento').change(function () {
        const estado = $(this).val();
        carregarCidades(estado);
    });

    // Carregar cidades ao iniciar, se já houver um estado selecionado
    const estadoInicial = $('#aluno_uf_nascimento').val();
    carregarCidades(estadoInicial);

    // Inicializar o Select2
    $('#aluno_municipio_nascimento_ibge').select2({
        width: '100%'
    });
});
  </script>


  <script type="text/javascript">


    $("#aluno_cep").focusout(function () {
      //Aqui vai o código	

      $.ajax({
        //O campo URL diz o caminho de onde virá os dados
        //É importante concatenar o valor digitado no CEP
        url: 'https://viacep.com.br/ws/' + $(this).val() + '/json/unicode/',
        //Aqui você deve preencher o tipo de dados que será lido,
        //no caso, estamos lendo JSON.
        dataType: 'json',
        //SUCESS é referente a função que será executada caso
        //ele consiga ler a fonte de dados com sucesso.
        //O parâmetro dentro da função se refere ao nome da variável
        //que você vai dar para ler esse objeto.
        success: function (resposta) {
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

mysql_free_result($EscolaLogada);

mysql_free_result($alunoEditar);
?>