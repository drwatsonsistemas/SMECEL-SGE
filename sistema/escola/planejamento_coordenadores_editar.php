<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: lista_planejamentos.php");
    exit;
}

// Selecionar o planejamento
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Planejamento = sprintf(
    "SELECT * FROM smc_planejamento_coordenador WHERE id_planejamento = %s AND id_coordenador = %s",
    GetSQLValueString($_GET['id'], "int"),
    GetSQLValueString($row_UsuLogado['usu_id'], "int")
);
$Planejamento = mysql_query($query_Planejamento, $SmecelNovo) or die(mysql_error());
$row_Planejamento = mysql_fetch_assoc($Planejamento);
$totalRows_Planejamento = mysql_num_rows($Planejamento);

// Se o planejamento não existir ou não pertencer ao usuário, redirecionar
if ($totalRows_Planejamento == 0) {
    header("Location: planejamento_coordenadores.php");
    exit;
}

// Converter campos de múltipla escolha em arrays para pré-preenchimento
$temas_selecionados = explode(',', $row_Planejamento['temas_integradores']);
$atividades_selecionadas = explode(',', $row_Planejamento['atividades_diarias']);
$intervencao_selecionada = explode(',', $row_Planejamento['intervencao_pedagogica']);
$monitoramento_selecionado = explode(',', $row_Planejamento['monitoramento_avaliacao']);
$avaliacao_selecionada = explode(',', $row_Planejamento['avaliacao_processual']);

// Processar a atualização
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    // Verificar permissões
    if ($row_UsuLogado['usu_update'] == "N") { // Assumindo que existe um campo usu_update para permissões de atualização
        header("Location: lista_planejamentos.php?permissao");
        exit;
    }

    // Processar campos de múltipla escolha (checkboxes)
    $temas_integradores = isset($_POST['temas_integradores']) ? implode(',', $_POST['temas_integradores']) : '';
    $atividades_diarias = isset($_POST['atividades_diarias']) ? implode(',', $_POST['atividades_diarias']) : '';
    $intervencao_pedagogica = isset($_POST['intervencao_pedagogica']) ? implode(',', $_POST['intervencao_pedagogica']) : '';
    $monitoramento_avaliacao = isset($_POST['monitoramento_avaliacao']) ? implode(',', $_POST['monitoramento_avaliacao']) : '';
    $avaliacao_processual = isset($_POST['avaliacao_processual']) ? implode(',', $_POST['avaliacao_processual']) : '';

    // Preparar o UPDATE para smc_planejamento_coordenador
    $updateSQL = sprintf("UPDATE smc_planejamento_coordenador SET 
        data_inicio=%s, data_fim=%s, temas_integradores=%s, atividade_promovida=%s, 
        atividades_diarias=%s, intervencao_pedagogica=%s, intervencao_pedagogica_obs=%s, acoes_cnca_sabe=%s, 
        acompanhamento_projetos=%s, monitoramento_avaliacao=%s, monitoramento_avaliacao_obs=%s, formacao_continuada=%s, 
        competencias_socioemocionais=%s, atendimento_familias=%s, avaliacao_processual=%s 
        WHERE id_planejamento=%s AND id_coordenador=%s AND id_escola=%s",
        GetSQLValueString($_POST['data_inicio'], "date"),
        GetSQLValueString($_POST['data_fim'], "date"),
        GetSQLValueString($temas_integradores, "text"),
        GetSQLValueString($_POST['atividade_promovida'], "text"),
        GetSQLValueString($atividades_diarias, "text"),
        GetSQLValueString($intervencao_pedagogica, "text"),
        GetSQLValueString($_POST['intervencao_pedagogica_obs'], "text"),
        GetSQLValueString($_POST['acoes_cnca_sabe'], "text"),
        GetSQLValueString($_POST['acompanhamento_projetos'], "text"),
        GetSQLValueString($monitoramento_avaliacao, "text"),
        GetSQLValueString($_POST['monitoramento_avaliacao_obs'], "text"),
        GetSQLValueString($_POST['formacao_continuada'], "text"),
        GetSQLValueString($_POST['competencias_socioemocionais'], "text"),
        GetSQLValueString($_POST['atendimento_familias'], "text"),
        GetSQLValueString($avaliacao_processual, "text"),
        GetSQLValueString($_POST['id_planejamento'], "int"),
        GetSQLValueString($row_UsuLogado['usu_id'], "int"),
        GetSQLValueString($row_EscolaLogada['escola_id'], "int")
    );

    // Executar o UPDATE
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


    $updateGoTo = "planejamento_coordenadores.php?atualizado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
}


?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
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

    <style>
        .ls-textarea {
            white-space: normal !important;
            /* Sobrescreve qualquer estilo que preserve espaços */
            padding: 5px !important;
            /* Ajusta o padding interno para evitar espaçamentos excessivos */
        }
    </style>
</head>

