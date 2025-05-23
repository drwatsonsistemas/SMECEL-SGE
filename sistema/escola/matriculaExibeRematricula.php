<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php 
//include "fnc/anoLetivo.php"; 
?>
<?php include('fnc/inverteData.php'); ?>
<?php include('../funcoes/url_base.php'); ?>


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

$colname_Matricula = "-1";
if (isset($_GET['cmatricula'])) {
  $colname_Matricula = $_GET['cmatricula'];
  $cmatricula = GetSQLValueString($colname_Matricula, "text");
}


if (!isset($_GET['cmatricula'])) {
  header("Location: vinculoAlunoExibirTurma.php?erro");
  exit;
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia,
aluno_id, aluno_nome, aluno_nascimento, aluno_hash, aluno_foto,
turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_hash = $cmatricula AND vinculo_aluno_id_escola = $row_EscolaLogada[escola_id]";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

if ($totalRows_Matricula == 0) { 
header("Location: vinculoAlunoExibirTurma.php?erro");
    exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ocorrencia = "
SELECT ocorrencia_id, ocorrencia_id_aluno, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_ano_letivo, ocorrencia_data, 
ocorrencia_hora, 
CASE ocorrencia_tipo
WHEN 1 THEN 'ADVERTÊNCIA'
WHEN 2 THEN 'SUSPENSÃO'
END AS ocorrencia_tipo, ocorrencia_afastamento_de, ocorrencia_afastamento_ate, ocorrencia_total_dias, ocorrencia_descricao,
COUNT(ocorrencia_tipo) AS ocorrencia_total 
FROM smc_ocorrencia
WHERE ocorrencia_id_aluno = $row_Matricula[vinculo_aluno_id_aluno] AND ocorrencia_ano_letivo = $row_AnoLetivo[ano_letivo_ano]
GROUP BY ocorrencia_tipo";
$Ocorrencia = mysql_query($query_Ocorrencia, $SmecelNovo) or die(mysql_error());
$row_Ocorrencia = mysql_fetch_assoc($Ocorrencia);
$totalRows_Ocorrencia = mysql_num_rows($Ocorrencia);

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
 
        <h1 class="ls-title-intro ls-ico-home">DETALHES DA REMATRÍCULA <?php echo $anoLetivoRematricula; ?></h1>
		<!-- CONTEÚDO -->
		<div class="ls-box">
		<h2>
		<?php echo $row_Matricula['aluno_nome']; ?> - <?php echo $row_Matricula['turma_nome']; ?> <?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?><span class="ls-background-danger">- ALUNO TRANSFERIDO EM <?php echo inverteData($row_Matricula['vinculo_aluno_datatransferencia']); ?></span><?php } ?></h2>
		</div>
		
<div class="row">

<div class="col-md-12">
			<?php if (isset($_GET["erro"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
                </div>
        <?php } ?>
		<?php if (isset($_GET["dadosEditados"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  OS DADOS DO ALUNO FORAM SALVOS COM SUCESSO.
                </div>
        <?php } ?>
		<?php if (isset($_GET["ocorrenciaRegistrada"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  OCORRÊNCIA DO ALUNO REGISTRADO COM SUCESSO.
                </div>
        <?php } ?>
		<?php if (isset($_GET["boletimcadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  BOLETIM CADASTRADO COM SUCESSO.
                </div>
        <?php } ?>
		       <?php if (isset($_GET["vinculoEditado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VÍNCULO DO ALUNO EDITADO COM SUCESSO.
                </div>
              <?php } ?>
		<?php if (isset($_GET["excluido"])) { ?>
                <div class="ls-alert-info ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VÍNCULO EXCLUIDO COM SUCESSO.
                </div>
        <?php } ?> 

</div>

<div class="col-md-12">
<a href="vinculoAlunoExibirTurmaRematricula.php?ct=<?php echo $row_Matricula['turma_id']; ?>" class="ls-btn-primary">Ver todos os alunos da turma <?php echo $row_Matricula['turma_nome']; ?></a>
<a href="vinculoAlunoExibirTurmaRematricula.php" class="ls-btn-primary">Ver alunos de todas as turmas</a>
</div>

<div class="col-md-9">

		
<table class="ls-table ls-no-hover ls-table-striped ls-table-bordered">
	<tr><td><a class="ls-ico-windows" href="print_form_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Ficha de Matrícula do Aluno</a></td></tr>
	<tr><td><a class="ls-ico-insert-template" href="print_comprovante_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Comprovante de Matrícula</a></td></tr>
	<tr><td><a class="ls-ico-insert-template" href="print_declaracao_matricula.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Declaração de Matrícula</a></td></tr>
	<tr><td><a class="ls-ico-envelop" href="print_declaracao_transferencia.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Declaração de Conclusão de Curso</a></td></tr>
	<tr><td><a class="ls-ico-envelop" href="print_declaracao_transferencia_conservado.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Declaração de aluno conservado</a></td></tr>
	<?php if ($row_Matricula['vinculo_aluno_situacao'] == "2") { ?><tr><td><a class="ls-ico-export" href="print_declaracao_transferencia_em_curso.php?hash=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" target="_blank">- Emitir Declaração de Transferência em Curso</a></td></tr><?php } ?>
	<?php if ($row_Matricula['vinculo_aluno_boletim']==0) { ?><tr><td><a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-ico-plus">- Cadastrar Boletim do aluno</a></td></tr><?php } ?>	
	<?php if ($row_Matricula['vinculo_aluno_boletim']==1) { ?><tr><td><a href="boletimVer.php?c=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>" class="ls-ico-search">- Visualizar Boletim do aluno</a></td></tr><?php } ?>	
	<tr><td><a class="ls-ico-pencil" href="alunoEditar.php?hash=<?php echo $row_Matricula['aluno_hash']; ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">- Editar dados do aluno</a></td></tr>
	<tr><td><a class="ls-ico-pencil2" href="vinculoAlunoEditarRematricula.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">- Editar matrícula do aluno</a></td></tr>
	<tr><td><a class="ls-ico-folder-open" href="ocorrenciaCadastrar.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">- Registrar Ocorrência do aluno</a></td></tr>
</table>

<div class="ls-box">
    <div class="row">
      <div class="col-md-2 ls-txt-center">
      <span class="ls-ico-alone ls-ico-screen"></span>
      </div>
      <div class="col-md-10">
          <h3 class="ls-title-5"><strong>Dados de acesso ao painel do aluno</strong></h3>
          <p>
			Data de nascimento: <strong><?php echo inverteData($row_Matricula['aluno_nascimento']); ?></strong><br> 
			Código: <strong><?php echo str_pad($row_Matricula['aluno_id'], 5, '0', STR_PAD_LEFT); ?></strong><br> 
			Senha: <strong><?php echo substr($row_Matricula['aluno_hash'],0,5); ?></strong>
		  </p>
      </div>
    </div>
  </div>

<?php if ($totalRows_Ocorrencia > 0) { ?>

<div class="ls-box ls-lg-space ls-color-danger ls-ico-info ls-ico-bg">
<h1 class="ls-title-1 ls-color-danger">Ocorrências do aluno</h1>
<?php do { ?>
<p><?php echo $row_Ocorrencia['ocorrencia_tipo']; ?>: <?php echo $row_Ocorrencia['ocorrencia_total']; ?></p>
<?php } while ($row_Ocorrencia = mysql_fetch_assoc($Ocorrencia)); ?>
  <a class="ls-btn-primary-danger" href="ocorrenciaExibe.php?cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>">VER OCORRÊNCIAS</a>
</div>





<?php } ?>


<br><br><br>

  </div>
    <div class="col-md-3 ls-txt-center">
	<table class="ls-table ls-no-hover ls-table-striped ls-table-bordered">
	<tr>
	<td>
	<?php if ($row_Matricula['aluno_foto'] == "") { ?>
	<img src="../../aluno/fotos/semfoto.jpg" width="100%">
	<?php } else { ?>
	<img src="<?php echo URL_BASE.'aluno/fotos/' ?><?php echo $row_Matricula['aluno_foto']; ?>" width="100%">
	<?php } ?>
	
	<a class="ls-btn ls-btn-block" href="webcam/index.php?hash=<?php echo htmlentities($row_Matricula['aluno_hash'], ENT_COMPAT, 'utf-8'); ?>&cmatricula=<?php echo $row_Matricula['vinculo_aluno_hash']; ?>#foto">CADASTRAR / ALTERAR FOTO</a>
	</td>
	</tr>
	
	</table>
	</div>

</div>


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

mysql_free_result($Ocorrencia);

mysql_free_result($Matricula);
?>
