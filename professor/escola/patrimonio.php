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
$query_Grupo = "SELECT patrimonio_grupo_bens_id, patrimonio_grupo_bens_descricao FROM smc_patrimonio_grupo_bens ORDER BY patrimonio_grupo_bens_descricao ASC";
$Grupo = mysql_query($query_Grupo, $SmecelNovo) or die(mysql_error());
$row_Grupo = mysql_fetch_assoc($Grupo);
$totalRows_Grupo = mysql_num_rows($Grupo);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema FROM smc_escola WHERE escola_situacao = '1' AND escola_id_sec = '$row_UsuLogado[usu_sec]' AND escola_id <> '$row_UsuLogado[usu_escola]' ORDER  BY escola_nome ASC";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$colname_Transferir = "-1";
if (isset($_GET['transferir'])) {
  $colname_Transferir = $_GET['transferir'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Transferir = sprintf("SELECT patrimonio_item_id, patrimonio_item_escola_id, patrimonio_item_descricao, patrimonio_item_grupo_id, patrimonio_item_etiqueta, patrimonio_item_num_serie, patrimonio_item_marca, patrimonio_item_modelo, patrimonio_item_cor, patrimonio_item_dimencoes, patrimonio_item_situacao, patrimonio_item_observacoes FROM smc_patrimonio_item WHERE patrimonio_item_id = %s", GetSQLValueString($colname_Transferir, "int"));
$Transferir = mysql_query($query_Transferir, $SmecelNovo) or die(mysql_error());
$row_Transferir = mysql_fetch_assoc($Transferir);
$totalRows_Transferir = mysql_num_rows($Transferir);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Listar = "
SELECT 
    pi.patrimonio_item_id, 
    pi.patrimonio_item_escola_id, 
    pi.patrimonio_item_descricao, 
    pi.patrimonio_item_grupo_id, 
    pi.patrimonio_item_etiqueta, 
    pi.patrimonio_item_num_serie, 
    pi.patrimonio_item_marca, 
    pi.patrimonio_item_modelo, 
    pi.patrimonio_item_cor, 
    pi.patrimonio_item_dimencoes, 
    pi.patrimonio_item_situacao, 
    pi.patrimonio_item_observacoes, 
    pi.patrimonio_item_localizacao, 
    pi.patrimonio_item_quantidade, 
    pi.patrimonio_item_aquisicao, 
    pi.patrimonio_fonte_recursos, 
    pg.patrimonio_grupo_bens_id, 
    pg.patrimonio_grupo_bens_descricao, 
    CASE pi.patrimonio_item_situacao
        WHEN 1 THEN 'EM USO'
        WHEN 2 THEN 'OCIOSO'
        WHEN 3 THEN 'ANTIECONÔMICO'
        WHEN 4 THEN 'RECUPERÁVEL'
        WHEN 5 THEN 'INSERVÍVEL'
        WHEN 6 THEN 'BOM'
        WHEN 7 THEN 'REGULAR'
        ELSE 'NÃO INFORMADO'
    END AS situacao_texto
FROM smc_patrimonio_item pi
INNER JOIN smc_patrimonio_grupo_bens pg 
    ON pg.patrimonio_grupo_bens_id = pi.patrimonio_item_grupo_id
WHERE pi.patrimonio_item_escola_id = '".mysql_real_escape_string($row_UsuLogado['usu_escola'])."'
ORDER BY pi.patrimonio_item_id DESC
";

$Listar = mysql_query($query_Listar, $SmecelNovo) or die(mysql_error());
$row_Listar = mysql_fetch_assoc($Listar);
$totalRows_Listar = mysql_num_rows($Listar);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: patrimonio.php?permissao"));
    die();
  }

  $escola_id = $row_UsuLogado['usu_escola'];
  
  $insertSQL = sprintf(
    "INSERT INTO smc_patrimonio_item (
  patrimonio_item_escola_id, 
  patrimonio_item_descricao, 
  patrimonio_item_grupo_id, 
  patrimonio_item_etiqueta, 
  patrimonio_item_num_serie, 
  patrimonio_item_marca, 
  patrimonio_item_modelo, 
  patrimonio_item_cor, 
  patrimonio_item_dimencoes, 
  patrimonio_item_situacao, 
  patrimonio_item_localizacao, 
  patrimonio_item_quantidade, 
  patrimonio_item_aquisicao, 
  patrimonio_fonte_recursos, 
  patrimonio_item_observacoes
) VALUES (
  '$escola_id', 
  %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
)",
    GetSQLValueString($_POST['patrimonio_item_descricao'], "text"),
    GetSQLValueString($_POST['patrimonio_item_grupo_id'], "int"),
    GetSQLValueString($_POST['patrimonio_item_etiqueta'], "text"),
    GetSQLValueString($_POST['patrimonio_item_num_serie'], "text"),
    GetSQLValueString($_POST['patrimonio_item_marca'], "text"),
    GetSQLValueString($_POST['patrimonio_item_modelo'], "text"),
    GetSQLValueString($_POST['patrimonio_item_cor'], "text"),
    GetSQLValueString($_POST['patrimonio_item_dimencoes'], "text"),
    GetSQLValueString($_POST['patrimonio_item_situacao'], "int"),
    GetSQLValueString($_POST['patrimonio_item_localizacao'], "int"),
    GetSQLValueString($_POST['patrimonio_item_quantidade'], "int"),
    GetSQLValueString(date('Y-m-d', strtotime($_POST['patrimonio_item_aquisicao'])), "date"),
    GetSQLValueString($_POST['patrimonio_fonte_recursos'], "text"),
    GetSQLValueString($_POST['patrimonio_item_observacoes'], "text")
  );


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  //$id_item = "LAST_INSERT_ID()";
  $data_item = date('Y-m-d');
  $escola_item = $row_UsuLogado['usu_escola'];
  $funcionario_item = $row_UsuLogado['usu_id'];
  $tipo = "1";

  $insertSQLMovimento = "INSERT INTO smc_patrimonio_movimento (patrimonio_movimento_item_id, patrimonio_movimento_tipo, patrimonio_movimento_escola_id, patrimonio_movimento_data_movimento, patrimonio_movimento_funcionario_id) VALUES (LAST_INSERT_ID(), '$tipo', '$escola_item', '$data_item', '$funcionario_item')";
  $Result2 = mysql_query($insertSQLMovimento, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "patrimonio.php?cadastrado";
  //if (isset($_SERVER['QUERY_STRING'])) {
  //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
  //$insertGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $insertGoTo));
}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {

  if ($row_UsuLogado['usu_update'] == "N") {
    header(sprintf("Location: patrimonio.php?permissao"));
    die();
  }

  $updateSQL = sprintf(
    "UPDATE smc_patrimonio_item SET patrimonio_item_escola_id=%s WHERE patrimonio_item_id=%s",
    GetSQLValueString($_POST['patrimonio_item_escola_id'], "int"),
    GetSQLValueString($_POST['patrimonio_item_id'], "int")
  );

  $id_item = $_POST['patrimonio_item_id'];
  $data_item = date('Y-m-d');
  $escola_item = $row_UsuLogado['usu_escola'];
  $funcionario_item = $row_UsuLogado['usu_id'];
  $tipo = "2";

  $insertSQLMovimento3 = "INSERT INTO smc_patrimonio_movimento (patrimonio_movimento_item_id, patrimonio_movimento_tipo, patrimonio_movimento_escola_id, patrimonio_movimento_data_movimento, patrimonio_movimento_funcionario_id) VALUES ('$id_item', '$tipo', '$escola_item', '$data_item', '$funcionario_item')";
  $Result3 = mysql_query($insertSQLMovimento3, $SmecelNovo) or die(mysql_error());

  $insertSQLMovimento4 = "INSERT INTO smc_patrimonio_movimento (patrimonio_movimento_item_id, patrimonio_movimento_tipo, patrimonio_movimento_escola_id, patrimonio_movimento_data_movimento, patrimonio_movimento_funcionario_id) VALUES ('$id_item', '1', '$_POST[patrimonio_item_escola_id]', '$data_item', '$funcionario_item')";
  $Result4 = mysql_query($insertSQLMovimento4, $SmecelNovo) or die(mysql_error());


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "patrimonio.php?transferido";
  //if (isset($_SERVER['QUERY_STRING'])) {
  //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
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
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main ">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">PATRIMÔNIO ESCOLAR</h1>
      <!-- CONTEÚDO -->
      <p><button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">CADASTRAR NOVO
          ÍTEM</button>
        <a href="patrimonio_transacoes.php" class="ls-btn-primary">TRANSAÇÕES</a>
        <a href="patrimonio_print.php" target="_blank" class="ls-btn-primary">IMPRIMIR</a>
      </p>

      <?php if (isset($_GET["transferido"])) { ?>
        <div class="ls-alert-success">Ítem transferido com sucesso!</div>
      <?php } ?>

      <?php if (isset($_GET["cadastrado"])) { ?>
        <div class="ls-alert-success">Ítem cadastrado com sucesso!</div>
      <?php } ?>

      <?php if (isset($_GET["permissao"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
        </div>
      <?php } ?>


      <?php if ($totalRows_Listar > 0) { ?>
        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th width="50" class="ls-txt-center">Nº</th>
              <th>DESCRIÇÃO</th>
              <th class="ls-txt-center">GRUPO</th>
              <th class="ls-txt-center">SITUAÇÃO</th>
              <th width="50"></th>
              <th width="50"></th>
            </tr>
          </thead>
          <?php $num = 1; ?>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><strong><?php echo $num; ?></strong></td>
              <td><?php echo $row_Listar['patrimonio_item_descricao']; ?></td>
              <td class="ls-txt-center"><?php echo $row_Listar['patrimonio_grupo_bens_descricao']; ?></td>
              <td class="ls-txt-center"><?php echo $row_Listar['patrimonio_item_situacao']; ?></td>
              <td><a href="patrimonio_editar.php?codigo=<?php echo $row_Listar['patrimonio_item_id']; ?>"
                  class="ls-ico-pencil2" title="Editar"></a></td>
              <td><a href="patrimonio.php?transferir=<?php echo $row_Listar['patrimonio_item_id']; ?>"
                  class="ls-ico-download2" title="Transferir ítem"></a></td>
            </tr>
            <?php $num++; ?>
          <?php } while ($row_Listar = mysql_fetch_assoc($Listar)); ?>
        </table>
      <?php } else { ?>

        <div class="ls-alert-info">Nenhum ítem cadastrado.</div>

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

  <div class="ls-modal" id="myAwesomeModal">
    <div class="ls-modal-large">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">CADASTRAR NOVO ÍTEM</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">

        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
          <label class="ls-label col-md-12">
            <b class="ls-label-text">DESCRIÇÃO DO BEM</b>
            <input type="text" name="patrimonio_item_descricao" value="" size="32" required>
          </label>

          <label class="ls-label col-md-4">
            <b class="ls-label-text">Nº TOMBAMENTO</b>
            <input type="text" name="patrimonio_item_etiqueta" value="" size="32">
          </label>
          <label class="ls-label col-md-4">
            <b class="ls-label-text">Nº SÉRIE</b>
            <input type="text" name="patrimonio_item_num_serie" value="" size="32">
          </label>
          <label class="ls-label col-md-4">
            <b class="ls-label-text">MARCA</b>
            <input type="text" name="patrimonio_item_marca" value="" size="32">
          </label>
          <label class="ls-label col-md-4">
            <b class="ls-label-text">MODELO</b>
            <input type="text" name="patrimonio_item_modelo" value="" size="32">
          </label>
          <label class="ls-label col-md-4">
            <b class="ls-label-text">COR</b>
            <input type="text" name="patrimonio_item_cor" value="" size="32">
          </label>
          <label class="ls-label col-md-4">
            <b class="ls-label-text">DIMENSÕES</b>
            <input type="text" name="patrimonio_item_dimencoes" value="" size="32">
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">GRUPO DO BEM</b>
            <div class="ls-custom-select">
              <select name="patrimonio_item_grupo_id" class="ls-select" required>
                <option value="">ESCOLHA</option>
                <?php do { ?>
                  <option value="<?php echo $row_Grupo['patrimonio_grupo_bens_id'] ?>">
                    <?php echo $row_Grupo['patrimonio_grupo_bens_descricao'] ?></option>
                <?php } while ($row_Grupo = mysql_fetch_assoc($Grupo)); ?>
              </select>
            </div>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">SITUAÇÃO</b>
            <div class="ls-custom-select">
              <select name="patrimonio_item_situacao" class="ls-select" required>
                <option value="">ESCOLHA</option>
                <option value="1" <?php if (!(strcmp(1, ""))) {
                  echo "SELECTED";
                } ?>>1 - EM USO</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {
                  echo "SELECTED";
                } ?>>2 - OCIOSO</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {
                  echo "SELECTED";
                } ?>>3 - ANTIECONOMICO</option>
                <option value="4" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>4 - RECUPERAVEL</option>
                <option value="5" <?php if (!(strcmp(5, ""))) {
                  echo "SELECTED";
                } ?>>5 - INSERVIVEL</option>
                <option value="6" <?php if (!(strcmp(6, ""))) {
                  echo "SELECTED";
                } ?>>6 - BOM</option>
                <option value="7" <?php if (!(strcmp(7, ""))) {
                  echo "SELECTED";
                } ?>>7 - REGULAR</option>

              </select>
            </div>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">LOCALIZAÇÃO</b>
            <div class="ls-custom-select">
              <select name="patrimonio_item_localizacao" class="ls-select" required>
                <option value="">ESCOLHA</option>
                <option value="1" <?php if (!(strcmp(1, ""))) {
                  echo "SELECTED";
                } ?>>1 - DIRETORIA</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {
                  echo "SELECTED";
                } ?>>2 - SECRETARIA</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {
                  echo "SELECTED";
                } ?>>3 - SALA DE COORDENAÇÃO</option>
                <option value="4" <?php if (!(strcmp(4, ""))) {
                  echo "SELECTED";
                } ?>>4 - PÁTIO</option>
                <option value="5" <?php if (!(strcmp(5, ""))) {
                  echo "SELECTED";
                } ?>>5 - SALA DOS PROFESSORES</option>
                <option value="6" <?php if (!(strcmp(6, ""))) {
                  echo "SELECTED";
                } ?>>6 - SALA DE PSICOPEDAGOGIA</option>
                <option value="7" <?php if (!(strcmp(7, ""))) {
                  echo "SELECTED";
                } ?>>7 - LABORATÓRIO DE INFORMÁTICA
                </option>
                <option value="8" <?php if (!(strcmp(8, ""))) {
                  echo "SELECTED";
                } ?>>8 - SALA DE LEITURA</option>
                <option value="9" <?php if (!(strcmp(9, ""))) {
                  echo "SELECTED";
                } ?>>9 - SALA DE AULA</option>
                <option value="10" <?php if (!(strcmp(10, ""))) {
                  echo "SELECTED";
                } ?>>10 - ALMOXARIFADO</option>
                <option value="11" <?php if (!(strcmp(11, ""))) {
                  echo "SELECTED";
                } ?>>11 - COZINHA</option>
                <option value="12" <?php if (!(strcmp(12, ""))) {
                  echo "SELECTED";
                } ?>>12 - AUDITÓRIO</option>

              </select>
            </div>
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">AQUISIÇÃO DO BEM</b>
            <input type="text" name="patrimonio_item_aquisicao" class="datepicker" id="datepickerExample" placeholder="dd/mm/aaaa">
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">QUANTIDADE</b>
            <input type="number" name="patrimonio_item_quantidade" value="" size="32">
          </label>
          <label class="ls-label col-md-6">
            <b class="ls-label-text">FONTE DE RECURSOS</b>
            <input type="text" name="patrimonio_fonte_recursos" value="" size="32">
          </label>
          <label class="ls-label col-md-12">
            <b class="ls-label-text">OBSERVAÇÕES</b>
            <input type="text" name="patrimonio_item_dimencoes" value="" size="32">
          </label>

          <label class="ls-label col-md-12">
            <input type="submit" value="CADASTRAR" class="ls-btn-primary ls-float-left">
          </label>

          <input type="hidden" name="MM_insert" value="form1">

        </form>

      </div>
    </div>
  </div><!-- /.modal -->


  <div class="ls-modal" id="myAwesomeModalEditar">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">TRANSFERIR PARA OUTRA ESCOLA/SETOR</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">

        <p>Transferindo o ítem:</p>
        <div class="ls-alert-info"><?php echo $row_Transferir['patrimonio_item_descricao']; ?></div>

        <form method="post" name="form2" action="<?php echo $editFormAction; ?>">

          <label class="ls-label col-md-12">
            <b class="ls-label-text">ESCOLA/SETOR</b>
            <div class="ls-custom-select">
              <select name="patrimonio_item_escola_id" class="ls-select" required>
                <option value="">ESCOLHA</option>
                <?php do { ?>
                  <option value="<?php echo $row_Escolas['escola_id'] ?>"><?php echo $row_Escolas['escola_nome'] ?></option>
                <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
              </select>
            </div>
          </label>



          <div class="ls-modal-footer">
            <input type="submit" value="TRANSFERIR" class="ls-btn-primary">
            <a href="patrimonio.php" class="ls-btn ls-float-righ">CANCELAR</a>
          </div>


          <input type="hidden" name="patrimonio_item_id" value="<?php echo $row_Transferir['patrimonio_item_id']; ?>">
          <input type="hidden" name="MM_update" value="form2">
          <input type="hidden" name="patrimonio_item_id" value="<?php echo $row_Transferir['patrimonio_item_id']; ?>">
        </form>

      </div>
    </div>
  </div><!-- /.modal -->


  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

  <?php if (isset($_GET["transferir"])) { ?>
    <script>
      locastyle.modal.open("#myAwesomeModalEditar");
    </script>
  <?php } ?>


</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Grupo);

mysql_free_result($Escolas);

mysql_free_result($Transferir);

mysql_free_result($Listar);

mysql_free_result($EscolaLogada);
?>