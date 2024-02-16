<?php

namespace App\Repository;

use App\Entity\Campagne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campagne>
 *
 * @method Campagne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campagne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campagne[]    findAll()
 * @method Campagne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampagneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campagne::class);
    }

    public function add(Campagne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Campagne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getMyCampains($id){
        return $this->createQueryBuilder('c')
            ->select('c.id, c.nom,c.isClosed,c.date, can.id as idCandidat, can.nom as nomCandidat')
            ->innerJoin('App\Entity\CampagneVotant','cv','WITH','cv.campagne = c.id AND IDENTITY(cv.votant) = :id')
            ->leftJoin('App\Entity\Vote','v','WITH','IDENTITY(v.votant) = IDENTITY(cv.votant) AND IDENTITY(v.campagne) = IDENTITY(cv.campagne)')
            ->leftJoin('v.candidat','can')
           // ->andWhere('IDENTITY(cv.votant) = :id')
            ->setParameter('id',$id)
            ->orderBy('c.date','DESC')
        ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function allCampains(){
        return $this->createQueryBuilder('c')
            ->select('c.id, c.nom,c.isClosed,c.date,COUNT(v.id) AS totalVotes, COUNT(cv.id) AS totalVotants')
            ->leftJoin('App\Entity\CampagneVotant','cv','WITH','cv.votant = c.id')
            ->leftJoin('App\Entity\Vote','v','WITH','IDENTITY(v.votant) = IDENTITY(cv.votant) AND IDENTITY(v.campagne) = IDENTITY(cv.campagne)')
            ->leftJoin('v.candidat','can')
              ->orderBy('c.date','DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Campagne[] Returns an array of Campagne objects
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

//    public function findOneBySomeField($value): ?Campagne
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
