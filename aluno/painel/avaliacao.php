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
$query_Matricula = "SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno FROM smc_vinculo_aluno WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

$colname_Conteudo = "-1";
if (isset($_GET['aula'])) {
  $colname_Conteudo = $_GET['aula'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Conteudo = sprintf("
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, 
plano_aula_texto, plano_aula_conteudo, plano_aula_atividade, plano_aula_atividade_resposta_obrigatoria, 
plano_aula_atividade_resposta_obrigatoria_data_limite, plano_aula_video, plano_aula_google_form, plano_aula_google_form_tempo, 
plano_aula_hash, func_id, func_nome, disciplina_id, disciplina_nome 
FROM smc_plano_aula 
INNER JOIN smc_func ON func_id = plano_aula_id_professor
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
WHERE plano_aula_hash = %s", GetSQLValueString($colname_Conteudo, "text"));
$Conteudo = mysql_query($query_Conteudo, $SmecelNovo) or die(mysql_error());
$row_Conteudo = mysql_fetch_assoc($Conteudo);
$totalRows_Conteudo = mysql_num_rows($Conteudo);



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


if ($totalRows_VisualizarAtividade==0) {
   
  $data = date("Y-m-d H:i:s");	
  $sql = "INSERT INTO smc_plano_aula_google_form (plano_aula_google_form_id_aluno, plano_aula_google_form_id_atividade, plano_aula_google_form_data_hora) VALUES ('$row_AlunoLogado[aluno_id]', '$row_Conteudo[plano_aula_id]', '$data')";
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
	
} 


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
<title><?php echo $row_AlunoLogado['aluno_nome']; ?>- SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
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
iframe {
	display:block; width:100%; border:none; margin:20px 0; padding:0;
}
</style>

</head>
<body class="indigo lighten-5" onload=startCountdown()>

<?php include "menu_top.php"?>

<div class="container">
  <div class="row white" style="margin: 10px 0;">
  

        
    <div class="col s12 m12">
	
	
	
      
 <h5><strong>AVALIAÇÃO DE <?php echo $row_Conteudo['disciplina_nome']; ?></strong></h5>
 <hr>





    
	

<?php if ($totalRows_VisualizarAtividade == 0) {  ?>


		  <blockquote>
		  
		  <p><strong>INSTRUÇÕES</strong></p>
		  
		  <li><strong>Não feche ou não volte</strong> a página antes de ter respondido o formulário.</li>
		  <li>A avaliação só poderá ser respondida uma vez, sendo validado apenas o primeiro envio.</li>		  
		  <li>Você terá exatamente <strong><?php echo $tempoProva; ?> minutos</strong> para concluir essa atividade.</li> 
		  
		  <p><strong>BOA SORTE!</strong></p>
		  

		  </blockquote>
		  
<script language="javascript">


function transforma_magicamente(s){
              
	function duas_casas(numero){
		if (numero <= 9){
			numero = "0"+numero;
        }
		return numero;
	}

    hora = duas_casas(Math.trunc(s/3600));
    minuto = duas_casas(Math.trunc((s%3600)/60));
    segundo = duas_casas((s%3600)%60);
              
    formatado = hora+":"+minuto+":"+segundo;
              
    return formatado;
 }

var g_iCount = new Number(); 
// de 30 a 0 //
var g_iCount = <?php echo $tempoProva*60; ?>; 
function startCountdown(){
       if((g_iCount - 1) >= 0){
               g_iCount = g_iCount - 1;
			   g_tempo = transforma_magicamente(g_iCount);
               //numberCountdown.innerText = '' + g_iCount;
			   numberCountdown.innerText = 'Tempo restante: ' + g_tempo;
               setTimeout('startCountdown()',1000);
			   
			   
			   if((g_iCount) == 300){
				alert("Atenção: Você tem apenas mais 5 minutos para concluir a avaliação!");   
			   }
			   
			   
       } else {
		   alert("O seu tempo se esgotou!");
		   document.location.reload(true);
	   }
}
</script>
<p class="center"><i class="material-icons">access_time</i> <b id="numberCountdown" style="font-size:x-large;border-radius:10px; width:70px; height:70px;margin:auto; padding:5px; background-color:#FFFFFF;"></b></p>
<meta http-equiv="refresh" content="<?php echo $tempoProva*60; ?>;URL=<?php echo "avaliacao.php?aula=".$colname_Conteudo; ?>">

<hr>

<p>	
<?php echo $row_Conteudo['plano_aula_google_form']; ?>
</p> 

<hr>

<p class="center">Antes de clicar no botão abaixo, clique em "Enviar" para enviar o formulário. Após concluir o envio, clique no botão abaixo "fechar" e você voltará para a página anterior.</p>

<p class="center"><a href="javascript:func()" onclick="confirmaExclusao('aulas_conteudo.php?aula=<?php echo $colname_Conteudo; ?>')" class="waves-effect waves-light btn-flat">FECHAR</a></p>

<hr>

<br>
<?php } else { ?>

<blockquote>
<strong class="center"><h4>ATENÇÃO</h4></strong>
<p class="center"><h5  class="center">Essa avaliação já foi visualizada em <?php echo date('d/m/Y à\s H\hi', strtotime($row_VisualizarAtividade['plano_aula_google_form_data_hora'])); ?></h5></p>


<p class="center">Clique em "Fechar" para voltar à página anterior. </p>

<p class="center"><a href="aulas_conteudo.php?aula=<?php echo $colname_Conteudo; ?>" class="waves-effect waves-light btn">FECHAR</a></p>

</blockquote>

<?php } ?>



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
		
		
		
		
$(function () {
    $('.indigo').bind('copy', function (e) {
        e.preventDefault();
        alert('Não é permitido usar a função de copiar');
    });
    $('.indigo').bind('paste', function (e) {
        e.preventDefault();
        alert('Não é permitido usar a função de colar');
    });
    $('.indigo').bind('cut', function (e) {
        e.preventDefault();
        alert('Não é permitido usar a função de recortar');
    });
});

});
		
	</script>

<script language="Javascript">
	function confirmaExclusao(codigo) {
     var resposta = confirm("Não esqueça de clicar no botão de Enviar o formulário. Deseja realmente concluir e voltar?");
     	if (resposta == true) {
     	     window.location.href = codigo;
    	 }
	}
	</script> 
	
</body>
</html>
<?php
mysql_free_result($AlunoLogado);

mysql_free_result($Matricula);

mysql_free_result($VisualizarAtividade);
?>
