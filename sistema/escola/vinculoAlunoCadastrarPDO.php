<?php
require_once('../../Connections/SmecelNovoPDO.php');
include('fnc/inverteData.php');
include "fnc/sessionPDO.php";
include "usuLogadoPDO.php";
include "fnc/anoLetivoPDO.php";
include "fnc/alunosConta.php";

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Determinar se é rematrícula ou matrícula
$anoLetivoRematricula = (isset($_GET["periodo"]) && $_GET["periodo"] == "rematricula")
  ? $row_AnoLetivo['ano_letivo_ano'] + 1
  : $row_AnoLetivo['ano_letivo_ano'];

$link = ($anoLetivoRematricula > $row_AnoLetivo['ano_letivo_ano'])
  ? "turmaCadastrarRematricula.php?nova"
  : "turmaCadastrar.php";
$linkNome = ($anoLetivoRematricula > $row_AnoLetivo['ano_letivo_ano'])
  ? "REMATRICULAR"
  : "MATRICULAR";
$tituloRematricula = ($anoLetivoRematricula > $row_AnoLetivo['ano_letivo_ano'])
  ? "<strong class='ls-color-warning'>Rematricular no Ano Letivo $anoLetivoRematricula</strong>"
  : "<strong class=''>Matricular no Ano Letivo $anoLetivoRematricula</strong>";

// Processar o formulário de matrícula
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if ($row_UsuLogado['usu_insert'] == "N") {
    header("Location: vinculoAlunoExibirTurma.php?permissao");
    exit;
  }

  // Gerar hash único
  $hash = md5($_POST['cHash'] . time());

  // Função para gerar string aleatória (PHP 7+)
  // function generateRandomString($size = 4) {
  //     $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  //     $randomString = '';
  //     for ($i = 0; $i < $size; $i++) {
  //         $randomString .= $chars[random_int(0, strlen($chars) - 1)];
  //     }
  //     return $randomString;
  // }

  // Função para gerar string aleatória
  function generateRandomString($size = 4)
  {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $randomString = '';
    for ($i = 0; $i < $size; $i++) {
      $randomString .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $randomString;
  }

  // Gerar código de verificação
  $codVerificacao = implode('-', [
    generateRandomString(),
    generateRandomString(),
    generateRandomString(),
    generateRandomString()
  ]);

  // Validar e preparar os dados
  $vinculo_aluno_nao_reprova = (isset($_POST['vinculo_aluno_nao_reprova']) && $_POST['vinculo_aluno_nao_reprova'] == "on") ? "S" : "N";
  $vinculo_aluno_saida = isset($_POST['vinculo_aluno_saida']) ? (int) $_POST['vinculo_aluno_saida'] : 0;
  if (!in_array($vinculo_aluno_saida, [0, 1, 2])) {
    $vinculo_aluno_saida = 0;
  }

  // Inserir na tabela smc_vinculo_aluno
  $insertSQL = "
        INSERT INTO smc_vinculo_aluno (
            vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec,
            vinculo_aluno_ano_letivo, vinculo_aluno_data, vinculo_aluno_vacina_data_retorno, vinculo_aluno_transporte,
            vinculo_aluno_ponto_id, vinculo_aluno_multietapa, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia,
            vinculo_aluno_vacina_atualizada, vinculo_aluno_internet, vinculo_aluno_repetente, vinculo_aluno_hash,
            vinculo_aluno_verificacao, vinculo_aluno_id_cuidador, vinculo_aluno_id_matriz_multi, vinculo_aluno_nao_reprova,
            vinculo_aluno_saida
        ) VALUES (
            :id_aluno, :id_turma, :id_escola, :id_sec, :ano_letivo, :data, :vacina_data_retorno, :transporte,
            :ponto_id, :multietapa, :da_casa, :historico_transferencia, :vacina_atualizada, :internet, :repetente,
            :hash, :verificacao, :id_cuidador, :id_matriz_multi, :nao_reprova, :saida
        )";

  $stmt = $SmecelNovo->prepare($insertSQL);
  $stmt->execute([
    ':id_aluno' => (int) $_POST['vinculo_aluno_id_aluno'],
    ':id_turma' => (int) $_POST['vinculo_aluno_id_turma'],
    ':id_escola' => (int) $_POST['vinculo_aluno_id_escola'],
    ':id_sec' => (int) $_POST['vinculo_aluno_id_sec'],
    ':ano_letivo' => $_POST['vinculo_aluno_ano_letivo'],
    ':data' => $_POST['vinculo_aluno_data'],
    ':vacina_data_retorno' => $_POST['vinculo_aluno_vacina_data_retorno'],
    ':transporte' => $_POST['vinculo_aluno_transporte'],
    ':ponto_id' => (int) $_POST['vinculo_aluno_ponto_id'],
    ':multietapa' => (int) $_POST['vinculo_aluno_multietapa'],
    ':da_casa' => $_POST['vinculo_aluno_da_casa'],
    ':historico_transferencia' => $_POST['vinculo_aluno_historico_transferencia'],
    ':vacina_atualizada' => $_POST['vinculo_aluno_vacina_atualizada'],
    ':internet' => $_POST['vinculo_aluno_internet'],
    ':repetente' => $_POST['vinculo_aluno_repetente'],
    ':hash' => $hash,
    ':verificacao' => $codVerificacao,
    ':id_cuidador' => (int) $_POST['vinculo_aluno_id_cuidador'],
    ':id_matriz_multi' => (int) $_POST['vinculo_aluno_matriz_multietapa'],
    ':nao_reprova' => $vinculo_aluno_nao_reprova,
    ':saida' => $vinculo_aluno_saida
  ]);

  // Registrar log na tabela smc_registros
  $usu = (int) $_POST['usu_id'];
  $esc = (int) $_POST['usu_escola'];
  $detalhes = $_POST['detalhes'];
  date_default_timezone_set('America/Bahia');
  $dat = date('Y-m-d H:i:s');

  $logSQL = "
        INSERT INTO smc_registros (
            registros_id_escola, registros_id_usuario, registros_tipo, registros_complemento, registros_data_hora
        ) VALUES (
            :id_escola, :id_usuario, :tipo, :complemento, :data_hora
        )";
  $stmtLog = $SmecelNovo->prepare($logSQL);
  $stmtLog->execute([
    ':id_escola' => $esc,
    ':id_usuario' => $usu,
    ':tipo' => 9,
    ':complemento' => "($detalhes)",
    ':data_hora' => $dat
  ]);

  // Redirecionar após sucesso
  $insertGoTo = "matriculaExibe.php?cadastrado&cmatricula=$hash";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header("Location: $insertGoTo");
  exit;
}

