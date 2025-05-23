<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include('../fnc/inverteData.php'); ?>
<?php include "../fnc/session.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf 
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
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_multietapa,
aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nome_social, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_pais, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual, aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_prof_mae, aluno_tel_mae, aluno_escolaridade_mae, aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, aluno_escolaridade_pai, aluno_rg_pai, aluno_cpf_pai, aluno_hash, turma_id, turma_nome, turma_etapa, turma_turno, turma_ano_letivo, turma_multisseriada, turma_matriz_id, etapa_id, etapa_id_filtro, etapa_nome, municipio_id, municipio_cod_ibge, municipio_nome, municipio_sigla_uf, pais_cod, pais_nome
FROM smc_vinculo_aluno
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = "
SELECT 
    COUNT(*) AS total_dias_faltas,
    SUM(CASE WHEN mes = 1 THEN 1 ELSE 0 END) AS janeiro,
    SUM(CASE WHEN mes = 2 THEN 1 ELSE 0 END) AS fevereiro,
    SUM(CASE WHEN mes = 3 THEN 1 ELSE 0 END) AS marco,
    SUM(CASE WHEN mes = 4 THEN 1 ELSE 0 END) AS abril,
    SUM(CASE WHEN mes = 5 THEN 1 ELSE 0 END) AS maio,
    SUM(CASE WHEN mes = 6 THEN 1 ELSE 0 END) AS junho,
    SUM(CASE WHEN mes = 7 THEN 1 ELSE 0 END) AS julho,
    SUM(CASE WHEN mes = 8 THEN 1 ELSE 0 END) AS agosto,
    SUM(CASE WHEN mes = 9 THEN 1 ELSE 0 END) AS setembro,
    SUM(CASE WHEN mes = 10 THEN 1 ELSE 0 END) AS outubro,
    SUM(CASE WHEN mes = 11 THEN 1 ELSE 0 END) AS novembro,
    SUM(CASE WHEN mes = 12 THEN 1 ELSE 0 END) AS dezembro
FROM (
    SELECT DISTINCT faltas_alunos_data, MONTH(faltas_alunos_data) AS mes
    FROM smc_faltas_alunos
    WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]' 
    AND faltas_alunos_justificada = 'N'
) AS subquery;";
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FaltasDetalhes = "
SELECT 
    faltas_alunos_data,
    COUNT(faltas_alunos_numero_aula) AS aulas_faltadas,
    '$row_Matricula[turma_matriz_id]' AS matriz_id
