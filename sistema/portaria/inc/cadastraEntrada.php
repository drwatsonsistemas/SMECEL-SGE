<?php

require("../conn/Conn.class.php");

$cod = $_POST['registro_id_aluno'];
$usu = $_POST['usuario'];
$sec = $_POST['secretaria'];
$esc = $_POST['escola'];
$ano = $_POST['ano_letivo'];
$tipo = "E";
$data = date('Y-m-d');
$dataExibe = date('d/m/Y');
$hora = date('H:i:s');
$horaExibe = date('H\hi');
$tamanho = strlen($cod);


//PESQUISA_ALUNO

$pesquisa = $pdo->prepare("
SELECT vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_situacao,
vinculo_aluno_ano_letivo,
aluno_id, aluno_nome, aluno_foto, turma_id, turma_nome 
FROM smc_vinculo_aluno
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE vinculo_aluno_id = :cod AND vinculo_aluno_id_escola = :esc AND vinculo_aluno_situacao = '1' AND vinculo_aluno_ano_letivo = :anoLetivo");
$pesquisa->bindParam(':cod', $_POST['registro_id_aluno']);
$pesquisa->bindParam(':esc', $_POST['escola']);
$pesquisa->bindParam(':anoLetivo', $_POST['ano_letivo']);
$pesquisa->execute();
$resultado = $pesquisa->rowCount();
$linha = $pesquisa->fetch( PDO::FETCH_ASSOC );



//ÚLTIMOS

$ultimas = $pdo->prepare("
SELECT 
catraca_id, catraca_id_matricula, catraca_data, catraca_hora, catraca_tipo,
vinculo_aluno_id, vinculo_aluno_id_aluno, vinculo_aluno_id_turma, vinculo_aluno_id_escola, vinculo_aluno_id_sec, vinculo_aluno_situacao, vinculo_aluno_ano_letivo,
aluno_id, aluno_nome, aluno_foto, turma_id, turma_nome
FROM smc_catraca
INNER JOIN smc_vinculo_aluno ON vinculo_aluno_id = catraca_id_matricula
INNER JOIN smc_aluno ON aluno_id = vinculo_aluno_id_aluno
INNER JOIN smc_turma ON turma_id = vinculo_aluno_id_turma 
WHERE catraca_data = :dataa AND vinculo_aluno_id_escola = :escc AND vinculo_aluno_ano_letivo = :anoLetivoo AND catraca_tipo = :tipoo
ORDER BY catraca_id DESC
LIMIT 0,6");
$ultimas->bindParam(':dataa', date('Y-m-d'));
$ultimas->bindParam(':escc', $_POST['escola']);
$ultimas->bindParam(':anoLetivoo', $ano = $_POST['ano_letivo']);
$ultimas->bindParam(':tipoo', $tipo);

$ultimas->execute();
$resultadoUltimas = $ultimas->rowCount();
//$linhaUltimas = $ultimas->fetch( PDO::FETCH_ASSOC );
$linhaUltimas = $ultimas->fetchAll( PDO::FETCH_ASSOC );



//VERIFICA REGISTRO DUPLICADO

$duplicado = $pdo->prepare("
SELECT 
catraca_id, catraca_id_matricula, catraca_data, catraca_tipo 
FROM smc_catraca
WHERE catraca_id_matricula = :cod AND catraca_data = :data AND catraca_tipo = :tipoE");
$duplicado->bindParam(':cod', $_POST['registro_id_aluno']);
$duplicado->bindParam(':data', date('Y-m-d'));
$duplicado->bindParam(':tipoE', $tipo);
$duplicado->execute();
$resultadoDuplicado = $duplicado->rowCount();
$linhaDuplicado = $duplicado->fetch( PDO::FETCH_ASSOC );


if ($resultado > 0) {
    $id_aluno = $linha['vinculo_aluno_id'];
    $nome_aluno = $linha['aluno_nome'];
	$nome_turma = $linha['turma_nome'];
  	if ($linha['aluno_foto']=="") {
		$foto = "semfoto.jpg"; 
	} else {
		$foto = $linha['aluno_foto'];
	}
}











//CADASTRO




if (empty($cod)) {
    echo "<div class=\"card-panel amber darken-2 center-align\"><h5>Informe o código do aluno</h5></div>";
    //die;
} elseif (!is_numeric($cod)) {
    echo "<div class=\"card-panel amber darken-2 center-align\"><h5>Código deve conter apenas dígitos numéricos</h5></div>";
    //die;
} elseif ($resultado == 0) {
    echo "<div class=\"card-panel amber darken-2 center-align\"><h5>Nenhum aluno encontrado com esse número de matrícula.</h5></div>";
    //die;
} elseif ($resultadoDuplicado > 0) {
    echo "
	
	<div class=\"card-panel green lighten-5\">
	
	
	<div class=\"row\">
      <div class=\"col s2\">
			<img src=\"../../aluno/fotos/{$foto}\" width=\"100%\" class=\"responsive-img\">
	  </div>
      <div class=\"col s10\">
			<h5 class=\"card-panel amber darken-2 center-align\">
			ENTRADA JÁ REGISTRADA NESTA DATA! <i class=\"material-icons\">report_problem</i>
			</h5>
			<p class=\"center-align\">
			<blockquote style=\"font-size:28px\">DATA: <strong>{$dataExibe}</strong> HORA: <strong>{$horaExibe}</strong></blockquote>
			</p>			
			<h6>
			<p><b>ALUNO(A):</b> {$nome_aluno}</p>
			<p><b>TURMA:</b> {$nome_turma}</p>	
			<p><b>MATRÍCULA:</b> {$cod}</p>
			<p><b>ANO LETIVO:</b> {$ano}</p>
			</h6>	
	  </div>
    </div>
	

	
	</div>
	
	
	
	
	<div class=\"card-panel amber darken-5 center-align\"><h6>Não será realizado mais de um registro de entrada por dia.</h6></div>";
    //die;
	
} else {
	
    $stmt = $pdo->prepare('INSERT INTO smc_catraca (catraca_id_matricula, catraca_id_usuario, catraca_tipo, catraca_data, catraca_hora) VALUES (:cod, :usua, :tipo, :data, :hora)');
    $stmt->execute(array(
        ':cod' => $id_aluno,
        ':usua' => $usu,
        ':tipo' => $tipo,
        ':data' => $data,
        ':hora' => $hora
    ));
	
	

	

    echo "
	
	
	<div class=\"card-panel green lighten-5\">
	
	
	<div class=\"row\">
      <div class=\"col s2\">
			<img src=\"../../aluno/fotos/{$foto}\" width=\"100%\" class=\"responsive-img\">
	  </div>
      <div class=\"col s10\">
			<h5 class=\"green-text text-darken-2 valign-wrapper center-align \">
			REGISTRO EFETUADO COM SUCESSO! <i class=\"material-icons\">check_circle</i>
			</h5>
			<p class=\"center-align\">
			<blockquote style=\"font-size:28px\">DATA: <strong>{$dataExibe}</strong> HORA: <strong>{$horaExibe}</strong></blockquote>
			</p>			
			<h6>
			<p><b>ALUNO(A):</b> {$nome_aluno}</p>
			<p><b>TURMA:</b> {$nome_turma}</p>	
			<p><b>MATRÍCULA:</b> {$cod}</p>
			<p><b>ANO LETIVO:</b> {$ano}</p>
			</h6>	
	  </div>
    </div>
	</div>
	
	
	
	
	
	
	
	
	
	
	";
    }
	
echo "

<h5 class=\"center\">Últimas entradas</h5>


<div class=\"row valign-wrapper center-align card-panel green lighten-4\">";		
foreach($linhaUltimas as $item){
	echo "<div class=\"col s2\">";
	
	if ($item['aluno_foto']=="") {
			$foto = "semfoto.jpg"; 
		} else {
			$foto = $item['aluno_foto'];
		}

   
   
   
				echo "<img src=\"../../aluno/fotos/$foto\" width=\"250\" class=\"responsive-img\">";
				
				echo "
				<small class=\"truncate\"><strong>{$item['aluno_nome']}</strong><br>
				<b class=\"red-text\">{$item['turma_nome']}</b><br>
				<b>MAT:</b> {$item['vinculo_aluno_id']}
				</small>
				";
				
				echo "<h5>".date("H\hi", strtotime($item['catraca_hora']))."</h5>";
   





	echo "</div>";
}
echo "</div>";	
	
	
	
	
	
	
	
	
	