  function noTilde(objResp) {
  var varString = new String(objResp.value);
  var stringAcentos = new String('àâêôûãõáéíóúçüÀÂÊÔÛÃÕÁÉÍÓÚÇÜ[]');
  var stringSemAcento = new String('aaeouaoaeioucuAAEOUAOAEIOUCU');
  
  var i = new Number();
  var j = new Number();
  var cString = new String();
  var varRes = "";
  
	for (i = 0; i < varString.length; i++) {
	  cString = varString.substring(i, i + 1);
		for (j = 0; j < stringAcentos.length; j++) {
		if (stringAcentos.substring(j, j + 1) == cString){
		cString = stringSemAcento.substring(j, j + 1);
		}
	  }
	  varRes += cString;
	}
	objResp.value = varRes;
	}
  $(function() {
	  $("input:text").keyup(function() {
  noTilde(this);
  });
  });
