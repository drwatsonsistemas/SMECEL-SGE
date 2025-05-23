<?php require_once('../../Connections/SmecelNovoPDO.php'); ?>
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
    <div class="ls-alert-warning"><strong>Aviso:</strong> A partir de amanhã, nossos servidores serão migrados durante o Carnaval. Durante esse período, o sistema ficará indisponível. Agradecemos a compreensão.</div>
    <div class="ls-box">
    &#128515; Seja bem-vind<?php if ($row_ProfLogado['func_sexo']==2) { echo "a"; } else { echo "o"; } ?>, <strong class="ls-text-capitalize"><?php $nomeProf = explode(" ", $row_ProfLogado['func_nome']); echo ucfirst(strtolower($nomeProf[0])); ?></strong>! 
    
    <?php if (date("m-d", strtotime($row_ProfLogado['func_data_nascimento'])) == (date("m-d"))) {  ?>
          &nbsp;&nbsp;&nbsp;&nbsp;<img src="../../img/bolo.png" width="15" > Que seu dia seja repleto de felicidades. <strong>Parabéns!</strong> 
        <?php } ?>
    
    </div>
    
    
    <div class="ls-txt-center">	
      <a href="aulas_calendario.php?target=aulas"><div class="col-md-3 col-xs-6 ls-background-success1 ls-md-space" style="background-color:#990000; color:#FFFFFF"><p><span class="ls-display-none-xs">REGISTRO DE </span>AULAS</p><h1 class="ls-ico-book"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="selecionar.php?target=frequencia&data=<?php echo date("Y-m-d"); ?>"><div class="col-md-3 col-xs-6 ls-background-info ls-md-space" style="background-color:#063; color:#FFFFFF"><p><span class="ls-display-none-xs">REGISTRAR </span>FREQUÊNCIA</p><h1 class="ls-ico-checkbox-checked"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="rendimento.php"><div class="col-md-3 col-xs-6 ls-background-primary ls-md-space" style="background-color:#C66; color:#FFFFFF"><p>RENDIMENTO</p><h1 class="ls-ico-bars"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
      <a href="planejamento_mapa.php"><div class="col-md-3 col-xs-6 ls-background-warning ls-md-space" style="background-color:#F60; color:#FFFFFF"><p>PLANEJAMENTO</p><h1 class="ls-ico-numbered-list"></h1><p></p><span class="ls-btn ls-btn-primary ls-btn-xs">ACESSAR</span></div></a>
    
    
    </div>
    
    
    <p>&nbsp;</p>    

<div class="ls-box ls-board-box">
  <header class="ls-info-header">
    <p class="ls-float-right ls-float-none-xs ls-small-info">Atualizado em <strong><?php echo date("d/m/Y"); ?></strong></p>
    <h2 class="ls-title-3">Dashboard</h2>
  </header>

  <div id="sending-stats" class="row ls-clearfix">
    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">TURMAS</h6>
        </div>

        <div class="ls-box-footer">
          <a href="grade_analitica.php" class="ls-btn ls-btn-xs">Ver horários</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Logins</h6>
        </div>

        <div class="ls-box-footer">
          <a href="logins.php" class="ls-btn ls-btn-xs">Ver logins</a>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">CURSOS</h6>
        </div>

        <div class="ls-box-footer">
          <a href="#" class="ls-btn ls-btn-xs">Ver cursos</a>
        </div>
      </div>
    </div>

	<div class="col-sm-6 col-md-3">
      <div class="ls-box">
        <div class="ls-box-head">
          <h6 class="ls-title-4">Avisos</h6>
        </div>

        <div class="ls-box-footer">
          <a href="avisos.php" class="ls-btn ls-btn-xs">Ver avisos	 </a>
        </div>
      </div>
    </div>


  </div>
  <br>
  <p class="ls-txt-right">Status: <strong class="ls-color-success">Ativo</strong> 
  <span style="clear: both;" class="ls-ico-help anti-fraud-pop" data-trigger="hover" data-ls-module="popover" data-placement="left" data-custom-class="ls-width-300" data-title="Sua conta está ativa!" data-content="Você tem vínculo com escolas e turmas."></span></p>
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

