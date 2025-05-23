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


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: funcListar.php?permissao"));
		break;
	}

$data = converteData($_POST['vinculo_data_inicio']);
$nomeFunc = $_POST['nome_funcionario'];

  $insertSQL = sprintf("INSERT INTO smc_vinculo (vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs) VALUES (%s, %s, %s, %s, %s, '$data', %s)",
                       GetSQLValueString($_POST['vinculo_id_escola'], "int"),
                       GetSQLValueString($_POST['vinculo_id_sec'], "int"),
                       GetSQLValueString($_POST['vinculo_id_funcionario'], "int"),
                       GetSQLValueString($_POST['vinculo_id_funcao'], "int"),
                       GetSQLValueString($_POST['vinculo_carga_horaria'], "text"),
                       //GetSQLValueString($_POST['vinculo_data_inicio'], "date"),
                       GetSQLValueString($_POST['vinculo_obs'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

// ** REGISTRO DE LOG DE USUÁRIO **
$usu = $_POST['usu_id'];
$esc = $_POST['usu_escola'];
$detalhes = $_POST['detalhes'];
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
'23', 
'($detalhes)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
// ** REGISTRO DE LOG DE USUÁRIO **
  
  
  $insertGoTo = "funcListar.php?vinculado=$nomeFunc";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
$query_Funcao = "SELECT funcao_id, funcao_secretaria_id, funcao_nome FROM smc_funcao WHERE funcao_secretaria_id = '$row_EscolaLogada[escola_id_sec]' ORDER BY funcao_nome ASC";
$Funcao = mysql_query($query_Funcao, $SmecelNovo) or die(mysql_error());
$row_Funcao = mysql_fetch_assoc($Funcao);
$totalRows_Funcao = mysql_num_rows($Funcao);

$colname_Funcionario = "-1";
if (isset($_GET['cod'])) {
  $colname_Funcionario = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionario = sprintf("SELECT func_id, func_id_sec, func_nome FROM smc_func WHERE func_id_sec = '$row_EscolaLogada[escola_id_sec]' AND func_id = %s", GetSQLValueString($colname_Funcionario, "int"));
$Funcionario = mysql_query($query_Funcionario, $SmecelNovo) or die(mysql_error());
$row_Funcionario = mysql_fetch_assoc($Funcionario);
$totalRows_Funcionario = mysql_num_rows($Funcionario);

if ($totalRows_Funcionario=="") {
	//echo "TODOS OS CAMPOS EM BRANCO";	
	header("Location: funcPesquisar.php?nada"); 
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

    <title>Vincular Funcionários</title>

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
  <body>
 <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Vincular Funcionário</h1>
        
        <form method="post" class="ls-form ls-form-horizontal row" name="form1" action="<?php echo $editFormAction; ?>">
          
          <fieldset>
          
    <label class="ls-label col-md-12">
      <b class="ls-label-text">NOME</b>
      <p class="ls-label-info">Nome do funcionário</p>
              <input type="text" name="" value="<?php echo $row_Funcionario['func_nome']; ?>" size="32" disabled>
    </label>
          


    <label class="ls-label col-md-12">
      <b class="ls-label-text">FUNÇÃO</b>
      <p class="ls-label-info">Informe a função</p>
      <div class="ls-custom-select">
			   <select name="vinculo_id_funcao"  class="ls-select" required>
                <option value="" >Escolha...</option>
                <?php do { ?>
                <option value="<?php echo $row_Funcao['funcao_id']?>" ><?php echo $row_Funcao['funcao_nome']?></option>
                <?php } while ($row_Funcao = mysql_fetch_assoc($Funcao)); ?>
              </select>   
       </div>       
    </label>
    
    <label class="ls-label col-md-6">
      <b class="ls-label-text">CARGA HORÁRIA</b>
      <p class="ls-label-info">Informe a função</p>
      <div class="ls-custom-select">
			   <select name="vinculo_carga_horaria" class="ls-select" required>
                <option value="" >Escolha...</option>
                <option value="20" >20h</option>
                <option value="30" >30h</option>
                <option value="40" >40h</option>
              </select>   
       </div>       
    </label>
          
			  <label class="ls-label col-md-6">
      <b class="ls-label-text">DATA</b>
      <p class="ls-label-info">Informe a data de início</p>
              <input type="text" name="vinculo_data_inicio" value="<?php echo date("d/m/Y"); ?>" size="32" class="date">
        </label>
    
              
              <label class="ls-label col-md-12">
      <b class="ls-label-text">OBSERVAÇÕES</b>
      <p class="ls-label-info">Observações gerais</p>
    
              <textarea name="vinculo_obs" cols="50" rows="5"></textarea>
    
    </label>
    
<label class="ls-label col-md-12">

              <input type="submit" value="VINCULAR" class="ls-btn-primary">
              <a class="ls-btn-dark" href="funcPesquisar.php">CANCELAR</a>
    
    </label>
    			  
              
              
          </fieldset>    
              
          <input type="hidden" name="vinculo_id_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="vinculo_id_sec" value="<?php echo $row_EscolaLogada['escola_id_sec']; ?>">
          <input type="hidden" name="nome_funcionario" value="<?php echo $row_Funcionario['func_nome']; ?>">
          <input type="hidden" name="vinculo_id_funcionario" value="<?php echo $row_Funcionario['func_id']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
		  
   	   	    <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="<?php echo $row_Funcionario['func_id']; ?> - <?php echo $row_Funcionario['func_nome']; ?>">

		  
        </form>
        
        <p>&nbsp;</p>
      </div>
    </main>

<?php include_once ("menu-dir.php"); ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
    <script type="text/javascript" src="../js/jquery.mask.min.js"></script>
	<script src="js/mascara.js"></script>
    
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Funcao);

mysql_free_result($Funcionario);
?>
