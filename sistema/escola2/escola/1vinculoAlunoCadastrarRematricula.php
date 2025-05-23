<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php 
//include "fnc/anoLetivo.php"; 
//$anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano']+1;
?>
<?php include('fnc/inverteData.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$hash = md5($_POST['cHash'].time());
	
function generateRandomString($size = 4){
   $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
   $randomString = '';
   for($i = 0; $i < $size; $i = $i+1){
      $randomString .= $chars[mt_rand(0,31)];
   }
   return $randomString;
}
function generateRandomString1($size = 4){
   $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
   $randomString = '';
   for($i = 0; $i < $size; $i = $i+1){
      $randomString .= $chars[mt_rand(0,31)];
   }
   return $randomString;
}
function generateRandomString2($size = 4){
   $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
   $randomString = '';
   for($i = 0; $i < $size; $i = $i+1){
      $randomString .= $chars[mt_rand(0,31)];
   }
   return $randomString;
}
function generateRandomString3($size = 4){
   $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
   $randomString = '';
   for($i = 0; $i < $size; $i = $i+1){
      $randomString .= $chars[mt_rand(0,31)];
   }
   return $randomString;
}

$codVerificacao = generateRandomString().'-'.generateRandomString1().'-'.generateRandomString2().'-'.generateRandomString3();

$ct = $_POST['vinculo_aluno_id_turma']; 
	
  $insertSQL = sprintf("INSERT INTO smc_vinculo_aluno (vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_data, vinculo_aluno_transporte, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_hash, vinculo_aluno_verificacao) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$hash', '$codVerificacao')",
                       GetSQLValueString($_POST['vinculo_aluno_id_aluno'], "int"),
                       GetSQLValueString($_POST['vinculo_aluno_id_turma'], "int"),
                       GetSQLValueString($_POST['vinculo_aluno_id_escola'], "int"),
                       GetSQLValueString($_POST['vinculo_aluno_id_sec'], "int"),
                       GetSQLValueString($_POST['vinculo_aluno_ano_letivo'], "text"),
                       GetSQLValueString(inverteData($_POST['vinculo_aluno_data']), "date"),
                       GetSQLValueString($_POST['vinculo_aluno_transporte'], "text"),
					   GetSQLValueString($_POST['vinculo_aluno_da_casa'], "text"),
					   GetSQLValueString($_POST['vinculo_aluno_historico_transferencia'], "text"),
					   GetSQLValueString($_POST['vinculo_aluno_vacina_atualizada'], "text"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
  
$usu = $_POST['usu_id'];
$esc = $_POST['usu_escola'];
$detalhes = $_POST['detalhes'];
date_default_timezone_set('America/Bahia');
$dat = date('Y-m-d H:i:s');

$sql = "
INSERT INTO smc_registros (
registros_id_escola, 
registros_id_usuario, 
registros_tipo, 
registros_complemento, 
registros_data_hora
) VALUES (
'$esc', 
'$usu', 
'9', 
'($detalhes)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());

  
  
    $insertGoTo = "matriculaExibe.php?cadastrado&cmatricula=$hash";
    //$insertGoTo = "vinculoAlunoExibirTurma.php?cadastrado&ct=$ct";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_UsuLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_UsuLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuLogado, "text"));
$UsuLogado = mysql_query($query_UsuLogado, $SmecelNovo) or die(mysql_error());
$row_UsuLogado = mysql_fetch_assoc($UsuLogado);
$totalRows_UsuLogado = mysql_num_rows($UsuLogado);
include "fnc/anoLetivo.php";
$anoLetivoRematricula = $row_AnoLetivo['ano_letivo_ano']+1;


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Aluno = "-1";
if (isset($_GET['c'])) {
  $colname_Aluno = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aluno = sprintf("SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_hash FROM smc_aluno WHERE aluno_hash = %s", GetSQLValueString($colname_Aluno, "text"));
$Aluno = mysql_query($query_Aluno, $SmecelNovo) or die(mysql_error());
$row_Aluno = mysql_fetch_assoc($Aluno);
$totalRows_Aluno = mysql_num_rows($Aluno);

if ($totalRows_Aluno == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
 	exit;
	}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_VinculosAnteriores = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, 
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO'
WHEN 3 THEN 'DEIXOU DE FREQUENTAR'
WHEN 4 THEN 'FALECIDO'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao, 
vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, turma_id, turma_nome, 
CASE turma_turno
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTNO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno, escola_id, escola_nome 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno
ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma
ON turma_id = vinculo_aluno_id_turma
INNER JOIN smc_escola
ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_id_aluno = '$row_Aluno[aluno_id]' AND vinculo_aluno_ano_letivo = '$anoLetivoRematricula'";
$VinculosAnteriores = mysql_query($query_VinculosAnteriores, $SmecelNovo) or die(mysql_error());
$row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores);
$totalRows_VinculosAnteriores = mysql_num_rows($VinculosAnteriores);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_ano_letivo = '$anoLetivoRematricula' AND turma_id_escola = '$row_EscolaLogada[escola_id]'
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

if ($totalRows_Turmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaCadastrarRematricula.php?nova"); 
 	exit;
	}
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>">
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
	 <link rel="stylesheet" href="../../css/foundation-datepicker.css">
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
 
        <h1 class="ls-title-intro ls-ico-home">Rematricula aluno(a) para o Ano Letivo de <?php echo $anoLetivoRematricula; ?></h1>
		<!-- CONTEÚDO -->
		
		
		<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-form-horizontal row" data-ls-module="form" autocomplete="off">
		
		
		<fieldset>
		
					<div class="ls-box">
							
					<label class="ls-label col-md-12">
						<div class="ls-alert-info"><strong>Aluno(a):</strong> <?php echo $row_Aluno['aluno_nome']?></div>
					</label>

					<label class="ls-label col-md-4"><b class="ls-label-text">DATA DA REMATRÍCULA</b>
						<input type="text" placeholder="INFORME A DATA" name="vinculo_aluno_data" id="data_matricula" value="<?php echo date("d/m/Y"); ?>" class="ls-field-lg data_matricula" required>
					</label>

					<label class="ls-label col-md-4"><b class="ls-label-text">TURMAS DO ANO LETIVO DE <?php echo $anoLetivoRematricula; ?></b>
					<div class="ls-custom-select ls-field-lg">
					<select name="vinculo_aluno_id_turma" class="ls-select" required>
						<option value="" <?php if (!(strcmp(-1, ""))) {echo "SELECTED";} ?>>-</option>
						<?php do { ?>
						<option value="<?php echo $row_Turmas['turma_id']?>" ><?php echo $row_Turmas['turma_nome']?> - <?php if ($row_Turmas['turma_turno']=="1") { echo "MATUTINO"; } else if ($row_Turmas['turma_turno']=="2") { echo "VESPERTINO"; } else { echo "NOTURNO"; }?></option>
						<?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
					</select>
					</div>
					</label>
					
					
			<label class="ls-label col-md-4">
            <b class="ls-label-text">UTILIZARÁ TRANSPORTE ESCOLAR?</b><a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informar apenas o aluno que mora na Zona Rural." data-title="Atenção"></a> <br><br>
            <p><label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_transporte" value="N" checked />
              NÃO </label>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_transporte" value="S" />
              SIM </label></p>
            </label>
					
			</div>		
					
			<label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">ALUNO MATRICULADO É DA ESCOLA (DA CASA) OU DE OUTRA ESCOLA/CIDADE (DE FORA)</b><a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Informar se o aluno para a matrícula é de casa (já estuda na escola) ou de fora (vindo de outra escola no município ou fora dele." data-title="Atenção"></a> <br><br>
            <p><label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_da_casa" value="C" onclick="javascript:da_casa();" checked />
              DA CASA </label>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_da_casa" value="F" onclick="javascript:de_fora();" />
              DE FORA </label></p>
            </label>
					
			<label class="ls-label col-sm-12 ls-box" style="display:none" id="historico">
            <b class="ls-label-text">ALUNO TRANSFERIDO COM HISTÓRICO OU DECLARAÇÃO</b><a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Se o aluno foi trasferido com Declaração, terá 30 dias a contar da data da matrícula para entregar o Histórico" data-title="Atenção"></a> <br><br>
            <p><label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_historico_transferencia" value="H" id="vinculo_aluno_historico_transferencia_h" />
              HISTÓRICO </label>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_historico_transferencia" value="D" id="vinculo_aluno_historico_transferencia_d" />
              DECLARAÇÃO </label></p>
            </label>
					
			<label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">CARTEIRA DE VACINAÇÃO DO ALUNO ESTÁ ATUALIZADA? <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" data-content="Ao marcar SIM, você DECLARA que o aluno ou responsável pelo aluno apresentou o documento que comprove que a Carteira de Vacinação está em dia." data-title="Atenção"></a></b> <br><br>
            <p>
			
			
			<p><label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_vacina_atualizada" value="S" checked onclick="javascript:aceite();"/>
              SIM </label>
            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_vacina_atualizada" value="N" />
              NÃO </label>            <label class="ls-label-text">
              <input type="radio" name="vinculo_aluno_vacina_atualizada" value="I" />
              SEM INFORMAÇÃO </label></p>
			
			
           
<div class="ls-alert-warning ls-dismissable" style="display:none" id="aviso_aceite">
  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
  <strong>Atenção!</strong> Ao marcar SIM, você DECLARA que o aluno ou responsável pelo aluno apresentou o documento que comprove que a Carteira de Vacinação está em dia.
</div>
		   
		   
		   </p>
            </label>

				
					
					
					
					</fieldset>
					  <div class="ls-actions-btn">
					    <input type="submit" value="REMATRICULAR" class="ls-btn-primary ls-btn-lg">
						<a href="vinculoAlunoExibirTurma.php" class="ls-btn-danger ls-btn-lg">Cancelar</a>
					  </div>
					

					

			<input type="hidden" name="cHash" value="<?php echo $row_Aluno['aluno_hash']; ?>">
			<input type="hidden" name="vinculo_aluno_id_aluno" value="<?php echo $row_Aluno['aluno_id']; ?>">
			<input type="hidden" name="vinculo_aluno_id_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
			<input type="hidden" name="vinculo_aluno_id_sec" value="<?php echo $row_EscolaLogada['escola_id_sec']; ?>">
			
			<input type="hidden" name="vinculo_aluno_ano_letivo" value="<?php echo $anoLetivoRematricula; ?>">
			<input type="hidden" name="MM_insert" value="form1">
			
	   	    <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
            <input type="hidden" name="usu_escola" value="<?php echo $row_UsuLogado['usu_escola']; ?>">
			<input type="hidden" name="detalhes" value="<?php echo $row_Aluno['aluno_id']; ?> - <?php echo $row_Aluno['aluno_nome']?>">


		</form>
				
		<?php if ($totalRows_VinculosAnteriores > 0) { ?>
		
		
		
		<div class="ls-alert">
		<strong>Atenção:</strong> 
		Foram encontrados os seguintes vínculos para <?php echo $row_VinculosAnteriores['aluno_nome']; ?> neste ano letivo de <?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?>:		
        
		<table width="100%" class="ls-table ls-table-striped ls-sm-space">
          <thead>
		  <tr>
            <th class="ls-txt-center">ESCOLA</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center">ANO LETIVO</th>
            <th class="ls-txt-center">DATA DA MATRÍCULA</th>
            <th class="ls-txt-center">SITUAÇÃO</td>
          </tr>
		  </thead>
		  <tbody>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['escola_nome']; ?></td>
              <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['turma_nome']; ?> <?php echo $row_VinculosAnteriores['turma_turno']; ?></td>
              <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_ano_letivo']; ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_VinculosAnteriores['vinculo_aluno_data']); ?></td>
              <td class="ls-txt-center"><?php echo $row_VinculosAnteriores['vinculo_aluno_situacao']; ?></td>
            </tr>
            <?php } while ($row_VinculosAnteriores = mysql_fetch_assoc($VinculosAnteriores)); ?>
			</tbody>
		</table>
		
		</div>
				
		<?php } ?>
		
		<p>&nbsp;</p>
		
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
 
	<script src="../../js/jquery.mask.js"></script>
	
	<script src="../../js/foundation-datepicker.js"></script>
    <script src="../../js/foundation-datepicker.pt-br.js"></script>
    
	<script type="text/javascript">
function da_casa()
{
	document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false; //Habilitando
	document.getElementById("historico").style.display = "none"; //Habilitando

}
function de_fora()
{
	document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false; //Habilitando
	document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false; //Habilitando
	document.getElementById("historico").style.display = "block"; //Habilitando
}

function aceite()
{
	document.getElementById("aviso_aceite").style.display = "block"; //Habilitando
}


</script>
	
	<script>
$(function(){
	$('#data_matricula').fdatepicker({
		//initialDate: '02/12/1989',
		format: 'dd/mm/yyyy',
		disableDblClickSelection: true,
		language: 'pt-br',
		leftArrow:'<<',
		rightArrow:'>>',
		closeIcon:'X',
		closeButton: false
	});
});
</script>


<script>

$(document).ready(function(){

  $('.data_matricula').mask('00/00/0000');

});

</script>  
	

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Aluno);

mysql_free_result($VinculosAnteriores);

mysql_free_result($Turmas);
?>
