<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

<?php
$colname_Escola = "-1";
if (isset($_GET['escola'])) {
    $colname_Escola = anti_injection($_GET['escola']);
} else {
    //header("Location:chamada.php");
}

$colname_Target = "-1";
if (isset($_GET['target'])) {
    $colname_Target = anti_injection($_GET['target']);
} else {
    //header("Location:chamada.php");
}

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
    $colname_Turma = anti_injection($_GET['turma']);
} else {
    //header("Location:chamada.php");
}

$colname_Aula = "-1";
if (isset($_GET['turma'])) {
    $colname_Aula = anti_injection($_GET['turma']);
} else {
    //header("Location:chamada.php");
}

if (isset($_GET['data'])) {
    $data = anti_injection($_GET['data']);
    $semana = date("w", strtotime($data));
    $diasemana = array('DOMINGO', 'SEGUNDA', 'TERÇA', 'QUARTA', 'QUINTA', 'SEXTA', 'SÁBADO');
    $dia_semana_nome = $diasemana[$semana];
} else {
    //header("Location:chamada.php");
}

$todas = "s";
if (isset($_GET['todas'])) {
    $todas = anti_injection($_GET['todas']);
} else {
    $todas = "s";
}

// Query para Aula
$query_Aula = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, 
    ch_lotacao_turma_id, ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, 
    disciplina_id, disciplina_nome, turma_id, turma_nome, turma_turno,
    CASE turma_turno
    WHEN 0 THEN 'INTEGRAL'
    WHEN 1 THEN 'MATUTINO'
    WHEN 2 THEN 'VESPERTINO'
    WHEN 3 THEN 'NOTURNO'
    END AS turma_turno_nome
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    WHERE ch_lotacao_id = :aula_id";
$stmt_Aula = $SmecelNovo->prepare($query_Aula);
$stmt_Aula->bindParam(':aula_id', $colname_Aula, PDO::PARAM_INT);
$stmt_Aula->execute();
$Aula = $stmt_Aula->fetchAll(PDO::FETCH_ASSOC);
$row_Aula = reset($Aula);
$totalRows_Aula = count($Aula);

if ($totalRows_Aula == 0) {
    //header("Location:chamada.php");
}

// Query para Turma
$query_Turma = "
    SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, 
           turma_turno, turma_total_alunos, turma_ano_letivo, turma_parecer 
    FROM smc_turma 
    WHERE turma_id = :turma_id";
$stmt_Turma = $SmecelNovo->prepare($query_Turma);
$stmt_Turma->bindParam(':turma_id', $row_Aula['ch_lotacao_turma_id'], PDO::PARAM_INT);
$stmt_Turma->execute();
$Turma = $stmt_Turma->fetchAll(PDO::FETCH_ASSOC);
$row_Turma = reset($Turma);
$totalRows_Turma = count($Turma);

// Query para Vínculos
$query_Vinculos = "
    SELECT vinculo_id, vinculo_id_escola, vinculo_id_sec, vinculo_id_funcionario 
    FROM smc_vinculo 
    WHERE vinculo_id_escola = :escola_id AND vinculo_id_funcionario = :func_id";
$stmt_Vinculos = $SmecelNovo->prepare($query_Vinculos);
$stmt_Vinculos->execute([
    ':escola_id' => $row_Turma['turma_id_escola'],
    ':func_id' => $row_ProfLogado['func_id']
]);
$Vinculos = $stmt_Vinculos->fetchAll(PDO::FETCH_ASSOC);
$row_Vinculos = reset($Vinculos);
$totalRows_Vinculos = count($Vinculos);

// Query para Outras aulas
$query_Outras = "
    SELECT ch_lotacao_id, ch_lotacao_professor_id, ch_lotacao_disciplina_id, ch_lotacao_turma_id, 
           ch_lotacao_dia, ch_lotacao_aula, ch_lotacao_obs, ch_lotacao_escola, disciplina_id, 
           disciplina_nome, disciplina_nome_abrev, turma_id, turma_ano_letivo, turma_turno
    FROM smc_ch_lotacao_professor
    INNER JOIN smc_disciplina ON disciplina_id = ch_lotacao_disciplina_id
    INNER JOIN smc_turma ON turma_id = ch_lotacao_turma_id
    WHERE turma_ano_letivo = :ano_letivo 
    AND ch_lotacao_dia = :dia 
    AND ch_lotacao_professor_id = :professor_id
    ORDER BY turma_turno, ch_lotacao_aula ASC";
