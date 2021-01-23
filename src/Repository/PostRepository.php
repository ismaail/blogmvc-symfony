<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class PostRepository
 *
 * @package App\Repository
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    /**
     * PostRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param array $filters
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate(int $page = 1, int $perPage = 10, array $filters = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.author', 'a')->addSelect('a')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $this->setQueryFilters($qb, $filters);

        $query = $qb->getQuery();

        $query->enableResultCache(null, 'posts_all');

        return new Paginator($query);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param array $filters
     */
    private function setQueryFilters($qb, array $filters): void
    {
        // Filter by Category slug.
        $category = $filters['category'] ?? null;
        if (null !== $category) {
            $qb->where('c.slug = :slug')
               ->setParameter('slug', $category);
        }

        // Filter by Author username.
        $author = $filters['author'] ?? null;
        if (null !== $author) {
            $qb->where('a.username = :username')
               ->setParameter('username', $author);
        }
    }

    /**
     * Find a single Post by slug,
     * if not found, throws 404 error.
     *
     * @param string $slug
     *
     * @return \App\Entity\Post
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findBySlug(string $slug)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.author', 'a')->addSelect('a')
            ->leftJoin('p.comments', 'm')->addSelect('m')
            ->where('p.slug = :slug')->setParameter('slug', $slug)
            ->orderBy('m.createdAt', 'desc')
            ->getQuery();

        $query->enableResultCache(null, 'post_by_slug_' . $slug);

        $post = $query->getOneOrNullResult();

        if (null === $post) {
            throw new NotFoundHttpException('Post not found');
        }

        return $post;
    }

    /**
     * @param int $cout
     *
     * @return \App\Entity\Post[]
     */
    public function latest(int $cout)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')->addSelect('a')
            ->orderBy('p.createdAt', 'desc')
            ->setMaxResults($cout)
            ->getQuery();

        $query->enableResultCache(null, 'posts_latest');

        return $query->getresult();
    }

    /**
     * @param \App\Entity\Post $post
     * @param \App\Entity\Comment $comment
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addComment(Post $post, Comment $comment): void
    {
        $post->addComment($comment);
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
    }
}
