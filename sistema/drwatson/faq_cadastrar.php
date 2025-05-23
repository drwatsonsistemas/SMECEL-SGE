
<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php require_once('funcoes/configuracoes.php'); ?>

<?php
// Inicializa a sessão
if (!isset($_SESSION)) {
    session_start();
}

// Controle de logout
if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
    $_SESSION['MM_Username'] = NULL;
    $_SESSION['MM_UserGroup'] = NULL;
    unset($_SESSION['MM_Username']);
    unset($_SESSION['MM_UserGroup']);
    header("Location: ../../index.php?exit");
    exit;
}

// Controle de acesso
$MM_authorizedUsers = "99";
$MM_restrictGoTo = "../../index.php?acessorestrito";
if (!((isset($_SESSION['MM_Username'])))) {
    header("Location: " . $MM_restrictGoTo);
    exit;
}

// Função para manipular valores SQL
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType)
    {
        $theValue = ($theType == "text") ? "'" . mysql_real_escape_string($theValue) . "'" : intval($theValue);
        return $theValue;
    }
}

if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "99";
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

$MM_restrictGoTo = "../../index.php?acessorestrito";
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
  
  $colname_UsuarioLogado = "-1";
  if (isset($_SESSION['MM_Username'])) {
    $colname_UsuarioLogado = $_SESSION['MM_Username'];
  }
  mysql_select_db($database_SmecelNovo, $SmecelNovo);
  $query_UsuarioLogado = sprintf("SELECT usu_id, usu_nome, usu_email, usu_senha, usu_tipo, usu_sec, usu_escola, usu_status, usu_cadastro FROM smc_usu WHERE usu_email = %s", GetSQLValueString($colname_UsuarioLogado, "text"));
  $UsuarioLogado = mysql_query($query_UsuarioLogado, $SmecelNovo) or die(mysql_error());
  $row_UsuarioLogado = mysql_fetch_assoc($UsuarioLogado);
  $totalRows_UsuarioLogado = mysql_num_rows($UsuarioLogado);


// Conexão ao banco de dados
mysql_select_db($database_SmecelNovo, $SmecelNovo);

// Ações de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar nova pergunta
    if (isset($_POST['add_faq'])) {
        $pergunta = $_POST['pergunta'];
        $resposta = $_POST['resposta'];
        $categoria = intval($_POST['categoria']);

        // Inserir a pergunta na tabela principal
        $query_InsertFAQ = sprintf(
            "INSERT INTO smc_faq_respostas (pergunta_padrao, resposta_oficial, categoria) VALUES (%s, %s, %s)",
            GetSQLValueString($pergunta, "text"),
            GetSQLValueString($resposta, "text"),
            GetSQLValueString($categoria, "text")
        );
        mysql_query($query_InsertFAQ, $SmecelNovo) or die(mysql_error());

        // Obter o ID da última inserção
        $lastInsertId = mysql_insert_id();

        // Inserir a pergunta como a primeira variação
        $query_InsertVariation = sprintf(
            "INSERT INTO smc_faq_variacoes_perguntas (id_resposta, variacao) VALUES (%s, %s)",
            GetSQLValueString($lastInsertId, "int"),
            GetSQLValueString($pergunta, "text")
        );
        mysql_query($query_InsertVariation, $SmecelNovo) or die(mysql_error());

        // Mensagem de sucesso
        $mensagem = "Pergunta e sua variação inicial adicionadas com sucesso!";
    }

    // Excluir pergunta
    if (isset($_POST['delete_faq'])) {
        $id = $_POST['faq_id'];
        $query_DeleteFAQ = sprintf("DELETE FROM smc_faq_respostas WHERE id = %s", GetSQLValueString($id, "int"));
        mysql_query($query_DeleteFAQ, $SmecelNovo) or die(mysql_error());
        $mensagem = "Pergunta frequente removida com sucesso!";
    }

    // Adicionar variação
    if (isset($_POST['add_variation'])) {
        $id_resposta = $_POST['id_resposta'];
        $variacao = $_POST['variacao'];

        $query_InsertVariation = sprintf(
            "INSERT INTO smc_faq_variacoes_perguntas (id_resposta, variacao) VALUES (%s, %s)",
            GetSQLValueString($id_resposta, "int"),
            GetSQLValueString($variacao, "text")
        );
        mysql_query($query_InsertVariation, $SmecelNovo) or die(mysql_error());
        $mensagem = "Variação adicionada com sucesso!";
    }

    // Excluir variação
    if (isset($_POST['delete_variation'])) {
        $id_variacao = $_POST['id_variacao'];

        $query_DeleteVariation = sprintf(
            "DELETE FROM smc_faq_variacoes_perguntas WHERE id = %s",
            GetSQLValueString($id_variacao, "int")
        );
        mysql_query($query_DeleteVariation, $SmecelNovo) or die(mysql_error());
        $mensagem = "Variação removida com sucesso!";
    }

    if (isset($_POST['update_faq'])) {
        $id_faq = $_POST['id_faq'];
        $resposta = $_POST['resposta'];
        $categoria = $_POST['categoria'];
    
        $query_UpdateFAQ = sprintf(
            "UPDATE smc_faq_respostas SET resposta_oficial = %s, categoria = %s WHERE id = %s",
            GetSQLValueString($resposta, "text"),
            GetSQLValueString($categoria, "text"),
            GetSQLValueString($id_faq, "int")
        );
        mysql_query($query_UpdateFAQ, $SmecelNovo) or die(mysql_error());
    
        $mensagem = "Resposta atualizada com sucesso!";
    }
}

