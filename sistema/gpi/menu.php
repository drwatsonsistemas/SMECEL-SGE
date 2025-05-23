<?php require_once('../../Connections/SmecelNovo.php'); ?>
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

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_chamadosNovos = "SELECT chamado_id, chamado_id_sec, chamado_id_escola, chamado_id_usuario, chamado_id_telefone, chamado_data_abertura, chamado_categoria, chamado_situacao, chamado_titulo, chamado_texto, chamado_imagem, chamado_visualizado FROM smc_chamados WHERE chamado_visualizado = 'N'";
$chamadosNovos = mysql_query($query_chamadosNovos, $SmecelNovo) or die(mysql_error());
$row_chamadosNovos = mysql_fetch_assoc($chamadosNovos);
$totalRows_chamadosNovos = mysql_num_rows($chamadosNovos);

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_ticketsNovos = "SELECT ticket_id, ticket_id_chamado, ticket_id_usuario, ticket_data, ticket_texto, ticket_imagem, ticket_visualizado FROM smc_ticket WHERE ticket_visualizado = 'N'";
$ticketsNovos = mysql_query($query_ticketsNovos, $SmecelNovo) or die(mysql_error());
$row_ticketsNovos = mysql_fetch_assoc($ticketsNovos);
$totalRows_ticketsNovos = mysql_num_rows($ticketsNovos);

$novos = $totalRows_chamadosNovos + $totalRows_ticketsNovos;
?>



<aside class="ls-sidebar">
  <div class="ls-sidebar-inner"> <a href="index.php" class="ls-go-prev"><span class="ls-text">Voltar à lista de serviços</span></a>
    <nav class="ls-menu">
      <ul>

        <li class=""><a href="index.php" class="ls-ico-dashboard ls-text-xs" title="Dashboard">início</a></li>
        <li><a href="contratos.php" class="ls-ico-multibuckets ls-text-xs" title="Prefeituras">Entidades</a></li>
        <li><a href="matriculas_total.php" class="ls-ico-stats ls-text-xs" title="Matrículas">Matrículas</a></li>
        <li><a href="usuarios.php" class="ls-ico-users ls-text-xs" title="Usuários">Usuários</a></li>
        <li><a href="suporte.php" class="ls-ico-hours ls-text-xs" title="Suporte">Suporte</a></li>
        <li><a href="chamados.php" class="ls-ico-numbered-list ls-text-xs" title="HelpDesk">Chamados <?php if ($novos > 0) { ?><span class="ls-tag-danger"><?php echo $novos; ?></span><?php } ?> </a></li>      
        <li><a href="atualizacoes.php" class="ls-ico-ftp ls-text-xs" title="Atualizações">Atualizações </a></li>      
        <li><a href="tutoriais_video.php" class="ls-ico-code ls-text-xs" title="Atualizações">Tutoriais </a></li>      
        <li><a href="manutencao.php" class="ls-ico-cog ls-text-xs" title="Atualizações">Manutenção </a></li>      

      </ul>
    </nav>
  </div>
</aside>
<?php
mysql_free_result($chamadosNovos);

mysql_free_result($ticketsNovos);
?>
