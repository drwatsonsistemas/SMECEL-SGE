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
aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_nis, aluno_sus, aluno_cpf,
turma_id, turma_nome, turma_id_escola
escola_id, escola_nome, escola_inep, escola_logo
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_id_turma = '$colname_Matricula' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome ASC", GetSQLValueString($colname_Matricula, "int"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);
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
<link type="text/css" rel="stylesheet" href="../1css/app.css" media="screen,projection"/>

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

<h4 align="center">RELATÓRIO DE SAÚDE BUCAL 9 ANOS</h4>
<h5 align="center">Ação coletiva de exame bucal com finalidade epidemiológica</h5>

<?php if ($totalRows_Matricula > 0) { // Show if recordset not empty ?>
  <table class="bordasimples" width="100%" style="font-size:11px;">
    <thead>
      <tr>
        <th></th>
        <th>ALUNO</th>
        <th class="center">NASC</th>
        <th class="center">I</th>
        <th class="center">SUS</th>
        <th class="center">CPF</th>
        <th class="center">ALT</th>
        <th class="center">AÇÃO</th>
        </tr>
    </thead>
    <tbody>
    
    
    
          <?php 
	  
	  $c = 0;
	  $o = 0;
	  $p = 0;
	  $pe = 0;
	  $ge = 0;
	  
	  
	  ?>
    
      <?php 
  $cod = 1;
  do { 
  ?>
  
  <?php $idadeA = idade($row_Matricula['aluno_nascimento']); ?>
  
   <?php if ($idadeA == 9) { ?>
  
  <?php
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_saude_bucal = "
			SELECT pse_s_bucal_id, pse_s_bucal_aluno_id, pse_s_bucal_matricula_id, pse_s_bucal_data, pse_s_bucal_qtd_dentes, pse_s_bucal_decidua, pse_s_bucal_permanente, pse_s_bucal_doenca_periodontal, pse_s_bucal_gengivite,
			CASE pse_s_bucal_doenca_periodontal
			WHEN 0 THEN '-'
			WHEN 1 THEN 'S'
			END AS pse_s_bucal_doenca_periodontal_nome, 
			CASE pse_s_bucal_gengivite
			WHEN 0 THEN '-'
			WHEN 1 THEN 'S'
			END AS pse_s_bucal_gengivite_nome, 
			pse_s_bucal_c1, pse_s_bucal_p1, pse_s_bucal_o1, pse_s_bucal_cpod1, 
			pse_s_bucal_c2, pse_s_bucal_ei2, pse_s_bucal_o2, pse_s_bucal_ceod2, 
			pse_s_bucal_observacoews, pse_s_bucal_inicio_tratamento, pse_s_bucal_final_tratamento, pse_s_bucal_inicio_tratamento_data_hora, 
			pse_s_bucal_cirurgiao_dentista, pse_s_bucal_asb 
			FROM sms_pse_saude_bucal
			WHERE pse_s_bucal_matricula_id = '$row_Matricula[vinculo_aluno_id]'
			ORDER BY pse_s_bucal_id DESC
			";
			$saude_bucal = mysql_query($query_saude_bucal, $SmecelNovo) or die(mysql_error());
			$row_saude_bucal = mysql_fetch_assoc($saude_bucal);
			$totalRows_saude_bucal = mysql_num_rows($saude_bucal);
			
			$totalD = $row_saude_bucal['pse_s_bucal_c1']+$row_saude_bucal['pse_s_bucal_p1']+$row_saude_bucal['pse_s_bucal_o1'];
			$totald = $row_saude_bucal['pse_s_bucal_c2']+$row_saude_bucal['pse_s_bucal_ei2']+$row_saude_bucal['pse_s_bucal_o2'];
			
			$c = $c+$row_saude_bucal['pse_s_bucal_c1']+$row_saude_bucal['pse_s_bucal_c2'];
			$o =$o+$row_saude_bucal['pse_s_bucal_o1']+$row_saude_bucal['pse_s_bucal_o2'];
			$p = $p+$row_saude_bucal['pse_s_bucal_p1']+$row_saude_bucal['pse_s_bucal_ei2'];
			
			if ($row_saude_bucal['pse_s_bucal_doenca_periodontal']==1) {
				$pe++;	
				}
			
			if ($row_saude_bucal['pse_s_bucal_gengivite']==1) {
				$ge++;	
				}
			

						
		?>
  
        <tr>
          <td class="center">
            <?php
	  echo $cod;
	  $cod++; 
	   ?>
          </td>
          <td><?php echo $row_Matricula['aluno_nome']; ?></td>
          <td class="center"><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></td>
          <td class="center"><?php echo idade($row_Matricula['aluno_nascimento']); ?></td>
          <td class="center"><?php echo $row_Matricula['aluno_sus']; ?></td>
          <td class="center"><?php echo $row_Matricula['aluno_cpf']; ?></td>
          <td class="center"><?php if (($totalD > 0) || ($totald > 0)) { ?><strong class="ls-tag">SIM</strong><?php } ?></td>
          <td class="center"><?php echo inverteData($row_saude_bucal['pse_s_bucal_data']); ?></td>
        </tr>
        
        
        
        
        
           <?php } ?>

        
        <?php } while ($row_Matricula = mysql_fetch_assoc($Matricula)); ?>
    </tbody>
  </table>
  
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

mysql_free_result($Matricula);
?>
