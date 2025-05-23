<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php //include "../../funcoes/anoLetivo.php"; ?>
<?php include "../../funcoes/inverteData.php"; ?>
<?php include "../../funcoes/idade.php"; ?>


<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../../index.php";
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
$MM_authorizedUsers = "1,4,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
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

$MM_restrictGoTo = "../../../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
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

$colname_Logado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_Logado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_Logado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

include "../../funcoes/anoLetivo.php";


$colname_Matricula = "-1";
if (isset($_GET['turma'])) {
  $colname_Matricula = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
aluno_id, aluno_nome, aluno_foto, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_cpf, aluno_nis, aluno_sus,
turma_id, turma_nome, turma_id_escola,
escola_id, escola_nome, escola_inep, escola_logo
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_id_turma = '$colname_Matricula' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome ASC

", GetSQLValueString($colname_Matricula, "int"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);


$firstDate = $row_Matricula['aluno_nascimento'];
$secondDate = date("Y-m-d");

$dateDifference = abs(strtotime($secondDate) - strtotime($firstDate));

$years  = floor($dateDifference / (365 * 60 * 60 * 24));
$months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
$days   = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *24) / (60 * 60 * 24));

$years." year,  ".$months." months and ".$days." days";

$anos = $years*12;

$meses = $months+$anos;




?>
<!DOCTYPE html>
<html>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>SMECEL - Sistema de Gestão Escolar Municipal</title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../1css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../1css/app.css"  media="screen,projection"/>

<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css1">


<style>
	table.bordasimples {border-collapse: collapse; font-size:11px; }
	table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:11px;}
	table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:11px;}


	</style>

<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="alert('Atenção: Configure sua impressora para o tamanho A4 e formato PAISAGEM');self.print();">


<img src="../../../img/logo/<?php echo $row_Matricula['escola_logo']; ?>" width="60" align="left">
<img src="../../../img/logo_pse.png" width="90" align="right">

<h4><?php echo $row_Matricula['escola_nome']; ?><br>INEP: <?php echo $row_Matricula['escola_inep']; ?><br>TURMA: <?php echo $row_Matricula['turma_nome']; ?></h4>

<h4 align="center">RELATÓRIO DE CONSUMO ALIMENTAR</h4>
<h5 align="center"></h5>