<body>
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>


    <main class="ls-main ">
        <div class="container-fluid">

            <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO - COORDENAÇÃO PEDAGÓGICA</h1>
            <!-- CONTEÚDO -->

            <div class="ls-actions-btn">
                <a href="planejamento_coordenadores.php" class="ls-btn-primary">VOLTAR</a>
            </div>

            <div class="ls-box ls-md-space">
                <form id="formEditarPlanejamento" class="ls-form ls-form-horizontal">
                    <input type="hidden" name="MM_update" value="form1">
                    <input type="hidden" name="id_planejamento"
                        value="<?php echo $row_Planejamento['id_planejamento']; ?>">

                    <!-- Período -->
                    <fieldset>
                        <legend class="ls-title-3">Período</legend>
                        <div class="ls-grid-row">
                            <div class="ls-col-md-6">
                                <label class="ls-label">
                                    <b class="ls-label-text">Data Início</b>
                                    <input type="date" name="data_inicio" class="ls-field"
                                        value="<?php echo $row_Planejamento['data_inicio']; ?>" required>
                                </label>
                            </div>
                            <div class="ls-col-md-6">
                                <label class="ls-label">
                                    <b class="ls-label-text">Data Fim</b>
                                    <input type="date" name="data_fim" class="ls-field"
                                        value="<?php echo $row_Planejamento['data_fim']; ?>" required>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Temas Integradores -->
                    <fieldset>
                        <legend class="ls-title-3">Temas Integradores Trabalhados com os Docentes</legend>
                        <?php
                        $opcoes_temas = [
                            'Educação em Direitos Humanos',
                            'Educação para diversidade',
                            'Educação para o trânsito',
                            'Saúde na escola',
                            'Educação Ambiental',
                            'Educação Financeira e para o consumo',
                            'Cultura Digital',
                            'Educação Fiscal',
                            'Educação das Relações Étnico-raciais'
                        ];
                        foreach ($opcoes_temas as $opcao) {
                            $checked = in_array($opcao, $temas_selecionados) ? 'checked' : '';
                            echo "<label class='ls-label'><input type='checkbox' name='temas_integradores[]' value='$opcao' $checked> $opcao</label>";
                        }
                        ?>
                    </fieldset>

                    <!-- Atividade Promovida -->
                    <fieldset>
                        <legend class="ls-title-3">Atividade Promovida</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="atividade_promovida" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['atividade_promovida']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Atividades Diárias Propostas -->
                    <fieldset>
                        <legend class="ls-title-3">Atividades Diárias Propostas</legend>
                        <?php
                        $opcoes_atividades = [
                            'Acompanhamento do início do turno',
                            'Acompanhamento nos AC',
                            'Conferir a presença dos professores e alunos',
                            'Monitorar pontualidade e abertura das salas de aula',
                            'Responder e-mails e mensagens importantes',
                            'Registrar ocorrências no diário de coordenação',
                            'Observar a dinâmica do professor e a interação com os alunos',
                            'Identificar necessidades pedagógicas',
                            'Receber professores, alunos e responsáveis para atendimentos pontuais',
                            'Atendimento individualizado',
                            'Gerenciar substituições ou ajustes no cronograma',
                            'Apoiar professores em situações desafiadoras',
                            'Oferecer feedback imediato, se necessário',
                            'Acompanhar registro nos SMECEL e notificar docente',
                            'Intensificar a BNCC COMPUTAÇÃO',
                            'Trabalhar leitura, escrita e oralidade na educação infantil',
                            'Foco no IDEB',
                            'Trabalhar leitura/escrita e oralidade nas etapas dos anos iniciais, finais e EJA',
                            'Foco na alfabetização nas diversas etapas'
                        ];
                        foreach ($opcoes_atividades as $opcao) {
                            $checked = in_array($opcao, $atividades_selecionadas) ? 'checked' : '';
                            echo "<label class='ls-label'><input type='checkbox' name='atividades_diarias[]' value='$opcao' $checked> $opcao</label>";
                        }
                        ?>
                    </fieldset>

                    <!-- Intervenção Pedagógica -->
                    <fieldset>
                        <legend class="ls-title-3">Intervenção Pedagógica</legend>
                        <?php
                        $opcoes_intervencao = [
                            'Creche I',
                            'Creche II',
                            'Pré I',
                            'Pré II',
                            '1º ano',
                            '2º ano',
                            '3º ano',
                            '4º ano',
                            '5º ano',
                            '6º ano',
                            '7º ano',
                            '8º ano',
                            '9º ano',
                            'EJA'
                        ];
                        foreach ($opcoes_intervencao as $opcao) {
                            $checked = in_array($opcao, $intervencao_selecionada) ? 'checked' : '';
                            echo "<label class='ls-label'><input type='checkbox' name='intervencao_pedagogica[]' value='$opcao' $checked> $opcao</label>";
                        }
                        ?>

                        <label class="ls-label">
                            <b class="ls-label-text">Observação</b>
                            <textarea name="intervencao_pedagogica_obs" class="ls-textarea"
                                rows="2"><?php echo htmlspecialchars(preg_replace("/\n+/", "\n", trim($row_Planejamento['intervencao_pedagogica_obs']))); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Ações para CNCA/SABE/SAEB/LEEI e Escola das Adolescências -->
                    <fieldset>
                        <legend class="ls-title-3">Ações para CNCA/SABE/SAEB/LEEI e Escola das Adolescências</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="acoes_cnca_sabe" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['acoes_cnca_sabe']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Acompanhamento de Projetos e Atividades -->
                    <fieldset>
                        <legend class="ls-title-3">Acompanhamento de Projetos e Atividades</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="acompanhamento_projetos" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['acompanhamento_projetos']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Monitoramento e Avaliação -->
                    <fieldset>
                        <legend class="ls-title-3">Monitoramento e Avaliação</legend>
                        <?php
                        $opcoes_monitoramento = [
                            'Creche I',
                            'Creche II',
                            'Pré I',
                            'Pré II',
                            '1º ano',
                            '2º ano',
                            '3º ano',
                            '4º ano',
                            '5º ano',
                            '6º ano',
                            '7º ano',
                            '8º ano',
                            '9º ano',
                            'EJA'
                        ];
                        foreach ($opcoes_monitoramento as $opcao) {
                            $checked = in_array($opcao, $monitoramento_selecionado) ? 'checked' : '';
                            echo "<label class='ls-label'><input type='checkbox' name='monitoramento_avaliacao[]' value='$opcao' $checked> $opcao</label>";
                        }
                        ?>
                        <label class="ls-label">
                            <b class="ls-label-text">Observação</b>
                            <textarea name="monitoramento_avaliacao_obs" class="ls-textarea"
                                rows="2"><?php echo htmlspecialchars(preg_replace('/\s+/', ' ', trim($row_Planejamento['monitoramento_avaliacao_obs'], " \t\n\r\0\x0B\xC2\xA0"))); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Formação Continuada e Estudo -->
                    <fieldset>
                        <legend class="ls-title-3">Formação Continuada e Estudo</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="formacao_continuada" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['formacao_continuada']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Atendimento às Competências Socioemocionais -->
                    <fieldset>
                        <legend class="ls-title-3">Atendimento às Competências Socioemocionais com a Equipe de Docentes
                        </legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="competencias_socioemocionais" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['competencias_socioemocionais']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Atendimento às Famílias -->
                    <fieldset>
                        <legend class="ls-title-3">Atendimento às Famílias</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="atendimento_familias" class="ls-textarea"
                                rows="4"><?php echo htmlspecialchars($row_Planejamento['atendimento_familias']); ?></textarea>
                        </label>
                    </fieldset>

                    <!-- Avaliação Processual -->
                    <fieldset>
                        <legend class="ls-title-3">Avaliação Processual</legend>
                        <?php
                        $opcoes_avaliacao = [
                            'Faz um acompanhamento do ritmo da prática docente',
                            'Ajusta as intervenções pedagógicas às características individuais dos docentes e da turma',
                            'Modifica estratégias do processo',
                            'Ajusta e orienta atividades em sala conforme necessidade apresentada pelo docente',
                            'Avalia o aprendizado do aluno e do docente ao final de um período de ensino',
                            'Autoavaliação do trabalho da coordenação pedagógica',
                            'Promove avaliação coletiva desse trabalho com a equipe escolar'
                        ];
                        foreach ($opcoes_avaliacao as $opcao) {
                            $checked = in_array($opcao, $avaliacao_selecionada) ? 'checked' : '';
                            echo "<label class='ls-label'><input type='checkbox' name='avaliacao_processual[]' value='$opcao' $checked> $opcao</label>";
                        }
                        ?>
                    </fieldset>


                    <!-- Botões -->
                    <div class="ls-actions-btn">
                        <button type="submit" class="ls-btn-success">Atualizar Planejamento</button>
                        <a href="planejamento_coordenadores.php" class="ls-btn">Voltar</a>
                    </div>
                </form>
            </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/locastyle.js"></script>

    <script>
        $(document).ready(function () {
            $('#formEditarPlanejamento').on('submit', function (event) {
                event.preventDefault(); // Impede o envio padrão do formulário

                // Coletar os dados do formulário
                var formData = $(this).serializeArray();
                var data = {};
                $.each(formData, function (i, field) {
                    if (data[field.name]) {
                        if (!Array.isArray(data[field.name])) {
                            data[field.name] = [data[field.name]];
                        }
                        data[field.name].push(field.value);
                    } else {
                        data[field.name] = field.value;
                    }
                });

                // Tratar checkboxes não marcados
                $('input[type="checkbox"]').each(function () {
                    if (!data[this.name]) {
                        data[this.name] = '';
                    }
                });

                // Enviar os dados via AJAX
                $.ajax({
                    type: 'POST',
                    url: 'crud/EditPlanCoord.php', // Arquivo PHP que processará a edição
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function () {
                                window.location.href = 'planejamento_coordenadores.php?atualizado';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Ocorreu um erro ao processar a requisição. Tente novamente.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>