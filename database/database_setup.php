<?php
$host = 'localhost';
$db = 'central_atendimento';
$user = 'root';
$pass = '';

function setupDatabase(): PDO
{
  global $host, $db, $user, $pass;
  try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    throw new Exception("Error Processing Request", 1);
  }
}

$pdo = setupDatabase();