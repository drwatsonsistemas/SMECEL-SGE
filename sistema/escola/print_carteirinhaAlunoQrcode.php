<?php
require_once('../../Connections/SmecelNovoPDO.php');
include('fnc/inverteData.php');
include "fnc/anti_injection.php";
include('../funcoes/url_base.php');
include "fnc/sessionPDO.php";
include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";

// Determinar o ano letivo
$anoLetivo = isset($_GET['ano']) ? $_GET['ano'] : $row_AnoLetivo['ano_letivo_ano'];

// Filtro de situação (opcional)
$st = isset($_GET['st']) ? $_GET['st'] : 0;
$stqry = $st ? "AND vinculo_aluno_situacao = :st " : "";
$nomeFiltro = "Todos"; // Valor padrão quando não há filtro de situação
if ($st) {
    switch ($st) {
        case 1:
            $nomeFiltro = "Matriculados";
            break;
        case 2:
            $nomeFiltro = "Transferidos";
            break;
        case 3:
            $nomeFiltro = "Desistentes";
            break;
        case 4:
            $nomeFiltro = "Falecidos";
            break;
        case 5:
            $nomeFiltro = "Outros";
            break;
        default:
            $nomeFiltro = "Todos";
            break;
    }
}

// Filtro de saída (sozinhos, acompanhados, todos)
$saida = isset($_GET['saida']) ? $_GET['saida'] : 0;
$saidaqry = "";
$saidaFiltro = "Todos";
if ($saida == 1) {
    $saidaqry = "AND vinculo_aluno_saida = 1 ";
    $saidaFiltro = "Acompanhados";
} elseif ($saida == 2) {
    $saidaqry = "AND vinculo_aluno_saida = 2 ";
    $saidaFiltro = "Sozinhos";
}

// Filtro de turma (opcional)
$codTurma = isset($_GET['ct']) ? $_GET['ct'] : 0;
$buscaTurma = $codTurma ? "AND turma_id = :turma_id " : "";

