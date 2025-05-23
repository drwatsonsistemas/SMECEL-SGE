<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php

$hoje = date("Y-m-d");
$ontem = date('Y-m-d', strtotime("-1 days", strtotime($hoje)));
$semana = date('Y-m-d', strtotime("-7 days", strtotime($hoje)));
$mes = date('Y-m-d', strtotime("-30 days", strtotime($hoje)));

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltas = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = " . SEC_ID . " AND faltas_alunos_data BETWEEN '$hoje' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltas = mysql_query($query_faltas, $SmecelNovo) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasOntem = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = " . SEC_ID . " AND faltas_alunos_data BETWEEN '$ontem' AND '$ontem'
GROUP BY faltas_alunos_matricula_id
";
$faltasOntem = mysql_query($query_faltasOntem, $SmecelNovo) or die(mysql_error());
$row_faltasOntem = mysql_fetch_assoc($faltasOntem);
$totalRows_faltasOntem = mysql_num_rows($faltasOntem);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasSemana = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = " . SEC_ID . " AND faltas_alunos_data BETWEEN '$semana' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltasSemana = mysql_query($query_faltasSemana, $SmecelNovo) or die(mysql_error());
$row_faltasSemana = mysql_fetch_assoc($faltasSemana);
$totalRows_faltasSemana = mysql_num_rows($faltasSemana);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_faltasMes = "
SELECT faltas_alunos_id, faltas_alunos_matricula_id, faltas_alunos_disciplina_id, faltas_alunos_numero_aula, faltas_alunos_data, faltas_alunos_justificada,
vinculo_aluno_id, vinculo_aluno_id_sec
FROM smc_faltas_alunos
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = faltas_alunos_matricula_id
WHERE vinculo_aluno_id_sec = " . SEC_ID . " AND faltas_alunos_data BETWEEN '$mes' AND '$hoje'
GROUP BY faltas_alunos_matricula_id
";
$faltasMes = mysql_query($query_faltasMes, $SmecelNovo) or die(mysql_error());
$row_faltasMes = mysql_fetch_assoc($faltasMes);
$totalRows_faltasMes = mysql_num_rows($faltasMes);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  if($_POST['usu_senha'] != $_POST['usu_senha_confirmar'] AND $_POST['usu_senha_confirmar'] != ""){
    $updateGoTo = "meus_dados.php?incorreta";
    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
    exit;
  }

  $updateSQL = sprintf("UPDATE smc_usu SET usu_nome=%s, usu_senha=%s, usu_contato=%s, usu_cargo=%s WHERE usu_id=%s",
   GetSQLValueString($_POST['usu_nome'], "text"),
   GetSQLValueString($_POST['usu_senha'], "text"),
   GetSQLValueString($_POST['usu_contato'], "text"),
   GetSQLValueString($_POST['usu_cargo'], "text"),
   GetSQLValueString($_POST['usu_id'], "int"));

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


    $updateGoTo = "meus_dados.php?atualizado";
    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">

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
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
    <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/locastyle.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>

