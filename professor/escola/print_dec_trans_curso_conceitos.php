<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>
<?php include('fnc/inverteData.php'); ?>
<?php include "fnc/calculos.php"; ?>
<?php include('../funcoes/url_base.php'); ?>
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

$colname_matricula = "-1";
if (isset($_GET['c'])) {
  $colname_matricula = $_GET['c'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matricula = sprintf("SELECT

  vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_multietapa,
  aluno_id,
  aluno_cod_inep,
  aluno_cpf,
  aluno_nome,
  aluno_nome_social,
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
  aluno_sus,
  aluno_tipo_deficiencia,
  aluno_laudo,
  aluno_alergia,
  aluno_alergia_qual,
  aluno_emergencia_avisar,
  aluno_emergencia_tel1,
  aluno_emergencia_tel2,
  aluno_prof_mae,
  aluno_tel_mae,
  aluno_escolaridade_mae,
  aluno_rg_mae,
  aluno_cpf_mae,
  aluno_prof_pai,
  aluno_tel_pai,
  aluno_escolaridade_pai,
  aluno_rg_pai,
  aluno_cpf_pai,
  aluno_hash,
  aluno_foto,
  turma_id,
  turma_nome,
  turma_etapa,
  turma_turno,
  turma_ano_letivo,
  turma_multisseriada,
  etapa_id,
  etapa_id_filtro,
  etapa_nome,
  municipio_id,
  municipio_cod_ibge,
  municipio_nome,
  municipio_sigla_uf
  FROM 
  smc_vinculo_aluno
  INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno 
  INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
  INNER JOIN smc_etapa ON etapa_id = turma_etapa
  INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge

  WHERE vinculo_aluno_hash = %s", GetSQLValueString($colname_matricula, "text"));
$matricula = mysql_query($query_matricula, $SmecelNovo) or die(mysql_error());
$row_matricula = mysql_fetch_assoc($matricula);
$totalRows_matricula = mysql_num_rows($matricula);

/*
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, 
vinculo_aluno_transporte, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, 
vinculo_aluno_situacao, vinculo_aluno_datatransferencia, vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, 
vinculo_aluno_vacina_atualizada, aluno_id, aluno_nome, aluno_nascimento, aluno_foto, aluno_filiacao1, aluno_hash, turma_id, turma_nome, turma_turno 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
*/

if ($totalRows_matricula == 0) {
	header("Location:turmaListar.php?nada");
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_turma = "SELECT turma_id, turma_id_escola, turma_id_sec, turma_matriz_id, turma_nome, turma_etapa, turma_turno, turma_total_alunos, turma_ano_letivo FROM smc_turma WHERE turma_id = '$row_matricula[vinculo_aluno_id_turma]'";
$turma = mysql_query($query_turma, $SmecelNovo) or die(mysql_error());
$row_turma = mysql_fetch_assoc($turma);
$totalRows_turma = mysql_num_rows($turma);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_matriz = "SELECT matriz_id, matriz_id_secretaria, matriz_id_etapa, matriz_nome, matriz_obs, matriz_anoletivo, matriz_hash, matriz_dias_letivos, matriz_semanas_letivas, matriz_dias_semana, matriz_minutos_aula, matriz_aula_dia, matriz_criterio_avaliativo FROM smc_matriz WHERE matriz_id = '$row_turma[turma_matriz_id]'";
$matriz = mysql_query($query_matriz, $SmecelNovo) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_disciplinasMatriz = "
SELECT matriz_disciplina_id, matriz_disciplina_id_matriz, matriz_disciplina_reprova, matriz_disciplina_id_disciplina, disciplina_id, disciplina_nome 
FROM smc_matriz_disciplinas
INNER JOIN smc_disciplina ON disciplina_id = matriz_disciplina_id_disciplina 
WHERE matriz_disciplina_id_matriz = '$row_turma[turma_matriz_id]'";
$disciplinasMatriz = mysql_query($query_disciplinasMatriz, $SmecelNovo) or die(mysql_error());
$row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz);
$totalRows_disciplinasMatriz = mysql_num_rows($disciplinasMatriz);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_criteriosAvaliativos = "SELECT ca_id, ca_id_secretaria, ca_descricao, ca_qtd_periodos, ca_qtd_av_periodos, ca_nota_min_av, ca_nota_max_av, ca_calculo_media_periodo, ca_media_min_periodo, ca_arredonda_media, ca_aproxima_media, ca_min_pontos_aprovacao_final, ca_min_media_aprovacao_final, ca_nota_min_recuperacao_final, ca_detalhes, ca_rec_paralela, ca_grupo_conceito FROM smc_criterios_avaliativos WHERE ca_id = '$row_matriz[matriz_criterio_avaliativo]'";
$criteriosAvaliativos = mysql_query($query_criteriosAvaliativos, $SmecelNovo) or die(mysql_error());
$row_criteriosAvaliativos = mysql_fetch_assoc($criteriosAvaliativos);
$totalRows_criteriosAvaliativos = mysql_num_rows($criteriosAvaliativos);

$rec = 0;
if ($row_criteriosAvaliativos['ca_rec_paralela']=="S") { 
  $rec = 1;
}

$multietapa = $row_matricula['etapa_nome'];

if ($row_matricula['turma_multisseriada']==1) {

  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_Etapa = "SELECT etapa_id, etapa_id_filtro, etapa_nome, etapa_idade, etapa_limite_turma, etapa_nome_abrev, etapa_ano_ef FROM smc_etapa WHERE etapa_id = $row_Matricula[vinculo_aluno_multietapa]";
  $Etapa = mysql_query($query_Etapa, $SmecelNovo) or die(mysql_error());
  $row_Etapa = mysql_fetch_assoc($Etapa);
  $totalRows_Etapa = mysql_num_rows($Etapa);

  $multietapa = $row_Etapa['etapa_nome'];

  mysql_free_result($Etapa);

}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_GrupoConceitos = "
SELECT conceito_itens_id, conceito_itens_id_conceito, conceito_itens_descricao, conceito_itens_legenda, conceito_itens_peso 
FROM smc_conceito_itens
WHERE conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
ORDER BY conceito_itens_peso ASC
";
$GrupoConceitos = mysql_query($query_GrupoConceitos, $SmecelNovo) or die(mysql_error());
$row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos);
$totalRows_GrupoConceitos = mysql_num_rows($GrupoConceitos);

$colname_Periodo = "";
$periodo = $row_criteriosAvaliativos['ca_qtd_periodos']; 
if (isset($_GET['periodo'])) {
 $colname_Periodo = $_GET['periodo'];
 $periodo = $colname_Periodo;
} else {
	$colname_Periodo = "";
  $periodo = $row_criteriosAvaliativos['ca_qtd_periodos']; 
}
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
  <title>TRANSFERENCIA-<?php echo $row_matricula['aluno_nome']; ?>-<?php echo $row_EscolaLogada['escola_nome']; ?>-<?php echo $multietapa; ?></title>
  <meta charset="utf-8">
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" type="text/css" href="css/locastyle.css">  <link rel="stylesheet" type="text/css" href="css/preloader.css">
  <script src="js/locastyle.js"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
  <link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>                
<style>
  html{
    -webkit-print-color-adjust: exact;
  }
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
    border: dotted 1px gray;
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
  }

  table.bordasimples {
    border-collapse: collapse;
    font-size:11px;
  }
  table.bordasimples tr td {
    border:1px solid #808080;
    padding:3px;
    font-size:11px;
  }
  table.bordasimples tr th {
    border:1px solid #808080;
    padding:3px;
    font-size:11px;
  }
</style>

<body onload="self.print();">

  <!-- CONTEÚDO -->

  <page size="A4" style="padding:25px;">

    <table>
     <tr>
       <td width="20%"><?php if ($row_EscolaLogada['escola_logo']<>"") { ?><img src="../../img/logo/<?php echo $row_EscolaLogada['escola_logo']; ?>" alt="" width="100px" /><?php } else { ?><img src="../../img/brasao_republica.png" alt="" width="80px" /><?php } ?></td>
       <td width="80%">
         <p><strong><?php echo $row_EscolaLogada['escola_nome']; ?></strong></p>
         <p>INEP: <?php echo $row_EscolaLogada['escola_inep']; ?> AUT: - D.O. -</p>
         <p>ENDEREÇO: <?php echo $row_EscolaLogada['escola_endereco']; ?>, <?php echo $row_EscolaLogada['escola_num']; ?>, <?php echo $row_EscolaLogada['escola_bairro']; ?></p>
         <p><?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?> CEP: <?php echo $row_EscolaLogada['escola_cep']; ?></p>
         <p>CNPJ: <?php echo $row_EscolaLogada['escola_cnpj']; ?></p>
         <p><?php echo $row_EscolaLogada['escola_email']; ?> <?php echo $row_EscolaLogada['escola_telefone1']; ?></p>
       </td>
     </tr>
   </table>

   <div class="row">
    <div class="col-xs-12 ls-txt-center">

     <br><br><br><br><br><p><h1>CERTIFICADO DE TRANSFERÊNCIA EM CURSO</h1></p><br><br><br><br>

   </div>
 </div>

 <div class="row">
  <div class="col-xs-12">
    <p style="line-height: 180%; text-align:justify; font-size:16px;">
      Certifico que deu-se entrada nesta data, um pedido de transferência do(a) aluno(a) <strong><?php if($row_matricula['aluno_nome_social'] == ''){ echo $row_matricula['aluno_nome']; }else{ echo $row_matricula['aluno_nome_social']; } ?></strong>, nascido(a) em <strong><?php echo inverteData($row_matricula['aluno_nascimento']); ?></strong>, 
      natural de <strong><?php echo $row_matricula['municipio_nome']; ?> - <?php echo $row_matricula['municipio_sigla_uf']; ?></strong>, filho(a) de <strong><?php echo $row_matricula['aluno_filiacao1']; ?></strong><?php if ($row_matricula['aluno_filiacao2']<>"") { ?> e <strong><?php echo $row_matricula['aluno_filiacao2']; ?></strong><?php } ?>,
      residente na <strong><?php echo $row_matricula['aluno_endereco']; ?>, <?php echo $row_matricula['aluno_numero']; ?>, <?php echo $row_matricula['aluno_bairro']; ?>, <?php echo $row_matricula['aluno_municipio']; ?>-<?php echo $row_matricula['aluno_uf']; ?></strong>, 
      sendo aluno(a) regularmente matriculado(a) e frequente nesta unidade de ensino, cursando o/a <strong><?php echo $multietapa; ?></strong>, no ano letivo de <strong><?php echo $row_matricula['turma_ano_letivo']; ?></strong>, servindo o presente certificado como documento hábil para inscrição condicional à matrícula.</p>

      <br><br>

    </div>
  </div>                    


                    <!--

<?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) { echo "ls-active"; } ?>" href="conceitoEfVer.php?c=<?php echo $colname_matricula; ?>&periodo=<?php echo $i; ?>"><?php echo $i; ?>º período</a>
<?php } ?>
<a class="ls-btn-primary <?php if ($colname_Periodo == $i) { echo "ls-active"; } ?>" href="conceitoEfVer.php?c=<?php echo $colname_matricula; ?>">Anual</a>    
-->                    



