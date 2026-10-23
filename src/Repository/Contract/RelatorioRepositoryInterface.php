<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use App\DTO\LinhaRelatorio;

interface RelatorioRepositoryInterface
{
    /** @return list<LinhaRelatorio> */
    public function listarLivrosPorAutor(): array;
}
