<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../fnc/inverteData.php'); ?>
<?php include "../fnc/session.php"; ?>
<?php include('../../funcoes/url_base.php'); ?>
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

include "../usuLogado.php";
include "../fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, 
sec_telefone2, sec_email, sec_nome_secretario, sec_ibge_municipio, sec_regra_media FROM smc_sec
WHERE sec_id = '$row_UsuLogado[usu_sec]'";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

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

$colname_Matricula = "-1";
if (isset($_GET['hash'])) {
  $colname_Matricula = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
  SELECT 
  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_multietapa,vinculo_aluno_repetente,
  aluno_id,
  aluno_cod_inep,
  aluno_cpf,
  aluno_nome,
  aluno_nascimento,
  aluno_nome_responsavel_legal,
  aluno_filiacao1,
  aluno_filiacao2,
  aluno_sexo,
  aluno_raca,
  aluno_nacionalidade,
  aluno_pais,
  aluno_uf_nascimento,
  aluno_municipio_nascimento,
  aluno_municipio_nascimento_ibge,
  aluno_aluno_com_deficiencia,
  aluno_nis,
  aluno_identidade,
  aluno_emissor,
  aluno_uf_emissor,
  aluno_data_espedicao,
  aluno_tipo_certidao,
  aluno_termo,
  aluno_folhas,
  aluno_livro,
  aluno_emissao_certidao,
  aluno_uf_cartorio,
  aluno_mucicipio_cartorio,
  aluno_nome_cartorio,
  aluno_num_matricula_modelo_novo,
  aluno_localizacao,
  aluno_cep,
  aluno_endereco,
  aluno_numero,
  aluno_complemento,
  aluno_bairro,
  aluno_uf,
  aluno_municipio,
  aluno_telefone,
  aluno_celular,
  aluno_email,
  aluno_foto,
  aluno_sus,
  aluno_tipo_deficiencia,
  aluno_laudo,
  aluno_alergia,
  aluno_alergia_qual,
  aluno_emergencia_avisar,
  aluno_emergencia_tel1,
  aluno_emergencia_tel2,
  aluno_prof_mae,
  aluno_tel_mae,
  aluno_escolaridade_mae,
  aluno_rg_mae,
  aluno_cpf_mae,
  aluno_prof_pai,
  aluno_tel_pai,
  aluno_escolaridade_pai,
  aluno_rg_pai,
  aluno_cpf_pai,
  aluno_recebe_bolsa_familia,
  CASE aluno_sexo
  WHEN 1 THEN 'MASCULINO'
  WHEN 2 THEN 'FEMININO'
  END AS aluno_sexo,
  CASE aluno_aluno_com_deficiencia
  WHEN 1 THEN 'SIM - '
  WHEN 2 THEN 'NÃO'
  END AS aluno_aluno_com_deficiencia,
  CASE aluno_alergia
  WHEN 1 THEN 'SIM - '
  WHEN 2 THEN 'NÃO'
  END AS aluno_alergia,
  CASE aluno_recebe_bolsa_familia
  WHEN 1 THEN 'SIM'
  WHEN 2 THEN 'NÃO'
  END AS aluno_recebe_bolsa_familia,
  CASE aluno_destro
  WHEN 1 THEN 'DESTRO'
  WHEN 2 THEN 'CANHOTO'
  END AS aluno_destro,
  aluno_hash,
  CASE aluno_raca
  WHEN 1 THEN 'BRANCA'
  WHEN 2 THEN 'PRETA'
  WHEN 3 THEN 'PARDA'
  WHEN 4 THEN 'AMARELA'
  WHEN 5 THEN 'INDÍGENA'
  WHEN 6 THEN 'NÃO DECLARADA'
  WHEN '-1' THEN 'SEM INFORMACAO'
  END AS aluno_raca_descricao,
  turma_id,
  turma_nome,
  turma_etapa,
  turma_turno,
  turma_ano_letivo,
  turma_multisseriada,
  etapa_id,
  etapa_id_filtro,
  etapa_nome,
  municipio_id,
  municipio_cod_ibge,
  municipio_nome,
  municipio_sigla_uf,
  pais_cod,
  pais_nome
  FROM 
  smc_vinculo_aluno
  LEFT JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
  LEFT JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  LEFT JOIN smc_etapa ON etapa_id = turma_etapa
  LEFT JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge
  LEFT JOIN smc_paises ON pais_cod = aluno_pais

  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);
