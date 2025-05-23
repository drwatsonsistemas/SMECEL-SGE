<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/anti_injection.php'); ?>
<?php include('../funcoes/inverteData.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Secretaria = "SELECT sec_id, sec_nome, sec_prefeitura, sec_cep, sec_uf, sec_cidade, sec_endereco, sec_num, sec_bairro, sec_telefone1, sec_telefone2, sec_email, sec_nome_secretario, sec_bloqueada, sec_aviso_bloqueio, sec_logo FROM smc_sec WHERE sec_id = $row_UsuarioLogado[usu_sec]";
$Secretaria = mysql_query($query_Secretaria, $SmecelNovo) or die(mysql_error());
$row_Secretaria = mysql_fetch_assoc($Secretaria);
$totalRows_Secretaria = mysql_num_rows($Secretaria);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Escola = "SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, escola_unidade_executora, escola_caixa_ux_prestacao_contas FROM smc_escola WHERE escola_id_sec = '$row_UsuarioLogado[usu_sec]' AND escola_situacao = '1' AND escola_ue = '1'";
$Escola = mysql_query($query_Escola, $SmecelNovo) or die(mysql_error());
$row_Escola = mysql_fetch_assoc($Escola);
$totalRows_Escola = mysql_num_rows($Escola);


$colname_Escola = "-1";
if (isset($_GET['escola'])) {
    $colname_Escola = (int) anti_injection($_GET['escola']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ac = "
SELECT 
    pc.id_planejamento, pc.id_coordenador, pc.data_inicio, pc.data_fim, pc.temas_integradores, pc.atividade_promovida, 
    pc.atividades_diarias, pc.intervencao_pedagogica, pc.intervencao_pedagogica_obs, pc.acoes_cnca_sabe, pc.acompanhamento_projetos, 
    pc.monitoramento_avaliacao, pc.monitoramento_avaliacao_obs, pc.formacao_continuada, pc.competencias_socioemocionais, pc.atendimento_familias, 
    pc.avaliacao_processual, pc.data_criacao, usu_id, usu_nome, escola_id, escola_id_sec, escola_nome 
FROM smc_planejamento_coordenador pc
INNER JOIN smc_usu ON usu_id = pc.id_coordenador
LEFT JOIN smc_escola ON escola_id = pc.id_escola
WHERE pc.id_escola = '$colname_Escola' AND escola_id_sec = '$row_UsuarioLogado[usu_sec]'
ORDER BY pc.data_inicio DESC
";
$Ac = mysql_query($query_Ac, $SmecelNovo) or die(mysql_error());
$row_Ac = mysql_fetch_assoc($Ac);
$totalRows_Ac = mysql_num_rows($Ac);

$nomeMenu = "ESCOLHA UMA ESCOLA";
if (isset($_GET['escola']) && $totalRows_Ac > 0) {
    $nomeMenu = $row_Ac['escola_nome'];
}

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
    <script src="js/locastyle.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

</head>

<body>
    <?php include_once("menu_top.php"); ?>
    <?php include_once "menu.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">PLANEJAMENTO</h1>
            <!-- CONTEUDO -->


            <div data-ls-module="dropdown" class="ls-dropdown">
                <a href="#" class="ls-btn-primary"><?php echo $nomeMenu; ?></a>
                <ul class="ls-dropdown-nav">
                    <?php do { ?>
                        <li><a
                                href="planejamento_coordenadores.php?escola=<?php echo $row_Escola['escola_id']; ?>"><?php echo $row_Escola['escola_nome']; ?></a>
                        </li>
                    <?php } while ($row_Escola = mysql_fetch_assoc($Escola)); ?>
                </ul>
            </div>



            <hr>
            <h1 id="status"></h1>
            <?php if ($totalRows_Ac > 0) { // Show if recordset not empty ?>
                <table class="ls-table ls-sm-space">
                    <thead>
                        <tr>
                            <th>Coordenador</th>
                            <th class="ls-txt-center">Período</th>
                            <th class="ls-txt-center">Temas Integradores</th>
                            <th class="ls-txt-center">Atividade Promovida</th>
                            <th class="ls-txt-center">Cadastro</th>
                            <th> </th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do { ?>
                            <tr id="linha-<?php echo $row_Ac['id_planejamento']; ?>">
                                <td><a
                                        href="#"><?php echo $row_Ac['usu_nome']; ?></a>
                                </td>
                                <td class="ls-txt-center">
                                    <?php echo date('d/m/Y', strtotime($row_Ac['data_inicio'])) . ' a ' . date('d/m/Y', strtotime($row_Ac['data_fim'])); ?>
                                </td>
                                <td class="ls-txt-center"><?php echo htmlspecialchars($row_Ac['temas_integradores']); ?></td>
                                <td class="ls-txt-center"><?php echo htmlspecialchars($row_Ac['atividade_promovida']); ?></td>
                                <td class="ls-txt-center"><?php echo date("d/m/Y - H:i", strtotime($row_Ac['data_criacao'])); ?>
                                </td>
                                <td class="ls-txt-center">
                                    <button data-ls-module="modal" data-action="" data-content="
    <div class='ls-box'>
        <h4>Temas Integradores</h4>
        <p><?php echo htmlspecialchars($row_Ac['temas_integradores']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Atividade Promovida</h4>
        <p><?php echo htmlspecialchars($row_Ac['atividade_promovida']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Período</h4>
        <p><?php echo date('d/m/Y', strtotime($row_Ac['data_inicio'])) . ' a ' . date('d/m/Y', strtotime($row_Ac['data_fim'])); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Atividades Diárias Propostas</h4>
        <p><?php echo htmlspecialchars($row_Ac['atividades_diarias']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Intervenção Pedagógica</h4>
        <p><?php echo htmlspecialchars($row_Ac['intervencao_pedagogica']); ?></p>
        <br>
        <h6>Observações</h6>
        <p><?php echo htmlspecialchars($row_Ac['intervencao_pedagogica_obs']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Ações para CNCA/SABE/SAEB/LEEI</h4>
        <p><?php echo htmlspecialchars($row_Ac['acoes_cnca_sabe']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Acompanhamento de Projetos</h4>
        <p><?php echo htmlspecialchars($row_Ac['acompanhamento_projetos']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Monitoramento e Avaliação</h4>
        <p><?php echo htmlspecialchars($row_Ac['monitoramento_avaliacao']); ?></p>
        <br>
        <h6>Observações</h6>
        <p><?php echo htmlspecialchars($row_Ac['monitoramento_avaliacao_obs']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Formação Continuada</h4>
        <p><?php echo htmlspecialchars($row_Ac['formacao_continuada']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Competências Socioemocionais</h4>
        <p><?php echo htmlspecialchars($row_Ac['competencias_socioemocionais']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Atendimento às Famílias</h4>
        <p><?php echo htmlspecialchars($row_Ac['atendimento_familias']); ?></p>
    </div>
    <div class='ls-box'>
        <h4>Avaliação Processual</h4>
        <p><?php echo htmlspecialchars($row_Ac['avaliacao_processual']); ?></p>
    </div>
    <div class='1ls-box'>
        <a href='impressao/rel_planejamento_coordenador.php?id=<?php echo $row_Ac['id_planejamento']; ?>' target='_blank' class='ls-btn-primary'>Imprimir Planejamento</a>
    </div>
" data-title="<?php echo $row_Ac['usu_nome']; ?> - Escola: <?php echo $row_Ac['escola_nome']; ?>"
                                         data-close="Fechar" class="ls-btn-primary">Ver
                                        Planejamento</button>
                                </td>
                            </tr>
                        <?php } while ($row_Ac = mysql_fetch_assoc($Ac)); ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>Nenhum planejamento cadastrado</p>
            <?php } // Show if recordset not empty ?>


            <p>&nbsp;</p>
            <!-- CONTEUDO -->
        </div>
    </main>
    <?php include_once "notificacoes.php"; ?>

    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".deletar").on('click', function () {


                var id = $(this).attr('id');
                var prof = $(this).attr('prof');


                Swal.fire({
                    title: 'Deletar este planejamento?',
                    text: "Esta ação não poderá ser desfeita.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, deletar!'
                }).then((result) => {
                    if (result.isConfirmed) {


                        $.ajax({
                            type: 'POST',
                            url: 'funcoes/deletar_planejamento.php',
                            data: {
                                id: id,
                                prof: prof
                            },
                            success: function (data) {

                                $("#linha-" + id).remove();

                                $('#status').html(data);

                                setTimeout(function () {




                                    //location.reload();          
                                }, 2000);

                            }
                        })

                        return true;







                    }
                })


            });
        });
    </script>
</body>

</html>
<?php
mysql_free_result($UsuarioLogado);

mysql_free_result($Secretaria);

mysql_free_result($Escola);

mysql_free_result($Ac);
?>