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
include "fnc/anoLetivo.php";}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: funcListar.php?permissao"));
		break;
	}
	
	
  $updateSQL = sprintf("UPDATE smc_ocorrencias_func SET ocorrencia_func_data = %s, ocorrencia_func_texto = %s, ocorrencia_func_tipo = %s, ocorrencia_func_obs = %s WHERE ocorrencia_id = %s AND ocorrencia_func_id_escola = %s",
                       GetSQLValueString($_POST['ocorrencia_func_data'], "date"),
                       GetSQLValueString($_POST['ocorrencia_func_texto'], "text"),
                       GetSQLValueString($_POST['ocorrencia_func_tipo'], "int"),
                       GetSQLValueString($_POST['ocorrencia_func_obs'], "text"),
                       GetSQLValueString($_GET['ocorrencia'], "int"),
                       GetSQLValueString($_POST['ocorrencia_func_id_escola'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
	
	
	date_default_timezone_set('America/Bahia');
	$dat = date('Y-m-d H:i:s');

    $updateGoTo = "ocorrenciaFuncionarioCadastrar.php?editado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = "
SELECT ocorrencia_id, ocorrencia_func_vinculo_id, ocorrencia_func_data, ocorrencia_func_texto, ocorrencia_func_tipo, ocorrencia_func_obs,
CASE ocorrencia_func_tipo 
WHEN 1 THEN 'ADVERTENCIA'
WHEN 2 THEN 'SUSPENSAO'
END ocorrencia_func_tipo_nome
FROM smc_ocorrencias_func
WHERE ocorrencia_func_vinculo_id = '$row_Vinculo[vinculo_id_funcionario]'
ORDER BY ocorrencia_func_data DESC 
";
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);



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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onLoad="javascript:mudarTurmaNao()">
<?php include_once ("menu-top.php"); ?>
<?php include_once ("menu-esc.php"); ?>
<main class="ls-main ">
  <div class="container-fluid">
    <!-- CONTEÚDO -->
    
    
    <div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">EDITAR DE OCORRÊNCIA</h4>
	  <p>
	  <h3><?php echo $row_Vinculo['func_nome']; ?><br><?php echo $row_Vinculo['funcao_nome']; ?></h3>
		</p>
	</div>
    <div class="ls-modal-body" id="myModalBody">
      <p>
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row">

            <label class="ls-label col-md-6"> 
			<b class="ls-label-text">DATA</b>
			<div class="ls-prefix-group">
              <input type="date" name="ocorrencia_func_data" class="1datepicker" tabindex="0" id="1dataInicio" value="<?php echo $row_Ocorrencia['ocorrencia_func_data']; ?>" size="32" required>
			  
			  </div>
            </label>
			
            <label class="ls-label col-sm-6">
            <b class="ls-label-text">TIPO</b>
            <div class="ls-custom-select">
              <select name="ocorrencia_func_tipo" class="ls-select" required tabindex="1">
                <option value="">ESCOLHA...</option>
                <option value="1" <?php if (!(strcmp(1, htmlentities($row_Ocorrencia['ocorrencia_func_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1 - ADVERTÊNCIA</option>
                <option value="2" <?php if (!(strcmp(2, htmlentities($row_Ocorrencia['ocorrencia_func_tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2 - SUSPENSÃO</option>
              </select>
            </div>
            </label>
            <label class="ls-label col-md-12"> <b class="ls-label-text">DETALHES</b>
              <textarea name="ocorrencia_func_texto" cols="50" rows="5" tabindex="2" ><?= htmlentities($row_Ocorrencia['ocorrencia_func_texto'], ENT_COMPAT, 'utf-8') ?></textarea>
            </label>
            <label class="ls-label col-md-12"> <b class="ls-label-text">OBSERVAÇÃO</b>
              <textarea name="ocorrencia_func_obs" cols="50" rows="3" tabindex="2"><?= htmlentities($row_Ocorrencia['ocorrencia_func_obs'], ENT_COMPAT, 'utf-8') ?></textarea>
            </label>
            <input type="hidden" name="ocorrencia_vinculo_id_funcionario" value="<?php echo $row_Vinculo['vinculo_id_funcionario']; ?>">
            <input type="hidden" name="ocorrencia_func_id_escola" value="<?php echo $row_Vinculo['vinculo_id_escola']; ?>">
            <input type="hidden" name="MM_update" value="form1">
          	  
	  </p>
    </div>
	
    <div class="ls-modal-footer">
		<button class="ls-btn ls-float-right" data-dismiss="modal" tabindex="4">CANCELAR</button>
		<input type="submit" value="EDITAR OCORRÊNCIA" class="ls-btn-primary" tabindex="3">    
		</div>
	
	</form>
	
  </div>
</div><!-- /.modal -->
    
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
      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
      <li><a href="#">&gt; Guia</a></li>
      <li><a href="#">&gt; Wiki</a></li>
    </ul>
  </nav>
</aside>



<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script type="text/javascript" src="../js/jquery.mask.min.js"></script> 
<script src="js/mascara.js"></script> 
<script>
	 locastyle.modal.open("#myAwesomeModal");
	</script> 

</body>
</html>
<?php
mysql_free_result($Pontos);

mysql_free_result($Etapa);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($VinculoEditar);
?>