<body>
    <?php include_once "inc/navebar.php"; ?>
    <?php include_once "inc/sidebar.php"; ?>
    <main class="ls-main">
        <div class="container-fluid">
            <h1 class="ls-title-intro ls-ico-home1"><img src="images/logo.png" width="45"> CONSELHO TUTELAR - Ano letivo
                <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>


            <div class="ls-box ls-board-box">


                <?php if (isset($_GET["atualizado"])) { ?>
                    <div class="ls-alert-success ls-dismissable"> <span data-ls-module="dismiss"
                            class="ls-dismiss">&times;</span>
                        <strong>Aviso:</strong> Dados atualizados com sucesso!
                    </div>
                    <hr>
                <?php } ?>

                <?php if (isset($_GET["preencher"])) { ?>
                    <div class="ls-alert-warning ls-dismissable"> <span data-ls-module="dismiss"
                            class="ls-dismiss">&times;</span>
                        <strong>Aviso:</strong> Preencha os dados de contato e cargo para continuar.
                    </div>
                    <hr>
                <?php } ?>

                <div class="row">

                    <div class="col-md-10">
                        <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form">
                            <label class="ls-label col-sm-12 col-md-12"> <b class="ls-label-text">NOME</b>
                                <input type="text" name="usu_nome"
                                    value="<?php echo htmlentities($row_UsuarioLogado['usu_nome'], ENT_COMPAT, 'utf-8'); ?>"
                                    size="32" required>
                            </label>
                            <label class="ls-label col-sm-12 col-md-12 <?php if (empty($row_UsuarioLogado['usu_contato']))
                                echo "ls-warning"; ?>">
                                <b class="ls-label-text">CONTATO</b>
                                <input type="text" name="usu_contato" class="celular9" placeholder="( ) _____-____"
                                    value="<?php echo htmlentities($row_UsuarioLogado['usu_contato'], ENT_COMPAT, 'utf-8'); ?>"
                                    size="32" required>
                                <?php if (empty($row_UsuarioLogado['usu_contato'])) { ?>
                                    <small class="ls-help-message">Preencha esse campo</small>
                                <?php } ?>
                            </label>
                            <label class="ls-label col-sm-12 col-md-12 <?php if (empty($row_UsuarioLogado['usu_cargo']))
                                echo "ls-warning"; ?>">
                                <b class="ls-label-text">FUNÇÃO</b>
                                <div class="ls-custom-select">
                                    <select class="ls-select" name="usu_cargo" required>
                                        <option value="" <?php if (empty($row_UsuarioLogado['usu_cargo'])) {
                                            echo "SELECTED";
                                        } ?>>Escolha...</option>
                                        <option value="1" <?php if (!(strcmp(1, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>DIRETOR</option>
                                        <option value="2" <?php if (!(strcmp(2, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>VICE-DIRETOR</option>
                                        <option value="3" <?php if (!(strcmp(3, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>COORDENADOR PEDAGÓGICO</option>
                                        <option value="4" <?php if (!(strcmp(4, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>SECRETÁRIO ESCOLAR</option>
                                        <option value="5" <?php if (!(strcmp(5, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>AUXILIAR DE SECRETARIA</option>
                                        <option value="6" <?php if (!(strcmp(6, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>GESTOR DE MATRÍCULAS</option>
                                        <option value="7" <?php if (!(strcmp(7, htmlentities($row_UsuarioLogado['usu_cargo'], ENT_COMPAT, 'utf-8')))) {
                                            echo "SELECTED";
                                        } ?>>OUTROS</option>
                                    </select>
                                </div>
                                <?php if (empty($row_UsuarioLogado['usu_cargo'])) { ?>
                                    <small class="ls-help-message">Preencha esse campo</small>
                                <?php } ?>
                            </label>

                            <label class="ls-label col-sm-12 col-md-12">
                                <b class="ls-label-text">SENHA</b>
                                <div class="ls-prefix-group">
                                    <input type="password" name="usu_senha" id="password_field"
                                        value="<?php echo $row_UsuarioLogado['usu_senha']; ?>" size="32" required>
                                    <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye"
                                        data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#password_field"
                                        href="#"> </a>
                                </div>
                            </label>
                            <label class="ls-label col-sm-12 col-md-12">
                                <b class="ls-label-text">CONFIRMAR SENHA</b>
                                <div class="ls-prefix-group">
                                    <input type="password" name="usu_senha_confirmar" id="password_field1" size="32">
                                    <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye"
                                        data-toggle-class="ls-ico-eye, ls-ico-eye-blocked"
                                        data-target="#password_field1" href="#"> </a>
                                </div>
                            </label>
                            <div class="ls-actions-btn">
                                <input type="submit" value="ATUALIZAR DADOS" class="ls-btn-primary ls-btn">
                            </div>
                            <input type="hidden" name="MM_update" value="form1">
                            <input type="hidden" name="usu_id" value="<?php echo $row_UsuarioLogado['usu_id']; ?>">
                            <input type="hidden" name="escola_id" value="<?php echo $row_EscolaLogada['escola_id']; ?>">
                        </form>
                    </div>

                    <p>&nbsp;</p>
                </div>
            </div>

        </div>
        <?php //include_once "inc/footer.php"; ?>
    </main>
    <?php include_once "inc/notificacoes.php"; ?>
    <!-- We recommended use jQuery 1.10 or up -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="css/locastyle.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/sweetalert2.min.js"></script>
    <?php if (isset($_GET["ops"])) { ?>
        <script type="application/javascript">
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Algo deu errado!'
            })
        </script>
    <?php } ?>
</body>

</html>
<?php
mysql_free_result($faltas);
mysql_free_result($faltasOntem);
mysql_free_result($faltasSemana);
mysql_free_result($faltasMes);
?>