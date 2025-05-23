<?php
require_once('../../Connections/SmecelNovoPDO.php');
include('fnc/inverteData.php');
include "fnc/sessionPDO.php";
include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Processar a atualização do formulário
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    if ($row_UsuLogado['usu_update'] == "N") {
        header("Location: vinculoAlunoExibirTurma.php?permissao");
        exit;
    }

    // Preparar dados do formulário
    $idTurma = (int)$_POST['vinculo_aluno_id_turma'];
    $vinculo_aluno_nao_reprova = (isset($_POST['vinculo_aluno_nao_reprova']) && $_POST['vinculo_aluno_nao_reprova'] == "on") ? "S" : "N";
    $vinculo_aluno_saida = isset($_POST['vinculo_aluno_saida']) ? (int)$_POST['vinculo_aluno_saida'] : 0;
    if (!in_array($vinculo_aluno_saida, [0, 1, 2])) {
        $vinculo_aluno_saida = 0;
    }
    $vinculo_aluno_datatransferencia = ($_POST['vinculo_aluno_situacao'] == 1) ? null : $_POST['vinculo_aluno_datatransferencia'];

    // Atualizar smc_vinculo_aluno
    $updateSQL = "
        UPDATE smc_vinculo_aluno 
        SET 
            vinculo_aluno_id_turma = :id_turma, 
            vinculo_aluno_situacao = :situacao, 
            vinculo_aluno_transporte = :transporte, 
            vinculo_aluno_ponto_id = :ponto_id, 
            vinculo_aluno_multietapa = :multietapa, 
            vinculo_aluno_datatransferencia = :datatransferencia, 
            vinculo_aluno_data = :data, 
            vinculo_aluno_internet = :internet, 
            vinculo_aluno_vacina_atualizada = :vacina_atualizada, 
            vinculo_aluno_dependencia = :dependencia, 
            vinculo_aluno_repetente = :repetente, 
            vinculo_aluno_da_casa = :da_casa, 
            vinculo_aluno_historico_transferencia = :historico_transferencia, 
            vinculo_aluno_id_cuidador = :id_cuidador, 
            vinculo_aluno_id_matriz_multi = :id_matriz_multi, 
            vinculo_aluno_nao_reprova = :nao_reprova, 
            vinculo_aluno_saida = :saida
        WHERE vinculo_aluno_id = :id";

    $stmt = $SmecelNovo->prepare($updateSQL);
    $stmt->execute([
        ':id_turma' => (int)$_POST['vinculo_aluno_id_turma'],
        ':situacao' => $_POST['vinculo_aluno_situacao'],
        ':transporte' => $_POST['vinculo_aluno_transporte'],
        ':ponto_id' => (int)$_POST['vinculo_aluno_ponto_id'],
        ':multietapa' => (int)$_POST['vinculo_aluno_multietapa'],
        ':datatransferencia' => $vinculo_aluno_datatransferencia,
        ':data' => $_POST['vinculo_aluno_data'],
        ':internet' => $_POST['vinculo_aluno_internet'],
        ':vacina_atualizada' => $_POST['vinculo_aluno_vacina_atualizada'],
        ':dependencia' => $_POST['vinculo_aluno_dependencia'],
        ':repetente' => $_POST['vinculo_aluno_repetente'],
        ':da_casa' => $_POST['vinculo_aluno_da_casa'],
        ':historico_transferencia' => $_POST['vinculo_aluno_historico_transferencia'],
        ':id_cuidador' => (int)$_POST['vinculo_aluno_id_cuidador'],
        ':id_matriz_multi' => (int)$_POST['vinculo_aluno_matriz_multietapa'],
        ':nao_reprova' => $vinculo_aluno_nao_reprova,
        ':saida' => $vinculo_aluno_saida,
        ':id' => (int)$_POST['vinculo_aluno_id']
    ]);

    // Registrar log na tabela smc_registros
    // Registrar log na tabela smc_registros
$usu = (int)$_POST['usu_id'];
$esc = (int)$_POST['usu_escola'];
$detalhes = $_POST['detalhes'];
$situacao1 = $_POST['vinculo_aluno_situacao'];

switch ($situacao1) {
    case '1':
        $situacao = 'MATRICULADO';
        break;
    case '2':
        $situacao = 'TRANSFERIDO';
        break;
    case '3':
        $situacao = 'DEIXOU DE FREQUENTAR';
        break;
    case '4':
        $situacao = 'FALECIDO';
        break;
    case '5':
        $situacao = 'OUTROS';
        break;
    default:
        $situacao = 'DESCONHECIDO';
        break;
}

