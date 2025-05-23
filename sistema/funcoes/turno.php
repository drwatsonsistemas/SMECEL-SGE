<?php 
function turno ($cod) {
		if ($cod == "1"){
			$turno = " / MAT";
		}else if ($cod == "2") {
			$turno = " / VESP";
		}else if ($cod == "3") {
			$turno = " / NOT";
		} else {
			$turno = "";
		}
		return $turno;
}
?>