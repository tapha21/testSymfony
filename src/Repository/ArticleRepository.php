<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    public function PaginateArticle(int $page, int $limit ): Paginator
    {
        // if ($libelle) {
        //     $queryBuilder->andWhere('a.libelle LIKE :libelle')
        //                  ->setParameter('libelle', '%' . $libelle . '%');
        // }
     $query=$this->createQueryBuilder('c')
         ->setFirstResult(($page-1)*$limit)
         ->setMaxResults($limit)
        ->orderBy('c.id', 'ASC')
        ->getQuery()
    ;
    return new Paginator($query);
}

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
