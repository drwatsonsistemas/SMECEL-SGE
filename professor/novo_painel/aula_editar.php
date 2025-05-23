<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php"; 
include "fnc/anti_injection.php";

$colname_EditarAula = isset($_GET['aula']) ? anti_injection($_GET['aula']) : "-1";
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";
$turma = isset($_GET['turma']) ? anti_injection($_GET['turma']) : "-1";
$data = isset($_GET['data']) ? anti_injection($_GET['data']) : "-1";

try {
    // Consulta principal (Editar Aula)
    $query_EditarAula = "
        SELECT plano_aula_id, plano_aula_id_habilidade, plano_aula_id_turma, plano_aula_id_disciplina, plano_aula_id_professor, plano_aula_data, plano_aula_data_cadastro, plano_aula_texto,
               plano_aula_publicado, plano_aula_hash, turma_id, turma_nome, turma_etapa, turma_turno, turma_id_escola, etapa_id, etapa_id_filtro, etapa_nome, etapa_ano_ef,
               escola_id, escola_nome, disciplina_id, disciplina_nome, disciplina_cor_fundo,
               CASE turma_turno
                   WHEN 0 THEN 'INTEGRAL'
                   WHEN 1 THEN 'MATUTINO'
                   WHEN 2 THEN 'VESPERTINO'
                   WHEN 3 THEN 'NOTURNO'
               END AS turma_turno
        FROM smc_plano_aula
        INNER JOIN smc_turma ON turma_id = plano_aula_id_turma
        INNER JOIN smc_etapa ON etapa_id = turma_etapa
        INNER JOIN smc_escola ON escola_id = turma_id_escola
        INNER JOIN smc_disciplina ON disciplina_id = plano_aula_id_disciplina
        WHERE plano_aula_hash = :aula_hash";

    $stmtEditarAula = $SmecelNovo->prepare($query_EditarAula);
    $stmtEditarAula->execute([':aula_hash' => $colname_EditarAula]);
    $row_EditarAula = $stmtEditarAula->fetch(PDO::FETCH_ASSOC);

    if (!$row_EditarAula) {
        throw new Exception("Aula não encontrada.");
    }

    $disciplina = $row_EditarAula['plano_aula_id_disciplina'];
    $etapa_ano = $row_EditarAula['etapa_ano_ef'];

    // Consulta Habilidades
    $consulta = " AND bncc_ef_ano IN ('$etapa_ano')";
    $query_Habilidades = "
        SELECT bncc_ef_id, bncc_ef_habilidades, bncc_ef_obj_conhec, bncc_ef_componente, bncc_ef_campos_atuacao, bncc_ef_eixo, bncc_ef_un_tematicas, bncc_ef_prat_ling, bncc_ef_ano
        FROM smc_bncc_ef
        WHERE bncc_ef_comp_id = :disciplina_id $consulta";

    $stmtHabilidades = $SmecelNovo->prepare($query_Habilidades);
    $stmtHabilidades->execute([':disciplina_id' => $disciplina]);
    $row_Habilidades = $stmtHabilidades->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
    exit;
}

$editFormAction = $_SERVER['PHP_SELF'] . "?" . htmlentities($_SERVER['QUERY_STRING']);

