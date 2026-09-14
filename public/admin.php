<?php
session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header("Location: /login");
    exit();
}


$solicitacoes = [
    [
        'id' => 1,
        'solicitante' => 'Maria Fernanda Santos',
        'descricao' => 'Deslizamento de terra próximo à residência com risco de desabamento.',
        'telefone' => '(33) 99999-9999',
        'endereco' => 'Rua Principal, 123 - Bairro Centro',
        'prioridade' => 'Urgente',
        'status' => 'Pendente',
        'data' => '14/09/2026 08:30'
    ],
    [
        'id' => 2,
        'solicitante' => 'João Paulo Silva',
        'descricao' => 'Árvore de grande porte com galhos caídos bloqueando a via pública.',
        'telefone' => '(33) 98888-8888',
        'endereco' => 'Av. Brasil, 450 - Bairro Novo',
        'prioridade' => 'Importante',
        'status' => 'Em Atendimento',
        'data' => '14/09/2026 07:15'
    ],
    [
        'id' => 3,
        'solicitante' => 'Carlos Eduardo',
        'descricao' => 'Dúvida quanto ao fornecimento de água potável no bairro.',
        'telefone' => '(33) 97777-7777',
        'endereco' => 'Rua das Flores, 88 - Bairro Alto',
        'prioridade' => 'Normal',
        'status' => 'Concluído',
        'data' => '13/09/2026 18:40'
    ]
];
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
                    <i class="bi bi-person-circle me-1"></i> Operador
                </span>
                <a href="../actions/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">


        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-list-task text-primary me-2"></i>Gestão de Solicitacões
                </h1>
                <p class="text-muted small mb-0">Triagem e acompanhamento dos chamados emergenciais cadastrados.</p>
            </div>
        </div>


        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Urgentes</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">1</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Importantes</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0">1</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Normais</span>
                        <h3 class="fw-bold text-info mt-1 mb-0">1</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm border-start border-dark border-4">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold text-uppercase">Total Chamados</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">3</h3>
                    </div>
                </div>
            </div>
        </div>


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
                                <th scope="col">Urgência</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($solicitacoes as $chamado): ?>
                                <tr>
                                    <td class="ps-4 text-nowrap text-muted"><?= $chamado['data']; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($chamado['solicitante']); ?></td>
                                    <td>
                                        <div style="max-width: 250px;" class="text-truncate" title="<?= htmlspecialchars($chamado['descricao']); ?>">
                                            <?= htmlspecialchars($chamado['descricao']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($chamado['telefone']); ?></div>
                                        <div class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($chamado['endereco']); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($chamado['prioridade'] === 'Urgente'): ?>
                                            <span class="badge bg-danger">URGENTE</span>
                                        <?php elseif ($chamado['prioridade'] === 'Importante'): ?>
                                            <span class="badge bg-warning text-dark">IMPORTANTE</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark">NORMAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($chamado['status'] === 'Pendente'): ?>
                                            <span class="badge bg-secondary">Pendente</span>
                                        <?php elseif ($chamado['status'] === 'Em Atendimento'): ?>
                                            <span class="badge bg-primary">Em Atendimento</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Concluído</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" title="Ver Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" title="Alterar Status">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>