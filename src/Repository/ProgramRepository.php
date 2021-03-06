<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    public function findAllWithCategories()
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c' )
            ->addSelect('c')
            ->getQuery();

        return $qb->execute();
    }

    public function findAllWithActors()
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c' )
            ->innerJoin('p.actors', 'a' )
            ->addSelect('c','a')
            ->getQuery();

        return $qb->execute();
    }

    public function search($title) {
        $qb =  $this->_em-> createQueryBuilder()
            ->select('p')
            ->from(Program::class,'p')
            ->Where('p.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->getQuery();

        return $qb->execute();
    }


    // /**
    //  * @return Program[] Returns an array of Program objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Program
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
