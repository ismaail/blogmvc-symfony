<?php

namespace App\Entity;

use Symfony\Component\Cache\DoctrineProvider;
use Doctrine\Common\Cache\Cache;

/**
 * Class CommentListener
 * @package App\Entity
 */
class CommentListener
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private Cache $cacheProvider;

    /**
     * CommentListener constructor.
     *
     * @param \Doctrine\Common\Cache\Cache $cacheProvider
     */
    public function __construct(Cache $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function postPersist(): void
    {
        $this->cache->deleteItem(urlencode('[post_by_slug][1]'));
    }
}
