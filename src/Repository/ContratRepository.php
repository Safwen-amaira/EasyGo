<?php

namespace App\Repository;

use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
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
   // Dans ContratRepository
public function findContractsExpiringSoon(): array
{
    $entityManager = $this->getEntityManager();
    
    // Requête pour récupérer les contrats expirant dans les 5 prochains jours
    $qb = $entityManager->createQueryBuilder();
    $qb->select('c')
       ->from(Contrat::class, 'c')
       ->where('c.dateFin <= :dateLimit')
       ->andWhere('c.dateFin >= :currentDate')
       ->setParameter('dateLimit', new \DateTime('+5 days'))
       ->setParameter('currentDate', new \DateTime('now'));

    return $qb->getQuery()->getResult();
}
/* @Route("/notifications", name="notifications")
     */
    public function getExpiringContracts()
    {
        // Récupère les contrats expirants depuis la base de données
        $contratsExpirants = $this->getDoctrine()
            ->getRepository(Contrat::class)
            ->findExpiringContracts();  // Exemple de méthode personnalisée

        // Retourner une réponse JSON avec les contrats expirants
        return new JsonResponse($contratsExpirants);
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
