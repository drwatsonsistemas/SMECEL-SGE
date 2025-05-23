<?php require_once('../Connections/SmecelNovo.php'); ?>
<?php include('../sistema/funcoes/inverteData.php'); ?>
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

if (isset($_POST['codigo'])) {


  //FALTA TRATAR CONTRA SQL-INJECTION

  function anti_injection($sql)
  {
    $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
    $sql = trim($sql);
    $sql = strip_tags($sql);
    $sql = (get_magic_quotes_gpc()) ? $sql : addslashes($sql);
    $sql = str_replace(" ", "", $sql);

    return $sql;
  }

  $loginUsername = anti_injection($_POST['codigo']);
  $password = anti_injection($_POST['senha']);
  $nascimento = inverteData($_POST['nascimento']);
  $MM_fldUserAuthorization = "aluno_usu_tipo";
  $MM_redirectLoginSuccess = "painel/index.php?bemvindo";
  $MM_redirectLoginFailed = "index.php?loginErr";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_SmecelNovo, $SmecelNovo);

  //$LoginRS__query="SELECT aluno_id, aluno_hash, aluno_nascimento, aluno_usu_tipo FROM smc_aluno WHERE aluno_nascimento='$nascimento' AND aluno_id='$loginUsername' AND aluno_hash LIKE '$password%'";
  $LoginRS__query = "SELECT aluno_id, aluno_hash, aluno_nascimento, aluno_usu_tipo FROM smc_aluno WHERE aluno_nascimento='$nascimento' AND aluno_id='$loginUsername'";




  $LoginRS = mysql_query($LoginRS__query, $SmecelNovo) or die(mysql_error());
  $loginRow = mysql_fetch_assoc($LoginRS);
  $loginFoundUser = mysql_num_rows($LoginRS);

  $senha = substr($loginRow['aluno_hash'], 0, 5);

  if ($senha <> $password) {
    header("Location: " . $MM_redirectLoginFailed);
    exit;
  }


  if ($loginFoundUser) {
    //$loginStrGroup = "";

    $loginStrGroup = mysql_result($LoginRS, 0, 'aluno_usu_tipo');

    if (PHP_VERSION >= 5.1) {
      session_regenerate_id(true);
    } else {
      session_regenerate_id();
    }
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
    }


    //REGISTRA Login
    date_default_timezone_set('America/Bahia');
    $dat = date('Y-m-d H:i:s');

    function getUserIP()
    {
      $client = @$_SERVER['HTTP_CLIENT_IP'];
      $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
      $remote = $_SERVER['REMOTE_ADDR'];

      if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
      } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
      } else {
        $ip = $remote;
      }

      return $ip;
    }


    $ip = getUserIP();






    // LOGIN

    $colname_AlunoLogado = "-1";
    if (isset($_SESSION['MM_Username'])) {
      $colname_AlunoLogado = $_SESSION['MM_Username'];
    }
    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, 
