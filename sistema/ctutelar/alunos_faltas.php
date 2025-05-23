<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>

<?php include "../funcoes/idade.php"; ?>

<?php


$hoje = date("Y-m-d");
$periodo_label = "HOJE";


$periodo = "";

if (isset($_GET['periodo']) && $_GET['periodo']<>"") {

  $periodo = $_GET['periodo'];
  
  switch ($periodo) {
    case 1:
    $data_final = $hoje;
    $data_inicio = $hoje;
    $periodo_label = "HOJE";
    break;
    case 2:
    $data_final = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-1 days",strtotime($hoje)));
    $periodo_label = "ONTEM";
    break;
    case 5:
    $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-5 days",strtotime($hoje)));
    $periodo_label = "OS ÚLTIMOS 5 DIAS";
    break;
    case 7:
    $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-7 days",strtotime($hoje)));
    $periodo_label = "A ÚLTIMA SEMANA";
    break;
    case 30:
    $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-30 days",strtotime($hoje)));
    $periodo_label = "O ÚLTIMO MÊS";
    break;
    case 180:
    $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-180 days",strtotime($hoje)));
    $periodo_label = "OS ÚLTIMOS 6 MESES";
    break;
    case 365:
    $data_final = date('Y-m-d', strtotime("0 days",strtotime($hoje)));
    $data_inicio = date('Y-m-d', strtotime("-365 days",strtotime($hoje)));
    $periodo_label = "O ÚLTIMO ANO";
    break;
    default:
    $data_final = $hoje;
    $data_inicio = $hoje;
    $periodo_label = "HOJE";
    break;
  }
  
  
  
  
} else {

  if ((isset($_GET['data_final']) && ($_GET['data_final']<>"")) && (isset($_GET['data_inicio']) && ($_GET['data_inicio']<>""))) {

    $data_final = date("Y-m-d", strtotime($_GET['data_final']));
    $data_inicio = date("Y-m-d", strtotime($_GET['data_inicio']));
    
  } else {

   $data_final = date("Y-m-d");
   $data_inicio = date("Y-m-d");
   
 }

}







mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_id_turma, vinculo_aluno_hash,
aluno_id, aluno_nome, aluno_nascimento, 
escola_id, escola_nome, turma_id, turma_nome, turma_turno, COUNT(*) AS total_faltas,
CASE turma_turno 
WHEN 0 THEN 'INTEG'
WHEN 1 THEN 'MATUT'
WHEN 2 THEN 'VESPE'
WHEN 3 THEN 'NOTUR'
END AS turma_turno
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_sec = ".SEC_ID." AND faltas_alunos_data BETWEEN '$data_inicio' AND '$data_final'
GROUP BY faltas_alunos_matricula_id
ORDER BY total_faltas DESC
";
$faltas = mysql_query($query_faltas, $SmecelNovo) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas); 
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
  <title>SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?> - FALTAS</h1>

      <div class="ls-box">

       <label class="ls-label col-md-12">
        <b class="ls-label-text">BUSQUE UM ALUNO</b>
        <input type="text" class="buscar-aluno" alt="fonte-tabela" placeholder="Digite o nome ou parte do nome de um aluno" autofocus/>
      </label>



    </div> 

    <p>FILTRO PARA <?php echo $periodo_label; ?></p>

    <div class="ls-box-filter">
      <form action="alunos_faltas.php" class="ls-form ls-form-inline">
        <label class="ls-label col-md-4 col-sm-4">
          <b class="ls-label-text">Período</b>
          <div class="ls-custom-select">
            <select name="periodo" id="select_period" class="ls-select">
              <option value="">Período</option>
              <option value="1">Hoje</option>
              <option value="2">Ontem</option>
              <option value="5">Últimos 5 dias</option>
              <option value="7">Última semana</option>
              <option value="30">Últimos 30 dias</option>
              <option value="180">Últimos 6 meses</option>
              <option value="365">Últimos 12 meses</option>
            </select>
          </div>
        </label>
        <label class="ls-label col-md-2 col-sm-2">
          <input type="date" name="data_inicio" class="" id="" value="<?php echo $data_inicio; ?>" autocomplete="off">
        </label>
        <label class="ls-label col-md-2 col-sm-2">
          <input type="date" name="data_final" class="" id="" value="<?php echo $data_final; ?>" autocomplete="off">
        </label>
        <label class="ls-label col-md-1 col-sm-1">
          <input type="submit" class="ls-btn-primary" value="Filtrar">
        </label>

      </form>
    </div>    

    <?php if ($totalRows_faltas > 0) { ?>
      <table class="ls-table ls-sm-space fonte-tabela">
        <thead>
          <tr>
            <th class="ls-txt-center" width="50"></th>
            <th class="ls-txt-center" width="70">MAT</th>
            <th class="ls-txt-center">ALUNO</th>
            <th class="ls-txt-center" width="80">IDADE</th>
            <th class="ls-txt-center">ESCOLA</th>
            <th class="ls-txt-center">TURMA</th>
            <th width="90" class="ls-txt-center">TURNO</th>
            <th width="90" class="ls-txt-center">FALTAS<br>(AULAS)</th>
          </tr>
        </thead>
        <tbody>
          <?php $num = 1; do { ?>
            <tr>
              <td class="ls-txt-center">
                <?php echo $num; $num++ ?>
              </td>

              <td class="ls-txt-center">
                <a href="aluno.php?aluno=<?php echo $row_faltas['vinculo_aluno_hash']; ?>&periodo=<?php echo $periodo; ?>&data_inicio=<?php echo $data_inicio; ?>&data_final=<?php echo $data_final; ?>"><?php echo $row_faltas['faltas_alunos_matricula_id']; ?></a>
              </td>
              <td><?php echo $row_faltas['aluno_nome']; ?></td>
              <td class="ls-txt-center"><?php echo idade($row_faltas['aluno_nascimento']); ?></td>
              <td class="ls-txt-center"><?php echo $row_faltas['escola_nome']; ?></td>
              <td class="ls-txt-center"><?php echo $row_faltas['turma_nome']; ?></td>
              <td class="ls-txt-center"><?php echo $row_faltas['turma_turno']; ?></td>
              <td class="ls-txt-center"><?php echo $row_faltas['total_faltas']; ?></td>
            </tr>
          <?php } while ($row_faltas = mysql_fetch_assoc($faltas)); ?>
        </tbody>
      </table>
    <?php } else { ?> 

      <strong>Nenhuma falta registrada. Escolha o período no filtro acima.</strong>

    <?php } ?>    

  </div>
  <?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="css/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="application/javascript">
</script>
<script type="text/javascript">
  $(function(){
    $(".buscar-aluno").keyup(function(){
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

<script>
  jQuery(document).ready(function($) {
 // Chamada da funcao upperText(); ao carregar a pagina
   upperText();
 // Funcao que faz o texto ficar em uppercase
   function upperText() {
// Para tratar o colar
     $("input").bind('paste', function(e) {
       var el = $(this);
       setTimeout(function() {
         var text = $(el).val();
         el.val(text.toUpperCase());
       }, 100);
     });

// Para tratar quando é digitado
     $("input").keypress(function() {
       var el = $(this);
       setTimeout(function() {
         var text = $(el).val();
         el.val(text.toUpperCase());
       }, 100);
     });
   }
 });
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
mysql_free_result($faltas);
?>