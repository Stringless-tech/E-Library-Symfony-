<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
*/
    public function findTop5RatedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('avg(g.value) as avg_value, b.title,b.author,b.imageFilename,b.id')
            ->join('b.grades','g')
            ->groupBy('g.bookId')
            ->orderBy('avg_value','DESC')
            ->setMaxResults( 5 )
            ->getQuery()
            ->getResult();
    }
    public function searchResults($slug)
    {
        return $this->createQueryBuilder('b')
            ->select('b.id,b.title,b.author,b.imageFilename,c.categoryName')
            ->join('b.category','c')
            ->where('b.title LIKE :val')
            ->orWhere('b.author LIKE :val')
            ->setParameter('val', '%'.$slug.'%')
            ->getQuery()
            ->getResult();
    }
}
