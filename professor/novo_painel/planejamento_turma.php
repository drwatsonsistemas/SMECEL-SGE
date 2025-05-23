<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

$query = "";
if (isset($_GET['escola'])) {
    $escola_id = anti_injection($_GET['escola']);
    $order_clause = "ORDER BY (escola_id = " . intval($escola_id) . ") DESC, escola_nome ASC";
} else {
    $order_clause = "ORDER BY escola_nome ASC";
}

try {
    // Consultar escolas
    $stmtEscolas = $SmecelNovo->prepare("
        SELECT 
            ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, 
            ch_lotacao_obs, ch_lotacao_escola, escola_id, escola_nome, turma_id, turma_nome, turma_turno, turma_ano_letivo
        FROM smc_ch_lotacao_professor
        INNER JOIN smc_escola ON escola_id = ch_lotacao_escola 
        INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id 
        WHERE ch_lotacao_professor_id = :professor_id AND turma_ano_letivo = :ano_letivo
        GROUP BY escola_id
        $order_clause
    ");
    $stmtEscolas->execute([
        ':professor_id' => ID_PROFESSOR,
        ':ano_letivo' => ANO_LETIVO
    ]);
    $row_escolas = $stmtEscolas->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_escolas = $stmtEscolas->rowCount();

    // Consultar turmas se a escola estiver definida
    if (isset($_GET['escola'])) {
        $escola = anti_injection($_GET['escola']);
        $stmtTurmas = $SmecelNovo->prepare("
            SELECT 
                ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, ch_lotacao_dia, 
                ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, turma_id, turma_nome, turma_ano_letivo, turma_turno, 
                turma_etapa, escola_id, escola_nome,
                CASE turma_turno
                    WHEN 0 THEN 'INTEGRAL'
                    WHEN 1 THEN 'MATUTINO'
                    WHEN 2 THEN 'VESPERTINO'
                    WHEN 3 THEN 'NOTURNO'
                END AS turma_turno_nome 
            FROM smc_ch_lotacao_professor
            INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
            INNER JOIN smc_escola ON escola_id = :escola
            WHERE turma_ano_letivo = :ano_letivo AND ch_lotacao_escola = :escola AND ch_lotacao_professor_id = :professor_id
            GROUP BY turma_id
            ORDER BY turma_turno, turma_etapa, turma_nome ASC
        ");
        $stmtTurmas->execute([
            ':escola' => $escola,
            ':ano_letivo' => ANO_LETIVO,
            ':professor_id' => ID_PROFESSOR
        ]);
        $row_turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
        $totalRows_turmas = $stmtTurmas->rowCount();
    }
} catch (PDOException $e) {
    die("Erro ao consultar dados: " . $e->getMessage());
}
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
    <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
    <?php include_once "inc/navebar.php"; ?>
    <?php include_once "inc/sidebar.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
            <p><a href="planejamento_mapa.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>



            <div class="ls-box1">
                <hr>

                <div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
                    <a href="#" class="ls-btn-primary ls-btn-block ls-btn-lg" role="combobox" aria-expanded="false">
                        <?php
                        $escolaSelecionada = isset($_GET['escola']) ? array_filter($row_escolas, function ($e) {
                            return $e['escola_id'] == $_GET['escola'];
                        }) : null;

                        echo isset($_GET['escola']) && $escolaSelecionada
                            ? substr(current($escolaSelecionada)['escola_nome'], 0, 30)
                            : "UNIDADE ESCOLAR ({$totalRows_escolas})";
                        ?>

                    </a>
                    <ul class="ls-dropdown-nav" aria-hidden="true">
                        <?php foreach ($row_escolas as $escola) { ?>
                            <li>
                                <a href="planejamento_turma.php?escola=<?php echo $escola['escola_id']; ?>">
                                    <?php echo substr($escola['escola_nome'], 0, 33); ?>...
                                </a>
                            </li>
                        <?php } ?>
                        <li>
                            <a class="ls-color-danger ls-divider" href="planejamento.php">LIMPAR</a>
                        </li>
                    </ul>
                </div>

                <?php if (isset($_GET['escola'])) { ?>

                    <div data-ls-module="dropdown" class="ls-dropdown ls-label col-md-12 col-xs-12">
                        <a href="#" style="background-color:#06C;" class="ls-btn-primary ls-btn-block ls-btn-lg"
                            role="combobox" aria-expanded="false">
                            <?php
                            $turmaSelecionada = isset($_GET['turma']) ? array_filter($rowDisciplinasAC, function ($t) {
                                return $t['turma_id'] == $_GET['turma'];
                            }) : null;

                            echo isset($_GET['turma']) && $turmaSelecionada
                                ? substr(current($turmaSelecionada)['turma_nome'], 0, 30)
                                : "TURMAS ({$totalRows_turmas})";
                            ?>

                        </a>
                        <ul class="ls-dropdown-nav" aria-hidden="true">
                            <?php foreach ($row_turmas as $turma) { ?>
                                <li>
                                    <a
                                        href="planejamento_lancar_novo_turma.php?escola=<?php echo $escola_id; ?>&turma=<?php echo $turma['turma_id']; ?>&etapa=<?php echo $turma['turma_etapa']; ?>">
                                        <?php echo $turma['turma_nome']; ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="ls-color-danger ls-divider" href="planejamento.php">LIMPAR</a>
                            </li>
                        </ul>
                    </div>

                <?php } ?>
            </div>


        </div>
        <?php //include_once "inc/footer.php"; ?>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>
    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
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