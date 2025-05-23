<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

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
SELECT *
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AI = "SELECT * FROM smc_ideb_iniciais_escola WHERE ID_ESCOLA = $row_EscolaLogada[escola_inep]";
$Ibge_AI = mysql_query($query_Ibge_AI, $SmecelNovo) or die(mysql_error());
$row_Ibge_AI = mysql_fetch_assoc($Ibge_AI);
$totalRows_Ibge_AI = mysql_num_rows($Ibge_AI);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Ibge_AF = "SELECT * FROM smc_ideb_finais_escola WHERE ID_ESCOLA = $row_EscolaLogada[escola_inep]";
$Ibge_AF = mysql_query($query_Ibge_AF, $SmecelNovo) or die(mysql_error());
$row_Ibge_AF = mysql_fetch_assoc($Ibge_AF);
$totalRows_Ibge_AF = mysql_num_rows($Ibge_AF);

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
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>



<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['ANO', 'PROJETADO', 'OBSERVADO'],
        ['2005',  <?php echo $row_Ibge_AI['VL_PROJECAO_2005']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2005']; ?>],
        ['2007',  <?php echo $row_Ibge_AI['VL_PROJECAO_2007']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2007']; ?>],
        ['2009',  <?php echo $row_Ibge_AI['VL_PROJECAO_2009']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2009']; ?>],
        ['2011',  <?php echo $row_Ibge_AI['VL_PROJECAO_2011']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2011']; ?>],
        ['2013',  <?php echo $row_Ibge_AI['VL_PROJECAO_2013']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2013']; ?>],
        ['2015',  <?php echo $row_Ibge_AI['VL_PROJECAO_2015']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2015']; ?>],
        ['2017',  <?php echo $row_Ibge_AI['VL_PROJECAO_2017']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2017']; ?>],
        ['2019',  <?php echo $row_Ibge_AI['VL_PROJECAO_2019']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2019']; ?>],
        ['2021',  <?php echo $row_Ibge_AI['VL_PROJECAO_2021']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2021']; ?>],
        ['2023',  <?php echo $row_Ibge_AI['VL_PROJECAO_2023']; ?>, <?php echo $row_Ibge_AI['VL_OBSERVADO_2023']; ?>],
      ]);

      var options = {
        chart: {
          title: 'PROJETADO X OBSERVADO - ANOS INICIAIS',
          subtitle: '',
         
        },
        legend: { position: "bottom" }
      };

      var chart = new google.visualization.ComboChart(document.getElementById('proj_obs_ai'));
      chart.draw(data, options);
    }
  </script>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['ANO', 'PROJETADO', 'OBSERVADO'],
        ['2005',  <?php echo $row_Ibge_AF['VL_PROJECAO_2005']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2005']; ?>],
        ['2007',  <?php echo $row_Ibge_AF['VL_PROJECAO_2007']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2007']; ?>],
        ['2009',  <?php echo $row_Ibge_AF['VL_PROJECAO_2009']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2009']; ?>],
        ['2011',  <?php echo $row_Ibge_AF['VL_PROJECAO_2011']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2011']; ?>],
        ['2013',  <?php echo $row_Ibge_AF['VL_PROJECAO_2013']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2013']; ?>],
        ['2015',  <?php echo $row_Ibge_AF['VL_PROJECAO_2015']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2015']; ?>],
        ['2017',  <?php echo $row_Ibge_AF['VL_PROJECAO_2017']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2017']; ?>],
        ['2019',  <?php echo $row_Ibge_AF['VL_PROJECAO_2019']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2019']; ?>],
        ['2021',  <?php echo $row_Ibge_AF['VL_PROJECAO_2021']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2021']; ?>],
        ['2023',  <?php echo $row_Ibge_AF['VL_PROJECAO_2023']; ?>, <?php echo $row_Ibge_AF['VL_OBSERVADO_2023']; ?>],
      ]);

      var options = {
        chart: {
          title: 'PROJETADO X OBSERVADO - ANOS FINAIS',
          subtitle: '',
         
        },
        legend: { position: "bottom" }
      };
      

      var chart = new google.visualization.ComboChart(document.getElementById('proj_obs_af'));
      chart.draw(data, options);
    }
  </script>


  <script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['ANO', 'OBSERVADO'],
        ['2005', <?php echo $row_Ibge_AI['VL_OBSERVADO_2005']; ?>],
        ['2007', <?php echo $row_Ibge_AI['VL_OBSERVADO_2007']; ?>],
        ['2009', <?php echo $row_Ibge_AI['VL_OBSERVADO_2009']; ?>],
        ['2011', <?php echo $row_Ibge_AI['VL_OBSERVADO_2011']; ?>],
        ['2013', <?php echo $row_Ibge_AI['VL_OBSERVADO_2013']; ?>],
        ['2015', <?php echo $row_Ibge_AI['VL_OBSERVADO_2015']; ?>],
        ['2017', <?php echo $row_Ibge_AI['VL_OBSERVADO_2017']; ?>],
        ['2019', <?php echo $row_Ibge_AI['VL_OBSERVADO_2019']; ?>],
        ['2021', <?php echo $row_Ibge_AI['VL_OBSERVADO_2021']; ?>],
        ['2023', <?php echo $row_Ibge_AI['VL_OBSERVADO_2023']; ?>]

      ]);

      var options = {
        chart: {
          title: 'IDEB MUNICIPAL - ANOS INICIAIS',
          subtitle: '',
         
        },
        legend: { position: "none" }
      };

      var chart = new google.charts.Bar(document.getElementById('div_anos_iniciais'));

      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>


<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['ANO', 'OBSERVADO'],
        ['2005', <?php echo $row_Ibge_AF['VL_OBSERVADO_2005']; ?>],
        ['2007', <?php echo $row_Ibge_AF['VL_OBSERVADO_2007']; ?>],
        ['2009', <?php echo $row_Ibge_AF['VL_OBSERVADO_2009']; ?>],
        ['2011', <?php echo $row_Ibge_AF['VL_OBSERVADO_2011']; ?>],
        ['2013', <?php echo $row_Ibge_AF['VL_OBSERVADO_2013']; ?>],
        ['2015', <?php echo $row_Ibge_AF['VL_OBSERVADO_2015']; ?>],
        ['2017', <?php echo $row_Ibge_AF['VL_OBSERVADO_2017']; ?>],
        ['2019', <?php echo $row_Ibge_AF['VL_OBSERVADO_2019']; ?>],
        ['2021', <?php echo $row_Ibge_AF['VL_OBSERVADO_2021']; ?>],
        ['2023', <?php echo $row_Ibge_AF['VL_OBSERVADO_2023']; ?>]

      ]);

      var options = {
        chart: {
          title: 'IDEB MUNICIPAL - ANOS FINAIS',
          subtitle: '',
        },
        legend: { position: "none" } 
      };

      var chart = new google.charts.Bar(document.getElementById('div_anos_finais'));

      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">INDICADORES IDEB</h1>
		<!-- CONTEÚDO -->

    <div class="ls-group-btn ls-group-active">
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_aprovacao.php">Taxa de Aprovação</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right" href="indicadores_saeb.php">Indicadores SAEB</a>
      <a class="ls-btn ls-ico-chart-bar-up ls-ico-right ls-active" href="indicadores_ideb.php">Indicadores IDEB</a>
    </div>  

    <br><br>


    <div class="ls-box">INEP ESCOLA: <strong><?php echo $row_EscolaLogada['escola_inep']; ?></strong></div>
		
		
    <div class="ls-box">
      <h3>ANOS INICIAIS</h3>
    <table class="ls-table">

      <tr>
      <th class="ls-txt-center">2005</th>
      <th class="ls-txt-center">2007</th>
      <th class="ls-txt-center">2009</th>
      <th class="ls-txt-center">2011</th>
      <th class="ls-txt-center">2013</th>
      <th class="ls-txt-center">2015</th>
      <th class="ls-txt-center">2017</th>
      <th class="ls-txt-center">2019</th>
      <th class="ls-txt-center">2021</th>
      <th class="ls-txt-center">2023</th>
      </tr>

      <tr>
      <td class="ls-txt-center"><span class="ls-btn"><?php echo $row_Ibge_AI['VL_OBSERVADO_2005']; ?></span></td>
      <td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2007'] > $row_Ibge_AI['VL_OBSERVADO_2005']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2007']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2009'] > $row_Ibge_AI['VL_OBSERVADO_2007']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2009']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2011'] > $row_Ibge_AI['VL_OBSERVADO_2009']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2011']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2013'] > $row_Ibge_AI['VL_OBSERVADO_2011']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2013']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2015'] > $row_Ibge_AI['VL_OBSERVADO_2013']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2015']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2017'] > $row_Ibge_AI['VL_OBSERVADO_2015']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2017']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2019'] > $row_Ibge_AI['VL_OBSERVADO_2017']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2019']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2021'] > $row_Ibge_AI['VL_OBSERVADO_2019']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2021']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AI['VL_OBSERVADO_2023'] > $row_Ibge_AI['VL_OBSERVADO_2021']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AI['VL_OBSERVADO_2023']; ?>
  </span>
