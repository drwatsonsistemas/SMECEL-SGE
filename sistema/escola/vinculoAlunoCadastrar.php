<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
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


if ((isset($_GET["periodo"])) && ($_GET["periodo"] == "rematricula")) {

  $anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano'] + 1;

  $link = "turmaCadastrarRematricula.php?nova";
  $linkNome = "REMATRICULAR";
  $tituloRematricula = "<strong class='ls-color-warning'>Rematricular no Ano Letivo " . $anoLetivoRematricula . "</strong>";

} else {

  $anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano'];

  $link = "turmaCadastrar.php";
  $linkNome = "MATRICULAR";
  $tituloRematricula = "<strong class=''>Matricular no Ano Letivo " . $anoLetivoRematricula . "</strong>";

}





if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
    die;
  }

  $hash = md5($_POST['cHash'] . time());

  function generateRandomString($size = 4)
  {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $randomString = '';
    for ($i = 0; $i < $size; $i = $i + 1) {
      $randomString .= $chars[mt_rand(0, 31)];
    }
    return $randomString;
  }
  function generateRandomString1($size = 4)
  {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $randomString = '';
    for ($i = 0; $i < $size; $i = $i + 1) {
      $randomString .= $chars[mt_rand(0, 31)];
    }
    return $randomString;
  }
  function generateRandomString2($size = 4)
  {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $randomString = '';
    for ($i = 0; $i < $size; $i = $i + 1) {
      $randomString .= $chars[mt_rand(0, 31)];
    }
    return $randomString;
  }
  function generateRandomString3($size = 4)
  {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $randomString = '';
    for ($i = 0; $i < $size; $i = $i + 1) {
      $randomString .= $chars[mt_rand(0, 31)];
    }
    return $randomString;
  }

  $codVerificacao = generateRandomString() . '-' . generateRandomString1() . '-' . generateRandomString2() . '-' . generateRandomString3();

  $ct = $_POST['vinculo_aluno_id_turma'];


  $vinculo_aluno_nao_reprova = (isset($_POST['vinculo_aluno_nao_reprova']) && $_POST['vinculo_aluno_nao_reprova'] == "on") ? "S" : "N";

  $vinculo_aluno_saida = isset($_POST['vinculo_aluno_saida']) ? (int) $_POST['vinculo_aluno_saida'] : 0;
  if (!in_array($vinculo_aluno_saida, [0, 1, 2])) {
    $vinculo_aluno_saida = 0;
  }

  $insertSQL = sprintf(
    "INSERT INTO smc_vinculo_aluno (vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_data, vinculo_aluno_vacina_data_retorno, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_multietapa, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_internet, vinculo_aluno_repetente, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_id_cuidador, vinculo_aluno_id_matriz_multi, vinculo_aluno_nao_reprova, vinculo_aluno_saida) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$hash', '$codVerificacao',%s, %s, %s, %s)",
    GetSQLValueString($_POST['vinculo_aluno_id_aluno'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_id_turma'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_id_escola'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_id_sec'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_ano_letivo'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_data'], "date"),
    GetSQLValueString($_POST['vinculo_aluno_vacina_data_retorno'], "date"),
    GetSQLValueString($_POST['vinculo_aluno_transporte'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_ponto_id'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_multietapa'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_da_casa'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_historico_transferencia'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_vacina_atualizada'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_internet'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_repetente'], "text"),
    GetSQLValueString($_POST['vinculo_aluno_id_cuidador'], "int"),
    GetSQLValueString($_POST['vinculo_aluno_matriz_multietapa'], "int"),
    GetSQLValueString($vinculo_aluno_nao_reprova, "text"),
    GetSQLValueString($vinculo_aluno_saida, "int")
  );


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());



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
'9', 
'($detalhes)', 
'$dat')
";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());



  $insertGoTo = "matriculaExibe.php?cadastrado&cmatricula=$hash";
  //$insertGoTo = "vinculoAlunoExibirTurma.php?cadastrado&ct=$ct";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


include "fnc/alunosConta.php";

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

$colname_Aluno = "-1";
if (isset($_GET['c'])) {
  $colname_Aluno = $_GET['c'];
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aluno = sprintf("SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_hash FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($colname_Aluno, "text"));
$Aluno = mysql_query($query_Aluno, $SmecelNovo) or die(mysql_error());
$row_Aluno = mysql_fetch_assoc($Aluno);
$totalRows_Aluno = mysql_num_rows($Aluno);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Pontos = "SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, te_ponto_latitude, te_ponto_longitude, te_ponto_obs FROM smc_te_ponto WHERE te_ponto_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY te_ponto_descricao ASC";
$Pontos = mysql_query($query_Pontos, $SmecelNovo) or die(mysql_error());
$row_Pontos = mysql_fetch_assoc($Pontos);
$totalRows_Pontos = mysql_num_rows($Pontos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev FROM smc_etapa";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("SELECT matriz_id, matriz_id_secretaria, matriz_nome, matriz_ativa FROM smc_matriz WHERE matriz_ativa = 'S' AND matriz_id_secretaria = %s", GetSQLValueString($row_EscolaLogada['sec_id'], "int"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

if ($totalRows_Aluno == "") {
  //echo "TURMA EM BRANCO";	
  header("Location: turmasAlunosVinculados.php?nada");
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosAnteriores = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_datatransferencia, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim,vinculo_aluno_id_cuidador,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DEIXOU DE FREQUENTAR'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, turma_id, turma_nome,turma_total_alunos, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTNO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno, 
escola_id, escola_nome, escola_id_sec, escola_telefone1, escola_telefone2, escola_email, 
sec_id, sec_cidade, sec_uf
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno
ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma
ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola
ON escola_id = vinculo_aluno_id_escola 
INNER JOIN smc_sec ON sec_id = escola_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Aluno[aluno_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$VinculosAnteriores = mysql_query($query_VinculosAnteriores, $SmecelNovo) or die(mysql_error());
$row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores);
$totalRows_VinculosAnteriores = mysql_num_rows($VinculosAnteriores);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
CASE turma_turno
WHEN 0 THEN 'INT'
WHEN 1 THEN 'MAT'
WHEN 2 THEN 'VESP'
WHEN 3 THEN 'NOT'
END AS turma_turno_nome
FROM smc_turma WHERE turma_ano_letivo = '$anoLetivoRematricula' AND turma_id_escola = '$row_EscolaLogada[escola_id]'
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

if ($totalRows_Turmas == "") {
  //echo "TURMA EM BRANCO";	
  header("Location: " . $link);
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaVinculos = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND vinculo_status = 1
ORDER BY func_nome ASC
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);
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
  <link rel="stylesheet" href="../../css/foundation-datepicker.css">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    .select2-container .select2-selection {
      border: 0px solid #ddd;
      /* Adicione bordas similares ao LWS */
      border-radius: 4px;
      height: 45px;
      padding: 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 28px;
      /* Alinhar texto verticalmente */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 45px;
    }
  </style>

</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home"><?php echo $tituloRematricula; ?></h1>
      <!-- CONTEÚDO -->

      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row"
        data-ls-module="form" autocomplete="off">
        <fieldset>
          <div class="ls-box">
            <label class="ls-label col-md-12">
              <div class="ls-alert-info"><strong>Aluno(a):</strong> <?php echo $row_Aluno['aluno_nome'] ?></div>
            </label>
            <label class="ls-label col-md-3"><b class="ls-label-text">DATA DA MATRÍCULA</b>
              <input type="date" placeholder="INFORME A DATA" name="vinculo_aluno_data" id="data_matricula1"
                value="<?php echo date("Y-m-d"); ?>" class="ls-field-lg" required>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">TURMA | TURNO | Nº MATRÍCULAS</b>
              <div class="ls-custom-select ls-field-lg lista-turmas">
                <select name="vinculo_aluno_id_turma" class="ls-select" id="lista-turmas" required>
                  <option value="" <?php if (!(strcmp(-1, ""))) {
                    echo "SELECTED";
                  } ?>>-</option>
                  <?php do { ?>
                    <option value="<?php echo $row_Turmas['turma_id'] ?>">
                      <div>

                        <?php echo $row_Turmas['turma_turno_nome'] ?> |
                        <?php echo $row_Turmas['turma_nome'] ?> |
                        <?php
                        $alunosTurma = alunosConta($row_Turmas['turma_id'], $anoLetivoRematricula);
                        echo $alunosTurma . "/" . $row_Turmas['turma_total_alunos'];
                        ?>

                      </div>
                    </option>
                  <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">TRANSPORTE ESCOLAR</b><a href="#" class="ls-ico-help" data-trigger="hover"
                data-ls-module="popover" data-placement="left"
                data-content="Informar apenas o aluno que mora na Zona Rural." data-title="Atenção"></a> <br>
              <br>
              <p>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" value="N" onclick="javascript:transporte_nao();"
                    checked />
                  NÃO UTILIZA</label>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" value="S"
                    onclick="javascript:transporte_sim();" />
                  UTILIZA </label>
              </p>
            </label>
          </div>
          <label class="ls-label col-md-12 ls-box" style="display:none" id="ponto">
            <b class="ls-label-text">PONTO</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_ponto_id" class="ls-select">
                <option value="" <?php if (!(strcmp(-1, ""))) {
                  echo "SELECTED";
                } ?>>-</option>
                <?php do { ?>
                  <option value="<?php echo $row_Pontos['te_ponto_id'] ?>"><?php echo $row_Pontos['te_ponto_descricao'] ?>
                    <?php echo $row_Pontos['te_ponto_endereco'] ?>   <?php echo $row_Pontos['te_ponto_num'] ?>
                    <?php echo $row_Pontos['te_ponto_bairro'] ?>   <?php echo $row_Pontos['te_ponto_obs'] ?>
                  </option>
                <?php } while ($row_Pontos = mysql_fetch_assoc($Pontos)); ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">Aluno repetente nessa etapa de ensino? </b> <a href="#" class="ls-ico-help"
              data-trigger="hover" data-ls-module="popover" data-placement="right"
              data-content="Informar se o aluno repetiu esse mesmo ano/série." data-title="Atenção"></a> <br> <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_repetente" value="S" />
                SIM </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_repetente" value="N" checked />
                NÃO </label>
            </p>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">Aluno(a) possui acesso à internet?</b> <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_internet" value="S" checked />
                SIM </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_internet" value="N" />
                NÃO </label>
            </p>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">ALUNO MATRICULADO É DA ESCOLA (DA CASA) OU DE OUTRA ESCOLA/CIDADE (DE FORA)</b> <a
              href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Informar se o aluno para a matrícula é de casa (já estuda na escola) ou de fora (vindo de outra escola no município ou fora dele."
              data-title="Atenção"></a> <br>
            <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_da_casa" value="C" onclick="javascript:da_casa();" checked />
                DA CASA </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_da_casa" value="F" onclick="javascript:de_fora();" />
                DE FORA </label>
            </p>
          </label>
          <label class="ls-label col-sm-12 ls-box" style="display:none" id="historico">
            <b class="ls-label-text">TRANSFERIDO COM HISTÓRICO OU DECLARAÇÃO</b><a href="#" class="ls-ico-help"
              data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Se o aluno foi trasferido com Declaração, terá 30 dias a contar da data da matrícula para entregar o Histórico"
              data-title="Atenção"></a> <br>
            <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_historico_transferencia" value="H"
                  id="vinculo_aluno_historico_transferencia_h" />
                HISTÓRICO </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_historico_transferencia" value="D"
                  id="vinculo_aluno_historico_transferencia_d" />
                DECLARAÇÃO </label>
            </p>
          </label>
          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">CARTEIRA DE VACINAÇÃO DO ALUNO ESTÁ ATUALIZADA? <a href="#" class="ls-ico-help"
                data-trigger="hover" data-ls-module="popover" data-placement="left"
                data-content="Ao marcar SIM, você DECLARA que o aluno ou responsável pelo aluno apresentou o documento que comprove que a Carteira de Vacinação está em dia."
                data-title="Atenção"></a></b> <br>
            <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="S" onclick="javascript:aceite();"
                  required />
                SIM </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="N" />
                NÃO </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="I" />
                SEM INFORMAÇÃO </label>
            </p>
            <p>
            <div class="ls-alert-warning ls-dismissable" style="display:none" id="aviso_aceite"> <span
                data-ls-module="dismiss" class="ls-dismiss">&times;</span> <strong>Atenção!</strong> Ao marcar SIM, você
              DECLARA que o aluno ou responsável pelo aluno apresentou o documento que comprove que a Carteira de
              Vacinação está em dia. </div>
            </p>
            <div style="display:none" id="aviso_aceite_1">
              <label class="ls-label col-md-5"> <b class="ls-label-text">DATA DO RETORNO PARA PRÓXIMA VACINA</b>
                <input type="date" placeholder="INFORME A DATA" name="vinculo_aluno_vacina_data_retorno"
                  id="data_retorno1" value="" class="ls-field-lg">
              </label>
            </div>
          </label>


          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">TURMA MULTISSERIADA</b> <a href="#" class="ls-ico-help" data-trigger="hover"
              data-ls-module="popover" data-placement="left"
              data-content="Informar se o aluno para a matrícula é de casa (já estuda na escola) ou de fora (vindo de outra escola no município ou fora dele."
              data-title="Atenção"></a> <br>
            <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_multietapa" value="S" onclick="javascript:multi_sim();"
                  checked />
                SIM </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_multietapa" value="N" onclick="javascript:multi_nao();"
                  checked />
                NÃO </label>
            </p>
          </label>


          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">SAÍDA DO ALUNO</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Indique se o aluno sai da escola sozinho ou acompanhado. Essa informação será usada para relatórios e controle na portaria."
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="1" required />
                ACOMPANHADO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="2" />
                SOZINHO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="0" />
                SEM INFORMAÇÃO
              </label>
            </p>
          </label>



          <label class="ls-label col-md-12 ls-box" style="display:none" id="multisseriada">
            <b class="ls-label-text">Etapa na turma multisseriada</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_multietapa" class="ls-select">
                <option value="" <?php if (!(strcmp(-1, ""))) {
                  echo "SELECTED";
                } ?>>-</option>
                <?php do { ?>
                  <option value="<?php echo $row_Etapa['etapa_id'] ?>"><?php echo $row_Etapa['etapa_nome'] ?></option>
                <?php } while ($row_Etapa = mysql_fetch_assoc($Etapa)); ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-md-12 ls-box" style="display:none" id="multisseriadaMatriz">
            <b class="ls-label-text">Matriz na turma multisseriada</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_matriz_multietapa" class="ls-select">
                <option value="" <?php if (!(strcmp(-1, ""))) {
                  echo "SELECTED";
                } ?>>-</option>
                <?php do { ?>
                  <option value="<?php echo $row_Matriz['matriz_id'] ?>"><?php echo $row_Matriz['matriz_nome'] ?></option>
                <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-md-12 ls-box">
            <b class="ls-label-text">VINCULAR CUIDADOR</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="O cuidador de alunos especiais na escola desempenha um papel crucial no suporte às necessidades individuais desses estudantes. Sua responsabilidade inclui oferecer assistência personalizada, adaptar o ambiente escolar, colaborar com professores e terapeutas, tudo para garantir o bem-estar e o desenvolvimento integral desses alunos."
              data-title="Caso o aluno possua um cuidador"></a>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_id_cuidador" class="ls-select">
                <option value="" <?php if (!(strcmp(-1, ""))) {
                  echo "SELECTED";
                } ?>>-</option>
                <?php do { ?>
                  <option value="<?php echo $row_ListaVinculos['vinculo_id'] ?>">
                    <?php echo $row_ListaVinculos['func_nome'] ?>
                  </option>
                <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
              </select>
            </div>
            <br>
          </label>

          <div class="ls-label col-md-12 ls-box">
            <b class="ls-label-text">ALUNO ESPECIAL (não reprova)</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Caso o aluno seja considerado especial na etapa de ensino dos anos finais e sua avaliação seja feita por meio de relatórios descritivos em vez de notas, essa opção deverá ser marcada."
              data-title="Caso o aluno seja especial">
            </a>
            <p>
              <label class="ls-label-text">
                <input type="checkbox" name="vinculo_aluno_nao_reprova" class="ls-field">
                SIM
              </label>
            </p>
          </div>

          <br>
          </label>

        </fieldset>
        <div class="ls-actions-btn">
          <input type="submit" value="MATRICULAR" class="ls-btn-primary ls-btn-lg ls-float-left" id="botaoMatricular">
          <input value="SALVANDO..." class="ls-btn-primary ls-btn-lg ls-disabled ls-float-left"
            id="botaoMatricularAguarde" style="display:none; float:left">
          <a href="vinculoAlunoExibirTurma.php" class="ls-btn-danger ls-btn-lg ls-txt-right ls-float-right">Cancelar</a>
        </div>
        <input type="hidden" name="cHash" value="<?php echo $row_Aluno['aluno_hash']; ?>">
        <input type="hidden" name="vinculo_aluno_id_aluno" value="<?php echo $row_Aluno['aluno_id']; ?>">
        <input type="hidden" name="vinculo_aluno_id_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
        <input type="hidden" name="vinculo_aluno_id_sec" value="<?php echo $row_EscolaLogada['escola_id_sec']; ?>">
        <input type="hidden" name="vinculo_aluno_ano_letivo" value="<?php echo $anoLetivoRematricula; ?>">
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
        <input type="hidden" name="detalhes"
          value="<?php echo $row_Aluno['aluno_id']; ?> - <?php echo $row_Aluno['aluno_nome'] ?>">
      </form>
      <?php if ($totalRows_VinculosAnteriores > 0) { ?>
        <div class="ls-alert-success"> <strong>Atenção:</strong> Foram encontrados os seguintes vínculos para
          <strong><?php echo $row_VinculosAnteriores['aluno_nome']; ?></strong> neste ano letivo de
          <?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?>:
          <table width="100%" class="ls-table ls-table-striped ls-sm-space">
            <thead>
              <tr>
                <th class="ls-txt-center">CIDADE</th>
                <th class="ls-txt-center">ESCOLA</th>
                <th class="ls-txt-center">TURMA</th>
                <th class="ls-txt-center" width="80">ANO</th>
                <th class="ls-txt-center" width="100">MATRÍCULA</th>
                <th class="ls-txt-center" width="120">TRANSFERÊNCIA</th>
                <th class="ls-txt-center" width="120">SITUAÇÃO</th>
                <th class="ls-txt-center">CONTATO DA ESCOLA</th>

              </tr>
            </thead>
            <tbody>
              <?php do { ?>
                <tr>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['sec_cidade']; ?> -
                    <?php echo $row_VinculosAnteriores['sec_uf']; ?>
                  </td>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_nome']; ?></td>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['turma_nome']; ?>
                    <?php echo $row_VinculosAnteriores['turma_turno']; ?>
                  </td>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?></td>
                  <td class="ls-txt-center"><?php echo inverteData($row_VinculosAnteriores['vinculo_aluno_data']); ?></td>
                  <td class="ls-txt-center">
                    <?php echo inverteData($row_VinculosAnteriores['vinculo_aluno_datatransferencia']); ?>
                  </td>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_situacao']; ?></td>
                  <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_email']; ?> <br>
                    <?php echo $row_VinculosAnteriores['escola_telefone1']; ?>
                    <br><?php echo $row_VinculosAnteriores['escola_telefone2']; ?>
                  </td>
                </tr>
              <?php } while ($row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores)); ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
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
  <script src="../../js/jquery.mask.js"></script>
  <script src="../../js/foundation-datepicker.js"></script>
  <script src="../../js/foundation-datepicker.pt-br.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



  <script>
    $(document).ready(function () {
      $('#lista-turmas').select2({
        placeholder: "Escolha a turma",
        width: '100%'
      });
    });
  </script>

  <script type="text/javascript">
    function da_casa() {
      document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false; //Habilitando
      document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false; //Habilitando
      document.getElementById("historico").style.display = "none"; //Habilitando

    }
    function de_fora() {
      document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false; //Habilitando
      document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false; //Habilitando
      document.getElementById("historico").style.display = "block"; //Habilitando
    }

    function aceite() {
      document.getElementById("aviso_aceite").style.display = "block"; //Habilitando
      document.getElementById("aviso_aceite_1").style.display = "block"; //Habilitando
    }

    function transporte_sim() {
      document.getElementById("ponto").style.display = "block"; //Habilitando
    }
    function transporte_nao() {
      document.getElementById("ponto").style.display = "none"; //Habilitando
    }

    function multi_sim() {
      document.getElementById("multisseriada").style.display = "block"; //Habilitando
      document.getElementById("multisseriadaMatriz").style.display = "block"; //Habilitando
    }
    function multi_nao() {
      document.getElementById("multisseriada").style.display = "none"; //Habilitando
      document.getElementById("multisseriadaMatriz").style.display = "none"; //Habilitando
    }
  </script>
  <script>
    $(function () {
      $('#data_matricula, #data_retorno').fdatepicker({
        //initialDate: '02/12/1989',
        format: 'dd/mm/yyyy',
        disableDblClickSelection: true,
        language: 'pt-br',
        leftArrow: '<<',
        rightArrow: '>>',
        closeIcon: 'X',
        closeButton: false
      });
    });
  </script>
  <script>

    $(document).ready(function () {

      $('.data_matricula, .data_retorno').mask('00/00/0000');


    });


    $(document).ready(function () {
      $('#botaoMatricular').click(function (event) {
        event.preventDefault(); // Previne o comportamento padrão do botão (envio imediato)

        // Troca os botões
        $("#botaoMatricular").css('display', 'none');
        $("#botaoMatricularAguarde").css('display', 'block');

        // Reabilita o botão após 2 segundos e envia o formulário
        setTimeout(function () {
          $("#botaoMatricular").css('display', 'block');
          $("#botaoMatricularAguarde").css('display', 'none');
          $('form[name="form1"]').submit(); // Envia o formulário após 2 segundos
        }, 2000); // Tempo de espera (2 segundos)
      });
    });

  </script>


</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Aluno);

mysql_free_result($Pontos);

mysql_free_result($Etapa);

mysql_free_result($VinculosAnteriores);

mysql_free_result($Turmas);
?>