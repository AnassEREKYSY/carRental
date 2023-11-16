<?php

namespace App\Repository;

use App\Entity\Vehicule;
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

    public function searchAvailableCars($dateDebut,$dateFin,Request $request,EntityManagerInterface $manager): void
    {
        $qb = $manager->createQueryBuilder();
        $qb->select('c')
        ->from(Commande::class, 'c')
        ->leftJoin('c.id_vehicule', 'v')
        ->where(
            $qb->expr()->orX(
                $qb->where($qb->expr()->not($qb->expr()->between('c.dateDebut' ,':datDebut',':dateFin')))
                ->andWhere($qb->expr()->not($qb->expr()->between('c.dateFin' ,':dateFin',':dateFin')))
                ->setParameter('monday', $monday->format('Y-m-d'))
                ->setParameter('sunday', $sunday->format('Y-m-d'))
            )
        )
        ->setParameter('searchTerm1', '%' . strtolower($form->get('champs')->getData()) . '%');
$result = $qb->getQuery()->getResult();
        $this->getEntityManager()->flush();
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
