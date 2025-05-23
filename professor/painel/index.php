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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escolas = "
SELECT 
ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
escola_id, escola_nome, turma_id, turma_nome, turma_ano_letivo 
FROM smc_ch_lotacao_professor
INNER JOIN smc_escola ON escola_id = ch_lotacao_escola
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_professor_id = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY ch_lotacao_escola";
$Escolas = mysql_query($query_Escolas, $SmecelNovo) or die(mysql_error());
$row_Escolas = mysql_fetch_assoc($Escolas);
$totalRows_Escolas = mysql_num_rows($Escolas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atividades = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_hash, turma_id, turma_ano_letivo 
FROM smc_plano_aula 
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY plano_aula_data DESC";
$Atividades = mysql_query($query_Atividades, $SmecelNovo) or die(mysql_error());
$row_Atividades = mysql_fetch_assoc($Atividades);
$totalRows_Atividades = mysql_num_rows($Atividades);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UltimasAulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_hash, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_ano_letivo 
FROM smc_plano_aula
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY plano_aula_data DESC LIMIT 0, 5";
$UltimasAulas = mysql_query($query_UltimasAulas, $SmecelNovo) or die(mysql_error());
$row_UltimasAulas = mysql_fetch_assoc($UltimasAulas);
$totalRows_UltimasAulas = mysql_num_rows($UltimasAulas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, com_at_aluno_duvida, com_at_aluno_comentario_professor,
plano_aula_id, plano_aula_texto, plano_aula_id_professor, plano_aula_id_disciplina, plano_aula_id_turma, plano_aula_hash, turma_id, turma_ano_letivo
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE com_at_aluno_duvida = 'S' AND plano_aula_id_professor = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND com_at_aluno_comentario_professor IS NULL
ORDER BY com_at_aluno_id DESC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ComentariosFull = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario, com_at_aluno_duvida, com_at_aluno_comentario_professor,
plano_aula_id, plano_aula_texto, plano_aula_id_professor, plano_aula_id_disciplina, plano_aula_id_turma, plano_aula_hash, turma_id, turma_ano_letivo
FROM smc_coment_ativ_aluno
INNER JOIN smc_plano_aula ON plano_aula_id = com_at_aluno_id_atividade
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
WHERE com_at_aluno_duvida = 'S' AND plano_aula_id_professor = '$row_ProfLogado[func_id]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY com_at_aluno_id DESC";
$ComentariosFull = mysql_query($query_ComentariosFull, $SmecelNovo) or die(mysql_error());
$row_ComentariosFull = mysql_fetch_assoc($ComentariosFull);
$totalRows_ComentariosFull = mysql_num_rows($ComentariosFull);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtividadesEnviadas = "
SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, 
plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora,
plano_aula_anexo_atividade_resposta_professor, plano_aula_anexo_atividade_visualizada_professor, plano_aula_anexo_atividade_visualizada_aluno,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_ano_letivo, aluno_id, aluno_nome, aluno_foto,
plano_aula_id, plano_aula_id_professor, plano_aula_hash   
FROM smc_plano_aula_anexo_atividade
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = plano_aula_anexo_atividade_id_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_plano_aula ON plano_aula_id = plano_aula_anexo_atividade_id_atividade
WHERE plano_aula_id_professor = '$row_ProfLogado[func_id]' AND plano_aula_anexo_atividade_visualizada_professor = 'N' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
GROUP BY plano_aula_anexo_atividade_id_aluno
ORDER BY plano_aula_anexo_atividade_id DESC";
$AtividadesEnviadas = mysql_query($query_AtividadesEnviadas, $SmecelNovo) or die(mysql_error());
$row_AtividadesEnviadas = mysql_fetch_assoc($AtividadesEnviadas);
$totalRows_AtividadesEnviadas = mysql_num_rows($AtividadesEnviadas);
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

      <title><?php echo $row_ProfLogado['func_nome']?> - </title>
    
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

<link href='../../sistema/calendar/core/main.css' rel='stylesheet' />
<link href='../../sistema/calendar/daygrid/main.css' rel='stylesheet' />
<script src='../../sistema/calendar/core/main.js'></script>
<script src='../../sistema/calendar/interaction/main.js'></script>
<script src='../../sistema/calendar/daygrid/main.js'></script>
<script src='../../sistema/calendar/core/main.js'></script>
<script src='../../sistema/calendar/core/locales/pt-br.js'></script>


<style>

  #calendar {
    max-width: 900px;
    margin: 0 auto;
	font-size:12px;
  }

table {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}

th, td {
	border:1px solid #ccc;
	padding:15px;
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
	 
	 
<small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small>		
<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
        </small>
	 
	 </p>
	 
	 <?php include "menu_esq.php"; ?>
	 
	 
	 </div>
     
    <div class="col s12 m10">
	
	  <h5>Bem-vindo(a),
        <?php $nome = explode(" ",$row_ProfLogado['func_nome']); echo $nome[0]; ?>
      </h5>
	  
	  <hr>
	  

	
<!--card stats start-->
      <div class="row">

         <a href="atividades.php">
		 <div class="col s12 m3 center">
            <div class="card white-text indigo lighten-2">
               <div class="card-content center">
                     
                     <h5 class="white-text lighten-4"><?php echo $totalRows_Atividades; ?></h5>
                     <p class="white-text lighten-4">aulas</p>
                     <p class="white-text lighten-4">cadastradas</p>
                  </div>
            </div>
         </div>
		 </a>

         <a href="comentarios.php">
         <div class="col s12 m3 center">
            <div class="card white-text orange">
               <div class="card-content center">
                     <h5 class="white-text lighten-4"><?php echo $totalRows_ComentariosFull; ?></h5>
                     <p class="white-text lighten-4">dúvidas</p>
                     <small class="white-text lighten-3"><?php if ($totalRows_Comentarios > 0) { ?><i class="tiny material-icons white-text">error</i> <?php echo $totalRows_Comentarios; ?> sem respostas<?php } else { ?><i class="tiny material-icons red-text">favorite</i> Todas respondidas <?php } ?></small>
                  </div>
            </div>
         </div>
		 </a>
		 
         <a href="#">
         <div class="col s12 m3 center">
            <div class="card white-text blue lighten-1">
               <div class="card-content center">
                     <h5 class="white-text lighten-4">0</h5>
                     <p class="white-text lighten-4">avisos da</p>
                     <p class="white-text lighten-4">escola</p>
                  </div>
            </div>
         </div>
		 </a>
		 
         <a href="#">
         <div class="col s12 m3 center">
            <div class="card white-text green lighten-1">
               <div class="card-content center">
                     <h5 class="white-text lighten-4"><?php echo $totalRows_Escolas; ?></h5>
                     <p class="white-text lighten-4">escola(s)</p>
                     <p class="white-text lighten-4">vinculadas</p>
                  </div>
            </div>
         </div>
		 </a>

      </div>
   <!--card stats end-->

	  <?php if ($totalRows_AtividadesEnviadas > 0) { ?>
	  
	  
	  
  <ul class="collapsible">
    <li>
      <div class="collapsible-header"><i class="material-icons">directions_run</i>Corrigir atividades (<?php echo $totalRows_AtividadesEnviadas; ?>)</div>
      <div class="collapsible-body"><span>
		  <table>
		  <thead>
			<tr>
			  <th class="center">ALUNO</th>
			  <th class="center">DATA/HORA</th>
			  <th></th>
			</tr>
			</thead>
			<tbody>
			<?php do { ?>
			  <tr>
				<td><?php echo $row_AtividadesEnviadas['aluno_nome']; ?></td>
				<td class="center"><?php echo date('H\hi - d/m/Y', strtotime($row_AtividadesEnviadas['plano_aula_anexo_atividade_data_hora'])); ?></td>
				<td class="center"><a href="forum.php?atividade=<?php echo $row_AtividadesEnviadas['plano_aula_anexo_atividade_id']; ?>&hash=<?php echo $row_AtividadesEnviadas['plano_aula_hash']; ?>">VISUALIZAR</a></td>
			  </tr>
			  <?php } while ($row_AtividadesEnviadas = mysql_fetch_assoc($AtividadesEnviadas)); ?>
			  </tbody>
		  </table>	  
	  </span></div>
    </li>
  </ul>
	  
	  
 

	  
	  <?php } ?>
		
        
		<div style="border: 1px solid #fafafa; padding:5px; margin:10px 0;">
		
		<h5><i class="tiny material-icons green-text">local_library</i> Escolas vinculadas</h5>
        
		<?php if ($totalRows_Escolas <> 0) { ?>
		 <table class="striped">
		 <?php do { ?>
         <tr>
         <td><a href="disciplinas.php?cod=<?php echo $row_Escolas['ch_lotacao_escola']; ?>" class=""><?php echo $row_Escolas['escola_nome']; ?><i class="tiny material-icons right">chevron_right</i></a></td>
         </tr>
         <?php } while ($row_Escolas = mysql_fetch_assoc($Escolas)); ?>
		</table>
		 <?php } else { ?>
		 <p>Nenhuma escola encontrada com turma ativa para este professor.</p>
		 <?php } ?>
		</div>
		

		
		<br>
		
		<div class="card-panel">
		<h5>Pesquisa</h5>
		<p>Prezado(a) professor(a),</p>
		<p>Contamos com sua ajuda para participar de uma rápida pesquisa em relação ao seu uso no Painel do Professor.</p>
		<p><a href="https://forms.gle/Aavn4cevZPt2YXAYA" class="waves-effect waves-light btn" target="_blank"><i class="material-icons left">info_outline</i>RESPONDER PESQUISA</a></p>
		<i>Se já respondeu essa pesquisa, pode desconsiderar este aviso.</i>
		</div>
		<br><br>
		

     
		
		
		
						

		
		
		 
		</div>
		

		

     
	  </div>
    </div>
  </div>
  
  
 <?php include ("rodape.php"); ?>
  
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
      <script type="text/javascript" src="../js/app.js"></script>
      	<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
			$('.collapsible').collapsible();
		});
	</script>
	
	<?php if (isset($_GET["erro"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class="btn-flat toast-action">ALGO DE ERRADO ACONTECEU POR AQUI</button>'});
</script>
  <?php } ?>
	
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);

mysql_free_result($Atividades);

mysql_free_result($UltimasAulas);

mysql_free_result($Comentarios);

mysql_free_result($Escolas);
?>