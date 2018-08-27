<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
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
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $this->setQueryFilters($qb, $filters);

        return new Paginator($qb->getQuery());
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
            $qb->leftJoin('p.category', 'c')
                ->where('c.slug = :slug')
                ->setParameter('slug', $category);
        }

        // Filter by Author username.
        $author = $filters['author'] ?? null;
        if (null !== $author) {
            $qb->leftJoin('p.author', 'a')
                ->where('a.username = :username')
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
        $post = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.author', 'a')->addSelect('a')
            ->where('p.slug = :slug')->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

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
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'desc')
            ->setMaxResults($cout)
            ->getQuery()
            ->getResult();
    }
}
