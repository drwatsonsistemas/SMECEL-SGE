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
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento 
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
$query_turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = '$row_matricula[vinculo_aluno_id_turma]'";
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
$query_criteriosAvaliativos = "
SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, 
ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, 
ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_grupo_conceito, ca_grupo_etario  
FROM smc_criterios_avaliativos 
WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso DESC
";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

$colname_Periodo = "";
$periodo = $row_criteriosAvaliativos['ca_qtd_periodos']; 
if (isset($_GET['periodo'])) {
  	$colname_Periodo = $_GET['periodo'];
	$periodo = $colname_Periodo;
} else {
	$colname_Periodo = "";
    $periodo = $row_criteriosAvaliativos['ca_qtd_periodos']; 
	}

  $message = "";

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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

</head>
<body>
	  <?php include_once ("menu-top.php"); ?>
      <?php include_once ("menu-esc.php"); ?>
      
                <main class="ls-main ">
                  <div class="container-fluid">
                     
                    <h1 class="ls-title-intro ls-ico-home">ACOMPANHAMENTO DO ALUNO</h1>
                    <!-- CONTEÚDO -->
                    
                    <div class="ls-box">
                      <table style="font-size:14px;" width="100%">
                        <tr>
                          <td style="padding:3px 0;">Aluno(a): <strong><?php echo $row_matricula['aluno_nome']; ?></strong></td>
                          <td>Nascimento: <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong></td>
                          <td>Turma: <strong><?php echo $row_turma['turma_nome']; ?></strong></td>
                        </tr>
                      </table>

                      <?php $message .= " Nome: ".$row_matricula['aluno_nome']; ?>
                      <?php $message .= " Nascimento: ".$row_matricula['aluno_nascimento']; ?>
                      <?php $message .= " Turma: ".$row_turma['turma_nome']; ?>
                    
                    </div>
                    <?php if (isset($_GET["boletimcadastrado"])) { ?>
                      <p>
                      <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss" class="ls-dismiss">&times;</span> Relatório gerado com sucesso. </div>
                      </p>
                      <?php } ?>



<div class="ls-box"> 
	<a href="matriculaExibe.php?cmatricula=<?php echo $colname_matricula; ?>" class="ls-btn">Voltar</a>  
	

<?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) { echo "ls-active"; } ?>" href="conceitoVerIA.php?c=<?php echo $colname_matricula; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º período</a>
<?php } ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) { echo "ls-active"; } ?>" href="conceitoVerIA.php?c=<?php echo $colname_matricula; ?>">Anual</a>    

</div>

<?php if ($colname_Periodo == "") { $colname_Periodo = "Anual"; } ?>

