<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/inverteData.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
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
turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento 
FROM smc_turma 
WHERE turma_tipo_atendimento = '1' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
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
if (isset($_GET['st'])) {	
  $stCod = anti_injection($_GET['st']);
  $stCod = (int)$stCod;
}

	$st = "1";
	$stqry = "AND vinculo_aluno_situacao = $st ";
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

			  $nomeFiltro = "Matriculados";
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
							   echo "Matriculados";
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
turma_total_alunos, turma_ano_letivo, turma_tipo_atendimento 
FROM smc_turma 
WHERE turma_tipo_atendimento = '1' AND turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' $buscaTurma 
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
 
.aluno1 {
	background-color: #ddd;
	height: 192px;
	object-fit: cover;
	width: 160px;
}

.aluno {
    background-color: #ddd;
    object-fit: cover;
    width: 100%;
    aspect-ratio: 1 / 1;
    /* Define a proporção da imagem como 1:1 */
    border-radius: 50%;
    /* Mantém a borda não arredondada, pode ser removido se necessário */
  }

.borda {

  border: solid 4px #23CE6B

}  

</style>  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
  <body>
  


  
  
<?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Alunos no EduConnect</h1>
		<!-- CONTEÚDO -->


		<?php if (isset($_GET["erro"])) { ?>
                <div class="ls-alert-danger ls-dismissable">
                  <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
                  <i class="ls-ico-cancel-circle"></i> OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
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

				<li><a href="vinculoAlunoExibirEduConnect.php">- TODAS -</a></li>
				<?php do { ?>
				<li><a href="vinculoAlunoExibirEduConnect.php?ct=<?php echo $row_ListarTurmas['turma_id']; ?>"><?php echo $row_ListarTurmas['turma_nome']; ?> - <?php echo $row_ListarTurmas['turma_turno_nome']; ?></a></li>
                <?php } while ($row_ListarTurmas = mysql_fetch_assoc($ListarTurmas)); ?>
				  
                </ul>
              </div>
 
</div>
        
		<?php 
      $totalAlunosEscola = 0; 
      $alunosEscolaEduConnect = 0;
    
    ?>
        
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
		aluno_id, aluno_cod_inep, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_sexo, aluno_foto, aluno_aceite_termos
		FROM smc_vinculo_aluno 
		INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
		WHERE vinculo_aluno_id_turma = '$row_ExibirTurmas[turma_id]' $stqry
		ORDER BY aluno_nome ASC";
		$ExibirAlunosVinculados = mysql_query($query_ExibirAlunosVinculados, $SmecelNovo) or die(mysql_error());
		$row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados);
		$totalRows_ExibirAlunosVinculados = mysql_num_rows($ExibirAlunosVinculados);




		?>
        
		<div class="ls-box ls-sm-space">
			
		<h3 class="ls-title-3 ls-txt-center"><?php echo $row_ExibirTurmas['turma_nome']; ?> - <?php echo $row_ExibirTurmas['turma_turno_nome']; ?> (<?php echo $nomeFiltro; ?>)</h3>
	        <hr>
        
        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
		
        <?php $contaAlunos = 1; ?>
        <?php $alunosTurmaEduConnect = 0; ?>



	<div style="float:left;">
	<?php do { ?>


			
			
	<?php //echo $row_ExibirAlunosVinculados['aluno_foto']; ?>
			
	<p style="float:left; margin:1px; text-align:center; padding:1px; width:150px; height:180px;" id="<?php echo $row_ExibirAlunosVinculados['aluno_hash']; ?>">
	 
      
        <?php
        if ($row_ExibirAlunosVinculados['aluno_aceite_termos']=="S") {
           $alunosTurmaEduConnect++;
           $alunosEscolaEduConnect++;
        } 
        ?>

	 
	 <?php if ($row_ExibirAlunosVinculados['aluno_foto']=="") { ?>
	 
			
      <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" border="0" width="50%" class="aluno <?php if ($row_ExibirAlunosVinculados['aluno_aceite_termos'] == "S") { echo "borda"; } ?>">
			<br>
      <small style="font-size:9px;">
			<a href="#<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" target="_blank">
      <?php echo current( str_word_count($row_ExibirAlunosVinculados['aluno_nome'],2)); ?> <?php $word = explode(" ", trim($row_ExibirAlunosVinculados['aluno_nome'])); echo $word[count($word)-1]; ?>
      </a>
      </small> 
			<?php } else { ?>
      <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_ExibirAlunosVinculados['aluno_foto']; ?>" border="0" width="50%" class="aluno <?php if ($row_ExibirAlunosVinculados['aluno_aceite_termos'] == "S") { echo "borda"; } ?>">
			<br>
      <small style="font-size:9px;">
			<a href="matriculaExibe.php?cmatricula=<?php echo $row_ExibirAlunosVinculados['vinculo_aluno_hash']; ?>" target="_blank"><?php echo current( str_word_count($row_ExibirAlunosVinculados['aluno_nome'],2)); ?> <?php $words = explode(" ", trim($row_ExibirAlunosVinculados['aluno_nome'])); echo $words[count($words)-1]; ?></a>
      </small>
			
	<?php } ?>


	 
	 </p>
	 
			
			
				
				
				<?php } while ($row_ExibirAlunosVinculados = mysql_fetch_assoc($ExibirAlunosVinculados)); ?>


        
				<?php mysql_free_result($ExibirAlunosVinculados); ?>
	
	</div>
	
  <p class="">Total de alunos vinculados na turma: <strong><?php echo $totalRows_ExibirAlunosVinculados; ?></strong></p>
  <p class="">Total de alunos que ativaram o perfil EduConnect na turma: <strong><?php echo $alunosTurmaEduConnect; ?></strong></p>
  
	
		
		
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
          <p>Total de alunos que ativaram o perfil EduConnect na escola: <strong><?php echo $alunosEscolaEduConnect; ?></strong></p>
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
	
<script language="Javascript">	
 $(document).ready(function(){
          setTimeout('$("#preload").fadeOut(100)', 1500);
      });
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
	//BUSCA NOTICIA
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
