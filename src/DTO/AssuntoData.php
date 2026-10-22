<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Assunto;
use Symfony\Component\Validator\Constraints as Assert;

final class AssuntoData
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 20)]
    public ?string $descricao = null;

    public static function fromEntity(Assunto $assunto): self
    {
        $data = new self();
        $data->descricao = $assunto->getDescricao();

        return $data;
    }
}
