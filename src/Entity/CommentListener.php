<?php

namespace App\Entity;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class CommentListener
 * @package App\Entity
 */
class CommentListener
{
    /**
     * @var \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    private AdapterInterface $cache;

    /**
     * CommentListener constructor.
     *
     * @param \Symfony\Component\Cache\Adapter\AdapterInterface $cache
     */
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function postPersist(): void
    {
        $this->cache->deleteItem(urlencode('[post_by_slug][1]'));
    }
}
