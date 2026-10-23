<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use App\Entity\Autor;

interface AutorRepositoryInterface
{
    public function salvar(Autor $autor): void;

    public function buscarPorId(int $id): ?Autor;

    public function remover(Autor $autor): void;

    /** @return list<Autor> */
    public function listarTodos(): array;
}
