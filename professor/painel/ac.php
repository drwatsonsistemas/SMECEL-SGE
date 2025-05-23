<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
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


$colname_Disciplinas = "-1";
if (isset($_GET['cod'])) {
  $colname_Disciplinas = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
disciplina_id, disciplina_nome, turma_id, turma_nome, turma_ano_letivo  
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id 
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND ch_lotacao_escola = %s AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_disciplina_id
", GetSQLValueString($colname_Disciplinas, "int"));
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

if ($totalRows_Disciplinas == 0) {
	header("Location:index.php?erro");
}


$colname_Escola = "-1";
if (isset($_GET['cod'])) {
  $colname_Escola = anti_injection($_GET['cod']);
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id = '$row_Disciplinas[ch_lotacao_escola]' AND escola_id = %s", GetSQLValueString($colname_Escola, "int"));
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AC = "
SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_criacao, disciplina_id, disciplina_nome, etapa_id, etapa_nome 
FROM smc_ac
LEFT JOIN smc_disciplina ON disciplina_id = ac_id_componente 
LEFT JOIN smc_etapa ON etapa_id = ac_id_etapa
WHERE ac_id_professor = '$row_ProfLogado[func_id]' AND ac_id_escola = '$colname_Escola' AND ac_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' ORDER BY ac_id DESC
";
$AC = mysql_query($query_AC, $SmecelNovo) or die(mysql_error());
$row_AC = mysql_fetch_assoc($AC);
$totalRows_AC = mysql_num_rows($AC);

$colname_Ac_editar = "-1";
if (isset($_GET['ac'])) {
  $colname_Ac_editar = $_GET['ac'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ac_editar = sprintf("SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, ac_recursos, ac_objeto_conhecimento, ac_metodologia, ac_avaliacao, ac_criacao FROM smc_ac WHERE ac_id = %s", GetSQLValueString($colname_Ac_editar, "int"));
$Ac_editar = mysql_query($query_Ac_editar, $SmecelNovo) or die(mysql_error());
$row_Ac_editar = mysql_fetch_assoc($Ac_editar);
$totalRows_Ac_editar = mysql_num_rows($Ac_editar);


if($totalRows_Escola=="") {
	header("Location:index.php?erro");
}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	
	$codigo = $_POST['ac_id'];
	
  $updateSQL = sprintf("UPDATE smc_ac SET ac_data_inicial=%s, ac_data_final=%s, ac_conteudo=%s, ac_objetivo_especifico=%s, ac_objeto_conhecimento=%s, ac_metodologia=%s, ac_recursos=%s, ac_avaliacao=%s WHERE ac_id=%s",
                       GetSQLValueString($_POST['ac_data_inicial'], "date"),
                       GetSQLValueString($_POST['ac_data_final'], "date"),
                       GetSQLValueString($_POST['ac_conteudo'], "text"),
                       GetSQLValueString($_POST['ac_objetivo_especifico'], "text"),
                       GetSQLValueString($_POST['ac_objeto_conhecimento'], "text"),
                       GetSQLValueString($_POST['ac_metodologia'], "text"),
                       GetSQLValueString($_POST['ac_recursos'], "text"),
                       GetSQLValueString($_POST['ac_avaliacao'], "text"),
					   GetSQLValueString($_POST['ac_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

  $updateGoTo = "ac.php?cod=$colname_Escola&editado";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_ac WHERE ac_id_professor=%s AND ac_id=%s",
                       GetSQLValueString($row_ProfLogado['func_id'], "text"),
					   GetSQLValueString($_GET['deletar'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "ac.php?cod=$colname_Escola&deletado";
//  if (isset($_SERVER['QUERY_STRING'])) {
  //  $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $deleteGoTo));
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
<title><?php echo $row_ProfLogado['func_nome']?>-</title>

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
        <h5>Planejamento de aulas</h5>
        <hr>
        <a href="disciplinas.php?cod=<?php echo $colname_Escola; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a> 
        
        <!-- Modal Trigger --> 
        <?php if ($totalRows_AC > 0) { // Show if recordset not empty ?>
  <table border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th class="center">DATA INÍCIO</th>
        <th class="center">DATA FINAL</th>
        <th class="center">COMPONENTE</th>
        <th class="center">ETAPA</th>
        <th class="center"></th>
        <th class="center"></th>
        </tr>
    </thead>
    <tbody>
      <?php $num = $totalRows_AC; do { ?>
        <tr>
          <td class="center"><?php echo inverteData($row_AC['ac_data_inicial']); ?></td>
          <td class="center"><?php echo inverteData($row_AC['ac_data_final']); ?></td>
          <td class="center"><?php echo $row_AC['disciplina_nome']; ?></td>
          <td class="center"><?php echo $row_AC['etapa_nome']; ?></td>
          <td class="center"><a href="ac.php?cod=<?php echo $colname_Escola; ?>&ac=<?php echo $row_AC['ac_id']; ?>"><i class="material-icons">create</i></a></td>
          <td class="center">
		  
		  <a href="javascript:func()" onclick="confirmaExclusao('cod=<?php echo $colname_Escola; ?>&deletar=<?php echo $row_AC['ac_id']; ?>')"><i class="material-icons red-text tooltipped" data-position="bottom" data-tooltip="Deletar aula">delete_forever</i></a>
		  
		  </td>
        </tr>
        <?php } while ($row_AC = mysql_fetch_assoc($AC)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
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
			$('.modal_cadastrar').modal();
		});
	</script>
	
	<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="langs/pt_BR.js"></script>

	<script>
	

	tinymce.init({
	  selector: 'textarea',
	  
	  mobile: {
      menubar: false
  },
	  
	  images_upload_url: 'postAcceptor.php',
	  automatic_uploads: true,
	  imagetools_proxy: 'proxy.php',
	  
	  //plugins: 'emoticons',
	  //toolbar: 'emoticons',
	  
	  //imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
	  	  
	  height: 200,
	  toolbar: ['paste undo redo | formatselect | forecolor | bold italic backcolor | bullist numlist | image | emoticons'],
	  plugins : ['textcolor','advlist autolink link image imagetools lists charmap print preview paste emoticons',
		'advlist autolink lists link image imagetools charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table paste code help wordcount'],
	  //force_br_newlines : false,
	  //force_p_newlines : false,
	  //forced_root_block : '',	
	  statusbar: false,
	  language: 'pt_BR',
	  menubar: false,
	  paste_as_text: true,
	  content_css: '//www.tinymce.com/css/codepen.min.css'
	});

</script>

<?php if (isset($_GET["ac"])) { ?>
        <!-- Modal Structure -->
        <div id="modal_editar" class="modal modal-fixed-footer modal_editar" style="height:100%">
          <div class="modal-content">
          <h4>EDITAR AC</h4>
          <p>
          <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
            <div class="row">
              <div class="input-field col s6">
                <input type="date" name="ac_data_inicial" value="<?php echo htmlentities($row_Ac_editar['ac_data_inicial'], ENT_COMPAT, ''); ?>" size="32">
                <label for="first_name">Data inicial</label>
              </div>
              <div class="input-field col s6">
                <input type="date" name="ac_data_final" value="<?php echo htmlentities($row_Ac_editar['ac_data_final'], ENT_COMPAT, ''); ?>" size="32">
                <label for="last_name">Data final</label>
              </div>
            </div>
			
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Direito de aprendizagem (EI) / Habilidades (EF)</b>
                <textarea name="ac_objetivo_especifico" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_objetivo_especifico'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Objetivo de aprendizagem (EI) / Objetos de Conhecimento (EF)</b>
                <textarea name="ac_objeto_conhecimento" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_objeto_conhecimento'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Metodologia</b>
                <textarea name="ac_metodologia" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_metodologia'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Recursos</b>
                <textarea name="ac_recursos" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_recursos'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Avaliações</b>
                <textarea name="ac_avaliacao" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_avaliacao'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="input-field col s12">
                <b for="textarea1">Observações</b>
                <textarea name="ac_conteudo" id="textarea2" cols="50" rows="5"><?php echo htmlentities($row_Ac_editar['ac_conteudo'], ENT_COMPAT, ''); ?></textarea>
              </div>
            </div>
			
			
            
            </p>
            </div>
            <div class="modal-footer">
              <input type="submit" class="btn" value="SALVAR">
			  <a href="ac.php?cod=<?php echo $colname_Escola; ?>" class="waves-effect waves-light btn-small btn-flat left"><i class="material-icons left">arrow_back</i> Voltar</a>
			  <input type="hidden" name="MM_update" value="form2">
			  <input type="hidden" name="ac_id" value="<?php echo $row_Ac_editar['ac_id']; ?>">
            </div>
          </form>
        </div>
	<script>
		$('.modal_editar').modal({
		dismissible: false
	});
    $('.modal_editar').modal('open');
	</script>
<?php } ?>

<?php if (isset($_GET["editado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">AC EDITADO COM SUCESSO</button>'});
</script>
<?php } ?>

<?php if (isset($_GET["cadastrado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">AC CADASTRADO COM SUCESSO</button>'});
</script>
<?php } ?>

<?php if (isset($_GET["deletado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">delete</i>&nbsp;<button class="btn-flat toast-action">AC DELETADO COM SUCESSO</button>'});
</script>
<?php } ?>

    	<script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Deseja realmente excluir esse planejamento?");
     	if (resposta == true) {
     	     window.location.href = "ac.php?"+codigo;
    	 }
	}
	</script>
	
</body>
</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($AC);

mysql_free_result($Ac_editar);
?>