<?php

declare(strict_types=1);

namespace App\Observer;

use App\Entity\Autor;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'antesDeCadastrar', entity: Autor::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'antesDeAlterar', entity: Autor::class)]
#[AsEntityListener(event: Events::postPersist, method: 'depoisDeCadastrar', entity: Autor::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'depoisDeAlterar', entity: Autor::class)]
#[AsEntityListener(event: Events::postRemove, method: 'depoisDeExcluir', entity: Autor::class)]
final class AutorObserver
{
    use NormalizaTexto;

    private const CAMPOS_DE_TEXTO = ['nome'];

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function antesDeCadastrar(Autor $autor): void
    {
        $autor->setNome($this->normalizarTexto((string) $autor->getNome()));
    }

    public function antesDeAlterar(Autor $autor, PreUpdateEventArgs $args): void
    {
        foreach (self::CAMPOS_DE_TEXTO as $campo) {
            if ($args->hasChangedField($campo)) {
                $args->setNewValue($campo, $this->normalizarTexto((string) $args->getNewValue($campo)));
            }
        }
    }

    public function depoisDeCadastrar(Autor $autor): void
    {
        $this->logger->info('Autor cadastrado.', [
            'codigo' => $autor->getId(),
            'nome' => $autor->getNome(),
        ]);
    }

    public function depoisDeAlterar(Autor $autor): void
    {
        $this->logger->info('Autor alterado.', [
            'codigo' => $autor->getId(),
            'nome' => $autor->getNome(),
        ]);
    }

    public function depoisDeExcluir(Autor $autor): void
    {
        $this->logger->info('Autor excluído.', [
            'nome' => $autor->getNome(),
        ]);
    }
}
