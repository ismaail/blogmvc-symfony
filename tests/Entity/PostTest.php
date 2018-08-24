<?php

namespace App\Tests\Entity;

use App\Tests\EntityCreator;
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
    use EntityCreator;

    /**
     * @test
     */
    public function it_creates_new_Post_with_the_correct_Slug()
    {
        $post = $this->createPost(['title' => 'Some Title 123']);
        $this->entityManager->flush();

        $this->assertEquals('some-title-123', $post->getSlug(), 'Wrong Post slug value.');
    }
}
