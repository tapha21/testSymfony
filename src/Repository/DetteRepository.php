<?php

namespace App\Repository;

use App\Entity\Dette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository;

/**
 * @extends ServiceEntityRepository<Dette>
 */
class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    //    /**
    //     * @return Dette[] Returns an array of Dette objects
    //     */
       public function PaginateDette(int $page, int $limit ): Paginator
       {
        $query=$this->createQueryBuilder('c')
            ->setFirstResult(($page-1)*$limit)
            ->setMaxResults($limit)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
       ;
       return new Paginator($query);
   }

   public function findByStatut(bool $isSolde, int $page, int $limit): Paginator
{
    $qb = $this->createQueryBuilder('d');

    if ($isSolde) {
        $qb->where('d.montant - d.montantVerser <= 0');
    } else {
        $qb->where('d.montant - d.montantVerser > 0');
    }
    $query = $qb
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getQuery();

    return new Paginator($query);
}

    //    public function findOneBySomeField($value): ?Dette
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
