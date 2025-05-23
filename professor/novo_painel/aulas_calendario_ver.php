<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php";
include "fnc/anti_injection.php";

try {
    // Consulta para buscar todas as aulas
    $query_TodasAulas = "
        SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, 
               plano_aula_data, plano_aula_texto, plano_aula_publicado, plano_aula_hash, disciplina_id, disciplina_nome, disciplina_cor_fundo 
        FROM smc_plano_aula
        INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
        WHERE plano_aula_id_professor = :professor_id
        ORDER BY plano_aula_data DESC";

    // Preparar a consulta
    $stmt = $SmecelNovo->prepare($query_TodasAulas);
    $stmt->bindValue(':professor_id', ID_PROFESSOR, PDO::PARAM_INT);
    $stmt->execute();

    // Buscar os resultados
    $TodasAulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar as aulas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-117872281-1');
</script>
<title>PROFESSOR | <?php echo htmlspecialchars($row_ProfLogado['func_nome'], ENT_QUOTES, 'UTF-8'); ?> | SMECEL - Sistema de Gestão Escolar</title>
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
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
<script src='<?php echo URL_BASE; ?>sistema/js/pt-br.js'></script>

<script>
$(document).ready(function() {
    // Obtém a data atual no formato ISO 8601
    const currentDate = moment().format('YYYY-MM-DD'); // Usa Moment.js para maior consistência

    $('#aulas_calendario').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek'
        },
        views: {
            listWeek: {buttonText: 'Agenda'}
        },
        defaultDate: currentDate, // Define a data padrão no formato correto
        navLinks: true, // Permite navegação por links de dias/semana
        editable: false, // Eventos não editáveis
        eventLimit: true, // Permite link "mais" para eventos excedentes
        events: [
            <?php foreach ($TodasAulas as $aula): ?>
            {
                title: '<?php echo addslashes($aula['disciplina_nome']); ?>', // Escapa caracteres especiais
                start: '<?php echo $aula['plano_aula_data']; ?>', // Certifique-se de que está no formato YYYY-MM-DD
                end: '<?php echo $aula['plano_aula_data']; ?>', // Certifique-se de que está no formato YYYY-MM-DD
                color: '<?php echo $aula['disciplina_cor_fundo']; ?>' // Cor de fundo do evento
            },
            <?php endforeach; ?>
        ]
    });
});
</script>

<style>
  #aulas_calendario {
      font-size: small;  
      max-width: 100%;
      margin: 0 auto;
  }
</style>
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">MAPA DE AULAS</h1>
    <p>
        <a href="aulas_calendario.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
    </p>
    <hr>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div id='aulas_calendario'></div>
            <p>&nbsp;</p>  
        </div>
    </div>
  </div>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="js/locastyle.js"></script>
<script src="js/sweetalert2.min.js"></script>
</body>
</html>
