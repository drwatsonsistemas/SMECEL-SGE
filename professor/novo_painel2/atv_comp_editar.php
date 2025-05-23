<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include 'conf/session.php'; ?>
<?php include 'fnc/anti_injection.php'; ?>
<?php

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= '?' . htmlentities($_SERVER['QUERY_STRING']);
}

// Tratamento de dados de GET
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
$pauta = isset($_GET['pauta']) ? anti_injection($_GET['pauta']) : "-1";

// Processando o formulário de atualização
if (isset($_POST['MM_update']) && $_POST['MM_update'] == 'form1') {
    // Tratamento de datas
    $pauta_data_inicial = isset($_POST['pauta_data_inicial']) ? date('Y-m-d', strtotime($_POST['pauta_data_inicial'])) : NULL;
    $pauta_data_final = isset($_POST['pauta_data_final']) ? date('Y-m-d', strtotime($_POST['pauta_data_final'])) : NULL;

    // Definindo a atividade
    $pauta_atividade = ($_POST['pauta_atividade'] != '0') ? $_POST['pauta_atividade'] : 99;
    $pauta_turno = $_POST['pauta_turno'];
    $pauta_atividade_outro = isset($_POST['pauta_atividade_outro']) ? $_POST['pauta_atividade_outro'] : '';

    // Atualizando a pauta
    $queryUpdate = "UPDATE smc_pauta SET pauta_data_inicial = :pauta_data_inicial, pauta_data_final = :pauta_data_final, 
                    pauta_atividade_outro = :pauta_atividade_outro, pauta_turno = :pauta_turno 
                    WHERE pauta_id = :pauta_id AND pauta_id_escola = :pauta_id_escola AND pauta_ano_letivo = :ano_letivo";
    $stmtUpdate = $SmecelNovo->prepare($queryUpdate);
    $stmtUpdate->bindParam(':pauta_data_inicial', $pauta_data_inicial, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':pauta_data_final', $pauta_data_final, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':pauta_atividade_outro', $pauta_atividade_outro, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':pauta_turno', $pauta_turno, PDO::PARAM_STR);
    $stmtUpdate->bindParam(':pauta_id', $pauta, PDO::PARAM_INT);
    $stmtUpdate->bindParam(':pauta_id_escola', $escola, PDO::PARAM_INT);
    $stmtUpdate->bindParam(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_STR);
    $stmtUpdate->execute();

    // Redirecionamento após atualização
    $insertGoTo = 'pauta.php?editado';
    if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? '&' : '?';
        $insertGoTo .= $_SERVER['QUERY_STRING'];
    }
    header('Location: ' . $insertGoTo);
}

// Consultas no banco de dados
// Consultando dados da pauta
$queryAC = "SELECT *, escola_id, escola_nome, pauta_atividade_id, pauta_descricao,
                    pauta_adc_atv_id, pauta_atv, pauta_id_pauta
            FROM smc_pauta
            LEFT JOIN smc_escola ON escola_id = pauta_id_escola
            INNER JOIN smc_pauta_adiciona_atv ON pauta_id_pauta = pauta_id
            INNER JOIN smc_pauta_atividades ON pauta_atividade_id = pauta_atv
            WHERE pauta_id = :pauta AND pauta_ano_letivo = :ano_letivo AND pauta_id_escola = :escola";
$stmtAC = $SmecelNovo->prepare($queryAC);
$stmtAC->bindParam(':pauta', $pauta, PDO::PARAM_INT);
$stmtAC->bindParam(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_STR);
$stmtAC->bindParam(':escola', $escola, PDO::PARAM_INT);
$stmtAC->execute();
$row_AC = $stmtAC->fetch(PDO::FETCH_ASSOC);
$totalRows_AC = $stmtAC->rowCount();

