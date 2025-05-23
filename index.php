<?php require_once('Connections/SmecelNovoPDO.php'); ?>
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['login'])) {
  $loginUsername = strtoupper($_POST['login']); // Converte o e-mail para maiúsculo
  $password = $_POST['senha']; // Senha mantida como está (pode ser sensível a maiúsculas)
  $MM_redirectLoginSuccess = "sistema/index.php";
  $MM_redirectLoginFailed = "index.php?err";
  $MM_redirecttoReferrer = false;

  // Prepara a query com PDO
  $query = "SELECT usu_email, usu_senha, usu_tipo, usu_status 
            FROM smc_usu 
            WHERE usu_email = :email AND usu_senha = :senha";
  $stmt = $SmecelNovo->prepare($query);
  $stmt->bindParam(':email', $loginUsername, PDO::PARAM_STR);
  $stmt->bindParam(':senha', $password, PDO::PARAM_STR);
  $stmt->execute();

  $row_login = $stmt->fetch(PDO::FETCH_ASSOC);
  $loginFoundUser = $stmt->rowCount();

  // Verifica se o usuário está inativo
  if ($loginFoundUser && $row_login['usu_status'] == "2") {
      header("Location: index.php?inativo");
      die();
  }

  if ($loginFoundUser) {
      $loginStrGroup = $row_login['usu_tipo'];

      // Regenera a sessão para segurança
      if (PHP_VERSION >= 5.1) {
          session_regenerate_id(true);
      } else {
          session_regenerate_id();
      }

      // Define variáveis de sessão
      $_SESSION['MM_Username'] = $loginUsername;
      $_SESSION['MM_UserGroup'] = $loginStrGroup;

      if (isset($_SESSION['PrevUrl']) && $MM_redirecttoReferrer) {
          $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
      }
      header("Location: " . $MM_redirectLoginSuccess);
      die();
  } else {
      header("Location: " . $MM_redirectLoginFailed);
      die();
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SMECEL - Sistema de Gestão Escolar Municipal</title>
  <meta name="description" content="Tenha o controle das informações Educacionais em seu município na palma da mão" />

  <link rel="canonical" href="https://www.smecel.com.br/" />
  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="SMECEL - Sistema de Gestão Escolar Municipal" />
  <meta property="og:description"
    content="Tenha o controle das informações Educacionais em seu município na palma da mão" />
  <meta property="og:url" content="https://www.smecel.com.br/" />
  <meta property="og:site_name" content="SMECEL" />
  <meta property="og:image" content="https://www.smecel.com.br/img/quadro1.jpg" />
  <meta property="og:image:width" content="600" />
  <meta property="og:image:height" content="400" />
  <meta property="og:image:type" content="image/jpeg" />
  <meta name="author" content="DR WATSON" />

  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="css/animate.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="css/app.css" media="screen,projection" />
  <style>
    body {
      display: flex;
      min-height: 80vh;
      flex-direction: column;
      margin: 0px;
      background-color: #fff;
      overflow: hidden;
    }

    main {
      flex: 1 0 auto;
    }

    .container {
      width: 100vw;
      height: 80vh;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center
    }

    .transparent {
      background-color: rgba(0, 0, 0, 0);
      box-shadow: 0px 0px 0px rgba(0, 0, 0, 0)
    }

    .sombra {
      text-shadow: 1px 1px 2px black;
    }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>


  <div class="navbar-fixed">
    <nav class="transparent" role="navigation">
      <div class="nav-wrapper"> <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i
            class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
          <li><a class="waves-effect waves-light btn-flat white-text sombra" href="aluno/"><i
                class="material-icons left">people</i>ÁREA DO ALUNO</a></li>
          <li><a class="waves-effect waves-light btn-flat white-text sombra" href="professor/"><i
                class="material-icons left">school</i>ÁREA DO PROFESSOR</a></li>
          <li><a class="waves-effect waves-light btn-flat white-text sombra modal-trigger" href="#login"><i
                class="material-icons left">lock_outline</i>ÁREA ADMINISTRATIVA</a></li>
        </ul>
      </div>
    </nav>
  </div>
  <ul class="sidenav" id="mobile-demo">

    <li>
      <div class="user-view">
        <div class="background">
          <img src="imagens/1.jpg" width="100%" class="responsive-img">
        </div>
        <br><br>
        <a href="#"><span class="white-text">ÁREA RESTRITA</span></a>
        <br>
      </div>
    </li>


    <li><a class="waves-effect waves-light" href="aluno/"><i class="material-icons left">people</i>ALUNO</a></li>
    <li><a class="waves-effect waves-light" href="professor/"><i class="material-icons left">school</i>PROFESSOR</a>
    </li>
    <li><a class="waves-effect waves-light modal-trigger" href="#login"><i
          class="material-icons left">lock_outline</i>PAINEL</a></li>
  </ul>
  <main>


    <center>
      <div class="container">
        <div class="row" style="display: inline-block; padding: 32px 48px 0px 48px;"> <a href="index.php"><img
              src="img/logo_smecel_background_flattened.png" class="responsive-img jello wow" data-wow-delay="0s"
              data-wow-duration="0.6s"></a> </div>
      </div>
    </center>

  </main>

  <!-- Modal Structure -->
  <div id="login" class="modal">
    <div class="modal-content">
      <p></p>
      <div class="row">

        <a href="#!" class="modal-action modal-close right-align right"><i class="material-icons">close</i></a>

        <center>
          <img src="img/logo_smecel_card.png" width="200">
        </center>

        <form class="col s12" action="<?php echo $loginFormAction; ?>" method="POST" name="logar" class="formLogin">
          <div class="row">
            <div class="input-field col s12"> <i class="material-icons prefix">email</i>
              <input type="email" id="usuario" name="login" value="" placeholder="E-mail" required>
            </div>
            <div class="input-field col s12"> <i class="material-icons prefix">lock_outline</i>
              <input type="password" id="senha" name="senha" value="" placeholder="Senha" required>
            </div>

            <div class="input-field col s12">
              <input name="logar" type="submit" value="ENTRAR" class="waves-effect waves-light btn" />
              <a href="lembrarsenha.php" class="waves-effect waves-light btn btn-flat right"><small>LEMBRAR
                  SENHA</small></a>
              <div id="resultado"></div>

            </div>
          </div>
        </form>




      </div>
    </div>
  </div>


  <!-- 
<div id="login" class="modal">
   <div class="modal-content">
    <p></p>
    <div class="row">
    
    <a href="#!" class="modal-action modal-close right-align right"><i class="material-icons">close</i></a>
    
       <center>
        <img src="img/logo_smecel_card.png" width="200">
      </center>
      
       <form class="col s12" method="post" action="" class="formLogin" id="#login-form">
       <div class="row">
        <div class="input-field col s12"> <i class="material-icons prefix">email</i>
           <input type="email" id="usuario" name="usuario" value="" placeholder="E-mail" required>
         </div>
        <div class="input-field col s12"> <i class="material-icons prefix">lock_outline</i>
           <input type="password" id="senha" name="senha" value="" placeholder="Senha" required>
         </div>
         
        <div class="input-field col s12">
        <a href="javascript:void()" id="btnEntrar" value="ACESSAR" class="waves-effect waves-light btn">ENTRAR</a> 
        <a href="lembrarsenha.php" class="waves-effect waves-light btn btn-flat right"><small>LEMBRAR SENHA</small></a>
        <div id="resultado"></div>
        
        </div>
      </div>
       </form>
      
       
       
       
     </div>
  </div>
 </div>
 
 
Modal Structure -->

  <!--

    <footer class="page-footer transparent row">
          <div class="footer-copyright row col s12">
      <a class="modal-trigger right white-text" href="#modalValidacao">Validar documentos</a>
          </div>
        </footer>
    
  <div id="modalValidacao" class="modal">
    <div class="modal-content">
      <h4>Validar</h4>
      <p>
    
    
  <div class="row">
    <form class="col s12" method="get" action="publico/validar.php" target="_blank" >

      <div class="row">
    
        <div class="input-field col s3">
          <input type="text" name="cod1" class="validate" maxlength="4" size="4">
        </div>
        <div class="input-field col s3">
          <input type="text" name="cod2" class="validate" maxlength="4" size="4">
      </div>
        <div class="input-field col s3">
          <input type="text" name="cod3" class="validate" maxlength="4" size="4">
        </div>
        <div class="input-field col s3">
          <input type="text" name="cod4" class="validate" maxlength="4" size="4">
        </div>
    
    
    </div>


   

    
    
    
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn-flat">VERIFICAR</button> </form>
    </div>
  </div>
  </div>
-->



  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="js/materialize.min.js"></script>
  <script type="text/javascript" src="js/wow.min.js"></script>
  <script type="text/javascript" src="js/pace.min.js"></script>
  <script src="snowfall/jquery.letItSnow.min.js"></script>
  <script>
    new WOW().init();
  </script>

  <script>
    $(document).ready(function () {
      $('body').letItSnow({

        flake: {

          // Default: <a href="https://www.jqueryscript.net/tags.php?/Bootstrap/">Bootstrap</a> Icons
          html: '<i class="bi bi-snow3"></i>',

          // min snowflake size
          minSize: 5,

          // max snowflake size
          maxSize: 20,

        }

      });
    });
  </script>


  <script>

    $('.parallax').parallax();
    $('.sidenav').sidenav();
    $('.modal').modal();





    $(document).ready(function () {
      var upperLimit = <?php $diretorio = scandir("imagens/");
      $qtd = count($diretorio) - 2;
      echo ("$qtd"); ?>;
      var randomNum = Math.floor((Math.random() * upperLimit) + 1);
      $("body").css("background", "url('imagens/1a.jpg') no-repeat center center fixed");//<--changed path
      //$("body").css("background","url('imagens/" + randomNum + ".jpg') no-repeat center center fixed");//<--changed path
      $("body").css("-webkit-background-size", "cover");
      $("body").css("-moz-background-size", "cover");
      $("body").css("-o-background-size", "cover");
      $("body").css("background-size", "cover");
      //$("body").css("-webkit-transition","background 8s ease-in-out");
      //$("body").css("-moz-transition","background 8s ease-in-out");
      //$("body").css("-o-transition","background 8s ease-in-out");
      //$("body").css("-ms-transition","background 8s ease-in-out");
      //$("body").css("transition","background 8s ease-in-out");
      $("body").css("-webkit-backface-visibility", "hidden");
      $("body").css("background-color", "black");



    });


  </script>
  <?php if (isset($_GET["err"])) { ?>
    <script>
      M.toast({
        html: 'Usuário/senha não conferem.',
        classes: 'red darken-3'
      });
    </script>
  <?php } ?>
  <?php if (isset($_GET["ops"])) { ?>
    <script>
      M.toast({
        html: 'Ops! Algo errado não está certo por aqui.',
        classes: 'red darken-3'
      });
    </script>
  <?php } ?>


  <?php if (isset($_GET["financeiro"])) { ?>
    <script>
      M.toast({
        html: 'Existe pendências financeiras. Entre em contato com o setor responsável.',
        classes: 'red darken-3'
      });
    </script>
  <?php } ?>



  <?php if (isset($_GET["inativo"])) { ?>
    <script>
      M.toast({
        html: 'Usuário inativo. Procure a direção da escola.',
        classes: 'orange darken-3'
      });
    </script>
  <?php } ?>
  <?php if (isset($_GET["saiu"])) { ?>
    <script>
      //M.toast({html: 'Você saiu do sistema em segurança.'});
      M.toast({
        html: 'Você saiu do sistema em segurança.',
        classes: 'green darken-1'
      });
    </script>
  <?php } ?>
  <?php if (isset($_GET["exit"])) { ?>
    <script>
      M.toast({
        html: 'Você saiu do sistema em segurança.',
        classes: 'green darken-1'
      });
    </script>
  <?php } ?>


  <script type="text/javascript">
    /*
        $(document).ready(function(){
           
            $("#btnEntrar").click(function(){
                
                var envio = $.post("login.php", {
                usuario: $("#usuario").val(),
                senha: $("#senha").val()
                })
                
                envio.done(function(data) {
                    $("#resultado").html(data);
                })
               
                envio.fail(function() { alert("Erro na requisição post"); })
            });
            return false;
        });
    
    */
  </script>


</body>

</html>