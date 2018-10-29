<?php

namespace App\Tests;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;

/**
 * Trait EntityCreator
 *
 * @package App\Tests
 *
 * @method \Doctrine\ORM\EntityManager getEntityManager()
 */
trait EntityCreator
{
    private static $userCounter = 0;

    /**
     * @param array $input
     *
     * @return \App\Entity\Category
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createCategory(array $input = []): Category
    {
        $category = new Category();
        $category->setName($input['name'] ?? 'Category Name');

        $this->getEntityManager()->persist($category);

        return $category;
    }

    /**
     * @param array $input
     *
     * @return \App\Entity\User
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createUser(array $input = []): User
    {
        $author = (new User())
            ->setUsername($input['username'] ?? 'user_'.self::$userCounter++)
            ->setPassword($input['password'] ?? 'password')
            ->setRoles([User::ROLE_ADMIN])
            ;

        $this->getEntityManager()->persist($author);

        return $author;
    }

    /**
     * @param array $input
     * @param \App\Entity\Category|null $category
     * @param \App\Entity\User|null $author
     *
     * @return \App\Entity\Post
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createPost(array $input = [], ?Category $category = null, ?User $author = null): Post
    {
        if (null === $category) {
            $category = $this->createCategory();
        }

        if ($author === null) {
            $author = $this->createUser();
        }

        $post = new Post();
        $post
            ->setTitle($input['title'] ?? 'Some Title')
            ->setContent($input['content'] ?? 'Some Content')
            ->setCategory($category)
            ->setAuthor($author);

        $createdAt = $input['created_at'] ?? null;
        if ($createdAt) {
            $post->setCreatedAt($createdAt);
        }

        $this->getEntityManager()->persist($post);

        return $post;
    }
}
