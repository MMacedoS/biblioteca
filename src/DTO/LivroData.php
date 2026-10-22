<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class LivroData
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 40, normalizer: 'trim')]
    public ?string $titulo = null;

    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 40, normalizer: 'trim')]
    public ?string $editora = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $edicao = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d{4}$/', message: 'Informe o ano com 4 dígitos.')]
    public ?string $anoPublicacao = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Assert\LessThan(value: 100000000)]
    public ?float $valor = null;

    /** @var Collection<int, Autor> */
    #[Assert\Count(min: 1, minMessage: 'Selecione ao menos um autor.')]
    public Collection $autores;

    /** @var Collection<int, Assunto> */
    public Collection $assuntos;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validarAno(ExecutionContextInterface $context): void
    {
        if ($this->anoPublicacao !== null && (int) $this->anoPublicacao > (int) date('Y')) {
            $context->buildViolation('O ano de publicação não pode ser no futuro.')
                ->atPath('anoPublicacao')
                ->addViolation();
        }
    }

    public static function fromEntity(Livro $livro): self
    {
        $data = new self();
        $data->titulo = $livro->getTitulo();
        $data->editora = $livro->getEditora();
        $data->edicao = $livro->getEdicao();
        $data->anoPublicacao = $livro->getAnoPublicacao();
        $data->valor = (float) $livro->getValor();
        $data->autores = new ArrayCollection($livro->getAutores()->toArray());
        $data->assuntos = new ArrayCollection($livro->getAssuntos()->toArray());

        return $data;
    }
}
