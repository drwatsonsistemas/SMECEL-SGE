<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>
<?php include('../../sistema/funcoes/url_limpa.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('America/Sao_Paulo');

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


$erroUp = "0";

if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	
	$id_resposta 	= $_POST['plano_aula_anexo_atividade_id_atividade'];
	$id_aluno 		= $_POST['plano_aula_anexo_atividade_id_aluno'];
	$novo_nome 		= md5(time())."_".$id_resposta."_".$id_aluno;
	$nova_data		= date('Y-m-d H:i:s');
	
include_once('../../sistema/funcoes/class.upload.php');

$handle = new Upload($_FILES['plano_aula_anexo_atividade_caminho']);

if ($handle->uploaded) 
{ 

$handle->file_new_name_body = $novo_nome;
$handle->mime_check = true;
$handle->allowed = array('application/pdf','application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*');
$handle->file_max_size = '20242880'; // 1KB
$handle->Process('../../anexos_respostas/'.$id_resposta.'/');

if ($handle->processed) 
{

$nome_do_arquivo = $handle->file_dst_name;

  $insertSQL = sprintf("INSERT INTO smc_plano_aula_anexo_atividade (plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_data_hora) VALUES ('$id_aluno', '$id_resposta', '$nome_do_arquivo', '$nova_data')",
                       GetSQLValueString($_POST['plano_aula_anexo_atividade_id_atividade'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  $insertGoTo = "aulas_conteudo.php?";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
    $insertGoTo .= "&anexoEnviado";
  }
  header(sprintf("Location: %s", $insertGoTo));
} 
else 
{
	
	$erroUp = "1";
	$erroValor = $handle->error;

}
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

if ($row_Matricula['vinculo_aluno_situacao']<>"1") { 
  header("Location:index.php");
}

$colname_Conteudo = "-1";
if (isset($_GET['aula'])) {
  $colname_Conteudo = $_GET['aula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, 
plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_meet, plano_aula_sicrona_hora, plano_aula_sicrona_minuto,
plano_aula_google_form, plano_aula_google_form_tempo, 
plano_aula_hash, func_id, func_nome, func_foto, disciplina_id, disciplina_nome 
FROM smc_plano_aula 
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_hash = %s", GetSQLValueString($colname_Conteudo, "text"));
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo);



if ($totalRows_Conteudo == 0) {
	$insertGoTo = "index.php?erro";
	header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Anexos = "SELECT plano_aula_anexo_id, plano_aula_anexo_id_atividade, plano_aula_anexo_arquivo, plano_aula_anexo_descricao FROM smc_plano_aula_anexo WHERE plano_aula_anexo_id_atividade = '$row_Conteudo[plano_aula_id]'";
$Anexos = mysql_query($query_Anexos, $SmecelNovo) or die(mysql_error());
$row_Anexos = mysql_fetch_assoc($Anexos);
$totalRows_Anexos = mysql_num_rows($Anexos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Comentarios = "
SELECT com_at_aluno_id, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_duvida, com_at_aluno_data_hora, 
com_at_aluno_comentario, com_at_aluno_comentario_professor, com_at_aluno_comentario_professor_data, vinculo_aluno_id, vinculo_aluno_id_aluno, aluno_id, aluno_nome, aluno_foto 
FROM smc_coment_ativ_aluno 
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = com_at_aluno_id_matricula
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE com_at_aluno_id_atividade = '$row_Conteudo[plano_aula_id]'
ORDER BY com_at_aluno_id ASC";
$Comentarios = mysql_query($query_Comentarios, $SmecelNovo) or die(mysql_error());
$row_Comentarios = mysql_fetch_assoc($Comentarios);
$totalRows_Comentarios = mysql_num_rows($Comentarios);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AtividadeResposta = "
SELECT plano_aula_anexo_atividade_id, plano_aula_anexo_atividade_id_aluno, plano_aula_anexo_atividade_id_atividade, 
plano_aula_anexo_atividade_caminho, plano_aula_anexo_atividade_texto, plano_aula_anexo_atividade_data_hora, plano_aula_anexo_atividade_resposta_professor, plano_aula_anexo_atividade_visualizada_professor, plano_aula_anexo_atividade_visualizada_aluno 
FROM smc_plano_aula_anexo_atividade
WHERE plano_aula_anexo_atividade_id_aluno = '$row_Matricula[vinculo_aluno_id]' AND plano_aula_anexo_atividade_id_atividade = '$row_Conteudo[plano_aula_id]'";
$AtividadeResposta = mysql_query($query_AtividadeResposta, $SmecelNovo) or die(mysql_error());
$row_AtividadeResposta = mysql_fetch_assoc($AtividadeResposta);
$totalRows_AtividadeResposta = mysql_num_rows($AtividadeResposta);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO smc_coment_ativ_aluno (com_at_aluno_duvida, com_at_aluno_id_atividade, com_at_aluno_id_matricula, com_at_aluno_data_hora, com_at_aluno_comentario) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString(isset($_POST['com_at_aluno_duvida']) ? "true" : "", "defined","'S'","'N'"),
                       GetSQLValueString($_POST['com_at_aluno_id_atividade'], "int"),
                       GetSQLValueString($_POST['com_at_aluno_id_matricula'], "int"),
                       GetSQLValueString($_POST['com_at_aluno_data_hora'], "date"),
                       GetSQLValueString(anti_injection($_POST['com_at_aluno_comentario']), "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

  $insertGoTo = "aulas_conteudo.php?aula=$row_Conteudo[plano_aula_hash]&comentou#rodape";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
	//$insertGoTo .= "#rodape";
  //}
  header(sprintf("Location: %s", $insertGoTo));
}


if ((isset($_GET['deletar'])) && ($_GET['deletar'] != "")) {
  $deleteSQL = sprintf("DELETE FROM smc_coment_ativ_aluno WHERE com_at_aluno_id_matricula = '$row_Matricula[vinculo_aluno_id]' AND com_at_aluno_id=%s",
                       GetSQLValueString($_GET['deletar'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "aulas_conteudo.php?aula=$row_Conteudo[plano_aula_hash]&deletado#rodape";
  
  //if (isset($_SERVER['QUERY_STRING'])) {
  //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
  //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  
  header(sprintf("Location: %s", $deleteGoTo));
}


if ((isset($_GET['deletarAtividade'])) && ($_GET['deletarAtividade'] != "")) {
		
  $deleteSQL = sprintf("DELETE FROM smc_plano_aula_anexo_atividade WHERE plano_aula_anexo_atividade_id_aluno = '$row_Matricula[vinculo_aluno_id]' AND plano_aula_anexo_atividade_id=%s",
                       GetSQLValueString($_GET['deletarAtividade'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

  $deleteGoTo = "aulas_conteudo.php?aula=$row_Conteudo[plano_aula_hash]&deletadoAtividade#respostas";
  
  //if (isset($_SERVER['QUERY_STRING'])) {
  //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
  //$deleteGoTo .= $_SERVER['QUERY_STRING'];
  //}
  
  header(sprintf("Location: %s", $deleteGoTo));
}

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


  $aula = $row_Conteudo['plano_aula_id'];
  $matricula = $row_Matricula['vinculo_aluno_id'];
  $data = date("Y-m-d H:i:s");
  $cookieAula = $aula."-".$matricula;

if ((isset($_POST["MM_insert3"])) && ($_POST["MM_insert3"] == "form3")) {  
  $sql = "INSERT INTO smc_visualiza_aula (visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora) VALUES ('$aula', '$matricula', '$data')";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  setcookie($cookieAula, $cookieAula, (time()+3600));
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Registrar = "
SELECT visualiza_aula_id, visualiza_aula_id_aula, visualiza_aula_id_matricula, visualiza_aula_data_hora 
FROM smc_visualiza_aula
WHERE visualiza_aula_id_aula = '$aula' AND visualiza_aula_id_matricula = '$matricula'";
$Registrar = mysql_query($query_Registrar, $SmecelNovo) or die(mysql_error());
$row_Registrar = mysql_fetch_assoc($Registrar);
$totalRows_Registrar = mysql_num_rows($Registrar);



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VisualizarAtividade = "
SELECT plano_aula_google_form_id, plano_aula_google_form_id_aluno, 
plano_aula_google_form_id_atividade, plano_aula_google_form_data_hora 
FROM smc_plano_aula_google_form
WHERE plano_aula_google_form_id_aluno = '$row_AlunoLogado[aluno_id]' AND
plano_aula_google_form_id_atividade = '$row_Conteudo[plano_aula_id]'
";
$VisualizarAtividade = mysql_query($query_VisualizarAtividade, $SmecelNovo) or die(mysql_error());
$row_VisualizarAtividade = mysql_fetch_assoc($VisualizarAtividade);
$totalRows_VisualizarAtividade = mysql_num_rows($VisualizarAtividade);  

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VideoAula = "SELECT * FROM smc_videoaula WHERE videoaula_id_aula = '$row_Conteudo[plano_aula_id]'";
$VideoAula = mysql_query($query_VideoAula, $SmecelNovo) or die(mysql_error());
$row_VideoAula = mysql_fetch_assoc($VideoAula);
$totalRows_VideoAula = mysql_num_rows($VideoAula);





if ($row_Conteudo['plano_aula_google_form_tempo'] == "" || $row_Conteudo['plano_aula_google_form_tempo'] == 0) {
  $tempoProva = 9999;
} else {
$tempoProva = $row_Conteudo['plano_aula_google_form_tempo'];
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
<title><?php echo $row_AlunoLogado['aluno_nome']; ?>-<?php echo $row_Matricula['turma_nome']; ?>-<?php echo $row_Matricula['turma_turno_nome']; ?>-<?php echo $row_Matricula['escola_nome']; ?></title>
<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!--Import materialize.css-->
<link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
<link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link href="../lib/css/emoji.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
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
        <small style="font-size:14px;"> <?php echo current( str_word_count($row_AlunoLogado['aluno_nome'],2)); ?>
        <?php $word = explode(" ", trim($row_AlunoLogado['aluno_nome'])); echo $word[count($word)-1]; ?>
        </small> </p>
      <?php include "menu_esq.php"; ?>
    </div>
    <div class="col s12 m10">
      <h5><strong>Conteúdo de apoio</strong></h5>
      <hr>
      <a href="index.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>VOLTAR</a> <a href="aulasNovas.php" class="waves-effect waves-light purple btn-small btn"><i class="material-icons left">new_releases</i>NOVAS</a> <a href="aulas.php" class="waves-effect waves-light btn-small btn"><i class="material-icons left">alarm_on</i>HOJE</a> <a href="aulasTodas.php" class="waves-effect waves-light orange btn-small btn"><i class="material-icons left">all_inclusive</i>TODAS</a> <a href="disciplinas.php" class="waves-effect waves-light btn-small btn green lighten-1"><i class="material-icons left">apps</i>DISCIPLINAS</a> <a href="aulas_conteudo_imprimir.php?aula=<?php echo $colname_Conteudo; ?>" target="_blank" class="waves-effect waves-light btn-small btn teal"><i class="material-icons left">print</i>IMPRIMIR</a>
      <hr>
      <div class="center">
        <?php if ($row_Conteudo['func_foto']=="") { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100px" class="hoverable prof1">
        <?php } else { ?>
        <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_Conteudo['func_foto']; ?>" width="100px" class="hoverable prof1">
        <?php } ?>
        <br>
        <?php echo $row_Conteudo['func_nome']; ?><br>
        <small><?php echo $row_Conteudo['disciplina_nome']; ?></small> </div>
      <blockquote>
        <p><small><?php echo inverteData($row_Conteudo['plano_aula_data']); ?> - AULA <?php echo $row_Conteudo['plano_aula_id']; ?></small></p>
        <p><?php echo $row_Conteudo['plano_aula_texto']; ?></p>
        <?php $totalPalavras = str_word_count($row_Conteudo['plano_aula_conteudo']); $totalMinutos = ceil($totalPalavras/30); ?>
        <?php if  ($totalMinutos > 1) { ?>
        <p>Tempo médio de leitura: <strong>
          <?php $totalPalavras = str_word_count($row_Conteudo['plano_aula_conteudo']); echo ceil($totalPalavras/50); ?>
          minutos.</strong> (Sem considerar anexos, vídeo e atividades)</p>
        <?php } ?>
      </blockquote>
      <hr>
      <div class="leitura">
        <?php if ($row_Conteudo['plano_aula_conteudo']<>"") { ?>
        <p class="flow-text">
        <div class="card-panel grey lighten-5"> <?php echo $row_Conteudo['plano_aula_conteudo']; ?> </div>
        </p>
        <?php } ?>
        <?php if ($row_Conteudo['plano_aula_meet']=="S") { ?>
        <div class="card-panel deep-purple lighten-5">
          <h5 class="center">AULA AO VIVO</h5>
          O link para a aula estará disponível às <b><?php echo $row_Conteudo['plano_aula_sicrona_hora']; ?>:<?php echo $row_Conteudo['plano_aula_sicrona_minuto']; ?></b>. Atualize essa página após este horário.
          <hr>
          <?php if ((date("H")>=$row_Conteudo['plano_aula_sicrona_hora']) && (date("i")>=$row_Conteudo['plano_aula_sicrona_minuto'])) { ?>
          <a class="btn" href="https://alpha.jitsi.net/<?php echo $row_Conteudo['plano_aula_id']; ?>-<?php echo url_limpa($row_Matricula['escola_nome']); ?>-<?php echo url_limpa($row_Conteudo['disciplina_nome']); ?>-<?php echo url_limpa($row_Matricula['turma_nome']); ?>" target="_blank">ASSISTIR AULA AO VIVO</a>
          <?php } ?>
          <br>
          <p> 
          <small> DICAS:<br>
          <ul>
            <li>-Siga instruções do seu professor e só acesse a aula clicando no botão acima no horário marcado.</li>
            <li>-Após clicar no botão, siga instruções de como baixar o APP ou acesse a aula diretamente pelo navegador (dê permissão para o uso da câmera e microfone).</li>
            <li>-Esteja ciente que poderão ocorrer instabilidades na transmissão.</li>
          </ul>
          </small>
          </p>
        </div>
        <?php } ?>
        <?php if ($totalRows_VideoAula > 0) { ?>
        <div class="card-panel blue lighten-4">
          <h5 class="center">Vídeo aula gravada</h5>
          <?php do { ?>
            <p>
            
            <div class="card-panel1">
              <video width="100%" controls>
                <source src="../../videoaula/<?php echo $row_Conteudo['plano_aula_id_turma']; ?>/<?php echo $row_Conteudo['plano_aula_id']; ?>/<?php echo $row_Conteudo['plano_aula_id_professor']; ?>/<?php echo $row_Conteudo['plano_aula_id_disciplina']; ?>/<?php echo $row_VideoAula['videoaula_nome']; ?>" type="video/mp4">
                Seu navegador não suporta estes arquivos. </video>
              </p>
            </div>
            <?php } while ($row_VideoAula = mysql_fetch_assoc($VideoAula)); ?>
        </div>
        <?php } ?>
        <?php if ($row_Conteudo['plano_aula_video']<>"") { ?>
        <div class="card-panel deep-orange lighten-5">
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
	 
$id = youtube_id_from_url($row_Conteudo['plano_aula_video']);
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
        <?php if ($totalRows_Anexos > 0) { ?>
        <div class="card-panel light-blue lighten-5">
          <h5 class="center">Anexos para estudo</h5>
          <?php do { ?>
            <p><a class="waves-effect waves-light btn-flat" href="<?php echo URL_BASE.'anexos/'.$row_Conteudo['plano_aula_id'] ?>/<?php echo $row_Anexos['plano_aula_anexo_arquivo']; ?>" target="_blank"><i class="material-icons">file_download</i> <?php echo $row_Anexos['plano_aula_anexo_descricao']; ?> (baixar)</a></p>
            <?php } while ($row_Anexos = mysql_fetch_assoc($Anexos)); ?>
        </div>
        <?php } ?>
        <?php if ($row_Conteudo['plano_aula_atividade']<>"") { ?>
        <div class="card-panel light-green lighten-5">
          <h5 class="center">Atividade proposta</h5>
          <p class="flow-text"><?php echo $row_Conteudo['plano_aula_atividade']; ?></p>
          
          <?php if ($row_Conteudo['plano_aula_atividade_resposta_obrigatoria']=="S") { ?>
          <hr>
          <div class="card-panel">
            <h5 class="center">Envie a atividade respondida até o dia <?php echo inverteData($row_Conteudo['plano_aula_atividade_resposta_obrigatoria_data_limite']); ?></h5>
            <blockquote> <strong>Instruções:</strong> Clique no botão abaixo para enviar a foto da sua atividade já respondida.<br>
              Se estiver utilizando um celular, ao clicar no botão você poderá utlizar a câmera para tirar uma foto, ou escolher uma do álbum no seu aparelho.<br>
              Tente tirar a foto em um ambiente iluminado e que mostre as informações com clareza. </blockquote>
            <?php if (($row_Conteudo['plano_aula_atividade_resposta_obrigatoria_data_limite'] >= date("Y-m-d")) || ($row_Conteudo['plano_aula_atividade_resposta_obrigatoria_data_limite'] == "") ) { ?>
            <div class="row">
              <form id="upload_form" enctype="multipart/form-data" method="post" class="col s12">
              
              <textarea name="texto" id="texto" placeholder="Escreva sua resposta ou envie uma foto da atividade" class="materialize-textarea" rows="5"></textarea>
              
                <div class="file-field input-field">
                  <div class="btn-small"> <span>ATIVIDADE</span>
                    <input type="file" name="atividade" id="atividade">
                  </div>
                  <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Selecione a atividade">
                  </div>
                </div>
                                <input type="hidden" name="aula" id="aula" value="<?php echo $row_Plano['plano_aula_id']; ?>">
                <input type="hidden" name="professor" value="<?php echo $row_ProfLogado['func_id']; ?>">
                <input type="hidden" name="componente" value="<?php echo $row_Disciplina['disciplina_id']; ?>">
                <input type="hidden" name="turma" value="<?php echo $row_Turma['turma_id']; ?>">
                <input type="button" value="ENVIAR" id="btnAtividade" onclick="uploadFile()" class="btn">
                <progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
                <blockquote class=" red lighten-5">
                  <h5 id="status"></h5>
                  <small id="loaded_n_total"></small>
                  <hr>
                </blockquote>
              </form>
            </div>
            <?php } else { ?>
            <p><span class="waves-effect waves-light btn disabled">ENVIO BLOQUEADO</span></p>
            <i>Prazo para envio da atividade expirou em <?php echo inverteData($row_Conteudo['plano_aula_atividade_resposta_obrigatoria_data_limite']); ?>. <br>
            Entre em contato com seu professor.</i>
            <?php } ?>
            
            
            
            <?php if ($totalRows_AtividadeResposta > 0) { // Show if recordset not empty ?>
              <h6  id="respostas">Seus arquivos enviados</h6>
              <?php do { ?>
                <p>
                
                <?php if ( $row_AtividadeResposta['plano_aula_anexo_atividade_caminho']<>"") { ?> 
                <a href="<?php echo URL_BASE.'anexos_respostas/'.$row_Conteudo['plano_aula_id'] ?>/<?php echo $row_AtividadeResposta['plano_aula_anexo_atividade_caminho']; ?>" target="_blank">
                <i class="material-icons tiny">file_download</i> 
                Arquivo enviado em <?php echo date('d/m/Y \à\s H\hi', strtotime($row_AtividadeResposta['plano_aula_anexo_atividade_data_hora'])); ?></a>
                <?php } ?>
                
                <?php if ( $row_AtividadeResposta['plano_aula_anexo_atividade_texto']<>"") { ?>
                <i class="material-icons tiny">text_fields</i> 
                Texto enviado em <?php echo date('d/m/Y \à\s H\hi', strtotime($row_AtividadeResposta['plano_aula_anexo_atividade_data_hora'])); ?><br>
                <i><?php echo $row_AtividadeResposta['plano_aula_anexo_atividade_texto']; ?></i>
                <?php } ?>
                 
                  <?php if ($row_AtividadeResposta['plano_aula_anexo_atividade_visualizada_professor']=="N") { ?>
                  <br><span class="orange lighten-2"><small>AGUARDANDO VISUALIZAÇÃO DO PROFESSOR</small></span>
                  <?php } else { ?>
                  <br><span class="green lighten-2"><small><?php echo $row_AtividadeResposta['plano_aula_anexo_atividade_resposta_professor']; ?></small></span>
                  <?php } ?>
                  
                  <a class="right" href="javascript:func()" onclick="confirmaExclusaoAtividade('aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>&deletarAtividade=<?php echo $row_AtividadeResposta['plano_aula_anexo_atividade_id']; ?>')"><i class="material-icons grey-text lighten-3">delete_forever</i></a>
                
                <hr>
                </p>
                <?php } while ($row_AtividadeResposta = mysql_fetch_assoc($AtividadeResposta)); ?>
              <?php } // Show if recordset not empty ?>
            <?php } // Show if recordset not empty ?>
          </div>
        
        <?php } ?>
        
        
        
        <?php if ($row_Conteudo['plano_aula_google_form']<>"") { ?>
        <br>
        <div class="card-panel light-green lighten-5">
          <h5 class="center"><i class="material-icons brown-text">class</i> AVALIAÇÃO  ON-LINE</h5>
          <blockquote>
            <p><strong>INSTRUÇÕES</strong></p>
            <li>Ao clicar no botão abaixo, uma nova página se abrirá para você responder a avaliação online.</li>
            <li><strong>Não feche ou não volte</strong> a página antes de ter respondido o formulário.</li>
            <li>A avaliação só poderá ser respondida uma vez, sendo validado apenas o primeiro envio.</li>
            <li>Você terá exatamente <strong><?php echo $tempoProva; ?> minutos</strong> para concluir essa avaliação.</li>
            <?php if ($totalRows_VisualizarAtividade==0) { ?>
            <p><a href="avaliacao.php?aula=<?php echo $colname_Conteudo; ?>" class="waves-effect waves-light btn">RESPONDER AVALIAÇÃO</a></p>
            <?php } else { ?>
            <p><a href="" class="waves-effect waves-light btn disabled">AVALIAÇÃO ACESSADA EM <?php echo date('d/m/Y À\S H\hi', strtotime($row_VisualizarAtividade['plano_aula_google_form_data_hora'])); ?></a></p>
            <?php } ?>
          </blockquote>
          <?php //echo $row_Conteudo['plano_aula_google_form']; ?>
        </div>
        <?php } ?>
      </div>
      <?php if ($totalRows_Registrar < 1) { ?>
      <form method="post" name="form3" action="" autocomplete="off" class="card-panel center">
        <div class="file-field input-field col s12"> Se você considera que já concluiu todas as atividades propostas nesta aula, clique no botão abaixo. </div>
        <input type="submit" value="MARCAR AULA COMO CONCLUIDA" class="waves-effect waves-light btn green">
        <input type="hidden" name="MM_insert3" value="form3">
        <input type="hidden" name="plano_aula_anexo_atividade_id_aluno" value="<?php echo $row_Matricula['vinculo_aluno_id']; ?>">
      </form>
      <?php } else { ?>
      <div class="card-panel white-text green"> Aula marcada como concluida. Parabéns! <i class="material-icons left">check_circle</i> </div>
      <?php } ?>
      <br>
      <h5>Comentários (<?php echo $totalRows_Comentarios; ?>):</h5>
      <?php if ($totalRows_Comentarios > 0) { ?>
      <br>
      <table class="striped1 highlight">
        <?php do { ?>
          <tr>
            <td class="top" valign="top" style="vertical-align: top;" width="60"><?php if ($row_Comentarios['aluno_foto']=="") { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable aluno">
              <?php } else { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Comentarios['aluno_foto']; ?>" width="100%" class="hoverable aluno">
              <?php } ?></td>
            <td valign="top" style="vertical-align: top;"><small style="font-size:10px;"> <a href="#"><?php echo current( str_word_count($row_Comentarios['aluno_nome'],2)); ?>
              <?php $word = explode(" ", trim($row_Comentarios['aluno_nome'])); echo $word[count($word)-1]; ?>
              </a>
              <?php if ($row_Comentarios['com_at_aluno_duvida']=="S") { ?>
              <span class="orange"> Dúvida </span>
              <?php } ?>
              <span class="right"><?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_data_hora'])); ?></span> </small>
              <p><?php echo nl2br(processtext($row_Comentarios['com_at_aluno_comentario'])); ?></p>
              <?php if ($row_Comentarios['com_at_aluno_comentario_professor']<>"") { ?>
              <p>
              
              <div style="display: block; padding: 5px 10px; border-left:1px solid #066; margin-left:20px; background-color:#FAFAFA;" class="blue1 lighten-5">
                <p style="display: block; float:left; width:40px; height:80px; margin-right:10px;">
                  <?php if ($row_Conteudo['func_foto']=="") { ?>
                  <img src="<?php echo URL_BASE.'professor/fotos/' ?>semfoto.jpg" width="100%" class="hoverable prof">
                  <?php } else { ?>
                  <img src="<?php echo URL_BASE.'professor/fotos/' ?><?php echo $row_Conteudo['func_foto']; ?>" width="100%" class="hoverable prof">
                  <?php } ?>
                </p>
                <p><small class="right"><?php echo date('H\hi - d/m/Y', strtotime($row_Comentarios['com_at_aluno_comentario_professor_data'])); ?></small></p>
                <strong>Professor(a) <?php echo current( str_word_count($row_Conteudo['func_nome'],2)); ?>
                <?php $word = explode(" ", trim($row_Conteudo['func_nome'])); echo $word[count($word)-1]; ?>
                respondeu:</strong>
                <p><?php echo nl2br(processtext($row_Comentarios['com_at_aluno_comentario_professor'])); ?></p>
              </div>
              </p>
              <?php } ?></td>
            <td width="20"><?php if ($row_Comentarios['com_at_aluno_id_matricula']==$row_Matricula['vinculo_aluno_id']) { ?>
              <a href="javascript:func()" onclick="confirmaExclusao('aula=<?php echo $row_Conteudo['plano_aula_hash']; ?>&deletar=<?php echo $row_Comentarios['com_at_aluno_id']; ?>')"><i class="material-icons grey-text lighten-3">delete_forever</i></a>
              <?php } ?></td>
          </tr>
          <?php } while ($row_Comentarios = mysql_fetch_assoc($Comentarios)); ?>
      </table>
      <?php } ?>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="grey lighten-4" id="rodape">
        <table>
          <tr>
            <td width="70" class="top" valign="top" style="vertical-align: top;"><?php if ($row_AlunoLogado['aluno_foto']=="") { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?>semfoto.jpg" width="100%" class="hoverable">
              <?php } else { ?>
              <img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_AlunoLogado['aluno_foto']; ?>" width="100%" class="hoverable">
              <?php } ?></td>
            <td class="top" valign="top" style="vertical-align: top;"><div class="lead emoji-picker-container">
                <textarea id="conteudoDuvida" style="min-height:80px;" name="com_at_aluno_comentario" class="white form-control textarea-control materialize-textarea" data-emojiable="true"  data-length="255" id="textarea1" required>
                </textarea>
                <label for="textarea1">
                  <?php if ($totalRows_Comentarios == 0) { ?>
                  Seja o primeiro a comentar o assunto acima <br>
                  <span class="caracteres">255</span> caracteres restantes <br>
                  <?php } else { ?>
                  Escreva um comentário sobre o assunto <br>
                  <span class="caracteres">255</span> caracteres restantes<br>
                  <?php } ?>
                </label>
              </div>
              <p>
                <label class="black-text">
                  <input type="checkbox" name="com_at_aluno_duvida">
                  <span>Marque esta opção se seu comentário é uma dúvida que precisa ser respondida pelo professor</span> </label>
              </p>
              <p>
                <button type="submit" value="POSTAR" class="btn waves-effect waves-light white-text" id="btnDuvida" onclick="return checkSubmission();">ENVIAR</button>
                <button id="enviandoDuvida" class="btn" style="display:none" disabled>Enviando...</button>
              </p></td>
          </tr>
        </table>
        <input type="hidden" name="com_at_aluno_id_atividade" value="<?php echo $row_Conteudo['plano_aula_id']; ?>">
        <input type="hidden" name="com_at_aluno_id_matricula" value="<?php echo $row_Matricula['vinculo_aluno_id']; ?>">
        <input type="hidden" name="com_at_aluno_data_hora" value="<?php echo date('Y-m-d H:i:s'); ?>">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
      <p>&nbsp;</p>
      <a href="aulas.php" class="waves-effect waves-light btn-small btn-flat"><i class="material-icons left">arrow_back</i>VOLTAR</a>
      <div class="card-panel"> <span class="blue-text text-darken-2">Utilize o espaço de comentários com responsabilidade.</span> </div>
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
<script src="../lib/js/config.js"></script> 
<script src="../lib/js/util.js"></script> 
<script src="../lib/js/jquery.emojiarea.js"></script> 
<script src="../lib/js/emoji-picker.js"></script> 
<script>


function _(el){
	return document.getElementById(el);
}
function uploadFile(){
	var file = _("atividade").files[0];
	var aula = document.getElementById("aula").value;
	var texto = document.getElementById("texto").value;
  
  let button = document.querySelector("#btnAtividade");
  button.disabled = true;
 


	// alert(file.name+" | "+file.size+" | "+file.type);
	var formdata = new FormData();
	formdata.append("atividade", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "envio_atividade.php?aula=<?php echo $row_Conteudo['plano_aula_id']; ?>&aluno=<?php echo $row_Matricula['vinculo_aluno_id']; ?>&texto=" + texto);
	ajax.send(formdata);
}  
function progressHandler(event){
	_("loaded_n_total").innerHTML = "Enviado "+(event.loaded/1000000)+" MB de "+(event.total/1000000)+" MB";
	var percent = (event.loaded / event.total) * 100;
	_("progressBar").value = Math.round(percent);
	_("status").innerHTML = Math.round(percent)+"% enviado... <br><small>Não feche a página até o carregamento total</small>";
}
function completeHandler(event){
	_("status").innerHTML = event.target.responseText;
	_("progressBar").value = 0;
	//document.location.reload(true);
	//alert("Processo concluído");
setTimeout(function() {
  document.location.reload(true);
}, 2000); // 3 minutos
}
function errorHandler(event){
	_("status").innerHTML = "Erro no envio";
}
function abortHandler(event){
	_("status").innerHTML = "Envio cancelado";
}
</script> 
<script type="text/javascript">




$(document).ready(function(){

$( "#upload_form" ).submit(function( event ) {
	
	$("#btnAtividade").attr("disabled", true);
	$("#envioAtividade").css("display", "block");
	$("#btnAtividade").val('ENVIANDO ATIVIDADE. AGUARDE...');	
	
});
});

		$(document).ready(function(){
			$('.sidenav').sidenav();
			$('.tabs').tabs();
			$('.modal').modal();			
			$('.dropdown-trigger').dropdown();
			$('.materialboxed').materialbox();
			
		});
	
$(document).on("input keyup", "#textarea1", function () {
    var caracteresRestantes = 255;
    var caracteresDigitados = parseInt($(this).val().length);
    var caracteresRestantes = caracteresRestantes - caracteresDigitados;

    $(".caracteres").text(caracteresRestantes);
});

	</script> 
<script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Deseja realmente excluir seu comentário?");
     	if (resposta == true) {
     	     window.location.href = "aulas_conteudo.php?"+codigo;
    	 }
	}
	</script> 
<script language="Javascript">
	function confirmaExclusaoAtividade(codigo) {
     var resposta = confirm("Deseja realmente excluir essa resposta?");
     	if (resposta == true) {
     	     window.location.href = "aulas_conteudo.php?"+codigo;
    	 }
	}
	</script> 
<script language="Javascript">

$( "#btnDuvida" ).click(function() {
		var comentario = $('#conteudoDuvida').val();
		if (comentario=='') {
			alert("Comentário não pode ser vazio");	
			} else {
			  $(this).css( "display", "none" );
			  $("#enviandoDuvida").css( "display", "block" );
		}
});

</script>
<div vw class="enabled" style="z-index:999">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script> 
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script> 
<script>
      $(function() {
        window.emojiPicker = new EmojiPicker({
          emojiable_selector: '[data-emojiable=true]',
          assetsPath: '../lib/img/',
          popupButtonClasses: 'fa fa-smile-o'
        });
        window.emojiPicker.discover();
      });
    </script>
<?php if (isset($_GET["comentou"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">Comentário realizado com sucesso.</button>'});
</script>
  <?php } ?>
<?php if (isset($_GET["deletado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">Comentário deletado com sucesso.</button>'});
</script>
  <?php } ?>
<?php if (isset($_GET["deletadoAtividade"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">Resposta deletada com sucesso.</button>'});
</script>
  <?php } ?>
<?php if (isset($_GET["anexoEnviado"])) { ?>
  <script>
M.toast({html: '<i class=\"material-icons\">check_circle</i>&nbsp;<button class="btn-flat toast-action">RESPOSTA ENVIADA COM SUCESSO</button>'});
</script>
  <?php } ?>
<?php if ($erroUp == "1") { ?>
<script>
M.toast({html: '<i class=\"material-icons red-text\">block</i>&nbsp;<button class="btn-flat toast-action"><?php echo $erroValor; ?></button>'});
</script>
<?php $erroUp = "0" ?>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($Anexos);

mysql_free_result($Registrar);

mysql_free_result($AtividadeResposta);

mysql_free_result($Comentarios);

mysql_free_result($Conteudo);

mysql_free_result($AlunoLogado);

mysql_free_result($VisualizarAtividade);

?>
