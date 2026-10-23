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
            $this->linha(1, 'Clarice Lispector', 10, 'A Hora da Estrela', '39.90'),
            $this->linha(2, 'Machado de Assis', 20, 'Dom Casmurro', '0.10'),
            $this->linha(2, 'Machado de Assis', 21, 'Memórias Póstumas', '0.20'),
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

    public function testTotalGeralContaCadaLivroUmaUnicaVez(): void
    {
        $repositorio = $this->createStub(RelatorioRepositoryInterface::class);
        $repositorio->method('listarLivrosPorAutor')->willReturn([
            $this->linha(1, 'Autor A', 10, 'Livro escrito a quatro mãos', '10.00'),
            $this->linha(2, 'Autor B', 10, 'Livro escrito a quatro mãos', '10.00'),
            $this->linha(2, 'Autor B', 11, 'Livro só do Autor B', '5.50'),
        ]);
        $service = new RelatorioService($repositorio);
        $grupos = $service->gerarLivrosPorAutor();

        $total = $service->calcularTotalGeral($grupos);

        self::assertSame('15.50', $total);
    }

    private function linha(int $autorCodigo, string $autorNome, int $livroCodigo, string $titulo, string $valor): LinhaRelatorio
    {
        return new LinhaRelatorio(
            autorCodigo: $autorCodigo,
            autorNome: $autorNome,
            livroCodigo: $livroCodigo,
            titulo: $titulo,
            editora: 'Editora Teste',
            edicao: 1,
            anoPublicacao: '2000',
            valor: $valor,
            assuntos: null,
        );
    }

    public function testTotalGeralDeRelatorioVazioEZero(): void
    {
        $repositorio = $this->createStub(RelatorioRepositoryInterface::class);
        $service = new RelatorioService($repositorio);

        self::assertSame('0.00', $service->calcularTotalGeral([]));
    }
}
