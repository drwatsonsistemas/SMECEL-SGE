<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php include "fnc/inverteData.php"; ?>
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

$query_Folhas = "SELECT *,
                        CASE folha_mes
                        WHEN '1' THEN 'Janeiro'
                        WHEN '2' THEN 'Fevereiro'
                        WHEN '3' THEN 'Março'
                        WHEN '4' THEN 'Abril'
                        WHEN '5' THEN 'Maio'
                        WHEN '6' THEN 'Junho'
                        WHEN '7' THEN 'Julho'
                        WHEN '8' THEN 'Agosto'
                        WHEN '9' THEN 'Setembro'
                        WHEN '10' THEN 'Outubro'
                        WHEN '11' THEN 'Novembro'
                        WHEN '12' THEN 'Dezembro'
                        END AS folha_mes_nome
FROM smc_folha WHERE folha_id_sec = $row_EscolaLogada[sec_id]
ORDER BY folha_id DESC
";
$Folhas = mysql_query($query_Folhas, $SmecelNovo) or die(mysql_error());
$row_Folhas = mysql_fetch_assoc($Folhas);
$totalRows_Folhas = mysql_num_rows($Folhas);

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
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>


    <main class="ls-main ">
        <div class="container-fluid">

            <h1 class="ls-title-intro ls-ico-docs">FOLHA DE PAGAMENTO</h1>
            <!-- CONTEÚDO -->
            <?php if ($totalRows_Folhas > 0) { ?>
                <table class="ls-table ls-table-striped">
                    <thead>
                        <tr>
                            <th>Mês de referência</th>
                            <th class="hidden-xs">Ano de referência</th>
                            <th class="hidden-xs">Data de abertura</th>
                            <th>Data de fechamento</th>
                            <th>Situação</th>
                            <th class="">Opções</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do { ?>
                            <?php
                            $query_FolhaFunc = "SELECT *
                        FROM smc_folha 
                    INNER JOIN smc_folha_lancamento ON folha_id = folha_lanc_id_folha
                    INNER JOIN smc_vinculo ON folha_lanc_id_vinculo = vinculo_id
                    WHERE folha_id = $row_Folhas[folha_id] AND folha_lanc_id_escola = $row_EscolaLogada[escola_id]";
                            $FolhaFunc = mysql_query($query_FolhaFunc, $SmecelNovo) or die(mysql_error());
                            $row_FolhaFunc = mysql_fetch_assoc($FolhaFunc);
                            $totalRows_FolhaFunc = mysql_num_rows($FolhaFunc);
                            ?>
                            <tr>
                                <td><?= $row_Folhas['folha_mes_nome']; ?></td>
                                <td class="hidden-xs"><?= $row_Folhas['folha_ano']; ?></td>
                                <td class="hidden-xs"><?= inverteData($row_Folhas['folha_data_de']); ?></td>
                                <td><?= inverteData($row_Folhas['folha_data_ate']); ?></td>
                                <td>
                                    <?php if ($row_Folhas['folha_aberta'] == "S") { ?>
                                        <b class="ls-color-success">ABERTA <span
                                                class="ls-ico-checkmark-circle ls-ico-right"></span></b>
                                    <?php } else { ?>
                                        <b class="ls-color-warning">FECHADA <span class="ls-ico-info ls-ico-right"></span></b>
                                    <?php } ?>
                                </td>
                                <td class="">
                                    <?php if ($totalRows_FolhaFunc == 0) { ?>
                                        <a href="#" class="ls-btn-primary ls-ico-plus"
                                            id="add-folha-<?= $row_Folhas['folha_id']; ?>"></a>
                                    <?php } else { ?>
                                        <a href="folha_pagamento_visualizar.php?folha=<?= $row_Folhas['folha_hash']; ?>"
                                            class="ls-btn-primary ls-ico-eye"></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } while ($row_Folhas = mysql_fetch_assoc($Folhas)); ?>
                    </tbody>
                </table>

                <script>
                    // Seleciona todos os botões de adição
                    $('a[id^="add-folha-"]').on('click', function (e) {
                        e.preventDefault();

                        // Obtém o ID da folha (dinamicamente)
                        var folha_id = $(this).attr('id').replace('add-folha-', '');

                        // Confirmação com SweetAlert2
                        Swal.fire({
                            title: 'Você tem certeza?',
                            text: "Deseja realmente adicionar esta folha?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sim, confirmar!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Executa a lógica via AJAX
                                $.ajax({
                                    url: 'processa_folha.php', // Página que vai processar a lógica
                                    method: 'POST',
                                    data: {
                                        folha_id: folha_id
                                    },
                                    success: function (response) {
                                        //console.log(response)
                                        // Exibe mensagem de sucesso
                                        Swal.fire(
                                            'Adicionado!',
                                            'A folha foi adicionada com sucesso.',
                                            'success'
                                        );

                                        // Após 1.5 segundos, atualiza a tabela ou a página
                                        setTimeout(function () {
                                            location.reload(); // Atualiza a página ou você pode usar outra função para atualizar uma parte específica da tabela
                                        }, 1500); // 1.5 segundos
                                    },
                                    error: function () {
                                        Swal.fire(
                                            'Erro!',
                                            'Ocorreu um problema ao processar sua solicitação.',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    });
                </script>

            <?php } else { ?>
                <div class="ls-alert-warning">Nenhuma folha de pagamento encontrada</div>
            <?php } ?>





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

    <script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>