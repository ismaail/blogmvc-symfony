<?php

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Entity\Comment;
use App\Tests\EntityCreator;
use App\Tests\DatabaseTestCase;

/**
 * Class PostRepositoryTest
 *
 * @package App\Tests
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
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
    protected function setUp(): void
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
        $this->makePost();
        $this->makePost();
        $this->getEntityManager()->flush();

        // Actions
        $paginator = $this->postRepository->paginate();

        // Assertions
        $this->assertEquals(2, $paginator->count(), 'Wrong Post Paginator count.');
    }

    /**
     * @test
     */
    public function it_returns_paginated_post_by_category()
    {
        // Preparations
        $category1 = $this->makeCategory(['name' => 'Cat 1']);
        $category2 = $this->makeCategory(['name' => 'Cat 2']);

        $this->makePost([], $category1);
        $this->makePost([], $category1);
        $this->makePost([], $category2);

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
        $author1 = $this->makeUser(['username' => 'user_A']);
        $author2 = $this->makeUser(['username' => 'user_B']);

        $this->makePost([], null, $author1);
        $this->makePost([], null, $author2);
        $this->makePost([], null, $author2);

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
        $this->makePost(['title' => 'Some Title 123']);
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
        $this->makePost();
        $this->getEntityManager()->flush();

        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(\DateTime::class, $post->getCreatedAt());
    }

    /**
     * @test
     */
    public function it_create_new_comment()
    {
        $post = $this->makePost();
        $this->getEntityManager()->flush();

        $comment = new Comment();
        $comment->setUsername('doe')
            ->setEmail('doe@example.com')
            ->setContent('some comment content')
            ;

        $this->postRepository->addComment($post, $comment);

        $createdPost = $this->postRepository->find(1);
        $comments = $createdPost->getComments();

        $this->assertCount(1, $comments);
        $this->assertSame('doe', $comments[0]->getUsername());
        $this->assertSame('doe@example.com', $comments[0]->getEmail());
        $this->assertSame('some comment content', $comments[0]->getContent());
    }
}
