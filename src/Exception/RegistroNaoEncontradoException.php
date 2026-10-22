<?php

declare(strict_types=1);

namespace App\Exception;

final class RegistroNaoEncontradoException extends RegraDeNegocioException
{
    public static function autor(int $id): self
    {
        return new self(sprintf('Autor de código %d não encontrado.', $id));
    }

    public static function assunto(int $id): self
    {
        return new self(sprintf('Assunto de código %d não encontrado', $id));
    }

    public static function livro(int $id): self
    {
        return new self(sprintf('Livro de código %d não encontrado.', $id));
    }
}
