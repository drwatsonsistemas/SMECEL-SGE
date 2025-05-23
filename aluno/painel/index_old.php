<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php //include('funcoes/logins.php'); ?>
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
turma_id, turma_nome, turma_turno,
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
$query_Colegas = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
 vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
 vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
 vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto 
 FROM smc_vinculo_aluno
 INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
 WHERE vinculo_aluno_id_turma = $row_Matricula[vinculo_aluno_id_turma] 
 ORDER BY RAND() LIMIT 0,9
 ";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa, disciplina_id, disciplina_nome,
CASE faltas_alunos_justificada
WHEN 'S' THEN 'SIM'
WHEN 'N' THEN 'NÃO'
END AS faltas_alunos_justificada_nome 
FROM smc_faltas_alunos
INNER JOIN smc_disciplina ON disciplina_id = faltas_alunos_disciplina_id
WHERE faltas_alunos_matricula_id = '$row_Matricula[vinculo_aluno_id]'
ORDER BY faltas_alunos_data DESC, faltas_alunos_numero_aula ASC
";
$Faltas = mysql_query($query_Faltas, $SmecelNovo) or die(mysql_error());
$row_Faltas = mysql_fetch_assoc($Faltas);
$totalRows_Faltas = mysql_num_rows($Faltas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencias = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
END AS ocorrencia_tipo_nome 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]' AND ocorrencia_id_turma = '$row_Matricula[vinculo_aluno_id_turma]'
ORDER BY ocorrencia_id DESC";
$Ocorrencias = mysql_query($query_Ocorrencias, $SmecelNovo) or die(mysql_error());
$row_Ocorrencias = mysql_fetch_assoc($Ocorrencias);
$totalRows_Ocorrencias = mysql_num_rows($Ocorrencias);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Avisos = "
SELECT aviso_turma_id, aviso_turma_id_turma, aviso_turma_id_escola, aviso_turma_data, DATE_FORMAT(aviso_turma_data, '%d/%m/%Y') AS aviso_turma_data, aviso_turma_hora, aviso_turma_texto, aviso_turma_ano 
FROM smc_aviso_turma 
WHERE aviso_turma_ano = '$row_Matricula[vinculo_aluno_ano_letivo]' AND aviso_turma_id_escola = '$row_Matricula[vinculo_aluno_id_escola]' AND (aviso_turma_id_turma = '0' OR aviso_turma_id_turma = '$row_Matricula[vinculo_aluno_id_turma]')
ORDER BY aviso_turma_id DESC";
$Avisos = mysql_query($query_Avisos, $SmecelNovo) or die(mysql_error());
$row_Avisos = mysql_fetch_assoc($Avisos);
$totalRows_Avisos = mysql_num_rows($Avisos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_NovasAulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_publicado, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_publicado = 'S'
";
$NovasAulas = mysql_query($query_NovasAulas, $SmecelNovo) or die(mysql_error());
$row_NovasAulas = mysql_fetch_assoc($NovasAulas);
$totalRows_NovasAulas = mysql_num_rows($NovasAulas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Logins = "SELECT login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip FROM smc_login_aluno WHERE login_aluno_id_aluno = '$row_Matricula[vinculo_aluno_id_aluno]'";
$Logins = mysql_query($query_Logins, $SmecelNovo) or die(mysql_error());
$row_Logins = mysql_fetch_assoc($Logins);
$totalRows_Logins = mysql_num_rows($Logins);


//CONTAGEM

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_video, plano_aula_publicado, plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND plano_aula_publicado = 'S' AND plano_aula_data <= NOW()
ORDER BY plano_aula_data DESC
";
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo); 

$novasAulas = 0;

do {

		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Visualizou = "SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora FROM smc_visualiza_aula WHERE visualiza_aula_id_aula = '$row_Conteudo[plano_aula_id]' AND visualiza_aula_id_matricula = '$row_Matricula[vinculo_aluno_id]' LIMIT 0,1";
		$Visualizou = mysql_query($query_Visualizou, $SmecelNovo) or die(mysql_error());
		$row_Visualizou = mysql_fetch_assoc($Visualizou);
		$totalRows_Visualizou = mysql_num_rows($Visualizou);

		if ($totalRows_Visualizou > 0) {
			
				$totalRows_Conteudo--;
		
		}





mysql_free_result($Visualizou);	
} while ($row_Conteudo = mysql_fetch_assoc($Conteudo));

//CONTAGEM



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
<title><?php echo $row_AlunoLogado['aluno_nome']; ?> - SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
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

	  <?php if ($totalRows_Logins == 1) { ?>
	  <blockquote class="flow-text green lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny green lighten-2"><i class="tiny material-icons">assistant_photo</i></a> 
	  Este parece ser seu primeiro login. Parabéns!
	  </blockquote>
	  <?php } ?>

	  <?php if ($totalRows_Conteudo > 0) { ?>
	  <blockquote class="flow-text red lighten-4" style="padding: 10px;">
	  <a class="btn-floating tiny pulse red lighten-2"><i class="tiny material-icons">add_alert</i></a> 
	  Você tem <strong><?php echo $totalRows_Conteudo; ?></strong> aula(s) pendente(s) <a href="aulas.php">Ver</a>
	  </blockquote>
	  <?php } ?>

  <div class="row white" style="margin: 10px 0;">
  
    <div class="col s12 m2 truncate hide-on-small-only">
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
        
    <div class="col s12 m6">
	
		<div class="row">
		
		<div class="col s4 show-on-small" style="display:none;">
		<p>
		<?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
        <?php } ?>
		</p>
		</div>
		
		<div class="col s8 m12">
		
		  <h5>Bem-vindo(a),
			<?php $nome = explode(" ",$row_AlunoLogado['aluno_nome']); echo $nome[0]; ?>
		  </h5>
		  
		  <blockquote><h6><strong><?php echo $row_Matricula['turma_nome']; ?> - <?php echo $row_Matricula['turma_turno_nome']; ?><br><?php echo $row_Matricula['escola_nome']; ?></strong></h6></blockquote>
		  
		  <hr>
		  
		<div class="row">
      
        <div class="col m2 s4 center">
	      faltas <br><a href="faltas.php" class="waves-effect waves-light btn-floating btn-small light-green lighten-2"><?php echo $totalRows_Faltas; ?></a>
	    </div>
        <div class="col m2 s4 center">
	      avisos <br><a href="avisos.php" class="waves-effect waves-light btn-floating btn-small deep-purple lighten-2"><?php echo $totalRows_Avisos; ?></a>
	    </div>
        <div class="col m2 s4 center">
	      ocorrências <br><a href="ocorrencias.php" class="waves-effect waves-light btn-floating btn-small orange lighten-2"> <?php echo $totalRows_Ocorrencias; ?></a>
	    </div>
        <div class="col m2 s4 center">
	      aulas<br><a href="aulas.php" class="waves-effect waves-light btn-floating btn-small blue lighten-2"> <?php echo $totalRows_NovasAulas; ?></a>
	    </div>
        <div class="col m2 s4 center">
		  acessos<br><a href="#" class="waves-effect waves-light btn-floating btn-small green lighten-2"> <?php echo $totalRows_Logins; ?></a>
        </div>
		
        <div class="col m2 s4 center">
        </div>
		
	  </div>
	  
		  
		</div>
		
		</div>
      	  	  
	  
	  <a href="calendario.php" class="waves-effect waves-light btn"><i class="material-icons left">today</i>CALENDÁRIO</a>
	  <a href="disciplinas.php" class="waves-effect waves-light purple accent-3 btn"><i class="material-icons left">apps</i>DISCIPLINAS</a>
	  
      <table class="striped">
      <thead>
      	<tr>
        	<th width="120"></th>
        	<th></th>
        </tr>
      
      </thead>

        <tbody>
        
        	<tr>
            	<td class="right grey-text text-darken-1">nome completo</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_nome']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">aniversário</td>
            	<td class="black-text"><strong><?php echo inverteData($row_AlunoLogado['aluno_nascimento']); ?></strong></td>
            </tr>

        	<tr>
            	<td class="right grey-text text-darken-1">idade</td>
            	<td class="black-text"><strong><?php echo idade($row_AlunoLogado['aluno_nascimento']); ?></strong></td>
            </tr>

        	<tr>
            	<td class="right grey-text text-darken-1">gênero</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_sexo']; ?></strong></td>
            </tr>
						
        	<tr>
            	<td class="right grey-text text-darken-1">filiação</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_filiacao1']; ?></strong></td>
            </tr>
			
            <?php if ($row_AlunoLogado['aluno_filiacao2']<>"") { ?>
        	<tr>
            	<td class="right grey-text text-darken-1">filiação</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_filiacao2']; ?></strong></td>
            </tr>
            <?php } ?>
            
        	<tr>
            	<td class="right grey-text text-darken-1">naturalidade</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_municipio_nascimento']; ?> (<?php echo $row_AlunoLogado['aluno_uf_nascimento']; ?>)</strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">CEP</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_cep']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">endereço</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_endereco']; ?>, <?php echo $row_AlunoLogado['aluno_numero']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">bairro</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_bairro']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">cidade</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_municipio']; ?> (<?php echo $row_AlunoLogado['aluno_uf']; ?>)</strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">telefone</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_telefone']; ?></strong></td>
            </tr>
			
        	<tr>
            	<td class="right grey-text text-darken-1">celular</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_celular']; ?></strong></td>
            </tr>

        	<?php if ($row_AlunoLogado['aluno_email']<>"") { ?>
            <tr>
            	<td class="right grey-text text-darken-1">e-mail</td>
            	<td class="black-text"><strong><?php echo $row_AlunoLogado['aluno_email']; ?></strong></td>
            </tr>
			<?php } ?>

			
        </tbody>
      </table>
      
      <br>
	  <hr>
      

      
    </div>
    
    <div class="col s12 m4">
	
	
  <div class="card-panel1 center">
  <p>

	<img src="../../img/atendente_edu_post_redes_sociais_painel_aluno.png" width="100%">

	<a href="https://www.instagram.com/smecel.itagimirim/" target="_blank"> <img src="../../img/icone_insta.png" width="40"> </a>
	<a href="https://www.facebook.com/smecel.itagimirim/" target="_blank"> <img src="../../img/icone_face.png" width="40"> </a>
	<a href="http://wa.me/557332892109" target="_blank"> <img src="../../img/icone_whats.png" width="40"> </a>
	
  </p>	
  </div>
	
    
    <h6><strong>Colegas de sala</strong> <small>(<a href="colegas.php">ver todos</a>)</small></h6>
    
		<p>
		<div class="row">
		<?php do { ?>

                
				<div class="col s4 center-align truncate">

				<a href="#">
                

                
				  <?php if ($row_Colegas['aluno_foto']=="") { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable"><br>
                  <?php } else { ?>
                  <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Colegas['aluno_foto']; ?>" width="100%" class="hoverable">
                  <?php } ?>

                 <div style="font-size:10px;">
                  <?php echo current( str_word_count($row_Colegas['aluno_nome'],2)); ?>
                  <?php $word = explode(" ", trim($row_Colegas['aluno_nome'])); echo $word[count($word)-1]; ?>
                 </div><br>
                 
                 </a>
                 
                 </div>
				 
                  
                  
          <?php } while ($row_Colegas = mysql_fetch_assoc($Colegas)); ?>
          </div>
		  </p>
         
          <br>
   
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
	
