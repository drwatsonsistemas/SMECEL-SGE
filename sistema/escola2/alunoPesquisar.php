<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "../funcoes/funcoes.php"; ?>
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
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$entra = 0;

$inepBusca = "";
if (isset($_POST['inep'])) {
  $inepBusca = anti_injection($_POST['inep']);
  $entra = 1;
}

$cpfBusca = "";
if (isset($_POST['cpf'])) {
  $cpfBusca = anti_injection($_POST['cpf']);
  $entra = 1;
}

$nomeBusca = "";
if (isset($_POST['nome'])) {
  $nomeBusca = anti_injection($_POST['nome']);
  $entra = 1;
}

$nascimentoBusca = "";
if (isset($_POST['nascimento'])) {
  $nascimentoBusca = converteData($_POST['nascimento']);
  $entra = 1;
}

$maeBusca = "";
if (isset($_POST['mae'])) {
  $maeBusca = anti_injection($_POST['mae']);
  $entra = 1;
}

if ($inepBusca != "") {

  $inep_no_where = "AND aluno_cod_inep LIKE '%" . $inepBusca . "%'";
} else {
  $inep_no_where = "";
}

if ($cpfBusca != "") {
  $cpfBusca = trim($cpfBusca);
  $cpfBusca = str_replace(['.', '-'], '', $cpfBusca); // Removendo formatação do CPF fornecido
  $cpf_no_where = "AND REPLACE(REPLACE(aluno_cpf, '.', ''), '-', '') = REPLACE(REPLACE('$cpfBusca', '.', ''), '-', '')";
} else {
  $cpf_no_where = "";
}
if ($nomeBusca != "") {
  $nome_score = ", MATCH (aluno_nome) AGAINST ('" . $nomeBusca . "' IN BOOLEAN MODE) AS nome_score";
  $nome_no_where = "AND MATCH (aluno_nome) AGAINST ('" . $nomeBusca . "' IN BOOLEAN MODE)";
  $ordem = "ORDER BY nome_score DESC";
} else {
  $nome_score = "";
  $nome_no_where = "";
  $ordem = "ORDER BY aluno_nome ASC";
}

if ($nascimentoBusca != "") {
  $nascimento_no_where = "AND aluno_nascimento LIKE '" . $nascimentoBusca . "'";
} else {
  $nascimento_no_where = "";
}

if ($maeBusca != "") {
  $mae_no_where = "AND (MATCH (aluno_filiacao1) AGAINST ('" . $maeBusca . "' IN BOOLEAN MODE) OR MATCH (aluno_filiacao2) AGAINST ('" . $maeBusca . "' IN BOOLEAN MODE))";
} else {
  $mae_no_where = "";
}


if ($entra == 1) {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_buscaFuncionario = "
SELECT
aluno_id,
aluno_cod_inep,
aluno_cpf,
aluno_nome,
aluno_nascimento,
aluno_filiacao1,
aluno_filiacao2,
aluno_sexo,
aluno_raca,
aluno_nacionalidade,
aluno_uf_nascimento,
aluno_municipio_nascimento,
aluno_municipio_nascimento_ibge,
aluno_aluno_com_deficiencia,
aluno_nis,
aluno_identidade,
aluno_emissor,
aluno_uf_emissor,
aluno_data_espedicao,
aluno_tipo_certidao,
aluno_termo,
aluno_folhas,
aluno_livro,
aluno_emissao_certidao,
aluno_uf_cartorio,
aluno_mucicipio_cartorio,
aluno_nome_cartorio,
aluno_num_matricula_modelo_novo,
aluno_localizacao,
aluno_cep,
aluno_endereco,
aluno_numero,
aluno_complemento,
aluno_bairro,
aluno_uf,
aluno_municipio,
aluno_telefone,
aluno_celular,
aluno_email,
aluno_hash,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf
$nome_score
FROM 
  smc_aluno
LEFT JOIN smc_municipio 
ON 
municipio_cod_ibge = aluno_municipio_nascimento_ibge  
WHERE
aluno_id <> 0
  $nome_no_where 
  $mae_no_where 
  $nascimento_no_where 
  $inep_no_where 
  $cpf_no_where 
  $ordem
";
  $buscaFuncionario = mysql_query($query_buscaFuncionario, $SmecelNovo) or die(mysql_error());
  $row_buscaFuncionario = mysql_fetch_assoc($buscaFuncionario);
  $totalRows_buscaFuncionario = mysql_num_rows($buscaFuncionario);

}

