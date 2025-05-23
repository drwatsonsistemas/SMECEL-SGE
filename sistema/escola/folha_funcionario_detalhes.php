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

if (isset($_GET['c']) && !empty($_GET['c'])) {
    $cod = $_GET['c'];
} else {
    die(header('Location: index.php?erro'));
}

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
    f.func_data_nascimento,  -- Adicionado
    f.func_cpf,              -- Adicionado
    f.func_regime,           -- Adicionado
    f.func_admissao,         -- Adicionado
    fc.funcao_id, 
    fc.funcao_nome,
    fe.folha_extra_data,
    fe.folha_extra_tipo, 
    ff.faltas_func_data,
    ff.faltas_tipo_dia_aula,
    COUNT(DISTINCT ff.faltas_func_id) AS total_faltas,  -- Contagem de faltas no período
    COUNT(DISTINCT fe.folha_extra_id) AS total_extras,  -- Contagem de extras no período
    fl_lanc.folha_lanc_obs,
    fl_lanc.folha_lanc_hash
FROM smc_vinculo v
INNER JOIN smc_func f ON f.func_id = v.vinculo_id_funcionario
INNER JOIN smc_funcao fc ON fc.funcao_id = v.vinculo_id_funcao
LEFT JOIN smc_faltas_func ff ON ff.faltas_func_id_func = v.vinculo_id_funcionario
LEFT JOIN smc_folha_extra fe ON fe.folha_extra_id_func = v.vinculo_id_funcionario
INNER JOIN smc_folha fl ON fl.folha_hash = '$folha' -- Junta com a folha filtrando pelo hash
INNER JOIN smc_folha_lancamento fl_lanc ON fl_lanc.folha_lanc_id_vinculo = v.vinculo_id -- Junta com o lançamento da folha
WHERE v.vinculo_id_escola = '$row_EscolaLogada[escola_id]' AND v.vinculo_id = '$cod'
AND v.vinculo_status = 1
AND (ff.faltas_func_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR ff.faltas_func_data IS NULL) -- Filtro de faltas dentro do período da folha
AND (fe.folha_extra_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR fe.folha_extra_data IS NULL) -- Filtro de extras dentro do período da folha
";
$ListaVinculos = mysql_query($query_ListaVinculos, $SmecelNovo) or die(mysql_error());
$row_ListaVinculos = mysql_fetch_assoc($ListaVinculos);
$totalRows_ListaVinculos = mysql_num_rows($ListaVinculos);

$query_TotalFaltas = "
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
    fc.funcao_id, 
    fc.funcao_nome,
    ff.faltas_func_data,
    ff.faltas_tipo_dia_aula
FROM smc_vinculo v
INNER JOIN smc_func f ON f.func_id = v.vinculo_id_funcionario
INNER JOIN smc_funcao fc ON fc.funcao_id = v.vinculo_id_funcao
LEFT JOIN smc_faltas_func ff ON ff.faltas_func_id_func = v.vinculo_id_funcionario
INNER JOIN smc_folha fl ON fl.folha_hash = '$folha' -- Junta com a folha filtrando pelo hash
WHERE v.vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
AND v.vinculo_id = '$cod'
AND v.vinculo_status = 1
AND (ff.faltas_func_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR ff.faltas_func_data IS NULL) -- Filtro de faltas dentro do período da folha
";

$TotalFaltas = mysql_query($query_TotalFaltas, $SmecelNovo) or die(mysql_error());
$row_TotalFaltas = mysql_fetch_assoc($TotalFaltas);
$totalRows_TotalFaltas = mysql_num_rows($TotalFaltas);


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_TotalExtras = "
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
    fc.funcao_id, 
    fc.funcao_nome,
    fe.folha_extra_data,
    fe.folha_extra_tipo
