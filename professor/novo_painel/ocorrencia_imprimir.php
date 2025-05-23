<?php 
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php"; 
include "fnc/anti_injection.php"; 
include('../../sistema/funcoes/inverteData.php'); 

// Inicia a conexão PDO
try {
    // Verifica se o parâmetro de consulta 'oc' está presente
    $colname_ac_edit = "-1";
    if (isset($_GET['oc'])) {
        $colname_ac_edit = $_GET['oc'];
    }

    // Verifica se o parâmetro de consulta 'escola' está presente
    $escola = "-1";
    if (isset($_GET['escola'])) {
        $escola = $_GET['escola'];
    }

    // Consulta para obter os dados da escola logada
    $query_EscolaLogada = "
    SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
           escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, escola_tema, sec_id, sec_cidade, sec_uf, sec_termo_matricula, escola_assinatura 
    FROM smc_escola
    INNER JOIN smc_sec ON sec_id = escola_id_sec 
    WHERE escola_id = :escola";
    
    // Prepare a consulta
    $stmt_EscolaLogada = $SmecelNovo->prepare($query_EscolaLogada);
    // Bind parâmetros
    $stmt_EscolaLogada->bindParam(':escola', $escola, PDO::PARAM_INT);
    // Execute a consulta
    $stmt_EscolaLogada->execute();
    $row_EscolaLogada = $stmt_EscolaLogada->fetch(PDO::FETCH_ASSOC);
    $totalRows_EscolaLogada = $stmt_EscolaLogada->rowCount();

    // Consulta para obter as ocorrências do professor
    $query_OC = "
    SELECT ocorrencia_id, ocorrencia_id_turma, ocorrencia_id_escola, ocorrencia_id_professor, ocorrencia_data, ocorrencia_descricao, 
           turma_id, turma_nome, escola_id, escola_nome
    FROM smc_ocorrencia_turma
    LEFT JOIN smc_turma ON turma_id = ocorrencia_id_turma
    LEFT JOIN smc_escola ON escola_id = ocorrencia_id_escola
    WHERE ocorrencia_id_professor = :professor
    ORDER BY ocorrencia_data DESC";

    // Prepare a consulta
    $stmt_OC = $SmecelNovo->prepare($query_OC);
    // Bind o parâmetro do ID do professor
    $stmt_OC->bindParam(':professor', $row_ProfLogado['func_id'], PDO::PARAM_INT);
    // Execute a consulta
    $stmt_OC->execute();
    $row_OC = $stmt_OC->fetch(PDO::FETCH_ASSOC);
    $totalRows_OC = $stmt_OC->rowCount();

} catch (PDOException $e) {
    // Caso ocorra algum erro na execução das consultas
    echo "Erro: " . $e->getMessage();
    exit();
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
  <title>OCORRENCIA | <?php echo $row_OC['turma_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" href="css/sweetalert2.min.css">

  <style>

    body {
      font-size: 12px;
      background-image:url(<?php if ($row_EscolaLogada['escola_logo']<>"") { ?>../../img/marcadagua/<?php echo $row_EscolaLogada['escola_logo']; ?><?php } else { ?>../../img/marcadagua/brasao_republica.png<?php } ?>);
      background-repeat:no-repeat;
      background-position:center center;
      z-index:-999;
    }
    p { margin-bottom: 1px; }
    page {
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
    }
    page[size="A4"] {
      width: 21cm;
      height: 29.7cm;
      padding: 5px;
      
    }
    page[size="A4"][layout="portrait"] {
      width: 29.7cm;
      height: 21cm;
    }
    @media print {
      body,
      page {
        margin: 0;
        box-shadow: 0;
      }
      
      #div_impressao {
       display:none;
     }  
   }
   table.bordasimples {border-collapse: collapse; font-size:10px; }
   table.bordasimples tr td {border:1px dotted #000000; padding:2px; font-size:14px; vertical-align: top; height:30px;}
   table.bordasimples tr th {border:1px dotted #000000; padding:2px; font-size:14px; vertical-align: top; height:30px;}



 </style>
</head>
<body onload="self.print()">
  <page size="A4">


    <div class="ls-txt-center1">

      <table>

       <tr>
        <td width="150px" class="ls-txt-center">
          <span><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></span>
        </td>
        
        <td width="350px">
          <h2><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></h2>
          <small>
            <?php echo $row_EscolaLogada['escola_endereco']; ?>, 
            <?php echo $row_EscolaLogada['escola_num']; ?> - 
            <?php echo $row_EscolaLogada['escola_bairro']; ?> - 
            <?php echo $row_EscolaLogada['escola_cep']; ?><br>
            CNPJ:<?php echo $row_EscolaLogada['escola_cnpj']; ?> INEP:<?php echo $row_EscolaLogada['escola_inep']; ?><br>
            <?php echo $row_EscolaLogada['escola_telefone1']; ?> <?php echo $row_EscolaLogada['escola_telefone2']; ?> <?php echo $row_EscolaLogada['escola_email']; ?>
          </small>
        </td>
        
        <td class="ls-txt-right" width="270px">

          <h2 class="ls-txt-right">OCORRÊNCIA</h2>
          
        </td>	
      </tr>
      
    </table>	
    
  </div>
  <br>
  <table class="ls-sm-space bordasimples" width="100%" style="font-size:10px;">
    <tr>
      <td><small><strong>Professor(a):</strong></small><br><?php echo $row_ProfLogado['func_nome'] ?>&nbsp;</td>
      <td><small><strong>Turma:</strong></small><br><?php echo $row_OC['turma_nome'] ?>&nbsp;</td>
      <td><small><strong>Data:</strong></small><br><?php echo inverteData($row_OC['ocorrencia_data']); ?>&nbsp;</td>
    </tr>
    
  </table >

  <br><br>

  <table class="ls-sm-space bordasimples" width="100%">
   <tr>
    <td><?php echo $row_OC['ocorrencia_descricao']; ?></td>
  </tr>

</table>
<br><br>
<table class="ls-sm-space bordasimples" width="100%">
  <tr>
    <td class="ls-v-align-middle">
      <br><br><br><p style="text-align:center">.................................................................................<br>PROFESSOR(A)</p>
    </td>
    <td class="ls-v-align-middle">
     <br><br><br><p style="text-align:center">.................................................................................<br>COORDENADOR(A)</p>
   </td>

 </tr>	
</table>
<br><br>
<p style="text-align:center">
  <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
  <?php 
  setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
  date_default_timezone_set('America/Sao_Paulo');
  echo strftime('%d de %B de %Y', strtotime('today'));
  ?>
</p>
</page>
</body>
</html>