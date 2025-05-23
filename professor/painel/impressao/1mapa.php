<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php include "../../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../../sistema/funcoes/notas.php'); ?>

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
	
  $logoutGoTo = "../../index.php?saiu";
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
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../../index.php?err";
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
$query_ProfLogado = sprintf("SELECT func_id, func_nome, func_email FROM smc_func WHERE func_id = %s", GetSQLValueString($colname_ProfLogado, "text"));
$ProfLogado = mysql_query($query_ProfLogado, $SmecelNovo) or die(mysql_error());
$row_ProfLogado = mysql_fetch_assoc($ProfLogado);
$totalRows_ProfLogado = mysql_num_rows($ProfLogado);
if($totalRows_ProfLogado=="") {
	header("Location:../index.php?loginErr");
}


$colname_Disciplinas = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplinas = $_GET['disciplina'];
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT boletim_id, boletim_id_vinculo_aluno, boletim_id_disciplina, boletim_1v1, boletim_2v1, boletim_3v1, boletim_1v2, boletim_2v2, 
boletim_3v2, boletim_1v3, boletim_2v3, boletim_3v3, boletim_1v4, boletim_2v4, boletim_3v4, boletim_af, 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao,
aluno_id, aluno_nome, aluno_foto, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_id_escola, turma_ano_letivo, escola_id, escola_nome 
FROM smc_boletim_disciplinas
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = boletim_id_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_disciplina ON disciplina_id = $colname_Disciplinas
INNER JOIN smc_turma ON turma_id = '$colname_Turma'
INNER JOIN smc_escola ON escola_id = turma_id_escola
WHERE boletim_id_disciplina = '$colname_Disciplinas' AND vinculo_aluno_id_turma = '$colname_Turma' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

if ($totalRows_Alunos == 0) {
	header("Location:index.php?erro");
}
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

      <title>SMECEL - Mapa de notas - <?php echo $row_Alunos['escola_nome']; ?> - <?php echo $row_Alunos['turma_nome']; ?> - <?php echo $row_Alunos['disciplina_nome']; ?> - <?php echo $row_ProfLogado['func_nome']; ?></title>
    
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="../../css/materialize.min.css"  media="screen,projection"/>
      <link type="text/css" rel="stylesheet" href="../../css/app.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	  
	  <style>
	  
	  
	  
	  
table{
    width:100%;
	border-collapse: collapse;
	font-size: 9px;
}

th, td{
    border:1px solid #ccc;
	padding: 4px;
}
th, td{
    padding:4px;
    height:10px;
    line-height:10px;
}
td input{
    display:block;
    width:100%;
    margin:0;
    padding:0;
    border:0 none;
    line-height:10px;
    height:10px;
}

