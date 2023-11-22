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

    public function searchAvailableCarsForCustomer($dateDebut,$dateFin): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('v')
            ->from(Vehicule::class, 'v')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->notIn(
                        'v.id',
                        $this->getEntityManager()->createQueryBuilder()
                            ->select('v2.id')
                            ->from(Vehicule::class, 'v2')
                            ->innerJoin('v2.commandes', 'c2')
                            ->where(
                                $qb->expr()->orX(
                                    $qb->expr()->between('c2.date_depart', ':dateDebut', ':dateFin'),
                                    $qb->expr()->between('c2.date_fin', ':dateDebut', ':dateFin')
                                )
                            )
                            ->getDQL()
                    ),
                    $qb->expr()->eq('v.available', ':available')
                )
            )
            ->setParameters([
                'dateFin' => $dateFin,
                'dateDebut' => $dateDebut,
                'available' => 'oui'
            ]);
        
        $result=$qb->getQuery()->getResult();
        $return=array();
        if($result !=null){
            $return=$result;
        }
        return $return;
    }

    public function searchAvailableCarsForAdmin($dateDebut,$dateFin): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('v')
            ->from(Vehicule::class, 'v')
            ->where(
                    $qb->expr()->notIn(
                        'v.id',
                        $this->getEntityManager()->createQueryBuilder()
                            ->select('v2.id')
                            ->from(Vehicule::class, 'v2')
                            ->innerJoin('v2.commandes', 'c2')
                            ->where(
                                $qb->expr()->orX(
                                    $qb->expr()->between('c2.date_depart', ':dateDebut', ':dateFin'),
                                    $qb->expr()->between('c2.date_fin', ':dateDebut', ':dateFin')
                                )
                            )
                            ->getDQL()
                    )
            )
            ->setParameters([
                'dateFin' => $dateFin,
                'dateDebut' => $dateDebut
            ]);
        return $qb->getQuery()->getResult();
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
