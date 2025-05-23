<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_escolas = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, escola_telefone2, 
escola_email, escola_inep, escola_cnpj, escola_logo, escola_ue, escola_situacao, escola_localizacao, escola_ibge_municipio, escola_tema, 
escola_unidade_executora, escola_caixa_ux_prestacao_contas, escola_libera_boletim,
CASE escola_localizacao
WHEN 'U' THEN 'URBANA' 
WHEN 'R' THEN 'RURAL'
END AS escola_localizacao
FROM smc_escola
WHERE escola_id_sec = ".SEC_ID." AND escola_situacao = '1' AND escola_ue = '1'";
$escolas = mysql_query($query_escolas, $SmecelNovo) or die(mysql_error());
$row_escolas = mysql_fetch_assoc($escolas);
$totalRows_escolas = mysql_num_rows($escolas);
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
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
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?> | ESCOLAS</h1>
    <table class="ls-table">
      <thead>
      <tr>
        <th class="ls-txt-center" width="50"></th>
        <th class="ls-txt-center">UNIDADE ESCOLAR</th>
        <th class="ls-txt-center" width="180">TELEFONES</th>
        <th class="ls-txt-center" width="300">E-MAIL</th>
        <th class="ls-txt-center" width="130">LOCALIZAÇÃO</th>
      </tr>
      </thead>
      <tbody>
      <?php $num=1; do { ?>
        <tr>
          <td><?php echo $num; $num++; ?></td>
          <td><?php echo $row_escolas['escola_nome']; ?><br><small><?php echo $row_escolas['escola_endereco']; ?>, <?php echo $row_escolas['escola_num']; ?>, <?php echo $row_escolas['escola_bairro']; ?>, <?php echo $row_escolas['escola_cep']; ?></small></td>
          <td class="ls-txt-center"><?php echo $row_escolas['escola_telefone1']; ?><br><?php echo $row_escolas['escola_telefone2']; ?></td>
          <td class="ls-txt-center"><?php echo $row_escolas['escola_email']; ?></td>
          <td class="ls-txt-center"><?php echo $row_escolas['escola_localizacao']; ?></td>
        </tr>
        <?php } while ($row_escolas = mysql_fetch_assoc($escolas)); ?>
        </tbody>
    </table>
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="css/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<script type="application/javascript">

</script>
</body>
</html>
<?php
mysql_free_result($escolas);
?>
