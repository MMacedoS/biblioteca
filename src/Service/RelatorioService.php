<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\GrupoRelatorio;
use App\Repository\Contract\RelatorioRepositoryInterface;

final class RelatorioService
{
    public function __construct(
        private readonly RelatorioRepositoryInterface $relatorioRepository,
    ) {
    }

    /** @return list<GrupoRelatorio> */
    public function gerarLivrosPorAutor(): array
    {
        $grupos = [];

        foreach ($this->relatorioRepository->listarLivrosPorAutor() as $linha) {
            $codigo = $linha->autorCodigo;

            $grupos[$codigo]['autorNome'] = $linha->autorNome;
            $grupos[$codigo]['livros'][] = $linha;
            $grupos[$codigo]['centavos'] = ($grupos[$codigo]['centavos'] ?? 0) + $this->paraCentavos($linha->valor);
        }

        return array_values(array_map(
            fn (array $grupo): GrupoRelatorio => new GrupoRelatorio(
                autorNome: $grupo['autorNome'],
                livros: $grupo['livros'],
                valorTotal: $this->deCentavos($grupo['centavos']),
            ),
            $grupos,
        ));
    }

    private function paraCentavos(string $valor): int
    {
        return (int) str_replace('.', '', $valor);
    }

    private function deCentavos(int $centavos): string
    {
        return sprintf('%d.%02d', intdiv($centavos, 100), $centavos % 100);
    }
}