<?php $message .= " Período: ".$colname_Periodo."º; "; ?>

                 
   <?php do { ?>

                   
   <?php
   
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_Campos = "SELECT campos_exp_id, campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos FROM smc_campos_exp";
    $Campos = mysql_query($query_Campos, $SmecelNovo) or die(mysql_error());
    $row_Campos = mysql_fetch_assoc($Campos);
    $totalRows_Campos = mysql_num_rows($Campos);

   ?>
   
	<?php do { ?>
    

    <?php
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Objetos = "SELECT campos_exp_obj_id, campos_exp_obj_id_campos_exp, campos_exp_obj_nome, campos_exp_obj_faixa_et_cod, campos_exp_obj_faixa_et_nome, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes FROM smc_campos_exp_objetivos WHERE campos_exp_obj_id_campos_exp = '$row_Campos[campos_exp_id]' AND campos_exp_obj_faixa_et_cod = '$row_criteriosAvaliativos[ca_grupo_etario]'";
        $Objetos = mysql_query($query_Objetos, $SmecelNovo) or die(mysql_error());
        $row_Objetos = mysql_fetch_assoc($Objetos);
        $totalRows_Objetos = mysql_num_rows($Objetos);
      
	  
        /*do { 
            $rendimento[] = $row_Rendimento['conc_acomp_id']."-".$row_Rendimento['conc_matricula_id']."-".$row_Rendimento['conc_periodo']."-".$row_Rendimento['conc_avaliacao'];
        } while ($row_Rendimento = mysql_fetch_assoc($Rendimento));*/
        
    ?>
                            
      <?php //echo utf8_encode($row_Campos['campos_exp_nome']); ?>

      <?php $message .= " Campo de Experiência: ".utf8_encode($row_Campos['campos_exp_nome']); ?>

      
      <?php do { ?>
      
      <?php 
        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_Acompanhamento = "
		SELECT acomp_id, acomp_id_matriz, acomp_id_crit, acomp_id_obj_aprend, acomp_descricao, acomp_hash 
		FROM smc_acomp_proc_aprend
		WHERE acomp_id_matriz = '$row_matriz[matriz_id]'
		AND acomp_id_crit = '$row_criteriosAvaliativos[ca_id]'
		AND acomp_id_obj_aprend = '$row_Objetos[campos_exp_obj_id]'
		";
        $Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
        $row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
        $totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
      ?>
      
      
      <?php //echo utf8_encode($row_Objetos['campos_exp_obj_campos_exp']); ?>

      <?php $message .= " Objetivo de aprendizagem: ".utf8_encode($row_Objetos['campos_exp_obj_campos_exp']); ?>
      
      
      
            <?php 
			
			$total = 0;
			$pontos = 0;
			
			?>
            
            
            <?php do { ?>
        
            
            <?php //echo $row_Acompanhamento['acomp_descricao']; ?>

            <?php //$message .= " Acompanhamento do processo de aprendizagem: ".$row_Acompanhamento['acomp_descricao']; ?>
            
            <?php for ($p = 1; $p <= $periodo; $p++) { ?>
            
            
			  <?php 
              
                  mysql_select_db($database_SmecelNovo, $SmecelNovo);
                  $query_ConceitoAluno = "
                  SELECT conc_id, conc_acomp_id, conc_matricula_id, conc_periodo, conc_avaliacao FROM smc_conceito_aluno
                  WHERE conc_acomp_id = '$row_Acompanhamento[acomp_id]' AND conc_matricula_id = '$row_matricula[vinculo_aluno_id]' AND conc_periodo = '$p'
                  ";
                  $ConceitoAluno = mysql_query($query_ConceitoAluno, $SmecelNovo) or die(mysql_error());
                  $row_ConceitoAluno = mysql_fetch_assoc($ConceitoAluno);
                  $totalRows_ConceitoAluno = mysql_num_rows($ConceitoAluno);
				  
				  $pontos = $pontos + $row_ConceitoAluno['conc_avaliacao'];
				  $total = $total + $row_GrupoConceitos['conceito_itens_peso'];
      
              ?>

          <?php //$message .= " Nota/Conceito: ".$row_ConceitoAluno['conc_avaliacao']; ?>
              
            
            
                
			<?php } ?>
            
            
            
           
            
        <?php } while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento)); ?>  
            
      
      <?php if ( $pontos > 0) { $percentual = number_format((($pontos/$total)*100),0)."%"; } else { echo ""; } ?>
      <?php //echo $pontos."/".$total; ?>

      <?php $message .= " Pontuação/Máximo: ".$pontos."/".$total; ?>
      <?php $message .= " Percentual: ".$percentual; ?>

      
      
      <?php } while ($row_Objetos = mysql_fetch_assoc($Objetos)); ?>   
      
      
      
    <?php } while ($row_Campos = mysql_fetch_assoc($Campos)); ?>
                    
                      
<?php } while ($row_matricula = mysql_fetch_assoc($matricula)); ?>
                  

    <form id="text-form" method="post">
        <textarea style="display: none;" id="texto" name="texto" rows="4" cols="50" required><?php echo $message; ?></textarea>
        <button type="submit" class="ls-btn-primary">Exibir relatório da Inteligência Artificial</button>
    </form>
    <div id="loading" style="display: none;"><img src="https://www.blogson.com.br/wp-content/uploads/2017/10/loading-gif-transparent-10.gif" width="100px"></div>
    <div id="response" style="padding: 15px 0;"></div>
    <div class="error" id="error"></div>
                 
                 
 <?php //echo $message."<br><hr><br>"; ?>                
                 
                                          
                      
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
                      <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
                      <li><a href="#">&gt; Guia</a></li>
                      <li><a href="#">&gt; Wiki</a></li>
                    </ul>
                  </nav>
                </aside>
                
                <!-- We recommended use jQuery 1.10 or up --> 
                <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
                <script src="js/locastyle.js"></script> 

                <script>
        document.getElementById('text-form').addEventListener('submit', function(event) {
            event.preventDefault();

            var textoInput = document.getElementById('texto').value;
            var responseDiv = document.getElementById('response');
            var errorDiv = document.getElementById('error');
            var loadingDiv = document.getElementById('loading');

            // Limpar mensagens anteriores
            responseDiv.innerHTML = '';
            errorDiv.innerHTML = '';
            loadingDiv.style.display = 'block';  // Mostrar "Carregando"

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'chatgpt_escola/ia_educacao_infantil.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    loadingDiv.style.display = 'none';  // Esconder "Carregando"
                    if (xhr.status === 200) {
                        responseDiv.innerHTML = xhr.responseText;
                    } else {
                        errorDiv.innerHTML = 'Erro: ' + xhr.statusText;
                    }
                }
            };

            xhr.send('texto=' + encodeURIComponent(textoInput));
        });
    </script>
                 
                <script type="text/javascript">


</script>
                </body>
                </html>
				<?php
mysql_free_result($UsuLogado);

mysql_free_result($matricula);

mysql_free_result($turma);

mysql_free_result($disciplinasMatriz);

mysql_free_result($criteriosAvaliativos);

mysql_free_result($matriz);

mysql_free_result($EscolaLogada);
?>
				