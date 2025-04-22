<?php

namespace App\Repository;

use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
/**
 * @extends ServiceEntityRepository<Contrat>
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    public function searchByName(?string $name): array
    {
        $qb = $this->createQueryBuilder('v');
    
        if ($name) {
            $qb->andWhere('v.nomprenom LIKE :nomprenom')
               ->setParameter('nomprenom', '%' . $name . '%');
        }
    
        return $qb->orderBy('v.dateFin', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
    public function findContractsExpiringSoon()
    {
        $dateLimit = new \DateTime('+7 days'); // Limite de 7 jours à partir d'aujourd'hui
        return $this->createQueryBuilder('c')
            ->where('c.dateFin <= :dateLimit')
            ->andWhere('c.dateFin >= :currentDate')
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('currentDate', new \DateTime())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Contrat[] Returns an array of Contrat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contrat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