</td>      </tr>

    </table>

    <div id="div_anos_iniciais" style="width: 100%; height: 500px;"></div>

    </div>

    <div class="ls-box">
      <h3>ANOS FINAIS</h3>
    <table class="ls-table">

      <tr>
      <th class="ls-txt-center">2005</th>
      <th class="ls-txt-center">2007</th>
      <th class="ls-txt-center">2009</th>
      <th class="ls-txt-center">2011</th>
      <th class="ls-txt-center">2013</th>
      <th class="ls-txt-center">2015</th>
      <th class="ls-txt-center">2017</th>
      <th class="ls-txt-center">2019</th>
      <th class="ls-txt-center">2021</th>
      <th class="ls-txt-center">2023</th>
      </tr>

      <tr>
      <td class="ls-txt-center"><span class="ls-btn"><?php echo $row_Ibge_AF['VL_OBSERVADO_2005']; ?></span></td>
      <td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2007'] > $row_Ibge_AF['VL_OBSERVADO_2005']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2007']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2009'] > $row_Ibge_AF['VL_OBSERVADO_2007']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2009']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2011'] > $row_Ibge_AF['VL_OBSERVADO_2009']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2011']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2013'] > $row_Ibge_AF['VL_OBSERVADO_2011']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2013']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2015'] > $row_Ibge_AF['VL_OBSERVADO_2013']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2015']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2017'] > $row_Ibge_AF['VL_OBSERVADO_2015']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2017']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2019'] > $row_Ibge_AF['VL_OBSERVADO_2017']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2019']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2021'] > $row_Ibge_AF['VL_OBSERVADO_2019']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2021']; ?>
  </span>
</td>
<td class="ls-txt-center">
  <span class="ls-btn<?php if ($row_Ibge_AF['VL_OBSERVADO_2023'] > $row_Ibge_AF['VL_OBSERVADO_2021']) { ?> ls-ico-shaft-up ls-color-success<?php } else { ?> ls-ico-shaft-down ls-color-danger<?php } ?>">
    <?php echo $row_Ibge_AF['VL_OBSERVADO_2023']; ?>
  </span>
</td>      </tr>

    </table>

    <div id="div_anos_finais" style="width: 100%; height: 500px;"></div>

    </div>

    <div class="ls-box">

    <h3>PROJETADO X OBSERVADO - ANOS INICIAIS</h3>

    <div id="proj_obs_ai" style="width: 100%; height: 500px;"></div>

    </div>
    
    <div class="ls-box">

    <h3>PROJETADO X OBSERVADO - ANOS FINAIS</h3>

    <div id="proj_obs_af" style="width: 100%; height: 500px;"></div>

    </div>
		
		
		
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
    <script src="https://assets.locaweb.com.br/locastyle/3.8.4/javascripts/locastyle.js" type="text/javascript"></script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
