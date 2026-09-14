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
        return value.replace(/\D+/g, '')
    }

    function formatTelefone(telefone) {
        const value = clearNumber(telefone.value)

        return value
            .substring(0, 11)
            .replace(/(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4,5})(\d{4})$/, '$1-$2')
    }

    function CriarSolicitacao(event) {
        event.preventDefault(); // Evita o envio padrão do formulário
        const solicitante = document.getElementById('nome').value;
        const solicitacao = document.getElementById('descricao').value;
        const telefone = document.getElementById('telefone').value;
        const endereco = document.getElementById('endereco').value;
        const prioridade = document.querySelector('input[name="prioridade"]:checked').value;

        //TODO: Remover alert e adicionar uma mensagem de erro na tela, caso algum campo esteja inválido
        if (!solicitante || !solicitacao || !telefone || !endereco || !prioridade) {
            alert('Por favor, preencha todos os campos.');
            return;
        }
        if (!['Normal', 'Importante', 'Urgente'].includes(prioridade)) {
            alert('Prioridade inválida. Selecione uma das opções disponíveis.');
            return;
        }
        if (clearNumber(telefone).length !== 11 && clearNumber(telefone).length !== 10) {
            alert('Telefone inválido. Certifique-se de que tenha digitado corretamente.');
            return;
        }

        const formData = new FormData();
        formData.append('solicitante', solicitante);
        formData.append('solicitacao', solicitacao);
        formData.append('telefone', telefone);
        formData.append('endereco', endereco);
        formData.append('prioridade', prioridade);

        fetch('/api/criar-solicitacao', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);

                //TODO: Pensar se deve-se redirecionar o usuário para outra página ou apenas exibir a mensagem de sucesso
                const alertBox = document.getElementById('alert');
                alertBox.style.display = 'block';
                const formulario = document.getElementById('formulario-solicitacao');
                formulario.reset();
                formulario.style.display = 'none'; // Oculta o formulário após o envio
            })
            .catch(error => console.error('Erro:', error));
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



                        <div id="alert" class="alert alert-success alert-dismissible fade show" style="display: none;"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>Solicitação registrada com sucesso!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form id="formulario-solicitacao" onsubmit="CriarSolicitacao(event); return false;">

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