<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,99";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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



require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Componentes = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, disciplina_diversificada, disciplina_id_campos_exp, disciplina_ata FROM smc_disciplina ORDER BY disciplina_nome ASC";
$Componentes = mysql_query($query_Componentes, $SmecelNovo) or die(mysql_error());
$row_Componentes = mysql_fetch_assoc($Componentes);
$totalRows_Componentes = mysql_num_rows($Componentes);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Material1 = "
SELECT material_id, material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash,
etapa_id, etapa_nome, etapa_nome_abrev, disciplina_id, disciplina_nome, disciplina_nome_abrev 
FROM smc_material_apoio
LEFT JOIN smc_etapa ON etapa_id = material_etapa 
LEFT JOIN smc_disciplina ON disciplina_id = material_componente
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 1
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 2
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 3
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 4
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 5
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 6
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 7
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 8
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
WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_tipo = 9
";
$Material9 = mysql_query($query_Material9, $SmecelNovo) or die(mysql_error());
$row_Material9 = mysql_fetch_assoc($Material9);
$totalRows_Material9 = mysql_num_rows($Material9);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {



  include('../funcoes/class.upload.php');

  $handle = new Upload($_FILES['material_link']);

  if ($handle->uploaded) {

    $nome = md5(date('YmdHis') . $row_Secretaria['sec_id']);
    $hash = md5($nome . $row_UsuarioLogado['usu_id']);


    $handle->allowed = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-excel', 'image/*');
    $handle->file_new_name_body = $nome;
    $handle->mime_check = true;
    $handle->file_max_size = '941605094';
    $handle->Process('../../material_apoio/');

    if ($handle->processed) {

      $nome_do_arquivo = $handle->file_dst_name;


      $insertSQL = sprintf(
        "INSERT INTO smc_material_apoio (material_id_sec, material_tipo, material_painel_escola, material_painel_professor, material_link, material_titulo, material_descricao, material_etapa, material_componente, material_hash) VALUES ('$row_Secretaria[sec_id]', %s, %s, %s, '$nome_do_arquivo', %s, %s, %s, %s, '$hash')",
        //GetSQLValueString($_POST['material_id_sec'], "int"),
        GetSQLValueString($_POST['material_tipo'], "int"),
        GetSQLValueString(isset($_POST['material_painel_escola']) ? "true" : "", "defined", "'S'", "'N'"),
        GetSQLValueString(isset($_POST['material_painel_professor']) ? "true" : "", "defined", "'S'", "'N'"),
        //GetSQLValueString($_POST['material_link'], "text"),
        GetSQLValueString($_POST['material_titulo'], "text"),
        GetSQLValueString($_POST['material_descricao'], "text"),
        GetSQLValueString($_POST['material_etapa'], "int"),
        GetSQLValueString($_POST['material_componente'], "int")
      );

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());


      $insertGoTo = "material_apoio.php?cadastrado";
      if (isset($_SERVER['QUERY_STRING'])) {
        //$insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
        //$insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $insertGoTo));
    } else {
      echo '<span class="alert panel">';
      echo ' Erro ao enviar arquivo: ' . $handle->error . '';
      echo '</span>';
    }
  }

}


if ((isset($_GET['material'])) && ($_GET['material'] != "")) {
  $deleteSQL = sprintf(
    "DELETE FROM smc_material_apoio WHERE material_id_sec = '$row_Secretaria[sec_id]' AND material_hash=%s",
    GetSQLValueString($_GET['material'], "text")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "material_apoio.php?deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

?>
<!DOCTYPE html>
<html class="ls-theme-green" lang="pt_BR">

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
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu_top.php"); ?>
  <?php include_once "menu.php"; ?>
  <DIV class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">MATERIAL DE APOIO</h1>
      <!-- CONTEUDO -->

      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary"><span
          class="ls-ico-plus ls-ico-left"></span> CADASTRAR MATERIAL DE APOIO</button>
      <hr>
      <?php if (isset($_GET["cadastrado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Documento ENVIADO com sucesso! </div>
      <?php } ?>
      <?php if (isset($_GET["deletado"])) { ?>
        <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Documento EXCLUÍDO com sucesso! </div>
      <?php } ?>
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
                      } ?></td>
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
                    } ?></td>
                  <td class="ls-txt-center">
                    <?php if ($row_Material2['material_painel_professor'] == "S") {
                      echo "SIM";
                    } else {
                      echo "NÃO";
                    } ?></td>
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

    <p>&nbsp;</p>
    <!-- CONTEUDO -->
    </div>
    </div>
  </main>
  <?php include_once "notificacoes.php"; ?>
  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-box1 ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">CADASTRO DE MATERIAL</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <form method="post" name="form1" enctype="multipart/form-data" action="<?php echo $editFormAction; ?>"
          class="ls-form-horizontal row">
          <div class="ls-label col-md-12">
            <p>DISPONÍVEL EM:</p>
            <label class="ls-label-text">
              <input type="checkbox" name="material_painel_escola" value="" checked>
              PAINEL DA ESCOLA </label>
            <label class="ls-label-text">
              <input type="checkbox" name="material_painel_professor" value="" checked>
              PAINEL DO PROFESSOR </label>
          </div>
          <div class="ls-label col-md-12"> <b class="ls-label-text">TIPO DE MATERIAL</b>
            <div class="ls-custom-select">
              <select name="material_tipo" class="ls-select" required>
                <option value="">-</option>
                <option value="1" <?php if (!(strcmp(1, ""))) {
                  echo "SELECTED";
                } ?>>DCRM</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {
                  echo "SELECTED";
                } ?>>LIVRO DIGITAL</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {
                  echo "SELECTED";
                } ?>>PLANEJAMENTO ANUAL</option>
                <option value="5" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>EDUCAÇÃO INFANTIL</option>
                <option value="6" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>ANOS INICIAIS</option>
                <option value="7" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>ANOS FINAIS</option>
                <option value="8" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>EJA</option>
                <option value="9" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>EDUCAÇÃO DO CAMPO</option>
                <option value="4" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>OUTROS</option>
              </select>
            </div>
          </div>
          <label class="ls-label col-md-12"> <b class="ls-label-text">TÍTULO DO ARQUIVO</b>
            <input type="text" name="material_titulo" value="" size="32" required>
          </label>
          <div class="ls-label col-md-12">
            <label class="ls-label"> <b class="ls-label-text">DESCRIÇÃO</b>
              <textarea name="material_descricao" rows="2"></textarea>
            </label>
          </div>
          <div class="ls-label col-md-6"> <b class="ls-label-text">ETAPA</b>
            <div class="ls-custom-select">
              <select name="material_etapa" class="ls-select">
                <option value="">TODAS</option>
                <?php do { ?>
                  <option value="<?php echo $row_Etapas['etapa_id'] ?>"><?php echo $row_Etapas['etapa_nome'] ?></option>
                <?php } while ($row_Etapas = mysql_fetch_assoc($Etapas)); ?>
              </select>
            </div>
          </div>
          <div class="ls-label col-md-6"> <b class="ls-label-text">COMPONENTE/CAMPO DE EXPERIÊNCIA</b>
            <div class="ls-custom-select">
              <select name="material_componente" class="ls-select">
                <option value="">TODOS</option>
                <?php do { ?>
                  <option value="<?php echo $row_Componentes['disciplina_id'] ?>">
                    <?php echo $row_Componentes['disciplina_nome'] ?></option>
                <?php } while ($row_Componentes = mysql_fetch_assoc($Componentes)); ?>
              </select>
            </div>
          </div>
          <label class="ls-label col-md-12"> <br>
            <b class="ls-label-text">ARQUIVO</b>
            <input type="file" name="material_link" value="" size="32" required>
          </label>
          <div class="ls-label col-md-12">
            <div class="ls-actions-btn">
              <input class="ls-btn-primary enviar" type="submit" value="ENVIAR">
            </div>
          </div>
          <input type="hidden" name="material_id_sec" value="<?php echo $row_Secretaria['sec_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
        </form>
      </div>
      <div class="ls-modal-footer">
        <button class="ls-btn-primary ls-float-right" data-dismiss="modal">FECHAR</button>
        <button class="ls-btn-primary" style="visibility:hidden">FECHAR</button>
      </div>
    </div>
  </div>
  <!-- /.modal -->

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script>

    jQuery(document).ready(function ($) {
      jQuery('.enviar').click(function () {
        //jQuery( this ).submit();
        //jQuery( this ).attr( 'disabled', true );
        jQuery(this).attr('value', 'Enviando...');
        //jQuery( this ).submit();
      });
    });

  </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Etapas);

mysql_free_result($Componentes);

mysql_free_result($Material1);
?>