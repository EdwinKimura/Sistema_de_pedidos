<?php 

namespace App\Application\Service;

use App\Domain\Entity\Categoria;
use App\Infrastructure\Repository\CategoriaRepository;

class CategoriaService
{
    public function __construct(private CategoriaRepository $repository)
    {
    }

    public function save(string $nome): Categoria
    {
        $categoria = new Categoria();
        $categoria->setNome($nome);
        return $this->repository->save($categoria);
    }

    public function list(): array
    {
        return $this->repository->list();
    }
}