// Consultas usando PDO
try {
  // Escola Logada
  $query_EscolaLogada = "
        SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, 
               escola_telefone1, escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, 
               sec_id, sec_cidade, sec_uf 
        FROM smc_escola
        INNER JOIN smc_sec ON sec_id = escola_id_sec 
        WHERE escola_id = :escola_id";
  $stmtEscolaLogada = $SmecelNovo->prepare($query_EscolaLogada);
  $stmtEscolaLogada->execute([':escola_id' => $row_UsuLogado['usu_escola']]);
  $row_EscolaLogada = $stmtEscolaLogada->fetch(PDO::FETCH_ASSOC);
  $totalRows_EscolaLogada = $stmtEscolaLogada->rowCount();

  // Aluno
  $colname_Aluno = isset($_GET['c']) ? $_GET['c'] : "-1";
  $query_Aluno = "
        SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, aluno_filiacao1, aluno_filiacao2, 
               aluno_sexo, aluno_raca, aluno_nacionalidade, aluno_uf_nascimento, aluno_municipio_nascimento, 
               aluno_aluno_com_deficiencia, aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, 
               aluno_data_espedicao, aluno_tipo_certidao, aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, 
               aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
               aluno_localizacao, aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, 
               aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_hash 
        FROM smc_aluno 
        WHERE aluno_hash = :aluno_hash";
  $stmtAluno = $SmecelNovo->prepare($query_Aluno);
  $stmtAluno->execute([':aluno_hash' => $colname_Aluno]);
  $row_Aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
  $totalRows_Aluno = $stmtAluno->rowCount();

  if ($totalRows_Aluno == 0) {
    header("Location: turmasAlunosVinculados.php?nada");
    exit;
  }

  // Pontos
  $query_Pontos = "
        SELECT te_ponto_id, te_ponto_id_sec, te_ponto_descricao, te_ponto_endereco, te_ponto_num, te_ponto_bairro, 
               te_ponto_latitude, te_ponto_longitude, te_ponto_obs 
        FROM smc_te_ponto 
        WHERE te_ponto_id_sec = :sec_id 
        ORDER BY te_ponto_descricao ASC";
  $stmtPontos = $SmecelNovo->prepare($query_Pontos);
  $stmtPontos->execute([':sec_id' => $row_UsuLogado['usu_sec']]);
  $row_Pontos = $stmtPontos->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Pontos = $stmtPontos->rowCount();

  // Etapa
  $query_Etapa = "
        SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev 
        FROM smc_etapa";
  $stmtEtapa = $SmecelNovo->prepare($query_Etapa);
  $stmtEtapa->execute();
  $row_Etapa = $stmtEtapa->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Etapa = $stmtEtapa->rowCount();

  // Matriz
  $query_Matriz = "
        SELECT matriz_id, matriz_id_secretaria, matriz_nome, matriz_ativa 
        FROM smc_matriz 
        WHERE matriz_ativa = 'S' AND matriz_id_secretaria = :sec_id";
  $stmtMatriz = $SmecelNovo->prepare($query_Matriz);
  $stmtMatriz->execute([':sec_id' => $row_EscolaLogada['sec_id']]);
  $row_Matriz = $stmtMatriz->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Matriz = $stmtMatriz->rowCount();

  // Vínculos Anteriores
  $query_VinculosAnteriores = "
        SELECT 
            vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, 
            vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_datatransferencia, 
            vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_id_cuidador,
            CASE vinculo_aluno_situacao
                WHEN 1 THEN 'MATRICULADO'
                WHEN 2 THEN 'TRANSFERIDO'
                WHEN 3 THEN 'DEIXOU DE FREQUENTAR'
                WHEN 4 THEN 'FALECIDO'
                WHEN 5 THEN 'OUTROS'
            END AS vinculo_aluno_situacao, 
            vinculo_aluno_datatransferencia,
            aluno_id, aluno_nome, 
            turma_id, turma_nome, turma_total_alunos, 
            CASE turma_turno
                WHEN 0 THEN 'INTEGRAL'
                WHEN 1 THEN 'MATUTINO'
                WHEN 2 THEN 'VESPERTNO'
                WHEN 3 THEN 'NOTURNO'
            END AS turma_turno, 
            escola_id, escola_nome, escola_id_sec, escola_telefone1, escola_telefone2, escola_email, 
            sec_id, sec_cidade, sec_uf
        FROM smc_vinculo_aluno
        INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
        INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
        INNER JOIN smc_escola ON escola_id = vinculo_aluno_id_escola 
        INNER JOIN smc_sec ON sec_id = escola_id_sec
        WHERE vinculo_aluno_id_aluno = :aluno_id AND vinculo_aluno_ano_letivo = :ano_letivo";
  $stmtVinculosAnteriores = $SmecelNovo->prepare($query_VinculosAnteriores);
  $stmtVinculosAnteriores->execute([
    ':aluno_id' => $row_Aluno['aluno_id'],
    ':ano_letivo' => $row_AnoLetivo['ano_letivo_ano']
  ]);
  $row_VinculosAnteriores = $stmtVinculosAnteriores->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_VinculosAnteriores = $stmtVinculosAnteriores->rowCount();

  // Turmas
  $query_Turmas = "
        SELECT turma_id, turma_id_escola, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo,
        CASE turma_turno
            WHEN 0 THEN 'INT'
            WHEN 1 THEN 'MAT'
            WHEN 2 THEN 'VESP'
            WHEN 3 THEN 'NOT'
        END AS turma_turno_nome
        FROM smc_turma 
        WHERE turma_ano_letivo = :ano_letivo AND turma_id_escola = :escola_id
        ORDER BY turma_turno, turma_etapa, turma_nome ASC";
  $stmtTurmas = $SmecelNovo->prepare($query_Turmas);
  $stmtTurmas->execute([
    ':ano_letivo' => $anoLetivoRematricula,
    ':escola_id' => $row_EscolaLogada['escola_id']
  ]);
  $row_Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_Turmas = $stmtTurmas->rowCount();

  if ($totalRows_Turmas == 0) {
    header("Location: $link");
    exit;
  }

  // Lista de Vínculos (Cuidadores)
  $query_ListaVinculos = "
        SELECT 
            vinculo_id, vinculo_id_escola, vinculo_acesso, vinculo_id_funcionario, vinculo_id_funcao, 
            vinculo_carga_horaria, DATE_FORMAT(vinculo_data_inicio, '%d/%m/%Y') AS vinculo_data_inicio, vinculo_obs, 
            func_id, func_nome, funcao_id, funcao_nome, func_regime, func_senha_ativa 
        FROM smc_vinculo 
        INNER JOIN smc_func ON func_id = vinculo_id_funcionario 
        INNER JOIN smc_funcao ON funcao_id = vinculo_id_funcao 
        WHERE vinculo_id_escola = :escola_id AND vinculo_status = 1
        ORDER BY func_nome ASC";
  $stmtListaVinculos = $SmecelNovo->prepare($query_ListaVinculos);
  $stmtListaVinculos->execute([':escola_id' => $row_EscolaLogada['escola_id']]);
  $row_ListaVinculos = $stmtListaVinculos->fetchAll(PDO::FETCH_ASSOC);
  $totalRows_ListaVinculos = $stmtListaVinculos->rowCount();

} catch (PDOException $e) {
  die("Erro ao executar consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html class="<?php echo htmlspecialchars($row_EscolaLogada['escola_tema']); ?>" lang="pt-br">

<head>
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

  <link rel="stylesheet" href="../../css/foundation-datepicker.css">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/locastyle.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .select2-container .select2-selection {
      border: 0px solid #ddd;
      border-radius: 4px;
      height: 45px;
      padding: 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 28px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 45px;
    }
  </style>
</head>

<body>
  <?php include_once("menu-top.php"); ?>
  <?php include_once("menu-esc.php"); ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home"><?php echo $tituloRematricula; ?></h1>
      <form method="post" name="form1" action="<?php echo htmlspecialchars($editFormAction); ?>"
        class="ls-form ls-form-horizontal row" data-ls-module="form" autocomplete="off">
        <fieldset>
          <div class="ls-box">
            <label class="ls-label col-md-12">
              <div class="ls-alert-info">
                <strong>Aluno(a):</strong> <?php echo htmlspecialchars($row_Aluno['aluno_nome']); ?>
              </div>
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">DATA DA MATRÍCULA</b>
              <input type="date" placeholder="INFORME A DATA" name="vinculo_aluno_data" id="data_matricula1"
                value="<?php echo date("Y-m-d"); ?>" class="ls-field-lg" required>
            </label>
            <label class="ls-label col-md-6">
              <b class="ls-label-text">TURMA | TURNO | Nº MATRÍCULAS</b>
              <div class="ls-custom-select ls-field-lg lista-turmas">
                <select name="vinculo_aluno_id_turma" class="ls-select" id="lista-turmas" required>
                  <option value="">-</option>
                  <?php foreach ($row_Turmas as $turma) { ?>
                    <option value="<?php echo htmlspecialchars($turma['turma_id']); ?>">
                      <?php echo htmlspecialchars($turma['turma_turno_nome']); ?> |
                      <?php echo htmlspecialchars($turma['turma_nome']); ?> |
                      <?php
                      $alunosTurma = alunosConta($turma['turma_id'], $anoLetivoRematricula);
                      echo "$alunosTurma/{$turma['turma_total_alunos']}";
                      ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </label>
            <label class="ls-label col-md-3">
              <b class="ls-label-text">TRANSPORTE ESCOLAR</b>
              <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
                data-content="Informar apenas o aluno que mora na Zona Rural." data-title="Atenção"></a> <br><br>
              <p>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" value="N" onclick="javascript:transporte_nao();"
                    checked />
                  NÃO UTILIZA
                </label>
                <label class="ls-label-text">
                  <input type="radio" name="vinculo_aluno_transporte" value="S"
                    onclick="javascript:transporte_sim();" />
                  UTILIZA
                </label>
              </p>
            </label>
          </div>
          <label class="ls-label col-md-12 ls-box" style="display:none" id="ponto">
            <b class="ls-label-text">PONTO</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_ponto_id" class="ls-select">
                <option value="">-</option>
                <?php foreach ($row_Pontos as $ponto) { ?>
                  <option value="<?php echo htmlspecialchars($ponto['te_ponto_id']); ?>">
                    <?php echo htmlspecialchars($ponto['te_ponto_descricao'] . ' ' .
                      $ponto['te_ponto_endereco'] . ' ' .
                      $ponto['te_ponto_num'] . ' ' .
                      $ponto['te_ponto_bairro'] . ' ' .
                      $ponto['te_ponto_obs']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">Aluno repetente nessa etapa de ensino? </b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="right"
              data-content="Informar se o aluno repetiu esse mesmo ano/série." data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_repetente" value="S" />
                SIM
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_repetente" value="N" checked />
                NÃO
              </label>
            </p>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">Aluno(a) possui acesso à internet?</b> <br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_internet" value="S" checked />
                SIM
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_internet" value="N" />
                NÃO
              </label>
            </p>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">ALUNO MATRICULADO É DA ESCOLA (DA CASA) OU DE OUTRA ESCOLA/CIDADE (DE FORA)</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Informar se o aluno para a matrícula é de casa (já estuda na escola) ou de fora (vindo de outra escola no município ou fora dele)."
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_da_casa" value="C" onclick="javascript:da_casa();" checked />
                DA CASA
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_da_casa" value="F" onclick="javascript:de_fora();" />
                DE FORA
              </label>
            </p>
          </label>
          <label class="ls-label col-sm-12 ls-box" style="display:none" id="historico">
            <b class="ls-label-text">TRANSFERIDO COM HISTÓRICO OU DECLARAÇÃO</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Se o aluno foi trasferido com Declaração, terá 30 dias a contar da data da matrícula para entregar o Histórico"
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_historico_transferencia" value="H"
                  id="vinculo_aluno_historico_transferencia_h" />
                HISTÓRICO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_historico_transferencia" value="D"
                  id="vinculo_aluno_historico_transferencia_d" />
                DECLARAÇÃO
              </label>
            </p>
          </label>
          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">CARTEIRA DE VACINAÇÃO DO ALUNO ESTÁ ATUALIZADA?</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Ao marcar SIM, você DECLARA que o aluno ou responsável pelo aluno apresentou o documento que comprove que a Carteira de Vacinação está em dia."
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="S" onclick="javascript:aceite();"
                  required />
                SIM
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="N" />
                NÃO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_vacina_atualizada" value="I" />
                SEM INFORMAÇÃO
              </label>
            </p>
            <p>
            <div class="ls-alert-warning ls-dismissable" style="display:none" id="aviso_aceite">
              <span data-ls-module="dismiss" class="ls-dismiss">&times;</span>
              <strong>Atenção!</strong> Ao marcar SIM, você DECLARA que o aluno ou responsável pelo aluno apresentou o
              documento que comprove que a Carteira de Vacinação está em dia.
            </div>
            </p>
            <div style="display:none" id="aviso_aceite_1">
              <label class="ls-label col-md-5">
                <b class="ls-label-text">DATA DO RETORNO PARA PRÓXIMA VACINA</b>
                <input type="date" placeholder="INFORME A DATA" name="vinculo_aluno_vacina_data_retorno"
                  id="data_retorno1" value="" class="ls-field-lg">
              </label>
            </div>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">SAÍDA DO ALUNO</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Indique se o aluno sai da escola sozinho ou acompanhado. Essa informação será usada para relatórios e controle na portaria."
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="1" required />
                ACOMPANHADO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="2" />
                SOZINHO
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_saida" value="0" />
                SEM INFORMAÇÃO
              </label>
            </p>
          </label>

          <label class="ls-label col-sm-12 ls-box">
            <b class="ls-label-text">TURMA MULTISSERIADA</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Informar se o aluno para a matrícula é de casa (já estuda na escola) ou de fora (vindo de outra escola no município ou fora dele)."
              data-title="Atenção"></a> <br><br>
            <p>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_multietapa" value="S" onclick="javascript:multi_sim();"
                   />
                SIM
              </label>
              <label class="ls-label-text">
                <input type="radio" name="vinculo_aluno_multietapa" value="N" onclick="javascript:multi_nao();" checked/>
                NÃO
              </label>
            </p>
          </label>

          <label class="ls-label col-md-12 ls-box" style="display:none" id="multisseriada">
            <b class="ls-label-text">Etapa na turma multisseriada</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_multietapa" class="ls-select">
                <option value="">-</option>
                <?php foreach ($row_Etapa as $etapa) { ?>
                  <option value="<?php echo htmlspecialchars($etapa['etapa_id']); ?>">
                    <?php echo htmlspecialchars($etapa['etapa_nome']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-md-12 ls-box" style="display:none" id="multisseriadaMatriz">
            <b class="ls-label-text">Matriz na turma multisseriada</b>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_matriz_multietapa" class="ls-select">
                <option value="">-</option>
                <?php foreach ($row_Matriz as $matriz) { ?>
                  <option value="<?php echo htmlspecialchars($matriz['matriz_id']); ?>">
                    <?php echo htmlspecialchars($matriz['matriz_nome']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <br>
          </label>

          <label class="ls-label col-md-12 ls-box">
            <b class="ls-label-text">VINCULAR CUIDADOR</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="O cuidador de alunos especiais na escola desempenha um papel crucial no suporte às necessidades individuais desses estudantes. Sua responsabilidade inclui oferecer assistência personalizada, adaptar o ambiente escolar, colaborar com professores e terapeutas, tudo para garantir o bem-estar e o desenvolvimento integral desses alunos."
              data-title="Caso o aluno possua um cuidador"></a>
            <div class="ls-custom-select ls-field-lg">
              <select name="vinculo_aluno_id_cuidador" class="ls-select">
                <option value="">-</option>
                <?php foreach ($row_ListaVinculos as $vinculo) { ?>
                  <option value="<?php echo htmlspecialchars($vinculo['vinculo_id']); ?>">
                    <?php echo htmlspecialchars($vinculo['func_nome']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <br>
          </label>

          <div class="ls-label col-md-12 ls-box">
            <b class="ls-label-text">ALUNO ESPECIAL (não reprova)</b>
            <a href="#" class="ls-ico-help" data-trigger="hover" data-ls-module="popover" data-placement="left"
              data-content="Caso o aluno seja considerado especial na etapa de ensino dos anos finais e sua avaliação seja feita por meio de relatórios descritivos em vez de notas, essa opção deverá ser marcada."
              data-title="Caso o aluno seja especial"></a>
            <p>
              <label class="ls-label-text">
                <input type="checkbox" name="vinculo_aluno_nao_reprova" class="ls-field">
                SIM
              </label>
            </p>
          </div>
        </fieldset>
        <div class="ls-actions-btn">
          <input type="submit" value="MATRICULAR" class="ls-btn-primary ls-btn-lg ls-float-left" id="botaoMatricular">
          <input value="SALVANDO..." class="ls-btn-primary ls-btn-lg ls-disabled ls-float-left"
            id="botaoMatricularAguarde" style="display:none; float:left">
          <a href="vinculoAlunoExibirTurma.php" class="ls-btn-danger ls-btn-lg ls-txt-right ls-float-right">Cancelar</a>
        </div>
        <input type="hidden" name="cHash" value="<?php echo htmlspecialchars($row_Aluno['aluno_hash']); ?>">
        <input type="hidden" name="vinculo_aluno_id_aluno" value="<?php echo (int) $row_Aluno['aluno_id']; ?>">
        <input type="hidden" name="vinculo_aluno_id_escola" value="<?php echo (int) $row_EscolaLogada['escola_id']; ?>">
        <input type="hidden" name="vinculo_aluno_id_sec" value="<?php echo (int) $row_EscolaLogada['escola_id_sec']; ?>">
        <input type="hidden" name="vinculo_aluno_ano_letivo"
          value="<?php echo htmlspecialchars($anoLetivoRematricula); ?>">
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="usu_id" value="<?php echo (int) $row_UsuLogado['usu_id']; ?>">
        <input type="hidden" name="usu_escola" value="<?php echo (int) $row_UsuLogado['usu_escola']; ?>">
        <input type="hidden" name="detalhes"
          value="<?php echo (int) $row_Aluno['aluno_id'] . ' - ' . htmlspecialchars($row_Aluno['aluno_nome']); ?>">
      </form>

      <?php if ($totalRows_VinculosAnteriores > 0) { ?>
        <div class="ls-alert-success">
          <strong>Atenção:</strong> Foram encontrados os seguintes vínculos para
          <strong><?php echo htmlspecialchars($row_VinculosAnteriores[0]['aluno_nome']); ?></strong> neste ano letivo de
          <?php echo htmlspecialchars($row_VinculosAnteriores[0]['vinculo_aluno_ano_letivo']); ?>:
          <table width="100%" class="ls-table ls-table-striped ls-sm-space">
            <thead>
              <tr>
                <th class="ls-txt-center">CIDADE</th>
                <th class="ls-txt-center">ESCOLA</th>
                <th class="ls-txt-center">TURMA</th>
                <th class="ls-txt-center" width="80">ANO</th>
                <th class="ls-txt-center" width="100">MATRÍCULA</th>
                <th class="ls-txt-center" width="120">TRANSFERÊNCIA</th>
                <th class="ls-txt-center" width="120">SITUAÇÃO</th>
                <th class="ls-txt-center">CONTATO DA ESCOLA</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($row_VinculosAnteriores as $vinculo) { ?>
                <tr>
                  <td class="ls-txt-center">
                    <?php echo htmlspecialchars($vinculo['sec_cidade'] . ' - ' . $vinculo['sec_uf']); ?></td>
                  <td class="ls-txt-center"><?php echo htmlspecialchars($vinculo['escola_nome']); ?></td>
                  <td class="ls-txt-center">
                    <?php echo htmlspecialchars($vinculo['turma_nome'] . ' ' . $vinculo['turma_turno']); ?></td>
                  <td class="ls-txt-center"><?php echo htmlspecialchars($vinculo['vinculo_aluno_ano_letivo']); ?></td>
                  <td class="ls-txt-center"><?php echo htmlspecialchars(inverteData($vinculo['vinculo_aluno_data'])); ?>
                  </td>
                  <td class="ls-txt-center">
                    <?php echo htmlspecialchars(inverteData($vinculo['vinculo_aluno_datatransferencia'])); ?></td>
                  <td class="ls-txt-center"><?php echo htmlspecialchars($vinculo['vinculo_aluno_situacao']); ?></td>
                  <td class="ls-txt-center">
                    <?php echo htmlspecialchars($vinculo['escola_email']); ?><br>
                    <?php echo htmlspecialchars($vinculo['escola_telefone1']); ?><br>
                    <?php echo htmlspecialchars($vinculo['escola_telefone2']); ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
      <p>&nbsp;</p>
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

  <script src="js/locastyle.js"></script>
  <script src="../../js/jquery.mask.js"></script>
  <script src="../../js/foundation-datepicker.js"></script>
  <script src="../../js/foundation-datepicker.pt-br.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#lista-turmas').select2({
        placeholder: "Escolha a turma",
        width: '100%'
      });
    });

    function da_casa() {
      document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false;
      document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false;
      document.getElementById("historico").style.display = "none";
    }

    function de_fora() {
      document.getElementById("vinculo_aluno_historico_transferencia_h").disabled = false;
      document.getElementById("vinculo_aluno_historico_transferencia_d").disabled = false;
      document.getElementById("historico").style.display = "block";
    }

    function aceite() {
      document.getElementById("aviso_aceite").style.display = "block";
      document.getElementById("aviso_aceite_1").style.display = "block";
    }

    function transporte_sim() {
      document.getElementById("ponto").style.display = "block";
    }

    function transporte_nao() {
      document.getElementById("ponto").style.display = "none";
    }

    function multi_sim() {
      document.getElementById("multisseriada").style.display = "block";
      document.getElementById("multisseriadaMatriz").style.display = "block";
    }

    function multi_nao() {
      document.getElementById("multisseriada").style.display = "none";
      document.getElementById("multisseriadaMatriz").style.display = "none";
    }

    $(function () {
      $('#data_matricula, #data_retorno').fdatepicker({
        format: 'dd/mm/yyyy',
        disableDblClickSelection: true,
        language: 'pt-br',
        leftArrow: '<<',
        rightArrow: '>>',
        closeIcon: 'X',
        closeButton: false
      });
    });

    $(document).ready(function () {
      $('.data_matricula, .data_retorno').mask('00/00/0000');
      $('#botaoMatricular').click(function (event) {
        event.preventDefault();
        $("#botaoMatricular").css('display', 'none');
        $("#botaoMatricularAguarde").css('display', 'block');
        setTimeout(function () {
          $("#botaoMatricular").css('display', 'block');
          $("#botaoMatricularAguarde").css('display', 'none');
          $('form[name="form1"]').submit();
        }, 2000);
      });
    });
  </script>
</body>

</html>