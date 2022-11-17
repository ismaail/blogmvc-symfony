<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Tests\TestHelper;
use App\Tests\EntityCreator;
use App\Tests\DatabaseTestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class DefaultControllerTest extends DatabaseTestCase
{
    use TestHelper;
    use EntityCreator;

    private function createPost(string $postTitle, string $creationDate, string $categoryName, string $authorName): Post
    {
        $user = $this->makeUser(['username' => $authorName]);
        $category = $this->makeCategory(['name' => $categoryName]);

        $postData = [
            'title' => $postTitle,
            'created_at' => new \DateTime($creationDate),
        ];

        return $this->makePost($postData, $category, $user);
    }

    /**
     * @test
     */
    public function it_list_paginated_posts()
    {
        // Preparations
        $this->createPost('Post Title 1', '2018-10-30', 'Cat A', 'jhon-doe');
        $this->createPost('Post Title 2', '2018-01-01', 'Cat B', 'jane-doe');

        $this->getEntityManager()->flush();

        // Actions
        $crawler = $this->client->request('GET', '/');

        $this->handleTestError($this->client->getResponse(), $crawler);

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');

        $postRepository = $this->getEntityManager()->getRepository('App:Post');
        $this->assertEquals(2, $postRepository->count([]));

        $headersNode = $crawler->filter('article > h2');
        $this->assertCount(2, $headersNode, 'Wrong count of Posts header.');
        $this->assertEquals('Post Title 1', $headersNode->eq(0)->text());
        $this->assertEquals('Post Title 2', $headersNode->eq(1)->text());

        $metadataNodes = $crawler->filter('article > p > small');
        // 1st Post
        $this->assertStringContainsString('Category : Cat A', $metadataNodes->eq(0)->text());
        $this->assertStringContainsString('by jhon-doe', $metadataNodes->eq(0)->text());
        $this->assertStringContainsString('on October 30th 2018', $metadataNodes->eq(0)->text());
        // 2nd Post
        $this->assertStringContainsString('Category : Cat B', $metadataNodes->eq(1)->text());
        $this->assertStringContainsString('by jane-doe', $metadataNodes->eq(1)->text());
        $this->assertStringContainsString('on January 1st 2018', $metadataNodes->eq(1)->text());

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
        $this->createPost('Post Title 1', '2018-10-30', 'Cat A', 'jhon-doe');
        $this->createPost('Post Title 2', '2018-01-01', 'Cat B', 'jane-doe');

        $this->getEntityManager()->flush();

        // Actions
        $crawler = $this->client->request('GET', '/category/cat-a');

        // Assertions
        $postRepository = $this->getEntityManager()->getRepository('App:Post');
        $this->assertEquals(2, $postRepository->count([]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');
        $this->assertCount(1, $crawler->filter('article > h2'));
    }

    /**
     * @test
     */
    public function it_lists_paginated_posts_by_author()
    {
        // Preparations
        $this->createPost('Post Title 1', '2018-10-30', 'Cat A', 'jhon-doe');
        $this->createPost('Post Title 2', '2018-01-01', 'Cat B', 'jane-doe');

        $this->getEntityManager()->flush();

        // Actions
        $crawler = $this->client->request('GET', '/author/jhon-doe');

        // Assertions
        $postRepository = $this->getEntityManager()->getRepository('App:Post');
        $this->assertEquals(2, $postRepository->count([]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');
        $this->assertCount(1, $crawler->filter('article > h2'));
    }

    /**
     * @test
     */
    public function it_shows_post_with_comments()
    {
        // Preparations
        $post = $this->createPost('Post Title 1', '2018-01-01', 'Cat A', 'jhon-doe');
        $comment = $this->makeComment([
            'username' => 'Jhon Smith',
            'email' => 'smith@example.com',
            'content' => 'Some Comment from J. Smith',
            'created_at' => new \DateTime('2018-10-30'),
        ]);
        $post->addComment($comment);

        $this->getEntityManager()->flush();

        // Actions
        $crawler = $this->client->request('GET', '/post/post-title-1');

        $this->handleTestError($this->client->getResponse(), $crawler);

        // Assertions
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong Request status code.');

        $this->assertCount(1, $post->getComments(), '>>> Wrong Comments count.');

        $postTitle = $crawler->filter('.page-header > h1');
        $this->assertEquals('Post Title 1', $postTitle->eq(0)->text());
        $postMetadata = $crawler->filter('.page-header > p > small');
        $this->assertStringContainsString('Category : Cat A', $postMetadata->text());
        $this->assertStringContainsString('by jhon-doe', $postMetadata->text());
        $this->assertStringContainsString('on January 1st 2018', $postMetadata->text());
        $commentsSectionHeader = $crawler->filter('.post-comments > h4');
        $this->assertEquals('1 Comment', $commentsSectionHeader->text(), '>>> Wrong Comments count text.');
        $commentImage = $crawler->filter('.post-comments img');
        $this->assertEquals(
            'https://www.gravatar.com/avatar/adf8993c31e25d3cdab61f992e5d98d4?d=mm&s=100',
            $commentImage->attr('src'),
            '>>> Wrong Comment avatar image src'
        );
        $comment = $crawler->filter('.post-comments p');
        $this->assertEquals(
            '@Jhon Smith October 30th 2018',
            $comment->eq(0)->text(),
            '>>> Wrong Comment username & date.'
        );
        $this->assertEquals('Some Comment from J. Smith', $comment->eq(1)->text(), '>>> Wrong Comment content.');
    }
}
