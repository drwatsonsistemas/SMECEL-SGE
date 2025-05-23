<?php require_once('../../Connections/SmecelNovo.php'); ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$profQry = "";
$nomeProfessor = "0";
if (isset($_GET['professor'])) {
  $colname_Professor = $_GET['professor'];
  $profQry = " AND plano_aula_id_professor = $colname_Professor";
  $nomeProfessor = "1";	
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Aulas = "
SELECT plano_aula_id, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
plano_aula_data, plano_aula_texto, plano_aula_conteudo, plano_aula_video, plano_aula_atividade, plano_aula_publicado, plano_aula_hash,
turma_id, turma_nome, turma_id_escola, turma_ano_letivo,
disciplina_id, disciplina_nome,
func_id, func_nome
FROM smc_plano_aula
INNER JOIN smc_turma ON turma_id = plano_aula_id_turma 
INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
INNER JOIN smc_func ON func_id = plano_aula_id_professor
WHERE turma_id_escola = $row_EscolaLogada[escola_id] AND turma_ano_letivo = $row_AnoLetivo[ano_letivo_ano] 
$profQry
";
$Aulas = mysql_query($query_Aulas, $SmecelNovo) or die(mysql_error());
$row_Aulas = mysql_fetch_assoc($Aulas);
$totalRows_Aulas = mysql_num_rows($Aulas);
//AND (plano_aula_conteudo IS NOT NULL OR plano_aula_video IS NOT NULL OR plano_aula_atividade IS NOT NULL) 

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Professor = sprintf("SELECT func_id, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, func_senha, func_senha_ativa, func_carga_horaria_semanal FROM smc_func WHERE func_id_sec = '$row_EscolaLogada[escola_id_sec]' AND func_id = %s", GetSQLValueString($colname_Professor, "int"));
$Professor = mysql_query($query_Professor, $SmecelNovo) or die(mysql_error());
$row_Professor = mysql_fetch_assoc($Professor);
$totalRows_Professor = mysql_num_rows($Professor);

if ($totalRows_Professor == "") {
	//echo "TURMA EM BRANCO";	
	header("Location: turmaListar.php?nada"); 
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
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

<link href='../calendar/core/main.css' rel='stylesheet' />
<link href='../calendar/daygrid/main.css' rel='stylesheet' />
<script src='../calendar/core/main.js'></script>
<script src='../calendar/interaction/main.js'></script>
<script src='../calendar/daygrid/main.js'></script>
<script src='../calendar/core/main.js'></script>
<script src='../calendar/core/locales/pt-br.js'></script>

<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,dayGridDay,listWeek'
      },
	  defaultView: 'dayGridWeek',
	  locale: 'pt-BR',
      defaultDate: '<?php echo date('Y-m-d'); ?>',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
	  
	  
	  <?php do { ?>
	  

	  
	  
	  
	  {
          title: '<?php echo anti_injection($row_Aulas['plano_aula_texto']); ?> - <?php echo $row_Aulas['disciplina_nome']; ?>',
          start: '<?php echo $row_Aulas['plano_aula_data']; ?>',
		  url: 'aulas_virtuais_ver.php?aula=<?php echo $row_Aulas['plano_aula_hash']; ?>',
		  

        },
	  
	  <?php } while ($row_Aulas = mysql_fetch_assoc($Aulas)); ?>
	  
	  
      ],
	  eventClick: function(info) {
    info.jsEvent.preventDefault(); // don't let the browser navigate

    if (info.event.url) {
      window.open(info.event.url);
    }
  }
    });

    calendar.render();
  });

</script>
<style>

  #calendar {
    max-width: 900px;
    margin: 0 auto;
	font-size:12px;
  }

</style>

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">CALENDÁRIO DE AULAS</h1>
		<!-- CONTEÚDO -->

		
<?php if ($nomeProfessor == "1") { ?>

<div class="ls-box"><h2><?php echo "Professor(a): </h2><h1>".$row_Professor['func_nome']; ?></h1></div>

<?php } ?>

<p><a href="ava_aulas_professores.php" class="ls-btn-primary">Voltar</a></p>

<hr>
<div id='calendar'></div> 

		
		
		
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

mysql_free_result($Professor);

mysql_free_result($Aulas);

mysql_free_result($EscolaLogada);
?>
