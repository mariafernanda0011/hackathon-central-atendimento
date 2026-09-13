<?php
// require_once '../../database/database_setup.php';

// $pdo = setupDatabase();

try {
  $solicitante = $_POST['solicitante'] ?? '';
  $solicitacao = $_POST['solicitacao'] ?? '';
  $telefone = $_POST['telefone'] ?? '';
  $endereco = $_POST['endereco'] ?? '';
  $prioridade = $_POST['prioridade'] ?? 'Normal';

  $status = "Pendente";

  echo json_encode([
    'solicitante' => $solicitante,
    'solicitacao' => $solicitacao,
    'telefone' => $telefone,
    'endereco' => $endereco,
    'prioridade' => $prioridade,
    'status' => $status
  ]);
  return;
  // if (empty($solicitante) || empty($solicitacao)) {
  //   throw new Exception("Solicitante e solicitação não podem estar vazios.");
  // }
  // if (!in_array($prioridade, ['Normal', 'Importante', 'Urgente'])) {
  //   throw new Exception("Prioridade inválida. Deve ser 'Normal', 'Importante' ou 'Urgente'.");
  // }
  // $sql = "INSERT INTO solicitacoes (solicitante, solicitacao, telefone, endereco, prioridade, status) VALUES (:solicitante, :solicitacao, :telefone, :endereco, :prioridade, :status) returning *";
  // $stmt = $pdo->prepare($sql);
  // $stmt->bindParam(':solicitante', $solicitante);
  // $stmt->bindParam(':solicitacao', $solicitacao);
  // $stmt->bindParam(':telefone', $telefone);
  // $stmt->bindParam(':endereco', $endereco);
  // $stmt->bindParam(':prioridade', $prioridade);
  // $stmt->bindParam(':status', $status);
  // $stmt->execute();
  // $solicitacao_obj = $stmt->fetch(PDO::FETCH_ASSOC);
  // echo json_encode($solicitacao_obj);
} catch (PDOException $e) {
  echo "Erro na conexão: " . $e->getMessage();
}
