<?php 

function arredonda($valor) 
{
	
$v = $valor;	
$numero = explode(".", $valor);//separa a string no ponto
       // analisa a nota e aplica a arredondamento

if (($valor >= $numero[0].".25") && ($valor < $numero[0].".75")) {
//$numero[0]++;
$numero[1]=5;	
} else if ($valor < $numero[0].".25") {
$numero[1]=0;	
	} else {
	$numero[0]++;
	$numero[1]=0;	
	}

	return $numero[0].'.'.$numero[1];
}

function mediaUnidade($nota,$arredonda,$aproxima,$mediaMin,$calculo,$qtdav,$digitos) {
	
	if ($calculo=="MEDIA") {
	$media = ($nota)/$qtdav;
	} else {
		$media = ($nota);
		}
	
	if ($arredonda=="S") {	   
	$media = arredonda($media);
	}

	if ($aproxima=="S") {
	$media = round($media,$digitos);
	}
	
	if ($media==0) { 
		echo "-"; 
		} else {
			if($media < $mediaMin) { 
			echo "<span style='color:red;'>".number_format($media, $digitos, '.', '')."</span>"; 
			} else {
				echo "<span style='color:blue;'>".number_format($media, $digitos, '.', '')."</span>";
				}
				}; 
		
	return $media;
	}

function exibeTraco($nota,$min,$ca_digitos) {
	if ($nota=="") {
		echo "-";
		} else {
			$nota = number_format($nota, $ca_digitos);
			if ($nota < $min) {
			echo "<span style='color:red;'>".$nota."</span>";
			} else {
				echo "<span style='color:black;'>".$nota."</span>";
				}
			}
			return;
	}

function totalPontos($m1,$digitos) {
	
	$tp = ($m1);
	if ($tp==0) {
		echo $tp = "-";	
	} else {
	echo $tp = number_format($tp, $digitos, '.', '');
	}
	return $tp;
}

function mediaCurso($media,$arredonda,$aproxima,$min,$qtdper,$digitos) {
	
	$mc = $media/$qtdper;
	
	if ($aproxima=="S") {
	 $mc = arredonda($mc);
	}
	
	if ($arredonda=="S") {
	 $mc = round($mc,$digitos);
	}
		
	if ($mc=="-") {
		echo $mc = "-";
	} else {
	if ($mc < $min) {
		echo $mc = "<span style='color:red;'>".number_format($mc, $digitos, '.', '')."</span>";
	} else {
		echo $mc = number_format($mc, $digitos, '.', '');
	}
	}
	return $mc;	
}

function avaliacaoFinal($nota,$min,$ca_digitos) {
	if ($nota=="") {
		$nota = "-";
		} else {
			$nota = number_format($nota, $ca_digitos);
			if ($nota < $min) {
			$nota = "<span style='color:red;'>".$nota."</span>";
			} else {
				$nota = $nota;
				}
			}
			return $nota;
	}

function resultadoFinal($mc,$af,$notaMin,$medMin,$digitos) {
	
	$mc = number_format((float)$mc, $digitos, '.', '');		
	$af = number_format((float)$af, $digitos, '.', '');	
	
	if (($mc=="-")&&($af=="-")) {
		$nota = "-";
	} else {
	if ($mc >= $medMin) {
		$nota = "APR";
	} else {
			if ($af >= $notaMin) {
				$nota = "APR";
			} else if (($af == "-")) {
				$nota = "-";
			} else {
				$nota = "<span style='color:red;'>CON</span>";
			}
	}
	}
	return $nota;
}
	