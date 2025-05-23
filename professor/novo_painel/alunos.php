<?php
require_once('../../Connections/SmecelNovoPDO.php');
include "conf/session.php";
include "fnc/anti_injection.php";

try {
    // Validação de entrada
    $turma_id = isset($_GET['turma']) && filter_var($_GET['turma'], FILTER_VALIDATE_INT) ? (int)$_GET['turma'] : -1;
    $escola_id = isset($_GET['escola']) && filter_var($_GET['escola'], FILTER_VALIDATE_INT) ? (int)$_GET['escola'] : -1;

    if ($turma_id <= 0 || $escola_id <= 0) {
        header("Location: seleciona_turma.php");
        exit;
    }

    // Query para buscar alunos
    $query_Alunos = "
        SELECT 
            va.vinculo_aluno_id, va.vinculo_aluno_id_aluno, va.vinculo_aluno_id_turma, va.vinculo_aluno_situacao, 
            va.vinculo_aluno_ano_letivo, va.vinculo_aluno_hash, va.vinculo_aluno_dependencia, 
            a.aluno_id, a.aluno_nome, a.aluno_foto, a.aluno_aluno_com_deficiencia, a.aluno_tipo_deficiencia, 
            t.turma_id, t.turma_nome, t.turma_ano_letivo, t.turma_etapa, t.turma_id_escola,
            CASE va.vinculo_aluno_situacao
                WHEN 1 THEN 'MATRICULADO'
                WHEN 2 THEN 'TRANSFERIDO(A)'
                WHEN 3 THEN 'DESISTENTE'
                WHEN 4 THEN 'FALECIDO(A)'
                WHEN 5 THEN 'OUTROS'
            END AS vinculo_aluno_situacao_nome
        FROM smc_vinculo_aluno va
        INNER JOIN smc_aluno a ON a.aluno_id = va.vinculo_aluno_id_aluno
        INNER JOIN smc_turma t ON t.turma_id = va.vinculo_aluno_id_turma
        WHERE va.vinculo_aluno_id_turma = :turma_id 
          AND va.vinculo_aluno_ano_letivo = :ano_letivo
          AND t.turma_id_escola = :escola_id
        ORDER BY a.aluno_nome";

    $stmt_Alunos = $SmecelNovo->prepare($query_Alunos);
    $stmt_Alunos->bindValue(':turma_id', $turma_id, PDO::PARAM_INT);
    $stmt_Alunos->bindValue(':ano_letivo', $row_AnoLetivo['ano_letivo_ano'], PDO::PARAM_INT);
    $stmt_Alunos->bindValue(':escola_id', $escola_id, PDO::PARAM_INT);
    $stmt_Alunos->execute();
    $row_Alunos = $stmt_Alunos->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_Alunos = $stmt_Alunos->rowCount();

    // Query para dados da escola
    $query_Escola = "
        SELECT escola_id, escola_nome
        FROM smc_escola 
        WHERE escola_id = :escola_id";
    $stmt_Escola = $SmecelNovo->prepare($query_Escola);
    $stmt_Escola->bindValue(':escola_id', $escola_id, PDO::PARAM_INT);
    $stmt_Escola->execute();
    $row_Escola = $stmt_Escola->fetch(PDO::FETCH_ASSOC);
    $totalRows_Escola = $stmt_Escola->rowCount();

    if ($totalRows_Alunos == 0 || $totalRows_Escola == 0) {
        $error_message = $totalRows_Alunos == 0 ? "Nenhum aluno encontrado para esta turma." : "Escola não encontrada.";
    }

} catch (PDOException $e) {
    $error_message = "Erro na consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
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
            <h1 class="ls-title-intro ls-ico-users">ALUNOS | Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

            <div class="ls-box" style="border-left: 5px #0066CC solid;">
                <p>ESCOLA: <strong><?php echo htmlspecialchars($row_Escola['escola_nome'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                <p>TURMA: <strong><?php echo htmlspecialchars($row_Alunos[0]['turma_nome'] ? $row_Alunos[0]['turma_nome'] : 'N/A', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                <p>TOTAL DE ALUNOS: <strong><?php echo $totalRows_Alunos; ?></strong></p>
            </div>

            <?php if (isset($error_message)) { ?>
                <div class="ls-alert-warning"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    <a href="seleciona_turma.php" class="ls-btn ls-ico-chevron-left">Voltar</a>
                </div>
            <?php } else { ?>
                <p>
                    <a href="seleciona_turma.php?escola=<?php echo htmlspecialchars($escola_id, ENT_QUOTES, 'UTF-8'); ?>" 
                       class="ls-btn ls-ico-chevron-left">Voltar</a>
                </p>
                <br>
                <table class="ls-table ls-sm-space ls-table-layout-auto ls-full-width ls-height-auto">
                    <thead>
                        <tr>
                            <th width="80">Foto</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($row_Alunos as $aluno) { ?>
                            <tr>
                                <td>
                                    <?php if (empty($aluno['aluno_foto'])) { ?>
                                        <img src="../../aluno/fotos/semfoto.jpg" alt="Foto do aluno" class="hoverable aluno circle" width="100%">
                                    <?php } else { ?>
                                        <img src="../../aluno/fotos/<?php echo htmlspecialchars($aluno['aluno_foto'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="Foto de <?php echo htmlspecialchars($aluno['aluno_nome'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             class="hoverable aluno circle" width="100%">
                                    <?php } ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($aluno['aluno_nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <?php if ($aluno['vinculo_aluno_situacao'] != "1") { ?>
                                        | <span class="ls-color-danger"><?php echo htmlspecialchars($aluno['vinculo_aluno_situacao_nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php } ?>
                                    <?php if ($aluno['vinculo_aluno_dependencia'] == "S") { ?>
                                        | <span class="ls-color-danger">CUMPRINDO DEPENDÊNCIA</span>
                                    <?php } ?>
                                    <?php if ($aluno['aluno_aluno_com_deficiencia'] == "1") { ?>
                                        | <img src="../../img/pne.png" width="15px" style="cursor:pointer" 
                                               alt="Aluno com necessidades especiais" 
                                               title="Necessidades especiais: <?php echo htmlspecialchars($aluno['aluno_tipo_deficiencia'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php } ?>
                                </td>
                                <td>
                                    <button class="ls-btn ls-btn-xs ls-btn-primary" data-ls-module="modal" 
                                            data-target="#modalOcorrencia" 
                                            data-aluno-id="<?php echo htmlspecialchars($aluno['aluno_id'], ENT_QUOTES, 'UTF-8'); ?>" 
                                            data-aluno-nome="<?php echo htmlspecialchars($aluno['aluno_nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                        Lançar Ocorrência
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Modal para Lançar Ocorrência -->
                <div class="ls-modal" id="modalOcorrencia">
                    <div class="ls-modal-box">
                        <div class="ls-modal-header">
                            <h4 class="ls-modal-title">Lançar Ocorrência</h4>
                        </div>
                        <div class="ls-modal-body">
                            <form id="formOcorrencia" action="salvar_ocorrencia.php" method="POST">
                                <input type="hidden" name="aluno_id" id="aluno_id">
                                <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($row_ProfLogado['func_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="turma_id" value="<?php echo htmlspecialchars($turma_id, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="ls-form-group">
                                    <label for="aluno_nome">Aluno</label>
                                    <input type="text" id="aluno_nome" class="ls-form-control" readonly>
                                </div>
                                <div class="ls-form-group">
                                    <label for="ocorrencia_data">Data</label>
                                    <input type="date" id="ocorrencia_data" name="ocorrencia_data" class="ls-form-control" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="ls-form-group">
                                    <label for="ocorrencia_tipo">Tipo</label>
                                    <select id="ocorrencia_tipo" name="ocorrencia_tipo" class="ls-form-control" required>
                                        <option value="disciplinar">Disciplinar</option>
                                        <option value="academica">Acadêmica</option>
                                        <option value="comportamental">Comportamental</option>
                                    </select>
                                </div>
                                <div class="ls-form-group">
                                    <label for="ocorrencia_descricao">Descrição</label>
                                    <textarea id="ocorrencia_descricao" name="ocorrencia_descricao" class="ls-form-control" 
                                              rows="4" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="ls-modal-footer">
                            <button class="ls-btn ls-btn-primary" onclick="$('#formOcorrencia').submit()">Salvar</button>
                            <button class="ls-btn ls-btn-danger" data-ls-module="modal" data-action="close">Cancelar</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Preencher modal com dados do aluno
            $('button[data-ls-module="modal"]').on('click', function() {
                const alunoId = $(this).data('aluno-id');
                const alunoNome = $(this).data('aluno-nome');
                $('#aluno_id').val(alunoId);
                $('#aluno_nome').val(alunoNome);
            });

            // Validação do formulário
            $('#formOcorrencia').on('submit', function(e) {
                const descricao = $('#ocorrencia_descricao').val().trim();
                if (descricao.length < 5) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'A descrição deve ter pelo menos 5 caracteres.'
                    });
                }
            });
        });
    </script>
</body>
</html>