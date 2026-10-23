<?php

declare(strict_types=1);

namespace App\Observer;

use App\Entity\Assunto;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'antesDeCadastrar', entity: Assunto::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'antesDeAlterar', entity: Assunto::class)]
#[AsEntityListener(event: Events::postPersist, method: 'depoisDeCadastrar', entity: Assunto::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'depoisDeAlterar', entity: Assunto::class)]
#[AsEntityListener(event: Events::postRemove, method: 'depoisDeExcluir', entity: Assunto::class)]
final class AssuntoObserver
{
    use NormalizaTexto;

    private const CAMPOS_DE_TEXTO = ['descricao'];

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function antesDeCadastrar(Assunto $assunto): void
    {
        $assunto->setDescricao($this->normalizarTexto((string) $assunto->getDescricao()));
    }

    public function antesDeAlterar(Assunto $assunto, PreUpdateEventArgs $args): void
    {
        foreach (self::CAMPOS_DE_TEXTO as $campo) {
            if ($args->hasChangedField($campo)) {
                $args->setNewValue($campo, $this->normalizarTexto((string) $args->getNewValue($campo)));
            }
        }
    }

    public function depoisDeCadastrar(Assunto $assunto): void
    {
        $this->logger->info('Assunto cadastrado.', [
            'codigo' => $assunto->getId(),
            'nome' => $assunto->getDescricao(),
        ]);
    }

    public function depoisDeAlterar(Assunto $assunto): void
    {
        $this->logger->info('Assunto alterado.', [
            'codigo' => $assunto->getId(),
            'nome' => $assunto->getDescricao(),
        ]);
    }

    public function depoisDeExcluir(Assunto $assunto): void
    {
        $this->logger->info('Assunto excluído.', [
            'nome' => $assunto->getDescricao(),
        ]);
    }
}
