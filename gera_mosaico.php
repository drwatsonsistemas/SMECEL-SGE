<?php 


$diretoria = "aluno/fotos/"; // esta linha não precisas é só um exemplo do conteudo que a variável vai ter

// selecionar só .jpg
$imagens = glob($diretoria . "*.jpg");

shuffle($imagens);

$num = 0;
// fazer echo de cada imagem
foreach($imagens as $imagem){

    //shuffle($imagem);

$aleatorio = rand(5,5);

  echo '<img style="float:left" src="'.$imagem.'" width="'.$aleatorio.'%"/>';

  $num++;

	if ($num == 1) {
		break;
	}

}




?>