document.addEventListener('DOMContentLoaded', () => {
    const modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhes'));
    const modalStatus = new bootstrap.Modal(document.getElementById('modalStatus'));

    // Delegação de evento para os botões de detalhes
    document.querySelectorAll('.btn-detalhes').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = btn.getAttribute('data-id');
            const chamado = solicitaesData.find(item => item.id == id);
            if (chamado) abrirModalDetalhes(chamado);
        });
    });

    // Preenche e abre o modal de Detalhes
    function abrirModalDetalhes(chamado) {
        document.getElementById('detalhes-id').textContent = chamado.id;
        document.getElementById('detalhes-solicitante').textContent = chamado.nome_solicitante;
        document.getElementById('detalhes-contato').textContent = chamado.contato;
        document.getElementById('detalhes-endereco').textContent = chamado.endereco;
        document.getElementById('detalhes-descricao').textContent = chamado.descricao;

        // Exibe o operador responsável ou indica solicitação pública
        const elemAdmin = document.getElementById('detalhes-admin');
        if (chamado.nome_admin) {
            elemAdmin.innerHTML = `<i class="bi bi-person-badge text-primary me-1"></i> ${chamado.nome_admin}`;
        } else if (chamado.admin_id) {
            elemAdmin.innerHTML = `<i class="bi bi-person-badge text-primary me-1"></i> Admin #${chamado.admin_id}`;
        } else {
            elemAdmin.innerHTML = `<span class="badge bg-light text-secondary border">Cidadão Público</span>`;
        }

        // Formatação de Data
        const dataObj = new Date(chamado.data_criacao);
        document.getElementById('detalhes-data').textContent = dataObj.toLocaleString('pt-BR');

        // Badge Urgência
        const badgeUrgencia = {
            'Urgente': '<span class="badge bg-danger">URGENTE</span>',
            'Importante': '<span class="badge bg-warning text-dark">IMPORTANTE</span>',
            'Normal': '<span class="badge bg-info text-dark">NORMAL</span>'
        };
        document.getElementById('detalhes-urgencia').innerHTML = badgeUrgencia[chamado.classe] || '';

        // Badge Status
        const badgeStatus = {
            'Pendente': '<span class="badge bg-secondary">Pendente</span>',
            'Em Atendimento': '<span class="badge bg-primary">Em Atendimento</span>',
            'Concluído': '<span class="badge bg-success">Concluído</span>'
        };
        document.getElementById('detalhes-status').innerHTML = badgeStatus[chamado.status] || '';

        modalDetalhes.show();
    }
});

let instanceModalStatus = null;

function abrirModalStatus(id, statusAtual) {
    document.getElementById('status-chamado-id').value = id;
    document.getElementById('select-status').value = statusAtual;

    const modalElement = document.getElementById('modalStatus');
    
    // Obtém a instância existente ou cria uma nova
    instanceModalStatus = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    instanceModalStatus.show();
}

function salvarStatus(event) {
    event.preventDefault();

    const id = document.getElementById('status-chamado-id').value;
    const novoStatus = document.getElementById('select-status').value;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', novoStatus);

    fetch('api/atualizar_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.erro || 'Erro HTTP ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.sucesso) {
            // Atualiza a badge na tabela
            const containerBadge = document.querySelector(`.badge-status-${id}`);
            if (containerBadge) {
                const badges = {
                    'Pendente': '<span class="badge bg-secondary">Pendente</span>',
                    'Em Atendimento': '<span class="badge bg-primary">Em Atendimento</span>',
                    'Concluído': '<span class="badge bg-success">Concluído</span>'
                };
                containerBadge.innerHTML = badges[novoStatus] || '';
            }

            // Garante o fechamento correto do modal
            const modalElement = document.getElementById('modalStatus');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            
            if (modalInstance) {
                modalInstance.hide();
            }
        } else {
            alert('Erro: ' + (data.erro || 'Não foi possível alterar o status.'));
        }
    })
    .catch(err => {
        console.error('Erro na requisição:', err);
        alert('Erro ao processar requisição: ' + err.message);
    });
}