$query_FAQ = "
SELECT 
    respostas.id, 
    respostas.pergunta_padrao,
    respostas.categoria, 
    respostas.likes, 
    respostas.dislikes, 
    (SELECT COUNT(*) FROM smc_faq_variacoes_perguntas AS variacoes WHERE variacoes.id_resposta = respostas.id) AS qtd_variacoes 
FROM smc_faq_respostas AS respostas";
$FAQ = mysql_query($query_FAQ);

?>



<!DOCTYPE html>
<html class="<?php echo COR_TEMA ?>">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117872281-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117872281-1');
</script>

<title>SMECEL - Sistema de Gestão Escolar</title>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="stylesheet" type="text/css" href="//assets.locaweb.com.br/locastyle/edge/stylesheets/locastyle.css">
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">

</head>
<body>
<?php include_once("menu_top.php"); ?>
<?php include_once("menu.php"); ?>

<main class="ls-main">
    <div class="container-fluid">
        <h1 class="ls-title-intro">Gerenciar FAQ</h1>
        
        <?php if (isset($mensagem)) { ?>
            <div class="ls-alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php } ?>

<!-- Botão para abrir a modal de adição -->
<button class="ls-btn-primary" data-ls-module="modal" data-target="#modalAddQuestion">Adicionar Nova Pergunta</button>
<a href="faq.php" target="_blank" class="ls-btn-primary">Testar buscas</a>

<div class="ls-group-btn ls-float-right" style="margin-bottom: 20px;">
    <button class="ls-btn" onclick="filterCategory('all')">Todos</button>
    <button class="ls-btn" onclick="filterCategory(1)">Painel Secretaria</button>
    <button class="ls-btn" onclick="filterCategory(2)">Painel Escola</button>
    <button class="ls-btn" onclick="filterCategory(3)">Painel Professor</button>
    <button class="ls-btn" onclick="filterCategory(4)">Painel Aluno</button>
</div>

<div class="ls-box-filter">
  <label class="ls-label">
    <input type="text" id="tableSearchInput" class="ls-field" placeholder="Digite para buscar..." onkeyup="filterTable()">
  </label>
</div>

<table class="ls-table ls-table-striped" style="margin-top: 20px;">
    <thead>
        <tr>
        <th class="ls-txt-left">Pergunta</th>
        <th class="ls-txt-left">Acompanhamento</th>
        <th width="150px" class="ls-txt-center">Categoria</th>
            <th width="400px" class="ls-txt-center">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row_FAQ = mysql_fetch_assoc($FAQ)) { ?>
            <tr data-category="<?= htmlspecialchars($row_FAQ['categoria']) ?>">
            <td><?= htmlspecialchars($row_FAQ['pergunta_padrao']) ?></td>
            <td><div class="ls-group-btn">
                <button class="ls-btn">👍 <?= $row_FAQ['likes'] ?></button>
                <button class="ls-btn">👎 <?= $row_FAQ['dislikes'] ?></button>
        </div>
            </td>
            <td class="ls-txt-center">
                    <?php 
                        switch ($row_FAQ['categoria']) {
                            case 1: echo "Painel Secretaria"; break;
                            case 2: echo "Painel Escola"; break;
                            case 3: echo "Painel Professor"; break;
                            case 4: echo "Painel Aluno"; break;
                            default: echo "Não especificado"; 
                        }
                    ?>
                </td>
                <td class="ls-txt-center">
    <!-- Gerenciar Variações -->
    <button class="ls-btn ls-btn-sm ls-btn-info" onclick="openVariationModal(<?= $row_FAQ['id'] ?>)">
        <span class="ls-ico-windows"> <?= htmlspecialchars($row_FAQ['qtd_variacoes']) ?></span>
    </button>
    
    <!-- Editar Resposta -->
    <button class="ls-btn ls-btn-sm ls-btn-warning" onclick="openEditModal(<?= $row_FAQ['id'] ?>)">
        <span class="ls-ico-pencil"></span>
    </button>
    
    <!-- Excluir -->
    <form method="post" action="" style="display:inline;">
        <input type="hidden" name="faq_id" value="<?= $row_FAQ['id'] ?>">
        <button type="submit" name="delete_faq" class="ls-btn ls-btn-sm ls-btn-danger">
            <span class="ls-ico-remove"></span>
        </button>
    </form>

</td>
              
            </tr>
        <?php } ?>
    </tbody>
