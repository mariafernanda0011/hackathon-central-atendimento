<?php
require_once __DIR__ . '/../database/database_setup.php';
$setores = $pdo->query("SELECT id, nome, descricao FROM setores WHERE ativo = 1 ORDER BY nome")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

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

<body class="bg-light py-4">

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <!-- Cabeçalho do Formulário -->
                        <div class="mb-4 text-center">
                            <h1 class="h3 fw-bold text-dark mb-2">
                                <i class="bi bi-shield-exclamation text-danger me-2"></i>Central de Atendimento
                                Emergencial
                            </h1>
                            <p class="text-muted small">Preencha os campos abaixo para registrar sua solicitação. A
                                equipe analisará o chamado de acordo com o nível de urgência.</p>
                        </div>

                        <!-- Alerta de Sucesso (Exibido após o envio via JS) -->
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

                        <!-- Formulário -->
                        <form id="formulario-solicitacao" onsubmit="CriarSolicitacao(event)">
                            <!-- Nome -->
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
                            <!-- Telefone / Contato -->
                            <div class="mb-4">
                                <label for="telefone" class="form-label fw-semibold">
                                    <i class="bi bi-telephone me-1"></i>Telefone / Contato <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="telefone" name="telefone"
                                    placeholder="Ex: (33) 99999-9999" oninput="this.value = formatTelefone(this)"
                                    required>
                            </div>
                            <!-- Endereço -->
                            <div class="mb-4">
                                <label for="endereco" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>Endereço <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="endereco" name="endereco"
                                    placeholder="Ex: Rua Principal, 123 - Bairro Centro" required>
                            </div>

                            <div class="mb-4">
                                <label for="setor_id" class="form-label fw-semibold">
                                    <i class="bi bi-building me-1"></i>Setor Responsável <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="setor_id" name="setor_id" required>
                                    <option value="">Selecione o setor que deve atender...</option>
                                    <?php foreach ($setores as $s): ?>
                                        <option value="<?= (int)$s['id'] ?>">
                                            <?= htmlspecialchars($s['nome']) ?> — <?= htmlspecialchars($s['descricao']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Níveis de Urgência -->
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/solicitacao.js"></script>
</body>

</html>