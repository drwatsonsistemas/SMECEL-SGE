<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
    session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
    $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
    //to fully log out a visitor we need to clear the session varialbles
    $_SESSION['MM_Username'] = NULL;
    $_SESSION['MM_UserGroup'] = NULL;
    $_SESSION['PrevUrl'] = NULL;
    unset($_SESSION['MM_Username']);
    unset($_SESSION['MM_UserGroup']);
    unset($_SESSION['PrevUrl']);

    $logoutGoTo = "../../index.php?exit";
    if ($logoutGoTo) {
        header("Location: $logoutGoTo");
        exit;
    }
}
?>
<?php
if (!isset($_SESSION)) {
    session_start();
}
$MM_authorizedUsers = "1,99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
    // For security, start by assuming the visitor is NOT authorized. 
    $isValid = False;

    // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
    // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
    if (!empty($UserName)) {
        // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
        // Parse the strings into arrays. 
        $arrUsers = Explode(",", $strUsers);
        $arrGroups = Explode(",", $strGroups);
        if (in_array($UserName, $arrUsers)) {
            $isValid = true;
        }
        // Or, you may restrict access to only certain users based on their username. 
        if (in_array($UserGroup, $arrGroups)) {
            $isValid = true;
        }
        if (($strUsers == "") && false) {
            $isValid = true;
        }
    }
    return $isValid;
}

$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?"))
        $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}
?>
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

require_once('funcoes/usuLogado.php');
require_once('funcoes/anoLetivo.php');


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


$colname_Matriz = "-1";
if (isset($_GET['hash'])) {
    $colname_Matriz = $_GET['hash'];
}

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$campo_exp_id = isset($_GET['campo_exp_id']) ? $_GET['campo_exp_id'] : null;

// Processar o formulário do campo de experiência
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $insertSQL = sprintf(
        "INSERT INTO smc_campos_exp (campos_exp_nome, campos_exp_mais, campos_exp_orientacoes, campos_exp_direitos, campo_exp_sec_id) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['campos_exp_nome'], "text"),
        GetSQLValueString($_POST['campos_exp_mais'], "text"),
        GetSQLValueString($_POST['campos_exp_orientacoes'], "text"),
        GetSQLValueString($_POST['campos_exp_direitos'], "text"),
        GetSQLValueString($_POST['campo_exp_sec_id'], "int")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    // Redireciona para o próximo passo com o ID do campo criado
    $campo_exp_id = mysql_insert_id();
    header("Location: matriz_disciplina_personalizada.php?hash=" . $colname_Matriz . "&step=2&campo_exp_id=" . $campo_exp_id);
    exit;
}

