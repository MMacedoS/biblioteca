<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\DTO\LinhaRelatorio;
use App\Repository\Contract\RelatorioRepositoryInterface;
use Doctrine\DBAL\Connection;

final class RelatorioRepository implements RelatorioRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    /** @return list<LinhaRelatorio> */
    public function listarLivrosPorAutor(): array
    {
        $linhas = $this->connection->fetchAllAssociative(<<<'SQL'
            SELECT autor_codigo, autor_nome, livro_codigo, titulo, editora,
                   edicao, ano_publicacao, valor, assuntos
            FROM vw_livros_por_autor
            ORDER BY autor_nome, titulo
            SQL);

        return array_map(
            static fn(array $linha): LinhaRelatorio => new LinhaRelatorio(
                autorCodigo: (int) $linha['autor_codigo'],
                autorNome: $linha['autor_nome'],
                livroCodigo: (int) $linha['livro_codigo'],
                titulo: $linha['titulo'],
                editora: $linha['editora'],
                edicao: (int) $linha['edicao'],
                anoPublicacao: $linha['ano_publicacao'],
                valor: $linha['valor'],
                assuntos: $linha['assuntos'],
            ),
            $linhas,
        );
    }
}
