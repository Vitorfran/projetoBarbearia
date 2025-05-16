
-- Criando o banco de dados
CREATE DATABASE IF NOT EXISTS projetobarbearia;
USE projetobarbearia;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    admin BOOLEAN NOT NULL DEFAULT FALSE,
    tipo ENUM('cliente', 'profissional') NOT NULL DEFAULT 'cliente',
    foto VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_plano INT NULL COMMENT 'ID do plano associado ao usuário'
);

-- Tabela de serviços
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    duracao INT NOT NULL COMMENT 'Duração em minutos',
    preco DECIMAL(10,2) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

-- Tabela de relação entre profissionais e serviços
CREATE TABLE profissionais_servicos (
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,
    PRIMARY KEY (profissional_id, servico_id),
    FOREIGN KEY (profissional_id) REFERENCES usuarios(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- Tabela de horários de trabalho
CREATE TABLE horarios_trabalho (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '1=Dom, 2=Seg, ..., 7=Sab',
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    FOREIGN KEY (profissional_id) REFERENCES usuarios(id)
);

-- Tabela de agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NOT NULL,
    status ENUM('confirmado', 'cancelado', 'concluido') DEFAULT 'confirmado',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES usuarios(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- Tabela de indisponibilidades
CREATE TABLE indisponibilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NOT NULL,
    motivo VARCHAR(255),
    FOREIGN KEY (profissional_id) REFERENCES usuarios(id)
);

-- Tabela de planos
CREATE TABLE planos(
    id_plano INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(30),
    descricao VARCHAR(100),
    preco DECIMAL(10,2)     
);

-- Inserts iniciais na tabela de serviços
INSERT INTO servicos (id, nome, descricao, duracao, preco, ativo) VALUES
(1, 'Barba', 'Barba com estilo e atenção aos detalhes. Seu visual, nossa paixão.', 60, 30.00, 1),
(2, 'Sobrancelha', 'Transforme o olhar com sobrancelhas feitas sob medida para seu rosto.', 25, 25.00, 1),
(3, 'Cortes Modernos', 'Cortes modernos, estilizados e feitos sob medida para seu estilo pessoal.', 0, 50.00, 1);
