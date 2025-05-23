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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_titulacao (titulacao_func_id, titulacao_tipo, titulacao_id_formacao, titulacao_horas, titulacao_data_inicio, titulacao_data_final, titulacao_data_entrega, titulacao_observacao) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['titulacao_func_id'], "int"),
                       GetSQLValueString($_POST['titulacao_tipo'], "int"),
                       GetSQLValueString($_POST['titulacao_id_formacao'], "int"),
                       GetSQLValueString($_POST['titulacao_horas'], "text"),
                       GetSQLValueString($_POST['titulacao_data_inicio'], "date"),
                       GetSQLValueString($_POST['titulacao_data_final'], "date"),
                       GetSQLValueString($_POST['titulacao_data_entrega'], "date"),
                       GetSQLValueString($_POST['titulacao_observacao'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "titulacao_cadastrar.php?cadastrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO smc_formacao (formacao_nome, formacao_descricao) VALUES (%s, %s)",
                       GetSQLValueString($_POST['formacao_nome'], "text"),
                       GetSQLValueString($_POST['formacao_descricao'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO smc_curso (curso_id_funcionario, curso_descricao, curso_instituicao, curso_dt_inicio, curso_dt_final, curso_ch, curso_observacao, curso_recebe) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['curso_id_funcionario'], "int"),
                       GetSQLValueString($_POST['curso_descricao'], "text"),
                       GetSQLValueString($_POST['curso_instituicao'], "text"),
                       GetSQLValueString($_POST['curso_dt_inicio'], "date"),
                       GetSQLValueString($_POST['curso_dt_final'], "date"),
                       GetSQLValueString($_POST['curso_ch'], "text"),
                       GetSQLValueString($_POST['curso_observacao'], "text"),
                       GetSQLValueString(isset($_POST['curso_recebe']) ? "true" : "", "defined","'S'","'N'"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
  $insertSQL = sprintf("INSERT INTO smc_licenca (lancamento_id_funcionario, lancamento_tipo, lancamento_data_saida, lancamento_data_retorno, lancamento_observacoes, lancamento_retorno) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['lancamento_id_funcionario'], "int"),
                       GetSQLValueString($_POST['lancamento_tipo'], "int"),
                       GetSQLValueString($_POST['lancamento_data_saida'], "date"),
                       GetSQLValueString($_POST['lancamento_data_retorno'], "date"),
                       GetSQLValueString($_POST['lancamento_observacoes'], "text"),
                       GetSQLValueString(isset($_POST['lancamento_retorno']) ? "true" : "", "defined","'S'","'N'"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
}



require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

$colname_Funcionario = "-1";
if (isset($_GET['cod'])) {
  $colname_Funcionario = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Funcionario = sprintf("SELECT func_id, func_usu_tipo, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, func_senha, func_senha_ativa, func_carga_horaria_semanal, func_vacina_covid19 FROM smc_func WHERE func_id_sec = '$row_Secretaria[sec_id]' AND func_id = %s", GetSQLValueString($colname_Funcionario, "int"));
$Funcionario = mysql_query($query_Funcionario, $SmecelNovo) or die(mysql_error());
$row_Funcionario = mysql_fetch_assoc($Funcionario);
$totalRows_Funcionario = mysql_num_rows($Funcionario);

if ($totalRows_Funcionario < 1) {
	$redireciona = "index.php?erro";
	header(sprintf("Location: %s", $redireciona));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Formacao = "SELECT formacao_id, formacao_nome, formacao_descricao FROM smc_formacao ORDER BY formacao_nome ASC";
$Formacao = mysql_query($query_Formacao, $SmecelNovo) or die(mysql_error());
$row_Formacao = mysql_fetch_assoc($Formacao);
$totalRows_Formacao = mysql_num_rows($Formacao);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Titulos = "SELECT titulacao_id, titulacao_func_id, titulacao_tipo, titulacao_id_formacao, titulacao_horas, titulacao_data_inicio, titulacao_data_final, titulacao_data_entrega, titulacao_observacao, formacao_id, formacao_nome FROM smc_titulacao INNER JOIN smc_formacao ON  formacao_id = titulacao_id_formacao WHERE titulacao_func_id = '$row_Funcionario[func_id]' ORDER BY titulacao_tipo ASC";
$Titulos = mysql_query($query_Titulos, $SmecelNovo) or die(mysql_error());
$row_Titulos = mysql_fetch_assoc($Titulos);
$totalRows_Titulos = mysql_num_rows($Titulos);


$colname_ListarCursos = "-1";
if (isset($_GET['cod'])) {
  $colname_ListarCursos = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarCursos = sprintf("SELECT curso_id, curso_id_funcionario, curso_descricao, curso_instituicao, curso_dt_inicio, curso_dt_final, curso_ch, curso_observacao, curso_recebe FROM smc_curso WHERE curso_id_funcionario = %s ORDER BY curso_descricao ASC", GetSQLValueString($colname_ListarCursos, "int"));
$ListarCursos = mysql_query($query_ListarCursos, $SmecelNovo) or die(mysql_error());
$row_ListarCursos = mysql_fetch_assoc($ListarCursos);
$totalRows_ListarCursos = mysql_num_rows($ListarCursos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TipoLicenca = "SELECT licenca_id, licenca_nome, licenca_obs FROM smc_licenca_tipo ORDER BY licenca_nome ASC";
$TipoLicenca = mysql_query($query_TipoLicenca, $SmecelNovo) or die(mysql_error());
$row_TipoLicenca = mysql_fetch_assoc($TipoLicenca);
$totalRows_TipoLicenca = mysql_num_rows($TipoLicenca);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_listarLicenca = "
SELECT 
lancamento_id, lancamento_id_funcionario, lancamento_tipo, lancamento_data_saida, lancamento_data_retorno, lancamento_observacoes, lancamento_retorno, 
func_id, func_nome, 
licenca_id, licenca_nome 
FROM smc_licenca 
INNER JOIN smc_func ON func_id = lancamento_id_funcionario 
INNER JOIN smc_licenca_tipo ON lancamento_tipo = licenca_id
WHERE lancamento_id_funcionario = '$row_Funcionario[func_id]'	
ORDER BY func_nome ASC";
$listarLicenca = mysql_query($query_listarLicenca, $SmecelNovo) or die(mysql_error());
$row_listarLicenca = mysql_fetch_assoc($listarLicenca);
$totalRows_listarLicenca = mysql_num_rows($listarLicenca);


function dias($d1, $d2){
    $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    $d2 = (is_string($d2) ? strtotime($d2) : $d2);  
    $diff_secs = abs($d1 - $d2);
    return floor($diff_secs / (3600 * 24));
}


if ((isset($_GET['titulacao'])) && ($_GET['titulacao'] != "") && ($_GET['cod'] == $row_Funcionario['func_id'])) {

  $deleteSQL = sprintf("DELETE FROM smc_titulacao WHERE titulacao_func_id = '$row_Funcionario[func_id]' AND titulacao_id=%s",
                       GetSQLValueString($_GET['titulacao'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "titulacao_cadastrar.php?cod=$colname_Funcionario&deletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


if ((isset($_GET['curso'])) && ($_GET['curso'] != "") && ($_GET['cod'] == $row_Funcionario['func_id'])) {

  $deleteSQL = sprintf("DELETE FROM smc_curso WHERE curso_id_funcionario = '$row_Funcionario[func_id]' AND curso_id=%s",
                       GetSQLValueString($_GET['curso'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "titulacao_cadastrar.php?cod=$colname_Funcionario&cursoDeletado";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['licenca'])) && ($_GET['licenca'] != "") && ($_GET['cod'] == $row_Funcionario['func_id'])) {

  $deleteSQL = sprintf("DELETE FROM smc_licenca WHERE lancamento_id_funcionario = '$row_Funcionario[func_id]' AND lancamento_id=%s",
                       GetSQLValueString($_GET['licenca'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "titulacao_cadastrar.php?cod=$colname_Funcionario&licencaDeletada";
  if (isset($_SERVER['QUERY_STRING'])) {
    //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
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
    <h1 class="ls-title-intro ls-ico-home">DETALHES</h1>
    <!-- CONTEUDO --> 
    
    <a class="ls-btn" href="funcionarios.php">VOLTAR</a>
    <p>&nbsp;</p>
    <div class="ls-box ls-lg-space ls-ico-user ls-ico-bg">

    <div class="col-md-2">
    <img src="../../professor/fotos/<?php echo $row_Funcionario['func_foto']; ?>" width="100%" class="ls-float-center">
    </div>

    <div class="col-md-10">
      <h1 class="ls-title-1 ls-color-theme"><?php echo $row_Funcionario['func_nome']; ?></h1>
      <P>&nbsp;</P>
      <p>CPF: <?php echo $row_Funcionario['func_cpf']; ?></p>
      <p>NASCIMENTO: <?php echo date("d/m/Y", strtotime($row_Funcionario['func_data_nascimento'])); ?></p>
      <p>MÃE: <?php echo$row_Funcionario['func_mae']; ?> &nbsp;&nbsp; PAI: <?php echo $row_Funcionario['func_pai']; ?></p>

      <p>RG: <?php echo $row_Funcionario['func_rg_numero']; ?> <?php echo $row_Funcionario['func_rg_emissor']; ?></p>
      <p>ENDEREÇO: <?php echo $row_Funcionario['func_endereco']; ?>      CPF: <?php echo $row_Funcionario['func_endereco_numero']; ?>      CPF: <?php echo $row_Funcionario['func_endereco_bairro']; ?></p>
      <p>TELEFONE: <?php echo $row_Funcionario['func_telefone']; ?> <?php echo $row_Funcionario['func_celular1']; ?> <?php echo $row_Funcionario['func_celular2']; ?></p>
      <p>E-MAIL: <?php echo $row_Funcionario['func_email']; ?></p>
    </div>


    </div>
    <hr>
    <div class="ls-box">
      <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-float-right">INCLUIR</button>
      <h6 class="ls-title-4">FORMAÇÕES</h6>
      <?php if ($totalRows_Titulos > 0) { // Show if recordset not empty ?>
        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th width="200">TIPO</th>
              <th>FORMAÇÃO</th>
              <th width="100" class="ls-txt-center">C/H</th>
              <th width="100" class="ls-txt-center">INÍCIO</th>
              <th width="100" class="ls-txt-center">TÉRMINO</th>
              <th width="100" class="ls-txt-center">ENTREGA</th>
              <th width="100" class="ls-txt-center">EXCLUIR</th>
            </tr>
          </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td><a href="#" class="ls-tag">
                  <?php 
			
			switch ($row_Titulos['titulacao_tipo']) {
				case 1:
					echo "GRADUAÇÃO";
					break;
				case 2:
					echo "PÓS";
					break;
				case 3:
					echo "MESTRADO";
					break;
				case 4:
					echo "DOUTORADO";
					break;
			}
			 
			 ?>
                  </a></td>
                <td><?php echo $row_Titulos['formacao_nome']; ?></td>
                <td class="ls-txt-center"><?php echo $row_Titulos['titulacao_horas']; ?></td>
                <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_Titulos['titulacao_data_inicio'])); ?></td>
                <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_Titulos['titulacao_data_final'])); ?></td>
                <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_Titulos['titulacao_data_entrega'])); ?></td>
                <td class="ls-txt-center"><a class="ls-btn ls-ico-remove ls-btn-xs" href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_Funcionario['func_id']; ?>','<?php echo $row_Titulos['titulacao_id']; ?>')"></td>
              </tr>
              <?php } while ($row_Titulos = mysql_fetch_assoc($Titulos)); ?>
          </tbody>
        </table>
        <?php } else { ?>
        <p>Nenhum título cadastrado</p>
        <?php } // Show if recordset not empty ?>
    </div>
    
    
    
    
    
    
    
    <div class="ls-box">
      <button data-ls-module="modal" data-target="#myAwesomeModalCursos" class="ls-btn-primary ls-float-right">INCLUIR</button>
      <h6 class="ls-title-4">CURSOS</h6>
      <?php if ($totalRows_ListarCursos > 0) { // Show if recordset not empty ?>
        <table class="ls-table">
          <thead>
            <tr>
              <th>DESCRIÇÃO</th>
              <th class="ls-txt-center">INSTITUIÇÃO</th>
              <th class="ls-txt-center" width="130">REMUNERADO</th>
              <th class="ls-txt-center" width="80">C/H</th>
              <th class="ls-txt-center" width="120">INÍCIO</th>
              <th class="ls-txt-center" width="120">TÉRMINO</th>
              <th class="ls-txt-center" width="80"></th>
            </tr>
          </thead>
          <tbody>
            <?php do { ?>
              <tr>
                <td>- <?php echo $row_ListarCursos['curso_descricao']; ?></td>
                <td class="ls-txt-center"><?php echo $row_ListarCursos['curso_instituicao']; ?></td>
                <td class="ls-txt-center"><?php if ($row_ListarCursos['curso_recebe']=="S") { ; ?>
                  SIM
                  <?php } ?></td>
                <td class="ls-txt-center"><?php echo $row_ListarCursos['curso_ch']; ?></td>
                <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_ListarCursos['curso_dt_inicio'])); ?></td>
                <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_ListarCursos['curso_dt_final'])); ?></td>
                <td><a class="ls-btn ls-ico-remove ls-btn-xs" href="javascript:func()" onclick="confirmaExclusaoCurso('<?php echo $row_Funcionario['func_id']; ?>','<?php echo $row_ListarCursos['curso_id']; ?>')"></a></td>
              </tr>
              <?php } while ($row_ListarCursos = mysql_fetch_assoc($ListarCursos)); ?>
          </tbody>
        </table>
        <?php } else { ?>
        <p>Nenhum curso cadastrado</p>
        <?php } // Show if recordset not empty ?>
    </div>
    
    
    
    
    
  <div class="ls-box">
      <button data-ls-module="modal" data-target="#myAwesomeModalLicenca" class="ls-btn-primary ls-float-right">INCLUIR</button>
      <h6 class="ls-title-4">LICENÇAS/AFASTAMENTOS</h6>
      
  
<?php if ($totalRows_listarLicenca > 0) { // Show if recordset not empty ?>
          <table border="0" width="100%" class="ls-table">
            <thead>
              <tr>
                <th>TIPO LICENÇA</th>
                <th class="ls-txt-center" width="120">SAÍDA</th>
                <th class="ls-txt-center" width="120">RETORNO</th>
                <th class="ls-txt-center" width="100">DIAS</th>
                <th class="ls-txt-center" width="120">CONCLUÍDO</th>
                <th width="160" class=""></th>
              </tr>
            </thead>
            <tbody>
              <?php do { ?>
                <tr>
                  <td>- <?php echo $row_listarLicenca['licenca_nome']; ?></td>
                  <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_listarLicenca['lancamento_data_saida'])); ?></td>
                  <td class="ls-txt-center"><?php echo date("d/m/Y", strtotime($row_listarLicenca['lancamento_data_retorno'])); ?></td>
                  <td class="ls-txt-center">
				  <?php 
					echo dias($row_listarLicenca['lancamento_data_saida'], $row_listarLicenca['lancamento_data_retorno'])
				  ?></td>
                  <td class="ls-txt-center">
				  <?php if($row_listarLicenca['lancamento_retorno']=="S") { ?>SIM<?php } else { ?>NÃO<?php } ?> 
				  <?php if ( ($row_listarLicenca['lancamento_data_retorno'] < date('Y-m-d')) && $row_listarLicenca['lancamento_retorno'] == "N" ) { echo "<br><a href=\"#\" class=\"ls-tag-danger\">VENCEU</a>"; } ?>
                  </td>
				  <td class="ls-txt-center">
                  <a href="licenca_editar.php?licenca=<?php echo $row_listarLicenca['lancamento_id']; ?>" class="ls-btn ls-ico-edit-admin ls-btn-xs"></a>
				  <a class="ls-btn ls-ico-remove ls-btn-xs" href="javascript:func()" onclick="confirmaExclusaoLicenca('<?php echo $row_Funcionario['func_id']; ?>','<?php echo $row_listarLicenca['lancamento_id']; ?>')"></a>
                  </td>
                </tr>
                <?php } while ($row_listarLicenca = mysql_fetch_assoc($listarLicenca)); ?>
            </tbody>
          </table>
          <?php } else { ?>
          
            <p>Nenhuma licença/afastamento cadastrado.</p>
          
           <?php } // Show if recordset not empty ?>  
  
  
  
  </div>      
    
    
    <div class="ls-modal" id="myAwesomeModalLicenca">
      <div class="ls-modal-box">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">REGISTRO DE LICENÇAS/AFASTAMENTOS</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
        <form method="post" name="form4" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
          <label class="ls-label col-md-12">
          <b class="ls-label-text">TIPO</b>
          <div class="ls-custom-select">
            <select name="lancamento_tipo" class="ls-select" required>
              <option value="-1">-</option>
              <?php do { ?>
              <option value="<?php echo $row_TipoLicenca['licenca_id']?>" ><?php echo $row_TipoLicenca['licenca_nome']?></option>
              <?php } while ($row_TipoLicenca = mysql_fetch_assoc($TipoLicenca)); ?>
            </select>
          </div>
          </label>
          <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">DATA DE INÍCIO/SAÍDA</b>
            <input type="date" name="lancamento_data_saida" value="" size="32">
          </label>
          <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">DATA DE RETORNO/PREVISTO</b>
            <input type="date" name="lancamento_data_retorno" value="" size="32">
          </label>
          <label class="ls-label col-sm-12"> <b class="ls-label-text">OBSERVAÇÕES</b>
            <textarea name="lancamento_observacoes" cols="50" rows="3"></textarea>
          </label>
          <div class="ls-label col-md-12">
            <label class="ls-label-text">
              <input type="checkbox" name="lancamento_retorno" value="" >
              MARQUE SE O PERÍODO DE LICENÇA JÁ FOI CONCLUÍDO </label>
          </div>
          <input type="hidden" name="lancamento_id_funcionario" value="<?php echo $row_Funcionario['func_id']; ?>">
          <input type="hidden" name="MM_insert" value="form4">
          </div>
          <div class="ls-modal-footer"> 
          	<a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
            <input type="submit" class="ls-btn-primary" value="CADASTRAR">
          </div>
        </form>
      </div>
    </div>
    <!-- /.modal -->
    
    <div class="ls-modal" id="myAwesomeModal">
      <div class="ls-modal-box">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">CADASTRAR</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
        <p>
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal">
          <fieldset>
            <label class="ls-label col-md-12">
            <b class="ls-label-text">TIPO</b>
            <div class="ls-custom-select">
              <select name="titulacao_tipo" class="ls-select">
                <option value="" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>-</option>
                <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>GRADUAÇÃO</option>
                <option value="2" <?php if (!(strcmp(2, ""))) {echo "SELECTED";} ?>>PÓS/ESPECIALIZAÇÃO</option>
                <option value="3" <?php if (!(strcmp(3, ""))) {echo "SELECTED";} ?>>MESTRADO</option>
                <option value="4" <?php if (!(strcmp(4, ""))) {echo "SELECTED";} ?>>DOUTORADO</option>
              </select>
            </div>
            </label>
            <label class="ls-label col-md-12">
            <b class="ls-label-text">FORMAÇÃO</b>
            <div class="ls-custom-select">
              <select name="titulacao_id_formacao" class="ls-select">
                <option value="" >-</option>
                <?php do {  ?>
                <option value="<?php echo $row_Formacao['formacao_id']?>" ><?php echo $row_Formacao['formacao_nome']?></option>
                <?php } while ($row_Formacao = mysql_fetch_assoc($Formacao)); ?>
              </select>
            </div>
            <a href="#" data-ls-module="modal" data-target="#myAwesomeModalAreas" class="ls-tag"><span class="ls-ico-plus"></span>CADASTRAR CURSO</a>
            </label>
            <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">INÍCIO DO CURSO</b>
              <input type="date" name="titulacao_data_inicio" value="" size="32">
            </label>
            <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">TÉRMINO DO CURSO</b>
              <input type="date" name="titulacao_data_final" value="" size="32">
            </label>
            <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">ENTREGA DO CERTIFICADO</b>
              <input type="date" name="titulacao_data_entrega" value="" size="32">
            </label>
            <label class="ls-label col-md-6 col-sm-12"> <b class="ls-label-text">TOTAL DE HORAS</b>
              <input type="text" name="titulacao_horas" value="" size="32">
            </label>
            <label class="ls-label col-md-12"> <b class="ls-label-text">Observação</b>
              <textarea name="titulacao_observacao" cols="50" rows="5"></textarea>
            </label>
            <input type="hidden" name="titulacao_func_id" value="<?php echo $row_Funcionario['func_id']; ?>">
            <input type="hidden" name="MM_insert" value="form1">
          </fieldset>
          </p>
          </div>
          <div class="ls-modal-footer"> <a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
            <input type="submit" class="ls-btn-primary" value="CADASTRAR">
          </div>
        </form>
      </div>
    </div>
    <!-- /.modal -->
    
    <div class="ls-modal" id="myAwesomeModalAreas">
      <div class="ls-modal-box">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">ÁREAS</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
        <p>
        <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form row">
          <fieldset>
            <label class="ls-label col-md-12">
            <b class="ls-label-text">NOME DO CURSO</b>
            <p class="ls-label-info">Ex.: PEDAGOGIA; GESTÃO ESCOLAR; etc.) </p>
            <input type="text" name="formacao_nome" value="" size="32">
            </label>
            <label class="ls-label col-md-12"> <b class="ls-label-text">NOME DO CURSO</b>
              <textarea name="formacao_descricao" cols="50" rows="5"></textarea>
            </label>
            <input type="hidden" name="MM_insert" value="form2">
          </fieldset>
          </p>
          </div>
          <div class="ls-modal-footer"> <a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
            <button type="submit" class="ls-btn-primary">SALVAR</button>
          </div>
        </form>
      </div>
    </div>
    <!-- /.modal -->
    
    <div class="ls-modal" id="myAwesomeModalCursos">
      <div class="ls-modal-box">
        <div class="ls-modal-header">
          <button data-dismiss="modal">&times;</button>
          <h4 class="ls-modal-title">CURSOS</h4>
        </div>
        <div class="ls-modal-body" id="myModalBody">
        <p>
        <form method="post" name="form3" action="<?php echo $editFormAction; ?>" class="ls-form row ls-form-horizontal">
          <fieldset>
          <label class="ls-label col-md-12">
          <b class="ls-label-text">CURSO</b>
          <p class="ls-label-info">Informe o nome do curso</p>
          <input type="text" name="curso_descricao" value="" size="32">
          </label>
          <label class="ls-label col-md-12">
          <b class="ls-label-text">INSTITUIÇÃO</b>
          <p class="ls-label-info">Informe o nome da instituição</p>
          <input type="text" name="curso_instituicao" value="" size="32">
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">INÍCIO</b>
          <p class="ls-label-info">Data de início</p>
          <input type="date" name="curso_dt_inicio" value="" size="32">
          </label>
          <label class="ls-label col-md-6">
          <b class="ls-label-text">TÉRMINO</b>
          <p class="ls-label-info">Data de término</p>
          <input type="date" name="curso_dt_final" value="" size="32">
          </label>
          <label class="ls-label col-md-12">
          <b class="ls-label-text">CARGA HORÁRIA</b>
          <p class="ls-label-info">Informe a carga horária</p>
          <input type="text" name="curso_ch" value="" size="32">
          </label>
          <label class="ls-label col-md-12"> <b class="ls-label-text">DETALHES</b>
            <textarea name="curso_observacao" cols="50" rows="5"></textarea>
          </label>
          <div class="ls-label col-md-12">
            <p>Curso conta como título remunerado?</p>
            <label class="ls-label-text">
              <input type="checkbox" name="curso_recebe" value="" class="ls-field-checkbox">
              SIM </label>
          </div>
          <input type="hidden" name="curso_id_funcionario" value="<?php echo $row_Funcionario['func_id']; ?>">
          <input type="hidden" name="MM_insert" value="form3">
          <fieldset>
          </p>
          <p>&nbsp;</p>
          </div>
          <div class="ls-modal-footer"> <a class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</a>
            <input type="submit" class="ls-btn-primary" value="CADASTRAR">
          </div>
        </form>
      </div>
    </div>
    <!-- /.modal -->
    
    <p>&nbsp;</p>
    <!-- CONTEUDO --> 
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script language="Javascript">
	function confirmaExclusao(funcionario,formacao) {
     var resposta = confirm("Deseja realmente remover esse ítem?");
     	if (resposta == true) {
     	     window.location.href = "titulacao_cadastrar.php?cod="+funcionario+"&titulacao="+formacao;
    	 }
	}
	</script> 
<script language="Javascript">
	function confirmaExclusaoCurso(funcionario,curso) {
     var resposta = confirm("Deseja realmente remover esse ítem?");
     	if (resposta == true) {
     	     window.location.href = "titulacao_cadastrar.php?cod="+funcionario+"&curso="+curso;
    	 }
	}
	</script>

<script language="Javascript">
	function confirmaExclusaoLicenca(funcionario,licenca) {
     var resposta = confirm("Deseja realmente remover esse ítem?");
     	if (resposta == true) {
     	     window.location.href = "titulacao_cadastrar.php?cod="+funcionario+"&licenca="+licenca;
    	 }
	}
	</script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Funcionario);

mysql_free_result($Formacao);

mysql_free_result($TipoLicenca);
?>