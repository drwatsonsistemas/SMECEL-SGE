<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
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
$MM_authorizedUsers = "6";
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, 
aluno_filiacao1, aluno_filiacao2, 
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo, 
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDÍGENA'
WHEN 6 THEN 'NÃO DECLARADA'
END AS aluno_raca, 
CASE aluno_nacionalidade
WHEN 1 THEN 'BRASILEIRA'
WHEN 2 THEN 'BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO'
WHEN 3 THEN 'EXTRANGEIRO'
END AS aluno_nacionalidade, 
aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge,  
CASE aluno_aluno_com_deficiencia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_aluno_com_deficiencia, 
aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, 
CASE aluno_tipo_certidao
WHEN 1 THEN 'MODELO ANTIGO'
WHEN 2 THEN 'MODELO NOVO'
END AS aluno_tipo_certidao, 
aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, 
aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao, 
aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, 
aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, 
CASE aluno_laudo
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_laudo, 
CASE aluno_alergia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_alergia, 
aluno_alergia_qual, 
CASE aluno_destro
WHEN 1 THEN 'DESTRO'
WHEN 2 THEN 'CANHOTO'
END AS aluno_destro, 
aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
aluno_prof_mae, aluno_tel_mae, 
CASE aluno_escolaridade_mae
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_mae, 
aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, 
CASE aluno_escolaridade_pai
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_pai, 
aluno_rg_pai, aluno_cpf_pai, aluno_hash, 
CASE aluno_recebe_bolsa_familia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_recebe_bolsa_familia,
aluno_foto,
aluno_aceite_termos,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf 
FROM smc_aluno
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome,
turma_id, turma_nome, turma_turno, turma_etapa,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = $row_Matricula[vinculo_aluno_id_turma]";
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = $row_Turma[turma_matriz_id]";
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina,
disciplina_id, disciplina_nome, disciplina_cor_fundo 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = $row_Matriz[matriz_id]";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

if ($row_Matricula['vinculo_aluno_situacao']<>"1") { 
  header("Location:index.php");
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
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $row_AlunoLogado['aluno_nome']; ?> - <?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?> - <?php echo $row_Matricula['escola_nome']; ?></title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
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
</style>


</head>
<body class="indigo lighten-5">

<?php include "menu_top.php"?>

<div class="container">
  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 hide-on-small-only">
      <p>
        <?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } ?>
				<br>
		<small style="font-size:14px;">
                  <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small>
      </p>

	<?php include "menu_esq.php"; ?>

    </div>
        
    <div class="col s12 m10">
      
 <h5><strong>DISCIPLINAS</strong></h5>
 <hr>
<a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>VOLTAR</a>
 <a href="aulasNovas.php" class="waves-effect waves-light purple btn-small btn"><i class="material-icons left">new_releases</i>NOVAS</a>
 <a href="aulas.php" class="waves-effect waves-light btn-small btn"><i class="material-icons left">alarm_on</i>HOJE</a>
 <a href="aulasTodas.php" class="waves-effect waves-light orange btn-small btn"><i class="material-icons left">all_inclusive</i>TODAS</a>
 <a href="disciplinas.php" class="waves-effect waves-light btn-small btn green lighten-1 disabled"><i class="material-icons left">apps</i>DISCIPLINAS</a> 
    
<hr>
 
 
 <div class="row">
 <?php do { ?>
 
 <?php
 //CONTAGEM
$dataa = date('Y-m-d');

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash 
FROM smc_plano_aula
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_id_disciplina = '$row_Disciplinas[matriz_disciplina_id_disciplina]' AND plano_aula_publicado = 'S' AND plano_aula_data <= '$dataa' AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL OR plano_aula_google_form IS NOT NULL)
";
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo); 

//CONTAGEM

 ?>
 
 		 <a href="aulasTodas.php?disciplina=<?php echo $row_Disciplinas['matriz_disciplina_id_disciplina']; ?>">
         <div class="col s12 m4 center center-align">
            <div class="card white-text lighten-2 center-align" style="background-color:<?php echo $row_Disciplinas['disciplina_cor_fundo']; ?>">
               <div class="card-content center center-align">
                     <h5 class="white-text lighten-4 center-align flow-text" style="height:120px; vertical-align:central;"><?php echo $row_Disciplinas['disciplina_nome']; ?></h5>
                     <p class="white-text lighten-4"><?php echo $totalRows_Conteudo; ?> AULAS</p>
                     <a href="aulasTodas.php?disciplina=<?php echo $row_Disciplinas['matriz_disciplina_id_disciplina']; ?>" class="btn-small btn-flat">VISUALIZAR</a>
                  </div>
            </div>
         </div>
         </a>
 
     
 <?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>
 </div>  
    
    
    
    </div>
  </div>
</div>

<!--JavaScript at end of body for optimized loading--> 
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
<script type="text/javascript" src="../js/materialize.min.js"></script> 
<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.dropdown-trigger').dropdown();
		});
	</script>
</body>
</html>
<?php
mysql_free_result($AlunoLogado);

mysql_free_result($Matricula);

mysql_free_result($Turma);

mysql_free_result($Matriz);

mysql_free_result($Disciplinas);

mysql_free_result($Conteudo);
?>
