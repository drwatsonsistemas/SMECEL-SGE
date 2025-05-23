<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
include "../escola/fnc/alunosConta.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_ue = '1' AND escola_situacao = '1'";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$url_atual = "$_SERVER[REQUEST_URI]";
	$url_atual = explode("?", $url_atual);
	
	if (isset($url_atual[1])) {
	if ($url_atual[1]=="") {
		$url_atual[1]="";
		} else {
	$url_atual[1]=$url_atual[1];
	}
	} else {
		
		$url_atual[1]="";
		}
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
    <h1 class="ls-title-intro ls-ico-home">MATRÍCULAS POR TURMA</h1>
    
   <a href="index.php" class="ls-btn-primary">VOLTAR</a>      <a href="impressao/matriculas.php?<?php echo $url_atual[1]; ?>" class="ls-btn" target="_blank">IMPRIMIR</a>


  <?php 
  $totalGeral = 0;
  do { 
  ?>
      
  <div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <h2 class="ls-title-3"><?php echo $row_Escolas['escola_nome']; ?></h2>
  </header>        
  
  
  <?php
	mysql_select_db($database_SmecelNovo, $SmecelNovo);
	$query_Alunos = "
	SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
	vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
	vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
	vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_etapa, turma_ano_letivo, turma_total_alunos, turma_multisseriada, COUNT(*) as total, escola_id, escola_situacao,
	etapa_id, etapa_nome, etapa_limite_turma,
	CASE turma_turno
	WHEN 0 THEN 'INTEGRAL' 
	WHEN 1 THEN 'MATUTINO' 
	WHEN 2 THEN 'VESPERTINO' 
	WHEN 3 THEN 'NOTURNO'
	END AS turno 
	FROM smc_vinculo_aluno
	INNER JOIN smc_turma ON vinculo_aluno_id_turma = turma_id
	INNER JOIN smc_etapa ON etapa_id = turma_etapa 
	INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
	WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_escola = '$row_Escolas[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_situacao = '1' AND escola_situacao = '1'
	GROUP BY vinculo_aluno_id_turma
	ORDER BY turma_turno ASC, turma_etapa ASC, turma_nome ASC";
	$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
	$row_Alunos = mysql_fetch_assoc($Alunos);
	$totalRows_Alunos = mysql_num_rows($Alunos);
  ?>      
        
		<?php if ($totalRows_Alunos > 0) { ?>
		
        <table class="ls-table ls-sm-space" width="100%">
        <thead>
        <tr>
          <th width="50"></th>
          <th width="40" class="ls-txt-center"></th>
          <th>TURMA</th>
          <th class="ls-txt-center">TURNO</th>
          <th class="ls-txt-center">TOTAL DE ALUNOS</th>
          <th class="ls-txt-center">%</th>          
          <th class="ls-txt-center" width="80"></th>
        </tr>
        </thead>
        <tbody>
        <?php 
		$totalAlunos = 0;
		$n = 1;
		$total = 0;
		do { ?>
          <tr>
            <td class="ls-txt-center"><?php echo $n; $n++;?></td>
			<td class=""><?php if ($row_Alunos['turma_multisseriada']=="1") { ?><span class="ls-ico-tree" title="Turma multisseriada"></span><?php } ?></td>
            <td><?php echo $row_Alunos['turma_nome']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Alunos['turno']; ?></td>
            <td class="ls-txt-center">
			
			
			
			<?php 
					if ($row_Alunos['turma_total_alunos']<>"") {
						$limiteAlunos = $row_Alunos['turma_total_alunos'];
					} else {
						$limiteAlunos = $row_Alunos['etapa_limite_turma'];
					}
					$alunosTurma = alunosConta($row_Alunos['turma_id'], $row_AnoLetivo['ano_letivo_ano']);
					echo $alunosTurma;
					$totalAlunos = $totalAlunos + $alunosTurma;
					?>/<?php echo $limiteAlunos; ?>
			
            <?php
					$perc = (($alunosTurma/$limiteAlunos)*100);
					$percentual = number_format($perc, 0);
					
					if ($percentual > 100) {
						$excedeu = $percentual-100;
						$percentual = 100;
					}
					?>
                    			
			         <?php if ($alunosTurma > $limiteAlunos) { ?>
                    <a class="ls-ico-info ls-ico-left ls-color-danger" title="Excedeu <?php echo $excedeu; ?>% do limite da turma"></a>
					<?php } ?>

			
			</td>
            
            <td class="ls-txt-center"> 
            

                    
        

					<div data-ls-module="progressBar" role="progressbar" aria-valuenow="<?php echo $percentual; ?>" class="ls-animated" <?php if ($row_Alunos['total'] > $row_Alunos['etapa_limite_turma']) { ?> style="background-color:#900;"<?php } ?>></div>
					
					</td>
                    
            <td class="ls-txt-center"><a href="alunos.php?turma=<?php echo $row_Alunos['turma_id']; ?>">Listar</a></td>
			
          </tr>
          <?php $total = $total+$row_Alunos['total']; ?>
          <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
          <tr>
          	<td></td>
          	<td></td>
          	<td class="ls-txt-right"></td>
          	<td class="ls-txt-center"><strong><?php echo $total; ?></strong></td>
			<td></td>
          </tr>
          </tbody>
      </table>
      
     
    
    <?php $totalGeral = $totalGeral+$total; ?>
	
	
  <?php } else { ?>
    <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum aluno matriculado nesta turma.</div>
  <?php } ?>	
  
  </div>
  
  <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
    
    

    
 
  
  <p>Total de alunos no município: <?php echo $totalGeral; ?></p>
  
  <hr>
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

mysql_free_result($Alunos);

mysql_free_result($Escolas);
?>