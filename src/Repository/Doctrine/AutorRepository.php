<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Autor;
use App\Repository\Contract\AutorRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Autor>
 */
class AutorRepository extends ServiceEntityRepository implements AutorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autor::class);
    }

    #[\Override]
    public function salvar(Autor $autor): void
    {
        $this->getEntityManager()->persist($autor);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function remover(Autor $autor): void
    {
        $this->getEntityManager()->remove($autor);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function buscarPorId(int $id): ?Autor
    {
        return $this->find($id);
    }

    #[\Override]
    public function listarTodos(): array
    {
        return $this->findBy([], ['nome' => 'ASC']);
    }
}
