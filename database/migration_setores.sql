USE central_atendimento;

-- Tabela de setores
CREATE TABLE IF NOT EXISTS setores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255) DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE = InnoDB;

INSERT INTO
    setores (nome, slug, descricao)
VALUES (
        'Bombeiros',
        'bombeiros',
        'Incêndios, resgates e salvamentos'
    ),
    (
        'Polícia',
        'policia',
        'Segurança pública e ocorrências criminais'
    ),
    (
        'SAMU',
        'samu',
        'Emergências médicas e atendimento pré-hospitalar'
    )
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome);

-- Administradores ganham tipo + setor
ALTER TABLE administradores
ADD COLUMN tipo ENUM('geral', 'setor') NOT NULL DEFAULT 'geral' AFTER senha,
ADD COLUMN setor_id INT NULL AFTER tipo,
ADD CONSTRAINT fk_admin_setor FOREIGN KEY (setor_id) REFERENCES setores (id) ON DELETE SET NULL;

-- Solicitações ganham setor de destino
ALTER TABLE solicitacoes
ADD COLUMN setor_id INT NULL AFTER classe,
ADD CONSTRAINT fk_solic_setor FOREIGN KEY (setor_id) REFERENCES setores (id) ON DELETE SET NULL;