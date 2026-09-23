<?php
session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header("Location: /login");
    exit();
}
// Conecta ao BD
require_once __DIR__ . '/../database/database_setup.php';

// Busca chamados e junta com a tabela administradores
$sessao     = $_SESSION['usuario_logado'];
$tipoAdmin  = $sessao['tipo'] ?? 'geral';
$setorAdmin = $sessao['setor_id'] ?? null;

$baseSelect = "SELECT s.*, a.nome AS nome_admin, st.nome AS nome_setor
               FROM solicitacoes s
               LEFT JOIN administradores a ON s.admin_id = a.id
               LEFT JOIN setores st        ON s.setor_id = st.id";

if ($tipoAdmin === 'setor' && $setorAdmin) {
    $stmt = $pdo->prepare($baseSelect . " WHERE s.setor_id = :setor ORDER BY s.id DESC");
    $stmt->execute([':setor' => $setorAdmin]);
} else {
    $stmt = $pdo->query($baseSelect . " ORDER BY s.id DESC");
}
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cálculo dinâmico dos contadores dos cards
$urgentes = 0;
$importantes = 0;
$normais = 0;

foreach ($solicitacoes as $chamado) {
    if ($chamado['classe'] === 'Urgente') $urgentes++;
    elseif ($chamado['classe'] === 'Importante') $importantes++;
    elseif ($chamado['classe'] === 'Normal') $normais++;
}

