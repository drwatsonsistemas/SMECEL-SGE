$(document).foundation();

//Background randomico
$(document).ready(function(){
	var upperLimit = 208;
	var randomNum = Math.floor((Math.random() * upperLimit) + 1);    
 $("body").css("background","url('imagens/" + randomNum + ".jpg') no-repeat center center fixed");//<--changed path
 $("body").css("-webkit-background-size","cover");
 $("body").css("-moz-background-size","cover");
 $("body").css("-o-background-size","cover");
 $("body").css("background-size","cover");
 $("body").css("-webkit-transition","background 4s ease-in-out");
 $("body").css("-moz-transition","background 4s ease-in-out");
 $("body").css("-o-transition","background 4s ease-in-out");
 $("body").css("-ms-transition","background 4s ease-in-out");
 $("body").css("transition","background 4s ease-in-out");
 $("body").css("-webkit-backface-visibility","hidden");
});

    $(document).ready(function(){
        $("#btnEntrar").click(function(event){
            var envio = $.post("login.php", { 
            usuario: $("#usuario").val(), 
            senha: $("#senha").val() 
            })
            envio.done(function(data) {
                $("#resultado").html(data);
            })
            envio.fail(function() { alert("Erro na requisição"); }) 
        });
    });

//refresh
    window.onload = Refresh;
    function Refresh() {
        setTimeout("refreshPage();", 90000);
    }
    function refreshPage() {
        window.location = location.href;
    }
// refresh
