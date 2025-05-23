<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php 
$colname_Disciplinas = "-1";
if (isset($_GET['componente'])) {
  $colname_Disciplinas = $_GET['componente'];
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  $colname_Turma = $_GET['turma'];
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_situacao, vinculo_aluno_ano_letivo, vinculo_aluno_hash, vinculo_aluno_dependencia,
vinculo_aluno_rel_aval, aluno_id, aluno_nome, aluno_foto, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia, disciplina_id, disciplina_nome, turma_id, turma_nome, turma_matriz_id, turma_id_escola, turma_ano_letivo, 
turma_etapa, matriz_id, matriz_criterio_avaliativo, ca_id, ca_questionario_conceitos,ca_forma_avaliacao,
CASE vinculo_aluno_situacao
WHEN 1 THEN 'MATRICULADO'
WHEN 2 THEN 'TRANSFERIDO(A)'
WHEN 3 THEN 'DESISTENTE'
WHEN 4 THEN 'FALECIDO(A)'
WHEN 5 THEN 'OUTROS'
END AS vinculo_aluno_situacao_nome  
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_disciplina ON disciplina_id = $colname_Disciplinas
INNER JOIN smc_turma ON turma_id = '$colname_Turma'
INNER JOIN smc_matriz ON matriz_id = turma_matriz_id
INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
WHERE vinculo_aluno_id_turma = '$colname_Turma' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' AND vinculo_aluno_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'
ORDER BY aluno_nome";
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

$linkMapa="rendimento_mapa";
if (($row_Alunos['turma_etapa']=="1") || ($row_Alunos['turma_etapa']=="2") || ($row_Alunos['turma_etapa']=="3")) {
	$linkAvaliar = "conceito";
	$nomeAvaliar = "CONCEITO"; 
} else {
  
  if ($row_Alunos['ca_questionario_conceitos']=="S") {

    $linkAvaliar = "conceitoEf";

    $nomeAvaliar = "CONCEITO EF"; 

    
  } else {

    if($row_Alunos['ca_forma_avaliacao'] == "Q"){
      $linkAvaliar = "qq_aluno";
      $linkMapa="rendimento_mapa_qq";
    }else{
      $linkAvaliar = "rendimento_aluno";
      $linkMapa="rendimento_mapa";
    }
    
    
    $nomeAvaliar = "NOTAS"; 
    
  }
  
}



if ($totalRows_Alunos == 0) {
	//header("Location:index.php?erro");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = sprintf("SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue FROM smc_escola WHERE escola_id = '$row_Alunos[turma_id_escola]'");
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);

if($totalRows_Escola=="") {
	//header("Location:../index.php?loginErr");
}
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?>| SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" href="css/sweetalert2.min.css">
  <style>
    .aluno {
     background-color: #ddd;
     border-radius: 100%;
     height: 50px;
     object-fit: cover;
     width: 50px;
   }
 </style>
</head>
<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">RENDIMENTO | Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
      
      <div class="ls-box" style="border-left: 5px #000066 solid;">
        
        <p>TURMA: <strong><?php echo $row_Alunos['turma_nome']; ?></strong></p>
        <p>EIXO/CAMPO DE EXPERIÊNCIA: <strong><?php echo $row_Alunos['disciplina_nome']; ?></strong></p>
        
      </div>
      
      <?php if ($totalRows_Alunos==0) { ?>
        NENHUM ALUNO COM BOLETIM GERADO. <a href="index.php">VOLTAR</a>
      <?php } else { ?>
        <p> <a href="rendimento.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
          
          <a href="<?= $linkMapa ?>.php?componente=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" class="ls-btn">MAPA DE NOTAS</a> 
          <!--<a href="plano_aula.php?disciplina=<?php echo $row_Alunos['disciplina_id']; ?>&turma=<?php echo $row_Alunos['turma_id']; ?>" class="waves-effect waves-light btn-small btn right"><i class="material-icons left">map</i> CONTEÚDO DAS AULAS</a>--> 
        </p>
        <br>
        <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto">
          <tr>
            <th width="80"></th>
            <th>NOME</th>
          </tr>
          <?php do { ?>
            <tr>
              <td><?php if ($row_Alunos['aluno_foto']=="") { ?>
                <img src="<?php echo '../../aluno/fotos/' ?>semfoto.jpg" class="hoverable aluno circle" border="0" width="100%">
              <?php } else { ?>
                <img src="<?php echo '../../aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>" class="hoverable aluno circle" border="0" width="100%">
              <?php } ?>
              <?php //echo $row_Alunos['aluno_nome']; ?></td>
              <td><span class="title"><strong><?php echo $row_Alunos['aluno_nome']; ?></strong>
                <?php if ( $row_Alunos['vinculo_aluno_situacao']<>"1") { ?>
                 | <span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
               <?php } ?>
               <?php if ( $row_Alunos['vinculo_aluno_dependencia']=="S") { ?>
                
                 | <span class="ls-color-danger">CUMPRINDO DEPENDÊNCIA NA TURMA</span>
               <?php } ?>
               <?php if ( $row_Alunos['aluno_aluno_com_deficiencia']=="1") { ?>
                
                | <img src="../../img/pne.png" width="15px" style="cursor:pointer" alt="Aluno(a) com necessidades especiais" title="Aluno(a) com necessidades especiais: <?php echo $row_Alunos['aluno_tipo_deficiencia']; ?>">
              <?php } ?>
            </span>
            <br><br>
            <small>
              <p> 
                <a class="ls-btn-primary ls-btn-xs" href="<?php echo $linkAvaliar; ?>.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" id="<?php echo $row_Alunos['boletim_id']; ?>"><?php echo $nomeAvaliar; ?></a> 
                <a href="parecer_coletivo.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn ls-btn-xs<?php if ( $row_Alunos['vinculo_aluno_rel_aval'] <> "") { ?> ls-ico-bell-o ls-ico-right<?php } ?>" id="<?php echo $row_Alunos['boletim_id']; ?>">P. COLET.</a> 
                <a href="parecer_individual.php?cod=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&disciplina=<?php echo $colname_Disciplinas; ?>&turma=<?php echo $colname_Turma; ?>" class="ls-btn ls-btn-xs">P. INDIV.</a> 
              </p>
            </small>
            
            
          </td>
          <?php //echo current( str_word_count($row_Alunos['aluno_nome'],2)); ?>
          <?php //$aluno = explode(" ", trim($row_Alunos['aluno_nome'])); echo $aluno[count($aluno)-1]; ?>

        </tr>
      <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
    <?php } ?>
  </table>
</div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 
<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>