<?php 
$perc = number_format(100/$totalRows_GrupoConceitos,0); 
$inicio = 0;
$parc = $perc;
$cont = 1;
$ver = 0;
?>

<table>
  <?php do { ?>
    <tr>


      <td class="ls-txt-right">
        <span class="ls-tag-warning">
          De <?php echo $inicio; ?>% até <?php echo $parc; ?>%
          <?php $cont++; ?>
          <?php if ($cont == $totalRows_GrupoConceitos) { $ver = 1; }?>     
          <?php $inicio = $parc+1; $parc = $parc + $perc + $ver; ?>
        </span>
        <td>
          <span class="ls-tag-info"><?php echo $row_GrupoConceitos['conceito_itens_legenda']; ?>: <?php echo $row_GrupoConceitos['conceito_itens_descricao']; ?></span>
        </td>
      </tr>
    <?php } while ($row_GrupoConceitos = mysql_fetch_assoc($GrupoConceitos)); ?>
  </table>	


  <?php $nn = 1; ?>
  <?php do { ?>      
    <table class="ls-table bordasimples ls-bg-header" width="100%">
      <thead>
        <tr>
          <td width="40" class="ls-txt-center"></td>
          <th class="ls-txt-center">COMPONENTES</th>
          <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
            <th width="50" class="ls-txt-center"><?php echo $i; ?>ª</th>
          <?php } ?>
          <th width="50" class="ls-txt-center">RF</th> 
          <?php do { ?>
            <?php 
            mysql_select_db($database_SmecelNovo, $SmecelNovo);
            $query_Acompanhamento = "
            SELECT quest_conc_id, quest_conc_id_matriz, quest_conc_id_etapa, quest_conc_id_comp, quest_conc_descricao, quest_conc_hash 
            FROM smc_questionario_conceitos
            WHERE quest_conc_id_matriz = '$row_turma[turma_matriz_id]' AND quest_conc_id_comp = '$row_disciplinasMatriz[disciplina_id]'
            ORDER BY quest_conc_descricao ASC
            ";
            $Acompanhamento = mysql_query($query_Acompanhamento, $SmecelNovo) or die(mysql_error());
            $row_Acompanhamento = mysql_fetch_assoc($Acompanhamento);
            $totalRows_Acompanhamento = mysql_num_rows($Acompanhamento);
            ?>


            <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
              <?php 

              $tot[$i] = 0;

              ?>
            <?php } ?>
          </tr>

        </thead>

        <tbody>
          <?php $n = 1; do { ?>
                            <!--
                              <tr>
                                <td class="ls-txt-center"><?php echo $n; $n++; ?></td>
                                <td><?php echo $row_Acompanhamento['quest_conc_descricao']; ?></td>
                                <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                                <?php 
		
		mysql_select_db($database_SmecelNovo, $SmecelNovo);
		$query_Avaliacao = "
		SELECT conc_ef_id, conc_ef_id_quest, conc_ef_id_matr, conc_ef_periodo, conc_ef_avaliac, conceito_itens_id_conceito, conceito_itens_peso, conceito_itens_legenda 
		FROM smc_conceito_ef
		LEFT JOIN smc_conceito_itens ON conceito_itens_peso = conc_ef_avaliac
		WHERE conc_ef_id_quest = '$row_Acompanhamento[quest_conc_id]' AND conc_ef_id_matr = '$row_matricula[vinculo_aluno_id]' AND conc_ef_periodo = '$i' AND conceito_itens_id_conceito = '$row_criteriosAvaliativos[ca_grupo_conceito]'
		";
		$Avaliacao = mysql_query($query_Avaliacao, $SmecelNovo) or die(mysql_error());
		$row_Avaliacao = mysql_fetch_assoc($Avaliacao);
		$totalRows_Avaliacao = mysql_num_rows($Avaliacao);
		
		
		$tot[$i] = $tot[$i]+$row_Avaliacao['conceito_itens_peso'];
		
				
		?>
                                  
                                  <td width="60" class="ls-txt-center"><?php if ($row_Avaliacao['conceito_itens_legenda']=="") { ?>
                                  -
                                  <?php } else { ?>
                                  <span class="" style="font-weight:bolder"><?php echo $row_Avaliacao['conceito_itens_legenda']; ?>
                                  <?php } ?>
                                  </span></td>
                                 
                                  
                                  
                                  
                                <?php } ?>
                              </tr>
                            -->
                          <?php } while ($row_Acompanhamento = mysql_fetch_assoc($Acompanhamento)); ?>
                          <tr>
                            <td class="ls-txt-center"><?php echo $nn; $nn++; ?></td>
                            <td class="ls-txt-left"><?php echo $row_disciplinasMatriz['disciplina_nome']; ?></td>
                            <?php 
                            $rf = 0;
                            ?>
                            <?php for ($i = 1; $i <= $row_criteriosAvaliativos['ca_qtd_periodos']; $i++) { ?>
                              <td width="50" class="ls-txt-center"><?php $res = number_format((($tot[$i]/($row_criteriosAvaliativos['ca_qtd_periodos']*$totalRows_Acompanhamento))*100),0); ?><strong><?php if ($res==0) { echo "-"; } else { echo $res."%"; } ?></strong></td>
                              <?php 
                              $rf = $rf+$res; 
                            } 
                            ?>
                            <td class="ls-txt-center" style="background-color: #EEEEEE;">
                              <?php $rf = number_format($rf/$row_criteriosAvaliativos['ca_qtd_periodos'] ,1) ?>
                              <strong><small><?php if ($res==0) { echo "-"; } else { echo $rf."%"; } ?></small></strong>
                            </td>
                          </tr>

                        <?php } while ($row_disciplinasMatriz = mysql_fetch_assoc($disciplinasMatriz)); ?>





                      </tbody>
                    </table>



                  <?php } while ($row_matricula = mysql_fetch_assoc($matricula)); ?>







                  <div class="row"><div class="col-xs-12"><p></p></div></div>

                  <div class="row"><div class="col-xs-12">
                    <br><p style="line-height: 180%; text-align:justify; font-size:16px;">O exposto acima é verdadeiro.</p>
                  </div>

                  <div class="row"><div class="col-xs-12"><p><br><br><br></p></div></div>
                  <p style="text-align:center">_________________________________________________________<br>Diretor(a) ou Secretário(a) Escolar</p>
                  <div class="row"><div class="col-xs-12"><p><br></p></div></div>
                  <p style="text-align:right">
                    <?php echo $row_EscolaLogada['sec_cidade']; ?>-<?php echo $row_EscolaLogada['sec_uf']; ?>, 
                    <?php 
                    setlocale(LC_TIME, 'pt_BR', 'utf-8', 'utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
                    echo utf8_encode(strftime('%d de %B de %Y', strtotime('today')));
                    ?>
                  </p>
                </div>
              </div>

              <div class="row"><div class="col-xs-12"><p><hr></p></div></div>

              <hr>
              <div class="row">
                <div class="col-xs-12">
                  <small>Código de certificação de validade: <strong><?php echo $row_matricula['vinculo_aluno_verificacao']; ?></strong><br>www.smecel.com.br</small>
                </div>
              </div>

            </page>


            <!-- We recommended use jQuery 1.10 or up --> 
            <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
            <script src="js/locastyle.js"></script> 

            <script type="text/javascript">


            </script>
          </body>
          </html>
          <?php
          mysql_free_result($UsuLogado);

          mysql_free_result($matricula);

          mysql_free_result($turma);

          mysql_free_result($disciplinasMatriz);

          mysql_free_result($criteriosAvaliativos);

          mysql_free_result($matriz);

          mysql_free_result($EscolaLogada);
        ?>			