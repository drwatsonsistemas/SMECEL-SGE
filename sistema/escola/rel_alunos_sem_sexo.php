<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/idade.php'); ?>

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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Turmas = "SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$Turmas = mysql_query($query_Turmas, $SmecelNovo) or die(mysql_error());
$row_Turmas = mysql_fetch_assoc($Turmas);
$totalRows_Turmas = mysql_num_rows($Turmas);

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
</head>

<body>
    <?php include_once("menu-top.php"); ?>
    <?php include_once("menu-esc.php"); ?>


    <main class="ls-main ">
        <div class="container-fluid">

            <h1 class="ls-title-intro ls-ico-home">RELAÇÃO DE ALUNOS SEM INFORMAÇÃO DO CPF NO CADASTRO</h1>
            <!-- CONTEÚDO -->

            <a href="verificacoes_administrativas.php" class="ls-btn ls-btn-sm ls-btn-primary">Voltar</a><br><br>

            <?php
            $m = 0;
            $f = 0;
            ?>

            <?php if ($totalRows_Turmas > 0) { ?>

                <?php
                $numTot = 0;
                do { ?>

                    <h3><?php echo $row_Turmas['turma_nome']; ?></h3>


                    <?php
                    mysql_select_db($database_SmecelNovo, $SmecelNovo);

                    $query_Alunos = "
SELECT 
    vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, 
    vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
    vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
    vinculo_aluno_situacao, vinculo_aluno_datatransferencia, aluno_id, aluno_nome, aluno_nome_social, aluno_nascimento,
    aluno_endereco, aluno_numero, aluno_bairro, aluno_telefone, aluno_celular, aluno_sexo, aluno_cpf,
    CASE aluno_sexo
        WHEN 1 THEN 'M'
        WHEN 2 THEN 'F'
        ELSE 'Indefinido'
    END AS sexo_legivel
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
WHERE 
    vinculo_aluno_id_turma = $row_Turmas[turma_id]
    AND vinculo_aluno_situacao = '1'
    AND (aluno_sexo IS NULL OR aluno_sexo NOT IN (1, 2))
ORDER BY aluno_nome ASC";

                    $Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
                    $row_Alunos = mysql_fetch_assoc($Alunos);
                    $totalRows_Alunos = mysql_num_rows($Alunos);
                    ?>


                    <?php if ($totalRows_Alunos > 0) { ?>

                        <table class="ls-table ls-sm-space">
                            <thead>
                                <tr>
                                    <th width="40"></th>
                                    <th class="ls-txt-center" width="70">MAT</th>
                                    <th class="ls-txt-left">NOME</th>
                                    <th width="60px" class="ls-txt-center">IDADE</th>
                                    <th width="60px" class="ls-txt-center">SEXO</th>
                                    <th class="ls-txt-center">ENDEREÇO</th>
                                    <th class="ls-txt-center">CONTATO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $num = 1;
                                do { ?>



                                    <tr>
                                        <td><?php echo $num;
                                        $num++;
                                        $numTot++; ?> </td>
                                        <td class="ls-txt-center"><a
                                                href="matriculaExibe.php?cmatricula=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>" target="_blank" rel="noopener noreferrer"><?php echo str_pad($row_Alunos['vinculo_aluno_id'], 5, '0', STR_PAD_LEFT); ?></a>
                                        </td>
                                        <td><?php echo $row_Alunos['aluno_nome_social'] != "" ? $row_Alunos["aluno_nome_social"] : $row_Alunos["aluno_nome"]; ?>
                                        </td>
                                        <td width="20px" class="ls-txt-center"> <?php echo idade($row_Alunos['aluno_nascimento']); ?>
                                        </td>
                                        <td width="20px" class="ls-txt-center"> <?php echo $row_Alunos['aluno_sexo']; ?></td>
                                        <td><small><?php echo $row_Alunos['aluno_endereco']; ?>,
                                                <?php echo $row_Alunos['aluno_numero']; ?>,
                                                <?php echo $row_Alunos['aluno_bairro']; ?></small></td>
                                        <td><small><?php echo $row_Alunos['aluno_telefone']; ?>
                                                <?php echo $row_Alunos['aluno_celular']; ?></small></td>
                                    <tr>


                                    <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>

                            </tbody>
                        </table>

                    <?php } else { ?>
                        <hr>
                        <p>Não há dados.</p>
                    <?php } ?>
                    <hr>
                <?php } while ($row_Turmas = mysql_fetch_assoc($Turmas)); ?>

                <p><strong>TOTAL DE ALUNOS: <?php echo $numTot; ?></strong></p>

                <!-- 
                <p class="ls-txt-center">
                    <a href="print_alunos_sem_cpf.php" class="ls-ico-paint-format ls-btn" target="_blank">Imprimir relação
                        de alunos sem CPF</a>
                </p> -->



            <?php } else { ?>

                <div class="ls-box">
                    Nenhum turno noturno cadastrado na escola
                </div>

            <?php } ?>





            <hr>
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
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/locastyle.js"></script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($Turmas);

mysql_free_result($Alunos);
?>