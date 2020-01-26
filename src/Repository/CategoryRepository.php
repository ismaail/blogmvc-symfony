<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class CategoryRepository
 *
 * @package App\Repository
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @var bool
     */
    private $useCache;

    /**
     * CategoryRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     * @param bool $useCache
     */
    public function __construct(ManagerRegistry $registry, bool $useCache)
    {
        parent::__construct($registry, Category::class);

        $this->useCache = $useCache;
    }

    /**
     * @return \App\Entity\Category[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->useResultCache($this->useCache, null, 'categories_all')
            ->getResult();
    }
}
