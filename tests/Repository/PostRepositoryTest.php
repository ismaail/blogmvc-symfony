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

        $this->postRepository = $this->entityManager->getRepository(Post::class);
    }

    /**
     * @test
     */
    public function it_returns_a_single_post_by_slug()
    {
        $this->createPost(['title' => 'Some Title 123']);
        $this->entityManager->flush();

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
}
