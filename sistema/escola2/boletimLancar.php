<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/calculos.php"; ?>
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

	if ($row_UsuLogado['usu_update']=="N") {
		header(sprintf("Location: vinculoAlunoExibirTurma.php?permissao"));
		die();
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

$colname_matricula = "-1";
if (isset($_GET['c'])) {
  $colname_matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = sprintf("SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento, vinculo_aluno_conselho,vinculo_aluno_conselho_reprovado, vinculo_aluno_conselho_parecer 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno
ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_matricula, "text"));
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

if ($totalRows_matricula == 0) {
	header("Location:turmaListar.php?nada");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, turma_resultado_consolidado FROM smc_turma WHERE turma_id = '$row_matricula[vinculo_aluno_id_turma]'";
$turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($turma);
$totalRows_turma = mysql_num_rows($turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_turma[turma_matriz_id]'";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_turma[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_digitos FROM smc_criterios_avaliativos WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

$rec = 0;
if ($row_criteriosAvaliativos['ca_rec_paralela']=="S") { 
$rec = 1;
}

$disabled = "";
if ($row_UsuLogado['usu_nota_aluno_escola']=="N") {
	$disabled = " disabled ";
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
                <link rel="stylesheet" type="text/css" href="css/locastyle.css">                <link rel="stylesheet" type="text/css" href="css/preloader.css">
            <script src="js/locastyle.js"></script>
                
                <style>
table.bordasimples {
	border-collapse: collapse;
}
table.bordasimples tr td {
	border:0px dotted #808080;
	padding:4px 2px;
}
table.bordasimples {
	font-size:20px;
}

input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            border: none;
            outline: none; /* Remove o contorno ao focar no campo */
            padding: 8px;
            font-size: 20px;
            border-bottom: 1px solid #ccc; /* Opcional: borda inferior para estética */
            width: 100%;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        textarea:focus {
            border-bottom: 1px solid #4CAF50; /* Muda a cor da borda inferior ao focar */
        }
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
                    <h1 class="ls-title-intro ls-ico-home">LANÇAMENTO DE NOTAS</h1>
                    <!-- CONTEÚDO -->
                    
                    <div class="ls-box">
                      <table style="font-size:14px;" width="100%">
                        <tr>
                          <td style="padding:3px 0;">Aluno(a): <strong><?php echo $row_matricula['aluno_nome']; ?></strong></td>
                          <td>Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong></td>
                          <td>Turma: <strong><?php echo $row_turma['turma_nome']; ?></strong></td>
                        </tr>
                      </table>
                      <?php if ($row_matricula['vinculo_aluno_conselho']=="S") { ?>
                      <br>
                      <div class="ls-alert-warning">Aluno APROVADO pelo Conselho de Classe.</div>
                      <?php } ?>
					  <?php if ($row_matricula['vinculo_aluno_conselho_reprovado']=="S") { ?>
						<br>
           <div class="ls-alert-danger">Aluno REPROVADO pelo Conselho de Classe.</div>
         <?php } ?>

        

                    </div>
                    <div class="ls-alert-warning"><strong>ATENÇÃO:</strong> Quando você fizer uma alteração no boletim de um aluno, será necessário consolidar novamente a turma à qual esse aluno pertence, se já tiver sido consolidada anteriormente. <a href="consolidar_resultados_finais.php">Clique aqui para consolidar</a></div>
                    <table class="ls-table1 ls-sm-space bordasimples" width="100%">

                    <?php if ($row_criteriosAvaliativos['ca_rec_paralela']=="S") { ?>
                    <br>
                    <div class="ls-alert-danger">
                      <strong>Atenção!</strong> A nota da recuperação paralela
                      deverá ser substituída na nota de uma das avaliações do período correspondente.
                    </div>
                    <?php } ?>
                      <thead>
                        <tr height="30">
                          <td width="200"></td>
                          <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <th class="ls-txt-center" style="background-color:#F5F5F5;" colspan="<?php echo $row_criteriosAvaliativos['ca_qtd_av_periodos']+$rec; ?>"><?php echo $p; ?>ª UNIDADE</th>
                          <th></th>
                          <?php } ?>
                          <th colspan="4" class="ls-txt-center" style="background-color:#F5F5F5;">RESULTADO</th>
                        </tr>
                        <tr height="30">
                          <th class="ls-txt-center" width="200">COMPONENTES CURRICULARES</th>
                          <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <?php for ($a = 1; $a <= $row_criteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                          <th class="ls-txt-center"> AV<?php echo $a; ?> </th>
                          <?php } ?>
                          <?php  if ($row_criteriosAvaliativos['ca_rec_paralela']=="S") { ?>
                          <th class="ls-txt-center" width="40">RP</th>
                          <?php } ?>
                          <th class="ls-txt-center" width="40">RU</th>
                          <?php } ?>
                          <th class="ls-txt-center" width="40">TP</th>
                          <th class="ls-txt-center" width="40">MC</th>
                          <th class="ls-txt-center" width="40">AF</th>
                          <th class="ls-txt-center" width="60">RF</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php do { ?>
                          <tr>
                          <td width="200"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                          <?php $tmu = 0; ?>
                          <?php for ($p = 1; $p <= $row_criteriosAvaliativos['ca_qtd_periodos']; $p++) { ?>
                          <?php $ru = 0; ?>
                          <?php for ($a = 1; $a <= $row_criteriosAvaliativos['ca_qtd_av_periodos']; $a++) { ?>
                          <td class="ls-txt-center" style="text-align:center"><?php 
                        mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_nota = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '$a'";
                        $nota = mysql_query($query_nota, $SmecelNovo) or die(mysql_error());
                        $row_nota = mysql_fetch_assoc($nota);
                        $totalRows_nota = mysql_num_rows($nota);
                        //echo exibeTraco($row_nota['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']);
						            $ru = $ru + $row_nota['nota_valor'];
                        ?>
                              <?php if ($totalRows_nota == 0) { ?>
                              <a class="ls-btn-danger" href="boletimCadastrarDisciplinasCorrecao.php?c=<?php echo $colname_matricula; ?>">Gerar notas</a>
                              <?php } else { ?>
                              <input 
                              type="text" 
                              max="<?php echo $row_criteriosAvaliativos['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_criteriosAvaliativos['ca_nota_min_av']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_nota['nota_hash']; ?>" 
                              value="<?php if ($row_nota['nota_valor']<>"") { echo number_format($row_nota['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>" 
                              notaAnterior="<?php if ($row_nota['nota_valor']<>"") { echo number_format($row_nota['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>"
                              turma = "<?php echo $row_turma['turma_id']; ?>"
                              escola = "<?php echo $row_EscolaLogada['escola_id']; ?>" 
                              decimal = "<?php echo $row_criteriosAvaliativos['ca_digitos']; ?>"
                              consolidado = "<?php echo $row_turma['turma_resultado_consolidado']; ?>"
                              disciplina="<?php echo $row_disciplinasMatriz['disciplina_nome']; ?>" 
                              class="ls-field-md nota"
							  style="display:block; text-align:center; width:60px; <?php if ($row_nota['nota_valor'] >= $row_criteriosAvaliativos['ca_nota_min_av']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_nota==0) { echo "disabled"; } ?>
                              <?php echo $disabled; ?>
                          >
                              <?php } ?></td>
                          <?php } ?>
                          <?php  
						
						if ($row_criteriosAvaliativos['ca_rec_paralela']=="S") { 
						
						mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaRecPar = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '$p' AND nota_num_avaliacao = '98'";
                        $notaRecPar = mysql_query($query_notaRecPar, $SmecelNovo) or die(mysql_error());
                        $row_notaRecPar = mysql_fetch_assoc($notaRecPar);
                        $totalRows_notaRecPar = mysql_num_rows($notaRecPar);
						
						?>
                          <td class="ls-txt-center" width="40"><input 
                              type="text" 
                              max="<?php echo $row_criteriosAvaliativos['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_criteriosAvaliativos['ca_nota_min_recuperacao_final']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaRecPar['nota_hash']; ?>" 
                              value="<?php if ($row_notaRecPar['nota_valor']<>"") { echo number_format($row_notaRecPar['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>" 
                              notaAnterior="<?php if ($row_notaRecPar['nota_valor']<>"") { echo number_format($row_notaRecPar['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>"
                              disciplina="<?php echo $row_disciplinasMatriz['disciplina_nome']; ?>"
                              turma = "<?php echo $row_turma['turma_id']; ?>"
                              escola = "<?php echo $row_EscolaLogada['escola_id']; ?>" 
                              decimal = "<?php echo $row_criteriosAvaliativos['ca_digitos']; ?>"
                              consolidado = "<?php echo $row_turma['turma_resultado_consolidado']; ?>"
                              class="ls-field-md nota"
                              <?php echo $disabled; ?>
							  style="display:block; width:60px; <?php if ($row_notaRecPar['nota_valor'] >= $row_criteriosAvaliativos['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              
                          >
                              <?php //echo exibeTraco($row_notaRecPar['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_av']); ?></td>
                          <?php } ?>
                          <td class="ls-txt-center ls-background-info1" width="40"><strong>
                            <?php $mu = mediaUnidade($ru,$row_criteriosAvaliativos['ca_arredonda_media'],$row_criteriosAvaliativos['ca_aproxima_media'],$row_criteriosAvaliativos['ca_media_min_periodo'],$row_criteriosAvaliativos['ca_calculo_media_periodo'],$row_criteriosAvaliativos['ca_qtd_av_periodos'],$row_criteriosAvaliativos['ca_digitos']); ?>
                            <?php $tmu = $tmu + $mu; ?>
                            </strong></td>
                          <?php } ?>
                          <td class="ls-txt-center" width="40"><strong>
                            <?php $tp = totalPontos($tmu,$row_criteriosAvaliativos['ca_digitos']); ?>
                            </strong></td>
                          <td class="ls-txt-center" width="40"><strong>
                            <?php $mc = mediaCurso($tp,$row_criteriosAvaliativos['ca_arredonda_media'],$row_criteriosAvaliativos['ca_aproxima_media'],$row_criteriosAvaliativos['ca_min_media_aprovacao_final'],$row_criteriosAvaliativos['ca_qtd_periodos'],$row_criteriosAvaliativos['ca_digitos']); ?>
                            </strong></td>
                          <td class="ls-txt-center" width="40"><strong>
                            <?php 
                        mysql_select_db($database_SmecelNovo, $SmecelNovo);
                        $query_notaAf = "SELECT nota_id, nota_id_matricula, nota_id_disciplina, nota_periodo, nota_num_avaliacao, nota_max, nota_min, nota_valor, nota_hash FROM smc_nota WHERE nota_id_matricula = '$row_matricula[vinculo_aluno_id]' AND nota_id_disciplina = $row_disciplinasMatriz[disciplina_id] AND nota_periodo = '99' AND nota_num_avaliacao = '99'";
                        $notaAf = mysql_query($query_notaAf, $SmecelNovo) or die(mysql_error());
                        $row_notaAf = mysql_fetch_assoc($notaAf);
                        $totalRows_notaAf = mysql_num_rows($notaAf);
						$af = avaliacaoFinal($row_notaAf['nota_valor'],$row_criteriosAvaliativos['ca_nota_min_recuperacao_final'],$row_criteriosAvaliativos['ca_digitos']);
                        ?>
                            </strong>
                              <input 
                              type="text" 
                              max="<?php echo $row_criteriosAvaliativos['ca_nota_max_av']; ?>" 
                              notaMin="<?php echo $row_criteriosAvaliativos['ca_nota_min_recuperacao_final']; ?>" 
                              step="0.1" 
                              name="<?php echo $row_notaAf['nota_hash']; ?>" 
                              value="<?php if ($row_notaAf['nota_valor']<>"") { echo number_format($row_notaAf['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>" 
                              notaAnterior="<?php if ($row_notaAf['nota_valor']<>"") { echo number_format($row_notaAf['nota_valor'],$row_criteriosAvaliativos['ca_digitos']); } ?>" 
                              disciplina="<?php echo $row_disciplinasMatriz['disciplina_nome']; ?>" 
                              turma = "<?php echo $row_turma['turma_id']; ?>"
                              escola = "<?php echo $row_EscolaLogada['escola_id']; ?>"
                              decimal = "<?php echo $row_criteriosAvaliativos['ca_digitos']; ?>"
                              consolidado = "<?php echo $row_turma['turma_resultado_consolidado']; ?>"
                              class="ls-field-md nota av_final_input"
                              disabled="true"
							  style="display:block; width:60px; <?php if ($row_notaAf['nota_valor'] >= $row_criteriosAvaliativos['ca_nota_min_recuperacao_final']) { echo "; color:blue;"; } else { echo "; color:red;"; }; ?>" 
                              <?php if ($totalRows_notaAf==0) { echo "disabled"; } ?>
                          ></td>
                          <td class="ls-txt-center" width="60"><?php 
					echo resultadoFinal($mc, $af, $row_criteriosAvaliativos['ca_nota_min_recuperacao_final'], $row_criteriosAvaliativos['ca_min_media_aprovacao_final'],$row_criteriosAvaliativos['ca_digitos']);				
				?></td>
                        </tr>
                          <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>
                      </tbody>
                    </table>
                    <div id="status"></div>
                    <div class="ls-box"> <a href="boletimVer.php?c=<?php echo $colname_matricula; ?>" class="ls-btn-primary">Voltar</a> <a href="javascript:void(0);" class="ls-btn ls-float-right" id="av_final">Lançar Avaliação Final</a> <a href="javascript:void(0);" class="ls-btn ls-float-right recarregar">Recalcular médias</a> <a href="conselho_lancar.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right" id="conselho">Conselho de Classe</a> <a href="reprovar_faltas.php?c=<?php echo $colname_matricula; ?>" class="ls-btn ls-float-right" id="conselho">Reprovar por faltas</a> </div>
                    <?php 
					if ($row_UsuLogado['usu_nota_aluno_escola']=="N") {
						echo "<div class='ls-alert-info'><strong>Atenção: </strong> Não é permitido a alteração de notas.</div>";
					}
					?>
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
                      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
                      <li><a href="#">&gt; Guia</a></li>
                      <li><a href="#">&gt; Wiki</a></li>
                    </ul>
                  </nav>
                </aside>
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script src="js/locastyle.js"></script> 
                <script src="../js/jquery.mask.js"></script> 
                
				
<?php if ($row_UsuLogado['usu_nota_aluno_escola']=="S") { ?>
<script type="text/javascript">
				
$(document).ready(function(){
$("input").blur(function(){

  //var valor = parseFloat($(this).val());
	var id 				    = $(this).attr('name');
	var valor 			  = $(this).val();
	var notaAnterior 	= $(this).attr('notaAnterior');
	var notaMax 		  = $(this).attr('max');
	var notaMin 		  = $(this).attr('notaMin');
	var disciplina 		= $(this).attr('disciplina');
  var turma         = $(this).attr('turma');
  var escola        = $(this).attr('escola');
  var decimal       = $(this).attr('decimal');
  var consolidado   = $(this).attr('consolidado');

  //var id = $(this).attr('name');
  //var valor = parseFloat($(this).val());
  //var notaAnterior = parseFloat($(this).attr('notaAnterior'));
  //var notaMax = parseFloat($(this).attr('max'));
  //var notaMin = parseFloat($(this).attr('notaMin'));
  //var disciplina = $(this).attr('disciplina');
  //var turma = $(this).attr('turma');
  //var escola = $(this).attr('escola');
  //var decimal       = $(this).attr('decimal');
  //var consolidado   = $(this).attr('consolidado');

  var valor1 = parseFloat(valor);
  var notaMin1 = parseFloat(notaMin);

	
	if (valor1 < notaMin1) {
		$(this).css("color", "red");
		} else {
			$(this).css("color", "blue");
			}
	
	
	if (valor != notaAnterior) {
	$.ajax({
		type : 'POST',
      url  : 'fnc/lancaNota.php',
      data : {
			id				:id,
			valor			:valor,
			notaMax			:notaMax,
			notaAnterior	:notaAnterior,
			disciplina		:disciplina,
      turma:turma,
      escola:escola,
      decimal:decimal,
      consolidado:consolidado
			},
			success:function(data){
				$('#status').html(data);
				
				setTimeout(function(){
					  $("#status").html("");					
					},15000);
				
				}
		})
	}
	
	  });
});

</script>



<?php } else { ?>


<script type="text/javascript">
				
$(document).ready(function(){
	$("input").blur(function(){
	
		$('#status').html("<hr><div class='ls-alert-danger'><strong>Atenção: </strong>Usuário não tem permissão para alterar as notas dos alunos.</div>");
		
	
	});
});

</script>


<?php } ?>


<script type="text/javascript">
				
$(document).ready(function(){

  <?php if ($row_criteriosAvaliativos['ca_digitos']=="1") { ?>
  $('.nota').mask('00.0', {reverse: true});
  <?php } else { ?>
    $('.nota').mask('00.00', {reverse: true});
  <?php } ?> 


  $('.money').mask('000.000.000.000.000,00', {reverse: true});
});				

$(document).ready(function() {
            $('.recarregar').click(function() {
                location.reload();
            });
      });  		  
		   
</script> 
                <script type="text/javascript">
  //Popula campo cidades com base na escolha do campo estados
    $(document).ready(function(){
        $('#av_final').click(function(){
			//$(".av_final_input").css("display", "block");
			//$(".av_final_input").addClass("ls-disabled");
			$(".av_final_input").prop("disabled", false);
        });
		
		
		
    });
	
	
    </script>
                </body>
                </html>
				<?php
mysql_free_result($UsuLogado);

mysql_free_result($matricula);

mysql_free_result($turma);

mysql_free_result($disciplinasMatriz);

mysql_free_result($criteriosAvaliativos);

mysql_free_result($nota);

mysql_free_result($matriz);

mysql_free_result($EscolaLogada);
?>
				