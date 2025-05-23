<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

$ANO_LETIVO = ANO_LETIVO;
$ID_PROFESSOR = ID_PROFESSOR;
// Configuração da paginação
$currentPage = $_SERVER["PHP_SELF"];
$maxRows_Logins = 200;
$pageNum_Logins = isset($_GET['pageNum_Logins']) ? intval($_GET['pageNum_Logins']) : 0;
$startRow_Logins = $pageNum_Logins * $maxRows_Logins;

// Consulta para obter os logins do professor
$query_Logins = "
        SELECT login_professor_id, login_professor_id_professor, login_professor_data_hora 
        FROM smc_login_professor 
        WHERE login_professor_id_professor = :professor_id
        ORDER BY login_professor_id DESC
        LIMIT :start_row, :max_rows
    ";

$stmt = $SmecelNovo->prepare($query_Logins);
$stmt->bindParam(':professor_id', $ID_PROFESSOR, PDO::PARAM_INT);
$stmt->bindParam(':start_row', $startRow_Logins, PDO::PARAM_INT);
$stmt->bindParam(':max_rows', $maxRows_Logins, PDO::PARAM_INT);
$stmt->execute();
$Logins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contagem total de logins
$query_count = "SELECT COUNT(*) as total FROM smc_login_professor WHERE login_professor_id_professor = :professor_id";
$stmt_count = $SmecelNovo->prepare($query_count);
$stmt_count->bindParam(':professor_id', $ID_PROFESSOR, PDO::PARAM_INT);
$stmt_count->execute();
$totalRows_Logins = $stmt_count->fetchColumn();

// Calculando total de páginas
$totalPages_Logins = ceil($totalRows_Logins / $maxRows_Logins) - 1;

// Montando a query string para paginação
$queryString_Logins = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = array_filter(explode("&", $_SERVER['QUERY_STRING']), function ($param) {
    return strpos($param, "pageNum_Logins") === false && strpos($param, "totalRows_Logins") === false;
  });
  if (count($params) > 0) {
    $queryString_Logins = "&" . htmlentities(implode("&", $params));
  }
}
$queryString_Logins = sprintf("&totalRows_Logins=%d%s", $totalRows_Logins, $queryString_Logins);

?>


<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
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
      <h1 class="ls-title-intro ls-ico-home">Lista de acessos</h1>
      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="logins_mapa.php"
          class="ls-btn ls-ico-calendar">MAPA DE ACESSOS</a></p>
      <table class="ls-table">
        <tr>
          <td>Data/Hora</td>
        </tr>
        <?php foreach ($Logins as $login): ?>
          <tr>
            <td>Acesso em <?php echo date("d/m/Y à\s H\hi", strtotime($login['login_professor_data_hora'])); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>

      <hr>

      <table class="ls-table">
        <tr>
          <td>
            <?php if ($pageNum_Logins > 0): ?>
              <a href="<?php echo sprintf("%s?pageNum_Logins=%d%s", $currentPage, 0, $queryString_Logins); ?>">Primeira
                página</a>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($pageNum_Logins > 0): ?>
              <a
                href="<?php echo sprintf("%s?pageNum_Logins=%d%s", $currentPage, max(0, $pageNum_Logins - 1), $queryString_Logins); ?>">Página
                Anterior</a>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($pageNum_Logins < $totalPages_Logins): ?>
              <a
                href="<?php echo sprintf("%s?pageNum_Logins=%d%s", $currentPage, min($totalPages_Logins, $pageNum_Logins + 1), $queryString_Logins); ?>">Próxima
                Página</a>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($pageNum_Logins < $totalPages_Logins): ?>
              <a
                href="<?php echo sprintf("%s?pageNum_Logins=%d%s", $currentPage, $totalPages_Logins, $queryString_Logins); ?>">Última
                Página</a>
            <?php endif; ?>
          </td>
        </tr>
      </table>


      <hr>
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
