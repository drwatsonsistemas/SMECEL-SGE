<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";
include "../../sistema/escola/fnc/exibeHorarioSecretariaProfessor.php";

$ANO_LETIVO = ANO_LETIVO;
$ID_PROFESSOR = ID_PROFESSOR;
// Preparando a consulta
$query_Turmas = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola,
    turma_id, turma_nome, turma_turno, turma_ano_letivo, turma_matriz_id,
    CASE turma_turno
    WHEN 0 THEN 'INTEGRAL'
    WHEN 1 THEN 'MATUTINO'
    WHEN 2 THEN 'VESPERTINO'
    WHEN 3 THEN 'NOTURNO'
    END AS turma_turno_nome  
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
    GROUP BY turma_id
    ORDER BY turma_turno, turma_nome";

// Preparando a query para execução com parâmetros
$stmt = $SmecelNovo->prepare($query_Turmas);

// Vinculando os parâmetros da consulta
$stmt->bindParam(':professor_id', $ID_PROFESSOR, PDO::PARAM_INT);
$stmt->bindParam(':ano_letivo', $ANO_LETIVO, PDO::PARAM_INT);

// Executando a consulta
$stmt->execute();

// Recuperando os resultados
$row_Turmas = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows_Turmas = $stmt->rowCount();  // Obtendo o número de linhas retornadas

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
      <h1 class="ls-title-intro ls-ico-home">GRADE</h1>
      <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a> <a href="grade_analitica.php"
          class="ls-btn">Grade analítica</a></p>
      <hr>
      <?php if ($totalRows_Turmas > 0) { // Show if recordset not empty ?>

        <?php
        // Prepara o loop para cada turma
        do {
          // Exibe o nome da turma e turno
          echo "<h3>{$row_Turmas['turma_nome']} - {$row_Turmas['turma_turno_nome']}</h3>";

          // Preparando a consulta para buscar a matriz da turma
          $query_matriz = "
        SELECT 
            matriz_id, matriz_anoletivo, matriz_aula_dia, matriz_dias_semana
        FROM smc_matriz
        WHERE matriz_id = :turma_matriz_id
    ";
          $stmt = $SmecelNovo->prepare($query_matriz);
          $stmt->bindParam(':turma_matriz_id', $row_Turmas['turma_matriz_id'], PDO::PARAM_INT);
          $stmt->execute();

          $row_matriz = $stmt->fetch(PDO::FETCH_ASSOC);

          // Checando se a matriz existe para a turma
          if ($row_matriz) {
            // Inicia a tabela para exibir os horários
            echo '<table class="ls-table ls-no-hover ls-table-striped ls-table-bordered ls-bg-header">
                <thead>
                    <tr>
                        <th class="ls-txt-center" width="50px"></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">SEGUNDA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEG</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">TERÇA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">TER</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">QUARTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUA</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">QUINTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">QUI</span></th>
                        <th class="ls-txt-center"><span class="ls-display-none-xs">SEXTA</span><span class="ls-display-none-sm ls-display-none-md ls-display-none-lg">SEX</span></th>
                    </tr>
                </thead>
                <tbody>';

            // Loop para exibir as aulas para cada dia da semana
            for ($a = 1; $a <= $row_matriz['matriz_aula_dia']; $a++) {
              echo "<tr><td class='ls-txt-center'>{$a}</td>";

              // Exibe os horários para cada dia da semana (1-5 representando os dias)
              for ($dia = 1; $dia <= 5; $dia++) {
                echo "<td class='ls-txt-center'>";
                echo exibeHorario($row_Turmas['turma_id'], $dia, $a, ID_PROFESSOR);
                echo "</td>";
              }

              echo "</tr>";
            }

            // Finaliza a tabela
            echo '</tbody></table>';
          }
        } while ($row_Turmas = $stmt->fetch(PDO::FETCH_ASSOC));
        ?>


      <?php } ?>












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
