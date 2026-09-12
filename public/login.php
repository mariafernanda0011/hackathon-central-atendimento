<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Central de Atendimento</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
</head>
<body class="bg-light py-5">

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <!-- Cabeçalho da Tela de Login -->
                        <div class="text-center mb-4">
                            <h1 class="h4 fw-bold text-dark mb-2">
                                <i class="bi bi-shield-lock-fill text-primary me-2"></i>Acesso Administrativo
                            </h1>
                            <p class="text-muted small">Área restrita para a gestão e triagem de chamados emergenciais.</p>
                        </div>

                        <!-- Alerta de Erro (Caso o login falhe no backend) -->
                        <?php if (isset($_GET['erro'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>E-mail ou senha incorretos.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>


                        <!-- Formulário de Login -->
                        <form action="../actions/fazer_login.php" method="POST">
                            
                            <!-- Campo E-mail -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small">
                                    <i class="bi bi-envelope me-1"></i>E-mail
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="admin@central.local" 
                                    required
                                >
                            </div>

                            <!-- Campo Senha -->
                            <div class="mb-4">
                                <label for="senha" class="form-label fw-semibold small">
                                    <i class="bi bi-key me-1"></i>Senha
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="senha" 
                                    name="senha" 
                                    placeholder="********" 
                                    required
                                >
                            </div>

                            <!-- Botão para entrar no Painel Administrativo (Submit do Form) -->
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>

                            <hr class="my-3 text-muted">

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>