let offset = 0;
const limit = 10;
let loading = false; // Evitar múltiplas requisições
let allDataLoaded = false; // Identificar se todos os dados foram carregados

function diaSemana(data) {
    // data vem como “YYYY-MM-DD”
    const [ano, mes, dia] = data.split('-').map(Number);
    // new Date(ano, mesIndex, dia) já assume o horário local
    const d = new Date(ano, mes - 1, dia);
  
    const dias = [
      'Domingo',
      'Segunda-feira',
      'Terça-feira',
      'Quarta-feira',
      'Quinta-feira',
      'Sexta-feira',
      'Sábado'
    ];
    return dias[d.getDay()];
  }
  

function loadAulas() {
    if (loading || allDataLoaded) return;

    loading = true;

    const loadingSpinner = document.getElementById('loadingSpinner');
    const noDataMessage = document.getElementById('noDataMessage');
    const allDataLoadedMessage = document.getElementById('allDataLoadedMessage');

    loadingSpinner.style.display = 'flex';
    allDataLoadedMessage.style.display = 'none'; // Esconde o texto de "Todas as aulas carregadas" enquanto carrega.

    const turmaId = document.getElementById('turmaIdInput').value;
    const disciplinaId = document.getElementById('disciplinaIdInput').value;

    fetch(`consultas/aulas_por_data.php?turma_id=${turmaId}&disciplina_id=${disciplinaId}&offset=${offset}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            console.log('Dados recebidos:', data);
            const tableBody = document.getElementById('aulasTableBody');

            if (data.error) {
                console.error('Erro:', data.error);
                loadingSpinner.style.display = 'none';
                return;
            }

            if (data.length === 0) {
                if (offset === 0) {
                    // Exibe mensagem caso não haja nenhuma aula
                    noDataMessage.style.display = 'block';
                } else {
                    // Exibe mensagem quando todas as aulas já foram carregadas
                    allDataLoaded = true;
                    loadingSpinner.style.display = 'none';
                    allDataLoadedMessage.style.display = 'block';
                }
                return;
            }

            // Esconde mensagens caso existam
            noDataMessage.style.display = 'none';
            allDataLoadedMessage.style.display = 'none';

            // Insere as novas aulas na tabela
            data.forEach(aula => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="ls-txt-center">${new Date(aula.plano_aula_data).toLocaleDateString()}</td>
                    <td class="ls-txt-center">${diaSemana(aula.plano_aula_data)}</td>
                    <td class="ls-txt-center">${aula.aulas_total}</td>
                `;
                tableBody.appendChild(row);
            });

            offset += limit;
            loading = false;
            loadingSpinner.style.display = 'none';
        })
        .catch(error => {
            console.error('Erro ao carregar aulas:', error);
            loading = false;
            loadingSpinner.style.display = 'none';
        });
}

// Listener para o evento de rolagem
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        loadAulas();
    }
});

// Carregar a primeira página
loadAulas();
