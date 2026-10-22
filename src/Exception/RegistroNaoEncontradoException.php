<?php

declare(strict_types=1);

namespace App\Exception;

final class RegistroNaoEncontradoException extends RegraDeNegocioException
{
    public static function autor(int $id): self
    {
        return new self(sprintf('Autor de código %d não encontrado.', $id));
    }
}
