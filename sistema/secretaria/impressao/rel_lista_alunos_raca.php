<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/anti_injection.php'); ?>
<?php include('../../escola/fnc/idade.php'); ?>


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
	
  $logoutGoTo = "../../../index.php?exit";
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

$MM_restrictGoTo = "../../../index.php?acessorestrito";
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

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);
require_once('../funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$tipo = 99;

//FILTROS
$tipo = "TODAS";
$qry_tipo = "";
$tipo_titulo = "TODAS AS RAÇAS/CORES";

if (isset($_GET['raca'])) {
  $tipo = anti_injection($_GET['raca']);
  switch($tipo) {
	 case 99:
	  $qry_tipo = "";
	  $tipo_titulo = "TODAS AS RAÇAS/CORES";
	  break;
	 case 98:
	  $qry_tipo = "AND aluno_raca IS NULL";
	  $tipo_titulo = "SEM INFORMAÇÃO NO CADASTRO";
	  break;
	 case 1:
	  $qry_tipo = " AND aluno_raca = '1'";
	  $tipo_titulo = "BRANCA";
	  break;
	 case 2:
	  $qry_tipo = " AND aluno_raca = '2'";
	  $tipo_titulo = "PRETA";
	  break;
	 case 3:
	  $qry_tipo = " AND aluno_raca = '3'";
	  $tipo_titulo = "PARDA";
	  break;
	 case 4:
	  $qry_tipo = " AND aluno_raca = '4'";
	  $tipo_titulo = "AMARELA";
	  break;
	 case 5:
	  $qry_tipo = " AND aluno_raca = '5'";
	  $tipo_titulo = "INDÍGENA";
	  break;
	 case 6:
	  $qry_tipo = " AND aluno_raca = '6'";
	  $tipo_titulo = "NÃO DECLARADA";
	  break;
	  default:
	   $qry_tipo = "";
	   $tipo_titulo = "TODAS AS RAÇAS/CORES";
	   //$red = "index.php?erro";
	   //header(sprintf("Location: %s", $red));
	   break;
	  }
}

$escola = 99;

//FILTROS
$escola = "TODAS";
$qry_escola = "";
$tipo_titulo = "TODAS AS RAÇAS/CORES";

if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
  switch($escola) {
	 case 99:
	  $qry_escola = "";
	  $escola_titulo = "TODAS AS ESCOLAS";
	  break;
    case "TODAS":
    $qry_escola = "";
    $escola_titulo = "";
    break;
	  default:
	   $qry_escola = " AND escola_id = $escola";
	   $escola_titulo = "";
	   //$red = "index.php?erro";
	   //header(sprintf("Location: %s", $red));
	   break;
	  }
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_situacao, escola_ue,
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1' $qry_escola";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

if ($totalRows_EscolaLogada==0) {
	echo "Nenhuma informação.";
	echo "<a href=\"javascript:window.close();\">Fechar</a>";
	exit;
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" type="text/css" href="../css/impressao.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
</head>
<body onload="self.print();">


    <!-- CONTEUDO -->
    
    <table class="bordasimples1" width="100%">
    	<tr>
        	<td class="ls-txt-center" width="60"></td>
        	<td class="ls-txt-center">		
				<?php if ($row_Secretaria['sec_logo'] <> "") { ?>
				  <img src="../../../img/logo/secretaria/<?php echo $row_Secretaria['sec_logo']; ?>" alt="Logo da <?php echo $row_Secretaria['sec_nome']; ?>" title="Logo da <?php echo $row_EscolaLogada['sec_nome']; ?>"  width="60" />
				<?php } else { ?>
				  <img src="../../../img/brasao_republica.png" width="60">
				<?php } ?>
              <h3><?php echo $row_Secretaria['sec_prefeitura']; ?></h3>
              <?php echo $row_Secretaria['sec_nome']; ?>
            </td>
        	<td class="ls-txt-center" width="60"></td>
        </tr>
    </table>
    <br>
    
    <h2 class="ls-txt-center">RELATÓRIO DE ALUNOS POR COR/RAÇA</h2>
    
    <hr>

<h2 class="ls-title-2 ls-txt-center"><?php echo $tipo_titulo; ?></h2>

<hr>

<?php
	$total_alunos = 0;
?>

<?php do { ?>

<?php 
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaAlunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
aluno_id, aluno_nome, aluno_nascimento, aluno_raca, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual,
turma_id, turma_nome, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome,
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDIGENA'
WHEN 6 THEN 'NÃO DECLARADA'
END AS aluno_raca_nome  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' $qry_tipo 
ORDER BY turma_turno, turma_nome, aluno_nome
";
$ListaAlunos = mysql_query($query_ListaAlunos, $SmecelNovo) or die(mysql_error());
$row_ListaAlunos = mysql_fetch_assoc($ListaAlunos);
$totalRows_ListaAlunos = mysql_num_rows($ListaAlunos);
?>
	
    <br><br>
	<h3 class="ls-txt-center"><?php echo $row_EscolaLogada['escola_nome']; ?></h3>
    <br>

		<?php if ($totalRows_ListaAlunos > 0) { ?>
		<table class="bordasimples ls-sm-space" width="100%">
          <thead>
		  <tr>
            <th class="ls-txt-center" width="55px"></th>
            <th class="ls-txt-center">ALUNO</th>
            <th class="ls-txt-center" width="100">IDADE</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center">COR/RAÇA</th>
          </tr>
		  </thead>
		  <tbody>
		  <?php $num = 1; ?>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
              <td><?php echo $row_ListaAlunos['aluno_nome']; ?></td>
              <td class="ls-txt-center"><?php echo idade($row_ListaAlunos['aluno_nascimento']); ?></td>
              <td class="ls-txt-center"><?php echo $row_ListaAlunos['turma_nome']; ?> - <?php echo $row_ListaAlunos['turma_turno_nome']; ?></td>
              <td class="ls-txt-center"><?php if ($row_ListaAlunos['aluno_raca']=="") { ?>SEM INFORMAÇÃO<?php } else { ?><?php echo $row_ListaAlunos['aluno_raca_nome']; ?><?php } ?></td>
            </tr>
            <?php } while ($row_ListaAlunos = mysql_fetch_assoc($ListaAlunos)); ?>
			</tbody>
        </table>
		
<div class="ls-box">
  <p>Total de alunos: <?php echo $totalRows_ListaAlunos; ?></p>
</div>
				
<?php
	$total_alunos = $total_alunos + $totalRows_ListaAlunos;
?>
		
		<?php } else { ?>
				<hr>
		<div class="ls-alert-warning">
                  Nenhuma informação encontrada.
        </div>
		<?php } ?>  
        
<?php } while ($row_EscolaLogada = mysql_fetch_assoc($EscolaLogada)); ?>    
    
    
<div class="ls-box ls-box-gray">
  <h5 class="ls-title-5">Total de alunos matriculados na cor/raça <?php echo $tipo_titulo; ?>: <?php echo $total_alunos; ?></h5>
  <p></p>
</div>  

<p class="ls-txt-right">Relatório impresso em <?php echo date("d/m/Y \à\s H:i"); ?><br>SMECEL | Sistema de Gestão Escolar</p> 

    
<!-- CONTEUDO -->

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);
?>