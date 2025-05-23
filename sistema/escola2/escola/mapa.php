<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include "fnc/anti_injection.php"; ?>

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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_Turma = "-1";
if (isset($_GET['turma'])) {
  anti_injection($colname_Turma = $_GET['turma']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matriculas = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao,
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_endereco, aluno_numero, aluno_bairro, aluno_uf, aluno_municipio, aluno_localizacao, aluno_foto,
turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_escola = '$row_EscolaLogada[escola_id]' AND vinculo_aluno_id_turma = '$colname_Turma' AND aluno_localizacao = '1'
";
$Matriculas = mysql_query($query_Matriculas, $SmecelNovo) or die(mysql_error());
$row_Matriculas = mysql_fetch_assoc($Matriculas);
$totalRows_Matriculas = mysql_num_rows($Matriculas);

if ($totalRows_Matriculas == 0) {
  header("Location: index.php?erro");
  exit;
}

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

  <style>
        /* Faz o mapa ocupar toda a tela */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100%;
            width: 100%;
        }
    </style>

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">MAPA DE ALUNOS DO <?php echo $row_Matriculas['turma_nome']; ?></h1>
		<!-- CONTEÚDO -->

    <p><a href="turmaListar.php" class="ls-btn">VOLTAR</a><a href="mapa_escola.php" class="ls-btn">ALUNOS DA ESCOLA</a></p><br>

    <p><small><?php echo $totalRows_Matriculas; ?> aluno(as)</small></p>

		
       
    <div id="container" style="height: 70vh; width: 100%;">
    <div id="map" style="height: 100%; width: 100%;"></div>
    </div>

    <!-- Carregamento da API do Google Maps -->
    <!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZmOxO_HqkcybUcGsj_aOIe-zsOlxq9Ak&callback=initMap"></script>-->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZmOxO_HqkcybUcGsj_aOIe-zsOlxq9Ak&callback=initMap&libraries=geometry" async defer></script>
    
    
    
    <script>
    // Lista de endereços, informações adicionais e fotos
    const addresses = [
        {
            address: "<?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?>, <?php echo $row_EscolaLogada['sec_cidade']; ?>, <?php echo $row_EscolaLogada['sec_uf']; ?>",
            info: "<?php echo $row_EscolaLogada['escola_nome']; ?> | INEP <?php echo $row_EscolaLogada['escola_inep']; ?>",
            photo: "<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>https://www.smecel.com.br/img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>https://www.smecel.com.br/img/brasao_republica.png<?php } ?>",
            isSchool: true // Marca como o endereço da escola
        },
        <?php do { ?>
        {
            address: "<?php echo $row_Matriculas['aluno_endereco']; ?>, <?php echo $row_Matriculas['aluno_numero']; ?>, <?php echo $row_Matriculas['aluno_bairro']; ?>, <?php echo $row_EscolaLogada['sec_cidade']; ?>, <?php echo $row_EscolaLogada['sec_uf']; ?>",
            info: "<?php echo $row_Matriculas['aluno_nome']; ?> - Turma: <?php echo $row_Matriculas['turma_nome']; ?>",
            photo: "<?php if ($row_Matriculas['aluno_foto'] == "") { ?>https://www.smecel.com.br/aluno/fotos/semfoto.jpg<?php } else { ?>https://www.smecel.com.br/aluno/fotos/<?php echo $row_Matriculas['aluno_foto']; ?><?php } ?>"
        },
        <?php } while ($row_Matriculas = mysql_fetch_assoc($Matriculas)); ?>
    ];

    let schoolPosition = null;

    // Nome da cidade para centralização inicial
    const city = "<?php echo $row_EscolaLogada['sec_cidade']; ?>, <?php echo $row_EscolaLogada['sec_uf']; ?>";

    // Função de inicialização do mapa
    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: { lat: -14.235004, lng: -51.92528 },
            mapTypeId: "roadmap"
        });

        const geocoder = new google.maps.Geocoder();

        // Centraliza o mapa com base no nome da cidade
        geocoder.geocode({ address: city }, (results, status) => {
            if (status === "OK") {
                const cityLocation = results[0].geometry.location;
                map.setCenter(cityLocation);
                map.setZoom(14);
            } else {
                console.error(`Erro ao centralizar o mapa para ${city}: ${status}`);
            }
        });

        // Adiciona marcadores ao mapa com intervalo de requisições
        addresses.forEach((location, index) => {
            setTimeout(() => {
                geocodeAddress(geocoder, map, location);
            }, index * 300);
        });
    }

    // Função para buscar coordenadas e exibir marcadores
    function geocodeAddress(geocoder, map, location) {
        geocoder.geocode({ address: location.address }, (results, status) => {
            if (status === "OK") {
                const position = results[0].geometry.location;

                if (location.isSchool) {
                    schoolPosition = position;
                }

                // Cria um ícone circular usando canvas
                const canvas = document.createElement('canvas');
                canvas.width = location.isSchool ? 70 : 50; // Ícone maior para a escola
                canvas.height = location.isSchool ? 70 : 50;
                const ctx = canvas.getContext('2d');

                const img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = () => {
                    ctx.beginPath();
                    ctx.arc(canvas.width / 2, canvas.height / 2, canvas.width / 2, 0, Math.PI * 2, true);
                    ctx.closePath();
                    ctx.clip();
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    const icon = {
                        url: canvas.toDataURL(),
                        scaledSize: new google.maps.Size(canvas.width, canvas.height)
                    };

                    // Adiciona o marcador ao mapa
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        icon: icon,
                        title: location.info
                    });

                    // Calcula a distância até a escola
                    let content = `<strong>${location.info}</strong><p>${location.address}</p>`;
                    if (!location.isSchool && schoolPosition) {
                        const distance = google.maps.geometry.spherical.computeDistanceBetween(
                            position,
                            schoolPosition
                        );
                        content += `<br>Distância até a escola: ${Math.round(distance)} metros`;
                    }

                    // Adiciona uma janela de informações ao marcador
                    const infoWindow = new google.maps.InfoWindow({
                        content: content
                    });

                    marker.addListener("click", () => {
                        infoWindow.open(map, marker);
                    });
                };
                img.src = location.photo; // Define a URL da imagem
            } else {
                console.warn(`Endereço não encontrado: ${location.address}`);
            }
        });
    }
</script>







  
  
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

mysql_free_result($Matriculas);

mysql_free_result($EscolaLogada);
?>
