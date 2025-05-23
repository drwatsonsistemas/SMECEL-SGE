<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include "conf/session.php"; ?>
<!DOCTYPE html>
<html class="<?php echo TEMA; ?>" lang="pt-br">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
<title>PROFESSOR | <?php echo $row_ProfLogado['func_nome']; ?> | SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">Ano letivo <?php echo $row_AnoLetivo['ano_letivo_ano']; ?></h1>
    
    
    <div class="ls-box">
    &#128515; Seja bem-vind<?php if ($row_ProfLogado['func_sexo']==2) { echo "a"; } else { echo "o"; } ?>, <strong class="ls-text-capitalize"><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']); echo ucfirst(strtolower($nomeProf[0])); ?></strong>!
    </div>
    
    <div class="row1 ls-txt-center">	
      <div class="col-md-3 col-xs-6 ls-background-success ls-md-space"><p><span class="ls-display-none-xs">REGISTRO DE </span>AULAS</p><h1 class="ls-ico-book"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="aulas_calendario.php?target=aulas">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-info ls-md-space"><p><span class="ls-display-none-xs">REGISTRAR </span>FREQUÊNCIA</p><h1 class="ls-ico-checkbox-checked"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="selecionar.php?target=frequencia&data=<?php echo date("Y-m-d"); ?>">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-warning ls-md-space"><p>PLANEJAMENTO</p><h1 class="ls-ico-numbered-list"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="#">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-primary ls-md-space"><p>RENDIMENTO</p><h1 class="ls-ico-bars"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="selecionar.php?target=rendimento&data=<?php echo date("Y-m-d"); ?>">ACESSAR</a></div>
    </div>
        
    <div class="row1 ls-txt-center">	
      <div class="col-md-3 col-xs-6 ls-background-success ls-md-space" style="background-color:#099; color:#FFF"><p>CALENDÁRIO<span class="ls-display-none-xs"> LETIVO</span></p><h1 class="ls-ico-book"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="calendario.php">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-info ls-md-space" style="background-color:#960; color:#FFF"><p>#</p><h1 class="ls-ico-checkbox-checked"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="selecionar.php?target=frequencia&data=<?php echo date("Y-m-d"); ?>">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-warning ls-md-space" style="background-color:#999; color:#FFF"><p>#</p><h1 class="ls-ico-numbered-list"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="#">ACESSAR</a></div>
      <div class="col-md-3 col-xs-6 ls-background-primary ls-md-space" style="background-color:#9C9; color:#FFF"><p>#</p><h1 class="ls-ico-bars"></h1><p></p><a class="ls-btn ls-btn-primary ls-btn-xs" href="#">ACESSAR</a></div>
    </div>
        

    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>

<script>


            jQuery('#form_cadastro').submit(function () {
                event.preventDefault();
                var dados = jQuery(this).serialize();
				
			
				$(".preload").css('display', 'block');
				//document.getElementByClass(".preload").style.display = "block";

                jQuery.ajax({
                    type: "POST",
                    url: "crud/model/insert.php",
                    data: dados,
                    success: function (data)
                    {
                        $("input").prop('disabled', true);
                        $("select").prop('disabled', true);
                        $("textarea").prop('disabled', true);
						 

                        $(".preload").css('display', 'none');
						$("#linkResultado").html(data);
						

                        setTimeout(function () {
                            $("#gerar_link").each(function () {
                                this.reset();
                            });

                            $("input").prop('disabled', false);
                            $("select").prop('disabled', false);
                            $("textarea").prop('disabled', false);
             
                        }, 2000);
                    }
                });

                return false;
            });

        </script> 

<script type="application/javascript">
/*
Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Tudo certo por aqui',
  showConfirmButton: false,
  timer: 1500
})
*/
</script>
</body>
</html>