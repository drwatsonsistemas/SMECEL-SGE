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
//return $numero[0];
//return $v + $add;
}

 
function exibeTraco($nota) {
	if ($nota=="") {
		echo "-";
		} else {
			if ($nota < 6) {
			echo "<span style='color:red;'>".$nota."</span>";
			} else {
				echo $nota;
				}
			}
			return;
	}

function mediaUnidade($nota1,$nota2,$nota3) {
	$media = ($nota1+$nota2+$nota3)/3;
	$media = round($media,1);
	//$media_explode = explode('.', $media);
	
	$media = arredonda($media);
	
	if ($media==0) { 
		echo "-"; 
		} else {
			if($media < 6) { 
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

function mediaCurso($media) {
	
	$mc = $media/4;
	$mc = round($mc,1);
	$mc = arredonda($mc);
	
	if ($mc=="-") {
		$mc = "-";
	} else {
	if ($mc < 6) {
		$mc = "<span style='color:red;'>".number_format($mc, 1, '.', '')."</span>";
	} else {
		$mc = number_format($mc, 1, '.', '');
	}
	}
	return $mc;
	
}

function avaliacaoFinal($nota) {
	if ($nota=="") {
		$nota = "-";
		} else {
			if ($nota < 5.0) {
			$nota = "<span style='color:red;'>".$nota."</span>";
			} else {
				$nota = $nota;
				}
			}
			return $nota;
	}


function resultadoFinal_bkp($mc,$af) {
	
	if (($mc=="-")&&($af=="-")) {
		echo "-";
	} else {
	
	if ($mc >= 6.0) {
		echo "APR";
	} else {
			
			if ($af >= 5.0) {
				echo "APR";
			} else {
				echo "<span style='color:red;'>CON</span>";
			}
		
	
	}
	}
	
	
	return;
	
}	
 
function resultadoFinal($mc,$af) {
	
	if (($mc=="-")&&($af=="-")) {
		echo "-";
	} else {
	
	if ($mc >= 6.0) {
		echo "APR";
	} else {
			
			if ($af >= 5.0) {
				echo "APR";
			} else if (($af == "-")) {
				echo "-";
			} else {
				echo "<span style='color:red;'>CON</span>";
			}
		
	
	}
	}
	
	
	return;
	
}	
	


 
 ?>