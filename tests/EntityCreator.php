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
 * @property \Doctrine\ORM\EntityManager $entityManager
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

        $this->entityManager->persist($category);

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
        $author = new User();
        $author
            ->setUsername($input['username'] ?? 'user_'.self::$userCounter++)
            ->setPassword($input['password'] ?? 'password');

        $this->entityManager->persist($author);

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
            ->setCreatedAt($input['created_at'] ?? new \DateTime())
            ->setCategory($category)
            ->setAuthor($author);

        $this->entityManager->persist($post);

        return $post;
    }
}
