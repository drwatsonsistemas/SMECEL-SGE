<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
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

  include "usuLogado.php";
  include "fnc/anoLetivo.php";
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  // Verificando permissões
  if ($row_UsuLogado['usu_insert'] == "N") {
    header(sprintf("Location: funcListar.php?permissao"));
    exit;
  }

  // Diretório onde os arquivos serão salvos
  $uploadDir = 'anexo_faltas/';
  $fileName = $_FILES['arquivo_anexo']['name'];
  $fileTmpName = $_FILES['arquivo_anexo']['tmp_name'];
  $fileSize = $_FILES['arquivo_anexo']['size'];
  $fileError = $_FILES['arquivo_anexo']['error'];

  $fileNewName = null; // Inicializa o nome do arquivo

  // Verifica se um arquivo foi anexado
  if (!empty($fileName)) {
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'docx', 'pdf');

    if (in_array($fileExt, $allowed) && $fileError === 0 && $fileSize < 5000000) {
      $fileNewName = uniqid('', true) . "." . $fileExt;
      $fileDestination = $uploadDir . $fileNewName;

      if (!move_uploaded_file($fileTmpName, $fileDestination)) {
        header("Location: faltasFuncionarioCadastrar.php?erro");
        exit;
      }
    } else {
      header("Location: faltasFuncionarioCadastrar.php?arquivoInvalido");
      exit;
    }
  }

  // Gerar o hash
  $hash = md5(date('Y-m-d H:i:s') . time() . $_POST['faltas_func_id_func'] . $_POST['faltas_func_id_funcao']);

  // Determinar a quantidade de faltas a registrar
  $quantidadeAulas = isset($_POST['quantidade_aulas']) ? (int) $_POST['quantidade_aulas'] : 1;

  // Se o tipo de falta for "DIA", registrar apenas uma falta
  if ($_POST['faltas_tipo_dia_aula'] == 1) { // Tipo "DIA"
    $quantidadeAulas = 1;
  }

  // Inserir os dados no banco de dados
  for ($i = 1; $i <= $quantidadeAulas; $i++) {
    $insertSQL = sprintf(
      "INSERT INTO smc_faltas_func (
                faltas_func_id_func,
                faltas_func_id_escola,
                faltas_func_id_funcao,
                faltas_func_data,
                faltas_func_tipo_jutificativa,
                faltas_func_obs,
                faltas_func_anexo,
                faltas_tipo_dia_aula,
                falta_hash
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['faltas_func_id_func'], "int"),
      GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
      GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
      GetSQLValueString($_POST['faltas_func_data'], "date"),
      GetSQLValueString($_POST['faltas_func_tipo_jutificativa'], "text"),
      GetSQLValueString($_POST['faltas_func_obs'], "text"),
      GetSQLValueString($fileNewName, "text"),
      GetSQLValueString($_POST['faltas_tipo_dia_aula'], "int"),
      GetSQLValueString($hash, "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  }

  // Redireciona após o sucesso
  $insertGoTo = "faltasFuncionarioCadastrar.php?faltalancada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
  exit;
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "formEdit")) {

  // Verificando permissões
  if ($row_UsuLogado['usu_update'] == "N") {
    header(sprintf("Location: funcListar.php?permissao"));
    exit;
  }

  // Diretório onde os arquivos serão salvos
  $uploadDir = 'anexo_faltas/';
  $fileName = $_FILES['edit_arquivo_anexo']['name'];
  $fileTmpName = $_FILES['edit_arquivo_anexo']['tmp_name'];
  $fileSize = $_FILES['edit_arquivo_anexo']['size'];
  $fileError = $_FILES['edit_arquivo_anexo']['error'];
  $fileType = $_FILES['edit_arquivo_anexo']['type'];

  // Inicializa a variável que vai armazenar o nome do arquivo
  $fileNewName = null;

  // Verifica se um arquivo foi anexado
  if (!empty($fileName)) {
    // Verifica a extensão do arquivo
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'docx', 'pdf');

    if (in_array($fileExt, $allowed)) {
      if ($fileError === 0) {
        if ($fileSize < 5000000) { // Limite de 5MB para o arquivo
          $fileNewName = uniqid('', true) . "." . $fileExt;
          $fileDestination = $uploadDir . $fileNewName;

          // Faz o upload do arquivo
          if (move_uploaded_file($fileTmpName, $fileDestination)) {
            // O arquivo foi enviado com sucesso
          } else {
            // Erro ao mover o arquivo
            $updateGoTo = "faltasFuncionarioCadastrar.php?erro";
            if (isset($_SERVER['QUERY_STRING'])) {
              $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
              $updateGoTo .= $_SERVER['QUERY_STRING'];
            }
            header(sprintf("Location: %s", $updateGoTo));
            exit;
          }
        } else {
          $updateGoTo = "faltasFuncionarioCadastrar.php?arquivoGrande";
          if (isset($_SERVER['QUERY_STRING'])) {
            $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
            $updateGoTo .= $_SERVER['QUERY_STRING'];
          }
          header(sprintf("Location: %s", $updateGoTo));
          exit;
        }
      } else {
        $updateGoTo = "faltasFuncionarioCadastrar.php?erro";
        if (isset($_SERVER['QUERY_STRING'])) {
          $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
          $updateGoTo .= $_SERVER['QUERY_STRING'];
        }
        header(sprintf("Location: %s", $updateGoTo));
        exit;
      }
    } else {
      $updateGoTo = "faltasFuncionarioCadastrar.php?naoPermitido";
      if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $updateGoTo));
      exit;
    }
  } else {
    // Se não houver arquivo novo, mantenha o arquivo existente
    $fileNewName = $_POST['edit_arquivo_anexo_atual'];
  }

  // Atualizar os dados no banco de dados
  $updateSQL = sprintf(
    "UPDATE smc_faltas_func SET faltas_func_id_func=%s, faltas_func_id_escola=%s, faltas_func_id_funcao=%s, faltas_func_data=%s, faltas_func_tipo_jutificativa=%s, faltas_func_obs=%s, faltas_func_anexo=%s, faltas_tipo_dia_aula=%s WHERE faltas_func_id=%s",
    GetSQLValueString($_POST['edit_faltas_func_id_func'], "int"),
    GetSQLValueString($_POST['edit_faltas_func_id_escola'], "int"),
    GetSQLValueString($_POST['edit_faltas_func_id_funcao'], "int"),
    GetSQLValueString($_POST['edit_faltas_func_data'], "date"),
    GetSQLValueString($_POST['edit_faltas_func_tipo_jutificativa'], "text"),
    GetSQLValueString($_POST['edit_faltas_func_obs'], "text"),
    GetSQLValueString($fileNewName, "text"), // Atualiza o nome do arquivo ou mantém o atual
    GetSQLValueString($_POST['edit_faltas_tipo_dia_aula'], "int"),
    GetSQLValueString($_POST['edit_faltas_func_id'], "int") // O ID do registro que está sendo atualizado
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  // Redireciona após o sucesso
  $updateGoTo = "faltasFuncionarioCadastrar.php?faltaAtualizada";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
  exit;
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


$colname_Vinculo = "-1";
if (isset($_GET['cod'])) {
  $colname_Vinculo = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs, func_id, func_nome, funcao_id, funcao_nome 
FROM smc_vinculo
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_id = %s", GetSQLValueString($colname_Vinculo, "int"));
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

if ($totalRows_Vinculo == 0) {
  $erro = "funcListar.php?erro";
  header(sprintf("Location: %s", $erro));
}

$beetween = "";
$dataInicio = "";
$dataFinal = "";
$periodo = "Período: Todos os registros.";

if ((isset($_POST["MM_busca"])) && ($_POST["MM_busca"] == "form2")) {

  $dataInicio = inverteData($_POST['dataInicio']);
  $dataFinal = inverteData($_POST['dataFinal']);

  $periodo = "Período: Entre os dias <strong>$_POST[dataInicio]</strong> e <strong>$_POST[dataFinal]</strong>";

  $beetween = " AND faltas_func_data BETWEEN '$dataInicio' AND '$dataFinal'";

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = "
SELECT faltas_func_id, faltas_func_id_func, faltas_func_id_escola, faltas_func_id_funcao, faltas_func_data, faltas_func_tipo_jutificativa, faltas_func_obs,faltas_tipo_dia_aula,
funcao_id, funcao_nome, faltas_func_anexo,
CASE faltas_func_tipo_jutificativa 
WHEN 1 THEN 'ATESTADO'
WHEN 2 THEN 'FALTA JUSTIFICADA'
WHEN 3 THEN 'SEM JUSTIFICATIVA'
END faltas_func_tipo_jutificativa_nome
FROM smc_faltas_func
INNER JOIN smc_funcao ON funcao_id = faltas_func_id_funcao
WHERE faltas_func_id_func = '$row_Vinculo[vinculo_id_funcionario]' $beetween
ORDER BY faltas_func_data DESC 
";
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);


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
  <meta name="description" content="Sistema de Gestão Escolar.">
  <link href="https://assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css" rel="stylesheet" type="text/css">
  <script src="js/locastyle.js"></script>
  <link rel="icon" sizes="192x192" href="img/icone.png">
  <link rel="apple-touch-icon" href="img/icone.png">
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

      <h1 class="ls-title-intro ls-ico-home">FALTA DO FUNCIONÁRIO</h1>
      <!-- CONTEÚDO -->

      <div class="ls-box">
        <p>FUNCIONÁRIO: <?php echo $row_Vinculo['func_nome']; ?></p>
        <p>CARGO: <?php echo $row_Vinculo['funcao_nome']; ?></p>
      </div>




      <div class="ls-box">

        <div class="col-md-6 col-sm-12">
          <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">REGISTRAR FALTA</button>
          <a href="funcListar.php" class="ls-btn">Voltar</a>
        </div>




        <form class="ls-form ls-form-inline col-md-6 col-sm-12 row" data-ls-module="form" method="post"
          action="faltasFuncionarioCadastrar.php?cod=<?php echo $colname_Vinculo; ?>">
          <label class="ls-label col-md-4 col-sm-12">
            <div class="ls-prefix-group">
              <span data-ls-module="popover" data-content="Escolha o período desejado e clique em 'Filtrar'."></span>
              <input type="date" name="dataInicio" class="1datepicker ls-daterange" id="datepicker1" autocomplete="off"
                required>
            </div>
          </label>

          <label class="ls-label col-md-4 col-sm-12">
            <div class="ls-prefix-group">
              <span data-ls-module="popover"
                data-content="Clique em 'Filtrar' para exibir  o período selecionado."></span>
              <input type="date" name="dataFinal" class="1datepicker 1ls-daterange" id="1datepicker2" autocomplete="off"
                required>
            </div>
          </label>

          <label class="ls-label col-md-4 col-sm-12">
            <input type="submit" value="BUSCAR" class="ls-btn">
          </label>


          <input type="hidden" name="MM_busca" value="form2">

        </form>



      </div>

      <?php if (isset($_GET["faltalancada"])) { ?>
        <div class="ls-alert-success ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Registro de falta realizada com sucesso.
        </div>
      <?php } ?>
      <?php if (isset($_GET["deletado"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Registro de falta excluído com sucesso.
        </div>
      <?php } ?>
      <?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Erro ao registrar falta.
        </div>
      <?php } ?>
      <?php if (isset($_GET["naoPermitido"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Tipo de arquivo não permitido.
        </div>
      <?php } ?>
      <?php if (isset($_GET["arquivoGrande"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          O anexo enviado é muito grande.
        </div>
      <?php } ?>



      <?php if ($totalRows_Faltas > 0) { // Show if recordset not empty ?>


        <p><?php echo $periodo; ?></p>

        <table class="ls-table ls-table-striped ls-sm-space">
          <thead>
            <tr>
              <th width="40"></th>
              <th width="150" class="ls-txt-center">DATA DA FALTA</th>
              <th class="ls-txt-center">TIPO DE JUSTIFICATIVA</th>
              <th class="ls-txt-center">ANEXO</th>
              <th class="ls-txt-center" width="500">OBSERVAÇÕES</th>
              <th class="ls-txt-center" width="50"></th>
            </tr>
          </thead>
          <tbody>
            <?php $cont = 1; ?>
            <?php do { ?>
              <tr>
                <td><?php echo $cont;
                $cont++; ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_Faltas['faltas_func_data']); ?></td>
                <td class="ls-txt-center"><?php echo $row_Faltas['faltas_func_tipo_jutificativa_nome']; ?></td>
                <td class="ls-txt-center">
                  <?php if ($row_Faltas['faltas_func_anexo'] != '') { ?>
                    <a href="anexo_faltas/<?= $row_Faltas['faltas_func_anexo'] ?>" target="_blank"
                      class="ls-ico-download2 ls-ico-right"></a>
                  <?php } ?>
                </td>
                <td class="ls-txt-center"><?php echo $row_Faltas['faltas_func_obs']; ?></td>
                <td class="ls-txt-center">
                  <a href="javascript:void(0);" class="ls-ico-pencil ls-ico-right" onclick="abreModalEditarFalta(
                    '<?php echo $row_Faltas['faltas_func_id']; ?>',
                    '<?php echo $row_Faltas['faltas_func_data']; ?>',
                    '<?php echo $row_Faltas['faltas_func_tipo_jutificativa']; ?>',
                    '<?php echo $row_Faltas['faltas_tipo_dia_aula']; ?>',
                    '<?php echo $row_Faltas['faltas_func_obs']; ?>',
                    '<?php echo $row_Faltas['faltas_func_anexo']; ?>'
                  )">
                  </a>


                  <a href="javascript:func()"
                    onclick="confirmaExclusao('<?php echo $row_Faltas['faltas_func_id']; ?>&cod=<?php echo $colname_Vinculo; ?>')"
                    class="ls-ico-cancel-circle ls-ico-right"></a>
                </td>
              </tr>
            <?php } while ($row_Faltas = mysql_fetch_assoc($Faltas)); ?>
          </tbody>
        </table>
        <p>Total de faltas: <?php echo $totalRows_Faltas; ?></p>
      <?php } else { ?>

        <div class="ls-alert-info"><strong>Atenção:</strong> Nenhuma falta registrada para este funcionário.</div>


      <?php } // Show if recordset not empty ?>
      <div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">REGISTRO DE FALTAS</h4>
            <p>
            <h3><?php echo $row_Vinculo['func_nome']; ?><br><?php echo $row_Vinculo['funcao_nome']; ?></h3>
            </p>
          </div>
          <div class="ls-modal-body" id="myModalBody">
            <p>
            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data"
              class="ls-form ls-form-horizontal row">

              <label class="ls-label col-md-6">
                <b class="ls-label-text">DATA</b>
                <div class="ls-prefix-group">
                  <input type="date" name="faltas_func_data" class="ls-field" tabindex="0" id="dataInicio"
                    value="<?php echo date('Y-m-d'); ?>" size="32" required>
                </div>
              </label>

              <label class="ls-label col-md-6">
                <b class="ls-label-text">JUSTIFICATIVA</b>
                <div class="ls-custom-select">
                  <select name="faltas_func_tipo_jutificativa" class="ls-select" required tabindex="1">
                    <option value="">ESCOLHA...</option>
                    <option value="1" <?php if (!(strcmp(1, ""))) {
                      echo "SELECTED";
                    } ?>>1 - ATESTADO</option>
                    <option value="2" <?php if (!(strcmp(2, ""))) {
                      echo "SELECTED";
                    } ?>>2 - FALTA JUSTIFICADA</option>
                    <option value="3" <?php if (!(strcmp(3, ""))) {
                      echo "SELECTED";
                    } ?>>3 - SEM JUSTIFICATIVA</option>
                  </select>
                </div>
              </label>

              <label class="ls-label col-md-6">
                <b class="ls-label-text">ANEXAR ARQUIVO</b>
                <input type="file" name="arquivo_anexo" class="ls-field" tabindex="2">
              </label>

              <label class="ls-label col-md-6">
                <b class="ls-label-text">TIPO DE FALTA</b>
                <div class="ls-custom-select">
                  <select id="faltas_tipo_dia_aula" name="faltas_tipo_dia_aula" class="ls-select" required tabindex="1"
                    onchange="toggleQuantidadeCampo()">
                    <option value="">ESCOLHA...</option>
                    <option value="1" <?php if (!(strcmp(1, ""))) {
                      echo "SELECTED";
                    } ?>>1 - DIA</option>
                    <option value="2" <?php if (!(strcmp(2, ""))) {
                      echo "SELECTED";
                    } ?>>2 - AULA</option>
                  </select>
                </div>
              </label>

              <!-- Campo de quantidade de aulas -->
              <label id="quantidadeAulasLabel" class="ls-label col-md-6" style="display: none;">
                <b class="ls-label-text">QUANTIDADE DE AULAS</b>
                <input type="number" name="quantidade_aulas" id="quantidade_aulas" class="ls-input" min="1"
                  placeholder="Digite a quantidade de aulas">
              </label>

              <label class="ls-label col-md-12">
                <b class="ls-label-text">DETALHES</b>
                <textarea name="faltas_func_obs" cols="50" rows="3" tabindex="3" class="ls-field"></textarea>
              </label>

              <input type="hidden" name="faltas_func_id_func"
                value="<?php echo $row_Vinculo['vinculo_id_funcionario']; ?>">
              <input type="hidden" name="faltas_func_id_escola"
                value="<?php echo $row_Vinculo['vinculo_id_escola']; ?>">
              <input type="hidden" name="faltas_func_id_funcao"
                value="<?php echo $row_Vinculo['vinculo_id_funcao']; ?>">
              <input type="hidden" name="MM_insert" value="form1">

              <div class="ls-actions-btn">
                <button class="ls-btn ls-float-right" data-dismiss="modal" tabindex="5">CANCELAR</button>
                <input type="submit" value="REGISTRAR FALTA" class="ls-btn-primary" tabindex="4">
              </div>

            </form>


          </div>
        </div>
      </div><!-- /.modal -->

      <div class="ls-modal" id="editModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">EDIÇÃO DE FALTAS</h4>
            <p>
            <h3><?php echo $row_Vinculo['func_nome']; ?><br><?php echo $row_Vinculo['funcao_nome']; ?></h3>
            </p>
          </div>
          <div class="ls-modal-body edit-modal-body">
            <p>
            <form method="post" name="formEdit" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data"
              class="ls-form ls-form-horizontal row">
              <!-- Campo de Data -->
              <label class="ls-label col-md-6">
                <b class="ls-label-text">DATA</b>
                <div class="ls-prefix-group">
                  <input type="date" name="edit_faltas_func_data" class="ls-field" required>
                </div>
              </label>

              <!-- Campo de Justificativa -->
              <label class="ls-label col-md-6">
                <b class="ls-label-text">JUSTIFICATIVA</b>
                <div class="ls-custom-select">
                  <select name="edit_faltas_func_tipo_jutificativa" class="ls-select" required>
                    <option value="">ESCOLHA...</option>
                    <option value="1">1 - ATESTADO</option>
                    <option value="2">2 - FALTA JUSTIFICADA</option>
                    <option value="3">3 - SEM JUSTIFICATIVA</option>
                  </select>
                </div>
              </label>

              <!-- Campo de Anexo -->
              <label class="ls-label col-md-6">
                <b class="ls-label-text">ANEXAR ARQUIVO</b>
                <input type="file" name="edit_arquivo_anexo" class="ls-field">
              </label>

              <!-- Campo de Tipo de Falta -->
              <label class="ls-label col-md-6">
                <b class="ls-label-text">TIPO DE FALTA</b>
                <div class="ls-custom-select">
                  <select name="edit_faltas_tipo_dia_aula" class="ls-select" required>
                    <option value="">ESCOLHA...</option>
                    <option value="1">1 - DIA</option>
                    <option value="2">2 - AULA</option>
                  </select>
                </div>
              </label>

              <!-- Campo de Observação -->
              <label class="ls-label col-md-12">
                <b class="ls-label-text">DETALHES</b>
                <textarea name="edit_faltas_func_obs" cols="50" rows="3" class="ls-field"></textarea>
              </label>

              <!-- Campos ocultos para IDs -->
              <input type="hidden" name="edit_faltas_func_id" value="">
              <input type="hidden" name="edit_faltas_func_id_func"
                value="<?php echo $row_Vinculo['vinculo_id_funcionario']; ?>">
              <input type="hidden" name="edit_faltas_func_id_escola"
                value="<?php echo $row_Vinculo['vinculo_id_escola']; ?>">
              <input type="hidden" name="edit_faltas_func_id_funcao"
                value="<?php echo $row_Vinculo['vinculo_id_funcao']; ?>">
              <input type="hidden" name="MM_update" value="formEdit">

              <!-- Botões de Ação -->
              <div class="ls-actions-btn">
                <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
                <input type="submit" value="SALVAR ALTERAÇÕES" class="ls-btn-primary">
              </div>
            </form>
          </div>
        </div>
      </div>






      <p>&nbsp;</p>
      <!-- CONTEÚDO -->
    </div>
  </main>
  <?php include_once("menu-dir.php"); ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

  <script src="js/pikaday.js"></script>
  <script>

    function toggleQuantidadeCampo() {
      var selectTipoFalta = document.getElementById('faltas_tipo_dia_aula');
      var quantidadeAulasLabel = document.getElementById('quantidadeAulasLabel');
      var quantidadeAulasInput = document.getElementById('quantidade_aulas');

      // Verifica se a opção "AULA" foi selecionada (valor 2)
      if (selectTipoFalta.value === '2') {
        quantidadeAulasLabel.style.display = 'block';  // Mostra o campo
        quantidadeAulasInput.setAttribute('required', 'required');  // Torna o campo obrigatório
      } else {
        quantidadeAulasLabel.style.display = 'none';   // Esconde o campo
        quantidadeAulasInput.removeAttribute('required');  // Remove a obrigatoriedade
      }
    }

    function abreModalEditarFalta(id, data, justificativa, tipoFalta, obs, anexo) {
      // Preenche os campos da modal de edição com os valores da falta selecionada
      document.querySelector('input[name="edit_faltas_func_id"]').value = id;
      document.querySelector('input[name="edit_faltas_func_data"]').value = data;
      document.querySelector('select[name="edit_faltas_func_tipo_jutificativa"]').value = justificativa;
      document.querySelector('select[name="edit_faltas_tipo_dia_aula"]').value = tipoFalta;
      document.querySelector('textarea[name="edit_faltas_func_obs"]').value = obs;

      // Se houver anexo, cria um link para visualizá-lo
      if (anexo !== '') {
        const anexoLink = document.createElement('a');
        anexoLink.href = 'anexo_faltas/' + anexo;
        anexoLink.target = '_blank';
        anexoLink.textContent = 'Ver Anexo';
        document.querySelector('.edit-modal-body').appendChild(anexoLink);
      }

      // Abre a modal de edição
      document.getElementById('editModal').style.display = 'block';
    }

    // Função para fechar a modal ao clicar no botão de fechar ou fora da modal
    document.querySelector('#editModal [data-dismiss="modal"]').addEventListener('click', function () {
      document.getElementById('editModal').style.display = 'none';
    });
  </script>

  <script>
    //locastyle.modal.open("#myAwesomeModal");
    locastyle.datepicker.newDatepicker('#dataInicio, #datepicker1, #datepicker2');
  </script>

  <script language="Javascript">
    function confirmaExclusao(id, nome) {
      var resposta = confirm("Deseja realmente excluir esse registro de falta?");
      if (resposta == true) {
        window.location.href = "faltasFuncionariosExcluir.php?falta=" + id;
      }
    }
  </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Faltas);

mysql_free_result($Vinculo);
?>