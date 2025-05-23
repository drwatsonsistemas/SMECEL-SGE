<?php require_once ('../../Connections/SmecelNovo.php'); ?>
<?php // include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
<?php
if (!function_exists('GetSQLValueString')) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '')
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case 'text':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'long':
      case 'int':
        $theValue = ($theValue != '') ? intval($theValue) : 'NULL';
        break;
      case 'double':
        $theValue = ($theValue != '') ? doubleval($theValue) : 'NULL';
        break;
      case 'date':
        $theValue = ($theValue != '') ? "'" . $theValue . "'" : 'NULL';
        break;
      case 'defined':
        $theValue = ($theValue != '') ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

include 'usuLogado.php';
include 'fnc/anoLetivo.php';

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die (mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);

$colname_funcionario = '-1';
if (isset($_GET['codigo'])) {
  $colname_funcionario = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_funcionario = sprintf("
SELECT func_id, func_usu_tipo, func_id_sec, func_nome, func_mae, func_pai, func_data_nascimento, func_uf_nascimento, func_municipio_nascimento, func_estado_civil, func_sexo, func_escolaridade, func_cpf, func_rg_numero, func_rg_emissor, 
func_titulo, func_titulo_secao, func_titulo_zona, func_pis, func_cnh_num, func_categoria, func_ctps, func_ctps_serie, func_reservista, func_endereco, func_endereco_numero, func_endereco_bairro, 
func_endereco_cep, func_endereco_uf, func_endereco_cidade, func_matricula, func_admissao, func_decreto, func_lotacao, func_cargo, 
func_regime, func_grupo_sanquineo, func_fator_rh, func_email, func_telefone, func_celular1, func_celular2, func_agencia_banco, func_conta_banco, 
func_nome_banco, func_area_concurso, func_formacao, func_situacao, func_foto, func_senha, func_senha_ativa, func_carga_horaria_semanal, func_vacina_covid19, funcao_id, funcao_nome,
vinculo_id, vinculo_id_funcionario, vinculo_status,id_regime,regime_nome
FROM smc_func
INNER JOIN smc_vinculo
ON func_id = vinculo_id_funcionario 
INNER JOIN smc_funcao ON funcao_id = func_cargo
LEFT JOIN smc_regime ON func_regime = id_regime
WHERE vinculo_status = 1 AND func_id_sec = '$row_EscolaLogada[escola_id_sec]' AND vinculo_id = %s", GetSQLValueString($colname_funcionario, 'int'));
$funcionario = mysql_query($query_funcionario, $SmecelNovo) or die (mysql_error());
$row_funcionario = mysql_fetch_assoc($funcionario);
$totalRows_funcionario = mysql_num_rows($funcionario);

if ($totalRows_funcionario == 0) {
  header('Location: index.php?err');
  exit;
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_logins = "SELECT login_professor_id, login_professor_id_professor, login_professor_data_hora FROM smc_login_professor WHERE login_professor_id_professor = '$row_funcionario[func_id]' ORDER BY login_professor_id DESC";
$logins = mysql_query($query_logins, $SmecelNovo) or die (mysql_error());
$row_logins = mysql_fetch_assoc($logins);
$totalRows_logins = mysql_num_rows($logins);

$colname_Cursos = '-1';
if (isset($_GET['codigo'])) {
  $colname_Cursos = $_GET['codigo'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Cursos = sprintf('SELECT curso_id, curso_id_funcionario, curso_descricao, curso_instituicao, curso_dt_inicio, curso_dt_final, curso_ch, curso_observacao, curso_recebe FROM smc_curso WHERE curso_id_funcionario = %s', GetSQLValueString($row_funcionario['func_id'], 'int'));
$Cursos = mysql_query($query_Cursos, $SmecelNovo) or die (mysql_error());
$row_Cursos = mysql_fetch_assoc($Cursos);
$totalRows_Cursos = mysql_num_rows($Cursos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_formacao = "
SELECT titulacao_id, titulacao_func_id, titulacao_tipo, titulacao_id_formacao, titulacao_horas, titulacao_data_inicio, titulacao_data_final, titulacao_data_entrega, titulacao_observacao, formacao_id, formacao_nome,
CASE titulacao_tipo
WHEN 1 THEN 'GRADUAÇÃO'
WHEN 2 THEN 'PÓS-GRADUAÇÃO'
WHEN 3 THEN 'MESTRADO'
WHEN 4 THEN 'DOUTORADO'
END AS titulacao_tipo 
FROM smc_titulacao 
INNER JOIN smc_formacao ON formacao_id = titulacao_id_formacao
WHERE titulacao_func_id = $row_funcionario[func_id]";
$formacao = mysql_query($query_formacao, $SmecelNovo) or die (mysql_error());
$row_formacao = mysql_fetch_assoc($formacao);
$totalRows_formacao = mysql_num_rows($formacao);

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
    .custom-center {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
</style>

</head>
  <body>
    <?php include_once ('menu-top.php'); ?>
          <?php include_once ('menu-esc.php'); ?>


    <main class="ls-main ">
      <div class="container-fluid">
 
        <h1 class="ls-title-intro ls-ico-home">Detalhes do servidor</h1>
		<!-- CONTEÚDO -->
        
        <p><a href="funcListar.php" class="ls-btn ls-ico-chevron-left">Voltar</a> 
        <a href="funcEditarVinculo.php?c=<?= $row_funcionario['vinculo_id'] ?>" class="ls-btn ls-ico-edit-admin">Editar vínculo</a>
        <a href="frequenciaFuncionarios.php?c=<?php echo $row_funcionario['vinculo_id']; ?>" class="ls-btn ls-ico-calendar" target="_blank">Folha de Frequência</a>
      </p>
        <div class="ls-box ls-lg-space ls-ico-bg">
       
       	  <div class="col-md-1 col-xs-3">	
				<?php if ($row_funcionario['func_foto'] <> '') { ?>
                <img src="../../professor/fotos/<?php echo $row_funcionario['func_foto']; ?>" alt="" width="100px"  /> 
                <?php } else { ?>
                <img src="../../img/no-photo-user.jpg" alt="" /> 
                <?php } ?>
          </div>

       	  <div class="col-md-11 col-xs-9">	
          <h1 class="ls-title-1 ls-color-theme"><?php echo $row_funcionario['func_nome']; ?></h1>
          <p>
          Nascimento: <strong><?php if ($row_funcionario['func_data_nascimento'] == '') { ?>-<?php } else { ?><?php echo date('d/m/Y', strtotime($row_funcionario['func_data_nascimento'])); ?><?php } ?></strong><br>
          CPF: <strong><?php echo $row_funcionario['func_cpf']; ?></strong><br>
          Cargo/Função: <strong><?php echo $row_funcionario['funcao_nome']; ?></strong><br>
          Regime: <strong><?php echo $row_funcionario['regime_nome']; ?></strong><br>
          Admissão: <strong><?php if ($row_funcionario['func_admissao'] == '') { ?>-<?php } else { ?><?php echo date('d/m/Y', strtotime($row_funcionario['func_admissao'])); ?><?php } ?></strong>
          </p>
          <?php if (isset($_GET['editado'])) { ?>
          <div class="ls-alert-success">Vínculo editado com sucesso!</div>
          <?php } ?>
          </div>


        </div>


<!-- <button data-ls-module="modal" data-target="#requerimentoFerias" class="ls-btn" target="_blank">REQUERIMENTO DE FÉRIAS</button>
<a href="requerimentoDireitos.php?c=<?php echo $row_funcionario['func_id']; ?>" class="ls-btn" target="_blank">FICHA DE REQUERIMENTO</a>		
-->

<ul class="ls-tabs-nav">
  <li class="ls-active"><a data-ls-module="tabs" href="#documentos">DOCUMENTOS</a></li>
  <li class=""><a data-ls-module="tabs" href="#requerimentos">REQUERIMENTOS</a></li>
  <li class=""><a data-ls-module="tabs" href="#declaracoes">DECLARAÇÕES</a></li>
  <li><a data-ls-module="tabs" href="#registros">REGISTROS</a></li>
  <li><a data-ls-module="tabs" href="#gerenciamentos">GERENCIAMENTOS</a></li>
  <li class=""><a data-ls-module="tabs" href="#formacao">FORMAÇÃO ACADÊMICA</a></li>
  <li class=""><a data-ls-module="tabs" href="#cursos">CURSOS</a></li>
</ul>
<div class="ls-tabs-container">
  <div id="requerimentos" class="ls-tab-content ">
    <div class="ls-tabs-btn">
      <ul class="ls-tabs-btn-nav">
        <li class="col-md-6 col-xs-6"><button data-ls-module="modal" data-target="#requerimentoFerias" class="ls-btn" target="_blank">REQUERIMENTO DE FÉRIAS</button></li>
        <li class="col-md-6 col-xs-6"><a href="requerimentoDireitos.php?c=<?php echo $row_funcionario['func_id']; ?>" class="ls-btn" target="_blank">FICHA DE REQUERIMENTO</a></li>
        <li class="col-md-6 col-xs-6"><button data-ls-module="modal" data-target="#licencaPremio" class="ls-btn" target="_blank">SOLICITAÇÃO DE LICENÇA PRÊMIO</button></li>
        <li class="col-md-6 col-xs-6"><button data-ls-module="modal" data-target="#licencaPremioPecunia" class="ls-btn" target="_blank">SOLICITAÇÃO DE PECÚNIA</button></li></ul>
    </div>
  </div>
  <div id="declaracoes" class="ls-tab-content ">
    <div class="ls-tabs-btn">
      <ul class="ls-tabs-btn-nav">
      <li class="col-md-6 col-xs-6"><a class="ls-btn" href="declaracaoVinculo.php?cod=<?php echo $row_funcionario['vinculo_id']; ?>" target="_blank">DECLARAÇÃO DE VÍNCULO</a></li>
      </div>
  </div>
  <div id="documentos" class="ls-tab-content ls-active">
  <div class="ls-tabs-btn">
  <table class="ls-table">
  <tbody>
    <tr>
      <td><small>CPF</small><br><strong><?php echo $row_funcionario['func_cpf']; ?></strong></td>
      <td><small>RG</small><br><strong><?php echo $row_funcionario['func_rg_numero']; ?> <?php echo $row_funcionario['func_rg_emissor']; ?></strong></td>
      <td><small>TÍTULO DE ELEITOR</small><br><strong><?php echo $row_funcionario['func_titulo']; ?> <?php echo $row_funcionario['func_titulo_secao']; ?> <?php echo $row_funcionario['func_titulo_zona']; ?></strong></td>
      <td><small>PIS</small><br><strong><?php echo $row_funcionario['func_pis']; ?></strong></td>
    </tr>

    <tr>
      <td><small>CNH</small><br><strong></strong></td>
      <td><small>CTPS</small><br><strong><?php echo $row_funcionario['func_ctps']; ?> <?php echo $row_funcionario['func_ctps_serie']; ?></strong></td>
      <td><small>RESERVISTA</small><br><strong><?php echo $row_funcionario['func_reservista']; ?></strong></td>
      <td></strong></td>
    </tr>

    <tr>
      <td><small>ENDEREÇO</small><br><strong><?php echo $row_funcionario['func_endereco']; ?> <?php echo $row_funcionario['func_endereco_numero']; ?> <?php echo $row_funcionario['func_endereco_bairro']; ?></strong></td>
      <td><small>CEP</small><br><strong><?php echo $row_funcionario['func_endereco_cep']; ?></strong></td>
      <td></strong></td>
      <td></strong></td>
    </tr>

    <tr>
      <td><small>MATRÍCULA</small><br><strong><?php echo $row_funcionario['func_matricula']; ?></strong></td>
      <td><small>ADMISSÃO</small><br><strong><?php if ($row_funcionario['func_admissao'] == '') { ?>-<?php } else { ?><?php echo date('d/m/Y', strtotime($row_funcionario['func_admissao'])); ?><?php } ?></strong></td>
      <td></strong></td>
      <td></strong></td>
    </tr>

    <tr>
      <td><small>EMAIL</small><br><strong><?php echo $row_funcionario['func_email']; ?></strong></td>
      <td><small>TELEFONE</small><br><strong><?php echo $row_funcionario['func_telefone']; ?></strong></td>
      <td><small>CELULAR</small><br><strong><?php echo $row_funcionario['func_celular1']; ?></strong></td>
      <td><small>CELULAR</small><br><strong><?php echo $row_funcionario['func_celular2']; ?></strong></td>
    </tr>

    <tr>
      <td><small>LOGINS NO PAINEL DO PROFESSOR</small><br><strong><?php echo $totalRows_logins ?> logins realizados</strong></td>
      <td><?php if ($totalRows_logins > 0) { ?><a class="ls-btn ls-float-right" data-ls-module="collapse" data-target="#0">Ver logins</a><small>ÚLTIMO LOGIN REALIZADO</small><br><strong><?php echo date('d/m/Y à\s H:i:s', strtotime($row_logins['login_professor_data_hora'])); ?></strong><?php } ?></td>
      <td></strong></td>
      <td></strong></td>
    </tr>

  </tbody>
</table>
    </div>
  </div>
  <div id="registros" class="ls-tab-content">
  <div class="ls-tabs-btn">
      <ul class="ls-tabs-btn-nav">
        <li class="col-md-6 col-xs-6"><a class="ls-btn" href="faltasFuncionarioCadastrar.php?cod=<?php echo $row_funcionario['vinculo_id']; ?>" target="_blank">REGISTRO DE FALTAS</a></li>
        <li class="col-md-6 col-xs-6"><a class="ls-btn" href="extrasFuncionarioCadastrar.php?cod=<?php echo $row_funcionario['vinculo_id']; ?>" target="_blank">REGISTRO DE EXTRAS</a></li>
        <li class="col-md-6 col-xs-6"><a class="ls-btn" href="ocorrenciaFuncionarioCadastrar.php?cod=<?php echo $row_funcionario['vinculo_id']; ?>" target="_blank">REGISTRO DE OCORRÊNCIA</a></li>
      </ul>
    </div>
  </div>
  <div id="gerenciamentos" class="ls-tab-content">
  <div class="ls-tabs-btn">
      <ul class="ls-tabs-btn-nav">
        <li class="col-md-6 col-xs-6"><a href="acessoProfessor.php?c=<?php echo $row_funcionario['vinculo_id']; ?>" class="ls-btn">GERENCIAR ACESSO</a></li>
        <li class="col-md-6 col-xs-6"><a href="senhaProfessor.php?c=<?php echo $row_funcionario['func_id']; ?>" class="ls-btn">SENHA DE ACESSO</a></li>
      </ul>
    </div>
  </div>
  <div id="formacao" class="ls-tab-content">
  <div class="ls-tabs-btn">
  <p>

<?php if ($totalRows_formacao > 0) { ?>  
<table class="ls-table ls-sm-space">
<thead>
    <tr>
      <th width="200">TIPO</th>
      <th>ÁREA</th>
      <th width="100">C/H</th>
    </tr>
</thead>
<tbody>
    <?php do { ?>
      <tr>
        <td width="200"><?php echo $row_formacao['titulacao_tipo']; ?></td>
        <td><?php echo $row_formacao['formacao_nome']; ?></td>
        <td width="100"><?php echo $row_formacao['titulacao_horas']; ?></td>
      </tr>
      <?php } while ($row_formacao = mysql_fetch_assoc($formacao)); ?>
    </tbody>
  </table>
  <?php } else { ?>

Nenhuma formação informada no cadastro.

<?php } ?>
  
  </p>
    </div>
  </div>

  <div id="cursos" class="ls-tab-content">
  <div class="ls-tabs-btn">
   <p>
<?php if ($totalRows_Cursos > 0) { ?>   
<table class="ls-table ls-sm-space">
<thead>
  <tr>
    <th>DESCRIÇÃO</th>
    <th>INSTITUIÇÃO</th>
    <th width="100">C/H</th>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_Cursos['curso_descricao']; ?></td>
      <td><?php echo $row_Cursos['curso_instituicao']; ?></td>
      <td width="100"><?php echo $row_Cursos['curso_ch']; ?></td>
    </tr>
    <?php } while ($row_Cursos = mysql_fetch_assoc($Cursos)); ?>
    </tbody>
</table>
  <?php } else { ?>

Nenhum curso informado no cadastro.

<?php } ?>

  </p>
    </div>
  </div>

</div>

<?php if ($totalRows_logins > 0) { ?>
<div data-ls-module="collapse" data-target="#0" class="ls-collapse ">
    <a href="#" class="ls-collapse-header">
      <h3 class="ls-collapse-title">Logins realizados</h3>
    </a>
    <div class="ls-collapse-body" id="0">
      <p>

<table class="ls-table ls-sm-space ls-table-striped">
  <tr>
    <td>Data/hora</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo date('d/m/Y à\s H:i:s', strtotime($row_logins['login_professor_data_hora'])); ?></td>
    </tr>
    <?php } while ($row_logins = mysql_fetch_assoc($logins)); ?>
</table>


      </p>
    </div>
  </div>
<?php } ?>  

<br><br><br><br><br><br>





<!-- CONTEÚDO -->
      </div>
    </main>

    <div class="ls-modal" id="requerimentoFerias">
  <div class="ls-modal-large">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">REQUERIMENTO DE FÉRIAS</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    <form method="GET" action="requerimentoFerias.php" class="ls-form ls-form-inline" data-ls-module="form" target="_blank">
    <label class="ls-label col-md-4">
        <p class="ls-label-info">Informe o ano de referência</p>
        <input type="number" name="ano_referencia" class="ls-no-spin" placeholder="Ex: 2023" size="32" required>
    </label>

    <label class="ls-label col-md-4">
        <p class="ls-label-info">Informe a data de início das férias</p>
        <input type="date" name="data_ferias_inicio" size="32" required>
    </label>

    <label class="ls-label col-md-4">
        <p class="ls-label-info">Informe a data de término das férias</p>
        <input type="date" name="data_ferias_final" size="32" required>
    </label>

    <input type="hidden" name="c" value="<?= $row_funcionario['func_id'] ?>">


    </div>
    <div class="ls-modal-footer">
      <button type="submit" class="ls-btn-primary">GERAR REQUERIMENTO</button>
    </div>
    </form>
  </div>
</div><!-- /.modal -->

<div class="ls-modal" id="licencaPremio">
  <div class="ls-modal-small">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">SOLICITAÇÃO DE LICENÇA PRÊMIO</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    <form method="GET" action="licencaPremio.php" class="ls-form ls-form-inline" data-ls-module="form" target="_blank">
    <label class="ls-label col-md-12">
        <p class="ls-label-info">Duração do Programa <span data-ls-module="popover" data-ls-popover="open" class="ls-ico-question ls-background-info" style="border-radius: 100%;cursor:pointer" data-content="
        <p>Insira a duração do programa ou atividade no formato: [número de meses] (número de meses por extenso) meses de [número de horas por semana]h.</p>
        <p>Exemplo: 03 (três) meses de 20h</p>
        "></span></p>
        <input type="text" name="duracao_programa" class="ls-no-spin" placeholder="Ex: 03 (três) meses de 20h" size="32" required>
    </label>

    <input type="hidden" name="c" value="<?= $row_funcionario['func_id'] ?>">


    </div>
    <div class="ls-modal-footer">
      <button type="submit" class="ls-btn-primary">GERAR REQUERIMENTO</button>
    </div>
    </form>
  </div>
</div><!-- /.modal -->

<div class="ls-modal" id="licencaPremioPecunia">
  <div class="ls-modal-small">
    <div class="ls-modal-header">
      <button data-dismiss="modal">&times;</button>
      <h4 class="ls-modal-title">SOLICITAÇÃO DE LICENÇA PRÊMIO EM PECÚNIA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
    <form method="GET" action="licencaPremioPecunia.php" class="ls-form ls-form-inline" data-ls-module="form" target="_blank">
    <label class="ls-label col-md-12">
        <p class="ls-label-info">Duração do Programa <span data-ls-module="popover" data-ls-popover="open" class="ls-ico-question ls-background-info" style="border-radius: 100%;cursor:pointer" data-content="
        <p>Insira a duração do programa ou atividade no formato: [número de meses] (número de meses por extenso) meses de [número de horas por semana]h.</p>
        <p>Exemplo: 03 (três) meses de 20h</p>
        "></span></p>
        <input type="text" name="duracao_programa" class="ls-no-spin" placeholder="Ex: 03 (três) meses de 20h" size="32" required>
    </label>

    <input type="hidden" name="c" value="<?= $row_funcionario['func_id'] ?>">


    </div>
    <div class="ls-modal-footer">
      <button type="submit" class="ls-btn-primary">GERAR REQUERIMENTO</button>
    </div>
    </form>
  </div>
</div><!-- /.modal -->

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include 'notificacoes.php'; ?>
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
mysql_free_result($funcionario);

mysql_free_result($logins);

mysql_free_result($Cursos);

mysql_free_result($formacao);

mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
