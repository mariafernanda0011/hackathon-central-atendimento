<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

ini_set('session.cookie_samesite', 'Lax');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Conexão com o banco de dados
    $dbPath = __DIR__ . '/../../database/database_setup.php';
    if (!file_exists($dbPath)) {
        $dbPath = __DIR__ . '/../config/database.php';
    }

    if (!file_exists($dbPath)) {
        throw new Exception("Arquivo de conexão com o banco não encontrado.");
    }
    require_once $dbPath;

    // 2. Identifica o Administrador pela Sessão (via ID ou Email)
    $admin_id = null;
    $isAdmin = false;

    if (!empty($_SESSION['usuario_logado'])) {
        $sessao = $_SESSION['usuario_logado'];

        // Se o ID já estiver na sessão
        if (is_array($sessao) && !empty($sessao['id'])) {
            $admin_id = (int)$sessao['id'];
            $isAdmin = true;
        }
        // Se a sessão tiver o E-MAIL, busca o ID no banco de dados
        elseif (is_array($sessao) && !empty($sessao['email'])) {
            $stmtUser = $pdo->prepare("SELECT id FROM administradores WHERE email = :email LIMIT 1");
            $stmtUser->execute([':email' => $sessao['email']]);
            $userFound = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($userFound && isset($userFound['id'])) {
                $admin_id = (int)$userFound['id'];
                $isAdmin = true;
            }
        }
        // Se for salvo apenas o ID diretamente como número/string
        elseif (is_numeric($sessao)) {
            $admin_id = (int)$sessao;
            $isAdmin = true;
        }
    }

    // 3. Recebe os dados do formulário
    $solicitante = trim($_POST['solicitante'] ?? '');
    $solicitacao = trim($_POST['solicitacao'] ?? '');
    $telefone    = trim($_POST['telefone'] ?? '');
    $endereco    = trim($_POST['endereco'] ?? '');
    $prioridade  = trim($_POST['prioridade'] ?? '');

    if (empty($solicitante) || empty($solicitacao) || empty($telefone) || empty($endereco) || empty($prioridade)) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Preencha todos os campos obrigatórios.']);
        exit();
    }

    $setor_id = filter_input(INPUT_POST, 'setor_id', FILTER_VALIDATE_INT);
    if (!$setor_id) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Selecione um setor responsável.']);
        exit();
    }

    // valida se o setor existe
    $chk = $pdo->prepare("SELECT id FROM setores WHERE id = :id AND ativo = 1");
    $chk->execute([':id' => $setor_id]);
    if (!$chk->fetch()) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Setor inválido.']);
        exit();
    }

    // 4. Insere a solicitação com o admin_id resolvido
    $sql = "INSERT INTO solicitacoes
        (nome_solicitante, descricao, contato, endereco, classe, setor_id, admin_id, status, data_criacao)
        VALUES (:solicitante, :descricao, :telefone, :endereco, :prioridade, :setor_id, :admin_id, 'Pendente', NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':solicitante' => $solicitante,
        ':descricao'   => $solicitacao,
        ':telefone'    => $telefone,
        ':endereco'    => $endereco,
        ':prioridade'  => $prioridade,
        ':setor_id'    => $setor_id,
        ':admin_id'    => $admin_id,
    ]);

    echo json_encode([
        'sucesso'  => true,
        'isAdmin'  => $isAdmin,
        'admin_id' => $admin_id
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'erro'    => 'Erro no servidor: ' . $e->getMessage()
    ]);
}