</table>    


    </div>
</main>

<!-- Modal para Adicionar Nova Pergunta -->
<div class="ls-modal" id="modalAddQuestion">
    <div class="ls-modal-box">
        <div class="ls-modal-header">
            <button data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">Adicionar Nova Pergunta</h4>
        </div>
        <div class="ls-modal-body">
            <form method="post" action="faq_cadastrar.php" class="ls-form-horizontal">
                <!-- Pergunta -->
                <label class="ls-label">
                    <b class="ls-label-text">Pergunta padrão</b>
                    
                    <input type="text" id="pergunta" name="pergunta" class="ls-field" placeholder="Digite a pergunta" required>
                    
                </label>

                <!-- Resposta -->
                <label class="ls-label">
                    <b class="ls-label-text">Resposta</b>
                
                        <textarea id="resposta" name="resposta" class="ls-field" rows="6" placeholder="Digite a resposta" required></textarea>
                    
                </label>

     
                        <div class="ls-custom-select">
                            <select name="categoria" class="ls-select" required>
                                <option value="" disabled selected>Selecione uma categoria</option>
                                <option value="1">Painel Secretaria</option>
                                <option value="2">Painel Escola</option>
                                <option value="3">Painel Professor</option>
                                <option value="4">Painel Aluno</option>
                            </select>
                            </div>
    

                <!-- Botões -->
                <div class="ls-actions-btn">
                    <button type="submit" name="add_faq" class="ls-btn-primary">Salvar</button>
                    <button type="button" class="ls-btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Gerenciar Variações -->
<div class="ls-modal" id="modalVariations">
    <div class="ls-modal-box">
        <div class="ls-modal-header">
            <button id="closeModalButton" class="ls-modal-close" data-dismiss="modal">&times;</button>
            <h4 class="ls-modal-title">Gerenciar Variações</h4>
        </div>
        <div class="ls-modal-body">
            <form method="post" action="faq_cadastrar.php" class="ls-form-horizontal">
                <!-- ID da Pergunta -->
                <input type="hidden" name="id_resposta" id="id_resposta">

                <!-- Nova Variação -->
                <label class="ls-label">
                    <b class="ls-label-text">Variação da pergunta</b>

                    <input type="text" id="variacao" name="variacao" class="ls-field" placeholder="Digite a variação da pergunta" required>

                </label>

                <!-- Botões -->
                <div class="ls-actions-btn">
                    <button type="submit" name="add_variation" class="ls-btn-primary">Adicionar</button>
                    <button type="button" class="ls-btn-danger" id="closeModalButtonSecondary">Fechar</button>
                </div>
            </form>

            <!-- Lista de Variações em Tabela -->
            <div id="variationList" style="margin-top: 20px;">
                <table class="ls-table ls-no-hover" id="variationTable">
                    <thead>
                        <tr>
                            <th>Variação</th>
                            <th style="width: 100px; text-align: center;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- As variações serão inseridas dinamicamente aqui -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Resposta -->
