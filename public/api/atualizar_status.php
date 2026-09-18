<?php
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    if (ob_get_length()) ob_clean();

    header('Content-Type: application/json; charset=utf-8');

    session_start();
    if (!isset($_SESSION['usuario_logado'])) {
        http_response_code(401);
        echo json_encode(['erro' => 'Sessão expirada. Faça login novamente.']);
        exit;
    }

    $dbPath = __DIR__ . '/../../database/database_setup.php';

    if (!file_exists($dbPath)) {
        http_response_code(500);
        echo json_encode(['erro' => 'Arquivo de banco de dados não encontrado.']);
        exit;
    }

    require_once $dbPath;

    try {
        $id     = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        $statusValidos = ['Pendente', 'Em Atendimento', 'Concluído'];

        if (!$id || !in_array($status, $statusValidos)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos para alteração de status.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE solicitacoes SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso!']);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar no banco: ' . $e->getMessage()]);
        exit;
    }
?>