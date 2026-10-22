<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use App\Entity\Livro;

interface LivroRepositoryInterface
{
    public function salvar(Livro $livro): void;
    public function buscarPorId(int $id): ?Livro;
    public function remover(Livro $livro): void;
    /** @return list<Livro> */
    public function listarTodos(): array;
}
