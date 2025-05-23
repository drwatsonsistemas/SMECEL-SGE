<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "../../sistema/funcoes/anoLetivo.php"; ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include "../../sistema/escola/fnc/calculos.php"; ?>



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


$colname_Matricula = "-1";
if (isset($_GET['cod'])) {
  $colname_Matricula = $_GET['cod'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Matricula, "text"));
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

$colname_Disciplina = "-1";
if (isset($_GET['disciplina'])) {
  $colname_Disciplina = $_GET['disciplina'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = sprintf("SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina WHERE disciplina_id = %s", GetSQLValueString($colname_Disciplina, "int"));
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_Turma[turma_matriz_id]'";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Criterios = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela FROM smc_criterios_avaliativos WHERE ca_id = '$row_Matriz[matriz_criterio_avaliativo]'";
$Criterios = mysql_query($query_Criterios, $SmecelNovo) or die(mysql_error());
$row_Criterios = mysql_fetch_assoc($Criterios);
$totalRows_Criterios = mysql_num_rows($Criterios);
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

      <title><?php echo $row_ProfLogado['func_nome']?> - Painel do Professor</title>
    
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
table.bordasimples {
	border-collapse: collapse;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
}
table.bordasimples tr td {
	border:1px dotted #000000;
	padding:2px;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
}
table.bordasimples tr th {
	border:1px dotted #000000;
	padding:2px;
	font-size:12px;
	alignment-adjust:central;
	text-align:center;
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
	
	  <h5>Lançamento de notas:</h5>
	  
	  <hr>

	  <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a>    
    

    
  
 <blockquote> <span style="margin-right:10px; text-align:center; float:left;">
                      <?php if ($row_Matricula['aluno_foto']=="") { ?>
                      <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="" border="0" width="50">
                      <?php } else { ?>
                      <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" class="" border="0" width="50">
                      <?php } ?>
                      <?php //echo $row_Alunos['aluno_nome']; ?>
                      </span> Turma: <strong><?php echo $row_Turma['turma_nome']; ?></strong><br>
                      Disciplina: <strong><?php echo $row_Disciplina['disciplina_nome']; ?></strong><br>
                      Aluno(a): <strong><?php echo $row_Matricula['aluno_nome']; ?></strong> </blockquote>
                    <p> <a href="alunos.php?disciplina=<?php echo $colname_Disciplina; ?>&turma=<?php echo $colname_Turma; ?>" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i> Voltar</a> <a href="javascript:void()" class="waves-effect waves-light btn-small btn-flat right recarregar">Recalcular</a> </p>
                    
					
					<?php if ($row_Matricula['vinculo_aluno_boletim']=="1") { ?>
                    
                    
                    
                    <table class="centered bordasimples">
                      <thead>
                        <tr class="purple blue lighten-1 white-text">
                          <th>PERÍODO</th>
                          <?php for ($c = 1; $c <= $row_Criterios['ca_qtd_av_periodos']; $c++) { ?>
                          <th>NOTA <?php echo $c; ?></th>
                          <?php } ?>
                          <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?><th class="ls-txt-center">RP</th><?php } ?>
                          <th>MÉDIA</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $tmu = 0; ?>
                        <?php for ($p = 1; $p <= $row_Criterios['ca_qtd_periodos']; $p++) { ?>
                        <tr>
                          <td width="70"><strong><?php echo $p; ?>º</strong></td>
                          <?php $ru = 0; ?>
                          <?php for ($a = 1; $a <= $row_Criterios['ca_qtd_av_periodos']; $a++) { ?>
                          <td><?php
								mysql_select_db($database_SmecelNovo, $SmecelNovo);
								$query_Notas = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
								$Notas = mysql_query($query_Notas, $SmecelNovo) or die(mysql_error());
								$row_Notas = mysql_fetch_assoc($Notas);
								$totalRows_Notas = mysql_num_rows($Notas);
								$ru = $ru + $row_Notas['nota_valor'];
							  ?>
                            <input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_av']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_Notas['nota_hash']; ?>" 
                              value="<?php echo $row_Notas['nota_valor']; ?>" 
                              notaAnterior="<?php echo $row_Notas['nota_valor']; ?>" 
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-field-md nota"
							  style="display:block; background-color:#F2F2F9; text-align:center; border:0px solid #000000; <?php if ($row_Notas['nota_valor'] >= $row_Criterios['ca_nota_min_av']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_Notas==0) { echo "disabled"; } ?>
                          >
                          </td>
                          <?php } ?>
                          
                          
                          <?php  
						
						if ($row_Criterios['ca_rec_paralela']=="S") { 
						
						mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaRecPar = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_Disciplina[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '98'";
                        $notaRecPar = mysql_query($query_notaRecPar, $SmecelNovo) or die(mysql_error());
                        $row_notaRecPar = mysql_fetch_assoc($notaRecPar);
                        $totalRows_notaRecPar = mysql_num_rows($notaRecPar);
						
						?>
                        <td>
						
						

                        <input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_recuperacao_final']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaRecPar['nota_hash']; ?>" 
                              value="<?php echo $row_notaRecPar['nota_valor']; ?>" 
                              notaAnterior="<?php echo $row_notaRecPar['nota_valor']; ?>" 
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-field-md nota"
							  style="display:block; background-color:#F2F2F9; text-align:center; border:0px solid #000000;  <?php if ($row_notaRecPar['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              
                          >
						
						
						<?php //echo exibeTraco($row_notaRecPar['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']); ?>
                        
                        
                        
                        
                        </td>
						<?php } ?>
                          
                          
                        <td width="70"><?php $mu = mediaUnidade($ru,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_media_min_periodo'],$row_Criterios['ca_calculo_media_periodo'],$row_Criterios['ca_qtd_av_periodos']); ?>
                            <?php $tmu = $tmu + $mu; ?></td>
                        </tr>
                        <?php } ?>
                        
                        
                        <tr height="50">
                          <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?><td></td><?php } ?>
                          <td colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+1; ?>">TOTAL DE PONTOS</td>
                          <td><?php $tp = totalPontos($tmu); ?></td>
                        </tr>
                        <tr height="50">
                          <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?><td></td><?php } ?>
                          <td colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+1; ?>">MÉDIA DO CURSO</td>
                          <td><?php $mc = mediaCurso($tp,$row_Criterios['ca_arredonda_media'],$row_Criterios['ca_aproxima_media'],$row_Criterios['ca_min_media_aprovacao_final'],$row_Criterios['ca_qtd_periodos']); ?></td>
                        </tr>
                        <tr height="50">
                          <?php  if ($row_Criterios['ca_rec_paralela']=="S") { ?><td></td><?php } ?>
                          <td colspan="<?php echo $row_Criterios['ca_qtd_av_periodos']+1; ?>">NOTA DE RECUPERAÇÃO</td>
                          <td><?php 
				mysql_select_db($database_SmecelNovo, $SmecelNovo);
				$query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND nota_id_disciplina = '$row_Disciplina[disciplina_id]' AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
				$notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
				$row_notaAf = mysql_fetch_assoc($notaAf);
				$totalRows_notaAf = mysql_num_rows($notaAf);
				$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_Criterios['ca_nota_min_recuperacao_final']);
			?>
                            <input 
                              type="text" 
                              inputmode="numeric"
                              max="<?php echo $row_Criterios['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_Criterios['ca_nota_min_recuperacao_final']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaAf['nota_hash']; ?>" 
                              value="<?php echo $row_notaAf['nota_valor']; ?>" 
                              notaAnterior="<?php echo $row_notaAf['nota_valor']; ?>" 
                              disciplina="<?php echo $row_Disciplina['disciplina_nome']; ?>" 
                              class="ls-field-md nota av_final_input"
							  style="display:block; background-color:#F2F2F9; text-align:center; border:0px solid #000000; <?php if ($row_notaAf['nota_valor'] >= $row_Criterios['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_notaAf==0) { echo "disabled"; } ?>
                          ></td>
                        </tr>
                      </tbody>
                    </table>
                    <br>
                    
                    
                    <table class="centered bordasimples">
                      <thead>
                        <tr class="purple blue lighten-1 white-text">
                          <th>RESULTADO</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr height="50">
                          <td>
								<?php 
                                    $resultado = resultadoFinal($mc, $af, $row_Criterios['ca_nota_min_recuperacao_final'], $row_Criterios['ca_min_media_aprovacao_final']);				
                                    if ($resultado == "APR") { echo "<div class=\"card-panel light-green lighten-2\">APROVADO</div>"; } else { echo "<div class=\"card-panel pink accent-1\">CONSERVADO</div>"; }
                                ?>
                		</td>
                        </tr>
                      </tbody>
                    </table>
                    <?php //mysql_free_result($Notas); ?>
                    <?php } else { ?>
                    <div class="card-panel yellow lighten-5">Boletim não gerado.</div>
                    <?php } ?>
                    <p>
                    <div id="status"></div>
                    </p>
   
		 
	</div>


     
	  </div>
    </div>
  </div>
  
      <!--JavaScript at end of body for optimized loading-->
   	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
<?php include ("rodape.php"); ?>      <script type="text/javascript" src="../js/app.js"></script>
	  <script src="../../js/jquery.mask.js"></script> 
      	<script type="text/javascript">
		$(document).ready(function(){
			$(".dropdown-trigger").dropdown();
			$('.sidenav').sidenav();
		});
	</script>
	
<script type="text/javascript">
				
$(document).ready(function(){
$("input").blur(function(){
	
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
				
$(document).ready(function(){
  $('.nota').mask('00.0', {reverse: true});
  $('.money').mask('000.000.000.000.000,00', {reverse: true});
});				

$(document).ready(function() {
            $('.recarregar').click(function() {
                location.reload();
            });
      });  		  
		   


$(function() {
   $(document).on('click', 'input[type=text]', function() {
     this.select();
   });
 });	 

</script>
	
    </body>
  </html>
  <?php
mysql_free_result($ProfLogado);
?>