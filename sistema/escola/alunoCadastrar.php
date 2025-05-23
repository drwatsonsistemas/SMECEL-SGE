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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
    exit;
  }

  if ($_POST['aluno_nascimento'] == '00/00/0000') {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = !empty($queryString) ? $queryString . '&dataInvalida' : 'dataInvalida';
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $queryString);
    exit;
  }

  // Remover máscaras dos campos
  $cpf = preg_replace('/[^0-9]/', '', $_POST['aluno_cpf']);
  $cep = preg_replace('/[^0-9]/', '', $_POST['aluno_cep']);
  $telefone = preg_replace('/[^0-9]/', '', $_POST['aluno_telefone']);
  $celular = preg_replace('/[^0-9]/', '', $_POST['aluno_celular']);
  $tel_mae = preg_replace('/[^0-9]/', '', $_POST['aluno_tel_mae']);
  $cpf_mae = preg_replace('/[^0-9]/', '', $_POST['aluno_cpf_mae']);
  $nis_mae = preg_replace('/[^0-9]/', '', $_POST['aluno_nis_mae']); // Sem máscara, mas garantindo apenas números
  $sus_mae = preg_replace('/[^0-9]/', '', $_POST['aluno_sus_mae']); // Sem máscara, mas garantindo apenas números
  $tel_pai = preg_replace('/[^0-9]/', '', $_POST['aluno_tel_pai']);
  $cpf_pai = preg_replace('/[^0-9]/', '', $_POST['aluno_cpf_pai']);
  $nis_pai = preg_replace('/[^0-9]/', '', $_POST['aluno_nis_pai']); // Sem máscara, mas garantindo apenas números
  $sus_pai = preg_replace('/[^0-9]/', '', $_POST['aluno_sus_pai']); // Sem máscara, mas garantindo apenas números
  $cpf_responsavel_legal = preg_replace('/[^0-9]/', '', $_POST['aluno_cpf_responsavel_legal']);
  $emergencia_tel1 = preg_replace('/[^0-9]/', '', $_POST['aluno_emergencia_tel1']);
  $emergencia_tel2 = preg_replace('/[^0-9]/', '', $_POST['aluno_emergencia_tel2']);

  $nomeAluno = mb_strtoupper(removerAcentos($_POST['aluno_nome']), 'UTF-8');
  $nomeAluno = str_replace("'", "", $nomeAluno);
  $filiacao1 = mb_strtoupper(removerAcentos($_POST['aluno_filiacao1']), 'UTF-8');
  $filiacao2 = mb_strtoupper(removerAcentos($_POST['aluno_filiacao2']), 'UTF-8');
  $geraHash = md5(time() . $_POST['aluno_nome'] . $_POST['escola_id']);

  $aluno_com_deficiencia = isset($_POST['aluno_aluno_com_deficiencia']) ? $_POST['aluno_aluno_com_deficiencia'] : null;
  $tipo_certidao = isset($_POST['aluno_tipo_certidao']) ? $_POST['aluno_tipo_certidao'] : null;
  $laudo = isset($_POST['aluno_laudo']) ? $_POST['aluno_laudo'] : null;
  $alergia = isset($_POST['aluno_alergia']) ? $_POST['aluno_alergia'] : null;
  $destro = isset($_POST['aluno_destro']) ? $_POST['aluno_destro'] : null;
  $recebe_bolsa_familia = isset($_POST['aluno_recebe_bolsa_familia']) ? $_POST['aluno_recebe_bolsa_familia'] : null;
  $nome_social = isset($_POST['usar_nome_social']) && $_POST['usar_nome_social'] === 'on' ? $_POST['aluno_nome_social'] : null;
  $tipo_deficiencia = isset($_POST['aluno_tipo_deficiencia']) ? $_POST['aluno_tipo_deficiencia'] : null;
  $alergia_qual = isset($_POST['aluno_alergia_qual']) ? $_POST['aluno_alergia_qual'] : null;

  $rg_responsavel_legal = preg_replace('/[^0-9]/', '', $_POST['aluno_rg_responsavel_legal']);
  $nis_responsavel_legal = preg_replace('/[^0-9]/', '', $_POST['aluno_nis_responsavel_legal']);
  $sus_responsavel_legal = preg_replace('/[^0-9]/', '', $_POST['aluno_sus_responsavel_legal']);

  $insertSQL = sprintf(
    "INSERT INTO smc_aluno (
      aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, 
      aluno_nacionalidade, aluno_pais, aluno_uf_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, 
      aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, 
      aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, 
      aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, 
      aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, 
      aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, 
      aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, 
      aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_recebe_bolsa_familia, 
      aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, 
      aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao, aluno_sangue_tipo, aluno_sangue_rh, 
      aluno_nome_responsavel_legal, aluno_cpf_responsavel_legal, aluno_grau_responsavel_legal, aluno_hash, aluno_nis_mae, 
      aluno_sus_mae, aluno_nis_pai, aluno_sus_pai, aluno_cid, aluno_nome_social, aluno_observacao,aluno_rg_responsavel_legal, aluno_nis_responsavel_legal, aluno_sus_responsavel_legal
    ) 
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['aluno_cod_inep'], "text"),
    GetSQLValueString($cpf, "text"), // CPF sem máscara
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
    GetSQLValueString($aluno_com_deficiencia, "int"),
    GetSQLValueString($nis_mae, "text"), // NIS sem máscara
    GetSQLValueString($_POST['aluno_identidade'], "text"),
    GetSQLValueString($_POST['aluno_emissor'], "text"),
    GetSQLValueString($_POST['aluno_uf_emissor'], "text"),
    GetSQLValueString(inverteData($_POST['aluno_data_espedicao']), "date"),
    GetSQLValueString($tipo_certidao, "int"),
    GetSQLValueString($_POST['aluno_termo'], "text"),
    GetSQLValueString($_POST['aluno_folhas'], "text"),
    GetSQLValueString($_POST['aluno_livro'], "text"),
    GetSQLValueString(inverteData($_POST['aluno_emissao_certidao']), "date"),
    GetSQLValueString($_POST['aluno_uf_cartorio'], "text"),
    GetSQLValueString($_POST['aluno_mucicipio_cartorio'], "text"),
    GetSQLValueString($_POST['aluno_nome_cartorio'], "text"),
    GetSQLValueString($_POST['aluno_num_matricula_modelo_novo'], "text"),
    GetSQLValueString($_POST['aluno_localizacao'], "int"),
    GetSQLValueString($cep, "text"), // CEP sem máscara
    GetSQLValueString($_POST['aluno_endereco'], "text"),
    GetSQLValueString($_POST['aluno_numero'], "text"),
    GetSQLValueString($_POST['aluno_complemento'], "text"),
    GetSQLValueString($_POST['aluno_bairro'], "text"),
    GetSQLValueString($_POST['aluno_uf'], "text"),
    GetSQLValueString($_POST['aluno_municipio'], "text"),
    GetSQLValueString($telefone, "text"), // Telefone sem máscara
    GetSQLValueString($celular, "text"), // Celular sem máscara
    GetSQLValueString($_POST['aluno_email'], "text"),
    GetSQLValueString($sus_mae, "text"), // SUS sem máscara
    GetSQLValueString($tipo_deficiencia, "text"),
    GetSQLValueString($laudo, "int"),
    GetSQLValueString($alergia, "int"),
    GetSQLValueString($alergia_qual, "text"),
    GetSQLValueString($destro, "int"),
    GetSQLValueString($_POST['aluno_emergencia_avisar'], "text"),
    GetSQLValueString($emergencia_tel1, "text"), // Telefone de emergência sem máscara
    GetSQLValueString($emergencia_tel2, "text"), // Telefone de emergência sem máscara
    GetSQLValueString($_POST['aluno_prof_mae'], "text"),
    GetSQLValueString($tel_mae, "text"), // Telefone da mãe sem máscara
    GetSQLValueString($_POST['aluno_escolaridade_mae'], "int"),
    GetSQLValueString($_POST['aluno_rg_mae'], "text"),
    GetSQLValueString($cpf_mae, "text"), // CPF da mãe sem máscara
    GetSQLValueString($_POST['aluno_prof_pai'], "text"),
    GetSQLValueString($tel_pai, "text"), // Telefone do pai sem máscara
    GetSQLValueString($_POST['aluno_escolaridade_pai'], "int"),
    GetSQLValueString($_POST['aluno_rg_pai'], "text"),
    GetSQLValueString($cpf_pai, "text"), // CPF do pai sem máscara
    GetSQLValueString($recebe_bolsa_familia, "int"),
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
    GetSQLValueString($cpf_responsavel_legal, "text"), // CPF do responsável sem máscara
    GetSQLValueString($_POST['aluno_grau_responsavel_legal'], "text"),
    GetSQLValueString($geraHash, "text"),
    GetSQLValueString($nis_mae, "text"), // NIS da mãe sem máscara
    GetSQLValueString($sus_mae, "text"), // SUS da mãe sem máscara
    GetSQLValueString($nis_pai, "text"), // NIS do pai sem máscara
    GetSQLValueString($sus_pai, "text"), // SUS do pai sem máscara
    GetSQLValueString($_POST['aluno_cid'], "text"),
    GetSQLValueString($nome_social, "text"),
    GetSQLValueString($_POST['aluno_observacao'], "text"),
    GetSQLValueString($rg_responsavel_legal, "text"),
    GetSQLValueString($nis_responsavel_legal, "text"), // NIS do responsável legal sem máscara
    GetSQLValueString($sus_responsavel_legal, "text") // SUS do responsável legal sem máscara
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond/dist/filepond.css">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.css">
  <script src="https://cdn.jsdelivr.net/npm/filepond/dist/filepond.min.js"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
  <!-- SELECT2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .select2-container .select2-selection {
      border: 0px solid #ddd;
      /* Adicione bordas similares ao LWS */
      border-radius: 4px;
      height: 40px;
      padding: 2px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 28px;
      /* Alinhar texto verticalmente */
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

      <h1 class="ls-title-intro ls-ico-home">Cadastro de Aluno</h1>
      <!-- CONTEÚDO -->

      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data"
        class="ls-form ls-form-horizontal row">
        <fieldset>
          <div id="ident" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-user">IDENTIFICAÇÃO</p>
            <div class="row">
              <label class="ls-label col-md-12">
                <b class="ls-label-text">Identificação única (gerado pelo Inep)</b>
                <input type="text" name="aluno_cod_inep" value="" maxlength="15">
              </label>
              <label class="ls-label col-md-6">
                <b class="ls-label-text">Nome completo</b>
                <input type="text" name="aluno_nome" value="" maxlength="255" required>
              </label>
              <label class="ls-label col-md-6">
                <b class="ls-label-text">Nome Social <a href="#" class="ls-ico-help" data-ls-popover="open"
                    data-trigger="hover" data-ls-module="popover" data-placement="top"
                    data-content="O Nome Social é o nome pelo qual pessoas transgêneras, travestis ou não binárias preferem ser chamadas, refletindo sua identidade de gênero. Use este campo apenas se o aluno optar por um nome diferente do seu nome de registro."
                    data-title="O que é Nome Social?"></a></b>
                <label class="ls-label-text">
                  <input type="checkbox" name="usar_nome_social" id="usar_nome_social" onchange="toggleNomeSocial()">
                  Usar Nome Social
                </label>
                <input type="text" name="aluno_nome_social" id="nome_social_input" value="" maxlength="255"
                  style="margin-top: 5px;" disabled>
              </label>
            </div>
            <div class="row">
              <label class="ls-label col-md-3">
                <b class="ls-label-text">Data de nascimento</b>
                <input type="text" name="aluno_nascimento" value="" class="date" maxlength="10" required>
              </label>
              <label class="ls-label col-md-3">
                <b class="ls-label-text">UF de nascimento</b>
                <div class="ls-custom-select">
                  <select name="aluno_uf_nascimento" id="aluno_uf_nascimento" class="ls-select" required>
                    <option value="">Escolha...</option>
                    <?php
                    $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                    foreach ($ufs as $uf) {
                      echo "<option value='$uf'>$uf</option>";
                    }
                    ?>
                  </select>
                </div>
              </label>
              <label class="ls-label col-md-6">
                <b class="ls-label-text">Município de nascimento</b>
                <div class="ls-custom-select">
                  <select name="aluno_municipio_nascimento_ibge" id="aluno_municipio_nascimento_ibge" class="ls-select"
                    required>
                    <option value="">Selecione um estado primeiro</option>
                  </select>
                </div>
              </label>
            </div>
            <div class="row">
              <label class="ls-label col-md-3">
                <b class="ls-label-text">Sexo</b>
                <div class="ls-custom-select">
                  <select name="aluno_sexo" class="ls-select" required>
                    <option value="">Escolha...</option>
                    <option value="1">Masculino</option>
                    <option value="2">Feminino</option>
                  </select>
                </div>
              </label>
              <label class="ls-label col-md-3">
                <b class="ls-label-text">Cor</b>
                <div class="ls-custom-select">
                  <select name="aluno_raca" class="ls-select" required>
                    <option value="">Escolha...</option>
                    <option value="1">Branca</option>
                    <option value="2">Preta</option>
                    <option value="3">Parda</option>
                    <option value="4">Amarela</option>
                    <option value="5">Indígena</option>
                    <option value="6">Não declarada</option>
                  </select>
                </div>
              </label>
              <label class="ls-label col-md-3">
                <b class="ls-label-text">Nacionalidade</b>
                <div class="ls-custom-select">
                  <select name="aluno_nacionalidade" id="aluno_nacionalidade">
                    <option value="1" selected>Brasileira</option>
                    <option value="2">Brasileira - Nascido no exterior ou naturalizado</option>
                    <option value="3">Estrangeiro</option>
                  </select>
                </div>
              </label>
              <label class="ls-label col-md-3">
                <b class="ls-label-text">País</b>
                <div class="ls-custom-select">
                  <select name="aluno_pais" id="aluno_pais" required>
                    <option value="76" selected>BRASIL</option>
                    <?php
                    mysql_data_seek($paises, 0);
                    while ($row_paises = mysql_fetch_assoc($paises)) {
                      if ($row_paises['pais_cod'] != 76) {
                        echo "<option value='{$row_paises['pais_cod']}'>{$row_paises['pais_nome']}</option>";
                      }
                    }
                    ?>
                  </select>
                </div>
              </label>
            </div>
          </div>

          <div id="filia" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ls-ico-users">FILIAÇÃO</p>
            <label class="ls-label col-md-4">
              <b class="ls-label-text">*Filiação 1:</b>
              <input type="text" name="aluno_filiacao1" maxlength="255" required>
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Profissão da mãe:</b>
              <input type="text" name="aluno_prof_mae" maxlength="64">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Telefone da mãe:</b>
              <input type="text" name="aluno_tel_mae" class="celular" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Escolaridade da mãe:</b>
              <div class="ls-custom-select">
                <select name="aluno_escolaridade_mae">
                  <option value="">Escolha...</option>
                  <option value="1">NÃO ESTUDOU</option>
                  <option value="5">ENSINO FUNDAMENTAL INCOMPLETO</option>
                  <option value="2">ENSINO FUNDAMENTAL COMPLETO</option>
                  <option value="6">ENSINO MÉDIO INCOMPLETO</option>
                  <option value="3">ENSINO MÉDIO COMPLETO</option>
                  <option value="7">ENSINO SUPERIOR INCOMPLETO</option>
                  <option value="4">ENSINO SUPERIOR COMPLETO</option>
                </select>
              </div>
            </label>
            <hr>
            <hr><br><br>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">RG da mãe:</b>
              <input type="text" name="aluno_rg_mae" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">CPF da mãe:</b>
              <input type="text" name="aluno_cpf_mae" maxlength="20" onblur="javascript: validarCPF(this);"
                onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">NIS da mãe:</b>
              <input type="text" name="aluno_nis_mae" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">SUS da mãe:</b>
              <input type="text" name="aluno_sus_mae" maxlength="32">
            </label>
            <label class="ls-label col-md-4">
              <b class="ls-label-text">Filiação 2:</b>
              <input type="text" name="aluno_filiacao2" maxlength="255">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Profissão do pai:</b>
              <input type="text" name="aluno_prof_pai" maxlength="64">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Telefone do pai:</b>
              <input type="text" name="aluno_tel_pai" class="celular" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Escolaridade do pai:</b>
              <div class="ls-custom-select">
                <select name="aluno_escolaridade_pai">
                  <option value="">Escolha...</option>
                  <option value="1">NÃO ESTUDOU</option>
                  <option value="5">ENSINO FUNDAMENTAL INCOMPLETO</option>
                  <option value="2">ENSINO FUNDAMENTAL COMPLETO</option>
                  <option value="6">ENSINO MÉDIO INCOMPLETO</option>
                  <option value="3">ENSINO MÉDIO COMPLETO</option>
                  <option value="7">ENSINO SUPERIOR INCOMPLETO</option>
                  <option value="4">ENSINO SUPERIOR COMPLETO</option>
                </select>
              </div>
            </label>
            <hr><br>
            <hr>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">RG do pai:</b>
              <input type="text" name="aluno_rg_pai" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">CPF do pai:</b>
              <input type="text" name="aluno_cpf_pai" maxlength="32" onblur="javascript: validarCPF(this);"
                onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">NIS do pai:</b>
              <input type="text" name="aluno_nis_pai" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">SUS do pai:</b>
              <input type="text" name="aluno_sus_pai" maxlength="32">
            </label>
            <label class="ls-label col-md-12">
              <small>*Preferencialmente nome da mãe no campo Filiação 1</small>
            </label>
          </div>

          <div id="responsavel" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-users">RESPONSÁVEL LEGAL</p>
            <div class="row">
              <label class="ls-label col-md-5">
                <b class="ls-label-text">Responsável legal (caso não seja criado por pai/mãe):</b>
                <input type="text" name="aluno_nome_responsavel_legal" maxlength="255">
              </label>
              <label class="ls-label col-md-3">
                <b class="ls-label-text">CPF do responsável legal:</b>
                <input type="text" name="aluno_cpf_responsavel_legal" maxlength="32"
                  onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);" class="cpf">
              </label>
              <label class="ls-label col-md-4">
                <b class="ls-label-text">Grau de parentesco do responsável legal:</b>
                <div class="ls-custom-select">
                  <select name="aluno_grau_responsavel_legal">
                    <option value="">Escolha...</option>
                    <option value="1">IRMÃO/IRMÃ</option>
                    <option value="2">TIO/TIA</option>
                    <option value="3">AVÔ/AVÓ</option>
                    <option value="4">OUTRO</option>
                  </select>
                </div>
              </label>
            </div>
            <div class="row">
              <label class="ls-label col-md-4">
                <b class="ls-label-text">RG do responsável legal:</b>
                <input type="text" name="aluno_rg_responsavel_legal" maxlength="32">
              </label>
              <label class="ls-label col-md-4">
                <b class="ls-label-text">NIS do responsável legal:</b>
                <input type="text" name="aluno_nis_responsavel_legal" maxlength="15">
              </label>
              <label class="ls-label col-md-4">
                <b class="ls-label-text">SUS do responsável legal:</b>
                <input type="text" name="aluno_sus_responsavel_legal" maxlength="32">
              </label>
            </div>
          </div>

          <div id="docum" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-text">DOCUMENTAÇÃO</p>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Número do CPF</b>
              <input type="text" name="aluno_cpf" class="cpf" value="" maxlength="32"
                onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);">
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">NIS</b>
              <input type="text" name="aluno_nis" class="nis" value="" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Cartão do SUS</b>
              <input type="text" name="aluno_sus" class="sus" value="" maxlength="32">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Número da identidade</b>
              <input type="text" name="aluno_identidade" value="" maxlength="16">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Órgão emissor</b>
              <input type="text" name="aluno_emissor" value="" maxlength="16">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">UF emissor</b>
              <div class="ls-custom-select">
                <select name="aluno_uf_emissor">
                  <option value="">ESCOLHA...</option>
                  <?php
                  $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                  foreach ($ufs as $uf) {
                    echo "<option value='$uf'>$uf</option>";
                  }
                  ?>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Data de expedição</b>
              <input type="text" name="aluno_data_espedicao" value="" class="date">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Modelo de certidão</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_tipo_certidao" value="1"
                  onclick="javascript:modeloantigo_certidao();" />
                Antigo
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_tipo_certidao" value="2" onclick="javascript:modelonovo_certidao();" />
                Novo
              </label>
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Termos</b>
              <input type="text" name="aluno_termo" id="aluno_termo" value="" maxlength="8">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Folhas</b>
              <input type="text" name="aluno_folhas" id="aluno_folhas" value="" maxlength="6">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Livro</b>
              <input type="text" name="aluno_livro" id="aluno_livro" value="" maxlength="6">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Emissão</b>
              <input type="text" name="aluno_emissao_certidao" id="aluno_emissao_certidao" value="" class="date">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Cartório</b>
              <div class="ls-custom-select">
                <select name="aluno_uf_cartorio" id="aluno_uf_cartorio">
                  <option value="">ESCOLHA...</option>
                  <?php
                  $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                  foreach ($ufs as $uf) {
                    echo "<option value='$uf'>$uf</option>";
                  }
                  ?>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-4">
              <b class="ls-label-text">Município</b>
              <input type="text" name="aluno_mucicipio_cartorio" id="aluno_mucicipio_cartorio" value="" maxlength="64">
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Nome de Cartório</b>
              <input type="text" name="aluno_nome_cartorio" id="aluno_nome_cartorio" value="" maxlength="255">
            </label>
            <label class="ls-label col-md-12">
              <b class="ls-label-text">Nº da matrícula modelo novo</b>
              <input type="text" name="aluno_num_matricula_modelo_novo" id="aluno_num_matricula_modelo_novo" value=""
                maxlength="255" class="certidao">
            </label>
          </div>

          <div id="local" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-target">LOCALIZAÇÃO</p>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Localização</b>
              <div class="ls-custom-select">
                <select name="aluno_localizacao" id="aluno_localizacao" required>
                  <option value="">-</option>
                  <option value="1">Urbana</option>
                  <option value="2">Rural</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">CEP</b>
              <input type="text" name="aluno_cep" id="aluno_cep" value="" maxlength="10" class="cep">
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Endereço</b>
              <input type="text" name="aluno_endereco" id="aluno_endereco" value="" maxlength="255">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Número</b>
              <input type="text" name="aluno_numero" id="aluno_numero" value="" maxlength="8">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Complemento</b>
              <input type="text" name="aluno_complemento" id="aluno_complemento" value="" maxlength="255">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Bairro</b>
              <input type="text" name="aluno_bairro" id="aluno_bairro" value="" maxlength="255">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">UF</b>
              <div class="ls-custom-select">
                <select name="aluno_uf" id="aluno_uf">
                  <option value="">ESCOLHA...</option>
                  <?php
                  $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                  foreach ($ufs as $uf) {
                    echo "<option value='$uf'>$uf</option>";
                  }
                  ?>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-4">
              <b class="ls-label-text">Município</b>
              <input type="text" name="aluno_municipio" id="aluno_municipio" value="" maxlength="64">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Telefone/Celular</b>
              <input type="text" name="aluno_telefone" value="" maxlength="255" class="celular">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Celular/WhatsApp</b>
              <input type="text" name="aluno_celular" value="" maxlength="255" class="celular">
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">E-mail</b>
              <input type="text" name="aluno_email" value="" maxlength="255">
            </label>
          </div>

          <div id="medico" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-book">DADOS MÉDICOS</p>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Aluno com alguma deficiência?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_aluno_com_deficiencia" value="1"
                  onclick="javascript:habilita_deficiencia();" />
                Sim
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_aluno_com_deficiencia" value="2"
                  onclick="javascript:desabilita_deficiencia();" checked />
                Não
              </label>
            </label>
            <label class="ls-label col-md-7">
              <b class="ls-label-text">Descreva a deficiência:</b>
              <input type="text" name="aluno_tipo_deficiencia" value="" maxlength="255" id="aluno_tipo_deficiencia">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Possui Laudo Médico?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_laudo" value="1" />
                Sim
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_laudo" value="2" checked />
                Não
              </label>
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Possui alguma alergia/intolerância?</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_alergia" value="1" onclick="javascript:habilita_alergia();" />
                Sim
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_alergia" value="2" onclick="javascript:desabilita_alergia();" checked />
                Não
              </label>
            </label>
            <label class="ls-label col-md-7">
              <b class="ls-label-text">Descreva a alergia/intolerância:</b>
              <input type="text" name="aluno_alergia_qual" value="" maxlength="64" id="aluno_alergia_qual">
            </label>
            <label class="ls-label col-md-2">
              <b class="ls-label-text">Destro/Canhoto</b><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_destro" value="1" />
                Destro
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_destro" value="2" />
                Canhoto
              </label>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Tipo sanguíneo</b><br>
              <div class="ls-custom-select">
                <select name="aluno_sangue_tipo">
                  <option value="">-</option>
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="AB">AB</option>
                  <option value="O">O</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Fator RH</b><br>
              <div class="ls-custom-select">
                <select name="aluno_sangue_rh">
                  <option value="">-</option>
                  <option value="+">POSITIVO (+)</option>
                  <option value="-">NEGATIVO (-)</option>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">Em caso de emergência, avisar a:</b>
              <input type="text" name="aluno_emergencia_avisar" value="" maxlength="64">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Telefone 1:</b>
              <input type="text" name="aluno_emergencia_tel1" value="" maxlength="32" class="celular">
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">Telefone 2:</b>
              <input type="text" name="aluno_emergencia_tel2" value="" maxlength="32" class="celular">
            </label>
          </div>

          <div id="deficiencia" class="ls-box ls-box-gray" style="display:none">
            <p class="ls-title-4 col-md-12 ls-ico-book">DEFICIÊNCIA, TEA OU HABILIDADES ESPECIAIS</p>
            <div class="ls-label col-md-12">
              <p>Deficiência:</p>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_bvisao" value="" /> Baixa
                visão</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_cegueira" value="" /> Cegueira</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_auditiva" value="" /> Deficiência
                auditiva</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_fisica" value="" /> Deficiência
                física</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_intelectual" value="" /> Deficiência
                intelectual</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_surdez" value="" /> Surdez</label>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_surdocegueira" value="" />
                Surdocegueira</label>
            </div>
            <div class="ls-label col-md-12">
              <p>Transtorno do espectro autista:</p>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_autista" value="" /> Transtorno do
                espectro autista</label>
            </div>
            <div class="ls-label col-md-12">
              <p>Altas habilidades/superdotação:</p>
              <label class="ls-label-text"><input type="checkbox" name="aluno_def_superdotacao" value="" /> Altas
                habilidades/superdotação</label>
            </div>
            <label class="ls-label col-md-7">
              <b class="ls-label-text">Informe o CID:</b>
              <input type="text" name="aluno_cid" id="aluno_cid" value="" maxlength="255">
            </label>
          </div>

          <div id="social" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-info">DADOS SOCIAIS</p>
            <label class="ls-label col-sm-12">
              <b class="ls-label-text">Aluno é beneficiário do Bolsa Família?</b><br><br>
              <label class="ls-label-text">
                <input type="radio" name="aluno_recebe_bolsa_familia" value="1" />
                Sim
              </label>
              <label class="ls-label-text">
                <input type="radio" name="aluno_recebe_bolsa_familia" value="0" />
                Não
              </label>
            </label>
          </div>

          <div id="obs" class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-pencil">OBSERVAÇÃO</p>
            <label class="ls-label">
              <b class="ls-label-text">Aparecerá na ficha individual do aluno</b>
              <textarea rows="4" name="aluno_observacao" class="ls-textarea-autoresize" maxlength="255"></textarea>
            </label>
          </div>

          <div class="ls-box ls-box-gray">
            <p class="ls-title-4 col-md-12 ls-ico-info">ENVIAR DOCUMENTOS</p>
            <label class="ls-label">
              <b class="ls-label-text">Selecione os documentos para envio: RG, CPF...</b>
              <input type="file" id="documentos" name="aluno_documento[]" multiple>
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
  <?php include_once("menu-dir.php"); ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../js/jquery.mask.js"></script>
  <script src="js/mascara.js"></script>
  <script src="js/validarCPF.js"></script>
  <script src="js/maiuscula.js"></script>
  <script src="js/semAcentos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    // Ativar FilePond nos inputs de arquivo
    FilePond.registerPlugin(FilePondPluginFileValidateType);
    const pond = FilePond.create(document.querySelector("#documentos"), {
      acceptedFileTypes: ['image/png', 'image/jpeg', 'application/pdf'],
      labelIdle: 'Arraste e solte os arquivos ou <span class="filepond--label-action">clique aqui</span>',
      allowMultiple: true
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var nascimentoInput = document.querySelector('input[name="aluno_nascimento"]');

      nascimentoInput.addEventListener('input', function () {
        var data = nascimentoInput.value;

        // Verifique se a data é "00/00/0000"
        if (data === '00/00/0000') {
          alert('Data de nascimento inválida. Por favor, insira uma data válida.');
          nascimentoInput.value = ''; // Limpa o campo
          nascimentoInput.focus();    // Coloca o foco no campo
        }
      });
    });
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
    function habilita_deficiencia() {
      document.getElementById("aluno_tipo_deficiencia").disabled = false; //Habilitando
      document.getElementById("deficiencia").style.display = "block"; //Habilitando
    }
    function desabilita_deficiencia() {
      document.getElementById("aluno_tipo_deficiencia").disabled = true; //Desabilitando
      document.getElementById("deficiencia").style.display = "none"; //Desabilitando
    }
  </script>
  <script type="text/javascript">
    function habilita_alergia() {
      document.getElementById("aluno_alergia_qual").disabled = false; //Habilitando
    }
    function desabilita_alergia() {
      document.getElementById("aluno_alergia_qual").disabled = true; //Desabilitando
    }
  </script>
  <script type="text/javascript">
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
  <script type="text/javascript">
    $(document).ready(function () {
      $('#aluno_uf_nascimento').change(function () {
        const estado = $('#aluno_uf_nascimento').val();

        // Verifica o valor selecionado
        if (estado) {
          $.get('cidades.php?estado=' + estado, function (data) {
            console.log('Cidades carregadas:', data); // Depuração
            $('#aluno_municipio_nascimento_ibge').html(data);
          }).fail(function () {
            console.error('Erro ao carregar cidades.');
          });
        } else {
          // Caso nenhum estado seja selecionado, limpa o select
          $('#aluno_municipio_nascimento_ibge').html('<option value="">Selecione um estado primeiro</option>');
        }
      });

      // Inicialize o select2 se necessário
      //$('#aluno_municipio_nascimento_ibge').select2();

      $('#aluno_municipio_nascimento_ibge').select2({
        //placeholder: "Escolha a UF primeiro",
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

mysql_free_result($paises);

mysql_free_result($EscolaLogada);
?>