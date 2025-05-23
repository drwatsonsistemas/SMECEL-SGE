<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

<?php
// Initialize the session
if (!isset($_SESSION)) {
    session_start();
}
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
    $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
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

$MM_authorizedUsers = "99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
    $isValid = False;
    if (!empty($UserName)) {
        $arrUsers = explode(",", $strUsers);
        $arrGroups = explode(",", $strGroups);
        if (in_array($UserName, $arrUsers)) {
            $isValid = true;
        }
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
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}

if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }
        $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
        switch ($theType) {
            case "text": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;
            case "long":
            case "int": $theValue = ($theValue != "") ? intval($theValue) : "NULL"; break;
            case "double": $theValue = ($theValue != "") ? doubleval($theValue) : "NULL"; break;
            case "date": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;
            case "defined": $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; break;
        }
        return $theValue;
    }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Atualizar situação do chamado
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    $updateSQL = sprintf(
        "UPDATE smc_chamados SET chamado_situacao=%s WHERE chamado_id=%s",
        GetSQLValueString($_POST['chamado_situacao'], "text"),
        GetSQLValueString($_POST['chamado_id'], "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

    if ($_POST['chamado_situacao'] == "F") {
        $updateGoTo = "chamados.php?encerrado";
    } else {
        $updateGoTo = "";
    }

    if (isset($_SERVER['QUERY_STRING'])) {
        $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
        $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
}

// Inserir nova resposta (ticket)
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $textoComplementar = "<small><strong>[Suporte SMECEL]</strong></small>";
    $insertSQL = sprintf(
        "INSERT INTO smc_ticket (ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_visualizado) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['ticket_id_chamado'], "int"),
        GetSQLValueString($_POST['ticket_id_usuario'], "int"),
        GetSQLValueString($_POST['ticket_data'], "date"),
        GetSQLValueString($_POST['ticket_texto'] . $textoComplementar, "text"),
        GetSQLValueString($_POST['ticket_visualizado'], "text")
    );

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($insertSQL, $SmecelNovo) or die(mysql_error());

    // Envio de e-mail
    $chamado = $_POST['chamado_numero'];
    $titulo = $_POST['chamado_titulo'];
    $email = $_POST['usu_email'];
    $nome = $_POST['usu_nome'];
    $texto = $_POST['ticket_texto'];

    require_once("../../classes/class.phpmailer.php");
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = 'smtp.smecel.com.br';
    $mail->SMTPAuth = true;
    $mail->Port = 587;
    $mail->Username = 'suporte@smecel.com.br';
    $mail->Password = 'Drw4tson@smecel';
    $mail->IsHTML(true);
    $mail->CharSet = "UTF-8";
    $mail->SetFrom('suporte@smecel.com.br', 'SMECEL via helpdesk');
    $mail->Subject = "[Chamado #$chamado] $titulo";
    $mail->AddAddress($email);
    $mail->AddBCC('rafael_fua@hotmail.com');
    $mail->MsgHTML("
        <p>Prezado(a) $nome,</p>
        <p>Há uma nova interação do suporte ao chamado #$chamado \"$titulo\".</p>
        <p><div style=\"padding-left: 20px; border-left: solid 2px red\"><strong>$texto</strong></div></p>
        <p>Para enviar uma resposta, faça login no painel de sua escola.</p>
        <p>Atenciosamente,<br><br>
        Equipe de suporte.<br>www.smecel.com.br</p>
    ");

    try {
        $mail->Send();
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
    } catch (phpmailerException $e) {
        echo $e->errorMessage();
    }
}

$colname_UsuarioLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
    $colname_UsuarioLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_UsuarioLogado = sprintf(
    "SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s",
    GetSQLValueString($colname_UsuarioLogado, "text")
);
$UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
$row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
$totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

$colname_Chamado = "-1";
if (isset($_GET['chamado'])) {
    $colname_Chamado = $_GET['chamado'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Chamado = sprintf(
    "SELECT chamado_id, chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, 
    chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado, chamado_numero, sec_id, sec_prefeitura, 
    escola_id, escola_nome, usu_id, usu_nome, usu_email, 
    CASE chamado_situacao
        WHEN 'A' THEN '<span class=\"ls-tag-info\">ABERTO</span>'
        WHEN 'F' THEN '<span class=\"ls-tag-warning\">ENCERRADO</span>'
    END AS chamado_situacao_nome 
    FROM smc_chamados
    INNER JOIN smc_sec ON sec_id = chamado_id_sec
    LEFT JOIN smc_escola ON escola_id = chamado_id_escola
    INNER JOIN smc_usu ON usu_id = chamado_id_usuario 
    WHERE chamado_numero = %s",
    GetSQLValueString($colname_Chamado, "text")
);
$Chamado = mysql_query($query_Chamado, $SmecelNovo) or die(mysql_error());
$row_Chamado = mysql_fetch_assoc($Chamado);
$totalRows_Chamado = mysql_num_rows($Chamado);

$updateSQL = "UPDATE smc_chamados SET chamado_visualizado = 'S' WHERE chamado_id = '$row_Chamado[chamado_id]'";
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ticket = "
    SELECT ticket_id, ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_imagem, ticket_visualizado, usu_id, usu_nome 
    FROM smc_ticket 
    INNER JOIN smc_usu ON usu_id = ticket_id_usuario
    WHERE ticket_id_chamado = '$row_Chamado[chamado_id]'";
$Ticket = mysql_query($query_Ticket, $SmecelNovo) or die(mysql_error());
$row_Ticket = mysql_fetch_assoc($Ticket);
$totalRows_Ticket = mysql_num_rows($Ticket);
?>

<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
<head>
    <title>SMECEL - Sistema de Gestão Escolar</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <style>
        .anexo-preview img {
            max-width: 200px;
            margin: 10px;
            border: 1px solid #ccc;
            padding: 5px;
        }
        .anexo-link {
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <?php include_once("menu_top.php"); ?>
    <?php include_once "menu.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home">CHAMADO</h1>

            <div class="ls-box ls-board-box">
                <header class="ls-info-header">
                    <h2 class="ls-title-3">CHAMADO #<?php echo $row_Chamado['chamado_numero']; ?></h2>
                    <p class="ls-float-right ls-float-none-xs ls-small-info"><?php echo $row_Chamado['chamado_data_abertura']; ?></p>
                </header>
                <p><?php echo $row_Chamado['escola_nome']; ?> (<?php echo $row_Chamado['sec_prefeitura']; ?>)</p>
                <p><?php echo $row_Chamado['usu_nome']; ?> (<?php echo $row_Chamado['usu_email']; ?>)</p>
                <p><?php echo $row_Chamado['chamado_id_telefone']; ?></p>
                <p><span class="ls-tag"><?php echo $row_Chamado['chamado_categoria']; ?></span></p>
                <hr>
                <p>Assunto:<br><strong><?php echo $row_Chamado['chamado_titulo']; ?></strong></p>
                <p><?php echo nl2br($row_Chamado['chamado_texto']); ?></p>

                <!-- Exibição dos anexos -->
                <?php
                $anexos = json_decode($row_Chamado['chamado_imagem'], true);
                if (!empty($anexos)) {
                    echo '<h3 class="ls-title-4">Anexos</h3>';
                    echo '<div class="anexo-preview">';
                    foreach ($anexos as $anexo) {
                        $file_path = "../../chamados_anexo/" . $anexo;
                        $file_ext = strtolower(pathinfo($anexo, PATHINFO_EXTENSION));
                        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<a href='$file_path' target='_blank'><img src='$file_path' alt='Anexo'></a>";
                        } else {
                            echo "<a href='$file_path' class='ls-btn anexo-link' target='_blank'>Baixar $anexo</a>";
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>

            <?php if ($totalRows_Ticket > 0) { ?>
                <?php 
                $resposta = 1;
                do { ?>
                    <div class="ls-box ls-box-gray">
                        <p><strong>#<?php echo $resposta++; ?> <?php echo $row_Ticket['usu_nome']; ?> respondeu em <?php echo date("d/m/Y", strtotime($row_Ticket['ticket_data'])); ?>:</strong></p>
                        <p><?php echo nl2br($row_Ticket['ticket_texto']); ?></p>
                    </div>
                    <?php
                    $updateSQL = "UPDATE smc_ticket SET ticket_visualizado = 'S' WHERE ticket_id_chamado = '$row_Chamado[chamado_id]'";
                    mysql_select_db($database_SmecelNovo, $SmecelNovo);
                    $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());
                    ?>
                <?php } while ($row_Ticket = mysql_fetch_assoc($Ticket)); ?>
            <?php } ?>

            <br>

            <?php if ($row_Chamado['chamado_situacao'] == "F") { ?>
                <div class="ls-alert-info"><strong>Atenção:</strong> 
                Este chamado já foi finalizado. Caso necessite fazer uma nova interação, clique no botão abaixo para reativar o chamado e escrever uma nova mensagem.
                </div>
            <?php } ?>

            <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="ls-form">
                <?php if ($row_Chamado['chamado_situacao'] == "A") { ?>
                    <span data-ls-module="modal" data-target="#myAwesomeModal" class="ls-btn-primary">NOVA RESPOSTA</span>
                <?php } ?>
                <a href="chamados.php" class="ls-btn">VOLTAR</a>
                <input type="submit" value="<?php echo $row_Chamado['chamado_situacao'] == "A" ? "ENCERRAR ESTE CHAMADO" : "REABRIR ESTE CHAMADO"; ?>" class="ls-btn-dark ls-float-right" />
                <input type="hidden" name="chamado_situacao" value="<?php echo $row_Chamado['chamado_situacao'] == "A" ? "F" : "A"; ?>" />
                <input type="hidden" name="MM_update" value="form1" />
                <input type="hidden" name="chamado_id" value="<?php echo $row_Chamado['chamado_id']; ?>" />
            </form>

            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <div class="ls-modal" id="myAwesomeModal">
                <div class="ls-modal-box ls-modal-large">
                    <div class="ls-modal-header">
                        <button data-dismiss="modal">×</button>
                        <h4 class="ls-modal-title">MENSAGEM COMPLEMENTAR AO CHAMADO #<?php echo $row_Chamado['chamado_numero']; ?></h4>
                    </div>
                    <div class="ls-modal-body" id="myModalBody">
                        <div class="ls-box"><small><?php echo $row_Chamado['chamado_texto']; ?></small></div>
                        <p>
                        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
                            <label class="ls-label col-md-12">
                                <b class="ls-label-text">TEXTO</b>
                                <p class="ls-label-info">Informe detalhes que complementem o chamado</p>
                                <textarea name="ticket_texto" id="mytextarea" cols="50" rows="5"></textarea>
                            </label>
                            <input type="hidden" name="chamado_numero" value="<?php echo $row_Chamado['chamado_numero']; ?>">
                            <input type="hidden" name="chamado_titulo" value="<?php echo $row_Chamado['chamado_titulo']; ?>">
                            <input type="hidden" name="usu_nome" value="<?php echo $row_Chamado['usu_nome']; ?>">
                            <input type="hidden" name="usu_email" value="<?php echo $row_Chamado['usu_email']; ?>">
                            <input type="hidden" name="ticket_id_chamado" value="<?php echo $row_Chamado['chamado_id']; ?>">
                            <input type="hidden" name="ticket_id_usuario" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
                            <input type="hidden" name="ticket_data" value="<?php echo date('Y-m-d'); ?>">
                            <input type="hidden" name="ticket_visualizado" value="S">
                            <input type="hidden" name="MM_insert" value="form1">
                            <label class="ls-label col-md-12">
                                <input type="submit" class="ls-btn-primary" value="SALVAR">
                                <a href="#" class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a>
                            </label>
                        </form>
                        </p>
                    </div>
                    <div class="ls-modal-footer"></div>
                </div>
            </div>
        </div>
    </main>
    <?php include_once "notificacoes.php"; ?>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js"></script>
    <script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#mytextarea',
            height: 300,
            toolbar: 'bold italic | bullist numlist | alignleft aligncenter alignright alignjustify | link h2 h3 blockquote',
            plugins: 'advlist autolink link autolink image imagetools lists charmap print preview paste',
            statusbar: false,
            menubar: false,
            paste_as_text: true,
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
</body>
</html>
<?php
mysql_free_result($UsuarioLogado);
mysql_free_result($Chamado);
mysql_free_result($Ticket);
?>