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

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>
  <title>Painel do Professor - SMECEL</title>

  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="../css/animate.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="css/app.css" media="screen,projection" />

  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }

    main {
      flex: 1 0 auto;
    }

    body {
      background: #fff;
      margin: 0px;
    }

    .input-field input[type=date]:focus+label,
    .input-field input[type=text]:focus+label,
    .input-field input[type=email]:focus+label,
    .input-field input[type=password]:focus+label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=email]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;
    }

    .container {
      width: 100vw;
      height: 100vh;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center
    }

    .box {
      width: 300px;
      height: 300px;
    }
  </style>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <main>
    <center>
      <div class="container">
        <div class="z-depth-1 grey lighten-4 row"
          style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">
          <?php if (isset($_GET["saiu"])) { ?>
            <p class="green-text"><i class="medium material-icons">sentiment_very_satisfied</i><br>
              Até logo!</p>
          <?php } ?>
          <div id="loginErrorMessage" class="red-text" style="display: none;">
            <i class="medium material-icons">sentiment_very_dissatisfied</i>
            <p id="errorText"></p>
          </div>

          <p class="indigo-text"><i class="material-icons">school</i> <br>PAINEL DO PROFESSOR</p>

          <div id="responseMessage" style="display: none;" class="card-panel green white-text"></div>

          <form id="loginForm" class="col s12 box" style="width:350px; height:360px;">
            <div class="input-field col s12">
              <input class='validate' value="<?php if(isset($_GET['codigo'])){ echo $_GET['codigo']; } ?>" id='codigo' name="codigo" type="text" required autofocus>
              <label for='codigo'>Código do Professor</label>
            </div>

            <div class="input-field col s12">
              <input class='validate' value="<?php if(isset($_GET['email'])){ echo $_GET['email']; } ?>"  id='email' name="email" type="email" required>
              <label for='email'>E-mail</label>
            </div>

            <div class="input-field col s12">
              <input class='validate' id='senha' value="<?php if(isset($_GET['senha'])){ echo $_GET['senha']; } ?>" name="senha" type="password" required>
              <label for='senha'>Senha</label>
            </div>

            <center>
              <div class='row'>
                <button id="loginButton" type="submit" class='col s12 btn btn-large waves-effect indigo'>ENTRAR</button>
              </div>
              <a class="btn btn-flat right modal-trigger" href="#modal1">Esqueci minha senha</a>
              <a class='btn btn-flat waves-effect' href="../">Voltar</a>
            </center>
          </form>
        </div>
      </div>
    </center>
  </main>





  <!-- Modal Structure -->
  <div id="modal1" class="modal">
    <div class="modal-content">
      <p>


        <center>
          <img src="../img/logo_smecel_card.png" width="300">
        </center>


      <form method="post" action="" class="col s12">
        <div class="row">
          <div class="input-field col s12"> <i class="material-icons prefix">email</i>
            <input type="email" id="email_rec" name="email_rec" value=""
              placeholder="Informe o e-mail para recuperar a senha" required>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
            <a type="button" id="btnSenha" class="waves-effect waves-light btn">Recuperar senha</a>
          </div>
        </div>
        <div id="resultadoSenha">
          <!-- Essa div irá receber os resultados -->
        </div>
      </form>

      </p>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">VOLTAR</a>
    </div>
  </div>

  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="js/materialize.min.js"></script>
  <script type="text/javascript" src="../js/wow.min.js"></script>
  <script type="text/javascript" src="../js/pace.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#loginForm').on('submit', function (e) {
        e.preventDefault(); // Evita o envio padrão do formulário

        // Desabilita o botão e exibe o estado de carregamento
        const loginButton = $('#loginButton');
        loginButton.text('Entrando...').prop('disabled', true);

        // Coleta os dados do formulário
        const formData = {
          codigo: $('#codigo').val(),
          email: $('#email').val(),
          senha: $('#senha').val()
        };

        // Faz a requisição AJAX
        $.ajax({
          url: 'processa_login.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              $('#responseMessage').text('Login realizado com sucesso! Redirecionando...')
                .removeClass('red')
                .addClass('green')
                .fadeIn();

              // Redireciona após 2 segundos
              setTimeout(() => {
                window.location.href = response.redirect;
              }, 2000);
            } else {
              $('#responseMessage').text(response.message)
                .removeClass('green')
                .addClass('red')
                .fadeIn();
              loginButton.text('ENTRAR').prop('disabled', false); // Reabilita o botão
            }
          },
          error: function () {
            $('#responseMessage').text('Erro no servidor. Tente novamente mais tarde!')
              .removeClass('green')
              .addClass('red')
              .fadeIn();
            loginButton.text('ENTRAR').prop('disabled', false); // Reabilita o botão
          }
        });
      });
    });
  </script>

  <script>
    new WOW().init();

    $(document).ready(function () {
      $('.modal').modal();






      //Recuperar senha
      $("#btnSenha").click(function (e) {
        var envio = $.post("senha.php", {
          email: $("#email_rec").val()
        })
        envio.done(function (data) {
          $("#resultadoSenha").html(data);
        })
        envio.fail(function () { alert("Erro na requisição"); })
      });

      $('#modalSenha').modal({
        dismissible: false
      });
      $('#modalSenha').modal('open');


    });

  </script>
  <script>

    $(document).ready(function () {
      var upperLimit = <?php $diretorio = scandir("../imagens/");
      $qtd = count($diretorio) - 2;
      echo ("$qtd"); ?>;;
      var randomNum = Math.floor((Math.random() * upperLimit) + 1);
      //$("body").css("background","url('../imagens/" + randomNum + ".jpg') no-repeat center center fixed");//<--changed path
      $("body").css("background", "url('../imagens/1a.jpg') no-repeat center center fixed");//<--changed path
      $("body").css("-webkit-background-size", "cover");
      $("body").css("-moz-background-size", "cover");
      $("body").css("-o-background-size", "cover");
      $("body").css("background-size", "cover");
      //$("body").css("-webkit-transition","background 4s ease-in-out");
      //$("body").css("-moz-transition","background 4s ease-in-out");
      //$("body").css("-o-transition","background 4s ease-in-out");
      //$("body").css("-ms-transition","background 4s ease-in-out");
      //$("body").css("transition","background 4s ease-in-out");
      $("body").css("-webkit-backface-visibility", "hidden");


    });


  </script>

</body>

</html>