<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php include "fnc/session.php"; ?>
<?php
$data = $_COOKIE['data']; // Fulano
$dataBase = $_COOKIE['dataBase']; // Fulano
$diasemana_numero = $_COOKIE['diasemana_numero']; // Fulano
$dia_semana_nome = $_COOKIE['dia_semana_nome']; // Fulano
//initialize the session

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



include "usuLogado.php";
include "fnc/anoLetivo.php";

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
if (isset($_GET['hash'])) {
  $colname_Aluno = $_GET['hash'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aluno = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto, turma_id, turma_nome, turma_turno 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_Aluno, "text"));
$Aluno = mysql_query($query_Aluno, $SmecelNovo) or die(mysql_error());
$row_Aluno = mysql_fetch_assoc($Aluno);
$totalRows_Aluno = mysql_num_rows($Aluno);




$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: turmasFrequencia.php?permissao"));
		break;
	}


$registrodefalta = "faltanaolancada";

   $falta = $_POST['falta'];

   foreach ($falta as $falt=>$value) {

			 //echo "<br>Aula: ".$value.". Codigo disciplina: ";
			 
			 $registrodefalta = "faltalancada";

			  mysql_select_db($database_SmecelNovo, $SmecelNovo);
			  $query_Aulas = "SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola FROM smc_ch_lotacao_professor WHERE ch_lotacao_turma_id = '$row_Aluno[vinculo_aluno_id_turma]' AND ch_lotacao_aula = '$value' AND ch_lotacao_dia = '$diasemana_numero'";
			  $Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
			  $row_Aulas = mysql_fetch_assoc($Aulas);
			  $totalRows_Aulas = mysql_num_rows($Aulas);

			  $disciplina = $row_Aulas['ch_lotacao_disciplina_id'];
			  $matricula = $row_Aluno['vinculo_aluno_id'];



				  $insertSQL = sprintf("INSERT INTO smc_faltas_alunos (faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data) VALUES ('$matricula', '$disciplina', '$value', '$dataBase')");
                       //GetSQLValueString($_POST['faltas_alunos_matricula_id'], "int"),
                       //GetSQLValueString($_POST['faltas_alunos_disciplina_id'], "int"),
                       //GetSQLValueString($_POST['faltas_alunos_numero_aula'], "text"),
                       //GetSQLValueString($_POST['faltas_alunos_data'], "date")));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

        }

  $insertGoTo = "alunosFrequencia.php?turma=" . $row_Aluno['vinculo_aluno_id_turma'] . "&".$registrodefalta;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula,
ch_lotacao_obs, ch_lotacao_escola, disciplina_id, disciplina_nome
FROM smc_ch_lotacao_professor
INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
WHERE ch_lotacao_turma_id = '$row_Aluno[vinculo_aluno_id_turma]' AND ch_lotacao_dia = '$diasemana_numero'
ORDER BY ch_lotacao_aula ASC
";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

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


		<!-- CONTEÚDO -->


<div class="ls-modal" data-modal-blocked id="cadFaltas">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">LANÇAMENTO DE FALTAS</h4>
	  
	</div>
    <div class="ls-modal-body" id="myModalBody">

	<div class="ls-box">

	  <div class="ls-float-right" style="width:50px;">
	  <?php if ($row_Aluno['aluno_foto']=="") { ?>
	 
			<img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" border="0" width="100%">
 
			<?php } else { ?>
			
			<img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Aluno['aluno_foto']; ?>" border="0" width="100%">
			
	<?php } ?>
	</div>


	  <p>
      ALUNO(A): <strong><?php echo $row_Aluno['aluno_nome']; ?></strong><br>
	  TURMA: <strong><?php echo $row_Aluno['turma_nome']; ?></strong><br>
	  TURNO: <strong><?php switch ($row_Aluno['turma_turno']) {
                            case 1:
                              echo "MATUTINO";
                              break;
                            case 2:
                              echo "VESPERTINO";
                              break;
                            case 3:
                              echo "NOTURNO";
                              break;			  
                        }  ?></strong><br>
		DATA: <strong><?php echo $dia_semana_nome; ?>, <?php echo $data; ?></strong>
        
        </p>				
	  
	  
	  
	  
	  
	</div>
	  
	<?php if ($totalRows_Disciplinas > 0) { ?>
	
	<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">


	<fieldset>
    <div class="ls-label col-md-12">
      <p>Selecione as aulas que ocorreram as faltas:</p>


	  <?php do { ?>
      
      <?php
	  
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_checaFalta = "SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada, faltas_alunos_justificativa FROM smc_faltas_alunos WHERE faltas_alunos_matricula_id = '$row_Aluno[vinculo_aluno_id]' AND faltas_alunos_data = '$dataBase' AND faltas_alunos_numero_aula = '$row_Disciplinas[ch_lotacao_aula]'";
		$checaFalta = mysql_query($query_checaFalta, $SmecelNovo) or die(mysql_error());
		$row_checaFalta = mysql_fetch_assoc($checaFalta);
		$totalRows_checaFalta = mysql_num_rows($checaFalta);
	  
	  ?>
      
       <label class="ls-label-text">
        <input type="checkbox" value="<?php echo $row_Disciplinas['ch_lotacao_aula']; ?>" <?php if ($totalRows_checaFalta > 0) { ?> disabled<?php } else { ?> name="falta[]" class="check" <?php } ?>>
       <?php echo $row_Disciplinas['ch_lotacao_aula']; ?>ª - <?php echo $row_Disciplinas['disciplina_nome']; ?>
      </label>

		<?php } while ($row_Disciplinas = mysql_fetch_assoc($Disciplinas)); ?>


		<p>
		<label class="ls-label-text">
		<input type="checkbox" id="checkTodos" name="checkTodos"> SELECIONAR TUDO
		</label>
		</p>
    </div>
  </fieldset>




    </div>
    <div class="ls-modal-footer">
      <button class="ls-btn ls-float-right" data-dismiss="modal">Close</button>
	  <a href="alunosFrequencia.php?turma=<?php echo $row_Aluno['vinculo_aluno_id_turma']; ?>" class="ls-btn ls-float-right">VOLTAR</a>
	  
	  <input type="submit" name="submit" value="LANÇAR FALTAS" class="ls-btn-primary">
      <input type="hidden" name="MM_insert" value="form1">
	  
    </div>
  
  </form>

	<?php mysql_free_result($checaFalta); ?>
	<?php } else { ?>
	
		<div class="ls-alert-info"><strong>Atenção:</strong> Não existem horário de aulas cadastrado para este dia. Preencha as informações corretamente para registrar a frequência deste aluno.</div>
	
        <div class="ls-modal-footer">
        
        <a href="grade.php" class="ls-btn-primary">CADASTRAR HORÁRIOS</a>

        <a href="alunosFrequencia.php?turma=<?php echo $row_Aluno['vinculo_aluno_id_turma']; ?>" class="ls-btn ls-float-right">VOLTAR</a>

		</div>
    
	<?php } ?>
	
  
  </div>
</div><!-- /.modal -->

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	<script>

	// Forma 1
$("#checkTodos").click(function(){
    $('.check').not(this).prop('checked', this.checked);
});

	</script>
	
	<script>
	locastyle.modal.open("#cadFaltas");
	</script>


  </body>
</html>
<?php
mysql_free_result($UsuLogado);


mysql_free_result($EscolaLogada);

mysql_free_result($Aluno);

mysql_free_result($Disciplinas);
?>
