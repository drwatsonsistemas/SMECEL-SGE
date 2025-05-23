<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../escola/fnc/idade.php'); ?>
<?php require_once('fnc/anti_injection.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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
	
  $logoutGoTo = "../../index.php?saiu=true";
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
$MM_authorizedUsers = "1,2,99";
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

$MM_restrictGoTo = "../../index.php?err=true";
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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);








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




?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Relação de alunos por cor/raça</h1>
		<!-- CONTEÚDO -->

    <div class="ls-box-filter">
  <form action="rel_lista_alunos_raca_escola.php" class="ls-form ls-form-inline ls-float-left">
  
    <label class="ls-label col-md-5 col-sm-5">
      <b class="ls-label-text">Cor/Raça</b>
      <div class="ls-custom-select">
        <select name="raca" class="ls-select">
          <option value="99" <?php if ($tipo == 99) { echo " selected"; } ?>>TODAS</option>
          <option value="1" <?php if ($tipo == 1) { echo " selected"; } ?>>BRANCA</option>
          <option value="2" <?php if ($tipo == 2) { echo " selected"; } ?>>PRETA</option>
          <option value="3" <?php if ($tipo == 3) { echo " selected"; } ?>>PARDA</option>
          <option value="4" <?php if ($tipo == 4) { echo " selected"; } ?>>AMARELA</option>
          <option value="5" <?php if ($tipo == 5) { echo " selected"; } ?>>INDÍGENA</option>
          <option value="6" <?php if ($tipo == 6) { echo " selected"; } ?>>NÃO DECLARADA</option>
          <option value="98" <?php if ($tipo == 98) { echo " selected"; } ?>>SEM INFORMAÇÃO</option>
        </select>
      </div>
    </label>
    
      
<div class="ls-actions-btn">
      <input type="submit" value="Buscar" class="ls-btn" title="Buscar">
    </div>
  </form>
</div>          

    <h2 class="ls-title-2">Cor/Raça: <?php echo $tipo_titulo; ?></h2>

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
aluno_id, aluno_nome, aluno_nome_social,aluno_nascimento, aluno_raca, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia, aluno_laudo, aluno_alergia, aluno_alergia_qual,
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



<h2><?php echo $row_EscolaLogada['escola_nome']; ?> </h2>

		<?php if ($totalRows_ListaAlunos > 0) { ?>
		<table width="100%" class="ls-table">
          <thead>
		  <tr>
            <th class="ls-txt-center" width="55px"></th>
            <th class="ls-txt-center">ALUNO</th>
            <th class="ls-txt-center">IDADE</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center">COR/RAÇA</th>
          </tr>
		  </thead>
		  <tbody>
		  <?php $num = 1; ?>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
              <td><?php echo $row_ListaAlunos['aluno_nome_social'] != "" ? $row_ListaAlunos["aluno_nome_social"] : $row_ListaAlunos["aluno_nome"]; ?></td>
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
  <h5 class="ls-title-5">Total de alunos matriculados na escola de cor/raça <?php echo $tipo_titulo; ?>: <?php echo $total_alunos; ?></h5>
  <p></p>
</div>		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
