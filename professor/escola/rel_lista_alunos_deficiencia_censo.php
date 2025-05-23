<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
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
$query_ListaAlunos = "
SELECT *, 
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE 
(
aluno_def_bvisao = '1' OR 
aluno_def_cegueira = '1' OR 
aluno_def_auditiva = '1' OR 
aluno_def_fisica = '1' OR 
aluno_def_intelectual = '1' OR 
aluno_def_surdez = '1' OR 
aluno_def_surdocegueira = '1' OR 
aluno_def_autista = '1' OR 
aluno_def_superdotacao = '1'
) 
AND vinculo_aluno_ano_letivo = $row_AnoLetivo[ano_letivo_ano] AND vinculo_aluno_id_escola = '$row_UsuLogado[usu_escola]' 
ORDER BY turma_turno, turma_nome, aluno_nome_social,aluno_nome
";
$ListaAlunos = mysql_query($query_ListaAlunos, $SmecelNovo) or die(mysql_error());
$row_ListaAlunos = mysql_fetch_assoc($ListaAlunos);
$totalRows_ListaAlunos = mysql_num_rows($ListaAlunos);
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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" type="text/css" href="css/preloader.css">
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
 
        <h1 class="ls-title-intro ls-ico-home">Relação de alunos com deficiência / CENSO</h1>
		<!-- CONTEÚDO -->
        
		<?php if ($totalRows_ListaAlunos > 0) { ?>
		<table width="100%" class="ls-table">
          <thead>
		  <tr>
            <th class="ls-txt-center" width="55px"></th>
            <th class="ls-txt-center">ALUNO</th>
            <th class="ls-txt-center">IDADE</th>
            <th class="ls-txt-center">TURMA</th>
            <th class="ls-txt-center">DEFICIENCIA</th>
            <th class="ls-txt-center">LAUDO</th>
          </tr>
		  </thead>
		  <tbody>
		  <?php $num = 1; ?>
          <?php do { ?>
            <tr>
              <td class="ls-txt-center"><?php echo $num; $num++; ?></td>
              <td><?php echo $row_ListaAlunos['aluno_nome_social'] != ""?$row_ListaAlunos["aluno_nome_social"] : $row_ListaAlunos["aluno_nome"]; ?></td>
              <td class="ls-txt-center"><?php echo idade($row_ListaAlunos['aluno_nascimento']); ?></td>
              <td class="ls-txt-center"><?php echo $row_ListaAlunos['turma_nome']; ?> - <?php echo $row_ListaAlunos['turma_turno_nome']; ?></td>
              <td class="ls-txt-center">
              <?php if ($row_ListaAlunos['aluno_def_bvisao']=="1") { echo "<span class='ls-tag'>BAIXA VISÃO</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_cegueira']=="1") { echo "<span class='ls-tag'>CEGUEIRA</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_auditiva']=="1") { echo "<span class='ls-tag'>AUDITIVA</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_fisica']=="1") { echo "<span class='ls-tag'>FÍSICA</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_intelectual']=="1") { echo "<span class='ls-tag'>INTELECTUAL</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_surdez']=="1") { echo "<span class='ls-tag'>SURDEZ</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_surdocegueira']=="1") { echo "<span class='ls-tag'>SURDOCEGUEIRA</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_autista']=="1") { echo "<span class='ls-tag'>AUTISMO</span>"; }; ?>
              <?php if ($row_ListaAlunos['aluno_def_superdotacao']=="1") { echo "<span class='ls-tag'>SUPERDOTAÇÃO</span>"; }; ?>
              </td>
              <td class="ls-txt-center"><?php if ($row_ListaAlunos['aluno_laudo']=="1") { echo "SIM"; }; ?></td>
            </tr>
            <?php } while ($row_ListaAlunos = mysql_fetch_assoc($ListaAlunos)); ?>
			</tbody>
        </table>
		
				
		
		<div class="ls-txt-center">
			<a class="ls-btn ls-ico-windows" href="print_lista_alunos_deficiencia.php" target="_blank"> Imprimir</a>
		</div>
		
		<?php } else { ?>
				<hr>
		<div class="ls-alert-warning">
                  Nenhuma informação encontrada.
        </div>
		<?php } ?>

		<br>
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

mysql_free_result($ListaAlunos);
?>
