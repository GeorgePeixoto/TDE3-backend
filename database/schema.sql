-- ============================================================
-- Schema do banco de dados - Agencia de Viagens
-- MySQL 8.x
-- ============================================================

CREATE DATABASE IF NOT EXISTS agencia_viagens
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE agencia_viagens;

-- ------------------------------------------------------------
-- Destinos
-- ------------------------------------------------------------
CREATE TABLE destinos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(150) NOT NULL,
    pais        VARCHAR(100) NOT NULL,
    descricao   TEXT,
    clima       VARCHAR(100) NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Pacotes de viagem
-- ------------------------------------------------------------
CREATE TABLE pacotes (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome                VARCHAR(200) NOT NULL,
    destino_id          INT UNSIGNED NOT NULL,
    duracao_dias        SMALLINT UNSIGNED NOT NULL,
    preco               DECIMAL(10,2)    NOT NULL,
    vagas_disponiveis   INT UNSIGNED     NOT NULL DEFAULT 0,
    created_at          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pacotes_destino
        FOREIGN KEY (destino_id) REFERENCES destinos (id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Clientes
-- ------------------------------------------------------------
CREATE TABLE clientes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(150) NOT NULL,
    email       VARCHAR(200) NOT NULL UNIQUE,
    telefone    VARCHAR(20),
    cpf         CHAR(11)     NOT NULL UNIQUE,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Reservas
-- ------------------------------------------------------------
CREATE TABLE reservas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT UNSIGNED   NOT NULL,
    pacote_id       INT UNSIGNED   NOT NULL,
    data_reserva    DATE           NOT NULL,
    status          ENUM('pendente','confirmada','cancelada') NOT NULL DEFAULT 'pendente',
    valor_pago      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservas_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reservas_pacote
        FOREIGN KEY (pacote_id) REFERENCES pacotes (id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Dados de exemplo
-- ============================================================

-- Destinos
INSERT INTO destinos (nome, pais, descricao, clima) VALUES
('Paris',           'Franca',   'A cidade-luz, famosa pela Torre Eiffel e gastronomia.',               'Temperado'),
('Toquio',          'Japao',    'Metropole vibrante que mistura tradicao e tecnologia.',               'Subtropical'),
('Gramado',         'Brasil',   'Cidade serrana gaucha com clima europeu e chocolate artesanal.',       'Subtropical de altitude'),
('Cancun',          'Mexico',   'Praias caribenhas com aguas cristalinas e ruinas maias.',             'Tropical'),
('Lisboa',          'Portugal', 'Capital portuguesa com historia rica e pasteis de nata.',              'Mediterraneo');

-- Pacotes
INSERT INTO pacotes (nome, destino_id, duracao_dias, preco, vagas_disponiveis) VALUES
('Paris Romantica',       1,  7,  8500.00, 15),
('Toquio Express',        2,  5,  9200.00, 10),
('Inverno em Gramado',    3,  4,  2800.00, 20),
('Cancun All Inclusive',  4,  6,  7400.00, 12),
('Lisboa e Arredores',    5,  8,  6900.00, 18),
('Paris e Versalhes',     1, 10, 12500.00,  8);

-- Clientes (CPF armazenado apenas como digitos, sem pontuacao)
INSERT INTO clientes (nome, email, telefone, cpf) VALUES
('Ana Silva',       'ana.silva@email.com',       '(11) 99999-1111', '12345678900'),
('Bruno Oliveira',  'bruno.oliveira@email.com',  '(21) 98888-2222', '23456789011'),
('Carla Santos',    'carla.santos@email.com',    '(31) 97777-3333', '34567890122'),
('Diego Ferreira',  'diego.ferreira@email.com',  '(41) 96666-4444', '45678901233');

-- Reservas
INSERT INTO reservas (cliente_id, pacote_id, data_reserva, status, valor_pago) VALUES
(1, 1, '2026-07-10', 'confirmada',  8500.00),
(2, 3, '2026-06-15', 'pendente',    2800.00),
(3, 4, '2026-08-01', 'confirmada',  7400.00),
(4, 2, '2026-09-20', 'pendente',    9200.00),
(1, 5, '2026-10-05', 'cancelada',   6900.00);