if ((isset($_GET["periodo"])) && ($_GET["periodo"] == "rematricula")) {
  $link = "vinculoAlunoCadastrar.php?periodo=rematricula";
  $linkNome = "REMATRICULAR";
  $tituloRematricula = "<strong class='ls-color-warning'>REMATRÍCULA</strong>";
} else {
  $link = "vinculoAlunoCadastrar.php";
  $linkNome = "MATRICULAR";
  $tituloRematricula = "";
}

// Verifica se o link já contém o "?"
$paramChar = strpos($link, '?') === false ? '?' : '&';

$inepBusca = "";
if (isset($_POST['inep'])) {
  $inepBusca = anti_injection($_POST['inep']);
}

$cpfBusca = "";
if (isset($_POST['cpf'])) {
  $cpfBusca = anti_injection($_POST['cpf']);
}


$nomeBusca = "";
if (isset($_POST['nome'])) {
  $nomeBusca = anti_injection($_POST['nome']);
}

$nascimentoBusca = "";
if (isset($_POST['nascimento'])) {
  $nascimentoBusca = $_POST['nascimento'];
}

$maeBusca = "";
if (isset($_POST['mae'])) {
  $maeBusca = anti_injection($_POST['mae']);
}

?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-117872281-1');
  </script>

  <title>Pesquisar Alunos</title>

  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">
  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>

  <main class="ls-main ">
    <div class="container-fluid">

      <h1 class="ls-title-intro ls-ico-users">Pesquisar aluno <?php echo $tituloRematricula; ?></h1>


      <?php if (isset($_GET["nada"])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          <strong>Informações inválidas.</strong> A equipe de suporte já foi comunicada do erro.
        </div>
      <?php } ?>

      <form action="" method="post" class="ls-form ls-form-horizontal row ls-box">
        <fieldset>

          <label class="ls-label col-md-6">
            <b class="ls-label-text">CPF DO ALUNO</b>
            <input value="<?php echo $cpfBusca; ?>" type="text" class="ls-field-sm cpf" name="cpf" id="cpf"
              onblur="javascript: validarCPF(this);" onkeypress="javascript: mascara(this, cpf_mask);"
              placeholder="CPF do aluno">
          </label>

          <label class="ls-label col-md-6">
            <b class="ls-label-text">INEP</b>
            <input value="<?php echo $inepBusca; ?>" type="text" class="ls-field-sm inep" name="inep" id="inep"
              placeholder="Código INEP">
          </label>

          <label class="ls-label col-md-12">
            <p>Preencha um dos dois campos acima, ou então os três campos abaixo:</p>
          </label>

          <label class="ls-label col-md-5">
            <b class="ls-label-text">NOME DO ALUNO</b>
            <input value="<?php echo $nomeBusca; ?>" type="text" class="ls-field-sm" name="nome" id="nome"
              pattern=".{4,}" title="Informe no mínimo 4 caracteres" placeholder="Nome completo ou parte" required>
          </label>

          <label class="ls-label col-md-2">
            <b class="ls-label-text">NASCIMENTO</b>
            <input value="<?php echo $nascimentoBusca; ?>" type="text" class="ls-field-sm date" name="nascimento"
              id="nascimento" placeholder="Data de nascimento" required>
          </label>

          <label class="ls-label col-md-5">
            <b class="ls-label-text">NOME DA FILIAÇÃO 1 OU FILIAÇÃO 2</b>
            <input value="<?php echo $maeBusca; ?>" type="text" class="ls-field-sm" name="mae" id="mae"
              placeholder="Nome da filiação 1 ou filiação 2" required>
          </label>

        </fieldset>


        <div class="ls-actions-btn">
          <button class="ls-btn-primary ls-btn-sm ls-ico-search">Pesquisar</button>
          <a href="alunoPesquisar.php" class="ls-btn-danger ls-btn-sm">Limpar</a>
        </div>
      </form>




      <?php if (($nomeBusca == "") and ($nascimentoBusca == "") and ($maeBusca == "") and ($cpfBusca == "") and ($inepBusca == "")) { ?>
        <div class="ls-alert-info1 ls-color-success"><strong>
            <span class="ls-ico-lamp ls-ico-left"></span> Atenção: </strong>
          Informe os campos acima e clique em pesquisar. Você pode informar apenas o primeiro nome em cada campo.
          <br><br>
          <strong>Dicas para encontrar um aluno já cadastrado:</strong><br>
          - Evite duplicidade de cadastros. Tente fazer uma busca detalhada do aluno; <br>
          - Procure o aluno informando apenas o CPF ou então o código INEP;<br>
          - Se preencher um dos campos informados na dica acima, os demais campos serão desabilitados na pesquisa;<br>
          - Para fazer a busca pelo nome do aluno, será necessário informar a data de nascimento e a filiação (nome do pai
          ou da mãe) do aluno;<br>
          - Você poderá informar apenas o primeiro nome do aluno e da filiação. Ex.: JOÃO PEREIRA DOS SANTOS NETO (pode
          ser informado apenas JOAO);<br>
          - É possível informar o primeiro nome da filiação, como também o sobrenome. Ex.: No caso da mãe do aluno se
          chamar MARIA DE JESUS SANTOS, você poderá digitar apenas MARIA, ou então SANTOS;<br>
          - Caso nenhum aluno seja encontrado após uma cuidadosa pesquisa, clique no botão "Cadastrar aluno", logo abaixo
          do campo de resultado da pesquisa;

        </div>
      <?php } else { ?>
        <?php if ($totalRows_buscaFuncionario > 0) { // Show if recordset not empty ?>
          <div class="ls-alert-info ls-dismissable">
            <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
            Foram encontrados <?php echo $totalRows_buscaFuncionario; ?> registros com os dados informados para esta
            pesquisa. Confira se todos os campos coincidem com os dados do(a) aluno(a).
          </div>
          <table class="ls-table ls-table-striped ls-sm-space">
            <thead>
              <tr>
                <th width="70px" class="ls-txt-center"></th>
                <th>RESULTADO</th>
                <th width="200px" class="ls-txt-center">VINCULAR</th>
              </tr>
            </thead>
            <tbody>

              <?php $num = "1";
              do { ?>


                <?php
                mysql_select_db($database_SmecelNovo, $SmecelNovo);
                $query_vinculos = "
          SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_ano_letivo FROM smc_vinculo_aluno
          WHERE vinculo_aluno_id_aluno = '$row_buscaFuncionario[aluno_id]'";
                $vinculos = mysql_query($query_vinculos, $SmecelNovo) or die(mysql_error());
                $row_vinculos = mysql_fetch_assoc($vinculos);
                $totalRows_vinculos = mysql_num_rows($vinculos);
                ?>


                <tr>
                  <td class="ls-txt-center"><?php echo $num;
                  $num++; ?> </td>

                  <td>
                    <p><strong>INEP:</strong> <?php echo $row_buscaFuncionario['aluno_cod_inep']; ?> </p>
                    <p><strong>ID ALUNO(A):</strong> <?php echo $row_buscaFuncionario['aluno_id']; ?> </p>
                    <p><strong>ALUNO(A):</strong> <?php echo $row_buscaFuncionario['aluno_nome']; ?>
                      <?php if ($row_buscaFuncionario['nome_score'] > "1.0") {
                        echo "<a href='#' class='ls-tag-success'>PROVÁVEL</a>";
                      } ?>
                    </p>
                    <p><strong>CPF:</strong> <?php echo $row_buscaFuncionario['aluno_cpf']; ?></p>
                    <p><strong>NASCIMENTO:</strong> <?php echo converteData($row_buscaFuncionario['aluno_nascimento']); ?></p>
                    <p><strong>FILIAÇÃO 1: </strong><?php echo $row_buscaFuncionario['aluno_filiacao1']; ?></p>
                    <p><strong>FILIAÇÃO 2: </strong><?php echo $row_buscaFuncionario['aluno_filiacao2']; ?></p>
                    <p><strong>MUNICÍPIO DE NASCIMENTO: </strong><?php echo $row_buscaFuncionario['municipio_nome']; ?> -
                      <?php echo $row_buscaFuncionario['municipio_sigla_uf']; ?>
                    </p>

                    <p>
                      <?php if ($totalRows_vinculos > 0) { ?>
                      <div class="ls-alert-info ls-dismissable">
                        Vínculos encontrados:
                        <?php do { ?>
                          <a href='#' class='ls-tag-info'><?php echo $row_vinculos['vinculo_aluno_ano_letivo']; ?></a>
                        <?php } while ($row_vinculos = mysql_fetch_assoc($vinculos)); ?>
                      </div>
                    <?php } else { ?>
                      <div class="ls-alert-warning">
                        Não foram encontrados vínculos para este cadastro
                      </div>
                    <?php } ?>
                    </p>

                  </td>
                  <td class="ls-txt-center">
                    <a href="<?php echo $link . $paramChar; ?>c=<?php echo $row_buscaFuncionario['aluno_hash']; ?>"
                      class="ls-ico-user-add ls-btn-primary">
                      <?php echo $linkNome; ?>
                    </a>
                  </td>
                </tr>
              <?php } while ($row_buscaFuncionario = mysql_fetch_assoc($buscaFuncionario)); ?>
            </tbody>
          </table>

          <p>
            <a class="ls-btn-primary ls-ico-windows ls-float-right" href="alunoCadastrar.php">Cadastrar aluno</a>
          </p>
          <hr>
        <?php } else { ?>
          <div class="ls-alert-warning">
            Nenhum resultado. <a class="ls-float-right" href="alunoCadastrar.php">Cadastrar aluno</a>
          </div>
          <hr>
          <p>
            <a class="ls-btn-primary ls-btn-lg ls-ico-windows  ls-float-right" href="alunoCadastrar.php">Cadastrar aluno</a>
          </p>
          <hr>
          <hr>
        <?php } ?>

      <?php } // Show if recordset not empty ?>

      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>

    </div>
  </main>


  <span data-ls-module="modal" data-target="#pesquiseAntes"></span>

  <div class="ls-modal" id="pesquiseAntes">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">Cadastro de Aluno</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <p><strong>Atenção</strong></p>
        <p>Antes de realizar um novo cadastro, faça uma pesquisa para saber se já existe um registro do aluno.</p>
      </div>
      <div class="ls-modal-footer">
        <a href="alunoPesquisar.php" class="ls-btn ls-float-right" data-dismiss="modal">Fechar</a>
        <a href="alunoPesquisar.php" class="ls-btn-primary" data-dismiss="modal">Pesquisar</a>
      </div>
    </div>
  </div><!-- /.modal -->



  <div class="ls-modal" id="dicas">
    <div class="ls-modal-box">
      <div class="ls-modal-header">
        <button data-dismiss="modal">&times;</button>
        <h4 class="ls-modal-title">Dicas para pesquissr um aluno</h4>
      </div>
      <div class="ls-modal-body" id="myModalBody">
        <p>




          <strong>Dicas para encontrar um aluno já cadastrado:</strong><br>
          - Evite duplicidade de cadastros. Tente fazer uma busca detalhada do aluno; <br>
          - Procure o aluno informando apenas o CPF ou então o código INEP;<br>
          - Se preencher um dos campos informados na dica acima, os demais campos serão desabilitados na pesquisa;<br>
          - Para fazer a busca pelo nome do aluno, será necessário informar a data de nascimento e a filiação (nome do
          pai ou da mãe) do aluno;<br>
          - Você poderá informar apenas o primeiro nome do aluno. Ex.: JOÃO PEREIRA DOS SANTOS NETO (pode ser informado
          apenas JOAO);<br>
          - É possível informar o primeiro nome da filiação, como também o sobrenome. Ex.: No caso da mãe do aluno se
          chamar MARIA DE JESUS SANTOS, você poderá digitar apenas MARIA, ou então SANTOS;<br>
          - Caso nenhum aluno seja encontrado após uma cuidadosa pesquisa, clique no botão "Cadastrar aluno", logo
          abaixo do campo de resultado da pesquisa;



        </p>
      </div>
      <div class="ls-modal-footer">

        <p><a class="ls-btn ls-float-right" data-dismiss="modal">FECHAR</a></p>
        <br><br>

      </div>
    </div>
  </div><!-- /.modal -->



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

  <script src="../../js/jquery.mask.js"></script>
  <script src="js/mascara.js"></script>
  <script src="js/validarCPF.js"></script>

  <script>
    jQuery(document).ready(function ($) {
      // Chamada da funcao upperText(); ao carregar a pagina
      upperText();
      // Funcao que faz o texto ficar em uppercase
      function upperText() {
        // Para tratar o colar
        $("input").bind('paste', function (e) {
          var el = $(this);
          setTimeout(function () {
            var text = $(el).val();
            el.val(text.toUpperCase());
          }, 100);
        });

        // Para tratar quando é digitado
        $("input").keypress(function () {
          var el = $(this);
          setTimeout(function () {
            var text = $(el).val();
            el.val(text.toUpperCase());
          }, 100);
        });
      }
    });
  </script>

  <script language="javascript">
    function noTilde(objResp) {
      var varString = new String(objResp.value);
      var stringAcentos = new String('àâêôûãõáéíóúçüÀÂÊÔÛÃÕÁÉÍÓÚÇÜ[]');
      var stringSemAcento = new String('aaeouaoaeioucuAAEOUAOAEIOUCU');

      var i = new Number();
      var j = new Number();
      var cString = new String();
      var varRes = "";

      for (i = 0; i < varString.length; i++) {
        cString = varString.substring(i, i + 1);
        for (j = 0; j < stringAcentos.length; j++) {
          if (stringAcentos.substring(j, j + 1) == cString) {
            cString = stringSemAcento.substring(j, j + 1);
          }
        }
        varRes += cString;
      }
      objResp.value = varRes;
    }
    $(function () {
      $("input:text").keyup(function () {
        noTilde(this);
      });
    });
  </script>




  <script>

    $(document).ready(function () {
      $("#inep").blur(function () {
        if ($("#inep").val() != '') {
          $('#cpf').attr("disabled", true);
          $('#nome').attr("disabled", true);
          $('#nascimento').attr("disabled", true);
          $('#mae').attr("disabled", true);
          $('#nome').attr("required", true);
          $('#nascimento').attr("required", true);
          $('#mae').attr("required", true);
        } else {
          $('#cpf').attr("disabled", false);
          $('#nome').attr("disabled", false);
          $('#nascimento').attr("disabled", false);
          $('#mae').attr("disabled", false);
          $('#nome').attr("required", false);
          $('#nascimento').attr("required", false);
          $('#mae').attr("required", false);
        }
      });
      $("#cpf").blur(function () {
        if ($("#cpf").val() != '') {
          $('#inep').attr("disabled", true);
          $('#nome').attr("disabled", true);
          $('#nascimento').attr("disabled", true);
          $('#mae').attr("disabled", true);
          $('#nome').attr("required", true);
          $('#nascimento').attr("required", true);
          $('#mae').attr("required", true);
        } else {
          $('#inep').attr("disabled", false);
          $('#nome').attr("disabled", false);
          $('#nascimento').attr("disabled", false);
          $('#mae').attr("disabled", false);
          $('#nome').attr("required", false);
          $('#nascimento').attr("required", false);
          $('#mae').attr("required", false);
        }
      });
    });

  </script>



</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);

mysql_free_result($buscaFuncionario)
?>