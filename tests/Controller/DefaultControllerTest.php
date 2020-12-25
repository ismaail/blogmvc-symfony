<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use App\Tests\TestHelper;
use App\Tests\DoctrineMocker;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 *
 * @package App\Tests\Controller
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DefaultControllerTest extends WebTestCase
{
    use TestHelper;
    use DoctrineMocker;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject $postRepository
     *
     * @return array
     */
    private function mockEmbedControllers($postRepository)
    {
        $categoryRepository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findAll'])
            ->getMock();

        $postRepository->expects($this->once())
            ->method('latest')
            ->with(5)
            ->willReturn([]);

        $categoryRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        return [$categoryRepository];
    }

    /**
     * @test
     */
    public function it_list_paginated_posts()
    {
        // Preparations
        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['paginate', 'latest'])
            ->getMock();

        [$categoryRepository] = $this->mockEmbedControllers($postRepository);

        $posts = $this->createPosts();
        $paginator = $this->mockPaginator($posts, 10);
        $postRepository->expects($this->once())
            ->method('paginate')
            ->with(1, 10, [])
            ->willReturn($paginator);

        $this->client->getContainer()->set('app.repository.post_repository', $postRepository);
        $this->client->getContainer()->set('app.repository.category_repository', $categoryRepository);

        // Actions
        $crawler = $this->client->request('GET', '/');

        $this->handleTestError($this->client->getResponse(), $crawler);

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');

        $headersNode = $crawler->filter('article > h2');
        $this->assertCount(2, $headersNode, 'Wrong count of Posts header.');
        $this->assertEquals('Post Title 1', $headersNode->eq(0)->text());
        $this->assertEquals('Post Title 2', $headersNode->eq(1)->text());

        $metadataNodes = $crawler->filter('article > p > small');
        $this->assertContains('Category : Cat A', $metadataNodes->eq(0)->text());
        $this->assertContains('by jhon doe', $metadataNodes->eq(0)->text());
        $this->assertContains('on January 1st 2018', $metadataNodes->eq(0)->text());
        $this->assertContains('Category : Cat B', $metadataNodes->eq(1)->text());
        $this->assertContains('by jane doe', $metadataNodes->eq(1)->text());
        $this->assertContains('on October 30th 2018', $metadataNodes->eq(1)->text());

        $linksNodes = $crawler->filter('article > p > a');
        $this->assertEquals('/post/post-title-1', $linksNodes->eq(0)->attr('href'));
        $this->assertEquals('/post/post-title-2', $linksNodes->eq(1)->attr('href'));
    }

    /**
     * @test
     */
    public function it_lists_paginated_posts_by_category()
    {
        // Preparations
        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['paginate', 'latest'])
            ->getMock();

        [$categoryRepository] = $this->mockEmbedControllers($postRepository);

        $posts = $this->createPosts();
        $paginator = $this->mockPaginator([$posts[0]], 10);
        $postRepository->expects($this->once())
            ->method('paginate')
            ->with(1, 10, ['category' => 'cat-a'])
            ->willReturn($paginator);

        $this->client->getContainer()->set('app.repository.post_repository', $postRepository);
        $this->client->getContainer()->set('app.repository.category_repository', $categoryRepository);

        // Actions
        $crawler = $this->client->request('GET', '/category/cat-a');

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');
        $this->assertCount(1, $crawler->filter('article > h2'));
    }

    /**
     * @test
     */
    public function it_lists_paginated_posts_by_author()
    {
        // Preparations
        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['paginate', 'latest'])
            ->getMock();

        [$categoryRepository] = $this->mockEmbedControllers($postRepository);

        $posts = $this->createPosts();
        $paginator = $this->mockPaginator([$posts[0]], 10);
        $postRepository->expects($this->once())
            ->method('paginate')
            ->with(1, 10, ['author' => 'jhon-doe'])
            ->willReturn($paginator);

        $this->client->getContainer()->set('app.repository.post_repository', $postRepository);
        $this->client->getContainer()->set('app.repository.category_repository', $categoryRepository);

        // Actions
        $crawler = $this->client->request('GET', '/author/jhon-doe');

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');
        $this->assertCount(1, $crawler->filter('article > h2'));
    }

    /**
     * @test
     */
    public function it_shows_post_with_comments()
    {
        $postRepository = $this->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findBySlug', 'latest'])
            ->getMock();

        [$categoryRepository] = $this->mockEmbedControllers($postRepository);

        /** @var \App\Entity\Post $post */
        $post = $this->createPosts()[0];

        $comment = (new Comment())
            ->setUsername('Jhon Smith')
            ->setEmail('smith@example.com')
            ->setCreatedAt(new \DateTime('2018-10-30'))
            ->setUpdatedAt(new \DateTime('2018-10-30'))
            ->setContent('Some Comment')
            ;
        $post->addComment($comment);

        //$paginator = $this->mockPaginator($posts, 10);
        $postRepository->expects($this->once())
            ->method('findBySlug')
            ->with('post-title-1')
            ->willReturn($post);

        $this->client->getContainer()->set('app.repository.post_repository', $postRepository);
        $this->client->getContainer()->set('app.repository.category_repository', $categoryRepository);

        // Actions
        $crawler = $this->client->request('GET', '/post/post-title-1');

        $this->handleTestError($this->client->getResponse(), $crawler);

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');

        $this->assertCount(1, $post->getComments(), '>>> Wrong Comments count.');

        $postTitle = $crawler->filter('.page-header > h1');
        $this->assertEquals('Post Title 1', $postTitle->eq(0)->text());
        $postMetadata = $crawler->filter('.page-header > p > small');
        $this->assertContains('Category : Cat A', $postMetadata->text());
        $this->assertContains('by jhon doe', $postMetadata->text());
        $this->assertContains('on January 1st 2018', $postMetadata->text());
        $commentsSectionHeader = $crawler->filter('.post-comments > h4');
        $this->assertEquals('1 Comment', $commentsSectionHeader->text(), '>>> Wrong Comments count text.');
        $commentImage = $crawler->filter('.post-comments img');
        $this->assertEquals('https://www.gravatar.com/avatar/adf8993c31e25d3cdab61f992e5d98d4?d=mm&s=100', $commentImage->attr('src'), '>>> Wrong Comment avatar image src');
        $comment = $crawler->filter('.post-comments p');
        $this->assertEquals('@Jhon Smith October 30th 2018', $comment->eq(0)->text(), '>>> Wrong Comment username & date.');
        $this->assertEquals('Some Comment', $comment->eq(1)->text(), '>>> Wrong Comment content.');
    }

    /**
     * Create 1 Posts, each with a separate Category & Author.
     *
     * @return array
     */
    private function createPosts(): array
    {
        $category1 = new Category();
        $category1->setName('Cat A');
        $category2 = new Category();
        $category2->setName('Cat B');

        $author1 = new User();
        $author1->setUsername('jhon doe');
        $author2 = new User();
        $author2->setUsername('jane doe');

        $post1 = new Post();
        $post1
            ->setTitle('Post Title 1')
            ->setSlug('post-title-1')
            ->setContent('Post 1 Content')
            ->setCreatedAt(new \DateTime('2018-01-01'))
            ->setCategory($category1)
            ->setAuthor($author1);

        $post2 = new Post();
        $post2
            ->setTitle('Post Title 2')
            ->setSlug('post-title-2')
            ->setContent('Post 1 Content')
            ->setCreatedAt(new \DateTime('2018-10-30'))
            ->setCategory($category2)
            ->setAuthor($author2);

        return [$post1, $post2];
    }
}
