<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personne>
 *
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function add(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Personne[] Returns an array of Personne objects
//     */
   public function findPersonByAgeInterval($ageMin, $ageMax): array
   {
        $queryBuilder = $this->createQueryBuilder('p');
            $this->addIntervalAge($queryBuilder, $ageMin, $ageMax);
            return $queryBuilder->getQuery()->getResult();
       ;
   }

   public function findPersonByAgeIntervalStatistic($ageMin, $ageMax): array
   {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('avg(p.age) as ageMoyen, count(p.id) as numberOfPersons'); // on récupère la moyenne d'âge dans un interval et on compte par rapport à l'id des personnes 
            $this->addIntervalAge($queryBuilder, $ageMin, $ageMax);
            return $queryBuilder->getQuery()->getScalarResult();  // getScalarResult() -> Permet d'avoir un résultat sous format scalaire (utilisé pour SUM ou COUNT)
    
   }

   private function addIntervalAge(QueryBuilder $queryBuilder, $ageMin, $ageMax){
        $queryBuilder->andWhere('p.age >= :ageMin and p.age <= :ageMax')
                 ->setParameters(['ageMin'=>$ageMin, 'ageMax'=>$ageMax]);
   }

//    public function findOneBySomeField($value): ?Personne
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
