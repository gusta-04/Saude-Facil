-- ============================================================
--  SaúdeFácil – Banco de Dados MySQL (XAMPP)
--  Execute no phpMyAdmin ou via terminal: mysql -u root < banco_de_dados.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS saudefacil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saudefacil;

-- ----------------------------
--  Tabela: usuarios
-- ----------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120) NOT NULL,
    cpf         CHAR(11) NOT NULL UNIQUE,
    email       VARCHAR(120) NOT NULL UNIQUE,
    senha       VARCHAR(255) NOT NULL,
    telefone    VARCHAR(20),
    data_nasc   DATE,
    perfil      ENUM('paciente','admin') DEFAULT 'paciente',
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------
--  Tabela: unidades
-- ----------------------------
CREATE TABLE IF NOT EXISTS unidades (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(120) NOT NULL,
    endereco    VARCHAR(200),
    bairro      VARCHAR(80),
    cidade      VARCHAR(80) DEFAULT 'Passo Fundo',
    telefone    VARCHAR(20),
    ativo       TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ----------------------------
--  Tabela: especialidades
-- ----------------------------
CREATE TABLE IF NOT EXISTS especialidades (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100) NOT NULL,
    icone       VARCHAR(50) DEFAULT 'bi-heart-pulse'
) ENGINE=InnoDB;

-- ----------------------------
--  Tabela: medicos
-- ----------------------------
CREATE TABLE IF NOT EXISTS medicos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(120) NOT NULL,
    crm             VARCHAR(20) NOT NULL UNIQUE,
    especialidade_id INT NOT NULL,
    unidade_id      INT NOT NULL,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id),
    FOREIGN KEY (unidade_id) REFERENCES unidades(id)
) ENGINE=InnoDB;

-- ----------------------------
--  Tabela: agenda (horários disponíveis)
-- ----------------------------
CREATE TABLE IF NOT EXISTS agenda (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    medico_id   INT NOT NULL,
    data        DATE NOT NULL,
    hora        TIME NOT NULL,
    vagas       INT DEFAULT 1,
    FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB;

-- ----------------------------
--  Tabela: consultas
-- ----------------------------
CREATE TABLE IF NOT EXISTS consultas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    agenda_id   INT NOT NULL,
    status      ENUM('agendada','cancelada','realizada') DEFAULT 'agendada',
    observacao  TEXT,
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES usuarios(id),
    FOREIGN KEY (agenda_id) REFERENCES agenda(id)
) ENGINE=InnoDB;

-- ============================================================
--  DADOS INICIAIS
-- ============================================================

-- Admin padrão (senha: admin123)
INSERT INTO usuarios (nome, cpf, email, senha, perfil) VALUES
('Administrador', '00000000000', 'admin@saudefacil.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usfutrzmi', 'admin');

-- Especialidades
INSERT INTO especialidades (nome, icone) VALUES
('Clínica Geral',       'bi-person-heart'),
('Pediatria',           'bi-emoji-smile'),
('Ginecologia',         'bi-gender-female'),
('Cardiologia',         'bi-heart-pulse'),
('Ortopedia',           'bi-bandaid'),
('Psicologia',          'bi-brain'),
('Oftalmologia',        'bi-eye'),
('Dermatologia',        'bi-shield-plus');

-- Unidades de saúde
INSERT INTO unidades (nome, endereco, bairro) VALUES
('UBS Centro',          'Rua Moron, 1400',           'Centro'),
('UBS Petrópolis',      'Av. Brasil, 850',            'Petrópolis'),
('UBS São José',        'Rua das Acácias, 230',       'São José'),
('UBS Vera Cruz',       'Av. Presidente Vargas, 910', 'Vera Cruz');

-- Médicos
INSERT INTO medicos (nome, crm, especialidade_id, unidade_id) VALUES
('Dr. Carlos Andrade',   'CRM/RS 12345', 1, 1),
('Dra. Ana Souza',       'CRM/RS 23456', 2, 1),
('Dra. Fernanda Lima',   'CRM/RS 34567', 3, 2),
('Dr. Roberto Nunes',    'CRM/RS 45678', 4, 2),
('Dr. Marcos Oliveira',  'CRM/RS 56789', 5, 3),
('Dra. Juliana Costa',   'CRM/RS 67890', 6, 3),
('Dr. Paulo Mendes',     'CRM/RS 78901', 7, 4),
('Dra. Cláudia Ramos',   'CRM/RS 89012', 8, 4);

-- Agenda (próximos dias)
INSERT INTO agenda (medico_id, data, hora, vagas) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', 3),
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 3),
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00:00', 3),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 2),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 2),
(3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:30:00', 4),
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 4),
(4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', 2),
(5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', 3),
(6, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '15:00:00', 2),
(7, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', 3),
(8, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', 3);
