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
	
	$hash = md5(time());
	
  $insertSQL = sprintf("INSERT INTO smc_matriz (matriz_id_secretaria, matriz_id_etapa, matriz_criterio_avaliativo, matriz_nome, matriz_obs, matriz_anoletivo, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_hash) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$hash')",
                       GetSQLValueString($_POST['matriz_id_secretaria'], "int"),
                       GetSQLValueString($_POST['matriz_id_etapa'], "int"),
                       GetSQLValueString($_POST['matriz_criterio_avaliativo'], "int"),
                       GetSQLValueString($_POST['matriz_nome'], "text"),
                       GetSQLValueString($_POST['matriz_obs'], "text"),
                       GetSQLValueString($_POST['matriz_anoletivo'], "text"),
                       GetSQLValueString($_POST['matriz_dias_letivos'], "text"),
                       GetSQLValueString($_POST['matriz_semanas_letivas'], "text"),
                       GetSQLValueString($_POST['matriz_dias_semana'], "text"),
                       GetSQLValueString($_POST['matriz_minutos_aula'], "text"),
                       GetSQLValueString($_POST['matriz_aula_dia'], "text"),
                       GetSQLValueString($_POST['matriz_hash'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  
  if ($_POST['matriz_id_etapa']=="1") {
	  
	    $insertGoTo = "bncc_camp_exp_cad.php?hash=$hash";
	  
	  } else {
  
  
  $insertGoTo = "matrizdisciplina.php?hash=$hash";
  
	  }
  
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash FROM smc_matriz WHERE matriz_id_secretaria = '$row_Secretaria[sec_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Etapa = "SELECT * FROM smc_etapa_filtro";
$Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
$row_Etapa = mysql_fetch_assoc($Etapa);
$totalRows_Etapa = mysql_num_rows($Etapa);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_CriterioAvaliativo = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes FROM smc_criterios_avaliativos WHERE ca_id_secretaria = '$row_UsuarioLogado[usu_sec]'";
$CriterioAvaliativo = mysql_query($query_CriterioAvaliativo, $SmecelNovo) or die(mysql_error());
$row_CriterioAvaliativo = mysql_fetch_assoc($CriterioAvaliativo);
$totalRows_CriterioAvaliativo = mysql_num_rows($CriterioAvaliativo);


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
    <h1 class="ls-title-intro ls-ico-home">MATRIZ CURRICULAR</h1>
    <div class="ls-box ls-board-box">
      <button data-ls-module="modal" data-target="#modalLarge" class="ls-btn-primary ls-ico-plus">CADASTRAR MATRIZ</button>
      <?php if ($totalRows_Matriz > 0) { // Show if recordset not empty ?>
        <table class="ls-table">
          <thead>
            <tr>
              <th width="250">NOME DA MATRIZ</th>
              <th class="ls-txt-center" width="200">DISCIPLINA</th>
              <th width="100"></th>
            </tr>
          </thead>
          <tbod>
          <?php do { ?>
            <tr>
              <td><?php echo $row_Matriz['matriz_nome']; ?> (<?php echo $row_Matriz['matriz_anoletivo']; ?>)</td>
              <td class="ls-txt-left">
			  
			  <?php 
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_MatrizDisciplinas = "
				SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome
				FROM smc_matriz_disciplinas
				INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
				WHERE matriz_disciplina_id_matriz = '$row_Matriz[matriz_id]'";
				$MatrizDisciplinas = mysql_query($query_MatrizDisciplinas, $SmecelNovo) or die(mysql_error());
				$row_MatrizDisciplinas = mysql_fetch_assoc($MatrizDisciplinas);
				$totalRows_MatrizDisciplinas = mysql_num_rows($MatrizDisciplinas);
				?>
                <?php if ($totalRows_MatrizDisciplinas > 0) { ?>
                <?php $num = 1; do { ?>
                  <small aclass="ls-tag"><?php echo $num; $num++; ?> - <?php echo $row_MatrizDisciplinas['disciplina_nome']; ?> </small><br>
                  <?php } while ($row_MatrizDisciplinas = mysql_fetch_assoc($MatrizDisciplinas)); ?>
                  
                  <?php } ?>
                  
                  <?php if ($row_Matriz['matriz_id_etapa'] == "1") { ?>
                  
                  <br><small><a href="bncc_camp_exp_cad.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>" class="ls-ico-plus ls-float-left">Objetivos de aprendizagem e desenvolvimento</a></small>
				  <br>
				  <br><small><a href="matrizdisciplina.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>" class="ls-ico-plus ls-float-left">Inserir Campos de Experiência</a></small>
				  
                  <?php } else { ?>
                  
                  <br><small><a href="matrizdisciplina.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>" class="ls-ico-plus ls-float-left">Inserir Componentes Curriculares</a></small>
                  
                  <?php } ?>
                  
                  </td>
              <td class="ls-txt-center"><a href="matriz_editar.php?matriz=<?php echo $row_Matriz['matriz_hash']; ?>" class="ls-ico-pencil ls-float-right">Editar</a></td>
            </tr>
            <?php } while ($row_Matriz = mysql_fetch_assoc($Matriz)); ?>
            </tbody>
          
        </table>
        <?php } else { ?>
        <hr>
        <div class="ls-alert-warning"><strong>Cuidado:</strong> Nenhuma Matriz cadastrada.</div>
        <?php } // Show if recordset not empty ?>
      <div class="ls-modal" id="modalLarge">
        <div class="ls-modal-large">
          <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">CADASTRAR NOVA MATRIZ</h4>
          </div>
          <div class="ls-modal-body">
            <p>
            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form-horizontal">
              <label class="ls-label col-md-9">
              <b class="ls-label-text">NOME DA MATRIZ</b>
              <p class="ls-label-info">Informe o nome da Matriz. (Ex.: 1º ao 5º Ano)</p>
              <input type="text" name="matriz_nome" value="" size="32" required>
              </label>
              <label class="ls-label col-md-3">
              <b class="ls-label-text">ANO LETIVO</b>
              <p class="ls-label-info">Ex.: <?php echo date('Y'); ?> </p>
              <input type="text" name="matriz_anoletivo" value="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>" size="32" required>
              </label>
              <label class="ls-label col-sm-12  col-md-6">
              <b class="ls-label-text">ETAPA DE ENSINO</b>
              <p class="ls-label-info">Informe a Etapa de Ensino</p>
              <div class="ls-custom-select">
                <select name="matriz_id_etapa" required>
                  <option value=""> </option>
                  <?php do { ?>
                  <option value="<?php echo $row_Etapa['etapa_filtro_id']?>" ><?php echo $row_Etapa['etapa_filtro_nome']?></option>
                  <?php } while ($row_Etapa = mysql_fetch_assoc($Etapa)); ?>
                </select>
              </div>
              </label>
              <label class="ls-label col-sm-12 col-md-6">
              <b class="ls-label-text">CRITÉRIOS AVALIATIVOS</b>
              <p class="ls-label-info">Informe o critério avaliativo</p>
              <div class="ls-custom-select">
                <select name="matriz_criterio_avaliativo" required>
                  <option value=""> </option>
                  <?php do {  ?>
                  <option value="<?php echo $row_CriterioAvaliativo['ca_id']?>" ><?php echo $row_CriterioAvaliativo['ca_descricao']?></option>
                  <?php } while ($row_CriterioAvaliativo = mysql_fetch_assoc($CriterioAvaliativo)); ?>
                </select>
              </div>
              </label>
              <label class="ls-label col-md-4">
              <b class="ls-label-text">DIAS LETIVOS</b>
              <p class="ls-label-info">Ex.:200 </p>
              <input type="text" name="matriz_dias_letivos" value="" size="32" required>
              </label>
              <label class="ls-label col-md-4">
              <b class="ls-label-text">SEMANAS LETIVAS</b>
              <p class="ls-label-info">Ex.:40 </p>
              <input type="text" name="matriz_semanas_letivas" value="" size="32" required>
              </label>
              <label class="ls-label col-md-4">
              <b class="ls-label-text">DIAS POR SEMANA</b>
              <p class="ls-label-info">Ex.:5 </p>
              <input type="text" name="matriz_dias_semana" value="" size="32" required>
              </label>
              <label class="ls-label col-md-6">
              <b class="ls-label-text">MINUTOS (AULA)</b>
              <p class="ls-label-info">Total de minutos da aula. Ex.:50</p>
              <input type="text" name="matriz_minutos_aula" value="" size="32" required>
              </label>
              <label class="ls-label col-md-6">
              <b class="ls-label-text">AULAS POR DIA</b>
              <p class="ls-label-info">Total de aulas por dia. Ex.:5</p>
              <input type="text" name="matriz_aula_dia" value="" size="32" required>
              </label>
              <label class="ls-label col-md-12">
              <b class="ls-label-text">OBSERVAÇÕES</b>
              <p class="ls-label-info">Informe detalhes sobre essa Matriz Curricular</p>
              <textarea name="matriz_obs" cols="50" rows="5"></textarea>
              </label>
              <div class="ls-modal-footer">
                <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
                <button type="submit" class="ls-btn-primary">SALVAR</button>
              </div>
              <input type="hidden" name="matriz_id_secretaria" value="<?php echo $row_Secretaria['sec_id']; ?>">
              <input type="hidden" name="matriz_hash" value="">
              <input type="hidden" name="MM_insert" value="form1">
            </form>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include_once "notificacoes.php"; ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Matriz);

mysql_free_result($Etapa);

mysql_free_result($CriterioAvaliativo);

mysql_free_result($MatrizDisciplinas);
?>