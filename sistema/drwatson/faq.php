<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>


<?php
// Inicializa a sessão
if (!isset($_SESSION)) {
    session_start();
}

// Controle de logout
if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
    $_SESSION['MM_Username'] = NULL;
    $_SESSION['MM_UserGroup'] = NULL;
    unset($_SESSION['MM_Username']);
    unset($_SESSION['MM_UserGroup']);
    header("Location: ../../index.php?exit");
    exit;
}

// Controle de acesso
$MM_authorizedUsers = "99";
$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])))) {
    header("Location: " . $MM_restrictGoTo);
    exit;
}

// Função para manipular valores SQL
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType)
    {
        $theValue = ($theType == "text") ? "'" . mysql_real_escape_string($theValue) . "'" : intval($theValue);
        return $theValue;
    }
}

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "99";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

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
  
  $colname_UsuarioLogado = "-1";
  if (isset($_SESSION['MM_Username'])) {
    $colname_UsuarioLogado = $_SESSION['MM_Username'];
  }
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
  $UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
  $row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
  $totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);

  ?>
<?php
// Conexão ao banco de dados
mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Inicializa variáveis
$resultados = [];
$mensagem = "";

// Processa a busca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['termo_busca'])) {
    $termo = mysql_real_escape_string($_POST['termo_busca']);

    // Consulta para buscar correspondências semânticas em perguntas e variações
    $query_Busca = "
        SELECT 
            r.id, 
            r.pergunta_padrao, 
            r.resposta_oficial,
            r.likes,
            r.dislikes,
            MATCH (v.variacao) AGAINST ('$termo' IN NATURAL LANGUAGE MODE) AS relevancia
        FROM 
            smc_faq_respostas r
        LEFT JOIN 
            smc_faq_variacoes_perguntas v 
        ON 
            r.id = v.id_resposta
        WHERE 
            MATCH (v.variacao) AGAINST ('$termo' IN NATURAL LANGUAGE MODE)
        GROUP BY 
            r.id
        ORDER BY 
            relevancia DESC, r.pergunta_padrao ASC
        LIMIT 1
    ";

    $resultadoBusca = mysql_query($query_Busca, $SmecelNovo) or die(mysql_error());
    $totalResultados = mysql_num_rows($resultadoBusca);

    if ($totalResultados > 0) {
        while ($row = mysql_fetch_assoc($resultadoBusca)) {
            $resultados[] = $row;
        }
    } else {
        $mensagem = "Nenhum resultado encontrado para o termo: " . htmlspecialchars($termo);
    }
}

function makeLinksClickable($text) {
  // Expressão regular para encontrar URLs
  $urlPattern = '/(https?:\/\/[^\s]+)/i';

  // Substituir URLs por links clicáveis
  return preg_replace_callback($urlPattern, function($matches) {
      $url = htmlspecialchars($matches[0]);
      return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a>";
  }, $text);
}
?>



<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
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
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once("menu.php"); ?>

<main class="ls-main">
    <div class="container-fluid">
        <h1 class="ls-title-intro">Página de ajuda</h1>

        <div class="ls-box-filter">
  <form method="post" action="faq.php" class="ls-form ls-form-inline ls-float-left" style="display: flex; gap: 10px; width: 100%;">
    <label class="ls-label" role="search" style="flex: 1; margin: 0;">
      <input type="text" id="termo_busca" name="termo_busca" class="ls-field" placeholder="Digite o termo de busca..." required style="width: 100%;">
    </label>
    <div class="ls-actions-btn">
      <button type="submit" class="ls-btn-primary">Buscar</button>
    </div>
  </form>
</div>



        <!-- Resultados da busca -->
<?php if (!empty($mensagem)) { ?>
    <div class="ls-alert-danger" style="margin-top: 20px;">
        <?= $mensagem ?>
    </div>
<?php } ?>

<?php if (!empty($resultados)) { ?>
  
        
                <?php foreach ($resultados as $resultado) { ?>
                  <div class="ls-box">
                        <h5 class="ls-title-4"><?= htmlspecialchars($resultado['pergunta_padrao']) ?></h5>
                        <br>
                        <?= nl2br(makeLinksClickable(strip_tags($resultado['resposta_oficial']))) ?>
                        <!--<small><?= number_format($resultado['relevancia'], 2) ?></small>-->

                        <hr>
                        <p>Essa informação foi útil?</p>
                        <div class="ls-group-btn" id="feedback-section-<?= $resultado['id'] ?>">
                            <button class="ls-btn" id="like-button-<?= $resultado['id'] ?>" onclick="sendFeedback(<?= $resultado['id'] ?>, 'like')">👍 Sim</button>
                            <button class="ls-btn" id="dislike-button-<?= $resultado['id'] ?>" onclick="sendFeedback(<?= $resultado['id'] ?>, 'dislike')">👎 Não</button>
                        </div>

                        <div id="thank-you-message-<?= $resultado['id'] ?>" style="display: none; margin-top: 10px;">
                            <p>Obrigado pelo seu feedback! 😊</p>
                        </div>

                </div>

                <!-- Pergunta "Essa informação foi útil?" -->
                    
          
                <?php } ?>
            
    
<?php } ?>

    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>

<script>
function sendFeedback(id, type) {
    // Desativa os botões para evitar múltiplos cliques
    const likeButton = document.getElementById(`like-button-${id}`);
    const dislikeButton = document.getElementById(`dislike-button-${id}`);
    likeButton.disabled = true;
    dislikeButton.disabled = true;

    fetch('faq/feedback.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id: id, type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Esconde os botões e mostra a mensagem de agradecimento
            const feedbackSection = document.getElementById(`feedback-section-${id}`);
            const thankYouMessage = document.getElementById(`thank-you-message-${id}`);
            if (feedbackSection) feedbackSection.style.display = 'none';
            if (thankYouMessage) thankYouMessage.style.display = 'block';
        } else {
            // Reativa os botões em caso de erro
            likeButton.disabled = false;
            dislikeButton.disabled = false;
            alert('Erro ao enviar feedback. Tente novamente.');
        }
    })
    .catch(error => {
        console.error('Erro ao enviar feedback:', error);
        // Reativa os botões em caso de erro
        likeButton.disabled = false;
        dislikeButton.disabled = false;
        alert('Erro ao enviar feedback. Tente novamente.');
    });
}
</script>



</body>
</html>