FROM smc_faltas_alunos
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]' 
AND faltas_alunos_justificada = 'N'
GROUP BY faltas_alunos_data";
$FaltasDetalhes = mysql_query($query_FaltasDetalhes, $SmecelNovo) or die(mysql_error());
$row_FaltasDetalhes = mysql_fetch_assoc($FaltasDetalhes);
$totalRows_FaltasDetalhes = mysql_num_rows($FaltasDetalhes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_aula_dia FROM smc_matriz WHERE matriz_id = '$row_Matricula[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$aulas_por_dia = $row_Matriz['matriz_aula_dia'];
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
    <?php echo "DECLARAÇÃO PARA BOLSA FAMÍLIA (FALTAS) - $row_Matricula[aluno_nome] - $row_Matricula[turma_nome] - $row_EscolaLogada[escola_nome]" ?>
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
      position: relative; /* Adicionado para suportar posicionamento absoluto dentro */
      height: 29.7cm; /* Altura fixa da página A4 */
    }

    page[size="A4"] {
      width: 21cm;
      height: 29.7cm;
      border: dotted 1px gray;
      padding: 15px;
    }

    page[size="A4"][layout="portrait"] {
      width: 29.7cm;
      height: 21cm;
    }

    @media print {
      body, page {
        margin: 0;
        box-shadow: 0;
      }
      
      .row {
        page-break-inside: avoid;
      }
    }
    table.bordasimples {
      border-collapse: collapse;
      font-size: 12px;
      width: 100%;
    }

    table.bordasimples tr td {
      border: 1px solid #808080;
      padding: 3px;
      font-size: 15px;
      font-weight: bold;
    }

    table.bordasimples tr th {
      border: 1px solid #808080;
      padding: 3px;
      font-size: 15px;
    }

    .assinatura-fixa {
      position: relative;
      margin-top: 30px;
      width: 100%;
      text-align: center;
    }
    
    /* Reduzir espaçamento para evitar quebras de página desnecessárias */
    .ls-txt-center br {
      display: none;
    }
    
    h1 {
      margin-top: 5px;
      margin-bottom: 5px;
      font-size: 24px;
    }
    
    /* Reduzir o tamanho do cabeçalho da escola */
    table:first-of-type {
      margin-bottom: 10px;
    }
    
    /* Reduzir espaçamento entre elementos */
    .row {
      margin-bottom: 5px;
    }
    
    /* Reduzir o tamanho da fonte do texto principal */
    p[style*="line-height"] {
      font-size: 14px;
      line-height: 140% !important;
    }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="self.print();">
  <page size="A4">
    <table>
      <tr>
        <td width="20%"><?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
              src="../../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
              width="100%" /><?php } else { ?><img src="../../../img/brasao_republica.png" alt=""
              width="100%" /><?php } ?></td>
        <td width="60%">
          <p><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></p>
          <p>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -</p>
          <p>ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>,
            <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?>
          </p>
          <p><?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP:
            <?php echo $row_EscolaLogada['escola_cep']; ?>
          </p>
          <p>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?></p>
          <p><?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></p>
        </td>
        <td width="20%"><img src="https://presenca.mec.gov.br/seb/assets/images/logos/logoPresenca.jpeg" width="100%">
        </td>
      </tr>
    </table>

    <div class="row">
      <div class="col-xs-12">
        <p></p>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 ls-txt-center">
        <h1>DECLARAÇÃO PARA BOLSA FAMÍLIA</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <p style="line-height: 150%; text-align:justify; font-size:16px;">
          Declaro para os devidos fins de localização e regularização no <strong>Projeto Presença</strong> e
          <strong>Bolsa Família</strong>, que
          <strong><?php if ($row_Matricula['aluno_nome_social'] == '') {
            echo $row_Matricula['aluno_nome'];
          } else {
            echo $row_Matricula['aluno_nome_social'];
          } ?></strong>,
          <?php if ($row_Matricula['aluno_cpf'] <> "") { ?>CPF nº
            <strong><?php echo $row_Matricula['aluno_cpf']; ?></strong>, <?php } ?>
          <?php if ($row_Matricula['aluno_nis'] <> "") { ?>NIS nº
            <strong><?php echo $row_Matricula['aluno_nis']; ?></strong>, <?php } ?>
          nascido(a) em <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong>,
          <?php if ($row_Matricula['aluno_nacionalidade'] == "3") { ?>estrangeiro nascido no país
            <strong><?php echo $row_Matricula['pais_nome']; ?></strong><?php } else { ?>natural de
            <strong><?php echo $row_Matricula['municipio_nome']; ?>-<?php echo $row_Matricula['municipio_sigla_uf']; ?></strong><?php } ?>,
          filho(a) de
          <strong><?php echo $row_Matricula['aluno_filiacao1']; ?></strong><?php if ($row_Matricula['aluno_filiacao2'] <> "") { ?>
            e <strong><?php echo $row_Matricula['aluno_filiacao2']; ?></strong><?php } ?>,
          residente na <strong><?php echo $row_Matricula['aluno_endereco']; ?>,
            <?php echo $row_Matricula['aluno_numero']; ?> - <?php echo $row_Matricula['aluno_bairro']; ?>,
            <?php echo $row_Matricula['aluno_municipio']; ?>-<?php echo $row_Matricula['aluno_uf']; ?></strong>
          está devidamente matriculado(a) e frequente nesta Unidade de Ensino, cursando o
          <strong><?php echo $row_Matricula['turma_nome']; ?> (<?php echo $multietapa; ?>)</strong>, no turno <strong><?php
                 switch ($row_Matricula['turma_turno']) {
                   case 0:
                     echo "INTEGRAL";
                     break;
                   case 1:
                     echo "MATUTINO";
                     break;
                   case 2:
                     echo "VESPERTINO";
                     break;
                   case 3:
                     echo "NOTURNO";
                     break;
                   default:
                     echo "-";
                     break;
                 }
                 ?></strong>, neste ano letivo de <strong><?php echo $row_Matricula['turma_ano_letivo']; ?></strong>.
        </p>
      </div>
    </div>
    <br>
    <p class="ls-txt-center">FALTAS POR MÊS (CONTADAS POR DIAS)</p>
    <table class="bordasimples" width="100%">
      <tr>
        <td class="ls-txt-center">JAN</td>
        <td class="ls-txt-center">FEV</td>
        <td class="ls-txt-center">MAR</td>
        <td class="ls-txt-center">ABR</td>
        <td class="ls-txt-center">MAI</td>
        <td class="ls-txt-center">JUN</td>
        <td class="ls-txt-center">JUL</td>
        <td class="ls-txt-center">AGO</td>
        <td class="ls-txt-center">SET</td>
        <td class="ls-txt-center">OUT</td>
        <td class="ls-txt-center">NOV</td>
        <td class="ls-txt-center">DEZ</td>
      </tr>
      <tr>
        <td class="ls-txt-center"><?php echo ($row_Faltas['janeiro'] > 0) ? $row_Faltas['janeiro'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['fevereiro'] > 0) ? $row_Faltas['fevereiro'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['marco'] > 0) ? $row_Faltas['marco'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['abril'] > 0) ? $row_Faltas['abril'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['maio'] > 0) ? $row_Faltas['maio'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['junho'] > 0) ? $row_Faltas['junho'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['julho'] > 0) ? $row_Faltas['julho'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['agosto'] > 0) ? $row_Faltas['agosto'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['setembro'] > 0) ? $row_Faltas['setembro'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['outubro'] > 0) ? $row_Faltas['outubro'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['novembro'] > 0) ? $row_Faltas['novembro'] : '-'; ?></td>
        <td class="ls-txt-center"><?php echo ($row_Faltas['dezembro'] > 0) ? $row_Faltas['dezembro'] : '-'; ?></td>
      </tr>
      <tr>
        <td colspan="12" class="ls-txt-center">TOTAL DE DIAS COM FALTAS: <strong><?php echo $row_Faltas['total_dias_faltas']; ?></strong></td>
      </tr>
    </table>

    <br>
    <p class="ls-txt-center">DETALHAMENTO DAS FALTAS</p>
    <table class="bordasimples" width="100%">
      <tr>
        <th class="ls-txt-center">DATA</th>
        <th class="ls-txt-center">TIPO DE FALTA</th>
      </tr>
      <?php
      if ($totalRows_FaltasDetalhes > 0) {
        do {
          $data_falta = $row_FaltasDetalhes['faltas_alunos_data'];
          $aulas_faltadas = $row_FaltasDetalhes['aulas_faltadas'];
          $tipo_falta = ($aulas_faltadas == $aulas_por_dia) ? "Falta Completa" : "Falta Parcial ($aulas_faltadas de $aulas_por_dia aulas)";
          ?>
          <tr>
            <td class="ls-txt-center"><?php echo inverteData($data_falta); ?></td>
            <td class="ls-txt-center"><?php echo $tipo_falta; ?></td>
          </tr>
          <?php
        } while ($row_FaltasDetalhes = mysql_fetch_assoc($FaltasDetalhes));
      } else {
        ?>
        <tr>
          <td colspan="2" class="ls-txt-center">Nenhuma falta registrada</td>
        </tr>
      <?php } ?>
    </table>

    <p style="font-size: 12px; text-align: justify;">
      <strong>Nota:</strong> A contagem mensal de faltas apresentada acima reflete os dias em que o aluno esteve
      ausente, considerando o percentual de presença baseado nos dias letivos. Faltas completas ocorrem quando o aluno
      esteve ausente em todas as aulas do dia, enquanto faltas parciais indicam ausência em apenas algumas aulas. A
      contagem original do sistema é feita por aulas, mas esta declaração foi ajustada para exibir dias de ausência,
      conforme solicitado pelo programa Bolsa Família. Para mais detalhes, a folha de frequência anual do aluno pode ser
      anexada, se necessário.
    </p>

    <div class="row">
      <div class="col-xs-12">
        <p></p>
      </div>
    </div>
    <br><br><br><br>
    <p style="text-align:right">
      <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>,
      <?php
      setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
      date_default_timezone_set('America/Sao_Paulo');
      echo utf8_encode(strftime('%d de %B de %Y', strtotime('today')));
      ?>
    </p>
    <br><br><br><br>
    <div class="row">
      <div class="col-xs-12">
        <p style="line-height: 180%; text-align:justify; font-size:16px;">Por ser verdade, assino a presente declaração.
        </p>
      </div>
    </div>
    <br><br><br><br>
    <div class="row">
      <div class="col-xs-12">
        <div class="ls-txt-right">
          <?php
          $aux = '../fnc/qr/php/qr_img.php?';
          $aux .= 'd=https://www.smecel.com.br/publico/matricula.php?chave=' . $row_Matricula['vinculo_aluno_verificacao'] . '&';
          $aux .= 'e=M&';
          $aux .= 's=2&';
          $aux .= 't=P';
          ?>
          <img src="<?php echo $aux; ?>" align="absmiddle" />
        </div>
      </div>
    </div>
    <div class="assinatura-fixa">
      <p>_________________________________________________________<br>Diretor(a) ou Secretário(a) Escolar</p>
    </div>
  </page>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="../js/locastyle.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);
mysql_free_result($EscolaLogada);
mysql_free_result($Matricula);
mysql_free_result($Faltas);
mysql_free_result($FaltasDetalhes);
mysql_free_result($Matriz);
?>