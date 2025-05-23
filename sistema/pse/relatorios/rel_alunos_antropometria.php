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

SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, 
aluno_id, aluno_nome, aluno_nascimento, aluno_sexo, aluno_filiacao1, aluno_nis, aluno_sus, aluno_cpf, 
turma_id, turma_nome, turma_turno,
escola_id, escola_nome, escola_inep, escola_logo,
CASE aluno_sexo
WHEN '1' THEN 'M'
WHEN '2' THEN 'F'
END AS aluno_sexo_legenda, 
CASE turma_turno 
WHEN 0 THEN 'INTEGRAL' 
WHEN 1 THEN 'MATUTINO' 
WHEN 2 THEN 'VESPERTINO' 
WHEN 3 THEN 'NOTURNO' 
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN '<span class=\"ls-color-success\">MATRIC</span>'
WHEN 2 THEN '<span class=\"ls-color-warning\">TRANSF</span>'
WHEN 3 THEN '<span class=\"ls-color-warning\">DESIST</span>'
WHEN 4 THEN '<span class=\"ls-color-warning\">FALECI</span>'
WHEN 5 THEN '<span class=\"ls-color-warning\">OUTROS</span>'
END AS vinculo_aluno_situacao 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE vinculo_aluno_id_turma = %s AND vinculo_aluno_situacao = '1'
ORDER BY aluno_nome ASC


", GetSQLValueString($colname_Matricula, "int"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

function idadeTempo ($idade,$data) {
	if ($idade <> "") {
    // Declara a data! :P
    $data = $idade;
    // Separa em dia, mês e ano
    list($ano, $mes, $dia) = explode('-', $data);
    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
	} else {
		$idade = "-";
	}
    return $idade;
}

function imc ($peso,$altura,$sexo,$idade) {
	$imc = $peso/($altura*$altura);
	return number_format($imc, 1);
	}

function idadeMeses ($idade, $pesagem) {
	$DataInicial = getdate(strtotime($idade));
	$DataFinal = getdate(strtotime($pesagem));
	$Dif = ($DataFinal[0] - $DataInicial[0]) / 86400;
	return $meses = round($Dif/30);
}

$semPeso = 0;
$magrezaAcentuada = 0;
$magreza = 0;
$eutrofia = 0;
$riscoDeSobrepeso = 0;
$sobrepeso = 0;
$obesidade = 0;
$obesidadeGrave = 0;

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

<h4 align="center">RELATÓRIO ANTROPOMÉTRICO</h4>
<h5 align="center"></h5>

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
        <th class="center" width="60">PESO</th>
        <th class="center" width="60">ALT</th>
        <th>ALT</th>
        <th>AÇÃO</th>
        </tr>
    </thead>
    <tbody>
      <?php 
  $cod = 1;
  do { 
  ?>
        <tr>
          <td class="center">
            <?php
	  echo $cod;
	  $cod++; 
	  
	  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria = sprintf("SELECT * FROM sms_pse_antropometria WHERE antrop_id_matricula = %s ORDER BY antrop_id DESC", GetSQLValueString($row_Matricula['vinculo_aluno_id'], "int"));
			$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
			$row_antropometria = mysql_fetch_assoc($antropometria);
			$totalRows_antropometria = mysql_num_rows($antropometria);
	  
	   ?>
          </td>
          <td><?php echo $row_Matricula['aluno_nome']; ?></td>
          <td class="center"><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></td>
          <td class="center"><?php echo idade($row_Matricula['aluno_nascimento']); ?></td>
          <td class="center"><?php echo $row_Matricula['aluno_sus']; ?></td>
          <td class="center"><?php echo $row_Matricula['aluno_cpf']; ?></td>
          <td class="center"><?php echo $row_antropometria['antrop_peso']; ?></td>
          <td class="center"><?php echo $row_antropometria['antrop_altura']; ?></td>
          
          
          
          <?php
		  
			
			

			
			$imc = "-";
			
			if ($totalRows_antropometria > 0) {
				
				
			$imc = imc($row_antropometria['antrop_peso'],$row_antropometria['antrop_altura'],1,1); 
			
			
			$idade_anos = idadeTempo($row_Matricula['aluno_nascimento'],$row_antropometria['antrop_data']);
			$idade_meses = idadeMeses($row_Matricula['aluno_nascimento'],$row_antropometria['antrop_data']);		
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_tabela_imc = "
			SELECT imc_id, imc_meses, imc_sexo, 
			imc_L, imc_M, imc_S, 
			imc_P01, imc_P1, imc_P3, imc_P5, imc_P10, imc_P15, imc_P25, imc_P50, imc_P75, imc_P85, imc_P90, imc_P95, imc_P97, imc_P99, imc_P999, 
			imc_SD4neg, imc_SD3neg, imc_SD2neg, imc_SD1neg, imc_SD0, imc_SD1, imc_SD2, imc_SD3, imc_SD4 
			FROM sms_pse_imc
			WHERE imc_meses = '$idade_meses' AND imc_sexo = '$row_Matricula[aluno_sexo]'";
			$tabela_imc = mysql_query($query_tabela_imc, $SmecelNovo) or die(mysql_error());
			$row_tabela_imc = mysql_fetch_assoc($tabela_imc);
			$totalRows_tabela_imc = mysql_num_rows($tabela_imc);
			
			if ($idade_meses < 60) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$sobrepeso++;
					
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; SIM &nbsp;</span>";
					$obesidade++;
					
				}
				
			} else if (($idade_meses >= 60) && ($idade_meses <= 120)){
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; SIM &nbsp;</span>";
					$obesidade++;
					
				}
					
					
			} else if (($idade_meses >= 121) && ($idade_meses < 240)) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$magrezaAcentuada++;

				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$obesidade++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; SIM grave&nbsp;</span>";
					$obesidadeGrave++;
					
				}
				
				
				} else if (($idade_meses >= 240) && ($idade_meses < 720)) {
					
				if ($imc < 18.5) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$magreza++;

				} else if (($imc >= 18.5) && ($imc < 25) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc >= 25) && ($imc < 30) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc >= 30) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$obesidade++;
					
				}
				
				
				} else if ($idade_meses >= 720) {
					
					
					
				if ($imc <= 22) {
					$res = "<span class=\"ls-color-danger\">&nbsp; SIM &nbsp;</span>";
					$magreza++;

				} else if (($imc > 22) && ($imc < 27) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; &nbsp;</span>";
					$eutrofia++;
					
				} else if ($imc >= 27) {
					$res = "<span class=\"ls-color-warning\">&nbsp; SIM &nbsp;</span>";
					$sobrepeso++;
					
				}
					
						
				}
	 
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$res."</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".inverteData($row_antropometria['antrop_data'])."</td>";
				
			} else {
				
				$semPeso++;
				
				echo "<td class=\"ls-txt-center ls-display-none-xs\"></td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\"></td>";
				}
		  
		  ?>
                    
          
          
        </tr>
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