html1,body1{
	overflow:hidden;
	width:100%;
	height:100%;
}
div#rolagem {
	padding-top: 20px;
	width:100%;
	height:100%;
	overflow:auto;
}
div#fixa {
	position: absolute;
	background-color: #ccc;
}
div#fixa table tr td {
	width: 105px;
	text-align: center;
}

	  
	  </style>
	  
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

    <body onload="alert('Atenção: Configure sua impressora para o formato RETRATO');self.print();">
    
    
  
    
	 <blockquote>
     
     <strong><?php echo $row_Alunos['escola_nome']; ?></strong><br>
	 Turma: <strong><?php echo $row_Alunos['turma_nome']; ?></strong><br>
	 Disciplina: <strong><?php echo $row_Alunos['disciplina_nome']; ?></strong><br>
	 Professor(a): <strong><?php echo $row_ProfLogado['func_nome']; ?></strong>
	 
	 </blockquote>
     
    <p style="text-align:center;">
    <h4 class="center-align" style="text-align:center;">LANÇAMENTO DE NOTAS</h4>
   </p>

    <?php if ($totalRows_Alunos==0) { ?>

		 
		 NENHUM ALUNO COM BOLETIM GERADO. <a href="../index.php">VOLTAR</a>
		 
		 <?php } else { ?>

			
		
		<table class="highlight centered" width="100%">
        <thead>

		<tr>
			<th colspan="1"></th>
			<th colspan="1"></th>
			<th class="light-green lighten-5" colspan="4">I UNIDADE</th>
			<th class="light-green lighten-5" colspan="4">II UNIDADE</th>
			<th class="light-green lighten-5" colspan="4">III UNIDADE</th>
			<th class="light-green lighten-5" colspan="4">IV UNIDADE</th>
			<th colspan="3">RESULTADO</th>
		</tr>

          <tr>
		    <th></th>
		    <th>ALUNO</th>
			<th>AV1</th>
			<th>AV2</th>
			<th>AV2</th>
			<th class="light-green lighten-5">MD</th>
			<th>AV1</th>
			<th>AV2</th>
			<th>AV3</th>
			<th class="light-green lighten-5">MD</th>
			<th>AV1</th>
			<th>AV2</th>
			<th>AV3</th>
			<th class="light-green lighten-5">MD</th>
			<th>AV1</th>
			<th>AV2</th>
			<th>AV3</th>			
			<th class="light-green lighten-5">MD</th>
			<th>TP</th>
			<th>MC</th>
			<th></th>
          </tr>
        </thead>

        <tbody>
		<?php $num = 1; do { ?>
          <tr>
		  <td><?php echo $num; $num++; ?></td>
		    <td>
			<?php echo $row_Alunos['aluno_nome']; ?>
			</td>
            
			<td><?php echo $row_Alunos['boletim_1v1']; ?></td>
			<td><?php echo $row_Alunos['boletim_2v1']; ?></td>
			<td><?php echo $row_Alunos['boletim_3v1']; ?></td>			
			<td class="light-green lighten-5" style="background-color:#D7D6D6"><strong><?php $mv1 = mediaUnidade($row_Alunos['boletim_1v1'],$row_Alunos['boletim_2v1'],$row_Alunos['boletim_3v1']); ?></strong></td>

			<td><?php echo $row_Alunos['boletim_1v2']; ?></td>
			<td><?php echo $row_Alunos['boletim_2v2']; ?></td>
			<td><?php echo $row_Alunos['boletim_3v2']; ?></td>						
            <td class="light-green lighten-5" style="background-color:#D7D6D6"><strong><?php $mv2 = mediaUnidade($row_Alunos['boletim_1v2'],$row_Alunos['boletim_2v2'],$row_Alunos['boletim_3v2']); ?></strong></td>
						
			<td><?php echo $row_Alunos['boletim_1v3']; ?></td>
			<td><?php echo $row_Alunos['boletim_2v3']; ?></td>
			<td><?php echo $row_Alunos['boletim_3v3']; ?></td>									
            <td class="light-green lighten-5" style="background-color:#D7D6D6"><strong><?php $mv3 = mediaUnidade($row_Alunos['boletim_1v3'],$row_Alunos['boletim_2v3'],$row_Alunos['boletim_3v3']); ?></strong></td>

			<td><?php echo $row_Alunos['boletim_1v4']; ?></td>
			<td><?php echo $row_Alunos['boletim_2v4']; ?></td>
			<td><?php echo $row_Alunos['boletim_3v4']; ?></td>												
            <td class="light-green lighten-5" style="background-color:#D7D6D6"><strong><?php $mv4 = mediaUnidade($row_Alunos['boletim_1v4'],$row_Alunos['boletim_2v4'],$row_Alunos['boletim_3v4']); ?></strong></td>
			
            <td><?php $tp = totalPontos($mv1,$mv2,$mv3,$mv4); ?></td>
            <td><?php echo $mc = mediaCurso($tp); ?></td>
            <td><?php $af = avaliacaoFinal($row_Alunos['boletim_af']); ?><?php resultadoFinal($mc,$af); ?></td>
          </tr>
		  <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
		  
		  
        </tbody>
      </table>
			
			

			
		

	
			
             

		 <?php } ?>
		
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../../js/materialize.min.js"></script>
      <script type="text/javascript" src="../../js/app.js"></script>
      	<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>
	
	
	
		
			<?php if (isset($_GET["notalancada"])) { ?>
			
			<script>
			  M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">DADOS SALVOS COM SUCESSO</button>'});
              </script>
        <?php } ?>
	
	
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Alunos);
?>