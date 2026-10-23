<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Livro;
use App\Repository\Contract\LivroRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livro>
 */
class LivroRepository extends ServiceEntityRepository implements LivroRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    #[\Override]
    public function salvar(Livro $livro): void
    {
        $this->getEntityManager()->persist($livro);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function remover(Livro $livro): void
    {
        $this->getEntityManager()->remove($livro);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function buscarPorId(int $id): ?Livro
    {
        return $this->find($id);
    }

    #[\Override]
    public function listarTodos(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.autores', 'a')
            ->addSelect('a')
            ->leftJoin('l.assuntos', 's')
            ->addSelect('s')
            ->orderBy('l.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
