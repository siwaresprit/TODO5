<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }




    public function findAuthorsWithZeroBooks()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.books', 'b')
            ->groupBy('a.id')
            ->having('COUNT(b) = 0')
            ->getQuery()
            ->getResult();
    }
    

    public function findAuthorsByBookCountRangeDQL($minBooks, $maxBooks)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT a FROM App\Entity\Author a
                WHERE a.nb_books BETWEEN :minBooks AND :maxBooks";

        $query = $em->createQuery($dql)
            ->setParameter('minBooks', $minBooks)
            ->setParameter('maxBooks', $maxBooks);

        return $query->getResult();
    }



    public function findAuthorsZero()
    {
        $em = $this->getEntityManager();
        
        $query = $em->createQuery('
            SELECT a
            FROM App\Entity\Author a
            WHERE a NOT IN (
                SELECT b.author
                FROM App\Entity\Book b
            )
        ');
        
        return $query->getResult();
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
