<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/inverteData.php'); ?>
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
	
  $logoutGoTo = "../../index.php?exit";
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
$MM_authorizedUsers = "1,99";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, 
turma_total_alunos, turma_ano_letivo, escola_id, escola_nome 
FROM smc_turma
INNER JOIN smc_escola ON escola_id = turma_id_escola  
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND turma_id_sec = '$row_Secretaria[sec_id]' AND turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

if ($totalRows_Turma < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
	}


$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_cod_inep, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_cpf,
CASE vinculo_aluno_situacao
WHEN 1 THEN '<strong>MATRICULADO</strong>'
WHEN 2 THEN '<span style=\"color:silver\">TRANSFERIDO</span>'
WHEN 3 THEN '<span style=\"color:red\">DESISTENTE</span>'
WHEN 4 THEN '<span style=\"color:red\">FALECIDO</span>'
WHEN 5 THEN '<span style=\"color:red\">OUTROS</span>'
END AS vinculo_aluno_situacao_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_turma = %s
ORDER BY aluno_nome ASC", GetSQLValueString($colname_Alunos, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);
?>

<!DOCTYPE html>
<html class="ls-theme-green">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css">
<script src="js/locastyle.js"></script><link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once "menu.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ALUNOS DA TURMA</h1>

    <a href="matriculas.php" class="ls-btn-primary">VOLTAR</a>


    <div class="ls-box ls-board-box">
    
   <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Dados atualizados em <strong><?php echo date("d/m/Y"); ?> às <?php echo date("H\hi"); ?></strong></p>
    <h2 class="ls-title-3"><?php echo $row_Turma['turma_nome']; ?></h2>
    <p class="ls-small-info"><?php echo $row_Turma['escola_nome']; ?></p>
  </header>
    
    
      <table class="ls-table ls-table-striped">
        <thead>
        <tr>
          <th width="60" class="ls-txt-center"></th>
          <th class="ls-txt-center">ALUNO</th>
          <th width="130" class="ls-txt-center">INEP</th>
          <th width="150" class="ls-txt-center">CPF</th>
          <th width="150" class="ls-txt-center">NASCIMENTO</th>
          <th width="150" class="ls-txt-center">SITUAÇÃO</th>
        </tr>
        </thead>
        <tbody>
        <?php 
		  $situacao1 = 0;
		  $situacao2 = 0;
		  $situacao3 = 0;
		  $situacao4 = 0;
		  $situacao5 = 0;
		$n = 1;
		do { 
		?>
          <tr>
            <td class="ls-txt-center"><strong><?php echo $n; $n++; ?></strong></td>
            <td class="ls-txt-left"><?php echo $row_Alunos['aluno_nome']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Alunos['aluno_cod_inep']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Alunos['aluno_cpf']; ?></td>
            <td class="ls-txt-center"><?php echo inverteData($row_Alunos['aluno_nascimento']); ?></td>
            <td class="ls-txt-center"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></td>
          </tr>
          
          <?php 

		  
		  switch ($row_Alunos['vinculo_aluno_situacao']) {
			  
			  case 1:
			  $situacao1++;
			  break;
			  case 2:
			  $situacao2++;
			  break;
			  case 3:
			  $situacao3++;
			  break;
			  case 4:
			  $situacao4++;
			  break;
			  case 5:
			  $situacao5++;
			  break;
			  }
		  
		  
		  ?>
          
          
          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
      	</tbody>
      </table>
    </div>
    
    
        <div class="ls-box">
        
        
    <!-- CHART -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
      ['MATRICULADOS (<?php echo $situacao1; ?>)', <?php echo $situacao1; ?>],
      ['TRANSFERIDOS (<?php echo $situacao2; ?>)', <?php echo $situacao2; ?>],
      ['DESISTENTES (<?php echo $situacao3; ?>)', <?php echo $situacao3; ?>],
      ['FALECIDOS (<?php echo $situacao4; ?>)', <?php echo $situacao4; ?>],
      ['OUTROS (<?php echo $situacao5; ?>)', <?php echo $situacao5; ?>]
        ]);
        var options = {'title':'SITUAÇÃO DA TURMA',
						is3D:false,
						pieSliceText:'percentage',
						fontSize:'12',
						legend:'bottom',
                      // 'width':400,
                       //'height':300
					   };
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

<!-- CHART -->
        </script>
        
        <div id="chart_div" style="width:100%; height:400px; padding-right:15px;"></div>

        </div>

<p>&nbsp;</p>
    
    
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Turma);

mysql_free_result($Alunos);
?>