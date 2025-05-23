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
  // Gerar o hash
  $hash = md5(date('Y-m-d H:i:s') . time() . $_POST['faltas_func_id_func'] . $_POST['folha_extra_data']);

  $quantidadeAulas = isset($_POST['quantidade_aulas']) ? (int) $_POST['quantidade_aulas'] : 1;
  $folha_extra_tipo = (int) $_POST['folha_extra_tipo'];

  // Inserir os dados no banco de dados
  if ($folha_extra_tipo == 1) {
    // Para "DIA", inserir apenas um registro
    $insertSQL = sprintf(
      "INSERT INTO smc_folha_extra (folha_extra_id_func, folha_extra_id_escola, folha_extra_id_funcao, folha_extra_data, folha_extra_tipo, folha_extra_justificativa, folha_extra_hash) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['faltas_func_id_func'], "int"),
      GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
      GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
      GetSQLValueString($_POST['folha_extra_data'], "date"),
      GetSQLValueString($_POST['folha_extra_tipo'], "int"),
      GetSQLValueString($_POST['folha_extra_justificativa'], "text"),
      GetSQLValueString($hash, "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  } elseif ($folha_extra_tipo == 2) {
    // Para "AULA", inserir múltiplos registros com base em quantidade_aulas
    for ($i = 0; $i < $quantidadeAulas; $i++) {
      $insertSQL = sprintf(
        "INSERT INTO smc_folha_extra (folha_extra_id_func, folha_extra_id_escola, folha_extra_id_funcao, folha_extra_data, folha_extra_tipo, folha_extra_justificativa, folha_extra_hash) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['faltas_func_id_func'], "int"),
        GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
        GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
        GetSQLValueString($_POST['folha_extra_data'], "date"),
        GetSQLValueString($_POST['folha_extra_tipo'], "int"),
        GetSQLValueString($_POST['folha_extra_justificativa'], "text"),
        GetSQLValueString($hash, "text")
      );

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
    }
  }
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

  $beetween = " AND folha_extra_data BETWEEN '$dataInicio' AND '$dataFinal'";

}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Extras = "
SELECT folha_extra_id, folha_extra_id_func, folha_extra_id_escola, folha_extra_data, folha_extra_tipo, folha_extra_justificativa, folha_extra_hash,
CASE folha_extra_tipo 
WHEN 1 THEN 'DIA'
WHEN 2 THEN 'AULA'
END folha_extra_tipo_nome
FROM smc_folha_extra
WHERE folha_extra_id_func = '$row_Vinculo[vinculo_id_funcionario]' AND folha_extra_id_escola = '$row_Vinculo[vinculo_id_escola]' $beetween
ORDER BY folha_extra_data DESC 
";
$Extras = mysql_query($query_Extras, $SmecelNovo) or die(mysql_error());
$row_Extras = mysql_fetch_assoc($Extras);
$totalRows_Extras = mysql_num_rows($Extras);


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
          <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">REGISTRAR EXTRA</button>
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
          Registro de extra realizada com sucesso.
        </div>
      <?php } ?>
      <?php if (isset($_GET["deletado"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Registro de extra excluído com sucesso.
        </div>
      <?php } ?>
      <?php if (isset($_GET["erro"])) { ?>
        <div class="ls-alert-warning ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          Erro ao registrar extra.
        </div>
      <?php } ?>



      <?php if ($totalRows_Extras > 0) { // Show if recordset not empty ?>


        <p><?php echo $periodo; ?></p>

        <table class="ls-table ls-table-striped ls-sm-space">
          <thead>
            <tr>
              <th width="40"></th>
              <th width="150" class="ls-txt-center">DATA</th>
              <th class="ls-txt-center">TIPO EXTRA</th>
              <th class="ls-txt-center">JUSTIFICATIVA</th>
              <th class="ls-txt-center" width="50"></th>
            </tr>
          </thead>
          <tbody>
            <?php $cont = 1; ?>
            <?php do { ?>
              <tr>
                <td><?php echo $cont;
                $cont++; ?></td>
                <td class="ls-txt-center"><?php echo inverteData($row_Extras['folha_extra_data']); ?></td>
                <td class="ls-txt-center"><?php echo $row_Extras['folha_extra_tipo_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_Extras['folha_extra_justificativa']; ?></td>
                <td class="ls-txt-center"><a href="javascript:func()"
                    onclick="confirmaExclusao('<?php echo $row_Extras['folha_extra_id']; ?>&cod=<?php echo $colname_Vinculo; ?>')"
                    class="ls-ico-cancel-circle ls-ico-right"></a></td>
              </tr>
            <?php } while ($row_Extras = mysql_fetch_assoc($Extras)); ?>
          </tbody>
        </table>
        <p>Total de faltas: <?php echo $totalRows_Extras; ?></p>
      <?php } else { ?>

        <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum extra registrada para este funcionário.</div>


      <?php } // Show if recordset not empty ?>
      <div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">REGISTRO DE EXTRAS</h4>
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
                  <input type="date" name="folha_extra_data" class="ls-field" tabindex="0" id="dataInicio"
                    value="<?php echo date('Y-m-d'); ?>" size="32" required>
                </div>
              </label>


              <label class="ls-label col-md-6">
                <b class="ls-label-text">TIPO DE EXTRA</b>
                <div class="ls-custom-select">
                  <select id="folha_extra_tipo" name="folha_extra_tipo" class="ls-select" required tabindex="1"
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
                <b class="ls-label-text">JUSTIFICATIVA</b>
                <textarea name="folha_extra_justificativa" cols="50" rows="3" tabindex="3" class="ls-field"></textarea>
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
                <input type="submit" value="REGISTRAR EXTRA" class="ls-btn-primary" tabindex="4">
              </div>

            </form>


          </div>
        </div>
      </div><!-- /.modal -->







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
    //locastyle.modal.open("#myAwesomeModal");
    //locastyle.datepicker.newDatepicker('#dataInicio, #datepicker1, #datepicker2');
  </script>

  <script language="Javascript">
    function toggleQuantidadeCampo() {
      var selectTipoFalta = document.getElementById('folha_extra_tipo');
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
    function confirmaExclusao(id, nome) {
      var resposta = confirm("Deseja realmente excluir esse registro de extra?");
      if (resposta == true) {
        window.location.href = "extrasFuncionarioExcluir.php?extra=" + id;
      }
    }
  </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Extras);

mysql_free_result($Vinculo);
?>