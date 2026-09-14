<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = "Preencha todos os campos!";
    header("Location: /login.php");
    exit();
}

/* 
 * --------------------------------------------------------------------------
 * AUTENTICAÇÃO
 * --------------------------------------------------------------------------
 * Nota: Aqui você pode substituir pela consulta PDO no seu banco de dados.
 * Exemplo fictício de credencial para testes:
 */
$usuario_valido = "admin@central.local";
$senha_valida = "admin123"; // Em produção, use password_verify() com hash!

if ($email === $usuario_valido && $senha === $senha_valida) {
    // Credenciais corretas: Cria a variável de sessão esperada pelo admin.php
    $_SESSION['usuario_logado'] = [
        'email' => $email,
        'nome'  => 'Operador Central'
    ];

    // Redireciona para o Painel Administrativo
    header("Location: /admin.php");
    exit();
} else {
    // Credenciais incorretas: Armazena mensagem de erro e volta pro login
    $_SESSION['erro_login'] = "E-mail ou senha incorretos!";
    header("Location: /login.php?erro=1");
    exit();
}