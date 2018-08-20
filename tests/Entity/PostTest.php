<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use App\Tests\DatabaseTestCase;

/**
 * Class PostTest
 *
 * @package App\Tests\Entity
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PostTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function it_creates_new_Post_with_the_correct_Slug()
    {
        $category = new Category();
        $category->setName('Category 1');
        $this->entityManager->persist($category);

        $author = new User();
        $author->setUsername('User 1')->setPassword('password');
        $this->entityManager->persist($author);

        $post = new Post();
        $post
            ->setTitle('Some Title 123')
            ->setContent('Some Content')
            ->setCreatedAt(new \DateTime())
            ->setCategory($category)
            ->setAuthor($author);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->assertEquals('some-title-123', $post->getSlug(), 'Wrong Post slug value.');
    }
}
