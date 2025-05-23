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

$colname_Atividade = "-1";
if (isset($_GET['hash'])) {
  $colname_Atividade = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Atividade = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_google_form, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula 
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_hash = %s", GetSQLValueString($colname_Atividade, "text"));
$Atividade = mysql_query($query_Atividade, $SmecelNovo) or die(mysql_error());
$row_Atividade = mysql_fetch_assoc($Atividade);
$totalRows_Atividade = mysql_num_rows($Atividade);


if($totalRows_Atividade=="") {
	header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, 
com_at_aluno_comentario, com_at_aluno_comentario_professor, com_at_aluno_comentario_professor_data, com_at_aluno_duvida, 
vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_id, aluno_nome, aluno_foto 
FROM smc_coment_ativ_aluno 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = com_at_aluno_id_matricula
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE com_at_aluno_id_atividade = '$row_Atividade[plano_aula_id]'
ORDER BY com_at_aluno_id ASC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtividadesEnviadas = "
SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, 
plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora,
plano_aula_anexo_atividade_resposta_professor, plano_aula_anexo_atividade_visualizada_professor, plano_aula_anexo_atividade_visualizada_aluno,
vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_id, aluno_nome, aluno_foto   
FROM smc_plano_aula_anexo_atividade
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = plano_aula_anexo_atividade_id_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE plano_aula_anexo_atividade_id_atividade = '$row_Atividade[plano_aula_id]'
GROUP BY plano_aula_anexo_atividade_id_aluno 
ORDER BY plano_aula_anexo_atividade_id DESC";
$AtividadesEnviadas = mysql_query($query_AtividadesEnviadas, $SmecelNovo) or die(mysql_error());
$row_AtividadesEnviadas = mysql_fetch_assoc($AtividadesEnviadas);
$totalRows_AtividadesEnviadas = mysql_num_rows($AtividadesEnviadas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VideoAula = "SELECT * FROM smc_videoaula WHERE videoaula_id_aula = '$row_Atividade[plano_aula_id]'";
$VideoAula = mysql_query($query_VideoAula, $SmecelNovo) or die(mysql_error());
$row_VideoAula = mysql_fetch_assoc($VideoAula);
$totalRows_VideoAula = mysql_num_rows($VideoAula);

function processtext($text,$nr=30)
    {
        $mytext=explode(" ",trim($text));
        $newtext=array();
        foreach($mytext as $k=>$txt)
        {
            if (strlen($txt)>$nr)
            {
                $txt=wordwrap($txt, $nr, " ", 1);
            }
            $newtext[]=$txt;
        }
        return implode(" ",$newtext);
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
.leitura img {
	max-width:100%;
	height:auto;	
	margin:10px 0;
}
iframe {
	display:block; width:100%; border:none; margin:0; padding:0;
}
.prof {
  background-color: #ddd;
  border-radius: 100%;
  height: 40px;
  object-fit: cover;
  width: 40px;  
}

.aluno {
  background-color: #ddd;
  border-radius: 100%;
  height: 60px;
  object-fit: cover;
  width: 60px;  
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
	
	  <h5>Atividade</h5>
	  
	  <hr>
	  
    <a href="plano_aula.php?turma=<?php echo $row_Atividade['plano_aula_id_turma']; ?>&disciplina=<?php echo $row_Atividade['plano_aula_id_disciplina']; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
    

	  <blockquote>
      <h5><?php echo $row_Atividade['plano_aula_id']; ?> - <?php echo $row_Atividade['plano_aula_texto']; ?></h5>
      <p><?php echo $row_Atividade['func_nome']; ?> - <?php echo $row_Atividade['disciplina_nome']; ?></p>
	  <small><?php echo inverteData($row_Atividade['plano_aula_data']); ?></small>
      </blockquote>
	  

	        
	  <div class="leitura">
	  <div class="card-panel1">
	  <p class="flow-text"><?php echo $row_Atividade['plano_aula_conteudo']; ?></p>
	  </div>

	  <?php if ($totalRows_VideoAula > 0) { ?>
<p>


<?php do { ?>

<div class="card-panel1">
<video width="100%" controls>
  <source src="../../videoaula/<?php echo $row_Atividade['plano_aula_id_turma']; ?>/<?php echo $row_Atividade['plano_aula_id']; ?>/<?php echo $row_ProfLogado['func_id']; ?>/<?php echo $row_Atividade['disciplina_id']; ?>/<?php echo $row_VideoAula['videoaula_nome']; ?>" type="video/mp4">
Seu navegador não suporta estes arquivos.
</video>
</div>

<?php } while ($row_VideoAula = mysql_fetch_assoc($VideoAula)); ?>


</p>
<?php } ?> 	  
	  
	  <?php if ($row_Atividade['plano_aula_video']<>"") { ?>
      <div class="card-panel1">
	  <h5 class="center">Vídeo de apoio</h5>
      <p>
	  
	  
<?php
	  
function youtube_id_from_url($url) {
    $pattern = 
        '%^# Match any YouTube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        |youtube(?:-nocookie)?\.com  # or youtube.com and youtube-nocookie
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char YouTube id.
        %x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}
	 
$id = youtube_id_from_url($row_Atividade['plano_aula_video']);
$width = '853';
$height = '480';

?>

		<div class="video-container">
		<iframe id="ytplayer" type="text/html" width="<?php echo $width ?>" height="<?php echo $height ?>"
			src="https://www.youtube.com/embed/<?php echo $id ?>?rel=0&showinfo=0&color=white&iv_load_policy=3"
			frameborder="0" allowfullscreen></iframe> 
		</div>
		</p>
		</div>

      <?php } ?>
	  <br>
	  <?php if ($row_Atividade['plano_aula_atividade']<>"") { ?>
	  <div class="card-panel1"><h5 class="center">Atividade proposta</h5>
	  <p class="flow-text"><?php echo $row_Atividade['plano_aula_atividade']; ?></p></div>
      <?php } ?>
	  
	  
      </div>
	  
	  
	  <br>
	  <?php if ($row_Atividade['plano_aula_google_form']<>"") { ?>
	  <h5 class="center">AVALIAÇÃO ONLINE</h5>
	  <?php echo $row_Atividade['plano_aula_google_form']; ?>
	  <br>
      <?php } ?>
	  
	  
      
      
      <h5>Comentários (<?php echo $totalRows_Comentarios; ?>):</h5>
	  
      <?php if ($totalRows_Comentarios > 0) { ?>
	  <br>
	  


	  <table class="striped1 1highlight">


        <?php do { ?>
          <tr id="comentario_<?php echo $row_Comentarios['com_at_aluno_id']; ?>" class="" style="border:2px solid #cacaca;">
            <td class="top" valign="top" style="vertical-align: top;" width="60">
			
			
				<?php if ($row_Comentarios['aluno_foto']=="") { ?>
				<img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable aluno">
				<?php } else { ?>
				<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Comentarios['aluno_foto']; ?>" width="100%" class="hoverable aluno">
				<?php } ?>
				
			
			</td>
			
            <td valign="top" style="vertical-align: top;">
			
		    <small style="font-size:10px;"> 
			<a href="#"><?php echo current( str_word_count($row_Comentarios['aluno_nome'],2)); ?>
				<?php $word = explode(" ", trim($row_Comentarios['aluno_nome'])); echo $word[count($word)-1]; ?></a> <?php if ($row_Comentarios['com_at_aluno_duvida']=="S") { ?><span class="orange"> Dúvida </span><?php } ?>
				  <span class="right"><?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_data_hora'])); ?></span>			
			</small> 
			
			
			<p><?php echo nl2br(processtext($row_Comentarios['com_at_aluno_comentario'])); ?></p>
			<small><a href="resposta_comentario.php?comentario=<?php echo $row_Comentarios['com_at_aluno_id']; ?>&hash=<?php echo $row_Atividade['plano_aula_hash']; ?>">Responder</a></small>
			
			
			<?php if ($row_Comentarios['com_at_aluno_comentario_professor']<>"") { ?>
			
			<p>
            <div style="padding: 10px 10px; border-left:1px solid #066; margin-left:10px; background-color:#F9F9F9;" class="1blue lighten-5">
			
			
		<p style="display: block; float:left; width:40px; height:80px; margin-right:10px;">
				
		
        <?php if ($row_ProfLogado['func_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable prof">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_ProfLogado['func_foto']; ?>" width="100%" class="hoverable prof">
        <?php } ?>
	 

	 
				
		</p>
			
			
			<p><small class="right"><?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_comentario_professor_data'])); ?></small></p>            
			<strong>Sua resposta:</strong>
			<p><?php echo nl2br(processtext($row_Comentarios['com_at_aluno_comentario_professor'])); ?></p>
			</div>
            </p>
			
			<?php } ?>


			</td>
            

          </tr>
          <?php } while ($row_Comentarios = mysql_fetch_assoc($Comentarios)); ?>
      </table>
      

      
      
<?php } ?>

      <br>
      
      <div class="card-panel">
      
      <h4>Atividades respondidas</h4>
      
      <?php if ($totalRows_AtividadesEnviadas > 0) { ?>
      <table>
        <tr>
          <td>ALUNO</td>
          <td>DATA/HORA</td>
          <td></td>
          <td></td>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_AtividadesEnviadas['aluno_nome']; ?></td>
            <td class="center"><?php echo date('H\hi - d/m/Y', strtotime($row_AtividadesEnviadas['plano_aula_anexo_atividade_data_hora'])); ?></td>
			<td class="center"><?php if ($row_AtividadesEnviadas['plano_aula_anexo_atividade_visualizada_professor']=="N") { ?><span class="orange">NOVA</span><?php } else { ?><span class="green">CORRIGIDA</span><?php } ?></td>
			<td class="center"><a href="resposta_atividade.php?atividade=<?php echo $row_AtividadesEnviadas['plano_aula_anexo_atividade_id']; ?>&hash=<?php echo $row_Atividade['plano_aula_hash']; ?>">VISUALIZAR/RESPONDER</a></td>
          </tr>
          <?php } while ($row_AtividadesEnviadas = mysql_fetch_assoc($AtividadesEnviadas)); ?>
      </table>
      <?php } else { ?>
      
      <p>Nenhuma atividade respondida até o momento.</p>
	  
	  <?php } ?>
      
      
      </div>
	  


	  

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


		 
	
    </div>
		

     
	  </div>
    </div>
  </div>
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>      <script type="text/javascript" src="../js/app.js"></script>
      	<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>
	

	

	
	<?php if (isset($_GET["respondido"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">COMENTÁRIO RESPONDIDO COM SUCESSO</button>'});
</script>
  <?php } ?>
	
	
    </body>
  </html>
  <?php
mysql_free_result($Atividade);

mysql_free_result($ProfLogado);

mysql_free_result($AtividadesEnviadas);
?>