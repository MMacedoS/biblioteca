<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\LivroData;
use App\Entity\Livro;
use App\Exception\RegistroNaoEncontradoException;
use App\Repository\Contract\LivroRepositoryInterface;

final class LivroService
{
    public function __construct(
        private readonly LivroRepositoryInterface $livroRepository,
    ) {
    }

    /** @return list<Livro> */
    public function listar(): array
    {
        return $this->livroRepository->listarTodos();
    }

    public function obter(int $id): Livro
    {
        return $this->livroRepository->buscarPorId($id)
            ?? throw RegistroNaoEncontradoException::livro($id);
    }

    public function criar(LivroData $data): Livro
    {
        $livro = new Livro();
        $this->aplicarDados($livro, $data);

        $this->livroRepository->salvar($livro);

        return $livro;
    }

    public function atualizar(Livro $livro, LivroData $data): void
    {
        $this->aplicarDados($livro, $data);

        $this->livroRepository->salvar($livro);
    }

    public function excluir(Livro $livro): void
    {
        $this->livroRepository->remover($livro);
    }

    private function aplicarDados(Livro $livro, LivroData $data): void
    {
        $livro->setTitulo((string) $data->titulo);
        $livro->setEditora((string) $data->editora);
        $livro->setEdicao((int) $data->edicao);
        $livro->setAnoPublicacao((string) $data->anoPublicacao);
        $livro->setValor($this->formatarValor((float) $data->valor));

        foreach ($livro->getAutores()->toArray() as $autor) {
            if (!$data->autores->contains($autor)) {
                $livro->removeAutor($autor);
            }
        }
        foreach ($data->autores as $autor) {
            $livro->addAutor($autor);
        }

        foreach ($livro->getAssuntos()->toArray() as $assunto) {
            if (!$data->assuntos->contains($assunto)) {
                $livro->removeAssunto($assunto);
            }
        }
        foreach ($data->assuntos as $assunto) {
            $livro->addAssunto($assunto);
        }
    }

    private function formatarValor(float $valor): string
    {
        return number_format($valor, 2, '.', '');
    }
}
