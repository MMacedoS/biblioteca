<?php

declare(strict_types=1);

namespace App\Observer;

use App\Entity\Livro;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'antesDeCadastrar', entity: Livro::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'antesDeAlterar', entity: Livro::class)]
#[AsEntityListener(event: Events::postPersist, method: 'depoisDeCadastrar', entity: Livro::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'depoisDeAlterar', entity: Livro::class)]
#[AsEntityListener(event: Events::postRemove, method: 'depoisDeExcluir', entity: Livro::class)]
final class LivroObserver
{
    use NormalizaTexto;

    private const CAMPOS_DE_TEXTO = ['titulo', 'editora'];

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function antesDeCadastrar(Livro $livro): void
    {
        $livro->setTitulo($this->normalizarTexto((string) $livro->getTitulo()));
        $livro->setEditora($this->normalizarTexto((string) $livro->getEditora()));
    }

    public function antesDeAlterar(Livro $livro, PreUpdateEventArgs $args): void
    {
        foreach (self::CAMPOS_DE_TEXTO as $campo) {
            if ($args->hasChangedField($campo)) {
                $args->setNewValue($campo, $this->normalizarTexto((string) $args->getNewValue($campo)));
            }
        }
    }

    public function depoisDeCadastrar(Livro $livro): void
    {
        $this->logger->info('Livro cadastrado.', [
            'codigo' => $livro->getId(),
            'titulo' => $livro->getTitulo(),
        ]);
    }

    public function depoisDeAlterar(Livro $livro): void
    {
        $this->logger->info('Livro alterado.', [
            'codigo' => $livro->getId(),
            'titulo' => $livro->getTitulo(),
        ]);
    }

    public function depoisDeExcluir(Livro $livro): void
    {
        $this->logger->info('Livro excluído.', [
            'titulo' => $livro->getTitulo(),
        ]);
    }
}