// Consultas usando PDO
try {
    // Escola Logada
    $query_EscolaLogada = "
        SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
               escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf 
        FROM smc_escola
        INNER JOIN smc_sec ON sec_id = escola_id_sec 
        WHERE escola_id = :escola_id";
    $stmtEscolaLogada = $SmecelNovo->prepare($query_EscolaLogada);
    $stmtEscolaLogada->execute([':escola_id' => $row_UsuLogado['usu_escola']]);
    $row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
    $totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();

    // Exibir Turmas
    $query_ExibirTurmas = "
        SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_tipo_atendimento,
               CASE turma_turno
                   WHEN 0 THEN 'INTEGRAL'
                   WHEN 1 THEN 'MATUTINO'
                   WHEN 2 THEN 'VESPERTINO'
                   WHEN 3 THEN 'NOTURNO'
               END AS turma_turno, 
               turma_total_alunos, turma_ano_letivo 
        FROM smc_turma 
        WHERE turma_tipo_atendimento = 1 AND turma_id_escola = :escola_id AND turma_ano_letivo = :ano_letivo $buscaTurma
        ORDER BY turma_turno, turma_etapa, turma_nome ASC";

    // Preparar os parâmetros dinamicamente
    $paramsTurmas = [
        ':escola_id' => $row_UsuLogado['usu_escola'],
        ':ano_letivo' => $anoLetivo
    ];
    if ($codTurma) {
        $paramsTurmas[':turma_id'] = $codTurma;
    }

    $stmtExibirTurmas = $SmecelNovo->prepare($query_ExibirTurmas);
    $stmtExibirTurmas->execute($paramsTurmas);
    $row_ExibirTurmas = $stmtExibirTurmas->fetchAll(PDO::FETCH_ASSOC);
    $totalRows_ExibirTurmas = $stmtExibirTurmas->rowCount();

    if ($totalRows_ExibirTurmas == 0) {
        header("Location: turmasAlunosVinculados.php?nada");
        exit;
    }
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
    <title>Carteirinha do Aluno | SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <script src="js/locastyle.js"></script>
    <style>
        body {
            font-size: 8px;
        }

        #quebra {
            page-break-before: auto;
        }

        @media print {
            .filtragem {
                display: none;
            }
        }
    </style>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="alert('Atenção: Configure sua impressora para o tamanho A4 e formato RETRATO'); self.print();">
    <div class="filtragem"
        style="margin-bottom: 10px; text-align: center; background: #f8f9fa; padding: 5px; border-radius: 4px;">
        <div class="ls-btn-group">
            <a href="?ct=<?php echo $codTurma; ?>&st=<?php echo $st; ?>&saida=0"
                class="ls-btn-sm <?php echo ($saida == 0) ? 'ls-btn-primary' : 'ls-btn-default'; ?>">Todos</a>
            <a href="?ct=<?php echo $codTurma; ?>&st=<?php echo $st; ?>&saida=1"
                class="ls-btn-sm <?php echo ($saida == 1) ? 'ls-btn-primary' : 'ls-btn-default'; ?>">Acompanhados</a>
            <a href="?ct=<?php echo $codTurma; ?>&st=<?php echo $st; ?>&saida=2"
                class="ls-btn-sm <?php echo ($saida == 2) ? 'ls-btn-primary' : 'ls-btn-default'; ?>">Sozinhos</a>
        </div>
    </div>

    <?php foreach ($row_ExibirTurmas as $turma) { ?>
        <?php
        // Exibir Alunos Vinculados
        $query_ExibirAlunosVinculados = "
            SELECT 
                vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
                vinculo_aluno_ano_letivo, vinculo_aluno_hash, vinculo_aluno_boletim, vinculo_aluno_situacao, 
                aluno_id, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_hash, aluno_foto, 
                aluno_nome_social, aluno_filiacao2
            FROM smc_vinculo_aluno 
            INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
            WHERE vinculo_aluno_id_turma = :turma_id $stqry $saidaqry
            ORDER BY aluno_nome ASC";
        $stmtExibirAlunosVinculados = $SmecelNovo->prepare($query_ExibirAlunosVinculados);
        $params = [':turma_id' => $turma['turma_id']];
        if ($st)
            $params[':st'] = $st;
        $stmtExibirAlunosVinculados->execute($params);
        $row_ExibirAlunosVinculados = $stmtExibirAlunosVinculados->fetchAll(PDO::FETCH_ASSOC);
        $totalRows_ExibirAlunosVinculados = $stmtExibirAlunosVinculados->rowCount();
        ?>

        <?php if ($totalRows_ExibirAlunosVinculados > 0) { ?>
            <?php foreach ($row_ExibirAlunosVinculados as $aluno) { ?>
                <?php
                $aux = 'fnc/qr/php/qr_img.php?';
                $aux .= 'd=' . $aluno['vinculo_aluno_id'] . '&';
                $aux .= 'e=M&';
                $aux .= 's=3&';
                $aux .= 't=P';
                ?>
                <div
                    style="display:block; width:86mm; height:54mm; float:left; padding:1mm; margin:0 1mm 1mm 0; border:dotted 0px #000000; background-image:url('../../img/fundoCarteirinha.jpg'); background-repeat: no-repeat; background-size: cover; page-break-inside: avoid;">
                    <br>
                    <h3 style="text-align:center">CARTEIRINHA DO ESTUDANTE</h3>
                    <p style="text-align:center"><small><?php echo htmlspecialchars($row_EscolaLogada['escola_nome']); ?></small>
                    </p>
                    <table width="100%">
                        <tr>
                            <td width="85">
                                <?php if (empty($aluno['aluno_foto'])) { ?>
                                    <img src="<?php echo URL_BASE . 'aluno/fotos/semfoto.jpg'; ?>" style="margin:1mm; width:20mm;">
                                <?php } else { ?>
                                    <img src="<?php echo URL_BASE . 'aluno/fotos/' . htmlspecialchars($aluno['aluno_foto']); ?>"
                                        style="margin:1mm; width:20mm;">
                                <?php } ?>
                            </td>
                            <td>
                                <strong>Aluno(a)</strong><br>
                                <?php echo htmlspecialchars($aluno['aluno_nome_social'] ?: $aluno['aluno_nome']); ?><br><br>
                                <strong>Turma</strong><br>
                                <?php echo htmlspecialchars($turma['turma_nome'] . ' ' . $turma['turma_turno']); ?><br><br>
                                <strong>Nascimento</strong><br>
                                <?php echo htmlspecialchars(inverteData($aluno['aluno_nascimento'])); ?><br><br>
                                <strong>Filiação</strong><br>
                                <?php echo htmlspecialchars($aluno['aluno_filiacao1']); ?><br>
                                <?php echo htmlspecialchars($aluno['aluno_filiacao2']); ?>
                            </td>
                            <td width="90">
                                <div class="ls-txt-center">
                                    <img src="<?php echo htmlspecialchars($aux); ?>" />
                                    <br>MAT <?php echo htmlspecialchars($aluno['vinculo_aluno_id']); ?>
                                </div>
                            </td>
                        </tr>
                        <!-- <?php include_once('relatorios_rodape.php'); ?> -->
                    </table>
                    <br><i style="text-align:center; float:right; margin-right:5px;">Válido durante o Ano Letivo de
                        <?php echo htmlspecialchars($row_AnoLetivo['ano_letivo_ano']); ?></i>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="ls-txt-center"><small><i>Nenhum aluno vinculado na turma.</i></small></p>
        <?php } ?>
    <?php } ?>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script language="Javascript">
        function confirmaExclusao(id) {
            var resposta = confirm("Deseja realmente excluir o vínculo deste aluno?");
            if (resposta) {
                window.location.href = "matriculaExcluir.php?hash=" + id;
            }
        }
    </script>
</body>

</html>