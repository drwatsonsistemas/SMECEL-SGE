<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ExibirAlunosVinculados = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_hash, vinculo_aluno_boletim,  
aluno_id, aluno_nome, aluno_nascimento, aluno_hash,
turma_id, turma_nome, turma_turno 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
ORDER BY turma_nome, aluno_nome ASC";
$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
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
 
        <h1 class="ls-title-intro ls-ico-home">Alunos vinculados na escola</h1>
		<!-- CONTEÚDO -->


		<?php if (isset($_GET["erro"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
                </div>
        <?php } ?>

		<?php if (isset($_GET["cadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  ALUNO VINCULADO COM SUCESSO.
                </div>
        <?php } ?>
		<?php if (isset($_GET["boletimcadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  BOLETIM CADASTRADO COM SUCESSO.
                </div>
        <?php } ?>

		<?php if (isset($_GET["excluido"])) { ?>
                <div class="ls-alert-info ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  VÍNCULO EXCLUIDO COM SUCESSO.
                </div>
        <?php } ?>

		<a class="ls-btn-primary ls-ico-windows" href="alunoPesquisar.php">Vincular aluno</a>
		<a class="ls-btn-primary ls-ico-user-add" href="alunoCadastrar.php">Cadastrar aluno</a>


		<?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		<table class="ls-table ls-table-striped ls-sm-space">
		<thead>
			<tr>
				<th width="350px">ALUNO</th>
				<th>NASCIMENTO</th>
				<th>TURMA</th>
				<th>TURNO</th>
				<th class="ls-txt-center">BOLETIM</th>
				<th class="ls-txt-center">MATRÍCULA</th>
			</tr>
			<tbody>
			<?php do { ?>
				<tr>
					<td><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></td>
					<td><?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?></td>
					<td><?php echo $row_ExibirAlunosVinculados['turma_nome']; ?></td>
					<td><?php

					if ($row_ExibirAlunosVinculados['turma_turno']=="1") {
						echo "MATUTINO";
					} else if ($row_ExibirAlunosVinculados['turma_turno']=="2") {
						echo "VESPERTINO";
					} else if ($row_ExibirAlunosVinculados['turma_turno']=="3"){
						echo "NOTURNO";
					}

					?></td>
					<td class="ls-txt-center">
					
					<div data-ls-module="dropdown" class="ls-dropdown">
					  <a href="#" class="ls-btn ls-btn-xs"><?php if ($row_ExibirAlunosVinculados['vinculo_aluno_boletim']==1) { ?><i class="ls-ico-checkmark"></i><?php } else { ?><i class="ls-ico-close ls-color-danger"></i><?php } ?></a>
					  <ul class="ls-dropdown-nav">
						  <?php if ($row_ExibirAlunosVinculados['vinculo_aluno_boletim']==0) { ?><li><a href="boletimCadastrarDisciplinas.php?c=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" class="ls-ico-plus">Cadastrar</a></li><?php } ?>
						  <?php if ($row_ExibirAlunosVinculados['vinculo_aluno_boletim']==1) { ?><li><a href="boletimVer.php?c=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" class="ls-ico-search">Visualizar</a></li><?php } ?>
					  </ul>
					</div>
					</td>
					<td class="ls-txt-center">
					
					<div data-ls-module="dropdown" class="ls-dropdown">
					  <a href="#" class="ls-btn ls-btn-xs"></a>
					  <ul class="ls-dropdown-nav">
						  <li><a href="print_form_matricula.php?hash=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" target="_blank" class="ls-ico-text" title="Ficha de matrícula do aluno">Ficha de matrícula</a></li>
						  <li><a href="print_declaracao_matricula.php?hash=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" target="_blank" class="ls-ico-envelope" title="Declaração de matrícula do aluno">Declaração de matrícula</a></li>
						  <li><a href="print_declaracao_transferencia.php?hash=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" target="_blank" class="ls-ico-export" title="Declaração de transferência do aluno">Declaração de transferência</a></li>
						  <li><a href="alunoEditar.php?hash=<?php echo $row_ExibirAlunosVinculados['aluno_hash']; ?>" class="ls-ico-export" title="Editar dados">Editar dados</a></li>
						  <li><a href="javascript:func()" onclick="confirmaExclusao('<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>')" class="ls-ico-cancel-circle ls-color-danger ls-divider">Excluir</a></li>
					  </ul>
					</div>
					</td>
				</tr>
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
		</tbody>
		</table>
		<?php } else { ?>
		<br><p><div class="ls-alert-info ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  NENHUM ALUNO VINCULADO.
                </div></p>
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
 

	<script language="Javascript">
	function confirmaExclusao(id) {
     var resposta = confirm("Deseja realmente excluir o vínculo deste aluno?");
     	if (resposta == true) {
     	     window.location.href = "matriculaExcluir.php?hash="+id;
    	 }
	}
	</script>

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ExibirAlunosVinculados);
?>
