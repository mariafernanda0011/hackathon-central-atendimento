<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Atendimento Emergencial</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/style.css">
</head>

<script>
function clearNumber(value) {
    return value.replace(/\D+/g, '');
}

function formatTelefone(telefone) {
    const value = clearNumber(telefone.value);

    return value
        .substring(0, 11)
        .replace(/(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{4,5})(\d{4})$/, '$1-$2');
}

function CriarSolicitacao(event) {
    event.preventDefault(); // Interrompe o envio padrão do formulário

    const alertBox = document.getElementById('alert');
    if (alertBox) {
        alertBox.classList.add('d-none');
        alertBox.classList.remove('d-flex');
    }
    const alertError = document.getElementById('alert-error');
    if (alertError) alertError.style.display = 'none';

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

    const formData = new FormData();
    formData.append('solicitante', solicitante);
    formData.append('solicitacao', solicitacao);
    formData.append('telefone', telefone);
    formData.append('endereco', endereco);
    formData.append('prioridade', prioridade);

    // Requisição tratando resposta como texto antes do JSON.parse para tratar erros do PHP
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

// Função auxiliar para exibir erros na tela sem usar alert()
function exibirErro(mensagem) {
    let alertError = document.getElementById('alert-error');
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
</script>

<body class="bg-light py-4">

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <div class="mb-4 text-center">
                            <h1 class="h3 fw-bold text-dark mb-2">
                                <i class="bi bi-shield-exclamation text-danger me-2"></i>Central de Atendimento
                                Emergencial
                            </h1>
                            <p class="text-muted small">Preencha os campos abaixo para registrar sua solicitação. A
                                equipe analisará o chamado de acordo com o nível de urgência.</p>
                        </div>

                        <div id="alert"
                            class="alert alert-success d-none justify-content-between align-items-center shadow-sm"
                            role="alert">
                            <div>
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <span>Solicitação registrada com sucesso!</span>
                            </div>
                            <button type="button" id="btn-ok-sucesso" class="btn btn-success btn-sm fw-bold px-3 ms-3">
                                OK
                            </button>
                        </div>

                        <form id="formulario-solicitacao" onsubmit="CriarSolicitacao(event)">

                            <div class="mb-4">
                                <label for="nome" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i>Nome Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" autofocus class="form-control" id="nome" name="nome"
                                    placeholder="Ex: Maria Fernanda Santos" required>
                            </div>

                            <!-- Descrição -->
                            <div class="mb-4">
                                <label for="descricao" class="form-label fw-semibold">
                                    <i class="bi bi-card-text me-1"></i>Descrição da Emergência <span
                                        class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="4"
                                    placeholder="Descreva detalhadamente o ocorrido..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="telefone" class="form-label fw-semibold">
                                    <i class="bi bi-telephone me-1"></i>Telefone / Contato <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="telefone" name="telefone"
                                    placeholder="Ex: (33) 99999-9999" oninput="this.value = formatTelefone(this)"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label for="endereco" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>Endereço <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="endereco" name="endereco"
                                    placeholder="Ex: Rua Principal, 123 - Bairro Centro" required>
                            </div>


                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Nível de Urgência <span
                                        class="text-danger">*</span>
                                </label>
                                <small class="text-muted d-block mb-2">Selecione a opção que melhor descreve a gravidade
                                    da situação:</small>

                                <div class="d-flex flex-column gap-2">

                                    <label
                                        class="priority-card border rounded p-3 d-flex align-items-start gap-3 cursor-pointer">
                                        <input type="radio" name="prioridade" value="Normal"
                                            class="form-check-input mt-1" required>
                                        <div>
                                            <span class="badge bg-info text-dark mb-1"> NORMAL </span>
                                            <p class="mb-0 small text-secondary">Ocorrências simples, solicitações de
                                                apoio, dúvidas ou situações sob controle.</p>
                                        </div>
                                    </label>


                                    <label
                                        class="priority-card border rounded p-3 d-flex align-items-start gap-3 cursor-pointer">
                                        <input type="radio" name="prioridade" value="Importante"
                                            class="form-check-input mt-1">
                                        <div>
                                            <span class="badge bg-warning text-dark mb-1"> IMPORTANTE </span>
                                            <p class="mb-0 small text-secondary">Situação grave ou risco potencial que
                                                necessita de atendimento rápido, mas sem risco de vida imediato.</p>
                                        </div>
                                    </label>

                                    <!-- URGENTE -->
                                    <label
                                        class="priority-card border rounded p-3 d-flex align-items-start gap-3 cursor-pointer">
                                        <input type="radio" name="prioridade" value="Urgente"
                                            class="form-check-input mt-1">
                                        <div>
                                            <span class="badge bg-danger mb-1"> URGENTE </span>
                                            <p class="mb-0 small text-secondary">Risco imediato à vida, acidentes
                                                graves, incêndios ou situações que exigem socorro instantâneo.</p>
                                        </div>
                                    </label>

                                </div>
                            </div>

                            <!-- Botão de Envio -->
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bi bi-send-fill me-2"></i>Enviar Solicitação
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>