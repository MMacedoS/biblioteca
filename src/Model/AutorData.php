<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Autor;
use Symfony\Component\Validator\Constraints as Assert;

final class AutorData
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 40)]
    public ?string $nome = null;

    public static function fromEntity(Autor $autor): self
    {
        $data = new self();
        $data->nome = $autor->getNome();

        return $data;
    }
}
