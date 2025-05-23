<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
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
$query_Ano = "SELECT ano_letivo_id, ano_letivo_ano, ano_letivo_aberto, ano_letivo_id_sec FROM smc_ano_letivo WHERE ano_letivo_aberto = 'N' AND ano_letivo_id_sec = '$row_UsuLogado[usu_sec]' ORDER BY ano_letivo_ano DESC";
$Ano = mysql_query($query_Ano, $SmecelNovo) or die(mysql_error());
$row_Ano = mysql_fetch_assoc($Ano);
$totalRows_Ano = mysql_num_rows($Ano);

$anoLetivo = $row_AnoLetivo['ano_letivo_ano'];
if (isset($_GET['ano'])) {
	
  if ($_GET['ano'] == "") {
		//echo "TURMA EM BRANCO";	
    header("Location: turmasAlunosVinculados.php?nada"); 
    exit;
  }

  $anoLetivo = anti_injection($_GET['ano']);
  $anoLetivo = (int)$anoLetivo;
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
$query_ListarTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo, turma_multisseriada 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$anoLetivo' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ListarTurmas = mysql_query($query_ListarTurmas, $SmecelNovo) or die(mysql_error());
$row_ListarTurmas = mysql_fetch_assoc($ListarTurmas);
$totalRows_ListarTurmas = mysql_num_rows($ListarTurmas);

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
$query_ListarTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo, turma_multisseriada 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$anoLetivo' 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ListarTurmas = mysql_query($query_ListarTurmas, $SmecelNovo) or die(mysql_error());
$row_ListarTurmas = mysql_fetch_assoc($ListarTurmas);
$totalRows_ListarTurmas = mysql_num_rows($ListarTurmas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirTurmas = "
SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome, 
turma_total_alunos, turma_ano_letivo, turma_multisseriada 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$anoLetivo' $buscaTurma 
ORDER BY turma_turno, turma_etapa, turma_nome ASC";
$ExibirTurmas = mysql_query($query_ExibirTurmas, $SmecelNovo) or die(mysql_error());
$row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas);
$totalRows_ExibirTurmas = mysql_num_rows($ExibirTurmas);

if ($totalRows_ExibirTurmas == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmasAlunosVinculados.php?nada"); 
  exit;
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <style>

  </style>
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

      <h1 class="ls-title-intro ls-ico-home">FREQUÊNCIA EM ALERTA</h1>

      <div class="ls-box">
        <p>
          Este percentual reflete a presença dos alunos com base no número de aulas cadastradas.
        </p>
      </div>
 

   <div class="ls-box-filter">

    <div data-ls-module="dropdown" class="ls-dropdown">
      <a href="#" class="ls-btn-primary ls-ico-menu"> Turma: <?php if (isset($_GET['ct'])) { echo $row_ExibirTurmas['turma_nome']." - ".$row_ExibirTurmas['turma_turno_nome']; } else { echo "TODAS"; } ?></a>
      <ul class="ls-dropdown-nav">

        <li><a href="vinculoAlunoFrequencia.php">- TODAS -</a></li>
        <?php do { ?>
          <li><a href="vinculoAlunoFrequencia.php?ct=<?php echo $row_ListarTurmas['turma_id']; ?>"><?php echo $row_ListarTurmas['turma_nome']; ?> - <?php echo $row_ListarTurmas['turma_turno_nome']; ?></a></li>
        <?php } while ($row_ListarTurmas = mysql_fetch_assoc($ListarTurmas)); ?>

      </ul>
    </div>

<div class="ls-group-btn ls-group-active ls-float-right">
  <a href="vinculoAlunoFrequencia.php" class="ls-btn">TODOS</a>
  <a href="vinculoAlunoFrequenciaAlerta.php" class="ls-btn ls-active">EM ALERTA</a>
  <a href="vinculoAlunoFrequenciaAbaixo.php" class="ls-btn">ABAIXO</a>
</div>


  </div>

  <?php $totalAlunosEscola = 0; ?>
  <?php $totalAulasTurma = 0; ?>

  <?php do { ?>
    <?php 


    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_ExibirAlunosVinculados = "
    SELECT 
    vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
    vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_multietapa, vinculo_aluno_dependencia, etapa_id, etapa_nome, etapa_nome_abrev,
    aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_sexo, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia
    FROM smc_vinculo_aluno 
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    LEFT JOIN smc_etapa ON etapa_id = vinculo_aluno_multietapa 
    WHERE vinculo_aluno_ano_letivo = '$anoLetivo' AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry AND vinculo_aluno_situacao = '1'
    ORDER BY aluno_nome ASC";
    $ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
    $row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
    $totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);

    ?>

    <div class="ls-box ls-sm-space">

     <h5 class="ls-title-5 ls-txt-center"> <?php echo $row_ExibirTurmas['turma_nome']; ?> (<?php echo $row_ExibirTurmas['turma_id']; ?>) <?php echo $row_ExibirTurmas['turma_turno_nome']; ?> (<?php echo $nomeFiltro; ?>)<?php if (($row_ExibirTurmas['turma_multisseriada']==1)) { ?> <br><b>MULTISSERIADA</b> <?php } ?></h5>


     <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>

      <?php $contaAlunos = 1; ?>

      <table class="ls-table ls-table-striped ls-sm-space fonte-tabela">
        <thead>
         <tr>
          <th class="ls-txt-center" width="40">Nº</th>
          <th class="ls-txt-center  hidden-xs" width="60">MAT</th>
          <th class="ls-txt-center" width="30%">ALUNO</th>
          <th class="ls-txt-center" width="50%">PERCENTUAL DE PRESENÇA</th>
          <th class="ls-txt-center" width="130">SITUAÇÃO</th>
        </tr>
        <tbody>
          <?php 
          do { 
           mysql_select_db($database_SmecelNovo, $SmecelNovo);
           $query_FaltasAulas = "
           SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, 
           faltas_alunos_data, faltas_alunos_justificada 
           FROM smc_faltas_alunos
           WHERE faltas_alunos_matricula_id = '$row_ExibirAlunosVinculados[vinculo_aluno_id]' AND faltas_alunos_justificada = 'N'";
           $FaltasAulas = mysql_query($query_FaltasAulas, $SmecelNovo) or die(mysql_error());
           $row_FaltasAulas = mysql_fetch_assoc($FaltasAulas);
           $totalRows_FaltasAulas = mysql_num_rows($FaltasAulas);

           mysql_select_db($database_SmecelNovo, $SmecelNovo);
           $query_AulasTurma = "
           SELECT * FROM smc_plano_aula WHERE plano_aula_id_turma = '$row_ExibirAlunosVinculados[vinculo_aluno_id_turma]'";
           $AulasTurma = mysql_query($query_AulasTurma, $SmecelNovo) or die(mysql_error());
           $row_AulasTurma = mysql_fetch_assoc($AulasTurma);
           $totalRows_AulasTurma = mysql_num_rows($AulasTurma);

           $percpresenca = 0;
           if($totalRows_AulasTurma > 0){
            $percfrequencia = number_format((($totalRows_FaltasAulas/$totalRows_AulasTurma) * 100),0);
            $percpresenca = 100 - $percfrequencia;

            if ($percpresenca < 0) {
              $percpresenca = 0;
            }
          }

          ?>

        <?php if ($percpresenca >= 75 && $percpresenca < 80) { ?>
          
        

          <tr>
           <td class="ls-txt-center"><?php 
           echo $contaAlunos;
           $contaAlunos++;		
         ?></td>
         <td class="ls-txt-center hidden-xs"><a href="matriculaExibe.php?cmatricula=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>&ano=<?php echo $anoLetivo; ?>"><?php echo str_pad($row_ExibirAlunosVinculados['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></a></td>
         <td> <?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?> <?php if ($row_ExibirAlunosVinculados['vinculo_aluno_dependencia']=="S") { echo "<span class='ls-color-danger'>(DEPENDÊNCIA)</span>"; } ?> 
         <?php if (($row_ExibirTurmas['turma_multisseriada']==1) && ($row_ExibirAlunosVinculados['vinculo_aluno_multietapa']==0)) { ?>
          <br><i class="ls-color-danger">*informe a etapa do aluno na turma multi</i> 
        <?php } else { ?>
          <br><b class="ls-color-success"><?php echo $row_ExibirAlunosVinculados['etapa_nome_abrev']; ?></b> 
        <?php } ?>


      </td>
      <td class="ls-txt-center">
        <div data-ls-module="progressBar" role="progressbar" class="ls-animated " aria-valuenow="<?php echo $percpresenca; ?>"></div> 
      </td>

      <td class="ls-txt-center">
        <?php if ($percpresenca > 80) { ?>
          <span class="ls-tag-success ls-ico-checkmark">Frequente</span>
        <?php } ?>
        <?php if ($percpresenca >= 75 && $percpresenca < 80) { ?>
          <span class="ls-tag-warning ls-ico-info">Em alerta</span>
        <?php } ?>
        <?php if ($percpresenca < 75) { ?>
          <span class="ls-tag-danger ls-ico-cancel-circle ">Abaixo</span>
        <?php } ?>
      </td>


    </tr>

    <?php } ?>  

  <?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
  <?php mysql_free_result($ExibirAlunosVinculados); ?>
</tbody>
</table>
Total de aulas cadastradas nessa turma: <strong><?php echo $totalRows_AulasTurma; ?></strong> | 
Total de alunos vinculados na turma: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong>

<?php } else { ?>

 <p class="ls-txt-center">
   <small><i>Nenhum aluno vinculado na turma.</i></small>
   <span class="ls-float-right"><a href="alunoPesquisar.php" class="ls-btn-primary ls-ico-user-add"> Vincular aluno</a></span>
 </p>

<?php } ?>

</div>  


<?php $totalAlunosEscola = $totalAlunosEscola + $totalRows_ExibirAlunosVinculados; ?> 


<?php } while ($row_ExibirTurmas = mysql_fetch_assoc($ExibirTurmas)); ?>


<?php if ($codTurma == "") { ?>
  <div class="ls-box ls-box-gray">
    <p>Total de alunos vinculados na escola: <strong><?php echo $totalAlunosEscola; ?></strong></p>
  </div>
<?php } ?>


<hr>


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


<script language="Javascript">
	function confirmaExclusao(id) {
   var resposta = confirm("Deseja realmente excluir o vínculo deste aluno?");
   if (resposta == true) {
     window.location.href = "matriculaExcluir.php?hash="+id;
   }
 }
</script>


<script src="../../js/jquery.mask.js"></script> 
<script src="js/mascara.js"></script> 
<script src="js/validarCPF.js"></script> 
<script src="js/maiuscula.js"></script> 
<script src="js/semAcentos.js"></script> 

</body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ListarTurmas);

mysql_free_result($ExibirTurmas);

mysql_free_result9($Ano)

//mysql_free_result($ExibirAlunosVinculados);
?>