<?php if (isset($_GET["bemvindo"])) { ?>
	 <!-- Modal Structure -->
  <div id="modalBemVindo" class="modal">
    <div class="modal-content">
      <h4>Olá, <?php $nome = explode(" ",$row_AlunoLogado['aluno_nome']); echo $nome[0]; ?>! <i class="material-icons blue-text darken-2">sentiment_very_satisfied</i></h4>
      <p>Seja bem-vindo(a) novamente!</p>
      <p>Você está no <strong>painel do aluno</strong>. Aqui você terá acesso às suas informações, suas notas, aos horários de aulas e outros avisos importantes. Para outras informações, você deve procurar a secretaria de sua escola.</p>
      <p>Atenciosamente,<br><br>Equipe da Secretaria Municipal de Educação!</p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">FECHAR</a>
    </div>
  </div>
<script type="text/javascript">
$(document).ready(function(){
    $('#modalBemVindo').modal({
		dismissible: false
	});
    $('#modalBemVindo').modal('open');
  });
</script>              
<?php } ?>
	
</body>
</html>
<?php
mysql_free_result($Logins);

mysql_free_result($NovasAulas);

mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);

mysql_free_result($Faltas);

mysql_free_result($Ocorrencias);

mysql_free_result($Avisos);

mysql_free_result($Colegas);

mysql_free_result($Conteudo);

mysql_free_result($Visualizou);
?>
