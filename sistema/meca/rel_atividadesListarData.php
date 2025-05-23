<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "funcoes/turno.php"; ?>
<?php include "funcoes/inverteData.php"; ?>
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
$MM_authorizedUsers = "1,2,3";
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

$currentPage = $_SERVER["PHP_SELF"];

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$maxRows_ListarAtividades = 99999999;
$pageNum_ListarAtividades = 0;
if (isset($_GET['pageNum_ListarAtividades'])) {
  $pageNum_ListarAtividades = $_GET['pageNum_ListarAtividades'];
}
$startRow_ListarAtividades = $pageNum_ListarAtividades * $maxRows_ListarAtividades;

$dataInicio = date('Y-m-d');
if (isset($_GET['dataInicio'])) {
  $dataInicio = $_GET['dataInicio'];
}

$dataFinal = date('Y-m-d');
if (isset($_GET['dataFinal'])) {
  $dataFinal = $_GET['dataFinal'];
}


$codEscola = "";
$buscaEscola = "";
$nomeEscola = "TODAS";
if (isset($_GET['escola'])) {
	
	if ($_GET['escola'] == "") {
	//echo "ESCOLA EM BRANCO";	
	header("Location: index.php?nada"); 
 	exit;
	}
	
	
			
  $codEscola = $_GET['escola'];
  $codEscola = (int)$codEscola;
  
  if ($_GET['escola'] == "0") {
	//echo "ESCOLA EM BRANCO";	
	$buscaEscola = "";
	} else {
		$buscaEscola = " AND smc_ativ_id_escola = $codEscola ";
	}
  
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarAtividades = "
SELECT smc_ativ_id, smc_ativ_data, date_format(smc_ativ_hora, '%H:%i') as hora, 
smc_ativ_id_escola, smc_ativ_id_turma, smc_ativ_qtd, smc_ativ_folhas, smc_ativ_duplex, 
smc_ativ_caminho, smc_ativ_hash, smc_ativ_obs, escola_id, escola_nome, turma_id, turma_nome, turma_turno 
FROM smc_atividade 
INNER JOIN smc_escola ON escola_id = smc_ativ_id_escola 
INNER JOIN smc_turma ON turma_id = smc_ativ_id_turma 
WHERE smc_ativ_data BETWEEN '$dataInicio' AND '$dataFinal' $buscaEscola ORDER BY smc_ativ_id DESC";
$query_limit_ListarAtividades = sprintf("%s LIMIT %d, %d", $query_ListarAtividades, $startRow_ListarAtividades, $maxRows_ListarAtividades);
$ListarAtividades = mysql_query($query_limit_ListarAtividades, $SmecelNovo) or die(mysql_error());
$row_ListarAtividades = mysql_fetch_assoc($ListarAtividades);

if (isset($_GET['totalRows_ListarAtividades'])) {
  $totalRows_ListarAtividades = $_GET['totalRows_ListarAtividades'];
} else {
  $all_ListarAtividades = mysql_query($query_ListarAtividades);
  $totalRows_ListarAtividades = mysql_num_rows($all_ListarAtividades);
}
$totalPages_ListarAtividades = ceil($totalRows_ListarAtividades/$maxRows_ListarAtividades)-1;

$hoje = date('Y-m-d');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContarImpressoesHojeHoje = "
SELECT 
smc_ativ_id, smc_ativ_data, smc_ativ_id_escola, smc_ativ_id_turma, smc_ativ_qtd, 
smc_ativ_folhas, smc_ativ_duplex, smc_ativ_caminho, smc_ativ_hash, smc_ativ_obs 
FROM smc_atividade 
WHERE smc_ativ_data BETWEEN '$dataInicio' AND '$dataFinal' $buscaEscola";
$ContarImpressoesHojeHoje = mysql_query($query_ContarImpressoesHojeHoje, $SmecelNovo) or die(mysql_error());
$row_ContarImpressoesHojeHoje = mysql_fetch_assoc($ContarImpressoesHojeHoje);
$totalRows_ContarImpressoesHojeHoje = mysql_num_rows($ContarImpressoesHojeHoje);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ContarImpressoesTotal = "
SELECT smc_ativ_id, smc_ativ_data, smc_ativ_id_escola, smc_ativ_id_turma, smc_ativ_qtd, smc_ativ_folhas, 
smc_ativ_duplex, smc_ativ_caminho, smc_ativ_hash, smc_ativ_obs 
FROM smc_atividade";
$ContarImpressoesTotal = mysql_query($query_ContarImpressoesTotal, $SmecelNovo) or die(mysql_error());
$row_ContarImpressoesTotal = mysql_fetch_assoc($ContarImpressoesTotal);
$totalRows_ContarImpressoesTotal = mysql_num_rows($ContarImpressoesTotal);

$queryString_ListarAtividades = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ListarAtividades") == false && 
        stristr($param, "totalRows_ListarAtividades") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ListarAtividades = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ListarAtividades = sprintf("&totalRows_ListarAtividades=%d%s", $totalRows_ListarAtividades, $queryString_ListarAtividades);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
escola_telefone1, escola_telefone2, escola_email, 
escola_inep, escola_cnpj, escola_logo, escola_ue 
FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_ue = '1' ORDER BY escola_nome";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

$colname_NomeEscola = "-1";
$nomeEscola = "";

if (isset($_GET['escola'])) {
  $colname_NomeEscola = $_GET['escola'];
  if ($_GET['escola'] == "0") {
	//echo "ESCOLA EM BRANCO";	
	$nomeEscola = "TODAS AS ESCOLAS";
	}
} else {
	$nomeEscola = "TODAS AS ESCOLAS";
	}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_NomeEscola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_id = %s", GetSQLValueString($colname_NomeEscola, "int"));
$NomeEscola = mysql_query($query_NomeEscola, $SmecelNovo) or die(mysql_error());
$row_NomeEscola = mysql_fetch_assoc($NomeEscola);
$totalRows_NomeEscola = mysql_num_rows($NomeEscola);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo</title>
<link rel="stylesheet" href="../../css/foundation.css">
<link rel="stylesheet" href="../../css/normalize.css">
<link rel="stylesheet" href="../../css/foundation-datepicker.css">
<!-- This is how you would link your custom stylesheet -->
<link rel="stylesheet" href="../css/app-painel.css">
<script src="../../js/vendor/modernizr.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
<?php include "menu.php"; ?>
<div class="row">
	<div class="small-12 columns"> 
	<a href="atividadesListarHoje.php" class="tiny info button">VOLTAR</a>
	<a href="#" data-reveal-id="myModal" class="tiny warning button">FILTRAR</a>
		<p>Atividades encontradas entre <strong><?php echo inverteData($dataInicio); ?></strong> e <strong><?php echo inverteData($dataFinal); ?></strong></p>
	</div>
</div>
<div class="row">
	<div class="small-12 columns">

		
		<?php 
		
		if ($totalRows_ListarAtividades > 0) { 
			
			$totalAtividades = 0;
			$totalFolhas = 0;
			
			do { 
			
			$totalAtividades = $totalAtividades + $row_ListarAtividades['smc_ativ_qtd'];
			$contaFolhas = $row_ListarAtividades['smc_ativ_qtd'] * $row_ListarAtividades['smc_ativ_folhas'];
			$totalFolhas = $totalFolhas + $contaFolhas;
			
		} while ($row_ListarAtividades = mysql_fetch_assoc($ListarAtividades)); 
		
		?>
				
		<p class="panel">
		<strong>Resultado <?php echo $row_NomeEscola['escola_nome']; ?><?php echo $nomeEscola; ?></strong> <br>
	  <table width="100%">
			<tr>
				<td class="text-center">Atividades Digitadas:</td>
				<td class="text-center">Atividades impressas (por aluno):</td>
				<td class="text-center">Total de folhas utilizadas:</td>
				<td class="text-center">Período:</td>
			</tr>
			<tr>
				<td class="text-center"><strong><?php echo $totalRows_ContarImpressoesHojeHoje ?></strong></td>
				<td class="text-center"><strong><?php echo $totalAtividades; ?></strong></td>
				<td class="text-center"><strong><?php echo $totalFolhas; ?> <?php $caixas = $totalFolhas/500; ?>(<?php echo round($caixas,1); ?> resmas)</strong></td>
				<td class="text-center"><strong>De <?php echo inverteData($dataInicio); ?> até <?php echo inverteData($dataFinal); ?></strong></td>
			</tr>		
		</table>
		</p>
		<?php } else { ?>
		<div data-alert class="alert-box secondary"> NENHUMA ATIVIDADE CADASTRADA <a href="#" class="close">&times;</a> </div>
		<?php } // Show if recordset not empty ?>
	</div>
</div>
<div class="row">
	<div class="small-12 columns">
		<ul class="button-group even-4">
			<li>
				<?php if ($pageNum_ListarAtividades > 0) { // Show if not first page ?>
					<a class="button" href="<?php printf("%s?pageNum_ListarAtividades=%d%s", $currentPage, 0, $queryString_ListarAtividades); ?>">PRIMEIRA</a>
					<?php } // Show if not first page ?>
			</li>
			<li>
				<?php if ($pageNum_ListarAtividades > 0) { // Show if not first page ?>
					<a class="button" href="<?php printf("%s?pageNum_ListarAtividades=%d%s", $currentPage, max(0, $pageNum_ListarAtividades - 1), $queryString_ListarAtividades); ?>">ANTERIOR</a>
					<?php } // Show if not first page ?>
			</li>
			<li>
				<?php if ($pageNum_ListarAtividades < $totalPages_ListarAtividades) { // Show if not last page ?>
					<a class="button" href="<?php printf("%s?pageNum_ListarAtividades=%d%s", $currentPage, min($totalPages_ListarAtividades, $pageNum_ListarAtividades + 1), $queryString_ListarAtividades); ?>">PRÓXIMA</a>
					<?php } // Show if not last page ?>
			</li>
			<li>
				<?php if ($pageNum_ListarAtividades < $totalPages_ListarAtividades) { // Show if not last page ?>
					<a class="button" href="<?php printf("%s?pageNum_ListarAtividades=%d%s", $currentPage, $totalPages_ListarAtividades, $queryString_ListarAtividades); ?>">ÚLTIMA</a>
					<?php } // Show if not last page ?>
			</li>
		</ul>
	</div>
</div>
<hr>

<?php include "rodape.php"; ?>

<div id="myModal" class="reveal-modal tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<form action="rel_atividadesListarData.php" method="get" autocomplete="off">
			<fieldset>
				<legend>Filtrar atividades por data</legend>
				<input type="text" id="dataInicio" name="dataInicio" value="" placeholder="Data inicial" required>
				<input type="text" id="dataFinal" name="dataFinal" value="" placeholder="Data final" required>
    
      
        <select name="escola">
          <option value="0">Todas as escolas</option>
		<?php do { ?>
          <option value="<?php echo $row_Escolas['escola_id']; ?>"><?php echo $row_Escolas['escola_nome']; ?></option>
        <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
        </select>
  
  
                <hr>
                
				<input type="submit" value="BUSCAR" class="button tiny">
			</fieldset>
		</form>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<script src="../../js/vendor/jquery.js"></script> 
<script src="../../js/foundation.min.js"></script> 
<script src="js/foundation/foundation.dropdown.js"></script> 
<script>
    $(document).foundation();
  </script> 
<script src="../../js/foundation-datepicker.js"></script> 
<script src="../../js/foundation-datepicker.pt-br.js"></script> 
<!-- ... --> 

<script>
$(function(){
	$('#dataInicio, #dataFinal').fdatepicker({
		//initialDate: '02-12-1989',
		format: 'yyyy-mm-dd',
		disableDblClickSelection: true,
		language: 'pt-br',
		leftArrow:'<<',
		rightArrow:'>>',
		closeIcon:'X',
		closeButton: false
	});
});
</script>
<script language="Javascript">
function confirmacao(id, cod) {
     var resposta = confirm("Deseja remover a atividade #"+cod+"?");
 
     if (resposta == true) {
          window.location.href = "atividadeExcluir.php?c="+cod;
     }
}
</script>

</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($NomeEscola);

mysql_free_result($ListarAtividades);

mysql_free_result($ContarImpressoesHojeHoje);

mysql_free_result($ContarImpressoesTotal);

mysql_free_result($Escolas);
?>