date_default_timezone_set('America/Bahia');
$dat = date('Y-m-d H:i:s');

$logSQL = "
    INSERT INTO smc_registros (
        registros_id_escola, registros_id_usuario, registros_tipo, registros_complemento, registros_data_hora
    ) VALUES (
        :id_escola, :id_usuario, :tipo, :complemento, :data_hora
    )";
$stmtLog = $SmecelNovo->prepare($logSQL);
$stmtLog->execute([
    ':id_escola' => $esc,
    ':id_usuario' => $usu,
    ':tipo' => 16,
    ':complemento' => "($detalhes SITUACAO: $situacao)",
    ':data_hora' => $dat
]);
    // Redirecionar após sucesso
    $updateGoTo = "matriculaExibe.php?vinculoEditado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header("Location: $updateGoTo");
    exit;
}

// Consultas usando PDO
try {
    // Escola Logada
    $query_EscolaLogada = "
        SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
               escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, 
               sec_id, sec_cidade, sec_uf 
        FROM smc_escola
        INNER JOIN smc_sec ON sec_id = escola_id_sec 
        WHERE escola_id = :escola_id";
    $stmtEscolaLogada = $SmecelNovo->prepare($query_EscolaLogada);
    $stmtEscolaLogada->execute([':escola_id' => $row_UsuLogado['usu_escola']]);
    $row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
    $totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();

    // Vínculo a Editar
    $colname_VinculoEditar = isset($_GET['cmatricula']) ? $_GET['cmatricula'] : "-1";
    $query_VinculoEditar = "
        SELECT 
            vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
            vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_ponto_id, vinculo_aluno_data, 
            vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao,
            vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_datatransferencia, 
            vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_nao_reprova, vinculo_aluno_dependencia, 
            vinculo_aluno_repetente, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
            vinculo_aluno_id_cuidador, vinculo_aluno_id_matriz_multi, vinculo_aluno_saida,
            aluno_id, aluno_nome, turma_id, turma_nome, turma_multisseriada 
        FROM smc_vinculo_aluno 
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
        INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
        WHERE vinculo_aluno_hash = :hash";
    $stmtVinculoEditar = $SmecelNovo->prepare($query_VinculoEditar);
    $stmtVinculoEditar->execute([':hash' => $colname_VinculoEditar]);
    $row_VinculoEditar = $stmtVinculoEditar->fetch(PDO::FETCH_ASSOC);
    $totalRows_VinculoEditar = $stmtVinculoEditar->rowCount();

    if ($totalRows_VinculoEditar == 0) {
        header("Location: turmasAlunosVinculados.php?nada");
        exit;
    }

    // Turmas
    $query_Turmas = "
        SELECT 
            turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
            CASE turma_turno
                WHEN 0 THEN 'INTEGRAL'
                WHEN 1 THEN 'MATUTINO'
                WHEN 2 THEN 'VESPERTINO'
                WHEN 3 THEN 'NOTURNO'
            END AS turma_turno_nome 
        FROM smc_turma 
        WHERE turma_ano_letivo = :ano_letivo AND turma_id_escola = :escola_id
        ORDER BY turma_turno, turma_etapa, turma_nome ASC";
    $stmtTurmas = $SmecelNovo->prepare($query_Turmas);
    $stmtTurmas->execute([
        ':ano_letivo' => $row_VinculoEditar['vinculo_aluno_ano_letivo'],
        ':escola_id' => $row_UsuLogado['usu_escola']
    ]);
    $row_Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_Turmas = $stmtTurmas->rowCount();

    // Pontos
    $query_Pontos = "
        SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, 
               te_ponto_latitude, te_ponto_longitude, te_ponto_obs 
        FROM smc_te_ponto 
        WHERE te_ponto_id_sec = :sec_id 
        ORDER BY te_ponto_descricao ASC";
    $stmtPontos = $SmecelNovo->prepare($query_Pontos);
    $stmtPontos->execute([':sec_id' => $row_UsuLogado['usu_sec']]);
    $row_Pontos = $stmtPontos->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_Pontos = $stmtPontos->rowCount();

    // Etapa
    $query_Etapa = "
        SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev 
        FROM smc_etapa";
    $stmtEtapa = $SmecelNovo->prepare($query_Etapa);
    $stmtEtapa->execute();
    $row_Etapa = $stmtEtapa->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_Etapa = $stmtEtapa->rowCount();

    // Matriz
    $query_Matriz = "
        SELECT matriz_id, matriz_id_secretaria, matriz_nome, matriz_ativa 
        FROM smc_matriz 
        WHERE matriz_ativa = 'S' AND matriz_id_secretaria = :sec_id";
    $stmtMatriz = $SmecelNovo->prepare($query_Matriz);
    $stmtMatriz->execute([':sec_id' => $row_EscolaLogada['sec_id']]);
    $row_Matriz = $stmtMatriz->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_Matriz = $stmtMatriz->rowCount();

    // Lista de Vínculos (Cuidadores)
    $query_ListaVinculos = "
        SELECT 
            vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, 
            vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
            func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa 
        FROM smc_vinculo 
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE vinculo_id_escola = :escola_id AND vinculo_status = 1
        ORDER BY func_nome ASC";
    $stmtListaVinculos = $SmecelNovo->prepare($query_ListaVinculos);
    $stmtListaVinculos->execute([':escola_id' => $row_EscolaLogada['escola_id']]);
    $row_ListaVinculos = $stmtListaVinculos->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_ListaVinculos = $stmtListaVinculos->rowCount();

} catch (PDOException $e) {
    die("Erro ao executar consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html class="<?php echo htmlspecialchars($row_EscolaLogada['escola_tema']); ?>" lang="pt-br">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
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
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <link rel="stylesheet" type="text/css" href="css/preloader.css">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body onload="javascript:mudarTurmaNao()">
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>
    <main class="ls-main">
        <div class="container-fluid">
            <p>&nbsp;</p>
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

    <div class="ls-modal" data-modal-blocked id="modalLarge" style="top:-150px;">
        <div class="ls-modal-large">
            <div class="ls-modal-header">
                <button data-dismiss="modal">&times;</button>
                <h4 class="ls-modal-title">
                    <strong><?php echo htmlspecialchars($row_VinculoEditar['aluno_nome']); ?> - <?php echo htmlspecialchars($row_VinculoEditar['turma_nome']); ?></strong><br>
                    Matrícula realizada em <?php echo date("d/m/Y", strtotime($row_VinculoEditar['vinculo_aluno_data'])); ?>
                </h4>
            </div>
            <div class="ls-modal-body">
                <form method="post" name="form1" action="<?php echo htmlspecialchars($editFormAction); ?>" class="ls-form ls-form-horizontal row">
                    <fieldset>
                        <label class="ls-label col-md-12 ls-txt-right">
                            <div class="ls-box ls-xs-space">
                                <b class="ls-label-text"><small>MUDAR ALUNO DE TURMA?</small></b>
                                <label class="ls-label-text">
                                    <input type="radio" id="mudandodeturma" name="mudandodeturma" value="1" onclick="javascript:mudarTurmaNao();" checked>
                                    <small>NÃO</small>
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" id="mudandodeturma" name="mudandodeturma" value="2" onclick="javascript:mudarTurmaSim();">
                                    <small>SIM</small>
                                </label>
                            </div>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_id_turma">
                            <b class="ls-label-text">TURMA</b>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_id_turma" id="vinculo_aluno_id_turma" class="ls-select" required>
                                    <?php foreach ($row_Turmas as $turma) { ?>
                                        <option value="<?php echo htmlspecialchars($turma['turma_id']); ?>" 
                                                <?php if ($turma['turma_id'] == $row_VinculoEditar['vinculo_aluno_id_turma']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($turma['turma_nome'] . ' ' . $turma['turma_turno_nome'] . ' (' . $turma['turma_ano_letivo'] . ')'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </label>
                        <label class="ls-label col-md-12 vinculo_aluno_data">
                            <b class="ls-label-text">DATA DA MATRÍCULA</b>
                            <input type="date" name="vinculo_aluno_data" id="vinculo_aluno_data" class="ls-field-lg date1" 
                                   value="<?php echo htmlspecialchars($row_VinculoEditar['vinculo_aluno_data']); ?>" size="32">
                        </label>
                        <label class="ls-label col-md-12 vinculo_aluno_situacao">
                            <b class="ls-label-text">SITUAÇÃO</b>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_situacao" id="vinculo_aluno_situacao">
                                    <option value="1" <?php if ($row_VinculoEditar['vinculo_aluno_situacao'] == 1) echo "selected"; ?>>MATRICULADO</option>
                                    <option value="2" <?php if ($row_VinculoEditar['vinculo_aluno_situacao'] == 2) echo "selected"; ?>>TRANSFERIDO</option>
                                    <option value="3" <?php if ($row_VinculoEditar['vinculo_aluno_situacao'] == 3) echo "selected"; ?>>DEIXOU DE FREQUENTAR</option>
                                    <option value="4" <?php if ($row_VinculoEditar['vinculo_aluno_situacao'] == 4) echo "selected"; ?>>FALECIDO</option>
                                    <option value="5" <?php if ($row_VinculoEditar['vinculo_aluno_situacao'] == 5) echo "selected"; ?>>OUTROS</option>
                                </select>
                            </div>
                        </label>
                        <div id="data_ocorrencia" class="ls-display-none">
                            <label class="ls-label col-md-12 vinculo_aluno_datatransferencia">
                                <b class="ls-label-text">DATA DA OCORRÊNCIA (transferência, desistência, falecimento etc.)</b>
                                <input type="date" name="vinculo_aluno_datatransferencia" id="vinculo_aluno_datatransferencia" 
                                       class="ls-field-lg date1" 
                                       value="<?php echo $row_VinculoEditar['vinculo_aluno_datatransferencia'] ?: date("Y-m-d"); ?>" size="32">
                            </label>
                        </div>
                        <label class="ls-label col-sm-12 vinculo_aluno_internet1">
                            <b class="ls-label-text">Aluno repetente nessa etapa de ensino?</b> <br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_repetente" value="S" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_repetente'] == "S") echo "checked"; ?> />
                                    SIM
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_repetente" value="N" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_repetente'] == "N") echo "checked"; ?> />
                                    NÃO
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_internet1">
                            <b class="ls-label-text">Aluno(a) possui acesso à internet?</b> <br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_internet" value="S" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_internet'] == "S") echo "checked"; ?> />
                                    SIM
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_internet" value="N" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_internet'] == "N") echo "checked"; ?> />
                                    NÃO
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12">
                            <b class="ls-label-text">ALUNO MATRICULADO É DA ESCOLA (DA CASA) OU DE OUTRA ESCOLA/CIDADE (DE FORA)</b> <br><br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_da_casa" value="C" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_da_casa'] == "C") echo "checked"; ?> />
                                    DA CASA
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_da_casa" value="F" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_da_casa'] == "F") echo "checked"; ?> />
                                    DE FORA
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12" id="historico">
                            <b class="ls-label-text">TRANSFERIDO COM HISTÓRICO OU DECLARAÇÃO</b><br><br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_historico_transferencia" value="H" 
                                           id="vinculo_aluno_historico_transferencia_h" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_historico_transferencia'] == "H") echo "checked"; ?> />
                                    HISTÓRICO
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_historico_transferencia" value="D" 
                                           id="vinculo_aluno_historico_transferencia_d" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_historico_transferencia'] == "D") echo "checked"; ?> />
                                    DECLARAÇÃO
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_transporte1">
                            <b class="ls-label-text">UTILIZA TRANSPORTE ESCOLAR?</b><br>
                            <label class="ls-label-text">
                                <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" 
                                       onclick="javascript:transporte_sim();" value="S" 
                                       <?php if ($row_VinculoEditar['vinculo_aluno_transporte'] == "S") echo "checked"; ?>>
                                Sim
                            </label>
                            <label class="ls-label-text">
                                <input type="radio" name="vinculo_aluno_transporte" id="vinculo_aluno_transporte" 
                                       onclick="javascript:transporte_nao();" value="N" 
                                       <?php if ($row_VinculoEditar['vinculo_aluno_transporte'] == "N") echo "checked"; ?>>
                                Não
                            </label>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_ponto_id" id="ponto" 
                               style="<?php echo ($row_VinculoEditar['vinculo_aluno_transporte'] == "N") ? 'display:none' : 'display:block'; ?>">
                            <b class="ls-label-text">PONTO</b>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_ponto_id" id="vinculo_aluno_ponto_id" class="ls-select">
                                    <option value="">-</option>
                                    <?php foreach ($row_Pontos as $ponto) { ?>
                                        <option value="<?php echo htmlspecialchars($ponto['te_ponto_id']); ?>" 
                                                <?php if ($ponto['te_ponto_id'] == $row_VinculoEditar['vinculo_aluno_ponto_id']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($ponto['te_ponto_descricao']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </label>
                        <label class="ls-label col-md-12 ls-box">
                            <b class="ls-label-text">VINCULAR CUIDADOR</b>
                            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left" 
                               data-content="O cuidador de alunos especiais na escola desempenha um papel crucial no suporte às necessidades individuais desses estudantes. Sua responsabilidade inclui oferecer assistência personalizada, adaptar o ambiente escolar, colaborar com professores e terapeutas, tudo para garantir o bem-estar e o desenvolvimento integral desses alunos." 
                               data-title="Caso o aluno possua um cuidador"></a>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_id_cuidador" class="ls-select">
                                    <option value="">-</option>
                                    <?php foreach ($row_ListaVinculos as $vinculo) { ?>
                                        <option value="<?php echo htmlspecialchars($vinculo['vinculo_id']); ?>" 
                                                <?php if ($vinculo['vinculo_id'] == $row_VinculoEditar['vinculo_aluno_id_cuidador']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($vinculo['func_nome']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <br>
                        </label>
                        <div class="ls-label col-md-12 ls-box">
                            <b class="ls-label-text">ALUNO ESPECIAL (não reprova)</b>
                            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
                               data-content="Caso o aluno seja considerado especial na etapa de ensino dos anos finais e sua avaliação seja feita por meio de relatórios descritivos em vez de notas, essa opção deverá ser marcada."
                               data-title="Caso o aluno seja especial"></a>
                            <p>
                                <label class="ls-label-text">
                                    <input type="checkbox" name="vinculo_aluno_nao_reprova" class="ls-field"
                                           <?php if ($row_VinculoEditar['vinculo_aluno_nao_reprova'] == "S") echo "checked"; ?>>
                                    SIM
                                </label>
                            </p>
                        </div>
                        <label class="ls-label col-sm-12" <?php echo ($row_VinculoEditar['turma_multisseriada'] == 0) ? "style='display:none'" : ''; ?>>
                            <b class="ls-label-text">ETAPA MULTISSERIADA</b>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_multietapa" id="vinculo_aluno_multietapa" class="ls-select">
                                    <option value="">-</option>
                                    <?php foreach ($row_Etapa as $etapa) { ?>
                                        <option value="<?php echo htmlspecialchars($etapa['etapa_id']); ?>" 
                                                <?php if ($etapa['etapa_id'] == $row_VinculoEditar['vinculo_aluno_multietapa']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($etapa['etapa_nome']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </label>
                        <label class="ls-label col-sm-12" <?php echo ($row_VinculoEditar['turma_multisseriada'] == 0) ? "style='display:none'" : ''; ?>>
                            <b class="ls-label-text">MATRIZ MULTISSERIADA</b>
                            <div class="ls-custom-select ls-field-lg">
                                <select name="vinculo_aluno_matriz_multietapa" id="vinculo_aluno_matriz_multietapa" class="ls-select">
                                    <option value="">-</option>
                                    <?php foreach ($row_Matriz as $matriz) { ?>
                                        <option value="<?php echo htmlspecialchars($matriz['matriz_id']); ?>" 
                                                <?php if ($matriz['matriz_id'] == $row_VinculoEditar['vinculo_aluno_id_matriz_multi']) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($matriz['matriz_nome']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_vacina_atualizada1">
                            <b class="ls-label-text">CARTEIRA DE VACINAÇÃO DO ALUNO ESTÁ ATUALIZADA?</b> <br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_vacina_atualizada" value="S" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_vacina_atualizada'] == "S") echo "checked"; ?> />
                                    SIM
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_vacina_atualizada" value="N" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_vacina_atualizada'] == "N") echo "checked"; ?> />
                                    NÃO
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_vacina_atualizada" value="I" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_vacina_atualizada'] == "I") echo "checked"; ?> />
                                    SEM INFORMAÇÃO
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_saida1">
                            <b class="ls-label-text">SAÍDA DO ALUNO</b>
                            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
                               data-content="Indique se o aluno sai da escola sozinho ou acompanhado. Essa informação será usada para relatórios e controle na portaria."
                               data-title="Atenção"></a> <br><br>
                            <p>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_saida" value="1" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_saida'] == 1) echo "checked"; ?> required />
                                    ACOMPANHADO
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_saida" value="2" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_saida'] == 2) echo "checked"; ?> />
                                    SOZINHO
                                </label>
                                <label class="ls-label-text">
                                    <input type="radio" name="vinculo_aluno_saida" value="0" 
                                           <?php if ($row_VinculoEditar['vinculo_aluno_saida'] == 0) echo "checked"; ?> />
                                    SEM INFORMAÇÃO
                                </label>
                            </p>
                        </label>
                        <label class="ls-label col-sm-12 vinculo_aluno_dependencia1">
                            <b class="ls-label-text">MATRÍCULA DE DEPENDÊNCIA?</b><br>
                            <label class="ls-label-text">
                                <input type="radio" name="vinculo_aluno_dependencia" id="vinculo_aluno_dependencia" value="S" 
                                       <?php if ($row_VinculoEditar['vinculo_aluno_dependencia'] == "S") echo "checked"; ?>>
                                Sim
                            </label>
                            <label class="ls-label-text">
                                <input type="radio" name="vinculo_aluno_dependencia" id="vinculo_aluno_dependencia" value="N" 
                                       <?php if ($row_VinculoEditar['vinculo_aluno_dependencia'] == "N") echo "checked"; ?>>
                                Não
                            </label>
                        </label>
                        <div class="ls-modal-footer">
                            <input type="submit" value="SALVAR ALTERAÇÃO" class="ls-btn-primary ls-btn-lg">
                            <a href="matriculaExibe.php?cmatricula=<?php echo htmlspecialchars($row_VinculoEditar['vinculo_aluno_hash']); ?>" 
                               class="ls-btn ls-btn-lg">CANCELAR</a>
                            <a onclick="abrirModalExclusao('<?php echo htmlspecialchars($row_VinculoEditar['vinculo_aluno_hash']); ?>', 
                               '<?php echo htmlspecialchars($row_VinculoEditar['aluno_nome']); ?>')" 
                               class="ls-btn-danger ls-float-right ls-btn-lg">EXCLUIR VÍNCULO</a>
                        </div>

                        <!-- Modal de Confirmação de Exclusão -->
                        <div class="ls-modal" id="modalConfirmacaoExclusao">
                            <div class="ls-modal-box">
                                <div class="ls-modal-header">
                                    <h4 class="ls-modal-title">Atenção - Exclusão de Vínculo</h4>
                                    <button data-dismiss="modal" class="ls-close-modal ls-ico-close"></button>
                                </div>
                                <div class="ls-modal-body">
                                    <div class="ls-alert-warning ls-alert-lg">
                                        <strong>ATENÇÃO!</strong>
                                        <p>A exclusão de vínculo é uma ação irreversível e só deve ser realizada em casos específicos.</p>
                                    </div>
                                    
                                    <div class="ls-box ls-box-gray">
                                        <h5 class="ls-title-5 ls-color-danger">
                                            <i class="ls-ico-close"></i> Casos em que a exclusão NÃO é recomendada:
                                        </h5>
                                        <ul class="ls-list ls-list-disc">
                                            <li>Aluno transferido para outra escola</li>
                                            <li>Aluno desistente</li>
                                            <li>Aluno falecido</li>
                                            <li>Aluno em situação de evasão</li>
                                        </ul>
                                        <div class="ls-alert-info ls-alert-sm">
                                            <p>Nestes casos, deve-se alterar a situação do aluno no sistema através do campo "SITUAÇÃO" acima.</p>
                                        </div>
                                    </div>

                                    <div class="ls-box ls-box-gray">
                                        <h5 class="ls-title-5 ls-color-success">
                                            <i class="ls-ico-check"></i> Casos em que a exclusão é permitida:
                                        </h5>
                                        <ul class="ls-list ls-list-disc">
                                            <li>Matrícula realizada por engano</li>
                                            <li>Duplicidade de cadastro</li>
                                            <li>Erro no processo de matrícula</li>
                                        </ul>
                                    </div>

                                    <div class="ls-alert-info ls-alert-lg">
                                        <p><strong>Confirmação necessária:</strong></p>
                                        <p>Você confirma que este é um dos casos permitidos para exclusão?</p>
                                        <p class="ls-color-danger"><small>Esta ação não poderá ser desfeita.</small></p>
                                    </div>
                                </div>
                                <div class="ls-modal-footer">
                                    <button type="button" class="ls-btn ls-btn-default" data-dismiss="modal">
                                        <i class="ls-ico-close"></i> Cancelar
                                    </button>
                                    <button type="button" class="ls-btn-danger" id="btnConfirmarExclusao">
                                        <i class="ls-ico-remove"></i> Confirmar Exclusão
                                    </button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <input type="hidden" name="MM_update" value="form1">
                    <input type="hidden" name="vinculo_aluno_id" value="<?php echo (int)$row_VinculoEditar['vinculo_aluno_id']; ?>">
                    <input type="hidden" name="usu_id" value="<?php echo (int)$row_UsuLogado['usu_id']; ?>">
                    <input type="hidden" name="usu_escola" value="<?php echo (int)$row_UsuLogado['usu_escola']; ?>">
                    <input type="hidden" name="detalhes" 
                           value="<?php echo htmlspecialchars($row_VinculoEditar['aluno_nome'] . ' - ' . $row_VinculoEditar['turma_nome']); ?>">
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
    <script type="text/javascript" src="../js/jquery.mask.min.js"></script>
    <script src="js/mascara.js"></script>
    <script>
        locastyle.modal.open("#modalLarge");

        $(document).ready(function() {
            function verificarSituacao() {
                var situacao = $('#vinculo_aluno_situacao').val();
                if (situacao != 1) {
                    $("#data_ocorrencia").removeClass('ls-display-none');
                    $("#vinculo_aluno_datatransferencia").attr("required", "true");
                } else {
                    $("#data_ocorrencia").addClass('ls-display-none');
                    $('#vinculo_aluno_datatransferencia').removeAttr('required');
                }
            }
            verificarSituacao();
            $('#vinculo_aluno_situacao').change(function() {
                verificarSituacao();
            });
        });

        function confirmaExclusao(id, nome) {
            var resposta = confirm("Deseja realmente remover o vínculo deste aluno?");
            if (resposta) {
                window.location.href = "matriculaExcluir.php?hash=" + id + "&nome=" + nome;
            }else{
                locastyle.modal.open("#modalLarge");
            }
        }

        function mudarTurmaSim() {
            $(".vinculo_aluno_vacina_atualizada1").css("display", "none");
            $(".vinculo_aluno_saida1").css("display", "none");
            $(".vinculo_aluno_dependencia1").css("display", "none");
            $(".vinculo_aluno_internet1").css("display", "none");
            $(".vinculo_aluno_data").css("display", "none");
            $(".vinculo_aluno_situacao").css("display", "none");
            $(".vinculo_aluno_datatransferencia").css("display", "none");
            $(".vinculo_aluno_transporte1").css("display", "none");
            $(".vinculo_aluno_ponto_id").css("display", "none");
            $(".vinculo_aluno_id_turma").css("display", "block");
        }

        function mudarTurmaNao() {
            $(".vinculo_aluno_vacina_atualizada1").css("display", "block");
            $(".vinculo_aluno_saida1").css("display", "block");
            $(".vinculo_aluno_dependencia1").css("display", "block");
            $(".vinculo_aluno_internet1").css("display", "block");
            $(".vinculo_aluno_data").css("display", "block");
            $(".vinculo_aluno_situacao").css("display", "block");
            $(".vinculo_aluno_datatransferencia").css("display", "block");
            $(".vinculo_aluno_transporte1").css("display", "block");
            $(".vinculo_aluno_ponto_id").css("display", "block");
            $(".vinculo_aluno_id_turma").css("display", "none");
        }

        function transporte_sim() {
            document.getElementById("ponto").style.display = "block";
        }

        function transporte_nao() {
            document.getElementById("ponto").style.display = "none";
        }

        function abrirModalExclusao(hash, nome) {
            // Armazena os dados para uso posterior
            window.hashAluno = hash;
            window.nomeAluno = nome;
            
            // Abre a modal
            locastyle.modal.open("#modalConfirmacaoExclusao");
        }

        $(document).ready(function() {
            $('#btnConfirmarExclusao').click(function() {
                // Fecha a modal
                locastyle.modal.close("#modalConfirmacaoExclusao");
                // Chama a função original de exclusão
                confirmaExclusao(window.hashAluno, window.nomeAluno);
            });
        });
    </script>
</body>
</html>