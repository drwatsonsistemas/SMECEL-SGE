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
SELECT patrimonio_item_id, patrimonio_item_escola_id, patrimonio_item_descricao, patrimonio_item_grupo_id, patrimonio_item_etiqueta, 
patrimonio_item_num_serie, patrimonio_item_marca, patrimonio_item_modelo, patrimonio_item_cor, patrimonio_item_dimencoes, 
patrimonio_item_situacao, patrimonio_item_observacoes, patrimonio_grupo_bens_id, patrimonio_grupo_bens_descricao,
CASE patrimonio_item_situacao
WHEN 1 THEN 'EM USO'
WHEN 2 THEN 'OCIOSO'
WHEN 3 THEN 'ANTIECONOMICO'
WHEN 4 THEN 'RECUPERAVEL'
WHEN 5 THEN 'INSERVIVEL'
END patrimonio_item_situacao 
FROM smc_patrimonio_item
INNER JOIN smc_patrimonio_grupo_bens ON patrimonio_grupo_bens_id = patrimonio_item_grupo_id
WHERE patrimonio_item_escola_id = '$row_UsuLogado[usu_escola]'
";
$Listar = mysql_query($query_Listar, $SmecelNovo) or die(mysql_error());
$row_Listar = mysql_fetch_assoc($Listar);
$totalRows_Listar = mysql_num_rows($Listar);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
		if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: patrimonio.php?permissao"));
		break;
	}
	
$escola_id = $row_UsuLogado['usu_escola'];
	
  $insertSQL = sprintf("INSERT INTO smc_patrimonio_item (patrimonio_item_escola_id, patrimonio_item_descricao, patrimonio_item_grupo_id, patrimonio_item_etiqueta, patrimonio_item_num_serie, patrimonio_item_marca, patrimonio_item_modelo, patrimonio_item_cor, patrimonio_item_dimencoes, patrimonio_item_situacao, patrimonio_item_observacoes) VALUES ('$escola_id', %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['patrimonio_item_descricao'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_grupo_id'], "int"),
                       GetSQLValueString($_POST['patrimonio_item_etiqueta'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_num_serie'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_marca'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_modelo'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_cor'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_dimencoes'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_situacao'], "text"),
                       GetSQLValueString($_POST['patrimonio_item_observacoes'], "text"));

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
	
	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: patrimonio.php?permissao"));
		break;
	}
	
  $updateSQL = sprintf("UPDATE smc_patrimonio_item SET patrimonio_item_escola_id=%s WHERE patrimonio_item_id=%s",
                       GetSQLValueString($_POST['patrimonio_item_escola_id'], "int"),
                       GetSQLValueString($_POST['patrimonio_item_id'], "int"));

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

	<style>
	table.bordasimples {border-collapse: collapse; font-size:7px; }
	table.bordasimples tr td {border:1px solid #808080; padding:2px; font-size:12px;}
	table.bordasimples tr th {border:1px solid #808080; padding:2px; font-size:9px;}
	.foo { 

 	writing-mode: vertical-lr;
	 -webkit-writing-mode: vertical-lr;
	 -ms-writing-mode: vertical-lr;

/* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
	-moz-transform:rotate(180deg);
	-o-transform: rotate(180deg); */
	
  }
</style>

</head>
<body onLoad="self.print()">

  <div class="ls-box">
		<span class="ls-float-left" style="margin-right:20px;"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
		<?php echo $row_EscolaLogada['escola_nome']; ?><br>
		<small>
		<?php echo $row_EscolaLogada['escola_endereco']; ?>, 
		<?php echo $row_EscolaLogada['escola_num']; ?> - 
		<?php echo $row_EscolaLogada['escola_bairro']; ?> - 
		<?php echo $row_EscolaLogada['escola_cep']; ?><br>
		CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
		<?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
		</small>
		</div>

			<div class="ls-txt-center" style="text-transform: uppercase;">
			<h3>RELATÓRIO DE PATRIMONIO</h3>
			</div>
            
            <hr>

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
<table width="100%" class="ls-sm-space ls-table-striped bordasimples">
  <thead>
  <tr>
    <th width="50" class="ls-txt-center">Nº</th>
    <th>DESCRIÇÃO</th>
    <th class="ls-txt-center">GRUPO</th>
    <th class="ls-txt-center">SITUAÇÃO</th>
  </tr>
  </thead>
  <?php $num = 1; ?>
  <?php do { ?>
    <tr>
      <td class="ls-txt-center"><strong><?php echo $num; ?></strong></td>
      <td><?php echo $row_Listar['patrimonio_item_descricao']; ?></td>
      <td class="ls-txt-center"><?php echo $row_Listar['patrimonio_grupo_bens_descricao']; ?></td>
      <td class="ls-txt-center"><?php echo $row_Listar['patrimonio_item_situacao']; ?></td>
    </tr>
    <?php $num++; ?>
    <?php } while ($row_Listar = mysql_fetch_assoc($Listar)); ?>
</table>

<hr>
<p class="ls-txt-center">
_______________________________________________<br>
Assinatura do responsável
</p>
<br>
<p class="ls-txt-center">
Impresso em <?php echo date("d/m/Y"); ?> às <?php echo date("H:i"); ?><br>
SMECEL | Sistema de Gestão Escolar | www.smecel.com.br

</p>



<?php } else { ?>

<div class="ls-alert-info">Nenhum ítem cadastrado.</div>

<?php } ?>



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
