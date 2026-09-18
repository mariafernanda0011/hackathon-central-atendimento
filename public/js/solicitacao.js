/**
 * Remove todos os caracteres não numéricos de uma string.
 * @param {string} value - Texto de entrada.
 * @returns {string} Texto contendo apenas dígitos.
 */
function clearNumber(value) {
    return value.replace(/\D+/g, '');
}


/**
 * Aplica a máscara dinâmica de telefone (DD) 9XXXX-XXXX ou (DD) XXXX-XXXX.
 * @param {HTMLInputElement} inputTelefone - O elemento de input do formulário.
 * @returns {string} Valor formatado com máscara.
 */
function formatTelefone(telefone) {
    const value = clearNumber(telefone.value);

    return value
        .substring(0, 11)
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{4,5})(\d{4})$/, '$1-$2');
}


/**
 * Exibe mensagens de erro dinâmicas no topo do formulário sem recarregar a página.
 * @param {string} mensagem - Texto da mensagem de erro.
 */
function exibirErro(mensagem) {
    let alertError = document.getElementById('alert-error');

    // Cria elemento de alerta caso ainda não exista na página
    if (!alertError) {
        alertError = document.createElement('div');
        alertError.id = 'alert-error';
        alertError.className = 'alert alert-danger alert-dismissible fade show mb-4';
        alertError.role = 'alert';

        const container = document.querySelector('.card-body');
        const alertSuccess = document.getElementById('alert');
        container.insertBefore(alertError, alertSuccess);
    }
    alertError.innerHTML =
        `<i class="bi bi-exclamation-triangle-fill me-2"></i>${mensagem}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    alertError.style.display = 'block';
}


/**
 * Processa o envio do formulário de solicitação emergencial via AJAX (Fetch API).
 * @param {Event} event - Evento do formulário no submit.
 */
function CriarSolicitacao(event) {
    event.preventDefault(); // Interrompe o envio padrão do formulário

    // Oculta alertas pré-existentes
    const alertBox = document.getElementById('alert');
    if (alertBox) {
        alertBox.classList.add('d-none');
        alertBox.classList.remove('d-flex');
    }
    const alertError = document.getElementById('alert-error');
    if (alertError) {
        alertError.style.display = 'none';
    }

    // Captura valores do formulário
    const solicitante = document.getElementById('nome').value;
    const solicitacao = document.getElementById('descricao').value;
    const telefone = document.getElementById('telefone').value;
    const endereco = document.getElementById('endereco').value;
    const prioridadeSelected = document.querySelector('input[name="prioridade"]:checked');

    // Validação dos campos no Front-end
    if (!solicitante || !solicitacao || !telefone || !endereco || !prioridadeSelected) {
        exibirErro('Por favor, preencha todos os campos obrigatórios.');
        return;
    }

    const prioridade = prioridadeSelected.value;

    if (!['Normal', 'Importante', 'Urgente'].includes(prioridade)) {
        exibirErro('Prioridade inválida. Selecione uma das opções disponíveis.');
        return;
    }

    const numTelefone = clearNumber(telefone);
    if (numTelefone.length !== 10 && numTelefone.length !== 11) {
        exibirErro('Telefone inválido. Certifique-se de ter digitado com o DDD (10 ou 11 dígitos).');
        return;
    }

    // Monta o FormData para envio via POST
    const formData = new FormData();
    formData.append('solicitante', solicitante);
    formData.append('solicitacao', solicitacao);
    formData.append('telefone', telefone);
    formData.append('endereco', endereco);
    formData.append('prioridade', prioridade);

    // Requisição HTTP para a API
    fetch('api/criar_solicitacao.php', {
            method: 'POST',
            body: formData
    })
    .then(async response => {
        const text = await response.text();

        if (!response.ok) {
            try {
                const jsonErr = JSON.parse(text);
                throw new Error(jsonErr.erro || `Erro HTTP ${response.status}`);
            } catch (e) {
                throw new Error(text || `Erro HTTP ${response.status}`);
            }
        }

        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error("Resposta inválida do servidor. Verifique o console.");
        }
    })
    .then(data => {
        console.log('Sucesso:', data);

        // 1. Reseta e oculta o formulário
        const formulario = document.getElementById('formulario-solicitacao');
        if (formulario) {
            formulario.reset();
            formulario.style.display = 'none';
        }

        // 2. Exibe a mensagem de alerta (Flexbox)
        const alertBox = document.getElementById('alert');
        if (alertBox) {
            alertBox.classList.remove('d-none');
            alertBox.classList.add('d-flex');
        }

        // 3. Ação do Botão OK
        const btnOk = document.getElementById('btn-ok-sucesso');
        if (btnOk) {
            btnOk.onclick = function() {
                // Se a solicitação foi enviada de dentro de um IFRAME/MODAL no Painel Admin
                if (window.self !== window.top) {
                    try {
                        // 1. Tenta atualizar a tabela do admin sem recarregar a página (se houver uma função para isso)
                        if (typeof window.top.carregarSolicitacoes === 'function') {
                            window.top.carregarSolicitacoes();
                        }

                        // 2. Busca a instância do Modal do Bootstrap no documento pai e fecha
                        const modalElement = window.top.document.querySelector('.modal.show');
                        if (modalElement && window.top.bootstrap) {
                            const modalInstance = window.top.bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                                return;
                            }
                        }
                    } catch (err) {
                        console.error('Erro ao fechar modal via JS:', err);
                    }

                    // Fallback caso não consiga fechar via Bootstrap JS: clica no botão de fechar (X) do modal no pai
                    const btnFecharModal = window.top.document.querySelector(
                        '.modal.show .btn-close, .modal.show [data-bs-dismiss="modal"]');
                    if (btnFecharModal) {
                        btnFecharModal.click();
                    } else {
                        // Se tudo falhar, recarrega só a página pai
                        window.top.location.reload();
                    }
                    return;
                }

                // Se estiver na tela pública (fora do Iframe), redireciona para a Home pública
                window.location.href = '/';
            };
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        exibirErro(error.message || 'Ocorreu um erro ao enviar sua solicitação. Tente novamente.');
    });
}