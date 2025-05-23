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


$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = anti_injection($_GET['turma']);
} else {
	header("Location:chamada.php");
	}

$colname_Aula = "-1";
if (isset($_GET['turma'])) {
  $colname_Aula = anti_injection($_GET['turma']);
}


if (isset($_GET['data'])) {
  $data = anti_injection($_GET['data']);
  $semana = date("w", strtotime($data));
  $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado');
  $dia_semana_nome = $diasemana[$semana];
} else {
	header("Location:chamada.php");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aula = sprintf("
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, 
ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE ch_lotacao_id = %s", GetSQLValueString($colname_Aula, "int"));
$Aula = mysql_query($query_Aula, $SmecelNovo) or die(mysql_error());
$row_Aula = mysql_fetch_assoc($Aula);
$totalRows_Aula = mysql_num_rows($Aula);

if($totalRows_Aula == 0) {
	//header("Location:chamada.php");
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer FROM smc_turma WHERE turma_id = %s", GetSQLValueString($row_Aula['ch_lotacao_turma_id'], "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculos = "SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario FROM smc_vinculo WHERE vinculo_id_escola = '$row_Turma[turma_id_escola]' AND vinculo_id_funcionario = '$row_ProfLogado[func_id]'";
$Vinculos = mysql_query($query_Vinculos, $SmecelNovo) or die(mysql_error());
$row_Vinculos = mysql_fetch_assoc($Vinculos);
$totalRows_Vinculos = mysql_num_rows($Vinculos);

include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Outras = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, 
ch_lotacao_escola, disciplina_id, disciplina_nome, disciplina_nome_abrev, turma_id, turma_ano_letivo, turma_turno
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
WHERE turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
AND ch_lotacao_dia = '$semana' 
AND ch_lotacao_professor_id = '$row_Vinculos[vinculo_id_funcionario]'
ORDER BY turma_turno, ch_lotacao_aula ASC";
$Outras = mysql_query($query_Outras, $SmecelNovo) or die(mysql_error());
$row_Outras = mysql_fetch_assoc($Outras);
$totalRows_Outras = mysql_num_rows($Outras);

if($totalRows_Outras < 1) {
	header("Location:chamada.php?erro");
}

$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = anti_injection($_GET['turma']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, 
vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, 
aluno_id, aluno_nome, aluno_nascimento, aluno_foto,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_turma = %s
ORDER BY aluno_nome ASC
", GetSQLValueString($row_Turma['turma_id'], "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);



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
table1 {
	width:100%;
	border-collapse: collapse;
	font-size:12px;
}
th1, td1 {
	border:1px solid #ccc;
	padding:5px;
	height:15px;
	line-height:15px;
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
          <small><a href="foto.php"><i class="tiny material-icons">photo_camera</i></a></small> <small style="font-size:14px;"> <?php echo current( str_word_count($row_ProfLogado['func_nome'],2)); ?>
          <?php $word = explode(" ", trim($row_ProfLogado['func_nome'])); echo $word[count($word)-1]; ?>
          </small> </p>
        <?php include "menu_esq.php"; ?>
      </div>
      <div class="col s12 m10">
        <h5>CHAMADA</h5>
        <hr>
        <a href="chamada.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>
		
		<blockquote>
		<h5><?php echo date("d/m/Y", strtotime($data)); ?> (<?php echo $dia_semana_nome; ?>)</h5>
		<p><b><?php echo $row_Turma['turma_nome']; ?> - <?php echo $row_Aula['turma_turno_nome']; ?></b></p>
        <p><?php echo $row_Aula['disciplina_nome']; ?> - <?php echo $row_Aula['ch_lotacao_aula']; ?>ª aula</p>
        <p></p>
		</blockquote>
        

        
        
        <?php do { ?>
         
          <a href="chamada_alunos.php?data=<?php echo $data; ?>&turma=<?php echo $row_Outras['ch_lotacao_id']; ?>" class="waves-effect waves-light btn-small<?php if ($row_Outras['ch_lotacao_id']==$row_Aula['ch_lotacao_id']) {?> orange<?php } ?>"><?php echo $row_Outras['ch_lotacao_aula']; ?>ª</a>
        
        <?php } while ($row_Outras = mysql_fetch_assoc($Outras)); ?>
          
          
<table>
        <thead>
          <tr>
            <th width="50" class="center"></th>
            <th>ALUNO</th>
            <th width="150" class="center"></th>
          </tr>
          </thead>
          <tbody>
          <?php 
		  $num = 1;
		  do { ?>
            <tr>
              <td class="center">
			  
			<span>
			<?php if ($row_Alunos['aluno_foto']=="") { ?>
			<img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg"  class="hoverable aluno" border="0" width="100%">
			<?php } else { ?>
			<img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>"  class="hoverable aluno" border="0" width="100%">
			<?php } ?>
			<?php //echo $row_Alunos['aluno_nome']; ?>
			</span>
			  
			  </td>
              <td>
			  
			  <small><?php echo $row_Alunos['aluno_nome']; ?></small>
			  <?php //echo current( str_word_count($row_Alunos['aluno_nome'],2)); ?>
                  <?php //$nome = explode(" ", trim($row_Alunos['aluno_nome'])); echo $nome[count($nome)-1]; ?>
			  
			  
			  </td>
			  
			  
			  <?php
			  
			  	  mysql_select_db($database_SmecelNovo, $SmecelNovo);
				  $query_Verifica = "
				  SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data 
				  FROM smc_faltas_alunos 
				  WHERE faltas_alunos_matricula_id = '$row_Alunos[vinculo_aluno_id]' AND faltas_alunos_data = '$data' AND faltas_alunos_numero_aula = '$row_Aula[ch_lotacao_aula]'";
				  $Verifica = mysql_query($query_Verifica, $SmecelNovo) or die(mysql_error());
				  $row_Verifica = mysql_fetch_assoc($Verifica);
				  $totalRows_Verifica = mysql_num_rows($Verifica);
			  
			  ?>
			  
			  
              <td class="center card-panel1">
			  <!-- Switch -->
				  Ausente?
				  <div class="switch">
					<label>
					  
					  <input <?php if($totalRows_Verifica > 0) { echo "checked"; }?> type="checkbox" aluno="<?php echo $row_Alunos['aluno_nome']; ?>" data="<?php echo $data; ?>" matricula="<?php echo $row_Alunos['vinculo_aluno_id']; ?>" disciplina="<?php echo $row_Aula['disciplina_id']; ?>" aula_numero="<?php echo $row_Aula['ch_lotacao_aula']; ?>">
					  <span class="lever"></span>
					  SIM
					</label>
				  </div>
			  </td>
            </tr>
            <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        	</tbody>
        </table>
        
      </div>

    </div>
  </div>
<div id="status"></div>
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
		});
	</script>
	
<script type="text/javascript">



$(document).ready(function() {
  $("input[type='checkbox']").on('click', function() {
	  
	  
	var matricula 		= $(this).attr('matricula');
	var aula_numero 	= $(this).attr('aula_numero');
	var data 			= $(this).attr('data');
	var disciplina 		= $(this).attr('disciplina');  
	var aluno 			= $(this).attr('aluno');  
	  
	  
	  
	  
    if ($(this).prop('checked')) {
		
		$.ajax({
		type : 'POST',
        url  : 'fnc/frequencia.php',
        data : {
			matricula				:matricula,
			aula_numero				:aula_numero,
			data					:data,
			aluno					:aluno,
			disciplina				:disciplina
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},5000);
				
				}
		})
		
      //alert('Falta do aluno '+aluno+' INCLUÍDA com sucesso!');
      return true;
    
	}
	
		$.ajax({
		type : 'POST',
        url  : 'fnc/frequencia.php',
        data : {
			matricula				:matricula,
			aula_numero				:aula_numero,
			data					:data,
			aluno					:aluno,
			disciplina				:disciplina
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},5000);
				
				}
		})
	
	
      //alert('Falta do aluno '+aluno+' REMOVIDA com sucesso!');
	  return true;
	
  });
});


/*				
$(document).ready(function(){
$("input[type='checkbox']").blur(function(){
	
	var id 				= $(this).attr('name');
	var valor 			= $(this).val();
	var notaAnterior 	= $(this).attr('notaAnterior');
	var notaMax 		= $(this).attr('max');
	var notaMin 		= $(this).attr('notaMin');
	var disciplina 		= $(this).attr('disciplina');
	
	if (valor < notaMin) {
		$(this).css("color", "red");
		} else {
			$(this).css("color", "blue");
			}
	
	
	if( (valor != notaAnterior) ) {
	$.ajax({
		type : 'POST',
        url  : 'fnc/lancaNota.php',
        data : {
			id				:id,
			valor			:valor,
			notaMax			:notaMax,
			notaAnterior	:notaAnterior,
			disciplina		:disciplina
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},15000);
				
				}
		})
	}
	
	  });
});
*/

</script>
	
</body>
</html>
<?php
mysql_free_result($ProfLogado);

mysql_free_result($Turma);

mysql_free_result($Outras);

mysql_free_result($Aula);

mysql_free_result($Alunos);
?>