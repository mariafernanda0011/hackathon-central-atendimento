<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../login.php");
        exit();
    }

    $dbPath = __DIR__ . '/../../database/database_setup.php';

    if (file_exists($dbPath)) {
        require_once $dbPath;
    } else {
        die("Erro interno: Arquivo de conexão com o banco não foi encontrado.");
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = "Preencha todos os campos!";
        header("Location: /login.php");
        exit();
    }

    // Busca o administrador cadastrado no banco de dados
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, tipo, setor_id
                       FROM administradores WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Valida se o usuário existe e se a senha bate com o hash criptografado
    if ($admin && password_verify($senha, $admin['senha'])) {
    session_regenerate_id(true);

    $_SESSION['usuario_logado'] = [
        'id'       => (int)$admin['id'],
        'nome'     => $admin['nome'],
        'email'    => $admin['email'],
        'tipo'     => $admin['tipo'],      // 'geral' ou 'setor'
        'setor_id' => $admin['setor_id'] ? (int)$admin['setor_id'] : null,
    ];

    header("Location: /admin");
    exit();
} else {
    // Credenciais incorretas: Armazena mensagem de erro e volta pro login
    $_SESSION['erro_login'] = "E-mail ou senha incorretos!";
    header("Location: /login.php?erro=1");
    exit();
}