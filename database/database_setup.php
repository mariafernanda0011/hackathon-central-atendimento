<?php
$host = 'localhost';
$db = 'CAE';
$user = 'postgres';
$pass = 'suasenha';

function setupDatabase(): PDO
{
  global $host, $db, $user, $pass;
  try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    throw new Exception("Error Processing Request", 1);
  }
}