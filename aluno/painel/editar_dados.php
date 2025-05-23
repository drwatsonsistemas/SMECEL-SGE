<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../funcoes/anti_injection.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../index.php?saiu";
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
$MM_authorizedUsers = "6";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
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

$MM_restrictGoTo = "../index.php?err";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  if($_POST['aluno_perfil_ativo'] == 'on'){
    $perfil_ativo = "S";
  }else{
    $perfil_ativo = "N";
  }
  $updateSQL = sprintf(
    "UPDATE smc_aluno SET aluno_endereco=%s, aluno_numero=%s, aluno_complemento=%s, aluno_bairro=%s, aluno_telefone=%s, aluno_celular=%s, aluno_email=%s, aluno_emergencia_tel1=%s, aluno_emergencia_tel2=%s, aluno_tel_mae=%s, aluno_tel_pai=%s, aluno_perfil_ativo=%s WHERE aluno_id=%s",
    GetSQLValueString(anti_injection($_POST['aluno_endereco']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_numero']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_complemento']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_bairro']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_telefone']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_celular']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_email']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_emergencia_tel1']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_emergencia_tel2']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_tel_mae']), "text"),
    GetSQLValueString(anti_injection($_POST['aluno_tel_pai']), "text"),
    GetSQLValueString(anti_injection($perfil_ativo), "text"),
    GetSQLValueString($_POST['aluno_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "index.php?salvo";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, 
aluno_filiacao1, aluno_filiacao2, 
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo, 
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDÍGENA'
WHEN 6 THEN 'NÃO DECLARADA'
END AS aluno_raca, 
CASE aluno_nacionalidade
WHEN 1 THEN 'BRASILEIRA'
WHEN 2 THEN 'BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO'
WHEN 3 THEN 'EXTRANGEIRO'
END AS aluno_nacionalidade, 
aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge,  
CASE aluno_aluno_com_deficiencia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_aluno_com_deficiencia, 
aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, 
CASE aluno_tipo_certidao
WHEN 1 THEN 'MODELO ANTIGO'
WHEN 2 THEN 'MODELO NOVO'
END AS aluno_tipo_certidao, 
aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, 
aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao, 
aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, 
aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, 
CASE aluno_laudo
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_laudo, 
CASE aluno_alergia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_alergia, 
aluno_alergia_qual, 
CASE aluno_destro
WHEN 1 THEN 'DESTRO'
WHEN 2 THEN 'CANHOTO'
END AS aluno_destro, 
aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
aluno_prof_mae, aluno_tel_mae, 
CASE aluno_escolaridade_mae
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_mae, 
aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, 
CASE aluno_escolaridade_pai
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_pai, 
aluno_rg_pai, aluno_cpf_pai, aluno_hash, 
CASE aluno_recebe_bolsa_familia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_recebe_bolsa_familia,
aluno_foto,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf 
FROM smc_aluno
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if ($totalRows_AlunoLogado == "") {
  header("Location:../index.php?loginErr");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno FROM smc_vinculo_aluno WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Editar = "SELECT aluno_id, aluno_usu_tipo, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_pais, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_destro, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, aluno_recebe_bolsa_familia, aluno_foto, aluno_def_bvisao, aluno_def_cegueira, aluno_def_auditiva, aluno_def_fisica, aluno_def_intelectual, aluno_def_surdez, aluno_def_surdocegueira, aluno_def_autista, aluno_def_superdotacao, aluno_sangue_tipo, aluno_sangue_rh, aluno_perfil_ativo FROM smc_aluno WHERE aluno_id = '$row_AlunoLogado[aluno_id]'";
$Editar = mysql_query($query_Editar, $SmecelNovo) or die(mysql_error());
$row_Editar = mysql_fetch_assoc($Editar);
$totalRows_Editar = mysql_num_rows($Editar);


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e
    Lazer</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="../css/app.css" media="screen,projection" />
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }

    th,
    td {
      border: 0px solid #ccc;
    }

    th,
    td {
      padding: 5px;
      height: 15px;
      line-height: 15px;
    }
  </style>


</head>

<body class="indigo lighten-5">

  <?php include "menu_top.php" ?>

  <div class="container">
    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m2 hide-on-small-only">
        <p>
          <?php if ($row_AlunoLogado['aluno_foto'] == "") { ?>
            <img src="<?php echo URL_BASE . 'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
          <?php } else { ?>
            <img src="<?php echo URL_BASE . 'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%"
              class="hoverable">
          <?php } ?>
          <br>
          <small style="font-size:14px;">
            <?php echo current(str_word_count($row_AlunoLogado['aluno_nome'], 2)); ?>
            <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome']));
            echo $word[count($word) - 1]; ?>
          </small>
        </p>

        <?php include "menu_esq.php"; ?>

      </div>

      <div class="col s12 m10">

        <h5><strong>Dados</strong></h5>
        <hr>
        <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i
            class="material-icons left">arrow_back</i>voltar</a>

        <div class="row">
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="col s12">

            <div class="row">
              <div class="input-field col s9">
                <b>Endereço</b>
                <input type="text" name="aluno_endereco"
                  value="<?php echo htmlentities($row_Editar['aluno_endereco'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>

              <div class="input-field col s3">
                <b>Nº</b>
                <input type="text" name="aluno_numero"
                  value="<?php echo htmlentities($row_Editar['aluno_numero'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Complemento</b>
                <input type="text" name="aluno_complemento"
                  value="<?php echo htmlentities($row_Editar['aluno_complemento'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Bairro</b>
                <input type="text" name="aluno_bairro"
                  value="<?php echo htmlentities($row_Editar['aluno_bairro'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Telefone</b>
                <input type="text" name="aluno_telefone" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_telefone'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Celular</b>
                <input type="text" name="aluno_celular" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_celular'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>E-mail</b>
                <input type="text" name="aluno_email"
                  value="<?php echo htmlentities($row_Editar['aluno_email'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Tel emergência 1</b>
                <input type="text" name="aluno_emergencia_tel1" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_emergencia_tel1'], ENT_COMPAT, 'utf-8'); ?>"
                  size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Tel emergência 2</b>
                <input type="text" name="aluno_emergencia_tel2" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_emergencia_tel2'], ENT_COMPAT, 'utf-8'); ?>"
                  size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Telefone (mãe)</b>
                <input type="text" name="aluno_tel_mae" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_tel_mae'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>Telefone (pai)</b>
                <input type="text" name="aluno_tel_pai" class="celular"
                  value="<?php echo htmlentities($row_Editar['aluno_tel_pai'], ENT_COMPAT, 'utf-8'); ?>" size="32">
              </div>
            </div>

            <div class="row">
              <div class="input-field col s12">
                <b>
                Ao desmarcar esta opção, seus colegas não poderão visualizar nem interagir com seu perfil no EduConnect até que você a habilite novamente.                </b>
                <p>
                  <label>
                    <input type="checkbox" name="aluno_perfil_ativo" <?php if($row_Editar['aluno_perfil_ativo'] == 'S'){echo 'checked';}  ?>/>
                    <span>Perfil ativo no EduConnect</span>
                  </label>
                </p>
              </div>
            </div>

            <input type="submit" value="SALVAR" class="btn-small">

            <input type="hidden" name="MM_update" value="form1">
            <input type="hidden" name="aluno_id" value="<?php echo $row_Editar['aluno_id']; ?>">

          </form>
        </div>
        <p>&nbsp;</p>
      </div>





    </div>
  </div>

  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="../js/materialize.min.js"></script>

  <script src="../../js/jquery.mask.js"></script>

  <script src="../../js/mascara.js"></script>



  <script type="text/javascript">
    $(document).ready(function () {
      $('.sidenav').sidenav();
      $('.tabs').tabs();
      $('.dropdown-trigger').dropdown();
    });
  </script>
</body>

</html>
<?php
mysql_free_result($AlunoLogado);

mysql_free_result($Matricula);

mysql_free_result($Editar);
?>