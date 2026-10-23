<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class GrupoRelatorio
{
    /** @param list<LinhaRelatorio> $livros */
    public function __construct(
        public string $autorNome,
        public array $livros,
        public string $valorTotal,
    ) {}

    public function quantidade(): int
    {
        return count($this->livros);
    }
}
