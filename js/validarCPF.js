	function validarCPF( cpf ){
		var vcpf = cpf.value;
		var filtro = /^\d{3}.\d{3}.\d{3}-\d{2}$/i;
		
		if(!filtro.test(vcpf))
		{
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
	   
		vcpf = remove(vcpf, ".");
		vcpf = remove(vcpf, "-");
	    
		if(vcpf.length != 11 || vcpf == "00000000000" || vcpf == "11111111111" ||
			vcpf == "22222222222" || vcpf == "33333333333" || vcpf == "44444444444" ||
			vcpf == "55555555555" || vcpf == "66666666666" || vcpf == "77777777777" ||
			vcpf == "88888888888" || vcpf == "99999999999")
		{
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
	   }

		soma = 0;
		for(i = 0; i < 9; i++)
		{
			soma += parseInt(vcpf.charAt(i)) * (10 - i);
		}
		
		resto = 11 - (soma % 11);
		if(resto == 10 || resto == 11)
		{
			resto = 0;
		}
		if(resto != parseInt(vcpf.charAt(9))){
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
		
		soma = 0;
		for(i = 0; i < 10; i ++)
		{
			soma += parseInt(vcpf.charAt(i)) * (11 - i);
		}
		resto = 11 - (soma % 11);
		if(resto == 10 || resto == 11)
		{
			resto = 0;
		}
		
		if(resto != parseInt(vcpf.charAt(10))){
			window.alert("CPF inválido. Tente novamente.");vcpf
			return false;
		}
		
		return true;
	 }
	 
	function remove(str, sub) {
		i = str.indexOf(sub);
		r = "";
		if (i == -1) return str;
		{
			r += str.substring(0,i) + remove(str.substring(i + sub.length), sub);
		}
		
		return r;
	}
	
	/**
	   * MASCARA ( mascara(o,f) e execmascara() ) CRIADAS POR ELCIO LUIZ
	   * elcio.com.br - http://elcio.com.br/ajax/mascara/
	   */
	function mascara(o,f){
		v_obj=o
		v_fun=f
		setTimeout("execmascara()",1)
	}

	function execmascara(){
		v_obj.value=v_fun(v_obj.value)
	}

	function cpf_mask(v){
		v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
		v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o terceiro e o quarto dígitos
		v=v.replace(/(\d{3})(\d)/,"$1.$2")    //Coloca ponto entre o setimo e o oitava dígitos
		v=v.replace(/(\d{3})(\d)/,"$1-$2")   //Coloca ponto entre o decimoprimeiro e o decimosegundo dígitos
		return v
	}
