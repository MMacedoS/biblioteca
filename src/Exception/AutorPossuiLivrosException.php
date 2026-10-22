<?php

declare(strict_types=1);

namespace App\Exception;

use App\Entity\Autor;

final class AutorPossuiLivrosException extends RegraDeNegocioException
{
    public static function paraAutor(Autor $autor, ?\Throwable $anterior = null): self
    {
        return new self(
            sprintf('O autor "%s" não pode ser excluído porque possui livros cadastrados.', $autor->getNome()),
            0,
            $anterior,
        );
    }
}