$total_chamados = count($solicitacoes);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Central de Atendimento</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <i class="bi bi-shield-lock-fill text-primary"></i> Painel Administrativo
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['usuario_logado']['nome']) ?>
                    <?php if (($_SESSION['usuario_logado']['tipo'] ?? 'geral') === 'geral'): ?>
                        <span class="badge bg-primary ms-1">Geral</span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark ms-1">
                            <?= htmlspecialchars($chamadoSetorNome ?? 'Setor') ?>
                        </span>
                    <?php endif; ?>
                </span>
                <a href="/api/logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-list-task text-primary me-2"></i>Gestão de Solicitações
                </h1>
                <p class="text-muted small mb-0">Triagem e acompanhamento dos chamados emergenciais cadastrados.</p>
            </div>

            <div class="d-flex gap-2">
                <?php if (($_SESSION['usuario_logado']['tipo'] ?? 'geral') === 'geral'): ?>
                    <a href="/admin_usuarios.php" class="btn btn-outline-primary btn-sm fw-bold">
                        <i class="bi bi-people me-1"></i> Admins por Setor
                    </a>
                <?php endif; ?>

                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal"
                    data-bs-target="#modalNovoChamado">
                    <i class="bi bi-plus-lg me-1"></i> Nova Solicitação
                </button>
            </div>
        </div>

        <div class="modal fade" id="modalNovoChamado" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title h6 fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>Nova Solicitação
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" style="height: 700px;">
                        <iframe src="/solicitacao" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards com Resumo -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Urgentes</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0"><?= $urgentes; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Importantes</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0"><?= $importantes; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Normais</span>
                        <h3 class="fw-bold text-info mt-1 mb-0"><?= $normais; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-dark border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Total Chamados</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0"><?= $total_chamados; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Chamados -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h2 class="h6 fw-bold mb-0 text-dark">Chamados Recebidos</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-4">Data/Hora</th>
                                <th scope="col">Solicitante</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Contato / Endereço</th>
                                <th scope="col">Setor</th>
                                <th scope="col">Urgência</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($solicitacoes)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Nenhuma solicitação cadastrada até o momento.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($solicitacoes as $chamado): ?>
                                    <tr id="linha-<?= $chamado['id']; ?>">
                                        <td class="ps-4 text-nowrap text-muted">
                                            <?= date('d/m/Y H:i', strtotime($chamado['data_criacao'])); ?>
                                        </td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($chamado['nome_solicitante']); ?>
                                        </td>
                                        <td>
                                            <div style="max-width: 230px;" class="text-truncate"
                                                title="<?= htmlspecialchars($chamado['descricao']); ?>">
                                                <?= htmlspecialchars($chamado['descricao']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div><i
                                                    class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($chamado['contato']); ?>
                                            </div>
                                            <div class="text-muted"><i
                                                    class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($chamado['endereco']); ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($chamado['nome_setor'] ?? '—') ?></td>
                                        <td>
                                            <?php if ($chamado['classe'] === 'Urgente'): ?>
                                                <span class="badge bg-danger">URGENTE</span>
                                            <?php elseif ($chamado['classe'] === 'Importante'): ?>
                                                <span class="badge bg-warning text-dark">IMPORTANTE</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">NORMAL</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-status-<?= $chamado['id']; ?>">
                                                <?php if ($chamado['status'] === 'Pendente'): ?>
                                                    <span class="badge bg-secondary">Pendente</span>
                                                <?php elseif ($chamado['status'] === 'Em Atendimento'): ?>
                                                    <span class="badge bg-primary">Em Atendimento</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Concluído</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Botão Ver Detalhes (Olho) -->
                                                <button type="button" class="btn btn-outline-secondary" title="Ver Detalhes"
                                                    onclick="abrirModalDetalhesPorId(<?= $chamado['id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <!-- Botão Alterar Status (Lápis) -->
                                                <button type="button" class="btn btn-outline-primary" title="Alterar Status"
                                                    onclick="abrirModalStatus(<?= $chamado['id']; ?>, '<?= $chamado['status']; ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- MODAL 1: Ver Detalhes do Chamado -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title h6 fw-bold" id="modalDetalhesLabel">
                        <i class="bi bi-card-heading me-2"></i>Detalhes da Solicitação #<span id="detalhes-id"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Solicitante:</strong>
                            <span id="detalhes-solicitante" class="fs-6 fw-semibold text-dark"></span>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Data e Hora de Registro:</strong>
                            <span id="detalhes-data" class="text-dark"></span>
                        </div>

                        <!-- CAMPO: Origem da Criacao / Tipo de Usuario -->
                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Cadastrado Por:</strong>
                            <span id="detalhes-admin" class="text-dark"></span>
                        </div>

                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Contato / Telefone:</strong>
                            <span id="detalhes-contato" class="text-dark"></span>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Nível de Urgência:</strong>
                            <div id="detalhes-urgencia" class="mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-muted d-block small">Status Atual:</strong>
                            <div id="detalhes-status" class="mt-1"></div>
                        </div>
                    </div>

                    <hr class="text-muted my-3">

                    <div class="mb-3">
                        <strong class="text-muted d-block small mb-1">Endereço da Ocorrência:</strong>
                        <div class="p-2 bg-light rounded border text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><span id="detalhes-endereco"></span>
                        </div>
                    </div>

                    <div>
                        <strong class="text-muted d-block small mb-1">Descrição Completa do Chamado:</strong>
                        <div class="p-3 bg-light rounded border text-dark"
                            style="white-space: pre-line; max-height: 200px; overflow-y: auto;" id="detalhes-descricao">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Alterar Status da Solicitação -->
    <div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title h6 fw-bold" id="modalStatusLabel">
                        <i class="bi bi-pencil-square me-2"></i>Alterar Status da Solicitação
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formAlterarStatus" onsubmit="salvarStatus(event)">
                    <div class="modal-body p-4">
                        <input type="hidden" id="status-chamado-id">

                        <div class="mb-3">
                            <label for="select-status" class="form-label fw-semibold">Selecione o novo status:</label>
                            <select id="select-status" class="form-select" required>
                                <option value="Pendente">Pendente</option>
                                <option value="Em Atendimento">Em Atendimento</option>
                                <option value="Concluído">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold">Salvar Alteração</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const solicitaesData = <?= json_encode($solicitacoes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        // Busca a solicitação pelo ID no array global
        function abrirModalDetalhesPorId(id) {
            const chamado = solicitaesData.find(item => Number(item.id) === Number(id));
            if (chamado) {
                abrirModalDetalhes(chamado);
            }
        }

        // Preenche e abre a Modal de Detalhes
        function abrirModalDetalhes(chamado) {
            document.getElementById('detalhes-id').textContent = chamado.id || '-';
            document.getElementById('detalhes-solicitante').textContent = chamado.nome_solicitante || 'Não informado';

            // Formatador de Data/Hora
            if (chamado.data_criacao) {
                const dataObj = new Date(chamado.data_criacao);
                document.getElementById('detalhes-data').textContent = dataObj.toLocaleString('pt-BR');
            } else {
                document.getElementById('detalhes-data').textContent = '-';
            }

            // Exibe se foi criado por um Usuário Comum ou por um Administrador
            const elemAdmin = document.getElementById('detalhes-admin');
            if (chamado.nome_admin) {
                elemAdmin.innerHTML = `<span class="fw-semibold text-dark">${chamado.nome_admin}</span> <span class="badge bg-dark ms-1">Administrativo</span>`;
            } else {
                elemAdmin.innerHTML = `<span class="badge bg-secondary">Usuário Comum</span> <span class="text-muted small">(Autoatendimento)</span>`;
            }

            document.getElementById('detalhes-contato').textContent = chamado.contato || 'Não informado';
            document.getElementById('detalhes-endereco').textContent = chamado.endereco || 'Não informado';
            document.getElementById('detalhes-descricao').textContent = chamado.descricao || 'Sem descrição.';

            // Renderiza Badge de Urgência
            const containerUrgencia = document.getElementById('detalhes-urgencia');
            if (chamado.classe === 'Urgente') {
                containerUrgencia.innerHTML = '<span class="badge bg-danger">URGENTE</span>';
            } else if (chamado.classe === 'Importante') {
                containerUrgencia.innerHTML = '<span class="badge bg-warning text-dark">IMPORTANTE</span>';
            } else {
                containerUrgencia.innerHTML = '<span class="badge bg-info text-dark">NORMAL</span>';
            }

            // Renderiza Badge de Status
            const containerStatus = document.getElementById('detalhes-status');
            if (chamado.status === 'Pendente') {
                containerStatus.innerHTML = '<span class="badge bg-secondary">Pendente</span>';
            } else if (chamado.status === 'Em Atendimento') {
                containerStatus.innerHTML = '<span class="badge bg-primary">Em Atendimento</span>';
            } else {
                containerStatus.innerHTML = '<span class="badge bg-success">Concluído</span>';
            }

            // Exibe o Modal
            const modalElement = document.getElementById('modalDetalhes');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        }
    </script>
    <script src="js/painel_admin.js"></script>
</body>

</html>