<?php if ($totalRows_Matricula > 0) { // Show if recordset not empty ?>

  <?php 
  $cod = 1;
  do { 
  ?>


<?php 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_c_alimentar = "
SELECT cons_alim_id, cons_alim_id_aluno, cons_alim_id_matricula, cons_alim_data, 
cons_alim_tomou_leite_peito_1, cons_alim_mingau_1, cons_alim_agua_cha_1, cons_alim_leite_vaca_1, cons_alim_formula_infantil_1, cons_alim_suco_fruta_1, cons_alim_fruta_1, cons_alim_comida_sal_1, cons_alim_outros_alimentos_bebidas_1, 
cons_alim_leite_peito_2, cons_alim_fruta_inteira_2, cons_alim_quantas_vezes_fruta_2, cons_alim_comida_sal_2, cons_alim_quantas_vezes_sal_2, cons_alim_oferecida_2, cons_alim_outro_leite_2, cons_alim_mingau_leite_2, cons_alim_iogurte_2, 
cons_alim_legumes_2, cons_alim_vegetal_2, cons_alim_verdura_folha_2, cons_alim_carne_boi_2, cons_alim_figado_2, cons_alim_feijao_2, cons_alim_arroz_2, cons_alim_hamburguer_2, cons_alim_bebidas_adoc_2, cons_alim_macarrao_inst_2, 
cons_alim_bisc_recheado_2, cons_alim_refeicoes_assistindo_3, cons_alim_quais_ref_cafe_3, cons_alim_quais_ref_lanche_3, cons_alim_quais_ref_almoco_3, cons_alim_quais_ref_lanche_tarde_3, cons_alim_quais_ref_jantar_3, 
cons_alim_quais_ref_ceia_3, cons_alim_feijao_3, cons_alim_frutas_3, cons_alim_verduras_3, cons_alim_hamburguer_3, cons_alim_bebidas_3, cons_alim_macarrao_inst_3, cons_alim_bisc_recheado_3, cons_alim_datetime 
FROM sms_pse_consumo_alimentar
WHERE cons_alim_id_matricula = '$row_Matricula[vinculo_aluno_id]'
ORDER BY cons_alim_id DESC
";
$c_alimentar = mysql_query($query_c_alimentar, $SmecelNovo) or die(mysql_error());
$row_c_alimentar = mysql_fetch_assoc($c_alimentar);
$totalRows_c_alimentar = mysql_num_rows($c_alimentar);

?>

            
            
      <?php if ($meses < 6) { ?>
      
      <div style="page-break-inside: avoid;" class="">	
          
      	  <table class="bordasimples" width="100%" style="font-size:12px;">
          <tr>
          <th class="ls-txt-left">NOME: <?php echo $row_Matricula['aluno_nome']; ?></th>
          <th class="ls-txt-left">NASCIMENTO: <?php echo inverteData($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">IDADE: <?php echo idade($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">SUS: <?php echo $row_Matricula['aluno_sus']; ?></th>
          <th class="ls-txt-left">CPF: <?php echo $row_Matricula['aluno_cpf']; ?></th>
          <th class="ls-txt-left">AÇÃO: <?php echo inverteData($row_c_alimentar['cons_alim_data']); ?></th>
          </tr>
          </table>
      
      <table class="bordasimples" width="100%" style="font-size:11px;">
        <tr>
        	<td>CRIANÇAS MENORES DE 6 MESES</td>
        	<td>SIM</td>
        	<td>NÃO</td>
        	<td>NÃO SABE</td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">A criança ontem tomou leite de peito?</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_tomou_leite_peito_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_tomou_leite_peito_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_tomou_leite_peito_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Mingau</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Água/chá</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_agua_cha_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_agua_cha_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_agua_cha_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Leite de vaca</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_vaca_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_vaca_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_vaca_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Fórmula infantil</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_formula_infantil_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_formula_infantil_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_formula_infantil_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Suco de fruta</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_suco_fruta_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_suco_fruta_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_suco_fruta_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Frutas</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Comida de sal (de panela, papa ou sopa)</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_1']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem a criança consumiu: Outros alimentos/bebidas</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outros_alimentos_bebidas_1']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outros_alimentos_bebidas_1']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outros_alimentos_bebidas_1']=="I") { echo "X"; } ?></td>
        </tr>
      
      </table>
      
      </div>
      <br>
      
      <?php } ?>

      
      
       <?php if (($meses >= 6) && ($meses <= 23)) { ?>
       
       
       <div style="page-break-inside: avoid;" class="">	
          
      	  <table class="bordasimples" width="100%" style="font-size:12px;">
          <tr>
          <th class="ls-txt-left">NOME: <?php echo $row_Matricula['aluno_nome']; ?></th>
          <th class="ls-txt-left">NASCIMENTO: <?php echo inverteData($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">IDADE: <?php echo idade($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">SUS: <?php echo $row_Matricula['aluno_sus']; ?></th>
          <th class="ls-txt-left">CPF: <?php echo $row_Matricula['aluno_cpf']; ?></th>
          <th class="ls-txt-left">AÇÃO: <?php echo inverteData($row_c_alimentar['cons_alim_data']); ?></th>
          </tr>
          </table>
          
          
          <table class="bordasimples" width="100%" style="font-size:11px;">
        <tr>
        	<td>CRIANÇAS DE 6 A 23 MESES</td>
        	<td width="50" class="ls-txt-center">SIM</td>
        	<td width="50" class="ls-txt-center">NÃO</td>
        	<td width="50" class="ls-txt-center">NÃO SABE</td>
        	<td width="50" class="ls-txt-center">QTD</td>            
        </tr>
     	<tr>
        	<td class="ls-txt-left">A criança ontem tomou leite do peito?</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_peito_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_peito_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_leite_peito_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem, a criança comeu fruta inteira, em pedaço ou amassada?</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_inteira_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_inteira_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_fruta_inteira_2']=="I") { echo "X"; } ?></td>
            <td class="ls-txt-center">
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_fruta_2']=="2") { echo "2"; } ?>
			<?php if ($row_c_alimentar['cons_alim_quantas_vezes_fruta_2']=="1") { echo "1"; } ?>
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_fruta_2']=="3") { echo "3 ou mais"; } ?>
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_fruta_2']=="I") { echo "N. Sabe"; } ?>
            </td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança comeu comida de sal (de panela, papa ou sopa)?</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_comida_sal_2']=="I") { echo "X"; } ?></td>
            <td class="ls-txt-center">
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_sal_2']=="2") { echo "2"; } ?>
			<?php if ($row_c_alimentar['cons_alim_quantas_vezes_sal_2']=="1") { echo "1"; } ?>
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_sal_2']=="3") { echo "3 ou mais"; } ?>
            <?php if ($row_c_alimentar['cons_alim_quantas_vezes_sal_2']=="I") { echo "N. Sabe"; } ?>
            </td>
        </tr>
     	<tr>
        	<td class="ls-txt-left" colspan="1">Se sim, essa comida foi oferecida:</td>
        	<td class="ls-txt-center" colspan="4">
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="1") { echo "EM PEDAÇOS"; } ?>
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="2") { echo "AMASSADA"; } ?>
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="3") { echo "PASSADA NA PENEIRA"; } ?>
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="4") { echo "LIQUIDIFICADA"; } ?>
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="5") { echo "SÓ O CALDO"; } ?>
			<?php if ($row_c_alimentar['cons_alim_oferecida_2']=="6") { echo "NÃO SABE"; } ?>
            </td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Outro leite que não o leite do peito</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outro_leite_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outro_leite_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_outro_leite_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Mingau com leite</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_leite_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_leite_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_mingau_leite_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Iogurte</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_iogurte_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_iogurte_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_iogurte_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Legumes (não considerar os utilizados como temperos, nem batata, mandioca/aipim/macaxeira, cará e inhame)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_legumes_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_legumes_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_legumes_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Vegetal ou fruta de cor alaranjada (abóbora ou jerimum, cenoura, mamão, manga) ou folhas verdes-escuras (couve, caruru, beldroega, bertalha, espinafre, mostarda)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_vegetal_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_vegetal_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_vegetal_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Verdura de folha (alface, acelga, repolho)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verdura_folha_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verdura_folha_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verdura_folha_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Carne (boi, frango, peixe, porco, miúdos, outras) ou ovo</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_carne_boi_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_carne_boi_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_carne_boi_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Fígado</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_figado_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_figado_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_figado_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Feijão</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Arroz, batata, inhame, aipim/macaxeira/mandioca, farinha ou macarrão (sem ser instantâneo)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_arroz_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_arroz_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_arroz_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Hambúrguer e/ou embutidos (presunto, mortadela, salame, linguiça, salsicha)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem a criança consumiu: Bebidas adoçadas (refrigerante, suco de caixinha, suco em pó, água de coco de caixinha, xaropes de guaraná/groselha, suco de fruta com adição de açúcar)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_adoc_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_adoc_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_adoc_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Macarrão instantâneo, salgadinhos de pacote ou biscoitos salgados</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Biscoito recheado, doces ou guloseimas (balas, pirulitos, chiclete, caramelo, gelatina)</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_2']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_2']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_2']=="I") { echo "X"; } ?></td>
            <td></td>
        </tr>

        </table>
      
      
      </div>
      <br>
      
      
      
      <?php } ?>

      
      
      
      
      
      
      <?php if ($meses >= 24) { ?>
      
      	  <div style="page-break-inside: avoid;" class="">	
          
      	  <table class="bordasimples" width="100%" style="font-size:12px;">
          <tr>
          <th class="ls-txt-left">NOME: <?php echo $row_Matricula['aluno_nome']; ?></th>
          <th class="ls-txt-left">NASCIMENTO: <?php echo inverteData($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">IDADE: <?php echo idade($row_Matricula['aluno_nascimento']); ?></th>
          <th class="ls-txt-left">SUS: <?php echo $row_Matricula['aluno_sus']; ?></th>
          <th class="ls-txt-left">CPF: <?php echo $row_Matricula['aluno_cpf']; ?></th>
          <th class="ls-txt-left">AÇÃO: <?php echo inverteData($row_c_alimentar['cons_alim_data']); ?></th>
          </tr>
          </table>
          
         <table class="bordasimples" width="100%" style="font-size:11px;">
        <tr>
        	<td>CRIANÇAS COM 2 ANOS OU MAIS, ADOLESCENTES, ADULTOS, GESTANTES E IDOSOS</td>
        	<td>SIM</td>
        	<td>NÃO</td>
        	<td>NÃO SABE</td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Você tem costume de realizar as refeições assistindo à TV, mexendo no computador e/ou celular?</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_refeicoes_assistindo_3']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_refeicoes_assistindo_3']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_refeicoes_assistindo_3']=="I") { echo "X"; } ?></td>
        </tr>
        <tr>
            <td colspan="4">Quais refeições você faz ao longo do dia?
            <label><input type="checkbox" name="cons_alim_quais_ref_cafe_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_cafe_3']=="S") { echo "checked"; } ?>  >&nbsp;Café da manhã</label>
            <label><input type="checkbox" name="cons_alim_quais_ref_lanche_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_lanche_3']=="S") { echo "checked"; } ?> >&nbsp;Lanche da manhã</label>
            <label><input type="checkbox" name="cons_alim_quais_ref_almoco_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_almoco_3']=="S") { echo "checked"; } ?> >&nbsp;Almoço</label>
            <label><input type="checkbox" name="cons_alim_quais_ref_lanche_tarde_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_lanche_tarde_3']=="S") { echo "checked"; } ?> >&nbsp;Lanche da tarde</label>
            <label><input type="checkbox" name="cons_alim_quais_ref_jantar_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_jantar_3']=="S") { echo "checked"; } ?> >&nbsp;Jantar</label>
            <label><input type="checkbox" name="cons_alim_quais_ref_ceia_3" value="" <?php if ($row_c_alimentar['cons_alim_quais_ref_ceia_3']=="S") { echo "checked"; } ?> >&nbsp;Ceia</label>
            </td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu FEIJÃO:</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_feijao_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu Frutas frescas (não considerar suco de frutas):</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_frutas_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_frutas_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_frutas_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu Verduras e/ou legumes (não considerar batata, mandioca, aipim, macaxeira, cará e inhame):</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verduras_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verduras_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_verduras_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu Hambúrguer e/ou embutidos (presunto, mortadela, salame, linguiça, salsicha):</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_hamburguer_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu Bebidas adoçadas (refrigerante, suco de caixinha, suco em pó, água de coco de caixinha, xaropes de guaraná/groselha, suco de fruta com adição de açúcar):</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bebidas_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td  class="ls-txt-left">Ontem, você consumiu Macarrão instantâneo, salgadinhos de pacote ou biscoitos salgados:</td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_3']=="S") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_3']=="N") { echo "X"; } ?></td>
        	<td  class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_macarrao_inst_3']=="I") { echo "X"; } ?></td>
        </tr>
     	<tr>
        	<td class="ls-txt-left">Ontem, você consumiu Biscoito recheado, doces ou guloseimas (balas, pirulitos, chiclete, caramelo, gelatina):</td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_3']=="S") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_3']=="N") { echo "X"; } ?></td>
        	<td class="ls-txt-center"><?php if ($row_c_alimentar['cons_alim_bisc_recheado_3']=="I") { echo "X"; } ?></td>
        </tr>
     </table>
     </div>
      <br>
      <?php } ?>
      
  
  <?php } while ($row_Matricula = mysql_fetch_assoc($Matricula)); ?>
  
  <p class="right">Total de alunos: <?php echo $totalRows_Matricula; ?></p>
  <small class="right">Impresso em <?php echo date("d/m/Y à\s H\hi "); ?> - SMECEL | www.smecel.com.br</small>
  
  <?php } else { ?>
    
  <div class="card-panel">
  <blockquote>NENHUM ALUNO CADASTRADO</blockquote>
  </div>

  <?php } // Show if recordset not empty ?>
  
  
  
<!-- FIM CONTAINER -->

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript" src="../../js/jquery.mask.min.js"></script> 
<script type="text/javascript" src="../../js/mascara.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$(".dropdown-trigger").dropdown();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($c_alimentar);

mysql_free_result($Matricula);
?>
