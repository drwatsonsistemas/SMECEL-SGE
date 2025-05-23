<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listaFuncoes = "SELECT funcao_id, funcao_nome, funcao_secretaria_id, funcao_observacoes FROM smc_funcao WHERE funcao_secretaria_id = '$row_EscolaLogada[escola_id_sec]' ORDER BY funcao_nome ASC";
$listaFuncoes = mysql_query($query_listaFuncoes, $SmecelNovo) or die(mysql_error());
$row_listaFuncoes = mysql_fetch_assoc($listaFuncoes);
$totalRows_listaFuncoes = mysql_num_rows($listaFuncoes);

$colname_Vinculo = "-1";
if (isset($_GET['c'])) {
  $colname_Vinculo = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = sprintf("SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, 
vinculo_carga_horaria, vinculo_data_inicio, vinculo_data_final, vinculo_status, vinculo_obs, func_id, func_nome 
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
WHERE 
vinculo_id_escola = %s
AND
vinculo_id = %s", GetSQLValueString($row_EscolaLogada['escola_id'], "int"), GetSQLValueString($colname_Vinculo, "int"));
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

if ($totalRows_Vinculo == "") {
	//echo "TURMA EM BRANCO";	
	//header("Location: funcListar.php?erro"); 
  //exit;
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  // Verificar permissão de atualização
  if ($row_UsuLogado['usu_update'] == "N") {
      header("Location: funcListar.php?permissao");
      exit;
  }


$nova_data = converteData($_POST['vinculo_data_inicio']);

// Verifica se o campo está vazio ou se é '0000-00-00' e define como NULL se for o caso
$data_final = (empty($_POST['vinculo_data_final']) || $_POST['vinculo_data_final'] == '0000-00-00') 
    ? "NULL" 
    : "'" . mysql_real_escape_string($_POST['vinculo_data_final']) . "'";

  // Construir a query de atualização
  $updateSQL = sprintf(
      "UPDATE smc_vinculo 
      SET vinculo_id_funcao = %s, 
          vinculo_carga_horaria = %s, 
          vinculo_data_inicio = '%s', 
          vinculo_data_final = %s, 
          vinculo_status = %s, 
          vinculo_obs = %s 
      WHERE vinculo_id = %s",
      GetSQLValueString($_POST['vinculo_id_funcao'], "int"),
      GetSQLValueString($_POST['vinculo_carga_horaria'], "text"),
      mysql_real_escape_string($nova_data),
      $data_final,
      GetSQLValueString($_POST['vinculo_status'], "text"),
      GetSQLValueString($_POST['vinculo_obs'], "text"),
      GetSQLValueString($_POST['vinculo_id'], "int")
  );

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  // Registro de log do usuário
  $usu = mysql_real_escape_string($_POST['usu_id']);
  $esc = mysql_real_escape_string($_POST['usu_escola']);
  $detalhes = mysql_real_escape_string($_POST['detalhes']);
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
      '24', 
      '($detalhes)', 
      '$dat'
  )";

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result2 = mysql_query($sql, $SmecelNovo) or die(mysql_error());

  // Redirecionar após a atualização
  echo $updateGoTo = "funcionarios_detalhes.php?codigo={$row_Vinculo['vinculo_id']}&editado";

  header("Location: $updateGoTo");
  exit;
}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>


  <main class="ls-main">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-home">Editar Vínculo</h1>

      <form method="post" class="ls-form ls-form-horizontal row" name="form1" action="<?php echo $editFormAction; ?>" data-ls-module="form">


       <label class="ls-label col-md-12">
        <b class="ls-label-text">FUNCIONÁRIO</b>
        <p class="ls-label-info">Não pode ser alterado</p>
        <input type="text" name="" value="<?php echo $row_Vinculo['func_nome']; ?>" size="32" disabled>
      </label>


      <label class="ls-label col-md-12">
        <b class="ls-label-text">FUNÇÃO</b>
        <p class="ls-label-info">Função que o funcionário ocupa na escola</p>
        <div class="ls-custom-select">
          <select name="vinculo_id_funcao" class="ls-select" required>
            <option value="">Escolha...</option>
            <?php do {  ?>
              <option value="<?php echo $row_listaFuncoes['funcao_id']?>" <?php if (!(strcmp($row_listaFuncoes['funcao_id'], htmlentities($row_Vinculo['vinculo_id_funcao'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>><?php echo $row_listaFuncoes['funcao_nome']?></option>
            <?php } while ($row_listaFuncoes = mysql_fetch_assoc($listaFuncoes)); ?>
          </select>
        </div>
      </label>


      <label class="ls-label col-md-3">
        <b class="ls-label-text">CARGA HORÁRIA</b>
        <p class="ls-label-info">Informe a carga horária do funcionário</p>
        <div class="ls-custom-select">
          <select name="vinculo_carga_horaria" class="ls-select" required>
            <option value="">Escolha...</option>
            <option value="20" <?php if (!(strcmp(20, htmlentities($row_Vinculo['vinculo_carga_horaria'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>20h</option>
            <option value="30" <?php if (!(strcmp(30, htmlentities($row_Vinculo['vinculo_carga_horaria'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>30h</option>
            <option value="40" <?php if (!(strcmp(40, htmlentities($row_Vinculo['vinculo_carga_horaria'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>40h</option>
          </select>
        </div>
      </label>

      <label class="ls-label col-md-3">
        <b class="ls-label-text">INÍCIO VÍNCULO</b>
        <p class="ls-label-info">Informe a data de início do vínculo</p>
        <input type="text" name="vinculo_data_inicio" value="<?php echo htmlentities(converteData($row_Vinculo['vinculo_data_inicio']), ENT_COMPAT, ''); ?>" size="32">
      </label>

      <label class="ls-label col-md-3">
        <b class="ls-label-text">FINAL VÍNCULO</b>
        <p class="ls-label-info">Informe a data de término do vínculo</p>
        <input type="date" name="vinculo_data_final" value="<?php echo $row_Vinculo['vinculo_data_final']; ?>" size="32">
      </label>

      <label class="ls-label col-md-3">
        <b class="ls-label-text">SITUAÇÃO VÍNCULO</b>
        <p class="ls-label-info">Informe o status atual</p>
        <div class="ls-custom-select">
          <select name="vinculo_status" class="ls-select" required>
            <option value="1" <?php if (!(strcmp("1", htmlentities($row_Vinculo['vinculo_status'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>ATIVO</option>
            <option value="2" <?php if (!(strcmp("2", htmlentities($row_Vinculo['vinculo_status'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>FINALIZADO</option>
          </select>
        </div>
      </label>


      <label class="ls-label col-md-12">
        <b class="ls-label-text">OBSERVAÇÃO</b>
        <textarea name="vinculo_obs" cols="50" rows="5"><?php echo htmlentities($row_Vinculo['vinculo_obs'], ENT_COMPAT, ''); ?></textarea>
      </label>

      <hr>            

      <div class="ls-actions-btn">
        <input type="submit" value="ATUALIZAR" class="ls-btn-primary">
        <a class="ls-btn-dark" href="funcionarios_detalhes.php?codigo=<?= $row_Vinculo['vinculo_id'] ?>">CANCELAR</a>
        <!--<a class="ls-btn-dark" href="excluirVinculo.php?cod=<?php echo $row_Vinculo['vinculo_id']; ?>&nome=<?php echo $row_ListaVinculos['func_nome']; ?>" class="ls-ico-cancel-circle"> Desvincular</a>-->
      </div>


      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="vinculo_id" value="<?php echo $row_Vinculo['vinculo_id']; ?>">

      <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
      <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
      <input type="hidden" name="detalhes" value="<?php echo $row_Vinculo['func_nome']; ?>">


    </form>

    <p>&nbsp;</p>
  </div>

</main>

<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/locastyle.js"></script>

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($listaFuncoes);

mysql_free_result($Vinculo);
?>
