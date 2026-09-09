CREATE DATABASE IF NOT EXISTS central_atendimento 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE central_atendimento;

-- Tabela de Administradores (Sem rota de cadastro de usuário público)
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela Principal de Solicitações
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_solicitante VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    classe ENUM('Normal', 'Importante', 'Urgente') NOT NULL DEFAULT 'Normal',
    contato VARCHAR(50) NOT NULL,
    endereco VARCHAR(200) NOT NULL,
    status ENUM('Pendente', 'Em Atendimento', 'Resolvido') NOT NULL DEFAULT 'Pendente',
    admin_id INT NULL, -- NULL se criado pelo público; ID do admin se criado ou assumido na área restrita
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES administradores(id) ON DELETE SET NULL
) ENGINE=InnoDB;
