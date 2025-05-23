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
turma_total_alunos, turma_ano_letivo 
FROM smc_turma 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
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

        <h1 class="ls-title-intro ls-ico-home">Alunos vinculados na escola no Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
		<!-- CONTEÚDO -->

 

		<?php if (isset($_GET["erro"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-cancel-circle"></i> OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
                </div>
        <?php } ?>

		<?php if (isset($_GET["permissao"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-cancel-circle"></i> VOCÊ NÃO TEM PERMISSÃO PARA REALIZAR ESTA AÇÃO.
                </div>
        <?php } ?>

		<?php if (isset($_GET["cadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-checkmark-circle"></i> ALUNO VINCULADO COM SUCESSO.
                </div>
        <?php } ?>
		<?php if (isset($_GET["boletimcadastrado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-checkmark-circle"></i> BOLETIM CADASTRADO COM SUCESSO.
                </div>
        <?php } ?>
		       <?php if (isset($_GET["vinculoeditado"])) { ?>
                <div class="ls-alert-success ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-checkmark-circle"></i> VÍNCULO DO ALUNO EDITADO COM SUCESSO.
                </div>
              <?php } ?>
		<?php if (isset($_GET["excluido"])) { ?>
                <div class="ls-alert-info ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-checkmark-circle"></i> VÍNCULO EXCLUIDO COM SUCESSO.
                </div>
        <?php } ?>
		
			
<div class="ls-box-filter">

			  <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary ls-ico-menu"> Turma: <?php if (isset($_GET['ct'])) { echo $row_ExibirTurmas['turma_nome']." - ".$row_ExibirTurmas['turma_turno_nome']; } else { echo "TODAS"; } ?></a>
                <ul class="ls-dropdown-nav">

				<li><a href="vinculoAlunoExibirTurma.php">- TODAS -</a></li>
				<?php do { ?>
				<li><a href="vinculoAlunoExibirTurma.php?ct=<?php echo $row_ListarTurmas['turma_id']; ?>"><?php echo $row_ListarTurmas['turma_nome']; ?> - <?php echo $row_ListarTurmas['turma_turno_nome']; ?></a></li>
                <?php } while ($row_ListarTurmas = mysql_fetch_assoc($ListarTurmas)); ?>
				  
                </ul>
              </div>
		
					  

			  <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary ls-ico-menu2"> Situação: <?php echo $nomeFiltro; ?></a>
                <ul class="ls-dropdown-nav">
				
				  <li><a href="vinculoAlunoExibirTurma.php">Todos</a></li>
				  <li><a href="vinculoAlunoExibirTurma.php?st=1<?php if ($codTurma <> "") { echo "&ct=$codTurma"; } ?>">Matriculados</a></li>
				  <li><a href="vinculoAlunoExibirTurma.php?st=2<?php if ($codTurma <> "") { echo "&ct=$codTurma"; } ?>">Transferidos</a></li>
				  <li><a href="vinculoAlunoExibirTurma.php?st=3<?php if ($codTurma <> "") { echo "&ct=$codTurma"; } ?>">Desistentes</a></li>
				  <li><a href="vinculoAlunoExibirTurma.php?st=4<?php if ($codTurma <> "") { echo "&ct=$codTurma"; } ?>">Falecidos</a></li>
				  <li><a href="vinculoAlunoExibirTurma.php?st=5<?php if ($codTurma <> "") { echo "&ct=$codTurma"; } ?>">Outros</a></li>
										
                </ul>
              </div>
			  
			  <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary  ls-ico-paint-format"> Relatórios</a>
                <ul class="ls-dropdown-nav">
				  <?php if ($codTurma <> "") { $sep = "&"; } else { $sep = "?"; } ?>	
				  <li><a href="print_alunos_conselho.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Aprovados pelo Conselho de Classe</a></li>
				  <li><a href="print_vinculoAlunoExibirTurma.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Por turma</a></li>
				  <li><a href="print_vinculoAlunoExibirTurmaTransporteEscolar.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Usuários do Transporte Escolar</a></li>
				  <li><a href="print_vinculoAlunoBolsaFamilia.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Beneficiários do Bolsa Família</a></li>
				  <li><a href="print_vinculoAlunoExibirTurmaIdentificacao.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Identificação dos alunos (CPF)</a></li>
				  <li><a href="print_vinculoAlunoExibirTurmaFiliacao.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Identificação dos alunos (Filiação)</a></li>
				  <li><a href="print_vinculoAlunoNis.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Identificação dos alunos (NIS)</a></li>
				  <li><a href="print_vinculoAlunoSus.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Identificação dos alunos (SUS)</a></li>
				  <li><a href="print_boletimTurma.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Boletim</a></li>
				  <li><a href="print_mapa_de_notas.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Mapa de notas</a></li>
				  <li><a href="print_lista_frequencia.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Frequência/assinaturas</a></li>
				  <li><a href="print_lista_endereco.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Endereço</a></li>
				  <li><a href="print_lista_telefone.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Contatos (e-mail, telefone)</a></li>
				  <li><a href="print_acessoPainelAluno.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Dados de acesso ao Painel do Aluno</a></li>
				  <li><a href="print_carteirinhaAluno.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Carteirinha de Estudante - Barras</a></li>
				  <li><a href="print_carteirinhaAlunoQrcode.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Carteirinha de Estudante - QR Code</a></li>
				  <li><a href="print_etiquetas.php<?php if ($codTurma <> "") { echo "?ct=$codTurma"; } ?><?php if ($stCod <> "") { echo $sep."st=$stCod"; } ?>" target="_blank">Imprimir etiquetas p/ pastas</a></li>
				  <li><a href="print_alunos_por_zona.php<?php if ($codTurma <> "") { echo "?ct=$codTurma&zona=2"; } else { echo "?zona=2"; } ?><?php if ($stCod <> "") { echo "&st=$stCod"; } ?>" target="_blank">Relação de alunos da Zona Rural</a></li>
				  <li><a href="print_alunos_por_zona.php<?php if ($codTurma <> "") { echo "?ct=$codTurma&zona=1"; } else { echo "?zona=1"; } ?><?php if ($stCod <> "") { echo "&st=$stCod"; } ?>" target="_blank">Relação de alunos da Zona Urbana</a></li>
				  <li><a href="print_alunos_por_bairro.php" target="_blank">Relação de alunos por Bairro</a></li>
				  <li><a href="print_alunos_por_bairro_rua.php" target="_blank">Relação de alunos por Bairro/Rua</a></li>
				  <li><a href="print_lista_entrega_kit.php" target="_blank">Recibo de entrega de Kits</a></li>
										
                </ul>
              </div>
			  
			  <a href="vinculoAlunoExibirTurmaAnoAnterior.php" class="ls-btn ls-float-right">Informações do Ano Letivo <?php echo $row_AnoLetivo['ano_letivo_ano']-1; ?></a>
              
 
</div>

<div class="ls-box">

    <form id="form_busca" autocomplete="off" action="redireciona.php" method="get" class="ls-form ls-form-inline row">
	<label class="ls-label col-md-12">
    <b class="ls-label-text">BUSQUE UM ALUNO</b>
	  <input id="inputString" type="text" class="validate" value="" placeholder="BUSQUE PELO NOME DO ALUNO OU O NOME DA MÃE DO ALUNO" onkeyup="lookup(this.value);" onblur="fill();" autofocus />
  </label>
	  <input type="hidden" id="campoBusca" value="" name="matricula" />
	  <div class="suggestionsBox" id="suggestions" style="display: none; margin-top:0px; width:100%; margin-top:0px; position: relative; border:none;">
	  <div class="suggestionList" id="autoSuggestionsList"> </div>
	  </div>
    </form>


</div>  
		
		        
        
		<?php $totalAlunosEscola = 0; ?>
        
        <?php do { ?>
        <?php 
		
		
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_ExibirAlunosVinculados = "
		SELECT 
		vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, 
		vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao,
		CASE vinculo_aluno_situacao
		WHEN 1 THEN 'MATRICULADO'
		WHEN 2 THEN '<span class=\"ls-color-danger\"><b>TRANSFERIDO</b></span>'
		WHEN 3 THEN '<span class=\"ls-color-danger\"><b>DESISTENTE</b></span>'
		WHEN 4 THEN '<span class=\"ls-color-danger\"><b>FALECIDO</b></span>'
		WHEN 5 THEN '<span class=\"ls-color-danger\"><b>OUTROS</b></span>'
		END AS vinculo_aluno_situacao_nome, 
		aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_sexo
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);
		?>
        
		<div class="ls-box ls-sm-space">
			
			<h5 class="ls-title-5 ls-txt-center"><?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?> (<?php echo $nomeFiltro; ?>)</h5>
	        
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		
        <?php $contaAlunos = 1; ?>

		<table class="ls-table ls-table-striped ls-sm-space fonte-tabela">
		<thead>
			<tr>
				<th class="ls-txt-center" width="40">Nº</th>
				<th class="ls-txt-center" width="60">MAT</th>
				<th class="ls-txt-center">ALUNO</th>
				<th class="ls-txt-center hidden-xs">FILIAÇÃO</th>
				<th class="ls-txt-center hidden-xs" width="130">INEP</th>
				<th class="ls-txt-center hidden-xs" width="130">CPF</th>
				<th class="ls-txt-center hidden-xs" width="130">NASCIMENTO</th>
				<th class="ls-txt-center hidden-xs" width="130">SITUAÇÃO</th>
			</tr>
			<tbody>
			<?php do { ?>
				<tr>
					<td class="ls-txt-center"><?php 
					echo $contaAlunos;
					$contaAlunos++;		
					?></td>
					<td class="ls-txt-center"><a href="matriculaExibe.php?cmatricula=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>"><?php echo str_pad($row_ExibirAlunosVinculados['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></a></td>
					<td><?php echo $row_ExibirAlunosVinculados['aluno_nome']; ?></td>
					<td class="ls-txt-center hidden-xs"><?php echo $row_ExibirAlunosVinculados['aluno_filiacao1']; ?></td>
					<td class="ls-txt-center hidden-xs"><?php echo $row_ExibirAlunosVinculados['aluno_cod_inep']; ?></td>
					<td class="ls-txt-center hidden-xs"><?php echo $row_ExibirAlunosVinculados['aluno_cpf']; ?></td>
					<td class="ls-txt-center hidden-xs"><?php echo inverteData($row_ExibirAlunosVinculados['aluno_nascimento']); ?></td>
					<td class="ls-txt-center hidden-xs"><?php echo $row_ExibirAlunosVinculados['vinculo_aluno_situacao_nome']; ?></td>
				

				

				</tr>
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>
				<?php mysql_free_result($ExibirAlunosVinculados); ?>
		</tbody>
		</table>
		
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


<script type="text/javascript">
$(function(){
    $(".buscar-funcionario").keyup(function(){
        //pega o css da tabela 
        var tabela = $(this).attr('alt');
        if( $(this).val() != ""){
            $("."+tabela+" tbody>tr").hide();
            $("."+tabela+" td:contains-ci('" + $(this).val() + "')").parent("tr").show();
        } else{
            $("."+tabela+" tbody>tr").show();
        }
    }); 
});
$.extend($.expr[":"], {
    "contains-ci": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});
</script>  

<script type="text/javascript">
	function lookup(inputString) {
		if(inputString.length == 0) {
			$('#suggestions').hide();
		} else {
			$.post("busca_aluno.php", {queryString: ""+inputString+""}, function(data){
				if(data.length > 5) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	}
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
	
	function exibe(thisValue) {
		$('#campoBusca').val(thisValue);
		$('#inputString').val("Redirecionando...");
		$("#form_busca").submit();
		}
</script>
<script type="text/javascript">
$('html').bind('keypress', function(e) {
   if(e.keyCode == 13) {
      return false;
   }
});
</script>

  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($ListarTurmas);

mysql_free_result($ExibirTurmas);

//mysql_free_result($ExibirAlunosVinculados);
?>
