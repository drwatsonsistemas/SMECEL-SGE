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

$anoLetivoAnterior = $row_AnoLetivo['ano_letivo_ano'];
$anoLetivoProximo = $anoLetivoAnterior + 1;

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turmasAnoAtual = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_id_sec = '$row_UsuLogado[usu_sec]' AND turma_ano_letivo = '$anoLetivoProximo'";
$turmasAnoAtual = mysql_query($query_turmasAnoAtual, $SmecelNovo) or die(mysql_error());
$row_turmasAnoAtual = mysql_fetch_assoc($turmasAnoAtual);
$totalRows_turmasAnoAtual = mysql_num_rows($turmasAnoAtual);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listaTurmas = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome
FROM smc_turma WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_id_sec = '$row_UsuLogado[usu_sec]' AND turma_ano_letivo = '$anoLetivoAnterior'
ORDER BY turma_etapa ASC, turma_nome ASC, turma_turno ASC";
$listaTurmas = mysql_query($query_listaTurmas, $SmecelNovo) or die(mysql_error());
$row_listaTurmas = mysql_fetch_assoc($listaTurmas);
$totalRows_listaTurmas = mysql_num_rows($listaTurmas);

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



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if (isset($_POST['turmas']) && is_array($_POST['turmas'])) {
    foreach ($_POST['turmas'] as $turmaId) {
      // Encontre os dados da turma selecionada para remanejar
      $query_turma = sprintf("SELECT * FROM smc_turma WHERE turma_id = %s", GetSQLValueString($turmaId, "int"));
      $turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
      $row_turma = mysql_fetch_assoc($turma);
      
      $insertSQL = sprintf(
        "INSERT INTO smc_turma (turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento, turma_multisseriada) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($row_turma['turma_id_escola'], "int"),
        GetSQLValueString($row_turma['turma_id_sec'], "int"),
        GetSQLValueString($row_turma['turma_matriz_id'], "int"),
        GetSQLValueString($row_turma['turma_nome'], "text"),
        GetSQLValueString($row_turma['turma_etapa'], "int"),
        GetSQLValueString($row_turma['turma_turno'], "int"),
        GetSQLValueString($row_turma['turma_total_alunos'], "text"),
        GetSQLValueString($row_turma['turma_ano_letivo'] + 1, "text"),
        GetSQLValueString($row_turma['turma_tipo_atendimento'], "text"),
        GetSQLValueString($row_turma['turma_multisseriada'], "text")
      );

      mysql_select_db($database_SmecelNovo, $SmecelNovo);
      $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
    }

    $insertGoTo = "turmaListarRematricula.php?remanejadas";
    if (isset($_SERVER['QUERY_STRING'])) {
      $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
      $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $insertGoTo));
  }
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

      <h1 class="ls-title-intro ls-ico-home">REMANEJAR TURMAS DE <?php echo $anoLetivoAnterior; ?> PARA
        <?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>
      </h1>
      <!-- CONTEÚDO -->


      <div class="ls-alert-info"><strong>Atenção:</strong> Abaixo estão listadas as turmas do Ano Letivo
        <?php echo $anoLetivoAnterior; ?>. Ao clicar no botão, as turmas serão remanejadas com as mesmas informações
        para o Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>. Os alunos vinculados não serão
        remanejados.
        Este processo não poderá ser desfeito.
      </div>

      <?php if ($totalRows_turmasAnoAtual > 0) { ?>
        <div class="ls-alert-danger"><strong>Atenção:</strong> Já existem <?php echo $totalRows_turmasAnoAtual ?> turma(s)
          cadastradas para o Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>. Cuidado para que não ocorram
          duplicidades ao remanejar as turmas.</div>
      <?php } ?>


      <!--<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" ls-form>
      <input type="submit" value="REMANEJAR AS TURMAS ABAIXO PARA O ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>" class="ls-btn-primary" />
      <input type="hidden" name="MM_insert" value="form1" />
    </form> -->


      <!-- Botão para remanejar turmas -->
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" ls-form>
        <input type="submit"
          value="REMANEJAR AS TURMAS SELECIONADAS PARA O ANO LETIVO <?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>"
          class="ls-btn-primary" />
        <input type="hidden" name="MM_insert" value="form1" />
        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th width="50">
                <!-- Checkbox para selecionar todas as turmas -->
                <input type="checkbox" id="selecionarTodos" onclick="selecionarTodasTurmas()">
              </th>
              <th>TURMAS <?php echo $anoLetivoAnterior; ?></th>
              <th>TURNO</th>
              <th>ANO LETIVO</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $num = 1;
            do { ?>
              <tr>
                <td>
                  <!-- Checkbox para selecionar a turma individualmente -->
                  <input type="checkbox" name="turmas[]" value="<?php echo $row_listaTurmas['turma_id']; ?>"
                    class="turma-checkbox">
                </td>
                <td><?php echo $row_listaTurmas['turma_nome']; ?></td>
                <td><?php echo $row_listaTurmas['turma_turno_nome']; ?></td>
                <td><?php echo $row_listaTurmas['turma_ano_letivo']; ?></td>
              </tr>
              <?php $num++;
            } while ($row_listaTurmas = mysql_fetch_assoc($listaTurmas)); ?>
          </tbody>
        </table>


      </form>
      <!-- CONTEÚDO -->
    </div>
  </main>
  <?php include_once("menu-dir.php"); ?>

  <!-- We recommended use jQuery 1.10 or up -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="js/locastyle.js"></script>

  <script>
    // Função para selecionar ou desmarcar todas as turmas
    function selecionarTodasTurmas() {
      const selecionarTodos = document.getElementById("selecionarTodos").checked;
      const checkboxes = document.querySelectorAll(".turma-checkbox");
      checkboxes.forEach(checkbox => {
        checkbox.checked = selecionarTodos;
      });
    }

    // Função para validar se ao menos uma turma está selecionada
    function validarSelecaoTurmas(event) {
      const checkboxes = document.querySelectorAll(".turma-checkbox");
      const algumaSelecionada = Array.from(checkboxes).some(checkbox => checkbox.checked);

      if (!algumaSelecionada) {
        alert("Por favor, selecione ao menos uma turma para remanejar.");
        event.preventDefault(); // Impede o envio do formulário
      }
    }

    // Adiciona a função de validação ao evento de envio do formulário
    document.getElementById("form1").addEventListener("submit", validarSelecaoTurmas);
  </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($turmasAnoAtual);

mysql_free_result($listaTurmas);

mysql_free_result($EscolaLogada);
?>