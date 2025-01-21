<?php

namespace App\DataFixtures;

use App\Domain\Entity\Categoria;
use App\Domain\Entity\Produto;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    public const CATEGORIA_LANCHE = 'Lanche';
    public const CATEGORIA_ACOMPANHAMENTO = 'Acompanhamento';
    public const CATEGORIA_BEBIDA = 'Bebida';
    public const CATEGORIA_SOBREMESA = 'Sobremesa';

    public function load(ObjectManager $manager): void
    {
        $categorias = [
            ["nome" => self::CATEGORIA_LANCHE],
            ["nome" => self::CATEGORIA_ACOMPANHAMENTO],
            ["nome" => self::CATEGORIA_BEBIDA],
            ["nome" => self::CATEGORIA_SOBREMESA],
        ];

        $produtosParaCadastro = [
            "Lanche" => [
                ['nome' => 'Hambúrguer Clássico', 'descricao' => 'Delicioso hambúrguer com carne bovina, queijo e alface.', 'preco' => 15.00, 'categoria' => 1],
                ['nome' => 'Cheeseburger', 'descricao' => 'Hambúrguer com queijo derretido.', 'preco' => 17.00, 'categoria' => 1],
                ['nome' => 'X-Bacon', 'descricao' => 'Hambúrguer com bacon crocante.', 'preco' => 20.00, 'categoria' => 1],
                ['nome' => 'Veggie Burger', 'descricao' => 'Hambúrguer vegetariano com vegetais frescos.', 'preco' => 18.00, 'categoria' => 1],
                ['nome' => 'Chicken Burger', 'descricao' => 'Hambúrguer de frango empanado.', 'preco' => 19.00, 'categoria' => 1],
            ],
            "Acompanhamento" => [
                ['nome' => 'Batata Frita', 'descricao' => 'Porção de batata frita crocante.', 'preco' => 10.00, 'categoria' => 2],
                ['nome' => 'Onion Rings', 'descricao' => 'Anéis de cebola empanados.', 'preco' => 12.00, 'categoria' => 2],
                ['nome' => 'Salada', 'descricao' => 'Salada fresca com molho especial.', 'preco' => 8.00, 'categoria' => 2],
                ['nome' => 'Chicken Nuggets', 'descricao' => 'Porção de nuggets de frango.', 'preco' => 15.00, 'categoria' => 2],
                ['nome' => 'Mozzarella Sticks', 'descricao' => 'Palitos de mozzarella empanados.', 'preco' => 14.00, 'categoria' => 2],
            ],
            "Bebida" => [
                ['nome' => 'Coca-Cola', 'descricao' => 'Refrigerante de cola.', 'preco' => 5.00, 'categoria' => 3],
                ['nome' => 'Sprite', 'descricao' => 'Refrigerante de limão.', 'preco' => 5.00, 'categoria' => 3],
                ['nome' => 'Suco de Laranja', 'descricao' => 'Suco natural de laranja.', 'preco' => 7.00, 'categoria' => 3],
                ['nome' => 'Água', 'descricao' => 'Garrafa de água mineral.', 'preco' => 3.00, 'categoria' => 3],
                ['nome' => 'Chá Gelado', 'descricao' => 'Chá gelado de pêssego.', 'preco' => 6.00, 'categoria' => 3],
            ],
            "Sobremesa" => [
                ['nome' => 'Sorvete', 'descricao' => 'Sorvete de baunilha com cobertura de chocolate.', 'preco' => 8.00, 'categoria' => 4],
                ['nome' => 'Torta de Maçã', 'descricao' => 'Torta de maçã caseira.', 'preco' => 10.00, 'categoria' => 4],
                ['nome' => 'Brownie', 'descricao' => 'Brownie de chocolate com nozes.', 'preco' => 12.00, 'categoria' => 4],
                ['nome' => 'Milkshake', 'descricao' => 'Milkshake de morango.', 'preco' => 15.00, 'categoria' => 4],
                ['nome' => 'Petit Gateau', 'descricao' => 'Bolo de chocolate com recheio cremoso e sorvete.', 'preco' => 18.00, 'categoria' => 4],
            ],
        ];

        foreach ($categorias as $dados) {
            $categoria = new Categoria();
            $categoria->setNome($dados['nome']);
            $manager->persist($categoria);

            foreach ($produtosParaCadastro[$dados['nome']] as $dataProduto) {
                $produto = new Produto();
                $produto->setNome($dataProduto['nome']);
                $produto->setDescricao($dataProduto['descricao']);
                $produto->setPreco($dataProduto['preco']);
                $produto->setCategoria($categoria);
                $manager->persist($produto);
            }
        }
        $manager->flush();
    }
}
