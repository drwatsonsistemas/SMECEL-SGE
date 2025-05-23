<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<?php include "../funcoes/anoLetivo.php"; ?>
<?php include "../funcoes/inverteData.php"; ?>
<?php include "../funcoes/idade.php"; ?>
<?php
$colname_Alunos = "-1";
if (isset($_GET['turma'])) {
  $colname_Alunos = $_GET['turma'];
}

$colname_Escola = "-1";
if (isset($_GET['escola'])) {
  $colname_Escola = $_GET['escola'];
}


mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Alunos = sprintf("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_ano_letivo, vinculo_aluno_transporte, 
vinculo_aluno_ponto_id, vinculo_aluno_data, vinculo_aluno_hash, vinculo_aluno_verificacao, vinculo_aluno_boletim, vinculo_aluno_situacao, vinculo_aluno_datatransferencia, 
vinculo_aluno_da_casa, vinculo_aluno_historico_transferencia, vinculo_aluno_vacina_atualizada, vinculo_aluno_vacina_data_retorno, vinculo_aluno_conselho, 
vinculo_aluno_conselho_parecer, vinculo_aluno_internet, vinculo_aluno_multietapa, vinculo_aluno_rel_aval, 
aluno_id, aluno_nome, aluno_nascimento, aluno_sexo, turma_id, turma_nome, turma_turno,
CASE aluno_sexo
WHEN '1' THEN 'M'
WHEN '2' THEN 'F'
END AS aluno_sexo_legenda, 
CASE turma_turno 
WHEN 0 THEN 'INTEGRAL' 
WHEN 1 THEN 'MATUTINO' 
WHEN 2 THEN 'VESPERTINO' 
WHEN 3 THEN 'NOTURNO' 
END AS turma_turno_nome,
CASE vinculo_aluno_situacao
WHEN 1 THEN '<span class=\"ls-color-success\">MATRIC</span>'
WHEN 2 THEN '<span class=\"ls-color-warning\">TRANSF</span>'
WHEN 3 THEN '<span class=\"ls-color-warning\">DESIST</span>'
WHEN 4 THEN '<span class=\"ls-color-warning\">FALECI</span>'
WHEN 5 THEN '<span class=\"ls-color-warning\">OUTROS</span>'
END AS vinculo_aluno_situacao 
FROM smc_vinculo_aluno 
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma
WHERE vinculo_aluno_id_turma = %s AND vinculo_aluno_situacao = '1'
ORDER BY aluno_nome ASC
", GetSQLValueString($colname_Alunos, "int"));
$Alunos = mysql_query($query_Alunos, $SmecelNovo) or die(mysql_error());
$row_Alunos = mysql_fetch_assoc($Alunos);
$totalRows_Alunos = mysql_num_rows($Alunos);

function idadeTempo ($idade,$data) {
	if ($idade <> "") {
    // Declara a data! :P
    $data = $idade;
    // Separa em dia, mês e ano
    list($ano, $mes, $dia) = explode('-', $data);
    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
	} else {
		$idade = "-";
	}
    return $idade;
}

function imc ($peso,$altura,$sexo,$idade) {
	$imc = $peso/($altura*$altura);
	return number_format($imc, 1);
	}

function idadeMeses ($idade, $pesagem) {
	$DataInicial = getdate(strtotime($idade));
	$DataFinal = getdate(strtotime($pesagem));
	$Dif = ($DataFinal[0] - $DataInicial[0]) / 86400;
	return $meses = round($Dif/30);
}

$semPeso = 0;
$magrezaAcentuada = 0;
$magreza = 0;
$eutrofia = 0;
$riscoDeSobrepeso = 0;
$sobrepeso = 0;
$obesidade = 0;
$obesidadeGrave = 0;


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
<title>SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="css/locastyle.css">
<link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Alunos - Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>

        <div class="ls-box">
          <h5 class="ls-title-3"><?php echo $row_Alunos['turma_nome']; ?> - <?php echo $row_Alunos['turma_turno_nome']; ?></h5>
        </div>
   
            <div class="ls-group-btn ls-group-active">
              <a href="alunos.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-ico-chevron-left">&nbsp;</a>
              <a href="saude_bucal.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-info">S. BUC.</a>
              <a href="consumo_alimentar.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-success">C. ALIM.</a>
              <a href="antropometria.php?turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>" class="ls-btn ls-color-warning ls-active">ANTROP.</a>
            </div>
            
            <h4 class="ls-title-4 ls-txt-center">ANTROPOMETRIA</h4>
    
    <table class="ls-table ls-sm-space">
    <thead>
      <tr>
        <th class="ls-txt-center ls-display-none-xs" width="40"></th>
        <th class="ls-txt-center ls-display-none-xs" width="60">MAT</th>
        <th>ALUNO</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">SEXO</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">IDADE</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">MESES</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">PESO</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">ALT</th>
        <th class="ls-txt-center ls-display-none-xs" width="60">IMC</th>
        <th class="ls-txt-center ls-display-none-xs" width="180">RESULTADO</th>
        <th class="ls-txt-center" width="40"></th>
      </tr>
      </thead>
      <tbody>
      <?php $num = 1; do { ?>
        <tr>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $num; $num++; ?></td>
          <td class="ls-txt-center ls-display-none-xs"><?php echo $row_Alunos['vinculo_aluno_id']; ?></td>
          <td><a class="ls-ico-upload ls-ico-right" href="antropometria_lancar.php?aluno=<?php echo $row_Alunos['vinculo_aluno_hash']; ?>&turma=<?php echo $colname_Alunos; ?>&escola=<?php echo $colname_Escola; ?>"><?php echo $row_Alunos['aluno_nome']; ?></a></td>
          
          
          <?php
		  
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_antropometria = sprintf("SELECT * FROM sms_pse_antropometria WHERE antrop_id_matricula = %s ORDER BY antrop_id DESC", GetSQLValueString($row_Alunos['vinculo_aluno_id'], "int"));
			$antropometria = mysql_query($query_antropometria, $SmecelNovo) or die(mysql_error());
			$row_antropometria = mysql_fetch_assoc($antropometria);
			$totalRows_antropometria = mysql_num_rows($antropometria);
			

			
			$imc = "-";
			
			if ($totalRows_antropometria > 0) {
				
				
			$imc = imc($row_antropometria['antrop_peso'],$row_antropometria['antrop_altura'],1,1); 
			
			
			$idade_anos = idadeTempo($row_Alunos['aluno_nascimento'],$row_antropometria['antrop_data']);
			$idade_meses = idadeMeses($row_Alunos['aluno_nascimento'],$row_antropometria['antrop_data']);		
			
			mysql_select_db($database_SmecelNovo, $SmecelNovo);
			$query_tabela_imc = "
			SELECT imc_id, imc_meses, imc_sexo, 
			imc_L, imc_M, imc_S, 
			imc_P01, imc_P1, imc_P3, imc_P5, imc_P10, imc_P15, imc_P25, imc_P50, imc_P75, imc_P85, imc_P90, imc_P95, imc_P97, imc_P99, imc_P999, 
			imc_SD4neg, imc_SD3neg, imc_SD2neg, imc_SD1neg, imc_SD0, imc_SD1, imc_SD2, imc_SD3, imc_SD4 
			FROM sms_pse_imc
			WHERE imc_meses = '$idade_meses' AND imc_sexo = '$row_Alunos[aluno_sexo]'";
			$tabela_imc = mysql_query($query_tabela_imc, $SmecelNovo) or die(mysql_error());
			$row_tabela_imc = mysql_fetch_assoc($tabela_imc);
			$totalRows_tabela_imc = mysql_num_rows($tabela_imc);
			
			if ($idade_meses < 60) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Risco sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
				
			} else if (($idade_meses >= 60) && ($idade_meses <= 120)){
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;
					
				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Risco sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
					
					
			} else if (($idade_meses >= 121) && ($idade_meses < 240)) {
				
				if ($imc < $row_tabela_imc['imc_P01']) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Magr. acentuada &nbsp;</span>";
					$magrezaAcentuada++;

				} else if (($imc >= $row_tabela_imc['imc_P01']) && ($imc < $row_tabela_imc['imc_P3']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Magreza &nbsp;</span>";
					$magreza++;
					
				} else if (($imc >= $row_tabela_imc['imc_P3']) && ($imc <= $row_tabela_imc['imc_P85']) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrofia &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc > $row_tabela_imc['imc_P85']) && ($imc <= $row_tabela_imc['imc_P97']) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$riscoDeSobrepeso++;
					
				} else if (($imc > $row_tabela_imc['imc_P97']) && ($imc <= $row_tabela_imc['imc_P999']) ) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				} else if ($imc > $row_tabela_imc['imc_P999']) {
					$res = "<span class=\"ls-color-black\">&nbsp; Obesidade grave&nbsp;</span>";
					$obesidadeGrave++;
					
				}
				
				
				} else if (($idade_meses >= 240) && ($idade_meses < 720)) {
					
				if ($imc < 18.5) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Baixo peso &nbsp;</span>";
					$magreza++;

				} else if (($imc >= 18.5) && ($imc < 25) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrófico &nbsp;</span>";
					$eutrofia++;
					
				} else if (($imc >= 25) && ($imc < 30) ) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				} else if ($imc >= 30) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Obesidade &nbsp;</span>";
					$obesidade++;
					
				}
				
				
				} else if ($idade_meses >= 720) {
					
					
					
				if ($imc <= 22) {
					$res = "<span class=\"ls-color-danger\">&nbsp; Baixo peso &nbsp;</span>";
					$magreza++;

				} else if (($imc > 22) && ($imc < 27) ) {
					$res = "<span class=\"ls-color-success\">&nbsp; Eutrófico &nbsp;</span>";
					$eutrofia++;
					
				} else if ($imc >= 27) {
					$res = "<span class=\"ls-color-warning\">&nbsp; Sobrepeso &nbsp;</span>";
					$sobrepeso++;
					
				}
					
						
				}
	 
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$row_Alunos['aluno_sexo_legenda']."</td>";						
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$idade_anos."</td>";		
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$idade_meses."</td>";		
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$row_antropometria['antrop_peso']." kg</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$row_antropometria['antrop_altura']." m</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$imc."</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">".$res."</td>";
				echo "<td class=\"ls-txt-center\"><span class=\"ls-ico-checkmark ls-color-success\"></span></td>";
				
			} else {
				
				$semPeso++;
				
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";						
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\">-</td>";
				echo "<td class=\"ls-txt-center ls-display-none-xs\"></td>";
				echo "<td class=\"ls-txt-center\"></td>";
				}
		  
		  ?>
          
          
        </tr>
        <?php } while ($row_Alunos = mysql_fetch_assoc($Alunos)); ?>
        </tbody>
    </table>
    
    
    <p>&nbsp;</p>
    
    
  <table class="ls-table">
  <tr><td>SEM PESAGEM: <?php echo $semPeso; ?></td></tr>
  </table>
  
  
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Situação', 'Quantidade'],
          ['Magreza Acentuada (<?php echo $magrezaAcentuada; ?>)', <?php echo $magrezaAcentuada; ?>],
          ['Magreza (<?php echo $magreza; ?>)', <?php echo $magreza; ?>],
          ['Eutrofia (<?php echo $eutrofia; ?>)', <?php echo $eutrofia; ?>],
          ['Risco de sobrepeso (<?php echo $riscoDeSobrepeso; ?>)', <?php echo $riscoDeSobrepeso; ?>],
          ['Sobrepeso (<?php echo $sobrepeso; ?>)', <?php echo $sobrepeso; ?>],
          ['Obesidade (<?php echo $obesidade; ?>)', <?php echo $obesidade; ?>],
          ['Obesidade grave (<?php echo $obesidadeGrave; ?>)', <?php echo $obesidadeGrave; ?>]
        ]);

        var options = {
          title: 'Avaliação antropométrica',
		  pieHole: 0.4,
        };
		


        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
    
    <div id="piechart" style="width: 100%; height: 500px;"></div>
    
    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
<?php if (isset($_GET["lancado"])) { ?>
<script type="application/javascript">

Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Lançamento realizado',
  showConfirmButton: false,
  timer: 1500
})

</script>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($Alunos);
?>