$multietapa = $row_Matricula['etapa_nome'];

if ($row_Matricula['turma_multisseriada'] == 1) {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id = $row_Matricula[vinculo_aluno_multietapa]";
  $Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
  $row_Etapa = mysql_fetch_assoc($Etapa);
  $totalRows_Etapa = mysql_num_rows($Etapa);

  $multietapa = $row_Etapa['etapa_nome'];

  mysql_free_result($Etapa);
}

$exibirNome = "";
$exibirCPF = "";

if ($row_Matricula['aluno_nome_responsavel_legal'] != '') {
  $exibirNome = $row_Matricula['aluno_nome_responsavel_legal'];
  $exibirCPF = $row_Matricula['aluno_cpf_responsavel_legal'];
} else if ($row_Matricula['aluno_filiacao1'] != '') {
  $exibirNome = $row_Matricula['aluno_filiacao1'];
  $exibirCPF = $row_Matricula['aluno_cpf_mae'];
} else {
  $exibirNome = $row_Matricula['aluno_filiacao2'];
  $exibirCPF = $row_Matricula['aluno_cpf_pai'];
}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>
    <?php echo "TERMO DE COMPROMISSO - $row_Matricula[aluno_nome] - $row_Matricula[turma_nome] - $row_EscolaLogada[escola_nome]" ?>
  </title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="../css/locastyle.css">

  <script src="../js/locastyle.js"></script>
  <style>
    body {
      font-size: 12px;

      background-image: url(<?php if ($row_EscolaLogada['escola_logo'] <> "") { ?>../../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../../img/marcadagua/brasao_republica.png<?php } ?>);
      background-repeat: no-repeat;
      background-position: center center;
      z-index: -999;

    }

    p {
      margin-bottom: 1px;
    }

    page {
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;

    }

    page[size="A4"] {
      width: 21cm;
      height: 29.7cm;
      padding: 5px;
    }

    page[size="A4"][layout="portrait"] {
      width: 29.7cm;
      height: 21cm;
    }

    @media print {

      body,
      page {
        margin: 0;
        box-shadow: 0;
      }
    }

    ol {
      counter-reset: item;
    }

    li {
      display: block;
    }

    li:before {
      content: counters(item, ".") " ";
      counter-increment: item;
    }

    li ol li {
      list-style-type: none;
    }

    #box-compromisso table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
      margin: 20px 0;
    }

    #box-compromisso th,
    #box-compromisso td {
      border: 1px solid #000;
      padding: 10px;
    }

    #box-compromisso th {
      background-color: #f2f2f2;
    }

    table.bordasimples {
      border-collapse: collapse;
      font-size: 10px;
    }

    table.bordasimples tr td {
      border: 1px dotted #000000;
      padding: 2px;
      font-size: 17px;
      vertical-align: top;
      height: 30px;
      font-weight: bolder;
    }

    table.bordasimples tr th {
      border: 1px dotted #000000;
      padding: 2px;
      font-size: 17px;
      vertical-align: top;
      height: 30px;
    }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="self.print();">

  <!-- CONTEÚDO -->




  <page size="A4" style="padding:50px;">



    <table>

      <tr>
        <td width="150px" class="ls-txt-center">
          <span><?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
                src="../../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
                width="100px" /><?php } else { ?><img src="../../../img/brasao_republica.png" alt=""
                width="80px" /><?php } ?></span>
        </td>

        <td width="350px">
          <h2><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></h2>
          <small>
            <?php echo $row_EscolaLogada['escola_endereco']; ?>,
            <?php echo $row_EscolaLogada['escola_num']; ?> -
            <?php echo $row_EscolaLogada['escola_bairro']; ?> -
            <?php echo $row_EscolaLogada['escola_cep']; ?><br>
            CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?>
            INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
            <?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?>
            <?php echo $row_EscolaLogada['escola_email']; ?>
          </small>
        </td>

        <td class="ls-txt-right" width="270px">

          <h2 class="ls-txt-right">FICHA DE MATRÍCULA</h2>
          <h1 class="ls-txt-right">ANO LETIVO <?php echo $row_Matricula['turma_ano_letivo']; ?></h1>

        </td>
      </tr>

    </table>

    <br>
    <h3 class="ls-txt-center"><strong>DADOS DA MATRÍCULA</strong></h3><br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td>
          <small><strong>Matrícula:</strong></small><br><?php echo str_pad($row_Matricula['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?>&nbsp;
        </td>
        <td><small><strong>Nome da turma
              matriculada:</strong></small><br><?php echo $row_Matricula['turma_nome']; ?>&nbsp;</td>
        <td><small><strong>Etapa:</strong></small><br><?php echo $multietapa; ?>
          <?php
          if ($row_Matricula['vinculo_aluno_repetente'] == 'S') {
            echo '<b>(Repetente)</b>';
          }
          ?>
        </td>
        <td><small><strong>Turno:</strong></small><br><?php echo $row_Matricula['turma_turno']; ?>&nbsp;</td>
        <td><small><strong>Matrícula
              em:</strong></small><br><?php echo date("d/m/Y", strtotime($row_Matricula['vinculo_aluno_data'])); ?>&nbsp;
        </td>
      </tr>
    </table>

    <br>
    <h3 class="ls-txt-center"><strong>DADOS PESSOAIS</strong></h3><br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td><small><strong>Nome do(a) aluno(a):</strong></small><br><?php echo $row_Matricula['aluno_nome']; ?>&nbsp;
        </td>
        <td>
          <small><strong>Nascimento:</strong></small><br><?php echo inverteData($row_Matricula['aluno_nascimento']); ?>&nbsp;
        </td>
        <td><small><strong>Sexo:</strong></small><br><?php echo $row_Matricula['aluno_sexo']; ?>&nbsp;</td>

        <td rowspan="0" width="155px">
          <div style="">
            <?php if ($row_Matricula['aluno_foto'] == "") { ?>
              <img src="<?php echo URL_BASE . '/aluno/fotos/' ?>semfoto.jpg" width="100%">
            <?php } else { ?>
              <img src="<?php echo URL_BASE . '/aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" width="100%">
            <?php } ?>
          </div>
        </td>
      </tr>
      <tr>
        <td><?php if ($row_Matricula['aluno_nacionalidade'] == "3") { ?><small><strong>Pais de
                nascimento:</strong></small><br><?php echo $row_Matricula['pais_nome']; ?>&nbsp;<?php } else { ?><small><strong>Município
                de nascimento:</strong></small><br><?php echo $row_Matricula['municipio_nome']; ?> -
            <?php echo $row_Matricula['municipio_sigla_uf']; ?>&nbsp;<?php } ?></td>
        <td><small><strong>RG:</strong></small><br><?php echo $row_Matricula['aluno_identidade']; ?>
          <?php echo $row_Matricula['aluno_emissor']; ?> <?php echo $row_Matricula['aluno_uf_emissor']; ?>&nbsp;</td>
        <td>
          <small><strong>Emissão:</strong></small><br><?php echo inverteData($row_Matricula['aluno_data_espedicao']); ?>&nbsp;
        </td>
      </tr>
      <tr>
        <td><small><strong>CPF:</strong></small><br><?php echo $row_Matricula['aluno_cpf']; ?>&nbsp;</td>
        <?php if ($row_Matricula['aluno_tipo_certidao'] == '1') { ?>
          <td><small><strong>Termos/Folhas/Livro:</strong></small><br><?php echo $row_Matricula['aluno_termo']; ?> /
            <?php echo $row_Matricula['aluno_folhas']; ?> / <?php echo $row_Matricula['aluno_livro']; ?>&nbsp;</td>
          <td><small><strong>Emissão
                Certidão:</strong></small><br><?php echo inverteData($row_Matricula['aluno_emissao_certidao']); ?>&nbsp;
          </td>
        <?php } elseif ($row_Matricula['aluno_tipo_certidao'] == '2') { ?>
          <td colspan="2"><small><strong>Matrícula certidão (modelo
                novo):</strong></small><br><?php echo $row_Matricula['aluno_num_matricula_modelo_novo']; ?>&nbsp;</td>
        <?php } else { ?>
          <td colspan="2"></td> <!-- Adicionei um <td> vazio para caso nenhuma condição seja atendida -->
        <?php } ?>
      </tr>
      <tr>
        <td><small><strong>Endereço:</strong></small><br><?php echo $row_Matricula['aluno_endereco']; ?>,
          <?php echo $row_Matricula['aluno_numero']; ?>&nbsp;</td>
        <td><small><strong>Bairro:</strong></small><br><?php echo $row_Matricula['aluno_bairro']; ?>&nbsp;</td>
        <td><small><strong>CEP:</strong></small><br><?php echo $row_Matricula['aluno_cep']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td><small><strong>Cidade:</strong></small><br><?php echo $row_Matricula['aluno_municipio']; ?> -
          <?php echo $row_Matricula['aluno_uf']; ?>&nbsp;</td>
        <td><small><strong>Telefones:</strong></small><br><?php echo $row_Matricula['aluno_telefone']; ?> /
          <?php echo $row_Matricula['aluno_celular']; ?>&nbsp;</td>
        <td><small><strong>Cor/Raça:</strong></small><br><?php echo $row_Matricula['aluno_raca_descricao']; ?>&nbsp;
        </td>
      </tr>

      <tr>
        <td><small><strong>E-mail:</strong></small><br><?php echo $row_Matricula['aluno_email']; ?>&nbsp;</td>
        <td><small><strong>NIS:</strong></small><br><?php echo $row_Matricula['aluno_nis']; ?>&nbsp;</td>
        <td><small><strong>SUS:</strong></small><br><?php echo $row_Matricula['aluno_sus']; ?>&nbsp;</td>

      </tr>


    </table>

    <br>
    <h3 class="ls-txt-center"><strong>DADOS CLÍNICOS</strong></h3><br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td><small><strong>Deficiência:</strong></small><br><?php echo $row_Matricula['aluno_aluno_com_deficiencia']; ?>
          <?php echo $row_Matricula['aluno_tipo_deficiencia']; ?>&nbsp;</td>
        <td><small><strong>Alergia:</strong></small><br><?php echo $row_Matricula['aluno_alergia']; ?>
          <?php echo $row_Matricula['aluno_alergia_qual']; ?>&nbsp;</td>
        <td><small><strong>Recebe bolsa
              família?</strong></small><br><?php echo $row_Matricula['aluno_recebe_bolsa_familia']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td><small><strong>Destro/Canhoto?</strong></small><br><?php echo $row_Matricula['aluno_destro']; ?>&nbsp;</td>
        <td><small><strong>Em caso de emergência,
              avisar:</strong></small><br><?php echo $row_Matricula['aluno_emergencia_avisar']; ?>&nbsp;</td>
        <td><small><strong>Telefones de
              emergência:</strong></small><br><?php echo $row_Matricula['aluno_emergencia_tel1']; ?>
          <?php echo $row_Matricula['aluno_emergencia_tel2']; ?>&nbsp;</td>
      </tr>
    </table>

    <br>
    <h3 class="ls-txt-center"><strong>DADOS FAMILIARES</strong></h3><br>
    <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
      <tr>
        <td><small><strong>Filiação 1:</strong></small><br><?php echo $row_Matricula['aluno_filiacao1']; ?>&nbsp;</td>
        <td><small><strong>Profissão:</strong></small><br><?php echo $row_Matricula['aluno_prof_mae']; ?>&nbsp;</td>
        <td><small><strong>Telefone:</strong></small><br><?php echo $row_Matricula['aluno_tel_mae']; ?>&nbsp;</td>
        <td><small><strong>Escolaridade:</strong></small><br><?php

        switch ($row_Matricula['aluno_escolaridade_mae']) {

          case "1":
            echo "NÃO ESTUDOU";
            break;
          case "2":
            echo "FUNDAMENTAL COMPLETO";
            break;
          case "3":
            echo "ENSINO MÉDIO COMPLETO";
            break;
          case "4":
            echo "SUPERIOR COMPLETO";
            break;
          case "5":
            echo "ENSINO FUNDAMENTAL INCOMPLETO";
            break;
          case "6":
            echo "ENSINO MÉDIO INCOMPLETO";
            break;
          case "7":
            echo "ENSINO SUPERIOR INCOMPLETO";
            break;
          default:
            echo "-";
            break;


        }

        ?>&nbsp;</td>
        <td><small><strong>RG:</strong></small><br><?php echo $row_Matricula['aluno_rg_mae']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td><small><strong>Filiação 2:</strong></small><br><?php echo $row_Matricula['aluno_filiacao2']; ?>&nbsp;</td>
        <td><small><strong>Profissão:</strong></small><br><?php echo $row_Matricula['aluno_prof_pai']; ?>&nbsp;</td>
        <td><small><strong>Telefone:</strong></small><br><?php echo $row_Matricula['aluno_tel_pai']; ?>&nbsp;</td>
        <td><small><strong>Escolaridade:</strong></small><br><?php

        switch ($row_Matricula['aluno_escolaridade_pai']) {

          case "1":
            echo "NÃO ESTUDOU";
            break;
          case "2":
            echo "FUNDAMENTAL COMPLETO";
            break;
          case "3":
            echo "ENSINO MÉDIO COMPLETO";
            break;
          case "4":
            echo "SUPERIOR COMPLETO";
            break;
          case "5":
            echo "ENSINO FUNDAMENTAL INCOMPLETO";
            break;
          case "6":
            echo "ENSINO MÉDIO INCOMPLETO";
            break;
          case "7":
            echo "ENSINO SUPERIOR INCOMPLETO";
            break;
          default:
            echo "-";
            break;


        }

        ?>&nbsp;</td>
        <td><small><strong>RG:</strong></small><br><?php echo $row_Matricula['aluno_rg_pai']; ?>&nbsp;</td>
      </tr>
    </table>

    <div class="row">
      <div class="col-xs-12">
        <p></p>
      </div>
    </div>
      <br><br><br><br><br><br><br><br><br>
      <div class="row">
        <div class="col-md-6">
          <div class="signature">
            <p class="ls-txt-center">_______________________________________<br>Pai ou responsável</p>
          </div>
        </div>
        <br><br><br>  
        <div class="col-md-6">
          <div class="signature">
            <p class="ls-txt-center">_______________________________________<br>Funcionário(a) da escola</p>
          </div>
        </div>
      </div>

    <div class="row">
      <div class="col-xs-12">
        <p></p>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 ls-txt-center">

        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <p>
        <h1>TERMO DE COMPROMISSO DOS PAIS E RESPONSÁVEIS LEGAIS DO ALUNO</h1>
        </p><br><br><br>

      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <p style="line-height: 180%; text-align:justify; font-size:16px;">À Direção desta Escola,</p>
        <p style="line-height: 180%; text-align:justify; font-size:16px;">
          Eu, <?= $exibirNome ?>, CPF <?= $exibirCPF ?> na condição responsável por
          <?php echo $row_Matricula['aluno_nome']; ?>, venho requerer a <strong>MATRÍCULA E PERMANÊNCIA</strong> neste
          Estabelecimento de <?= $row_Matricula['etapa_nome'] ?>, assumindo o compromisso de fidelidade ao estabelecido
          no <strong>REGIMENTO INTERNO E REGIMENTO DISCIPLINAR DESTA ESCOLA</strong>, sob pena de <strong>CANCELAMENTO
            DA MATRÍCULA</strong>.
        </p>

      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 ls-txt-center">

        <br><br>
        <p>
        <h2>TERMO DE COMPROMISSO</h2>
        </p><br><br>

      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <ol>
          <li>Comprometo-me em manter os dados cadastrais atualizados e comparecer na escola sempre que solicitado para
            ter ciência da situação escolar e disciplinar do referido discente;
            <ol type="1">
              <li>Estou ciente que o Estatuto da Criança e do Adolescente (Lei 8069/90) em seu Art. 129,V, impõe aos
                pais a obrigação de acompanhar a frequência e o aproveitamento escolar dos filhos e que o descumprimento
                desta obrigação será levado ao conhecimento das autoridades competentes.</li>
            </ol>
          </li>
          <li>Responsabilizo-me pelo ressarcimento de quaisquer danos e/ou prejuízos que venham a ser causados pelo(a)
            aluno(a) acima citado(a) ao patrimônio móvel ou imóvel, bem como a outra(s) pessoa(s) neste espaço
            educacional;</li>
          <li>Concordo com a exposição da imagem do(a) aluno(a) em eventos cívicos, desportivos e culturais, internos e
            externos, bem como a exposição de suas produções intelectuais, artísticas e culturais sem quaisquer ônus
            para esta escola e/ou PMBA;</li>
          <li>Comprometo-me em adquirir todos os uniformes exigidos para a frequência do(a) aluno(a) nesta Unidade
            Escolar, atendendo ao prazo estabelecido pela Direção Geral;</li>
          <li>Autorizo as saídas pedagógicas e de representação do(a) aluno(a) sempre que forem necessárias, sob a
            vigilância e acompanhamento de prepostos da Escola;</li>
          <li>Torno-me ciente que:
            <ol type="1">
              <li>O processo de alocação em turmas antes ou durante o ano letivo é de competência desta Escola, o(a)
                aluno(a) poderá ser transferido de uma turma (sala) para outra conforme ajustes necessários, em
                atendimento às necessidades pedagógicas e disciplinares;</li>
              <li>A AVALIAÇÃO DE APRENDIZAGEM seguirá o regimento interno municipal que diz:</li>
            </ol>
          </li>
          <div id="box-compromisso">
            <table>
              <tr>
                <th class="ls-txt-left" colspan="3"><strong>O REGIME TRIMESTRAL DE AVALIAÇÃO É DADO ATRAVÉS DE UMA
                    ESCALA ANUAL DE 0 A 100 PONTOS, DE ACORDO COM O REGIMENTO UNIFICADO DA REDE MUNICIPAL DE ENSINO. ATA
                    COMEMN DE 26/04/2023 – Nº 09</strong></th>
              </tr>
              <tr>
                <th colspan="3">DISTRIBUIÇÃO DE PONTOS</th>
              </tr>
              <tr>
                <th>1º Trimestre » Máximo de 30 Pontos</th>
                <th>2º Trimestre » Máximo de 30 Pontos</th>
                <th>3º Trimestre » Máximo de 40 Pontos</th>
              </tr>
              <tr>
                <td>Mínimo para Aprovação » 18 Pontos</td>
                <td>Mínimo para Aprovação » 18 Pontos</td>
                <td>Mínimo para Aprovação » 24 Pontos</td>
              </tr>
            </table>
          </div>
          <li>Torno-me ciente que:
            <ol type="1">
              <li>Neste ato de conheço e concordo com o fato de <strong>O ALUNO DESTA ESCOLA estar sujeito ao que
                  prescreve o Regimento Disciplinar e principalmente estou ciente da existência do “ COMITÊ
                  DISCIPLINAR”, a que pode ser submetido todo (a) aluno (a) que se encontrar com nota de comportamento
                  inferior a 2,0 (dois) pontos (grau de comportamento incompatível) ou que tenha cometido transgressão
                  disciplinar de natureza eliminatória. Nestes dois casos, o aluno poderá ser “DESLIGADO” da
                  Escola</strong>;</li>
              <li>O Regimento Disciplinar estabelece um escalonamento de notas e graus de comportamento estando
                disponível também, uma “Cópia Física” na sala dos Diretores desta Escola para consulta.</li>
              <li>É obrigatória a presença dos pais e (ou) responsáveis na Escola, a fim de realizarem o
                </strong>“CONTRADITÓRIO E A AMPLA DEFESA”</strong> do discente, sempre que o mesmo receber comunicações
                e/ou punições disciplinares, bem como no momento de assinatura do procedimento apuratório de possíveis
                infrações. Nesses dois casos, somente será permitida a entrada do aluno na presença dos pais e (ou)
                responsáveis;</li>
              <li>
                Tenho direito de ingressar com Reconsideração de Ato (revisão da punição), junto ao Diretor Disciplinar,
                sempre que considerar que alguma punição imposta ao discente for considerada ilegal, injusta, etc;
              </li>
            </ol>
          </li>
          <li>
            A Matrícula do Aluno(a) do(a) qual sou responsável será <strong>CONTRAINDICADA</strong> nos seguintes casos:
            <ol type="1">
              <li>O (A) aluno(a) ter ingressado no comportamento incompatível, ter sido submetido ao Comitê Disciplinar
                e considerado contraindicado a permanecer nesta Escola, e/ou ter cometido falta eliminatória;</li>
              <li>Tenho ciência que, em caso de não aceitação aos termos aqui estabelecidos, tenho o direito de
                solicitar à Direção da Escola, o remanejamento do meu (minha) filho (a) ou dependente legal, para outro
                Estabelecimento de Ensino da Rede Pública Municipal, sem a ocorrência de prejuízo pedagógico.</li>
            </ol>
          </li>
        </ol>


      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <p></p>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <br><br>
        <p style="line-height: 180%; text-align:justify; font-size:16px;">“Declaro estar ciente que o(a) aluno(a) do(a)
          qual sou responsável está sob a regência do <strong>Regimento Interno e Regimento Interno Disciplinar da
            Escola Municipal <?= $row_EscolaLogada['escola_nome'] ?></strong>, no Município de Medeiros Neto – BA,
          estando de acordo com a sua aplicação durante todo o período em que o(a) aluno(a) pertencer à esta Escola."
        </p><br>
        <p>OBS: O referido Termo de Compromisso não prejudica as demais Normas contidas no Regimento Interno da Escola.
        </p>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <p><br><br><br><br><br></p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="signature">
            <p class="ls-txt-center">_______________________________________<br>Pai ou responsável</p>
          </div>
        </div>
        <br><br><br>  
        <div class="col-md-6">
          <div class="signature">
            <p class="ls-txt-center">_______________________________________<br>Funcionário(a) da escola</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <p><br><br><br><br><br></p>
        </div>
      </div>
      <p style="text-align:right">
        <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>,
        <?php
        setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        echo strftime('%d de %B de %Y', strtotime($row_Matricula['vinculo_aluno_data']));
        ?>
      </p>
    </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <p>
          <hr>
        </p>
      </div>
    </div>

    <hr>
    <div class="row">
      <div class="col-xs-12">


        <?php
        $aux = '../fnc/qr/php/qr_img.php?';
        $aux .= 'd=https://www.smecel.com.br/publico/matricula.php?chave=' . $row_Matricula['vinculo_aluno_verificacao'] . '&';
        $aux .= 'e=M&';
        $aux .= 's=3&';
        $aux .= 't=P';
        ?>
        <div>
          <img src="<?php echo $aux; ?>" align="absmiddle" />
          <small><strong>https://www.smecel.com.br/publico/matricula.php?chave=<?php echo $row_Matricula['vinculo_aluno_verificacao']; ?></strong></small>
        </div>

      </div>
    </div>


  </page>



  <!-- CONTEÚDO -->



  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="../js/locastyle.js"></script>


</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Matricula);
?>