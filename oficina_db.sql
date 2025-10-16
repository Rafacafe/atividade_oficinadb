CREATE DATABASE oficina_db;
USE oficina_db;

-- Tabela de ordens de serviço
CREATE TABLE ordemservico (
    id_ordem INT PRIMARY KEY AUTO_INCREMENT,
    data_ordem DATE NOT NULL,
    descricao TEXT
);

-- Tabela de veículos
CREATE TABLE veiculo (
    id_veiculo INT PRIMARY KEY AUTO_INCREMENT,
    modelo_veiculo VARCHAR(100),
    placa_veiculo VARCHAR(10),
    id_ordem INT,
    FOREIGN KEY (id_ordem) REFERENCES ordemservico(id_ordem)
);

-- Tabela de clientes
CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome_cliente VARCHAR(100),
    id_veiculo INT,
    FOREIGN KEY (id_veiculo) REFERENCES veiculo(id_veiculo)
);

-- Tabela de serviços
CREATE TABLE servico (
    id_servico INT PRIMARY KEY AUTO_INCREMENT,
    tipo_servico VARCHAR(100),
    preco_servico DOUBLE
);

-- Relação entre ordem de serviço e os serviços realizados
CREATE TABLE inclui (
    id_inclui INT PRIMARY KEY AUTO_INCREMENT,
    id_ordem INT,
    id_servico INT,
    FOREIGN KEY (id_ordem) REFERENCES ordemservico(id_ordem),
    FOREIGN KEY (id_servico) REFERENCES servico(id_servico)
);