$stmt_Outras = $SmecelNovo->prepare($query_Outras);
$stmt_Outras->execute([
    ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano'],
    ':dia' => $row_Aula['ch_lotacao_dia'],
    ':professor_id' => $row_Vinculos['vinculo_id_funcionario']
]);
$Outras = $stmt_Outras->fetchAll(PDO::FETCH_ASSOC);
$row_Outras = reset($Outras);
$totalRows_Outras = count($Outras);

$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
    $colname_Alunos = anti_injection($_GET['turma']);
}

// Query para Alunos
$query_Alunos = "
    SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
           vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, 
           vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
           vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
           vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, 
           vinculo_aluno_conselho, vinculo_aluno_conselho_parecer, vinculo_aluno_dependencia, 
           aluno_id, aluno_nome, aluno_nascimento, aluno_foto, aluno_aluno_com_deficiencia, aluno_tipo_deficiencia,
           CASE vinculo_aluno_situacao
           WHEN 1 THEN 'MATRICULADO'
           WHEN 2 THEN 'TRANSFERIDO(A)'
           WHEN 3 THEN 'DESISTENTE'
           WHEN 4 THEN 'FALECIDO(A)'
           WHEN 5 THEN 'OUTROS'
           END AS vinculo_aluno_situacao_nome 
    FROM smc_vinculo_aluno
    INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
    WHERE vinculo_aluno_id_turma = :turma_id
    ORDER BY aluno_nome ASC";
