<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include('fnc/idade.php'); ?>
<?php include('fnc/idadeSerie.php'); ?>


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
$query_listaTurmas = "
SELECT 
turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo, etapa_id, etapa_idade 
FROM smc_turma
INNER JOIN smc_etapa ON etapa_id = turma_etapa 
WHERE turma_id_escola = '$row_UsuLogado[usu_escola]' AND turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]'";
$listaTurmas = mysql_query($query_listaTurmas, $SmecelNovo) or die(mysql_error());
$row_listaTurmas = mysql_fetch_assoc($listaTurmas);
$totalRows_listaTurmas = mysql_num_rows($listaTurmas);


?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
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
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
  <?php include_once ("menu-top.php"); ?>
  <?php include_once ("menu-esc.php"); ?>


  <main class="ls-main ">
    <div class="container-fluid">


      <h1 class="ls-title-intro ls-ico-home">Distorção Idade/Série</h1>
      <!-- CONTEÚDO -->

      <p><a class="ls-btn-primary ls-ico-paint-format" href="print_aluno_idade_serie.php" target="_blank"> Imprimir</a></p>

      <?php do { ?>





        <h1><?php echo $row_listaTurmas['turma_nome']; ?></h1>



        <?php

        mysql_select_db($database_SmecelNovo, $SmecelNovo);
        $query_listaAlunos = "SELECT 
        vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
        vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
        vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, aluno_id, aluno_nome, aluno_nome_social,aluno_nascimento 
        FROM smc_vinculo_aluno
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
        WHERE vinculo_aluno_id_turma = '$row_listaTurmas[turma_id]'";
        $listaAlunos = mysql_query($query_listaAlunos, $SmecelNovo) or die(mysql_error());
        $row_listaAlunos = mysql_fetch_assoc($listaAlunos);
        $totalRows_listaAlunos = mysql_num_rows($listaAlunos);

        ?>

        <?php if ($totalRows_listaAlunos>0) { ?>

          <table class="ls-table">

           <thead>
            <tr>
             <th>ALUNO</th>
             <th class="ls-txt-center">NASCIMENTO</th>
             <th class="ls-txt-center">IDADE</th>
             <th class="ls-txt-center">RESULTADO</th>
           </tr>
         </thead>
         <tbody>
           <?php do { ?>
             <tr>
              <td><?php echo $row_listaAlunos['aluno_nome_social']!= "" ? $row_listaAlunos["aluno_nome_social"] : $row_listaAlunos["aluno_nome"]; ?></td>
              <td class="ls-txt-center"><?php echo inverteData($row_listaAlunos['aluno_nascimento']); ?></td>
              <td class="ls-txt-center"><?php echo idade($row_listaAlunos['aluno_nascimento']); ?></td>
              <td class="ls-txt-center">
                <?php if (idade($row_listaAlunos['aluno_nascimento']) <> "-") {

                  if($row_listaTurmas['etapa_id'] != '2'){

                    if (idade($row_listaAlunos['aluno_nascimento']) > ($row_listaTurmas['etapa_idade']+1)) {
                     $diferenca = idade($row_listaAlunos['aluno_nascimento']) - $row_listaTurmas['etapa_idade'];
                     echo "<span class='ls-ico-chevron-up ls-ico-left ls-color-danger'> Acima ".$diferenca." ano(s)</span>";
                   } else if (idade($row_listaAlunos['aluno_nascimento']) < $row_listaTurmas['etapa_idade']) {
                     $diferenca = $row_listaTurmas['etapa_idade'] - idade($row_listaAlunos['aluno_nascimento']);
                     echo "<span class='ls-ico-chevron-down ls-ico-left ls-color-danger'> Abaixo ".$diferenca." ano(s)</span>";
                   } else {
                     echo "<span class='ls-ico-checkmark ls-color-success'> </span>";
                   }
                 }else{
                  if(idade($row_listaAlunos['aluno_nascimento']) == 4 OR idade($row_listaAlunos['aluno_nascimento']) == 5){
                    echo "<span class='ls-ico-checkmark ls-color-success'> </span>";
                  }
                 }
               } else {
                 echo "-";
               }
               ?>
             </td>
           </tr>
         <?php } while ($row_listaAlunos = mysql_fetch_assoc($listaAlunos)); ?>
       </tbody>
     </table>

   <?php } else { ?>
     <hr>
     <div class="ls-alert-warning">
      Nenhuma informação encontrada.
    </div>
  <?php } ?>


<?php } while ($row_listaTurmas = mysql_fetch_assoc($listaTurmas)); ?>


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

mysql_free_result($listaTurmas);

mysql_free_result($listaAlunos);
?>
