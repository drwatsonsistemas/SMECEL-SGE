<?php require_once('../../Connections/SmecelNovo.php'); ?>
<?php //include "fnc/anoLetivo.php"; ?>

<?php include "fnc/session.php"; ?>
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

include "usuLogado.php";
include "fnc/anoLetivo.php";

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_EscolaLogada = "
SELECT escola_id, escola_id_sec, escola_nome, escola_cep, escola_endereco, escola_num, escola_bairro, escola_telefone1, 
escola_telefone2, escola_email, escola_inep, escola_cnpj, escola_logo, sec_id, sec_cidade, sec_uf, escola_tema 
FROM smc_escola
INNER JOIN smc_sec ON sec_id = escola_id_sec 
WHERE escola_id = '$row_UsuLogado[usu_escola]'";
$EscolaLogada = mysql_query($query_EscolaLogada, $SmecelNovo) or die(mysql_error());
$row_EscolaLogada = mysql_fetch_assoc($EscolaLogada);
$totalRows_EscolaLogada = mysql_num_rows($EscolaLogada);
?>
<!DOCTYPE html>
<html class="<?php echo $row_EscolaLogada['escola_tema']; ?>" lang="pt-br">
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
<link rel="stylesheet" type="text/css" href="css/preloader.css">
  <link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    
    <style>
      .kanban-list {
        min-height: 200px;
        border: 1px solid #ddd;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 4px;
        transition: all 0.3s ease;
      }

      .aluno-item {
        padding: 10px;
        margin: 5px;
        background: #fff;
        border: 1px solid #ccc;
        cursor: move;
        border-radius: 4px;
        transition: all 0.3s ease;
        position: relative;
        padding-right: 120px; /* Espaço para as tags */
        display: flex;
        align-items: center;
        min-height: 60px;
      }

      .aluno-item.nao-matriculado {
        cursor: not-allowed;
        opacity: 0.7;
      }

      .aluno-item.nao-matriculado:hover {
        background-color: #f5f5f5;
      }

      .aluno-item .situacao-tag {
        position: absolute;
        right: 5px;
        font-size: 0.8em;
        top: 50%;
        transform: translateY(-50%);
      }

      .aluno-item .resultado-tag {
        position: absolute;
        right: 5px;
        font-size: 0.8em;
        top: 50%;
        transform: translateY(-50%);
        margin-right: 80px;
      }

      .aluno-item .remove-btn {
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        color: #d9534f;
        cursor: pointer;
        font-size: 1.2em;
        opacity: 0.7;
        transition: opacity 0.3s ease;
      }

      .aluno-item .remove-btn:hover {
        opacity: 1;
      }

      .aluno-item.dragging {
        opacity: 1;
      }

      .aluno-item.dragging-clone {
        opacity: 0.5;
        background: #f8f9fa;
        border: 2px dashed #ccc;
      }

      .ls-box {
        margin-bottom: 20px;
      }

      /* Cores das turmas */
      .turma-1 { background-color: #e3f2fd; border-color: #90caf9; }
      .turma-2 { background-color: #f3e5f5; border-color: #ce93d8; }
      .turma-3 { background-color: #e8f5e9; border-color: #a5d6a7; }
      .turma-4 { background-color: #fff3e0; border-color: #ffcc80; }
      .turma-5 { background-color: #fce4ec; border-color: #f48fb1; }
      .turma-6 { background-color: #e0f7fa; border-color: #80deea; }
      .turma-7 { background-color: #f1f8e9; border-color: #c5e1a5; }
      .turma-8 { background-color: #fbe9e7; border-color: #ffab91; }
      .turma-9 { background-color: #ede7f6; border-color: #b39ddb; }
      .turma-10 { background-color: #e8eaf6; border-color: #9fa8da; }

      /* Cores dos itens de aluno */
      .aluno-turma-1 { background-color: #bbdefb; }
      .aluno-turma-2 { background-color: #e1bee7; }
      .aluno-turma-3 { background-color: #c8e6c9; }
      .aluno-turma-4 { background-color: #ffe0b2; }
      .aluno-turma-5 { background-color: #f8bbd0; }
      .aluno-turma-6 { background-color: #b2ebf2; }
      .aluno-turma-7 { background-color: #dcedc8; }
      .aluno-turma-8 { background-color: #ffccbc; }
      .aluno-turma-9 { background-color: #d1c4e9; }
      .aluno-turma-10 { background-color: #c5cae9; }
    </style>

</head>
  <body>
    <?php include_once ("menu-top.php"); ?>
          <?php include_once ("menu-esc.php"); ?>


    <main class="ls-main ">
      <div class="container-fluid">
        <h1 class="ls-title-intro ls-ico-users">REMANEJAR ALUNOS</h1>
        
        <!-- Selects de Turmas -->
        <div class="ls-box">
          <div class="row">
            <div class="col-md-6">
              <label class="ls-label">
                <b class="ls-label-text">Selecione a Turma do Ano Letivo Atual (<?php echo $row_AnoLetivo['ano_letivo_ano']; ?>)</b>
                <div class="ls-custom-select">
                  <select id="turmaAtual" name="turma_atual" class="ls-select" onchange="carregarAlunos(this.value)">
                    <option value="">Selecione uma turma</option>
                    <?php
                    $query_listaTurmas = "SELECT t.turma_id, t.turma_nome, t.turma_ano_letivo,
                                         CASE t.turma_turno 
                                           WHEN 0 THEN 'Integral'
                                           WHEN 1 THEN 'Matutino'
                                           WHEN 2 THEN 'Vespertino'
                                           WHEN 3 THEN 'Noturno'
                                         END as turno_nome
                                         FROM smc_turma t 
                                         WHERE t.turma_id_escola = '$row_UsuLogado[usu_escola]' 
                                         AND t.turma_ano_letivo = '$row_AnoLetivo[ano_letivo_ano]' 
                                         ORDER BY t.turma_nome";
                    $listaTurmas = mysql_query($query_listaTurmas, $SmecelNovo) or die(mysql_error());
                    
                    while ($row_listaTurmas = mysql_fetch_assoc($listaTurmas)) {
                      echo "<option value='{$row_listaTurmas['turma_id']}'>{$row_listaTurmas['turma_nome']} ({$row_listaTurmas['turno_nome']}) ({$row_listaTurmas['turma_ano_letivo']})</option>";
                    }
                    ?>
                  </select>
                </div>
              </label>
            </div>
            <div class="col-md-6" id="selectProximaTurma" style="display: none;">
              <label class="ls-label">
                <b class="ls-label-text">Selecione a Turma do Próximo Ano Letivo (<?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>)</b>
                <div class="ls-custom-select">
                  <select id="turmaProxima" name="turma_proxima" class="ls-select">
                    <option value="">Selecione uma turma</option>
                    <?php
                    $query_turmasProximas = "SELECT t.turma_id, t.turma_nome, t.turma_ano_letivo,
                                           CASE t.turma_turno 
                                             WHEN 0 THEN 'Integral'
                                             WHEN 1 THEN 'Matutino'
                                             WHEN 2 THEN 'Vespertino'
                                             WHEN 3 THEN 'Noturno'
                                           END as turno_nome
                                           FROM smc_turma t 
                                           WHERE t.turma_id_escola = '$row_UsuLogado[usu_escola]' 
                                           AND t.turma_ano_letivo = '" . ($row_AnoLetivo['ano_letivo_ano'] + 1) . "' 
                                           ORDER BY t.turma_nome";
                    $turmasProximas = mysql_query($query_turmasProximas, $SmecelNovo) or die(mysql_error());
                    
                    while ($row_turmasProximas = mysql_fetch_assoc($turmasProximas)) {
                      echo "<option value='{$row_turmasProximas['turma_id']}'>{$row_turmasProximas['turma_nome']} ({$row_turmasProximas['turno_nome']}) ({$row_turmasProximas['turma_ano_letivo']})</option>";
                    }
                    ?>
                  </select>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Área de Alunos (Drag) -->
        <div class="ls-box" id="alunosContainer" style="display: none;">
          <div class="row">
            <div class="col-md-6">
              <h4 class="ls-xs-space" id="turmaNome"></h4>
              <div id="alunosList" class="kanban-list">
                <!-- Alunos serão carregados via AJAX -->
              </div>
            </div>
            <div class="col-md-6">
              <h4 class="ls-xs-space" id="turmaDestinoNome"></h4>
              <div id="turmaDestino" class="kanban-list" ondrop="drop(event)" ondragover="allowDrop(event)" style="display: none;">
                <!-- Alunos arrastados aparecerão aqui -->
              </div>
              <div id="turmaDestinoAviso" class="ls-alert-warning" style="display: none;">
                <strong>Selecione uma turma de destino</strong>
                <p>Escolha uma turma do próximo ano letivo para continuar.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Botão para Confirmar Matrículas -->
        <div class="ls-actions-btn">
          <button id="confirmarMatriculas" class="ls-btn-primary ls-btn-lg" onclick="confirmarMatriculas()">Confirmar Matrículas</button>
        </div>
      </div>
    </main>

    <aside class="ls-notification">
      <nav class="ls-notification-list" id="ls-notification-curtain" style="left: 1716px;">
        <h3 class="ls-title-2">Notificações</h3>
    <ul>
      <?php include "notificacoes.php"; ?>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-help-curtain" style="left: 1756px;">
        <h3 class="ls-title-2">Feedback</h3>
    <ul>
      <li><a href="https://webmail.smecel.com.br" target="_blank">&gt; Acesse o webmail de sua escola</a></li>
    </ul>
      </nav>

      <nav class="ls-notification-list" id="ls-feedback-curtain" style="left: 1796px;">
        <h3 class="ls-title-2">Ajuda</h3>
        <ul>
          <li class="ls-txt-center hidden-xs">
            <a href="tutoriais_video.php" class="ls-btn-dark ls-btn-tour">Tutorial (Vídeos)</a>
          </li>
          <li><a href="#">&gt; Guia</a></li>
          <li><a href="#">&gt; Wiki</a></li>
        </ul>
      </nav>
    </aside>

    <!-- We recommended use jQuery 1.10 or up -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="//assets.locaweb.com.br/locastyle/edge/javascripts/locastyle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      // Variáveis globais para controle de cores
      let turmaAtualCor = 1;
      let turmaProximaCor = 2;

      $(document).ready(function() {
        // Inicializar Select2
        $('#turmaAtual, #turmaProxima').select2({
          placeholder: "Selecione uma turma",
          width: '100%'
        });

        // Mostrar/esconder select de turma próxima e área de kanban
        $('#turmaAtual').on('change', function() {
          if ($(this).val()) {
            $('#selectProximaTurma').show();
            $('#turmaProxima').val('').trigger('change');
            $('#alunosContainer').hide();
            
            // Atualizar cor da turma atual
            turmaAtualCor = (turmaAtualCor % 10) + 1;
            $('#alunosList').removeClass().addClass('kanban-list turma-' + turmaAtualCor);
            
            // Atualizar nome da turma
            const turmaNome = $(this).find('option:selected').text();
            $('#turmaNome').text(turmaNome);
          } else {
            $('#selectProximaTurma').hide();
            $('#alunosContainer').hide();
            $('#turmaNome').text('');
          }
        });

        // Mostrar/esconder área de kanban quando uma turma próxima é selecionada
        $('#turmaProxima').on('change', function() {
          if ($(this).val()) {
            $('#alunosContainer').show();
            
            // Atualizar cor da turma próxima
            turmaProximaCor = (turmaProximaCor % 10) + 1;
            $('#turmaDestino').removeClass().addClass('kanban-list turma-' + turmaProximaCor);
            
            // Atualizar nome da turma de destino
            const turmaNome = $(this).find('option:selected').text();
            $('#turmaDestinoNome').text(turmaNome);
            
            // Mostrar kanban e esconder aviso
            $('#turmaDestino').show();
            $('#turmaDestinoAviso').hide();
          } else {
            $('#alunosContainer').hide();
            $('#turmaDestinoNome').text('');
            $('#turmaDestino').hide();
            $('#turmaDestinoAviso').show();
          }
        });

        // Inicializar aviso
        $('#turmaDestinoAviso').show();

        // Inicializar Sortable para permitir reordenação
        $('#alunosList, #turmaDestino').sortable({
          items: '.aluno-item',
          connectWith: '.kanban-list',
          handle: '.aluno-item',
          placeholder: 'aluno-item-placeholder',
          forcePlaceholderSize: true,
          opacity: 0.8,
          revert: true,
          start: function(e, ui) {
            ui.placeholder.height(ui.item.height());
          },
          stop: function(e, ui) {
            // Se o item foi movido para o destino, esconde o original
            if (ui.item.parent().attr('id') === 'turmaDestino') {
              const alunoId = ui.item.attr('id');
              const alunoOriginal = $('#alunosList').find(`#${alunoId}`);
              if (alunoOriginal.length) {
                alunoOriginal.hide();
              }
            }
          }
        }).disableSelection();
      });

      // Adicionar estilos para o placeholder
      $('<style>')
        .text(`
          .aluno-item-placeholder {
            border: 2px dashed #ccc;
            background: #f9f9f9;
            margin: 5px;
            border-radius: 4px;
          }
        `)
        .appendTo('head');

      // Função para exibir toast
      function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        });

        Toast.fire({
          icon: type,
          title: message
        });
      }

      // Função para carregar alunos da turma selecionada
      function carregarAlunos(turmaId) {
        if (!turmaId) {
          $('#alunosContainer').hide();
          return;
        }

        $.ajax({
          url: 'carregar_alunos.php',
          method: 'POST',
          data: { turma_id: turmaId },
          success: function(response) {
            $('#alunosList').html(response);
            // Adicionar classe de cor aos itens de aluno
            $('#alunosList .aluno-item').each(function() {
              const situacao = $(this).data('situacao');
              $(this).addClass('aluno-turma-' + turmaAtualCor);
              
              // Se não estiver matriculado, adiciona classe e remove draggable
              if (situacao !== 1) {
                $(this).addClass('nao-matriculado');
                $(this).attr('draggable', 'false');
              }
            });
            $('#alunosContainer').show();
          },
          error: function() {
            showToast('Erro ao carregar alunos.', 'danger');
          }
        });
      }

      // Funções de Drag-and-Drop
      function allowDrop(event) {
        event.preventDefault();
      }

      function drag(event) {
        // Verifica se o aluno está matriculado
        const alunoElement = event.target;
        if (alunoElement.classList.contains('nao-matriculado')) {
          event.preventDefault();
          return;
        }

        // Cria um clone para o efeito de arrasto
        const clone = alunoElement.cloneNode(true);
        clone.classList.add('dragging-clone');
        document.body.appendChild(clone);
        
        // Define a imagem de arrasto como o clone
        event.dataTransfer.setDragImage(clone, 0, 0);
        
        // Remove o clone após um pequeno delay
        setTimeout(() => {
          document.body.removeChild(clone);
        }, 0);

        event.dataTransfer.setData("aluno_id", alunoElement.id);
        event.dataTransfer.setData("source_id", alunoElement.parentElement.id);
      }

      function drop(event) {
        event.preventDefault();
        const alunoId = event.dataTransfer.getData("aluno_id");
        const sourceId = event.dataTransfer.getData("source_id");
        const targetId = event.target.closest('.kanban-list').id;
        const alunoElement = document.getElementById(alunoId);
        
        // Verifica se o aluno está matriculado
        if (alunoElement.classList.contains('nao-matriculado')) {
          return;
        }

        // Se o aluno já existe no destino, não faz nada
        if (document.getElementById(targetId).querySelector(`#${alunoId}`)) {
          return;
        }
        
        // Clona o elemento para a turma de destino
        const alunoClone = alunoElement.cloneNode(true);
        
        // Remove a tag de situação do clone
        const situacaoTag = alunoClone.querySelector('.situacao-tag');
        if (situacaoTag) {
          situacaoTag.remove();
        }
        
        // Adiciona o botão de remover apenas na turma de destino
        const removeBtn = document.createElement('i');
        removeBtn.className = 'ls-ico-remove remove-btn';
        removeBtn.setAttribute('onclick', 'removeAluno(this)');
        removeBtn.setAttribute('title', 'Remover aluno');
        alunoClone.appendChild(removeBtn);
        
        // Se estiver movendo para o destino, esconde o original
        if (targetId === 'turmaDestino') {
          alunoElement.style.display = 'none';
        }
        
        document.getElementById(targetId).appendChild(alunoClone);
        
        // Atualizar cor do aluno para a cor da turma de destino
        if (targetId === 'turmaDestino') {
          alunoClone.classList.remove('aluno-turma-' + turmaAtualCor);
          alunoClone.classList.add('aluno-turma-' + turmaProximaCor);
        } else {
          alunoClone.classList.remove('aluno-turma-' + turmaProximaCor);
          alunoClone.classList.add('aluno-turma-' + turmaAtualCor);
        }
      }

      // Função para remover aluno do kanban
      function removeAluno(element) {
        const alunoItem = $(element).closest('.aluno-item');
        const alunoId = alunoItem.attr('id');
        
        // Mostra o aluno original no kanban de origem
        const alunoOriginal = $('#alunosList').find(`#${alunoId}`);
        if (alunoOriginal.length) {
          alunoOriginal.show();
        }
        
        // Remove o clone do kanban de destino
        alunoItem.fadeOut(300, function() {
          $(this).remove();
        });
      }

      // Função para confirmar matrículas
      function confirmarMatriculas() {
        const turmaDestinoId = $('#turmaProxima').val();
        const alunos = [];
        $('#turmaDestino .aluno-item').each(function() {
          alunos.push($(this).attr('id').replace('aluno_', ''));
        });

        if (!turmaDestinoId || alunos.length === 0) {
          showToast('Selecione uma turma de destino e arraste pelo menos um aluno.', 'warning');
          return;
        }

        $.ajax({
          url: 'matricular_alunos.php',
          method: 'POST',
          data: {
            turma_id: turmaDestinoId,
            alunos: alunos,
            ano_letivo: '<?php echo $row_AnoLetivo['ano_letivo_ano'] + 1; ?>',
            escola_id: '<?php echo $row_EscolaLogada['escola_id']; ?>',
            sec_id: '<?php echo $row_EscolaLogada['escola_id_sec']; ?>'
          },
          success: function(response) {
            try {
              const result = JSON.parse(response);
              if (result.status === 'success') {
                showToast('Matrículas realizadas com sucesso!');
                setTimeout(function() {
                  window.location.reload();
                }, 2000);
              } else {
                showToast(result.message || 'Erro ao realizar matrículas.', 'danger');
              }
            } catch (e) {
              showToast('Erro ao processar resposta do servidor.', 'danger');
            }
          },
          error: function() {
            showToast('Erro ao realizar matrículas.', 'danger');
          }
        });
      }
    </script>
 
  </body>
</html>
<?php
mysql_free_result($UsuLogado);

mysql_free_result($EscolaLogada);
?>