// Consultando atividades de pauta
$queryAtv = "SELECT * FROM smc_pauta_atividades ORDER BY pauta_descricao ASC";
$stmtAtv = $SmecelNovo->prepare($queryAtv);
$stmtAtv->execute();
$row_pautas = $stmtAtv->fetch(PDO::FETCH_ASSOC);
$totalRows_pautas = $stmtAtv->rowCount();

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
    <?php include_once 'inc/navebar.php'; ?>
    <?php include_once 'inc/sidebar.php'; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">ATIVIDADE COMPLEMENTAR</h1>
            <p><a href="pauta.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>


            <div class="ls-box ">
                <label class="ls-label col-md-12 ls-flex">
                    <b class="ls-label-text ls-text-lg ls-text-left">Pauta formativa/informativa:</b>
                </label>
                <br>
                <form method="post" name="form1" action="<?= $editFormAction ?>" class="ls-form">
                    <fieldset>

                        <label class="ls-label col-md-6 col-sm-12">
                            <b class="ls-label-text">Data inicial</b>
                            <div class="ls-prefix-group">
                                <input type="date" name="pauta_data_inicial"
                                    value="<?= $row_AC['pauta_data_inicial'] ?>" size="32" required>
                            </div>
                        </label>

                        <label class="ls-label col-md-6 col-sm-12">
                            <b class="ls-label-text">Data final</b>
                            <div class="ls-prefix-group">
                                <input type="date" name="pauta_data_final" value="<?= $row_AC['pauta_data_final'] ?>"
                                    size="32" required>
                            </div>
                        </label>

                        <label class="ls-label col-md-3 col-sm-12">
                            <b class="ls-label-text">Turno</b>
                            <div class="ls-custom-select">
                                <select class="ls-select" name="pauta_turno">
                                    <option value="MAT" <?php echo $row_AC['pauta_turno'] == "MAT" ? 'selected' : '' ?>>
                                        MATUTINO </option>
                                    <option value="VESP" <?php echo $row_AC['pauta_turno'] == "VESP" ? 'selected' : '' ?>>
                                        VESPERTINO </option>
                                    <option value="NOT" <?php echo $row_AC['pauta_turno'] == "NOT" ? 'selected' : '' ?>>
                                        NOTURNO </option>
                                </select>
                            </div>
                        </label>


                    </fieldset>

                    <br>
                    <fieldset>
                        <!-- Exemplo com Checkbox -->
                        <div class="ls-label col-md-12">
                            <p>Selecione a(s) atividades realizadas</p>
                            <?php
                            $pauta_atividade_outro = '';

                            // Verificar se há resultados antes de usar o loop
                            if ($totalRows_AC > 0) {
                                // Extrair o valor de pauta_atividade_outro (apenas o primeiro registro)
                                $pauta_atividade_outro = $row_AC['pauta_atividade_outro'];
                            }

                            // Recuperando todas as atividades de pauta
                            $stmtPautas = $SmecelNovo->prepare("SELECT * FROM smc_pauta_atividades ORDER BY pauta_descricao ASC");
                            $stmtPautas->execute();
                            $pautas = $stmtPautas->fetchAll(PDO::FETCH_ASSOC);

                            // Loop pelas atividades
                            foreach ($pautas as $row_pautas) {
                                $atividade_id = $row_pautas['pauta_atividade_id'];
                                $atividade_descricao = utf8_encode($row_pautas['pauta_descricao']);
                                $atividade_selecionada = ''; // Inicializa a variável de seleção como vazia
                            
                                // Verifica se a atividade atual está associada à pauta
                                if ($totalRows_AC > 0) {
                                    // Reseta o ponteiro para o início dos resultados de $AC
                                    $stmtAC->execute();
                                    $ac_atividades = $stmtAC->fetchAll(PDO::FETCH_ASSOC);

                                    // Percorre as atividades associadas à pauta
                                    foreach ($ac_atividades as $row_AC) {
                                        if ($row_AC['pauta_atividade_id'] == $atividade_id) {
                                            $atividade_selecionada = 'checked'; // Marca a atividade como selecionada
                                            break; // Não é necessário continuar verificando
                                        }
                                    }
                                }
                                ?>
                                <label class="ls-label-text">
                                    <input type="checkbox" onclick="adicionarAtividade(<?= $pauta ?>, <?= $atividade_id ?>)"
                                        value="<?= $atividade_id ?>" <?= $atividade_selecionada ?> name="pauta_atividade"
                                        class="ls-field-checkbox">
                                    <?= $atividade_descricao ?> <br>
                                </label>
                            <?php } ?>
                        </div>


                    </fieldset>
                    <label class="ls-label col-md-12 col-xs-12">
                        <b class="ls-label-text">Outra atividade:</b>
                        <textarea rows="1" name="pauta_atividade_outro"
                            class="ls-textarea-autoresize"><?= $pauta_atividade_outro ?></textarea>
                    </label>


                    <div class="ls-actions-btn">
                        <button class="ls-btn">Editar</button>
                        <input type="hidden" name="MM_update" value="form1" />
                    </div>
                </form>

            </div>




        </div>
        <?php // include_once "inc/footer.php"; ?>
    </main>
    <?php include_once 'inc/notificacoes.php'; ?>
    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script type="application/javascript">
        function adicionarAtividade(id_pauta, id_atividade) {
            $('input[type="checkbox"]').prop('disabled', true);
            jQuery.ajax({
                type: "POST",
                url: "crud/pauta/insert_atv.php",
                data: { id_pauta: id_pauta, id_atividade: id_atividade },
                cache: true,
                success: function (data) {
                    setTimeout(
                        function () {
                            $('input[type="checkbox"]').prop('disabled', false);
                        }, 1000);

                    console.log(data);
                }
            });
        }

    </script>

</body>

</html>