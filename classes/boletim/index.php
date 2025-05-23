<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>VISUALIZAR BOLETIM</title>
<style>

 body {
        margin: 0px
    }
    .container {
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center
    }
    .box {
        width: 300px;
        height: auto;
        background: #fff;
    }
	.caixa {
		display: block;
		passing: 10px;
	}

</style>

</head>

<body>

<div class="container">
    <div class="box">


<div class="caixa">
<h3>CÓDIGO</h3>
<form action="impressaoboletim.php" method="get" name="form">
<input name="c" type="text" value="" maxlength="19" size="25" class="validacao" />
<input type="submit" value="enviar" />
</form>
</div>

    </div>
</div>


</body>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="../js/jquery.mask.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
  $('.validacao').mask('AAAA-AAAA-AAAA-AAAA');
 

});
</script>


</html>