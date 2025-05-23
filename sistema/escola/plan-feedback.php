<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/sessionPDO.php"; ?>

<?php

include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$planejamento = isset($_GET['plan']) ? $_GET['plan'] : "-1";

$stmtEscolaLogada = $SmecelNovo->prepare(
    "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
  escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf, 
  sec_termo_matricula, escola_assinatura 
  FROM smc_escola 
  INNER JOIN smc_sec ON sec_id = escola_id_sec 
  WHERE escola_id = :escola"
);
$stmtEscolaLogada->execute([':escola' => $row_UsuLogado['usu_escola']]);
$row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
$totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();


// Consultar AC Edit
$stmt_plan_edit = $SmecelNovo->prepare(
    "SELECT p.*, 
            f.func_nome, 
            f.func_id,
            GROUP_CONCAT(DISTINCT pm.smc_id_metodologia) AS metodologias_marcadas,
            GROUP_CONCAT(DISTINCT pa.smc_id_avaliacao) AS avaliacoes_marcadas,
            GROUP_CONCAT(DISTINCT pt.smc_id_temas_integradores) AS temas_marcados,
            GROUP_CONCAT(DISTINCT sem.smc_id_estrategias_metodologicas) AS estrategias_metodologicas_marcadas,
            GROUP_CONCAT(DISTINCT pae.smc_id_avaliacao_edinf) AS avaliacoes_marcadas_edinf
     FROM smc_planejamento p
     INNER JOIN smc_func f ON p.smc_id_professor = f.func_id
     LEFT JOIN smc_plan_metod_relacionamento pmr ON p.smc_id_planejamento = pmr.smc_id_planejamento
     LEFT JOIN smc_planejamento_metodologias pm ON pmr.smc_id_metodologia = pm.smc_id_metodologia
     LEFT JOIN smc_plan_ava_relacionamento par ON p.smc_id_planejamento = par.smc_id_planejamento
     LEFT JOIN smc_planejamento_avaliacoes pa ON par.smc_id_avaliacao = pa.smc_id_avaliacao
     LEFT JOIN smc_plan_temas_relacionamento ptr ON p.smc_id_planejamento = ptr.smc_id_planejamento
     LEFT JOIN smc_planejamento_temas_integradores pt ON ptr.smc_id_tema = pt.smc_id_temas_integradores
     LEFT JOIN smc_plan_estrate_relacionamentos per ON p.smc_id_planejamento = per.smc_id_planejamento
     LEFT JOIN smc_planejamento_estrategias_metodologicas sem ON per.smc_id_estrate_metod = sem.smc_id_estrategias_metodologicas
     LEFT JOIN smc_plan_avaliacaoedinf_relacionamento pae_rel ON p.smc_id_planejamento = pae_rel.smc_id_planejamento
     LEFT JOIN smc_planejamento_avaliacaoedinf pae ON pae_rel.smc_id_avaliacaoedinf = pae.smc_id_avaliacao_edinf
     WHERE p.smc_id_planejamento = :plan_id
     GROUP BY p.smc_id_planejamento"
);


$stmt_plan_edit->bindParam(':plan_id', $planejamento, PDO::PARAM_INT);
$stmt_plan_edit->execute();
$row_plan_edit = $stmt_plan_edit->fetch(PDO::FETCH_ASSOC);

// Converter metodologias e avaliações marcadas em arrays para facilitar a comparação no HTML
$metodologias_marcadas = explode(',', $row_plan_edit['metodologias_marcadas']);
$avaliacoes_marcadas = explode(',', $row_plan_edit['avaliacoes_marcadas']);
$avaliacoes_marcadas_edinf = explode(',', $row_plan_edit['avaliacoes_marcadas_edinf']);