// Processar o formulário dos objetivos
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
    $insertSQL = sprintf(
        "INSERT INTO smc_campos_exp_objetivos (campos_exp_obj_id_campos_exp, campos_exp_obj_faixa_et_cod, campos_exp_obj_campos_exp, campos_exp_obj_abordagem, campos_exp_obj_sugestoes) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['campos_exp_obj_id_campos_exp'], "int"),
        GetSQLValueString($_POST['campos_exp_obj_faixa_et_cod'], "int"),
        GetSQLValueString($_POST['campos_exp_obj_campos_exp'], "text"),
        GetSQLValueString($_POST['campos_exp_obj_abordagem'], "text"),
        GetSQLValueString($_POST['campos_exp_obj_sugestoes'], "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result2 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    // Redireciona de volta para o mesmo passo para permitir adicionar mais objetivos
    header("Location: matriz_disciplina_personalizada.php?hash=" . $colname_Matriz . "&step=2&campo_exp_id=" . $_POST['campos_exp_obj_id_campos_exp']);
    exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriz = sprintf("
  SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_criterio_avaliativo, ca_id, ca_descricao, ca_forma_avaliacao, ca_questionario_conceitos, ca_etapa_id 
  FROM smc_matriz
  INNER JOIN smc_criterios_avaliativos ON ca_id = matriz_criterio_avaliativo 
  WHERE matriz_hash = %s", GetSQLValueString($colname_Matriz, "text"));
$Matriz = mysql_query($query_Matriz, $SmecelNovo) or die(mysql_error());
$row_Matriz = mysql_fetch_assoc($Matriz);
$totalRows_Matriz = mysql_num_rows($Matriz);

if ($totalRows_Matriz < 1) {
    $semEscolas = "index.php?erro";
    header(sprintf("Location: %s", $semEscolas));
}




mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ListarDisciplinas = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_eixo, matriz_disciplina_id_disciplina, disciplina_cor_fundo, matriz_disciplina_ch_ano, matriz_disciplina_reprova, disciplina_id, disciplina_nome, disciplina_nome_abrev, disciplina_bncc, disciplina_eixo_id, disciplina_eixo_nome,
CASE matriz_disciplina_reprova
WHEN 'S' THEN 'SIM'
WHEN 'N' THEN 'NÃO'
END AS matriz_disciplina_reprova 
FROM smc_matriz_disciplinas 
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina
LEFT JOIN smc_disciplina_eixos ON matriz_disciplina_eixo = disciplina_eixo_id
WHERE matriz_disciplina_id_matriz = '$row_Matriz[matriz_id]'";
$ListarDisciplinas = mysql_query($query_ListarDisciplinas, $SmecelNovo) or die(mysql_error());
$row_ListarDisciplinas = mysql_fetch_assoc($ListarDisciplinas);
$totalRows_ListarDisciplinas = mysql_num_rows($ListarDisciplinas);

if ((isset($_GET['cod'])) && ($_GET['cod'] != "")) {


    $matriz = $row_Matriz['matriz_id'];

    $deleteSQL = sprintf(
        "DELETE FROM smc_matriz_disciplinas WHERE matriz_disciplina_id_matriz = '$matriz' AND matriz_disciplina_id=%s",

        GetSQLValueString($_GET['cod'], "int")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($deleteSQL, $SmecelNovo) or die(mysql_error());

    $deleteGoTo = "matrizdisciplina.php?hash=$colname_Matriz&deletado";
    if (isset($_SERVER['QUERY_STRING'])) {
        //$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
        //$deleteGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $deleteGoTo));
}

$educacaoInfantil = " WHERE disciplina_bncc = '' ";

if ($row_Matriz['matriz_id_etapa'] == "1") {
    $educacaoInfantil = " WHERE disciplina_bncc = 'S' ";
} else {
    $educacaoInfantil = " WHERE disciplina_bncc IS NULL ";
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Disciplinas = "
SELECT disciplina_id, disciplina_ordem, disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_area_conhecimento_id, area_conhecimento_id, area_conhecimento_nome 
FROM smc_disciplina 
INNER JOIN smc_area_conhecimento ON area_conhecimento_id = disciplina_area_conhecimento_id
$educacaoInfantil
ORDER BY disciplina_nome ASC";
$Disciplinas = mysql_query($query_Disciplinas, $SmecelNovo) or die(mysql_error());
$row_Disciplinas = mysql_fetch_assoc($Disciplinas);
$totalRows_Disciplinas = mysql_num_rows($Disciplinas);

$query_Eixos = "
SELECT *
FROM smc_disciplina_eixos 
ORDER BY disciplina_eixo_nome ASC";
$Eixos = mysql_query($query_Eixos, $SmecelNovo) or die(mysql_error());
$row_Eixos = mysql_fetch_assoc($Eixos);
$totalRows_Eixos = mysql_num_rows($Eixos);
?>

<!DOCTYPE html>
<html class="ls-theme-green">

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

    <style>
        /* Corrigir o tamanho do Select2 dentro do .ls-custom-select */
        .ls-custom-select .select2-container {
            width: 100% !important;
        }

        /* Ajusta a altura para alinhar corretamente */
        .select2-container .select2-selection--single {
            height: 40px !important;
            line-height: 40px !important;
        }
    </style>
</head>

<body>
    <?php include_once("menu_top.php"); ?>
    <?php include_once "menu.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">CADASTRAR CAMPO DE EXPERIÊNCIA PERSONALIZADO</h1>

            <!-- Indicador de progresso -->
            <div class="ls-box">
                <div class="ls-progressbar">
                    <div class="ls-progressbar-bar" style="width: <?php echo ($step == 1) ? '50%' : '100%'; ?>"></div>
                </div>
                <p class="ls-txt-center Ls-text-xl" style="font-weight: bold;">
                    <?php if ($step == 1): ?>
                        Passo 1 de 2: Cadastrar Campo de Experiência
                    <?php else: ?>
                        Passo 2 de 2: Cadastrar Objetivos
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($step == 1): ?>
                <!-- Formulário do Campo de Experiência -->
                <div class="ls-box ls-board-box">
                    <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Nome do Campo de Experiência*</b>
                            <input type="text" name="campos_exp_nome" placeholder="" required>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Descrição*</b>
                            <textarea name="campos_exp_mais" rows="4" required
                                onfocus="if(this.value == 'Digite aqui a descrição do campo de experiência...') this.value = '';"
                                onblur="if(this.value == '') this.value = 'Digite aqui a descrição do campo de experiência...';">Digite aqui a descrição do campo de experiência...</textarea>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Orientações</b>
                            <textarea name="campos_exp_orientacoes" rows="4"></textarea>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Direitos de Aprendizagem</b>
                            <textarea name="campos_exp_direitos" rows="4"></textarea>
                        </label>

                        <input type="hidden" name="campo_exp_sec_id" value="<?php echo $row_UsuarioLogado['usu_sec']; ?>">
                        <input type="hidden" name="MM_insert" value="form1">

                        <div class="ls-actions-btn">
                            <button type="submit" class="ls-btn-primary">Próximo Passo</button>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- Formulário dos Objetivos -->
                <div class="ls-box ls-board-box">
                    <?php
                    // Buscar informações do campo de experiência
                    $query_Campo = "SELECT * FROM smc_campos_exp WHERE campos_exp_id = '$campo_exp_id'";
                    $Campo = mysql_query($query_Campo, $SmecelNovo) or die(mysql_error());
                    $row_Campo = mysql_fetch_assoc($Campo);
                    ?>

                    <h3 class="ls-title-3">Campo de Experiência: <?php echo $row_Campo['campos_exp_nome']; ?></h3>

                    <form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="ls-form">

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Faixa Etária*</b>
                            <div class="ls-custom-select">
                                <select name="campos_exp_obj_faixa_et_cod" required>
                                    <option value="">--ESCOLHA--</option>
                                    <option value="1">Bebês (0 a 1 ano e 6 meses)</option>
                                    <option value="2">Crianças bem pequenas (1 ano e 7 meses a 3 anos e 11 meses)</option>
                                    <option value="3">Crianças pequenas (4 anos a 5 anos e 11 meses)</option>
                                </select>
                            </div>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Descrição*</b>
                            <textarea name="campos_exp_obj_campos_exp" rows="4" required
                                onfocus="if(this.value == 'Ex: (EI03CO11) Adota hábitos saudáveis de uso de artefatos computacionais, seguindo recomendações de órgãos de saúde competentes.') this.value = '';"
                                onblur="if(this.value == '') this.value = 'Ex: (EI03CO11) Adota hábitos saudáveis de uso de artefatos computacionais, seguindo recomendações de órgãos de saúde competentes.';">Ex: (EI03CO11) Adota hábitos saudáveis de uso de artefatos computacionais, seguindo recomendações de órgãos de saúde competentes.</textarea>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Abordagem</b>
                            <textarea name="campos_exp_obj_abordagem" rows="4"></textarea>
                        </label>

                        <label class="ls-label col-md-12">
                            <b class="ls-label-text">Sugestões</b>
                            <textarea name="campos_exp_obj_sugestoes" rows="4"></textarea>
                        </label>

                        <input type="hidden" name="campos_exp_obj_id_campos_exp" value="<?php echo $campo_exp_id; ?>">
                        <input type="hidden" name="MM_insert" value="form2">

                        <div class="ls-actions-btn">
                            <button type="submit" class="ls-btn-primary">Adicionar Objetivo</button>
                            <a href="matrizdisciplina.php?hash=<?php echo $row_Matriz['matriz_hash']; ?>"
                                class="ls-btn">Finalizar</a>
                        </div>
                    </form>

                    <!-- Lista de objetivos já cadastrados -->
                    <div class="ls-box ls-board-box">
                        <h4 class="ls-title-4">Objetivos Cadastrados</h4>
                        <table class="ls-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Faixa Etária</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query_Objetivos = "SELECT *, 
                                CASE campos_exp_obj_faixa_et_cod
                                WHEN 1 THEN 'Bebês (0 a 1 ano e 6 meses)'
                                WHEN 2 THEN 'Crianças bem pequenas (1 ano e 7 meses a 3 anos e 11 meses)'
                                WHEN 3 THEN 'Crianças pequenas (4 anos a 5 anos e 11 meses)'
                                END AS campos_exp_obj_faixa_et_nome
                                FROM smc_campos_exp_objetivos 
                                INNER JOIN smc_campos_exp ON campos_exp_id = campos_exp_obj_id_campos_exp
                                WHERE campos_exp_obj_id_campos_exp = '$campo_exp_id' ORDER BY campos_exp_obj_nome";
                                $Objetivos = mysql_query($query_Objetivos, $SmecelNovo) or die(mysql_error());
                                while ($row_Objetivos = mysql_fetch_assoc($Objetivos)) { ?>
                                    <tr>
                                        <td><?php echo $row_Objetivos['campos_exp_nome']; ?></td>
                                        <td><?php echo $row_Objetivos['campos_exp_obj_faixa_et_nome']; ?></td>
                                        <td><?php echo $row_Objetivos['campos_exp_obj_campos_exp']; ?></td>
                                        <td>
                                            <a href="?hash=<?php echo $colname_Matriz; ?>&delete_obj=<?php echo $row_Objetivos['campos_exp_obj_id']; ?>&campo_exp_id=<?php echo $campo_exp_id; ?>"
                                                class="ls-btn-danger ls-ico-remove"
                                                onclick="return confirm('Tem certeza que deseja excluir este objetivo?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include_once "notificacoes.php"; ?>


    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Disciplinas);

mysql_free_result($Matriz);

mysql_free_result($ListarDisciplinas);
?>