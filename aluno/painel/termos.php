<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>
<?php include('../../sistema/funcoes/url_base.php'); ?>
<?php include('../../sistema/funcoes/idade.php'); ?>
<?php include('../../sistema/funcoes/anti_injection.php'); ?>

<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);

  $logoutGoTo = "../index.php?saiu";
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
$MM_authorizedUsers = "6";
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

$MM_restrictGoTo = "../index.php?err";
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


$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
  SELECT * FROM smc_aluno
  INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
  WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
if($totalRows_AlunoLogado=="") {
  header("Location: index.php?loginErr");
}

if (isset($_GET['uid'])) {

  $hash = anti_injection($_GET['uid']);

}else{
  $hash = anti_injection($row_AlunoLogado['aluno_hash']);
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Perfil = sprintf("
  SELECT * 
  FROM smc_aluno 
  WHERE aluno_hash = %s", GetSQLValueString($hash, "text"));
$Perfil = mysql_query($query_Perfil, $SmecelNovo) or die(mysql_error());
$row_Perfil = mysql_fetch_assoc($Perfil);
$totalRows_Perfil = mysql_num_rows($Perfil);
if($totalRows_Perfil=="") {
  header("Location: index.php?loginErr");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, 
vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, turma_id, turma_nome, turma_turno, turma_tipo_atendimento, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome, sec_id, sec_cidade, sec_uf 
FROM smc_vinculo_aluno 
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola
INNER JOIN smc_sec ON sec_id = vinculo_aluno_id_sec
WHERE vinculo_aluno_id_aluno = '$row_Perfil[aluno_id]' AND turma_tipo_atendimento = '1' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
$Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
$row_Matricula = mysql_fetch_assoc($Matricula);
$totalRows_Matricula = mysql_num_rows($Matricula);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Colegas = "
SELECT 
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola,
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, 
vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_dependencia,
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_foto, aluno_hash 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
WHERE vinculo_aluno_id_turma = '$row_Matricula[vinculo_aluno_id_turma]' AND vinculo_aluno_dependencia = 'N'
ORDER BY RAND()
";
$Colegas = mysql_query($query_Colegas, $SmecelNovo) or die(mysql_error());
$row_Colegas = mysql_fetch_assoc($Colegas);
$totalRows_Colegas = mysql_num_rows($Colegas);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  $termos = 0;
  if($_POST['termos'] == "on"){
    $termos = "S";
  }

  $updateSQL = sprintf("UPDATE smc_aluno 
    SET 
    aluno_aceite_termos = '$termos'
    WHERE aluno_hash=%s",
    GetSQLValueString($row_AlunoLogado['aluno_hash'], "text"));


  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $Result1 = mysql_query($updateSQL, $SmecelNovo) or die(mysql_error());


  $updateGoTo = "perfil.php?bemvindo";
  //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
  //}
  header(sprintf("Location: %s", $updateGoTo));


  //$updateGoTo = "celular.php?aluno=".$_POST['aluno_hash'];

}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $row_AlunoLogado['aluno_nome']; ?> - EduConnect</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
  <link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">


<style>



    table {
     width:100%;
     border-collapse: collapse;
     font-size:12px;
   }
   th, td {
     border:0px solid #ccc;
   }
   th, td {
     padding:5px;
     height:15px;
     line-height:15px;
   }
   .aluno {
     background-color: #ddd;
     border-radius: 0%;
     object-fit: cover;
     height: 80px;
     width:  100%;
   }

   @media only screen and (max-width: 600px) {
    .profile-pic-mobile {
      width: 100%; /* Ajuste conforme necessário */
      padding-top: 18px;
    }
    blockquote.hide-on-small-only h4 {
      margin: 0; /* Remove margem para alinhar corretamente */
    }
  }


</style>


</head>
<body class="<?= $row_Perfil['aluno_cor_fundo'] ?> lighten-4">

  <?php include "menu_top_social.php"?>

  <div class="container">

    <div class="row white" style="margin: 10px 0;">

      <div class="col s12 m3 hide-on-small-only">


      </div>

      <div class="col s12 m6 1flow-text">

        <h3 class="center">LEIA NOSSOS TERMOS</h3> 
        
        <hr>

        <h5>Termos de Uso da Rede Social Escolar "EduConnect"</h5>
        <p class="">
        Bem-vindo(a) à nossa rede social escolar! Antes de começar a interagir, leia atentamente os termos abaixo e clique em "Aceito" para confirmar sua concordância. Estes termos são importantes para garantir um ambiente seguro, respeitoso e adequado para todos.       </p>

       <h5>Objetivo da Rede Social</h5>
       <p class="">
       A "EduConnect" foi criada exclusivamente para os alunos matriculados na rede escolar. O objetivo é promover a convivência e o respeito entre os estudantes, incentivando a empatia, a colaboração e o apoio mútuo. Nesta plataforma, você pode interagir com seus colegas, compartilhar experiências, aprender e crescer juntos.       </p>

       <h5>Respeito e Empatia</h5>
       <p class="">
       Todos os participantes devem tratar uns aos outros com respeito. Bullying, assédio, discriminação ou qualquer tipo de comportamento ofensivo não serão tolerados. Contribua para um ambiente positivo, apoiando e incentivando seus colegas.       </p>

       <h5>Privacidade e Segurança</h5>
       <p class="">
       Seus dados pessoais são protegidos pela LGPD (Lei Geral de Proteção de Dados). Isso significa que suas informações serão usadas apenas para fins educacionais e de convivência social dentro da rede "EduConnect" e nunca serão compartilhadas com terceiros sem sua permissão. Você tem o direito de acessar, corrigir ou solicitar a exclusão dos seus dados a qualquer momento.       </p>

       <h5>Direitos e Deveres dos Estudantes</h5>
       <p class="">
       Você tem o direito de compartilhar suas opiniões e ideias de forma respeitosa. É seu dever manter a privacidade dos seus colegas. Não compartilhe informações pessoais de outros estudantes sem permissão. Use a rede de forma responsável, lembrando-se de que suas ações podem afetar os outros.       </p>

       <h5>Moderação e Consequências</h5>
       <p class="">
       A rede "EduConnect" é monitorada para garantir um ambiente seguro e positivo. Comentários ou ações que violem estes termos poderão ser removidos, e os responsáveis poderão ser advertidos ou suspensos da rede. Em casos graves, os pais ou responsáveis, bem como a escola, serão notificados.       </p>

       <h5>Educação Digital e Comportamento Online</h5>
       <p class="">
       Como parte da Política Nacional de Educação Digital (PNED), incentivamos práticas saudáveis e éticas no uso da internet. Aprenda e pratique boas maneiras digitais, como respeitar a privacidade alheia e evitar a disseminação de fake news.       </p>

       <h5>Termos e Atualizações</h5>
       <p class="">
       Estes termos podem ser atualizados periodicamente para refletir novas práticas ou políticas. Você será informado(a) de quaisquer mudanças significativas e deverá revisar e aceitar os novos termos para continuar usando a rede "EduConnect".      </p>

       <hr>

      <?php if ($row_AlunoLogado['aluno_aceite_termos']=="N") { ?>
       <form method="post"action="termos.php" name="form1" id="form1">
        <p>
          <label>
            <input type="checkbox" name="termos" required class="validate" />
            <span>Ao clicar em "Aceito", você concorda com todos os termos descritos acima e se compromete a utilizá-la de maneira responsável e respeitosa.</span>
          </label>
        </p>
        <input type="hidden" name="MM_update" value="form1" />
        <button type="submit" class="waves-effect waves-light btn center-align">ACEITAR OS TERMOS</button><br><br>
        <a class="" href="index.php"><i class="material-icons left">chevron_left</i> VOLTAR</a>

      </form>
      <?php } else { ?>

        <div class="card-panel blue lighten-5 center">
        Você já aceitou os termos.
        </div>

        <p><a class="right" href="perfil.php"><i class="material-icons right">chevron_right</i>ACESSAR</a></p><br><br><br><br>

      <?php } ?>

      <br>


    </div>


    <div class="col s12 m3">



    </div>


    </div>





  </div>



  <!--JavaScript at end of body for optimized loading--> 
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> 
  <script type="text/javascript" src="../js/materialize.min.js"></script> 

</body>
</html>
<?php
mysql_free_result($Matricula);

mysql_free_result($AlunoLogado);
?>
