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

 
function exibeTraco($nota,$min) {
	if ($nota=="") {
		echo "-";
		} else {
			if ($nota < $min) {
			echo "<span style='color:red;'>".$nota."</span>";
			} else {
				echo $nota;
				}
			}
			return;
	}

function exibeTracoBloco($nota) {
	if ($nota=="") {
		echo "-";
		} else {
			
				echo $nota;
				
			}
			return;
	}
	
function mediaUnidade($nota1,$nota2,$nota3,$arredonda,$aproxima,$mediaMin,$calculo) {
	
	if ($calculo=="MEDIA") {
	$media = ($nota1+$nota2+$nota3)/3;
	} else {
		$media = ($nota1+$nota2+$nota3);
		}
	
	
	if ($aproxima=="S") {
	$media = round($media,1);
	}
	
	if ($arredonda=="S") {	   
	$media = arredonda($media);
	}
	
	if ($media==0) { 
		echo "-"; 
		} else {
			if($media < $mediaMin) { 
			echo "<span style='color:red;'>".number_format($media, 1, '.', '')."</span>"; 
			} else {
				echo number_format($media, 1, '.', '');
				}
				}; 
		
	return $media;
	}

function totalPontos($m1, $m2, $m3, $m4) {
	
	$tp = ($m1+$m2+$m3+$m4);
	if ($tp==0) {
		echo $tp = "-";	
	} else {
	echo number_format($tp, 1, '.', '');
	}
	return $tp;
	
}

function mediaCurso($media,$arredonda,$aproxima,$min) {
	
	$mc = $media/4;
	
	if ($arredonda=="S") {
	$mc = round($mc,1);
	}
	
	if ($aproxima=="S") {
	$mc = arredonda($mc);
	}
	
	if ($mc=="-") {
		$mc = "-";
	} else {
	if ($mc < $min) {
		$mc = "<span style='color:red;'>".number_format($mc, 1, '.', '')."</span>";
	} else {
		$mc = number_format($mc, 1, '.', '');
	}
	}
	return $mc;
	
}

function avaliacaoFinal($nota,$min) {
	if ($nota=="") {
		$nota = "-";
		} else {
			if ($nota < $min) {
			$nota = "<span style='color:red;'>".$nota."</span>";
			} else {
				$nota = $nota;
				}
			}
			return $nota;
	}
	
function avaliacaoFinalLancamentoBoletim($nota,$min) {
	if ($nota=="") {
		$nota = "-";
		} else {
			if ($nota < $min) {
			//$nota = "<span style='color:red;'>".$nota."</span>";
			$nota = $nota;
			} else {
				$nota = $nota;
				}
			}
			return $nota;
	}


function resultadoFinal_bkp($mc,$af) {
	
	if (($mc=="-")&&($af=="-")) {
		$nota = "-";
	} else {
	
	if ($mc >= 6.0) {
		$nota = "APR";
	} else {
			
			if ($af >= 6.0) {
				$nota = "APR";
			} else {
				$nota = "<span style='color:red;'>CON</span>";
			}
		
	
	}
	}
	
	return $nota;
	
}	
 
function resultadoFinal($mc,$af,$notaMin,$medMin) {
	
	$mc = number_format((float)$mc, 1, '.', '');		
	$af = number_format((float)$af, 1, '.', '');	
	
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

function resultadoFinalTp($tp,$af,$notaMin,$minPontos) {



if (($tp=="-")&&($af=="-")) {
	$nota = "-";
	} else {
	$pontos = number_format($tp, 1, '.', '');	
	if ($pontos >= $minPontos) {
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
	


 
 ?>