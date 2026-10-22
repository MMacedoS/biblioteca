<?php

declare(strict_types=1);

namespace App\Exception;

use App\Entity\Assunto;

final class AssuntoPossuiLivrosException extends RegraDeNegocioException
{
    public static function paraAssunto(Assunto $assunto, ?\Throwable $anterior = null): self
    {
        return new self(
            sprintf('O assunto "%s" não pode ser excluído porque possui livros cadastrados.', $assunto->getDescricao()),
            0,
            $anterior,
        );
    }
}
