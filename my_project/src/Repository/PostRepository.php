<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPosted($maxResults=null){

        $query =  $this->createQueryBuilder('p')
            ->andWhere('p.posted = :val')
            ->andWhere('p.publishDate<:now')
            ->setParameter('val', true)
            ->setParameter('now', new \DateTime() )
            ->orderBy('p.publishDate', 'DESC');

        if($maxResults != null){
            $query = $query->setMaxResults($maxResults);
        };
        $query = $query
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function findTopFive(){
        return $this->createQueryBuilder('p')
            ->andWhere('p.posted = :val')
            ->andWhere('p.publishDate<:now')
            ->setParameter('val', true)
            ->setParameter('now', new \DateTime() )
            ->orderBy('p.readCount', 'DESC')
            ->setMaxResults('5')
            ->getQuery()
            ->getResult();
    }
//
    public function findByTags($tagID){
        return $this->createQueryBuilder('p')
            ->join("p.hashtags",'h')
            ->andWhere('p.posted = :val')
            ->andWhere('p.publishDate<:now')
            ->andWhere('h.id=:id')
            ->setParameter('id', $tagID)
            ->setParameter('val', true)
            ->setParameter('now', new \DateTime() )
            ->orderBy('p.publishDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAuthor($author){
        return $this->createQueryBuilder('p')
            ->andWhere('p.author=:author')
            ->andWhere('p.posted = :val')
            ->andWhere('p.publishDate<:now')
            ->setParameter('author', $author)
            ->setParameter('val', true)
            ->setParameter('now', new \DateTime() )
            ->orderBy('p.publishDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
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
    public function findOneBySomeField($value): ?Post
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