aluno_filiacao1, aluno_filiacao2, 
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo, 
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDÍGENA'
WHEN 6 THEN 'NÃO DECLARADA'
END AS aluno_raca, 
CASE aluno_nacionalidade
WHEN 1 THEN 'BRASILEIRA'
WHEN 2 THEN 'BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO'
WHEN 3 THEN 'EXTRANGEIRO'
END AS aluno_nacionalidade, 
aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge,  
CASE aluno_aluno_com_deficiencia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_aluno_com_deficiencia, 
aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, 
CASE aluno_tipo_certidao
WHEN 1 THEN 'MODELO ANTIGO'
WHEN 2 THEN 'MODELO NOVO'
END AS aluno_tipo_certidao, 
aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, 
aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao, 
aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, 
aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, 
CASE aluno_laudo
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_laudo, 
CASE aluno_alergia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_alergia, 
aluno_alergia_qual, 
CASE aluno_destro
WHEN 1 THEN 'DESTRO'
WHEN 2 THEN 'CANHOTO'
END AS aluno_destro, 
aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
aluno_prof_mae, aluno_tel_mae, 
CASE aluno_escolaridade_mae
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_mae, 
aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, 
CASE aluno_escolaridade_pai
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_pai, 
aluno_rg_pai, aluno_cpf_pai, aluno_hash, 
CASE aluno_recebe_bolsa_familia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_recebe_bolsa_familia,
aluno_foto,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf 
FROM smc_aluno
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
    $AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
    $row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
    $totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);
    if ($totalRows_AlunoLogado == "") {
      header("Location:../index.php?loginErr");
    }


    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $query_Matricula = "
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, 
vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, 
vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, 
vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, escola_id, escola_nome,
turma_id, turma_nome, turma_turno,
CASE turma_turno
WHEN 0 THEN 'INTEGRAL'
WHEN 1 THEN 'MATUTINO'
WHEN 2 THEN 'VESPERTINO'
WHEN 3 THEN 'NOTURNO'
END AS turma_turno_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
WHERE vinculo_aluno_id_aluno = '$row_AlunoLogado[aluno_id]' ORDER BY vinculo_aluno_id DESC LIMIT 0,1";
    $Matricula = mysql_query($query_Matricula, $SmecelNovo) or die(mysql_error());
    $row_Matricula = mysql_fetch_assoc($Matricula);
    $totalRows_Matricula = mysql_num_rows($Matricula);

    // LOGIN








    $sql = "INSERT INTO smc_login_aluno (login_aluno_id_aluno, login_aluno_data_hora, login_aluno_ano) VALUES ('$loginUsername', '$dat', '$row_Matricula[vinculo_aluno_ano_letivo]')";

    mysql_select_db($database_SmecelNovo, $SmecelNovo);
    $Result1 = mysql_query($sql, $SmecelNovo) or die(mysql_error());


    header("Location: " . $MM_redirectLoginSuccess);
  } else {
    header("Location: " . $MM_redirectLoginFailed);
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
  <title>Painel do Aluno - SMECEL</title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="../css/animate.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="css/app.css" media="screen,projection" />
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


      <div class="container fadeInDown wow" data-wow-delay="0s" data-wow-duration="0.5s">
        <div class="z-depth-1 grey lighten-4 row"
          style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <?php if (isset($_GET["saiu"])) { ?>
            <p class="green-text"><i class="medium material-icons">sentiment_very_satisfied</i><br>
              Até logo!</p>
          <?php } ?>
          <?php if (isset($_GET["loginErr"])) { ?>
            <p class="red-text"><i class="medium material-icons">sentiment_very_dissatisfied</i><br>
              Os dados inseridos não conferem!</p>
          <?php } ?>



          <p>
            <img src="../img/logo_smecel_card.png" width="350px">
          </p>

          <br>

          <p class="indigo-text1"><i class="material-icons">face</i><br> PAINEL DO ALUNO</p>


          <form id="loginForm" class="col s12 box" style="width:350px; height:360px;">
            <div class="input-field col s12">
              <input class="validate date" id="nascimento" name="nascimento" type="text" inputmode="numeric" required
                autofocus>
              <label for="nascimento">Data de nascimento do aluno</label>
            </div>

            <div class="input-field col s12">
              <input class="validate" id="codigo" name="codigo" type="text" inputmode="numeric" required>
              <label for="codigo">Código</label>
            </div>

            <div class="input-field col s12">
              <input class="validate" minlength="5" id="senha" name="senha" type="password" required>
              <label for="senha">Senha</label>
            </div>

            <center>
              <div class="row">
                <button id="loginButton" type="submit" class="btn waves-effect indigo">ENTRAR</button>
                <a class="btn btn-flat waves-effect" href="../">Voltar</a>
              </div>
            </center>

            <div id="errorMessage" class="card-panel red lighten-5" style="display: none;">
              <p class="red-text">
                <span id="errorText">{{texto}}</span>
              </p>
            </div>
            <div id="successMessage" class="card-panel green lighten-5" style="display: none;">
              <p class="green-text">
                <span id="successText"></span>
              </p>
            </div>
          
          </form>
          

        </div>
      </div>

    </center>
  </main>


  <!--JavaScript at end of body for optimized loading-->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="js/materialize.min.js"></script>
  <script type="text/javascript" src="../js/wow.min.js"></script>
  <script type="text/javascript" src="../js/pace.min.js"></script>

  <script>
    $(document).ready(function () {
        $('#loginForm').on('submit', function (event) {
            event.preventDefault(); // Evita o comportamento padrão do formulário

            // Captura os dados do formulário
            const formData = {
                codigo: $('#codigo').val(),
                senha: $('#senha').val(),
                nascimento: $('#nascimento').val()
            };

            // Desabilita o botão imediatamente ao clicar
            $('#loginButton').text('AGUARDE').prop('disabled', true);

            // Faz a requisição AJAX
            $.ajax({
                url: 'funcoes/processa_login.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Exibe uma mensagem de sucesso
                        $('#successText').text('Login realizado com sucesso! Redirecionando...');
                        $('#successMessage').show();

                        // Adiciona um atraso de 3 segundos antes do redirecionamento
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 3000);
                    } else {
                        // Exibe mensagem de erro
                        $('#errorText').text(response.message);
                        $('#errorMessage').show();

                        // Reabilita o botão após 3 segundos
                        setTimeout(function () {
                            $('#loginButton').text('ENTRAR').prop('disabled', false);
                            $('#errorMessage').hide();
                        }, 3000);
                    }
                },
                error: function (xhr, status, error) {
                    // Exibe mensagem de erro genérica
                    $('#errorText').text('Erro no servidor. Por favor, tente novamente mais tarde.');
                    $('#errorMessage').show();

                    // Reabilita o botão após 3 segundos
                    setTimeout(function () {
                        $('#loginButton').text('ENTRAR').prop('disabled', false);
                        $('#errorMessage').hide();
                    }, 3000);

                    console.error('Erro no servidor:', xhr.responseText);
                }
            });
        });
    });
</script>

  <script>
    new WOW().init();
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
      //$("body").css("-webkit-transition","background 8s ease-in-out");
      //$("body").css("-moz-transition","background 8s ease-in-out");
      //$("body").css("-o-transition","background 8s ease-in-out");
      //$("body").css("-ms-transition","background 8s ease-in-out");
      //$("body").css("transition","background 8s ease-in-out");
      $("body").css("-webkit-backface-visibility", "hidden");

    });


  </script>
  <script type="text/javascript" src="../js/jquery.mask.min.js"></script>
  <script type="text/javascript" src="../js/mascara.js"></script>

</body>

</html>