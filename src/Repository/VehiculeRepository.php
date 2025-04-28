<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }
    public function searchByName(?string $search): array
{
    $qb = $this->createQueryBuilder('v');

    if ($search) {
        $qb->andWhere('v.name LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }

    return $qb->getQuery()->getResult();
}


    public function searchByNameWithEtat(?string $name): array
    {
        $qb = $this->createQueryBuilder('v');

        if ($name) {
            $qb->andWhere('v.name LIKE :name')
               ->setParameter('name', '%' . $name . '%');
        }

        $vehicules = $qb->orderBy('v.created', 'DESC')
                        ->getQuery()
                        ->getResult();

        // Ajouter l'état calculé pour chaque véhicule
        foreach ($vehicules as $vehicule) {
            $vehicule->getEtat();  // Appel de la méthode getEtat sur chaque véhicule
        }

        return $vehicules;
    }



//    /**
//     * @return Vehicule[] Returns an array of Vehicule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vehicule
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
