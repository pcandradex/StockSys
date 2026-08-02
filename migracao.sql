-- ========================================================
-- MIGRAÇÃO: Histórico de movimentações de estoque
-- Execute este script no painel do banco de dados (phpMyAdmin
-- da Locaweb / DBaaS) ANTES de subir os novos arquivos PHP.
-- ========================================================

CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    tipo ENUM('entrada','saida','ajuste') NOT NULL,
    quantidade INT NOT NULL,
    saldo_anterior INT NOT NULL,
    saldo_novo INT NOT NULL,
    motivo VARCHAR(255) DEFAULT NULL,
    usuario_id INT DEFAULT NULL,
    usuario_nome VARCHAR(100) DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_produto FOREIGN KEY (produto_id)
        REFERENCES produtos(id) ON DELETE CASCADE,
    INDEX idx_mov_produto (produto_id),
    INDEX idx_mov_data (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