FROM smc_vinculo v
INNER JOIN smc_func f ON f.func_id = v.vinculo_id_funcionario
INNER JOIN smc_funcao fc ON fc.funcao_id = v.vinculo_id_funcao
LEFT JOIN smc_folha_extra fe ON fe.folha_extra_id_func = v.vinculo_id_funcionario
INNER JOIN smc_folha fl ON fl.folha_hash = '$folha' -- Junta com a folha filtrando pelo hash
WHERE v.vinculo_id_escola = '$row_EscolaLogada[escola_id]' 
AND v.vinculo_id = '$cod'
AND v.vinculo_status = 1
AND (fe.folha_extra_data BETWEEN fl.folha_data_de AND fl.folha_data_ate OR fe.folha_extra_data IS NULL) -- Filtro de extras dentro do período da folha
";

$TotalExtras = mysql_query($query_TotalExtras, $SmecelNovo) or die(mysql_error());
$row_TotalExtras = mysql_fetch_assoc($TotalExtras);
$totalRows_TotalExtras = mysql_num_rows($TotalExtras);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

    if ($row_UsuLogado['usu_update'] == "N") {
        header(sprintf("Location: funcListar.php?permissao"));
        exit;
    }

    $updateSQL = sprintf(
        "UPDATE smc_folha_lancamento set folha_lanc_obs=%s WHERE folha_lanc_hash=%s AND folha_lanc_id_vinculo=%s",
        GetSQLValueString($_POST['folha_lanc_obs'], "text"),
        GetSQLValueString($row_ListaVinculos['folha_lanc_hash'], "int"),
        GetSQLValueString($cod, "int")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    $updateGoTo = "folha_funcionario_detalhes.php?&editado";
    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
}

// Processamento do formulário de faltas
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formFalta")) {
    // Verificando permissões
    if ($row_UsuLogado['usu_insert'] == "N") {
        header(sprintf("Location: folha_funcionario_detalhes.php?permissao&folha=$folha&c=$cod"));
        exit;
    }

    // Diretório onde os arquivos serão salvos
    $uploadDir = 'anexo_faltas/';
    $fileName = $_FILES['arquivo_anexo']['name'];
    $fileTmpName = $_FILES['arquivo_anexo']['tmp_name'];
    $fileSize = $_FILES['arquivo_anexo']['size'];
    $fileError = $_FILES['arquivo_anexo']['error'];

    $fileNewName = null; // Inicializa o nome do arquivo

    // Verifica se um arquivo foi anexado
    if (!empty($fileName)) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'docx', 'pdf');

        if (in_array($fileExt, $allowed) && $fileError === 0 && $fileSize < 5000000) {
            $fileNewName = uniqid('', true) . "." . $fileExt;
            $fileDestination = $uploadDir . $fileNewName;

            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                header("Location: folha_funcionario_detalhes.php?erro&folha=$folha&c=$cod");
                exit;
            }
        } else {
            header("Location: folha_funcionario_detalhes.php?arquivoInvalido&folha=$folha&c=$cod");
            exit;
        }
    }

    // Gerar o hash
    $hash = md5(date('Y-m-d H:i:s') . time() . $_POST['faltas_func_id_func'] . $_POST['faltas_func_id_funcao']);

    // Determinar a quantidade de faltas a registrar
    $quantidadeAulas = isset($_POST['quantidade_aulas']) ? (int) $_POST['quantidade_aulas'] : 1;

    // Se o tipo de falta for "DIA", registrar apenas uma falta
    if ($_POST['faltas_tipo_dia_aula'] == 1) { // Tipo "DIA"
        $quantidadeAulas = 1;
    }

    // Inserir os dados no banco de dados
    for ($i = 1; $i <= $quantidadeAulas; $i++) {
        $insertSQL = sprintf(
            "INSERT INTO smc_faltas_func (
                faltas_func_id_func,
                faltas_func_id_escola,
                faltas_func_id_funcao,
                faltas_func_data,
                faltas_func_tipo_jutificativa,
                faltas_func_obs,
                faltas_func_anexo,
                faltas_tipo_dia_aula,
                falta_hash
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($_POST['faltas_func_id_func'], "int"),
            GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
            GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
            GetSQLValueString($_POST['faltas_func_data'], "date"),
            GetSQLValueString($_POST['faltas_func_tipo_jutificativa'], "text"),
            GetSQLValueString($_POST['faltas_func_obs'], "text"),
            GetSQLValueString($fileNewName, "text"),
            GetSQLValueString($_POST['faltas_tipo_dia_aula'], "int"),
            GetSQLValueString($hash, "text")
        );

        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
    }

    header("Location: folha_funcionario_detalhes.php?faltalancada&folha=$folha&c=$cod");
    exit;
}

