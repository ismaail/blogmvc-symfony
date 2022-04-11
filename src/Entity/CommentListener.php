<?php

namespace App\Entity;

use Symfony\Contracts\Cache\CacheInterface;

/**
 * Class CommentListener
 * @package App\Entity
 */
class CommentListener
{
    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private CacheInterface $cacheProvider;

    /**
     * CommentListener constructor.
     *
     * @param \Symfony\Contracts\Cache\CacheInterface $cacheProvider
     */
    public function __construct(CacheInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param \App\Entity\Comment $comment
     */
    public function postPersist(Comment $comment): void
    {
        $cachekey = 'post_by_slug_' . $comment->getPost()->getSlug();

        $this->cacheProvider->delete($cachekey);
    }
}
