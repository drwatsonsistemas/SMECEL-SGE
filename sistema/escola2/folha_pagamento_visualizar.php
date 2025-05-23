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

if (isset($_GET['folha']) && !empty($_GET['folha'])) {
    $folha = $_GET['folha'];
} else {
    die(header('Location: index.php?erro'));
}

$query_Folhas = "SELECT * FROM smc_folha WHERE folha_hash = '$_GET[folha]'";
$Folhas = mysql_query($query_Folhas, $SmecelNovo) or die(mysql_error());
$row_Folhas = mysql_fetch_assoc($Folhas);
$totalRows_Folhas = mysql_num_rows($Folhas);

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
$query_ListaVinculos = "
SELECT 
    v.vinculo_id, 
    v.vinculo_id_escola, 
    v.vinculo_acesso, 
    v.vinculo_id_funcionario, 
    v.vinculo_id_funcao, 
    v.vinculo_data_inicio,
    v.vinculo_carga_horaria, 
    DATE_FORMAT(v.vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, 
    v.vinculo_obs,
    f.func_id, 
    f.func_nome, 
    r.regime_nome AS func_regime_nome, -- Nome do regime vindo da tabela smc_regime
    fc.funcao_id, 
    fc.funcao_nome, 
    -- Contagem de faltas por tipo (dias e aulas)
    COUNT(DISTINCT CASE WHEN ff.faltas_tipo_dia_aula = 1 THEN ff.faltas_func_id END) AS total_faltas_dias,  -- Faltas por dias
    COUNT(DISTINCT CASE WHEN ff.faltas_tipo_dia_aula = 2 THEN ff.faltas_func_id END) AS total_faltas_aulas, -- Faltas por aulas
    -- Contagem de extras por tipo (dias e aulas)
    COUNT(DISTINCT CASE WHEN fe.folha_extra_tipo = 1 THEN fe.folha_extra_id END) AS total_extras_dias,  -- Extras por dias
    COUNT(DISTINCT CASE WHEN fe.folha_extra_tipo = 2 THEN fe.folha_extra_id END) AS total_extras_aulas, -- Extras por aulas
    fl_lanc.folha_lanc_obs,  -- Observação do lançamento da folha
    fl.folha_hash,
    fl.folha_ano,
    CASE fl.folha_mes
        WHEN '1' THEN 'JANEIRO'
        WHEN '2' THEN 'FEVEREIRO'
        WHEN '3' THEN 'MARÇO'
        WHEN '4' THEN 'ABRIL'
        WHEN '5' THEN 'MAIO'
        WHEN '6' THEN 'JUNHO'
        WHEN '7' THEN 'JULHO'
        WHEN '8' THEN 'AGOSTO'
        WHEN '9' THEN 'SETEMBRO'
        WHEN '10' THEN 'OUTUBRO'
        WHEN '11' THEN 'NOVEMBRO'
        WHEN '12' THEN 'DEZEMBRO'
    END AS folha_mes_nome
FROM smc_vinculo v
INNER JOIN smc_func f ON f.func_id = v.vinculo_id_funcionario
LEFT JOIN smc_regime r ON r.id_regime = f.func_regime -- Novo INNER JOIN com a tabela smc_regime
LEFT JOIN smc_funcao fc ON fc.funcao_id = v.vinculo_id_funcao
LEFT JOIN smc_faltas_func ff ON ff.faltas_func_id_func = v.vinculo_id_funcionario
LEFT JOIN smc_folha_extra fe ON fe.folha_extra_id_func = v.vinculo_id_funcionario
LEFT JOIN smc_folha fl ON fl.folha_hash = '$folha' -- Junta com a folha filtrando pelo hash
LEFT JOIN smc_folha_lancamento fl_lanc ON fl_lanc.folha_lanc_id_vinculo = v.vinculo_id -- Junta com o lançamento da folha
WHERE v.vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND v.vinculo_status = 1
AND (ff.faltas_func_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR ff.faltas_func_data IS NULL) -- Filtro de faltas dentro do período da folha
AND (fe.folha_extra_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR fe.folha_extra_data IS NULL) -- Filtro de extras dentro do período da folha
GROUP BY v.vinculo_id, f.func_nome, fc.funcao_nome
ORDER BY f.func_nome ASC
";

$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListaFunc = "
SELECT 
vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa 
FROM smc_vinculo 
INNER JOIN smc_func 
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao
ON funcao_id = vinculo_id_funcao 
WHERE vinculo_id_escola = '$row_EscolaLogada[escola_id]'
AND vinculo_status = 1
ORDER BY func_nome ASC
";
$ListaFunc = mysql_query($query_ListaFunc, $SmecelNovo) or die(mysql_error());
$row_ListaFunc = mysql_fetch_assoc($ListaFunc);
$totalRows_ListaFunc = mysql_num_rows($ListaFunc);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

    // Verificando permissões
    if ($row_UsuLogado['usu_insert'] == "N") {
        header(sprintf("Location: funcListar.php?permissao"));
        exit;
    }

    $hash = md5($_POST['vinculo_id'] . date('Ymdhis'));
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $insertSQL = sprintf("INSERT INTO smc_folha_lancamento 
    (folha_lanc_id_folha, folha_lanc_id_escola, folha_lanc_id_vinculo, folha_lanc_hash) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($_POST['folha_id'], "int"),
        GetSQLValueString($row_EscolaLogada['escola_id'], "int"),
        GetSQLValueString($_POST['vinculo_id'], "int"),
        GetSQLValueString($hash, "text")
    );

    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    $insertGoTo = "folha_pagamento_visualizar.php?folha=$folha&adicionado";

    header(sprintf("Location: %s", $insertGoTo));
    exit;

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
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>


    <main class="ls-main ">
        <div class="container-fluid">

            <h1 class="ls-title-intro ls-ico-docs">FOLHA DE PAGAMENTO -
                <?= $row_ListaVinculos['folha_mes_nome'] . "/" . $row_ListaVinculos['folha_ano'] ?>
            </h1>
            <a href="folha_pagamento.php" class="ls-btn-primary ls-ico-chevron-left">Voltar</a>
            <hr>
            <button data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary ls-ico-user">Adicionar
                funcionário</button>

            <?php if (isset($_GET['adicionado'])) { ?>
                <hr>
                <div class="ls-alert-success">Funcionário adicionado</div>
            <?php } ?>
            <!-- CONTEÚDO -->
            <?php if ($totalRows_ListaVinculos > 0) { ?>
                <table class="ls-table ls-table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2" width="50"></th>
                            <th rowspan="2" width="300">Funcionário</th>
                            <th rowspan="2">Função</th>
                            <th rowspan="2" width="50">CH</th>
                            <th rowspan="2">Situação</th>
                            <th colspan="2" class="ls-txt-center">Extras</th> <!-- Extras no topo -->
                            <th colspan="2" class="ls-txt-center">Faltas</th> <!-- Faltas no topo -->
                            <th rowspan="2">Observação</th>
                            <th rowspan="2" width="50"></th>
                        </tr>
                        <tr>
                            <!-- Cabeçalhos de subtópicos "Dias" e "Aulas" para Extras e Faltas -->
                            <th class="ls-txt-center">Dias</th>
                            <th class="ls-txt-center">Aulas</th>
                            <th class="ls-txt-center">Dias</th>
                            <th class="ls-txt-center">Aulas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0; ?>
                        <?php do { ?>
                            <tr>
                                <td class="ls-txt-center"><?= ++$count ?></td>
                                <td>
                                    <a href="folha_funcionario_detalhes.php?c=<?= $row_ListaVinculos['vinculo_id'] ?>&folha=<?= $row_ListaVinculos['folha_hash'] ?>&escola=<?= $escola ?>"
                                        class="ls-ico-user">
                                        <?= $row_ListaVinculos['func_nome'] ?>
                                    </a>
                                </td>
                                <td><?= $row_ListaVinculos['funcao_nome'] ?></td>
                                <td><?= $row_ListaVinculos['vinculo_carga_horaria']; ?></td>
                                <td><?= $row_ListaVinculos['func_regime_nome'] ?></td>

                                <!-- Exibe o total de aulas de faltas -->
                                <td class="ls-txt-center"><span class="<?php if($row_ListaVinculos['total_extras_dias'] > 0){echo 'ls-tag-info';} ?>"><?= $row_ListaVinculos['total_extras_dias'] ?></span></td>
                                <!-- Exibe o total de dias extras -->
                                <td class="ls-txt-center"><span class="<?php if($row_ListaVinculos['total_extras_aulas'] > 0){echo 'ls-tag-info';} ?>"><?= $row_ListaVinculos['total_extras_aulas'] ?></span></td>

                                <td class="ls-txt-center"><span class="<?php if($row_ListaVinculos['total_faltas_dias'] > 0){echo 'ls-tag-danger';} ?>"><?= $row_ListaVinculos['total_faltas_dias'] ?></span></td>
                                <!-- Exibe o total de dias de faltas -->
                                <td class="ls-txt-center"><span class="<?php if($row_ListaVinculos['total_faltas_aulas'] > 0){echo 'ls-tag-danger';} ?>"><?= $row_ListaVinculos['total_faltas_aulas'] ?></span></td>
                                <!-- Exibe o total de aulas extras -->
                                <td><?= $row_ListaVinculos['folha_lanc_obs'] ?></td>
                                <td class="ls-txt-center">
                                    <a href="javascript:func()"
                                        onclick="confirmaExclusao('<?php echo $row_ListaVinculos['vinculo_id']; ?>','<?php echo $row_ListaVinculos['func_nome']; ?>','<?php echo $folha; ?>')"
                                        class="ls-ico-cancel-circle ls-divider ls-color-danger"></a>
                                </td>
                            </tr>
                        <?php } while ($row_ListaVinculos = mysql_fetch_assoc($ListaVinculos)); ?>
                    </tbody>
                </table>


            <?php } else { ?>
                <div class="ls-alert-warning">Nenhuma folha de pagamento encontrada</div>
            <?php } ?>

            <!-- CONTEÚDO -->
        </div>
    </main>

    <div class="ls-modal" id="myAwesomeModal">
        <div class="ls-modal-box">
            <form action="<?= $editFormAction; ?>" method="post">
                <div class="ls-modal-header">
                    <button data-dismiss="modal">&times;</button>
                    <h4 class="ls-modal-title">Adicionar funcionário</h4>
                </div>
                <div class="ls-modal-body" id="myModalBody">
                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">Adicionar</b>
                        <div class="ls-custom-select">
                            <select class="ls-custom" name="vinculo_id">
                                <?php do { ?>
                                    <option value="<?= $row_ListaFunc['vinculo_id'] ?>"><?= $row_ListaFunc['func_nome'] ?>
                                    </option>
                                <?php } while ($row_ListaFunc = mysql_fetch_assoc($ListaFunc)); ?>
                            </select>
                        </div>
                        <input type="hidden" name="folha_id" value="<?= $row_Folhas['folha_id']; ?>">
                        <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
                    </label>
                </div>
                <div class="ls-modal-footer">
                    <button class="ls-btn ls-float-right" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="ls-btn-primary">Adicionar</button>
                    <input type="hidden" name="MM_insert" value="form1">
                </div>
            </form>
        </div>
    </div><!-- /.modal -->

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script language="Javascript">
        function confirmaExclusao(id, nome, folha) {
            var resposta = confirm("Deseja realmente remover o vínculo do(a) colaborador(a) " + nome + " nesta escola?");
            if (resposta == true) {
                window.location.href = "excluirVinculoFolha.php?cod=" + id + "&nome=" + nome + "&folha=" + folha;
            }
        }
    </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>