// Processamento do formulário de extras
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formExtra")) {
    // Verificando permissões
    if ($row_UsuLogado['usu_insert'] == "N") {
        header(sprintf("Location: folha_funcionario_detalhes.php?permissao&folha=$folha&c=$cod"));
        exit;
    }

    // Gerar o hash
    $hash = md5(date('Y-m-d H:i:s') . time() . $_POST['faltas_func_id_func'] . $_POST['folha_extra_data']);

    $quantidadeAulas = isset($_POST['quantidade_aulas']) ? (int) $_POST['quantidade_aulas'] : 1;
    $folha_extra_tipo = (int) $_POST['folha_extra_tipo'];

    // Inserir os dados no banco de dados
    if ($folha_extra_tipo == 1) {
        // Para "DIA", inserir apenas um registro
        $insertSQL = sprintf(
            "INSERT INTO smc_folha_extra (folha_extra_id_func, folha_extra_id_escola, folha_extra_id_funcao, folha_extra_data, folha_extra_tipo, folha_extra_justificativa, folha_extra_hash) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($_POST['faltas_func_id_func'], "int"),
            GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
            GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
            GetSQLValueString($_POST['folha_extra_data'], "date"),
            GetSQLValueString($_POST['folha_extra_tipo'], "int"),
            GetSQLValueString($_POST['folha_extra_justificativa'], "text"),
            GetSQLValueString($hash, "text")
        );

        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
    } elseif ($folha_extra_tipo == 2) {
        // Para "AULA", inserir múltiplos registros com base em quantidade_aulas
        for ($i = 0; $i < $quantidadeAulas; $i++) {
            $insertSQL = sprintf(
                "INSERT INTO smc_folha_extra (folha_extra_id_func, folha_extra_id_escola, folha_extra_id_funcao, folha_extra_data, folha_extra_tipo, folha_extra_justificativa, folha_extra_hash) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($_POST['faltas_func_id_func'], "int"),
                GetSQLValueString($_POST['faltas_func_id_escola'], "int"),
                GetSQLValueString($_POST['faltas_func_id_funcao'], "int"),
                GetSQLValueString($_POST['folha_extra_data'], "date"),
                GetSQLValueString($_POST['folha_extra_tipo'], "int"),
                GetSQLValueString($_POST['folha_extra_justificativa'], "text"),
                GetSQLValueString($hash, "text")
            );

            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());
        }
    }

    header("Location: folha_funcionario_detalhes.php?extralancado&folha=$folha&c=$cod");
    exit;
}