$stmtDisciplinasAC = $SmecelNovo->prepare("SELECT DISTINCT smc_id_planejamento, smc_id_componente, disciplina_nome, disciplina_id 
    FROM smc_planejamento_componente
    INNER JOIN smc_disciplina ON disciplina_id = smc_id_componente
    WHERE smc_id_planejamento = :plan_id");
$stmtDisciplinasAC->bindValue(':plan_id', $_GET['plan'], PDO::PARAM_INT);
$stmtDisciplinasAC->execute();
$disciplinasAC = $stmtDisciplinasAC->fetchAll(PDO::FETCH_ASSOC);
$totalRowsDisciplinasAC = $stmtDisciplinasAC->rowCount();


// Consulta para obter informações da turma
$stmt_Turma = $SmecelNovo->prepare(
    "SELECT * FROM smc_turma WHERE turma_id = :turma"
);
$stmt_Turma->bindParam(':turma', $row_plan_edit['smc_id_turma'], PDO::PARAM_INT);
$stmt_Turma->execute();
$row_Turma = $stmt_Turma->fetch(PDO::FETCH_ASSOC);
$etapa = $row_Turma['turma_etapa'];

$stmt_periodos = $SmecelNovo->prepare(
    "SELECT * FROM smc_unidades WHERE per_unid_id = :id_unidade"
);
$stmt_periodos->execute([
    ':id_unidade' => $row_plan_edit['smc_id_periodo']
]);
$result_periodos = $stmt_periodos->fetch(PDO::FETCH_ASSOC);

// Consulta para obter informações da etapa
$stmt_Etapa = $SmecelNovo->prepare(
    "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
        FROM smc_etapa WHERE etapa_id = :etapa_id"
);
$stmt_Etapa->bindParam(':etapa_id', $etapa, PDO::PARAM_INT);
$stmt_Etapa->execute();
$row_Etapa = $stmt_Etapa->fetch(PDO::FETCH_ASSOC);

$queryMetodologias = "SELECT smc_id_metodologia, smc_metodologia FROM smc_planejamento_metodologias";
$stmtMetodologias = $SmecelNovo->query($queryMetodologias);
$metodologias = $stmtMetodologias->fetchAll(PDO::FETCH_ASSOC);


$queryAvaliacoes = "SELECT smc_id_avaliacao, smc_tipo, smc_descricao FROM smc_planejamento_avaliacoes";
$stmtAvaliacoes = $SmecelNovo->query($queryAvaliacoes);
$avaliacoes = $stmtAvaliacoes->fetchAll(PDO::FETCH_ASSOC);

$queryTemasIntegradores = "SELECT smc_id_temas_integradores, smc_tema_integrador FROM smc_planejamento_temas_integradores";
$stmtTemasIntegradores = $SmecelNovo->query($queryTemasIntegradores);
$temasIntegradores = $stmtTemasIntegradores->fetchAll(PDO::FETCH_ASSOC);


$queryEstrategiasMetodologicas = "SELECT smc_id_estrategias_metodologicas, smc_estrategias_metodologicas FROM smc_planejamento_estrategias_metodologicas";
$stmtEstrategiasMetodologicas = $SmecelNovo->query($queryEstrategiasMetodologicas);
$EstrategiasMetodologicas = $stmtEstrategiasMetodologicas->fetchAll(PDO::FETCH_ASSOC);

$queryAvaliacaoEdinf = "SELECT smc_id_avaliacao_edinf, smc_avaliacao_edinf FROM smc_planejamento_avaliacaoedinf";
$stmtAvaliacaoEdinf = $SmecelNovo->query($queryAvaliacaoEdinf);
$AvaliacaoEdinf = $stmtAvaliacaoEdinf->fetchAll(PDO::FETCH_ASSOC);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    try {
        // Preparar a query de atualização
        $updateSQL = "
            UPDATE smc_planejamento 
            SET 
                smc_feedback = :feedback, 
                smc_planejamento_correcao = :correcao, 
                planejamento_status = :status 
            WHERE smc_id_planejamento = :planejamento_id";

        // Preparar a execução da consulta com PDO
        $stmt = $SmecelNovo->prepare($updateSQL);

        // Bind dos valores recebidos do formulário
        $stmt->bindParam(':feedback', $_POST['planejamento_feedback'], PDO::PARAM_STR);
        $stmt->bindParam(':correcao', $_POST['planejamento_correcao'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $_POST['planejamento_status'], PDO::PARAM_INT);
        $stmt->bindParam(':planejamento_id', $_POST['planejamento_id'], PDO::PARAM_INT);

        // Executar a query
        $stmt->execute();

        // Redirecionamento após a atualização bem-sucedida
        $updateGoTo = "ac-professor.php?codigo=" . $row_plan_edit['smc_id_professor'];
        if (isset($_SERVER['QUERY_STRING'])) {
            $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
            $updateGoTo .= $_SERVER['QUERY_STRING'];
        }
        header("Location: " . $updateGoTo);
        exit;

    } catch (PDOException $e) {
        die("Erro ao atualizar o planejamento: " . $e->getMessage());
    }
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
    <script src="js/locastyle.js"></script>
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
            <h1 class="ls-title-intro ls-ico-home">ACOMPANHAMENTO DE PLANEJAMENTO</h1>
            <!-- CONTEÚDO -->

            <div class="ls-box">
                <h5 class="ls-title-5">Professor(a): <?php echo $row_plan_edit['func_nome']; ?><br>Etapa:
                    <?php echo $row_Etapa['etapa_nome']; ?>
                </h5>
                <br>
                Relatório feito em
                <?php if ($row_plan_edit['planejamento_criacao'] == "") { ?>
                    <?php echo inverteData($row_plan_edit['smc_planejamento_data_inicial']); ?><?php } else { ?>
                    <?php echo date("d/m/Y - H:i", strtotime($row_plan_edit['planejamento_criacao'])); ?><?php } ?>
                <br>
                Componente(s):&nbsp;
                <?php if (!empty($disciplinasAC)) {
                    foreach ($disciplinasAC as $disciplinaAC) { ?>

                        <span class="">
                            <?php echo htmlspecialchars($disciplinaAC['disciplina_nome'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;| &nbsp;

                        </span>
                    <?php }
                } ?>
            </div>

            <div class="ls-box">
                <h5 class="ls-title-5">TEMAS INTEGRADORES</h5>
                <p>
                    <?php
                    if (!empty($temasIntegradores)) {
                        $temasSelecionados = explode(',', $row_plan_edit['temas_marcados']);
                        $temasExibidos = [];

                        foreach ($temasIntegradores as $tema) {
                            if (in_array($tema['smc_id_temas_integradores'], $temasSelecionados)) {
                                $temasExibidos[] = mb_convert_encoding($tema['smc_tema_integrador'], 'UTF-8', 'ISO-8859-1');
                            }
                        }

                        // Exibir os temas selecionados formatados corretamente
                        if (!empty($temasExibidos)) {
                            echo implode(" | ", $temasExibidos);
                        } else {
                            echo "Nenhum tema integrador selecionado.";
                        }
                    } else {
                        echo "Nenhum tema integrador encontrado.";
                    }
                    ?>
                </p>
                <?php if ($row_plan_edit['smc_atv_dev_tema_integrador'] <> '') { ?>
                  <br><small><strong>ATIVIDADE DESENVOLVIDA PARA O TEMA INTEGRADOR:</strong></small><br>
                    <p><?php echo $row_plan_edit['smc_atv_dev_tema_integrador']; ?></p>
                <?php } ?>

            </div>

            <?php if ($row_Etapa['etapa_id_filtro'] == "1") { ?>
                <div class="ls-box">
                    <h5 class="ls-title-5">DIREITOS DE APRENDIZAGEM</h5>
                    <p>
                        <?php
                        $direitosAprendizagem = [];

                        if ($row_plan_edit['ac_da_conviver'] === "S")
                            $direitosAprendizagem[] = "CONVIVER";
                        if ($row_plan_edit['ac_da_brincar'] === "S")
                            $direitosAprendizagem[] = "BRINCAR";
                        if ($row_plan_edit['ac_da_participar'] === "S")
                            $direitosAprendizagem[] = "PARTICIPAR";
                        if ($row_plan_edit['ac_da_explorar'] === "S")
                            $direitosAprendizagem[] = "EXPLORAR";
                        if ($row_plan_edit['ac_da_expressar'] === "S")
                            $direitosAprendizagem[] = "EXPRESSAR";
                        if ($row_plan_edit['ac_da_conhecerse'] === "S")
                            $direitosAprendizagem[] = "CONHECER-SE";

                        echo (!empty($direitosAprendizagem)) ? implode(" | ", $direitosAprendizagem) : "Nenhum direito de aprendizagem marcado.";
                        ?>
                    </p>
                </div>
            <?php } ?>

            <?php
            function exibirCampo($titulo, $conteudo)
            {
                if (!empty($conteudo)) {
                    echo '<div class="ls-box">';
                    echo '<h5 class="ls-title-5">' . $titulo . '</h5>';
                    echo '<p>' . nl2br($conteudo) . '</p>';
                    echo '</div>';
                }
            }

            exibirCampo('Atividades permanentes', $row_plan_edit['smc_atividades_permanentes']);
            exibirCampo('Objetivos de aprendizagem e desenvolvimento', $row_plan_edit['obj_aprendizagem_desenvolvimento']);
            exibirCampo('Aprendizagens vivenciadas pelas crianças (Saberes)', $row_plan_edit['aprendizagens_saberes']);
            exibirCampo('Metodologia', $row_plan_edit['metodologia']);
            exibirCampo('Recursos', $row_plan_edit['recursos']);
            exibirCampo('Avaliação', $row_plan_edit['avaliacao']);
            exibirCampo('OBJETOS DE CONHECIMENTO/CONTEÚDO(S)', $row_plan_edit['smc_obj_conhecimento_conteudos']);
            exibirCampo('HABILIDADES', $row_plan_edit['smc_habilidades']);
            ?>


            <div class="ls-box">
                <h5 class="ls-title-5">ESTRATÉGIAS METODOLÓGICAS</h5>
                <p>
                    <?php
                    if (!empty($EstrategiasMetodologicas)) {
                        // Obtém os IDs selecionados para estratégias metodológicas
                        $EstrategiasSelecionadas = explode(',', $row_plan_edit['estrategias_metodologicas_marcadas']);
                        $EstrategiasExibidas = [];

                        // Percorre todas as estratégias disponíveis
                        foreach ($EstrategiasMetodologicas as $estrategia) {
                            // Converte o texto da estratégia para o encoding desejado
                            $nome_estrategia = mb_convert_encoding($estrategia['smc_estrategias_metodologicas'], 'UTF-8', 'ISO-8859-1');
                            // Se o ID da estratégia estiver entre os selecionados, adiciona à lista de exibição
                            if (in_array($estrategia['smc_id_estrategias_metodologicas'], $EstrategiasSelecionadas)) {
                                $EstrategiasExibidas[] = $nome_estrategia;
                            }
                        }

                        // Exibe as estratégias selecionadas, ou uma mensagem se nenhuma foi selecionada
                        if (!empty($EstrategiasExibidas)) {
                            echo implode("<br>", $EstrategiasExibidas);
                        } else {
                            echo "Nenhuma estratégia metodológica selecionada.";
                        }
                        
                        // Exibir estratégia metodológica personalizada, se existir
                        if (!empty($row_plan_edit['estrategia_metodologica_personalizada'])) {
                            echo "<br><strong>Outra:</strong> " . $row_plan_edit['estrategia_metodologica_personalizada'];
                        }
                    } else {
                        echo "Nenhuma estratégia metodológica encontrada.";
                    }
                    ?>
                </p>
            </div>


            <div class="ls-box">
                <h5 class="ls-title-5">AVALIAÇÃO</h5>
                <p>
                    <?php
                    if ($row_Etapa['etapa_id_filtro'] == "1") {
                        // Exibição para Educação Infantil
                        if (!empty($AvaliacaoEdinf)) {
                            $AvaliacoesInfSelecionadas = explode(',', $row_plan_edit['avaliacoes_marcadas_edinf']);
                            $AvaliacoesInfExibidas = [];

                            foreach ($AvaliacaoEdinf as $avainf) {
                                if (in_array($avainf['smc_id_avaliacao_edinf'], $AvaliacoesInfSelecionadas)) {
                                    $AvaliacoesInfExibidas[] = mb_convert_encoding($avainf['smc_avaliacao_edinf'], 'UTF-8', 'ISO-8859-1');
                                }
                            }

                            // Exibir as avaliações selecionadas formatadas corretamente
                            if (!empty($AvaliacoesInfExibidas)) {
                                echo implode(" | ", $AvaliacoesInfExibidas);
                            } else {
                                echo "Nenhuma avaliação selecionada.";
                            }
                        } else {
                            echo "Nenhuma avaliação encontrada.";
                        }
                    } else {
                        // Exibição para Anos Iniciais/Finais
                        if (!empty($avaliacoes)) {
                            $avaliacoesSelecionadas = explode(',', $row_plan_edit['avaliacoes_marcadas']);
                            $tipos = ['Processual', 'Somativa', 'Formativa'];
                            foreach ($tipos as $tipo) {
                                echo "<strong>$tipo</strong><br>";
                                foreach ($avaliacoes as $avaliacao) {

                                    if ($avaliacao['smc_tipo'] === $tipo && in_array($avaliacao['smc_id_avaliacao'], $avaliacoesSelecionadas)) {
                                        $descricao = mb_convert_encoding($avaliacao['smc_descricao'], 'UTF-8', 'ISO-8859-1');
                                        echo $descricao . '<br>';
                                    }
                                }
                                echo '<br>';
                            }
                            if (!empty($row_plan_edit['smc_avaliacao_personalizada'])) {
                                echo '<strong>Outra:</strong> ' . $row_plan_edit['smc_avaliacao_personalizada'];
                            }
                        } else {
                            echo "Nenhuma avaliação encontrada.";
                        }
                    }
                    ?>
                </p>
            </div>



            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form ls-box">

                <h5 class="ls-title-5">Parecer da Coordenação Pedagógica</h5>

                <label class="ls-label">
                    <b class="ls-label-text">Status</b>
                    <div class="ls-custom-select">
                        <select class="ls-custom" name="planejamento_correcao">
                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_plan_edit['smc_planejamento_correcao'], ENT_COMPAT, 'utf-8')))) {
                                echo "SELECTED";
                            } ?>>&#128077; Tudo certo</option>
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_plan_edit['smc_planejamento_correcao'], ENT_COMPAT, 'utf-8')))) {
                                echo "SELECTED";
                            } ?>>&#9995; Necessita correção no planejamento
                            </option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_plan_edit['smc_planejamento_correcao'], ENT_COMPAT, 'utf-8')))) {
                                echo "SELECTED";
                            } ?>>&#128076; Planejamento corrigido</option>
                        </select>
                    </div>
                </label>
                <label class="ls-label"> <b class="ls-label-text">Instrução da Coordenação Pedagógica</b>
                    <textarea name="planejamento_feedback" cols="50"
                        rows="5"><?php echo htmlentities($row_plan_edit['smc_feedback'], ENT_COMPAT, 'utf-8'); ?></textarea>
                </label>
                <div class="ls-actions-btn">
                    <input class="ls-btn-primary" type="submit" value="REGISTRAR ACOMPANHAMENTO">
                    <a href="<?php echo "ac-professor.php?codigo=$row_plan_edit[ac_id_professor]" ?>"
                        class="ls-btn">VOLTAR</a>
                </div>
                <input type="hidden" name="planejamento_id"
                    value="<?php echo $row_plan_edit['smc_id_planejamento']; ?>">
                <input type="hidden" name="planejamento_status" value="1">
                <input type="hidden" name="MM_update" value="form1">
            </form>
            <p>&nbsp;</p>
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
                <li class="ls-txt-center hidden-xs"> <a href="tutoriais_video.php"
                        class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a> </li>
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