<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


$turma = "-1";
if (isset($_GET['c'])) {
  $turma = $_GET['c'];
} else {
	header("Location: grade.php?nada"); 
 	exit;	
}


$dia = "";
if (isset($_GET['dia'])) {
  $dia = $_GET['dia'];
  
  switch($dia) {
	case 1:
	$diaNome = "SEGUNDA-FEIRA";
	break;
	case 2:
	$diaNome = "TERÇA-FEIRA";
	break;
	case 3:
	$diaNome = "QUARTA-FEIRA";
	break;
	case 4:
	$diaNome = "QUINTA-FEIRA";
	break;
	case 5:
	$diaNome = "SEXTA-FEIRA";
	break;
	default:
	header("Location: grade.php?nada"); 
 	exit;	
}
  
} else {
	header("Location: grade.php?nada"); 
 	exit;
}





$aula = "";
if (isset($_GET['aula'])) {
  $aula = $_GET['aula'];
    switch($aula) {
	case 1:
	$aulaNome = "1º HORÁRIO";
	break;
	case 2:
	$aulaNome = "2º HORÁRIO";
	break;
	case 3:
	$aulaNome = "3º HORÁRIO";
	break;
	case 4:
	$aulaNome = "4º HORÁRIO";
	break;
	case 5:
	$aulaNome = "5º HORÁRIO";
	break;
	case 6:
	$aulaNome = "6º HORÁRIO";
	break;
	case 7:
	$aulaNome = "7º HORÁRIO";
	break;
	case 8:
	$aulaNome = "8º HORÁRIO";
	break;
	case 9:
	$aulaNome = "9º HORÁRIO";
	break;
	case 10:
	$aulaNome = "10º HORÁRIO";
	break;
	default:
	header("Location: grade.php?nada"); 
 	exit;	
}

} else {
	header("Location: grade.php?nada"); 
 	exit;
}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	if ($row_UsuLogado['usu_insert']=="N") {
		header(sprintf("Location: grade.php?permissao"));
		exit;
	}
	
  $insertSQL = sprintf("INSERT INTO smc_ch_lotacao_professor (ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_escola) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ch_lotacao_professor_id'], "int"),
                       GetSQLValueString($_POST['ch_lotacao_disciplina_id'], "int"),
                       GetSQLValueString($_POST['ch_lotacao_turma_id'], "int"),
                       GetSQLValueString($_POST['ch_lotacao_dia'], "int"),
                       GetSQLValueString($_POST['ch_lotacao_aula'], "int"),
                       GetSQLValueString($_POST['ch_lotacao_escola'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
  
  
$usu = $_POST['usu_id'];
$esc = $_POST['escola_id'];
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
'6', 
'($detalhes)', 
'$dat')
";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());
  

  $insertGoTo = "horariosEditar.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = "
SELECT vinculo_id, vinculo_id_escola, vinculo_id_funcionario, vinculo_id_funcao, vinculo_status 
vinculo_carga_horaria, vinculo_data_inicio, vinculo_obs, func_id, func_nome, funcao_id, funcao_docencia 
FROM smc_vinculo 
INNER JOIN smc_func ON func_id = vinculo_id_funcionario
INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = $row_EscolaLogada[escola_id] AND funcao_docencia = 'S' 
AND vinculo_status = 1
ORDER BY func_nome ASC";
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplina = "SELECT disciplina_id, disciplina_nome, disciplina_nome_abrev FROM smc_disciplina ORDER BY disciplina_nome ASC";
$Disciplina = mysql_query($query_Disciplina, $SmecelNovo) or die(mysql_error());
$row_Disciplina = mysql_fetch_assoc($Disciplina);
$totalRows_Disciplina = mysql_num_rows($Disciplina);

$colname_Turma = "-1";
if (isset($_GET['c'])) {
  $colname_Turma = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turma = sprintf("
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_matriz_id, 
turma_ano_letivo FROM smc_turma WHERE turma_id_escola = '$row_EscolaLogada[escola_id]' AND turma_id = %s", GetSQLValueString($colname_Turma, "int"));
$Turma = mysql_query($query_Turma, $SmecelNovo) or die(mysql_error());
$row_Turma = mysql_fetch_assoc($Turma);
$totalRows_Turma = mysql_num_rows($Turma);

	
if ($totalRows_Turma == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: grade.php?nada"); 
 	exit;
	}	

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarDisciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, matriz_disciplina_ch_ano, disciplina_id, disciplina_nome, disciplina_nome_abrev, disciplina_bncc,disciplina_eixo_id, disciplina_eixo_nome,matriz_disciplina_eixo 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id
WHERE matriz_disciplina_id_matriz = '$row_Turma[turma_matriz_id]'
ORDER BY disciplina_nome ASC";
$ListarDisciplinasMatriz = mysql_query($query_ListarDisciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_ListarDisciplinasMatriz = mysql_fetch_assoc($ListarDisciplinasMatriz);
$totalRows_ListarDisciplinasMatriz = mysql_num_rows($ListarDisciplinasMatriz);	

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
    






    <main class="ls-main ">
      <div class="container-fluid">
 
		<!-- CONTEÚDO -->
		

<div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">CADASTRAR HORÁRIO (<?php echo $row_Turma['turma_nome']?>)</h4><br>
	  <h5><?php echo $diaNome ?> - <?php echo $aulaNome ?></h5>
    </div>
    <div class="ls-modal-body" id="myModalBody">



<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row">

		<label class="ls-label col-sm-12">
		<b class="ls-label-text">Profissional</b>
		<div class="ls-custom-select">
		<select name="ch_lotacao_professor_id" class="ls-select" required>
            <option value=""> - </option>
			<?php do { ?>
            <option value="<?php echo $row_Vinculo['vinculo_id_funcionario']?>" ><?php echo $row_Vinculo['func_nome']?></option>
            <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>
          </select>
		  </div>
		  </label>

		<label class="ls-label col-sm-12">
		<b class="ls-label-text">Disciplina</b>
		<div class="ls-custom-select">
          <select name="ch_lotacao_disciplina_id" class="ls-select" required>
		  <option value=""> - </option>
            <?php do {  ?>
            <option value="<?php echo $row_ListarDisciplinasMatriz['disciplina_id']?>" ><?php echo $row_ListarDisciplinasMatriz['disciplina_nome']?><?php if($row_ListarDisciplinasMatriz['disciplina_eixo_id'] != ''){ echo " - (".$row_ListarDisciplinasMatriz['disciplina_eixo_nome'].")"; } ?></option>
            <?php } while ($row_ListarDisciplinasMatriz = mysql_fetch_assoc($ListarDisciplinasMatriz)); ?>
          </select>
		  </div>
		  </label>

              <div class="ls-modal-footer">
    <input type="submit" value="INSERIR HORÁRIO" class="ls-btn ls-btn-primary">
		  <a href="grade.php"  class="ls-btn">CANCELAR</a>  
	  
    </div>


      <input type="hidden" name="ch_lotacao_turma_id" value="<?php echo $turma; ?>">
      <input type="hidden" name="ch_lotacao_dia" value="<?php echo $dia; ?>">
      <input type="hidden" name="ch_lotacao_aula" value="<?php echo $aula; ?>">
      <input type="hidden" name="ch_lotacao_escola" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
      <input type="hidden" name="MM_insert" value="form1">
	  
	      <input type="hidden" name="usu_id" value="<?php echo $row_UsuLogado['usu_id']; ?>">
          <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
          <input type="hidden" name="detalhes" value="TURMA: <?php echo $turma; ?>, DIA: <?php echo $dia; ?>, AULA: <?php echo $aula; ?>">

	  
    </form>



    </div>
  </div>
</div><!-- /.modal -->
		
		
		
		
		
		<!-- CONTEÚDO -->
      </div>
    </main>



    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
 
	<script>
	locastyle.modal.open("#myAwesomeModal");
	</script>
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Vinculo);

mysql_free_result($Disciplina);

mysql_free_result($Turma);
?>
