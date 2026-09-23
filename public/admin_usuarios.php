<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) { header("Location: /login"); exit(); }
if (($_SESSION['usuario_logado']['tipo'] ?? 'geral') !== 'geral') {
    http_response_code(403); exit('Acesso restrito ao administrador geral.');
}

require_once __DIR__ . '/../database/database_setup.php';

// Criar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $setor = filter_input(INPUT_POST, 'setor_id', FILTER_VALIDATE_INT);

    if ($nome && $email && $senha && $setor) {
        $stmt = $pdo->prepare("INSERT INTO administradores (nome, email, senha, tipo, setor_id)
                               VALUES (:n, :e, :s, 'setor', :setor)");
        $stmt->execute([
            ':n' => $nome, ':e' => $email,
            ':s' => password_hash($senha, PASSWORD_BCRYPT),
            ':setor' => $setor,
        ]);
    }
}

// Excluir
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM administradores WHERE id = :id AND tipo = 'setor'")
        ->execute([':id' => (int)$_GET['del']]);
    header("Location: /admin_usuarios.php"); exit;
}

$setores = $pdo->query("SELECT id, nome FROM setores WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$admins  = $pdo->query("SELECT a.id, a.nome, a.email, s.nome AS setor
                        FROM administradores a
                        LEFT JOIN setores s ON a.setor_id = s.id
                        WHERE a.tipo = 'setor'
                        ORDER BY s.nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Administradores por Setor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold"><i class="bi bi-people-fill text-primary"></i> Admins por Setor</h3>
    <a href="/admin" class="btn btn-outline-secondary btn-sm">← Voltar</a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h5 class="h6 fw-bold mb-3">Cadastrar novo admin de setor</h5>
      <form method="POST" class="row g-2">
        <div class="col-md-3"><input name="nome" class="form-control" placeholder="Nome" required></div>
        <div class="col-md-3"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-3"><input name="senha" type="password" class="form-control" placeholder="Senha" required></div>
        <div class="col-md-2">
          <select name="setor_id" class="form-select" required>
            <option value="">Setor...</option>
            <?php foreach ($setores as $s): ?>
              <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-1"><button class="btn btn-primary w-100">OK</button></div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Nome</th><th>Email</th><th>Setor</th><th class="text-end pe-3">Ações</th></tr></thead>
      <tbody>
      <?php foreach ($admins as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['nome']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?></td>
          <td><span class="badge bg-info text-dark"><?= htmlspecialchars($a['setor'] ?? '—') ?></span></td>
          <td class="text-end pe-3">
            <a href="?del=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Remover este admin?')">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>