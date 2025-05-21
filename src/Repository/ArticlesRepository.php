<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    //    /**
    //     * @return Articles[] Returns an array of Articles objects
    //     */
        public function LimiteAticlesQuery(string $sort = 'a.id', string $direction = 'ASC'): Query
        {
            $field = ['a.id', 'a.name'];

            if(!in_array($sort, $field))
            {
                $sort = 'a.id';
            }

            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

            return $this->createQueryBuilder('a')
                ->orderBy($sort, $direction)
                ->getQuery()
                //->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
