<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/anti_injection.php"; ?>
<?php include "fnc/dataLocal.php"; ?>


<?php include "fnc/session.php"; ?>
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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$codTurma = "";
$buscaTurma = "";
if (isset($_GET['ct'])) {
	
	if ($_GET['ct'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
  $codTurma = anti_injection($_GET['ct']);
  $codTurma = (int)$codTurma;
  $buscaTurma = "AND turma_id = $codTurma ";
}

$stCod = "";
$stqry = "";

if (isset($_GET['st'])) {	
  $stCod = anti_injection($_GET['st']);
  $stCod = (int)$stCod;
}

	//$st = "1";
	//$stqry = "AND vinculo_aluno_situacao = $st ";
	if (isset($_GET['st'])) {
	
	if ($_GET['st'] == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}
	
	$st = anti_injection($_GET['st']);
	$st = (int)$st;
	$stqry = "AND vinculo_aluno_situacao = $st ";
	}

			  $nomeFiltro = "Todos";
			  if (isset($_GET['st'])) {
					switch ($_GET['st']) {
							case 1:
						$nomeFiltro = "Matriculados";
								break;
							case 2:
						$nomeFiltro = "Transferidos";
								break;
							case 3:
						$nomeFiltro = "Desistentes";
								break;
							case 4:
						$nomeFiltro = "Falecidos";
								break;
							case 5:
						$nomeFiltro = "Outros";
								break;
							default:
							   echo "Todos";
					}	
			  }
	
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}

function formata_tel($tel){
 //verificando se é celular
 $array_pre_numero = array ("9","8","7");
 // retirando espaços
 $tel = trim($tel);
 // seria melhor cirar uma white list.
 // tratando manualmente
 $tel = str_replace("-", "", $tel);
 $tel = str_replace("(", "", $tel);
 $tel = str_replace(")", "", $tel);
 $tel = str_replace("_", "", $tel);
 $tel = str_replace(" ", "", $tel);
 //---------------------
 $tamanho = strlen($tel);
 // maior
 if($tamanho  > '10'){
  // não faz nada
  $telefone = $tel;
 }
 //igual
 if($tamanho == '10'){
  $verificando_celular = substr($tel, 2, 1);
  if(in_array($verificando_celular, $array_pre_numero)){
  $telefone.= substr($tel, 0, 2);
  $telefone.= "9"; // nono digito
  $telefone.= substr($tel, 2);
  }
  else{
   $telefone = $tel;
  }
 }
 if($tamanho < '10'){
  $telefone = $tel;
 }
 return "55".$telefone;
}


function primeiro_nome($str){
  $nome = explode(" ",$str);
  $primeiro_nome = $nome[0];
  $primeiro_nome = strtolower($primeiro_nome);
  $primeiro_nome = ucfirst($primeiro_nome);

  return $primeiro_nome;
}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
<script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">ALUNOS QUE ACESSARAM</h1>
		<!-- CONTEÚDO -->
		
		
        
        
<?php $totalAlunosEscola = 0; ?>
<?php $totalAcessaram = 0; ?>
        
        <?php do { ?>
        <?php

	
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao,
		aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash,
		aluno_localizacao, aluno_endereco, aluno_numero, aluno_bairro,
		aluno_telefone, aluno_celular, aluno_email, aluno_emergencia_tel1, aluno_emergencia_tel2, aluno_tel_mae, aluno_tel_pai,
		CASE aluno_localizacao
		WHEN 1 THEN 'ZONA URBANA'
		WHEN 2 THEN 'ZONA RURAL'
		END AS aluno_localizacao_nome 
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
        
		
			
			
	        
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		
		<div class="ls-box ls-sm-space">
        
		<?php $contaAlunos = 1; ?>
		<?php $contaAcessaram = 0; ?>
		
		<h5 class="ls-title-5 ls-txt-center"><?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?>  (<?php echo $nomeFiltro; ?>)</h5>

		<table class="ls-sm-space ls-table bordasimples" width="100%" style="font-size:10px;">
		<thead>
			<tr>
				<th class="ls-txt-center" width="35px">Nº</th>
				<th>ALUNO</th>
				<th class="ls-txt-center">CONTATOS</th>
                <th width="50px" class="ls-txt-center"></th>
                <th width="50px" class="ls-txt-center"></th>

			</tr>
			<tbody>
			<?php do { ?>
            
            <?php 
			
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_Acessos = "SELECT login_aluno_id, login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ip, login_aluno_ano FROM smc_login_aluno WHERE login_aluno_id_aluno = '$row_ExibirAlunosVinculados[aluno_id]' AND login_aluno_ano = '$row_AnoLetivo[ano_letivo_ano]'";
			$Acessos = mysql_query($query_Acessos, $SmecelNovo) or die(mysql_error());
			$row_Acessos = mysql_fetch_assoc($Acessos);
			$totalRows_Acessos = mysql_num_rows($Acessos);
			
			
			$dataWhats = inverteData($row_ExibirAlunosVinculados['aluno_nascimento']);
			$codigoWhats = str_pad($row_ExibirAlunosVinculados['aluno_id'], 5, '0', STR_PAD_LEFT);
			$senhaWhats = substr($row_ExibirAlunosVinculados['aluno_hash'],0,5);
			
			?>
            
            <?php //if ($totalRows_Acessos == 0) { ?>
            
				<tr style="border-bottom:black solid 1 px;">
					<td class="ls-txt-center">
					<?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?>                    
                    </td>
					<td><strong><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></strong><br>
                    <?php echo $row_ExibirAlunosVinculados['aluno_endereco']; ?>, 
                    <?php echo $row_ExibirAlunosVinculados['aluno_numero']; ?> - 
                    <?php echo $row_ExibirAlunosVinculados['aluno_bairro']; ?> - 
                    <?php echo $row_ExibirAlunosVinculados['aluno_localizacao_nome']; ?>
                    
                    </td>



					<td class="ls-txt-center">


	<table width="100%">

	
	<?php if ($row_ExibirAlunosVinculados['aluno_telefone'] <> "") { ?>
	<tr>
	<td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></td>
	<td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_telefone']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_telefone']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_telefone']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?>
	
    <?php if ($row_ExibirAlunosVinculados['aluno_celular'] <> "") { ?>
	<tr>
	<td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_celular']; ?></td>
	<td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_celular']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_celular']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_celular']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_celular']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?>
	
    <?php if ($row_ExibirAlunosVinculados['aluno_emergencia_tel1'] <> "") { ?>
	<tr>
	<td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_emergencia_tel1']; ?></td>
	<td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergencia_tel1']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_emergencia_tel1']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergencia_tel1']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergencia_tel1']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?> 
    
	<?php if ($row_ExibirAlunosVinculados['aluno_emergencia_tel2'] <> "") { ?>
	<tr>
	<td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_emergencia_tel2']; ?></td>
	<td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergecia_tel2']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_emergecia_tel2']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergecia_tel2']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_emergencia_tel2']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?>
    
	<?php if ($row_ExibirAlunosVinculados['aluno_tel_pai'] <> "") { ?>
	<tr><td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_tel_pai']; ?></td>
	<td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_pai']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_tel_pai']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_pai']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_pai']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?> 
    
	<?php if ($row_ExibirAlunosVinculados['aluno_tel_mae'] <> "") { ?>
	<tr><td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_tel_mae']; ?></td><td>
	<a class="ls-btn ls-btn-xs ls-ico-bell-o" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_mae']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20como%20vai%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20ainda%20n%C3%A3o%20acessou%20nenhuma%20vez%20o%20seu%20painel%20do%20aluno.%20Quer%20que%20eu%20te%20ajude%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_tel_mae']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-star2" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_mae']); ?>&text=Ol%C3%A1%20<?php echo primeiro_nome($row_ExibirAlunosVinculados['aluno_nome']); ?>%2C%20tudo%20bem%3F%20Sou%20uma%20das%20pessoas%20respons%C3%A1veis%20pela%20escola%20que%20voc%C3%AA%20estuda%20e%20vi%20que%20acessou%20muito%20pouco%20o%20seu%20painel%20do%20aluno.%20Posso%20te%20ajudar%3F" target="_blank"><?php //echo $row_ExibirAlunosVinculados['aluno_telefone']; ?></a> 
	<a class="ls-btn ls-btn-xs ls-ico-multibuckets" href="https://api.whatsapp.com/send?phone=<?php echo formata_tel($row_ExibirAlunosVinculados['aluno_tel_mae']); ?>&text=*DADOS%20DE%20ACESSO%20AO%20PAINEL%20DO%20ALUNO:*%0A%0ANASCIMENTO%3A%20*<?php echo $dataWhats; ?>*%0AC%C3%93DIGO%3A%20*<?php echo $codigoWhats; ?>*%0ASENHA%3A%20*<?php echo $senhaWhats; ?>*%0A%0AAcesse%20o%20site%20www.smecel.com.br%2Faluno%20e%20clique%20no%20link%20%C3%81rea%20do%20Aluno%20para%20informar%20os%20dados%20acima." target="_blank"></a>
	</td>
	</tr>
	<?php } ?> 
    
	<?php if ($row_ExibirAlunosVinculados['aluno_email'] <> "") { ?>
	<tr><td width="120"><?php echo $row_ExibirAlunosVinculados['aluno_email']; ?></td><td></td></tr>
	<?php } ?> 
    

	</table>
                    
                    </td>
                    
                    
                    <td class="ls-txt-center"><?php if ($totalRows_Acessos > 0) { ?> <span class="ls-ico-checkmark-circle ls-ico-right ls-color-success"> </span><?php $totalAcessaram++; //$cont++; ?><?php } else { ?> <span class="1ls-ico-cancel-circle ls-ico-right ls-color-danger"> </span><?php } ?></td>
				    <td class="ls-txt-center"><?php if ($totalRows_Acessos > 0) { ?> <?php echo $totalRows_Acessos; ?> <?php } ?></td>

                    
                </tr>
                
                
                <?php //} ?>
                
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
		</tbody>
		</table>
		 
		<p>Alunos vinculados na turma: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong></p>
		
		</div>
		
		
		<?php } ?>
          
          
         <?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?> 
          
          
          <?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>
          
          
          
          <?php if ($codTurma == "") { ?>
          <div class="ls-box ls-box-gray">
		  <p>Total de alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
          <p><span class="ls-ico-checkmark-circle ls-ico-left ls-color-success"> Acessaram o painel: </span> <strong><?php echo $totalAcessaram; ?> (<?php $percAcessaram = ($totalAcessaram * 100) / $totalAlunosEscola; ?><?php echo number_format($percAcessaram, 1, ',', ' '); ?>%)</strong> </p>
          <p><span class="ls-ico-cancel-circle ls-ico-left ls-color-danger"> Não acessaram: </span> <strong><?php echo $naoAcessaram = $totalAlunosEscola - $totalAcessaram; ?> (<?php $percNaoAcessaram = ($naoAcessaram * 100) / $totalAlunosEscola; ?><?php echo number_format($percNaoAcessaram, 1, ',', ' '); ?>%)</strong> </p>
		  </div>
		  <?php } ?>
		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
