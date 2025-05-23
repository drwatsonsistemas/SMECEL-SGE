<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

// Recuperando os parâmetros do período da URL
$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : null;
$fim = isset($_GET['fim']) ? $_GET['fim'] : null;

if (!$inicio || !$fim) {
    die(header("Location: rel.php?nada"));
}


include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);


$query_FuncionariosVinculados = "
SELECT 
    smc_func.func_id,
    smc_func.func_nome,
    smc_func.func_cpf,
    smc_func.func_data_nascimento,
    smc_func.func_telefone,
    smc_func.func_email,
    smc_func.func_regime,
    smc_funcao.funcao_nome,
    smc_regime.id_regime,
    smc_regime.regime_nome,
    COUNT(smc_faltas_func.faltas_func_id) AS total_faltas
FROM smc_vinculo
INNER JOIN smc_func ON smc_func.func_id = smc_vinculo.vinculo_id_funcionario
INNER JOIN smc_funcao ON smc_funcao.funcao_id = smc_vinculo.vinculo_id_funcao
LEFT JOIN smc_regime ON smc_func.func_regime = smc_regime.id_regime
LEFT JOIN smc_faltas_func ON smc_faltas_func.faltas_func_id_func = smc_func.func_id 
    AND smc_faltas_func.faltas_func_data BETWEEN '$inicio' AND '$fim'
WHERE smc_vinculo.vinculo_id_escola = '{$row_UsuLogado['usu_escola']}'
AND smc_vinculo.vinculo_status != 2
GROUP BY smc_func.func_id
ORDER BY smc_func.func_nome ASC";

$FuncionariosVinculados = mysql_query($query_FuncionariosVinculados, $SmecelNovo) or die(mysql_error());
$row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados);
$totalRows_FuncionariosVinculados = mysql_num_rows($FuncionariosVinculados);
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

    <title>RELAÇÃO DE FUNCIONÁRIOS (COMPLETO) | SMECEL - Sistema de Gestão Escolar</title>
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
        table.bordasimples {
            border-collapse: collapse;
            font-size: 7px;
        }

        table.bordasimples tr td {
            border: 1px solid #808080;
            padding: 2px;
            font-size: 9px;
        }

        table.bordasimples tr th {
            border: 1px solid #808080;
            padding: 2px;
            font-size: 9px;
        }

        .foo {

            writing-mode: vertical-lr;
            -webkit-writing-mode: vertical-lr;
            -ms-writing-mode: vertical-lr;

            /* 	-webkit-transform:rotate(180deg); //tente 90 no lugar de 270
    -moz-transform:rotate(180deg);
    -o-transform: rotate(180deg); */

        }
    </style>

    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body onload="self.print();">

    <div class="ls-txt-center">

        <?php if ($row_EscolaLogada['escola_logo'] <> "") { ?><img
                src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt=""
                width="60px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt=""
                width="60px" /><?php } ?><br>
        <strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong><br>
        <small>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -<br>
            ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>,
            <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?>
            <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP:
            <?php echo $row_EscolaLogada['escola_cep']; ?><br>
            CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
            <?php echo $row_EscolaLogada['escola_telefone1']; ?></small>

        <br><br>
        <p><strong>Relatório de Faltas - Período: <?php echo date("d/m/Y", strtotime($inicio)); ?> a
                <?php echo date("d/m/Y", strtotime($fim)); ?></strong></p>

        </div>

        <?php if ($totalRows_FuncionariosVinculados > 0) { ?>
            <table width="100%" class="ls-sm-space ls-table-striped bordasimples">
                <thead>
                    <tr>
                        <th class="ls-data-descending">#</th>
                        <th class="ls-txt-center">Nome</th>
                        <th class="ls-txt-center">Função</th>
                        <th class="ls-txt-center">CPF</th>
                        <th class="ls-txt-center">Regime</th>
                        <th class="ls-txt-center">Data de Nascimento</th>
                        <th class="ls-txt-center">Faltas no Período</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 0;
                    do {
                        $count++; ?>
                        <tr>
                            <td class="ls-txt-center"><?php echo $count; ?></td>
                            <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['func_nome']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['funcao_nome']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['func_cpf']; ?></td>
                            <td class="ls-txt-center"><?php echo $row_FuncionariosVinculados['regime_nome']; ?></td>
                            <td class="ls-txt-center">
                                <?php echo date("d/m/Y", strtotime($row_FuncionariosVinculados['func_data_nascimento'])); ?>
                            </td>
                            <td class="ls-txt-center">
                                <strong><?php echo $row_FuncionariosVinculados['total_faltas']; ?></strong></td>
                        </tr>
                    <?php } while ($row_FuncionariosVinculados = mysql_fetch_assoc($FuncionariosVinculados)); ?>
                </tbody>
                <?php include_once('relatorios_rodape.php') ?>
            </table>
        <?php } else { ?>
            <div class="ls-alert-info"><strong>Atenção:</strong> Nenhum funcionário com faltas no período selecionado.</div>
        <?php } ?>



        <!-- We recommended use jQuery 1.10 or up -->
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($FuncionariosVinculados);
?>