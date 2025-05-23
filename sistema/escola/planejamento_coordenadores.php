<?php
require_once('../../Connections/SmecelNovo.php');
include "fnc/session.php";

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Planejamentos = sprintf(
    "SELECT * FROM smc_planejamento_coordenador WHERE id_coordenador = %s ORDER BY data_inicio DESC",
    GetSQLValueString($row_UsuLogado['usu_id'], "int")
);
$Planejamentos = mysql_query($query_Planejamentos, $SmecelNovo) or die(mysql_error());
$row_Planejamentos = mysql_fetch_assoc($Planejamentos);
$totalRows_Planejamentos = mysql_num_rows($Planejamentos);
?>

<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
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

    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO - COORDENAÇÃO PEDAGÓGICA</h1>

            <div class="ls-actions-btn">
                <a href="planejamento_coordenadores_novo.php" class="ls-btn-primary">Novo Planejamento</a>
            </div>

            <!-- Tabela de Planejamentos -->
            <div class="ls-box ls-md-space">
                <?php if ($totalRows_Planejamentos > 0) { ?>
                    <table class="ls-table ls-table-striped" id="tabela-planejamentos">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Temas Integradores</th>
                                <th>Atividade Promovida</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do { ?>
                                <tr data-id="<?php echo $row_Planejamentos['id_planejamento']; ?>">
                                    <td><?php echo date('d/m/Y', strtotime($row_Planejamentos['data_inicio'])) . ' a ' . date('d/m/Y', strtotime($row_Planejamentos['data_fim'])); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row_Planejamentos['temas_integradores']); ?></td>
                                    <td><?php echo htmlspecialchars($row_Planejamentos['atividade_promovida']); ?></td>
                                    <td>
                                        <div data-ls-module="dropdown" class="ls-dropdown">
                                            <a href="#" class="ls-btn-primary">Ações</a>

                                            <ul class="ls-dropdown-nav">
                                                <li><a href="planejamento_coordenadores_editar.php?id=<?php echo $row_Planejamentos['id_planejamento']; ?>"
                                                        class="ls-ico-pencil">Editar</a></li>
                                                <li><a href="#" class="ls-ico-remove excluir-planejamento"
                                                        data-id="<?php echo $row_Planejamentos['id_planejamento']; ?>">Excluir</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php } while ($row_Planejamentos = mysql_fetch_assoc($Planejamentos)); ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p class="ls-alert-info">Nenhum planejamento encontrado.</p>
                <?php } ?>
            </div>
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
                <li><a href="https://webmail.smecel.com.br" target="_blank">> Acesse o webmail de sua escola</a></li>
            </ul>
        </nav>
        <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
            <h3 class="ls-title-2">Ajuda</h3>
            <ul>
                <li class="ls-txt-center hidden-xs">
                    <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
                </li>
                <li><a href="#">> Guia</a></li>
                <li><a href="#">> Wiki</a></li>
            </ul>
        </nav>
    </aside>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Função para exclusão via AJAX
            $('.excluir-planejamento').on('click', function (e) {
                e.preventDefault(); // Impede o comportamento padrão do link
                e.stopPropagation(); // Impede a propagação do evento para outros handlers

                var id = $(this).data('id'); // Pega o ID do planejamento
                var row = $('tr[data-id="' + id + '"]'); // Linha correspondente na tabela

                // Confirmação antes de excluir com SweetAlert2
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Você está prestes a excluir este planejamento. Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // URL da requisição
                        var url = 'planejamento_coordenadores_deletar.php';
                        console.log('URL da requisição:', window.location.origin + '/' + url);

                        // Requisição AJAX para excluir
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: { id: id },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    // Remove a linha da tabela com animação
                                    row.fadeOut(300, function () {
                                        $(this).remove();
                                        // Verifica se a tabela está vazia
                                        if ($('#tabela-planejamentos tbody tr').length === 0) {
                                            $('#tabela-planejamentos').replaceWith('<p class="ls-alert-info">Nenhum planejamento encontrado.</p>');
                                        }
                                    });
                                    // Mensagem de sucesso com SweetAlert2
                                    Swal.fire({
                                        title: 'Excluído!',
                                        text: 'Planejamento excluído com sucesso.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    // Mensagem de erro com SweetAlert2
                                    Swal.fire({
                                        title: 'Erro!',
                                        text: 'Erro ao excluir o planejamento: ' + response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.log('Resposta bruta do servidor:', xhr.responseText); // Log da resposta bruta
                                // Mensagem de erro com SweetAlert2
                                Swal.fire({
                                    title: 'Erro!',
                                    text: 'Erro na requisição AJAX: ' + xhr.responseText,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>

<?php
mysql_free_result($Planejamentos);
mysql_free_result($UsuLogado);
mysql_free_result($EscolaLogada);
?>