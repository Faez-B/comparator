<?php

namespace App\Repository;

use DateTime;
use App\Entity\Marque;
use App\Entity\Energie;
use App\Entity\Voiture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Voiture>
 *
 * @method Voiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voiture[]    findAll()
 * @method Voiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    public function add(Voiture $entity, bool $flush = false): void
    {
        $entity->setLastUpdate(new DateTime());
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Voiture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * It returns all the cars that match the given criteria
     *
     * @param Marque marque the brand of the car
     * @param Energie energie The energy type of the car (gasoline, diesel, electric, etc.)
     * @param float prixMax The maximum price of the car
     *
     * @return array of objects
     */
    public function search(
        Marque $marque = null,
        Energie $energie = null,
        float $prixMax = null,
        String $etat = null,
        float $conso = null,
        String $sortType = null
    ) {
        $query = $this->createQueryBuilder('v');
        $query->select('v');

        if ($marque instanceof Marque) {
            $query
                ->andWhere('v.marque = :marque')
                ->setParameter('marque', $marque);

        }

        if ($energie instanceof Energie) {

            $query
                ->andWhere('v.energie = :energie')
                ->setParameter('energie', $energie);
        }

        if ($prixMax) {

            $query
                ->andWhere('v.prix <= :prixMax')
                ->setParameter('prixMax', $prixMax);
        }

        if ($sortType) {
            switch ($sortType) {
                case 'alphabet':
                    $query->orderBy('v.nom', 'ASC');
                    break;

                case 'prixASC':
                    $query->orderBy('v.prix', 'ASC');
                    break;

                case 'prixDESC':
                    $query->orderBy('v.prix', 'DESC');
                    break;

                    // case 'consoElecASC':
                    //     $query->andWhere('v.energie.id = 4')
                    //         ->orderBy('v.consommation', 'ASC');
                    //     break;

                default:
                    break;
            }
        }

        if ($etat) {
            $query->andWhere('v.etat LIKE :etat')
                ->setParameter('etat', "%neuf%");
        }

        return $query->getQuery()->getResult();
    }

    /**
     * It returns the maximum price of all the vehicles in the database
     */
    public function getMaxPrix()
    {
        return $this->createQueryBuilder('v')
            ->select('MAX(v.prix)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * It returns all the cars after $year
     */
    public function getAllAfterYear(int $year)
    {
        return $this->createQueryBuilder('v')
            ->andWhere("v.annee > :year")
            ->setParameter("year", "%" . $year . "%")
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Voiture[] Returns an array of Voiture objects
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

    //    public function findOneBySomeField($value): ?Voiture
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
