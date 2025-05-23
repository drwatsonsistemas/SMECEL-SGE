<?php
require_once('../../Connections/SmecelNovoPDO.php'); 
include "conf/session.php";
include "fnc/anti_injection.php";

// Ação do formulário
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Atualização de senha
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    try {
        // Preparar a consulta de atualização
        $updateSQL = "UPDATE smc_func SET func_senha = :func_senha WHERE func_id = :func_id";

        // Preparar o statement PDO
        $stmt = $SmecelNovo->prepare($updateSQL);
        
        // Bind dos parâmetros
        $stmt->bindParam(':func_senha', $_POST['func_senha'], PDO::PARAM_STR);
        $stmt->bindParam(':func_id', $_POST['func_id'], PDO::PARAM_INT);

        // Executar a consulta
        $stmt->execute();

        // Redirecionar após atualização
        $updateGoTo = "senha.php?alterada";
        if (isset($_SERVER['QUERY_STRING'])) {
            $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
            $updateGoTo .= $_SERVER['QUERY_STRING'];
        }
        header("Location: $updateGoTo");
        exit;
    } catch (PDOException $e) {
        // Em caso de erro na execução, mostra uma mensagem de erro
        die("Erro ao atualizar senha: " . $e->getMessage());
    }
}

// Consulta para pegar a senha do professor logado
try {
    $query_Senha = "SELECT func_id, func_senha FROM smc_func WHERE func_id = :func_id";
    $stmt_Senha = $SmecelNovo->prepare($query_Senha);
    $stmt_Senha->bindParam(':func_id', $row_ProfLogado['func_id'], PDO::PARAM_INT);
    $stmt_Senha->execute();

    // Recuperando os resultados
    $row_Senha = $stmt_Senha->fetch(PDO::FETCH_ASSOC);
    $totalRows_Senha = $stmt_Senha->rowCount();

} catch (PDOException $e) {
    // Em caso de erro na consulta
    die("Erro ao recuperar dados da senha: " . $e->getMessage());
}

?>

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
<link rel="stylesheet" type="text/css" href="css/locastyle.css"><link rel="stylesheet" href="css/sweetalert2.min.css">
</head>
<body>
<?php include_once "inc/navebar.php"; ?>
<?php include_once "inc/sidebar.php"; ?>
<main class="ls-main">
  <div class="container-fluid">
    <h1 class="ls-title-intro ls-ico-home">ALTERAÇÃO DE SENHA</h1>
    <p><a href="index.php" class="ls-btn ls-ico-chevron-left">Voltar</a></p>

    <div class="ls-alert-warning"><strong>Atenção:</strong> Não utilize caracteres especiais como:<br>
      <ul>
      <li class="ls-sm-margin-left"># - (hashtag)</li>
      <li class="ls-sm-margin-left">! - (exclamação)</li>
      </ul>
    </div>
    <br>
<form action="" method="post" id="form1" name="form1" action="<?php echo $editFormAction; ?>" class="ls-form row" onsubmit="return validarSenha();" >
  <fieldset>
    <label class="ls-label col-md-4">
      <b class="ls-label-text">NOVA SENHA</b>
      <div class="ls-prefix-group">
        <input id="nsenha" type="password" class="validate" name="func_senha" value="" required>
          <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#nsenha" href="#">
          </a>
      </div>
    </label>
    
    <label class="ls-label col-md-4">
      <b class="ls-label-text">REPITA A SENHA</b>
      <div class="ls-prefix-group">
        <input id="rsenha" type="password" class="validate" required>
          <a class="ls-label-text-prefix ls-toggle-pass ls-ico-eye" data-toggle-class="ls-ico-eye, ls-ico-eye-blocked" data-target="#rsenha" href="#">
          </a>
      </div>
    </label>
    
    
    
  </fieldset>
  
  <div class="ls-actions-btn">
    <input type="submit" value="ALTERAR" class="ls-btn">
	  <a href="index.php" class="ls-btn-danger">CANCELAR</a>  
  </div>
  
<input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="func_id" value="<?php echo $row_Senha['func_id']; ?>">  
</form>

  
    
    
  </div>
<?php //include_once "inc/footer.php"; ?>
</main>
<?php include_once "inc/notificacoes.php"; ?>
<!-- We recommended use jQuery 1.10 or up --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="js/locastyle.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/sweetalert2.min.js"></script>
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

      	<script type="text/javascript">

		
		function validarSenha(){
        nsenha = document.form1.nsenha.value;
        rsenha = document.form1.rsenha.value;
        if (nsenha != rsenha){ 
		
		
			Swal.fire({
			  //position: 'top-end',
			  icon: 'warning',
			  title: 'As senhas informadas não são iguais. Verifique o que digitou.',
			  showConfirmButton: true
			  //timer: 1500
			})
		
             return false;
        }
        return true;
 }
		
	</script>

<?php if (isset($_GET["alterada"])) { ?>
<script type="application/javascript">

Swal.fire({
  //position: 'top-end',
  icon: 'success',
  title: 'Senha alterada com sucesso',
  showConfirmButton: false,
  timer: 1500
})

</script>
  <?php } ?>
  
  
</body>
</html>