<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\Vehicule;
use ArrayObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function remove(Vehicule $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function searchAvailableCars($dateDebut,$dateFin): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('v')
        ->from(Vehicule::class, 'v')
        ->leftJoin('v.commandes', 'c')
        ->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->not($qb->expr()->between('c.date_depart', ':dateDebut', ':dateFin')),
                    $qb->expr()->not($qb->expr()->between('c.date_fin', ':dateDebut', ':dateFin')),
                    $qb->expr()->not($qb->expr()->like('c.date_fin', ':dateFin')),
                    $qb->expr()->not($qb->expr()->like('c.date_depart', ':dateDebut')),
                    $qb->expr()->eq('v.available', ':available')
                ),
                $qb->expr()->eq('v.available', ':available')
            )
        )
        ->setParameters([
            'dateFin' => $dateFin,
            'dateDebut' => $dateDebut,
            'available' => 'oui'
        ]);
        return $qb->getQuery()->getresult();
        
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
