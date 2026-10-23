<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\AutorData;
use App\Entity\Autor;
use App\Exception\AutorPossuiLivrosException;
use App\Exception\RegistroNaoEncontradoException;
use App\Repository\Contract\AutorRepositoryInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

final class AutorService
{
    public function __construct(
        private readonly AutorRepositoryInterface $autorRepository,
    ) {
    }

    /** @return list<Autor> */
    public function listar(): array
    {
        return $this->autorRepository->listarTodos();
    }

    public function obter(int $id): Autor
    {
        return $this->autorRepository->buscarPorId($id)
            ?? throw RegistroNaoEncontradoException::autor($id);
    }

    public function criar(AutorData $data): Autor
    {
        $autor = new Autor();
        $autor->setNome((string) $data->nome);

        $this->autorRepository->salvar($autor);

        return $autor;
    }

    public function atualizar(Autor $autor, AutorData $data): void
    {
        $autor->setNome((string) $data->nome);

        $this->autorRepository->salvar($autor);
    }

    public function excluir(Autor $autor): void
    {
        try {
            $this->autorRepository->remover($autor);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw AutorPossuiLivrosException::paraAutor($autor, $e);
        }
    }
}