<div class="ls-modal" id="modalEditResponse">
    <div class="ls-modal-box">
        <div class="ls-modal-header">
            <button data-dismiss="modal" class="ls-modal-close">&times;</button>
            <h4 class="ls-modal-title">Editar Resposta</h4>
        </div>
        <div class="ls-modal-body">
            <form method="post" action="faq_cadastrar.php" class="ls-form-horizontal">
                <!-- ID da Pergunta -->
                <input type="hidden" name="id_faq" id="edit_id_faq">

                <!-- Pergunta -->
                <label class="ls-label">
                    <b class="ls-label-text">Pergunta Padrão</b>
                    <textarea id="edit_pergunta" name="pergunta" class="ls-field" rows="4" readonly></textarea>
                </label>

                <!-- Resposta -->
                <label class="ls-label">
                    <b class="ls-label-text">Resposta</b>
                    <textarea id="edit_resposta" name="resposta" class="ls-field" rows="6" placeholder="Digite a resposta" required></textarea>
                </label>

                <!-- Categoria -->
                <label class="ls-label">
                    <b class="ls-label-text">Categoria</b>
                    <div class="ls-custom-select">
                        <select name="categoria" id="edit_categoria" class="ls-select" required>
                            <option value="" disabled>Selecione uma categoria</option>
                            <option value="1">Painel Secretaria</option>
                            <option value="2">Painel Escola</option>
                            <option value="3">Painel Professor</option>
                            <option value="4">Painel Aluno</option>
                        </select>
                    </div>
                </label>

                <!-- Botões -->
                <div class="ls-actions-btn">
                    <button type="submit" name="update_faq" class="ls-btn-primary">Salvar</button>
                    <button type="button" class="ls-btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>


<script>
// Função para abrir o modal de gerenciamento de variações
function openVariationModal(id_resposta) {
    document.getElementById('id_resposta').value = id_resposta;

    // Requisição AJAX para carregar variações
    //fetch(`faq/get_variations.php?id_resposta=${id_resposta}`)
      fetch(`faq/get_variations.php?id_resposta=${id_resposta}&t=${Date.now()}`)
        .then(response => response.json())
        .then(data => {
            let rows = '';
            data.forEach(variation => {
                rows += `<tr>
                            <td>${variation.variacao}</td>
                            <td style="text-align: center; float:right">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id_variacao" value="${variation.id}">
                                    <button type="submit" name="delete_variation" class="ls-btn-danger ls-btn-xs">Excluir</button>
                                </form>
                            </td>
                         </tr>`;
            });
            document.querySelector('#variationTable tbody').innerHTML = rows;
        });

    // Abrir a modal programaticamente
    const modal = document.getElementById('modalVariations');
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('ls-opened');
}

// Fechar a modal manualmente
function closeModal() {
    const modal = document.getElementById('modalVariations');
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('ls-opened');
}

// Adicionar eventos aos botões de fechamento
document.getElementById('closeModalButton').addEventListener('click', closeModal);
document.getElementById('closeModalButtonSecondary').addEventListener('click', closeModal);
</script>

<script>

// Função para abrir o modal de edição
function openEditModal(id_faq) {
    fetch(`faq/get_faq.php?id_faq=${id_faq}`)
        .then(response => response.json())
        .then(data => {
            // Preencher os campos no modal
            document.getElementById('edit_id_faq').value = data.id;
            document.getElementById('edit_pergunta').value = data.pergunta_padrao;
            document.getElementById('edit_resposta').value = data.resposta_oficial;

            // Selecionar a categoria correspondente
            const categoriaSelect = document.getElementById('edit_categoria');
            categoriaSelect.value = data.categoria;

            // Abrir o modal
            const modal = document.getElementById('modalEditResponse');
            modal.setAttribute('aria-hidden', 'false');
            modal.classList.add('ls-opened');
        })
        .catch(error => {
            console.error('Erro ao carregar os dados:', error);
            alert('Não foi possível carregar os dados. Verifique o console.');
        });
}

// Função para fechar o modal manualmente
function closeModalEdit() {
    const modal = document.getElementById('modalEditResponse');
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('ls-opened');
}

function filterCategory(category) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const rowCategory = row.getAttribute('data-category');
        
        if (category === 'all' || rowCategory === category.toString()) {
            row.style.display = ''; // Mostra a linha
        } else {
            row.style.display = 'none'; // Esconde a linha
        }
    });
}

</script>

<script>
function filterTable() {
    const searchValue = document.getElementById('tableSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.ls-table tbody tr');

    rows.forEach(row => {
        const questionText = row.cells[0].textContent.toLowerCase();
        row.style.display = questionText.includes(searchValue) ? '' : 'none';
    });
}

document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
    button.addEventListener('click', () => {
        const modal = document.getElementById('modalEditResponse');
        if (modal) {
            modal.setAttribute('aria-hidden', 'true');
            modal.classList.remove('ls-opened');
        }
    });
});
</script>



</body>
</html>