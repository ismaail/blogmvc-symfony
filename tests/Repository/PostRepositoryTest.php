<?php

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Tests\EntityCreator;
use App\Tests\DatabaseTestCase;

/**
 * Class PostRepositoryTest
 *
 * @package App\Tests
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PostRepositoryTest extends DatabaseTestCase
{
    use EntityCreator;

    /**
     * @var \App\Repository\PostRepository
     */
    private $postRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->postRepository = $this->getEntityManager()->getRepository(Post::class);
    }

    /**
     * @test
     */
    public function it_returns_paginated_posts()
    {
        // Preparations
        $this->createPost();
        $this->createPost();
        $this->getEntityManager()->flush();

        // Actions
        $paginator = $this->postRepository->paginate(1, 10);

        // Assertions
        $this->assertEquals(2, $paginator->count(), 'Wrong Post Paginator count.');
    }

    /**
     * @test
     */
    public function it_returns_paginated_post_by_category()
    {
        // Preparations
        $category1 = $this->createCategory(['name' => 'Cat 1']);
        $category2 = $this->createCategory(['name' => 'Cat 2']);

        $this->createPost([], $category1);
        $this->createPost([], $category1);
        $this->createPost([], $category2);

        $this->getEntityManager()->flush();

        // Actions
        $paginator1 = $this->postRepository->paginate(1, 10, ['category' => 'cat-1']);
        $paginator2 = $this->postRepository->paginate(1, 10, ['category' => 'cat-2']);

        // Assertions
        $this->assertEquals(2, $paginator1->count(), 'Wrong Post Paginator count.');
        $this->assertEquals(1, $paginator2->count(), 'Wrong Post Paginator count.');

        foreach ($paginator1 as $post) {
            /** @var \App\Entity\Post $post */
            $this->assertEquals('Cat 1', $post->getCategory()->getName(), 'Wrong Post Category Name.');
        }

        foreach ($paginator2 as $post) {
            /** @var \App\Entity\Post $post */
            $this->assertEquals('Cat 2', $post->getCategory()->getName(), 'Wrong Post Category Name.');
        }
    }

    /**
     * @test
     */
    public function it_returns_paginated_post_by_author()
    {
        // Preparations
        $author1 = $this->createUser(['username' => 'user_A']);
        $author2 = $this->createUser(['username' => 'user_B']);

        $this->createPost([], null, $author1);
        $this->createPost([], null, $author2);
        $this->createPost([], null, $author2);

        $this->getEntityManager()->flush();

        // Actions
        $paginator1 = $this->postRepository->paginate(1, 10, ['author' => 'user_A']);
        $paginator2 = $this->postRepository->paginate(1, 10, ['author' => 'user_B']);

        // Assertions
        $this->assertEquals(1, $paginator1->count(), 'Wrong Post Paginator count.');
        $this->assertEquals(2, $paginator2->count(), 'Wrong Post Paginator count.');

        foreach ($paginator1 as $post) {
            /** @var \App\Entity\Post $post */
            $this->assertEquals('user_A', $post->getAuthor()->getUsername(), 'Wrong Post Author username.');
        }

        foreach ($paginator2 as $post) {
            /** @var \App\Entity\Post $post */
            $this->assertEquals('user_B', $post->getAuthor()->getUsername(), 'Wrong Post Author username.');
        }
    }

    /**
     * @test
     */
    public function it_returns_a_single_post_by_slug()
    {
        $this->createPost(['title' => 'Some Title 123']);
        $this->getEntityManager()->flush();

        $foundPost = $this->postRepository->findBySlug('some-title-123');

        $this->assertInstanceOf(Post::class, $foundPost);
        $this->assertEquals('Some Title 123', $foundPost->getTitle(), 'Wrong Post Title value.');
    }

    /**
     * @test
     */
    public function it_throw_NotFoundHttpException_if_post_dont_exists()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Post not found');

        $this->postRepository->findBySlug('non-existant-post');
    }

    /**
     * @test
     */
    public function createdAt_datetime_is_auto_generated()
    {
        $this->createPost();
        $this->getEntityManager()->flush();

        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(\DateTime::class, $post->getCreatedAt());
    }
}
