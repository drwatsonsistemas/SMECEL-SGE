<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "fnc/anti_injection.php"; ?>
<?php

// Pegando os valores de GET e tratando com anti_injection
$componente = isset($_GET['componente']) ? anti_injection($_GET['componente']) : "-1";
$etapa = isset($_GET['etapa']) ? anti_injection($_GET['etapa']) : "-1";
$colname_Componente = isset($_GET['componente']) ? anti_injection($_GET['componente']) : "-1";

// Consulta para pegar os dados do componente
$query_Componente = "SELECT disciplina_id, disciplina_area_conhecimento_id, disciplina_codigo_inep, disciplina_ordem, 
                             disciplina_nome, disciplina_nome_abrev, disciplina_cor_fundo, disciplina_bncc, 
                             disciplina_diversificada, disciplina_id_campos_exp 
                      FROM smc_disciplina 
                      WHERE disciplina_id = :componente";
$stmt_Componente = $SmecelNovo->prepare($query_Componente);
$stmt_Componente->bindParam(':componente', $colname_Componente, PDO::PARAM_INT);
$stmt_Componente->execute();
$row_Componente = $stmt_Componente->fetch(PDO::FETCH_ASSOC);
$totalRows_Componente = $stmt_Componente->rowCount();

// Consulta para pegar os dados da etapa
$colname_Etapa = isset($_GET['etapa']) ? anti_injection($_GET['etapa']) : "-1";
$query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef 
                FROM smc_etapa 
                WHERE etapa_id = :etapa";
$stmt_Etapa = $SmecelNovo->prepare($query_Etapa);
$stmt_Etapa->bindParam(':etapa', $colname_Etapa, PDO::PARAM_INT);
$stmt_Etapa->execute();
$row_Etapa = $stmt_Etapa->fetch(PDO::FETCH_ASSOC);
$totalRows_Etapa = $stmt_Etapa->rowCount();

// Pegando o valor de escola
$escola = isset($_GET['escola']) ? anti_injection($_GET['escola']) : "-1";

$ID_PROFESSOR = ID_PROFESSOR;
$ANO_LETIVO = ANO_LETIVO;
// Consulta para pegar os dados da AC
$query_ac = "SELECT ac_id, ac_id_professor, ac_id_escola, ac_id_componente, ac_id_etapa, ac_ano_letivo, 
                    ac_data_inicial, ac_data_final, ac_conteudo, ac_objetivo_especifico, ac_objeto_conhecimento, 
                    ac_metodologia, ac_recursos, ac_avaliacao, ac_criacao, ac_da_conviver, ac_da_brincar, 
                    ac_da_participar, ac_da_explorar, ac_da_expressar, ac_da_conhecerse, ac_ce_eo, ac_ce_ts, 
                    ac_ce_ef, ac_ce_cg, ac_ce_et, ac_ce_di 
             FROM smc_ac
             WHERE ac_id_professor = :professor_id AND ac_id_componente = :componente 
               AND ac_id_etapa = :etapa AND ac_id_escola = :escola AND ac_ano_letivo = :ano_letivo
             ORDER BY ac_data_inicial DESC";
$stmt_ac = $SmecelNovo->prepare($query_ac);
$stmt_ac->bindParam(':professor_id', $ID_PROFESSOR, PDO::PARAM_INT);
$stmt_ac->bindParam(':componente', $colname_Componente, PDO::PARAM_INT);
$stmt_ac->bindParam(':etapa', $colname_Etapa, PDO::PARAM_INT);
$stmt_ac->bindParam(':escola', $escola, PDO::PARAM_INT);
$stmt_ac->bindParam(':ano_letivo', $ANO_LETIVO, PDO::PARAM_INT);
$stmt_ac->execute();
$row_ac = $stmt_ac->fetch(PDO::FETCH_ASSOC);
$totalRows_ac = $stmt_ac->rowCount();

// Ação do formulário
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Inserção de dados no banco
if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "form2") {
  $insertSQL = "INSERT INTO smc_pauta (pauta_id_professor, pauta_id_escola, pauta_ano_letivo) 
                VALUES (:professor_id, :escola, :ano_letivo)";
  
  $stmt_insert = $SmecelNovo->prepare($insertSQL);
  $stmt_insert->bindParam(':professor_id', $ID_PROFESSOR, PDO::PARAM_INT);
  $stmt_insert->bindParam(':escola', $escola, PDO::PARAM_INT);
  $stmt_insert->bindParam(':ano_letivo', $ANO_LETIVO, PDO::PARAM_INT);
  $stmt_insert->execute();

  $id_pauta = $SmecelNovo->lastInsertId();

  $insertGoTo = "atv_comp_editar.php?escola=$escola&pauta=$id_pauta";
  header("Location: $insertGoTo");
  exit;
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
<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>

<div class="ls-modal" data-modal-blocked id="myAwesomeModal">
  <div class="ls-modal-box">
  <div class="ls-modal-header">
      <h4 class="ls-modal-title">NOVA PAUTA</h4>
    </div>
    <div class="ls-modal-body" id="myModalBody">
      <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
            <input type="submit" class="ls-btn-primary ls-btn-lg ls-btn-block" value="REGISTRAR PAUTA">
            <input type="hidden" name="MM_insert" value="form2">
      </form>
    </div>
  </div>
</div><!-- /.modal -->

<main class="ls-main">
  <div class="container-fluid">
   
  </div>
</div>







<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script> 
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="js/sweetalert2.min.js"></script> 

<script src="https://cdn.tiny.cloud/1/iq1gy8qt7s1b9bj92c2fc7whie95augu8r8kz97dgoufkli8/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 
<script src="langs/pt_BR.js"></script> 
<script>
    locastyle.modal.open("#myAwesomeModal");
</script>

</body>
</html>
