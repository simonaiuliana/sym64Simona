<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }



    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.article_date_create', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findAllByAuthor(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :val')
            ->setParameter('val', $id)
            ->orderBy('p.article_date_create', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findTenLastPublished(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.published = 1')
            ->orderBy('p.article_date_posted', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findAllPublished(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.published = 1')
            ->orderBy('p.article_date_posted', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Article Returns an array of Article objects
     */
    public function getArticleBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title_slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
