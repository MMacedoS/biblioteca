<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\LinhaRelatorio;
use App\Repository\Contract\RelatorioRepositoryInterface;
use App\Service\RelatorioService;
use PHPUnit\Framework\TestCase;

final class RelatorioServiceTest extends TestCase
{
    public function testAgrupaLinhasPorAutorESomaOsValores(): void
    {
        $repositorio = $this->createStub(RelatorioRepositoryInterface::class);
        $repositorio->method('listarLivrosPorAutor')->willReturn([
            $this->linha(1, 'Clarice Lispector', 'A Hora da Estrela', '39.90'),
            $this->linha(2, 'Machado de Assis', 'Dom Casmurro', '0.10'),
            $this->linha(2, 'Machado de Assis', 'Memórias Póstumas', '0.20'),
        ]);
        $service = new RelatorioService($repositorio);

        $grupos = $service->gerarLivrosPorAutor();

        self::assertCount(2, $grupos);

        self::assertSame('Clarice Lispector', $grupos[0]->autorNome);
        self::assertSame(1, $grupos[0]->quantidade());
        self::assertSame('39.90', $grupos[0]->valorTotal);

        self::assertSame('Machado de Assis', $grupos[1]->autorNome);
        self::assertSame(2, $grupos[1]->quantidade());
        self::assertSame('0.30', $grupos[1]->valorTotal);
    }

    public function testDevolveListaVaziaQuandoNaoHaLivros(): void
    {
        $repositorio = $this->createStub(RelatorioRepositoryInterface::class);
        $repositorio->method('listarLivrosPorAutor')->willReturn([]);
        $service = new RelatorioService($repositorio);

        self::assertSame([], $service->gerarLivrosPorAutor());
    }

    private function linha(int $autorCodigo, string $autorNome, string $titulo, string $valor): LinhaRelatorio
    {
        return new LinhaRelatorio(
            autorCodigo: $autorCodigo,
            autorNome: $autorNome,
            livroCodigo: random_int(1, 1000),
            titulo: $titulo,
            editora: 'Editora Teste',
            edicao: 1,
            anoPublicacao: '2000',
            valor: $valor,
            assuntos: null,
        );
    }
}
