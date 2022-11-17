<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Tests\EntityCreator;
use App\Tests\DatabaseTestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class PostTest extends DatabaseTestCase
{
    use EntityCreator;

    /**
     * @test
     */
    public function it_creates_new_Post_with_the_correct_Slug()
    {
        $post = $this->makePost(['title' => 'Some Title 123']);
        $this->getEntityManager()->flush();

        $this->assertEquals('some-title-123', $post->getSlug(), 'Wrong Post slug value.');
    }
}
