<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>
<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case 'text':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'long':
      case 'int':
        $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
        break;
      case 'double':
        $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
        break;
      case 'date':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'defined':
        $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_prefeitura, sec_uf, sec_logo, sec_nome, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_email 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die (mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_FrequenciaFuncionario = '-1';
if (isset($_GET['c'])) {
  $colname_FrequenciaFuncionario = $_GET['c'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_FrequenciaFuncionario = sprintf("
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, 
vinculo_data_inicio, vinculo_obs, func_id, func_nome, func_cpf, func_rg_numero, funcao_id, funcao_nome,
func_endereco, func_endereco_numero, func_endereco_bairro, func_municipio_nascimento, func_endereco_uf, func_endereco_cidade, func_regime, func_estado_civil
FROM smc_vinculo
INNER JOIN smc_func ON vinculo_id_funcionario = func_id 
INNER JOIN smc_funcao ON vinculo_id_funcao = funcao_id 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND func_id = %s", GetSQLValueString($colname_FrequenciaFuncionario, 'int'));
$FrequenciaFuncionario = mysql_query($query_FrequenciaFuncionario, $SmecelNovo) or die (mysql_error());
$row_FrequenciaFuncionario = mysql_fetch_assoc($FrequenciaFuncionario);
$totalRows_FrequenciaFuncionario = mysql_num_rows($FrequenciaFuncionario);

//regime
$regime;
if($row_FrequenciaFuncionario['func_regime'] == 1){
  $regime = 'concursado';
}
if($row_FrequenciaFuncionario['func_regime'] == 2){
  $regime= 'contratado';
} 
if($row_FrequenciaFuncionario['func_regime'] == 3){
  $regime = 'comissionado';
}

//estado civil
$estado_civil;
if($row_FrequenciaFuncionario['func_estado_civil'] == 1){
  $estado_civil = 'solteiro';
}
if($row_FrequenciaFuncionario['func_estado_civil'] == 2){
  $estado_civil = 'casado';
}
if($row_FrequenciaFuncionario['func_estado_civil'] == 3){
  $estado_civil = 'viúvo';
}
if($row_FrequenciaFuncionario['func_estado_civil'] == 4){
  $estado_civil = 'união estável';
}
if($row_FrequenciaFuncionario['func_estado_civil'] == 5){
  $estado_civil = '';
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
    <title>LICENÇA PREMIO - <?php echo $row_FrequenciaFuncionario['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css"><script src="js/locastyle.js"></script>
    
<style media="print">
.no_imp {
	display: none;
}
.pagebreak {
	page-break-before: always;
}
</style>

    
    <style>
table.bordasimples {
	border-collapse: collapse;
	font-size:7px;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:4px;
	font-size:9px;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:3px;
	font-size:9px;
}
</style>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    </head>
    <body onload="self.print();">
<div class="container-fluid"> 
      <!-- CONTEÚDO -->
      
      <div class="ls-box1 ls-sm-space" style="page-break-after: 1always;">

      
    <br><br>

        
        
        <div class="row">
  <div class="col-xs-12 ls-txt-center">
  
	<p><h1>Requerimento</h1></p><br>
  <p style="line-height: 180%; text-align:right; font-size:16px;t">
<?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
<?php
setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
echo strftime('%d de %B de %Y', strtotime('today'));
?>
</p>
  <br><br><br>
	
  </div>
</div>
<p style="line-height: 180%; text-align:justify; font-size:16px;">
Prezado(a) Secretário(a) de Educação
</p>

<br><br>    
        
  <p style="line-height: 180%; text-align:justify; font-size:16px;">

  Eu, <b><?= $row_FrequenciaFuncionario['func_nome'] ?></b>, brasileiro, <?= $estado_civil ?>, natural de <?= $row_FrequenciaFuncionario['func_municipio_nascimento'] ?>, 
  portador da Cédula de Identidade nº. <?= $row_FrequenciaFuncionario['func_rg_numero'] ?> 
  e do CPF nº. <?= $row_FrequenciaFuncionario['func_cpf'] ?>, 
  residente e domiciliado na rua <?= $row_FrequenciaFuncionario['func_endereco'] ?>, nº <?= $row_FrequenciaFuncionario['func_endereco_numero'] ?>,
  <?= $row_FrequenciaFuncionario['func_endereco_bairro'] ?>, <?= $row_FrequenciaFuncionario['func_endereco_cidade'] . '-' . $row_FrequenciaFuncionario['func_endereco_uf'] ?>,
  <?= $regime ?> para o cargo de <?= $row_FrequenciaFuncionario['funcao_nome'] ?>. 
  Venho muito respeitosamente, requerer a Vossa Excelência <b>Licença Prêmio de <?= $_GET['duracao_programa'] ?> em pagamento de pecúnia</b>. 
  Para garantia de tal beneficio vale-se dos dispostos nas leis: Municipal nº236, art.51º inciso II, e na lei do estatuto do servidor publico
  o direito à licença prêmio de 03 (três) meses a cada 05(cinco) anos de exercício efetivo e ininterrupto, 
  sem prejuízo da remuneração. 		
  <br><br>


  Nestes termos,<br>
  Pede deferimento.

    
  </p>


<br><br><br><br>

 <p style="line-height: 180%; text-align:center; font-size:16px;">_________________________________________________________<br>Servidor(a)</p>  
 
   
  </div>
      
      <!-- CONTEÚDO --> 
    </div>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FrequenciaFuncionario);
?>
