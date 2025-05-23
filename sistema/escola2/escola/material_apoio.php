<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material1 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 1
";
$Material1 = mysql_query($query_Material1, $SmecelNovo) or die(mysql_error());
$row_Material1 = mysql_fetch_assoc($Material1);
$totalRows_Material1 = mysql_num_rows($Material1);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material2 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 2
";
$Material2 = mysql_query($query_Material2, $SmecelNovo) or die(mysql_error());
$row_Material2 = mysql_fetch_assoc($Material2);
$totalRows_Material2 = mysql_num_rows($Material2);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material3 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 3
";
$Material3 = mysql_query($query_Material3, $SmecelNovo) or die(mysql_error());
$row_Material3 = mysql_fetch_assoc($Material3);
$totalRows_Material3 = mysql_num_rows($Material3);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material4 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 4
";
$Material4 = mysql_query($query_Material4, $SmecelNovo) or die(mysql_error());
$row_Material4 = mysql_fetch_assoc($Material4);
$totalRows_Material4 = mysql_num_rows($Material4);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material5 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 5
";
$Material5 = mysql_query($query_Material5, $SmecelNovo) or die(mysql_error());
$row_Material5 = mysql_fetch_assoc($Material5);
$totalRows_Material5 = mysql_num_rows($Material5);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material6 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 6
";
$Material6 = mysql_query($query_Material6, $SmecelNovo) or die(mysql_error());
$row_Material6 = mysql_fetch_assoc($Material6);
$totalRows_Material6 = mysql_num_rows($Material6);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material7 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 7
";
$Material7 = mysql_query($query_Material7, $SmecelNovo) or die(mysql_error());
$row_Material7 = mysql_fetch_assoc($Material7);
$totalRows_Material7 = mysql_num_rows($Material7);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material8 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 8
";
$Material8 = mysql_query($query_Material8, $SmecelNovo) or die(mysql_error());
$row_Material8 = mysql_fetch_assoc($Material8);
$totalRows_Material8 = mysql_num_rows($Material8);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material9 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_UsuLogado[usu_sec]' AND material_tipo = 9
";
$Material9 = mysql_query($query_Material9, $SmecelNovo) or die(mysql_error());
$row_Material9 = mysql_fetch_assoc($Material9);
$totalRows_Material9 = mysql_num_rows($Material9);


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
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>

  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">MATERIAL DE APOIO</h1>
      <!-- CONTEÚDO -->


      <ul class="ls-tabs-nav">
        <li class="ls-active"><a data-ls-module="tabs" href="#dcrm">DCRM (<?php echo $totalRows_Material1; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#livros">LIVROS (<?php echo $totalRows_Material2; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#plan">PLANEJAMENTO ANUAL (<?php echo $totalRows_Material3; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#edinf">EDUCAÇÃO INFANTIL (<?php echo $totalRows_Material5; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#anosini">ANOS INICIAIS (<?php echo $totalRows_Material6; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#anosfim">ANOS FINAIS (<?php echo $totalRows_Material7; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#eja">EJA (<?php echo $totalRows_Material8; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#campo">EDUCAÇÃO DO CAMPO (<?php echo $totalRows_Material9; ?>)</a></li>
        <li><a data-ls-module="tabs" href="#outros">DIVERSOS (<?php echo $totalRows_Material4; ?>)</a></li>
      </ul>
      <div class="ls-tabs-container">
        <div id="dcrm" class="ls-tab-content ls-active">
          <p>
            <?php if ($totalRows_Material1 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                  <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
                  <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
                  <th width="120" class="ls-txt-center"></th>
                </tr>
              </thead>

              <tbody>
                <?php do { ?>
                  <tr>
                    <td><a href="../../material_apoio/<?php echo $row_Material1['material_link']; ?>" target="_blank"><span
                          class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material1['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material1['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material1['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material1['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material1['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material1['disciplina_nome']; ?>
                      <?php } ?>
                    </td>
                    <td class="ls-txt-center">
                      <?php if ($row_Material1['material_painel_escola'] == "S") {
                        echo "SIM";
                      } else {
                        echo "NÃO";
                      } ?>
                    </td>
                    <td class="ls-txt-center">
                      <?php if ($row_Material1['material_painel_professor'] == "S") {
                        echo "SIM";
                      } else {
                        echo "NÃO";
                      } ?>
                    </td>
                    <td class="ls-txt-right">
                      <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                        <ul class="ls-dropdown-nav">
                          <li><a href="material_apoio.php?material=<?php echo $row_Material1['material_hash']; ?>"
                              class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                        </ul>
                      </div>


                    </td>

                  </tr>

                <?php } while ($row_Material1 = mysql_fetch_assoc($Material1)); ?>
                <tr>
                  <td colspan="6">
                    <p><small><strong><?php echo $totalRows_Material1; ?></strong> arquivo(s) enviado(s).</small></p>
                  </td>
                </tr>
              </tbody>

            </table>
          <?php } else { ?>
            Nenhum arquivo adicionado
          <?php } ?>
          </p>
        </div>

        <div id="livros" class="ls-tab-content">
          <p>
            <?php if ($totalRows_Material2 > 0) { ?>
            <table class="ls-table ls-sm-space">
              <thead>

                <tr>
                  <th width="50"></th>
                  <th>TÍTULO</th>
                  <th width="220">ETAPA</th>
                  <th>COMP/CAMPO EXP.</th>
                  <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
                  <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
                  <th width="120" class="ls-txt-center"></th>
                </tr>
              </thead>

              <tbody>
                <?php do { ?>
                  <tr>
                    <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material2['material_link']; ?>"
                        target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                    <td><strong><?php echo $row_Material2['material_titulo']; ?></strong> <br>
                      <i><?php echo $row_Material2['material_descricao']; ?></i>
                    </td>
                    <td><?php if ($row_Material2['etapa_nome_abrev'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material2['etapa_nome_abrev']; ?>
                      <?php } ?>
                    </td>
                    <td><?php if ($row_Material2['disciplina_nome'] == "") { ?>
                        SEM CRITÉRIOS
                      <?php } else { ?>
                        <?php echo $row_Material2['disciplina_nome']; ?>
                      <?php } ?>
                    </td>
                    <td class="ls-txt-center">
                      <?php if ($row_Material2['material_painel_escola'] == "S") {
                        echo "SIM";
                      } else {
                        echo "NÃO";
                      } ?>
                    </td>
                    <td class="ls-txt-center">
                      <?php if ($row_Material2['material_painel_professor'] == "S") {
                        echo "SIM";
                      } else {
                        echo "NÃO";
                      } ?>
                    </td>
                    <td class="ls-txt-right">
                      <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                        <ul class="ls-dropdown-nav">
                          <li><a href="material_apoio.php?material=<?php echo $row_Material2['material_hash']; ?>"
                              class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                        </ul>
                      </div>
            </div>

            </td>

            </tr>

          <?php } while ($row_Material2 = mysql_fetch_assoc($Material2)); ?>
          <tr>
            <td colspan="6">
              <p><small><strong><?php echo $totalRows_Material2; ?></strong> arquivo(s) enviado(s).</small></p>
            </td>
          </tr>
          </tbody>

          </table>
        <?php } else { ?>
          Nenhum arquivo adicionado
        <?php } ?>
        </p>
      </div>

      <div id="plan" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material3 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead) <tr>
            <th width="50"></th>
            <th>TÍTULO</th>
            <th width="220">ETAPA</th>
            <th>COMP/CAMPO EXP.</th>
            <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
            <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
            <th width="120" class="ls-txt-center"></th>
            </tr>
            </thead>

            <tbody>
              <?php do { ?>
                <tr>
                  <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material3['material_link']; ?>"
                      target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                  <td><strong><?php echo $row_Material3['material_titulo']; ?></strong> <br>
                    <i><?php echo $row_Material3['material_descricao']; ?></i>
                  </td>
                  <td><?php if ($row_Material3['etapa_nome_abrev'] == "") { ?>
                      SEM CRITÉRIOS
                    <?php } else { ?>
                      <?php echo $row_Material3['etapa_nome_abrev']; ?>
                    <?php } ?>
                  </td>
                  <td><?php if ($row_Material3['disciplina_nome'] == "") { ?>
                      SEM CRITÉRIOS
                    <?php } else { ?>
                      <?php echo $row_Material3['disciplina_nome']; ?>
                    <?php } ?>
                  </td>
                  <td class="ls-txt-center">
                    <?php if ($row_Material3['material_painel_escola'] == "S") {
                      echo "SIM";
                    } else {
                      echo "NÃO";
                    } ?></td>
                  <td class="ls-txt-center">
                    <?php if ($row_Material3['material_painel_professor'] == "S") {
                      echo "SIM";
                    } else {
                      echo "NÃO";
                    } ?></td>
                  <td class="ls-txt-right">
                    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                      <ul class="ls-dropdown-nav">
                        <li><a href="material_apoio.php?material=<?php echo $row_Material3['material_hash']; ?>"
                            class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                      </ul>
                    </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material3 = mysql_fetch_assoc($Material3)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material3; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>

    <div id="outros" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material4 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead) <tr>
            <th width="50"></th>
            <th>TÍTULO</th>
            <th width="220">ETAPA</th>
            <th>COMP/CAMPO EXP.</th>
            <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
            <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
            <th width="120" class="ls-txt-center"></th>
            </tr>
            </thead>

            <tbody>
              <?php do { ?>
                <tr>
                  <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material4['material_link']; ?>"
                      target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                  <td><strong><?php echo $row_Material4['material_titulo']; ?></strong> <br>
                    <i><?php echo $row_Material4['material_descricao']; ?></i>
                  </td>
                  <td><?php if ($row_Material4['etapa_nome_abrev'] == "") { ?>
                      SEM CRITÉRIOS
                    <?php } else { ?>
                      <?php echo $row_Material4['etapa_nome_abrev']; ?>
                    <?php } ?>
                  </td>
                  <td><?php if ($row_Material4['disciplina_nome'] == "") { ?>
                      SEM CRITÉRIOS
                    <?php } else { ?>
                      <?php echo $row_Material4['disciplina_nome']; ?>
                    <?php } ?>
                  </td>
                  <td class="ls-txt-center">
                    <?php if ($row_Material4['material_painel_escola'] == "S") {
                      echo "SIM";
                    } else {
                      echo "NÃO";
                    } ?></td>
                  <td class="ls-txt-center">
                    <?php if ($row_Material4['material_painel_professor'] == "S") {
                      echo "SIM";
                    } else {
                      echo "NÃO";
                    } ?></td>
                  <td class="ls-txt-right">
                    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                      <ul class="ls-dropdown-nav">
                        <li><a href="material_apoio.php?material=<?php echo $row_Material4['material_hash']; ?>"
                            class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                      </ul>
                    </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material4 = mysql_fetch_assoc($Material4)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material4; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>

    <div id="edinf" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material5 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>

            <tr>
              <th width="50"></th>
              <th>TÍTULO</th>
              <th width="220">ETAPA</th>
              <th>COMP/CAMPO EXP.</th>
              <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
              <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
              <th width="120" class="ls-txt-center"></th>
            </tr>
          </thead>

          <tbody>
            <?php do { ?>
              <tr>
                <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material5['material_link']; ?>"
                    target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                <td><strong><?php echo $row_Material5['material_titulo']; ?></strong> <br>
                  <i><?php echo $row_Material5['material_descricao']; ?></i>
                </td>
                <td><?php if ($row_Material5['etapa_nome_abrev'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material5['etapa_nome_abrev']; ?>
                  <?php } ?>
                </td>
                <td><?php if ($row_Material5['disciplina_nome'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material5['disciplina_nome']; ?>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Material5['material_painel_escola'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Material5['material_painel_professor'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-right">
                  <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                    <ul class="ls-dropdown-nav">
                      <li><a href="material_apoio.php?material=<?php echo $row_Material5['material_hash']; ?>"
                          class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                    </ul>
                  </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material5 = mysql_fetch_assoc($Material5)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material5; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>

    <div id="anosfim" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material6 > 6) { ?>
        <table class="ls-table ls-sm-space">
          <thead>

            <tr>
              <th width="50"></th>
              <th>TÍTULO</th>
              <th width="220">ETAPA</th>
              <th>COMP/CAMPO EXP.</th>
              <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
              <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
              <th width="120" class="ls-txt-center"></th>
            </tr>
          </thead>

          <tbody>
            <?php do { ?>
              <tr>
                <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material7['material_link']; ?>"
                    target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                <td><strong><?php echo $row_Material7['material_titulo']; ?></strong> <br>
                  <i><?php echo $row_Material7['material_descricao']; ?></i>
                </td>
                <td><?php if ($row_Material7['etapa_nome_abrev'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material7['etapa_nome_abrev']; ?>
                  <?php } ?>
                </td>
                <td><?php if ($row_Material7['disciplina_nome'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material7['disciplina_nome']; ?>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Material7['material_painel_escola'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Material7['material_painel_professor'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-right">
                  <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                    <ul class="ls-dropdown-nav">
                      <li><a href="material_apoio.php?material=<?php echo $row_Material7['material_hash']; ?>"
                          class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                    </ul>
                  </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material7 = mysql_fetch_assoc($Material7)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material7; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>

    <div id="anosini" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material6 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>

            <tr>
              <th width="50"></th>
              <th>TÍTULO</th>
              <th width="220">ETAPA</th>
              <th>COMP/CAMPO EXP.</th>
              <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
              <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
              <th width="120" class="ls-txt-center"></th>
            </tr>
          </thead>

          <tbody>
            <?php do { ?>
              <tr>
                <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material6['material_link']; ?>"
                    target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                <td><strong><?php echo $row_Material6['material_titulo']; ?></strong> <br>
                  <i><?php echo $row_Material6['material_descricao']; ?></i>
                </td>
                <td><?php if ($row_Material6['etapa_nome_abrev'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material6['etapa_nome_abrev']; ?>
                  <?php } ?>
                </td>
                <td><?php if ($row_Material6['disciplina_nome'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material6['disciplina_nome']; ?>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Material6['material_painel_escola'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Material6['material_painel_professor'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-right">
                  <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                    <ul class="ls-dropdown-nav">
                      <li><a href="material_apoio.php?material=<?php echo $row_Material6['material_hash']; ?>"
                          class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                    </ul>
                  </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material6 = mysql_fetch_assoc($Material6)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material6; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>

    <div id="eja" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material8 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>

            <tr>
              <th width="50"></th>
              <th>TÍTULO</th>
              <th width="220">ETAPA</th>
              <th>COMP/CAMPO EXP.</th>
              <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
              <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
              <th width="120" class="ls-txt-center"></th>
            </tr>
          </thead>

          <tbody>
            <?php do { ?>
              <tr>
                <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material8['material_link']; ?>"
                    target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                <td><strong><?php echo $row_Material8['material_titulo']; ?></strong> <br>
                  <i><?php echo $row_Material8['material_descricao']; ?></i>
                </td>
                <td><?php if ($row_Material8['etapa_nome_abrev'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material8['etapa_nome_abrev']; ?>
                  <?php } ?>
                </td>
                <td><?php if ($row_Material8['disciplina_nome'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material8['disciplina_nome']; ?>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Material8['material_painel_escola'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Material8['material_painel_professor'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-right">
                  <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                    <ul class="ls-dropdown-nav">
                      <li><a href="material_apoio.php?material=<?php echo $row_Material8['material_hash']; ?>"
                          class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                    </ul>
                  </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material8 = mysql_fetch_assoc($Material8)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material8; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>


    <div id="campo" class="ls-tab-content">
      <p>
        <?php if ($totalRows_Material9 > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>

            <tr>
              <th width="50"></th>
              <th>TÍTULO</th>
              <th width="220">ETAPA</th>
              <th>COMP/CAMPO EXP.</th>
              <th width="120" class="ls-txt-center">PAINEL ESCOLA</th>
              <th width="120" class="ls-txt-center">PAINEL PROFESSOR</th>
              <th width="120" class="ls-txt-center"></th>
            </tr>
          </thead>

          <tbody>
            <?php do { ?>
              <tr>
                <td class="ls-txt-center"><a href="../../material_apoio/<?php echo $row_Material9['material_link']; ?>"
                    target="_blank"><span class="ls-ico-cloud-download"></span></a></td>
                <td><strong><?php echo $row_Material9['material_titulo']; ?></strong> <br>
                  <i><?php echo $row_Material9['material_descricao']; ?></i>
                </td>
                <td><?php if ($row_Material9['etapa_nome_abrev'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material9['etapa_nome_abrev']; ?>
                  <?php } ?>
                </td>
                <td><?php if ($row_Material9['disciplina_nome'] == "") { ?>
                    SEM CRITÉRIOS
                  <?php } else { ?>
                    <?php echo $row_Material9['disciplina_nome']; ?>
                  <?php } ?>
                </td>
                <td class="ls-txt-center">
                  <?php if ($row_Material9['material_painel_escola'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Material9['material_painel_professor'] == "S") {
                    echo "SIM";
                  } else {
                    echo "NÃO";
                  } ?></td>
                <td class="ls-txt-right">
                  <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-danger"></a>
                    <ul class="ls-dropdown-nav">
                      <li><a href="material_apoio.php?material=<?php echo $row_Material9['material_hash']; ?>"
                          class=""><span class="ls-ico-remove ls-color-danger ls-txt-right">EXCLUIR</span></a></li>
                    </ul>
                  </div>
        </div>

        </td>

        </tr>

      <?php } while ($row_Material9 = mysql_fetch_assoc($Material9)); ?>
      <tr>
        <td colspan="6">
          <p><small><strong><?php echo $totalRows_Material9; ?></strong> arquivo(s) enviado(s).</small></p>
        </td>
      </tr>
      </tbody>

      </table>
    <?php } else { ?>
      Nenhum arquivo adicionado
    <?php } ?>
    </p>
    </div>
    </div>

    </div>
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
        <li class="ls-txt-center hidden-xs">
          <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
        </li>
        <li><a href="#">&gt; Guia</a></li>
        <li><a href="#">&gt; Wiki</a></li>
      </ul>
    </nav>
  </aside>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>