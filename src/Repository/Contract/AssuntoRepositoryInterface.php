<?php

namespace App\Repository\Contract;

use App\Entity\Assunto;

interface AssuntoRepositoryInterface
{
    public function salvar(Assunto $assunto): void;
    public function buscarPorId(int $id): ?Assunto;
    public function remover(Assunto $assunto): void;
    /** @return list<Assunto> */
    public function listarTodos(): array;
}
