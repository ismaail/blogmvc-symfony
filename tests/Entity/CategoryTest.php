<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Tests\DatabaseTestCase;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function it_creates_new_Category_with_the_correct_Slug()
    {
        $category = new Category();
        $category->setName('Foo Bar');

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();

        $this->assertEquals('foo-bar', $category->getSlug(), 'Wrong Category slug value.');
    }
}
