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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_usuario = "-1";
if (isset($_GET['usuario'])) {
  $colname_usuario = $_GET['usuario'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_usuario = sprintf("
SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro, usu_insert, usu_update, usu_delete, usu_m_ava, usu_m_administrativo, usu_m_formacao, usu_m_transporte, usu_m_merenda, usu_m_patrimonio, 
usu_m_relatorios, usu_m_graficos, usu_m_configuracoes, usu_foto, usu_aceite_lgpd, usu_aceite_lgpd_data, escola_id, escola_nome,
CASE usu_tipo
WHEN 1 THEN 'SECRETARIA'
WHEN 2 THEN 'ESCOLAR'
WHEN 3 THEN 'MECANOGRAFIA'
WHEN 4 THEN 'PSE'
WHEN 5 THEN 'PORTARIA'
WHEN 6 THEN 'CONSELHO TUTELAR'
END AS usu_tipo_desc
FROM smc_usu 
LEFT JOIN smc_escola ON escola_id = usu_escola
WHERE usu_sec = '$row_Secretaria[sec_id]' AND usu_id = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $SmecelNovo) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

if ($totalRows_usuario < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_OutrasEscolas = "SELECT usu_escola_id, usu_escola_id_usu, usu_escola_id_escola, escola_id, escola_nome FROM smc_usu_escolas INNER JOIN smc_escola ON escola_id = usu_escola_id_escola WHERE usu_escola_id_usu = '$row_usuario[usu_id]'";
$OutrasEscolas = mysql_query($query_OutrasEscolas, $SmecelNovo) or die(mysql_error());
$row_OutrasEscolas = mysql_fetch_assoc($OutrasEscolas);
$totalRows_OutrasEscolas = mysql_num_rows($OutrasEscolas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_logs = "
SELECT log_id, log_id_usu, log_id_escola, log_data_hora, escola_id, escola_nome, escola_id_sec, usu_id, usu_nome, usu_sec, usu_tipo 
FROM smc_log
LEFT JOIN smc_escola ON escola_id = log_id_escola
INNER JOIN smc_usu ON usu_id = log_id_usu
WHERE usu_sec =  '$row_usuario[usu_sec]' AND log_id_usu = '$row_usuario[usu_id]' AND usu_tipo <> '99'
ORDER BY log_id DESC";
$logs = mysql_query($query_logs, $SmecelNovo) or die(mysql_error());
$row_logs = mysql_fetch_assoc($logs);
$totalRows_logs = mysql_num_rows($logs);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Registros = "
SELECT registros_id, registros_id_escola, registros_id_usuario, registros_tipo, registros_complemento, registros_data_hora, escola_id, escola_nome, escola_id_sec, usu_id, usu_nome 
FROM smc_registros
INNER JOIN smc_escola ON escola_id = registros_id_escola 
INNER JOIN smc_usu ON usu_id = registros_id_usuario
WHERE escola_id_sec = '$row_usuario[usu_sec]' AND registros_id_usuario = '$row_usuario[usu_id]'
ORDER BY registros_id DESC";
$Registros = mysql_query($query_Registros, $SmecelNovo) or die(mysql_error());
$row_Registros = mysql_fetch_assoc($Registros);
$totalRows_Registros = mysql_num_rows($Registros);

function registros($cod) {
	
switch($cod)
{
    case '1';
		$situacao = 'ATUALIZOU OS DADOS DA UNIDADE ESCOLAR';
		break;
    case '2';
		$situacao = 'ATUALIZOU OS DADOS DE USUÁRIO';
		break;
    case '3';
		$situacao = 'CADASTROU UM NOVO OFÍCIO';
		break;
    case '4';
		$situacao = 'EDITOU UM OFÍCIO';
		break;
    case '5';
		$situacao = 'EDITOU UMA AULA NA GRADE DE HORÁRIOS';
		break;
    case '6';
		$situacao = 'CADASTROU UMA AULA NA GRADE DE HORÁRIOS';
		break;
    case '7';
		$situacao = 'EXCLUIU UMA AULA NA GRADE DE HORÁRIO';
		break;
    case '8';
		$situacao = 'CADASTROU UM ALUNO NO SISTEMA';
		break;
    case '9';
		$situacao = 'VINCULOU UM ALUNO NO SISTEMA';
		break;
    case '10';
		$situacao = 'EDITOU OS DADOS DE UM ALUNO';
		break;
    case '11';
		$situacao = 'CADASTROU/GEROU BOLETIM DE ALUNO';
		break;
    case '12';
		$situacao = 'EDITOU AS NOTAS NO BOLETIM';
		break;
    case '13';
		$situacao = 'EDITOU NOTA INDIVIDUAL NO BOLETIM';
		break;
    case '14';
		$situacao = 'INSERIU UMA NOVA DISCIPLINA NO BOLETIM';
		break;
    case '15';
		$situacao = 'EXCLUIU UMA DISCIPLINA NO BOLETIM';
		break;
    case '16';
		$situacao = 'EDITOU O VÍNCULO DO ALUNO';
		break;
    case '17';
		$situacao = 'EXCLUIU O VÍNCULO DO ALUNO';
		break;
    case '18';
		$situacao = 'CADASTROU A OCORRÊNCIA DE UM ALUNO';
		break;
    case '19';
		$situacao = 'EXCLUIU A OCORRÊNCIA DE UM ALUNO';
		break;
    case '20';
		$situacao = 'CADASTROU UMA TURMA';
		break;
    case '21';
		$situacao = 'EDITOU UMA TURMA';
		break;
    case '22';
		$situacao = 'EXCLUIU UMA TURMA';
		break;
    case '23';
		$situacao = 'VINCULOU UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
		break;
    case '24';
		$situacao = 'EDITOU O VÍNCULO DE UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
		break;
    case '25';
		$situacao = 'EXCLUIU O VÍNCULO DE UM FUNCIONÁRIO NA UNIDADE ESCOLAR';
		break;
	default:
		$situacao = 'SEM DADOS';
		break;	
}
	
echo $situacao;
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
    <h1 class="ls-title-intro ls-ico-home">DETALHES DO USUÁRIO</h1>
    <!-- CONTEUDO -->
    
    <p><a class="ls-btn-primary" href="usuarios.php">VOLTAR</a></p>
  
  <div class="ls-box-group">  
  <div class="ls-box ls-md-space">
    <div class="row">
      <div class="col-md-2 ls-txt-center">
      <img src="../../img/funcionarios/<?php echo $row_usuario['usu_foto']; ?>" width="100%">
      </div>
      <div class="col-md-10">
        
          <h2 class="ls-title-4"><strong><?php echo $row_usuario['usu_nome']; ?> (<?php echo $row_usuario['usu_email']; ?>)</strong></h2><br>
          <p class="ls-title-6"><strong>Usuário(a) do sistema desde <?php echo date("d/m/Y", strtotime($row_usuario['usu_cadastro'])); ?> <?php if ($row_usuario['usu_status']=="1") { echo "<span class='ls-tag-success'>ATIVO</span>"; } else { echo "<span class='ls-tag-danger'>INATIVO</span>"; } ?></strong></p>
		  <p>Usuário(a) do tipo <strong><?php echo $row_usuario['usu_tipo_desc']; ?></strong></p>          
		  <p>Login principal: <strong><?php echo $row_usuario['escola_nome']; ?></strong></p> 
          
          <?php if ($totalRows_OutrasEscolas > 0) { ?>
          <p><strong>ACESSO À OUTRAS ESCOLAS:</strong></p>
          <?php do { ?>
            <p><?php echo $row_OutrasEscolas['escola_nome']; ?></p>
          <?php } while ($row_OutrasEscolas = mysql_fetch_assoc($OutrasEscolas)); ?>
          <?php } ?>
          
          
          <?php 
		  function diasDatas($data_inicial,$data_final) {
			$diferenca = strtotime($data_final) - strtotime($data_inicial);
			$dias = floor($diferenca / (60 * 60 * 24)); 
			
			if ($dias==0) {
				
				$res = "hoje";
				
				} else if ($dias == 1) {
					$res = "ontem";
					} else {
						$res = "há ".$dias." dias";
						}
			
			return $res;
		}
		
		$dataHoje = date("Y-m-d");
		  ?>
          
           <?php if ($totalRows_logs > 0) { ?>
          <p><span class="ls-ico-hours ls-ico-left"></span> Último login realizado em <?php echo date("d/m/Y à\s H\hi", strtotime($row_logs['log_data_hora'])); ?> <?php $dataLogin = date("Y-m-d", strtotime($row_logs['log_data_hora'])); ?> <em> (<?php echo diasDatas($dataLogin,$dataHoje); ?>)</em></p>
          <?php } ?>
          
      </div>
    </div>
  </div>
  </div>
  
  
  <div data-ls-module="collapse" data-target="#0" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title">LOGINS (<?php echo $totalRows_logs; ?>)</h3>
    </a>
    <div class="ls-collapse-body" id="0">
      <p>
      <?php if ($totalRows_logs > 0) { ?>
      <table class="ls-table ls-sm-space ls-table-striped ls-bg-header">
      <thead>
      <tr>
        <th width="70"></th>
        <th width="180" class="ls-txt-center">DATA/HORA</th>
        <th class="ls-txt-center">LOCAL DE LOGIN</th>
        
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
        <tr>
          <td>#<?php echo $row_logs['log_id']; ?></td>
          <td class="ls-txt-center"><?php echo date("d/m/Y - H\hi", strtotime($row_logs['log_data_hora'])); ?><?php //echo $row_logs['log_data_hora']; ?></td>
          <td><?php echo $row_logs['escola_nome']; ?></td>
          
        </tr>
        <?php } while ($row_logs = mysql_fetch_assoc($logs)); ?>
    	</tbody>
    </table> 
    <?php } ?>  
      </p>
    </div>
  </div>


<div data-ls-module="collapse" data-target="#1" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title">REGISTRO DE ATIVIDADES (<?php echo $totalRows_Registros; ?>)</h3>
    </a>
    <div class="ls-collapse-body" id="1">
      <p>
      <?php if ($totalRows_Registros > 0) { ?>
        <table width="100%" class="ls-table ls-sm-space ls-table-striped ls-bg-header">
        <thead>
          <tr>
            <th align="center" width="150">DATA/HORA</th>
            <th align="center">ESCOLA</th>
            <th align="center">DETALHES</th>
            </tr>
        </thead>
        <tbody>
          <?php do { ?>
            <tr>
              <td align="center"><?php echo date("H\hi - d/m/Y", strtotime($row_Registros['registros_data_hora'])); ?></td>
              <td><?php echo $row_Registros['escola_nome']; ?></td>
              <td align="center"><strong><?php echo registros($row_Registros['registros_tipo']); ?></strong> <?php echo $row_Registros['registros_complemento']; ?></td>
            </tr>
            <?php } while ($row_Registros = mysql_fetch_assoc($Registros)); ?>
        </tbody>
      </table>
      <?php } ?>
      </p>
    </div>
  </div>  
  
 
    
    
    <p>&nbsp;</p>
    <!-- CONTEUDO -->    
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

mysql_free_result($OutrasEscolas);

mysql_free_result($usuario);
?>