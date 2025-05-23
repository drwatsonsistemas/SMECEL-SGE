<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
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
	
  $logoutGoTo = "../index.php?saiu";
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
$MM_authorizedUsers = "7";
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

$MM_restrictGoTo = "../index.php?err";
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

$colname_ProfLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_ProfLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email, func_foto FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);

if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);
include "fnc/anoLetivo.php";


if (isset($_GET['data'])) {
  $data = $_GET['data'];
  $semana = date("w", strtotime($data));
  $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
  $dia_semana_nome = $diasemana[$semana];
} else {
	$data = date("Y-m-d");
	$semana = date("w", strtotime($data));
	$diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
	$dia_semana_nome = $diasemana[$semana];
}
//echo $data;



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, disciplina_id, disciplina_nome,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]' AND ch_lotacao_dia = '$semana'
ORDER BY turma_turno, ch_lotacao_aula ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);
//WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]' AND ch_lotacao_escola = '$row_Vinculos[vinculo_id_escola]' AND ch_lotacao_dia = '$semana'


   if (isset($_GET['data'])) {
   			$pagina = "chamada_alunos.php";
   		} else {
   			$pagina = "chamada.php";
	   }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title><?php echo $row_ProfLogado['func_nome']?></title>

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>

<!--Let browser know website is optimized for mobile-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
}
</style>
</head>

<body class="indigo lighten-5">
<?php include ("menu_top.php"); ?>
<div class="section no-pad-bot" id="index-banner">
  <div class="container">
    <div class="row white" style="margin: 10px 0;">
      <div class="col s12 m2 hide-on-small-only">
        <p>
          <?php if ($row_ProfLogado['func_foto']=="") { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
          <?php } else { ?>
          <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%" class="hoverable">
          <?php } ?>
          <br>
          <small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small> <small style="font-size:14px;"> <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
          <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
          </small> </p>
        <?php include "menu_esq.php"; ?>
      </div>
      <div class="col s12 m10">
        <h5>CHAMADA:</h5>
        <hr>
        <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
        <form method="get" action="<?php echo $pagina; ?>"  class="col s12">
          <blockquote>
            <h5><?php echo date("d/m/Y", strtotime($data)); ?> (<?php echo $dia_semana_nome; ?>)</h5>
          </blockquote>
          <div class="row">
            <div class="input-field col s12">
              <input type="date" name="data" value="<?php echo $data; ?>">
              <label for="first_name">Escolha uma data</label>
            </div>
            <?php if (isset($_GET['data'])) {	?>
            <div class="input-field col s12">
              <?php if ($totalRows_Turmas>0) { ?>
              <select name="turma" required>
                <option value=""	 disabled selected>Escolha a turma</option>
                <?php do { ?>
                <option value="<?php echo $row_Turmas['ch_lotacao_id']; ?>" <?php if (isset($_GET['turma']) && ($row_Turmas['turma_id'] == $_GET['turma'])) {echo "SELECTED";} ?>><?php echo $row_Turmas['ch_lotacao_aula']; ?>ª aula - <?php echo $row_Turmas['disciplina_nome']; ?> - <?php echo $row_Turmas['turma_nome']; ?> - <?php echo $row_Turmas['turma_turno_nome']; ?></option>
                <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
              </select>
              <label>Turmas</label>
              <?php } else { ?>
              <option value="" disabled selected>Não há horários para este dia</option>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
          <input type="submit" value="SELECIONAR" class="btn">
          <a href="chamada.php" class="btn red">Limpar</a>
        </form>
        <p>&nbsp;</p>
      </div>
    </div>
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>
<script type="text/javascript" src="../js/app.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
			$('select').formSelect();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($Turmas);
?>