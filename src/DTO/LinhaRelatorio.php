<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class LinhaRelatorio
{
    public function __construct(
        public int $autorCodigo,
        public string $autorNome,
        public int $livroCodigo,
        public string $titulo,
        public string $editora,
        public int $edicao,
        public string $anoPublicacao,
        public string $valor,
        public ?string $assuntos,
    ) {}
}
