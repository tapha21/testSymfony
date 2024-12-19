<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    public function PaginateClient($page, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery();
    
        return $queryBuilder->getResult();
    }

    public function filterClients(FilterDTO $filters, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($filters->getNom()) {
            $qb->andWhere('c.nom LIKE :nom')
               ->setParameter('nom', '%' . $filters->getNom() . '%');
        }

        if ($filters->getTelephone()) {
            $qb->andWhere('c.telephone LIKE :telephone')
               ->setParameter('telephone', '%' . $filters->getTelephone() . '%');
        }

        return $qb->setMaxResults($limit)
                  ->setFirstResult(($page - 1) * $limit)
                  ->getQuery()
                  ->getResult();
    }

    public function countFilteredClients(FilterDTO $filters): int
    {
        $qb = $this->createQueryBuilder('c')
                   ->select('COUNT(c.id)');

        if ($filters->getNom()) {
            $qb->andWhere('c.nom LIKE :nom')
               ->setParameter('nom', '%' . $filters->getNom() . '%');
        }

        if ($filters->getTelephone()) {
            $qb->andWhere('c.telephone LIKE :telephone')
               ->setParameter('telephone', '%' . $filters->getTelephone() . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    

    //    public function findBy(ClientFilterDTO $clientfilterdto): array
    //    {
    //     $query=$this->createQueryBuilder('c');
    //     if(!empty($clientfilterdto->telephone)){
    //         $query=andWhere('c.telephone =:telephone')->setParameter('telephone',$clientfilterdto->telephone);
    //     }
    //     if(!empty($clientfilterdto->surname)){
    //         $query=andWhere('c.nom =:nom')->setParameter('nom',$clientfilterdto->surname);
    //     }
    //        return $query-> $this->createQueryBuilder('c');
    //        $query ->setParameter('val', $value) 
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
