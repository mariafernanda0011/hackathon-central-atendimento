<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Atendimento Emergencial</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex align-items-center min-vh-100 py-4">

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">

                <div class="card shadow-sm border-0 p-4 p-md-5">
                    <div class="card-body">
                        
                        <!-- Título e Instrução -->
                        <h1 class="h3 fw-bold text-dark mb-3">
                            <i class="bi bi-shield-exclamation text-danger me-2"></i>Central de Atendimento Emergencial
                        </h1>
                        <p class="text-muted mb-4">
                            Selecione a opção desejada para continuar no sistema.
                        </p>
                        
                        <!-- Botões de ação -->
                        <div class="d-grid gap-3">
                            <a href="/solicitacao"
                                class="btn btn-danger btn-lg py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-plus-circle-fill fs-4"></i>
                                <span>Registrar Chamado</span>
                            </a>
                            <a href="/login"
                                class="btn btn-outline-primary btn-lg py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-shield-lock-fill fs-4"></i>
                                <span>Login Administrativo</span>
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Rodapé -->
                <p class="text-muted small mt-4">
                    Em caso de risco iminente, contate também os serviços públicos de emergência da sua região.
                </p>

            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>