// Atualização de aula
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    try {
        $updateSQL = "
            UPDATE smc_plano_aula
            SET plano_aula_id_habilidade = :habilidade,
                plano_aula_data = :data,
                plano_aula_texto = :texto,
                plano_aula_hash = :hash
            WHERE plano_aula_id = :aula_id";

        $stmtUpdate = $SmecelNovo->prepare($updateSQL);
        $stmtUpdate->execute([
            ':habilidade' => $_POST['plano_aula_id_habilidade'],
            ':data' => $_POST['plano_aula_data'],
            ':texto' => $_POST['plano_aula_texto'],
            ':hash' => $_POST['plano_aula_hash'],
            ':aula_id' => $_POST['plano_aula_id'],
        ]);

        $updateGoTo = "aulas.php?escola=$escola&turma=$turma&data=$data&salvo";
        header("Location: $updateGoTo");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar: " . $e->getMessage();
        exit;
    }
}
?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-117872281-1');
  </script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" type="image/png" href="https://www.smecel.com.br/favicon-32x32.png">
  <title>Editar Aula | SMECEL</title>
  <link rel="stylesheet" href="css/locastyle.css">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
  <?php include_once "inc/navebar.php"; ?>
  <?php include_once "inc/sidebar.php"; ?>
  <main class="ls-main">
    <div class="container-fluid">
      <h1 class="ls-title-intro ls-ico-home">Editar Aula <?php echo $row_EditarAula['plano_aula_id']; ?></h1>
      <a href="aulas.php?escola=<?php echo $escola; ?>&turma=<?php echo $turma; ?>&data=<?php echo $data; ?>" class="ls-btn ls-ico-chevron-left">Voltar</a>

      <form method="post" action="<?php echo $editFormAction; ?>" class="ls-form row">
        <div class="ls-box-filter">
          <h5 class="ls-title-5"><?php echo $row_EditarAula['escola_nome']; ?></h5>
          <p><strong><?php echo $row_EditarAula['turma_nome']; ?>, <?php echo $row_EditarAula['turma_turno']; ?></strong></p>
          <p style="color:<?php echo $row_EditarAula['disciplina_cor_fundo']; ?>;"><?php echo $row_EditarAula['disciplina_nome']; ?></p>
        </div>
        
        <label class="ls-label col-md-3">
          <b class="ls-label-text">Data</b>
          <input type="date" name="plano_aula_data" value="<?php echo htmlspecialchars($row_EditarAula['plano_aula_data']); ?>" required>
        </label>
        <label class="ls-label col-md-9">
          <b class="ls-label-text">Assunto</b>
          <input type="text" name="plano_aula_texto" value="<?php echo htmlspecialchars($row_EditarAula['plano_aula_texto']); ?>" required>
        </label>

        <table class="ls-table ls-sm-space">
          <thead>
            <tr>
              <th>Habilidade</th>
              <th width="40"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($row_Habilidades as $habilidade): ?>
              <tr>
                <td>
                  <label>
                    <input type="radio" name="plano_aula_id_habilidade" value="<?php echo $habilidade['bncc_ef_id']; ?>" 
                      <?php echo ($row_EditarAula['plano_aula_id_habilidade'] == $habilidade['bncc_ef_id']) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($habilidade['bncc_ef_habilidades']); ?>
                  </label>
                </td>
                <td>
                  <a href="#" class="ls-ico-help" data-ls-module="popover" 
                     data-content="<strong><?php echo htmlspecialchars($habilidade['bncc_ef_obj_conhec']); ?></strong>" 
                     data-title="<?php echo htmlspecialchars($habilidade['bncc_ef_habilidades']); ?>">
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <td>
                <label>
                  <input type="radio" name="plano_aula_id_habilidade" value="0" <?php echo ($row_EditarAula['plano_aula_id_habilidade'] == 0) ? 'checked' : ''; ?>>
                  Nenhuma opção
                </label>
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div class="ls-actions-btn">
          <input type="submit" value="Salvar" class="ls-btn-primary">
          <a href="aulas.php?escola=<?php echo $escola; ?>&turma=<?php echo $turma; ?>&data=<?php echo $data; ?>" class="ls-btn-danger">Cancelar</a>
        </div>

        <input type="hidden" name="plano_aula_id" value="<?php echo $row_EditarAula['plano_aula_id']; ?>">
        <input type="hidden" name="plano_aula_hash" value="<?php echo htmlspecialchars($row_EditarAula['plano_aula_hash']); ?>">
        <input type="hidden" name="MM_update" value="form1">
      </form>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/locastyle.js"></script>
  <script src="js/sweetalert2.min.js"></script>
</body>
</html>
