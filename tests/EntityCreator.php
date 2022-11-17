<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;

/**
 * @method \Doctrine\ORM\EntityManager getEntityManager()
 */
trait EntityCreator
{
    private static int $userCounter = 0;

    public function makeCategory(array $input = []): Category
    {
        $category = new Category();
        $category->setName($input['name'] ?? 'Category Name');

        $this->getEntityManager()->persist($category);

        return $category;
    }

    public function makeUser(array $input = []): User
    {
        $author = (new User())
            ->setUsername($input['username'] ?? 'user_' . self::$userCounter++)
            ->setPassword($input['password'] ?? 'password')
            ->setRoles([User::ROLE_ADMIN])
            ;

        $this->getEntityManager()->persist($author);

        return $author;
    }

    public function makePost(array $input = [], ?Category $category = null, ?User $author = null): Post
    {
        if (null === $category) {
            $category = $this->makeCategory();
        }

        if ($author === null) {
            $author = $this->makeUser();
        }

        $post = new Post();
        $post
            ->setTitle($input['title'] ?? 'Some Title')
            ->setContent($input['content'] ?? 'Some Content')
            ->setCategory($category)
            ->setAuthor($author);

        $createdAt = $input['created_at'] ?? new \DateTime();
        if ($createdAt) {
            $post->setCreatedAt($createdAt);
        }

        $this->getEntityManager()->persist($post);

        return $post;
    }

    public function makeComment(array $input = []): Comment
    {
        $comment = new Comment();
        $username = 'username_' . self::$userCounter++;

        $comment
            ->setUsername($input['username'] ?? $username)
            ->setEmail($input['email'] ?? $username . '@example.com')
            ->setCreatedAt($input['created_at'] ?? new \DateTime())
            ->setUpdatedAt($input['updated_at'] ?? new \DateTime())
            ->setContent($input['content'] ?? 'Some Comment #' . self::$userCounter)
            ;

        $this->getEntityManager()->persist($comment);

        return $comment;
    }
}
