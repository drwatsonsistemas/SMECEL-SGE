<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php include('../../sistema/funcoes/inverteData.php'); ?>

<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php?saiu";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "6";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php?err";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_AlunoLogado = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_AlunoLogado = $_SESSION['MM_Username'];
}
mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_AlunoLogado = sprintf("
SELECT aluno_id, aluno_cod_inep, aluno_cpf, aluno_nome, aluno_nascimento, 
aluno_filiacao1, aluno_filiacao2, 
CASE aluno_sexo
WHEN 1 THEN 'MASCULINO'
WHEN 2 THEN 'FEMININO'
END AS aluno_sexo, 
CASE aluno_raca
WHEN 1 THEN 'BRANCA'
WHEN 2 THEN 'PRETA'
WHEN 3 THEN 'PARDA'
WHEN 4 THEN 'AMARELA'
WHEN 5 THEN 'INDÍGENA'
WHEN 6 THEN 'NÃO DECLARADA'
END AS aluno_raca, 
CASE aluno_nacionalidade
WHEN 1 THEN 'BRASILEIRA'
WHEN 2 THEN 'BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO'
WHEN 3 THEN 'EXTRANGEIRO'
END AS aluno_nacionalidade, 
aluno_uf_nascimento, aluno_municipio_nascimento, aluno_municipio_nascimento_ibge,  
CASE aluno_aluno_com_deficiencia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_aluno_com_deficiencia, 
aluno_nis, aluno_identidade, aluno_emissor, aluno_uf_emissor, aluno_data_espedicao, 
CASE aluno_tipo_certidao
WHEN 1 THEN 'MODELO ANTIGO'
WHEN 2 THEN 'MODELO NOVO'
END AS aluno_tipo_certidao, 
aluno_termo, aluno_folhas, aluno_livro, aluno_emissao_certidao, 
aluno_uf_cartorio, aluno_mucicipio_cartorio, aluno_nome_cartorio, aluno_num_matricula_modelo_novo, 
CASE aluno_localizacao
WHEN 1 THEN 'ZONA URBANA'
WHEN 2 THEN 'ZONA RURAL'
END AS aluno_localizacao, 
aluno_cep, aluno_endereco, aluno_numero, aluno_complemento, aluno_bairro, aluno_uf, 
aluno_municipio, aluno_telefone, aluno_celular, aluno_email, aluno_sus, aluno_tipo_deficiencia, 
CASE aluno_laudo
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_laudo, 
CASE aluno_alergia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_alergia, 
aluno_alergia_qual, 
CASE aluno_destro
WHEN 1 THEN 'DESTRO'
WHEN 2 THEN 'CANHOTO'
END AS aluno_destro, 
aluno_emergencia_avisar, aluno_emergencia_tel1, aluno_emergencia_tel2, 
aluno_prof_mae, aluno_tel_mae, 
CASE aluno_escolaridade_mae
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_mae, 
aluno_rg_mae, aluno_cpf_mae, aluno_prof_pai, aluno_tel_pai, 
CASE aluno_escolaridade_pai
WHEN 1 THEN 'NÃO ESTUDOU'
WHEN 2 THEN 'CONCLUIU O FUNDAMENTAL'
WHEN 3 THEN 'CONCLUIU O MÉDIO'
WHEN 4 THEN 'CONCLUIU O SUPERIOR'
END AS aluno_escolaridade_pai, 
aluno_rg_pai, aluno_cpf_pai, aluno_hash, 
CASE aluno_recebe_bolsa_familia
WHEN 1 THEN 'SIM'
WHEN 2 THEN 'NÃO'
END AS aluno_recebe_bolsa_familia,
municipio_id,
municipio_cod_ibge,
municipio_nome,
municipio_sigla_uf 
FROM smc_aluno
INNER JOIN smc_municipio ON municipio_cod_ibge = aluno_municipio_nascimento_ibge 
WHERE aluno_id = %s", GetSQLValueString($colname_AlunoLogado, "int"));
$AlunoLogado = mysql_query($query_AlunoLogado, $SmecelNovo) or die(mysql_error());
$row_AlunoLogado = mysql_fetch_assoc($AlunoLogado);
$totalRows_AlunoLogado = mysql_num_rows($AlunoLogado);

if($totalRows_AlunoLogado=="") {
	header("Location:../index.php?loginErr");
}
?>
<!DOCTYPE html>
  <html lang="pt-br">
    <head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $row_AlunoLogado['aluno_nome']; ?> - SMECEL - Secretaria Municipal de Educação, Cultura, Esporte e Lazer</title>
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
      <link type="text/css" rel="stylesheet" href="../css/app.css"  media="screen,projection"/>
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
    <body>
    
  <nav class="blue darken-4" role="navigation">
    <div class="nav-wrapper container">
      <a id="logo-container" href="index.php" class="brand-logo"><i class="material-icons">home</i> SMECEL</a>
      <ul class="right hide-on-med-and-down">
	    <li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="dados.php">MEUS DADOS</a></li>
		<li><a class="waves-effect waves-light btn-flat white-text modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
      </ul>

      <ul id="nav-mobile" class="sidenav">
		<li><a class="waves-effect waves-light btn-flat modal-trigger" href="dados.php">MEUS DADOS</a></li>
        <li><a class="waves-effect waves-light btn-flat modal-trigger" href="<?php echo $logoutAction ?>"><i class="material-icons left">lock_outline</i>SAIR</a></li>
      </ul>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>
    
   <div class="container">
    
    
     
    <div class="section">
    <div class="row">
     <div class="col s12">
     
     <h4>DADOS DO ALUNO</h4>
     <table class="responsive-table">
        <tbody>
          <tr>
            <td><small>ALUNO</small><br><?php echo $row_AlunoLogado['aluno_nome']; ?>&nbsp;</td>
            <td><small>NASCIMENTO</small><br><?php echo inverteData($row_AlunoLogado['aluno_nascimento']); ?>&nbsp;</td>
            <td><small>CPF</small><br><?php echo $row_AlunoLogado['aluno_cpf']; ?>&nbsp;</td>
            <td><small>SEXO</small><br><?php echo $row_AlunoLogado['aluno_sexo']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>FILIAÇÃO 1</small><br><?php echo $row_AlunoLogado['aluno_filiacao1']; ?>&nbsp;</td>
            <td><small>FILIAÇÃO 2</small><br><?php echo $row_AlunoLogado['aluno_filiacao2']; ?>&nbsp;</td>
            <td><small>NACIONALIDADE</small><br><?php echo $row_AlunoLogado['aluno_nacionalidade']; ?>&nbsp;</td>
            <td><small>NATURALIDADE</small><br><?php echo $row_AlunoLogado['municipio_nome']; ?> - <?php echo $row_AlunoLogado['aluno_uf_nascimento']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>NIS</small><br><?php echo $row_AlunoLogado['aluno_nis']; ?>&nbsp;</td>
            <td><small>SUS</small><br><?php echo $row_AlunoLogado['aluno_sus']; ?>&nbsp;</td>
            <td><small>RG</small><br><?php echo $row_AlunoLogado['aluno_identidade']; ?> <?php echo $row_AlunoLogado['aluno_emissor']; ?> <?php echo $row_AlunoLogado['aluno_uf_emissor']; ?>&nbsp;</td>
            <td><small>DATA EXPEDIÇÃO</small><br><?php echo inverteData($row_AlunoLogado['aluno_data_espedicao']); ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>TIPO DE CERTIDÃO</small><br><?php echo $row_AlunoLogado['aluno_tipo_certidao']; ?>&nbsp;</td>
            <td><small>TERMO</small><br><?php echo $row_AlunoLogado['aluno_termo']; ?>&nbsp;</td>
            <td><small>FOLHAS</small><br><?php echo $row_AlunoLogado['aluno_folhas']; ?>&nbsp;</td>
            <td><small>LIVROS</small><br><?php echo $row_AlunoLogado['aluno_livro']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>EMISSÃO CERTIDÃO</small><br><?php echo inverteData($row_AlunoLogado['aluno_emissao_certidao']); ?>&nbsp;</td>
            <td><small>MUNICÍPIO CARTÓRIO</small><br><?php echo $row_AlunoLogado['aluno_mucicipio_cartorio']; ?>-<?php echo $row_AlunoLogado['aluno_uf_cartorio']; ?>&nbsp;</td>
            <td><small>NOME DO CARTÓRIO</small><br><?php echo $row_AlunoLogado['aluno_nome_cartorio']; ?>&nbsp;</td>
            <td><small>CERTIDÃO MODELO NOVO</small><br><?php echo $row_AlunoLogado['aluno_num_matricula_modelo_novo']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>LOCALIZAÇÃO</small><br><?php echo $row_AlunoLogado['aluno_localizacao']; ?>&nbsp;</td>
            <td><small>CEP</small><br><?php echo $row_AlunoLogado['aluno_cep']; ?>&nbsp;</td>
            <td><small>ENDEREÇO</small><br><?php echo $row_AlunoLogado['aluno_endereco']; ?>&nbsp;</td>
            <td><small>NÚMERO</small><br><?php echo $row_AlunoLogado['aluno_numero']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>COMPLEMENTO</small><br><?php echo $row_AlunoLogado['aluno_complemento']; ?>&nbsp;</td>
            <td><small>BAIRRO</small><br><?php echo $row_AlunoLogado['aluno_bairro']; ?>&nbsp;</td>
            <td><small>UF</small><br><?php echo $row_AlunoLogado['aluno_uf']; ?>&nbsp;</td>
            <td><small>MUNICÍPIO</small><br><?php echo $row_AlunoLogado['aluno_municipio']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>TELEFONE</small><br><?php echo $row_AlunoLogado['aluno_telefone']; ?>&nbsp;</td>
            <td><small>CELULAR</small><br><?php echo $row_AlunoLogado['aluno_celular']; ?>&nbsp;</td>
            <td><small>E-MAIL</small><br><?php echo $row_AlunoLogado['aluno_email']; ?>&nbsp;</td>
            <td><small></small><br>&nbsp;</td>
          </tr>
          <tr>
            <td><small>POSSUI DEFICIÊNCIA</small><br><?php echo $row_AlunoLogado['aluno_tipo_deficiencia']; ?>&nbsp;</td>
            <td><small>POSSUI LAUDO</small><br><?php echo $row_AlunoLogado['aluno_laudo']; ?>&nbsp;</td>
            <td><small>POSSUI ALERGIA</small><br><?php echo $row_AlunoLogado['aluno_alergia']; ?>&nbsp;</td>
            <td><small>QUAL ALERGIA?</small><br><?php echo $row_AlunoLogado['aluno_alergia_qual']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>DESTRO/CANHOTO</small><br><?php echo $row_AlunoLogado['aluno_destro']; ?>&nbsp;</td>
            <td><small>EM CASO DE EMRGÊNCIA AVISAR</small><br><?php echo $row_AlunoLogado['aluno_emergencia_avisar']; ?>&nbsp;</td>
            <td><small>TELEFONE DE EMERGÊNCIA</small><br><?php echo $row_AlunoLogado['aluno_emergencia_tel1']; ?>&nbsp;</td>
            <td><small>TELEFONE DE EMERGÊNCIA</small><br><?php echo $row_AlunoLogado['aluno_emergencia_tel2']; ?>&nbsp;</td>
          </tr>
          <tr>
            <td><small>RECEBE BOLSA FAMÍLIA?</small><br><?php echo $row_AlunoLogado['aluno_recebe_bolsa_familia']; ?>&nbsp;</td>
            <td><small></small><br>&nbsp;</td>
            <td><small></small><br>&nbsp;</td>
            <td><small></small><br>&nbsp;</td>
          </tr>
        </tbody>
      </table>
     
     
             
     </div>
    </div>
    </div>
    
    
    
   </div>
	
	
	
	
	
  
	
	

      <!--JavaScript at end of body for optimized loading-->
	  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="../js/materialize.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.sidenav').sidenav();
		});
	</script>	  
    </body>
  </html>
  <?php
mysql_free_result($AlunoLogado);
?>