$stmt_Alunos = $SmecelNovo->prepare($query_Alunos);
$stmt_Alunos->bindParam(':turma_id', $row_Turma['turma_id'], PDO::PARAM_INT);
$stmt_Alunos->execute();
$Alunos = $stmt_Alunos->fetchAll(PDO::FETCH_ASSOC);
$row_Alunos = reset($Alunos);
$totalRows_Alunos = count($Alunos);
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
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
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
            <h1 class="ls-title-intro ls-ico-home">FREQUÊNCIA</h1>

            <p>
                <a href="selecionar.php?escola=<?php echo $colname_Escola; ?>&target=frequencia&data=<?php echo $data; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a>
                <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary"><?php echo $dia_semana_nome; ?> (MUDAR DATA)</button>
                <a href="mapa_faltas.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=frequencia&data=<?php echo $data; ?>&todas=s&ct=<?php echo $row_Turma['turma_id']; ?>" class="ls-btn ls-ico-calendar">MAPA</a>
            </p>

            <div class="ls-box-filter">
                <h5 class="ls-title-5"><?php echo $row_Turma['turma_nome']; ?> - <?php echo $row_Aula['turma_turno_nome']; ?></h5>
                <p><strong><?php echo $row_Aula['disciplina_nome']; ?> - <?php echo date("d/m/y", strtotime($data)); ?> (<?php echo $dia_semana_nome; ?>)</strong></p>
            </div>

            <div class="ls-pagination-filter">
                <ul class="ls-pagination ls-float-left">
                    <?php foreach ($Outras as $row_Outras) { ?>
                        <li class="<?php if ($row_Outras['ch_lotacao_id'] == $row_Aula['ch_lotacao_id']) { ?>ls-active<?php } ?>">
                            <a href="frequencia.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $row_Outras['ch_lotacao_id']; ?>&target=<?php echo $colname_Target; ?>&data=<?php echo $data; ?>&todas=<?php echo $todas; ?>">
                                <?php echo $row_Outras['ch_lotacao_aula']; ?>ª
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <hr>

            <div class="ls-float-right">
                <div class="ls-group-btn ls-group-active">
                    <a href="frequencia.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=<?php echo $colname_Target; ?>&data=<?php echo $data; ?>&todas=s" 
                       class="ls-btn <?php if ($todas == "s") { echo "ls-active ls-ico-checkmark-circle ls-color-success"; } ?>">Marcar falta em todas</a>
                    <a href="frequencia.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=<?php echo $colname_Target; ?>&data=<?php echo $data; ?>&todas=n" 
                       class="ls-btn <?php if ($todas == "n") { echo "ls-active ls-ico-checkmark-circle ls-color-success"; } ?>">Somente a <?php echo $row_Aula['ch_lotacao_aula']; ?>ª aula</a>
                </div>
                <small>*a exclusão da falta é individual</small>
            </div>

            <table class="ls-table">
                <thead>
                    <tr>
                        <th width="80" class="ls-txt-center"></th>
                        <th>ALUNOS</th>
                        <th width="100" class="ls-txt-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $num = 1;
                    foreach ($Alunos as $row_Alunos) { 
                        // Query para Verifica
                        $query_Verifica = "
                            SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, 
                                   faltas_alunos_numero_aula, faltas_alunos_justificada, faltas_alunos_data 
                            FROM smc_faltas_alunos 
                            WHERE faltas_alunos_matricula_id = :matricula_id 
                            AND faltas_alunos_data = :data 
                            AND faltas_alunos_numero_aula = :aula_numero";
                        $stmt_Verifica = $SmecelNovo->prepare($query_Verifica);
                        $stmt_Verifica->execute([
                            ':matricula_id' => $row_Alunos['vinculo_aluno_id'],
                            ':data' => $data,
                            ':aula_numero' => $row_Aula['ch_lotacao_aula']
                        ]);
                        $Verifica = $stmt_Verifica->fetchAll(PDO::FETCH_ASSOC);
                        $row_Verifica = reset($Verifica);
                        $totalRows_Verifica = count($Verifica);
                    ?>
                        <tr>
                            <td class="ls-txt-left">
                                <a href="mapa_faltas_individual.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=frequencia&data=<?php echo $data; ?>&todas=s&ct=<?php echo $row_Turma['turma_id']; ?>&aluno=<?php echo $row_Alunos['vinculo_aluno_id']; ?>">
                                    <span>
                                        <?php if ($row_Alunos['aluno_foto'] == "") { ?>
                                            <img src="<?php echo URL_BASE.'/aluno/fotos/' ?>semfoto.jpg" class="aluno" border="0" width="100%">
                                        <?php } else { ?>
                                            <img src="<?php echo URL_BASE.'/aluno/fotos/' ?><?php echo $row_Alunos['aluno_foto']; ?>" class="hoverable aluno" border="0" width="100%">
                                        <?php } ?>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <small>
                                    <?php echo $row_Alunos['aluno_nome']; ?>
                                    <?php if ($row_Alunos['vinculo_aluno_situacao'] != "1") { ?>
                                        <br><span class="ls-color-danger"><?php echo $row_Alunos['vinculo_aluno_situacao_nome']; ?></span>
                                    <?php } ?>
                                    <?php if ($row_Verifica['faltas_alunos_justificada'] == "S") { ?>
                                        <br><span class="ls-color-info">JUSTIFICADA</span>
                                    <?php } ?>
                                    <?php if ($row_Alunos['vinculo_aluno_dependencia'] == "S") { ?>
                                        <br><span class="ls-color-danger">CUMPRINDO DEPENDÊNCIA NA TURMA</span>
                                    <?php } ?>
                                </small>
                                <a href="mapa_faltas_individual.php?escola=<?php echo $colname_Escola; ?>&turma=<?php echo $colname_Turma; ?>&target=frequencia&data=<?php echo $data; ?>&todas=s&ct=<?php echo $row_Turma['turma_id']; ?>&aluno=<?php echo $row_Alunos['vinculo_aluno_id']; ?>" class="ls-ico-eye ls-ico-right"></a>
                                <?php if ($row_Alunos['aluno_aluno_com_deficiencia'] == "1") { ?>
                                    <br><br>
                                    <img src="../../img/pne.png" width="15px" style="cursor:pointer" alt="Aluno(a) com necessidades especiais" title="Aluno(a) com necessidades especiais: <?php echo $row_Alunos['aluno_tipo_deficiencia']; ?>">
                                <?php } ?>
                            </td>
                            <td class="center card-panel1">
                                <div data-ls-module="switchButton" class="ls-switch-btn ls-float-right">
                                    <input type="checkbox" 
                                           id="<?php echo $row_Alunos['vinculo_aluno_id']; ?>" 
                                           dia="<?php echo $row_Aula['ch_lotacao_dia']; ?>" 
                                           turma="<?php echo $row_Alunos['vinculo_aluno_id_turma']; ?>" 
                                           ano="<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>" 
                                           prof="<?php echo $row_ProfLogado['func_id']; ?>" 
                                           aluno="<?php echo $row_Alunos['aluno_nome']; ?>" 
                                           data="<?php echo $data; ?>" 
                                           matricula="<?php echo $row_Alunos['vinculo_aluno_id']; ?>" 
                                           disciplina="<?php echo $row_Aula['disciplina_id']; ?>" 
                                           aula_numero="<?php echo $row_Aula['ch_lotacao_aula']; ?>" 
                                           multi="<?php echo $todas; ?>" 
                                           <?php if ($row_Alunos['vinculo_aluno_situacao'] != "1" || $row_Verifica['faltas_alunos_justificada'] == "S") { ?> disabled <?php } else { ?> <?php if ($totalRows_Verifica == 0) { echo "checked"; } ?> <?php } ?>>
                                    <label class="ls-switch-label" 
                                           for="<?php echo $row_Alunos['vinculo_aluno_id']; ?>" 
                                           name="label-<?php echo $row_Alunos['vinculo_aluno_id']; ?>" 
                                           ls-switch-off="<?php if ($row_Verifica['faltas_alunos_justificada'] == "S") { ?>Justificada<?php } else if ($row_Alunos['vinculo_aluno_situacao'] != "1") { ?>Transf.<?php } else { ?>Ausente<?php } ?>" 
                                           ls-switch-on="Presente"><span></span></label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div id="status"></div>
            <hr>
        </div>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>

    <div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
            <div class="ls-modal-header">
                <button data-dismiss="modal">×</button>
                <h4 class="ls-modal-title">ESCOLHA UMA DATA</h4>
            </div>
            <div class="ls-modal-body" id="myModalBody">
                <p>
                    <form action="frequencia.php" class="ls-form">
                        <input type="hidden" name="escola" value="<?php echo $colname_Escola; ?>">
                        <input type="hidden" name="turma" value="<?php echo $colname_Turma; ?>">
                        <input type="hidden" name="target" value="<?php echo $colname_Target; ?>">
                        <label class="ls-label col-md-12 col-xs-12"> 
                            <b class="ls-label-text">DATA</b>
                            <input type="date" name="data" class="" id="data" value="<?php echo $data; ?>" autocomplete="off" onchange="this.form.submit()">
                        </label>
                        <input type="hidden" name="alterada" value="true">
                    </form>
                </p>
            </div>
            <div class="ls-modal-footer">
                <button type="" class="ls-btn ls-btn-primary ls-btn-block" data-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("input[type='checkbox']").on('click', function() {
                var multi = $(this).attr('multi');
                var matricula = $(this).attr('matricula');
                var aula_numero = $(this).attr('aula_numero');
                var data = $(this).attr('data');
                var disciplina = $(this).attr('disciplina');
                var aluno = $(this).attr('aluno');
                var prof = $(this).attr('prof');
                var ano = $(this).attr('ano');
                var turma = $(this).attr('turma');
                var dia = $(this).attr('dia');

                if ($(this).prop('checked')) {
                    $.ajax({
                        type: 'POST',
                        url: 'crud/alunos_frequencia/frequencia.php',
                        data: {
                            matricula: matricula,
                            aula_numero: aula_numero,
                            data: data,
                            aluno: aluno,
                            disciplina: disciplina,
                            multi: multi,
                            prof: prof,
                            ano: ano,
                            turma: turma,
                            dia: dia
                        },
                        success: function(data) {
                            $('#status').html(data);
                            setTimeout(function() {
                                $("#status").html("");
                            }, 5000);
                        }
                    });
                    return true;
                }

                $.ajax({
                    type: 'POST',
                    url: 'crud/alunos_frequencia/frequencia.php',
                    data: {
                        matricula: matricula,
                        aula_numero: aula_numero,
                        data: data,
                        aluno: aluno,
                        disciplina: disciplina,
                        multi: multi,
                        prof: prof,
                        ano: ano,
                        turma: turma,
                        dia: dia
                    },
                    success: function(data) {
                        $('#status').html(data);
                        setTimeout(function() {
                            $("#status").html("");
                        }, 2000);
                    }
                });
                return true;
            });
        });
    </script>
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
    <?php if (isset($_GET["alterada"])) { ?>
        <script type="application/javascript">
            Swal.fire({
                icon: 'success',
                title: 'DATA ALTERADA',
                text: '<?php echo $dia_semana_nome; ?> - <?php echo date("d/m/y", strtotime($data)); ?> ',
                showConfirmButton: false,
                timer: 2500
            })
        </script>
    <?php } ?>
</body>
</html>