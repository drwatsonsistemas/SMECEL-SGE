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
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema,  sec_id, sec_cidade, sec_uf 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
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

  <title>SMECEL - Sistema de Gestão Escolar</title>
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

      <h1 class="ls-title-intro ls-ico-search">Verificações Administrativas</h1>

      <?php if (isset($_GET['nada'])) { ?>
        <div class="ls-alert-danger ls-dismissable">
          <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
          OCORREU UM ERRO NA AÇÃO ANTERIOR. UM E-MAIL FOI ENVIADO AO ADMINISTRADOR DO SISTEMA.
        </div>
      <?php } ?>

      <div class="ls-box">

        <label class="ls-label col-md-12">
          <b class="ls-label-text">BUSQUE UM RELATÓRIO</b>
          <input type="text" class="buscar-relatorio" alt="fonte-tabela"
            placeholder="Digite o nome ou parte do nome de um tipo de relatório" autofocus />
        </label>



      </div>

<!-- 
      <table class="ls-table fonte-tabela">
        <thead>
          <tr>
            <th>FUNCIONÁRIOS</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table> -->




      <table class="ls-table fonte-tabela">
        <thead>
          <tr>
            <th>ALUNOS</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <a href="rel_alunos_sem_sexo.php" title="Alunos sem sexo definido no cadastro" target="_blank"> - Relação de
                alunos sem
                sexo definido no cadastro</a>
            </td>
          </tr>
          <tr>
            <td><a href="alunos_matriculas_duplicadas.php" title="" target="_blank">- Relação de alunos com matrículas
                duplicadas</a></td>
          </tr>
          <tr>
            <td><a href="rel_alunos_sem_cpf.php" title="" target="_blank">- Relação de alunos sem informação de CPF no
                cadastro</a></td>
          </tr>
          <tr>
            <td>
              <a href="rel_alunos_data_nascimento_incorreta.php" title="Alunos com data de nascimento incorreta ou não informada" target="_blank"> - Relação de alunos com data de nascimento incorreta ou não informada</a>
            </td>
          </tr>
          <tr>
            <td><a href="rel_alunos_sem_nis.php" title="" target="_blank">- Relação de alunos sem informação de NIS no
                cadastro</a></td>
          </tr>
        
        </tbody>
      </table>

      <hr>
      <br><br><br><br><br><br><br><br>


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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script type="text/javascript">
    $(function () {
      $(".buscar-relatorio").keyup(function () {
        //pega o css da tabela 
        var tabela = $(this).attr('alt');
        if ($(this).val() != "") {
          $("." + tabela + " tbody>tr").hide();
          $("." + tabela + " td:contains-ci('" + $(this).val() + "')").parent("tr").show();
        } else {
          $("." + tabela + " tbody>tr").show();
        }
      });
    });
    $.extend($.expr[":"], {
      "contains-ci": function (elem, i, match, array) {
        return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
      }
    });

    document.addEventListener("DOMContentLoaded", function () {
      document.getElementById("faltas-periodo").addEventListener("click", async function () {
        const { value } = await Swal.fire({
          title: "Selecione o período",
          html: `
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label for="start-date" style="width: 40%;">Data de Início:</label>
            <input type="date" id="start-date" class="swal2-input" style="width: 55%;" required>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label for="end-date" style="width: 40%;">Data de Fim:</label>
            <input type="date" id="end-date" class="swal2-input" style="width: 55%;" required>
          </div>
        </div>
      `,
          focusConfirm: false,
          showCancelButton: true,
          preConfirm: () => {
            const startDate = document.getElementById("start-date").value;
            const endDate = document.getElementById("end-date").value;
            if (!startDate || !endDate) {
              Swal.showValidationMessage("Preencha ambas as datas.");
              return false;
            }
            if (endDate < startDate) {
              Swal.showValidationMessage("A data final não pode ser menor que a inicial.");
              return false;
            }
            return { startDate, endDate };
          }
        });

        if (value) {
          window.open(`print_funcionarios_faltas_periodo.php?inicio=${value.startDate}&fim=${value.endDate}`, "_blank");
        }
      });
    });

  </script>

</body>

</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>