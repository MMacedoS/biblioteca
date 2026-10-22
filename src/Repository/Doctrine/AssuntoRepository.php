<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Assunto;
use App\Repository\Contract\AssuntoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Override;

/**
 * @extends ServiceEntityRepository<Assunto>
 */
class AssuntoRepository extends ServiceEntityRepository implements AssuntoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assunto::class);
    }

    #[Override]
    public function salvar(Assunto $assunto): void
    {
        $this->getEntityManager()->persist($assunto);
        $this->getEntityManager()->flush();
    }

    #[Override]
    public function remover(Assunto $assunto): void
    {
        $this->getEntityManager()->remove($assunto);
        $this->getEntityManager()->flush();
    }

    #[Override]
    public function buscarPorId(int $id): ?Assunto
    {
        return $this->find($id);
    }

    #[Override]
    public function listarTodos(): array
    {
        return $this->findBy([], ['descricao' => 'ASC']);
    }
}
