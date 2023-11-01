<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }



    public function countPublishedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.published = :published')
            ->setParameter('published', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function countUnpublishedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.published = :published')
            ->setParameter('published', false)
            ->getQuery()
            ->getSingleScalarResult();
    }





    public function findBySearchTerm($searchTerm)
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.title LIKE :term')
        ->setParameter('term', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();
} 



public function findByTime()
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->where('b.publicationDate < :year')
        ->andWhere('a.nb_books > 10')
        ->setParameter('year', '2023-01-01')
        ->getQuery()
        ->getResult();
}


public function updateBooksCategory()
{
    $queryBuilder = $this->createQueryBuilder('b');
    $queryBuilder
        ->update(Book::class, 'b')
        ->set('b.category', ':newCategory')
        ->where('b.category = :oldCategory')
        ->setParameter('oldCategory', 'Science-Fiction')
        ->setParameter('newCategory', 'Romance');

    $query = $queryBuilder->getQuery();
    $query->execute();
}

public function countBooksInRomanceCategory()
    {
        $em = $this->getEntityManager();

        $dql = "SELECT COUNT(b.id) 
                FROM App\Entity\Book b 
                WHERE b.category = :category";

        $query = $em->createQuery($dql)
            ->setParameter('category', 'Romance');

        return $query->getSingleScalarResult();
    }
    
    



}

    





//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

