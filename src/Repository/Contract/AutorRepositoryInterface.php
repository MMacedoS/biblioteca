<?php

namespace App\Repository\Contract;

use App\Entity\Autor;

interface AutorRepositoryInterface
{
    public function salvar(Autor $autor): void;
    public function buscarPorId(int $id): ?Autor;
    public function remover(Autor $autor): void;
    public function listarTodos(): array;
}
