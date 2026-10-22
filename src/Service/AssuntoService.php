<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\AssuntoData;
use App\Entity\Assunto;
use App\Exception\AssuntoPossuiLivrosException;
use App\Exception\RegistroNaoEncontradoException;
use App\Repository\Contract\AssuntoRepositoryInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

final class AssuntoService
{
    public function __construct(
        private readonly AssuntoRepositoryInterface $assuntoRepository,
    ) {}

    /** @return list<Assunto> */
    public function listar(): array
    {
        return $this->assuntoRepository->listarTodos();
    }

    public function obter(int $id): Assunto
    {
        return $this->assuntoRepository->buscarPorId($id)
            ?? throw RegistroNaoEncontradoException::assunto($id);
    }

    public function criar(AssuntoData $data): Assunto
    {
        $assunto = new Assunto();
        $assunto->setDescricao((string) $data->descricao);

        $this->assuntoRepository->salvar($assunto);

        return $assunto;
    }

    public function atualizar(Assunto $assunto, AssuntoData $data): void
    {
        $assunto->setDescricao((string) $data->descricao);

        $this->assuntoRepository->salvar($assunto);
    }

    public function excluir(Assunto $assunto): void
    {
        try {
            $this->assuntoRepository->remover($assunto);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw AssuntoPossuiLivrosException::paraAssunto($assunto, $e);
        }
    }
}
