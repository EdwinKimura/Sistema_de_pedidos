-- Criando a tabela de Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL
);

-- Criando a tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

-- Criando a tabela de Produtos
CREATE TABLE IF NOT EXISTS produtos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    categoria_id INT,
    CONSTRAINT fk_produto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

INSERT INTO categorias (nome) VALUES
    ('Lanche'),
    ('Acompanhamento'),
    ('Bebida'),
    ('Sobremesa');

-- Inserindo produtos (evita duplicação com ON CONFLICT)
INSERT INTO produtos (nome, descricao, preco, categoria_id) VALUES
    -- Lanches
    ('Hambúrguer Clássico', 'Delicioso hambúrguer com carne bovina, queijo e alface.', 15.00, (SELECT id FROM categorias WHERE nome = 'Lanche')),
    ('Cheeseburger', 'Hambúrguer com queijo derretido.', 17.00, (SELECT id FROM categorias WHERE nome = 'Lanche')),
    ('X-Bacon', 'Hambúrguer com bacon crocante.', 20.00, (SELECT id FROM categorias WHERE nome = 'Lanche')),
    ('Veggie Burger', 'Hambúrguer vegetariano com vegetais frescos.', 18.00, (SELECT id FROM categorias WHERE nome = 'Lanche')),
    ('Chicken Burger', 'Hambúrguer de frango empanado.', 19.00, (SELECT id FROM categorias WHERE nome = 'Lanche')),

    -- Acompanhamentos
    ('Batata Frita', 'Porção de batata frita crocante.', 10.00, (SELECT id FROM categorias WHERE nome = 'Acompanhamento')),
    ('Onion Rings', 'Anéis de cebola empanados.', 12.00, (SELECT id FROM categorias WHERE nome = 'Acompanhamento')),
    ('Salada', 'Salada fresca com molho especial.', 8.00, (SELECT id FROM categorias WHERE nome = 'Acompanhamento')),
    ('Chicken Nuggets', 'Porção de nuggets de frango.', 15.00, (SELECT id FROM categorias WHERE nome = 'Acompanhamento')),
    ('Mozzarella Sticks', 'Palitos de mozzarella empanados.', 14.00, (SELECT id FROM categorias WHERE nome = 'Acompanhamento')),

    -- Bebidas
    ('Coca-Cola', 'Refrigerante de cola.', 5.00, (SELECT id FROM categorias WHERE nome = 'Bebida')),
    ('Sprite', 'Refrigerante de limão.', 5.00, (SELECT id FROM categorias WHERE nome = 'Bebida')),
    ('Suco de Laranja', 'Suco natural de laranja.', 7.00, (SELECT id FROM categorias WHERE nome = 'Bebida')),
    ('Água', 'Garrafa de água mineral.', 3.00, (SELECT id FROM categorias WHERE nome = 'Bebida')),
    ('Chá Gelado', 'Chá gelado de pêssego.', 6.00, (SELECT id FROM categorias WHERE nome = 'Bebida')),

    -- Sobremesas
    ('Sorvete', 'Sorvete de baunilha com cobertura de chocolate.', 8.00, (SELECT id FROM categorias WHERE nome = 'Sobremesa')),
    ('Torta de Maçã', 'Torta de maçã caseira.', 10.00, (SELECT id FROM categorias WHERE nome = 'Sobremesa')),
    ('Brownie', 'Brownie de chocolate com nozes.', 12.00, (SELECT id FROM categorias WHERE nome = 'Sobremesa')),
    ('Milkshake', 'Milkshake de morango.', 15.00, (SELECT id FROM categorias WHERE nome = 'Sobremesa')),
    ('Petit Gateau', 'Bolo de chocolate com recheio cremoso e sorvete.', 18.00, (SELECT id FROM categorias WHERE nome = 'Sobremesa'));

-- Criando a tabela de Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id SERIAL PRIMARY KEY,
    cliente_id INT,
    status VARCHAR(50) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    criado_em BIGINT DEFAULT EXTRACT(EPOCH FROM NOW())::BIGINT,
    modificado_em BIGINT DEFAULT EXTRACT(EPOCH FROM NOW())::BIGINT,
    CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

-- Criando a tabela de Itens do Pedido
CREATE TABLE IF NOT EXISTS item_pedidos (
    id SERIAL PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL CHECK (quantidade > 0),
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION atualizar_modificado_em()
RETURNS TRIGGER AS $$
BEGIN
    NEW.modificado_em = EXTRACT(EPOCH FROM NOW())::BIGINT;
RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_atualizar_modificado_em
    BEFORE UPDATE ON pedidos
    FOR EACH ROW
    EXECUTE FUNCTION atualizar_modificado_em();
