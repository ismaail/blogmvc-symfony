<?php

namespace App\Entity;

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
     * @param \App\Entity\Comment $comment
     */
    public function postPersist(Comment $comment): void
    {
        $cachekey = 'post_by_slug_' . $comment->getPost()->getSlug();

        $this->cacheProvider->delete($cachekey);
    }
}
