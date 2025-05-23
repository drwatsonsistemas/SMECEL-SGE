<?php require_once('../../../Connections/SmecelNovo.php'); ?>
<?php require_once('../funcoes/inverteData.php'); ?>
<?php require_once('../funcoes/anti_injection.php'); ?>
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

require_once('../funcoes/usuLogado.php');
require_once('../funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

// Pegando o ano letivo mais recente
$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];

if (isset($_GET['ano'])) {
  if ($_GET['ano'] == "") {
    $anoLetivo = $anos[0]['ano_letivo_ano'];
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int) $anoLetivo;
}


$etapa=99;
$qry_etapa="";
$qry_sql_etapa ="";
if(isset($_GET['etapa'])){
  $etapa = anti_injection($_GET['etapa']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapas = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id_filtro IN (1,3,7) $qry_etapa";
$Etapas = mysql_query($query_Etapas, $SmecelNovo) or die(mysql_error());
$row_Etapas = mysql_fetch_assoc($Etapas);
$totalRows_Etapas = mysql_num_rows($Etapas);


if($etapa <> 99){
  $titulo_etapa = $row_Etapas['etapa_nome'];
}else{
  $titulo_etapa = "TODAS AS ETAPAS";
}

$escola = 99;
//FILTROS
//$escola = "TODAS";
$qry_escola = "";

if (isset($_GET['escola'])) {
  $escola = anti_injection($_GET['escola']);
  switch($escola) {
   case 99:
   $qry_escola = "";
   $escola_titulo = "TODAS AS ESCOLAS";
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
$query_Escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, 
escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim, 
escola_latitude, escola_longitude, escola_localizacao_diferenciada 
FROM smc_escola
WHERE escola_id_sec = ' $row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1'";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

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
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="self.print();alert('Configure a impressora para o formato PAISAGEM')">

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

<h2 class="ls-txt-center">RELATÓRIO GERAL</h2>
<br>
<h3 class="ls-txt-center">Alunos por Etapa de Ensino</h3>
<br>  
<?php $totalAlunos = 0 ?>
<?php do { ?>

  <table class="ls-table1 ls-sm-space bordasimples" width="100%">
   <thead>
     <tr>
      <th>
        <h2><?php echo $row_Etapas['etapa_nome_abrev']; ?> (<?php echo $row_Etapas['etapa_nome']; ?>)</h2>
      </th>
      <th>
        <h2>Turma</h2>
      </th>
      <th>
        <h2>Escola</h2>
      </th>
      <th>
        <h2>Nascimento</h2>
      </th>
    </tr>
  </thead>

  <?php 
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Vinculo = "
  SELECT vinculo_aluno_id,vinculo_aluno_id_aluno,vinculo_aluno_id_turma,vinculo_aluno_id_escola,vinculo_aluno_situacao,
  turma_id, turma_nome, turma_etapa, turma_id_sec, turma_id_escola, turma_ano_letivo,turma_tipo_atendimento, aluno_id, aluno_nome,aluno_nascimento, escola_id, escola_id_sec, escola_nome,escola_situacao 
  FROM smc_vinculo_aluno
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
  INNER JOIN smc_escola ON escola_id = turma_id_escola
  WHERE turma_tipo_atendimento = '1' AND vinculo_aluno_situacao = '1' AND turma_id_sec = '$row_UsuarioLogado[usu_sec]' AND turma_ano_letivo = '$anoLetivo' AND escola_situacao = '1'
   AND turma_etapa = '$row_Etapas[etapa_id]'
   $qry_escola
  ORDER BY turma_nome,aluno_nome ASC
  ";
  $Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
  $row_Vinculo = mysql_fetch_assoc($Vinculo);
  $totalRows_Vinculo = mysql_num_rows($Vinculo);

  ?>

  <tbody>

    <?php if ($totalRows_Vinculo>0) { ?>
      
      <?php do { ?>

        <tr>
          <td>- <?php echo $row_Vinculo['aluno_nome']; ?></td>
          <td><?php echo $row_Vinculo['turma_nome']; ?></td>
          <td><?php echo $row_Vinculo['escola_nome']; ?></td>
          <td class="ls-txt-center"><?php echo inverteData($row_Vinculo['aluno_nascimento']); ?></td>
        </tr>

        <?php $totalAlunos++; ?>
      <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>
      
      <tr>
        <td colspan="4"><i class="ls-float-right">Total de alunos vinculados nesta etapa: <strong><?php echo $totalRows_Vinculo; ?></strong></i></td>
      </tr>


    <?php } else { ?>
      <tr>
        <td colspan="4">- Nenhum aluno vinculado nesta etapa</td>
      </tr>
    <?php } ?>
  </tbody>



</table>  

<hr>
<?php } while ($row_Etapas = mysql_fetch_assoc($Etapas)); ?>
<tr>
<p class="ls-txt-right"><small>SMECEL | Sistema de Gestão Escolar - www.smecel.com.br <br>Impresso em <?php echo date("d/m/Y à\s H\hi"); ?></small></p>
  <td ><i class="ls-float-right">Total de alunos: <strong><?php echo $totalAlunos; ?></strong></i></td>
</tr>


<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Etapas);

mysql_free_result($Vinculo);
?>