function nome_mes($mes)
{
    switch ($mes) {
        case '01' or '1':
            echo 'Janeiro';
            break;
        case '02' or '2':
            echo 'Fevereiro';
            break;
        case '03' or '3':
            echo 'Março';
            break;
        case '04' or '4':
            echo 'Abril';
            break;
        case '05' or '5':
            echo 'Maio';
            break;
        case '06' or '6':
            echo 'Junho';
            break;
        case '07' or '7':
            echo 'Julho';
            break;
        case '08' or '8':
            echo 'Agosto';
            break;
        case '09' or '9':
            echo 'Setembro';
            break;
        case '10':
            echo 'Outubro';
            break;
        case '11':
            echo 'Novembro';
            break;
        case '12':
            echo 'Dezembro';
        default:
            echo 'Erro';
            break;
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
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>


    <main class="ls-main ">
        <div class="container-fluid">

            <h1 class="ls-title-intro ls-ico-docs">FOLHA DE PAGAMENTO</h1>
            <a href="folha_pagamento_visualizar.php?folha=<?= $folha ?>"
                class="ls-btn-primary ls-ico-chevron-left">Voltar</a>
            <hr>
            <div class="ls-box ls-lg-space ls-ico-bg">

                <div class="col-md-1 col-xs-3">
                    <?php if ($row_ListaVinculos['func_foto'] <> '') { ?>
                        <img src="../../professor/fotos/<?php echo $row_ListaVinculos['func_foto']; ?>" alt=""
                            width="100px" />
                    <?php } else { ?>
                        <img src="../../img/no-photo-user.jpg" alt="" />
                    <?php } ?>
                </div>

                <div class="col-md-11 col-xs-9">
                    <h1 class="ls-title-1 ls-color-theme"><?php echo $row_ListaVinculos['func_nome']; ?></h1>
                    <p>
                        Nascimento:
                        <strong><?php if ($row_ListaVinculos['func_data_nascimento'] == '') { ?>-<?php } else { ?><?php echo date('d/m/Y', strtotime($row_ListaVinculos['func_data_nascimento'])); ?><?php } ?></strong><br>
                        CPF: <strong><?php echo $row_ListaVinculos['func_cpf']; ?></strong><br>
                        Cargo/Função: <strong><?php echo $row_ListaVinculos['funcao_nome']; ?></strong><br>
                        Regime: <strong><?php echo $row_ListaVinculos['func_regime']; ?></strong><br>
                        Admissão:
                        <strong><?php if ($row_ListaVinculos['func_admissao'] == '') { ?>-<?php } else { ?><?php echo date('d/m/Y', strtotime($row_ListaVinculos['func_admissao'])); ?><?php } ?></strong>
                    </p>
                    <?php if (isset($_GET['editado'])) { ?>
                        <div class="ls-alert-success">Vínculo editado com sucesso!</div>
                    <?php } ?>
                </div>

            </div>

            <!-- Botões para abrir os modais -->
            <div class="ls-box">
                <div class="col-md-12">
                    <button data-ls-module="modal" data-target="#modalFalta" class="ls-btn-primary">REGISTRAR
                        FALTA</button>
                    <button data-ls-module="modal" data-target="#modalExtra" class="ls-btn-primary">REGISTRAR
                        EXTRA</button>
                </div>
            </div>

            <!-- Modal de Registro de Faltas -->
            <div class="ls-modal" id="modalFalta">
                <div class="ls-modal-box">
                    <div class="ls-modal-header">
                        <button data-dismiss="modal">&times;</button>
                        <h4 class="ls-modal-title">REGISTRO DE FALTAS</h4>
                        <h3><?php echo $row_ListaVinculos['func_nome']; ?><br><?php echo $row_ListaVinculos['funcao_nome']; ?>
                        </h3>
                    </div>
                    <div class="ls-modal-body">
                        <form method="post" name="formFalta" action="<?php echo $editFormAction; ?>"
                            enctype="multipart/form-data" class="ls-form ls-form-horizontal row">
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">DATA</b>
                                <div class="ls-prefix-group">
                                    <input type="date" name="faltas_func_data" class="ls-field"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </label>

                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">JUSTIFICATIVA</b>
                                <div class="ls-custom-select">
                                    <select name="faltas_func_tipo_jutificativa" class="ls-select" required>
                                        <option value="">ESCOLHA...</option>
                                        <option value="1">1 - ATESTADO</option>
                                        <option value="2">2 - FALTA JUSTIFICADA</option>
                                        <option value="3">3 - SEM JUSTIFICATIVA</option>
                                    </select>
                                </div>
                            </label>

                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">ANEXAR ARQUIVO</b>
                                <input type="file" name="arquivo_anexo" class="ls-field">
                            </label>

                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">TIPO DE FALTA</b>
                                <div class="ls-custom-select">
                                    <select id="faltas_tipo_dia_aula" name="faltas_tipo_dia_aula" class="ls-select"
                                        required onchange="toggleQuantidadeCampoFalta()">
                                        <option value="">ESCOLHA...</option>
                                        <option value="1">1 - DIA</option>
                                        <option value="2">2 - AULA</option>
                                    </select>
                                </div>
                            </label>

                            <label id="quantidadeAulasLabelFalta" class="ls-label col-md-6" style="display: none;">
                                <b class="ls-label-text">QUANTIDADE DE AULAS</b>
                                <input type="number" name="quantidade_aulas" id="quantidade_aulas_falta"
                                    class="ls-input" min="1" placeholder="Digite a quantidade de aulas">
                            </label>

                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">DETALHES</b>
                                <textarea name="faltas_func_obs" cols="50" rows="3" class="ls-field"></textarea>
                            </label>

                            <input type="hidden" name="faltas_func_id_func"
                                value="<?php echo $row_ListaVinculos['vinculo_id_funcionario']; ?>">
                            <input type="hidden" name="faltas_func_id_escola"
                                value="<?php echo $row_ListaVinculos['vinculo_id_escola']; ?>">
                            <input type="hidden" name="faltas_func_id_funcao"
                                value="<?php echo $row_ListaVinculos['vinculo_id_funcao']; ?>">
                            <input type="hidden" name="MM_insert" value="formFalta">

                            <div class="ls-actions-btn">
                                <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
                                <input type="submit" value="REGISTRAR FALTA" class="ls-btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de Registro de Extras -->
            <div class="ls-modal" id="modalExtra">
                <div class="ls-modal-box">
                    <div class="ls-modal-header">
                        <button data-dismiss="modal">&times;</button>
                        <h4 class="ls-modal-title">REGISTRO DE EXTRAS</h4>
                        <h3><?php echo $row_ListaVinculos['func_nome']; ?><br><?php echo $row_ListaVinculos['funcao_nome']; ?>
                        </h3>
                    </div>
                    <div class="ls-modal-body">
                        <form method="post" name="formExtra" action="<?php echo $editFormAction; ?>"
                            enctype="multipart/form-data" class="ls-form ls-form-horizontal row">
                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">DATA</b>
                                <div class="ls-prefix-group">
                                    <input type="date" name="folha_extra_data" class="ls-field"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </label>

                            <label class="ls-label col-md-6">
                                <b class="ls-label-text">TIPO DE EXTRA</b>
                                <div class="ls-custom-select">
                                    <select id="folha_extra_tipo" name="folha_extra_tipo" class="ls-select" required
                                        onchange="toggleQuantidadeCampoExtra()">
                                        <option value="">ESCOLHA...</option>
                                        <option value="1">1 - DIA</option>
                                        <option value="2">2 - AULA</option>
                                    </select>
                                </div>
                            </label>

                            <label id="quantidadeAulasLabelExtra" class="ls-label col-md-6" style="display: none;">
                                <b class="ls-label-text">QUANTIDADE DE AULAS</b>
                                <input type="number" name="quantidade_aulas" id="quantidade_aulas_extra"
                                    class="ls-input" min="1" placeholder="Digite a quantidade de aulas">
                            </label>

                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">JUSTIFICATIVA</b>
                                <textarea name="folha_extra_justificativa" cols="50" rows="3"
                                    class="ls-field"></textarea>
                            </label>

                            <input type="hidden" name="faltas_func_id_func"
                                value="<?php echo $row_ListaVinculos['vinculo_id_funcionario']; ?>">
                            <input type="hidden" name="faltas_func_id_escola"
                                value="<?php echo $row_ListaVinculos['vinculo_id_escola']; ?>">
                            <input type="hidden" name="faltas_func_id_funcao"
                                value="<?php echo $row_ListaVinculos['vinculo_id_funcao']; ?>">
                            <input type="hidden" name="MM_insert" value="formExtra">

                            <div class="ls-actions-btn">
                                <button class="ls-btn ls-float-right" data-dismiss="modal">CANCELAR</button>
                                <input type="submit" value="REGISTRAR EXTRA" class="ls-btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <form method="POST" action="<?php echo $editFormAction; ?>" class="ls-form row">

                    <label class="ls-label col-md-12">
                        <b class="ls-label-text">OBSERVAÇÃO</b>
                        <textarea name="folha_lanc_obs" cols="50" rows="3" tabindex="3"
                            class="ls-field"><?= $row_ListaVinculos['folha_lanc_obs'] ?></textarea>
                    </label>
                    <input type="hidden" name="MM_update" value="form1">
                    <div class="ls-actions-btn">
                        <button type="submit" class="ls-btn">Salvar</button>
                    </div>


                </form>
            </div>

            <div class="col-md-6">
                <h3>Extras (<?= $row_ListaVinculos['total_extras'] ?>)</h3>
                <?php 
                $TotalExtras = mysql_query($query_TotalExtras, $SmecelNovo) or die(mysql_error());
                $totalRows_TotalExtras = mysql_num_rows($TotalExtras);
                
                if ($totalRows_TotalExtras > 0) { ?>
                    <div class="ls-table-responsive">
                        <table class="ls-table ls-table-striped ls-sm-space">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_TotalExtras = mysql_fetch_assoc($TotalExtras)) { ?>
                                    <tr>
                                        <td><?= inverteData($row_TotalExtras['folha_extra_data']) ?></td>
                                        <td>
                                            <?php
                                            if ($row_TotalExtras['folha_extra_tipo'] == 1) {
                                                echo 'Dia';
                                            } else {
                                                echo 'Aula';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="ls-alert-warning">Nenhum extra registrado para esse mês.</div>
                <?php } ?>
            </div>


            <div class="col-md-6">
                <h3>Faltas (<?= $row_ListaVinculos['total_faltas'] ?>)</h3>
                <?php 
                $TotalFaltas = mysql_query($query_TotalFaltas, $SmecelNovo) or die(mysql_error());
                $totalRows_TotalFaltas = mysql_num_rows($TotalFaltas);
                
                if ($totalRows_TotalFaltas > 0) { ?>
                    <div class="ls-table-responsive">
                        <table class="ls-table ls-table-striped ls-sm-space">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_TotalFaltas = mysql_fetch_assoc($TotalFaltas)) { ?>
                                    <tr>
                                        <td><?= inverteData($row_TotalFaltas['faltas_func_data']) ?></td>
                                        <td>
                                            <?php
                                            if ($row_TotalFaltas['faltas_tipo_dia_aula'] == 1) {
                                                echo 'Dia';
                                            } else {
                                                echo 'Aula';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="ls-alert-warning">Nenhuma falta registrada para esse mês.</div>
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

    <script>
        function toggleQuantidadeCampoFalta() {
            var selectTipoFalta = document.getElementById('faltas_tipo_dia_aula');
            var quantidadeAulasLabel = document.getElementById('quantidadeAulasLabelFalta');
            var quantidadeAulasInput = document.getElementById('quantidade_aulas_falta');

            if (selectTipoFalta.value === '2') {
                quantidadeAulasLabel.style.display = 'block';
                quantidadeAulasInput.setAttribute('required', 'required');
            } else {
                quantidadeAulasLabel.style.display = 'none';
                quantidadeAulasInput.removeAttribute('required');
            }
        }

        function toggleQuantidadeCampoExtra() {
            var selectTipoExtra = document.getElementById('folha_extra_tipo');
            var quantidadeAulasLabel = document.getElementById('quantidadeAulasLabelExtra');
            var quantidadeAulasInput = document.getElementById('quantidade_aulas_extra');

            if (selectTipoExtra.value === '2') {
                quantidadeAulasLabel.style.display = 'block';
                quantidadeAulasInput.setAttribute('required', 'required');
            } else {
                quantidadeAulasLabel.style.display = 'none';
                quantidadeAulasInput.removeAttribute('required');
            }
        }

        // Adiciona mensagens de sucesso/erro
        <?php if (isset($_GET["faltalancada"])) { ?>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Falta registrada com sucesso.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php } ?>

        <?php if (isset($_GET["extralancado"])) { ?>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Extra registrado com sucesso.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php } ?>

        <?php if (isset($_GET["erro"])) { ?>
            Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao processar sua solicitação.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php } ?>

        <?php if (isset($_GET["arquivoInvalido"])) { ?>
            Swal.fire({
                title: 'Erro!',
                text: 'O arquivo anexado é inválido.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php } ?>

        <?php if (isset($_GET["permissao"])) { ?>
            Swal.fire({
                title: 'Erro!',
                text: 'Você não tem permissão para realizar esta ação.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php } ?>
    </script>
</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>