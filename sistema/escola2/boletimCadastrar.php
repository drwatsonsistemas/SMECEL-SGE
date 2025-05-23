<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);


$codTurma = "";
$buscaTurma = "";
$nomeTurma = "TODAS AS TURMAS";
if (isset($_GET['turma'])) {
	
	//if ($_GET['turma'] == "") {
	//echo "TURMA EM BRANCO";	
	//header("Location: turmasAlunosVinculados.php?nada"); 
 	//exit;
	//}
	
  $codTurma = anti_injection($_GET['turma']);
  $codTurma = (int)$codTurma;
  $buscaTurma = " AND vinculo_aluno_id_turma = $codTurma ";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Vinculo = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, aluno_id, aluno_nome, turma_id, turma_nome, turma_turno, turma_id_escola, turma_ano_letivo 
FROM 
smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = $row_EscolaLogada[escola_id] AND vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma
ORDER BY vinculo_aluno_id_turma, aluno_nome
";
$Vinculo = mysql_query($query_Vinculo, $SmecelNovo) or die(mysql_error());
$row_Vinculo = mysql_fetch_assoc($Vinculo);
$totalRows_Vinculo = mysql_num_rows($Vinculo);

if ($totalRows_Vinculo < 1) {
 header("Location: turmasAlunosVinculados.php?nada");
}

if (isset($_GET['turma'])) {
	$nomeTurma = $row_Vinculo['turma_nome'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "
SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, 
turma_total_alunos, turma_ano_letivo 
FROM smc_turma
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY turma_turno, turma_etapa ASC ";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);
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
 
    <h1 class="ls-title-intro ls-ico-home">BOLETIM DO ALUNO</h1>
    <!-- CONTEÚDO -->
    
    <p>
    <div data-ls-module="dropdown" class="ls-dropdown"> <a href="#" class="ls-btn-primary">ESCOLHA A TURMA</a>
      <ul class="ls-dropdown-nav">
        <li><a href="boletimCadastrar.php">TODAS</a></li>
        <?php do { ?>
          <li><a href="boletimCadastrar.php?turma=<?php echo $row_Turmas['turma_id']; ?>"><?php echo $row_Turmas['turma_nome']; ?></a></li>
          <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>
      </ul>
    </div>
    </p>
    
    <h3  class="ls-txt-center">LISTANDO <?php echo $nomeTurma; ?></h3>
    
    <?php if ($totalRows_Vinculo==0) { ?>
    <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum aluno com boletim cadastrado.</div>
    <?php } else { ?>
    <table width="100%" class="ls-table ls-table-striped ls-sm-space">
      <thead>
        <tr>
          <th class="ls-txt-center" width="60"></th>
          <th>Aluno</th>
          <th class="ls-txt-center" width="300">Turma</th>
          <th class="ls-txt-center" width="150">Visualizar/lançar</th>
        </tr>
      </thead>
      <tbody>
        <?php 
		  $num = 1;
		  do { ?>
          <tr>
            <td class="ls-txt-center"><strong><?php echo $num; $num++; ?></strong></td>
            <td><?php echo $row_Vinculo['aluno_nome']; ?></td>
            <td class="ls-txt-center"><?php echo $row_Vinculo['turma_nome']; ?></td>
            <td class="ls-txt-center"><?php if ($row_Vinculo['vinculo_aluno_boletim']==0) { ?>
              <a href="boletimCadastrarDisciplinasLista.php?c=<?php echo $row_Vinculo['vinculo_aluno_hash']; ?>" class="ls-ico-calendar-more ls-color-danger" title="Cadastrar Boletim"></a>
              <?php } ?>
              <?php if ($row_Vinculo['vinculo_aluno_boletim']==1) { ?>
              <a href="boletimVer.php?c=<?php echo $row_Vinculo['vinculo_aluno_hash']; ?>" target="_blank" class="ls-ico-eye" title="Visualizar boletim"></a>
              <?php } ?></td>
          </tr>
          <?php } while ($row_Vinculo = mysql_fetch_assoc($Vinculo)); ?>
      </tbody>
    </table>
    <?php } ?>
    
    <!-- CONTEÚDO --> 
  </div>
</main>
<?php include_once ("menu-dir.php"); ?>

<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
 
</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($Turmas);

mysql_free_result($EscolaLogada);

mysql_free_result($Vinculo);
?>
