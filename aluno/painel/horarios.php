<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>

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
$MM_authorizedUsers = "6";
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT *
FROM smc_aluno
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_dependencia,
escola_id, escola_nome, turma_id, turma_nome,turma_tipo_atendimento, turma_etapa 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma  
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' AND vinculo_aluno_dependencia = 'N' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_MatriculaDep = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_dependencia,
escola_id, escola_nome, turma_id, turma_nome,turma_tipo_atendimento, turma_etapa 
FROM smc_vinculo_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma  
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' AND vinculo_aluno_dependencia = 'S' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$MatriculaDep = mysql_query($query_MatriculaDep, $SmecelNovo) or die(mysql_error());
$row_MatriculaDep = mysql_fetch_assoc($MatriculaDep);
$totalRows_MatriculaDep = mysql_num_rows($MatriculaDep);



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
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link type="text/css" rel="stylesheet" href="css/geral.css" media="screen,projection" />

<style>
table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th, td {
	border:0px solid #ccc;
}
th, td {
	padding:5px;
	height:15px;
	line-height:15px;
}
table.bordasimples {
	border-collapse: collapse;
	font-size:7px;
}
table.bordasimples tr td {
	border:1px solid #eeeeee;
	padding:2px;
	font-size:12px;
}
table.bordasimples tr th {
	border:1px solid #eeeeee;
	padding:2px;
	font-size:9px;
}
</style>


</head>
<body class="indigo lighten-5">

<?php include "menu_top.php"?>

<div class="container">
  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 hide-on-small-only">
      <p>
      <?php 
        if (!empty($row_AlunoLogado['aluno_foto2'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/' . $row_AlunoLogado['aluno_foto2']; ?>" width="100%" class="hoverable">
        <?php } elseif (!empty($row_AlunoLogado['aluno_foto'])) { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos/' . $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } else { ?>
            <img src="<?php echo URL_BASE.'aluno/fotos2/semfoto.jpg'; ?>" width="100%" class="hoverable">
        <?php } ?>
				<br>
		<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small>
      </p>

	<?php include "menu_esq.php"; ?>

    </div>
        
    <div class="col s12 m10">


     <h5><strong>Quadro de horários</strong></h5>
	 <hr>
	 <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>voltar</a> 
	 <?php include('funcoes/print_exibeHorario.php'); ?>

    <h5><?php echo $row_Matricula['turma_nome']?></h5>
    <table class="responsive-table bordasimples striped">
          <thead>
            <tr class="blue">
              <th class="center-align" width="30px"></th>
              <th class="center-align">SEG</th>
              <th class="center-align">TER</th>
              <th class="center-align">QUA</th>
              <th class="center-align">QUI</th>
              <th class="center-align">SEX</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="center-align">1ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,1); ?></td>
            </tr>
            <tr>
              <td class="center-align">2ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,2); ?></td>
            </tr>
            <tr>
              <td class="center-align">3ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,3); ?></td>
            </tr>
            <tr>
              <td class="center-align">4ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,4); ?></td>
            </tr>
            <tr>
              <td class="center-align">5ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],1,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],2,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],3,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],4,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_Matricula['turma_id'],5,5); ?></td>
            </tr>
          </tbody>
        </table>
		
		<hr>
		
        <?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionarios = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_turma_id, func_id, func_nome 
FROM smc_ch_lotacao_professor
INNER JOIN smc_func ON func_id = ch_lotacao_professor_id
WHERE ch_lotacao_turma_id = $row_Matricula[turma_id]
GROUP BY ch_lotacao_professor_id ASC";
$Funcionarios = mysql_query($query_Funcionarios, $SmecelNovo) or die(mysql_error());
$row_Funcionarios = mysql_fetch_assoc($Funcionarios);
$totalRows_Funcionarios = mysql_num_rows($Funcionarios);
?>
        <?php if ($totalRows_Funcionarios > 0) { ?>
        <small>
        <?php do { ?>
          (<b><?php echo $row_Funcionarios['ch_lotacao_professor_id'] ?></b>) <?php echo $row_Funcionarios['func_nome'] ?> | 
          <?php } while ($row_Funcionarios = mysql_fetch_assoc($Funcionarios)); ?>
        </small>
        <?php } ?>  
      
      



<?php if ($totalRows_MatriculaDep>0) { ?>


<h4>Matrícula de dependência</h4>
<h5><?php echo $row_MatriculaDep['turma_nome']?></h5>


    <table class="responsive-table bordasimples striped">
          <thead>
            <tr class="blue">
              <th class="center-align" width="30px"></th>
              <th class="center-align">SEG</th>
              <th class="center-align">TER</th>
              <th class="center-align">QUA</th>
              <th class="center-align">QUI</th>
              <th class="center-align">SEX</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="center-align">1ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],1,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],2,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],3,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],4,1); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],5,1); ?></td>
            </tr>
            <tr>
              <td class="center-align">2ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],1,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],2,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],3,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],4,2); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],5,2); ?></td>
            </tr>
            <tr>
              <td class="center-align">3ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],1,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],2,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],3,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],4,3); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],5,3); ?></td>
            </tr>
            <tr>
              <td class="center-align">4ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],1,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],2,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],3,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],4,4); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],5,4); ?></td>
            </tr>
            <tr>
              <td class="center-align">5ª</td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],1,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],2,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],3,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],4,5); ?></td>
              <td class="center-align"><?php echo print_exibeHorario($row_MatriculaDep['turma_id'],5,5); ?></td>
            </tr>
          </tbody>
        </table>

<hr>


<?php } ?>


    </div>
    

    
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.dropdown-trigger').dropdown();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);

mysql_free_result($Funcionarios);
?>
