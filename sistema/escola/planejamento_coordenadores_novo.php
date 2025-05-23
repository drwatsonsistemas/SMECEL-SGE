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
                <form id="formPlanejamento" class="ls-form ls-form-horizontal">
                    <input type="hidden" name="MM_insert" value="form1">
                    <fieldset>
                        <legend class="ls-title-3">Período</legend>
                        <div class="ls-grid-row">
                            <div class="ls-col-md-6">
                                <label class="ls-label">
                                    <b class="ls-label-text">Data Início</b>
                                    <input type="date" name="data_inicio" class="ls-field" required>
                                </label>
                            </div>
                            <div class="ls-col-md-6">
                                <label class="ls-label">
                                    <b class="ls-label-text">Data Fim</b>
                                    <input type="date" name="data_fim" class="ls-field" required>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Temas Integradores -->
                    <fieldset>
                        <legend class="ls-title-3">Temas Integradores Trabalhados com os Docentes</legend>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Educação em Direitos Humanos">
                            Educação em Direitos Humanos
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Educação para diversidade">
                            Educação para diversidade
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Educação para o trânsito">
                            Educação para o trânsito
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Saúde na escola"> Saúde na escola
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Educação Ambiental"> Educação
                            Ambiental
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]"
                                value="Educação Financeira e para o consumo"> Educação Financeira e para o consumo
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Cultura Digital"> Cultura Digital
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]" value="Educação Fiscal"> Educação Fiscal
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="temas_integradores[]"
                                value="Educação das Relações Étnico-raciais"> Educação das Relações Étnico-raciais
                        </label>
                    </fieldset>

                    <!-- Atividade Promovida -->
                    <fieldset>
                        <legend class="ls-title-3">Atividade Promovida</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="atividade_promovida" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Atividades Diárias Propostas -->
                    <fieldset>
                        <legend class="ls-title-3">Atividades Diárias Propostas</legend>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Acompanhamento do início do turno"> Acompanhamento do início do turno
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]" value="Acompanhamento nos AC">
                            Acompanhamento nos AC
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Conferir a presença dos professores e alunos"> Conferir a presença dos
                            professores e alunos
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Monitorar pontualidade e abertura das salas de aula"> Monitorar pontualidade e
                            abertura das salas de aula
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Responder e-mails e mensagens importantes"> Responder e-mails e mensagens
                            importantes
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Registrar ocorrências no diário de coordenação"> Registrar ocorrências no diário
                            de coordenação
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Observar a dinâmica do professor e a interação com os alunos"> Observar a
                            dinâmica do professor e a interação com os alunos (Visitas às salas de aula)
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Identificar necessidades pedagógicas"> Identificar necessidades pedagógicas
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Receber professores, alunos e responsáveis para atendimentos pontuais"> Receber
                            professores, alunos e responsáveis para atendimentos pontuais
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]" value="Atendimento individualizado">
                            Atendimento individualizado
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Gerenciar substituições ou ajustes no cronograma"> Gerenciar substituições ou
                            ajustes no cronograma
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Apoiar professores em situações desafiadoras"> Apoiar professores em situações
                            desafiadoras
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Oferecer feedback imediato, se necessário"> Oferecer feedback imediato, se
                            necessário
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Acompanhar registro nos SMECEL e notificar docente"> Acompanhar registro nos
                            SMECEL e notificar docente
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]" value="Intensificar a BNCC COMPUTAÇÃO">
                            Intensificar a BNCC COMPUTAÇÃO
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Trabalhar leitura, escrita e oralidade na educação infantil"> Trabalhar leitura,
                            escrita e oralidade na educação infantil
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]" value="Foco no IDEB"> Foco no IDEB
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Trabalhar leitura/escrita e oralidade nas etapas dos anos iniciais, finais e EJA">
                            Trabalhar leitura/escrita e oralidade nas etapas dos anos iniciais, finais e EJA
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="atividades_diarias[]"
                                value="Foco na alfabetização nas diversas etapas"> Foco na alfabetização nas diversas
                            etapas
                        </label>
                    </fieldset>

                    <!-- Intervenção Pedagógica -->
                    <fieldset>
                        <legend class="ls-title-3">Intervenção Pedagógica</legend>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="Creche I"> Creche I
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="Creche II"> Creche II
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="Pré I"> Pré I
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="Pré II"> Pré II
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="1º ano"> 1º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="2º ano"> 2º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="3º ano"> 3º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="4º ano"> 4º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="5º ano"> 5º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="6º ano"> 6º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="7º ano"> 7º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="8º ano"> 8º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="9º ano"> 9º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="intervencao_pedagogica[]" value="EJA"> EJA
                        </label>

                        <label class="ls-label">
                            <b class="ls-label-text">Observação</b>
                            <textarea name="intervencao_pedagogica_obs" class="ls-textarea" rows="2"></textarea>
                        </label>
                    </fieldset>

                    <!-- Ações para CNCA/SABE/SAEB/LEEI e Escola das Adolescências -->
                    <fieldset>
                        <legend class="ls-title-3">Ações para CNCA/SABE/SAEB/LEEI e Escola das Adolescências</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="acoes_cnca_sabe" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Acompanhamento de Projetos e Atividades -->
                    <fieldset>
                        <legend class="ls-title-3">Acompanhamento de Projetos e Atividades</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="acompanhamento_projetos" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Monitoramento e Avaliação -->
                    <fieldset>
                        <legend class="ls-title-3">Monitoramento e Avaliação</legend>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="Creche I"> Creche I
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="Creche II"> Creche II
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="Pré I"> Pré I
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="Pré II"> Pré II
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="1º ano"> 1º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="2º ano"> 2º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="3º ano"> 3º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="4º ano"> 4º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="5º ano"> 5º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="6º ano"> 6º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="7º ano"> 7º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="8º ano"> 8º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="9º ano"> 9º ano
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="monitoramento_avaliacao[]" value="EJA"> EJA
                        </label>

                        <label class="ls-label">
                            <b class="ls-label-text">Observação</b>
                            <textarea name="monitoramento_avaliacao_obs" class="ls-textarea" rows="2"></textarea>
                        </label>
                    </fieldset>

                    <!-- Formação Continuada e Estudo -->
                    <fieldset>
                        <legend class="ls-title-3">Formação Continuada e Estudo</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="formacao_continuada" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Atendimento às Competências Socioemocionais -->
                    <fieldset>
                        <legend class="ls-title-3">Atendimento às Competências Socioemocionais com a Equipe de Docentes
                        </legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="competencias_socioemocionais" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Atendimento às Famílias -->
                    <fieldset>
                        <legend class="ls-title-3">Atendimento às Famílias</legend>
                        <label class="ls-label">
                            <b class="ls-label-text">Descrição</b>
                            <textarea name="atendimento_familias" class="ls-textarea" rows="4"></textarea>
                        </label>
                    </fieldset>

                    <!-- Avaliação Processual -->
                    <fieldset>
                        <legend class="ls-title-3">Avaliação Processual</legend>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Faz um acompanhamento do ritmo da prática docente"> Faz um acompanhamento do
                            ritmo da prática docente
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Ajusta as intervenções pedagógicas às características individuais dos docentes e da turma">
                            Ajusta as intervenções pedagógicas às características individuais dos docentes e da turma
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Modifica estratégias do processo"> Modifica estratégias do processo
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Ajusta e orienta atividades em sala conforme necessidade apresentada pelo docente">
                            Ajusta e orienta atividades em sala conforme necessidade apresentada pelo docente
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Avalia o aprendizado do aluno e do docente ao final de um período de ensino">
                            Avalia o aprendizado do aluno e do docente ao final de um período de ensino (bimestral,
                            trimestral, semestral ou anual)
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Autoavaliação do trabalho da coordenação pedagógica"> Autoavaliação do trabalho
                            da coordenação pedagógica
                        </label>
                        <label class="ls-label">
                            <input type="checkbox" name="avaliacao_processual[]"
                                value="Promove avaliação coletiva desse trabalho com a equipe escolar"> Promove
                            avaliação coletiva desse trabalho com a equipe escolar
                        </label>
                    </fieldset>

                    <!-- Botões -->
                    <div class="ls-actions-btn">
                        <button type="submit" class="ls-btn-success">Salvar Planejamento</button>
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
            $(document).ready(function() {
                $('#formPlanejamento').on('submit', function (event) {
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
                        url: 'crud/InsertPlanCoord.php', // Arquivo PHP que processará os dados
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
                                    window.location.href = 'planejamento_coordenadores.php?cadastrado';
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
                            console.error('Erro:', error);
                            console.error('Status:', status);
                            console.error('Response:', xhr);
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